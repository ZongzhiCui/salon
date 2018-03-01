<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/1
 * Time: 9:43
 */

class OrderController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        $this->obj = ObjFactory::createObj('OrderModel');
    }
    public function index(){
        $field = $_REQUEST;
        $orders = $this->obj->getIndex($field);
        $this->assign($orders);
        $this->display();
    }
    public function add(){
        $id = $_POST['id'];
        $b = $_POST['b']; //b=1是套餐   b=2是员工
        $r = $this->obj->getAdd($b,$id);
        if ($r === false){
            Tools::jump('./index.php?p=Home&c=Order&a=index',$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Home&c=Order&a=index','预约成功!',2);
    }
}