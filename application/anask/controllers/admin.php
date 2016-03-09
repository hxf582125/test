<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends Do_Controller {

  /**
   * Index Page for this controller.
   *
   * Maps to the following URL
   *    http://example.com/index.php/welcome
   *  - or -
   *    http://example.com/index.php/welcome/index
   *  - or -
   * Since this controller is set as the default controller in
   * config/routes.php, it's displayed at http://example.com/
   *
   * So any other public methods not prefixed with an underscore will
   * map to /index.php/welcome/<method_name>
   * @see http://codeigniter.com/user_guide/general/urls.html
   */
  function __construct()
  {
    parent::__construct();
    $this->load->model('news');
    $this->load->library('pagination');

  }
  /**
     * 新闻列表
     */
  public function index()
  {
    //新闻类型 用于搜索条件
    $type = $this->news->existWhere('type',array());

    $this->smarty->assign('type',$type);
    $this->smarty->display('newsList.html');
  }
  /**
     * 新闻列表数据处理
     */
  public function newsListAjax()
  {
    $post = $_POST;
    $array = array(
      'title' => $post['title'],
      'type' => $post['type'],
      'is_show' => $post['isShow']
     );
    
    $data = $this->news->existField('news',$array);
    
    $limit = $post['limit'];//每页展示个数
    $offset = ($this->uri->segment(3,1)-1)*$limit;//偏移值,从第几个开始
    $config['base_url'] = base_url('admin/newsListAjax');
    $config['total_rows'] = count($data);//新闻数量
    $config['per_page'] = $limit;
    $config['first_link'] = '首页';
    $config['last_link'] = '尾页';
    $config['next_link'] = '下一页';
    $config['prev_link'] = '上一页';
    $this->pagination->initialize($config);
    $page=$this->pagination->create_links();
    $list = $this->news->newsList($array,$limit,$offset);
    foreach ($list as $key => $val) 
    {
      $list[$key]['pubTime'] = date('Y-m-d H:i:s',$val['pubTime']);
    }
    $status = (empty($list)) ? 0 : 1;
    echo json_encode(array('list' => $list, 'status' => $status, 'page' => $page));
    exit;
  }
  /**
     * 添加新闻
     */
  public function addNews()
  {
    //新闻类型
    $type = $this->news->existWhere('type',array());

    $this->smarty->assign('type',$type);
    $this->smarty->display('addNews.html');
  }
  /**
     * 添加新闻处理数据
     */
  public function addNewsAjax()
  {
    $post = $_POST;
    //判断新闻是否存在
    if(count($this->news->existWhere('news',array('title' => $post['title']))) > 0)
    {
      $msg = '该新闻信息已存在,请修改';
      $status = 0 ;
      echo json_encode(array('msg' => $msg , 'status' => $status ));
      exit;
    }

    //判断新闻url是否存在
    if(count($this->news->existWhere('news',array('url' => $post['url']))) > 0 && $post['url'])
    {
      $msg = '该新闻链接已存在,请修改';
      $status = 0 ;
      echo json_encode(array('msg' => $msg , 'status' => $status ));
      exit;
    }
    //判断置顶是否存在
    if(count($this->news->existWhere('news',array('is_top' => $post['isTop'], 'is_top !=' => 0))) > 0)
    {
      $msg = '该等级新闻置顶已存在,请修改';
      $status = 0 ;
      echo json_encode(array('msg' => $msg , 'status' => $status ));
      exit;
    }
    $array = array(
      'title' => $post['title'] , 
      'url' => $post['url'] ,
      'content' => $post['content'] , 
      'type' => $post['type'] , 
      'pubTime' => strtotime($post['pubTime'].str_replace('：',':',$post['hours'])) , 
      'is_show' => $post['isShow'] , 
      'is_top' => $post['isTop'], 
      'img' => $post['img']
      );
    $insertId = $this->news->addNews($array);
    $msg = ($insertId >= 1) ? '新闻添加成功' : '新闻添加失败' ;
    $status = ($insertId >= 1) ? 1 : 0 ;
    echo json_encode(array('msg' => $msg , 'status' => $status ));
    exit;
  }
  /**
     * 编辑新闻
     */
  public function editNews()
  {
    //新闻id
    $id = $this->uri->segment(3);
    //新闻类型
    $type = $this->news->existWhere('type',array());
    //新闻详情
    $news = $this->news->existWhere('news',array('id' => $id));

    $this->smarty->assign('type',$type);
    $this->smarty->assign('news',$news[0]);
    $this->smarty->display('editNews.html');
  }
  /**
     * 编辑新闻数据处理
     */
  public function editNewsAjax()
  {
    $post = $_POST;
    $array = array(
      'title' => $post['title'] , 
      'url' => $post['url'] , 
      'content' => $post['content'] , 
      'type' => $post['type'] , 
      'pubTime' => strtotime($post['pubTime'].str_replace('：',':',$post['hours'])) , 
      'is_show' => $post['isShow'] , 
      'is_top' => $post['isTop'], 
      'img' => $post['img']
      );
    //判断新闻是否存在
    $title = $this->news->existWhere('news',array('title' => $post['title']));
    if(count($title) > 0 && $title[0]['id'] != $post['id'])
    {
      $msg = '该新闻信息已存在,请修改';
      $status = 0 ;
      echo json_encode(array('msg' => $msg , 'status' => $status ));
      exit;
    }
    //判断url是否存在
    $url = $this->news->existWhere('news',array('url' => $post['url']));
    if(count($url) > 0 && $url[0]['url'] && $url[0]['id'] != $post['id'])
    {
      $msg = '该新闻链接已存在,请修改';
      $status = 0 ;
      echo json_encode(array('msg' => $msg , 'status' => $status ));
      exit;
    }
    //判断置顶是否存在
    $isTop = $this->news->existWhere('news',array('is_top' => $post['isTop'], 'is_top !=' => 0));
    if(count($isTop) > 0 && $isTop[0]['id'] != $post['id'])
    {
      $msg = '该等级新闻置顶已存在,请修改';
      $status = 0 ;
      echo json_encode(array('msg' => $msg , 'status' => $status ));
      exit;
    }
    //通过新闻id获得新闻信息
    $info=$this->news->existWhere('news',array('id'=>$post['id']));
    //图片地址
    $img = $_SERVER['DOCUMENT_ROOT'].$info[0]['img'];
    if($info[0]['img'] && file_exists($img) && $post['img']!=$info[0]['img'])
    {
      unlink($_SERVER['DOCUMENT_ROOT'].$info[0]['img']);
    }
    $num = $this->news->editNews($array,$post['id']);
    $msg = ($num >= 0) ? '新闻编辑成功' : '新闻编辑失败' ;
    $status = ($num >= 0) ? 1 : 0 ;
    echo json_encode(array('msg' => $msg , 'status' => $status ));
    exit;
  }
  /**
     * 删除新闻
     */
  public function delNews()
  {
    //新闻id
    $id = $this->uri->segment(3);
    //通过新闻id获得新闻信息
    $info=$this->news->existWhere('news',array('id'=>$id));
    //图片地址
    $img = $_SERVER['DOCUMENT_ROOT'].$info[0]['img'];
    if($info[0]['img'] && file_exists($img))
    {
      unlink($_SERVER['DOCUMENT_ROOT'].$info[0]['img']);
    }
    $num = $this->news->delNews($id);
    $msg = ($num >= 0) ? '新闻删除成功' : '新闻删除失败' ;
    $status = ($num >= 0) ? 1 : 0 ;
    echo json_encode(array('msg' => $msg , 'status' => $status ));
    exit;
  }
}
