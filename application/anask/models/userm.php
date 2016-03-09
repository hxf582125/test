<?php

if(!defined('BASEPATH')) exit();

    /**
     * 用户model
     *
     */
class Userm extends CI_Model{

	function __construct(){
        parent::__construct();
    }
    /**
     * 根据条件和表名获得信息
     * $table  表名 string
     * $array  条件 array
     * 
     */
    public function existField($table,$array)
    {
        return $this->db->select('*')->where($array)->get($table)->result_array();
    }

}