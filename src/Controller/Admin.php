<?php

namespace App\Controller;

use App\Config\GenFun as GenFun;

class Admin extends \App\Controller\Master\MasterController
{

    private $AdminModel = null;

    private $IsLogged = false;

    public function __construct()
    {
        parent::__construct();
        // print_r($_SESSION);
        $this->IsLogged = $this->isLogged();
        $this->AdminModel = new \App\Model\AdminModel();

    }

    public function init()
    {
        if (!$this->IsLogged) {
            $data = [];
            $data['logged'] = false;
            $data['navLink'] = 'admin';
            $this->view('login', $data);
        } else {
            $data = [];
            $data['logged'] = true;
            $data['user'] = $this->returnUser();
            $this->asignNav('personal_data');
            $this->view('admin', $data);
        }
    }

    public function AddPost()
    {
        if (!$this->IsLogged) {
            GenFun::redirect('/admin');
        }

        $data = [];

        $PostID = \filter_input(INPUT_POST, 'PostID', FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^\d{1,10}$/")));
        if ($PostID) {
            $PostData = $this->AdminModel->getPostByID($this->returnUser(), $PostID);
            $data["Action"] = 'edit';
            $data["PostData"] = $PostData;
            // print_r($data);
        } else {
            $data["Action"] = 'add';
        }

        $this->asignNav('posts');
        $this->view('admin_post', $data);
    }

    public function MyPosts()
    {
        if (!$this->IsLogged) {
            GenFun::redirect('/admin');
        }

        $data = [];
        $data['myPosts'] = $this->AdminModel->getMyPosts($this->returnUser());
        // Starting Here
        $this->asignNav('posts');
        $this->view('my_post', $data);
    }

    public function UpdateUser()
    {
        if (!$this->IsLogged) {
            GenFun::redirect('/admin');
        }
        $Email = \filter_input(INPUT_POST, 'Email', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $Pass = \filter_input(INPUT_POST, 'Password', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        if ($Email && $Pass) {
            $this->AdminModel->UpdateUser($this->returnUser(), $Email, $Pass);
            echo 1;
            exit();
        }
        echo 0;
        exit();
    }

    public function DeletePost()
    {
        $ID = \filter_input(INPUT_POST, 'removePostID', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        if ($ID) {
            $Result = $this->AdminModel->DeletePost($this->returnUser(), $ID);
            if ($Result) {
                echo 1;
                exit();
            }
        }
    }

    public function CreatePost()
    {
        $Title = \filter_input(INPUT_POST, 'Title', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $Desc = \filter_input(INPUT_POST, 'Desc', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $imgPath = \filter_input(INPUT_POST, 'imgPath', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $imgVisible = \filter_input(INPUT_POST, 'imgVisible', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
        $ID = \filter_input(INPUT_POST, 'UpdateID', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $Display = \filter_input(INPUT_POST, 'PostVisible', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $Url = \filter_input(INPUT_POST, 'Url', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

        if (!$Title) {
            $Title = '';
        }

        if (!$Desc) {
            $Desc = '';
        }

        if(!$Display){
            $Display = 0;
        }

        $Result = $this->AdminModel->SavePost($this->returnUser(), [
            'Title' => $Title,
            'Desc' => $Desc,
            'imgPath' => $imgPath,
            'imgVisible' => $imgVisible,
            'Url' => $Url,
            'ID' => $ID,
            'Display' => $Display,
        ]);

        if ($Result) {
            echo 1;
            exit();
        }
    }

    public function Login()
    {
        // \filter_input(INPUT_GET, 'catids', FILTER_VALIDATE_REGEXP, array("options" => array("regexp" => "/^\[[0-9,]+\]$/")));
        $Email = \filter_input(INPUT_POST, 'Email', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        $Pass = \filter_input(INPUT_POST, 'Pass', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

        if ($Email && $Pass) {
            $Login = $this->AdminModel->Login($Email, $Pass);
            if ($Login) {
                $_SESSION["user"] = $Email;
                $_SESSION["loggedAt"] = date("Y-m-d H:i:s");
                $_SESSION["logged"] = true;
                echo 1;
            } else {
                echo 0;
            }
        }
    }

    public function LogOut()
    {
        session_unset();
        session_destroy();
        GenFun::redirect('/admin');
    }

    private function createBasicUser()
    {
        $this->AdminModel->insertUser('Admin', hash('whirlpool', 'Sample1234'));
    }

}
