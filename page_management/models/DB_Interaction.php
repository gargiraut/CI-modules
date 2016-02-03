<?php
class DB_Interaction extends CI_Model
{
	var $table_name 	= TBL_PAGE;
	
	function __construct()
	{
		// call the Model constructor
		parent::__construct();

	}
	//-----------------------------------------------------------------------------------------------------------
	/*
	| Function to get the New Rank 
	*/
	function get_new_rank_insert($parent_id)
	{
		$SQL_COUNT = "SELECT 
								COUNT(id) AS DPCS_ID 
					    FROM 
								".TBL_PAGE." 
					   WHERE 
								page_parent_id = ".$parent_id."";
		$page_count = $this->run_query($SQL_COUNT);
		return $page_count;		
								
	}
	
	
	function rerank_data($id,$catgeory_id)
	{
			$SQL_RERANK = "SELECT 			
									*
							FROM 
									".TBL_PAGE." 
							WHERE 
									`id` != ".$id." 
								AND 
									page_parent_id = ".$catgeory_id."
							ORDER BY 
									`rank`, `modified` DESC";
			$rank_count  = $this->run_query($SQL_RERANK);
			return 	$rank_count;														
	}
	
	
	//-----------------------------------------------------------------------------------------------------------
	/*
	| Function to Change Ranks
	*/
	function get_rank_dpcs()
	{
		$SQL_COUNT = "SELECT 
								COUNT(id) AS PAGE_COUNT,page_parent_id 
					   FROM 
								".TBL_PAGE."  
					GROUP BY 
								page_parent_id";
		$page_count = $this->run_query($SQL_COUNT);
		return $page_count;								
	}
	//------------------------------------------------------------------------------------------------------------
	/*
	| Get the Version Details for Page EDIT
	*/
	function get_version_details($dpcs_id)
	{
		$SQL_VERSION_DETAILS = "SELECT 
																			id,page_head,page_html_data,created
														 FROM 
																			".TBL_PAGE_HISTORY."	
														WHERE 
																			current_copy_flag =0
																	AND
																			page_id =  ".$dpcs_id."
														";
		$version_details	= $this->run_query($SQL_VERSION_DETAILS);	
		return $version_details;
	}
	
	//--------------------------------------------------------------------------------------------------------
	/*
	| Get Page Management Log Data 
	*/
	function get_data_logpages($log_version_id)
	{
		$SQL_GET_LOG = "SELECT 
														page_name,page_head,page_html_data,page_meta_keywords,
														page_meta_description,page_h1,page_title_tag,seo_content,created
										FROM 
														".TBL_PAGE_HISTORY." 
										WHERE 
														id = ".$log_version_id."
									";
		$log_records = $this->run_query($SQL_GET_LOG);							
		return $log_records;							
	}
	
	//-----------------------------------------------------------------------------------------------
	
	function log_sub_pages()
	{
		$SQL_LOG_SUBPAGES = "SELECT 
																	PM_LOG.id,PM_LOG.no_of_subpages,PM_LOG.created,PM_LOG.created_by,USERS.email,PM_LOG.ipaddress
													FROM
																	".TBL_PAGE_SUBPAGE_LOG." PM_LOG	
													INNER JOIN
																	".TBL_USERS." USERS ON (PM_LOG.user_id = USERS.id)	
													ORDER BY 
																		PM_LOG.created	";
		$log_data		= $this->run_query($SQL_LOG_SUBPAGES);
		return	$log_data;	
	}
	
	//------------------------------------------------------------------------------------------
	/*
	| Function ot get the History Page details
	*/
	function get_history_data($page_id)
	{
	    $SQL_HISTORY = "SELECT 
																* 
												FROM 
																".TBL_PAGE_HISTORY." 
											WHERE 
																id = ".$page_id."
											";
			$history_data = $this->run_query($SQL_HISTORY);
			return $history_data;			
	}
	
