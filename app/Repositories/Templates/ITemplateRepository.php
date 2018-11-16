<?php

namespace App\Repositories\Templates;

use phpDocumentor\Reflection\Types\Null_;

interface ITemplateRepository {
    public function getTemplate($userId);
    public function getTemplateById($userId, $template_id);
    public function getTemplateByQuery($userId, $search_key, $sort_column, $order_by, $page);
    public function createTemplate($userId, $attribute);
    public function deleteTemplate($userId, $template_id);
    public function updateTemplate($userId, $attribute, $template_id);
}