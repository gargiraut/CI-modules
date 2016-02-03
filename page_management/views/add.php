<script type="text/javascript">
$(document).ready(function(){

	$("#redirect_page").change(function(){
	
		var redirect_url    = $("#redirect_name").val();
		var redirect_page   = $("#redirect_page").val();
		var redirect_option = $("#redirect_page option");
		
		if(redirect_url && redirect_url!='')
		{
			var msg = "[ WARNING : Updating Record/s ] \n\n Are you sure you want to update the selected Record/s of the SEO redirect ?  \n\n . Redirect url ="+redirect_page;
			var conf_redirect = confirm(msg);
			if(conf_redirect) 
			{
				$("#redirect_name").val('');
			}
			else
			{
				
				redirect_option[0].selected="selected";
			}
		}
	});
	$(".version_content_preview").css('display','none');
	$("#restore_confirmation_box").css('display','none');
	
});


/*
| Function to uncheck the Radio Element Since user wants toi upload the image
*/
function uncheck_radio_button()
{
	var checkUploaded =  	$("#page_featured_image").val();
	
	if(checkUploaded)
	{
		$(".fet_img").attr("checked", false); 
	}
	
}

//-------------------------------------------------------------------------------
function setImageDpcs(ImageIndicator)
{
	var checkUploaded =  	$("#page_featured_image").val();
	if(checkUploaded)
	{
		var msg = "[ WARNING : Deleting ] \n\n Are you sure you want to Neglect The Selected Image For upload?";	  
		var conf = confirm(msg);
		if(conf) 
		{
					$("#page_featured_image").val('');
		}	
		else 
		{
				$(".fet_img").attr("checked", false);
				return false;
		} 
	}
	else
	{
		$("#page_featured_image").val('');
	}	
}


//--------------------------------------------------------------------------------------------
function preview_modal_version(version_class_name,width_dialog,height_dialog)
{
		var select_box_version = $('#previous_version_select :selected').val();
		if(select_box_version!=0)
		{
			dialog_id = "restore_confirmation_box_"+select_box_version;
			show_preview_dialog_new(select_box_version,width_dialog,height_dialog);
		}	
		else
		{
			var msg_info = "<h1>To View Page Content</h1><br/><p>Please Select the version</p>";
			info_message(msg_info);
		}
		
}

//--------------------------------------------------------------
function show_preview_dialog_new(dialog_id,width_dialog,height_dialog)
{
	var $loading = $('Vishal');
	var $dialog = $('<div></div>').append($loading.clone());
	$dialog.load('<?php echo site_url('page_management/ajax_call/history_content/')?>/'+dialog_id).dialog({
						title: 'DPCS History data',
						width: width_dialog,
						height: height_dialog,
						modal: false,
						buttons: {
												Ok: function() {
															$( this ).dialog( "close" );
															return false;
													}
											}
					});

				return false;
}

function preview_browser_version()
{
		var select_box_version = $('#previous_version_select :selected').val();
		if(select_box_version!=0)
		{
			var front_end_url = '<?php echo site_url('history_preview')?>/'+select_box_version;
			$("#link_frontend_preview").attr('href',front_end_url);	
			
		}	
		else
		{
			var msg_info = "<h1>To View Page Content</h1><br/><p>Please Select the version</p>";
			info_message(msg_info);
		}	
		
}

function show_modal_dialog()
{
		$("#restore_confirmation_box").css('display','block');
		var select_box_version = $('#previous_version_select :selected').val();
		if(select_box_version!=0)
		{
			$("#restore_version_id").val(select_box_version);
			$("#version_html").html('You are About to restore Version date :'+$('#previous_version_select :selected').text());
			$('#restore_confirmation_box').dialog({
					bgiframe: true,
					modal: true,
					width: 500,
					height:200,
					buttons: {
						Submit: function(){
							 $("#popup_confimrmation").submit();
						},
						Cancel: function() {
											 $(this).dialog("close");
						}
					}
			});	
			
		}	
		else
		{
			var msg_info = "<h1>To View Page Content</h1><br/><p>Please Select the version to restore</p>";
			info_message(msg_info);
		}	
		
}

