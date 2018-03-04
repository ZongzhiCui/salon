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
        $field = $_REQUEST;
        $mall = $this->obj->getIndex($field);
        $this->assign($mall);
        $this->display('index');
    }
}