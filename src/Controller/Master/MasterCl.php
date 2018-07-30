<?php

namespace App\Controller\Master;


class MasterController{


	protected $twig = null;
	protected $master = null;

	function __construct( $master = 'index.html'){

		$loader = new \Twig_Loader_Filesystem('/var/www/html/public/tpl');
		$this->twig = new \Twig_Environment($loader, array(
		    'cache' => '/var/www/html/public/cache',
		));	
		
	}

	protected function view(){
		$template = $twig->load('index.html');
		echo $template->render(array('the' => 'variables', 'go' => 'here'));		
	}
}