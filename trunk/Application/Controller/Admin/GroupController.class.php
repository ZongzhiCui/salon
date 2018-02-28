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
        $this->obj = ObjFactory::createObj('HomeModel');
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
}