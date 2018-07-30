<?php

namespace App\Model;

use App\Model\Model;
use App\Config\GenFun;
use Carbon\Carbon;

class AdminModel
{


    private $Db = null;
    private $Posts = [];
    private $NumOfComments = [];
    private $PostId = [];

    public function __construct()
    {
        $this->Db = Model::Db('Fun')->getDB();        
    }
    
    public function insertUser(string $Email, string $Pass):void{

        $sql = "insert into User (Email,Pass,CreatedAt) values ( {$this->e($Email)}, {$this->e($Pass)}, now() ) ";
        $this->Db->InsertRow($sql);
    }    


    public function Login(string $Email, string $Pass):bool{
        if(empty($Email) || empty($Pass) ) return false;

        $sql = "select * from User where Email = {$this->e($Email)} and Pass = ". $this->e( hash( 'whirlpool', $Pass) );
        // GenFun::log_data($sql);
        $Data = $this->Db->SelectRow($sql, true);

        if(!empty($Data)) { 
            unset($Data['Pass']);          
            $_SESSION['user_data'] = $Data;
            return true;
        }
        else return false;
        
    }

    public function UpdateUser(array $User, string $Email, string $Pass){
        $sql = "update User set Email = {$this->e($Email)},  Pass = ". $this->e( hash( 'whirlpool', $Pass) )." where ID = {$User['user_data']['ID']}";
        $this->Db->UpdateRow($sql);
    }

    public function updateAvatar($imgName, $user){
        $sql = "update `User` set Avatar = {$this->e($imgName)} where ID = {$user['user_data']['ID']} ";
        $this->Db->UpdateRow($sql);
        $_SESSION['user_data']['Avatar'] = $imgName;
    }

    public function SavePost(array $User, array $Data ):bool{
        extract( $Data );
        if(!$ID){
            $sql = "INSERT INTO `Fun`.`Post` (`Title`, `Desc`, `Display`, `Url`, `UserID`, `UpdatedAt`, `CreatedAt`) VALUES ( {$this->e($Title)}, {$this->e($Desc)}, $Display, {$this->e($Url)}, {$User['user_data']['ID']}, now(), now()) ";  
            $ID = $this->Db->InsertRow($sql, true); 
        }else{
            $sql = " UPDATE `Fun`.`Post` SET `Title`={$this->e($Title)}, `Desc` = {$this->e($Desc)}, `UpdatedAt` = now(), `Display` = $Display  WHERE  `ID`= $ID ; ";
            // GenFun::log_data($sql);
            $this->Db->UpdateRow($sql, true); 
            $sql = "delete from PicturePost where PostID = $ID ";
            $this->Db->DeleteRow($sql, true); 
        }

        if(!empty($imgPath)){
            foreach($imgPath as $k=>$v){
                $sql = "INSERT INTO `Fun`.`PicturePost` (`ImgPath`, `PostID`, `Display`, `CreatedAt`) VALUES ({$this->e($v)}, $ID , {$this->e($imgVisible[$k])}, now());";
                $this->Db->InsertRow($sql);
            }
        }
        return true;
    }
    
    public function getMyPosts( array $User):array{
        $sql = "SELECT p.ID,
                        p.Title,
                        p.`Desc`,
                        p.Display,
                        p.CreatedAt
                FROM `User` u
                INNER JOIN Post p ON p.UserID = u.ID
                WHERE u.ID = {$User['user_data']['ID']} order by p.CreatedAt desc";
        $Data = $this->Db->SelectRows($sql, true);
        if(empty($Data)){
            return [];
        }else{
            foreach($Data as $k=>$v){
                $Data[ $k ]['Desc'] =  urldecode( $v['Desc'] );
                $Data[ $k ]['Display'] =  ( $v['Display'] == 1 )?'Visible':'Hidden';
            }
            return $Data;
        }
    }

    public function DeletePost(array $User, int $id ):bool{
        $Data = $this->getPostByID( $User, $id);
        if(!empty($Data)){
            if(!empty($Data['Img'])){
                foreach($Data['Img'] as $i){
                    $imgPath = GenFun::rootPath().'uploads/'.str_replace('_dirsep_','/', $i['path']);
                    @unlink($imgPath);
                    $sql = "delete from PicturePost where PostID = $id ";
                    $this->Db->DeleteRow($sql);
                }
            }
            $sql = "delete from Post where ID = $id ";
            $this->Db->DeleteRow($sql);            
            return true;
        }
        return false;
    }

