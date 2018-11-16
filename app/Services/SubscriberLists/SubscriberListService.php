<?php

namespace App\Services\SubscriberLists;

use App\Repositories\SubscriberLists\ISubscriberListRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscriberList;
use Illuminate\Support\Facades\DB;
use App\Repositories\Campaign\ICampaignRecipientsRepository;
use App\Repositories\Subscribers\ISubscriberRepository;
use App\Repositories\Reports\IReportListSummaryRepository;
use phpDocumentor\Reflection\Types\This;

class SubscriberListService extends BaseService implements ISubscriberListService {
    protected $subscriberListRepo;
    protected $campaignRecipientRepo;
    protected $subscirberRepo;
    protected $reportListSummaryRepo;

    public function __construct(
            ISubscriberListRepository $subscriberListRepo,
            ICampaignRecipientsRepository $campaignRecipientRepo,
            ISubscriberRepository $subscriberRepo,
            IReportListSummaryRepository $repotrListSummaryRepo) {
        $this->subscriberListRepo = $subscriberListRepo;
        $this->campaignRecipientRepo = $campaignRecipientRepo;
        $this->subscirberRepo = $subscriberRepo;
        $this->reportListSummaryRepo = $repotrListSummaryRepo;
    }
    public function getAllSubscribers() {
    }

    public function fetchListOptionsByUser( $user_id ){
        $allList = $this->subscriberListRepo->findWhere(['user_id'=> $user_id, 'is_global' => 0], ['id', 'name']);
        $results = array();
        foreach ($allList as $list){
            $results[$list['id']] = $list['name'];
        }
        return $results;
    }

    public function getAllSubscriberListByUser($request) {
        $user = Auth::user();
        $userID = $user->isGroup4() ? $user->reader_id : $user->id;
        $paging = $request->get ( 'page' );
        $search_key = $request->get ( 'search' );
        $column_sort = $request->get ( 'field' );
        $orderBy = $request->get ( 'orderBy' );
        $results = $this->subscriberListRepo->getSubscriberListByUser ( $userID, $search_key, $column_sort, $orderBy );

        foreach ($results->data as $item) {
            $canBeDeleted = $user->isGroup4() ? true : $this->campaignRecipientRepo->checkSubscriberListOfUserCanBeDeleted($item->id);
            $totalCount = $this->subscirberRepo->getCountStatusItem($item->id);
            $item->active_subscribers = $totalCount['active_subscribers'];
            $item->inactive_subscribers = $totalCount['inactive_subscribers'];
            $item->canBeDeleted = $canBeDeleted;
        }
        return [
                'data' => $results->data,
                'recordsTotal' => $results->total,
                'recordsFiltered' => $results->total,
                'total' => $results->total
        ];
    }

    public function createNewSubscriberList($request) {

        $userID = Auth::user ()->id;
        $request ['created_by'] = $userID;
        $request ['user_id'] = $userID;
        $request ['updated_by'] = $userID;
        $request ['total_subscribers'] = 0;
        $result = $this->subscriberListRepo->create($request->toArray());
        // Get id data for run store procedure to create subscriber_<list_id>
        $id = $result->id;
        $this->subscriberListRepo->createNewSubscriberListTemplate($id);
        return $this->success($result);
    }

    public function deleteSubscriberList($list_id) {
        $subscriberList = $this->subscriberListRepo->findWhere([
                "id" => $list_id,
                "user_id" => Auth::user()->id
        ])->first();

        if (!empty($subscriberList)) {

            $subscriberList = $subscriberList->toArray();
            if($subscriberList['is_global'] == false) {
                $canBeDeleteItem = $this->campaignRecipientRepo->checkSubscriberListOfUserCanBeDeleted($list_id);
                if ($canBeDeleteItem) {
                    $result = $this->subscriberListRepo->deleteSubscriberListItem($list_id);
                    return $this->success($result);
                }
            }
        }
        return $this->fail();
    }

    /**
     * FN get subscriber list
     * @param int $listId
     * @return object
     */
    public function getSubscriberList($listId) {
        $user = Auth::user();
        $subscriberList = $this->subscriberListRepo->findWhere([
                "id" => $listId,
                "user_id" => $user->id
        ])->first();

        if(empty($subscriberList)) {
            return $this->fail();
        }

        return $this->success($subscriberList->toArray());
    }

