<?php

namespace App\Repositories\Settings;

interface IInboundConfigRepository {
    public function getDataTableList($keyword, $orderBy = 'expiry_date', $orderDirection = 'ASC');
}