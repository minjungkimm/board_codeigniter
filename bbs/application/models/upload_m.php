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
    function insert_file($upload)
    {
		$detail = array(
            'file_size' => (int)$upload['file_size'],
            'image_width' => $upload['image_width'],
            'image_height' => $upload['image_height'], 
            'file_ext' => $upload['file_ext'] //파일 확장자
        );

        $insert_array = array(
            'username' => $upload['username'],
            'subject' => $upload['subject'],
            'contents' => $upload['cotents'], 
            'file_path' => $upload['file_path'], //파일 업로드된 경로
            'file_name' => $upload['file_name'],
            'original_name' => $upload['orig_name'],
            'detail_info' => serialize($detail),
            'reg_date' => data("Y-m-d H:i:s")
        );

        //$this->db->insert('upload_files',$insert_array);
        /*
        $this->db->set('username',$upload['username']);
        $this->db->set('subject',$upload['subject']);
        $this->db->set('cotents',$upload['cotents']);
        $this->db->set('file_path',$upload['file_path']);
        $this->db->set('file_name',$upload['file_name']);
        $this->db->set('original_name',$upload['orig_name']);
        $this->db->set('detail_info',serialize($detail));
        $this->db->set('reg_date','NOW()',false);
        
        $this->db->insert('upload_files');
        */
        $this->db->insert('upload_files',$insert_array);
        $result = $this->db->insert_id();

        return $result;
    }
    
    
	
}

/* End of file auth_m.php */
/* Location: ./application/models/auth_m.php */