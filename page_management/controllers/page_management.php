<?php
/**
 * Page  Management - class
 * To show the pages
 * 
 * Currrent class must call the constructor of the Base Class called Controller
 */
class page_management extends MX_Controller{    
	
	var $msg 				= "";	
	var $errors 			= "";
	var $database_limit		= "5";
	var $mode 				= "";
	var $page_name			= "page_management";	
	var $bread_crumb = array();
	
		
    function __construct()
	{
		parent::__construct();
		// load 'DB_Interaction' model	
		//load 'Page Db Interaction module for page creation'
		 $this->page_db_interaction = $this->load->model('Page_DB_Interaction');	
		 $this->db_interaction = $this->load->model('DB_Interaction');		
		// load Helpers
		$this->load->helper(array('date','text'));
		$this->page_name = $this->uri->segment(1);	
		
		
	}
    
	/**
	 * Index
	 *
	 * This is like index file which get called by default
	 * Using the mode and a switch case we will  controlthe flow of the functions
	 *
	 */
    function index()
	 {	
	
		if(trim($this->uri->segment(1)) != '' && trim($this->uri->segment(1)) != 'home'&& trim($this->uri->segment(1)) != 'history_preview')
		{
			//Call show_list by default
			$this->_show_page(trim($this->uri->segment(1)));			
		}
		
		elseif($this->uri->segment(1) == 'history_preview')
		{
			
				$this->history_preview();
		}
		else
		{
			//featured product listing
			$variants	= array();
			$where 										= " featured= 1 AND status = 1";
			$data['image_path'] 			= ROOTBASEPATH . "media/images/product/";
			$data['product'] 					= $this->db_interaction->get_records_query(0,8,$where,TBL_PRODUCT);	
			

			//Load the default welcome page.
			$this->_display('welcome_message',$data);			
		}		
			
	}
	
	
	/**
	 * Show List
	 *
	 * Lets you list the records in the database.
	 * All records that are not in trash are listed.
	 *
	 * @access	public
	 * @param	integer - default set to 0 for pagination
	 */	
	function _show_page($page='')
	{		
     	 $page_identity = $page;
				 
		 $bread_crumb 	= $this->load->library('bread_crumb');
		 if($page_identity !='')
		 {
  			  $bread_crumb->bread_crumb($page_identity);
			  //$bread_crumb = array_reverse($bread_crumb->return_crumb);
			  $bread_crumb_array_temp = $bread_crumb->bread_crumb;
			  if(is_array($bread_crumb_array_temp))
			  		$bread_crumb_array = array_reverse($bread_crumb_array_temp);
			  else
			  		$bread_crumb_array = $page_identity;	
		 }	 
		 else
		 	 $bread_crumb_array = '';
		 
		
		//$where 		= "page_name = ".$this->db->escape($page)." AND status =1 ";
		$where 		= "page_name = ".$this->db->escape($page)."";
		//get all page details based on the page identity(page unique name)		
		$page_details  	= $this->db_interaction->get_records_use_query($where);  
		
		if(is_array($page_details) && count($page_details) > 0)
		{
			$data = $page_details[0];
			
			if($data['status'] == 1)
			{
				//IF THE PAGE IS ACTIVE THAN DISPLAY THE PAGE 
				$data['bread_crumb']	= $bread_crumb_array;
				$data["fck_data"] = str_replace("#base_url#",WEBSITEURL,$page_details[0]["page_html_data"]);				
				$data["fck_data"] = str_replace("#base_url_index#",WEBSITEURLINDEX,$data["fck_data"]);
			
				$this->_display("frontend",$data);
			}
			else
			{
				$redirect_details = $this->db_interaction->get_page_name($data['redirect_page_id']);
				
				if(is_array($redirect_details) && count($redirect_details)>0)
				{
					$redirect_page_name   = $redirect_details[0]['page_name'];
					$redirect_page_status = $redirect_details[0]['status'];					
					$redirect_page_id     = $redirect_details[0]['id'];					
					if($redirect_page_status == 1)
					{
						//SINCE IF THE PAGE IS ENABLED THAN SHOW ELSE TO HOME PAGE
						if($redirect_page_id == 10)
							redirect("",'location',301);
						else
							redirect($redirect_page_name,'location',301);
					}
					else
					{
						redirect("",'location',301);
					}
				}//ENDOF if(is_array($redirect_details)...
				else
				{
					redirect("",'location',301); //SINCE FOR PAGES CREATED BY CLIENT THERE WILL BE NO REDIRECTOR .
				}
			}
		}
		else
		{
			redirect("",'location',301);
		}
	}
	
