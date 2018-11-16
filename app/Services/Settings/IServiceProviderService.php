<?php

namespace App\Services\Settings;

interface IServiceProviderService {
    public function fetchAll();
    public function fetchAllOptions();
    public function fetchAllConfig();
    public function getDefaultServiceProvider();
    public function setDefaultServiceProvider($service_provider);
}