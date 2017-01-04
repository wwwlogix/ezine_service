<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Session_input extends CI_Controller {

  public function index() {

		//load the session library
		$this->load->driver('session');
			//load the view/session.php
		$this->load->view('session_input');
    
    $this->load->helper('url');

		//if usur submits the form, 
		if ($this->input->post('userinput')) {
			//save the input
			$this->session->set_userdata('user_input', $this->input->post('userinput'));
			//prompt the user that data has been save, give a link to session_output
			//echo "<br>Session has been save! <a href='" . base_url() . "index.php/session_output'> Get the session from other page  </a>";

		}	
	}

}
 
?>