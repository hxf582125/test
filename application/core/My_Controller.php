<?php
/**
* @file My_Controller.php
* @synopsis  核心控制器重写
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2013-02-18 14:46:29
*/

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class My_Controller extends CI_Controller{

	public $db;
	public $mg;
	public $base_url;

    function __construct(){
		parent::__construct();
		$this->__init__();
	}

	private function __init__(){
		$this->db = $this->load->database('default',true);
		$this->load->driver('cache', array('adapter' => 'file'));
		$this->base_url = base_url();
		$this->smarty->assign('base_url',$this->base_url);
		$this->smarty->assign('systime',date('r'));

	}

	public function showmessage($messages, $url_forward = '', $second = 3){
		if($url_forward && empty($second)){
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: $url_forward");
		}else{
			if($url_forward){
				$message = "<font color=\"red\" size=\"5\"><b>{$messages}</b></font><script>setTimeout(\"window.location.href ='$url_forward';\", ".($second*1000).");</script>";
			}
			$this->smarty->assign('msg',$message);
			$this->smarty->assign('second',$second);
			$this->smarty->assign('message',$messages);
			$this->smarty->assign('content',"<a href=\"$url_forward\">".$messages."</a>");
			$this->smarty->assign('gourl',$url_forward);
			$this->smarty->display('showmessage.tpl');
		}
		exit();
	}

	public function showmsg($messages, $url_forward = '', $second = 3){  //内部跳转
		if($url_forward && empty($second)){
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: $url_forward");
		}else{
			if($url_forward){
				$message = "<font color=\"red\" size=\"5\"><b>{$messages}</b></font><script>setTimeout(\"window.location.href ='" . base_url() . "$url_forward';\", ".($second*1000).");</script>";
			}
			$this->smarty->assign('msg',$message);
			$this->smarty->assign('second',$second);
			$this->smarty->assign('message',$messages);
			$this->smarty->assign('content',"<a href=\"$url_forward\">".$messages."</a>");
			$this->smarty->assign('gourl',$url_forward);
			$this->smarty->display('showmsg.tpl');
		}
		exit();
	}
}

/**
 * Class Do_Controller   (登录)
 *
 */
class Do_Controller extends My_Controller{

    function __construct(){
		parent::__construct();
		$this->checkLogin();
	}

    /**
     * 验证登陆状态
     */
    private function checkLogin()
    {
      //验证是否已经登陆
      if(!$this->session->userdata('name'))
      {
        echo '<script type="text/javascript">window.top.location.href="' . base_url() . '";</script>';
          exit();        
      }
		}	
}
