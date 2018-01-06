<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 사용자 인증 controller.
 *
 * @author Jongwon, Byun <advisor@cikorea.net>
 */
class Auth extends CI_Controller {

 	function __construct()
	{
		parent::__construct();
        $this->load->model('auth_m');
		$this->load->helper('form');
	}

	/**
	 * 주소에서 메소드가 생략되었을 때 실행되는 기본 메소드
	 */
	public function index()
	{
		$this->login();
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
	 * 로그인 처리
	 */
	public function login()
	{
		//폼 검증 라이브러리 로드
		$this->load->library('form_validation');

		$this->load->helper('alert');
		
		//회원정보 모델
		$this->load->model('user_m');

		//폼 검증할 필드와 규칙 사전 정의
		$this->form_validation->set_rules('username', '아이디', 'required|alpha_numeric');
		$this->form_validation->set_rules('password', '비밀번호', 'required');

		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

		if ( $this->form_validation->run() == TRUE )
  		{
	 		$auth_data = array(
				'username' => $this->input->post('username', TRUE),
				'email' => $this->input->post('email', TRUE),
				'password' => $this->input->post('password', TRUE) 
	  		);

	  		$result = $this->auth_m->login($auth_data);

			if ( $result )
   			{
   				//세션 생성
				$newdata = array(
                   'username'  => $result->username,
                   'email'     => $result->email,
                   'logged_in' => TRUE
				);

				//사용자가 입력한 값과 디비상의 정보가 일치함에 따라 = 계정 정보 존재유무에 따라 로그인하고,
				//암호화된 비밀번호값과 입력한 비밀번호값이 일치하는지 확인
				if($auth_data['username'] == $newdata['username'] && password_verify($auth_data['password'],$result->password))
				{	
					$this->session->set_userdata($newdata);
					alert('로그인 되었습니다.', '/bbs/board/lists/ci_board/page/1');
  					exit;
				}
				else
				{
					alert('아이디 또는 비밀번호가 틀렸습니다.','/bbs/auth/login');
					exit;
				}
  				
   			}
   			else
   			{
   				//실패시
  				alert('아이디나 비밀번호를 확인해 주세요.', '/bbs/auth/login');
  				exit;
   			}

  		}
  		else
  		{
	 		//쓰기폼 view 호출
	 		$this->load->view('auth/login_v');
		}
	}

	public function logout()
	{
		$this->load->helper('alert');

		$this->session->sess_destroy();

		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		alert('로그아웃 되었습니다.', '/bbs/auth/login');
  		exit;
	}


	/*
	회원가입
	 */
	public function register()
	{
		//폼 검증 라이브러리 로드
		$this->load->library('form_validation');

		//회원정보 모델
		$this->load->model('user_m');

		$this->load->helper('alert');

		//폼 검증할 필드와 규칙 사전 정의
		$this->form_validation->set_rules('username', '아이디', 'required|alpha_numeric|min');
		$this->form_validation->set_rules('password', '비밀번호', 'required|min_length[6]|max_length[30]|matches[re_password]');
		$this->form_validation->set_rules('nickname', '이름', 'required|min_length[3]|max_length[20]');
		$this->form_validation->set_rules('email', '이메일', 'required|valid_email|is_unique[users.email]');

		echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';

		if ( $this->form_validation->run() == TRUE )
  		{
			$this->load->helper('password');
			/*if(!function_exists('password_hash')){
				$this->load->helper('password');
		  	}*/

			//$hash = password_hash($this->input->post('password'),PASSWORD_BCRYPT);
			//단방향 해쉬통해서 비밀번호 암호화
			//1111-> 다른 정보로 변환하여 이것을 저장해 사용자의 실제 비밀번호를 기록하지않음
			//사용자가 로그인 시도 시 사용자가 입력한 비번값과 디비에 저장된 해쉬값이 일치하면
			//인증된 사용자임을 식별함
			//php 5.5 버젼부터 제공되는 api
			//사용자가 입력한 값과 디비에 저장된 해쉬값의 일치확인을 위해 password_verify를 사용
			//	if(paswword_verify($password,$hash)){
			//	}else{	}

	 		$regist_data = array(
				'username' => $this->input->post('username', TRUE),
				//'password' => $this->$hash,
				'password' => $this->input->post('password', TRUE),
				'nickname' => $this->input->post('nickname', TRUE),
				'email' => $this->input->post('email', TRUE) 
	  		);

	  		$result = $this->user_m->add($regist_data);

			if ( $result )
   			{
  				alert('회원가입이 정상적으로 완료되었습니다..', '/bbs/auth/login');
  				exit;
   			}
   			else
   			{
   				//실패시
  				alert('회원가입이 실패하였습니다.', '/bbs/auth/register');
  				exit;
   			}

  		}
  		else
  		{
	 		//쓰기폼 view 호출
	 		$this->load->view('auth/register_v.php');
		}	
	}

}

/* End of file auth.php */
/* Location: ./bbs/application/controllers/auth.php */