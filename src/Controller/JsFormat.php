<?php

namespace App\Controller;

use App\Config\GenFun as GenFun;

class JsFormat extends \App\Controller\Master\MasterController
{

    function init(){
        $data = [];
        if(isset($_POST['text_to_js_var'])){
            $data['text_to_js_var'] = self::ConverttextToJsVars($_POST['pasteTextHere']);
        
        }
        $this->view('JsFormat', $data);
    }


    private static function ConverttextToJsVars($str){
        $regexval = '/^(.*?)\n/';
        $out = "var html = ''; \n";
        $split = preg_split('/\n/', $str);
        foreach($split as $s){
            $out .= preg_replace('/^(.+)$/', "html += '$1'; \n", trim($s));
        }
        return trim($out);
    }
}