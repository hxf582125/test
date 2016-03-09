<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends My_Controller {

    /**
     * Index Page for this controller.
     * Maps to the following URL
     *      http://example.com/index.php/welcome
     *  - or -  
     *      http://example.com/index.php/welcome/index
     *  - or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    function __construct(){
        parent::__construct();
        $this->load->model('userm');
    $this->load->helper('captcha');

    }

    public function index()
    {
    //验证是否已经登陆
    if($this->session->userdata('name'))
    {
      $this->smarty->display('index.html');
    }
    else
    {
        $this->smarty->display('login.html');
    }    
    }
    /**
     * 用户登录数据处理
     *
     */
    public function loginAjax()
    {
        $post = $_POST;
        //根据登录名查询用户信息
        $info = $this->userm->existField('admin',array('user_name' => $post['name']));
        //debug($info);
        //用户是否存在
        if(count($info) > 0)
        {
            //密码是否相同
            if(md5(md5($post['pwd']).$info[0]['pwd_add']) == $info[0]['user_pwd'])
            {
              //保存用户session
              $data=array('name' => $info[0]['user']);
              $this->session->set_userdata($data);
              $msg =  '登录成功';
              $status = 1 ;
        
            }
            else
            {
                $msg = '帐号或密码错误';
                $status = 0;
            }
            
        }
        else
        {
            $msg = '帐号不存在';
            $status = 0;
        }
        echo json_encode(array('msg' => $msg,'status' => $status));
        exit();
    }
  //退出登录
    public function loginOut()
    {
    $this->session->unset_userdata('name');
    //跳转到
    header("Location:". base_url());
    }
}
