<?php

namespace App\Services\Settings;

use App\Services\BaseService;
use App\Repositories\Settings\IPreferredServiceProviderRepository;
use Illuminate\Support\Facades\File;

class PreferredServiceProviderService extends BaseService implements IPreferredServiceProviderService {
    /**
     */
    protected $preferredServiceProviderRepo;

    /**
     *
     * @param IServiceProviderRepository $serviceProviderRepo
     */
    public function __construct(IPreferredServiceProviderRepository $IPreferredServiceProviderRepo) {
        $this->preferredServiceProviderRepo = $IPreferredServiceProviderRepo;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Settings\IServiceProviderService::fetchAll()
     */
    public function fetchAll() {
        return $this->preferredServiceProviderRepo->all ();
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IPreferredServiceProviderService::fetchAllGroupByCountry()
     */
    public function fetchAllGroupByCountry( $preventCountries = [] ) {
        $allData = $this->fetchAll ();
        $results = [ ];
        foreach ( $allData as $key => $item ) {
            if ( $preventCountries && in_array($item->country, $preventCountries) ) {
                continue;
            }
            $results [$item->country] [] = $item->network;
        }
        return $results;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IPreferredServiceProviderService::fetchAllCountryEnabled()
     */
    public function fetchAllCountryEnabled($allowCountries = []) {
        $allData = $this->fetchAll ();
        $results = [ ];
        foreach ( $allData as $key => $item ) {
            if ( $allowCountries&& in_array($item->country, $allowCountries) ) {
                $results [$item->country] [] = $item->network;
            }
        }
        return $results;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IPreferredServiceProviderService::fetchAllPreferredGroupByCountryNetwork()
     */
    public function fetchAllPreferredGroupByCountryNetwork() {
        $allData = $this->fetchAll ();

        $results = [ ];
        foreach ( $allData as $item ) {
            $results [$item->country] [$item->network] = $item->service_provider;
        }
        return $results;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Settings\IPreferredServiceProviderService::getServiceProviderByCountryNetwork()
     */
    public function getServiceProviderByCountryNetwork($country, $network) {
        return $this->preferredServiceProviderRepo->findWhere ( [
                'country' => $country,
                'network' => $network
        ], [
                'id',
                'service_provider'
        ] )->first ();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Settings\IPreferredServiceProviderService::updateServiceProvider()
     */
    public function updateServiceProvider(array $attributes, $id) {
        return $this->preferredServiceProviderRepo->update ( $attributes, $id );
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Settings\IPreferredServiceProviderService::importServiceProvider()
     */
    public function importServiceProvider($file, $path = '/public/settings/service-provider') {
        if ($file->isValid ()) {
            // Upload file
            $fileName = time () . '.' . $file->getClientOriginalExtension ();
            $fileUploaded = $file->storeAs ( $path, $fileName );

            // Move data to database
            $filePath = addCslashes ( storage_path ( 'app/' . $fileUploaded ), '\\' );

            // Detect the line ending character of a csv file
            $lineEndCharacter = self::getLineEndingCharacterCSV($filePath);

            //
            $result = $this->preferredServiceProviderRepo->importCSV ( $filePath, $lineEndCharacter );

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
}