    /**
     * FN insert data Global Suppression List
     * @return subscriberList
     */
    public function createGlobalSupperssionList($userId) {
        $data = [
                "user_id" => $userId,
                "name" => "Global Suppression List",
                "is_global" => true,
                "total_subscribers" => 0,
                "created_by" => $userId,
                "updated_by" => $userId,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s')
        ];

        $result = $this->subscriberListRepo->create($data);
        $this->subscriberListRepo->createNewSubscriberListTemplate($result->id);
        return $result;
    }

    /**
     * fn create invalid entries list
     * {@inheritDoc}
     * @see \App\Services\SubscriberLists\ISubscriberListService::createInvalidEntriesList()
     */
    public function createInvalidEntriesList($userId) {
        $data = [
                "user_id" => $userId,
                "name" => "Invalid Entries List",
                "is_global" => true,
                "is_invalid" => true,
                "total_subscribers" => 0,
                "created_by" => $userId,
                "updated_by" => $userId,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s')
        ];

        $result = $this->subscriberListRepo->create($data);
        $this->subscriberListRepo->createNewSubscriberListTemplate($result->id);
        return $result;
    }

    public function getReportListSummary($listID) {
        $currencyUser = Auth::user()->currency;
        $result = $this->reportListSummaryRepo->getListSummary($listID);
        $resultChart = $result->dataChart;
        $resultData = $result->data;
        $dataChart = [];
        $dataMap = [];
        $data = [];
        $arrColor = config("constants.array_color_chart");
        $colorDefault = config("constants.color_chart_default");
        /* array_push($dataChart, [
                'Country', 'Total Price', (object)[ 'role' => 'style' ]
        ]); */
        //array_push($dataMap, ['Country', 'Total Price']);
        foreach ($resultChart as $key => $item) {
            $country = $this->getCountry(strtoupper($item->country));
            $country = count($country) > 1 ? "Unknown" : $country;

            array_push($dataChart, [
                    'country' => $country,
                    'total' => floatval(number_format($item->total, 2)),
                    'color' => $key < 5 ? $arrColor[$key] : $colorDefault
            ]);

            array_push($dataMap, [
                    'country' => $country,
                    'total' => number_format($item->total, 2),
            ]);
        }

        foreach ($result->data as $item) {
            $country = $this->getCountry(strtoupper($item->country));
            $country = count($country) > 1 ? "Unknown" : $country;
            $currency = $this->getCurrency(strtoupper($currencyUser));
            $item->country = $country;
            $item->network = $item->network != "" ? strtoupper($item->network) : "Unknown";
            $item->delivery_rate = $item->delivered == 0 || $item->totals == 0 ? 0 : round($item->delivered / $item->totals * 100) . "%";
            $item->total_price = number_format($item->total_price, 2) . " " . $currency->code;
            array_push($data, $item);
        }

        return (object) [
            "dataChart" => $dataChart,
            "dataMap" => $dataMap,
            "data" => $data
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SubscriberLists\ISubscriberListService::getGlobalSuppressionList()
     */
    public function getGlobalSuppressionList($idUser = null, $columns = ['*']) {
        $where = array (
                'is_global' => 1,
                'is_invalid' => 0
        );
        if ($idUser) {
            $where ['user_id'] = $idUser;
            return $this->subscriberListRepo->findWhere ( $where, $columns )->first();
        }
        return $this->subscriberListRepo->findWhere ( $where, $columns );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SubscriberLists\ISubscriberListService::getInvalidEntriesList()
     */
    public function getInvalidEntriesList($idUser = null, $columns = ['*']) {
        $where = array (
                'is_global' => 1,
                'is_invalid' => 1
        );
        if ($idUser) {
            $where ['user_id'] = $idUser;
            return $this->subscriberListRepo->findWhere ( $where, $columns )->first();
        }
        return $this->subscriberListRepo->findWhere ( $where, $columns );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SubscriberLists\ISubscriberListService::getDetectSubscriberList()
     */
    public function getDetectSubscriberList() {
        return $this->subscriberListRepo->scopeQuery(function($query){
            return $query->where([
                    'is_global' => 0,
                    'detect_status' => 'PENDING'
            ])->orderBy('detect_updated_at','ASC');
        })->first();
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\SubscriberLists\ISubscriberListService::updateSubscriberList()
     */
    public function updateSubscriberList( $idList, array $attributes ) {
        return $this->subscriberListRepo->update( $attributes, $idList );
    }

    public function updateSubscriberListViaModel( $idList, array $attributes ) {
        return $this->subscriberListRepo->updateSubscriberList( $attributes, $idList );
    }
}
