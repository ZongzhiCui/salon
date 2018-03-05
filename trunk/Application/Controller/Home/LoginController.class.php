<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/2/12
 * Time: 14:09
 */

class LoginController extends Controller
{
    private $obj;

    //构造方法 创建DB对象给下面的方法使用
    public function __construct()
    {
        $this->obj = ObjFactory::createObj('VipModel');
    }
    public function index(){
        session_start();
        if (isset($_SESSION['user'])) {
            Tools::jump('./index.php?p=Home&c=Home&a=index');
        }
        if (isset($_COOKIE['vip_id']) && isset($_COOKIE['vip_password'])) {
            $id = $_COOKIE['vip_id'];
            $pwd = $_COOKIE['vip_password'];
            $r = $this->obj->getCookie_check($id, $pwd,'user');
            //判断返回结果如果全等于false则输出错误信息,并跳到登录界面
            if ($r === false) {
                Tools::jump('./index.php?p=Home&c=Login&a=index', $this->obj->getError(), 3);
            }
            Tools::jump('./index.php?p=Home&c=Home&a=index');
        }
        $this->display('index');
    }
    public function add_save(){ //在美发项目中未只用这个方法,备用当代码资源
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            $field = $_POST;
//            var_dump($field);die;
            //这里判断上传文件
                if ($_FILES['head']['error'] != 4){
                    //接收到文件数据 实现上传文件的功能
                    $file = $_FILES['head'];
                    //UploadTools对象里的upload来实现上传
                    $upload = new UploadTool();
                    $head = $upload->upload($file,'User/');
//                返回值 失败跳转到添加,成功就写$field 实现加入数据库
                    if ($head === false){
                        Tools::jump('./index.php?p=Home&c=Login&a=add_save',$upload->getError(),3);
                    }
                    //拿到上传的原图 制作一个缩略图
                    $thumb = new ImageTool();
                    $thumb_logo = $thumb->thumb($head,100,80);
                    if ($thumb_logo === false){
                        Tools::jump('./index.php?p=Home&c=Login&a=add_save',$thumb->getError(),3);
                    }
//                    unlink(__DIR__.'/../'.$head);
                    $field['photo'] = $thumb_logo;
                }
                $user_add = new UserModel();
                $r = $user_add->getHomeAdd_save($field);
                if ($r === false){
                    Tools::jump('./index.php?p=Home&c=Login&a=add_save',$user_add->getError(),3);
                }
                Tools::jump('./index.php?p=Home&c=Home&a=index','插入数据成功',1);
        }else{
            $this->display('registeration');
        }
    }
    public function login_check()
    {
        $field = $_POST;
        $r = $this->obj->getLogin_check($field,'user');
        if ($r === false) {
            Tools::jump('./index.php?p=Home&c=Login&a=index', $this->obj->getError(), 3);
        }
        Tools::jump('./index.php?p=Home&c=Home&a=index');
    }

    public function logout()
    {
        @session_start();
        unset($_SESSION['user']);
        setcookie('vip_id', null, -1, '/');
        setcookie('vip_password', null, -1, '/');
        Tools::jump('./index.php?p=Home&c=Home&a=index');
    }
    public function registeration(){
        $this->display();
    }
}