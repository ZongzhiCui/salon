<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/1/23
 * Time: 20:58
 */

class UserModel extends Model
{
    public function getAll($field=[]){
        //处理搜索数据
        $where = '1=1 ';
        if (!empty($field['keyword'])){
            $where .= "and (realname like '%{$field['keyword']}%' or username like '%{$field['keyword']}%')";
        }
        if(!empty($field['telephone'])){
            $where.=" and telephone like '%{$field['telephone']}%'";
        }
        if(isset($field['sex'])){
            $where.=" and sex = {$field['sex']}";
        }

        //分页显示
        $page_size = $field['page_size']??4;
        //>>计算count totalPage
        $sql = "select count(id) from `user` where ".$where;

        $count = $this->pdo->fetchColumn($sql);
        $total_page = ceil($count/$page_size);

        //>>开始页和每页条数
        $page = intval($field['page']??1);
        $page = $page<1?1:$page;
        $page = $page>$total_page?$total_page:$page;
        $start = ($page-1)*$page_size;
        $limit = " limit {$start},{$page_size}";

        $sql = "select * from `user` where {$where} order by id desc {$limit}";
        $rs = $this->pdo->fetchAll($sql);

        //充值记录
        //消费记录


        $html = PageTool::myYeMa($page,$page_size,$total_page);
        return [
            'rows'=>$rs,
            'count'=>$count,
            'total_page'=>$total_page,
            'page'=>$page,
            'page_size'=>$page_size,
            'html'=>$html,
//            'recharge'=>$recharge,
//            'consume'=>$consume,
        ];
//        return $rs;
//    $fenye = new PageTools();
//    $rs = $fenye->myFenYe($field,'user');
//    return $rs;
    }
    //前台个人消费记录
    public function getHomeAll(){
            //处理搜索数据
            $where = '1=1 ';
            if (!empty($field['keyword'])){
                $where .= "and (realname like '%{$field['keyword']}%' or username like '%{$field['keyword']}%')";
            }
            if(!empty($field['telephone'])){
                $where.=" and telephone like '%{$field['telephone']}%'";
            }
            if(isset($field['sex'])){
                $where.=" and sex = {$field['sex']}";
            }

            //分页显示
            $page_size = $field['page_size']??4;
            //>>计算count totalPage
            $sql = "select count(id) from `user` where ".$where;

            $count = $this->pdo->fetchColumn($sql);
            $total_page = ceil($count/$page_size);

            //>>开始页和每页条数
            $page = intval($field['page']??1);
            $page = $page<1?1:$page;
            $page = $page>$total_page?$total_page:$page;
            $start = ($page-1)*$page_size;
            $limit = " limit {$start},{$page_size}";

            $sql = "select * from `user` where {$where} order by id desc {$limit}";
            $rs = $this->pdo->fetchAll($sql);

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

            $html = PageTool::myYeMa($page,$page_size,$total_page);
            return [
                'rows'=>$rs,
                'count'=>$count,
                'total_page'=>$total_page,
                'page'=>$page,
                'page_size'=>$page_size,
                'html'=>$html,
                'recharge'=>$recharge,
                'consume'=>$consume,
            ];

    }

    /**
     * 获取我的预约记录
     */
    public function getMy_order(){
        @session_start();
        $sql = "select * from `order` where realname={$_SESSION['user']['id']} and cancel=0 order by id desc";
        $my_order = $this->pdo->fetchAll($sql);
//        var_dump($my_order);die;
        foreach($my_order as &$value){
            if (!empty($value['barber'])){
                $sql = "select * from member where id={$value['barber']}";
                $value['mem_name'] = $this->pdo->fetchRow($sql)['realname'];
            }
            if (!empty($value['plan_id'])){
                $sql = "select * from plan where id={$value['plan_id']}";
                $value['plan_name'] = $this->pdo->fetchRow($sql)['name'];
            }
        }
        return [
            'my_order'=>$my_order,
        ];
    }

