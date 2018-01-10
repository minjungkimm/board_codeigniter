<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 사용자인증 모델
 *
 * @author Jongwon Byun <advisor@cikorea.net>
 */
class User_m extends CI_Model
{
    /*회원정보 CRUD 기능하는 모델 */
    function __construct()
    {
        parent::__construct();
    }

    function gets()
    {
        return $this->db->query("SELECT * FROM users")->result();
    }

    function add($option)
    {
        $this->load->helper('password');
        $password_security=password_hash($option['password'],PASSWORD_BCRYPT);

        $this->db->set('user_id',$option['username']);
        $this->db->set('email',$option['email']);
        $this->db->set('password',$password_security);
        $this->db->set('user_name',$option['nickname']);
        $this->db->set('reg_date','NOW()',false);
        $this->db->insert('users');
        $result = $this->db->insert_id();
        //데이터베이스에 레코드를 삽입할때 아이디번호를 삽입해줍니다.
        
        return $result;
    }

}

/* End of file auth_m.php */
/* Location: ./application/models/auth_m.php */