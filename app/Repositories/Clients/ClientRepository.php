<?php

namespace App\Repositories\Clients;

use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class ClientRepository extends BaseRepository implements IClientRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\User";
    }

    /**
     * Get all client user group
     *
     * @return Collection
     */
    public function getAllClient(bool $for_reader_select = false) {
        $user = Auth::user ();
        if ($user->isGroup1 ()) {
            $query = DB::table ( 'users' )->where ( 'type', 'GROUP2' );
        } elseif ($user->isGroup2 ()) {
            if ($for_reader_select) {
                $query = DB::table ( 'users' )->where ( 'type', 'GROUP3' )->where ( 'parent_id', $user->id );
            } else {
                $query = DB::table ( 'users' )->where ( 'parent_id', $user->id )->whereIn ( 'type', [
                        'GROUP3',
                        'GROUP4'
                ] );
            }
        }
        return $query->orderBy('created_at', 'DESC')->get();
    }
    public function createNewClient(array $attributes, bool $isGroup3 = false) {
        $result = parent::create ( $attributes );
        if ($result) {
            try {
                if ($isGroup3) {
                    DB::statement ( "call generateCampaignTableByUser(?)", array (
                            $result->id
                    ) );
                    DB::statement ( "call new_template_template(?)", array (
                            $result->id
                    ) );
                    DB::statement ( "call new_report_summary_template(?)", array (
                            $result->id
                    ) );
                }
                DB::statement ( "CALL generatePriceConfigurationTableByUser({$result->id})" );
            } catch ( \Exception $e ) {
                // Show error message from SQL statement
            }
        }
        return $result;
    }
    public function getClientById($id) {
        return parent::find ( $id );
    }
    public function deleteClientById($list_id) {
        if (! $list_id) {
            return false;
        }
        $result = false;
        $list_error_delete = "";
        foreach ( $list_id as $client_id ) {
            $client = $this->find ( $client_id );
            // Cannot delete client group 2 if the client have a children
            $count_client_children = DB::table ( 'users' )->where ( 'parent_id', $client_id )->count ();
            if ($client->type == "GROUP2" && $count_client_children > 0) {
                $list_error_delete = $list_error_delete . $client->email . ",";
                continue;
            }
            if ($client->type == 'GROUP3') {
                $query = DB::table ( 'campaign_u_' . $client_id )->whereNotIn ( 'status', [
                        'DRAFT'
                ] )->count ();
                // Cannot delete client group 3 if the client have campaign that it's not status is DRAFT
                if ($query > 0) {
                    $list_error_delete = $list_error_delete . $client->email . ",";
                    continue;
                }
                $this->removeAllQueueTableByUser ( $client_id );
                DB::statement ( "call removeCampaignTableByUser(?)", array (
                        $client_id
                ) );
                DB::statement ( "call remove_template_template(?)", array (
                        $client_id
                ) );
                DB::statement ( "call remove_report_summary_template(?)", array (
                        $client_id
                ) );
                DB::table ( 'subscriber_lists' )->where ( 'user_id', $client_id )->delete ();
            }
            $this->addCredit($client_id, $client->credits - $client->credits_usage, "", true); // withdraw credits from deleted user
            DB::table ('billing_transaction')->where('user_id', $client_id)->delete(); // remove all transaction of deleted user
            $result = $this->delete ( $client_id );
            if (! $result) {
                $list_error_delete = $list_error_delete . $client->email . ",";
            }
        }

        if ($list_error_delete == "") {
            return [
                    'status' => true,
            ];
        }

        return [
                'status' => false,
                'error' => Lang::get ( 'client.delete_failed' ) . $list_error_delete
        ];
    }
    public function removeAllQueueTableByUser($userId) {
        $query = DB::table ( 'campaign_u_' . $userId );
        $list_campaign = $query->get ();
        foreach ( $list_campaign as $campaign ) {
            DB::statement ( "call removeQueueTableByUser(?,?)", array (
                    $userId,
                    $campaign->id
            ) );
        }
    }
    public function updateStatusClient($list_id, $status) {
        if (! booleanValue ( $status ) || ! $list_id) {
            return false;
        }
        foreach ( $list_id as $client_id ) {
            $client = $this->find ( $client_id );
            if (! $client) {
                return false;
            }
            $client->status = ($status == "true") ? "ENABLED" : "DISABLED";
            $client->save ();
        }
        return true;
    }
    public function update(array $attributes, $id, $table_name = null) {
        return parent::update ( $attributes, $id );
    }

    /**
     * fn add or draw creadit for user
     * {@inheritDoc}
     * @see \App\Repositories\Clients\IClientRepository::addCredit()
     */
    public function addCredit($id, $credits, $description, $isWithdraw = false) {
        $user = Auth::user (); // this is user account
        $client = DB::table('users')->where('id', $id)->first(); // this is client account
        if ($isWithdraw) { // Withdraw credits
            $user->credits_usage = round($user->credits_usage - $credits, 2);
            $client->credits = round($client->credits - $credits, 2);
            DB::table('users')->where('id', $user->id)->update(['credits_usage' => $user->credits_usage]);
            DB::table('users')->where('id', $client->id)->update(['credits' => $client->credits]);
        } else { // Add credits
            $user->credits_usage = round($user->credits_usage + $credits, 2);
            $client->credits = round($client->credits + $credits, 2);
            DB::table('users')->where('id', $user->id)->update(['credits_usage' => $user->credits_usage]);
            DB::table('users')->where('id', $client->id)->update(['credits' => $client->credits]);
        }

        // log transaction
        $new_transaction = new Transaction();
        $new_transaction->user_id = $id;
        $new_transaction->description = $description;
        $new_transaction->type = $isWithdraw ? "WITHDRAW" : "ADD";
        $new_transaction->credits = $credits;
        $new_transaction->currency = $client->currency;
        $new_transaction->created_by = $user->id;
        $new_transaction->updated_by = $user->id;
        $new_transaction->save();

        return true;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Clients\IClientRepository::updateCredits()
     */
    public function updateCredits( $idUser, $credits, $isMinus = true ) {
        $tableName = $this->model->getTable();
        if ( $isMinus ) {
            $qr = DB::raw("UPDATE {$tableName} SET `credits_usage` = `credits_usage` + {$credits} WHERE `id` = {$idUser}");
        } else {
            $qr = DB::raw("UPDATE {$tableName} SET `credits_usage` = `credits_usage` - {$credits} WHERE `id` = {$idUser}");
        }
        return DB::statement( $qr );
    }

    /**
     *  Transfer credits from parent to child. If parent is group 1, we should transfer credit to group 2 no condition. If
     *  parent is group 2, we should transfer if group 2 have enough credit to transfer to group 3. Step to transfer include:
     *    - Reset credits_usage of child
     *    - Increase credits usage of user.
     *
     * @param int $id: id of user
     * @return boolean
     */
    public function transferCreditMonthly($user) {
        $parent = DB::table('users')->where('id', $user->parent_id)->first(); // this is parent account

        $user->credits_usage = 0; // reset credits_usage
        $user->credits = $user->credits_limit;
        $parent->credits_usage += $user->credits_limit; // add credits_limit to credit_usage of parent

        DB::table('users')->where('id', $parent->id)->update(['credits_usage' => $parent->credits_usage]);
        DB::table('users')->where('id', $user->id)->update([
                'credits_usage' => $user->credits_usage,
                'credits' => $user->credits
        ]);

        // // log transaction
        $new_transaction = new Transaction();
        $new_transaction->user_id = $user->id;
        $new_transaction->type = "ADD";
        $new_transaction->credits = $user->credits_limit;
        $new_transaction->currency = $user->currency;
        $new_transaction->created_by = $parent->id;
        $new_transaction->updated_by = $parent->id;
        $new_transaction->save();

        return true;
    }

    public function updateCreditsLimitForMonthlyType($clientId, $creditChange, $description, $isDescrease = false) {
        $client = DB::table('users')->where('id', $clientId)->first(); // this is client account
        $user = DB::table('users')->where('id', $client->parent_id)->first(); // this is user account

        if ($isDescrease) {
            $new_limit = $client->credits_limit - $creditChange;
            if ($new_limit < 0) {
                return [
                        'status' => false,
                        'message' => Lang::get ( 'client.exceed_credit_limit_min' )
                ];
            } else {
                $client->credits_limit =  $new_limit;
                $client->credits = $new_limit;
                $user->credits_usage = $user->credits_usage - $creditChange;
            }

        } else {
            $new_limit = $client->credits_limit + $creditChange;
            if ($this->checkTotalLimitedOfParent($client, $new_limit)) {
                $client->credits_limit =  $new_limit;
                $client->credits = $new_limit;
                $user->credits_usage = $user->credits_usage + $creditChange;
            } else {
                return [
                        'status' => false,
                        'message' => Lang::get ( 'client.exceed_credit_limit' )
                ];
            }
        }

        DB::table('users')->where('id', $client->id)->update(['credits_limit' => $client->credits_limit, 'credits' => $client->credits]);
        DB::table('users')->where('id', $user->id)->update(['credits_usage' => $user->credits_usage]);

        // log transaction
        $new_transaction = new Transaction();
        $new_transaction->user_id = $client->id;
        $new_transaction->description = $description;
        $new_transaction->type = $isDescrease ? "DECREASE_LIMIT" : "INCREASE_LIMIT";
        $new_transaction->credits = $creditChange;
        $new_transaction->currency = $client->currency;
        $new_transaction->created_by = Auth::user()->id;
        $new_transaction->updated_by = Auth::user()->id;
        $new_transaction->save();

        return [
                'status' => true,
                'message' => Lang::get ( 'client.UpdateSuccessfully' )
        ];
    }

    private function checkTotalLimitedOfParent($client, $child_limit) {
        $parent = DB::table('users')->where('id', $client->parent_id)->first();
        if ($parent->billing_type == "UNLIMITED") {
            return true;
        }
        $child_array = DB::table('users')->where('parent_id', $parent->id)->get();
        $limit_total = 0;
        foreach ($child_array as $child) {
            $limit_total += ($child->id == $client->id) ? $child_limit : $child->credits_limit;
        }

        return $parent->credits_limit >= $limit_total;
    }

    public function getMaxTotalLimitedOfChild($client) {
        $parent = DB::table('users')->where('id', $client->parent_id)->first();
        if ($parent->billing_type == "UNLIMITED") {
            return null;
        }
        $child_array = DB::table('users')->where('parent_id', $parent->id)->get();
        $limit_total = 0;
        foreach ($child_array as $child) {
            $limit_total += $child->credits_limit;
        }

        return $limit_total;
    }

    public function createApiAccount(array $attributes, bool $isGroup3 = false) {
        $result = parent::create ( $attributes );
        if ($result) {
            try {
                if ($isGroup3) {
                    DB::statement ( "call generateCampaignTableByApiUser(?)", array (
                            $result->id
                    ) );
                    DB::statement ( "call new_template_template(?)", array (
                            $result->id
                    ) );
                    DB::statement ( "call new_report_summary_template(?)", array (
                            $result->id
                    ) );
                }
                DB::statement ( "CALL generatePriceConfigurationTableByUser({$result->id})" );
            } catch ( \Exception $e ) {
                // Show error message from SQL statement
            }
        }
        return $result;
    }

}
?>