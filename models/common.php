<?php
class Common extends CI_Model {
   
    function __construct() 
	{
        parent::__construct();
	}

//------------select all----------		
    public function general($tbl, $colname="",$value,$order="") 
	{
           $this->db->select("*");
		   $this->db->from($tbl);
		   $this->db->where($colname,$value);
		   $query=$this->db->get();
			return $query;
	}
	public function checklogin($user_name,$user_password,$user_type="") 
	{
           $query=$this->db->query("SELECT * FROM en_admins WHERE usr_name='".$user_name."'
		   							AND usr_password='".$user_password."' AND usr_status='1'");
			return $query;
	}
	function commonInsert($table,$data)
     {
	   $this->db->insert($table,$data); 
	   return $this->db->insert_id();
	 }
	 
	 //...................Function user for Gettting the Common Select 	 
	 function commonSelect($table,$data="")
       {
			$this->db->select('*');
			$this->db->from($table);
	       	$query=$this->db->get();
		    return $query;
	    	
	   }
	   function commonDelete($table,$col,$val)
		{
		   $this->db->where($col,$val);
		   $this->db->delete($table);
		}
		
		function editcategory($id)
		{
	   //Update The category
		  return $this->db->query("SELECT * FROM en_categories WHERE `cat_id`='$id' ");
      }  
	  function editmenu($id)
		{
			//echo"dddccc";
	   //Update The menu
	   return $this->db->query("SELECT * FROM en_menu WHERE `men_id`='$id' ");
        }  
		function editservices($id)
			{
			 return $this->db->query("SELECT * FROM  en_services WHERE `ser_id`='$id'");	
			}
		function editportfolio($id)
		{
			//echo"dddccc";
	   //Update The Portfolio
	   return $this->db->query("SELECT * FROM en_portfolio WHERE `port_id`='$id' ");
        }  
		
	   function common_update($tbl,$data,$col,$id)
		{
	   //Update The menu
		  $this->db->where($col, $id);
          $this->db->update($tbl,$data);
        }  
		function get_services()
		{
			return $this->db->query("SELECT * FROM en_services ORDER BY ser_name ASC");
		}
		function get_port_services($id)
		{
			return $this->db->query("SELECT * FROM port_services 
									INNER JOIN en_services ON por_services_id=ser_id
									WHERE por_porrfolio_id='".$id."'
									ORDER BY ser_name ASC");
		}
	function commonselectDet($tablename,$col_name,$value, $order_by='', $order="ASC")
	 {
		$this->db->select('*');
		$this->db->from($tablename);
		$this->db->where($col_name,$value); 
		if($order_by!=''){
			$this->db->order_by($order_by, $order);	
		}
		$query = $this->db->get(); 		    
		return $query;
	 }
	function portfolioservices($id)
	{
		return $this->db->query("SELECT * FROM port_services 
									INNER JOIN en_services ON por_services_id=ser_id
									WHERE por_porrfolio_id='".$id."'
									");
	}
    function loginCheck($tablename,$col_name,$value,$col_name1,$value1)
    {
        $this->db->select('*');
        $this->db->from($tablename);
        $this->db->where($col_name,$value);
        $this->db->where($col_name1,$value1);
//        $this->db->join('user_profile', 'user_profile.user_id = '.$tablename.'.user_id');
        $this->db->join('user_memership', 'user_memership.user_profile_id= user_profile.profile_id', 'left');
        $query = $this->db->get();
        return $query;
    }
    function verify_number($number,$full_name)
    {
        $this->db->select('*');
        $this->db->from("user_profile");
        $this->db->where("member_ship_id",$number);
        $this->db->where("full_name",$full_name);
        $query = $this->db->get();
        return $query;
    }
    function get_info($id)
    {
        return $this->db->query("SELECT email,profile_id FROM user_profile WHERE profile_id='".$id."'");
    }
    function employee_card($tablename,$user_id)
    {
        $this->db->select('profile_picture,full_name,date_of_birth,profile_picture,full_name,date_of_birth,profile_id,user_profile_id,position_in_org,
        province,city_district,sub_district,user_id,qr_code');
        $this->db->from($tablename);
        $this->db->where("profile_id",$user_id);
        $this->db->join('user_memership', 'user_memership.user_profile_id = '.$tablename.'.profile_id');
        $query = $this->db->get();
        return $query;
    }
    function get_urls($type)
    {
        $this->db->select('*');
        $this->db->from("links");
        $this->db->where("link_type",$type);
        $query = $this->db->get();
        return $query;
    }
    function get_approved_members_list()
    {
        return $this->db->query("SELECT * FROM user_profile /*INNER JOIN user_memership ON user_profile.profile_id=user_memership.user_profile_id*/ WHERE  member_ship_id!='0'");
    }
    function edit_proifle_records($id)
    {
        $this->db->select('*');
        $this->db->from("user_profile");
//        $this->db->where($col_name,$value);
        $this->db->where("profile_id",$id);
//        $this->db->join('users', 'user_profile.user_id = users.user_id');
        $this->db->join('user_memership', 'user_memership.user_profile_id= user_profile.profile_id');
        $query = $this->db->get();
        return $query;
    }
    function polling_results($id)
    {
        $this->db->select('*');
        $this->db->from("polling_results");
        $this->db->where("poll_id",$id);
        $this->db->join('user_profile','user_profile.profile_id=polling_results.user_id');
        $this->db->join('polling','polling_id=poll_id');
        $query = $this->db->get();
        return $query;
    }
    function checkAnswers ($uesr_id,$polling_id)
    {
       return $this->db->query("SELECT * FROM polling_results WHERE  user_id='".$uesr_id."' AND  poll_id='".$polling_id."'");
    }
    function chkPayments ($date,$id)
    {
      return $dbQuery = $this->db->query("SELECT * FROM user_payments WHERE  profile_id='".$id."' AND  payment_date >= '".$date."' ORDER BY payment_id DESC LIMIT 1");
    }
    function topics($id)
    {
        return $dbQuery = $this->db->query("SELECT * FROM topics WHERE `parent` = '".$id."' AND title!=''");
    }
    function get_cat_topics($id)
    {
        return $dbQuery = $this->db->query("SELECT * FROM topics INNER JOIN users ON authorid = user_id WHERE `parent` = '".$id."' AND title!=''");
    }
    function get_topics_replies($id)
    {
        return $dbQuery = $this->db->query("SELECT parent,
                                                id,
                                                id2,
                                                message,
                                                authorid,
                                                timestamp,
                                                timestamp2,profile_id,
                                                profile_picture,
                                                full_name
                                                  FROM topics INNER JOIN user_profile ON authorid = profile_id WHERE `id` = '".$id."' AND id2 > 1 ");
    }
    function create_new_topic($jsonData)
    {
        return $dbQuery = $this->db->query('insert into topics (parent, id, id2, title, message, authorid, timestamp, timestamp2) select "'.$jsonData['cate_id'].'", ifnull(max(id), 0)+1, "1", "'.$jsonData['title'].'", "'.$jsonData['message'].'", "'.$jsonData['profile_id'].'", "'.time().'", "'.time().'" from topics');
    }
    function create_new_topic_reply($jsonData)
    {

        $id = $jsonData['topic_id'];
        $query1=$this->db->query('select count(t.id) as nb1, t.title, t.parent, c.name from topics as t, categories as c where t.id="'.$jsonData['topic_id'].'" and t.id2=1 and c.id=t.parent group by t.id');
        $dn1 = $query1->row();
        return $dbQuery = $this->db->query('insert into topics (parent, id, id2, title, message, authorid, timestamp, timestamp2) select "'.$dn1->parent.'", "'.$id.'", max(id2)+1, "", "'.$jsonData['message'].'", "'.$jsonData['profile_id'].'", "'.time().'", "'.time().'" from topics where id="'.$id.'"') and mysql_query('update topics set timestamp2="'.time().'" where id="'.$id.'" and id2=1');
    }
    function get_deviceToken()
    {
        $this->db->select('device_token');
        $this->db->from("user_profile");
        $query = $this->db->get();
        return $query;
    }
    function create_new_topic1($topic_title,$cat_id,$topic_desc,$user_id)
    {
        return $dbQuery = $this->db->query('insert into topics (parent, id, id2, title, message, authorid, timestamp, timestamp2) select "'.$cat_id.'", ifnull(max(id), 0)+1, "1", "'.$topic_title.'", "'.$topic_desc.'", "'.$user_id.'", "'.time().'", "'.time().'" from topics');
    }
	/************************ Ezine ****************************** */
	
	
	
	function ezine_checklogin($username,$password)
    { 
		$query = "SELECT *
				  FROM ezine_user_table
				  WHERE username = '".$username."' 
				  AND password = '".$password."' ";
       return $this->db->query($query);
    }
	
	function checkcompanyname($user_id,$companyname)
    { 
		$query = "SELECT *
				  FROM ezine_user_table
				  WHERE user_id = '".$user_id."' 
				  AND companyname != '' ";
       return $this->db->query($query);
    }
	
	function updatecompanyname($user_id,$companyname)
    { 
		$query = "UPDATE ezine_user_table
				 SET companyname = '".$companyname."' 
				 WHERE user_id = '".$user_id."'";
      
	   return $this->db->query($query);
    }
	
	function get_user_article_recording($user_id,$article_id)
    { 
		$query = "SELECT *
				  FROM ezine_recording_table
				  WHERE user_id = '".$user_id."' 
				  AND article_id = '".$article_id."' ";
       return $this->db->query($query);
    }
	
	function get_user_sessionstatus_recording($user_id,$session_Completed)
    { 
		$query = "SELECT *
				  FROM ezine_recording_table
				  WHERE user_id = '".$user_id."' 
				  AND session_Completed = '".$session_Completed."' ";
       return $this->db->query($query);
    }
	
	function get_aricle_month($article_id)
    { 
		$query = "SELECT `article_month` , `article_year`
				  FROM ezine_article_table
				  WHERE article_id = '".$article_id."' ";
       return $this->db->query($query);
    }
	function updaterecording($user_id,$article_id,$colname,$colname_value)
    { 
		$query = "UPDATE ezine_recording_table
				 SET $colname = '".$colname_value."' 
				 WHERE user_id = '".$user_id."'
				 AND article_id = '".$article_id."'";
      
	    $this->db->query($query);
		
		$query = "SELECT *
				  FROM ezine_recording_table
				  WHERE user_id = '".$user_id."' 
				  AND article_id = '".$article_id."'";
      
	   $result =  $this->db->query($query);
	   $data   = $result->row();
	  if($data->owners_message && $data->ne1 && $data->ne1_inst && $data->ne2 && $data->ne2_inst && $data->custom_article && $data->custom_article_inst )
	  {
		  $sess_completed = 1;
		$query = "UPDATE ezine_recording_table
				 SET  session_Completed =  '".$sess_completed."'
				 WHERE user_id = '".$user_id."'
				 AND article_id = '".$article_id."'"; 
				 
	   return $this->db->query($query);
	  }
	   
    }
	function updaterecording_session($user_id,$article_id,$colname,$colname_value)
    { 
		$sess_completed = 1;
		$query = "UPDATE ezine_recording_table
				 SET $colname = '".$colname_value."',
				 session_Completed =  '".$sess_completed."'
				 WHERE user_id = '".$user_id."'
				 AND article_id = '".$article_id."'"; 
				 
	   return $this->db->query($query);
    }
	
	function checkrecording($user_id,$article_id)
    { 
		
		$query = "SELECT *
				  FROM ezine_recording_table
				  WHERE user_id = '".$user_id."' 
				  AND article_id = '".$article_id."'";
      
	   return $this->db->query($query);
    }
}

 