<?php

namespace App\Services\Clients;

interface IPriceConfigurationService {
    public function fetchAll( $idUser, $columns );
    public function fetchAllGroupByCountry( $idUser );
    public function getPriceConfigurationByCountryNetwork( $idUser, $country, $network = null );
    public function updatePriceConfiguration( $idUser, array $attributes, $id );
    public function createPriceConfiguration( $idUser, array $attributes );
    public function disabledCountries($idUser);
    public function deletePriceConfiguration( $idUser, $id );
    public function importPriceConfiguration($idClient, $file, $path = '/public/settings/price-config');
    public function enabledCountries($idUser);
}