<?php

namespace App\Services\Settings;

use App\Services\BaseService;
use App\Repositories\Settings\ICountryRepository;

class CountryService extends BaseService implements ICountryService {
    /**
     */
    protected $countryRepo;

    /**
     *
     * @param IcountryRepository $IcountryRepo
     */
    public function __construct(ICountryRepository $IcountryRepo) {
        $this->countryRepo = $IcountryRepo;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\ICountryService::fetchAll()
     */
    public function fetchAll() {
        return $this->countryRepo->all ();
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\ICountryService::fetchAllOptions()
     */
    public function fetchAllOptions(){
        $allData = $this->fetchAll();
        $results = [];
        foreach ( $allData as $item ) {
            $results[$item->code] = $item->name;
        }
        return $results;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\ICountryService::findById()
     */
    public function findById($id) {
        return $this->countryRepo->find($id);
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\ICountryService::findByCode()
     */
    public function findByCode( $code ) {
        return $this->countryRepo->findByField('code', $code)->first();
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\ICountryService::updateCountry()
     */
    public function updateCountry(array $attributes, $id) {
        return $this->countryRepo->update ( $attributes, $id );
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\ICountryService::updateCountryByCode()
     */
    public function updateCountryByCode(array $attributes, $code) {
        $country = $this->findByCode($code);
        return $this->countryRepo->update($attributes, $country->id);
    }
}