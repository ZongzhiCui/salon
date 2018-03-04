<?php

/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/3/3
 * Time: 14:20
 */
class RankingModel extends Model
{
    public function getAll(){
        //写sql
        //根据histories表中的type,以及其对应的amount
        $a='充值';
        $sql = "select distinct sum(amount) as sum from histories where `type`='{$a}' GROUP BY user_id order by `sum` desc limit 3";
        //执行sql
        $user_money = $this->pdo->fetchAll($sql);//得到会员充值排行
        foreach($user_money as &$money_v){
            $sql = "select user_id,sum(amount) as `sum` from histories where `type`='{$a}' group by user_id having `sum`='{$money_v['sum']}'";
            $sum = $this->pdo->fetchAll($sql);
            foreach($sum as &$a_v){
                $sql = "select realname from user where id='{$a_v['user_id']}'";
                $realname = $this->pdo->fetchColumn($sql);
                $a_v['user_id'] = $realname;
            }
//           var_dump($a);die;
            $money_v['sum'] = $sum;
        }
//        var_dump($sum);die;
    $b='消费';
        //写sql
        //根据histories表中的type,以及其对应的amount
        $sql = "select distinct sum(amount) as sum from histories where `type`='{$b}' GROUP BY user_id order by `sum` desc limit 3";
        //执行sql
        $user_save = $this->pdo->fetchAll($sql);//得到会员充值排行
        foreach($user_save as &$save_v){
            $sql = "select user_id,sum(amount) as `sum` from histories where `type`='{$b}' group by user_id having `sum`='{$save_v['sum']}'";
            $sum = $this->pdo->fetchAll($sql);
            foreach($sum as &$a_v){
                $sql = "select realname from user where id='{$a_v['user_id']}'";
                $realname = $this->pdo->fetchColumn($sql);
                $a_v['user_id'] = $realname;
            }
//           var_dump($a);die;
            $save_v['sum'] = $sum;
        }
        //写sql
        //根据histories表中的type,以及其对应的amount
        $sql = "select distinct count(member_id) as count from histories WHERE member_id != 0 GROUP BY member_id order by `count` desc limit 3";
        //执行sql
        $member = $this->pdo->fetchAll($sql);//得到会员充值排行
//        var_dump($member);die;
        foreach($member as &$mem){
            $sql = "select member_id,count(member_id) as `count` from histories WHERE member_id != 0 group by member_id having `count`='{$mem['count']}'";
            $count = $this->pdo->fetchAll($sql);
            foreach($count as &$cou){
                $sql = "select realname from `member` where id='{$cou['member_id']}'";
                $realname = $this->pdo->fetchColumn($sql);
                $cou['real'] = $realname;
            }
//           var_dump($a);die;
            $mem['count'] = $count;
        }
        return ['user_money'=>$user_money,'user_save'=>$user_save,'member'=>$member];
    }



}