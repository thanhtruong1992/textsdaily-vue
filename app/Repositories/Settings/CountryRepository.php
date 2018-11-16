<?php

namespace App\Repositories\Settings;

use Prettus\Repository\Eloquent\BaseRepository;

class CountryRepository extends BaseRepository implements ICountryRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\Country";
    }
}