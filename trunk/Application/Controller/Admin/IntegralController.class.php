<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/2
 * Time: 21:02
 */

class IntegralController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        $this->obj = ObjFactory::createObj('IntegralModel');
    }
    public function index(){
        $totals = $this->obj->getIndex();
        $this->assign($totals);
        $this->display('index');
    }
    public function have(){
        $id = addslashes($_GET['id']);
        $r = $this->obj->getHave($id);
        if ($r === false){
            Tools::jump('./index.php?p=Admin&c=Integral&a=index',$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Admin&c=Integral&a=index');
    }
    public function add(){
        $user_id = addslashes($_POST['user_id']);
        $this->obj->getAdd($user_id);
        Tools::jump('./index.php?p=Admin&c=Integral&a=index');
    }
}