<?php

declare(strict_types=1);

namespace PWP\includes\editor;

use PWP\includes\editor\Editor_Project;
use PWP\includes\utilities\response\I_Response;

class PIE_Project extends Editor_Project
{
    private Product_PIE_Data $editorData;
    private string $projectId; 

    
    public function __construct(Product_PIE_Data $data, string $projectId )
    {
        $this->editorData = $data;
        $this->projectId = $projectId; 
        parent::__construct(Product_PIE_Data::MY_EDITOR, $projectId);
    }

    public function get_project_editor_url(bool $skipUpload = false,  $organisation_apikey = ''): string
    {
        $id = $this->get_project_id();
        if(isset($_GET['organisationid'])){
            $key = $organisation_apikey;
        }else{
            $key =  get_option('pie_api_key');
        }
        $params = array();
        if ($skipUpload || !$this->editorData->uses_image_upload())
            $params['skip'] = 'true';

        $params = array_merge($params, $this->editorData->get_editor_params());

        $params['lang'] = $this->get_editor_lang();
		$params['a'] =  $key;
		    


       
        $url = apply_filters('pwp_generate_pie_project_url', '', $id, $params);

        return $url;

    }

    
    private function get_editor_lang(): string
    {
        if (defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE) {
            return ICL_LANGUAGE_CODE;
        }
        return explode("_", get_locale())[0];
    }
	

}
