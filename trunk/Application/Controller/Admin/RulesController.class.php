<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/1
 * Time: 21:22
 */

class RulesController extends PlatformController
{
    private $obj;
    public function __construct()
    {
        parent::__construct();
        @session_start();
        $this->obj = ObjFactory::createObj('RulesModel');
    }
    //当会员消费的时候调用这个方法把消费记录存入消费记录histories
    static public function record(){

    }
    public function index(){
        $field = $_REQUEST;
        $all = $this->obj->getIndex($field);
        $this->display();
    }
}