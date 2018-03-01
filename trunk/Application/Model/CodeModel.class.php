<?php

/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/3/1
 * Time: 13:13
 */
class CodeModel extends Model
{
    public function getAll($field)
    {
        $where = '1=1 ';
        if (!empty($field['keyword'])) {
            $where .= "and code like '%{$field['keyword']}%'";
        }
        if (!empty($field['money'])) {
            intval($field['money']);
            $where .= " and money < {$field['money']}";
        }

//分页显示
        $page_size = $field['page_size']??4;
//>>计算count totalPage
        $sql = "select count(id) from `code` where " . $where;
        $count = $this->pdo->fetchColumn($sql);
        $total_page = ceil($count / $page_size);

//>>开始页和每页条数
        $page = intval($field['page']??1);
        $page = $page < 1 ? 1 : $page;
        $page = $page > $total_page ? $total_page : $page;
        $start = ($page - 1) * $page_size;
        $limit = " limit {$start},{$page_size}";

        $sql = "select * from code where {$where} order by id desc {$limit}";
        $arts = $this->pdo->fetchAll($sql);
        $html = PageTool::myYeMa($page, $page_size, $total_page);
        return [
            'rows' => $arts,
            'count' => $count,
            'total_page' => $total_page,
            'page' => $page,
            'page_size' => $page_size,
            'html' => $html
        ];
    }
    public function getdelete($id){
        //准备sql
        $sql="select status from code where id={$id}";
        //执行sql
        $res=$this->pdo->fetchColumn($sql);
    if ($res==1){
        $this->error='不能删除未使用的代金券';
        return false;
    }
    else{
        $sql="delete from code where id={$id}";
        return $this->pdo->execute($sql);
    }
    }
    public function getadd_user(){
        //准备sql
        $sql="select realname,id from user";
        //执行sql返回值
        return $this->pdo->fetchAll($sql);
    }
    public function getAdd($data){
        //准备sql
        $name=uniqid();
        $sql="insert into code set 
code='{$name}',
user_id='{$data['user_id']}',
money='{$data['money']}',
status=1
";
        $this->pdo->execute($sql);
    }

}