<?php

namespace App\Repositories\Subscribers;

use App\Repositories\Subscribers\ISubscriberRepository;
use App\Repositories\BaseRepository;
use App\Models\CustomField;
use DB;
use Session;
use File;
use Auth;

class SubscriberRepository extends BaseRepository implements ISubscriberRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\Subscriber";
    }

    private function changeTableName( $idList ) {
        $this->__changeTableName ( array (
                'l_template' => 'l_' . $idList
        ) );
    }

    public function getListSubscribers($listId, $paging, $search_key, $column_sort, $orderBy, $filter, $flagFilter){
        $table = "subscribers_l_" . $listId;
        $user = Auth::user();
        $query = DB::table($table);

        if(!empty($user) && !!$user->encrypted) {
            $query->selectRaw("*, ENCRYPT_PHONE(phone) AS phone_encrypted, NULL AS phone, CONVERT_TZ(unsubscription_date, 'UTC', '" . $user->time_zone . "') AS unsubscription_date, (SELECT name FROM campaign_u_" . $user->id . " WHERE id = campaign_id) AS campaign_name");
        }else {
            $query->selectRaw("*, phone AS phone_encrypted, NULL AS phone, CONVERT_TZ(unsubscription_date, 'UTC', '" . $user->time_zone . "') AS unsubscription_date, (SELECT name FROM campaign_u_" . $user->id . " WHERE id = campaign_id) AS campaign_name");
        }

        // search key
        if(isset($search_key)) {
            $query->orWhere('first_name', 'like', "%$search_key%");
            $query->orWhere('last_naem', 'like', "%$search_key%");
        }

        if(isset($column_sort) && isset($orderBy)) {
            $query->orderBy($column_sort, $orderBy);
        }

        if(count($filter) > 0) {
            $this->filterSubscriber($filter, $flagFilter, $query);
        }

        return $query->paginate(10);
    }
    public function readFile($file, $line, $file_terminated, $file_enclosed = null) {
        if(!!File::exists($file)) {
            if (($handle = fopen($file, "r")) !== FALSE) {
                $result = "";
                $dataResult = file_get_contents($file);
                $data = $this->breakLine($dataResult);
                $arrData = $data['arrData'];
                Session::put('breakLine', $data['breakLine']);
                for($i = 1; $i <= count($arrData); $i++) {
                    if($line == $i) {
                        if(empty($file_enclosed)) {
                            $result = str_getcsv ($arrData[$i-1], $file_terminated);
                        }else {
                            $result = str_getcsv ($arrData[$i-1], $file_terminated, $file_enclosed);
                        }
                    }
                }
                fclose($handle);
                return $result;
            }
        }

        return false;
    }
    public function getCustomFieldOfUser($userId, $listId){
        $customFields = CustomField::where('user_id', $userId)->where("list_id", $listId)->orWhere('global', true)->get();

        return $customFields;
    }
    public function cloneTableSubscriber($tableName, $tableTempName) {
        return DB::statement("call clone_table_subscriber(?,?)", array($tableName, $tableTempName));
    }
    public function importSubscribersCSV($pathFile, $stringField, $tableTempName, $line, $fileTerminated, $fileEnclosed, $breakLine, $stringCol) {
        //DB::statement("call import_subscribers_csv(?,?,?,?,?)", array($pathFile, $stringField, $tableTempName, $fileTerminated == "," ? "," : $fileTerminated, $fileEnclosed));
        if($fileEnclosed == "'") {
            $loadDataQuery = 'LOAD DATA LOCAL INFILE "' . $pathFile . '" INTO TABLE ' . $tableTempName . ' FIELDS TERMINATED BY "' . $fileTerminated . '" ENCLOSED BY "'. $fileEnclosed .'" LINES TERMINATED BY "' . $breakLine . '" IGNORE ' . $line . ' LINES ('.$stringCol.') SET ' . $stringField;
        }else{
            $loadDataQuery = "LOAD DATA LOCAL INFILE '" . $pathFile . "' INTO TABLE {$tableTempName} FIELDS TERMINATED BY '" . $fileTerminated . "' ENCLOSED BY '". $fileEnclosed ."' LINES TERMINATED BY '" . $breakLine ."' IGNORE $line LINES (".$stringCol.") SET " . $stringField;
        }
        $pdo = DB::connection()->getPdo();
        return $pdo->exec($loadDataQuery);
    }
    public function moveDataSubscribers($tableTempName, $tableName, $updateIfDuplicatue, $update_fields, $list_id, $user_id, $status = null, $flagUpdate = false) {
        $query = DB::select("call move_data_subscribers(?,?,?,?,?,?,?,?)", array($tableTempName, $tableName, $updateIfDuplicatue, $update_fields, $list_id, $user_id, $status, $flagUpdate));
        return $query;
    }
    public function exportSubscribersCSV($tableName, $arrField, $fileName, $filter, $flagFilter, $headerExport) {
        try {
            $dataQuery = "";
            $user = Auth::user();
            $dataQuery= DB::table($tableName);
            $dataQuery->selectRaw(implode(", ", $arrField));

            $this->filterSubscriber($filter, $flagFilter, $dataQuery);
            $query = "SELECT " . implode(" , ", $headerExport) . "  UNION ALL ";
            $query .= $dataQuery->toSql();
            $dataReplace = $dataQuery->getBindings();
            $query = $this->replaceQuery($query, $dataReplace);
            $query .= " INTO OUTFILE '" . $fileName ."' CHARACTER SET UTF8 FIELDS TERMINATED BY ',' LINES TERMINATED BY '\n'";
            $pdo = DB::connection()->getPdo();
            return $pdo->exec($query);
        }catch(\Exception $e) {
            return false;
        }
    }
    public function deleteSubscribers ($ids, $tableCampaign, $tableSubscriber) {
        $campaigns = [];
        try {
            $campaigns = DB::table($tableCampaign)->where("list_id", $listID)->get()->toArray();
        }catch(\Exception $e) {

        }

        if(count($campaigns) > 0) {
            return false;
        }

        $result = DB::table($tableSubscriber)->whereIn("id", $ids)->delete();
        return true;
    }
    public function filterSubscriber($filter, $flagFilter, $query) {
        $arrIs = [
                "=",
                "!="
        ];
        if($flagFilter == "and") {
            if(isset($filter['status'])) {
                $data = $filter['status'];
                $flag = $data->flag;
                $val = $data->val;
                $query->where('status', $flag, $val);
            }
            if(isset($filter['phone'])) {
                $data = $filter['phone'];
                $flag = $data->flag;
                $val = $data->val;
                if(in_array($flag, $arrIs)) {
                    $query->where('phone', $flag, $val);
                }else{
                    $query->where('phone', $flag, "%$val%");
                }
            }

            if(isset($filter['first_name'])){
                $data = $filter['first_name'];
                $flag = $data->flag;
                $val = $data->val;
                if(in_array($flag, $arrIs)) {
                    $query->where('first_name', $flag, $val);
                }else{
                    $query->where('first_name', $flag, "%$val%");
                }
            }

            if(isset($filter['last_name'])) {
                $data = $filter['last_name'];
                $flag = $data->flag;
                $val = $data->val;
                if(in_array($flag, $arrIs)) {
                    $query->where('last_name', $flag, $val);
                }else{
                    $query->where('last_name', $flag, "%$val%");
                }
            }
        }else {
            if(isset($filter['status'])) {
                $data = $filter['status'];
                $flag = $data->flag;
                $val = $data->val;
                $query->orWhere('status', $flag, $val);
            }
            if(isset($filter['phone'])) {
                $data = $filter['phone'];
                $flag = $data->flag;
                $val = $data->val;
                if(in_array($flag, $arrIs)) {
                    $query->orWhere('phone', $flag, $val);
                }else{
                    $query->orWhere('phone', $flag, "%$val%");
                }
            }

            if(isset($filter['first_name'])){
                $data = $filter['first_name'];
                $flag = $data->flag;
                $val = $data->val;
                if(in_array($flag, $arrIs)) {
                    $query->orWhere('first_name', $flag, $val);
                }else{
                    $query->orWhere('first_name', $flag, "%$val%");
                }
            }

            if(isset($filter['last_name'])) {
                $data = $filter['last_name'];
                $flag = $data->flag;
                $val = $data->val;
                if(in_array($flag, $arrIs)) {
                    $query->orWhere('last_name', $flag, $val);
                }else{
                    $query->orWhere('last_name', $flag, "%$val%");
                }
            }
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\BaseRepository::replaceQuery()
     */
    public function replaceQuery($sql, $bindings) {
        $needle = '?';
        foreach ($bindings as $replace){
            $pos = strpos($sql, $needle);
            if ($pos !== false) {
                if (gettype($replace) === "string") {
                    $replace = ' "'.addslashes($replace).'" ';
                }
                $sql = substr_replace($sql, $replace, $pos, strlen($needle));
            }
        }
        return $sql;
    }

    public function breakLine($data) {
        $arrdata = [];
        $breakLine = "";
        $n = strpos($data, "\n", 1);
        $rn = strpos($data, "\r\n", 1);
        $r = strpos($data, "\r", 1);
        if($rn !== false) {
            $breakLine = "\r\n";
        }else if($r !== false){
            $breakLine = "\r";
        }else {
            $breakLine = "\n";
        }

        $arrData = str_getcsv($data, $breakLine);
        return [
            'arrData' => $arrData,
            "breakLine" => $breakLine
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Subscribers\ISubscriberRepository::getCountStatusItem()
     */
    public function getCountStatusItem($list_id) {
        $table = "subscribers_l_" . $list_id;
        $query = DB::table($table);
        $count_element_array = ['active_subscribers' => 0,'inactive_subscribers' => 0];
        $result = $query->select(DB::raw('count(*) as number, status'))->groupBy('status')->get();
        $active_subscribers = 0;
        $inactive_subscribers = 0;
        foreach ($result as $element_count) {
            if ($element_count->status == 'SUBSCRIBED') {
                $active_subscribers += $element_count->number;
            } else {
                $inactive_subscribers += $element_count->number;
            }

            $count_element_array['active_subscribers'] = $active_subscribers;
            $count_element_array['inactive_subscribers']= $inactive_subscribers;
        }

        return $count_element_array;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Subscribers\ISubscriberRepository::updateByListId()
     */
    public function updateByListId( $attributes, $id, $listId )
    {
        $this->changeTableName( $listId );
        if ( is_array($id) ) {
            return DB::table($this->model->getTable ())->whereIn('id', $id)->update($attributes);
        } else {
            return parent::update($attributes, $id, $this->model->getTable ());
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Subscribers\ISubscriberRepository::findSubscriber()
     */
    public function findSubscriber( $idList, $id, $columns = ['*'] ) {
        $this->changeTableName($idList);
        return parent::find($id, $columns);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Subscribers\ISubscriberRepository::getAllTableSubscribers()
     */
    public function getAllTableSubscribers() {
        $preventTableName = $this->model->getTable();
        $this->changeTableName('%');
        $likeTableName = $this->model->getTable ();
        return $this->getAllTable ( $likeTableName, $preventTableName );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Subscribers\ISubscriberRepository::getDetectData()
     */
    public function getDetectData($idList, $limit, $offset = 0) {
        $this->changeTableName ( $idList );
        $qr = DB::table( $this->model->getTable() )->where([
                ['status', '=', 'SUBSCRIBED'],
                ['detect_status', '=', 'PENDING']
        ])->orderBy('detect_updated_at', 'ASC');
        //
        if ( $offset ) {
            $qr->offset( $offset );
        }
        //
        if ( $limit ) {
            $qr->take( $limit );
        }
        // Update status to PENDING
        $updateResults = $qr->update([
                'detect_status' => 'PROCESSING'
        ]);
        //
        if ( $updateResults ) {
            return DB::table( $this->model->getTable() )->select('id', 'phone', 'mccmnc')->where('detect_status', '=', 'PROCESSING')->get();
        } else {
            return false;
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Subscribers\ISubscriberRepository::countTotalsByDetectStatus()
     */
    public function countTotalsByDetectStatus($idList) {
        $this->changeTableName($idList);
        return DB::table($this->model->getTable())->select(DB::raw('detect_status, COUNT(1) AS totals'))->groupBy('detect_status')->get();
    }

    public function deleteSubscribersWithStatus($listID, $supperssedID, $status, $flagSupperssion = false) {
        return DB::select("call remove_subscribers(?,?,?,?)", array($listID, $supperssedID, $status, $flagSupperssion));
    }

    public function exportSubscriberWithStatus($listID, $status, $headers, $fields, $fileName, $supperssedID) {
        return DB::statement("call export_subscribers_with_status(?,?,?,?,?,?)", array($listID, $status, $headers, $fields, $fileName, $supperssedID));
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Repositories\Subscribers\ISubscriberRepository::createSubscriber()
     */
    public function createSubscriber(array $attributes, $idList) {
        $this->changeTableName($idList);
        return parent::create($attributes, $this->model->getTable());
    }

    /**
     * fn get subscriber by phone
     * @param unknown $phone
     * @param unknown $idList
     * @return mixed|array|Closure
     */
    public function getSubscriberByPhone($phone, $campaignID, $idList) {
        $this->changeTableName($idList);
        return parent::findWhere([
            "phone" => $phone,
            "campaign_id" => $campaignID
        ])->first();
    }

    /**
     * fn get total subscriber of list
     * @param string $idList
     * @return object
     */
    public function getTotalSubscriberByListID($idList) {
        $this->changeTableName($idList);
        return DB::table($this->model->getTable())->select(DB::raw('COUNT(1) AS totals'))->first();
    }
}
