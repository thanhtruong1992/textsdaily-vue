<?php

namespace App\Services\Templates;

use App\Repositories\Templates\ITemplateRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class TemplateService extends BaseService implements ITemplateService {
    protected $temlateRepo;
    public function __construct(ITemplateRepository $templateRepo) {
        $this->temlateRepo = $templateRepo;
    }
    
    public function getTemplates() {
        $userId = Auth::user()->id;
        $result = $this->temlateRepo->getTemplate($userId);
        return $result;
    }
    
    public function getTemplateByQuery(Request $request) {
        $userId = Auth::user()->id;
        $paging = $request->get ( 'page' );
        $search_key = $request->get ( 'search' );
        $column_sort = $request->get ( 'field' );
        $orderBy = $request->get ( 'orderBy' );
        $results = $this->temlateRepo->getTemplateByQuery($userId, $search_key, $column_sort, $orderBy, $paging);
        $dataList = $results->items ();
        foreach ($dataList as $template) {
            $template->created_at = date(config('app.datetime_format'), strtotime($template->created_at));
            $template->updated_at = date(config('app.datetime_format'), strtotime($template->updated_at));
        }
        return [
                'data' => $dataList,
                'recordsTotal' => $results->total (),
                'recordsFiltered' => $results->total (),
                'total' => $results->total ()
        ];
    }
    
    public function createTemplate($attribute) {
        $user_id = Auth::user()->id;
        $attribute['created_by'] = $user_id;
        $attribute['updated_by'] = $user_id;
        $result = $this->temlateRepo->createTemplate($user_id, $attribute);
        return $result ? true : false;
    }
    
    public function updateTemplate($attribute, $template_id) {
        $user_id = Auth::user()->id;
        $attribute['updated_by'] = $user_id;
        $result = $this->temlateRepo->updateTemplate($user_id, $attribute, $template_id);
        return $result ? true : false;
    }
    
    public function getTemplateByID($id) {
        $user_id = Auth::user()->id;
        $template = $this->temlateRepo->getTemplateById($user_id, $id);
        return $template;
    }
    
    public function deleteTemplate($id) {
        $user_id = Auth::user()->id;
        $result = $this->temlateRepo->deleteTemplate($user_id, $id);
        return $result;
    }
}