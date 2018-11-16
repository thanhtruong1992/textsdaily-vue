<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Services\Templates\ITemplateService;
use App\Http\Requests\CreateTemplateRequest;
use Illuminate\Support\Facades\Lang;

class TemplateController extends Controller
{
    protected $request;
    protected $templateService;
    public function __construct(Request $request, ITemplateService $templateService){
        $this->request = $request;
        $this->templateService = $templateService;
    }
    
    public function getTemplate(Request $request) {
        $result = $this->templateService->getTemplates();
        return [
                'data' => $result,
                'recordsTotal' => count($result),
                'recordsFiltered' => count($result)
        ];
    }
    
    public function getTemplateByQuery(Request $request) {
        $result = $this->templateService->getTemplateByQuery($request);
        return $result;
    }
    
    public function store(CreateTemplateRequest $request) {
        $result = $this->templateService->createTemplate($request->all());
        if ($result) {
            Session::flash ( 'success', Lang::get('template.create_success') );
        } else {
            Session::flash ( 'error', Lang::get('template.create_failed') );
        }
        return redirect ()->route ( 'templates.index' );
    }
    
    public function info($id) {
        $result = $this->templateService->getTemplateByID($id);
        return view('admins.templates.add', ['template' => $result]);
    }
    
    public function update(CreateTemplateRequest $request, $id) {
        $result = $this->templateService->updateTemplate($request->all(), $id);
        if ($result) {
            Session::flash ( 'success', Lang::get('template.updated_success') );
        } else {
            Session::flash ( 'error', Lang::get('template.updated_failed') );
        }
        return redirect ()->route ( 'templates.index' );
    }
    
    public function delete(Request $request) {
        $result = $this->templateService->deleteTemplate($request->template_id);
        if ($result) {
            return [
                    'status' => true,
                    'message' => Lang::get('template.delete_success')
            ];
        } else {
            return [
                    'status' => false,
                    'message' => Lang::get('template.delete_failed')
            ];
        }
    }
}