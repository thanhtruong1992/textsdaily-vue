<?php

namespace App\Services\Settings;

interface IPreferredServiceProviderService {
    public function fetchAll();
    public function fetchAllGroupByCountry( $preventCountries = [] );
    public function fetchAllPreferredGroupByCountryNetwork();
    public function getServiceProviderByCountryNetwork( $country, $network );
    public function updateServiceProvider( array $attributes, $id );
    public function importServiceProvider( $file );
    public function fetchAllCountryEnabled($allowCountries = []);
}