<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/3
 * Time: 14:24
 */

class MallController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        $this->obj = ObjFactory::createObj('MallModel');
    }
    public function index(){
        $mall = $this->obj->getAdminIndex();
        $this->assign($mall);
        $this->display('index');
    }
    public function add(){
        $this->display('add');
    }
    public function add_save(){
        $field = $_POST;
        $r = $this->obj->getAdminAdd($field);
        if ($r === false){
            Tools::jump('./index.php?p=Admin&c=Mall&a=add',$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Admin&c=Mall&a=index');
    }
    public function edit(){
        $id = addslashes($_GET['id']);
        $mall = $this->obj->getEdit($id);
        $this->assign($mall);
        $this->display('edit');
    }
    public function edit_save(){
        $field = $_POST;
        $r = $this->obj->getEdit_save($field);
        if ($r === false){
            Tools::jump('./index.php?p=Admin&c=Mall&a=edit&id='.$field['id'],$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Admin&c=Mall&a=index');
    }
}