    public function getPostByID(  array $User, int $id ):array{
        $sql = "SELECT p.ID AS PostID,
                        p.Title,
                        p.`Desc`,
                        p.Display AS PostDisplay,
                        pp.ImgPath,
                        pp.Display AS ImgDisplay
                FROM `User` u
                LEFT JOIN Post p ON p.UserID = u.ID
                LEFT JOIN PicturePost pp ON pp.PostID = p.ID
                WHERE u.ID = {$User['user_data']['ID']} 
                AND p.ID = $id";
        $Data = $this->Db->SelectRows($sql, true);
        if(empty($Data)){
            return [];
        }else{
            $out = [];
            foreach($Data as $k=>$v){
                $out['ID'] = $v['PostID'];
                $out['Title'] = $v['Title'];
                $out['Display'] = $v['PostDisplay'];
                $out['Desc'] = urldecode( $v['Desc'] );
                if (!empty($v['ImgPath'])) {
                    $out['Img'][] = [
                        'path' => $v['ImgPath'],
                        'display' => $v['ImgDisplay']
                    ];
                }
            }
            return $out;        
        }
    }

    public function getAllPostsWithImg($Url = false){
        $sql = "SELECT p.ID AS PostID,
                        p.Title,
                        p.Url,
                        p.`Desc`,
                        p.Display AS PostDisplay,
                        pp.ImgPath,
                        pp.Display AS ImgDisplay,
                        u.Email,
                        u.Avatar,
                        p.CreatedAt
                FROM `User` u
                LEFT JOIN Post p ON p.UserID = u.ID
                LEFT JOIN PicturePost pp ON pp.PostID = p.ID
                where p.Display = 1 and pp.Display = 1 
                ";
        if($Url){
            $sql .= " and p.Url = {$this->e($Url)} ";
        }else{
            $sql .= " order by p.CreatedAt desc ";
        }
        $Data = $this->Db->SelectRows($sql, true);
        if(empty($Data)){
            return [];
        }else{
            $out = [];
            if($Url){
                $this->PostId = $Data[0]['PostID'];
            }
            foreach($Data as $k=>$v){
                $out[$v['PostID']]['ID'] = $v['PostID'];
                $this->Posts[] = $v['PostID'];
                $out[$v['PostID']]['Url'] = $v['Url'];
                $out[$v['PostID']]['Title'] = $v['Title'];
                $out[$v['PostID']]['Display'] = $v['PostDisplay'];
                $out[$v['PostID']]['Email'] = $v['Email'];
                $out[$v['PostID']]['Avatar'] = $v['Avatar'];
                $out[$v['PostID']]['CreatedAt'] = $v['CreatedAt'];
                Carbon::setLocale('sr');
                $out[$v['PostID']]['FormatedDate'] = Carbon::createFromTimeString( $v['CreatedAt'] )->diffForHumans();
                $out[$v['PostID']]['Desc'] = urldecode( $v['Desc'] );
                if (!empty($v['ImgPath'])) {
                    $out[$v['PostID']]['Img'][] = [
                        'path' => $v['ImgPath'],
                        'display' => $v['ImgDisplay']
                    ];
                }            
            }
            return $out;            
        }
    }

    public function getPostId(){
        return $this->PostId;
    }

    public function returnCommnetsByPosts($limit = 10):array{
        if(!empty($this->Posts)){
            $out = [];
            foreach($this->Posts as $postid){
                $sql = "select c.*, u.ID as UserId, u.Email, u.Avatar, count(*) as commentsCount from `Comment` c left join `User` u on u.ID = c.UserID where c.PostID = $postid group by c.ID order by c.CreatedAt desc limit $limit ";
                $Data = $this->Db->SelectRows($sql, true);
                if($Data){                                       
                    foreach($Data as $d){
                        $d['Content'] = htmlspecialchars_decode( $d['Content'] );
                        $d['FormatedDate'] = Carbon::createFromTimeString( $d['CreatedAt'], 'Europe/Belgrade' )->diffForHumans();
                        $out[ $d['PostID'] ][ ] = $d;
                    }
                    
                }
            }
            // print_r( $out );
            // die();
            return $out;
        }

        return [];
    }
    /**
     * Temp solution 
     *
     * @param boolean $postid
     * @return void
     */
    public function getNumOfComments($postid = false){
        if($postid){
            $sql = "select count(*) as num from `Comment` c where c.PostID = $postid";
            $Data = $this->Db->SelectRow($sql, true);
            if($Data){
                return (int) $Data['num'];
            }
        }else{
            return $this->NumOfComments;
        }
    }

    public function emailExists( string $email ):bool{
        $sql = "select 1 from `User` where `Email` = {$this->e($email)} ";
        GenFun::log_data($sql);
        $Data = $this->Db->SelectRow($sql, true);
        if(empty($Data)) return false;
        return true;
    }

    public function saveComment(array $d){
        extract($d);
        $userid = isset($user['user_data']['ID'])?$user['user_data']['ID']:"null";
        $sql = "INSERT INTO `Fun`.`Comment` (`Content`, `PostID`, `UserID`, `CreatedAt`, `Display`) VALUES ({$this->e($Content)}, {$PostID}, {$userid}, now(), 1);";
        $this->Db->InsertRow($sql);
    }

    private function e($d)
    {
        return $this->Db->EscapeField($d);
    }
}