<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/2
 * Time: 21:04
 */

class IntegralModel extends Model
{
    public function getIndex(){
        $sql = "select u.realname,i.* from `user` u JOIN integral i ON i.user_id=u.id";
        $integrals = $this->pdo->fetchAll($sql);
//        var_dump($integrals);die;
        /**$sql = "select * from user";
        $users = $this->pdo->fetchAll($sql);*/
        //获取已经存在计分表里的用户ID!
        $cunzai = [];
        foreach($integrals as $v){
            $cunzai[] = $v['user_id'];
        }
        $s_user = [];
        $sql = "select * from user ";
        $users = $this->pdo->fetchAll($sql);
        foreach($users as $value){
            if (!in_array($value['id'],$cunzai)){
                $s_user[] = $value;
            }
        }

//        var_dump($s_user);die;
        return [
            'integrals'=>$integrals,
            'users'=>$s_user,
            ];
    }
    public function getHave($id){
        $sql = "select * from `histories` where user_id={$id}";
        $ids = $this->pdo->fetchAll($sql);
        $num = [];
        foreach($ids as $v){
            if ($v['type'] == '消费'){
                $num[] = bcsub($v['amount'],$v['handsel'],2);
            }
        }
        $sum = array_sum($num)*100;
        $sql = "update integral set total={$sum} where user_id={$id}";
        $r = $this->pdo->execute($sql);
        return $r;
    }
    public function getAdd($user_id){
        $sql = "insert into integral set user_id={$user_id} ";
        $r = $this->pdo->execute($sql);
        return $r;
    }
}