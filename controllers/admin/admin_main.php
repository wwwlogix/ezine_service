<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_main extends CI_Controller {

	public function __construct()
		{
			parent::__construct();
			$this->load->model('common');
			$this->load->helper('functions');
			//$this->load->helper('date');
			$this->load->library('session');
			$this->load->helper(array('form', 'url'));
			$this->load->library('form_validation');
			$this->load->library('upload');
			$this->load->library('image_lib');
			//$this->load->library('session');
			//auth(); 
		}

	public function index()
	{
		//$data['content']=$this->load->view('admin/pages/dashboard','',true);
		$this->load->view('admin/pages/login');
	}
	public function login_page ()
	{
		echo $this->session->userdata('u_name');
		$user_name=$this->input->post('name');
		$password=$this->input->post('password');
		$this->common->general("en_admins","");
	}
	public function dashboard()
	{
		$data['content']=$this->load->view('admin/pages/dashboard',$data,true);
		$this->load->view('admin/main/index',$data);
	}
	public function portfolio()
	{
		$data['form_name']="addportfolio";
		$table="en_portfolio";
		$data['category'] = $this->common->commonSelect($table);
		//print_r($data);
		$data['content']=$this->load->view('admin/pages/portfolio',$data,true);
		$this->load->view('admin/main/index',$data);
	}
	public function addportfolio()
	{
		$data['form_name']="addportfolio";
		$this->form_validation->set_rules('name', 'Name', 'required');
		//$this->form_validation->set_rules('photo', 'Image', 'required');
		$this->form_validation->set_rules('design', 'Design ', 'required');
		$this->form_validation->set_rules('development', 'Development', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');
		$this->form_validation->set_rules('status', 'Status', 'required');
		$this->form_validation->set_rules('client', 'Client', 'required');
		$this->form_validation->set_rules('csource', 'Client source ', 'required');
		$this->form_validation->set_rules('sdate', 'Start date', 'required');
		$this->form_validation->set_rules('edate', 'End date', 'required');
		

		if ($this->form_validation->run() == FALSE)
		{
			$data['button_name']="Add Portfolio";
			$table="en_portfolio";
			$data['category'] = $this->common->commonSelect($table);
			$data['port_services']=$this->common->get_port_services($id);
			//echo "iffifffff";
			$data['srevices'] = $this->common->get_services();
			$data['content']=$this->load->view('admin/pages/portfolio_form',$data,true);
			$this->load->view('admin/main/index',$data);
		} 
		else
		{
			
	// UUUUUUUUUUUUUUUUUUUUUUbannerfileUUUUUUUUUUUUUUUUUUUUUUUUUUUUUU
				$FileName = $_FILES['image']['name'];
				$file_name ='';
				if($FileName!='')
				{
						$file_name=time().'_'.basename($_FILES['image']['name']);
						$file_name = str_replace(" ","_",$file_name);
						$file_name = str_replace("%","_",$file_name);
						$file_ext = end(explode(".", basename($_FILES['bannerfile']['name'])));
						$file_name2 = str_replace('.'.$file_ext,'',basename($_FILES['bannerfile']['name']));
						
						$file_uploaddir = FCPATH."uploads/port_folio_images/"; // FCPATH."uploads\page_banners\ ";
						$file_uploaddir = str_replace(" ","",$file_uploaddir);
						
						$file_uploadfile = $file_uploaddir.$file_name;
						
						if (move_uploaded_file($_FILES['image']['tmp_name'], $file_uploadfile)) 
							{
								$config2['image_library'] 	= 'gd2';
								$config2['source_image']	= $file_uploadfile;
								$config2['new_image']		= $file_uploaddir;
								$config2['create_thumb'] 	= TRUE;
								$config2['thumb_marker'] 	= "";
								$config2['maintain_ratio'] 	= TRUE;
								$config2['width']	 		= 905;
								$config2['height']			= 264;
								//$this->load->library('image_lib', $config2);
								
								
								
								$config2['x_axis'] = 0; // Same width as before
								$config2['y_axis'] = 264; // Crop to height
								$this->image_lib->initialize($config2);
								$this->image_lib->resize();
								$this->image_lib->crop();
								
								if ( ! $this->image_lib->resize())
								{
									$this->session->set_userdata('msg' , $this->image_lib->display_errors());	 
									header("Location: ".base_url()."admin/pages/add_page");
									exit;
									 
								}
						
								$this->image_lib->clear();
								unset($config2);
								
								
							} // if (move_uploaded_file
						else{
								
							$this->session->set_userdata('msg' , 'An error accoured during banner file upload');	 
								//header("Location: ".base_url()."admin/pages/add_page");
								exit;
							
							}
					
				} // if($bg_FileName!='')
				// UUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUU				
			
			$data_save = NULL;
			$data_save['port_name']=$this->input->post('name');
			$data_save['port_image']=$file_name;
			$data_save['port_designed']=$this->input->post('design');
			$data_save['port_developed']=$this->input->post('development');
			$data_save['port_description']=$this->input->post('description');
			$data_save['port_status']=$this->input->post('status');
			$data_save['port_clinet']=$this->input->post('client');
			$data_save['port_client_source']=$this->input->post('csource');
			$data_save['port_start_date']=data_base_date($this->input->post('sdate'));
			$data_save['port_end_date']=data_base_date($this->input->post('edate'));
			$data_save['port_link']=$this->input->post('link');
			$table="en_portfolio";
			$portfolioId = $this->common->commonInsert($table,$data_save);
			$services=$this->input->post('services');
			foreach($services as $web_services)
			{
				$data_services['por_services_id']=$web_services;
				$data_services['por_porrfolio_id']=$portfolioId;
				$this->common->commonInsert("port_services",$data_services);
			}
			
			$table="en_portfolio";
		    $data['category']=$this->common->commonSelect($table);
		    $data['content']=$this->load->view('admin/pages/portfolio',$data,true);
		    $this->load->view('admin/main/index',$data);
		}
		
	}
	public function editportfolio($id)
	{
		$data['srevices'] = $this->common->get_services();
		$data['category'] = $this->common->editportfolio($id);
		$data['port_services']=$this->common->get_port_services($id);
		$data['button_name']="Update";
		$data['form_name']="save_portfolio_updates";
		$data['content']=$this->load->view('admin/pages/portfolio_form',$data,true);
		$this->load->view('admin/main/index',$data);
	}

	function save_portfolio_updates()
	{
		
		// UUUUUUUUUUUUUUUUUUUUUUbannerfileUUUUUUUUUUUUUUUUUUUUUUUUUUUUUU
				$FileName = $_FILES['image']['name'];
				$file_name ='';
				if($FileName!='')
				{
						$file_name=time().'_'.basename($_FILES['image']['name']);
						$file_name = str_replace(" ","_",$file_name);
						$file_name = str_replace("%","_",$file_name);
						$file_ext = end(explode(".", basename($_FILES['bannerfile']['name'])));
						$file_name2 = str_replace('.'.$file_ext,'',basename($_FILES['bannerfile']['name']));
						
						$file_uploaddir = FCPATH."uploads/port_folio_images/"; // FCPATH."uploads\page_banners\ ";
						$file_uploaddir = str_replace(" ","",$file_uploaddir);
						
						$file_uploadfile = $file_uploaddir.$file_name;
						if (move_uploaded_file($_FILES['image']['tmp_name'], $file_uploadfile)) 
							{
								$config2['image_library'] 	= 'gd2';
								$config2['source_image']	= $file_uploadfile;
								$config2['new_image']		= $file_uploaddir;
								$config2['create_thumb'] 	= TRUE;
								$config2['thumb_marker'] 	= "";
								$config2['maintain_ratio'] 	= TRUE;
								//$config2['width']	 		= 905;
								//$config2['height']			= 264;
								//$this->load->library('image_lib', $config2);
								
								
								$this->image_lib->clear();
								unset($config2);
								
								
							} // if (move_uploaded_file
							
					
					
				} // if($bg_FileName!='')
				// UUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUU
		$portId=$this->input->post('port_id');	
		$data_save['port_name']=$this->input->post('name');
		if($FileName!='')
		{
			$data_save['port_image']=$file_name;
		}
		$data_save['port_designed']=$this->input->post('design');
		$data_save['port_developed']=$this->input->post('development');
		$data_save['port_description']=$this->input->post('description');
		$data_save['port_create_date']=data_base_date($this->input->post('cdate'));
		$data_save['port_status']=$this->input->post('status');
		$data_save['port_clinet']=$this->input->post('client');
		$data_save['port_client_source']=$this->input->post('csource');
		$data_save['port_start_date']=data_base_date($this->input->post('sdate'));
		$data_save['port_end_date']=data_base_date($this->input->post('edate'));
		$data_save['port_link']=$this->input->post('link');
		$table="en_portfolio";
		
		$updateId = $this->common->common_update($table,$data_save,'port_id',$portId);
		$this->common->commonDelete("port_services","por_porrfolio_id",$portId);
		$services=$this->input->post('services');
			foreach($services as $web_services)
			{
				$data_services['por_services_id']=$web_services;
				$data_services['por_porrfolio_id']=$portId;
				$this->common->commonInsert("port_services",$data_services);
			}
			
		$data['category']=$this->common->commonSelect($table);
		$data['content']=$this->load->view('admin/pages/portfolio',$data,true);
		$this->load->view('admin/main/index',$data);
	}

	
	public function category()
	{
		$data['form_name']="addcategory";
		$table="en_categories";
		$data['category'] = $this->common->commonSelect($table);
		//print_r($data);
		$data['content']=$this->load->view('admin/pages/category',$data,true);
		$this->load->view('admin/main/index',$data);
	}
	public function addcategory()
	{
		$data['form_name']="addcategory";
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('status', 'Status', 'required');
		$this->form_validation->set_rules('cdate', 'Date ', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			
			$table="en_categories";
			$data['category'] = $this->common->commonSelect($table);
			//echo "iffifffff";
			$data['content']=$this->load->view('admin/pages/category_form',$data,true);
			$this->load->view('admin/main/index',$data);
		}
		else
		{
			$data_save = NULL;
			$data_save['cat_name']=$this->input->post('name');
			$data_save['cat_status']=$this->input->post('status');
			$data_save['cat_create_date']=$this->input->post('cdate');
			$table="en_categories";
			$insertId = $this->common->commonInsert($table,$data_save);
			$table="en_categories";
		    $data['category']=$this->common->commonSelect($table);
		    $data['content']=$this->load->view('admin/pages/category',$data,true);
		    $this->load->view('admin/main/index',$data);
	
		}
	}
	public function editcategory($id)
	{
		$data['category'] = $this->common->editcategory($id);
		
		$data['button_name']="Update";
		$data['form_name']="save_cat_updates";
		$data['content']=$this->load->view('admin/pages/category_form',$data,true);
		$this->load->view('admin/main/index',$data);
	}
	function save_cat_updates()
	{
		$cateId=$this->input->post('cat_id');
		$data_save['cat_name']=$this->input->post('name');
		$data_save['cat_status']=$this->input->post('status');
		$data_save['cat_create_date']=data_base_date($this->input->post('cdate'));
	    //echo $this->input->post('cdate');
		//data_base_date($this->input->post('cdate'));
		//exit;
		//exit;
		$table="en_categories";
		$updateId = $this->common->common_update($table,$data_save,'cat_id',$cateId);
	    $this->db->last_query();
	    $data['category']=$this->common->commonSelect($table);
		$data['content']=$this->load->view('admin/pages/category',$data,true);
		$this->load->view('admin/main/index',$data);
		
	}
	
	public function menu()
	{
		$data['form_name']="addmenu";
		$table="en_menu";
		$data['category'] = $this->common->commonSelect($table);
		//print_r($data);
		$data['content']=$this->load->view('admin/pages/menu',$data,true);
		$this->load->view('admin/main/index',$data);
	}
	public function addmenu()
	{
		$data['form_name']="addmenu";
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('status', 'Status', 'required');
		$this->form_validation->set_rules('cdate', 'Date ', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$data['button_name']="Add Menu";
			$table="en_menu";
			$data['category'] = $this->common->commonSelect($table);
			//echo "iffifffff";
			$data['content']=$this->load->view('admin/pages/menu_form',$data,true);
			$this->load->view('admin/main/index',$data);
		} else{
			$data_save = NULL;
			$data_save['men_name']=$this->input->post('name');
			$data_save['men_status']=$this->input->post('status');
			$data_save['men_date']=$this->input->post('cdate');
			$table="en_menu";
			$insertId = $this->common->commonInsert($table,$data_save);
			$table="en_menu";
		    $data['category']=$this->common->commonSelect($table);
		    $data['content']=$this->load->view('admin/pages/menu',$data,true);
		    $this->load->view('admin/main/index',$data);
	
		}
	}
	public function services()
	{
		$data['form_name']="addservices";
		$table="en_services";
		$data['category'] = $this->common->commonSelect($table);
		//print_r($data);
		$data['content']=$this->load->view('admin/pages/services',$data,true);
		$this->load->view('admin/main/index',$data);
	}
	public function addservices()
	{
		$data['form_name']="addservices";
		$this->form_validation->set_rules('sname', 'Name', 'required');
		$this->form_validation->set_rules('description', 'Description', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			$data['button_name']="Add Services";
			$table="en_services";
			$data['category'] = $this->common->commonSelect($table);
			//echo "iffifffff";
			$data['content']=$this->load->view('admin/pages/services_form',$data,true);
			$this->load->view('admin/main/index',$data);
		} else{
			$data_save = NULL;
			$data_save['ser_name']=$this->input->post('sname');
			$data_save['ser_des']=$this->input->post('description');
			$table="en_services";
			$insertId = $this->common->commonInsert($table,$data_save);
			$table="en_services";
		    $data['category']=$this->common->commonSelect($table);
		    $data['content']=$this->load->view('admin/pages/services',$data,true);
		    $this->load->view('admin/main/index',$data);
		}
	}
	public function editservices($id)
		{
			$data['category']= $this->common->editservices($id);
			$data['button_name']= "Update";
			$data['form_name']="save_services_updates";
			$data['content']=$this->load->view('admin/pages/services_form',$data,true);
			$this->load->view('admin/main/index',$data);
		}
	function save_services_updates()
		{
			$ServiceId=$this->input->post('ser_id');
			$data_save['ser_name']=$this->input->post('sname');
			$data_save['ser_des']=$this->input->post('description');
			$table="en_services";
			$UpserId=$this->common->common_update($table,$data_save,'ser_id',$ServiceId);
			//$this->load->view('admin/main/index',$data);
			$this->db->last_query();
			$data['category']=$this->common->commonSelect($table);
			$data['content']=$this->load->view('admin/pages/services',$data,true);
		    $this->load->view('admin/main/index',$data);
		}
		public function contact()
	{
		//$data['form_name']="addcontact";
		$table="en_contact_us";
		$data['category'] = $this->common->commonSelect($table);
		//print_r($data);
		$data['content']=$this->load->view('admin/pages/contact',$data,true);
		$this->load->view('admin/main/index',$data);
	}
	public function addcontact()
	{
		//$data['form_name']="addcontact";
		$this->form_validation->set_rules('name', 'Name', 'required');
		$this->form_validation->set_rules('email', 'Email', 'required');
		$this->form_validation->set_rules('message', 'Message', 'required');

		if ($this->form_validation->run() == FALSE)
		{
			//$data['button_name']="SEND";
			$table="en_contact_us";
			$data['category'] = $this->common->commonSelect($table);
			//echo "iffifffff";
			$data['content']=$this->load->view('pages/landing_page',$data,true);
		    $this->load->view('main/index',$data);
		} else{
			$data_save = NULL;
			$data_save['cont_name']=$this->input->post('name');
			$data_save['cont_email']=$this->input->post('email');
			$data_save['cont_msg']=$this->input->post('message');
			$table="en_contact_us";
			$insertId = $this->common->commonInsert($table,$data_save);
			$table="en_contact_us";
		    $data['category']=$this->common->commonSelect($table);
		    $data['content']=$this->load->view('pages/landing_page',$data,true);
			$this->load->view('main/index',$data);
		}
	}
		
		
		
	function delete()
	{
		$id=$this->input->post('id');
		$table_name=$this->input->post('tbl_name');
		$col_name=$this->input->post('col_name');
		$this->common->commonDelete($table_name,$col_name,$id);
	}
	public function editmenu($id)
	{
		//echo "ddd";
		$data['category'] = $this->common->editmenu($id);
		$data['button_name']="Update";
		$data['form_name']="save_menu_updates";
		$data['content']=$this->load->view('admin/pages/menu_form',$data,true);
		$this->load->view('admin/main/index',$data);
	}
	function save_menu_updates()
	{
		$menId=$this->input->post('men_id');	
		$data_save['men_name']=$this->input->post('name');
		$data_save['men_status']=$this->input->post('status');
		$data_save['men_date']=data_base_date($this->input->post('cdate'));
		$table="en_menu";
		$updateId = $this->common->common_update($table,$data_save,'men_id',$menId);
		$this->db->last_query();
		$data['category']=$this->common->commonSelect($table);
		$data['content']=$this->load->view('admin/pages/menu',$data,true);
		$this->load->view('admin/main/index',$data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */