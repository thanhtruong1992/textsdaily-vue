<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['validator']->extend('emailmultiple', function ($attribute, $value, $parameters)
        {
            if(strlen($value) > 0) {
                $regex = "/[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+.[a-zA-Z]+/";
                $arrEmail = explode(";", $value);
                for($i = 0; $i < count($arrEmail); $i++) {
                    $email = $arrEmail[$i];
                    if(preg_match_all($regex, $email) == 0) {
                        return false;
                    }
                }
            }

            return true;
        });

        $this->app['validator']->extend('stringOrArray', function ($attribute, $value, $parameters)
        {
            if(!!is_string($value) || !!is_array($value)) {
                // valid data
                return true;
            }

            // invalid data
            return false;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
