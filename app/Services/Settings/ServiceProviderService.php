<?php

namespace App\Services\Settings;

use App\Services\BaseService;
use App\Repositories\Settings\IServiceProviderRepository;

class ServiceProviderService extends BaseService implements IServiceProviderService {
    /**
     */
    protected $serviceProviderRepo;

    /**
     *
     * @param IServiceProviderRepository $serviceProviderRepo
     */
    public function __construct(IServiceProviderRepository $IServiceProviderRepo) {
        $this->serviceProviderRepo = $IServiceProviderRepo;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Settings\IServiceProviderService::fetchAll()
     */
    public function fetchAll() {
        return $this->serviceProviderRepo->all ();
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Settings\IServiceProviderService::fetchAllOptions()
     */
    public function fetchAllOptions() {
        $allData = $this->fetchAll ();
        $results = array ();
        foreach ( $allData as $item ) {
            $results [$item->code] = $item->name;
        }
        //
        return $results;
    }

    /**
     *
     * {@inheritDoc}
     * @see \App\Services\Settings\IServiceProviderService::fetchAllConfig()
     */
    public function fetchAllConfig() {
        $allData = $this->fetchAll ();
        $results = array ();
        foreach ( $allData as $item ) {
            $results [$item->code]['config_url'] = $item->config_url;
            $results [$item->code]['config_username'] = $item->config_username;
            $results [$item->code]['config_password'] = $item->config_password;
            $results [$item->code]['config_access_key'] = $item->config_access_key;
        }
        //
        return $results;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \App\Services\Settings\IServiceProviderService::getDefaultServiceProvider()
     */
    public function getDefaultServiceProvider() {
        return $this->serviceProviderRepo->scopeQuery ( function ($query) {
            return $query->where( 'default', 1 );
        } )->first ();
    }

    public function setDefaultServiceProvider($service_provider) {
        return  $this->serviceProviderRepo->setDefaultServiceProvider($service_provider);
    }
}