</script>
<?php $this->load->view('editor_disclamer');?>
<div style="width:1000px"> 
<form method="post" action="<?php echo site_url($this->page_name);?>" name="<?php echo $this->form_name; ?>" id="<?php echo $this->form_name; ?>" class="login" enctype="multipart/form-data">
<input type="hidden" name="mode" value="<?php echo $submit_name; ?>" />
<input type="hidden" name="token" id="token" value="<?php echo $token; ?>" />
<input type="hidden" name="page_parent_original" id="page_parent_original" value="<?php echo $page_parent_original; ?>" />
<h3 class="headers gray"><?php echo $pagetitle_sub; ?></h3>
<fieldset class="active"> 
<ul>	
	<li> 
		<div><label class="description">Fields marked with<span class="required"> *</span> are mandatory.</label></div>
	</li>	
	<li>
		<span> 			
			<label for="id" class="field_width"><span class="required">*</span> ID</label> 
		</span>		
		<span> 
			<label><?php echo $id; ?><input type="hidden" name="id" id="id" value="<?php echo $id; ?>" /></label>
		</span>		 
	</li>
	 <li> <span>
        <label for="status" class="field_width"><span class="requiredgrey">*</span> Status</label>
        </span> <span>
        <input type="checkbox" name="status" id="status" value="1" <? if($status == 1){ echo 'checked="checked"';}?> />
           </span>
		   <p class="cms_help">Make the page viewable on the website</p>
		   	 </li>		
	
	<li>
		<span> 			
			<label for="page_featured" class="field_width">&nbsp;&nbsp;&nbsp;&nbsp;Featured</label> 
		</span>		
		<span> 
			<input type="checkbox" name="page_featured" value="1" id="page_featured" <?php if($page_featured == 1){echo 'checked="checked"';} ?> class="element" />
		</span>		 
		 <p class="cms_help"> Make the page featured on the relevant part of the site</p>
	</li>
	<li>
		<span> 			
			<label for="display_footer" class="field_width">&nbsp;&nbsp;&nbsp;&nbsp;Shown On Footer</label> 
		</span>		
		<span> 
			<input type="checkbox" name="display_footer" value="1" id="display_footer" 
			<?php if($display_footer == 1){echo 'checked="checked"';} ?>  class="element" />
		</span><br />		 
	</li>
	<?php  if($this->session->userdata('user') == 'admin@exateam.com')
		{
		?>
	<li>
		 
        <span> 			
			<label for="hide_client" class="field_width">&nbsp;&nbsp;&nbsp;&nbsp;Hide page from client</label> 
		</span>		
		<span> 
       
			<input type="checkbox" name="hide_client" value="1" id="hide_client" 
			<?php if($hide_client == 1){echo 'checked="checked"';} ?>  class="element" />
		<?
        }
		?>
        
		</span><br />		 
	</li>
	<li>
		<span> 			
			<label for="hide_client" class="field_width">&nbsp;&nbsp;&nbsp;&nbsp;Show on Menu</label> 
		</span>		
		<span> 
       
			<input type="checkbox" name="menu_frontend" value="1" id="menu_frontend" 
			<?php if($menu_frontend == 1){echo 'checked="checked"';} ?>  class="element" />
		       
		</span>
		 <p class="cms_help"> Make the page viewable on the Left nav of the Website</p><br />		 
	</li>
	
	</ul> 
	<br/>
	<ul>
	
	
		
	<li <? //if($this->session->userdata("email_id") <> "admin@exateam.com" ) {echo 'style="display:none;"';}?>>
		<span> 			
			<label for="page_parent" class="field_width"><span class="required">*</span> Parent Page</label> 
		</span>	
		 <p class="cms_help">Make the page a child page for an existing parent page within the site. </p>	
		<span> 
			<select id="page_parent" class="element select width_big" name="page_parent">
			<?php echo $list_pages; ?>
			</select>
		</span>		 
	</li>
	
	<?php if($this->session->userdata("email_id") == "admin@exateam.com" || $wm_client_status == 1){ ?>
	<li>
		<span> 			
			<label for="page_name" class="field_width"><span class="required">*</span> Page Name</label> 
		</span>		
		<span> 
			<input type="text" name="page_name" id="page_name" value="<?php echo $page_name; ?>" maxlength="50" class="element text text_width" />
		</span> <p class="cms_help">(All letter will be made to lower case and space changed to hyphen '-')</p>		 
	</li>	
    <li>
		<span> 			
			<label for="page_head" class="field_width"><span class="required">*</span> Page Head</label> 
		</span>		
		<span> 
			<input type="text" name="page_head" id="page_head" value="<?php echo $page_head; ?>" maxlength="50" class="element text text_width" />
		</span> 
	</li>		
	<?php 
	}else
{	
	?>
		<li>
		<span> 			
			<label for="page_name" class="field_width"><span class="required">*</span> Page Name</label> 
		</span>		
		<span> 
			<input type="text" name="page_name" id="page_name" value="<?php echo $page_name; ?>" maxlength="50" class="element text text_width" />
		</span> <p class="cms_help">(All letter will be made to lower case and space changed to hyphen '-')</p>		 
	</li>
	
     <li>
		<span> 			
			<label for="page_head" class="field_width"><span class="required">*</span> Page Head</label> 
		</span>		
		<span> 
			<input type="text" name="page_head" id="page_head" value="<?php echo $page_head; ?>" maxlength="50" class="element text text_width" />
		</span> 
	</li>	
	<?php } ?>
	<?php if($this->session->userdata("email_id") == "admin@exateam.com" || $wm_client_status == 1) {?>
	<li>
		<span> 			
			<label for="page_title_tag" class="field_width"><span class="required">*</span> Page Title Tag</label> 
		</span>		
		<span> 
			<input type="text" name="page_title_tag" id="page_title_tag" value="<?php echo $page_title_tag; ?>" maxlength="250" class="element text text_width" />
		</span>		 
	</li>
	
	<li>
		<span> 			
			<label for="redirect_page_id" class="field_width"><span class="required">&nbsp;</span>Redirect url</label> 
		</span>		
		<span> 
			<select id="redirect_page_id" class="element select width_big" name="redirect_page_id">
				<?php echo $redirect_page_id; ?>
			</select>
		
			</select>
		</span>
		<p class="cms_help">(These will be used for the SEO redirect when the page is set as Inative)</p>		 
	</li>	
	<?php 
	} 
	if($this->session->userdata("email_id") == "admin@exateam.com" || $wm_client_status == 1) 
	{
		$style= "";
	}
	else
	{
		$style= "style='display:none'";
	}
	?>
	<li <?php echo $style;?>>
		<span> 			
			<label for="page_header" class="field_width"><span class="required">*</span> Page Header</label> 
		</span>		
		<span> 
			<input type="text" name="page_header" id="page_header" value="<?php echo $page_header; ?>" maxlength="250" class="element text text_width" />
		</span>		 
	</li>
	
	<li <?php echo $style;?>>
		<span> 			
			<label for="page_footer" class="field_width"><span class="required">*</span> Page Footer</label> 
		</span>		
		<span> 
			<input type="text" name="page_footer" id="page_footer" value="<?php echo $page_footer; ?>" maxlength="250" class="element text text_width" />
		</span>		 
	</li> 
	<?php
	if($this->session->userdata("email_id") == "admin@exateam.com" || $wm_client_status == 1) 
	{
	?>
	<li>
		<span> 			
			<label for="page_meta_keywords" class="field_width"><span class="required">*</span> Meta Keywords</label> 
		</span>		
		<span> 
			<textarea name="page_meta_keywords" id="page_meta_keywords" class="element textarea" cols="51" rows="5"><?php echo $page_meta_keywords; ?></textarea>
		</span>		 
	</li>
	
	<li>
		<span> 			
			<label for="page_meta_description" class="field_width"><span class="required">*</span> Meta Description</label> 
		</span>		
		<span> 
			<textarea name="page_meta_description" id="page_meta_description" class="element textarea" cols="51" rows="5"><?php echo $page_meta_description; ?></textarea>
		</span>		 
	</li> 
	
	<li>
		<span> 			
			<label for="page_h1" class="field_width"><span class="required">*</span> H1</label> 
		</span>		
		<span> 
			<textarea name="page_h1" id="page_h1" class="element textarea" cols="51" rows="5"><?php echo $page_h1; ?></textarea>
		</span>		 
	</li>
<li>
		<span> 			
			<label for="seo_content" class="field_width"><span class="required">*</span>Site Map Content</label> 
		</span>		
		<span> 
			<textarea name="seo_content" id="seo_content" class="element textarea" cols="51" rows="5"><?php echo $seo_content; ?></textarea>
		</span>		 
	</li> 	
	<? } ?>
	<li>
		<span> 			
			<label for="page_html_data" class="field_width"><span class="requiredgrey">*</span> Page Content</label> 
		</span>	
        <p class="cms_help">While copying text from Word document, please click on <img src="<?=base_url();?>images/backend/icons/word_icon.jpg" alt="word_icon" title="word_icon" style="padding-bottom:1px"/> "Paste from word" icon.  </p>	
		<span>
			<?php 
				$data = array (
											'name'		=>'page_html_data',
											'id'		=>'page_html_data',
											'cols'		=> CK_ROW,
											'rows'		=> CK_COLS,
											'content'	=> $page_html_data
                                        );
                        echo content_for_layout($data);
			
			 ?> 			
		</span>	 
	</li> 
	<?php
			if($submit_name == 'update')
			{
	?>
	<li>
		<span> 			
			<label for="previous_version_select" class="field_width"><span class="required">&nbsp;</span>Previous Version</label> 
		</span>		
		<span> 
			<select name="previous_version_select" id="previous_version_select" class="element select width_big" onchange="preview_browser_version();">
				<?php echo $version_number ;?>
			</select>
		</span>	
			<?php 
				if(is_array($version_details) && count($version_details)>0)
				{
					if($verison_selected_id!='')
					{
						$href_link = site_url('history_preview/'.$verison_selected_id);
					}
					else
					{
						$href_link = 'javascript:;';
					}
			?>
			
			<span class="positive"> 
				Preview Version Content<img src="<?php echo base_url();?>/images/backend/preview_stats.gif" alt="Preview Version" title="Preview Version" style="cursor:pointer" onclick="javascript:preview_modal_version('restore_confirmation_box','700','500')";/>
			</span>		
			<span class="positive"> 
				Preview in frontend 
				<a href="<?php echo $href_link;?>" target="_blank" title="View Verison in frontend" id="link_frontend_preview">
								<img src="<?php echo base_url();?>/images/backend/preview_stats.gif" alt="Preview Browser Version" title="Preview Browser Version" style="cursor:pointer"/>
				</a>
			</span>	
			<?php
					}
			?>			
	</li> 
	<br />
	<?php
	}
	if($this->session->userdata("email_id") == "admin@exateam.com") {?>
	<li>
		<span> 			
			<label for="page_include_file" class="field_width"><span class="required">&nbsp;</span> Load Module Name</label> 
		</span>		
		<span> 
			<input type="text" name="page_include_file" id="page_include_file" value="<?php echo $page_include_file; ?>" maxlength="250" class="element text text_width" />
		</span>		 
	</li> 
	<li>
		<span> 			
			<label for="dashboard_category" class="field_width"><span class="required">&nbsp;</span> Dashboard Category</label> 
		</span>		
		<span> 
			<select name="dashboard_category" id="dashboard_category" class="element select width_big">
				<?php echo $dashboard_category;?>
			</select>
		</span>	
			
	</li> 
	<? } ?>
	<br />
	<fieldset>
	<legend>Left Call To Action For This Page</legend>
	   <ul>
	   
	<li>
		<span> 			
			<label for="page_featured_page" class="field_width"><span class="requiredgrey">*</span>Page To feature</label> 
		</span>
			 <p class="cms_help">Select a different page you want to be featured when user view the page. </p>	
		<span> 
			<select id="page_featured_page" class="element select width_big" name="page_featured_page">
						 <option value=""> Select... </option>
			  <?php
					  if (is_array($list_pages_featured))
					  {
						  foreach ($list_pages_featured as $states)
						  {
						  ?>
			  <option <?php if ($states['id'] == $page_featured_page) echo 'selected="selected"';?> value="<?php echo $states['id'];?>"> <?php echo $states['page_name'];?> </option>
			  <?php
						  }
					  }
					  ?>
		
			</select>
		</span>		 
	</li>
	   <li>
	    <p class="cms_help">Select the image to appear when the page is selected as the "featured page". </p>
	   </li>
	   
	   <li> 
	   	<span>
		
        	<label class="field_width"><span class="required">&nbsp;</span>Select Image</label>
			 </span>
		<span style="display:block; width:875px">
			 <label style="display:block; float:left; margin: 0 15px 0 0; clear:none;">
					<input name="featured_image" id="featured_image1" class="fet_img" type="radio" value="pearl.jpg" onchange="javascript: setImageDpcs(1);" <?php if($featured_image == "pearl.jpg"){ echo 'checked="checked"'; }  ?> />
					<img src="<?php echo base_url();?>media/images/page_management/thumb/pearl_small.jpg" title="pearl_small" alt="pearl_small" />
			</label>
			<label style="display:block; float:left; margin: 0 15px 0 0; clear:none;">
					<input name="featured_image" id="featured_image2"  class="fet_img" type="radio" value="rose.jpg" onchange="javascript: setImageDpcs(2)"  <?php if($featured_image == "rose.jpg"){ echo 'checked="checked"'; }  ?>/> 
					<img src="<?php echo base_url();?>media/images/page_management/thumb/rose_small.jpg" alt="rose_small" title="rose_small" />
			</label>
			<label style="display:block; float:left; margin: 0 15px 0 0; clear:none;">
					 <input name="featured_image" id="featured_image3"  class="fet_img" type="radio" value="chocalate.jpg" onchange="javascript: setImageDpcs(3);" <?php if($featured_image == "chocalate.jpg"){ echo 'checked="checked"';}  ?>/>
				<img src="<?php echo base_url();?>media/images/page_management/thumb/chocalate_small.jpg" alt="chocalate_small" title="chocalate_small" />
			</label>
		 <label style="display:block; float:left; margin: 0 15px 0 0; clear:none;">
					  <input name="featured_image" id="featured_image4"  class="fet_img" type="radio" value="love.jpg" onchange="javascript: setImageDpcs(4)"  <?php if($featured_image == "love.jpg"){ echo 'checked="checked"'; } ?>/>
				<img src="<?php echo base_url();?>media/images/page_management/thumb/love_small.jpg" alt="love_small" title="love_small" />
			</label>
		 
		  	 <label style="display:block; float:left; margin: 0 15px 0 0; clear:none;">
					 <input name="featured_image" id="featured_image5"  class="fet_img" type="radio" value="new1.png" onchange="javascript: setImageDpcs(5)"  <?php if($featured_image == "new1.png"){ echo 'checked="checked"'; } ?>/>
				<img src="<?php echo base_url();?>media/images/page_management/thumb/new1.png" alt="new1" title="new1" width="60" height="65" />
			</label> 
		 	<label style="display:block; float:left; margin: 0 15px 0 0; clear:none;">
					  <input name="featured_image" id="featured_image6"  class="fet_img" type="radio" value="new_round.png" onchange="javascript: setImageDpcs(6)"  <?php if($featured_image == "new_round.png"){ echo 'checked="checked"'; } ?>/>
		<img src="<?php echo base_url();?>media/images/page_management/thumb/new_round.png" alt="new_round" title="new_round" width="60" height="65"/>
			</label> 
		<label style="display:block; float:left; margin: 0 15px 0 0; clear:none;">
				<input name="featured_image" id="featured_image7"  class="fet_img" type="radio" value="special.png" onchange="javascript: setImageDpcs(7)"  <?php if($featured_image == "special.png"){ echo 'checked="checked"'; } ?>/>
		<img src="<?php echo base_url();?>media/images/page_management/thumb/special.png" alt="special" title="special" width="60" height="65" />
			</label>
		<label style="display:block; float:left; margin: 0 15px 0 0; clear:none;">
				<input name="featured_image" id="featured_image8"  class="fet_img" type="radio" value="star.png" onchange="javascript: setImageDpcs(8)"  <?php if($featured_image == "star.png"){ echo 'checked="checked"'; } ?>/>
		<img src="<?php echo base_url();?>media/images/page_management/thumb/star.png" alt="star" title="star" width="60" height="65" />
			</label>
			
		
        </span>
	  </li>
	  
	  
	   <li> <span>
        <label class="field_width"><span class="required">&nbsp;</span>or Upload Image</label>
        </span> <span>
        <input type="file" name="page_featured_image" id="page_featured_image"  class="element file" onChange="uncheck_radio_button();"/>
       <!-- <input type="hidden" name="page_featured_image" id="page_featured_image" value="" />-->
        </span>
		
		 <?php
		 if( check_file_exists($image_path . 'large/' . $featured_image) && $featured_flag == 1)
		 { ?>
        <span id="response_image">
			<a href="<?php echo base_url()."media/images/page_management/large/". $featured_image;?>" title="Image" rel="lightbox"><img src="<?php echo base_url(); ?>images/backend/camera.jpg" border="0" title="View image" alt="View image" width="18" height="18" /></a>&nbsp;&nbsp;<a href="javascript:void(0);" title="Delete image" onclick="javascript:ajaxCallPassData('<?php echo site_url($this->parent_controller) ; ?>/ajax_call/delete_image','response_image','id=<?php echo $id; ?>&amp;image=<?php echo $featured_image; ?>');"><img src="<?php echo base_url(); ?>images/backend/delete_file.jpg" border="0" title="Delete image" alt="Delete image" width="18" height="18" /></a></span>
        <?php } ?>
		
		
	</li>
	</ul>
	</fieldset>
	
	 <br />
	<li class="buttons"> 
		<button type="button" class="positive" onclick="handle_submit('<?php echo $this->form_name; ?>','<?php echo site_url($this->page_name)?>/show_list');"> 
		<img src="<?php echo base_url();?>images/backend/icons/articles_menu.gif" alt="Back to list" title="Back to list" /> 
			Back to list
		</button>
		
		<button type="button" class="positive" name="<?php echo $submit_name; ?>" onclick="handle_submit('<?php echo $this->form_name ?>','<?php echo site_url($this->page_name."/".$submit_name)?>');" value="<?php echo $submit_value; ?>"> 
		<img src="<?php echo base_url();?>images/backend/icons/categories_add.gif" alt="Add page" title="Add page" /> 
			<?php echo $submit_value; ?>
		</button> 
		<?php
			if($submit_name == 'update')
			{
		?>
		<button type="button" class="positive" name="<?php echo $submit_name; ?>" onclick="show_modal_dialog();" value="Restore from Version"> 
				<img src="<?php echo base_url();?>images/backend/restore.jpg" alt="Restore Previous Version" title="Restore Previous Version" /> 
					Restore from Version
				</button> 		
		<?php
		}
		?>		
	</li> 
