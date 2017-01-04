<?php
class Selection_model extends CI_Model {
   
    function __construct() 
	{
        parent::__construct();
	}

//------------select all----------		
    public function SelectAll($tbl, $where="") 
	{
       
           $query = $this->db->query('SELECT member_name, member_designation, member_image,
		   								 member_join_date, member_salary, member_degree, 	
										 member_status, member_description FROM en_members');
			return $query;
	}
}
 ?>
 
 