<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//error_reporting(E_ALL);
class App extends CI_Controller {


	public function __construct()
		{
			parent::__construct();
            $this->load->model('login');
            $this->load->model('common');
            $this->load->library('session');
            $this->load->helper('functions_helper');
            if( ! ini_get('date.timezone') )
            {
                date_default_timezone_set('GMT');
            }
           // auth();
		}

	public function get_locations()
	{
        $jsonData = $this->jsonDataDecoding();//get json string
        $query = $this->common->commonSelect("office_locations");
        $data = $query->result();
        $result = array('status' => 'success', 'msg' => $data);
        echo json_encode($result);

	}
    function register()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $data_save['user_name'] = $jsonData['user_name'];
        $data_save['user_first_name']= $jsonData['user_f_name'];
        $data_save['user_last_name']= $jsonData['user_l_name'];
        $data_save['user_email']= $jsonData['user_email'];
        $data_save['user_pass']= $jsonData['user_password'];
        $data_save['user_name']= $jsonData['user_type'];
        $data_save['user_joining_date']= date('Y-m-d H:i:s');
        $data_save['user_status']= '1';
        $data_save['user_type']= '1';
        $result = $this->common->commonselectDet("users","user_email",$jsonData['user_email']);
        if($result->num_rows()>0)
        {
            $result = array('status' => 'error', 'msg' => "user already exixts!");
        }
        else
        {
            $result = array('status' => 'success', 'msg' => "User created successfully !");

            $this->db->insert("users",$data_save);
        }
        echo json_encode($result);

    }
    function login_chck()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $result = $this->common->loginCheck("users","user_email",$jsonData['user_email'],"user_pass",$jsonData['user_password']);
        if($result->num_rows()>0)
        {
            $result = array('status' => 'success', 'msg' => $result->row());
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "Invalid Login information ");
        }
        echo json_encode($result);
    }
    function registration ()
    {
        echo "dsfs";

        $jsonData = $this->jsonDataDecoding(); //get json string
        if($jsonData['profile_picture']!="")
        {
            //===================save profile image======================================
            $imagepath = "uploads/port_folio_images/";
            $file_name = $this->uploadBase64Image($jsonData['profile_picture'],$imagepath);
            //===================save profile image======================================
        }
        else
        {
            $file_name = "0";
        }
        if($jsonData['national_id_card']!="")
        {
            //===================save profile image======================================
            $imagepath1 = "uploads/user_documents/";
            $IDcard = $this->uploadBase64Image($jsonData['national_id_card'],$imagepath);
            //===================save profile image======================================
        }
        else
        {
            $IDcard = "0";
        }
        echo $jsonData['full_name'];
        $data_save['profile_picture'] = $file_name;
        $data_save['national_id_card'] = $IDcard;
        $data_save['full_name'] = $jsonData['full_name'];
        $data_save['id_card_number'] = $jsonData['id_card_number'];
        $data_save['gender'] = $jsonData['gender'];
        $data_save['city_of_birth'] = $jsonData['city_of_birth'];
        $data_save['date_of_birth'] = $jsonData['date_of_birth'];
        $data_save['martial_status'] = $jsonData['martial_status'];
        $data_save['address'] = $jsonData['address'];
        $data_save['telephone'] = $jsonData['telephone'];
        $data_save['email'] = $jsonData['email'];
        $data_save['religion'] = $jsonData['religion'];
        $data_save['last_education'] = $jsonData['last_education'];
        $data_save['acadmic_title'] = $jsonData['acadmic_title'];
        $data_save['occupation'] = $jsonData['occupation'];
        $data_save['workplace_comp_inst'] = $jsonData['workplace_comp_inst'];
        $data_save['workplace_address'] = $jsonData['workplace_address'];
        $data_save['reason_join'] = $jsonData['reason_join'];
        $this->db->insert("user_profile",$data_save);
        echo $this->db->last_query();
        $result = array('status' => 'success', 'msg' => "Account Created !");
        echo json_encode($result);
    }
}