	//-----------------------------------------------------------------------------------------
	/*
	| Function to get the Page count
	*/
	function get_dpcs_inactive_count($where)
	{
		$SQL_INACTIVE = "SELECT 
															COUNT(id) AS INACTIVE_PAGE
											FROM 
															".TBL_PAGE."
											WHERE 
															".$where."
										";
		$inactive_count  = $this->run_query($SQL_INACTIVE);
		return 	$inactive_count;	
	}
	
	//----------------------------------------------------------------------------------------
	
	function get_count_catgeories()
	{
		$SQL_COUNT_CATGEORIES = "SELECT 
																				COUNT(id) AS TOTAL_ROWS,
																				category_id 
														FROM 
																				products 
														GROUP BY 
																				category_id";
		$catgeories_count  = $this->run_query($SQL_COUNT_CATGEORIES);
		return 	$inactive_count;	
	}
	
	//--------------------------------------------------------------------------------------------
	/*
	| Function to get the List of user from the Page User Map tablwe 
	|
	*/
	function get_user_details()
	{
			$SQL_USER = "SELECT 
															PMSUB.no_of_subpages AS PAGES,
															USERS.username  AS USERNAME,
															USERS.id	AS USERID
										FROM
															users USERS
											INNER JOIN 
															page_management_subpages PMSUB ON (USERS.id = PMSUB.user_id)
									";
			$user_details = $this->run_query($SQL_USER);
			return $user_details;			
	}
	
