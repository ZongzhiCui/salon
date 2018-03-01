<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/1/28
 * Time: 19:12
 */

class PlatformController extends Controller
{
    public function __construct()
    {
        session_start();
        if(!isset($_SESSION['user'])){
            if (isset($_COOKIE['vip_id']) && isset($_COOKIE['vip_password'])){
                $id = $_COOKIE['vip_id'];
                $pwd = $_COOKIE['vip_password'];
                $obj = ObjFactory::createObj('VipModel');
                $r = $obj->getCookie_check($id,$pwd,'user');
                //判断返回结果如果全等于false则输出错误信息,并跳到登录界面
                if ($r === false){
                    Tools::jump('./index.php?p=Home&c=Login&a=index',$obj->getError(),3);
                }
                return;
            }
            Tools::jump('./index.php?p=Home&c=Login&a=index');
        }
    }
}