	/**
	 * Display
	 *
	 * Lets you display layout
	 * It loads the header file, the default messages file, the module view file and the footer
	 * Note : This function cannot be accessed directly through browser as it is private to the class.
	 *
	 * @access	private
	 * @param	string
	 * @param	array	
	 */
	function _display($viewname,$data)
	{
		
		//getting the page details
		$page_identity = $this->uri->segment(1);
		 if($page_identity == '')
		 {
			$page_identity = 'home';
		 }
		$page_details  = $this->page_db_interaction->get_records_where("page_name",$page_identity);    		

		if(count($page_details) > 0){
			$page_sub_details  = $this->page_db_interaction->get_records_where("page_parent_id",$page_details[0]['id']); 
			
			$data['sub_page_details'] = $page_sub_details;				
			if(!empty($page_details[0]["page_html_data"]))
			{
			$data["fck_data"] = str_replace("#base_url#",WEBSITEURL,$page_details[0]["page_html_data"]);
			$data["fck_data"] = str_replace("#base_url_index#",WEBSITEURLINDEX,$data["fck_data"]);	
			}
			else
				$data["fck_data"] = '';
		}
		
		
		$this->page_name = trim($page_identity);
		// load view and add additional data 
		$data["page_details"] = $page_details;
		
		//FOR WEB MAGNET STATUS
			$data['web_magnet'] = $this->page_db_interaction->web_magnet();	
	
		$this->load->view($page_details[0]["page_header"],$data);			
	   $this->load->view($viewname,$data);
		$this->load->view($page_details[0]["page_footer"],$data);
    }
	
	//------------------------------------------------------------------------------------------------
	/*
	| Function ot print the History Details of page
	*/
	function history_preview()
	{
		
	 $page_identity = $this->uri->segment(2);
	 $bread_crumb 	= $this->load->library('bread_crumb');
	 if($page_identity !='')
	 {
				$page_details  	= $this->db_interaction->get_history_data($page_identity);  
		
				if(is_array($page_details) && count($page_details) > 0)
				{
					$data 				= $page_details[0];
					$page_name			= $data['page_name'];
					$bread_crumb->bread_crumb($page_name);
					$bread_crumb_array_temp = $bread_crumb->bread_crumb;
						if(is_array($bread_crumb_array_temp))
								$bread_crumb_array = array_reverse($bread_crumb_array_temp);
						else
								$bread_crumb_array = $page_name;	
				 }	 
				 else
					 $bread_crumb_array = '';
					
					
					$data['bread_crumb']	= $bread_crumb_array;
					$data["fck_data"] = str_replace("#base_url#",WEBSITEURL,$page_details[0]["page_html_data"]);				
					$data["fck_data"] = str_replace("#base_url_index#",WEBSITEURLINDEX,$data["fck_data"]);
					$this->page_name = trim($page_identity);
					$data["page_details"] = $page_details;
					$data['web_magnet'] = $this->page_db_interaction->web_magnet();	
					$this->load->view($page_details[0]["page_header"],$data);			
					$this->load->view('frontend',$data);
					$this->load->view($page_details[0]["page_footer"],$data);
				
		}//EN DO Fif(is_array($page_details) && count($page_details) > 0)		
		else
		{
			redirect("",'location',301);
		}
	}
	
	
}
?>