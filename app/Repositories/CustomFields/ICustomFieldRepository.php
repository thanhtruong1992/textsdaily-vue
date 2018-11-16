<?php

namespace App\Repositories\CustomFields;

use phpDocumentor\Reflection\Types\Null_;

interface ICustomFieldRepository {
    public function addCustomfield($tableName, $columnName);
    public function getCusfomFieldOfSubscriber($listId, $userId = null);
    public function getCusfomFieldOfSubscriberByColumnForPersonalize($userId, array $listId, array $columnName = null);
    public function getCusfomFieldOfSubscriberByColumn($userId, array $listId, array $columnName = null);
}