    /**
     * 把客户最新的留言修改到 预约表里
     */
    public function getMy_content($id,$content){
        $sql = "update `order` set content='{$content}' where id={$id}";
        $r = $this->pdo->execute($sql);
        return $r;
    }
    public function getDel_order($id){
        /**
         * 下次优化扩展功能:必须要后台同意预约前,如果后台同意预约了 ,需要付违约金
         */
        $sql = "select * from  `order` where id={$id}";
        $my_order = $this->pdo->fetchRow($sql);
        @session_start();
        $this->pdo->beginTransaction();
        if ($my_order['status'] == 1){
            //取消成功预约美发师扣30
            if ($my_order['barber'] != 0){
                $money = bcsub($_SESSION['user']['money'],30,2);
                $sql = "update `user` set money={$money} where id={$_SESSION['user']['id']}";
                $r = $this->pdo->execute($sql);
                if ($r === false){
                    $this->error = '扣除违约金失败';
                    $this->pdo->roolBack();
                    return false;
                }
            }
            //取消成功预约的套餐扣20%
            if ($my_order['plan_id'] != 0){
                $sql = "select * from plan where id={$my_order['plan_id']}";
                $plan_money = $this->pdo->fetchRow($sql)['money'];
                $money = bcsub($_SESSION['user']['money'],bcmul($plan_money,0.2,2),2);
                $sql = "update `user` set money={$money} where id={$_SESSION['user']['id']}";
                $r = $this->pdo->execute($sql);
                if ($r === false){
                    $this->error = '扣除违约金失败';
                    $this->pdo->roolBack();
                    return false;
                }
            }
        }

        $sql = "update `order` set cancel=-1 where id={$id}";
        $num = $this->pdo->execute($sql);
        if ($num === false){
            $this->error = '取消预约失败';
            $this->pdo->roolBack();
            return false;
        }
        $this->pdo->commit();
        return $num;
    }
    public function getMy_mallBuy(){
        @session_start();
        $sql = "select * from mallbuy where user_id={$_SESSION['user']['id']}";
        $my_mallbuy = $this->pdo->fetchAll($sql);
        foreach ($my_mallbuy as &$value){
            $sql = "select * from mall where id={$value['mall_id']}";
            $mall_name = $this->pdo->fetchRow($sql);
            $value['mall_name'] = $mall_name['name'];
        }

        return [
            'my_mallbuy'=>$my_mallbuy,
        ];
    }

    public function getAdd_save($data,$file){
        //判定2次密码的相同
        if($data['pwd1']!=$data['pwd2']){
            $this->error = '两次输入的密码不一致';
            return false;
//            Tools::jump('两次输入的密码不一致','./index.php?c=User&a=index',3);
        }
        //先判断用户名必须大于3位
        if (strlen($data['username'])<3){
            $this->error = '用户名不能小于3位';
            return false;
        }
        if (empty($data['pwd1'])){
            $this->error='请填写密码';
            return false;
        }
        //移动图片
        $up= new UploadTool();
        $res=$up->upload($file,'user/');
        if ($res===false){
            $this->error='图片上传失败'.$up->getError();
            return false;
        }
        $im=new ImageTool();
        $thumb=$im->thumb($res,80,80);
        //判断用户名在数据库的唯一性
        //>>1.根据名字去数据库读取数据如果数据存在则不可添加
        $sql = "select `id` from `user` where username='{$data['username']}'";
        $r = $this->pdo->fetchAll($sql);
        if (!empty($r)){
            $this->error = '该用户名已经存在!';
            return false;
        }
        //准备数据
        $time=time();
        $ip=$_SERVER['REMOTE_ADDR'];
        $ip=ip2long($ip);
        //准备sql
        $sql="insert into `user` set 
username='{$data['username']}',
password='{$data['pwd1']}',
realname='{$data['realname']}',
sex='{$data['sex']}',
telephone='{$data['telephone']}',
remark='{$data['remark']}',
photo='{$thumb}',
last_login='{$time}',
last_login_ip='{$ip}'
";
        $this->pdo->execute($sql);
    }

    //前台注册会员
    public function getHomeAdd_save($field){
        //先判断用户名必须大于3位
        if (strlen($field['username'])<3){
            $this->error = '用户名不能小于3位';
            return false;
        }
        //判断用户名在数据库的唯一性
        //>>1.根据名字去数据库读取数据如果数据存在则不可添加
        $sql = "select id from user where username='{$field['username']}'";
        $r = $this->pdo->fetchRow($sql);
        if (!empty($r)){
            $this->error = '该用户名已经存在!';
            return false;
        }
        //判定2次密码的相同
        if($field['pwd1']!==$field['pwd2']){
            $this->error = '两次输入的密码不一致';
            return false;
//            Tools::jump('两次输入的密码不一致','./index.php?c=User&a=index',3);
        }
        $field['password']=Tools::myPwd($field['pwd1']);
        unset($field['pwd2']);
        unset($field['pwd1']);
        $sql = Tools::myInsert('user',$field);
//        var_dump($sql);die;
        return $this->pdo->execute($sql);
    }

    public function getEdit_user($id){
        $id = addslashes($id);
        $sql = "select * from user where id={$id}";
        $user = $this->pdo->fetchRow($sql);
        return $user;
    }

