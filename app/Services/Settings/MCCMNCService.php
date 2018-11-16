<?php

namespace App\Services\Settings;

use App\Services\BaseService;
use App\Repositories\Settings\IMCCMNCRepository;
use Illuminate\Support\Facades\File;

class MCCMNCService extends BaseService implements IMCCMNCService {
    /**
     */
    protected $MCCMNCRepo;

    /**
     *
     * @param IServiceProviderRepository $serviceProviderRepo
     */
    public function __construct(IMCCMNCRepository $IMCCMNCRepo) {
        $this->MCCMNCRepo = $IMCCMNCRepo;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMCCMNCService::fetchAll()
     */
    public function fetchAll() {
        return $this->MCCMNCRepo->all ();
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMCCMNCService::fetchAllOptions()
     */
    public function fetchAllOptions() {
        $allData = $this->fetchAll();
        $results = [];
        foreach ($allData as $item) {
            $results[$item->mccmnc]['country'] = $item->country;
            $results[$item->mccmnc]['network'] = $item->network;
        }
        return $results;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMCCMNCService::getAllDataFormatDataTable()
     */
    public function getAllDataFormatDataTable($request) {
        $orderColumn = $request->get ( 'field' );
        $orderDirection = $request->get ( 'orderBy' );
        $keywork = $request->get ('search', '');
        $results = $this->MCCMNCRepo->getDataTableList($keywork, $orderColumn, $orderDirection);
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
     * @see \App\Services\Settings\IMCCMNCService::getMCCMNC()
     */
    public function getMCCMNC($id) {
        return $this->MCCMNCRepo->find($id);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMCCMNCService::updateMCCMNC()
     */
    public function updateMCCMNC(array $attributes, $id) {
        return $this->MCCMNCRepo->update ( $attributes, $id );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMCCMNCService::importMCCMNC()
     */
    public function importMCCMNC($file, $path = '/public/settings/mcc-mnc') {
        if ($file->isValid ()) {
            // Upload file
            $fileName = time () . '.' . $file->getClientOriginalExtension ();
            $fileUploaded = $file->storeAs ( $path, $fileName );

            // Move data to database
            $filePath = addCslashes ( storage_path ( 'app/' . $fileUploaded ), '\\' );

            // Detect the line ending character of a csv file
            $lineEndCharacter = self::getLineEndingCharacterCSV($filePath);

            //
            $result = $this->MCCMNCRepo->importCSV ( $filePath, $lineEndCharacter );

            // Remove file
            File::delete ( storage_path ( 'app/' . $fileUploaded ) );

            //
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMCCMNCService::deleteMCCMNC()
     */
    public function deleteMCCMNC( $id ) {
        if (isset($id)) {
            $result = $this->MCCMNCRepo->delete($id);
            return $this->success($result);
        }
        return $this->fail();
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IMCCMNCService::getCountryNetworkByMCCMNC()
     */
    public function getCountryNetworkByMCCMNC( $mccmnc ) {
        return $this->MCCMNCRepo->findByField('mccmnc', $mccmnc, ['country', 'network'])->first();
    }
}