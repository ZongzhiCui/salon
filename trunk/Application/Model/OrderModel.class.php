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
        $sql = "select * from member where {$where} order by id desc {$limit}";
        $mems = $this->pdo->fetchAll($sql);
//        var_dump($plans,$mems);die;
        $html = PageTool::myYeMa($page,$page_size,$total_page);

        //充值记录
        //消费记录
        @session_start();
//        var_dump($_SESSION['user']);die;
        $sql = "select * from histories where user_id={$_SESSION['user']['id']} order by id desc";
        $vip = $this->pdo->fetchAll($sql);
        $recharge = [];
        $consume = [];
        foreach($vip as $v){
            if ($v['type'] == '充值'){
                $recharge[] = $v;
            }elseif ($v['type'] == '消费'){
                $consume[] = $v;
            }
        }
        /*var_dump($consume);
        echo '<hr>';
        var_dump($recharge);die;*/
        return [
            'plans'=>$plans,
            'mems'=>$mems,
            'count'=>$count,
            'total_page'=>$total_page,
            'page'=>$page,
            'page_size'=>$page_size,
            'html'=>$html,
            'recharge'=>$recharge,
            'consume'=>$consume,
        ];
    }
    public function getAdd($ob,$id){
        @session_start();
        $field['realname'] = $_SESSION['user']['id'];
        $field['phone'] = $_SESSION['user']['telephone'];
        if (strtotime($_POST['date']) < time()){
            $this->error = '预约时间至少提前一小时!';
            return false;
        }
        $field['date'] = strtotime($_POST['date']);
        $field['content'] = $_POST['content'];
        if ($ob = 1){//
            $field['plan_id'] = $id;
            $sql = Tools::myInsert('order',$field);
            $r = $this->pdo->execute($sql);
            return $r;
        }elseif ($ob = 2){//
            $field['barber'] = $id;
            $sql = "select * from `order` barber={$field['barber']}";
            $barber = $this->pdo->fetchAll($sql);
            foreach ($barber as $val){
                if ($val['date']==$field['date']){
                    $this->error = '您预约的这位美发师当天已经被预约!';
                    return false;
                }
            }
            $sql = Tools::myInsert('order',$field);
            $r = $this->pdo->execute($sql);
            return $r;
        }
    }
    /**
     * 后台管理预约
     */
    public function getAdminIndex($field=[]){
        $where = '1=1 ';
        if (!empty($field['keyword'])){
            $where .= "and (o.username like '%{$field['keyword']}%' or o.realname like '%{$field['keyword']}%' )";
        }
        //分页显示
        $page_size = $field['page_size']??4;
        //>>计算count totalPage
        $sql = "select count(id) from `order` where del=0 and cancel=0 AND ".$where;
        $count = $this->pdo->fetchColumn($sql);
        $total_page = ceil($count/$page_size);

        //>>开始页和每页条数
        $page = intval($field['page']??1);
        $page = $page<1?1:$page;
        $page = $page>$total_page?$total_page:$page;
        $start = ($page-1)*$page_size;
        $limit = " limit {$start},{$page_size}";

        $sql = "select m.*,o.* from `order` o LEFT join member m ON m.id=o.barber where o.del=0 and o.cancel=0 AND ".$where.$limit;
        $orders = $this->pdo->fetchAll($sql);
        foreach ($orders as &$valu){
            $sql = "select * from `user` where id={$valu['realname']}";
            $user = $this->pdo->fetchRow($sql);
            $valu['name'] = $user['realname'];
        }
        foreach ($orders as &$value){
            if ($value['plan_id'] != 0){
                $sql = "select * from `plan` where id={$value['plan_id']}";
                $plan = $this->pdo->fetchRow($sql);
                $value['plan'] = $plan['name'];
            }else{
                $value['plan'] = '未预约套餐';
            }
        }
        $html = PageTool::myYeMa($page,$page_size,$total_page);
        return [
            'order'=>$orders,
            'count'=>$count,
            'total_page'=>$total_page,
            'page'=>$page,
            'page_size'=>$page_size,
            'html'=>$html
        ];
    }
    public function getEdit($field){
        $sql = Tools::myUpdate('order',$field);
        $r = $this->pdo->execute($sql);
        return $r;
    }
    public function getDelete($id){
        $field['id'] = $id;
        $field['del'] = -1;
        $sql = Tools::myUpdate('order',$field);
        $r = $this->pdo->execute($sql);
        return $r;
    }
    public function getStatus($q,$id){
        $field['id'] = $id;
        $field['status'] = $q;
        $sql = Tools::myUpdate('order',$field);
        $r = $this->pdo->execute($sql);
        return $r;
    }
}