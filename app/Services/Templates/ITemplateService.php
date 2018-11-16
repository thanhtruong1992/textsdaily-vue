<?php

namespace App\Services\Templates;

use Illuminate\Http\Request;

interface ITemplateService {
    public function getTemplates();
    public function getTemplateByQuery(Request $request);
    public function getTemplateByID($id);
    public function deleteTemplate($id);
    public function createTemplate($attribute);
    public function updateTemplate($attribute, $template_id);
}