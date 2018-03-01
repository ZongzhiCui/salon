<?php

/**
 * Created by PhpStorm
 * User: Lenovo
 * Date: 2018/3/1
 * Time: 9:41
 */
class PlanModel extends Model
{
    public function getAll($field)
    {
        $where = '1=1 ';
        if (!empty($field['keyword'])) {
            $where .= "and name like '%{$field['keyword']}%'";
        }
        if (!empty($field['money'])){
            intval($field['money']);
            $where.=" and money < {$field['money']}";
        }

//分页显示
        $page_size = $field['page_size']??4;
//>>计算count totalPage
        $sql = "select count(id) from `plan` where " . $where;
        $count = $this->pdo->fetchColumn($sql);
        $total_page = ceil($count / $page_size);

//>>开始页和每页条数
        $page = intval($field['page']??1);
        $page = $page < 1 ? 1 : $page;
        $page = $page > $total_page ? $total_page : $page;
        $start = ($page - 1) * $page_size;
        $limit = " limit {$start},{$page_size}";

        $sql = "select * from plan where {$where} order by id desc {$limit}";
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
    public function getAdd($data){
        //判断健壮性
        if (empty($data['name'])){
            $this->error='套餐名不能为空';
            return false;
        }
        if (empty($data['des'])){
            $this->error='套餐描述不能为空';
            return false;
        }
        if (empty($data['money'])){
            $this->error='套餐价格不能为空';
            return false;
        }
        //准备sql
        $sql="insert into plan set `name`='{$data['name']}',
des='{$data['des']}',
money='{$data['money']}',
status='{$data['status']}'
";
        //执行sql
        $this->pdo->execute($sql);
    }
    public function getdelete($id){
        //判断套餐是否上线
        //准备sql
        $sql="select status from plan where id={$id}";
        $res=$this->pdo->fetchColumn($sql);
        //判断上线状态
        if ($res==0){
            $this->error='不能删除正在上线的套餐';
            return false;
        }else{
            //准备sql
            $sql="delete from plan where id={$id}";
            return $this->pdo->execute($sql);
        }
    }
    public function getedit($id){
        //准备sql
        $sql="select * from plan where id ={$id}";
        //执行sql返回值
        return $this->pdo->fetchRow($sql);
    }
    public function getedit_save($data){
        //判断健壮性
        if (empty($data['name'])){
            $this->error='套餐名不能为空';
            return false;
        }
        if (empty($data['des'])){
            $this->error='套餐描述不能为空';
            return false;
        }
        if (empty($data['money'])){
            $this->error='套餐价格不能为空';
            return false;
        }
        //准备sql
        $sql="update plan set `name`='{$data['name']}',
des='{$data['des']}',
money='{$data['money']}',
status='{$data['status']}'
where id='{$data['id']}'
";
        $this->pdo->execute($sql);
    }
}