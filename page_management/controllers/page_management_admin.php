<?php
/**
 * Page  Management - class
 * To create new pages
 * 
 * Currrent class must call the constructor of the Base Class called Controller
 */
class page_management_admin extends Controller{    
	
	var $msg 				= "";	
	var $errors 			= "";
	var $database_limit		= "5";
	var $mode 				= "";
	var $page_name			= "page_management/page_management_admin";
	var $parent_controller	= "page_management";
	var $form_name			= "form_page_management_admin";
	var $page_h1			= "Page Creation - DPCS";
	var $banner_page		= TBL_BANNERS_PAGE;
	var $option_list 		= array();
	var $dashboard_category	= array(
									1 => 'web_site_pagecontrol',
									'system_tools',
									'special_purpose_cms_tools',
									'ecommerce_tools',
									'gallery_tools',
									'customer_relationship_tools',
									'demo_resources'
								  );
	var $page_name_rules		= '\.\:\-_ a-z0-9'; // alpha-numeric, dashes, underscores, colons or periods
	
    function page_management_admin()
		{
				parent::Controller();
				//Check login
				$this->load->module('login');
				
				// load 'DB_Interaction' model
				$this->load->model('DB_Interaction');
				$this->load->model('Page_DB_Interaction');
				// load library
				$this->load->library('Table');
					/*-----------------------------------------------------------------------*/ 
				//URL TO HELP FOR REDIRECT WHEN USER LOGS OUT
				/*
				| Basic REASON behind these is that Ajax Call should not be part of the REMEMBER URL in lOGIN function
				*/
					$this->session->unset_userdata('LastUrl');	
					$this->session->set_userdata('LastUrl', $this->page_name);
				 /*-----------------------------------------------------------------------*/ 
				// load Helpers
				$this->load->helper(array('date','text','useful'));
						
			
				$l = new Login();
				if($l->_is_logged_in())
				{
				} 
				else 
				{
					redirect('login');
				}
				
				//---------------------------CHECK MODULE ACESS--------------------------
				if($this->session->userdata('user') != 'admin@exateam.com')
				{
					if(!$l->_module_access($this->parent_controller))
					{
						$this->session->set_flashdata('invalid_url_access','You do not Have Sufficient Priviledge to Access the Page !');
						redirect('adminx');
					}
				}
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
				//Call show_list by default
				$this->show_list();		
			
		}

	
	/**
	 * Search
	 *
	 * Lets you list the records in the database as oer the given search criteria
	 * All records that are not in trash are listed.
	 *
	 * @access	public
	 * @param	integer - default set to 0 for pagination
	 */

	/*----------------------------------------------------------------------
	| Function to build the arrey in the format that the slider effect can take place
	*/
	function search($per_page=0)
	{
		$query 						 	= "";
		$page_management 	 	=  array();
		$tree 		  	 		 	= array();
		$history_result_ids = array();
		
			if($this->input->post("search_filter") )		
				$search_filter	= $this->input->post("search_filter");		
			else
				$search_filter	= $this->session->userdata('search_filter');		
		
				$search_filter = trim(quotes_to_entities($search_filter));
					
			if(empty($search_filter))
				redirect($this->page_name);
		
			if($this->session->userdata('success_message'))
			{
				$this->msg = $this->session->userdata('success_message');
			}	
			if($this->session->flashdata('invalid_access'))
			{
				$this->errors = $this->session->flashdata('invalid_access');
			}
				$data 		= array(
										"pagetitle" 	=> "List of page(s) in system",					  
										"mode"				=> "show_list",
										"msg" 				=> $this->msg,	
										"errors" 			=> $this->errors
									);
							
		
		
		if($this->session->userdata('user') == 'admin@exateam.com')
		{
			$users_result 			= $this->db_interaction->get_dpcs_details_search(0,$search_filter);
			$total_rows 	  		= $this->db_interaction->get_dpcs_details_searchcount(0,$search_filter);
			$user_details_sub		= $this->db_interaction->get_user_details(); //EXPLICITLY TO FETCH THE USER DETAILS FROM THE SYSTEM
			$where_trash_rows		= "status = 2";
			
		}
		else
		{
		   $user_id_sesison 					= $this->session->userdata('user_id');
			 $allowed_modules_session   = isset($this->session->userdata['modules_allowed']['pid_frontend'])?$this->session->userdata['modules_allowed']['pid_frontend']:'';
			 if($allowed_modules_session!='')
					 $modules_allowed        = implode(',',array_keys($allowed_modules_session));
			 else
					 $modules_allowed        = 0;
					 
			 $users_result 							= $this->db_interaction->get_dpcs_details_search(1,$search_filter,$modules_allowed,$user_id_sesison);
		   $total_rows 								= $this->db_interaction->get_dpcs_details_searchcount(1,$search_filter,$modules_allowed,$user_id_sesison);
			 $user_details_sub					= array();
			 
			 $data["cnt_non_subpage"]		= $this->count_non_subpages($user_id_sesison);
			 $data["cnt_db_subpage"]		= $this->count_db_subpages($user_id_sesison);
			 
			 $where_trash_rows					= " status = 2 AND (ID IN (".$modules_allowed.") OR  user_id = ".$user_id_sesison.")";
		}
		
		$SQL_HISTORY_INDICATOR = "SELECT 
																				id,page_id 
															FROM 
																				".TBL_PAGE_HISTORY." 
															GROUP BY 
																					page_id";
		$history_result = $this->db_interaction->run_query($SQL_HISTORY_INDICATOR);
		if(is_array($history_result) && count($history_result)>0)
		{
			foreach($history_result as & $history_result_details)
			{
					$history_result_ids[$history_result_details['id']] = $history_result_details['page_id'];
			}//END OF foreach($users_result as & $user_details)
		}//END OF if(is_array($users_result) && count($users_result)>0)
		
		if(is_array($users_result) && count($users_result)>0)
		{
			foreach($users_result as & $user_details)
			{
					if(in_array($user_details['id'],$history_result_ids))
					{
						$user_details['history_id'] = 1;
					}
					else
					{
							$user_details['history_id'] = 0;
					}
			}//END OF foreach($users_result as & $user_details)
		}//END OF if(is_array($users_result) && count($users_result)>0)
		
		if(is_array($total_rows) && count($total_rows)>0)
			$total_rows_count = $total_rows[0]['PAGE_COUNT'];
		else
			$total_rows_count = 0;
			
		$data['cnt_total_rows'] 		= $total_rows;
		$data['users_result']     	= $users_result;
		
		$data['all_pages']							= $this->_get_all_pages();
		$data['user_sub_page_details']	= $user_details_sub;
		
		$SQL = "SELECT wm_client FROM ".TBL_WEB_MAGNET."";
		$wm_client = $this->db_interaction->run_query($SQL);
		$data['web_magnet'] = $wm_client[0]["wm_client"];
		
		$trash_rows 					= $this->db_interaction->get_dpcs_inactive_count($where_trash_rows);
		if(is_array($trash_rows) && count($trash_rows)>0)
		{
			$data['trash_rows']		= $trash_rows[0]['INACTIVE_PAGE'];
		}
		else
		{
			$data['trash_rows']		= 0;
		}
		
		
		//Unset session data
		$this->_clear_search_filters();
		$this->session->set_userdata('search_filter', $search_filter);		
		// load view and add additional data 
		$this->_display("list_search",$data);
		
		
	}
	 
	
	
	/**
	 * Search Trash
	 *
	 * Lets you list the records in the database as oer the given search criteria
	 * All records that are in trash are listed.
	 *
	 * @access	public
	 * @param	integer - default set to 0 for pagination
	 */	
	function search_trash($per_page=0)
	{
		if(isset($_POST['search_filter']))		
			$search_filter	= get_param("search_filter");		
		else
			$search_filter	= $this->session->userdata('search_filter');		
		
		$search_filter = trim(quotes_to_entities($search_filter));
		
		if(empty($search_filter))
			redirect($this->page_name."/show_trash_list");
		
		$data	= array("pagetitle" 		=> "Search results for '".$search_filter."' in trash",
						"search_filter"		=> $search_filter,
						"mode"				=> "search",
					  	"msg" 				=> $this->msg,	
					 	"errors" 			=> $this->errors);		
		
		
		
		// load pagination library
		$this->load->library('Digg_Pagination');
		//********************SET UP PAGINATION VALUES****************************
        //set up per_page_value, per_page_seg, cur_page_seg and $data['pbase_url']
        //************************************************************************
        $this->load->plugin('pagination');
        
        $per_page_value = 50;  //default - unless overridden later
        $per_page_seg = 4;    //the uri segment for the per page value
        $cur_page_seg = 5;    //the url segment for the current page value (generally +1 of per page seg)
        
        $per_page = get_per_page($per_page_value, $per_page_seg);
        $offset = get_offset($cur_page_seg, $per_page);
		
        //generate the query
		$where				  	= "status =  2";

		$where_or_like			= array(
										"page_name" 				=>		$search_filter,
										"page_meta_keywords" 		=>		$search_filter,
										"page_meta_description" 	=>		$search_filter,
										"page_h1" 					=>		$search_filter,
										"page_title_tag" 			=>		$search_filter,
										"page_html_data" 			=>		$search_filter,
									);		
        $data['users_result']	= $this->db_interaction->get_records_use_query($where,$where_or_like,$offset, $per_page,"id , page_name");

        //find out the total amount of records
        $total_rows = count($this->db_interaction->get_records_use_query($where,$where_or_like));
		
		$data['pbase_url'] = site_url($this->page_name."/search_trash/");
        $data['pagination'] = init_paginate($cur_page_seg, $total_rows, $per_page, $per_page_seg, $data['pbase_url']);
 		
		// get total no. of user in trash
		$where_trash_rows		= array("status !=" => 2) ;
		$data['total_rows_items']		= $this->db_interaction->get_num_records_where($where_trash_rows);
		
		// get all pages		
		$data['all_pages']= $this->_get_all_pages();

		// load view and add additional data 
		$this->_display("trash",$data);
		
		// Set the search result in a session array
		$this->session->set_userdata('search_filter', $search_filter);		
		
	}	
	
	//-------------------------------------------------------------------------
	/*
	| Function for the Request Upgrade 
	| Get the PM email address from the login 
	*/
		function request_upgrade()
		{
			$loggedin_email = $this->session->userdata('email_id');
			$userid 		= $this->session->userdata('user_id');
		//$userid = 241;
			if(isset($userid) && $userid !='')
			{
				$sql_pmemail = "SELECT 
											users.email AS USERS,
											pm.email AS PM
								FROM 
											users
								LEFT JOIN
											users AS  pm
										  ON 
											users.pmid=pm.id
								WHERE 
											users.id=".$userid."
								";
				$result_email = $this->db_interaction->run_query($sql_pmemail);					
				
				if(is_array($result_email) && count($result_email)>0)				
				{
					$client_email  = $result_email[0]['USERS'];
					$pm_email  	   = $result_email[0]['PM'];
				}
				
				$data = array(
					"pagetitle" 			=> "Request for more pages",
					"msg"	 				=> $this->msg,	
					"errors" 				=> $this->errors,
					"client_email"			=> $client_email,
					"pm_email"				=> $pm_email, 
					"Subject"   			=> "Request to add more subpages in Page Management (DPCS)",
					"website"				=> base_url(),
							);
				$this->_display('request_pages',$data);				
			}
			else
			{
				redirect('login');
			}
		}
		
		function sendrequest()
		{
		
		$this->load->plugin('phpmailer');
		
		$details = array(
									'pm_email'			=>	$this->input->post('email_address'),
									'subject'			=>  $this->input->post('subject'),
									'client_email'		=>  $this->input->post('from') ,
									'website_name'		=>  $this->input->post('website_name'),
									'number_pages'		=>  $this->input->post('number_pages'),
									'request'			=>	$this->input->post('request')
								);
		
		$message 		=  $this->load->view("dpcs_upgrade_request",$details,TRUE);
		$subject		=  $this->input->post('subject');
		$body 			=  $message;	
				
				
		$recepients = array(
								'PM' 	=> $this->input->post('email_address')
 						    );
		
		$cc 		= array();
		
			//$cc =array();	
		$bcc 		= array(
							
								'vishal'	=> 'username@exateam.com'
							);
		
		
	   send_email($subject,$body,$recepients,$cc,$bcc );
	   
	   $msg = "Thank you Your Request is forwarded";
	   $this->session->set_userdata('success_message',$msg);
	   redirect($this->page_name.'/show_list');
	   
		
	}
	
	
	/*----------------------------------------------------------------------
	| Function to build the arrey in the format that the slider effect can take place
	*/
	function show_list() 
	{
		$query 			 = "";
		$page_management =  array();
		$tree 		  	 = array();
		
		if($this->session->userdata('success_message'))
		{
			$this->msg = $this->session->userdata('success_message');
		}	
		if($this->session->flashdata('invalid_access'))
		{
			$this->errors = $this->session->flashdata('invalid_access');
		}
		if($this->session->flashdata('restore_version'))
		{
			$this->msg = $this->session->flashdata('restore_version');
		}	
		
		$data 		= array(
							  "pagetitle" 	=> "List of page(s) in system",					  
							  "mode"		=> "show_list",
							  "msg" 		=> $this->msg,	
							  "errors" 		=> $this->errors
						  );
							
		
		
		if($this->session->userdata('user') == 'admin@exateam.com')
		{
			$users_result 			= $this->db_interaction->get_dpcs_details(0);
			$total_rows 	  		= $this->db_interaction->get_dpcs_details_count(0);
			$user_details	  		= $this->db_interaction->get_user_details(); //EXPLICITLY TO FETCH THE USER DETAILS FROM THE SYSTEM
			$where_trash_rows		= "status = 2";
		}
		else
		{
		   $user_id_sesison 					= $this->session->userdata('user_id');
			 $allowed_modules_session  		 = isset($this->session->userdata['modules_allowed']['pid_frontend'])?$this->session->userdata['modules_allowed']['pid_frontend']:'';
			 if($allowed_modules_session!='')
					 $modules_allowed        = implode(',',array_keys($allowed_modules_session));
			 else
					 $modules_allowed        = 0;
					 
			 $users_result 							= $this->db_interaction->get_dpcs_details(1,$modules_allowed,$user_id_sesison);
		   $total_rows 								= $this->db_interaction->get_dpcs_details_count(1,$modules_allowed,$user_id_sesison);
			 $user_details  						= array();
			 
			 $data["cnt_non_subpage"]		= $this->count_non_subpages($user_id_sesison);
			 $data["cnt_db_subpage"]		= $this->count_db_subpages($user_id_sesison);
			 
			 $where_trash_rows					= " status = 2 AND (ID IN (".$modules_allowed.") OR  user_id = ".$user_id_sesison.")";
		}
	
		if(is_array($users_result) && count($users_result)>0)
		{
			foreach($users_result as $page_key => $page_details)	
			{
				$page_management[$users_result[$page_key]['id']] = $users_result[$page_key];
			}
		
			foreach ($page_management as $id=>&$node)
			{
				if(isset($node['page_parent_id'])) //IF PARENT IS DISABLED THAN DISABLE 
				{	
					if ($node['page_parent_id'] == 0)
					{
						$tree[$id] = &$node;
						
					}
					else
					{
						if (!isset($page_management[$node['page_parent_id']]['children'])) 
							$page_management[$node['page_parent_id']]['children'] = array(); //IGNORE ALL CHILD IF NO PARENT ACTIVE
							
							$page_management[$node['page_parent_id']]['children'][$id] = &$node;
							
							if(isset($page_management[$node['page_parent_id']]['page_name'])) //SINCE IF NO LADDER STRUCTURE THAN CLASS IS NT REQUIRE
							{
							$page_management[$node['page_parent_id']]['children'][$id]['class'] = $page_management[$node['page_parent_id']]['page_name'].'_'.$node['page_parent_id'];   //THESE IS JUST TO GET THE PARENT TREE STRUCTURE 
							}
							else
							{
								$page_management[$node['page_parent_id']]['children'][$id]['class'] = '';
							}
					}
				}
			}//ENDOFforeach ($page_management as $id=>&$node)
		}
		
		
		if(is_array($total_rows) && count($total_rows)>0)
			$total_rows_count = $total_rows[0]['PAGE_COUNT'];
		else
			$total_rows_count = 0;
			
		$data['cnt_total_rows'] = $total_rows_count;
		$data['users_result']     = $tree;
		
		$data['all_pages']							= $this->_get_all_pages();
		$data['user_sub_page_details']	= $user_details;
		
		$sql = "SELECT wm_client FROM ".TBL_WEB_MAGNET."";
		$wm_client = $this->db_interaction->run_query($sql);
		$data['web_magnet'] = $wm_client[0]["wm_client"];
		
		$trash_rows 					= $this->db_interaction->get_dpcs_inactive_count($where_trash_rows);
		if(is_array($trash_rows) && count($trash_rows)>0)
		{
			$data['trash_rows']		= $trash_rows[0]['INACTIVE_PAGE'];
		}
		else
		{
			$data['trash_rows']		= 0;
		}
		
		$page_rank_count = array();
		$page_count 	 = $this->db_interaction->get_rank_dpcs(); 
		if(is_array($page_count) && count($page_count)>0)
		 {
				foreach($page_count as $pagecount)
				{
						$page_rank_count[$pagecount['page_parent_id']] = $pagecount['PAGE_COUNT'];
				}
		}
			$data['page_rank_count']		= $page_rank_count;
			
		//Unset session data
		
		$this->_clear_search_filters();
		
		// load view and add additional data 
		$this->_display("list",$data);
		
		
	}
	