	//------------------------------------------------------------------------------------
	/*
	| Function to get the Dpcs details for the slidder effect of the page 
	*/
		/* function get_dpcs_details($page_parent_id,$hide_client = 0)
		{
			$SQL_DPCS = "SELECT 
									id,page_name,page_head,page_parent_id,
									created,rank,hide_client,display_footer,status
						  FROM 
									".TBL_PAGE."
						  WHERE 
									status <> 2
								AND 
									page_parent_id = ".$page_parent_id."
									";
			if($hide_client == 1)
			{
				$SQL_DPCS .= " AND hide_client <> 1";	
			}
			$SQL_DPCS .= " ORDER BY page_parent_id";	
			
			$page_details = $this->run_query($SQL_DPCS);
			return $page_details;
		} */
	//------------------------------------------------------------------------------------
	/*
	| Function to get the Dpcs details for the slidder effect of the page 
	*/
		function get_dpcs_details($hide_client = 0,$allowed_modules_session='',$user_session='')
		{
			$SQL_DPCS = "SELECT
													PM.id,PM.page_name,PM.page_head,PM.page_parent_id,
													PM.created,PM.rank,PM.hide_client,PM.display_footer,PM.status,
													PM.menu_frontend,IFNULL( PMLOG.page_id, 0 ) AS history_id
										FROM
													".TBL_PAGE." PM
										LEFT JOIN 
													".TBL_PAGE_HISTORY." PMLOG ON ( PM.id = PMLOG.page_id )
										WHERE
													PM.status <> 2
									";
			if($hide_client == 1)
			{
				$SQL_DPCS 				.= " AND (PM.id IN (".$allowed_modules_session.") OR PM.user_id =".$user_session.")";
			}
			$SQL_DPCS .= " GROUP BY PM.id
                            ORDER BY page_parent_id";	
		
			
			$page_details = $this->run_query($SQL_DPCS);
			return $page_details;
		}	
		
		
		//------------------------------------------------------------------------------------
	/*
	| Function to get the Dpcs details for the slidder effect of the page 
	*/
		function get_dpcs_details_search($hide_client = 0,$like_keyword,$allowed_modules_session='',$user_session='')
		{
			$SQL_DPCS = "SELECT 
														id,page_name,page_head,page_parent_id,
														created,rank,hide_client,display_footer,status,menu_frontend
										FROM 
														".TBL_PAGE."
										WHERE 
															status <> 2
								
									";
			if($hide_client == 1)
			{
				$SQL_DPCS 				.= " AND (id IN (".$allowed_modules_session.") OR user_id =".$user_session.") ";	
			}
			
				$SQL_DPCS 				.= " AND 
																	 (page_name LIKE '".$like_keyword."%' 
																OR 
																	 page_meta_keywords LIKE '".$like_keyword."%'
																OR
																	 page_h1 LIKE '".$like_keyword."%'
																OR
																	 page_title_tag LIKE '".$like_keyword."%')
																	 ";	
			
			$SQL_DPCS .= " ORDER BY page_parent_id";	
		
			
			$page_details = $this->run_query($SQL_DPCS);
			return $page_details;
		}
		
		
		//------------------------------------------------------------------------------------
	/*
	| Function to get the Dpcs details for the slidder effect of the page 
	*/
		function get_dpcs_details_searchcount($hide_client = 0,$like_keyword,$allowed_modules_session='',$user_session='')
		{
			$SQL_DPCS = "SELECT 
														COUNT(id) AS PAGE_COUNT
									FROM 
													".TBL_PAGE."
									WHERE 
													status <> 2";
			if($hide_client == 1)
			{
				//$SQL_DPCS .= " AND hide_client <> 1";	
					$SQL_DPCS 	.= " AND ( id IN (".$allowed_modules_session.") OR user_id =".$user_session." )";	
			}
			
			$SQL_DPCS 				.= " AND 
																	 (page_name LIKE '".$like_keyword."%' 
																OR 
																	 page_meta_keywords LIKE '".$like_keyword."%'
																OR
																	 page_h1 LIKE '".$like_keyword."%'
																OR
																	 page_title_tag LIKE '".$like_keyword."%')
																	 ";	
			$SQL_DPCS .= " ORDER BY page_parent_id";	
			
			$page_details = $this->run_query($SQL_DPCS);
			return $page_details;
		}	
	//------------------------------------------------------------------------------------
	/*
	| Function to get the Dpcs details for the slidder effect of the page 
	*/
		function get_dpcs_details_list($offset, $per_page,$hide_client = 0)
		{
			$SQL_DPCS = "SELECT 
									id,page_name,page_head,page_parent_id,
									created,rank,hide_client,display_footer,status
						  FROM 
									".TBL_PAGE."
						  WHERE 
									status <> 2";
			if($hide_client == 1)
			{
				$SQL_DPCS .= " AND hide_client <> 1";	
			}
			$SQL_DPCS .= " ORDER BY page_parent_id
							LIMIT
					 			 {$offset}, {$per_page}
							";	
			
			$page_details = $this->run_query($SQL_DPCS);
			return $page_details;
		}	
	//------------------------------------------------------------------------------------
	/*
	| Function to get the Dpcs details for the slidder effect of the page 
	*/
		function get_dpcs_details_count($hide_client = 0,$allowed_modules_session='',$user_session='')
		{
			$SQL_DPCS = "SELECT 
														COUNT(id) AS PAGE_COUNT
									FROM 
													".TBL_PAGE."
									WHERE 
													status <> 2";
			if($hide_client == 1)
			{
				//$SQL_DPCS .= " AND hide_client <> 1";	
					$SQL_DPCS 	.= " AND ( id IN (".$allowed_modules_session.") OR user_id =".$user_session." )";	
			}
			$SQL_DPCS .= " ORDER BY page_parent_id";	
			
			$page_details = $this->run_query($SQL_DPCS);
			return $page_details;
		}	
	//-------------------------------------------------------------------------------------
	/*
	| Function to get the page name for the seo 301 redirect
	*/
	function get_page_name($page_id)
	{
		$SQL_PAGE_NAME = "SELECT 
									id,page_name,status
							 FROM 
									".TBL_PAGE."
							 WHERE 
									id =".$page_id."
							";
		$page_name_seo     = $this->run_query($SQL_PAGE_NAME);
		return $page_name_seo;	
	}
	//------------------------------------------------------------------------------------------
	/*
	|  Function to get all variants from the database 
	*/
	
