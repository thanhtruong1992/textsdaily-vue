<?php

namespace App\Repositories\Settings;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;

class ServiceProviderRepository extends BaseRepository implements IServiceProviderRepository {

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\ServiceProvider";
    }
    
    public function setDefaultServiceProvider($service_provider) {
//         dd($service_provider);
        $default_provider = DB::table('service_provider')->where('default', 1)->update(['default' => 0]);
        $selected_provider = DB::table('service_provider')->where('code', $service_provider)->update(['default' => 1]);
        
        return true;
    }
}