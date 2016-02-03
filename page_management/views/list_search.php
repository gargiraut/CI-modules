<script type="text/javascript" language="javascript">
//<![CDATA[ 
//mass delete or restore
$(document).ready(function (){
	$("#mass_action").change (function() {  
		if($("#mass_action").val() == 'mass_trash')
		{
			confirm_delete_sel('<?php echo $this->form_name; ?>','<?php echo site_url($this->page_name."/delete_selected")?>');  
		}	
	});	
	
				  
				  $("#no_subpage_submit").click(function(){
						$.post("<?php echo site_url($this->page_name."/update_subpages")?>", { num: $("#total_no").val()},
						   function(data){
							alert("Updated successfully");
							$("#total_no").val(data);

							var ac_val = eval($("#cnt_non_subpage").val());
							var db_val = eval($("#total_no").val());

							if( db_val > ac_val ){
								 $("#add_dpcspage").attr("style","display:inline");
								 var msg = "<h2 style='color:#FF0000'>You can add "+eval(db_val - ac_val)+" more sub pages.</h2>";
							}else{
								 if($("#login_user").val() != 'admin@exateam.com')
								 		$("#add_dpcspage").attr("style","display:none");
								 
								 var msg = "<h2 style='color:#FF0000'>You can add no more sub pages.</h2>";
							}
							$("#message_nopages").html(msg);
						});
				  });
					
					//--------------------------------------------------------------------------------------------------------
		/*
		| To check the COndition if the user has selected some pages than only allow them to update
		*/	
			$("#user_id").change(function(){
				
					var user_ids = $("#user_id").val();
					
					if(user_ids != 0)
					{
							$("#no_subpage_submit").attr('disabled','');
							$("#total_no").val($("#user_"+user_ids).val());
					}
					else
					{
						$("#no_subpage_submit").attr('disabled','disabled');
						$("#total_no").val(0);
					}
			});
			
			//--------------------------------------------------------------------------------------------------------
			/*
			| To check if the Number entered by user is in proper format
			*/
				$("#total_no").keypress(function (e){ 
					if( e.which!=8 && e.which!=0 && (e.which<48 || e.which>57))
					{
							$("#error_message").html("Digits Only"); 		//display error message
							return false;
					}	
				});
		
});
//]]>	
</script>
<?php
	if($this->session->userdata('user') != 'admin@exateam.com')
	{
		if($cnt_db_subpage <= $cnt_non_subpage)
		{
			$msg_subpage = "You can add no more sub pages.";//$style = "";$style = 'style="display:none"';
			$indicator 		= 1;
			$page_add_count = 0;
		}	
		else
		{
			$msg_subpage = "You can add ".($cnt_db_subpage-$cnt_non_subpage)." more sub pages.";//$style = "";
			$value = $cnt_db_subpage-$cnt_non_subpage;
			
			if($value < 3)
				$indicator = 1;
			else
				$indicator = 0;
			
			$page_add_count = $value;		
		}

	if($indicator == 1)
	{	
?>
<div class="yellow1 gen1" id="info_message">
    <div class="yellow2 gen2">
        <div class="yellow3 gen3">
            <div class="gen4 yellow">
                <h3>Information Messages!</h3>
               <ul>
				<?php 
					if($page_add_count == 0)
					{
				?>
				<li>
					You Cannot Add any New Pages . Request for upgrade to Add new pages
				</li>
				<?php	
					}
					else
					{
				?>
               <li>
					You have got only Minimum pages Left to add . Request for upgrade to Add new pages
				</li>
				<?php
					}
				?>
               </ul>
            </div>
        </div>
    </div>
</div>
<?php
	}//END OF if($indicator == 1)
}	//END OF if($this->session->userdata('user') != 'admin@exateam.com')
else
{
	$msg_subpage = '';
}
?>
<input type="hidden" name="cnt_non_subpage" id="cnt_non_subpage" value="<?php echo isset($cnt_non_subpage)?$cnt_non_subpage:'';?>" />
<input type="hidden" name="login_user" id="login_user" value="<?=$this->session->userdata('user')?>" />

