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
        //准备sql
        $a='充值';
//        $sql="select DISTINCT sum(amount) as sum from histories where `type`='{$a}' GROUP BY amount ORDER by sum DESC limit 3";
//        $res=$this->pdo->fetchAll($sql);
//        var_dump($res);die;
       $sql="SELECT DISTINCT user_id,sum(amount) as sum from histories where `type`='{$a}' GROUP BY user_id ORDER BY sum DESC limit 0,3";
    $res=$this->pdo->fetchAll($sql);
//var_dump($res);die;
        $i=0;
    foreach ($res as $key=>&$v){
        $sql="select * from `user` where id='{$v['user_id']}'";
        $vals=$this->pdo->fetchRow($sql);
        $v['real']=$vals;
        $i=$i+1;
    $v['i']=$i;
    }
    $b='消费';
        $sql="SELECT DISTINCT user_id,sum(amount) as sum from histories where `type`='{$b}' GROUP BY user_id ORDER BY sum DESC limit 0,3";
        $things=$this->pdo->fetchAll($sql);
        $i=0;
        foreach ($things as $key=>&$v){
            $sql="select * from `user` where id='{$v['user_id']}'";
            $vals=$this->pdo->fetchRow($sql);
            $v['real']=$vals;
            $i=$i+1;
            $v['i']=$i;
        }
        $i=0;
        $sql="SELECT DISTINCT member_id,count(member_id) as count from histories where member_id != 0 GROUP BY member_id ORDER BY count DESC limit 0,3";
        $member=$this->pdo->fetchAll($sql);
        foreach ($member as &$v){
            $sql="select * from member where id='{$v['member_id']}'";
            $m=$this->pdo->fetchRow($sql);
            $v['member']=$m['realname'];
            $i=$i+1;
            $v['i']=$i;
        }
            
        return ['rows'=>$res,'things'=>$things,'member'=>$member];

    }

}