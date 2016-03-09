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
     * 用户注册
     * $array 用户信息 array
     * 
     */
    public function addUser($array)
    {
    	$this->db->insert('user',$array);
    	return $this->db->insert_id();
    }
    /**
     * 根据用户id编辑用户信息
     * $array 信息组 array
     * $id 用户id 
     * 
     */
    public function editUser($array,$id)
    {
        $this->db->set($array);
        $this->db->where('user_id', $id);
        $this->db->update('user');
        return $this->db->affected_rows();
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
    /**
     * 前一百预约
     * 
     */
    public function orderlist()
    {
        return $this->db->select('*')->order_by('id asc')->get('admin_group_level',100,0)->result_array();
    }
    /**
     * 后面排序
     * 
     */
    public function otherlist($limit)
    {
        return $this->db->select('*')->order_by('id asc')->get('admin_group_level',$limit,100)->result_array();
        // $sql="select * from admin_group_level order by id asc offset 100";
        // $list=$this->db->query($sql);
        // return $list->result_array();
    }

}