	function get_variants_frontend()
	{
		$SQL_FRONTEND_VARIANTS = "SELECT	 
											PVM.id,PVM.product_id AS product_code,
											PVM.variant_name AS PARENT_VARIANT_ID,
											PVM.variant_value AS CHILD_VARIANT_ID,PVM.variant_sign, 
											PVM.amount,PV.variant_name,PV.variant_value
									FROM
											".TBL_PRODUCT_MAP." PVM
							  INNER JOIN 
											".TBL_PURCHASE_VARIANTS." PV ON 
										(PVM.variant_name = PV.variant_id  AND PVM.variant_value = PV.id)
									WHERE
												PVM.status = 1
											AND 
												PV.status = 1";
		$variants_map     = $this->run_query($SQL_FRONTEND_VARIANTS);
		return $variants_map;										
	}
	
	
	
	//----------------------------------------------------------------------
	function get_page_all($query_indicator,$modules_allowed='',$user_id='')
	{
		if($query_indicator == 0)
		{
			$SQL_PAGE_ALL      = "SELECT 
																			id,page_name,page_parent_id,user_id,hide_client
														FROM 
																			".TBL_PAGE." 
														WHERE 	
																			status <> 2
														ORDER BY 
																				page_parent_id";
		}
		else
		{
			//CLIENT LOGIN 
			$SQL_PAGE_ALL      = "SELECT 
																			id,page_name,page_parent_id,user_id,hide_client
														FROM 
																			".TBL_PAGE." 
														WHERE 	
																			status <> 2 AND (id IN (".$modules_allowed.") OR user_id = ".$user_id.")
														ORDER BY 
																				page_parent_id";
		}		
		$all_pages = $this->run_query($SQL_PAGE_ALL);								
		return $all_pages;
	}
	
	//----------------------------------------------------------------------
	function page_name_check($page_name)
	{
		$SQL_PAGE_NAME = "SELECT 
									COUNT(id) AS PAGE_NAME
							 FROM 
									".TBL_PAGE."
							 WHERE 
									page_name =".$this->db->escape($page_name)."
							";
		$page_name_count     = $this->run_query($SQL_PAGE_NAME);
		return $page_name_count;					
	}
		
	//-----------------------------------------------------------------------------
	function update_page_name_check($page_name,$page_id)
	{
	   $SQL_UPDATE_PAGE_NAME = "SELECT
											COUNT(id) AS UPDATE_PAGE_NAME
									FROM
											".TBL_PAGE."
									WHERE
											  id <> ".$page_id."
										 AND 
											  page_name = ".$this->db->escape($page_name)."
								  ";
		$update_page_name_count     = $this->run_query($SQL_UPDATE_PAGE_NAME);
		return $update_page_name_count;											  
	}
	
	//------------------------------------------------------------------------------------------
	
	
	function run_query($query)
	{
		$result_set = $this->db->query($query);		
		if($result_set->num_rows > 0)		
			return $result_set->result_array();		
	}
	
	
	function update_banner($id,$data,$tablename)
	{
		$this->db->where('map_id', $id);
		$this->db->update($tablename, $data);
 
	}
	
	/**
	 * Get All records
	 *
	 * Lets you fetch all the records.
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */	
	function get_all_records($order_by=NULL)
	{
		if ($order_by != NULL)
			$this->db->order_by($order_by);
			
		$query=$this->db->get($this->table_name);
		if($query->num_rows()>0)
		{
			// return result set as an associative array
			return $query->result_array();
		}
	}
	/**
	 * Get All records For Web Mangnet SEO
	 *
	 * Lets you fetch all the records.
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */	
	function get_all_records_webmagnet()
	{
		$query=$this->db->get(TBL_WEBMAGNET);
		if($query->num_rows()>0)
		{
			// return result set as an associative array
			return $query->result_array();
		}
	}
	
