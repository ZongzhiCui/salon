<?php

/**
 * Created by PhpStorm.
 * User: Lenovo
 * Date: 2018/2/28
 * Time: 10:18
 */
class GroupModel extends Model
    {
        public function getAll(){
            $sql="select * from `group`";
           return $this->pdo->fetchAll($sql);

        }
    }