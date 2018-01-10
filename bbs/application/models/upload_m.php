<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 사용자인증 모델
 *
 * @author Jongwon Byun <advisor@cikorea.net>
 */
class Upload_m extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /*
    * 이미지 업로드 모델
    */
    function insert_file($upload ='' ,$info = '')
    {
		$detail = array(
            'file_size' => (int)$upload['file_size'],
            'image_width' => $upload['image_width'],
            'image_height' => $upload['image_height'], 
            'file_ext' => $upload['file_ext'] //파일 확장자
        );

        /*$insert_array = array(
            'username' => $info['username'],
            'subject' => $info['subject'],
            'contents' => $info['contents'], 
            'file_path' => $upload['file_path'], //파일 업로드된 경로
            'file_name' => $upload['file_name'],
            'original_name' => $upload['orig_name'],
            'detail_info' => serialize($detail),
            'reg_date' => data("Y-m-d H:i:s")
        );*/
        
        $this->db->set('user_id',$info['user_id']);
        $this->db->set('subject',$info['subject']);
        $this->db->set('contents',$info['contents']);
        $this->db->set('file_path',$upload['file_path']);
        $this->db->set('file_name',$upload['file_name']);
        $this->db->set('original_name',$upload['orig_name']);
        $this->db->set('detail_info',json_encode($detail));
        $this->db->set('reg_date','NOW()',false);
        
        $this->db->insert('ci_board');

        $result = $this->db->insert_id();

        return $result;
    }
    
    
	
}

/* End of file auth_m.php */
/* Location: ./application/models/auth_m.php */