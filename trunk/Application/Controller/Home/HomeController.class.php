<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/2/3
 * Time: 21:20
 */

class HomeController extends Controller
{
    private $obj;
    public function __construct()
    {
        @session_start();
        $this->obj = ObjFactory::createObj('HomeModel');
    }
    public function index(){
        $rows = $this->obj->getIndex();
        $this->assign($rows);
        $this->display('index');
    }
    public function gallery(){
        $this->display('gallery');
    }
    public function personalcenter(){
/*        @session_start();
        if (!empty($_SESSION['admin'])){
            $this->assign($_SESSION);
        }*/
        $this->display('personalcenter');
    }
    public function edit_save(){
        $field = $_POST;
        $r = $this->obj->getEdit($field);
        if ($r === false){
            Tools::jump("index.php?p=Home&c=Home&a=home",$this->obj->getError(),3);
        }
        Tools::jump('./index.php?p=Home&c=Login&a=index');
    }
}

//class_implements()