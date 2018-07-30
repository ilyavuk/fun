<?php

namespace App\Controller;

use App\Config\GenFun as GenFun;

class Comment extends \App\Controller\Master\MasterController
{

    private $AdminModel = null;

    private $IsLogged = false;

    public function __construct()
    {
        parent::__construct();
        $this->IsLogged = $this->isLogged();
        $this->AdminModel = new \App\Model\AdminModel();
    }

}