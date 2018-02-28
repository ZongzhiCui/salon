<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/2/3
 * Time: 22:05
 */

class HomeModel extends Model
{
    public function getIndex(){
        $page_size = $field['page_size']??4;
        //>>计算count totalPage
        $sql = "select count(id) from `article`";
        $count = $this->pdo->fetchColumn($sql);
        $total_page = ceil($count/$page_size);

        //>>开始页和每页条数
        $page = intval($field['page']??1);
        $page = $page<1?1:$page;
        $page = $page>$total_page?$total_page:$page;
        $start = ($page-1)*$page_size;
        $limit = " limit {$start},{$page_size}";
        $sql = "select * from article ORDER BY id DESC ".$limit;
        $rows = $this->pdo->fetchAll($sql);
        $html = PageTool::myYeMa($page,$page_size,$total_page);
        return [
            'rows'=>$rows,
            'html'=>$html
        ];
    }

    public function getEdit($field){
        if ($field['which'] == 'a_username'){
            $sql = "update `user` set username='{$field['username']}' where id={$field['id']}";
            $r = $this->pdo->execute($sql);
            if ($r === false){
                $this->error = '修改数据失败!';
                return false;
            }
            @session_start();
            $_SESSION['admin']['username'] = $field['username'];
        }elseif ($field['which'] == 'a_head'){
            @session_start();
            $unlink = $_SESSION['admin']['head'];
            $file = $_FILES['head'];
            $upload = new UploadTool();
            $logo = $upload->upload($file,'Admin/');
            if ($logo === false){
                $this->error = $upload->getError();
                return false;
            }
            //微略图
            $thumb = new ImageTool();
            $thumb_logo = $thumb->thumb($logo,100,80);
            if ($thumb_logo === false){
//                Tools::jump("index.php?c=Home&a=home",$thumb->getError(),3);
                $this->error = $upload->getError();
                return false;
            }
            unlink(__DIR__.'/../'.$logo);
            $field['head'] = $thumb_logo;
            //这里删除修改前的那些图片------------------------------
            if (!empty($unlink)){
                unlink(__DIR__.'/../'.$unlink['head']);
            }
            //写入数据库头像路径
            $sql = "update `user` set head='{$field['head']}' where id={$field['id']}";
            $r = $this->pdo->execute($sql);
            if ($r === false){
                $this->error = '修改数据失败!';
                return false;
            }
            $_SESSION['admin']['head'] = $field['head'];
        }
    }
}