    public function getEdit_save($data,$file){
       if (strlen($data['pwd1'])>=32){
           $this->error='密码长度过长';
           return false;
       }
        //判定2次密码的相同
        if($data['pwd1']!==$data['pwd2']){
            $this->error = '两次输入的密码不一致';
            return false;
//            Tools::jump('两次输入的密码不一致','./index.php?c=User&a=index',3);
        }
        //先判断用户名必须大于3位
        if (strlen($data['username'])<3){
            $this->error = '用户名不能小于3位';
            return false;
        }
//        print_r($data['pwd']);die;
        if (!empty($data['pwd']) || !empty($data['pwd1'])){
            $sql="select * from `user` where id='{$data['id']}'";
            $res=$this->pdo->fetchRow($sql);
            if ($res['password']!=Tools::myPwd($data['pwd'])){
                $this->error='旧密码错误';
                return false;
            }
        }
        else{
            $sql="select password from `user` where id='{$data['id']}'";
            $data['pwd1']=$this->pdo->fetchColumn($sql);
        }

        //准备数据
        $time=time();
        $ip=$_SERVER['REMOTE_ADDR'];
        $ip=ip2long($ip);
        if (strlen($data['pwd1'])==32){
         $pwd=$data['pwd1'];
        }else{
            $pwd=Tools::myPwd($data['pwd1']);
        }
        if ($file['error']!=4){
            //移动图片
            $up= new UploadTool();
            $res=$up->upload($file,'user/');
            if ($res===false){
                $this->error='图片上传失败'.$up->getError();
                return false;
            }
        $im=new ImageTool();
        $thumb=$im->thumb($res,40,40);

        //准备sql
        $sql="update `user` set 
username='{$data['username']}',
password='{$pwd}',
realname='{$data['realname']}',
sex='{$data['sex']}',
telephone='{$data['telephone']}',
remark='{$data['remark']}',
photo='{$thumb}',
last_login='{$time}',
last_login_ip='{$ip}' where id={$data['id']}
";
        }
        else{
            $sql="update `user` set 
username='{$data['username']}',
password='{$pwd}',
realname='{$data['realname']}',
sex='{$data['sex']}',
telephone='{$data['telephone']}',
remark='{$data['remark']}',
last_login='{$time}',
last_login_ip='{$ip}' where id={$data['id']}
";
        }

        $this->pdo->execute($sql);


    }
    public function getrecharge($id){
        //准备sql
        $sql="select * from `user` where id={$id}";
       //处理返回值
        return $this->pdo->fetchRow($sql);
    }
    public function get_save($data){
            //健壮性
        if ($data['money']<0){
            $this->error='充值金额不能小于0';
            return false;
        }
        //查询用户vip等级
        $sql="select `level` from `user` where id={$data['id']}";
        $vals=$this->pdo->fetchColumn($sql);
        if ($data['money']>=5000 && $vals<1){
            $sql="update `user` set `level` = 1 where id={$data['id']}";
            $this->pdo->execute($sql);
            $this->error='恭喜你,已成为我们公司的VIP用户';
        }
        //查询倍率
        if($data['money']>=100) {
            $sql = "select handsel from rules where recharge<='{$data['money']}' order by recharge desc limit 1";
            $res = $this->pdo->fetchColumn($sql);
            $sql = "update `user` set money=money+($res*{$data['money']}) where id='{$data['id']}'";
            $this->pdo->execute($sql);
            $v=($res*$data['money'])-$data['money'];
        }
        else{
            //准备sql
            $sql = "update `user` set money=money+{$data['money']} where id='{$data['id']}'";
            //执行sql
            $this->pdo->execute($sql);
            $v=0;
        }
        //加入消费记录
        $time=time();
        $sql="select money from `user` where id ={$data['id']}";
        $val=$this->pdo->fetchColumn($sql);
        $sql="insert into histories(user_id,`type`,amount,content,`time`,remainder,handsel) VALUES (
'{$data['id']}',
'充值',
'{$data['money']}',
'充值{$data['money']}',
'{$time}',
'{$val}',
'{$v}'
)";
        $this->pdo->execute($sql);

