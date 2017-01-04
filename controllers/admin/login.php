<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('common');
		//$this->load->library('form_validation');
		$this->load->library('session');
	}
	public function index()
	{
		$this->load->view('admin/pages/login');
	}
	function login_validate()
	{
		$user_name=$this->input->post('name');
		$password=$this->input->post('password');
		$login_status=$this->common->checklogin($user_name,$password);
		if($login_status->num_rows()==0)
		{
			echo 1;
		}
		else
		{
			$data_user=$login_status->row();
			$this->session->set_userdata('u_id',$data_user->usr_id);
			$this->session->set_userdata('u_name',$data_user->usr_name);
			$this->session->set_userdata('u_type',$data_user->usr_type);
			$this->session->set_userdata('u_email',$data_user->usr_email);
			$this->session->set_userdata('u_status',$data_user->usr_status);
			$this->session->set_userdata($newdata);
		}
	}
}
