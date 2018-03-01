<?php

/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/2/28
 * Time: 13:40
 */
class MemberModel extends Model
{
    public function getAll($field){
        $where = '1=1 ';
        if (!empty($field['keyword'])){
            $where .= "and (username like '%{$field['keyword']}%' or realname like '%{$field['keyword']}%' )";
        }
        //分页显示
        $page_size = $field['page_size']??4;
        //>>计算count totalPage
        $sql = "select count(id) from `member` where ".$where;
        $count = $this->pdo->fetchColumn($sql);
        $total_page = ceil($count/$page_size);

        //>>开始页和每页条数
        $page = intval($field['page']??1);
        $page = $page<1?1:$page;
        $page = $page>$total_page?$total_page:$page;
        $start = ($page-1)*$page_size;
        $limit = " limit {$start},{$page_size}";

        $sql = "select * from member where {$where} order by id desc {$limit}";
        $arts = $this->pdo->fetchAll($sql);
        foreach($arts as &$v){
            $sql="select * from `group` where id={$v['group_id']}";
            $res=$this->pdo->fetchRow($sql);
            $v['res']=$res;
        }
        $html = PageTool::myYeMa($page,$page_size,$total_page);
        return [
            'rows'=>$arts,
            'count'=>$count,
            'total_page'=>$total_page,
            'page'=>$page,
            'page_size'=>$page_size,
            'html'=>$html
        ];
    }
    public function getAdd_group(){
        //准备sql
        $sql="select * from `group`";
    //执行sql,返回值
       return $this->pdo->fetchAll($sql);
    }
    public function getAdd($data,$file){
        //判断两次密码是否一致
        if ($data['password1']!=$data['password2']){
            $this->error='两次密码不一致';
            return false;
        }
        //判断健壮性
        if (empty($data['username'])){
            $this->error='请输入用户名';
            return false;
        }
        if (empty($data['telephone'])){
            $this->error='请输入电话号码';
            return false;
        }
       //移动图片
        $up=new UploadTool();
        //原图路径
        $logo_src=$up->upload($file,'member/');
        //制作缩率图
        $im=new ImageTool();
        $thumb_logo=$im->thumb($logo_src,80,80);
        //准备添加数据
        $ip=ip2long($_SERVER['REMOTE_ADDR']);
        $time=time();
        //准备sql
        $sql="insert into `member`(username,password,realname,sex,telephone,group_id,last_login,last_login_ip,is_admin,photo) VALUES(
'{$data['username']}',
'{$data['password2']}',
'{$data['realname']}',
'{$data['sex']}',
'{$data['telephone']}',
'{$data['group_id']}',
'{$time}',
'{$ip}',
'{$data['is_admin']}',
'{$thumb_logo}'
)";
        //执行sql
        $this->pdo->execute($sql);
    }
    public function getdelete($id){
        //准备sql
        $sql="select status from member where id={$id}";
        $res=$this->pdo->fetchColumn($sql);
//        var_dump($res);die;
        if ($res==0){
            $sql="delete from member where id={$id}";
            //执行sql
            $this->pdo->execute($sql);
        }else{
            $this->error='不能开除正在工作中的员工';
            return false;
    }
    }
    public function getedit($id){
        //准备sql
        $sql="select * from member where id={$id}";
        //处理并返回值
       return $this->pdo->fetchRow($sql);
    }
    public function getedit_group(){
        //准备sql
        $sql="select * from `group`";
        //处理并返回值
        return $this->pdo->fetchAll($sql);
    }
    public function getedit_save($data,$file){
        //判断旧密码是否正确
        //准备sql
        $sql="select * from member where id={$data['id']}";
        //执行sql
        $res=$this->pdo->fetchRow($sql);
        //判断两次密码是否一致
        if ($data['password']!=$res['password']){
            $this->error='旧密码错误';
            return false;
        }
        if ($data['password1']!=$data['password2']){
            $this->error='两次密码不一致';
            return false;
        }
        if (empty($data['telephone'])){
            $this->error='请填写电话号码';
            return false;
        }

        //判断健壮性
        if (empty($data['username'])){
            $this->error='请输入用户名';
            return false;
        }
        if (empty($data['telephone'])){
            $this->error='请输入电话号码';
            return false;
        }
        if ($file['error']!=4 ) {
        //移动图片
        $up=new UploadTool();
        //原图路径
        $logo_src=$up->upload($file,'member/');
        if ($logo_src===false){
            $this->error='图片上传失败'.$up->getError();
            return false;
        }
        //制作缩率图
        $im=new ImageTool();
        $thumb_logo=$im->thumb($logo_src,80,80);
        //准备添加数据
        $ip=ip2long($_SERVER['REMOTE_ADDR']);
        $time=time();
        //准备sql

          $sql = "update member set username='{$data['username']}',
  password='{$data['password2']}',
  realname='{$data['realname']}',
  sex='{$data['sex']}',
  telephone='{$data['telephone']}',
  group_id='{$data['group_id']}',
  last_login_ip='{$ip}',
  is_admin='{$data['is_admin']}',
    photo='{$thumb_logo}' WHERE id='{$data['id']}'
";
      }else{
            $ip=ip2long($_SERVER['REMOTE_ADDR']);
        $sql = "update member set username='{$data['username']}',
  password='{$data['password2']}',
  realname='{$data['realname']}',
  sex='{$data['sex']}',
  telephone='{$data['telephone']}',
  group_id='{$data['group_id']}',
  last_login_ip='{$ip}',
  is_admin='{$data['is_admin']}'
 where id={$data['id']}
";
      }
        $this->pdo->execute($sql);
    }
}