	/**
	 * Show Trash List
	 *
	 * Lets you list the records in the database.
	 * All records that are in trash are listed.
	 *
	 * @access	public
	 * @param	integer - default set to 0 for pagination
	 */	
	function show_trash_list($per_page=0)
	{
		$query 		= "";
		if($this->session->flashdata('restore_indicator'))
		{
			$this->errors = $this->session->flashdata('restore_indicator');
		}
		$data 		= array(
												"pagetitle" 	=> "List of page(s) in trash",
												"mode"				=> "show_trash_list",
												"msg" 				=> $this->msg,	
												"errors" 			=> $this->errors);
		
		//Unset session data
		$this->_clear_search_filters();			 
		
		// load pagination library
		$this->load->library('Digg_Pagination');
		//********************SET UP PAGINATION VALUES****************************
        //set up per_page_value, per_page_seg, cur_page_seg and $data['pbase_url']
        //************************************************************************
        $this->load->plugin('pagination');
        
        $per_page_value = 50;  //default - unless overridden later
        $per_page_seg = 4;    //the uri segment for the per page value
        $cur_page_seg = 5;    //the url segment for the current page value (generally +1 of per page seg)
        
        $per_page = ($per_page)? $per_page:get_per_page($per_page_value, $per_page_seg);
        $offset = get_offset($cur_page_seg, $per_page);
        
        if($this->session->userdata('user') == 'admin@exateam.com')
				{
					//generate the query
							$data['users_result'] = $this->db_interaction->get_records($offset, $per_page,array("status" => 2),"rank , page_name");
					 $total_rows = count($this->db_interaction->get_records(NULL, NULL,array("status" => 2)));
				}
				else
				{
						$allowed_modules_session   = isset($this->session->userdata['modules_allowed']['pid_frontend'])?$this->session->userdata['modules_allowed']['pid_frontend']:'';
					 if($allowed_modules_session!='')
							 $modules_allowed        = implode(',',array_keys($allowed_modules_session));
					 else
							 $modules_allowed        = 0;
							 
							$user_id		= $this->session->userdata('user_id');
							
							$SQL_TRASH ="SELECT 
																		id,page_name,page_head,page_header,page_footer,status,
																		created,modified,display_footer,hide_client,rank,created_by,user_id,page_parent_id
														FROM 
																		".TBL_PAGE." 
														WHERE
																			STATUS = 2
																	AND
																		 (ID IN (".$modules_allowed.") OR user_id = ".$user_id.")
														ORDER BY 
																	 rank
														 LIMIT
																	 {$offset}, {$per_page}";
							
							$data['users_result'] = $this->db_interaction->run_query($SQL_TRASH);
							$SQL_TRASH ="SELECT 
																		count(id) AS TRASH_ROWS
														FROM 
																		".TBL_PAGE." 
														WHERE
																			STATUS = 2
																	AND
																		 (ID IN (".$modules_allowed.") OR user_id = ".$user_id.")
														ORDER BY 
																	 rank
														 ";
					//$data['users_result'] = $this->db_interaction->get_records($offset, $per_page,array("status" => 2,"ID !="=>1),"rank , page_name");
					$total_rows_trash = $this->db_interaction->run_query($SQL_TRASH);
					if(is_array($total_rows_trash) && count($total_rows_trash)>0)
					{
						$total_rows = $total_rows_trash[0]['TRASH_ROWS'];
					}
					else
					{
						$total_rows =0;
					}
					
					//$total_rows = count($this->db_interaction->get_records(NULL, NULL,array("status" => 2,"hide_client !="=>1)));
				}
        //find out the total amount of records
        $total_rows = count($this->db_interaction->get_records(NULL, NULL,array("status" => 2,"hide_client !="=>1)));
        
        $data['pbase_url'] = site_url($this->page_name."/show_trash_list/");
        $data['pagination'] = init_paginate($cur_page_seg, $total_rows, $per_page, $per_page_seg, $data['pbase_url']);		
		
		// get total no. of user in trash
		$where_trash_rows		= array("status !=" => 2) ;
		$data['total_rows_items']		= $this->db_interaction->get_num_records_where($where_trash_rows);
		
		// get all pages		
		$data['all_pages']= $this->_get_all_pages();
		
		// load view and add additional data 
		$this->_display("trash",$data);		
	}

	/**
	 * Add
	 *
	 * Lets you fill a form to add a user in the system
	 * If the form is posted then the posted values will be set in the variables.
	 * It is necessary to declare a variable and define a default value.
	 *
	 * @access	public
	 * @param	array
	 */		
	function add($frm=array())
	{		
		//Check the no of allowed page is complete or not
		if($this->session->userdata('user') <> 'admin@exateam.com')
		{
			 $user_id_sesison 					= $this->session->userdata('user_id');
			if($this->count_db_subpages($user_id_sesison) > $this->count_non_subpages($user_id_sesison))
			{
				// Do Nothing
			}
			else
			{
				redirect($this->page_name);
			}
		}

		$this->session->set_userdata("page_action","Add");
		
		$list_pages 	= "";
		$preselected 	= $this->input->post('page_parent');
		//$list_pages 	= $this->mapTree($preselected,'',1); //IF YOU WANT TO ALLOW MULTIPLE MASTER PAGE THAN UNCOMMENT THESE AND COMMENT THE BELOW LINE
		$list_pages 	= $this->mapTree($preselected);
		
		//JUST TO PREVENT IOVERLAPPING OF STRING
		$this->option_list = '';
		
		$redirect_pages 	= "";
		$preselected_pages 	= $this->input->post('redirect_page');
		$redirect_pages 	= $this->mapTree($preselected_pages);
		//$list_pages 	= $this->_build_tree($preselected);

		
		
		$list_pages_featured = "";
		$list_pages_featured = $this->_get_featured_pages();
		  		
			
		
//Fetching  Data For the SEO Web Magnet.....................
		$result_data		= $this->db_interaction->get_all_records_webmagnet(); //Find out the data
		
		if(count($result_data) > 0){
				$orgmeta_keywords					= $result_data[0]["meta_keywords"];
				$orgmeta_description			= $result_data[0]["meta_description"];
				$orgh1										= $result_data[0]["h1"];
				$orgtitle_tag							= $result_data[0]["title_tag"];
			
		}else{
				$orgmeta_keywords		= "";
				$orgmeta_description	= "";
				$orgh1					= "";
				$orgtitle_tag			= "";
		}		
/////////////////////////////////////////////

		if (!isset($frm["id"]) 											|| empty($frm["id"])) 										$frm["id"] =  "-";
		if (!isset($frm["page_name"]) 							|| empty($frm["page_name"])) 							$frm["page_name"] =  "";	
		if (!isset($frm["page_identity"])						|| empty($frm["page_identity"])) 					$frm["page_identity"] =  "";	
		if (!isset($frm["page_header"]) 						|| empty($frm["page_header"])) 						$frm["page_header"] =  FRONTEND_HEADER;	
		if (!isset($frm["page_footer"]) 						|| empty($frm["page_footer"])) 						$frm["page_footer"] =  FRONTEND_FOOTER;	
		if (!isset($frm["page_meta_keywords"]) 			|| empty($frm["page_meta_keywords"])) 		$frm["page_meta_keywords"] =  $orgmeta_keywords;	
		if (!isset($frm["page_meta_description"])   || empty($frm["page_meta_description"])) 	$frm["page_meta_description"] =  $orgmeta_description;	
		if (!isset($frm["page_h1"]) 								|| empty($frm["page_h1"])) 								$frm["page_h1"] =  $orgh1;	
		if (!isset($frm["page_head"])								|| empty($frm["page_head"])) 							$frm["page_head"] =  "";
		
		if (!isset($frm["page_title_tag"])					|| empty($frm["page_title_tag"])) 				$frm["page_title_tag"] =  $orgtitle_tag;	
		if (!isset($frm["seo_content"])							|| empty($frm["seo_content"])) 						$frm["seo_content"] 	 =  "";	
		if (!isset($frm["page_html_data"])					|| empty($frm["page_html_data"])) 				$frm["page_html_data"] =  "";	
		if (!isset($frm["redirect_name"])						|| empty($frm["redirect_name"])) 					$frm["redirect_name"] =  "";	
		if (!isset($frm["page_include_file"])				|| empty($frm["page_include_file"])) 			$frm["page_include_file"] =  "";	
		if (!isset($frm["page_featured"]) 					|| empty($frm["page_featured"])) 					$frm["page_featured"] =  0;	
		if (!isset($frm["page_featured_page"]) 			|| empty($frm["page_featured_page"])) 		$frm["page_featured_page"] =  "";	
		if (!isset($frm["featured_image_name"]) 		|| empty($frm["featured_image_name"])) 		$frm["featured_image_name"] =  "";		
		if (!isset($frm["status"]) 									|| empty($frm["status"])) 								$frm["status"] 			=  0;
		if (!isset($frm["display_footer"]) 					|| empty($frm["display_footer"])) 				$frm["display_footer"]  =  0;
		if (!isset($frm["hide_client"]) 						|| empty($frm["hide_client"])) 						$frm["hide_client"] 	=  0;
		if (!isset($frm["menu_frontend"]) 					|| empty($frm["menu_frontend"])) 					$frm["menu_frontend"] 	=  0;
		if (!isset($frm["dashboard_category"])			|| empty($frm["dashboard_category"]))
		{
			 $frm["dashboard_category"] 		=  $this->list_dashboard_category();
		}
		else
		{
			 $dashboard_category				=  $this->list_dashboard_category($frm['dashboard_category']); 
			 $frm["dashboard_category"] 		=  $dashboard_category;
		}	

		$web_magnet = $this->page_db_interaction->web_magnet();
		$wm_client_status = $web_magnet['wm_client'];
		
		
		$token = generate_random_id();
		$this->session->set_userdata("token",$token);
				
		$data = array(
										"pagetitle" 								=> "Add page",	
										"pagetitle_sub" 						=> "Page Data",		
										"id" 												=> $frm["id"],
										"wm_client_status"					=> $wm_client_status,
										"page_name" 								=> $frm["page_name"],
										"redirect_name" 						=> $frm["redirect_name"],
										"page_identity" 						=> $frm["page_identity"],
										"page_header"								=> $frm["page_header"],			
										"page_footer"								=> $frm["page_footer"],
										"page_meta_keywords"				=> $frm["page_meta_keywords"],			
										"page_meta_description"			=> $frm["page_meta_description"],			
										"page_h1"										=> $frm["page_h1"],			
										"page_title_tag"						=> $frm["page_title_tag"],
										"page_head"									=> $frm["page_head"],				
										"seo_content"								=> $frm["seo_content"],				
										"page_html_data"						=> $frm["page_html_data"],	
										"page_include_file"					=> $frm["page_include_file"],	
										"page_featured"		    			=> $frm["page_featured"],	
										"page_featured_page"				=> $frm["page_featured_page"],
										"status"										=> $frm["status"],					
										"display_footer"						=> $frm["display_footer"],
										"hide_client"								=> $frm["hide_client"],
										"menu_frontend"							=> $frm["menu_frontend"],
										"token"											=> $token,
										"submit_name"								=> "insert",
										"submit_value"	  					=> "Add page",
										"list_pages"								=> $list_pages,
										"redirect_page_id"					=> $redirect_pages,
										"list_pages_featured"				=> $list_pages_featured,
										"page_parent_original"				=> $this->input->post('page_parent'),
										"dashboard_category"				=> $frm["dashboard_category"],
										"msg"	 											=> $this->msg,
										"errors" 										=> $this->errors);
			
			
	   if(isset($frm["featured_image_name"]))
		{
		   $data['featured_image'] =  $frm["featured_image_name"];
		}
		
		// load view and add additional data 
		$this->_display("add",$data);
	}
	
	function count_non_subpages($user_id_sesison)
	{
		if($user_id_sesison == 0)
		{
				$sql 	= "SELECT COUNT(id) AS COUNT FROM ".TBL_PAGE." WHERE page_parent_id <>10 AND STATUS <>2";
				$result = $this->db_interaction->run_query($sql);
				if(count($result) > 0)
						return $result[0]["COUNT"];		
				else
						return 0;
		}
		else	
		{
			//CLIENT ACCESS 
				$sql 	= "SELECT COUNT(id) AS COUNT FROM ".TBL_PAGE." WHERE page_parent_id <>10  AND user_id =".$user_id_sesison;
				$result = $this->db_interaction->run_query($sql);
				if(count($result) > 0)
						return $result[0]["COUNT"];		
				else
						return 0;
		}		
	}
	
	//------------------------------------------------------------------------------------------------
	function count_db_subpages($user_id_sesison)
	{
		if($user_id_sesison == 1)
		{
			$sql 	= "SELECT no_of_subpages AS COUNT FROM page_management_subpages";
			$result = $this->db_interaction->run_query($sql);
			if(count($result) > 0)
					return $result[0]["COUNT"];		
			else
					return 0;
		}
		else
		{
			$sql 	= "SELECT no_of_subpages AS COUNT FROM page_management_subpages WHERE user_id =".$user_id_sesison;
			$result = $this->db_interaction->run_query($sql);
			if(count($result) > 0)
					return $result[0]["COUNT"];		
			else
					return 0;
		}
	}
	
	//--------------------------------------------------------------------------------------------------
	function update_subpages()
	{
		$num 		 = (int)$this->input->post('num');
		$user_id = (int)$this->input->post('user_id');
		$sql 		 = "UPDATE 
												page_management_subpages 
									SET 
												no_of_subpages = ".$num.",
												modified			 = ".$this->db->escape(date("Y-m-d H:i:S")).",
												modified_by		 = ".$this->db->escape($this->session->userdata('user'))."
									WHERE 
												user_id		=".$user_id;
		$this->db->query($sql);
		$this->log_subpages($num,$user_id);
		echo $num;
	}
	
	//-----------------------------------------------------------------------------------------------------
	
	function log_subpages($pages,$user_id)
	{
			$data = array(
											'no_of_subpages'   => $pages,
											'user_id'					 =>	$user_id,
											'created'					 => date("Y-m-d H:i:S"),
											'created_by' 			 =>	$this->session->userdata('user'),
											'ipaddress'				 => $this->getRealIpAddr()
									 );
						$this->db_interaction->add_user($data,TBL_PAGE_SUBPAGE_LOG);			 
	}
	
	//---------------------------------------------------------------------------------------------------
	
	function list_dashboard_category($send_rate=0)
	{
		$data_str		='';
		$batch_size		=$this->dashboard_category;
		$data_str		= "<option value= \"0\">Select</option>\n";
		if(is_array($batch_size) && count($batch_size)>0)
		{
			foreach($batch_size as $key=>$value)
			{
				$selected_option	= "";
				if($key == $send_rate)
				{
					$selected_option = 'selected="selected"';
					
				}
				$data_str.="<option value= \"".$key."\" ".$selected_option.">".str_replace('_',' ',ucwords($value))."</option>\n";
			}
		}
		return $data_str;
	}	
	
