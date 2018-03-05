<?php
/**
 * User: 120.79.139.251:cuioo.cuiwq.cn
 * Date: 2018/3/4
 * Time: 15:53
 */

class ExportModel extends Model
{
    public function gelIndex(){
        $sql = "select * from `user`";
        return $this->pdo->fetchAll($sql);
    }
}