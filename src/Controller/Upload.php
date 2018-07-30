<?php

namespace App\Controller;

use App\Config\GenFun as GenFun;

class Upload extends \App\Controller\Master\MasterController
{

    private $AdminModel = null;

    public function __construct()
    {
        parent::__construct();
        $this->IsLogged = $this->isLogged();
        $this->AdminModel = new \App\Model\AdminModel();
    }
    
    private function upload(){
        $imageFileType = strtolower(pathinfo(basename($_FILES["file"]["name"]),PATHINFO_EXTENSION));
        $data = [];

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $data['Error'] = "Err: Sorry, only JPG, JPEG, PNG & GIF files are allowed, wrong ext. for ".$_FILES["file"]["name"];
            return $data;
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

        $data['img'] = $folder."_dirsep_".$file_name;
        $data['width'] = $width;
        $data['height'] = $height;
        return $data;        
    }

    public function uploadAvatar(){
        $data = $this->upload();
        if(!empty($data['img'])){
            $this->AdminModel->updateAvatar($data['img'], $this->returnUser());
            echo \json_encode($data);
            exit();
        }
    }

    public function post(){
        $data = $this->upload();
        if(!empty($data['img'])){
            echo \json_encode($data);
            exit();
        }        
    }
}
