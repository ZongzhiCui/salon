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
            //准备sql
            $sql="select * from `group`";
          //处理sql 返回值
           return $this->pdo->fetchAll($sql);
        }
        public function getdelete($id){
            //准备sql
            $sql="delete from `group` where id={$id}";
            //执行sql
            $this->pdo->execute($sql);
        }
        public function getedit($id){
            //准备sql
            $sql="select * from `group` where id={$id}";
            //执行sql
            $res=$this->pdo->fetchRow($sql);
            //返回值
            return $res;
        }
        public function getedit_save($data){
            //判断健壮性
            if(empty($data['name'])){
                $this->error='请填写名字';
                return false;
            }
            //准备sql
            $sql="update `group` set `name`='{$data['name']}' where id={$data['id']}";
            //执行sql
            $this->pdo->execute($sql);
        }
        public function getadd($data){
            //判断健壮性
            if (empty($data['name'])){
                $this->error='请填写名称';
                return false;
            }
            //准备sql
            $sql="insert into `group` set `name`='{$data['name']}'";
            //执行sql
            $this->pdo->execute($sql);
        }
    }