	//----------------------------------------------------------------------------------------------------
	/**
	 * Insert
	 *
	 * Lets you add a new record in the database
	 * It first validates the form and depending on the errors  occurred or not, necessary actions are taken.
	 *
	 * @access	private
	 */
	function insert()
	{
		
		if (count($_POST) == 0)
    {   
			redirect($this->page_name);
    }
    
		$this->errors =  $this->_verify_form();
		
		// check for token to solve post back issue
		$token_error = $this->_token_check($this->input->post('token'));
		
		if(!empty($this->errors))
		{
			$this->add($_POST);
		}
		else if(!empty($token_error))
		{
			$this->errors = $token_error;
			$this->add($_POST);
		}
		else
		{ 
			$data_insert = 0;
			$image_insert = 0;
			if(isset($_FILES['page_featured_image']['name']) && !empty($_FILES['page_featured_image']['name']))
			{ 
				$check = $this->_upload_file_check('page_featured_image');	
				
				if($check == 0)
				{
					$this->add($_POST);
				}
				else
				{ 
					$data_insert = 1;
					$image_insert = 1;
				}
			}//-----------------------------end if true upload image and enter data
			else
			{ 
				$data_insert = 1;
				$image_insert = 0;
			} //---------------------------------------only enter data		
			
			if($data_insert == 1)
			{
				
				$web_magnet = $this->page_db_interaction->web_magnet();
				
				$wm_client_status = $web_magnet['wm_client'];
				// fix the rank on every fresh insert
				//$new_rank = $this->db_interaction->get_num_records();
				$new_ranks = $this->db_interaction->get_new_rank_insert($this->input->post("page_parent"));
				
				$new_rank = $new_ranks[0]['DPCS_ID']+1;
				
				if(isset($_POST['page_featured']))
				{
					$page_featured = 1;
				}
				else
				{
					$page_featured = 0;
				}	
				
				if(isset($_POST['display_footer']))
					$display_footer = 1;
				else
					$display_footer = 0;
	
				if(isset($_POST['hide_client']))
					$hide_client = 1;
				else
					$hide_client = 0;
	
				if(isset($_POST['menu_frontend']))
					$menu_frontend = 1;
				else
					$menu_frontend = 0;
				
				//-------------------NEW NAMING OF PAGE-------------------------------
			$page_name_input  		= 	array( "/^\s+/","/\s{2,}/","/\s+\$/");
			$page_name_replace 		= 	array(""," ","");
			$page_rename			=   $this->input->post("page_name");
			$sanitized_page_name 	=   preg_replace($page_name_input,$page_name_replace,$page_rename);
				//REPLACE ALL SPACES WITH THE UNDERSCORE
			$special = array(" ","_","/");
			$replace = array("-","-","");
			$page_name			=   strtolower(str_replace($special,$replace,$sanitized_page_name));
			
			
				
				if($this->session->userdata("email_id") == "admin@exateam.com")
				 {
					$data = array( 
												 'page_name'							=> $page_name,
												 'page_title_tag' 				=> $this->input->post("page_title_tag"),
												 'page_parent_id'					=> $this->input->post("page_parent"),
												 'page_header'						=> $this->input->post("page_header"),
												 'page_footer'						=> $this->input->post("page_footer"),
												 'page_meta_keywords'			=> $this->input->post("page_meta_keywords"),
												 'page_meta_description'	=> $this->input->post("page_meta_description"),
												 'page_h1'								=> $this->input->post("page_h1"),
												 'seo_content'						=> $this->input->post("seo_content"),
												 'redirect_page_id'				=> $this->input->post("redirect_page_id"),
												 "page_head"							=> $this->input->post("page_head"),
												 'page_html_data'					=> $this->input->post("page_html_data"),
												 'dashboard_category'			=> $this->input->post("dashboard_category"),
												 'page_include_file'			=> $this->input->post("page_include_file"),
												 'page_featured'		    	=> $page_featured,
												 'page_featured_page'			=> $this->input->post("page_featured_page"),
												 'featured_image_name'		=> $this->input->post("featured_image"), 	
												 'status'									=> $this->input->post("status"),
												 'display_footer'					=> $display_footer,
												 'hide_client'						=> $hide_client,
												 'menu_frontend'					=> $menu_frontend,
												 'rank'										=> $new_rank,
												 'created'								=> date("Y-m-d H:i:S"),
												 'created_by'							=> $this->session->userdata("user"),
												 'user_id' 							  => $this->session->userdata('user_id'),
												 'version_date' 	 				=> date("Y-m-d H:i:S")											 
						);		
					
									
				}
				elseif($this->session->userdata("email_id") != "admin@exateam.com" && $wm_client_status == 1) {
					$data = array( 
															'page_name'							=> $page_name,	   
															'page_title_tag' 				=> $this->input->post("page_title_tag"),
															'page_parent_id'				=> $this->input->post("page_parent"),
															'page_header'						=> $this->input->post("page_header"),
															'page_footer'						=> $this->input->post("page_footer"),
															'page_meta_keywords'		=> $this->input->post("page_meta_keywords"),
															'page_meta_description'	=> $this->input->post("page_meta_description"),
															'page_h1'								=> $this->input->post("page_h1"),
														  'seo_content'						=> $this->input->post("seo_content"),
															'redirect_page_id'			=> $this->input->post("redirect_page_id"),
															"page_head"							=> $this->input->post("page_head"),
															'page_html_data'				=> $this->input->post("page_html_data"),
															//'page_include_file'			=> $this->input->post("page_include_file"), //CLIENT WONT BE ADDING MODULES
															'page_featured'		    	=> $page_featured,
															'page_featured_page'		=> $this->input->post("page_featured_page"),
															'featured_image_name'		=> $this->input->post("featured_image"), 	
															'status'								=> $this->input->post("status"),
															'display_footer'				=> $display_footer,
															'menu_frontend'					=> $menu_frontend,
															'rank'									=> $new_rank,
															'created'								=> date("Y-m-d H:i:S"),
															'created_by'						=> $this->session->userdata("user"),
															'version_date' 	 				=> date("Y-m-d H:i:S"),				
															'user_id'							  => $this->session->userdata('user_id')																	
											);		
					
									
				}
				else{
					$data = array( 
												 'page_name'							=> $page_name,	   
												 "page_head"							=> $this->input->post("page_head"),
												 'page_parent_id'					=> $this->input->post("page_parent"),
												 'page_header'						=> $this->input->post("page_header"),
												 'redirect_page_id'				=> $this->input->post("redirect_page_id"),
												 'page_footer'						=> FRONTEND_FOOTER,		   
												 'page_html_data'					=> $this->input->post("page_html_data"),
												 'page_featured'		   	  => $page_featured,
												 'page_featured_page'			=> $this->input->post("page_featured_page"),
												 'featured_image_name'		=> $this->input->post("featured_image"), 	
												 'status'									=> $this->input->post("status"),
												 'display_footer'					=> $display_footer,
												 'menu_frontend'					=> $menu_frontend,
												 'rank'										=> $new_rank,
												 'created'								=> date("Y-m-d H:i:S"),
												 'version_date' 	 				=> date("Y-m-d H:i:S"),
												 'created_by'							=> $this->session->userdata("user"),	
												 'user_id'							  => $this->session->userdata('user_id')												 
										);
				}		
						
				$this->db_interaction->add_user($data);
				$id = $this->db->insert_id();
				//------------------------------------------------
				$this->history_log($data,$id);
				//$this->_re_rank($id,0,$this->input->post("page_parent"));
				//--------------------------------------------------
				
				if($image_insert == 1)
							$this->_upload_file($id,'add');	
				
				//----------------------------------------------------------------------------------------------------------------
				if($this->session->userdata("email_id") != "admin@exateam.com") //SINCE ADMIN HAS ACCESS TO ALL MODULES
				{
						$SQL = "INSERT INTO 
																	".TBL_PAGE_USERS." 
														SET 
																	 uid = ".$this->session->userdata('user_id').",
																	 pid = ".$id." 
									";
						$this->db->query($SQL);			
				}		
				
				//-------------------------------------------------------------------------------------------------------------------
				
				
				$query 	= "SELECT id FROM ".TBL_PAGE." WHERE page_name = '".$page_name."'";
				$result = $this->db->query($query);
				
				foreach($result->result_array() as $root)
					{
					  $map_id = $root['id'];
					}
							
				$data_page = array(
								   "page_name"  => $page_name,
									"map_id"    =>  $map_id
								   );
				//$this->db->insert($this->banner_page, $data_page); 
				$this->msg = "Page '".$page_name."' is added successfully.";									
				//$this->_re_rank($id);
				
				$this->show_list();
			
				}
		}
	}
	
	
	/*
	| Function to validate whether the User has the Prvivledge to do so 
	*/
	function validate_priviledege($id)
	{
		if($this->session->userdata('user') == 'admin@exateam.com')
		{
			return true;
		}//END OF if(if($this->session->userdata('user') == 'admin@exateam.com'))
		else
		{
				$allowed_modules_session   = isset($this->session->userdata['modules_allowed']['pid_frontend'])?$this->session->userdata['modules_allowed']['pid_frontend']:'';
					 if($allowed_modules_session!='')
							 $modules_allowed        = implode(',',array_keys($allowed_modules_session));
					 else
							 $modules_allowed        = 0;
							 
							$user_id		= $this->session->userdata('user_id');
				
					$SQL = "SELECT 
														GROUP_CONCAT(id) AS ALLOWED_ID
									FROM 
														".TBL_PAGE."
									WHERE 
															STATUS <> 2
												AND 
															(id IN (".$modules_allowed.") OR user_id = ".$user_id.") 
									ORDER BY
														page_parent_id ";	
					$allowed_page_ids = $this->db_interaction->run_query($SQL);
					
					if(is_array($allowed_page_ids) && count($allowed_page_ids)>0)
					{
						 $allowed_modules_user = $allowed_page_ids[0]['ALLOWED_ID'];
						 $modules_allowed      = explode(',',$allowed_modules_user);
					}//END OF if(is_array($allowed_page_ids) && count($allowed_page_ids)>0)
					else
					{
						 $modules_allowed = array();
					}		
						
						if(in_array($id,$modules_allowed))
							return true;
						else
							return false;
		}		
	}
	
	
	/**
	 * Edit
	 *
	 * Lets you edit and existing record from the database.
	 * When called for the first time, it will fetch the record details from the database 
	 * and pre fill the the form fields.	
	 * If the form is posted then the posted values will be set in the variables.
	 * It is necessary to declare a variable and define a default value.
	 *
	 * @access	private
	 * @param	array
	 */	
	function edit($id,$frm=array())
	{
		$id = (int) $this->uri->segment(4);
		
		$check_priviledge = $this->validate_priviledege($id);
		if(!$check_priviledge)	
		{
			$message = "You don't have Sufficient Priviledge to Edit these page";
			$this->session->set_flashdata('invalid_access',$message);
			redirect($this->page_name);
		}
		else
		{
					$mode = (isset($frm['mode'])) ? $frm['mode'] : "";
				
					$this->session->set_userdata("page_action","Edit");	
				
					if($id == "") $id = $this->input->post('id');
				

					if( $id == 0 || empty($id))
					{
						redirect($this->page_name);			
					}
					else
					{
					
						if($mode != "update")
						{
							$result = $this->db_interaction->get_records_where('id',$id);
							$cnt = count($result);
							if($cnt > 0)
							{	
								$frm = $result[0];
								$frm["page_parent"] = $frm["page_parent_id"];		
												
							}	
							else
							{
								redirect($this->page_name,"refresh");	
							}
						}
						else{
							$result = $this->db_interaction->get_records_where('id',$id);
							$frm 											  = $result[0];
							$frm["page_parent"] 			  = $frm["page_parent_id"];		
							$frm["featured_image_name"] = $frm["featured_image_name"];			
						}			
					}
					
					$token = generate_random_id();
					$this->session->set_userdata("token",$token);
					
					
					
					$list_pages = "";
					$list_pages 	= $this->mapTree($frm["page_parent"],$id);//INDICATOR THAT WE NEED A BASE PAGE
					
					//JUST TO PREVENT IOVERLAPPING OF STRING
					$this->option_list = '';
					
					$redirect_pages = "";
					$redirect_pages 	= $this->redirect_mapTree($frm["redirect_page_id"]);
					
					/*$list_pages_featured = "";
					$list_pages_featured = $this->_build_tree_featured($frm["page_featured_page"],$id);*/
					
					$list_pages_featured = "";
					$list_pages_featured = $this->_get_featured_pages();
					
					$web_magnet = $this->page_db_interaction->web_magnet();
					
					
					$wm_client_status = $web_magnet['wm_client'];
					$wm_status		  = $web_magnet['wm_status'];	
					
					$version_details 			= $this->db_interaction->get_version_details($id);
					$verison_selected_id 	= $this->input->post('verison_selected_id');
					$version_number  			= $this->_version_number_details($version_details,$verison_selected_id);
					
					$data = array(
													"pagetitle" 							=> "Update Page : '".$frm["page_name"]."'",
													"pagetitle_sub" 					=> "Page Data",			
													"id" 											=> $frm["id"],
													"wm_client_status"				=> $wm_client_status,
													"wm_status"								=> $wm_status,
													"page_name" 							=> $frm["page_name"],				
													"page_header"							=> $frm["page_header"],			
													"page_footer"							=> $frm["page_footer"],
													"page_meta_keywords"			=> $frm["page_meta_keywords"],			
													"page_meta_description"		=> $frm["page_meta_description"],			
													"page_h1"									=> $frm["page_h1"],	
													"seo_content"							=> $frm["seo_content"],	
													"page_head"								=> $frm["page_head"],		
													"page_title_tag"					=> $frm["page_title_tag"],		
													"page_html_data"					=> $frm["page_html_data"],
													"page_include_file"				=> $frm["page_include_file"],
													"page_featured"		    		=> $frm["page_featured"],
													"featured_image"	  		  => $frm["featured_image_name"],
													"page_featured_page"			=> $frm["page_featured_page"],
													"page_parent"							=> $frm["page_parent"],		
													"status"									=> $frm["status"],		
													"display_footer"					=> $frm["display_footer"],
													"hide_client"							=> $frm["hide_client"],
													"menu_frontend"						=> $frm["menu_frontend"],
													"token"		            		=> $token,
													"dashboard_category"			=> $this->list_dashboard_category($frm['dashboard_category']),
													"version_number"					=> $version_number,
													"version_details"					=> $version_details,
													"verison_selected_id"			=> $verison_selected_id,
													"submit_name"							=> "update",
													"submit_value"	  				=> "Update Page",
													"list_pages"							=> $list_pages,
													"redirect_page_id"				=> $redirect_pages,
													"list_pages_featured"			=> $list_pages_featured,
													"page_parent_original"				=> $frm["page_parent"] ,
													"msg"	 										=> $this->msg,	
													"errors" 									=> $this->errors);
					
							
					if(isset($frm["featured_image_name"]))
					{
						 $data['featured_image'] =  $frm["featured_image_name"];
						 $data['featured_flag'] =  $frm["featured_image_new_flag"];
					}
					
					// load view and add additional data 
					$this->_display("add",$data);
		}			
	}
	
	
	//-------------------------------------------------------------------------------------------------------------------
	/*
	| Function to get the version number of the Current Page (If Any)
	*/
		function _version_number_details($version_details,$verison_selected_id=0)
		{
			
			$data_str					= '';
			$data_str		= "<option value= \"0\">Select Version</option>\n";
			if(is_array($version_details) && count($version_details)>0)
			{
				foreach($version_details as $key=>$value)
				{
					$selected_option	= "";
					if($key == $verison_selected_id)
					{
						$selected_option = 'selected="selected"';
						
					}
					$data_str.="<option value= \"".$value['id']."\" ".$selected_option.">".date('d-M-Y h:i:s',strtotime($value['created']))."</option>\n";
				}
			}
			return $data_str;
		}
  //--------------------------------------------------------------------------------------------------------------------  
	/**
	 * Update
	 *
	 * Lets you update an existing record in the database
	 * It first validates the form and depending on the errors occurred or not, necessary actions are taken.
	 *
	 * @access	private
	 */
	function update()
	{	 
		
		if (count($_POST) == 0)			
			redirect($this->page_name);
		
			
		$this->errors =  $this->_verify_form("update");						
		
		
		$id = (int) $this->input->post("id");

		
		if(!empty($this->errors) || empty($id))
		{
		
			$this->edit($id,$_POST);
		}
		else
		{
			$data_insert = 0;
			$image_insert = 0;
			if(isset($_FILES['page_featured_image']['name']) && !empty($_FILES['page_featured_image']['name']))
			{ 
				$check = $this->_upload_file_check('page_featured_image');	
				
				if($check == 0)
				{
					$this->edit($id,$_POST);
				}
				else
				{
					$data_insert = 1;
					$image_insert = 1;
				}
			}//-----------------------------end if true upload image and enter data
			else
			{
				$data_insert = 1;
				$image_insert = 0;
			} //---------------------------------------only enter data		
			
			if($data_insert == 1)
			{
			
			$web_magnet = $this->page_db_interaction->web_magnet();
			
			$wm_client_status = $web_magnet['wm_client'];
		    if(isset($_POST['page_featured']))
			{
			    $page_featured = 1;
			}
			else
			{
			    $page_featured = 0;
			}
			if(isset($_POST['display_footer']))
			    $display_footer = 1;
			else
			    $display_footer = 0;
			
			if(isset($_POST['hide_client']))
			    $hide_client = 1;
			else
			    $hide_client = 0;
			
			if(isset($_POST['menu_frontend']))
			    $menu_frontend = 1;
			else
			    $menu_frontend = 0;
			
			/*$special = array(" ",
							 "_",
							 "/");
			$replace = array("-",
							 "-",
							 "");		
			$page_name = strtolower(str_replace($special,$replace,trim($this->input->post("page_name"))));*/
			
			$page_name_input  		= 	array( "/^\s+/","/\s{2,}/","/\s+\$/");
			$page_name_replace 		= 	array(""," ","");
			$page_rename			=   $this->input->post("page_name");
			$sanitized_page_name =   preg_replace($page_name_input,$page_name_replace,$page_rename);
				//REPLACE ALL SPACES WITH THE UNDERSCORE
			$special = array(" ","_","/");
			$replace = array("-","-","");
			$page_name			=   strtolower(str_replace($special,$replace,$sanitized_page_name));

			
			if($this->session->userdata("email_id") == "admin@exateam.com")
			 { 
				$data = array(
												'page_name'								=> $page_name ,
												'page_title_tag' 					=> $this->input->post("page_title_tag"),
												'page_parent_id'					=> $this->input->post("page_parent"),
												'page_header'							=> $this->input->post("page_header"),
												'page_footer'							=> $this->input->post("page_footer"),
												'page_meta_keywords'			=> $this->input->post("page_meta_keywords"),
												'page_meta_description'		=> $this->input->post("page_meta_description"),
												'page_h1'									=> $this->input->post("page_h1"),
												'seo_content'							=> $this->input->post("seo_content"),
												"page_head"								=> $this->input->post("page_head"),
												'dashboard_category'			=> $this->input->post("dashboard_category"),
												'redirect_page_id'				=> $this->input->post("redirect_page_id"),
												'page_html_data'					=> $this->input->post("page_html_data"),
												'page_include_file'				=> $this->input->post("page_include_file"),
												'featured_image_name'			=> $this->input->post("featured_image"), 	
												'status'									=> $this->input->post("status"),
												'display_footer'					=> $display_footer,
												'hide_client'							=> $hide_client,
												'menu_frontend'						=> $menu_frontend,
												'page_featured'		   		  => $page_featured,
												'page_featured_page'			=> $this->input->post("page_featured_page"),
												'modified_by'							=> $this->session->userdata("user"),
												'user_id'							  	=> $this->session->userdata('user_id')
				);					
			}	
			elseif($this->session->userdata("email_id") != "admin@exateam.com" && $wm_client_status == 1){
				$data = array(
												'page_name'								=> $page_name ,
												'page_title_tag' 					=> $this->input->post("page_title_tag"),
												'page_parent_id'					=> $this->input->post("page_parent"),
												'page_header'							=> $this->input->post("page_header"),
												'page_footer'							=> $this->input->post("page_footer"),
												'redirect_page_id'				=> $this->input->post("redirect_page_id"),
												'page_meta_keywords'			=> $this->input->post("page_meta_keywords"),
												'page_meta_description'		=> $this->input->post("page_meta_description"),
												'featured_image_name'			=> $this->input->post("featured_image"), 	
												'page_h1'									=> $this->input->post("page_h1"),
												'seo_content'							=> $this->input->post("seo_content"),
												"page_head"								=> $this->input->post("page_head"),
												'page_html_data'					=> $this->input->post("page_html_data"),					
												'status'									=> $this->input->post("status"),
												'display_footer'					=> $display_footer,
												'hide_client'							=> $hide_client,
												'menu_frontend'						=> $menu_frontend,
												'page_featured'		    		=> $page_featured,
												'page_featured_page'			=> $this->input->post("page_featured_page"),
												'modified_by'							=> $this->session->userdata("user"),
												'user_id'							  	=> $this->session->userdata('user_id')
				);
			}
			else{ 
				if($wm_client_status == 1){
					$data = array(
											 'page_name'								=> $page_name ,	 
											"page_head"									=> $this->input->post("page_head"),					   
											 'page_parent_id'						=> $this->input->post("page_parent"),
											 'page_header'							=> $this->input->post("page_header"),
											 'page_footer'							=> FRONTEND_FOOTER,		   
											 'page_html_data'						=> $this->input->post("page_html_data"),
											 'page_featured'		  		  => $page_featured,
											 'redirect_page_id'					=> $this->input->post("redirect_page_id"),
											 'featured_image_name'			=> $this->input->post("featured_image"), 	
											 'page_featured_page'				=> $this->input->post("page_featured_page"),
											 'status'										=> $this->input->post("status"),
											 'display_footer'						=> $display_footer,					   
											 'hide_client'							=> $hide_client,
											 'menu_frontend'						=> $menu_frontend,
											 'modified'									=> date("Y-m-d H:i:S"),
											 'modified_by'							=> $this->session->userdata("user"),
											 'user_id'							  	=> $this->session->userdata('user_id')											 
					);
				}	
				else{			
					$data = array( 		
										 'page_name'						=> $page_name ,	 
										 'page_parent_id'				=> $this->input->post("page_parent"),
										 "page_head"						=> $this->input->post("page_head"),		
										 'page_header'					=> FRONTEND_HEADER,
										 'page_footer'					=> FRONTEND_FOOTER,		   
										 'page_html_data'				=> $this->input->post("page_html_data"),
										 'page_featured'		    => $page_featured,
										 'redirect_page_id'			=> $this->input->post("redirect_page_id"),
										 'featured_image_name'	=> $this->input->post("featured_image"), 	
											'page_featured_page'	=> $this->input->post("page_featured_page"),
										 'status'								=> $this->input->post("status"),
										 'display_footer'				=> $display_footer,		
										 'menu_frontend'				=> $menu_frontend,					   
										 'modified'							=> date("Y-m-d H:i:S"),
										 'modified_by'					=> $this->session->userdata("user"),
										 'user_id'					  	=> $this->session->userdata('user_id')												 
					);
				}	
			}	
			
			$this->db_interaction->update_user($id,$data);	
			//----------------------------------------------------------
			$this->history_log($data,$id);
			//---------------------------------------------------------
			//----------------------------RANK---------------------------
				$page_parent_id 						= $this->input->post("page_parent");
				$page_parent_original 	= $this->input->post("page_parent_original");
				if($page_parent_id != $page_parent_original)
				{
					$rank_details 		= $this->db_interaction->get_new_rank_insert($this->input->post('page_parent'));
					
					$new_rank         =  $rank_details[0]['DPCS_ID'];
		
					$data = array(
													'rank'						=> $new_rank,
												);
					$this->db_interaction->update_user($id,$data);
				}	
				//------------RANK ENDS------------------------------------
			
			
			if($image_insert == 1)	
				$this->_upload_file($id,'update');	

			/*
			| UPDATING BANNERS SINCE THEY PAGE NAME SHOULD BE UPDATED IN THE BANNERS MODULE
			*/			
			$data_page = array(
			                  	 "page_name" => $page_name,
							  );
			$this->db_interaction->update_banner($id,$data_page,TBL_BANNERS_PAGE);
			
			
			
			//$this->db->insert($this->banner_page, $data_page); 
			$this->msg = "Page id ".$id." ".$page_name." is updated successfully.";			
			$this->show_list();	
		
			}
		}	 
	}
	
