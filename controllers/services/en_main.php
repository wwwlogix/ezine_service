<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//error_reporting(E_ALL);
class En_main extends CI_Controller {


	public function __construct()
		{
			parent::__construct();
            $this->load->model('login');
            $this->load->model('common');
            $this->load->library('session');
            $this->load->helper('functions_helper');
            $this->load->library('email');
            $this->load->library('ciqrcode');
            $this->load->helper('form');
            $this->load->helper('functions_helper');
            define( 'API_ACCESS_KEY', 'AIzaSyDmQvawL6Z8LnWzMiWv-17vaYN3JNpq4lw' );
            $this->load->helper('url');
           // auth();
		}

	public function index()
	{
        if($this->session->userdata('user_id')=="")
        {
            $data['title']="Login";
            $data['content']=$this->load->view('main/login',$data,false);
        }
        else
        {
            redirect(base_url().'en_main/dashboard');
        }
	}
    public function verify_login ()
    {
       $user_name = $this->input->post('user_name');
       $password = $this->input->post('password');
       $query = $this->login->checklogin($user_name,$password,'0');

        if($query->num_rows()==1)
        {
            $result = $query->row();
            $this->session->set_userdata($result);
        }
       echo  $query->num_rows();

    }
    public function dashboard ()
    {
        $data['title']="Dashboard";
        $data['content']=$this->load->view('pages/dashboard',$data,true);
        $this->load->view('main/index',$data);
    }
    function log_out()
    {
        $this->session->sess_destroy();
    }
    public function locations($id)
    {
        if($id=="")
            $id='0';
        else
            $id=$id;

        $data['title']="Add new location";
        $data['category_id']=$id;
        $data['categories'] = $this->common->commonSelect("map_categories");
        $data['get_locations']=$this->common->commonselectDet("office_locations","category_id",$id);

        $data['content']=$this->load->view('pages/add_location',$data,true);
        $this->load->view('main/index',$data);
    }
    public function save_location()
    {
        //==================------------------------->
        // get lat long here

            $Address =  $this->input->post('location_name');
            $uploaddir = 'uploads/location_images/';
            $file_name = basename($_FILES['image']['name']);
            $uploadfile = $uploaddir . basename($file_name);

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile))
            {

            } else
            {
                echo "Possible file upload attack!\n";
                exit;
            }
            $data_save['office_title']=$this->input->post('office_title');
            $data_save['phone_number']=$this->input->post('phone_number');
            $data_save['address']=$this->input->post('address');
            $data_save['image_url']=$file_name;
            $data_save['location_add_date']=date('Y-m-d H:i:s');
            $data_save['location_lat']=$this->input->post('lat');
            $data_save['location_long']=$this->input->post('long');
            $data_save['category_id']=$this->input->post('categories');
            $this->db->insert("office_locations",$data_save);
            redirect(base_url().'en_main/locations/'.$this->input->post('categories').'?st=1');

    }
    public function commom_delete()
    {
        $id = $this->input->post('id');
        $table = $this->input->post('table');
        $col = $this->input->post('col');
        $this->common->commonDelete($table,$col,$id);

    }
    function edit_location($id)
    {
        $data['title']="Edit Location";
        $data['categories'] = $this->common->commonSelect("map_categories");
        $data['get_location_edit']=$this->common->commonselectDet("office_locations","location_id",$id);
        $data['content']=$this->load->view('pages/edit_location',$data,true);
        $this->load->view('main/index',$data);
    }
    function update_location ()
    {
        //==================------------------------->
        // get lat long here
        $Address =  $this->input->post('location_name');
        $id =  $this->input->post('id');
        $uploaddir = 'uploads/location_images/';
        if($_FILES['image']['name']!="")
        {
            $file_name = basename($_FILES['image']['name']);
            $uploadfile = $uploaddir . basename($file_name);

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile))
            {

            } else
            {
                echo "Possible file upload attack!\n";
                exit;
            }
            $data_save['image_url']=$file_name;
        }
        $data_save['office_title']=$this->input->post('location_name');
        $data_save['phone_number']=$this->input->post('phone_number');
        $data_save['address']=$this->input->post('address');
        $data_save['location_add_date']=date('Y-m-d H:i:s');
        $data_save['location_lat']=$this->input->post('lat');
        $data_save['location_long']=$this->input->post('long');
        $data_save['category_id']=$this->input->post('categories');
        $this->common->common_update("office_locations",$data_save,"location_id",$id);
        redirect(base_url().'en_main/locations/'.$this->input->post('categories').'?st=3');
        //==================------------------------->

    }
    function pending_requests ()
    {
        $data['title']="User Pending Requests";
        $data['pending_requests']=$this->common->commonselectDet("user_profile","member_ship_id","");
        $data['content']=$this->load->view('pages/pending_requests',$data,true);
        $this->load->view('main/index',$data);
    }
    public function approve_request($id)
    {
        $membership_id = rand();
        $data_save['member_ship_id'] = $membership_id;
        $this->common->common_update("user_profile",$data_save,"profile_id",$id);
        $result = $this->common->get_info($id);
        $data = $result->row();
        //----------------------------email library=========================>

        //=============================send email
        $result = $this->common->commonselectDet("users","user_id",$id);

        $data = $result->row();
        $user_email = $data->user_email;
        $user_pass = $data->orignal_password;
        $user_name = $data->user_name;
        $this->email->from('membership@wwwlogix.com', 'Member Ship');
        $this->email->set_mailtype("html");
        $this->email->to($user_email);
        $this->email->subject('Login Information ');
        $this->email->message('Hello '.$user_name.' Here is your login information  <br /> Membership ID = '.$membership_id.' <br /> Password is '.$user_pass);
        $this->email->send();

//        echo $this->email->print_debugger();
        redirect(base_url().'en_main/pending_requests?status=1');
    }
    public function member_list($id)
    {
        $data['title']="Members  List";
        $data['pending_requests']=$this->common->get_approved_members_list();
        $data['content']=$this->load->view('pages/pending_requests',$data,true);
        $this->load->view('main/index',$data);

    }
    function user_detail($id)
    {
        $data['get_detail']=$this->common->commonselectDet("user_profile","profile_id",$id);
        $data['title']="User Detail ";
        $data['content']=$this->load->view('pages/user_detial',$data,true);
        $this->load->view('main/index',$data);

    }
    public function url()
    {
        $token = $_GET['token'];
        $data['links'] = $this->common->get_urls($token);
        if($_GET['id']!="")
        {
            $data['get_detail']=$this->common->commonselectDet("links","link_id",$_GET['id']);
            $data['title']="Edit  ".ucfirst($token)." URL";
        }
        else
        {
            $data['title']="Add New ".ucfirst($token)." URL";
        }
        $data['content']=$this->load->view('pages/add_urls',$data,true);
        $this->load->view('main/index',$data);
    }
    function save_url()
    {
        $uploaddir = 'uploads/urlimages/';
        $file_name = basename($_FILES['image']['name']);
        $uploadfile = $uploaddir . basename($file_name);

        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile))
        {

        } else
        {
            echo "Possible file upload attack!\n";
            exit;
        }
        $linkType = $this->input->post('type');
        $data_save['link_title']=$this->input->post('title');
        $data_save['link_url']=$this->input->post('url');
        $data_save['link_image']=$file_name;
        $data_save['link_type']=$linkType;
        $data_save['link_add_date']=date("Y-m-d H:i:s");
        //========================
        $this->db->insert("links",$data_save);
        redirect(base_url()."en_main/url/".$linkType);
    }
    function update_url()
    {
        $link_id = $this->input->post('link_id');
        $file_namne = $_FILES['image']['name'];
        if($file_namne=="")
        {}
        else
        {
            if($_FILES['image']['name']!="") {
                $uploaddir = 'uploads/urlimages/';
                $file_name = basename($_FILES['image']['name']);
                $uploadfile = $uploaddir . basename($file_name);

                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadfile)) {

                } else {
                    echo "Possible file upload attack!\n";
                    exit;
                }
            }
            $data_save['link_image']=$file_name;
        }

        $linkType = $this->input->post('type');
        $data_save['link_title']=$this->input->post('title');
        $data_save['link_url']=$this->input->post('url');

        $data_save['link_type']=$linkType;
        $data_save['link_add_date']=date("Y-m-d H:i:s");
        //========================
        $this->common->common_update("links",$data_save,"link_id",$link_id);
        redirect(base_url()."en_main/url?token=".$linkType);
    }
    function categories()
    {
        if($_GET['id']!="")
        {
            $data['get_detail']=$this->common->commonselectDet("map_categories","map_cat_id",$_GET['id']);
            $data['title']="Edit Map Categories ";
        }
        else
        {
            $data['title']="Map Categories ";
        }
        $data['categories'] = $this->common->commonSelect("map_categories");
        $data['content']=$this->load->view('pages/map_categories',$data,true);
        $this->load->view('main/index',$data);
    }
    function save_categories ()
    {
        $data_save['cat_name']=$this->input->post('cat_title');
        $data_save['cat_add_date']=date("Y-m-d H:i:s");
        $data_save['cat_status']='0';
        //========================
        $this->db->insert("map_categories",$data_save);
        redirect(base_url()."en_main/categories");
    }
    function update_cates()
    {
        $map_cat_id=$this->input->post('cat_id');
        $data_save['cat_name']=$this->input->post('cat_title');
        $this->common->common_update("map_categories",$data_save,"map_cat_id",$map_cat_id);
        redirect(base_url()."en_main/categories");
    }
    function polling()
    {
        $data['title']="Polling Question List";
        $data['question'] = $this->common->commonSelect("polling");
        $data['content']=$this->load->view('pages/polling_questions',$data,true);
        $this->load->view('main/index',$data);
    }
    function save_question()
    {
        $data_save['poll_question']=$this->input->post('question');
        $data_save['add_date']=date("Y-m-d H:i:s");
        $data_save['poll_status']='0';
        //========================
        $this->db->insert("polling",$data_save);
        $inser_id = $this->db->insert_id();
        $result = $this->common->get_deviceToken();
        foreach($result->result() as $token)
        {
            $device_token = str_replace("Device registered, registration ID=","",$token->device_token);
            if($device_token!="")
                $this->send_push($device_token,$this->input->post('question'),$inser_id);
        }
        redirect(base_url()."en_main/polling?st=1");
    }
    function polling_results($id)
    {
        $data['title']="Results";
        $data['polling_results'] = $this->common->polling_results($id);
        $data['content']=$this->load->view('pages/polling_results',$data,true);
        $this->load->view('main/index',$data);
    }
    function forumCategories()
    {
        $data['category_edit']="";
        $data['title']="Forum Categories ";
        $data['forum_categories'] = $this->common->commonSelect("categories");
        $data['content']=$this->load->view('pages/forum_categories',$data,true);
        $this->load->view('main/index',$data);
    }
    function save_forum_categories()
    {
        $data_save['name'] = $this->input->post('categories_title');
        $data_save['position'] = $this->input->post('categories_postion');
        $data_save['description'] = $this->input->post('forum_desc');
        $this->db->insert("categories",$data_save);
        redirect(base_url()."en_main/forumCategories?st=1");
    }
    function edit_forum_categories($id)
    {
        $data['title'] = "Edit Categories ";
        $data['category_edit'] = $this->common->commonselectDet("categories","id",$id);
        $data['forum_categories'] = $this->common->commonSelect("categories");
        $data['content']=$this->load->view('pages/forum_categories',$data,true);
        $this->load->view('main/index',$data);
    }
    function update_forum_categories()
    {
        $id = $this->input->post('id');
        $data_save['name'] = $this->input->post('categories_title');
        $data_save['position'] = $this->input->post('categories_postion');
        $data_save['description'] = $this->input->post('forum_desc');
        $this->common->common_update("categories",$data_save,"id",$id);
        redirect(base_url()."en_main/forumCategories?st=1");
    }
    function forum($id)
    {
        $data['cate_name'] = $this->common->commonselectDet("categories","id",$id);
        $data['topics'] = $this->common->topics($id);
        $data['category_id']=$id;
        $data['title']="Forum Topics ";
        $data['content']=$this->load->view('pages/forum_topics',$data,true);
        $this->load->view('main/index',$data);
    }
    function save_topic()
    {
        $topic_title=$this->input->post('topic_title');
        $cat_id = $this->input->post('id');
        $topic_desc=$this->input->post('topic_desc');
        $user_id=$this->session->userdata('user_id');

        $this->common->create_new_topic1($topic_title,$cat_id,$topic_desc,$user_id);
        $inser_id = $this->db->insert_id();
        $inser_id = get_topic_id();
        //================ push notifications
        $result = $this->common->get_deviceToken();
        foreach($result->result() as $token)
        {
            $device_token = str_replace("Device registered, registration ID=","",$token->device_token);
            if($device_token!="")
                $this->send_push($device_token,$topic_title,$inser_id);
        }
        redirect(base_url()."en_main/forum/".$cat_id);
    }
    function  send_push($token,$msg,$id)
    {
        // API access key from Google API's Console

        $registrationIds = array($token);
// prep the bundle
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
        echo $result;
    }
}
