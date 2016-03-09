<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index_page extends My_Controller {

	/**
	 * Index Page for this controller.
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	function __construct(){
		parent::__construct();
    $this->load->helper('captcha');
	}

	public function index()
	{
    //验证码
    $cap = $this->captcha();
		if($this->session->userdata('name'))
		{
    	$this->smarty->display('index.html');
		}
		else
		{
      $this->smarty->assign('cap',$cap);
			$this->smarty->display('login.html');
		}
	}
	/**
     * 验证码处理函数
     *
     */
  public function captcha()
  {

    $vals = array(
    'word' => rand(100000,999999),
    'img_path' => dirname(BASEPATH).'/static/image/captcha/',
    'img_url' => '/static/image/captcha/',
    'img_width' => 80,
    'img_height' => 30,
    'expiration' => 7200
    );

    $cap = create_captcha($vals);
    return $cap;
  }

}
