<?php


namespace App\Model;

use App\Model\Db\Database;


class Model{
	
	private static $_instance;

	private $Db;

	private function __construct($Db){

		$this->Db = new Database($Db, [] );
	
	}

	public static function Db($Db) {
		if(!self::$_instance) { 
			self::$_instance = new self($Db);
		}
		return self::$_instance;
	}

	public function getDB(){
		return $this->Db;
	}
}


