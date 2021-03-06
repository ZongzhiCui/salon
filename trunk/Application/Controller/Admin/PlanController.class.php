<?php

/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/3/1
 * Time: 9:40
 */
class PlanController extends PlatformController
{
    private $obj;
    //构造方法 创建DB对象给下面的方法使用
    public function __construct()
    {
        parent::__construct();
        $this->obj = new PlanModel();
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
            //处理数据
            $res=$this->obj->getAdd($data);
            //显示页面
            if ($res===false){
                $this->redirect('index.php?p=Admin&c=Plan&a=add','添加失败'.$this->obj->getError(),3);
            }else{
                $this->redirect('index.php?p=Admin&c=Plan&a=index','添加成功',3);
            }
        }else{
            //接受数据
            //处理数据
            //显示页面
            $this->display();
        }
        }
    public function delete(){
        //接受数据
        $id=$_GET['id'];
        //处理数据
        $res=$this->obj->getdelete($id);
        //显示页面
        if ($res===false){
            $this->redirect('index.php?p=Admin&c=Plan&a=index','删除失败'.$this->obj->getError(),3);
        }else{
            $this->redirect('index.php?p=Admin&c=Plan&a=index','删除成功',3);
        }
    }
    public function edit(){
        //分支
        if ($_SERVER['REQUEST_METHOD']=='POST'){
        //接受数据
            $data=$_POST;
            //处理数据
            $res=$this->obj->getedit_save($data);
            //显示页面
            if ($res===false){
                $this->redirect('index.php?p=Admin&c=Plan&a=edit&id='.$data['id'],'修改失败'.$this->obj->getError(),3);
            }else{
                $this->redirect('index.php?p=Admin&c=Plan&a=index','修改成功',3);
            }
        }else{
            //接受数据
            $id=$_GET['id'];
            //处理数据
            $res=$this->obj->getedit($id);
            //显示页面
            $this->assign('row',$res);
            $this->display();
        }
    }
}