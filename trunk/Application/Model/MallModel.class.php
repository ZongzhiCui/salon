<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/3
 * Time: 14:26
 */

class MallModel extends Model
{
    public  function getAdminIndex(){
        $sql = "select * from mall order by id desc";
        $mall = $this->pdo->fetchAll($sql);

        return [
            'mall'=>$mall,
        ];
    }
    public function getAdminAdd($field){
        if ($_FILES['logo']['error'] != 4){
            $file = $_FILES['logo'];
            $upload = new UploadTool();
            $logo = $upload->upload($file,'Mall/');
            if ($logo === false){
                $this->error = $upload->getError();
                return false;
            }
            $imagetool = new ImageTool();
            $thumb_logo = $imagetool->thumb($logo,340,340);
            if ($thumb_logo === false){
                $this->error = $imagetool->getError();
                return false;
            }
            $field['logo'] = $thumb_logo;
            unlink($logo);
        }
        $sql = Tools::myInsert('Mall',$field);
        $num = $this->pdo->execute($sql);
        if ($num === false){
            $this->error = '添加数据失败!';
            return false;
        }
        return $num;
    }
    public function getEdit($id){
        $sql = "select * from mall where id={$id}";
        return $this->pdo->fetchRow($sql);
    }
    public function getEdit_save($field){
        if ($_FILES['logo']['error'] != 4){
            $file = $_FILES['logo'];
            $upload = new UploadTool();
            $logo = $upload->upload($file,'Mall/');
            if ($logo === false){
                $this->error = $upload->getError();
                return false;
            }
            $imagetool = new ImageTool();
            $thumb_logo = $imagetool->thumb($logo,340,340);
            if ($thumb_logo === false){
                $this->error = $imagetool->getError();
                return false;
            }
            $field['logo'] = $thumb_logo;
            unlink($logo);
        }
        $sql = Tools::myUpdate('Mall',$field);
        $num = $this->pdo->execute($sql);
        if ($num === false){
            $this->error = '修改数据失败!';
            return false;
        }
        return $num;
    }

    /**
     * 前台页面模型
     */
    public function getIndex($field){
        $where = '1=1 ';
        if (!empty($field['keyword'])){
            $where .= "and (name like '%{$field['keyword']}%' or intro like '%{$field['keyword']}%'  )";
        }

        //分页显示
        $page_size = $field['page_size']??6;
        //>>计算count totalPage
        $sql = "select count(id) from `mall` where ".$where;
        $count = $this->pdo->fetchColumn($sql);
        $total_page = ceil($count/$page_size);

        //>>开始页和每页条数
        $page = intval($field['page']??1);
        $page = $page<1?1:$page;
        $page = $page>=$total_page?$total_page:$page;
        $start = ($page-1)*$page_size;
        $limit = " limit {$start},{$page_size}";

        $sql = "select * from mall where ".$where."order by id desc".$limit;
        $mall = $this->pdo->fetchAll($sql);

        @session_start();
        $sql = "select * from integral where user_id={$_SESSION['user']['id']}";
        $integral = $this->pdo->fetchRow($sql);

        $html = PageTool::myYeMa($page,$page_size,$total_page);
        return [
            'mall'=>$mall,
            'count'=>$count,
            'total_page'=>$total_page,
            'page'=>$page,
            'page_size'=>$page_size,
            'html'=>$html,
            'integral'=>$integral,
        ];
    }
}