        $a='充值';
        $sql="select sum(amount) from histories where user_id={$data['id']} && `type`='{$a}'";
        $res=$this->pdo->fetchColumn($sql);
        if ($res>=200000 && $vals <6){
            $sql="update `user` set `level`=6 where id={$data['id']}";
            $this->pdo->execute($sql);
            $this->error='恭喜你已经升级为我们公司VIP6';
        }
        elseif($res>=100000 && $vals <5){
            $sql="update `user` set `level`=5 where id={$data['id']} && `level`<5";
            $this->pdo->execute($sql);
            $this->error='恭喜你已升级为我们公司VIP5';
        }
        elseif($res>=50000 && $vals <4){
            $sql="update `user` set `level`=4 where id={$data['id']} && `level`<4";
            $this->pdo->execute($sql);
            $this->error='恭喜你已升级为我们公司VIP4';
        }
        elseif($res>=20000 && $vals <3){
            $sql="update `user` set `level`=3where id={$data['id']} && `level`<3";
            $this->pdo->execute($sql);
            $this->error='恭喜你已升级为我们公司VIP3';
        } elseif($res>=10000 && $vals <2){
            $sql="update `user` set `level`=2 where id={$data['id']} && `level`<2";
            $this->pdo->execute($sql);
            $this->error='恭喜你已升级为我们公司VIP2';
        }
        return $v;
    }
    public function gethdxq(){
        $sql="select * from rules";
        return $this->pdo->fetchAll($sql);
    }
    public function getedit_hdxq($id){
        //准备sql
        $sql="select * from `rules` where id={$id}";
        //执行sql
        return $this->pdo->fetchRow($sql);

    }
    public function gethdxq_save($data){
        //健壮性
        if (empty($data['recharge'])){
            $this->error='请输入金额';
            return false;
        }
        if (empty($data['handsel'])){
            $this->error='请输入活动倍率';
            return false;
        }
        if ($data['recharge']<0){
            $this->error='充值金额不能为负数';
            return false;
        }
        if ($data['handsel']<0){
            $this->error='赠送倍率不能为负数';
            return false;
        }
        //准备sql,并执行
        $sql="update `rules` set recharge='{$data['recharge']}',handsel='{$data['handsel']}' where id='{$data['id']}'" ;
        $this->pdo->execute($sql);
    }
    public function getadd_gz($data){
        //健壮性
        if (empty($data['recharge'])){
            $this->error='请输入金额';
            return false;
        }
        if (empty($data['handsel'])){
            $this->error='请输入活动倍率';
            return false;
        }
        if ($data['recharge']<0){
            $this->error='充值金额不能为负数';
            return false;
        }
        if ($data['handsel']<0){
            $this->error='赠送倍率不能为负数';
            return false;
        }
        //准备sql
        $sql="insert into rules set recharge='{$data['recharge']}',handsel='{$data['handsel']}'";
        //执行sql
        $this->pdo->execute($sql);
    }
    public function getDelete($id){
        //判断有无消费记录
        //准备sql
        $sql="select * from histories where user_id={$id}";
        //执行sql 判断是否为false 如果是可以删除 反之返回false
        $res=$this->pdo->fetchAll($sql);
        if (empty($res)){
            //没有记录 可以删除
            //准备sql
            $sql="delete from `user` where id={$id}";
            $this->pdo->execute($sql);
        }
        else{
            $this->error='此用户有消费记录';
            return false;
        }
    }
    public function getedit_vip($data){
        //健壮性
        if ($data['condition']<0){
            $this->error='累计充值金额不能小于0';
            return false;
        }
        if (is_numeric($data['condition'])===false){
            $this->error='累计金额亲输入数字';
            return false;
        }
        if (is_numeric($data['discount'])===false){
            $this->error='会员折扣亲输入数字';
            return false;
        }
        if (is_numeric($data['level'])===false){
            $this->error='会员等级请输入数字';
            return false;
        }

        if (empty($data['discount'])){
            $this->error='请输入会员折扣';
            return false;
        }
        if (empty($data['condition'])){
            $this->error='请输入累计充值金额';
            return false;
        }
        if ($data['discount']<0){
            $this->error='折扣不能为负数';
            return false;
        }
        if (empty($data['level'])){
            $this->error='请输入会员等级';
            return false;
        }
        if ($data['level']<0){
            $this->error='会员等级不能为负数';
            return false;
        }
        //准备sql
        $sql="update rules set 
discount='{$data['discount']}',
`level`='{$data['level']}',
`condition`='{$data['condition']}' where id='{$data['id']}'
";
        //执行sql
        $this->pdo->execute($sql);
    }
    public function getadd_vip($data){
        //健壮性
        if ($data['condition']<0){
            $this->error='累计充值金额不能小于0';
            return false;
        }
        if (is_numeric($data['condition'])===false){
            $this->error='累计金额亲输入数字';
            return false;
        }
        if (is_numeric($data['discount'])===false){
            $this->error='会员折扣亲输入数字';
            return false;
        }
        if (is_numeric($data['level'])===false){
            $this->error='会员等级请输入数字';
            return false;
        }

        if (empty($data['discount'])){
            $this->error='请输入会员折扣';
            return false;
        }
        if (empty($data['condition'])){
            $this->error='请输入累计充值金额';
            return false;
        }
        if ($data['discount']<0){
            $this->error='折扣不能为负数';
            return false;
        }
        if (empty($data['level'])){
            $this->error='请输入会员等级';
            return false;
        }
        if ($data['level']<0){
            $this->error='会员等级不能为负数';
            return false;
        }
        //准备sql
        $sql="insert into rules set 
discount='{$data['discount']}',
`level`='{$data['level']}',
`condition`='{$data['condition']}
";
        //执行sql
        $this->pdo->execute($sql);
    }

}