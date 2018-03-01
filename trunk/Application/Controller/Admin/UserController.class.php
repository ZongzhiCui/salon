<?php

/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/3/1
 * Time: 15:36
 */
class UserController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        @session_start();
        $this->obj = ObjFactory::createObj('UserModel');
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
            $res=$this->obj->getAdd_save($data,$file['logo']);
            //显示页面
        if ($res===false){
            $this->redirect('index.php?p=Admin&c=User&a=add','添加失败'.$this->obj->getError(),3);
        }else{
            $this->redirect('index.php?p=Admin&c=User&a=index','添加成功',3);
        }
        }else{
            //接受数据
            //处理数据
            //显示页面
            $this->display();
        }
    }
    public function edit(){
        if ($_SERVER['REQUEST_METHOD']=='POST'){
            if ($_SERVER['REQUEST_METHOD']=='POST'){
                //接受数据
                $data=$_POST;
                $file=$_FILES;
                //处理数据
                $res=$this->obj->getEdit_save($data,$file['logo']);
                //显示页面
                if ($res===false){
                    $this->redirect('index.php?p=Admin&c=User&a=edit&id='.$data['id'],'修改失败'.$this->obj->getError(),3);
                }
                else{
                    $this->redirect('index.php?p=Admin&c=User&a=index','修改成功',3);
                }
            }

        }else{
            //接受数据
            $id=$_GET['id'];
            //处理数据
            $res=$this->obj->getEdit_user($id);
            //显示页面
            $this->assign('row',$res);
            $this->display();
        }
    }
    public function recharge()
    {
        //分析:首先弹出表单 在进行充值 如果大于5000升级为vip
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        //接受数据
            $data=$_POST;
        //处理数据
            $res=$this->obj->get_save($data);
            //显示页面
        } else {
            //接受数据
            $id=$_GET['id'];
            //处理数据
           $res= $this->obj->getrecharge($id);
            //显示页面
            $this->assign('row',$res);
            $this->display();
        }
    }
}