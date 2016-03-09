<?php
/**
 * Created by PhpStorm.
 * Date: 14/10/28
 * Time: 下午4:10
 */

class Uploadimg extends Do_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->library('image_lib');
    }

    /**
     * 上传商品图片
     */
    public function index(){
        //指定上传路径	DIRECTORY_SEPARATOR
        $xiangDui = str_replace('\\','/',dirname(dirname(dirname(dirname($_SERVER['PHP_SELF']))))).'news/'.$_GET['u'].'/';
        //服务器路径
        $targetBasePath = str_replace('\\','/',realpath(dirname(__DIR__). '/../../')).$xiangDui;
        $targetPath = $targetBasePath . date('Ymd');

        if(!is_dir($targetPath)){
            mkdir($targetPath, 0777, true);
        }

        $config['upload_path'] = $targetPath;
        $config['file_name'] = substr(md5(mt_rand() . uniqid()), 8, 16);
        $config['allowed_types'] = 'jpg|jpeg|png';
        $config['max_size'] = '1024';
        $config['max_width'] = '800';
        $config['max_height'] = '800';
        $this->load->library('upload', $config);

        if(!$this->upload->do_upload('Filedata')){
            $error = array('error' => $this->upload->display_errors());
            echo json_encode(array('code' => 500, 'info' => $error));
            exit;
        }

        $imgData = $this->upload->data();
        $img = $imgData['full_path'];
        
       //生成新图
           $imgConfig['maintain_ratio'] = TRUE;
           $imgConfig['source_image'] = $img;
           $imgConfig['width'] = 190;
           $imgConfig['height'] = 115;
           $imgConfig['new_image'] = $targetPath;
           $this->image_lib->initialize($imgConfig);
           $newImage = $this->image_lib->resize();
           if(!$newImage){
               echo json_encode(array('code' => 500, 'info' => $this->image_lib->display_errors()));
               exit;
           }
        //加水印
        //$this->has_mark($img);

        //返回给前台显示的图片
        $returnUrl = $xiangDui.date('Ymd').'/'.$imgData['file_name'];
		
        // //写入数据库
        // $sql_img = $xiangDui.date('Ymd').'/'.$imgData['file_name'];

        
        // //返回给前台显示的图片
        // $returnUrl = $sql_img;

        $data = array(
            "imgurl"=>$returnUrl
        );
        echo json_encode(array('code' => 1, 'info' => '', 'data' =>$data ));
    }

    /**
     * 加水印
     * @param $img
     */
    public function has_mark($img){
        $this->image_lib->clear();
        $config['source_image'] = $img;
        $config['wm_type'] = 'overlay';
        $config['wm_overlay_path'] = 'F:\ljj\static\image\sy\shuiyin.png';
        $config['wm_opacity'] = '50';
        $config['wm_x_transp'] = '1';
        $config['wm_y_transp'] = '1';
        $this->image_lib->initialize($config);
        $this->image_lib->watermark();
    }
}