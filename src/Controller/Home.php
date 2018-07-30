<?php

namespace App\Controller;

use \App\Model\Model;
use App\Config\Email;
use App\Config\GenFun;

class Home extends \App\Controller\Master\MasterController{
	

    private $AdminModel = null;

    private $IsLogged = false;

    public function __construct()
    {
        parent::__construct();
        $this->IsLogged = $this->isLogged();
        $this->AdminModel = new \App\Model\AdminModel();

	}
	
	public function init(){
		$Data = [];
		$Data['Posts'] = $this->AdminModel->getAllPostsWithImg();
        $Data['User'] = $this->returnUser();
        $Data['Comments'] = $this->AdminModel->returnCommnetsByPosts(3);
		$this->view('main_page', $Data);
    }
    
    public function handlePost(string $PostUrl){
		$Data = [];
		$Data['Posts'] = $this->AdminModel->getAllPostsWithImg( $PostUrl );
        $Data['User'] = $this->returnUser();
        $Data['Comments'] = $this->AdminModel->returnCommnetsByPosts();
        $Data['DisplayCommentForm'] = true;
        $Data['PostId'] = $this->AdminModel->getPostId();
        $Data['NumOfComments'] = $this->AdminModel->getNumOfComments( $this->AdminModel->getPostId() );
		$this->view('main_page', $Data);
    }

    public function saveComment(){
        $PostID = \filter_input(INPUT_POST, 'PostID', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $Content = \filter_input(INPUT_POST, 'Content', FILTER_SANITIZE_STRING );
        $addComments = \filter_input(INPUT_POST, 'addComments', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        // $Content = $_POST['Content'];
        if($PostID && $Content && $addComments){
            $this->AdminModel->saveComment([
                'PostID' => $PostID,
                'Content' => $Content,
                'user' => $this->returnUser()
            ]);
            echo 1;
        }
        echo 0;
        exit;
    }

    public function login(){
        $data = [];
        $data['logged'] = false;
        $data['front_end'] = true;
        $this->view('login', $data);        
    }

    public function signin(){
        $data = [];
        $data['title'] = "Please register";
        $this->view('signin', $data);       
    }

    public function checkEmail(){
        $Email = \filter_input(INPUT_POST, 'Email', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        if($Email){
            if(!$this->AdminModel->emailExists( $Email )){
                echo 1;
            }else{
                echo 0;
            }
            exit();
        }
        return false;
    }

    public function Register(){
        $Email = \filter_input(INPUT_POST, 'Email', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $Pass = \filter_input(INPUT_POST, 'Pass', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH); 
        if ($Email && $Pass) {
            $this->AdminModel->insertUser($Email, hash('whirlpool', $Pass));
            $Login = $this->AdminModel->Login($Email, $Pass);
            if ($Login) {
                $_SESSION["user"] = $Email;
                $_SESSION["loggedAt"] = date("Y-m-d H:i:s");
                $_SESSION["logged"] = true;
                echo 1;
            } else {
                echo 0;
            }   
            exit();
        } 
        echo 0;     
    }

    public function LogOut()
    {
        session_unset();
        session_destroy();
        GenFun::redirect('/');
    }

}