	/**
	 * Get All records Object
	 *
	 * Lets you fetch all the records.
	 *
	 * @access	public
	 * @return	object - the whole object query is returned
	 */	
	function get_all_records_object($select="*",$where=array(),$order_by=NULL,$offset=NULL,$limit=NULL)
	{
		$this->db->select($select);
		
		//add where clause processing here if required
        $this->db->where($where);		
            
        //$this->db->having($having);
        
        if ($offset != NULL)
            $this->db->offset($offset);
			
        if ($limit != NULL)
            $this->db->limit($limit);
			
        if ($order_by != NULL)
			$this->db->order_by($order_by);
			
		$query=$this->db->get($this->table_name);
		// return result set as an associative array
		return $query;		
	}
	
	/**
	 * Get records Where
	 * 
	 * Lets you fetch all the records depending on the where condition.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */	
	function get_records_where($field,$param)
	{
		$this->db->where($field,$param);
		$query=$this->db->get($this->table_name);
		
		// return result set as an associative array
		return $query->result_array();
	}
	/**
	 * Get Limit records Where
	 * 
	 * Lets you fetch records using the LIMIT clause
	 * This is mainly used for pagination
	 *
	 * @access	public
	 * @param	string
	 * @param	integer
	 * @param	array
	 * @return	array
	 */		
	function get_limit_records_where($row,$limit=0,$where)
	{
		$query=$this->db->get_where($this->table_name,$where,$limit,$row);
		if($query->num_rows()>0)
		{
			// return result set as an associative array
			return $query->result_array();
		}
	}
	
	/**
	 * Get Num records
	 * 
	 * Lets you fetch total number of records
	 *
	 * @access	public
	 * @return	integer
	 */
	function get_num_records()
	{
		return $this->db->count_all($this->table_name);
	}
	
	/**
	 * Get Num records Where
	 * 
	 * Lets you fetch total number of records depending on the where condition
	 *
	 * @access	public
	 * @param	array
	 * @return	integer
	 */
	function get_num_records_where($params)
	{
		$this->db->where($params);
		$query=$this->db->get($this->table_name);
		// return result set as an associative array
		return count($query->result_array());
	}
	
	/**
	 * Add Web Magnet
	 * 
	 * Lets you add web magent to the table
	 *
	 * @access	public
	 * @param	array	
	 */
	function add_webmagnet($data=array())
	{
		if(count($data) >  0){
			$this->db->insert(TBL_WEBMAGNET, $data);
		}
	}
	/**
	 * Add User
	 * 
	 * Lets you add user to the table
	 *
	 * @access	public
	 * @param	array	
	 */	
	function add_user($data=array(),$table_name_insert = TBL_PAGE)
	{
		$this->db->insert($table_name_insert, $data); 
	}
	
	/**
	 * Update User
	 * 
	 * Lets you update user depending on the id specified
	 *
	 * @access	public
	 * @param	integer
	 * @param	array	
	 */	
	function update_user($id,$data)
	{
		$this->db->where('id', $id);
		$this->db->update($this->table_name, $data);
 
	}
	/**
	 * Update seo data
	 * 
	 * Lets you update seo data depending on the condition specified
	 *
	 * @access	public
	 * @param	integer
	 * @param	array
	 */	
	function update_seodata($where,$data)
	{
		$this->db->where($where);
		$this->db->update($this->table_name, $data);
 
	}
	/**
	 * Update Web Magnet
	 * 
	 * Lets you update web magent data
	 *
	 * @access	public
	 * @param	array	
	 */	
	function update_webmagnet($data)
	{
		$this->db->update(TBL_WEBMAGNET, $data); // update the seo_webmagenet table
		
		
 	}
	
	/**
	 * Delete User
	 * 
	 * Lets you delete user depending on the id specified
	 *
	 * @access	public
	 * @param	integer
	 */	
	function delete_user($id)
	{
		$this->db->where('id', $id);
		$this->db->delete($this->table_name);
 
	}
		