<?php
		if($this->session->userdata('user') == 'admin@exateam.com')
		{ 
		?>
		<fieldset>
	<legend>ADMIN PANEL UPDATE</legend>
	<ul>
    <li>
						<span class="grey_text">Total number of sub pages allowed to be added:&nbsp;. </span>
							<select name="user_id" id="user_id" class="element select width_medium">
									<option value="0" title="select Username">Select Username </option>
									<?php 
									
												if(is_array($user_sub_page_details) && count($user_sub_page_details)>0)
												{
													foreach($user_sub_page_details as $user_details)
													{
											?>
												<option value="<?php echo $user_details['USERID']?>" title="User Name<?php echo $user_details['USERNAME']?>">
															<?php echo str_replace('_',' ',ucwords($user_details['USERNAME']));?>
												</option>
												<?php
													}
												}
									?>
							</select>
					<input type="text" name="total_no" id="total_no" value="0" class="element text" style="width:50px;"/>&nbsp;&nbsp;
					<input type="button" value="Update" id="no_subpage_submit" disabled="disabled" />
					<span style="color:#FF0000;font-family:Arial,Helvetica,sans-serif;font-size:12px;" id="error_message"></span>
		</li>
      <?php 
			
					if(is_array($user_sub_page_details) && count($user_sub_page_details)>0)
					{
						foreach($user_sub_page_details as $user_details)
						{
				?>
					<input type="hidden" name="user_<?php echo $user_details['USERID']?>" id="user_<?php echo $user_details['USERID']?>" value="<?php echo $user_details['PAGES']?>"/>
					<?php
						}
					}
			?>
    </ul>
 </fieldset>
			
												
<?php }?>
<div style="margin-top:20px" id="filter_users_response">
<div class="mytables" align="left">
	<form name="<?php echo $this->form_name; ?>_search" id="<?php echo $this->form_name; ?>_search" action="<?php echo site_url($this->page_name)?>/search" method="post">
      <input type="text" class="element text medium" name="search_filter" id="search_filter" value="" />
      &nbsp;&nbsp;<a href="javascript:void(0);" onclick="handle_submit('<?php echo $this->form_name; ?>_search','<?php echo site_url($this->page_name.'/search')?>');" title="Click here to search">Search</a>&nbsp;|&nbsp;<a href="<?php echo site_url($this->page_name)?>" title="Click here to show normal listing">Normal Listing</a><br/>
      <script type="text/javascript"> 
	  //<![CDATA[ 
jQuery(document).ready(function() {
	jQuery("#search_filter").suggest("<?php echo site_url($this->parent_controller); ?>/ajax_call/search_filter/");});
	//]]>		
</script>
    </form>
</div>

	<div id="message_nopages"><h2 style="color:#FF0000"><?=$msg_subpage?></h2></div>
