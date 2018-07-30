<?php

namespace App\Controller;

class Test extends \App\Controller\Master\MasterController
{

    public function init(){
        $this->view('test');
    }
}
