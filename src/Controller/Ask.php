<?php

namespace App\Controller;

use App\Config\GenFun as GenFun;

class Ask extends \App\Controller\Master\MasterController
{

    private $AskModel = null;

    public function __construct()
    {
        parent::__construct();
        $this->AskModel = new \App\Model\AskModel();
    }


    public function init()
    {
        $data = [];
        if(!empty($_GET['edit'])){
            $data['edit'] = $this->AskModel->search($_POST, $_GET['edit']);
        }
        $this->view('ask', $data);
    }

    public function upload()
    {

        $imageFileType = strtolower(pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION));

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Err: Sorry, only JPG, JPEG, PNG & GIF files are allowed, wrong ext. for ".$_FILES["file"]["name"];
            die();
        }

        $target_dir = "/var/www/html/uploads/";
        $folder = date('d_m_Y', strtotime('now'));
        $full_path_to_dir = $target_dir.$folder;
        if(!file_exists($full_path_to_dir)){
            mkdir($full_path_to_dir, 0777);
        }
        $file_name = GenFun::random_string() . "_" . strtotime('now').".{$imageFileType}";
        $target_file = $full_path_to_dir ."/".$file_name;
        $uploadOk = 1;

        move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
        
        list($width, $height) = getimagesize($target_file);

        $data = [];
        $data['img'] = $folder."_dirsep_".$file_name;
        $data['width'] = $width;
        $data['height'] = $height;

        echo \json_encode($data);
        die();
    }

    public function save(){
        if(isset($_POST['Title'])){
            $this->AskModel->saveData($_POST);
            echo 1;
            return;
        }      
    }

    public function update(){
        if(isset($_POST['id'])){
            $this->AskModel->updateData($_POST);
            echo 1;
            return;
        }      
    }

    public function delete(){
        if(isset($_POST['del_id'])){
            $this->AskModel->deleteItem($_POST['del_id']);
            echo 1;
            return;
        }
    }
    /**
     * Get results from search
     *
     * @return void
     */
    public function search(){
        if(isset($_POST['Title'])){           
            echo json_encode( $this->AskModel->search($_POST) );
            return;
        }
        
    }



}