	/**
	 * Get Search User
	 * getSearchrecords
	 * Lets you delete user depending on the id specified
	 *
	 * @access	public
	 * @param	array
	 * @param	array
	 * @return	array
	 */	
	function get_search_records($params=array(),$where=array())
	{
		$this->db->where($where);
		$this->db->like($params); 
		$query=$this->db->get($this->table_name);
		if($query->num_rows()>0)
		{
			// return result set as an associative array
			return $query->result_array();
		}
	}
	
	/**
	 * Get Records Use Query
	 * 
	 * Lets you build you own query and then run
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	array
	 */	
	function get_records_use_query($where="",$like=array(),$offset=NULL,$limit=NULL,$order_by=NULL)
	{
		$query = "";
		
		$query .= " SELECT * FROM (`".$this->table_name."`) ";
		if(!empty($where))
		{
			$query .= " WHERE $where ";	
			
			$cnt = array($like);
			if (!empty($like) && is_array($like) && $cnt > 0)
			{
				$query .= " AND ( ";					
				foreach($like as $field=>$value)			
				{
					$temp_arr[] = " `$field` LIKE '%".$value."%' ";
				}		
				$query .= implode(" OR ",$temp_arr);;			
				$query .= " ) ";		
			}			
			
		}
		
	 	if ($order_by != NULL)
		{
			$query .= " ORDER BY $order_by ";
		}		
			
        if ($limit != NULL)
		{
            $query .= " LIMIT $limit ";
			
			if ($offset != NULL)
				$query .= " , $offset ";	
		}	
				
		$query=$this->db->query($query);
		if($query->num_rows()>0)
		{
			// return result set as an associative array
			return $query->result_array();
		}
	}
	
	function get_records($offset, $limit,$where=array(),$like=array(),$or_like=array(),$order_by='')
    {
        //add where clause processing here if required
        $this->db->where($where);		
		
        $this->db->select('*');    
        //$this->db->having($having);
        
        if ($offset != NULL)
            $this->db->offset($offset);
        if ($limit != NULL)
            $this->db->limit($limit);
			
        if ($order_by != NULL)
			$this->db->order_by($order_by);
		
		if (!empty($like) && is_array($like))
			$this->db->like($like[0],$like[1]);
		
		if (!empty($or_like) && is_array($or_like))
		{
			$cnt = count($or_like);
			for($i=0 ; $i < $cnt ; $i++)			
				$this->db->or_like($or_like[$i][0],$or_like[$i][1]); 
		}
        if ($limit == NULL && $offset == NULL)
        {
            $count = $this->db->get($this->table_name);
            return $count->result_array();
        }
        else
		{
			$query = $this->db->get($this->table_name);
            return $query->result_array();
		}	
    }
	
	function get_records_like_only($offset, $limit,$where=array(),$like=array(),$order_by='')
    {
        //add where clause processing here if required
        $this->db->where($where);		
		
        $this->db->select('*');    
        //$this->db->having($having);
        
        if ($offset != NULL)
            $this->db->offset($offset);
        if ($limit != NULL)
            $this->db->limit($limit);
			
        if ($order_by != NULL)
			$this->db->order_by($order_by);
				
		if (!empty($like) && is_array($like))
		{	
			$this->db->like($like); 
		}
        if ($limit == NULL && $offset == NULL)
        {
            $count = $this->db->get($this->table_name);
            return $count->result_array();
        }
        else
		{
			$query = $this->db->get($this->table_name);
            return $query->result_array();
		}	
    }
	
	/**
	 * Get Data
	 * 
	 * Lets you fetch all the records depending on the where condition.
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	array
	 */	
	function get_records_query($offset, $limit, $where = '', $table_name = '',$or_like='',$order_by='')
    {
			$query = "SELECT * FROM
				`".$table_name."`
				WHERE ".$where."
				$or_like
				$order_by
				LIMIT $offset,  $limit			
				";
		$result_set = $this->db->query($query);
		if($result_set->num_rows > 0)
			return $result_set->result_array();
	}
	
}

?>