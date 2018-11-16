<?php

namespace App\Repositories\TransactionHistories;

use DB;
use App\Repositories\BaseRepository;
use Auth;
use Carbon\Carbon;

class TransactionHistoryRepository extends BaseRepository implements ITransactionHistoryRepository {

    function model() {
        return "App\\Models\\Transaction";
    }

    /**
     * fn get all camapign
     * @param string $listUser
     * @param datetime $from
     * @param datetime $to
     * @param string $typeUser
     * @return array data
     */
    public function getTransactionHistoryCampains($listUser, $from, $to, $timezone, $typeUser, $defaultCurrency, $page) {
        return DB::select("call transaction_history_campaigns(?,?,?,?,?,?,?,?,?,?)", array($listUser, $from, $to, $timezone, $typeUser, $defaultCurrency, false, '', '', $page));
    }

    /**
     * fn export CSV Campaign
     * @param string $listUser
     * @param datetime $from
     * @param datetime $to
     * @param string $typeUser
     * @param string $headerCSV
     * @param string $pathFile
     * @return boolean
     */
    public function exportCSVCampaigns($listUser, $from, $to, $timezone, $typeUser, $defaultCurrency, $headerCSV, $pathFile) {
        return DB::statement("call transaction_history_campaigns(?,?,?,?,?,?,?,?,?,?)", array($listUser, $from, $to, $timezone, $typeUser, $defaultCurrency, true, $headerCSV, $pathFile, 0));
    }

    /**
     * fn get report campaign
     * @param unknown $campaignID
     * @param unknown $userID
     * @param unknown $typeUser
     * @param unknown $defaultPrice
     * @param unknown $defaultCurrency
     * @return unknown
     */
    public function getReportCampaign($campaignID, $userID, $typeUser, $defaultCurrency) {
        return DB::select("call transaction_history_campaign(?,?,?,?,?,?,?)", array($campaignID, $userID, $typeUser, $defaultCurrency, false, '', ''));
    }

    /**
     * fn export csv report campaign
     * @param unknown $campaignID
     * @param unknown $userID
     * @param unknown $typeUser
     * @param unknown $defaultPrice
     * @param unknown $defaultCurrency
     * @param unknown $headerCSV
     * @param unknown $pathFile
     * @return unknown
     */
    public function exportCSVReportCampaign($campaignID, $userID, $typeUser, $defaultCurrency, $headerCSV, $pathFile) {
        return DB::statement("call transaction_history_campaign(?,?,?,?,?,?,?)", array($campaignID, $userID, $typeUser, $defaultCurrency, true, $headerCSV, $pathFile));
    }

    public function getTransactionByQuery($from, $to, $timezone, $sort_column, $order_by, $page) {
        $user = Auth::user();
        $db_timezone = DB::select('select @@global.time_zone as timeZone;');
        $query = DB::table('billing_transaction')
        -> select(DB::raw("CONVERT_TZ(billing_transaction.created_at, '" . $db_timezone[0]->timeZone . "', '" . $timezone . "') as created_at"), 'billing_transaction.description', 'billing_transaction.type',
                'users.name', 'users.billing_type', 'billing_transaction.credits', 'billing_transaction.currency')
                -> leftJoin('users', 'users.id', '=', 'billing_transaction.user_id')
                -> where('billing_transaction.created_by', $user->id);

                if (isset($from) && isset($to)) {
                    $fromDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($from), $timezone)->setTimezone($db_timezone[0]->timeZone)->toDateTimeString();
                    $toDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($to), $timezone)->setTimezone($db_timezone[0]->timeZone)->toDateTimeString();

                    $query->whereBetween('billing_transaction.created_at', [$fromDate, $toDate]);
                } else if(isset($from)) {
                    $fromDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($from), $timezone)->setTimezone($db_timezone[0]->timeZone)->toDateTimeString();

                    $query->where('billing_transaction.created_at', '>=', $fromDate);
                } else if(isset($to)) {
                    $toDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($to), $timezone)->setTimezone($db_timezone[0]->timeZone)->toDateTimeString();
                    $query->where('billing_transaction.created_at', '<=', $toDate);
                }

                if (isset ( $sort_column ) && isset ( $order_by )) {
                    $query->orderBy ( 'billing_transaction.' . $sort_column, $order_by );
                }

                return $query->paginate (10);
    }

    public function exportCSVClient($from, $to, $timezone, $headerCSV, $pathFile) {
        $user = Auth::user();
        $db_timezone = DB::select('select @@global.time_zone as timeZone;');
        $query = DB::table('billing_transaction')
        -> select(DB::raw("CONVERT_TZ(billing_transaction.created_at, '" . $db_timezone[0]->timeZone . "', '" . $timezone . "') as created_at"), 'billing_transaction.description', 'billing_transaction.type',
                'users.name', 'users.billing_type', 'billing_transaction.credits', 'billing_transaction.currency')
                -> leftJoin('users', 'users.id', '=', 'billing_transaction.user_id')
                -> where('billing_transaction.created_by', $user->id);

        if (isset($from) && isset($to)) {
            $fromDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($from), $timezone)->setTimezone($db_timezone[0]->timeZone)->toDateTimeString();
            $toDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($to), $timezone)->setTimezone($db_timezone[0]->timeZone)->toDateTimeString();

            $query->whereBetween('billing_transaction.created_at', [$fromDate, $toDate]);
        } else if(isset($from)) {
            $fromDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($from), $timezone)->setTimezone($db_timezone[0]->timeZone)->toDateTimeString();

            $query->where('billing_transaction.created_at', '>=', $fromDate);
        } else if(isset($to)) {
            $toDate = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($to), $timezone)->setTimezone($db_timezone[0]->timeZone)->toDateTimeString();
            $query->where('billing_transaction.created_at', '<=', $toDate);
        }

        $str_query = $this->replaceQuery($query->toSql(), $query->getBindings());
        $queryCSV = "SELECT " . $headerCSV . " UNION SELECT created_at, IFNULL(description, '') AS description, type, name, billing_type, credits, currency INTO OUTFILE '" . $pathFile  . "' FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '' LINES TERMINATED BY '\n' FROM (" . $str_query . ") AS T1";
        $pdo = DB::connection()->getPdo();
        return $pdo->exec($queryCSV);
    }
}