</ul>			
</fieldset> 
</form> 
</div>

<!-------VERSION CONTENT PREVIEW----------->
<?php
					if($submit_name == 'update')
					{
						if(is_array($version_details) && count($version_details)>0)
						{
							
							?>
							<!-- POP UP FORM FOR CONFIRMATION-->
<div id="restore_confirmation_box" title="Restore This Version of Page">
	<form id="popup_confimrmation" name="popup_confimrmation" method="post" class="login" action="<?php echo site_url($this->page_name.'/restore_version')?>">
			<input type="hidden" name="restore_dpcs_id" id="restore_dpcs_id" value="<?php echo $id;?>"/>
			<input type="hidden" name="restore_version_id" id="restore_version_id" value=""/>
			<input type="hidden" name="which_page" id="which_page" value="show_list"/>
      <fieldset class="active" style="width:400px">
    <ul>
		<li> 
				<span>
						<label id="version_html">
								You are about to restore the Version 
						</label>
				</span>
    </li>
    <li> 
				<span>
						<label class="field_width2">Comments</label>
				</span> 
				<span>
        <label>
						<textarea name="user_comments" id="user_comments" class="textarea_class"></textarea>
        </label>
        </span> 
			</li>
      
  </ul>
	</fieldset>
  </form>
	
</div>
<!-- POP UP FORM FOR CONFIRMATION-->
							<?php
						}//END OF if(is_array($version_details) && count($version_details)>0)
					}//END OF if($submit_name == 'update')
					?>

					