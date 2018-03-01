<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/1
 * Time: 9:44
 */

class OrderModel extends Model
{
    public function getIndex($field=[]){
        $where = '1=1 ';
        if (!empty($field['keyword'])){
            $where .= "and (username like '%{$field['keyword']}%' or realname like '%{$field['keyword']}%' )";
        }

        //分页显示
        $page_size = $field['page_size']??4;
        //>>计算count totalPage
        $sql = "select count(id) from `member` where ".$where;
        $count = $this->pdo->fetchColumn($sql);
        $total_page = ceil($count/$page_size);

        //>>开始页和每页条数
        $page = intval($field['page']??1);
        $page = $page<1?1:$page;
        $page = $page>$total_page?$total_page:$page;
        $start = ($page-1)*$page_size;
        $limit = " limit {$start},{$page_size}";
        $sql = "select * from plan where status=0";
        $plans = $this->pdo->fetchAll($sql);
        $sql = "select * from member where {$where} order by id desc {$limit}";;
        $mems = $this->pdo->fetchAll($sql);
        $html = PageTool::myYeMa($page,$page_size,$total_page);
        return [
            'plans'=>$plans,
            'mems'=>$mems,
            'count'=>$count,
            'total_page'=>$total_page,
            'page'=>$page,
            'page_size'=>$page_size,
            'html'=>$html
        ];
    }
    public function getAdd($b,$id){
        @session_start();
        $field['realname'] = $_SESSION['user']['realname'];
        $field['phone'] = $_SESSION['user']['telephone'];
        if (strtotime($_POST['date']) < time()){
            $this->error = '预约时间至少提前一小时!';
            return false;
        }
        $field['date'] = $_POST['date'];
        if ($b = 1){//
            $field['plan_id'] = $id;
            $sql = Tools::myInsert('order',$field);
            $r = $this->pdo->execute($sql);
            return $r;
        }elseif ($b = 2){//
            $field['barber'] = $id;
            $sql = Tools::myInsert('order',$field);
            $r = $this->pdo->execute($sql);
            return $r;
        }
    }
}