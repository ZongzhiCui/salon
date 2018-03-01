<?php

/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/2/28
 * Time: 10:16
 */
class GroupController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        @session_start();
        $this->obj = ObjFactory::createObj('GroupModel');
    }
    public function index(){
        //接受数据
        //处理数据
        $group=new GroupModel();
        $rows=$group->getAll();
        //显示页面
        $this->assign('rows',$rows);
        $this->display('index');

    }
    public function delete()
    {
        //接受数据
        $id = $_GET['id'];
        //处理数据
        $res = $this->obj->getdelete($id);
        //显示页面
        if ($res === false) {
            $this->redirect('index.php?p=Admin&c=Group&a=index', '删除失败'.$this->obj->getError(), 3);
        } else {
            $this->redirect('index.php?p=Admin&c=Group&a=index', '删除成功', 3);
    }
    }
    public function edit(){
        //分支
        if ($_SERVER['REQUEST_METHOD']=='POST'){
            //接受数据
            $data=$_POST;
            //处理数据
           $res= $this->obj->getedit_save($data);
            //显示页面
            if ($res===false){
                $this->redirect("index.php?p=Admin&c=Group&a=edit&id='{$data['id']}'",'修改失败'.$this->obj->getError(),3);
            }
            $this->redirect('index.php?p=Admin&c=Group&a=index','修改成功',3);

        }else{
            //接受数据
            $id=$_GET['id'];
            //处理数据
            $row=$this->obj->getedit($id);
            //分配数据
            $this->assign('row',$row);
            //显示页面
            $this->display('edit');
        }
    }
    public function add(){
            //接受数据
            $data=$_POST;
            //处理数据
           $res= $this->obj->getadd($data);
            //显示页面分支
        if ($res===false){
            $this->redirect('index.php?p=Admin&c=Group&a=index','添加失败'.$this->obj->getError(),3);
        }else{
        $this->redirect('index.php?p=Admin&c=Group&a=index','添加成功',3);
    }
    }
    }