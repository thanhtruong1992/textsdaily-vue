<?php

namespace App\Services\Settings;

interface ICountryService {
    public function fetchAll();
    public function fetchAllOptions();
    public function findById($id);
    public function findByCode( $code );
    public function updateCountry( array $attributes, $id );
    public function updateCountryByCode(array $attributes, $code);
}