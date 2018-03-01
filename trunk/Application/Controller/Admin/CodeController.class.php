<?php

/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/3/1
 * Time: 13:13
 */
class CodeController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        $this->obj = new CodeModel();
    }
    public function index(){
        //接受数据
        $field = $_REQUEST;
        //处理数据
        $rows= $this->obj->getAll($field);
        //分配数据
        $this->assign($rows);
        //显示页面
        $this->display('index');
    }
    public function delete(){
        //接受数据
        $id=$_GET['id'];
        //处理数据
        $res=$this->obj->getdelete($id);
        //显示页面
        if ($res===false){
        $this->redirect('index.php?p=Admin&c=Code&a=index','删除失败'.$this->obj->getError(),3);
    }
    else{
            $this->redirect('index.php?p=Admin&c=Code&a=index','删除成功',3);
    }
    }
    public function add(){
        //分支
        if ($_SERVER['REQUEST_METHOD']=='POST'){
            //接受数据
            $data=$_POST;
            //处理数据
            $this->obj->getAdd($data);
            //显示页面
            $this->redirect('index.php?p=Admin&c=Code&a=index','添加成功',3)
;
        }else{
            //接受数据
            //处理数据
           $res= $this->obj->getadd_user();
            //显示页面
            $this->assign('rows',$res);
            $this->display();
        }
    }
}