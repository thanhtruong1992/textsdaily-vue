<?php

namespace App\Services\Settings;

use App\Services\BaseService;
use App\Repositories\Settings\IInboundConfigRepository;
use Illuminate\Support\Facades\Auth;

class InboundConfigService extends BaseService implements IInboundConfigService {
    /**
     */
    protected $inboundConfigRepo;

    /**
     *
     * @param IInboundConfigRepository $IInboundConfigRepo
     */
    public function __construct(IInboundConfigRepository $IInboundConfigRepo) {
        $this->inboundConfigRepo = $IInboundConfigRepo;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IInboundConfigService::fetchAll()
     */
    public function fetchAll() {
        return $this->inboundConfigRepo->all ();
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IInboundConfigService::getAllDataFormatDataTable()
     */
    public function getAllDataFormatDataTable($request) {
        $orderColumn = $request->get ( 'field' );
        $orderDirection = $request->get ( 'orderBy' );
        $keywork = $request->get ('search', '');
        $results = $this->inboundConfigRepo->getDataTableList($keywork, $orderColumn, $orderDirection);
        //
        return [
                'data' => $results->items(),
                'recordsTotal' => $results->total(),
                'recordsFiltered' => $results->total()
        ];
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IInboundConfigService::getInboundConfig()
     */
    public function getInboundConfig($id) {
        return $this->inboundConfigRepo->find($id);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IInboundConfigService::updateInboundConfig()
     */
    public function updateInboundConfig(array $attributes, $id) {
        $currentUser = Auth::user();
        $attributes['updated_by'] = $currentUser->id;
        return $this->inboundConfigRepo->update ( $attributes, $id );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IInboundConfigService::getInboundConfigByField()
     */
    public function getInboundConfigByField($field, $value) {
        return $this->inboundConfigRepo->findByField($field, $value)->first();
    }
}