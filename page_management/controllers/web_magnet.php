<?php
/**
 * Web Magnet - class
 * To create new pages
 * 
 * Currrent class must call the constructor of the Base Class called Controller
 */
class Web_magnet extends Controller{    
	
	var $msg 				= "";	
	var $errors 			= "";
	var $mode 				= "";
	var $page_name			= "page_management/web_magnet";
	var $parent_controller	= "web_magnet";
	var $form_name			= "form_web_magnet";
	var $page_h1			= "Web Magnet";
	
    function Web_magnet()
	{
		parent::Controller();
		//Check login
		$this->load->module('login');
		
		// load 'DB_Interaction' model
		$this->load->model('DB_Interaction');
		
		// load library
		$this->load->library('Table');
		
		// load Helpers
		$this->load->helper(array('date','text'));
		
		$l = new Login();
		if($l->_is_logged_in())
		{
		} 
		else 
		{
			redirect('login');
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
		$this->show_webmagnet();		
		
	}

	/**
	 * Show Web Magnet List
	 *
	 * Lets you list the records in the database.
	 *
	 * @access	public
	 */	
	function show_webmagnet()
	{
/////////Initalize the variable
		$meta_keywords		= "";
		$meta_description	= "";
		$h1					= "";
		$title_tag			= "";
//////////////////////////
		$result_data	= $this->db_interaction->get_all_records_webmagnet(); //Find out the data
		
        $datatype		= (count($result_data) > 0)? "update":"insert"; //Specity the type
		if($datatype == "update"){
			$meta_keywords		= $result_data[0]["meta_keywords"];
			$meta_description	= $result_data[0]["meta_description"];
			$h1					= $result_data[0]["h1"];
			$title_tag			= $result_data[0]["title_tag"];
		}

		$data = array(
						"pagetitle" 		=> "List Of Web Magnet For SEO",
						"submit_name"		=> "webmagnetupdate",
						"submit_value"		=> "Web Magnet Update",
						"meta_keywords"		=> $meta_keywords,
						"meta_description"	=> $meta_description,
						"h1"				=> $h1,
						"title_tag"			=> $title_tag,
						"datatype"			=> $datatype,
						"msg"	 			=> $this->msg,	
						"errors" 			=> $this->errors);
		$this->_display('webmagnet_form',$data);
	}
	/**
	 * Update
	 *
	 * Lets you update an existing record in the database
	 * It first validates the form and depending on the errors occurred or not, necessary actions are taken.
	 *
	 * @access	private
	 */
	function webmagnetupdate()
	{
		if (count($_POST) == 0)
			redirect($this->page_name);

		if($this->input->post("mode") == "webmagnetupdate"){
			if($this->input->post("datatype") == "insert"){ //Insert The Data
				$data = array(
						'meta_keywords'		=> $this->input->post("meta_keywords"),
						'meta_description' 	=> $this->input->post("meta_description"),
						'h1'				=> $this->input->post("h1"),
						'title_tag'			=> $this->input->post("title_tag"),
						'created'			=> date("Y-m-d H:i:s"),
						'created_by'		=> $this->session->userdata("user")
						);
				$this->db_interaction->add_webmagnet($data);
				$this->msg = "Web Magnet Data is added successfully.";
			}else if($this->input->post("datatype") == "update"){ //Update the Record
//Fetching Old Data.....................
				$result_data		= $this->db_interaction->get_all_records_webmagnet(); //Find out the data
				$meta_keywords		= $result_data[0]["meta_keywords"];
				$meta_description	= $result_data[0]["meta_description"];
				$h1					= $result_data[0]["h1"];
				$title_tag			= $result_data[0]["title_tag"];
/////////////////////////////////////////////

				$data = array(
						'meta_keywords'		=> $this->input->post("meta_keywords"),
						'meta_description' 	=> $this->input->post("meta_description"),
						'h1'				=> $this->input->post("h1"),
						'title_tag'			=> $this->input->post("title_tag"),
						'modified'			=> date("Y-m-d H:i:s"),
						'modified_by'		=> $this->session->userdata("user")
						);
				$this->db_interaction->update_webmagnet($data);
				//update the meta keyword for all pages.
				if($meta_keywords != $this->input->post("meta_keywords")){
					$data 	= array('page_meta_keywords'	=> $this->input->post("meta_keywords"));
					$where 	= array ('page_meta_keywords'	=> $meta_keywords);
					$this->db_interaction->update_seodata($where,$data);
				}
				//update the meta description for all pages.
				if($meta_description != $this->input->post("meta_description")){
					$data 	= array('page_meta_description'		=> $this->input->post("meta_description"));
					$where 	= array ('page_meta_description'	=> $meta_description);
					$this->db_interaction->update_seodata($where,$data);
				}
				//update the h1 tag for all pages.
				if($h1 != $this->input->post("h1")){
					$data 	= array('page_h1'	=> $this->input->post("h1"));
					$where 	= array ('page_h1'	=> $h1);
					$this->db_interaction->update_seodata($where,$data);
				}
				//update the Title Tag tag for all pages.
				if($title_tag != $this->input->post("title_tag")){
					$data 	= array('page_title_tag'	=> $this->input->post("title_tag"));
					$where 	= array ('page_title_tag'	=> $title_tag);
					$this->db_interaction->update_seodata($where,$data);
				}
				$this->msg = "Web Magnet Data is updated successfully.";
			}
		}
		$this->show_webmagnet();	
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
		// load view and add additional data 
		$data["page_heading"] = $this->page_h1;
		$this->load->view('adminheader',$data);
		$this->load->view('top_messages',$data);
        $this->load->view($viewname,$data);
        $this->load->view('adminfooter',$data);
	}
	
}
?>