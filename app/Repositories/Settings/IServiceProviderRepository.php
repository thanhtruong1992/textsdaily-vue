<?php

namespace App\Repositories\Settings;

interface IServiceProviderRepository {
    public function setDefaultServiceProvider($service_provider);
}