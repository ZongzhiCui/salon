<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/4
 * Time: 10:32
 */

class MallBuyController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        $this->obj = new MallBuyModel();
    }
    public function index(){
        $mallbuy = $this->obj->getMallBuy();
        $this->assign($mallbuy);
        $this->display();
    }
    public function send(){
        $id = addslashes($_GET['id']);
        $r = $this->obj->getSend($id);
        if ($r === false){
            Tools::jump('./index.php?p=Admin&c=MallBuy&a=index',$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Admin&c=MallBuy&a=index');
    }
    public function delete(){
        Tools::jump('./index.php?p=Admin&c=MallBuy&a=index');
    }
}