<?php

namespace App\Repositories\Templates;

use App\Repositories\Templates\ITemplateRepository;
use App\Repositories\BaseRepository;
use App\Models\Template;
use DB;

class TemplateRepository extends BaseRepository implements ITemplateRepository {
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model() {
        return "App\\Models\\Template";
    }

    private function changeTableName($idUser) {
        $this->__changeTableName ( array (
                'u_template' => 'u_' . $idUser 
        ) );
    }
    
    public function getTemplate($userId) {
        $template_table_name = 'template_u_' . $userId;
        $queryBuilder = DB::table ( $template_table_name );
        return $queryBuilder->get();
    }
    
    public function getTemplateById($userId, $template_id) {
        $template_table_name = 'template_u_' . $userId;
        $query = DB::table($template_table_name)->where('id', $template_id);
        return $query->first();
    }

    public function getTemplateByQuery($userId, $search_key, $sort_column, $order_by, $page) {
        $template_table_name = 'template_u_' . $userId;
        $queryBuilder = DB::table ( $template_table_name );
        
        // search keywork
        if (isset ( $search_key )) {
            $queryBuilder->where ( function ($q) use ($search_key) {
                $q  ->orWhere ( 'name', "LIKE", "%$search_key%" );
            } );
        }
        
        // sort
        if (isset ( $sort_column ) && isset ( $order_by )) {
            $queryBuilder->orderBy ( $sort_column, $order_by );
        }
        
        $templateResult = $queryBuilder->paginate (10);
        return $templateResult;
    }
    
    public function createTemplate($userId, $attributes) {
        $this->changeTableName($userId);
        return parent::create($attributes, $this->model->getTable ());
    }
    
    public function updateTemplate($userId, $attribute, $template_id) {
        $this->changeTableName($userId);
        return parent::update($attribute, $template_id, $this->model->getTable ());
    }
    
    public function deleteTemplate($userId, $template_id) {
        $template_table_name = 'template_u_' . $userId;
        $queryBuilder = DB::table ( $template_table_name )->where('id', $template_id)->delete();
        return $queryBuilder;
    }
}