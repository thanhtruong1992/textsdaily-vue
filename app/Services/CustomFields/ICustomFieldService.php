<?php

namespace App\Services\CustomFields;

interface ICustomFieldService {
    public function createCustomField($request);
    public function getCustomFieldOfSubscriber($list_id);
    public function getCustomFieldOfSubscriberByColumn($list_id, $columnName = null);
    public function getCustomFieldOfSubscriberByColumnForPersonalize($list_id, $columnName = null, $userId= null);
}
