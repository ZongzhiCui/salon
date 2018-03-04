<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/4
 * Time: 8:39
 */

class MallBuyController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        $this->obj = ObjFactory::createObj('MallBuyModel');
    }
    public function buy(){
        $id = intval($_POST['id']);
        $address = $_POST['address'];
        $r = $this->obj->getBuy($id,$address);
        Tools::jump('./index.php?p=Home&c=Mall&a=index');
    }
}