<?php

namespace App\Services\Clients;

use App\Services\BaseService;
use App\Repositories\Clients\IPriceConfigurationRepository;
use File;

class PriceConfigurationService extends BaseService implements IPriceConfigurationService {
    /**
     */
    protected $priceConfigurationRepo;

    /**
     *
     * @param IPriceConfigurationRepository $serviceProviderRepo
     */
    public function __construct(IPriceConfigurationRepository $IPriceConfigurationRepo) {
        $this->priceConfigurationRepo = $IPriceConfigurationRepo;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Settings\IPriceConfigurationService::fetchAll()
     */
    public function fetchAll( $idUser, $columns ) {
        return $this->priceConfigurationRepo->allPriceConfiguration ( $idUser, $columns );
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Settings\IPriceConfigurationService::fetchAllGroupByCountry()
     */
    public function fetchAllGroupByCountry( $idUser ) {
        $allData = $this->fetchAll ( $idUser, ['id', 'country', 'network', 'price', 'disabled'] );

        $results = [ ];
        foreach ( $allData as $item ) {
            if ( $item->network ) {
                $network = $this->removeNonAlphanumericCharacters($item->network);
                $results [$item->country] [$network] ['price'] = $item->price;
                $results [$item->country] [$network] ['disabled'] = $item->disabled;
            } else {
                $results [$item->country] ['price'] = $item->price;
                $results [$item->country] ['disabled'] = $item->disabled;
            }
        }
        return $results;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Settings\IPriceConfigurationService::getPriceConfigurationByCountryNetwork()
     */
    public function getPriceConfigurationByCountryNetwork($idUser, $country, $network = null) {
        return $this->priceConfigurationRepo->findWherePriceConfiguration ( $idUser, [
                'country' => $country,
                'network' => $network
        ], [
                'id',
                'price',
                'disabled'
        ] );
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Settings\IPriceConfigurationService::updatePriceConfiguration()
     */
    public function updatePriceConfiguration($idUser, array $attributes, $id) {
        // Remove if country price is null and enable
        return $this->priceConfigurationRepo->updatePriceConfiguration ( $idUser, $attributes, $id );
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Clients\IPriceConfigurationService::createPriceConfiguration()
     */
    public function createPriceConfiguration($idUser, array $attributes) {
        return $this->priceConfigurationRepo->createPriceConfiguration ( $idUser, $attributes );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Clients\IPriceConfigurationService::disabledCountries()
     */
    public function disabledCountries($idUser) {
        $countries = $this->priceConfigurationRepo->getDisabledCoutries($idUser);
        //
        $results = [];
        foreach ( $countries as $country ) {
            $results[] = $country->country;
        }
        return $results;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Clients\IPriceConfigurationService::enabledCountries()
     */
    public function enabledCountries($idUser) {
        $countries = $this->priceConfigurationRepo->getEnabledCoutries($idUser);
        //
        $results = [];
        foreach ( $countries as $country ) {
            $results[] = $country->country;
        }
        return $results;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Clients\IPriceConfigurationService::deletePriceConfiguration()
     */
    public function deletePriceConfiguration( $idUser, $id ) {
        return $this->priceConfigurationRepo->deletePriceConfiguration($idUser, $id);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Clients\IPriceConfigurationService::importPriceConfiguration()
     */
    public function importPriceConfiguration($idClient, $file, $path = '/public/settings/price-config') {
        if ($file->isValid ()) {
            // Upload file
            $fileName = time () . '.' . $file->getClientOriginalExtension ();
            $fileUploaded = $file->storeAs ( $path, $fileName );

            // Move data to database
            $filePath = addCslashes ( storage_path ( 'app/' . $fileUploaded ), '\\' );

            // Detect the line ending character of a csv file
            $lineEndCharacter = self::getLineEndingCharacterCSV($filePath);

            //
            $result = $this->priceConfigurationRepo->importCSV ( $idClient, $filePath, $lineEndCharacter );

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