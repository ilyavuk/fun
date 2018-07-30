<?php

namespace App\Model;

use App\Model\Db\Database;
use App\Config\GenFun;

class AskModel
{

    private $Db = null;

    public function __construct()
    {
        if (is_null($this->Db)) {
            $this->Db = Model::Db('App')->getDB(); 
        }
    }

    public function saveData($d)
    {
        $sql = "INSERT INTO `App`.`Content` (`Title`, `Content`) VALUES ({$this->e($d['Title'])}, {$this->e($d['Content'])}); ";
        $id = $this->Db->InsertRow($sql, true);
        $this->insertImages($d, $id);

    }

    public function updateData($d)
    {
        // First delete images, than insert again
        $sql = "delete from App.Img where parent_id = {$d['id']} ";
        $this->Db->DeleteRow($sql);

        $sql = "update App.Content set `Title` = {$this->e($d['Title'])}, `Content` = {$this->e($d['Content'])} where id = {$d['id']}  ";
        $this->Db->UpdateRow($sql);

        $this->insertImages($d, $d['id']);
    }

    private function insertImages($d, $id)
    {
        if (!empty($d['img'])) {
            foreach ($d['img'] as $img) {
                $sql = "INSERT INTO `App`.`Img` (`parent_id`, `name`) VALUES ($id, {$this->e($img)});";
                $this->Db->InsertRow($sql);
            }
        }
    }

    public function search($d, $id = false)
    {
        $title = $cont = '';

        if(!empty($d['Title'])) $title = trim($d['Title']);
        if (!empty($title)) {
            $title = \preg_replace('/\s+/', ' ', $title);
            $title = \preg_split('/\s+/', $title);
        }

        if(!empty($d['Content'])) $cont = trim($d['Content']);
        if (!empty($cont)) {
            $cont = \preg_replace('/\s+/', ' ', $cont);
            $cont = \preg_split('/\s+/', $cont);
        }
        $sql = "select
                c.id, c.Title, c.Content, i.name as ImgName, i.id as ImgId
                from Content c left join Img i on i.parent_id = c.id where 1 = 1 ";

        $sqlTitle = $sqlDesc = '';
        if ($id) {
            $sql .= "and c.id = $id ";
        } else {
            if (!empty($title)) {
                foreach ($title as $t) {
                    $sqlTitle .= " and lower(c.Title) like lower('%{$t}%') ";
                }
                $sqlTitle = preg_replace('/^ and/', ' ', $sqlTitle);
                $sqlTitle = "( $sqlTitle )";
            }
            if (!empty($cont)) {
                foreach ($cont as $c) {
                    $sqlDesc .= " and lower(c.Content) like lower('%{$c}%') ";
                }
                $sqlDesc = preg_replace('/^ and/', ' ', $sqlDesc);
                $sqlDesc = "( $sqlDesc )";
            }
            if (!empty($sqlTitle) && !empty($sqlDesc)) {
                $sql .= "and ($sqlTitle or $sqlDesc) ";
            } else {
                $sql .= "and ($sqlTitle $sqlDesc) ";
            }
        }

        $data = $this->Db->SelectRows($sql, true);

        if (!empty($data)) {
            $out = [];
            foreach ($data as $v) {
                $out[$v['id']]['id'] = $v['id'];
                $out[$v['id']]['Title'] = $v['Title'];
                $out[$v['id']]['Content'] = $v['Content'];
                if (!empty($v['ImgId'])) {
                    $out[$v['id']]['Img'][$v['ImgId']] = $v['ImgName'];
                }
            }

            if ($id) {
                return $out[$id];
            }

            return $out;

        }
        return false;
    }

    public function deleteItem($id){
        $sql = "select * from App.Img where parent_id = $id ;";
        $D = $this->Db->SelectRows($sql, true);
        if(!empty($D)){
            foreach($D as $d){
                $imgPath = GenFun::rootPath().'/uploads/'. str_replace('_dirsep_','/',$d['name']);
                @unlink($imgPath);
            }
        }

        $sql = "delete from App.Img where parent_id = $id ";
        $this->Db->DeleteRow($sql);

        $sql = "delete from App.Content where id = $id";
        $this->Db->DeleteRow($sql);
    }

    private function e($d)
    {
        return $this->Db->EscapeField($d);
    }
}
