<?php
/**
 *新闻信息model
 **/
if(!defined('BASEPATH')) exit();

class News extends CI_Model{

    function __construct(){
        parent::__construct();
    }

    /**
     * 新闻信息 获得数据
     * $array 搜索条件 array
     * $limit 查询条数
     * $offset 从第几个查
     */
    public function newsList($array,$limit,$offset)
    {
        return $this->db->select("news.*,type.name")->join('type','news.type = type.type_id')->order_by('news.pubTime','desc')->like($array)->get('news',$limit,$offset)->result_array();

    }
    /**
     * 添加新闻信息
     * $array 新闻信息组 array
     *
     */
    public function addNews($array)
    {
        //入库
        $this->db->insert('news', $array);
        return mysql_insert_id();
    }
    /**
     * 根据新闻信息id编辑新闻信息
     * $array 新闻信息组 array
     * $id 新闻id 
     * 
     */
    public function editNews($array,$id)
    {
        $this->db->set($array);
        $this->db->where('id', $id);
        $this->db->update('news');
    }
    /**
     * 根据新闻信息id删除新闻信息
     * $id 新闻id
     *
     */
    public function delNews($id)
    {
        $this->db->where_in('id', $id);
        $this->db->delete('news');
        return $this->db->affected_rows();
    }
    /**
     * 根据传的字段与对应值查询在对应表里的数量
     * $table 查询的表
     * $array 传的数据 array
     */
    public function existField($table,$array)
    {
        return $this->db->select('*')->like($array)->get($table)->result_array();
    }
    /**
     * 根据传的字段与对应值查询在对应表里的数量
     * $table 查询的表
     * $array 传的数据 array
     */
    public function existWhere($table,$array)
    {
        return $this->db->select('*')->where($array)->get($table)->result_array();
    }
}