<?php 
$this->load->helper('form'); 
$attributes = array('id' 	=> $this->form_name,'name' => $this->form_name);
//$hidden 	= array('mode' 	=> '', 'id'=>'','curr_rank'=>'','new_rank'=>'','r_mode'=>'');
echo form_open($this->page_name, $attributes);
?>
<?php if(!empty($users_result) && is_array($users_result)){ ?>

<div>
<?php
	if($this->session->userdata('user') != 'admin@exateam.com')
	{
		if($cnt_db_subpage > $cnt_non_subpage || $this->session->userdata('user') == 'admin@exateam.com')
			$style = "";
		else
			$style = 'style="display:none"';
	}
	else
	{
			$style = "";
	}
?>
<a href="<?php echo site_url($this->page_name.'/add')?>" class="btn green float_left" id="add_dpcspage" title="Click here to add a page" <?=$style?> >Add Page</a>
<?php  if(($this->session->userdata('user') != 'admin@exateam.com') && ($web_magnet == 1)) {?><a href="<?php echo site_url($this->page_name.'/seo')?>" class="btn green float_left" title="Click here to seo update">SEO Updation</a><? } elseif($this->session->userdata('user') == 'admin@exateam.com') { ?><a href="<?php echo site_url($this->page_name.'/seo')?>" class="btn green float_left" title="Click here to seo update">SEO Updation</a><? } ?>
<a href="javascript:void(0);" onclick="javascript:confirm_delete_sel('<?php echo $this->form_name ?>','<?php echo site_url($this->page_name."/delete_selected")?>');" class="btn green float_right" title="Click her to delete records">Delete Selected</a> <a href="<?php echo site_url($this->page_name.'/export'); ?>" title="Export data in excel format" class="float_right"><img src="<?php echo base_url(); ?>images/backend/tree/xls.png" alt="Export" title="Export" border="0" />&nbsp;Export to excel&nbsp;&nbsp;</a> </div>
<br /><br />
<?php if(!empty($pagination)){ ?>
<div class="pagination" align="left">
    <?php echo $pagination;?><!-- this is the number of records part - an example only -->
<span>Display <a href="<?php echo $pbase_url; ?>/10" title="Click here to list 10 records per page">10</a> | <a href="<?php echo $pbase_url; ?>/20" title="Click here to list 20 records per page">20</a> records per page</span>    
</div>
<br/>
<?php } ?>
<table  class="pickme sortable-onload-1 rowstyle-alt no-arrow mytables" id="striped2"> 
	<thead>
	<tr>
		<th width="20" class="center"><input type="checkbox" name="whatever" value="" onclick="CheckUncheckAll('<?php echo $this->form_name; ?>')" /></th> 
		<th width="2%" class="sortable-numeric">ID</th>
		<th align="left" width="27%" class="sortable-text">Page Parent</th>
		<th align="left" width="30%" class="sortable-text">Page Name</th>
		<th align="left" width="8%" class="sortable-date">Created</th>
		<th align="left" width="4%" class="sortable-numeric" colspan="2">Rank</th>
		<?php if($this->session->userdata('user') == 'admin@exateam.com'){ ?><th align="left" width="4%" class="sortable-numeric">Hide From Client</th><?php } ?>
		<th align="left" width="4%" class="sortable-numeric">Show on Footer</th>
		<th align="left" width="4%" class="sortable-numeric">Status</th>
		<th width="19%">Action</th>
	</tr>	 
	</thead>
	
	<tfoot> 
		<tr> 
			<th colspan="11" class="right"> 
				<label for="mass_action">With selected:</label> 
				<select style="text-transform:none!important;width:150px" name="mass_action" id="mass_action" class="element select"> 
					<option value="">Please select action</option> 					
					<option value="mass_trash" id="mass_trash">Delete</option> 					
				</select></th> 
		</tr> 
	</tfoot> 
	
	<tbody>
	<?php foreach($users_result as $key => $user){ 
		if($user['id']== 81) //DISABLED PAGEMANAGEMENT FROM USER AS DISSCUSIION ON 10/SEP/09
			continue;
	 ?>
	<tr onclick="lockRowUsingCheckbox();lockRow();" class="pane">	
		<td><input type="checkbox" name="tablechoice[]" value="<?php echo $user['id']; ?>" /></td>
		<td class="left"><?php echo $user['id']; ?></td>
		<td class="left"><?php echo $all_pages[$user['page_parent_id']]; ?></td>
		<td class="left"><?php echo $user['page_name']; ?></td>
		<td class="left"><?php echo mysql_to_human($user['created'],'Y-m-d'); ?></td>
		<td align="right" valign="middle">
		<?php echo $user['rank']; ?></td><td align="left">		 
		&nbsp;&nbsp;<img src="<?php echo base_url(); ?>images/backend/save_file.jpg" onclick="change_rank('<?php echo $user['id']; ?>','<?php echo $user['rank']; ?>','<?php echo $cnt_total_rows; ?>','<?php echo $this->form_name; ?>','<?php echo site_url($this->page_name);?>/change_rank');" style="cursor:pointer;" title="Click to Change Rank" alt="Click to Change Rank"  />		
		</td>
		<?php if($this->session->userdata('user') == 'admin@exateam.com'){ ?><td align="center" valign="middle"><label class="status_label"><?php echo $user['hide_client'] ?></label>
                    <?php
                    if ($user['hide_client'] == 1)
                    {
                    ?>
                    <img src="<?php echo base_url(); ?>images/backend/page_right.jpg" alt="Click here to make this record inactive" title="Click here to make this record inactive" border="0" width="18" height="18" class="cursor_pointer" onclick="change_hide_client('<?php echo site_url($this->page_name.'/inactive_hide_client/'.$user['id']);?>','0');"/>
                    <?php
                    }
                    else
                    {
                    ?>
                    <img src="<?php echo base_url(); ?>images/backend/page_wrong.jpg" alt="Click here to make this record active" title="Click here to make this record active" border="0" width="18" height="18" class="cursor_pointer" onclick="change_hide_client('<?php echo site_url($this->page_name.'/active_hide_client/'.$user['id']);?>','1');"/>
                    <?php
                    }
                    ?>
        </td><?php } ?>
		<td align="center" valign="middle"><label class="status_label"><?php echo $user['display_footer'] ?></label>
                    <?php
                    if ($user['display_footer'] == 1)
                    {
                    ?>
                    <img src="<?php echo base_url(); ?>images/backend/page_right.jpg" alt="Click here to hide footer" title="Click here to hide footer" border="0" width="18" height="18" class="cursor_pointer" onclick="display_footer('<?php echo site_url($this->page_name.'/hide_footer/'.$user['id']);?>','0');"/>
                    <?php
                    }
                    else
                    {
                    ?>
                    <img src="<?php echo base_url(); ?>images/backend/page_wrong.jpg" alt="Click here to display footer" title="Click here to display footer" border="0" width="18" height="18" class="cursor_pointer" onclick="display_footer('<?php echo site_url($this->page_name.'/show_footer/'.$user['id']);?>','1');"/>
                    <?php
                    }
                    ?>
        </td>
		<td align="center" valign="middle"><label class="status_label"><?php echo $user['status'] ?></label>
                    <?php
                    if ($user['status'] == 1)
                    {
                    ?>
                    <img src="<?php echo base_url(); ?>images/backend/page_right.jpg" alt="Click here to make this record inactive" title="Click here to make this record inactive" border="0" width="18" height="18" class="cursor_pointer" onclick="change_active('<?php echo site_url($this->page_name.'/inactive/'.$user['id']);?>','0');"/>
                    <?php
                    }
                    else
                    {
                    ?>
                    <img src="<?php echo base_url(); ?>images/backend/page_wrong.jpg" alt="Click here to make this record active" title="Click here to make this record active" border="0" width="18" height="18" class="cursor_pointer" onclick="change_active('<?php echo site_url($this->page_name.'/active/'.$user['id']);?>','1');"/>
                    <?php
                    }
                    ?>
         </td>
		  <td align="right" width="10%">
						<?php
						if($this->session->userdata('user') == 'admin@exateam.com')
						{
							$edit_preview_indicator  = 1;
						}
						else
						{
								if($user['hide_client'] == 1)
								{
										$edit_preview_indicator  = 0;
								}
								else
								{	
									$edit_preview_indicator  = 1;
								}
						}
						if ($edit_preview_indicator == 1)
						{
						?>
						<a href="<?php echo site_url($this->page_name."/edit/".$user['id']); ?>" title="Edit record with id: <?php echo $user['id']; ?>">	
								<img src="<?php echo base_url(); ?>images/backend/page_edit.jpg" title="Edit record with id: <?php echo $user['id']; ?>" alt="Edit record with id: <?php echo $user['id']; ?>" border="0" class="cursor_pointer" width="18" height="18"  />
						</a>&nbsp;&nbsp;
							<img src="<?php echo base_url(); ?>images/backend/page_delete.jpg" title="Delete record with id: <?php echo $user['id']; ?>" alt="Delete record with id: <?php echo $user['id']; ?>" border="0" onclick="javascript:confirm_delete('<?php echo site_url($this->page_name.'/delete/'.$user['id']);?>');" class="cursor_pointer" width="18" height="18"  />
	  &nbsp;&nbsp;
							<?php 
								}
							?>
	  <a target="_blank" href="<? echo site_url($user['page_name']) ?>" title="Preview record with id: <?php echo $user['id'];?>">
	  <img src="<?php echo base_url(); ?>images/backend/page_preview.jpg" alt="Preview user with id: <?php echo $user['id']; ?>" title="Preview user with id: <?php echo $user['id']; ?>" border="0"  class="cursor_pointer" width="18" height="18" />
	  </a>	
		<?php
							if ($user['history_id'] != 0)
							{
							?>
								 <a target="_blank" href="<?php echo site_url($this->page_name."/history/".$user['id']); ?>" title="Preview history record with id: <?php echo $user['id'];?>">
								<img src="<?php echo base_url(); ?>images/backend/history.jpg" alt="Preview history record with id: <?php echo $user['id']; ?>" title="Preview history record with id: <?php echo $user['id']; ?>" border="0"  class="cursor_pointer" width="18" height="18" />
								</a>
							<?php 
							}
							?>	
	 	 
                </td>
	</tr>	
	<?php }//end foreach ?>
	</tbody>
</table>




<?php }else{ ?>
<h4>Sorry, no records were found in the system.</h4>
<a href="<?php echo site_url($this->page_name.'/add');?>" title="Click here to add a record"><img src="<?php echo base_url(); ?>images/backend/page_plus.jpg" alt="Click here to add a record" title="Click here to add a record" border="0" />&nbsp; Click here to add a record</a>
<?php }//end if	?>

<?php if ($trash_rows > 0){ ?>
<br />
	<a href="<?php echo site_url($this->page_name.'/show_trash_list');?>" title="Click here to list records in trash"><img src="<?php echo base_url(); ?>images/backend/trash_big.gif" alt="Click here to list records in trash" title="Click here to list records in trash" border="0" align="right" /></a><br/>
	
<?php } 
?>
<input type="hidden" name="mode" id="mode"/>
<input type="hidden" name="id" id="id"/>
<input type="hidden" name="curr_rank" id="curr_rank"/>
<input type="hidden" name="new_rank" id="new_rank"/>
<input type="hidden" name="r_mode" id="r_mode"/>

<?
echo form_close();
?>

</div>
