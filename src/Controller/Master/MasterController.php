<?php

namespace App\Controller\Master;

class MasterController
{

    protected $twig = null;
    protected $master = null;

    public function __construct()
    {

        $loader = new \Twig_Loader_Filesystem('/var/www/html/public/tpl');
        $this->twig = new \Twig_Environment($loader, array(
            // 'cache' => '/var/www/html/public/cache',
            'cache' => false,
            'debug'=> true
        ));

        // turn on debug option
        $this->twig->addExtension(new \Twig_Extension_Debug());

        $this->twig->addGlobal('activeNav', '');
        // $this->twig->addFunction(new \Twig_SimpleFilter('pp', function ($string) {
        //      print_r($string);
        // })); 

        if($this->isLogged()){
            if(empty($_SESSION['user_data']['Avatar'])) $_SESSION['user_data']['Avatar'] = 'custom_dirsep_default-avatar.jpg';
            $this->twig->addGlobal('userg', $_SESSION);
        }      
	}

    protected function asignNav(string $data):void{
        $this->twig->addGlobal('activeNav', $data);
    }

    /**
     * view
     * @return void 
     */
    protected function view( $template, $data = [] )
    {
        echo $this->twig->render($template.'.html',  $data);
    }

    /**
     * Return is user is logged in
     *
     * @return boolean
     */
    protected function isLogged():bool{
        if(!empty($_SESSION["logged"])) return true;
        return false;
    }

    protected function returnUser(){
        if($this->isLogged()){
            return $_SESSION;
        }else{
            return [];
        }
    }
 
}
