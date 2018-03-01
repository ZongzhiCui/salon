<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/1/23
 * Time: 20:58
 */

class UserModel extends Model
{
    public function getAll($field=[]){
        //处理搜索数据
        $where = '1=1 ';
        if (!empty($field['keyword'])){
            $where .= "and (realname like '%{$field['keyword']}%' or username like '%{$field['keyword']}%')";
        }
        if(!empty($field['telephone'])){
            $where.=" and telephone like '%{$field['telephone']}%'";
        }
        if(isset($field['sex'])){
            $where.=" and sex = {$field['sex']}";
        }

        //分页显示
        $page_size = $field['page_size']??4;
        //>>计算count totalPage
        $sql = "select count(id) from `user` where ".$where;

        $count = $this->pdo->fetchColumn($sql);
        $total_page = ceil($count/$page_size);

        //>>开始页和每页条数
        $page = intval($field['page']??1);
        $page = $page<1?1:$page;
        $page = $page>$total_page?$total_page:$page;
        $start = ($page-1)*$page_size;
        $limit = " limit {$start},{$page_size}";

        $sql = "select * from `user` where {$where} order by id desc {$limit}";
//        var_dump($sql);die;
        $rs = $this->pdo->fetchAll($sql);
        $html = PageTool::myYeMa($page,$page_size,$total_page);
        return [
            'rows'=>$rs,
            'count'=>$count,
            'total_page'=>$total_page,
            'page'=>$page,
            'page_size'=>$page_size,
            'html'=>$html
        ];
//        return $rs;
//    $fenye = new PageTools();
//    $rs = $fenye->myFenYe($field,'user');
//    return $rs;
    }
    public function getAdd_save($data,$file){
        //判定2次密码的相同
        if($data['pwd1']!==$data['pwd2']){
            $this->error = '两次输入的密码不一致';
            return false;
//            Tools::jump('两次输入的密码不一致','./index.php?c=User&a=index',3);
        }
        //先判断用户名必须大于3位
        if (strlen($data['username'])<3){
            $this->error = '用户名不能小于3位';
            return false;
        }
        if (empty($data['pwd1'])){
            $this->error='请填写密码';
            return false;
        }
        //移动图片
        $up= new UploadTool();
        $res=$up->upload($file,'user/');
        if ($res===false){
            $this->error='图片上传失败'.$up->getError();
            return false;
        }
        $im=new ImageTool();
        $thumb=$im->thumb($res,80,80);
        //判断用户名在数据库的唯一性
        //>>1.根据名字去数据库读取数据如果数据存在则不可添加
        $sql = "select `id` from `user` where username='{$data['username']}'";
        $r = $this->pdo->fetchAll($sql);
        if (!empty($r)){
            $this->error = '该用户名已经存在!';
            return false;
        }
        //准备数据
        $time=time();
        $ip=$_SERVER['REMOTE_ADDR'];
        $ip=ip2long($ip);
        //准备sql
        $sql="insert into `user` set 
username='{$data['username']}',
password='{$data['pwd1']}',
realname='{$data['realname']}',
sex='{$data['sex']}',
telephone='{$data['telephone']}',
remark='{$data['remark']}',
photo='{$thumb}',
last_login='{$time}',
last_login_ip='{$ip}'
";
        $this->pdo->execute($sql);
    }
    public function getEdit_user($id){
        $id = addslashes($id);
        $sql = "select * from user where id={$id}";
        $user = $this->pdo->fetchRow($sql);
        return $user;
    }

    public function getEdit_save($data,$file){
        //判定2次密码的相同
        if($data['pwd1']!==$data['pwd2']){
            $this->error = '两次输入的密码不一致';
            return false;
//            Tools::jump('两次输入的密码不一致','./index.php?c=User&a=index',3);
        }
        //先判断用户名必须大于3位
        if (strlen($data['username'])<3){
            $this->error = '用户名不能小于3位';
            return false;
        }
        if (empty($data['pwd1'])){
            $this->error='请填写密码';
            return false;
        }


        //判断用户名在数据库的唯一性
        //>>1.根据名字去数据库读取数据如果数据存在则不可添加
        $sql = "select `id` from `user` where username='{$data['username']}'";
        $r = $this->pdo->fetchAll($sql);
        if (!empty($r)){
            $this->error = '该用户名已经存在!';
            return false;
        }
        $sql="select * from `user` where id='{$data['id']}'";
        $res=$this->pdo->fetchRow($sql);
        if ($res['password']!=Tools::myPwd($data['pwd'])){
            $this->error='旧密码错误';
            return false;
        }
        //准备数据
        $time=time();
        $ip=$_SERVER['REMOTE_ADDR'];
        $ip=ip2long($ip);
        $pwd=Tools::myPwd($data['pwd1']);

        if ($file['error']!=4){
            //移动图片
            $up= new UploadTool();
            $res=$up->upload($file,'user/');
            if ($res===false){
                $this->error='图片上传失败'.$up->getError();
                return false;
            }
        $im=new ImageTool();
        $thumb=$im->thumb($res,80,80);

        //准备sql
        $sql="update `user` set 
username='{$data['username']}',
password='{$pwd}',
realname='{$data['realname']}',
sex='{$data['sex']}',
telephone='{$data['telephone']}',
remark='{$data['remark']}',
photo='{$thumb}',
last_login='{$time}',
last_login_ip='{$ip}' where id={$data['id']}
";
        }
        else{
            $sql="update `user` set 
username='{$data['username']}',
password='{$pwd}',
realname='{$data['realname']}',
sex='{$data['sex']}',
telephone='{$data['telephone']}',
remark='{$data['remark']}',
last_login='{$time}',
last_login_ip='{$ip}' where id={$data['id']}
";
        }
        $this->pdo->execute($sql);


    }
    public function getrecharge($id){
        //准备sql
        $sql="select * from `user` where id={$id}";
       //处理返回值
        return $this->pdo->fetchRow($sql);
    }
    public function get_save($data){
            //健壮性
        if ($data['money']<0){
            $this->error='充值金额不能小于0';
            return false;
        }
        //准备sql
        //执行sql
    }
    public function getDelete($id){

    }

}