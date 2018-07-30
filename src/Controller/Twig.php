<?php

namespace App\Controller;

use \App\Model\Model;

class Twig{
	
	function __construct(){

		$loader = new \Twig_Loader_Filesystem('/var/www/html/public/tpl');
		$twig = new \Twig_Environment($loader, array(
		    'cache' => '/var/www/html/public/cache',
		));	
		
		$template = $twig->load('index.html');
		echo $template->render(array('the' => 'variables', 'go' => 'here'));	
	}

}