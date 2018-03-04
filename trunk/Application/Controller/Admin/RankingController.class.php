<?php

/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/3/3
 * Time: 14:19
 */
class RankingController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        @session_start();
        $this->obj = ObjFactory::createObj('RankingModel');
    }
    public function index(){
        //接受数据
        //处理数据
       $rows= $this->obj->getAll();
//       $member= $this->obj->getmember();
//       var_dump($member);die;
//       $this->assign('member',$member);
        //显示页面
        $this->assign($rows);
        $this->display('index');
    }
}