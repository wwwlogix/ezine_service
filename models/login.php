<?php
class Login extends CI_Model {
   
    function __construct() 
	{
        parent::__construct();
	}

//------------select all----------		
    public function checklogin($user_name,$user_password,$user_type="") 
	{
           $query=$this->db->query("SELECT * FROM users WHERE user_name='".$user_name."'
		   							AND user_pass='".$user_password."' AND user_status='0'");
           return $query;
	}

}
 ?>