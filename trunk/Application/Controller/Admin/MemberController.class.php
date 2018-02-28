<?php

/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/2/28
 * Time: 13:38
 */
class MemberController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        @session_start();
        $this->obj = ObjFactory::createObj('MemberModel');
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
    public function add(){
        //分支
        if ($_SERVER['REQUEST_METHOD']=='POST'){
        //接受数据
            $data=$_POST;
            $file=$_FILES;
            //处理数据
            $res=$this->obj->getAdd($data,$file['logo']);
            //显示页面
            if ($res===false){
                $this->redirect('index.php?p=Admin&c=Member&a=add','添加失败'.$this->obj->getError(),3);
            }
            else{
                $this->redirect('index.php?p=Admin&c=Member&a=index','添加成功',3);
            }
        }else{
            //接受数据
            //处理数据
           $res= $this->obj->getAdd_group();
           //分配数据
            $this->assign('res',$res);
           //显示页面
            $this->display();
        }
    }
    public function delete(){
        //接收数据
        $id=$_GET['id'];
        //处理数据
       $res= $this->obj->getdelete($id);
        //显示页面
        if ($res===false){
            $this->redirect('index.php?p=Admin&c=Member&a=index','开除失败'.$this->obj->getError(),3);
        }else{
        $this->redirect('index.php?p=Admin&c=Member&a=index','开除成功',3);
    }
}
    public function edit(){
        if ($_SERVER['REQUEST_METHOD']=='POST'){
            //接受数据
            $data=$_POST;
            $file=$_FILES;
            //处理数据
            $res=$this->obj->getedit_save($data,$file['logo']);
            //显示页面
            if ($res===false){
                $this->redirect('index.php?p=Admin&c=Member&a=edit&id='.$data['id'],'修改失败'.$this->obj->getError(),3);
            }
            else{
                $this->redirect('index.php?p=Admin&c=Member&a=index','修改成功',3);
            }
        }
        else{
            //接受数据
            $id=$_GET['id'];
            //处理数据
            $row=$this->obj->getedit($id);
            $res=$this->obj->getedit_group();
            $this->assign('vals',$res);
            $this->assign('row',$row);
            //显示页面
            $this->display('edit');
        }
    }
}