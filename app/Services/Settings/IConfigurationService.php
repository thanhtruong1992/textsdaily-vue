<?php

namespace App\Services\Settings;

interface IConfigurationService {
    public function fetchConfiguration();
    public function updateConfiguration(array $attributes);
    public function autoTriggerReport();
    public function sendEmailReport();
}