	//------------------------------------------------------------------------------------------------------------------------
	function show_log_subpages()
	{
		 $this->load->helper('useful');
		 $log_data = $this->db_interaction->log_sub_pages();
		 if(is_array($log_data) && count($log_data)>0)
		 {
				$data =array( 
											"pagetitle" 	  	    	=> "DPCS Sub Page Count",	
											"pagetitle_sub" 				=> "Sub page Count",
											'log_data'							=> $log_data,
											"msg"	 		 							=>  $this->msg,
											"errors" 	     					=>  $this->errors
											);
							$this->_display("dpcs_sub_page",$data);
		 }
		 else
		 {
				redirect($this->page_name);
		 }
	}
	
	
	
	//--------------------------------------------------------------------------------------------------------------------------
	  function history($id)
    {
				$this->load->helper('useful');
				if($this->session->flashdata('restore_version'))
				{
					$this->msg = $this->session->flashdata('restore_version');
				}
				
				$id== (int) $this->uri->segment(4);
				if( $id == 0 || empty($id))
				{
						redirect($this->page_name);
				}
				else
				{
					 $SQL_HISTORY= " SELECT 
																		*
														FROM    
																	".TBL_PAGE_HISTORY."
														WHERE   
																		page_id=".$id."";
					$history_log   =$this->db_interaction->run_query($SQL_HISTORY);
					
					$SQL_USER = "SELECT 
																PM_LOG.user_id,COUNT(PM_LOG.id) AS LOG_COUNT,USERS.email
												FROM 
																page_management_log PM_LOG
											INNER JOIN	
																users USERS ON (PM_LOG.user_id = USERS.id)
												WHERE
																PM_LOG.page_id = ".$id." 
												GROUP BY
																PM_LOG.user_id
											";
											
						$data_count   = $this->db_interaction->run_query($SQL_USER);	
					
					if(is_array($history_log) && count($history_log)>0)
					{
									
						$data = array(
														"pagetitle" 					=> "Preview History For : '".$history_log[0]["page_name"]."'",
														"pagetitle_sub" 			=> "Log Data",			
														"history" 						=> $history_log,
														"user_count"					=> $data_count,
														"msg"	 								=> $this->msg,	
														"errors" 							=> $this->errors
											);
									
								
						$this->_display('history',$data);
					}
					else
					{
							redirect($this->page_name);
					}
				}
					
		}         
	  //------------------------------------------------------------------------------------------------------
		function restore_version()
		{
				
				if(count($_POST)==0)
					redirect($this->page_name);
				
				if(isset($_POST['page_h1']))
					$page_h1 = 1;
				else
					$page_h1 = 0;	
				
				if(isset($_POST['page_title']))
					$page_title = 1;
				else
					$page_title = 0;

				if(isset($_POST['page_name']))
					$page_name = 1;
				else
					$page_name = 0;		
					
				if(isset($_POST['meta_keywords']))
					$meta_keywords = 1;
				else
					$meta_keywords = 0;	
				
				if(isset($_POST['meta_description']))
					$meta_description = 1;
				else
					$meta_description = 0;		
				
				$usercomments = "<br/>Comments : ".$this->input->post('user_comments')."<br/> User Details ".$this->session->userdata('user')." has update version on".date('Y-m-d H:i:s');
				$dpcs_id 			= $this->input->post('restore_dpcs_id');
				$version_id 	= $this->input->post('restore_version_id');
				$which_page 	= $this->input->post('which_page');
				
				//----------------------------------------------------
					$SQL_UPDATE = "UPDATE 
																	".TBL_PAGE_HISTORY." 
													SET 
																	current_copy_flag = 0 
													WHERE
																	page_id =".$dpcs_id."
												";
					$this->db->query($SQL_UPDATE);	
				
					
					
					$get_page_management_log_data = $this->db_interaction->get_data_logpages($version_id);
					
					if(is_array($get_page_management_log_data) && count($get_page_management_log_data)>0)
					{
						 $data_update_dpcs = array(
																				'page_html_data' => $get_page_management_log_data[0]['page_html_data'],
																			  'page_head'			 => $get_page_management_log_data[0]['page_head'],
																				'seo_content'		 => $get_page_management_log_data[0]['seo_content'],
																				'version_date' 	 => $get_page_management_log_data[0]['created'],
																			);
							if($meta_keywords == 1)
							{
								$data_update_dpcs['page_meta_keywords'] =  $get_page_management_log_data[0]['page_meta_keywords'];
							}
							if($meta_description == 1)
							{
								$data_update_dpcs['page_meta_description'] =  $get_page_management_log_data[0]['page_meta_description'];
							}
							
							if($page_h1 == 1)
							{
								$data_update_dpcs['page_h1'] 					 =  $get_page_management_log_data[0]['page_h1'];
							}
							
							if($page_title == 1)
							{
								$data_update_dpcs['page_title_tag'] 					 =  $get_page_management_log_data[0]['page_title_tag'];
							}
							
							if($page_name == 1)
							{
								$data_update_dpcs['page_name'] 								 =  $get_page_management_log_data[0]['page_name'];
							}
							
							$this->db_interaction->update_user($dpcs_id,$data_update_dpcs);
							
					}//END OF if(is_array($get_page_management_log_data) && count($get_page_management_log_data)>0)
					
					$usercomments  .= "<br/>Updated Feilds :" .implode('|',array_keys($data_update_dpcs)).'<br/><hr/>';
					$SQL_UPDATE_VERSION = "UPDATE 
													".TBL_PAGE_HISTORY." 
											SET 
													current_copy_flag = 1,
													user_comments	    = CONCAT_WS('<br/>',user_comments,".$this->db->escape($usercomments)."),
													modified_by				= ".$this->db->escape($this->session->userdata('user')).",
													ip_address				= ".$this->db->escape($this->getRealIpAddr()).",
													modified			= ".$this->db->escape(date('Y-m-d H:i:s'))."																	
											WHERE
																					id  =".$version_id."
												";
					$this->db->query($SQL_UPDATE_VERSION);
					$message = "Page with version id : ".$version_id." and version date :".$get_page_management_log_data[0]['created']." is restored Successfully";
					$this->session->set_flashdata('restore_version',$message);
					if($which_page == 'show_list')
						redirect($this->page_name);
					else
						redirect($this->page_name.'/history/'.$dpcs_id);
						
		}
		
		
		//------------------------------------------------------------------------------------------------------

		/*
		* FUNCTION TO SHOW History Page data
		*
		*/
			function show_page_data($page_id)
			{
				
				$SQL_PAGE_DATA = "SELECT 
																			page_head,page_html_data
														FROM 
																			".TBL_PAGE_HISTORY."
														WHERE
																			id = ".$page_id."
													";
				$page_data = $this->db_interaction->run_query($SQL_PAGE_DATA);
				if(is_array($page_data) && count($page_data)>0)
				{
						$data = array(
													'page_id'			=> $page_id,
													'page_data'		=> $page_data[0]
												 );
						
				}
				else
				{
					$data = array(
													'page_id'			=> $page_id,
													'page_data'		=> array()
												 );		
					
				}
			
				$this->load->view('page_history_content',$data);			
				
			}	
	//-----------------------------------------------------------------------------------------------------------------------------
	
