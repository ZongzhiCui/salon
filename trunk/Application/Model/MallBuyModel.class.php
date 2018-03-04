<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/4
 * Time: 8:47
 */

class MallBuyModel extends Model
{
    public function getBuy($id,$address){
        @session_start();
        $this->pdo->beginTransaction();
        $sql = "insert into mallbuy set user_id='{$_SESSION['user']['id']}',mall_id={$id},address='{$address}'";
        $num = $this->pdo->execute($sql);
        if ($num === false){
            $this->error = '写入积分购买记录失败!';
            $this->pdo->roolBack();
            return false;
        }
        //查询当前积分.和商品积分.
        $sql = "select * from mall where id={$id}";
        $price = $this->pdo->fetchRow($sql);
        $price['price'];

        $sql = "select * from integral where id={$_SESSION['user']['id']}";
        $user = $this->pdo->fetchRow($sql);
        $user['total'];
        //剩余积分为
        if ($user['total'] < $price['price']){
            $this->error = '积分不足';
            $this->pdo->roolBack();
            return false;
        }
        $field['total'] = bcsub($user['total'],$price['price']);
        $sql = "update integral set total={$field['total']} where user_id={$_SESSION['user']['id']}";
        $r = $this->pdo->execute($sql);
        if ($r === false){
            $this->error = '更改积分数据失败';
            $this->pdo->roolBack();
            return false;
        }

        //把积分商城商品数量减1;
        $sql = "update mall set num=num-1 where id ={$id}";
        $n = $this->pdo->execute($sql);
        if ($n === false){
            $this->error = '库存减1失败';
            $this->pdo->roolBack();
            return false;
        }
        $this->pdo->commit();
        return $num;
    }

    /**
     * 后台积分购买订单
     */
    public function getMallBuy(){
        $sql = "select * from mallbuy";
        $mallbuy = $this->pdo->fetchAll($sql);

        return [
            'mallbuy'=>$mallbuy,
        ];
    }
    //点击发货
    public function getSend($id){
        $sql = "update mallbuy set status=1 where id={$id}";
        $r = $this->pdo->execute($sql);
        if ($r === false){
            $this->error = '发货修改失败';
            return false;
        }
        return $r;
    }
}