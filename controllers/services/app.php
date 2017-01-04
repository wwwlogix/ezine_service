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
            $this->load->library('email');
            $this->load->library('ciqrcode');
            define( 'API_ACCESS_KEY', 'AIzaSyDmQvawL6Z8LnWzMiWv-17vaYN3JNpq4lw' );
//            if( ! ini_get('date.timezone') )
//            {
//                date_default_timezone_set('GMT');
//            }


        // auth();
		}

	public function get_locations()
	{
        $jsonData = $this->jsonDataDecoding();//get json string
        $cate_id = $jsonData['cat_id'];
        $query = $this->common->commonselectDet("office_locations","category_id",$cate_id);
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
        $result = $this->common->loginCheck("user_profile","member_ship_id",$jsonData['user_email'],"password",sha1($jsonData['user_password']));
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

        $jsonData = $this->jsonDataDecoding();//get json string
        $result = $this->common->commonselectDet("users","user_email",$jsonData['email']);
        if($result->num_rows()==0) {
            if ($jsonData['profile_picture'] != "") {
                //===================save profile image======================================
                $imagepath = "uploads/port_folio_images/";
                $file_name = $this->uploadBase64Image($jsonData['profile_picture'], $imagepath);
                //===================save profile image======================================
            } else {
                $file_name = "0";
            }
            if ($jsonData['national_id_card'] != "") {
                //===================save profile image======================================
                $imagepath1 = "uploads/user_documents/";
                $IDcard = $this->uploadBase64Image($jsonData['national_id_card'], $imagepath1);
                //===================save profile image======================================
            } else {
                $IDcard = "0";
            }

            //=============================================>
            //Save QR code
            $params['data'] = $jsonData['id_card_number'];
            $params['level'] = 'H';
            $params['size'] = 10;
            $qrCodeImageNAme = rand()."-".str_replace(" ","-",$jsonData['full_name']).'.png';
            $params['savename'] = FCPATH."uploads/qr_code/".$qrCodeImageNAme;
            $this->ciqrcode->generate($params);
            //======================================>
            //insert users login info in users table
            $data_account['user_email'] = $jsonData['email'];
            $password = substr(rand(), 0, 4);
            $data_account['user_pass'] = sha1($password);
            $data_account['user_joining_date'] = date('Y-m-d');
            $data_account['user_status'] = '1';
            $data_account['user_name'] = $jsonData['full_name'];
            $data_account['device_token'] = $jsonData['device_token'];
            $data_account['orignal_password'] = $password;
            $this->db->insert("users", $data_account);
            $inser_id = $this->db->insert_id();
//            $inser_id = $this->db->payment_check();

            //=================profile information
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
            $data_save['user_id'] = $inser_id;
            $data_save['password'] = sha1($password);
            $data_save['qr_code'] = $qrCodeImageNAme;
            $data_save['payment'] = '1';
            $data_save['device_token'] = $jsonData['device_token'];
            $inser_id = $this->db->insert("user_profile", $data_save);
            $profile_id = $this->db->insert_id();
            $result = array('status' => 'success', 'msg' => "Account Created !");
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "User Already Exists!");
        }
        echo json_encode($result);
    }
    public function memberShip()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $data_save['member_since'] = $jsonData['member_since'];
        $data_save['position_in_org'] = $jsonData['org_position'];
        $data_save['province'] = $jsonData['province'];
        $data_save['city_district'] = $jsonData['city_dist'];
        $data_save['sub_district'] = $jsonData['sub_dist'];
        $data_save['recomendation_from'] = $jsonData['recomdations'];
        $data_save['user_profile_id'] = $jsonData['user_profile_id'];
        $this->db->insert("user_memership",$data_save);
        //----------------------------email library=========================>
        $result = $this->common->commonselectDet("users","user_id",$jsonData['user_profile_id']);
        $data = $result->row();
        $user_email = $data->user_email;
        $user_pass = $data->orignal_password;
        $user_name = $data->user_name;
        $this->email->from('membership@wwwlogix.com', 'Member Ship');
        $this->email->set_mailtype("html");
        $this->email->to($user_email);
        $this->email->subject('Login Information ');
        $this->email->message('Hello '.$user_name.' Here is your login information  <br /> Email Address = '.$user_email.' <br /> Password is '.$user_pass);
        $this->email->send();