	/**
	 * Active
	 *
	 * Lets you activate a record.
	 * It updates the status field as 1
	 *
	 * @access	private
	 * @param	integer
	 */	
	function active($id)
	{
		$id = (int) $id;
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			$data = array(
			   'status' 	=> 1,			   
			   'modified_by'=> $this->session->userdata("user")
            );
			$this->db_interaction->update_user($id,$data);
			$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is made active.";
			$this->show_list();	
		}
	}
	
	/**
	 * Inactive
	 *
	 * Lets you deactivate a record.
	 * It updates the status field as 0
	 *
	 * @access	private
	 * @param	integer
	 */	
	function inactive($id)
	{
		$id = (int) $this->uri->segment(4);
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			$data = array(
			   'status' 	=> 0,			   
			   'modified_by'=> $this->session->userdata("user")
            );
			$this->db_interaction->update_user($id,$data);
			$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is made inactive.";
			$this->show_list();	
		}
	}
	
	/**
	 * Archive
	 *
	 * Lets you archive a record.
	 * It adds duplicate of the module and adds it in archived table as well
	 *
	 * @access	private
	 * @param	integer
	 */	
	function archived($id)
	{
		$id = (int) $id;
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			// check for token to solve post back issue
			//$token_error = $this->_token_check($this->input->post('token'));
			
			$result = $this->db_interaction->get_records_where('id',$id);
			
			//echo "<pre>";
			//print_r($result);
			$frm = $result[0];
			$num_versions = $frm['num_versions']+1;
			$page_name = $frm['page_name']."_v".$num_versions;
			$page_include_file = $frm['page_include_file']."_v".$num_versions;
			//echo "----".$page_name."<br />----".$page_include_file;
//			exit;
			
			$data = array( 		
					  'num_versions'		    => $num_versions
					);
			$this->db_interaction->update_user($id,$data);
			
			$data = array( 
					   'page_name'								=> $page_name,	  
					   'page_html_data'							=> $frm['page_html_data'],
					   'page_header'							=> $frm['page_header'],
					   'page_footer'							=> $frm['page_footer'],
					   'page_meta_keywords'						=> $frm['page_meta_keywords'],
					   'page_meta_description'					=> $frm['page_meta_description'],
					   'page_h1'								=> $frm['page_h1'],
					   'page_title_tag' 						=> $frm['page_title_tag'],
					   'page_parent_id'							=> $frm['page_parent_id'],
					   'page_link_id'							=> $frm['page_link_id'],
					   'page_include_file'						=> $page_include_file,
					   'page_featured'		    				=> $frm['page_featured'],
					   'featured_image_name' 					=> $frm['featured_image_name'],
					   'featured_image_new_flag'				=> $frm['featured_image_new_flag'],
					   'page_related_page' 						=> $frm['page_related_page'],
					   'page_featured_page'						=> $frm['page_featured_page'],
					   'created'								=> $frm['created'],
					   'modified' 								=> date("Y-m-d H:i:S"),
					   'status'									=> $frm['status'],
					   'display_footer'							=> $frm['display_footer'],
					   'hide_client'							=> $frm['hide_client'],
					   'rank'									=> $frm['rank'],
					   'created_by'								=> $frm['created_by'],
					   'modified_by'  							=> $frm['modified_by'],
					   'archived'  								=> 1,
					   'version_no'							    => $num_versions				
					);
			$this->db_interaction->add_user($data);
			$ins_id = $this->db->insert_id();
			
			$query = "INSERT INTO ".TBL_ARCHIVED_MODULES." (page_id, name, status, module_key, parent_id) VALUES (".$ins_id.", '".$page_name."', '1', '".$id."','".$id."');";		
			$this->db->query($query);
			$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is archived. Pagename: '".$page_name."' , Load Module Name: '".$page_include_file."'";
			$this->show_list();			}
	}
	
	
	/**
	*Sample
	*/
	function sample($id)
	{
		$id = (int) $id;
		$page = (int) $this->uri->segment(5);
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			$data = array(
			   'sample' 	=> 1,			   
			   'modified_by'=> $this->session->userdata("user")
            );
			$this->db_interaction->update_user($id,$data);
			$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is a sample.";
			if($page==1)
				redirect('archived_modules/archived_modules_admin');
			else
				$this->show_list();	
		}
	}
	
	
	/**
	*Not a Sample
	*/
	function not_sample($id)
	{
		$id = (int) $id;
		$page = (int) $this->uri->segment(5);
		
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			$data = array(
			   'sample' 	=> 0,			   
			   'modified_by'=> $this->session->userdata("user")
            );
			$this->db_interaction->update_user($id,$data);
			$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is not a sample.";
			if($page==1)
				redirect('archived_modules/archived_modules_admin');
			else
				$this->show_list();	
		}
	}
	
	/**
	 * Active hide client
	 *
	 * Lets you activate a record.
	 * It updates the status field as 1
	 *
	 * @access	private
	 * @param	integer
	 */	
	function active_hide_client($id)
	{
		$id = (int) $id;
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			$data = array(
			   'hide_client' 	=> 1,			   
			   'modified_by'=> $this->session->userdata("user")
            );
			$this->db_interaction->update_user($id,$data);
			$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is made visible to client.";
			$this->show_list();	
		}
	}
	
	/**
	 * Inactive hide client
	 *
	 * Lets you deactivate a record.
	 * It updates the status field as 0
	 *
	 * @access	private
	 * @param	integer
	 */	
	function inactive_hide_client($id)
	{
		$id = (int) $this->uri->segment(4);
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			$data = array(
			   'hide_client' 	=> 0,			   
			   'modified_by'=> $this->session->userdata("user")
            );
			$this->db_interaction->update_user($id,$data);
			$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is made invisible from client.";
			$this->show_list();	
		}
	}
	
	
	/**
	 * Active hide client
	 *
	 * Lets you activate a record.
	 * It updates the status field as 1
	 *
	 * @access	private
	 * @param	integer
	 */	
	function active_frontend_menu($id)
	{
		$id = (int) $id;
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			$data = array(
			   'menu_frontend' 	=> 1,			   
			   'modified_by'=> $this->session->userdata("user")
            );
			$this->db_interaction->update_user($id,$data);
			$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is made visible in Front End Menu.";
			$this->show_list();	
		}
	}
	
	/**
	 * Inactive hide client
	 *
	 * Lets you deactivate a record.
	 * It updates the status field as 0
	 *
	 * @access	private
	 * @param	integer
	 */	
	function inactive_frontend_menu($id)
	{
		$id = (int) $this->uri->segment(4);
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			$data = array(
			   'menu_frontend' 	=> 0,			   
			   'modified_by'=> $this->session->userdata("user")
            );
			$this->db_interaction->update_user($id,$data);
			$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is made invisible Front End Menu.";
			$this->show_list();	
		}
	}
	
	
	/**
	 * Active hide client
	 *
	 * Lets you activate a record.
	 * It updates the status field as 1
	 *
	 * @access	private
	 * @param	integer
	 */	
	function show_footer($id)
	{
		$id = (int) $id;
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			$data = array(
			   'display_footer' 	=> 1,			   
			   'modified_by'=> $this->session->userdata("user")
            );
			$this->db_interaction->update_user($id,$data);
			$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is displayed on footer.";
			$this->show_list();	
		}
	}
	
	/**
	 * Inactive hide client
	 *
	 * Lets you deactivate a record.
	 * It updates the status field as 0
	 *
	 * @access	private
	 * @param	integer
	 */	
	function hide_footer($id)
	{
		$id = (int) $this->uri->segment(4);
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			$data = array(
			   'display_footer' 	=> 0,			   
			   'modified_by'=> $this->session->userdata("user")
            );
			$this->db_interaction->update_user($id,$data);
			$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is not displayed on footer.";
			$this->show_list();	
		}
	}
	/**
	 * Delete
	 *
	 * Lets you move the particular record in trash
	 * It updates the status field as 2
	 *
	 * @access	private
	 * @param	integer
	 */	
	function delete()
	{
		$id = (int) $this->uri->segment(4);
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
				$check_priviledge = $this->validate_priviledege($id);
				if(!$check_priviledge)	
				{
						$message = "You don't have Sufficient Priviledge to Delete these page";
						$this->session->set_flashdata('invalid_access',$message);
						redirect($this->page_name);
				}//END of if(!$check_priviledge)	
				else
				{
						//first check whether it does not have any chil Pages.
						//$params 		= array("page_parent_id" => $id ,'status'=>1);
						$params 		= array("page_parent_id" => $id);
						$result_set_cnt = $this->db_interaction->get_num_records_where($params);

						if($result_set_cnt > 0)
						{
							$this->errors = "<li>Page '".$this->_get_user_details($id,"page_name")."' cannot be deleted as there are sub Page(s) within it.</li>";
						}
						else
						{			 
							$data = array(
														 'status' 	=> 2,				   
														 'modified_by'=> $this->session->userdata("user")
													);
							$this->db_interaction->update_user($id,$data);
							$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is moved to trash.";
						}
						$this->show_list();	
				}		
		}
	}
	
	/**
	 * Delete Selected
	 *
	 * Lets you move the particular records in trash
	 * It updates the status field as 2
	 *
	 * @access	private
	 * @param	array
	 */	
	function delete_selected()
	{

		$ids 			  = array();
		$names 			  = array();	
		$names_errors 	  = array();
		$str_names		  = "";
		$str_names_errors = "";
		
		$ids = $this->input->post('tablechoice');

		if( $ids == 0 || !is_array($ids))
		{
			redirect($this->page_name);			
		}
		else
		{
			foreach($ids as $id)
			{
				//first check whether it does not have any chil Pages.
				$params 		= array("page_parent_id" => $id,"status ="=>1);
				$result_set_cnt = $this->db_interaction->get_num_records_where($params);
		
				$itmem_name		= $this->_get_user_details($id,"page_name");
				if($result_set_cnt > 0)
				{
					$names_errors[] = $itmem_name;
				}
				else
				{
							
					
$data = array(
					   'status' 	=> 2,					   
					   'modified_by'=> $this->session->userdata("user")
					);
					$this->db_interaction->update_user($id,$data);
					$names[] = $itmem_name;
				}
				
			}

			$str_names			= implode("', '",$names);			
			$str_names_errors	= implode("', '",$names_errors);
			$this->msg  = "";
			
			if(!empty($str_names))
				$this->msg 	= "Page(s) : '".$str_names."' is moved to trash.&nbsp;";				
			
			if(!empty($str_names_errors))
				$this->msg 	.= "Page(s) '".$str_names_errors."' cannot be moved to trash as there are sub page(s) within it.";
			
			$this->show_list();	
		}
	}
	
	/**
	 * Delete Permenantly
	 *
	 * Lets you delete the particular record permanenty from the database.
	 *
	 * @access	private
	 * @param	integer
	 */	
	function delete_permenantly()
	{
		$id = (int) $this->uri->segment(4);
		$names	= "";
		
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{	
			// as the records are getting deleted permanently,
			// we must first get the required details and then delete it permanently.
			$params 		= array("page_parent_id" => $id,'status'=>2);
			$result_set_cnt = $this->db_interaction->get_num_records_where($params);
			$names = $this->_get_user_details($id,"page_name");
			if($result_set_cnt > 0)
			{
							$this->errors = "<li>Page '".$this->_get_user_details($id,"page_name")."' cannot be deleted as there are sub Page(s) within it.</li>";
			}
			else
			{
				$this->db_interaction->delete_user($id);	
				
				$query 	= "DELETE FROM ".$this->banner_page." WHERE map_id = ".$id;
				$this->db->query($query);		
				
				if($this->session->userdata("email_id") != "admin@exateam.com") //SINCE ADMIN HAS ACCESS TO ALL MODULES
				{
						$SQL = "DELETE FROM
																	".TBL_PAGE_USERS_BACKEND." 
														WHERE
																	 pid = ".$id." 
									";
						$this->db->query($SQL);			
				}		

				
				if(!empty($names))
				{
					$this->msg 	= "Page '".$names."' is deleted permanently.";
				}	
				else
				{	
					$this->errors 	= "<li>Cannot delete page(s) that have been previously deleted.</li>";
				}
			}
			$this->show_trash_list();	
		}
	}
	
	/**
	 * Delete Selected Permenantly
	 *
	 * Lets you delete the particular records selected permanenty from the database.
	 *
	 * @access	private
	 * @param	array
	 */	
	function delete_selected_permenantly()
	{
		$ids 						= array();
		$no_restore			= array();	
		$names					= array();	
		$allowed 				= array();	
    $disallowed     = array();  
		$parent_id_array= array();	
		$str_names			= "";
		$norestore			= "";
		
		$ids = $this->input->post('tablechoice');
		
		if( $ids == 0 || !is_array($ids))
		{
			redirect($this->page_name);			
		}
		else
		{
			
			 $ids_restore 			= implode(',',$ids);
			 
			 //modified by Ravi for the parent child check while deleting	-	start
			 
      $qry = "SELECT id, page_name, status, page_parent_id FROM ".TBL_PAGE." WHERE id IN(".$ids_restore.") OR page_parent_id IN (".$ids_restore.")";
			
				$handle = $this->db->query($qry);
				$restore_selected = $handle->result_array();
        if(is_array($restore_selected) && count($restore_selected)>0)
        {
            foreach($restore_selected as $key => $val)
            {
              $parent_id_array[]  =  $val['page_parent_id'];
            }
            $parent_id_array = array_unique($parent_id_array);
         
            $disallowed = array_intersect($ids, $parent_id_array);
            $allowed = array_diff($ids, $disallowed);
        }
         
			//parent child check while deleting  -  end
			foreach($ids as $id)
			{
				if(in_array($id,$allowed))
				{
					$names[] = $this->_get_user_details($id,"page_name");
					$this->db_interaction->delete_user($id);
					//delete the page from banner 
					$query 	= "DELETE FROM ".$this->banner_page." WHERE map_id = ".$id;
					$this->db->query($query);
					if($this->session->userdata("email_id") != "admin@exateam.com") //SINCE ADMIN HAS ACCESS TO ALL MODULES
					{
						$SQL = "DELETE FROM
																	".TBL_PAGE_USERS_BACKEND." 
														WHERE
																	 pid = ".$id." 
									";
						$this->db->query($SQL);			
					}		
				}
				else
				{
						if(isset($disallowed[$id]))
							$no_restore[] =  $disallowed[$id];
				}	
			}
			$check_if_deleted = $this->_check_if_empty($names);

			if($check_if_deleted == 1)
			{
				$str_names	= implode(", ",$names);				
			}
			
			if(is_array($no_restore) && count($no_restore)>0)
			{
				$this->errors 	= "<li>Cannot delete page(s) ".implode(", ",$no_restore)." since Parent page is in Trash. Delete child page first/li>";
			}			
			
			if(!empty($str_names))	
			{
				$this->msg 	= "Page(s) : '".$str_names."' deleted permanently.";	
			}
			else
			{	
				$this->errors 	= "<li>Cannot delete page(s) that have been previously deleted.</li>";
				
			}					
			$this->show_trash_list();	
		}
	}
	
	/**
	 * Restore
	 *
	 * Lets you restore the record.
	 * It will move the record from trash to normal listing.
	 * It updates the status field as 1 
	 *
	 * @access	private
	 * @param	integer
	 */
	function restore()
	{
		$restore_indicator = 0;	
		$id = (int) $this->uri->segment(4);
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
			$restore_check = $this->validate_restore_functionality($id);
			
			if(is_array($restore_check) && count($restore_check)>0)
			{
					if($restore_check[0]['PARENT_STATUS']== 2)
					{
						$restore_indicator = 0;	
						$message = "Page ".$restore_check[0]['CHILD_NAME']." Cannot be restored as The parent Page ".$restore_check[0]['PARENT_NAME']." is in trash . Please Restore ".$restore_check[0]['PARENT_NAME']." first";
						 $this->session->set_flashdata('restore_indicator',$message);
						 redirect($this->page_name.'/show_trash_list');
					}
					else
					{
						$restore_indicator = 1;
					}
			}
			if($restore_indicator ==1)
			{
				$data = array(
										 'status' 	=> 1,			   
										 'modified_by'=> $this->session->userdata("user")
											);
				$this->db_interaction->update_user($id,$data);
				$this->msg = "Page '".$this->_get_user_details($id,"page_name")."' is restored successful.";
				$this->show_trash_list();	
			}	
		}
	}
	
	function validate_restore_functionality($id)
	{
		$restore_status  = array();
		$SQL_RESTORE ="SELECT 
													PARENT.page_name AS PARENT_NAME,PARENT.STATUS AS PARENT_STATUS ,PARENT.id AS PARENT_ID,
													CHILD.id AS CHILD_ID,CHILD.page_name AS CHILD_NAME
										FROM 
													".TBL_PAGE." PARENT
									LEFT JOIN 
													".TBL_PAGE." CHILD ON (CHILD.page_parent_id=PARENT.id)
										WHERE
													CHILD.id IN(".$id.")";
		$restore_status = $this->db_interaction->run_query($SQL_RESTORE);
		return $restore_status;
		
	}
	
	
	/**
	 * Restore Selected
	 *
	 * Lets you restore the records.
	 * It will move the records from trash to normal listing.
	 * It updates the status field as 1 
	 *
	 * @access	private
	 * @param	array
	 */
	function restore_selected()
	{
		$ids 						= array();
		$names 					= array();	
		$no_restore			= array();	
		$allowed 				= array();	
		$disallowed 		= array();	
		$str_names			= "";
		$norestore			= "";
		
		$ids = $this->input->post('tablechoice');
		
		if( $ids == 0 || !is_array($ids))
		{
			redirect($this->page_name);			
		}
		else
		{
			$ids_restore 			= implode(',',$ids);
			$restore_selected = $this->validate_restore_functionality($ids_restore);
			
			if(is_array($restore_selected) && count($restore_selected)>0)
			{
				foreach($restore_selected as $dpcs_details)
				{
					if($dpcs_details['PARENT_STATUS'] == 2)
						$disallowed[$dpcs_details['CHILD_ID']] = $dpcs_details['PARENT_NAME'];
					else
						$allowed[$dpcs_details['CHILD_ID']] = $dpcs_details['PARENT_NAME'];
				}
			}
			
			foreach($ids as $id)
			{
				if(array_key_exists($id,$allowed))
				{
					$data = array(
													'status' 	=> 1,			   
													'modified_by'=> $this->session->userdata("user")
											);
						
					$this->db_interaction->update_user($id,$data);
					$names[] = $this->_get_user_details($id,"page_name");
				}
				else
				{
						if(isset($disallowed[$id]))
							$no_restore[] =  $disallowed[$id];
				}				
			}
			$str_names			= implode("', '",$names);
			$norestore			= implode("', '",$no_restore);
			if($str_names <> '')
				$this->msg 			= "Page(s) : '".$str_names."' restored successfully.";
			
			if($norestore <> '')
			$this->errors 	= "Page(s) : '".$norestore."' can be restored since parent page is in trash .";
			$this->show_trash_list();	
		}
	}
	
	/**
	 * Preview
	 *
	 * Lets you preview a given record from the database.
	 *
	 * @access	private
	 * @param	integer
	 */	
	function preview()	
	{
		$id = (int) $this->uri->segment(4);
		
		if( $id == 0 || empty($id))
		{
			redirect($this->page_name);			
		}
		else
		{
		
			$result = $this->db_interaction->get_records_where('id',$id);
			$cnt = count($result);
			
			
			if($cnt > 0)
			{
				$frm 		= $result[0];
				$page_parent 	= $this->_get_user_details($frm["page_parent_id"],"page_name");			
				$page_parent 	= ($page_parent) ? $page_parent : "Base";

				$page_link	 	= $this->_get_user_details($frm["page_link_id"],"page_name");			
				$page_link 		= ($page_link) ? $page_link : "Base";

				$data = array("pagetitle" 		=> "Preview page : '".$frm["page_name"]."'",
					"pagetitle_sub" 		=> "Page Data",			
					"id" 					=> $frm["id"],
					"page_name" 			=> $frm["page_name"],
					"page_header"			=> $frm["page_header"],			
					"page_footer"			=> $frm["page_footer"],
					"page_meta_keywords"	=> $frm["page_meta_keywords"],			
					"page_meta_description"	=> $frm["page_meta_description"],			
					"page_h1"				=> $frm["page_h1"],			
					"page_title_tag"		=> $frm["page_title_tag"],		
					"page_html_data"		=> $frm["page_html_data"],	
					"page_include_file"		=> $frm["page_include_file"],
					"page_featured"		    => $frm["page_featured"],
					"page_related_page"		=> $frm["page_related_page"],
					"page_featured_page"	=> $frm["page_featured_page"],
					"page_parent" 			=> $page_parent,
					"page_link" 			=> $page_link,
					"msg"	 				=> $this->msg,	
					"errors" 				=> $this->errors);
				
				$this->_display('preview',$data);
			}
			else
			{ 
				redirect($this->page_name);
			}	
		}
	}	
	/**
	 * Seo
	 *
	 * Lets you update the seo details for all the pages
	 *
	 * @access	public
	 */		
	function seo()
	{
		$result_data	= $this->db_interaction->get_records(NULL, NULL,array("status =" => 1),'','','page_name asc');
		
		$this->page_h1 = "Web Magnet";
		
		$data = array(
						"pagetitle" 		=> "List Of Pages In System",
						"submit_name"		=> "seoupdate",
						"submit_value"		=> "Seo Update",
						"result_data"		=> $result_data,
						"msg"	 			=> $this->msg,	
						"errors" 			=> $this->errors);
						
						
		$this->_display('seo',$data);
	}
	/**
	 * Seo Updation
	 *
	 * Lets you update the seo details for all the pages
	 *
	 * @access	public
	 */		
	function seoupdate()
	{
		$mode	= $this->input->post('mode');
		$ids	= $this->input->post('idsArray');
		
		if($mode == "seoupdate" && strlen($ids) >  0)
		{
			$ids = explode(',',$ids);
			foreach($ids as $id)
			{
				$mk							= $_POST[$id.'meta_keywords'];
				$md							= $_POST[$id.'meta_description'] ;
				$h1							= $_POST[$id.'h1'];
				$seo_content		= $_POST[$id.'seo_content'];
				$title_page		  = $_POST[$id.'title_tag'];

				$data = array(
								'page_meta_keywords' 		=> $mk,
								'page_meta_description'	=> $md,
								'page_h1'	 							=> $h1,
								'page_title_tag' 				=> $title_page,
								'seo_content'						=> $seo_content,
								'modified_by'						=> $this->session->userdata("user")
							);
			$this->db_interaction->update_user($id,$data);
			$this->history_log($data,$id);
			}
		}//END OF if($mode == "seoupdate" && strlen($ids) >  0)
		$this->msg = "Records are updated successfully.";	
		$this->show_list();	
	}
	/**
	 * Export
	 *
	 * Lets you export the db data in an excel format
	 *
	 * @access	public
	 * @return 	xls - excel file
	 */		
	function export()
	{
		$filename 	= $this->page_name.date("Ymdhis");
		$this->load->plugin('to_excel');
		$query = "SELECT 
														tbl1.id AS `Menu ID` , 
														tbl1.page_name AS `Page Name` ,
														tbl2.page_name AS `Page Parent` , 
														tbl1.page_meta_keywords AS `Meta Keywords` , 
														tbl1.page_meta_description AS `Meta Description` , 
														tbl1.page_h1 AS `H1 Tag` ,
														tbl1.page_title_tag AS `Page Title` ,
														tbl1.page_html_data AS `Page Content` , 
														tbl1.page_include_file AS `Include External File` , 
														tbl1.page_featured AS `Featured Page` , 
														tbl1.page_related_page AS `Related Page` , 
														tbl1.created AS `Created`
								FROM 
														".TBL_PAGE." AS tbl1
								LEFT JOIN 
														".TBL_PAGE." AS tbl2 ON tbl1.page_parent_id = tbl2.id";
		if($this->session->userdata('user') == 'admin@exateam.com')
		{
			$query .="	WHERE 
														tbl1.status !=2
								ORDER BY 
														tbl1.page_parent_id, tbl1.rank";
		}
		else
		{
				$allowed_modules_session   = isset($this->session->userdata['modules_allowed']['pid_frontend'])?$this->session->userdata['modules_allowed']['pid_frontend']:'';
			 if($allowed_modules_session!='')
					 $modules_allowed        = implode(',',array_keys($allowed_modules_session));
			 else
					 $modules_allowed        = 0;
					 
					$user_id		= $this->session->userdata('user_id');
					
			$query .="	WHERE 
														tbl1.status !=2 AND (tbl1.id IN (".$modules_allowed.") OR tbl1.user_id = ".$user_id.")
								ORDER BY 
														tbl1.page_parent_id, tbl1.rank";		
		}		
		$result_set = $this->db->query($query);

		to_excel($result_set, $filename); // filename is optional, without it, the plugin will default to 'exceloutput'
	}
	
	
	
	function change_rank()
	{
		// do necessary actions
		$id 			= (int) $this->input->post('id');
		$curr_rank 		= (int) $this->input->post('curr_rank');
		$site_id_rank 	= (int) $this->input->post('category_id_rank');
		$new_rank 	= (int) $this->input->post('new_rank');		
		
		if(!empty($id) || !empty($id) || !empty($id))
		{
			
			$data = array(
			   'rank' 			=> $new_rank,               			   
			   'modified_by'	=> $this->session->userdata("user")
			);
						
			$this->db_interaction->update_user($id,$data);
			$this->_re_rank($id, $new_rank,$site_id_rank);
			//$this->_re_rank();
			
			$this->msg 		= "Page '".$this->_get_user_details($id,"page_name")."' is updated with new rank : '".$new_rank ."' (old rank : '".$curr_rank."')";	
		}
		else
		{	
			$this->errors 	= "<li>Please enter a valid rank for a record.</li>";
		}
			
		$this->show_list();
	}
		/**
	 * Re Rank 
	 * 
	 * Fixes the rank, for all the records in normal listing page in orderby rank
	 * And then records in trash are given ranking.
	 *
	 * @access private
	 *
	 */
	/*function _re_rank($id = '', $new_rank = '',$category_id_rank='')
  	{
		// re-arrange News not in trash
			$res_rank = array();
			if(isset($id))
				{
						$res_rank  = $this->db_interaction->rerank_data($id,$category_id_rank);
				}
				$cnt_res_rank = count($res_rank);
				
				if(is_array($res_rank) && $cnt_res_rank > 0)
				{
					$rank	= 1;
						foreach($res_rank as $key => $items)
						{
								$rank = ($new_rank == $rank) ? $rank+1:$rank;
							$data = array(
								 'rank' 	=> $rank,				   
								 'modified_by'=> $this->session->userdata("user")
							);
							$this->db_interaction->update_user($items['id'],$data);			
							$rank++;
						}	
				}
  	}*/
	
	/**
	 * Re Rank 
	 * 
	 * Fixes the rank, for all the records in normal listing page in orderby rank
	 * And then records in trash are given ranking.
	 *
	 * @access private
	 *
	 */
	function _re_rank($id = '', $new_rank = '',$site_id_rank='')
  	{
		// re-arrange Pages not in trash
		$res_rank = array();
		if(isset($id))
		{
			$res_rank  = $this->db_interaction->rerank_data($id,$site_id_rank);
		}
		$cnt_res_rank = count($res_rank);
		if(is_array($res_rank) && $cnt_res_rank > 0)
		{
			$rank	= 1;
			foreach($res_rank as $key => $items)
			{
				$rank = ($new_rank == $rank) ? $rank+1:$rank;
				$data = array(
								'rank' 	=> $rank,				   
								'modified_by'=> $this->session->userdata("user")
							);
				$this->db_interaction->update_user($items['id'],$data);			
				$rank++;
			}//END OF foreach($res_rank as $key => $items)
		}//END OF if(is_array($res_rank) && $cnt_res_rank > 0)
  	}
	
	
	
	/**
	 * Change Rank
	 *
	 * Lets you change the rank of a record
	 * It also re-aaranges the rest of the records with its new ranks
	 *
	 * @access	public
	 */		
	function change_ranks()
	{
		
		// do necessary actions
		$id 		= (int) $this->input->post('id');
		$curr_rank 	= (int) $this->input->post('curr_rank');

		$new_rank 	= (int) $this->input->post('new_rank');		
		
		if(!empty($id) || !empty($id) || !empty($id))
		{
			
			$data = array(
			   'rank' 			=> $new_rank,               			   
			   'modified_by'	=> $this->session->userdata("user")
			);
						
			$this->db_interaction->update_user($id,$data);
			
			$this->_re_rank();
			
			$this->msg 		= "Page '".$this->_get_user_details($id,"page_name")."' is updated with new rank : '".$new_rank ."' (old rank : '".$curr_rank."')";	
		}
		else
		{	
			$this->errors 	= "<li>Please enter a valid rank for a record.</li>";
		}
			
		$this->show_list();
	}
	
	
	
	//-----------------------------------------------------------------------------------------------------------------
	/*
	| Function to Maintain History of DPCS
	*/
	
	function history_log($data,$last_insert_id)
	{
			
			//----------------------------------------------------
			$SQL_UPDATE = "UPDATE 
															".TBL_PAGE_HISTORY." 
											SET 
															current_copy_flag =0 
											WHERE
															page_id =".$last_insert_id."
										";
			$this->db->query($SQL_UPDATE);	
			//-------------------NEW NAMING OF PAGE-------------------------------
			$ip_address = $this->getRealIpAddr();
			$data_insert = array( 
												 'page_name'							=> isset($data['page_name'])?$data['page_name']:'',
												 'page_title_tag' 				=> isset($data['page_title_tag'])?$data['page_title_tag']:'',
												 'page_parent_id'					=> isset($data["page_parent_id"])?$data["page_parent_id"]:'',
												 'page_header'						=> isset($data["page_header"])?$data["page_header"]:FRONTEND_HEADER,
												 'page_footer'						=> isset($data["page_footer"])?$data["page_footer"]:FRONTEND_FOOTER,
												 'page_meta_keywords'			=> isset($data["page_meta_keywords"])?$data["page_meta_keywords"]:'',
												 'page_meta_description'	=> isset($data["page_meta_description"])?$data["page_meta_description"]:'',
												 'page_h1'								=> isset($data["page_h1"])?$data["page_h1"]:'',
												 'seo_content'						=> isset($data["seo_content"])?$data["seo_content"]:'',
												 'redirect_page_id'				=> isset($data["redirect_page_id"])?$data["redirect_page_id"]:'',
												 "page_head"							=> isset($data["page_head"])?$data["page_head"]:'',
												 'page_html_data'					=> isset($data["page_html_data"])?$data["page_html_data"]:'',
												 'dashboard_category'			=> isset($data["dashboard_category"])?$data["dashboard_category"]:'',
												 'page_include_file'			=> isset($data["page_include_file"])?$data["page_include_file"]:'',
												 'page_featured'		    	=> isset($data['page_featured'])?$data['page_featured']:'',
												 'page_featured_page'			=> isset($data["page_featured_page"])?$data["page_featured_page"]:'',
												 'featured_image_name'		=> isset($data["featured_image_name"])?$data["featured_image_name"]:'',
												 'status'									=> isset($data["status"])?$data["status"]:'',
												 'display_footer'					=> isset($data['display_footer'])?$data['display_footer']:'',
												 'hide_client'						=> isset($data['hide_client'])?$data['hide_client']:'',
												 'menu_frontend'					=> isset($data['menu_frontend'])?$data['menu_frontend']:'',
												 'created'								=> date("Y-m-d H:i:s"),
												 'created_by'							=> $this->session->userdata("user"),
												 'user_id' 							  => $this->session->userdata('user_id'),
												 'page_id'								=> $last_insert_id,
												 'current_copy_flag'      => 1,
												 'ip_address'							=> $ip_address
						);	
						$this->db_interaction->add_user($data_insert,TBL_PAGE_HISTORY);
				
	}
	
	
	//-----------------------------------------------------------------------------------
	/*
	| Function to get the IP ADDRESS
	*/
	 function getRealIpAddr()
	 {
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
		    $ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			 $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	 }	
	
	
	
	/**
	 * Re Rank 
	 * 
	 * Fixes the rank, for all the records in normal listing page in orderby rank
	 * And then records in trash are given ranking.
	 *
	 * @access private
	 *
	 */
	function _re_ranks()
  	{
		// re-arrange Pages not in trash
		$res_rank = $this->db_interaction->get_all_records('rank , modified desc');
		$cnt_res_rank = count($res_rank);
		if(is_array($res_rank) && $cnt_res_rank > 0)
		{
			$rank	= 1;
			foreach($res_rank as $key => $items)
			{
				$data = array(
				   'rank' 	=> $rank,				   
				   'modified_by'=> $this->session->userdata("user")
				);
				$this->db_interaction->update_user($items['id'],$data);			
				$rank++;
			}	
		}
  	}
	
	/**
	 * Verify Form
	 *
	 * Lets you validate the whole form with the parameters passed to the validation class.
	 * We need to load 2 helper classes	:	form and url
	 * We need to load 1 library class	:	validation
	 * Note : This function cannot be accessed directly through browser as it is private to the class.
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed - depending on the validation (string or integer)
	 */
	function _verify_form($mode="")
	{  
		$this->load->library('Form_validation');
		$web_magnet			 = $this->page_db_interaction->web_magnet();
		$wm_client_status    = $web_magnet['wm_client'];
		if($this->session->userdata("email_id") == "admin@exateam.com") 
		{
		if($mode=="update")
		{ 
		$rules = array( 
				array(
					 'field'   => 'page_name',
					 'label'   => 'page name',
					 'rules'   => 'trim|required|callback_pagename_update_check|min_length[2]|max_length[50]|xss_clean'
					 ),
					   array(
                     'field'   => 'page_title_tag',
                     'label'   => 'page title tag',
                     'rules'   => 'trim|required|min_length[2]|max_length[250]|xss_clean'
					 ),
				  array(
                     'field'   => 'page_header',
                     'label'   => 'page header',
                     'rules'   => 'trim|required|xss_clean'
					 ),
				  array(
                     'field'   => 'page_footer',
                     'label'   => 'page footer',
                     'rules'   => 'trim|required|xss_clean'
					 ),
				  array(
                     'field'   => 'page_meta_keywords',
                     'label'   => 'meta keywords',
                     'rules'   => 'trim|required|min_length[2]|xss_clean'
					 ),
				  array(
                     'field'   => 'page_meta_description',
                     'label'   => 'meta description',
                     'rules'   => 'trim|required|min_length[2]|xss_clean'
					 ),
				  array(
                     'field'   => 'page_h1',
                     'label'   => 'H1',
                     'rules'   => 'trim|required|min_length[2]|xss_clean'
					 ), 				
				  array(
                     'field'   => 'page_include_file',
                     'label'   => 'include external file',
                     'rules'   => 'trim|xss_clean'
					 ),
				array(
                     'field'   => 'page_head',
                     'label'   => 'Page Head',
                     'rules'   => 'trim|xss_clean|required'
					 )					 			
					  );
					
		
		}
		else			 
		{	$rules = array(
			  	array(
					 'field'   => 'page_name',
					 'label'   => 'page name',
					 'rules'   => 'trim|required|callback_pagename_check|min_length[2]|max_length[50]|xss_clean'
					  ),
				  array(
                     'field'   => 'page_title_tag',
                     'label'   => 'page title tag',
                     'rules'   => 'trim|required|min_length[2]|max_length[250]|xss_clean'
					 ),
				  array(
                     'field'   => 'page_header',
                     'label'   => 'page header',
                     'rules'   => 'trim|required|xss_clean'
					 ),
				  array(
                     'field'   => 'page_footer',
                     'label'   => 'page footer',
                     'rules'   => 'trim|required|xss_clean'
					 ),
				  array(
                     'field'   => 'page_meta_keywords',
                     'label'   => 'meta keywords',
                     'rules'   => 'trim|required|min_length[2]|xss_clean'
					 ),
				  array(
                     'field'   => 'page_meta_description',
                     'label'   => 'meta description',
                     'rules'   => 'trim|required|min_length[2]|xss_clean'
					 ),
				  array(
                     'field'   => 'page_h1',
                     'label'   => 'H1',
                     'rules'   => 'trim|required|min_length[2]|xss_clean'
					 ), 				
				  array(
                     'field'   => 'page_include_file',
                     'label'   => 'include external file',
                     'rules'   => 'trim|xss_clean'
					 ),
					 array(
                     'field'   => 'page_head',
                     'label'   => 'Page Head',
                     'rules'   => 'trim|xss_clean|required'
					 )				 			 
	           );	
			}
			}
			else{   
				/* if($wm_client_status == 1)
				{
					$rules = array(
					array(
						 'field'   => 'page_name',
						 'label'   => 'page name',
						 'rules'   => 'trim|required|callback_pagename_update_check|min_length[2]|max_length[50]|xss_clean'
						  ),
					  array(
						 'field'   => 'page_header',
						 'label'   => 'page header',
						 'rules'   => 'trim|required|xss_clean'
						 )
				   );	
				}
				else */
				{
					if($mode=="update")
					{ 
						$rules = array( 
										array(
												 'field'   => 'page_name',
												 'label'   => 'page name',
												 'rules'   => 'trim|required|callback_pagename_update_check|min_length[2]|max_length[50]|xss_clean'
 											  ),
										array(
											 'field'   => 'page_head',
											 'label'   => 'Page Head',
											 'rules'   => 'trim|xss_clean|required'
											 )
										);
					}
					else
					{
						$rules = array(
										array(
											 'field'   => 'page_name',
											 'label'   => 'page name',
											 'rules'   => 'trim|required|callback_pagename_check|min_length[2]|max_length[50]|xss_clean'
											  ),
										  array(
											 'field'   => 'page_head',
											 'label'   => 'Page Head',
											 'rules'   => 'trim|xss_clean|required'
											 )
									  );		
					}
				  	
				 } 
				   
			}
			
		$this->form_validation->set_rules($rules);
		$this->form_validation->set_error_delimiters('<li>', '</li>');	
		$this->form_validation->run();
		$error = validation_errors();
		if(trim($this->input->post("page_header")) != ""){
			$path = ROOTBASEPATH."/application/views/".$this->input->post("page_header").".php";
			if(!is_file($path)){
				$error.="<li>".$this->input->post("page_header")." for Page Header does not exist.</li>";
			}
		}
		if(trim($this->input->post("page_footer")) != ""){
			$path = ROOTBASEPATH."/application/views/".$this->input->post("page_footer").".php";
			if(!is_file($path)){
				$error.="<li>".$this->input->post("page_footer")." for Page Footer does not exist.</li>";
			}
		}
		########Add Error For page has rights for adding more sub pages or not.########
		if($mode != "update"){
			//$this->input->post("page_parent");
			$user_id = $this->session->userdata('user_id');
			if($this->session->userdata('user') == 'admin@exateam.com' && $this->count_db_subpages($user_id) <= $this->count_non_subpages($user_id) && $this->input->post("page_parent") <> 10){
				$error = "<li>You can add no more sub pages.</li>".$error;
			}

		}
		###################OVER###################
		if ($error != "")
		{
			return $error;
		}
		else
		{
			return 0;
		}
	}
		
	/**
	 * Display
	 *
	 * Lets you display layout
	 * It loads the header file, the default messages file, the module view file and the footer
	 * Note : This function cannot be accessed directly through browser as it is private to the class.
	 *
	 * @access	public
	 * @param	string
	 * @param	array	
	 */
	function _display($viewname,$data)
	{
	    $data['image_path'] = ROOTBASEPATH . "media/images/page_management/";
		// load view and add additional data 
		$data["page_heading"] = $this->page_h1;
		$this->load->view('adminheader',$data);
		$this->load->view('top_messages',$data);
        $this->load->view($viewname,$data);
        $this->load->view('adminfooter',$data);
	}
	
	/**
	 * Display
	 *
	 * Lets you get details of a particular user
	 *
	 * @access	private
	 * @param	integer
	 * @param	string
	 * @param	mixed - array or string	
	 */
	function _get_user_details($id,$fieldname="")
	{
		if(!empty($id))
		{
			$result = $this->db_interaction->get_records_where('id',$id);			
			$cnt = count($result);
			if(is_array($result) && $cnt > 0)
			{
				$result = $result[0];
			
				$cnt = count($result);
				if($cnt > 0)
				{
					if(isset($result[$fieldname])) return $result[$fieldname];
				}
			}
			else
				return false;	
		}
	}
	 	
	//-----------------------------------------------------------------------------------------
	/*
	| Custom Function to check product name 
	| CONDITION 1 : Check if product name is not empty
	| CONDITION 2 : Check if the product name should not consist of disallwoed characters
	| CONDITION 3 : Check if the product name is not duplicate
	*/
	function pagename_update_check($page_name)
	{
		$this->load->helper('url_name');
		$page_id 	 = $this->input->post('id');
		
		if(empty($page_name) || trim($page_name)=='')
		{ //CHECK FOR EMPTY
		
			$this->form_validation->set_message('pagename_update_check', 'Page name is required');
			return FALSE;
		}
		elseif (! preg_match("/^[".$this->page_name_rules."]+$/i", $page_name))
		{//CHECK FOR DISALLOWED CHARACTERS
		
			$this->form_validation->set_message('pagename_update_check', 'An invalid name was submitted as the page name: "'.$page_name.'" The name can only contain alpha-numeric characters, dashes, underscores, colons and spaces');
			return FALSE;
		}
		else
		{//CHECK FOR UNIQUE NESS
			
			//REMOVE THE UNNECESSARY SPACES BETWEEN STRING
			
			$page_name_input  	= 	array( "/^\s+/","/\s{2,}/","/\s+\$/");
			$page_name_replace 	= 	array(""," ","");
			$sanitized_page_name =   preg_replace($page_name_input,$page_name_replace,$page_name);
			
			//REPLACE ALL SPACES WITH THE UNDERSCORE
			$illegal 				= array("_","/"," ");
			$allowed 				= array("-","-","-");
			
			$page_rename		=   url_title(str_replace($illegal,$allowed,$sanitized_page_name));
			 //$page_rename			=   str_replace(" ","-",$sanitized_page_name);
			
			$product_update_check_inproduct      =   update_product_check($page_rename);
			 $product_update_check_indpcs         =   update_page_name_check($page_rename,$page_id);
			 $product_update_check_incategory     =   update_category_check($page_rename);
			 $product_update_check_inconfig       =   update_reservedword_check($page_rename);
			 
			 if($product_update_check_inproduct == 1)
			 {
				$this->form_validation->set_message('pagename_update_check', 'Page name '.$sanitized_page_name.' already exist');
				return FALSE;
			 }
			 elseif($product_update_check_indpcs == 1)
			 {
				$this->form_validation->set_message('pagename_update_check', 'Page name '.$sanitized_page_name.' already exist in Dpcs ');
				return FALSE;
			 }
			 elseif($product_update_check_incategory == 1)
			 {
				$this->form_validation->set_message('pagename_update_check', 'Page name '.$sanitized_page_name.' already exist in Category ');
				return FALSE;
			 }
			 elseif($product_update_check_incategory == 1)
			 {
				$this->form_validation->set_message('pagename_update_check', 'Page name '.$sanitized_page_name.' already exist in Category ');
				return FALSE;
			 }
			 elseif($product_update_check_inconfig == 1)
			 {
				$this->form_validation->set_message('pagename_update_check', 'Product name '.$sanitized_page_name.' Cannot be used as it is reserved keyword ');
				return FALSE;
			 }
			 else
			 {
				return TRUE;
			 }
		}
	}
	
	//-----------------------------------------------------------------------------------------
	/*
	| Custom Function to check page name 
	| CONDITION 1 : Check if page name is not empty
	| CONDITION 2 : Check if the page name should not consist of disallwoed characters
	| CONDITION 3 : Check if the page name is not duplicate
	*/
	function pagename_check($page_name)
	{
		$this->load->helper('url_name');
		//$page_name = ($this->input->post('page_name'));
		if(empty($page_name) || trim($page_name)=='')
		{ //CHECK FOR EMPTY
		
			$this->form_validation->set_message('pagename_check', 'Page name is required');
			return FALSE;
		}
		elseif (! preg_match("/^[".$this->page_name_rules."]+$/i", $page_name))
		{//CHECK FOR DISALLOWED CHARACTERS
		
			$this->form_validation->set_message('pagename_check', 'An invalid name was submitted as the page name: "'.$page_name.'" The name can only contain alpha-numeric characters, dashes, underscores, colons and spaces');
			return FALSE;
		}
		else
		{//CHECK FOR UNIQUE NESS
			
			//REMOVE THE UNNECESSARY SPACES BETWEEN STRING
			
			$page_name_input  	 = 	array( "/^\s+/","/\s{2,}/","/\s+\$/");
			$page_name_replace 	 = 	array(""," ","");
			$sanitized_page_name =   preg_replace($page_name_input,$page_name_replace,$page_name);
			
			//REPLACE ALL SPACES WITH THE UNDERSCORE
			 //$page_rename			=   str_replace(" ","-",$sanitized_page_name);
			$illegal 				= array("_","/"," ");
			$allowed 				= array("-","-","-");
			
			$page_rename		=   url_title(str_replace($illegal,$allowed,$sanitized_page_name));
			$indicator 				=   page_name_check($page_rename); 	
			
			 if($indicator == 0)
			 {
				return true;
			 }	
			 elseif($indicator == 1)	
			 {
				$this->form_validation->set_message('pagename_check', 'Page name '.$sanitized_page_name.' already exist as a Page In Dpcs');
					return FALSE;
			 }
			 elseif($indicator == 2)	
			 {
				$this->form_validation->set_message('pagename_check', 'Page name '.$sanitized_page_name.' already exist');
					return FALSE;
			 }
			 elseif($indicator == 3)	
			 {
				$this->form_validation->set_message('pagename_check', 'Page name '.$sanitized_page_name.' already exist as a Category Name');
					return FALSE;
			 }
			 elseif($indicator == 4)	
			 {
				$this->form_validation->set_message('pagename_check', 'Page name '.$sanitized_page_name.' Cannot be used as it is reserved Keyword');
					return FALSE;
			 }
			 else
			 {
				return FALSE;
			 }
			
		}
	}
	
	/**
	 * Clear Search Filters
	 *
	 * Lets you Unset all the values for search query
	 *
	 * @access	private
	 */	
	function _clear_search_filters()
	{
		// Unset session array
		if(isset($_SESSION['search_filter']) && !empty($_SESSION['search_filter']))
		{
			$array_items = array('search_filter' 	=> '');
			$this->session->unset_userdata($array_items);		
		}
	}
	
	
	/**
	 * Check If Empty
	 *
	 * Lets you check whether a string or an array is empty or not
	 *
	 * @access	private
	 * @return boolean
	 */	
	function _check_if_empty($data)
	{
		$set_return = 1;
		if(is_array($data))
		{
			$cnt = count($data);
			if($data > 0)
			{
				foreach($data as $key=>$value)
				{
					if(empty($value))
						$set_return = 0;
				}
			}
			else
			{
				$set_return = 0;
			}
		}
		else
		{
			if(empty($data))
				$set_return = 0;
		}
		
		return $set_return;
	}
	
	
	

	/**
	 * Buld Tree
	 *
	 * Lets you create a select box using a recursive function
	 *
	 * @access private
	 * @param string
	 * @param string
	 * @return string
	 */	
	function _build_tree($selected=NULL,$current_id=NULL)
	{
		$data_str 	= "";
		$id			= 0;
		$indent 	= "";
		
		//Define first element
	//	$data_str .= "<option value=\"\">Please Select</option>\n\n";
		$this->_list_data_recursively(&$id,&$data_str,$indent,$selected,$current_id);			  
		return $data_str ;
	}
	
	//------------------------------------------------------------------------------------------
	/*
	| Function to create the option box for the categories upto nth level
	| these Function is bascialy the replacement of the _build_tree and _list_data_recursively
	*/
	
		function mapTree($selected_value="",$neglect_id="",$base_page_needed=0) 
		{
			
			$dataset 	  = array();
			$tree 		  = array();
			if($this->session->userdata("email_id") == "admin@exateam.com")
			{
				$all_page	  = $this->db_interaction->get_page_all(0);
			}
			else
			{
				$allowed_modules_session   = isset($this->session->userdata['modules_allowed']['pid_frontend'])?$this->session->userdata['modules_allowed']['pid_frontend']:'';
			 if($allowed_modules_session!='')
					 $modules_allowed        = implode(',',array_keys($allowed_modules_session));
			 else
					 $modules_allowed        = 0;
					 
					$user_id		= $this->session->userdata('user_id');
					
					$all_page	  = $this->db_interaction->get_page_all(1,$modules_allowed,$user_id);
			}			
			
			
			if(is_array($all_page) && count($all_page)>0)
			{
				foreach($all_page as $page_details)
				{
					$dataset[$page_details['id']]= array(
															'page_name' 		=> $page_details['page_name'],
															'parent_id' 		=> $page_details['page_parent_id'],
															'user_id' 			=> $page_details['user_id'],
															'hide_client' 	=> $page_details['hide_client'],
															);		
				}//END OF foreach($all_category as $category_details)
				
				foreach ($dataset as $id=>&$node)
				{
					if(isset($node['parent_id'])&& $neglect_id !=$id) //IF PARENT IS DISABLED THAN DISABLE THE ENTIRE ARRAY OF CHILD   
					{	
						if ($node['parent_id'] == 0)
						{
							$tree[$id] = &$node;
						}
						else
						{
							if (!isset($dataset[$node['parent_id']]['children'])) 
								$dataset[$node['parent_id']]['children'] = array(); //IGNORE ALL CHILD IF NO PARENT ACTIVE
							//if($neglect_id != $id && $neglect_id != $node['parent_id'])
							if($node['parent_id']!=$neglect_id)
								$dataset[$node['parent_id']]['children'][$id] = &$node;
						}
					}
				}
			}//END OF if(is_array($all_category) && count($all_category)>0)
			
			
			if($neglect_id == 10)
				$this->option_list .= "<option value='0'>Home Page</option>\n\n";
			
			$this->display_tree($tree,'',$selected_value);
			return $this->option_list;
		}
	
		
		//------------------------------------------------------------------------------------------
	/*
	| Function to create the option box for the categories upto nth level
	| these Function is bascialy the replacement of the _build_tree and _list_data_recursively
	*/
	
		function redirect_mapTree($selected_value="",$neglect_id="") 
		{
			
			$dataset 	  = array();
			$tree 		  = array();
			if($this->session->userdata("email_id") == "admin@exateam.com")
			{
				$all_page	  = $this->db_interaction->get_page_all(0);
			}
			else
			{
				$allowed_modules_session   = isset($this->session->userdata['modules_allowed']['pid_frontend'])?$this->session->userdata['modules_allowed']['pid_frontend']:'';
			 if($allowed_modules_session!='')
					 $modules_allowed        = implode(',',array_keys($allowed_modules_session));
			 else
					 $modules_allowed        = 0;
					 
					$user_id		= $this->session->userdata('user_id');
					
					$all_page	  = $this->db_interaction->get_page_all(1,$modules_allowed,$user_id);
			}			
			
			if(is_array($all_page) && count($all_page)>0)
			{
				foreach($all_page as $page_details)
				{
					$dataset[$page_details['id']]= array(
															'page_name' 		=> $page_details['page_name'],
															'parent_id' 		=> $page_details['page_parent_id'],
															'user_id' 			=> $page_details['user_id'],
															'hide_client' 	=> $page_details['hide_client'],
															);	
				}//END OF foreach($all_category as $category_details)
				
				foreach ($dataset as $id=>&$node)
				{
					if(isset($node['parent_id'])) //IF PARENT IS DISABLED THAN DISABLE THE ENTIRE ARRAY OF CHILD   //REMOVED CONDTIOTN OF NEGLECT ID
					{	
						if ($node['parent_id'] == 0)
						{
							$tree[$id] = &$node;
						}
						else
						{
							if (!isset($dataset[$node['parent_id']]['children'])) 
								$dataset[$node['parent_id']]['children'] = array(); //IGNORE ALL CHILD IF NO PARENT ACTIVE
							//if($neglect_id != $id && $neglect_id != $node['parent_id'])
						
								$dataset[$node['parent_id']]['children'][$id] = &$node;
						}
					}
				}
			}//END OF if(is_array($all_category) && count($all_category)>0)
			
						
			$this->display_tree($tree,'',$selected_value);
		
			return $this->option_list;
		}
		
		
		//----------------------------------------------------------------------------------------------------
		/*
		| Recursive array created by the  map tree function would be used here to make in the select box format
		| selected value is also passed 
		*/
		
		
		function display_tree($nodes,$indent=0,$selected="") 
		{
			
			$indent_style='';
			foreach ($nodes as $key=>$node) 
			{
						
				$selected_option	= "";
				// this is to preselect an item if its the same ID
				if($key == $selected)
				{
					$selected_option = 'selected="selected"';
				}	
				 if($this->session->userdata("email_id") == "admin@exateam.com")
				 {
							$indent_style = str_repeat('->',$indent*1);
							$this->option_list .= "<option value=\"" . ($key) . "\" ".$selected_option." >".$indent_style.($node['page_name'])."</option>\n\n";
				 }
				 else
				 {
					 if($this->session->userdata('page_action') == 'Edit')
					 {
							$indent_style = str_repeat('->',$indent*1);
							$this->option_list .= "<option value=\"" . ($key) . "\" ".$selected_option." >".$indent_style.($node['page_name'])."</option>\n\n";
					 }
					 else
						{
								if($key!=10)
								{
									$indent_style = str_repeat('->',$indent*1);
									$this->option_list .= "<option value=\"" . ($key) . "\" ".$selected_option." >".$indent_style.($node['page_name'])."</option>\n\n";
								}
						}						
						
	        }			
 				if (isset($node['children']))
					$this->display_tree($node['children'],$indent+1,$selected);
			}//END OF foreach ($nodes as $key=>$node) 
		}
	
	/**
	 * List Data Recursively
	 *
	 * Lets you create a select box option element using a recursive function
	 *
	 * @access private
	 * @param integer
	 * @param string
	 * @param string
	 */	
	function _list_data_recursively(&$id=0,&$data_str,$indent,$selected,$current_id)
	{
		$CI =& get_instance();//print "<pre>"; print_r ($CI->session->userdata); 
		$temp_data 		= array();
		$result_array 	= array();
		
		
		if(!empty($current_id) && $current_id != NULL)
			$where=array("status"=>1,"page_parent_id"=>$id, "id !="=>$current_id);
		else
			$where=array("status"=>1,"page_parent_id"=>$id);
			
		$order_by="page_name";
		$temp_data = $this->db_interaction->get_all_records_object("id,page_name,page_parent_id",$where,$order_by);
       
		if($temp_data->num_rows > 0 )
		{
			
			$result_array = $temp_data->result_array();
			$cnt = count($result_array);
			if($cnt > 0)
			{
				for($i=0 ; $i < $cnt ; $i++)
				{
					$selected_option	= "";
					$base_id 			= $result_array[$i]['id'];
					$parent_id 			= $result_array[$i]['page_parent_id'];
					// this is to preselect an item if its the same ID
					if($base_id == $selected)
					{
						$selected_option = 'selected="selected"';
					}
					if(strtolower($result_array[$i]['page_name']) == "base" && $this->session->userdata('user') <> "admin@exateam.com" && $this->session->userdata('page_action') == "Add"){
						$pass = "";
					}else{
						$pass = "->&nbsp;";
						$data_str .= "<option value=\"" . ($base_id) . "\" ".$selected_option." >$indent" . ($result_array[$i]['page_name'])."</option>\n\n";
					//Do Nothing.........
					}
					
					if ($base_id != $parent_id) 
						$this->_list_data_recursively($base_id,$data_str,$indent.$pass,$selected,$current_id);
				}
			}
			
		}	
		
	}
	
	
	
	/**
	 * _get_featured_pages
	 *
	 * Lets you create a select box using a recursive function
	 *
	 * @access private
	 * @param string
	 * @param string
	 * @return string
	 */	
	function _get_featured_pages()
	{
		  if($this->session->userdata('user') == 'admin@exateam.com')
			{
					$query 	= "SELECT * FROM ".TBL_PAGE." WHERE  status = 1 ";
			}
			else
			{
					$allowed_modules_session   = isset($this->session->userdata['modules_allowed']['pid_frontend'])?$this->session->userdata['modules_allowed']['pid_frontend']:'';
			 if($allowed_modules_session!='')
					 $modules_allowed        = implode(',',array_keys($allowed_modules_session));
			 else
					 $modules_allowed        = 0;
					 
					$user_id		= $this->session->userdata('user_id');
					
					$query 	= "SELECT 
														id,page_name,page_parent_id,user_id,hide_client 
											FROM 
														".TBL_PAGE." 
											WHERE  
															status = 1 
														AND 
															(id IN (".$modules_allowed.") OR user_id = ".$user_id.")
														";
			}			
			$result = $this->db->query($query);
			if($result->num_rows > 0)
			{
			  foreach($result->result_array() as $root)
				{
					$fetured_pages[] = $root;				
				}	
			}
			return $fetured_pages;
	}
	
	/**
	 * Buld Tree
	 *
	 * Lets you create a select box using a recursive function
	 *
	 * @access private
	 * @param string
	 * @param string
	 * @return string
	 */	
	function _build_tree_featured($selected=NULL,$current_id=NULL)
	{
		$data_str 	= "";
		$id			= 0;
		$indent 	= "";
		
		//Define first element
		$data_str .= "<option value=\"0\">Base</option>\n\n";
		$this->_list_data_recursively_featured(&$id,&$data_str,$indent,$selected,$current_id);			  
		return $data_str ;
	}
	
	
	/**
	 * List Data Recursively
	 *
	 * Lets you create a select box option element using a recursive function
	 *
	 * @access private
	 * @param integer
	 * @param string
	 * @param string
	 */	
	function _list_data_recursively_featured(&$id=0,&$data_str,$indent,$selected,$current_id)
	{
		$temp_data 		= array();
		$result_array 	= array();
		
		
		if(!empty($current_id) && $current_id != NULL)
			$where=array("status"=>1,"page_parent_id"=>$id, "id !="=>$current_id, "featured_image_name !=" =>"");
		else
			$where=array("status"=>1,"page_parent_id"=>$id, "featured_image_name !=" =>"");
		
		$order_by="page_name";
		$temp_data = $this->db_interaction->get_all_records_object("id,page_name,page_parent_id",$where,$order_by);
      
		if($temp_data->num_rows > 0 )
		{
			$result_array = $temp_data->result_array();
			$cnt = count($result_array);
			if($cnt > 0)
			{
				for($i=0 ; $i < $cnt ; $i++)
				{
					$selected_option	= "";
					$base_id 			= $result_array[$i]['id'];
					$parent_id 			= $result_array[$i]['page_parent_id'];
					
					// this is to preselect an item if its the same ID
					if($base_id == $selected)
					{
						$selected_option = 'selected="selected"';
					}	
					 $data_str .= "<option value=\"" . ($base_id) . "\" ".$selected_option." >$indent" . ($result_array[$i]['page_name'])."</option>\n\n";
					if ($base_id != $parent_id) 
						$this->_list_data_recursively_featured($base_id,$data_str,$indent."->&nbsp;",$selected,$current_id);
				}
			}
			
		}	
		
	}
	
	
	
	
	/**
	 * Get All pages
	 *
	 * Lets you create an array of all pages 
	 *
	 * @access private
	 * @retuen array
	 */	
	function _get_all_pages()
	{	
		$filter_all_menu_item = array();
		
		// get all pages
		$all_pages_array = $this->db_interaction->get_all_records();
		$cnt_items = count($all_pages_array);
		$filter_all_menu_item[0] = ""; 
		if($cnt_items > 0)
		{
			// If the parent is 0 then the menu is base
			$filter_all_menu_item[0] = "Home"; 
			
			foreach($all_pages_array as $item_array)
			{
				$filter_all_menu_item[$item_array['id']] = $item_array['page_name'];
			}
		}
	
		return $filter_all_menu_item;
	}
	
	/**
	 * Token Check
	 *
	 * Lets you validate the duplicate tokesn and post back issue.
	 *
	 * @access	private
	 * @param	string
	 * @return	boolean/string	
	 */
	function _token_check($str)
	{
		$session_token = $this->session->userdata("token");
		$this->session->unset_userdata("token");
		
		if (empty($session_token) || $session_token != $str ) 
		{
			return '<li>The form cannot be posted twice, please re-enter new data</li>';		
		} 
		else
		{				
			return FALSE;
		}		
	}
	
	
	/*
	*Upload file check
	*/
	
	function _upload_file_check($image)
	{
		$this->load->library('File_Uploader');
		$file_name 		= md5('1');
		$params 		= array("file_name" => $file_name,"field_name" => $image);
		
		$this->file_uploader->initialize($params);
		$path 			= ROOTBASEPATH . "media/images/page_management/normal";
		$config = array(
							"upload_path" => $path,
							"allowed_types" => AllowedTypes,
							"max_size" 		=> MAXSIZE,
							"max_width" 	=> MAXWIDTH,
							"max_height"	=> MAXHEIGHT,
							"overwrite"	=> TRUE						
							);
			
		$this->file_uploader->upload_file($config);				
		
		if(isset($this->file_uploader->errors['do_upload'][0]) || $this->file_uploader->upload_status == 0)
		{ 
			 $this->errors = '<li>'.$this->file_uploader->errors['do_upload'][0].'</li>';				
			 return 0;
		}
		else 
			return 1;
				   
	}
	
	function _upload_file($id,$type)
	{
	  $this->load->library('File_Uploader');
		$name 		= "";
		$name1 		= array();
		$file_name 	= "";
		$path 		= "";
		if(isset($_FILES['page_featured_image']['name']) && $_FILES['page_featured_image']['name'] != '' && !empty($id))
		{	
			$id 	= (int)$id;
			if($type == "update" && $id > 0)
			{
				$names = $this->_get_user_details($id,"*");
				$image_path_large   = ROOTBASEPATH . "media/images/page_management/large/";
				$image_path_medium 	= ROOTBASEPATH . "media/images/page_management/medium/";	
				$image_path_thumb 	= ROOTBASEPATH . "media/images/page_management/thumb/";
				$image_path_normal 	= ROOTBASEPATH . "media/images/page_management/normal/";
				if(!empty($names['featured_image_name']))
				{
					 $image_large_path1 = $image_path_large.$names['featured_image_name']; 
					 if(is_file($image_large_path1))
					 {
					 	@unlink($image_large_path1);
					 }
					 $image_medium_path1 = $image_path_medium.$names['featured_image_name']; 
					 if(is_file($image_medium_path1))
					 {
					 	@unlink($image_medium_path1);
					 }
					 
					 $image_thumb_path1 = $image_path_thumb.$names['featured_image_name']; 
					 if(is_file($image_thumb_path1))
					 {
						@unlink($image_thumb_path1);			 
					 }
					 
					  $image_normal_path1 = $image_path_normal.$names['featured_image_name']; 
					 if(is_file($image_normal_path1))
					 {
						@unlink($image_normal_path1);			 
					 }
				}	
			}
			$file_name 		= md5($id) ."_1";			
			$params = array("file_name" => $file_name,"field_name" => "page_featured_image");		
			$this->file_uploader->initialize($params);
											
			// Large Image		
			$path = ROOTBASEPATH . "media/images/page_management/normal";
			$config = array(
							"upload_path" => $path,
							"allowed_types" => AllowedTypes,
							"max_size" 		=> MAXSIZE,
							"max_width" 	=> MAXWIDTH,
							"max_height"	=> MAXHEIGHT, 
							"overwrite"		=> TRUE						
							);
							
			$this->file_uploader->upload_file($config);				

			if($this->file_uploader->upload_status)
			{
				// first upload the file in large and
				// then resize the image to largee specifications.
				$resize_path = $this->file_uploader->data["upload_data"];								
	
				// second resize thumbnail and then move to thumbnail directory
				$thumb_path 	= ROOTBASEPATH . "media/images/page_management/thumb/"; // destination 	
				$normal_path 	= ROOTBASEPATH . "media/images/page_management/normal/"; // Source	
				$new_file_name 	= $thumb_path . $resize_path["raw_name"] . $resize_path["file_ext"];
				$old_file_name 	= $normal_path . $resize_path["raw_name"] . '_thumb' . $resize_path["file_ext"];
				
				$this->file_uploader->_resize_to_thumbnail(array("upload_path" =>  $resize_path["full_path"]),DPCS_THUMB_IMG_W,DPCS_THUMB_IMG_H);		
				$this->file_uploader->_move_file($old_file_name, $new_file_name);
				
				// second resize medium image and then move to medium directory
				$medium_path 	= ROOTBASEPATH . "media/images/page_management/medium/";	
				$normal_path 	= ROOTBASEPATH . "media/images/page_management/normal/";	
				$new_file_name 	= $medium_path . $resize_path["raw_name"] . $resize_path["file_ext"];
				$old_file_name 	= $normal_path  . $resize_path["raw_name"] . '_thumb' . $resize_path["file_ext"];
				
				$this->file_uploader->_resize_to_medium(array("upload_path" =>  $resize_path["full_path"]),DPCS_MEDIUM_IMG_W,DPCS_MEDIUM_IMG_H);		
				$this->file_uploader->_move_file($old_file_name, $new_file_name);	
				
				// second resize medium image and then move to medium directory
				$large_path 	= ROOTBASEPATH . "media/images/page_management/large/";	
				$normal_path 	= ROOTBASEPATH . "media/images/page_management/normal/";	
				$new_file_name 	= $large_path . $resize_path["raw_name"] . $resize_path["file_ext"];
				$old_file_name 	= $normal_path  . $resize_path["raw_name"] . '_thumb' . $resize_path["file_ext"];
				
				$this->file_uploader->_resize_to_large(array("upload_path" =>  $resize_path["full_path"]),DPCS_LARGE_IMG_W,DPCS_LARGE_IMG_H);		
				$this->file_uploader->_move_file($old_file_name, $new_file_name);	
								
				// Update DB with the file name
				$data = array(
				   'featured_image_name'	=> $resize_path["file_name"],
				   'featured_image_new_flag'=> 1,
				   'modified_by'			=> $this->session->userdata("user")
				);
	
				$this->db_interaction->update_user($id,$data);			
			}						
		}
		else{
			if(!empty($_POST['featured_image']))
			{
				// Update DB with the file name
					$data = array(
					   'featured_image_name'	=> $_POST['featured_image'],
					   'modified_by'			=> $this->session->userdata("user")
					);
					$this->db_interaction->update_user($id,$data);				
				
			}
		}
		
	} 
	

		
}
?>