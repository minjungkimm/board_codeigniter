<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 파일 업로드용 controller
 *
 * 
 */
class Controlls extends CI_Controller {

 	function __construct()
	{
        parent::__construct();
        $this->load->database();
        $this->load->model('upload_m');
        $this->load->helper(array('date','url','alert'));
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	}

	/**
	 * 주소에서 메소드가 생략되었을 때 실행되는 기본 메소드
	 */
	public function index()
	{
		$this->upload_file();
	}

	/**
	 * 사이트 헤더, 푸터를 자동으로 추가해준다.
	 *
	 */
	public function _remap($method)
 	{
 		//헤더 include
        $this->load->view('header_v');

		if( method_exists($this, $method) )
		{
			$this->{"{$method}"}();
		}

		//푸터 include
		$this->load->view('footer_v');
    }

    
 	/**
	 * 이미지 파일 업로드
	 */
	function upload_file()
 	{
		//경고창 헬퍼 로딩
		$this->load->helper('alert');
		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

		if( @$this->session->userdata('logged_in') == TRUE )
		{
			//폼 검증 라이브러리 로드
			$this->load->library('form_validation');

			//폼 검증할 필드와 규칙 사전 정의
			$this->form_validation->set_rules('subject', '제목', 'required');
			$this->form_validation->set_rules('contents', '내용', 'required');

			if ( $this->form_validation->run() == TRUE )
			{

				$info = array(
					'user_id' => $this->session->userdata['user_id'],
					'subject' => $this->input->post('subject', TRUE),
					'contents' => $this->input->post('contents', TRUE) 
				  );

                $config['upload_path'] = './static/user';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '100';
                $config['max_width']  = '1024';
                $config['max_height']  = '768';

                $this->load->library('upload', $config);

                if(!$this->upload->do_upload("upload_file"))
                {
                    echo $this->upload->display_errors();
                    alert('다시 업로드해주시길 바랍니다.', '/bbs/board/upload_v');
                }
                else
                {
					//$data = array('upload_data' => $this->upload->data());
					$upload_data = $this->upload->data();
					//var_dump($upload_data);

					//var_dump($upload_data['file_path']);
					
					$imagePathStrPos =strpos($upload_data['file_path'], '/bbs');
					$imagePath=substr($upload_data['file_path'], $imagePathStrPos);

					//var_dump($imagePath);

					$upload_data['file_path'] = $imagePath;

					//var_dump($upload_data['file_path']);
					
					$config['image_library'] = 'gd2';
					$config['source_image'] = $upload_data['file_path'].$upload_data['file_name'];
					$config['create_thumb'] = TRUE; //썸네일 생성
					$config['new_image'] = './static/user/thumb/'.$upload_data['file_name']; //복사본 생성할 경우
					$config['maintain_ratio'] = TRUE;
					$config['width'] = 16;
					$config['height'] = 16;
				
					$this->load->library('image_lib',$config);
					
					$this->image_lib->resize();
					$this->image_lib->crop();

					$result = $this->upload_m->insert_file($upload_data,$info);

                    if($result)
                    {
                        alert('업로드에 성공하였습니다.', '/bbs/board/lists/ci_board/page/1');
                        exit;
                        //var_dump($upload_data);
                    }
                    else
                    {
                        alert('다시 업로드해주시길 바랍니다.', '/bbs/controlls/upload_file');
					    exit;      
					}
                
                }
			}
			else
			{
				//쓰기폼 view 호출
				$this->load->view('board/upload_v');
			}
		}
		else
		{
			alert('로그인후 작성해주시기 바랍니다.', '/bbs/auth/login/');
			exit;
		}
 	}


	function thumb_view()
	{

	}


}

/* End of file board.php */
/* Location: ./application/controllers/test.php */