//        echo $this->email->print_debugger();
        $result = array('status' => 'success', 'msg' => "Profile Updated !");
        echo json_encode($result);
    }
    function verify_numbre()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $membership = $jsonData['member_number'];
        $full_name  = $jsonData['full_name'];
        $query = $this->common->verify_number($membership,$full_name);
        $total_records = $query->num_rows();
        if($total_records>0)
        {
            $result = $query->result();
            $result = array('status' => 'success', 'msg' => $result);
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "No Record !");
        }
        echo json_encode($result);
    }
    public function sync_locations ()
    {
        $query = $this->common->commonSelect("office_locations");
        $data = $query->result();
        $result = array('status' => 'success', 'msg' => $data);
        echo json_encode($result);
    }
    function edit_profile()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        //=============================================>

        if ($jsonData['profile_picture'] != "") {
            //===================save profile image======================================
            $imagepath = "uploads/port_folio_images/";
            $file_name = $this->uploadBase64Image($jsonData['profile_picture'], $imagepath);
            //===================save profile image======================================
        } else {
            $file_name = "0";
        }
        if ($jsonData['national_id_card'] != "") {
            //===================save profile image======================================
            $imagepath1 = "uploads/user_documents/";
            $IDcard = $this->uploadBase64Image($jsonData['national_id_card'], $imagepath);
            //===================save profile image======================================
        } else {
            $IDcard = "0";
        }

        $data_save['profile_picture'] = $file_name;
        $data_save['national_id_card'] = $IDcard;
        $data_save['full_name'] = $jsonData['full_name'];
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
        $user_id = $jsonData['profile_id'];

        $this->common->common_update("user_profile",$data_save,"profile_id",$user_id);

        $result = $this->common->edit_proifle_records($user_id);
        $row = $result->row();
        $result = array('status' => 'success', 'msg' => $row);
        echo json_encode($result);
    }
    function employee_card()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $user_id = $jsonData['user_id'];
        $result = $query = $this->common->employee_card("user_profile",$user_id);
        if($result->num_rows())
        {
            $data = $result->result();
            $result = array('status' => 'success', 'msg' => $data);
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "No Data found !");
        }
        echo json_encode($result);
    }
    function get_links()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $type = $jsonData['type'];
        $res = $this->common->commonselectDet("links","link_type",$type);
        if($res->num_rows()>0)
        {
            $data = $res->result();
            $result = array('status' => 'success', 'msg' => $data);
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "Sorry data not found !");
        }
        echo json_encode($result);
    }
    function get_map_categories()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $queryReturn = $this->common->commonSelect("map_categories");
        $result = $queryReturn->result();
        $result = array('status' => 'success', 'msg' =>$result);
        echo json_encode($result);
    }
    function polling_questions()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $queryReturn = $this->common->commonSelect("polling");
        $result = $queryReturn->result();
        $result = array('status' => 'success', 'msg' =>$result);
        echo json_encode($result);
    }
    function polling_result()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $question_id = $jsonData['question_id'];
        $res = $this->common->commonselectDet("polling_results","poll_id",$question_id);
        if($res->num_rows()>0)
        {
            $result = array('status' => 'success', 'msg' =>"");
            echo json_encode($result);
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "No data found !");
            echo json_encode($result);
        }
    }
    function pollresult()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $data_save['poll_id'] = $jsonData['polling_id'];
        $data_save['result'] = $jsonData['answer'];
        $data_save['user_id'] = $jsonData['user_id'];
        $data_save['result_date'] = date('Y-m-d H:i:s');
        $result = $this->common->checkAnswers ($jsonData['user_id'],$jsonData['polling_id']);

        if($result->num_rows()>0)
        {
            $result = array('status' => 'error', 'msg' =>"Already Answered");
        }
        else
        {
            $this->db->insert("polling_results",$data_save);
            $result = array('status' => 'success', 'msg' =>'Answered Successfully !');
        }
        echo json_encode($result);
    }
    function payment_check()
    {
       $jsonData = $this->jsonDataDecoding();//get json string
       $profile_id = $jsonData['profile_id'];
//       $payment_date =  date("Y-m-d");
      if($profile_id==0 || $profile_id=='')
      {
          $result = array('status' => 'error', 'msg' =>"false");
      }
      else
      {
          $payment_date =  $jsonData['payment_date'];
          $res = $this->common->chkPayments($payment_date,$profile_id);
//        echo $this->db->last_query();

          if($res->num_rows()>0)
          {
              $result = array('status' => 'success', 'msg' =>"true");
          }
          else
          {
              $data_save['profile_id'] = $profile_id;
              $data_save['payment_date'] = $payment_date;
              $msg = "false";
              $result = array('status' => 'error', 'msg' =>$msg);
          }
      }

        echo json_encode($result);
    }
    function save_payments_date()
    {
        $jsonData = $this->jsonDataDecoding(); //get json string
        $profile_id = $jsonData['profile_id'];
        $payment_date =  $jsonData['payment_date'];

        $data_save['profile_id'] = $profile_id;
        $data_save['payment_date'] = $payment_date;
        $this->db->insert("user_payments",$data_save);
    }
    function get_forum_categories()
    {
        $jsonData = $this->jsonDataDecoding(); //get json string
        $queryReturn = $this->common->commonSelect("categories");
        $data = $queryReturn->result();
        $result = array('status' => 'success', 'msg' =>$data);
        echo json_encode($result);
    }
    function get_category_topics()
    {
        $jsonData = $this->jsonDataDecoding(); //get json string
        $category_id = $jsonData['cat_id'];
        $res = $this->common->get_cat_topics($category_id);
        if($res->num_rows()>0)
        {
            $data = $res->result();
            $result = array('status' => 'success', 'msg' =>$data);
        }
        else
        {
            $result = array('status' => 'error', 'msg' =>"Sorry no data found !");
        }
        echo json_encode($result);
    }
    function get_topics_replies()
    {
        $jsonData = $this->jsonDataDecoding(); //get json string
        $topic_id = $jsonData['topic_id'];
        $res = $this->common->get_topics_replies($topic_id);
        if($res->num_rows()>0)
        {
            $data = $res->result();
            $result = array('status' => 'success', 'msg' =>$data);
        }
        else
        {
            $result = array('status' => 'error', 'msg' =>"Sorry no data found !");
        }
        echo json_encode($result);
    }
    function cretae_topic()
    {
        $jsonData = $this->jsonDataDecoding(); //get json string
        $this->common->create_new_topic($jsonData);
        $result = array('status' => 'success', 'msg' =>'Topic Created Successfully !');
        echo json_encode($result);
    }
    function topic_reply()
    {
        $jsonData = $this->jsonDataDecoding(); //get json string
       $message = $jsonData['message'];
        $this->common->create_new_topic_reply($jsonData);
        //================ push notifications
        $result = $this->common->get_deviceToken();
        foreach($result->result() as $token)
        {
            $device_token = str_replace("Device registered, registration ID=","",$token->device_token);
            if($device_token!="")
                $this->send_push($device_token,$message,$jsonData['topic_id']);
        }
        $result = array('status' => 'success', 'msg' =>'Comment Posted Successfully !');
        echo json_encode($result);
    }
    function  send_push($token,$msg,$id)
    {
        $registrationIds = array($token);
        $msg = array
        (
            'title'		=> $msg,
            'id'	=> $id,
            'vibrate'	=> 1,
            'sound'		=> 1,
            'largeIcon'	=> 'large_icon',
            'smallIcon'	=> 'small_icon'
        );
        $fields = array
        (
            'registration_ids' 	=> $registrationIds,
            'data'			=> $msg
        );

        $headers = array
        (
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
//        echo $result;
    }
	
	
	function ezine_login_check()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
		 $result = $this->common->ezine_checklogin($jsonData['username'],sha1($jsonData['password']));
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
	
	 function ezine_get_all_users()
    {
		$jsonData = $this->jsonDataDecoding(); //get json string
        $queryReturn = $this->common->commonSelect("ezine_user_table");
        $data = $queryReturn->result();
        $result = array('status' => 'success', 'msg' =>$data);
        echo json_encode($result);
    }
	
	
	 function ezine_get_user()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $user_id = $jsonData['user_id'];
        $res = $this->common->commonselectDet("ezine_user_table","user_id",$user_id);
        if($res->num_rows()>0)
        {
            $data = $res->result();
            $result = array('status' => 'success', 'msg' => $data);
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "Sorry data not found !");
        }
        echo json_encode($result);
    }
	
	
	
	
	 function ezine_edit_companyname()
    {
		  $jsonData = $this->jsonDataDecoding();//get json string
        
        $user_id = $jsonData['user_id'];
		$data_save['companyname'] = $jsonData['companyname'];
		
        $result = $this->common->checkcompanyname($jsonData['user_id'],$jsonData['companyname']);
		 if($result->num_rows()>0)
        {
            $result = array('status' => 'error', 'msg' =>"Company Name already Present");
        }
        else
        {
			$result = $this->common->updatecompanyname($jsonData['user_id'],$jsonData['companyname']);
            $result = array('status' => 'success', 'msg' =>"Company Name Added Successfully");
        }
        echo json_encode($result);

    }
	
	
	 function ezine_get_all_articles()
    {
		$jsonData = $this->jsonDataDecoding(); //get json string
        $queryReturn = $this->common->commonSelect("ezine_article_table");
        $data = $queryReturn->result();
        $result = array('status' => 'success', 'msg' =>$data);
        echo json_encode($result);
    }
	
	
	function ezine_get_article()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $article_id = $jsonData['article_id'];
        $res = $this->common->commonselectDet("ezine_article_table","article_id",$article_id);
        if($res->num_rows()>0)
        {
            $data = $res->result();
            $result = array('status' => 'success', 'msg' => $data);
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "Sorry data not found !");
        }
        echo json_encode($result);
    }
	
	function ezine_get_config()
    {
		$jsonData = $this->jsonDataDecoding(); //get json string
        $queryReturn = $this->common->commonSelect("ezine_config_table");
        $data = $queryReturn->result();
        $result = array('status' => 'success', 'msg' =>$data);
        echo json_encode($result);
    }
	
	function ezine_get_all_recordings()
    {
		$jsonData = $this->jsonDataDecoding(); //get json string
        $queryReturn = $this->common->commonSelect("ezine_recording_table");
        $data = $queryReturn->result();
        $result = array('status' => 'success', 'msg' =>$data);
        echo json_encode($result);
    }
	
	function ezine_get_user_recording()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $user_id = $jsonData['user_id'];
        $res = $this->common->commonselectDet("ezine_recording_table","user_id",$user_id);
        if($res->num_rows()>0)
        {
            $data = $res->result();
            $result = array('status' => 'success', 'msg' => $data);
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "Sorry data not found !");
        }
        echo json_encode($result);
    }
	
	function ezine_get_article_recording()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $article_id = $jsonData['article_id'];
        $res = $this->common->commonselectDet("ezine_recording_table","article_id",$article_id);
        if($res->num_rows()>0)
        {
            $data = $res->result();
            $result = array('status' => 'success', 'msg' => $data);
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "Sorry data not found !");
        }
        echo json_encode($result);
    }
	
	function ezine_get_user_article_recording()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $user_id = $jsonData['user_id'];
		$article_id = $jsonData['article_id'];
        $res = $this->common->get_user_article_recording($user_id,$article_id);
        if($res->num_rows()>0)
        {
            $data = $res->result();
            $result = array('status' => 'success', 'msg' => $data);
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "Sorry data not found !");
        }
        echo json_encode($result);
    }
	
	 function ezine_get_user_sessionstatus_recording()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $user_id = $jsonData['user_id'];
		$session_Completed = $jsonData['session_Completed'];
        $res = $this->common->get_user_sessionstatus_recording($user_id,$session_Completed);
        if($res->num_rows()>0)
        {
            $data = $res->result();
            $result = array('status' => 'success', 'msg' => $data);
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "Sorry data not found !");
        }
        echo json_encode($result);
    }
	
	
	function ezine_get_article_month()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $article_id = $jsonData['article_id'];
        $res = $this->common->get_aricle_month($article_id);
        if($res->num_rows()>0)
        {
            $data = $res->result();
            $result = array('status' => 'success', 'msg' => $data);
        }
        else
        {
            $result = array('status' => 'error', 'msg' => "Sorry data not found !");
        }
        echo json_encode($result);
    }
	
	  function ezine_set_user_recording()
	  {	$jsonData = $this->jsonDataDecoding();//get json string
		
            
       /*	 if ($jsonData['owners_message'] != "") {
                //===================save Owner's Message ======================================
                $audiopath = "uploads/audio_recording/";
                $file_name_ownersmessage = $this->uploadBase64Image($jsonData['owners_message'], $audiopath);
                //===================save Owner's Message======================================
            } else {
                $file_name_ownersmessage = "0";
            }
			
			if ($jsonData['n&e1'] != "") {
                //===================save n&e1======================================
                $audiopath = "uploads/audio_recording/";
                $file_name_ne1 = $this->uploadBase64Image($jsonData['n&e1'], $audiopath);
                //===================save n&e1======================================
            } else {
                $file_name_ne1 = "0";
            }
			
			if ($jsonData['n&e1_inst'] != "") {
                //===================save n&e1_inst======================================
                $audiopath = "uploads/audio_recording/";
                $file_name_ne1_inst = $this->uploadBase64Image($jsonData['n&e1_inst'], $audiopath);
                //===================save n&e1_inst======================================
            } else {
                $file_name_ne1_inst = "0";
            }
			
			if ($jsonData['n&e2'] != "") {
                //===================save n&e2======================================
                $audiopath = "uploads/audio_recording/";
                $file_name_ne2 = $this->uploadBase64Image($jsonData['n&e2'], $audiopath);
                //===================save n&e2======================================
            } else {
                $file_name_ne2 = "0";
            }
			
			if ($jsonData['n&e2_inst'] != "") {
                //===================save n&e2_inst======================================
                $audiopath = "uploads/audio_recording/";
                $file_name_ne2_inst = $this->uploadBase64Image($jsonData['n&e2_inst'], $audiopath);
                //===================save n&e2_inst======================================
            } else {
                $file_name_ne2_inst = "0";
            }
           
		   if ($jsonData['custom_article'] != "") {
                //===================save custom_article======================================
                $audiopath = "uploads/audio_recording/";
                $file_name_custom_article = $this->uploadBase64Image($jsonData['custom_article'], $audiopath);
                //===================save custom_article======================================
            } else {
                $file_name_custom_article = "0";
            }
			
			if ($jsonData['custom_article_inst'] != "") {
                //===================save custom_article_inst======================================
                $audiopath = "uploads/audio_recording/";
                $file_name_custom_article_inst = $this->uploadBase64Image($jsonData['custom_article_inst'], $audiopath);
                //===================save custom_article_inst======================================
            } else {
                $file_name_custom_article_inst = "0";
            }
            
			*/
			
            
            
			$data_save['user_id'] = $jsonData['user_id'];
			$data_save['article_id'] = $jsonData['article_id'];
			$data_save['owners_message'] = $jsonData['owners_message'];
            $data_save['ne1'] = $jsonData['ne1'];
			$data_save['ne1_inst'] = $jsonData['ne1_inst'];
			$data_save['ne2'] = $jsonData['ne2'];
			$data_save['ne2_inst'] = $jsonData['ne2_int'];
			$data_save['custom_article'] = $jsonData['custom_article'];
			$data_save['custom_article_inst'] = $jsonData['custom_article_inst'];
			$data_save['session_Completed'] = 0 ;
			$data_save['session_Url'] = '' ;
            $data_save['recording_date'] = date('Y-m-d H:i:s');
			
            $this->db->insert("ezine_recording_table",$data_save);
            
			$result = array('status' => 'success', 'msg' => "Recording Inserted !");
       
        echo json_encode($result);
    }
	
	
	 public function ezine_set_user()
    {
        $jsonData = $this->jsonDataDecoding();//get json string
        $data_save['username'] = $jsonData['username'];
        $data_save['password'] = $jsonData['password'];
        $data_save['companyname'] = $jsonData['companyname'];
        $this->db->insert("ezine_user_table",$data_save);
		
		$result = array('status' => 'success', 'msg' => "User Added");
        echo json_encode($result);
    }
	
	 
	 
	 public function audio_upload(){
		// Path to move uploaded files
		$target_path = "uploads/audio_recording/";
		 // array for final json respone
		$response = array();
 		// getting server ip address
		$server_ip = gethostbyname(gethostname());
 		// final file url that is being uploaded
		$file_upload_url = 'http://' . $server_ip . '/' . $target_path;
 
	if (isset($_FILES['file']['name'])) {
 	   $target_path = $target_path . basename($_FILES['file']['name']);
 		 $response['file_name'] = basename($_FILES['file']['name']);
    
 
    try {
        // Throws exception incase file is not being moved
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            // make error flag true
            $response['error'] = true;
            $response['message'] = 'Could not move the file!';
        }
 
        // File successfully uploaded
        $response['message'] = 'File uploaded successfully!';
		
		
        $response['error'] = false;
        $response['file_path'] = $file_upload_url . basename($_FILES['file']['name']);
   		 } catch (Exception $e) {
        // Exception occurred. Make error flag true
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    	}
		} else {
   		 // File parameter is missing
  	  $response['error'] = true;
   	 $response['message'] = 'Not received any file!F';
		}
 
	// Echo final json response to client
	echo json_encode($response);
	}
	
	 public function ezine_set_owner_recording(){
		// Path to move uploaded files
		$target_path = "uploads/audio_recording/";
		 // array for final json respone
		$response = array();
 		// getting server ip address
		$server_ip = gethostbyname(gethostname());
 		// final file url that is being uploaded
		$file_upload_url = 'http://' . $server_ip . '/' . $target_path;
 
	if (isset($_FILES['file']['name'])) {
 	   $target_path = $target_path . basename($_FILES['file']['name']);
 		 
		 // reading other post parameters
   		 $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
   		 $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';
		 $response['owner_file_name'] = basename($_FILES['file']['name']);
    		$response['user_id'] = $user_id;
			$response['article_id'] = $article_id;
			
			
			
	 
    try {
        // Throws exception incase file is not being moved
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            // make error flag true
            $response['error'] = true;
            $response['message'] = 'Could not move the file!';
        }
 
        // File successfully uploaded
        $response['message'] = 'File uploaded successfully!';
        $response['error'] = false;
        $response['file_path'] = $file_upload_url . basename($_FILES['file']['name']);
		
		$result = $this->common->checkrecording($response['user_id'],$response['article_id']);
		if($result->num_rows()>0)
        {
		
		$colname = 'owners_message';
		$colname_value = $response['file_path'];
		$this->common->updaterecording($response['user_id'],$response['article_id'],$colname,$colname_value);
	
        }
        else
        {
		$data_save['user_id'] = $user_id;
		$data_save['article_id'] = $article_id;
        $data_save['owners_message'] = $response['file_path'];
		$data_save['ne1'] = '';
		$data_save['ne1_inst'] = '';
		$data_save['ne2'] = '';
		$data_save['ne2_inst'] = '';
		$data_save['custom_article'] = '';
		$data_save['custom_article_inst'] = '';
		$data_save['session_Completed'] = 0;
		$data_save['recording_date'] = date('Y-m-d H:i:s');
        $this->db->insert("ezine_recording_table",$data_save);
        }
		
		
	
		
   		 } catch (Exception $e) {
        // Exception occurred. Make error flag true
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    	}
		} else {
   		 // File parameter is missing
  	  $response['error'] = true;
   	 $response['message'] = 'Not received any file!F';
		}
 
	// Echo final json response to client
	echo json_encode($response);
	}
	
	/* public function ezine_set_owner_rerecording(){
		$target_path = "uploads/audio_recording/";
		$response = array();
		$server_ip = gethostbyname(gethostname());
		$file_upload_url = 'http://' . $server_ip . '/' . $target_path;
 		if (isset($_FILES['file']['name'])) {
 		   $target_path = $target_path . basename($_FILES['file']['name']);
   		   $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
   		   $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';
		   $response['owner_file_name'] = basename($_FILES['file']['name']);
    	   $response['user_id'] = $user_id;
		   $response['article_id'] = $article_id;
	 try {
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            $response['error'] = true;
            $response['message'] = 'Could not move the file!';
        }
        $response['message'] = 'File uploaded successfully!';
        $response['error'] = false;
        $response['file_path'] = $file_upload_url . basename($_FILES['file']['name']);
		$colname = 'owners_message';
		$colname_value = $response['file_path'];
		$this->common->updaterecording($response['user_id'],$response['article_id'],$colname,$colname_value);
        
		 } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    	}
		} else {
  	    $response['error'] = true;
   	 	$response['message'] = 'Not received any file!F';
		}
	    echo json_encode($response);
	} */
	
	 public function ezine_set_ne1_recording(){
		$target_path = "uploads/audio_recording/";
		$response = array();
		$server_ip = gethostbyname(gethostname());
		$file_upload_url = 'http://' . $server_ip . '/' . $target_path;
 		if (isset($_FILES['file']['name'])) {
 		   $target_path = $target_path . basename($_FILES['file']['name']);
   		   $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
   		   $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';
		   $response['ne1_file_name'] = basename($_FILES['file']['name']);
    	   $response['user_id'] = $user_id;
		   $response['article_id'] = $article_id;
	 try {
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            $response['error'] = true;
            $response['message'] = 'Could not move the file!';
        }
        $response['message'] = 'File uploaded successfully!';
        $response['error'] = false;
        $response['file_path'] = $file_upload_url . basename($_FILES['file']['name']);
		$colname = 'ne1';
		$colname_value = $response['file_path'];
		$this->common->updaterecording($response['user_id'],$response['article_id'],$colname,$colname_value);
        
		 } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    	}
		} else {
  	    $response['error'] = true;
   	 	$response['message'] = 'Not received any file!F';
		}
	    echo json_encode($response);
	}
	
	 public function ezine_set_ne2_recording(){
		$target_path = "uploads/audio_recording/";
		$response = array();
		$server_ip = gethostbyname(gethostname());
		$file_upload_url = 'http://' . $server_ip . '/' . $target_path;
 		if (isset($_FILES['file']['name'])) {
 		   $target_path = $target_path . basename($_FILES['file']['name']);
   		   $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
   		   $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';
		   $response['ne2_file_name'] = basename($_FILES['file']['name']);
    	   $response['user_id'] = $user_id;
		   $response['article_id'] = $article_id;
	 try {
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            $response['error'] = true;
            $response['message'] = 'Could not move the file!';
        }
        $response['message'] = 'File uploaded successfully!';
        $response['error'] = false;
        $response['file_path'] = $file_upload_url . basename($_FILES['file']['name']);
		$colname = 'ne2';
		$colname_value = $response['file_path'];
		$this->common->updaterecording($response['user_id'],$response['article_id'],$colname,$colname_value);
        
		 } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    	}
		} else {
  	    $response['error'] = true;
   	 	$response['message'] = 'Not received any file!F';
		}
	    echo json_encode($response);
	}
	
	
	 public function ezine_set_ne2_inst_recording(){
		$target_path = "uploads/audio_recording/";
		$response = array();
		$server_ip = gethostbyname(gethostname());
		$file_upload_url = 'http://' . $server_ip . '/' . $target_path;
 		if (isset($_FILES['file']['name'])) {
 		   $target_path = $target_path . basename($_FILES['file']['name']);
   		   $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
   		   $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';
		   $response['ne2_inst_file_name'] = basename($_FILES['file']['name']);
    	   $response['user_id'] = $user_id;
		   $response['article_id'] = $article_id;
	 try {
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            $response['error'] = true;
            $response['message'] = 'Could not move the file!';
        }
        $response['message'] = 'File uploaded successfully!';
        $response['error'] = false;
        $response['file_path'] = $file_upload_url . basename($_FILES['file']['name']);
		$colname = 'ne2_inst';
		$colname_value = $response['file_path'];
		$this->common->updaterecording($response['user_id'],$response['article_id'],$colname,$colname_value);
        
		 } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    	}
		} else {
  	    $response['error'] = true;
   	 	$response['message'] = 'Not received any file!F';
		}
	    echo json_encode($response);
	}
	
	public function ezine_set_ne1_inst_recording(){
		$target_path = "uploads/audio_recording/";
		$response = array();
		$server_ip = gethostbyname(gethostname());
		$file_upload_url = 'http://' . $server_ip . '/' . $target_path;
 		if (isset($_FILES['file']['name'])) {
 		   $target_path = $target_path . basename($_FILES['file']['name']);
   		   $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
   		   $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';
		   $response['ne1_inst_file_name'] = basename($_FILES['file']['name']);
    	   $response['user_id'] = $user_id;
		   $response['article_id'] = $article_id;
	 try {
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            $response['error'] = true;
            $response['message'] = 'Could not move the file!';
        }
        $response['message'] = 'File uploaded successfully!';
        $response['error'] = false;
        $response['file_path'] = $file_upload_url . basename($_FILES['file']['name']);
		$colname = 'ne1_inst';
		$colname_value = $response['file_path'];
		$this->common->updaterecording($response['user_id'],$response['article_id'],$colname,$colname_value);
        
		 } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    	}
		} else {
  	    $response['error'] = true;
   	 	$response['message'] = 'Not received any file!F';
		}
	    echo json_encode($response);
	}
	
	
	 public function ezine_set_custom_article_recording(){
		$target_path = "uploads/audio_recording/";
		$response = array();
		$server_ip = gethostbyname(gethostname());
		$file_upload_url = 'http://' . $server_ip . '/' . $target_path;
 		if (isset($_FILES['file']['name'])) {
 		   $target_path = $target_path . basename($_FILES['file']['name']);
   		   $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
   		   $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';
		   $response['custom_article_file_name'] = basename($_FILES['file']['name']);
    	   $response['user_id'] = $user_id;
		   $response['article_id'] = $article_id;
	 try {
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            $response['error'] = true;
            $response['message'] = 'Could not move the file!';
        }
        $response['message'] = 'File uploaded successfully!';
        $response['error'] = false;
        $response['file_path'] = $file_upload_url . basename($_FILES['file']['name']);
		$colname = 'custom_article';
		$colname_value = $response['file_path'];
		$this->common->updaterecording($response['user_id'],$response['article_id'],$colname,$colname_value);
        
		 } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    	}
		} else {
  	    $response['error'] = true;
   	 	$response['message'] = 'Not received any file!F';
		}
	    echo json_encode($response);
	}
	
	
	 public function ezine_set_custom_article_inst_recording(){
		$target_path = "uploads/audio_recording/";
		$response = array();
		$server_ip = gethostbyname(gethostname());
		$file_upload_url = 'http://' . $server_ip . '/' . $target_path;
 		if (isset($_FILES['file']['name'])) {
 		   $target_path = $target_path . basename($_FILES['file']['name']);
   		   $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : '';
   		   $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : '';
		   $response['custom_article_inst_file_name'] = basename($_FILES['file']['name']);
    	   $response['user_id'] = $user_id;
		   $response['article_id'] = $article_id;
	 try {
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
            $response['error'] = true;
            $response['message'] = 'Could not move the file!';
        }
        $response['message'] = 'File uploaded successfully!';
        $response['error'] = false;
        $response['file_path'] = $file_upload_url . basename($_FILES['file']['name']);
		$colname = 'custom_article_inst';
		$colname_value = $response['file_path'];
		$this->common->updaterecording($response['user_id'],$response['article_id'],$colname,$colname_value);
        
        
		 } catch (Exception $e) {
        $response['error'] = true;
        $response['message'] = $e->getMessage();
    	}
		} else {
  	    $response['error'] = true;
   	 	$response['message'] = 'Not received any file!F';
		}
	    echo json_encode($response);
	}
	
}
