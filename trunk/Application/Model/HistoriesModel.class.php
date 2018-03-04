<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/2
 * Time: 8:59
 */

class HistoriesModel extends Model
{
    public function getIndex($field=[]){
        $where = '1=1 ';
        if (!empty($field['keyword'])){
            $where .= "and (o.username like '%{$field['keyword']}%' or o.realname like '%{$field['keyword']}%' )";
        }
        //分页显示
        $page_size = $field['page_size']??4;
        //>>计算count totalPage
        $sql = "select count(id) from `histories` where del=0 AND ".$where;
        $count = $this->pdo->fetchColumn($sql);
        $total_page = ceil($count/$page_size);

        //>>开始页和每页条数
        $page = intval($field['page']??1);
        $page = $page<1?1:$page;
        $page = $page>$total_page?$total_page:$page;
        $start = ($page-1)*$page_size;
        $limit = " limit {$start},{$page_size}";

        $sql = "select m.*,h.* from `histories` h LEFT join member m ON m.id=h.member_id where h.del=0 AND ".$where." order by h.id DESC ".$limit;
        $records = $this->pdo->fetchAll($sql);
        foreach ($records as &$valu){
            $sql = "select * from `user` where id={$valu['user_id']}";
            $user = $this->pdo->fetchRow($sql);
            $valu['name'] = $user['realname'];
        }
        $html = PageTool::myYeMa($page,$page_size,$total_page);
        return [
            'order'=>$records,
            'count'=>$count,
            'total_page'=>$total_page,
            'page'=>$page,
            'page_size'=>$page_size,
            'html'=>$html
        ];
    }
    public function getDelete($id){
        $field['id'] = $id;
        $field['del'] = -1;
        $sql = Tools::myUpdate('histories',$field);
        $r = $this->pdo->execute($sql);
        return $r;
    }
    public function getRecharge(){
        $sql = "select * from `user`";
        $users = $this->pdo->fetchAll($sql);
        return [
            'users'=>$users,
        ];
    }
    public function getRecharge_save($field){
        /**读取出规则表里充值金额对应的优惠活动**/
        $sql = "select * from rules where recharge<='{$field['amount']}' ORDER by recharge DESC limit 1";
        $handsel = $this->pdo->fetchRow($sql);
        $recharge = bcmul($field['amount'],$handsel['handsel'],2);     //$field['amount']*$handsel['handsel'];
        /**查询出用户的余额进行充值**/
        $sql = "select * from `user` where id={$field['user_id']}";
        $user = $this->pdo->fetchRow($sql);

/**     bcsub — 2个任意精度数字的减法
 *      bcmul — 2个任意精度数字乘法计算
 *      bcdiv — 2个任意精度的数字除法计算
 *      bcadd — 2个任意精度数字的加法计算
 * Example #1 bcadd() 示例
        $a = '1.234';
        $b = '5';

        echo bcadd($a, $b);     // 6
        echo bcadd($a, $b, 4);  // 6.2340*/
        $this->pdo->beginTransaction();
        $field['remainder'] = bcadd($user['money'],$recharge,2);   //$user['money']+$recharge;
        $sql = "update `user` set money='{$field['remainder']}' where id={$field['user_id']}";
        $a = $this->pdo->execute($sql);
        if ($a === false){
            $this->error = '账户充值失败!';
            $this->pdo->rollBack();
            return false;
        }
//        var_dump($field);die;
        $field['handsel'] = bcmul($field['amount'],$handsel['handsel']-1,2);
        $field['type'] = '充值';
        $field['time'] = time();
        //将数据写入数据库
        $sql = Tools::myInsert('histories',$field);
        $r = $this->pdo->execute($sql);
        if ($r === false){
            $this->error = '消费数据添加失败!';
            $this->pdo->rollBack();
            return false;
        }
        $this->pdo->commit();
        return $r;
    }
    public function getConsume(){
        $sql = "select * from `user`";
        $users = $this->pdo->fetchAll($sql);
        $sql = "select * from `plan`";
        $plans = $this->pdo->fetchAll($sql);
        $sql = "select * from `member`";
        $members = $this->pdo->fetchAll($sql);
        $sql = "select * from `code`";
        $codes = $this->pdo->fetchAll($sql);
        return [
            'users'=>$users,
            'plans'=>$plans,
            'members'=>$members,
            'codes'=>$codes,
        ];
    }
    public function getConsume_save($field){
        $this->pdo->beginTransaction();
        /**
         * 先判断代金券是否可用
         */
        if (!empty($field['code_id'])){
            $sql = "select * from `code` where id={$field['code_id']}";
            $code = $this->pdo->fetchRow($sql);
            if ($code['user_id'] != $field['user_id']){
                $this->error = '您没有这个代金券';
                return false;
            }
            if ($code['status'] != 1){
                $this->error = '您的代金券已经被使用';
                return false;
            }
            //代金券可以使用 $code['money'];
            $vouchers = $code['money'];
            //使用优惠券的话把优惠券做废掉
            $sql = "update code set status=0 where id={$field['code_id']}";
            $r = $this->pdo->execute($sql);
            if ($r !== 1){
                $this->error = '您的代金券已经被使用';
                $this->pdo->rollBack();
                return false;
            }
        }else{
            //没有使用代金券
            $vouchers = 0;
        }

        //读取用户对应会员等级
        $sql = "select * from `user` where id={$field['user_id']}";
        $level = $this->pdo->fetchRow($sql);
//        $level['level'];//会员等级

        /**读取出规则表里充值金额对应的优惠活动**/
        $sql = "select * from rules where level='{$level['level']}' limit 1";
        $discount = $this->pdo->fetchRow($sql);
//        $discount['discount'];//规则表里对应等级的优惠折扣
        $discount = bcdiv($discount['discount'],10,2);

        //读取出套餐里的名称和价格
        $sql = "select * from plan where id='{$field['plan_id']}'";
        $plan = $this->pdo->fetchRow($sql);
        unset($field['plan_id']);
//        $plan['name'];$plan['money'];//套餐表里的名字和价格
        if (empty($field['amount'])) { //如果没有自定义金额就按套餐的价格来
            $field['amount'] = bcsub($plan['money'],$vouchers,2);
            $field['remainder'] = bcsub($level['money'],$field['amount'],2);
            $field['content'] = $plan['name'];
            $field['handsel'] = $vouchers;
        }else{//自定义价格有会员等级对应的折扣.如果有优惠券先减优惠券再优惠
            //消费的原价值  -- 为 -- $field['amount']
            //用了优惠券的价格 $abc
            $abc = bcsub($field['amount'],$vouchers,2);
            //再折扣后的价格
            $money = bcmul($abc,$discount,2);
            //账户余额
            $field['remainder'] = bcsub($level['money'],$money,2);
            //优惠的价格保存到数据库
            $field['handsel'] = bcsub($field['amount'],$money,2);
            $field['content'] = '其他消费';
        }

        $sql = "update `user` set money='{$field['remainder']}' where id={$field['user_id']}";
        $a = $this->pdo->execute($sql);
        if ($a === false){
            $this->error = '账户充值失败!';
            $this->pdo->rollBack();
            return false;
        }
        $field['time'] = time();
        $field['type'] = '消费';
        $sql = Tools::myInsert('histories',$field);
        $r = $this->pdo->execute($sql);
        if ($r === false){
            $this->error = '消费数据添加失败!';
            $this->pdo->rollBack();
            return false;
        }
        $this->pdo->commit();
        return $r;
    }
}