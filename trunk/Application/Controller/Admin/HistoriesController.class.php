<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/1
 * Time: 21:22
 */

class HistoriesController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        @session_start();
        $this->obj = ObjFactory::createObj('HistoriesModel');
    }
    public function index(){
        $field = $_REQUEST;
        $order = $this->obj->getIndex($field);
        $this->assign($order);
        $this->display('index');
    }
    public function delete(){
        $id = addslashes($_GET['id']);
        $r = $this->obj->getDelete($id);
        if ($r === false){
            Tools::jump('./index.php?p=Admin&c=Histories&a=index',$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Admin&c=Histories&a=index');
    }
    public function recharge(){
        $recharge = $this->obj->getRecharge();
        $this->assign($recharge);
        $this->display('recharge');
    }
    public function recharge_save(){
        $field = $_POST;
        $r = $this->obj->getRecharge_save($field);
        if ($r === false){
            Tools::jump('./index.php?p=Admin&c=Histories&a=index',$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Admin&c=Histories&a=index');
    }
    public function consume(){
        $recharge = $this->obj->getConsume();
        $this->assign($recharge);
        $this->display('consume');
    }
    public function consume_save(){
        $field = $_POST;
        $r = $this->obj->getConsume_save($field);
        if ($r === false){
            Tools::jump('./index.php?p=Admin&c=Histories&a=index',$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Admin&c=Histories&a=index');
    }
}