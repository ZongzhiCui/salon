<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/1
 * Time: 16:30
 */

class OrderController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        $this->obj = new OrderModel();
    }
    public function index(){
        $field = $_REQUEST;
        $order = $this->obj->getAdminIndex($field);
        $this->assign($order);
        $this->display('index');
    }
    public function edit(){
        $field = $_POST;
        $r = $this->obj->getEdit($field);
        if ($r === false){
            Tools::jump('./index.php?p=Admin&c=Order&a=index',$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Admin&c=Order&a=index');
    }
    public function delete(){
        $id = addslashes($_GET['id']);
        $r = $this->obj->getDelete($id);
        if ($r === false){
            Tools::jump('./index.php?p=Admin&c=Order&a=index',$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Admin&c=Order&a=index');
    }
    public function status(){
        $q = $_GET['q'];
        $id = addslashes($_GET['id']);
        $r = $this->obj->getStatus($q,$id);
        if ($r === false){
            Tools::jump('./index.php?p=Admin&c=Order&a=index',$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Admin&c=Order&a=index');
    }

    /**
     *
     */
    public function search(){
        $search = $_POST['search'];
        $r = $this->obj->getSearch($search);
        if ($r===false){
            echo '';
        }else{
            header('Content-Type: text/json; charset=utf-8');
            echo json_encode($r);
        }
    }
}