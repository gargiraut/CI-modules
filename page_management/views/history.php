<script  type='text/javascript' src='<?php echo base_url(); ?>js/modal.js'></script>
<link href="<?php echo base_url(); ?>css/backend/popup.css" rel="stylesheet" type="text/css" media="screen" />
<script type="text/javascript" lang="javascript">
    var openMyModal = function(source, width, height)
    {
        modalWindow.windowId = "myModal";
        randnum = Math.random(); 
        modalWindow.width = 700;
        modalWindow.height = 920;
        modalWindow.content = "<iframe id='"+randnum+"' width='"+width+"' height='"+height+"' frameborder='0' scrolling='no' allowtransparency='true' src='" + source + "'></iframe>";
        modalWindow.open();
    }; 

function show_modal_dialog(dpcs_log_id,dpcs_page_id)
{
		$("#restore_confirmation_box").css('display','block');
		document.getElementById('popup_confimrmation').reset();//SINCE USER CLOSE THE FORM IT SHOUDL RESET THE VALUE
		$("#restore_dpcs_id").val(dpcs_page_id);
		$("#restore_version_id").val(dpcs_log_id);
		$('#restore_confirmation_box').dialog({
			bgiframe: true,
			modal: true,
			width: 500,
			height:300,
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
$(document).ready(function(){
	$("#restore_confirmation_box").css('display','none');
	$(".version_control_comments").css('display','none');
})				
</script>
<fieldset>
	<legend>Quick Notes</legend>
	<ul>
    <li>
						<span class="grey_text">User Page Created Details:&nbsp; </span> <br/>
							<?php 
								if(is_array($user_count) && count($user_count)>0)
								{
									foreach($user_count as $user_history)
									{
								?>
									<span class="grey_text"><?php echo $user_history['email']?>&nbsp; &nbsp; Log Count <?php echo $user_history['LOG_COUNT']?></span><br/>
								<?php								
									}
								}//END OF if(is_array($user_count) && count($user_count)>0)
							
							?>
			
					
		</li>
		
    </ul>
 </fieldset>
 <br/>
    <div style="overflow:auto; height:100%;width:100%;border:1px solid #CCCCCC;">
    <table cellspacing="2" cellpadding="2" border="1" class="pickme rowstyle-alt no-arrow mytables">
        <tbody>
         <tr>
            <th width="25%" class="nt"><b>Current Copy</b></th>

            <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                       {
													if($historyvalue['current_copy_flag'] == 1)
													{
														$image_name = base_url().'images/backend/tick.png';
													}
													else
													{
														$image_name = base_url().'images/backend/delete.gif';
													}
                   ?>
                         
												 <th width="25%" class="nt">
												 <img src="<?php echo $image_name?>" alt="<?php echo $image_name?>"title="<?php echo $image_name;?>"/></th>
            <?php
                        }
                    }
               ?>
        </tr>
        <tr>
            <th width="25%" class="nt">Modified Days</th>

            <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
												
                   ?>
                         <th width="25%" class="nt"><?php echo date_when(strtotime($historyvalue['created'])); ?></th>
            <?php
                        }
                    }
               ?>
        </tr>
				<tr>
            <th width="25%" class="nt">Last Restored</th>

            <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
												
                   ?>
                         <th width="25%" class="nt"><?php echo date_when(strtotime($historyvalue['modified'])); ?></th>
            <?php
                        }
                    }
               ?>
        </tr>
				<tr>
            <th width="25%" class="nt">Modified Date</th>

            <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
												
                   ?>
                         <th width="25%" class="nt">Modified on :<?php echo $historyvalue['created']; ?></th>
            <?php
                        }
                    }
               ?>
        </tr>
				<tr>
            <th align="centre" class="nt">User who updated details

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php 
																$user_details = search_answers($user_count,'user_id',$historyvalue['user_id']);
																if(is_array($user_details) && count($user_details)>0)
																{
																			echo $user_details[0]['email'];	
																}																
												?></td>
            <?php
                        }
                    }
               ?>

        </tr>
				<tr>
            <th align="centre" class="nt">Page Content

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td> 
																<?php
																if($historyvalue['current_copy_flag'] != 1)
																{
																?>
																<a class="show_confirmation_dialog" id="show_confirmation_dialog"  style="cursor:pointer;" onclick="show_modal_dialog('<?php echo $historyvalue['id']; ?>','<?php echo $historyvalue['page_id']; ?>')">
																			<img src="<?php echo base_url(); ?>images/backend/restore.jpg" alt="Restore record: <?php echo $historyvalue['page_head'];?>" title="Restore record: <?php echo $historyvalue['page_head']; ?>" border="0"  class="cursor_pointer" width="18" height="18" />																			
																</a> &nbsp;&nbsp;
																<?php
																}//EN DOF if($historyvalue['current_copy_flag'] != 1)
																?>
																<a class="show_map"id="show_map"  style="cursor:pointer;" onclick="openMyModal('<?php echo site_url($this->page_name.'/show_page_data/'.$historyvalue['id']);?>', 969, 681);">
																			<img src="<?php echo base_url(); ?>images/backend/html_data.png" alt="Preview record: <?php echo $historyvalue['page_head'];?>" title="Preview record: <?php echo $historyvalue['page_head']; ?>" border="0"  class="cursor_pointer" width="18" height="18" />
																			
																</a> &nbsp;&nbsp;
																<a target="_blank" href="<?php echo site_url('history_preview/'.$historyvalue['id']) ?>" title="Preview record with id: <?php echo $historyvalue['id'];?>">
	  <img src="<?php echo base_url(); ?>images/backend/page_preview.jpg" alt="Preview user with id: <?php echo $historyvalue['id']; ?>" title="Preview record with id: <?php echo $historyvalue['id']; ?>" border="0"  class="cursor_pointer" width="18" height="18" />
	  </a>
												</td>
            <?php
                        }
                    }
               ?>

        </tr>
        <tr>
            <th align="centre" class="nt">Page Head
           
             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['page_header'];?></td>
            <?php
                        }
                    }
               ?> 
            
        </tr>
        <tr>
            <th align="centre" class="nt">Page Footer

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['page_footer'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
        <tr>
            <th align="centre" class="nt">Meta Keywords

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['page_meta_keywords'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
        <tr>
            <th align="centre" class="nt">Meta Description

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo  $historyvalue['page_meta_description'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
       <tr>
            <th align="centre" class="nt">H1

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo  $historyvalue['page_h1'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
        <tr>
            <th align="centre" class="nt">Title

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['page_title_tag'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
				<tr>
            <th align="centre" class="nt">Site Map Content

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['seo_content'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
        <tr>
            <th align="centre" class="nt">Parent Page

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['page_parent_id'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
        <tr>
            <th align="centre" class="nt">Seo Redirect

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo  $historyvalue['redirect_page_id'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
   
       <tr>
            <th align="centre" class="nt">Featured Page

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['page_featured'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
        <tr>
            <th align="centre" class="nt">Featured Image

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
											<td>
												<?php 
														$set_large_image = ( check_file_exists(ROOTBASEPATH.'media/images/page_management/large/' .$historyvalue['featured_image_name']) ) ? 1:0; 
															if($set_large_image == 1)
																{ ?>
																	<a href="<?php echo base_url()."media/images/page_management/large/". $historyvalue['featured_image_name'];?>" title="DPCS image<?php echo $historyvalue['featured_image_name']; ?>" class="imginfo" rel="lightbox">
															<?php	
																} 
																if($set_large_image == 1)
																{ 
															?>
															<img src="<?php echo base_url();?>/images/backend/camera.jpg" alt="user" title="user" /></a>
															<?php 
															} 
												?></td>
            <?php
                        }
                    }
               ?>

        </tr>
       
        
        <tr>
            <th align="centre" class="nt">Created 

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['created'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
        <tr>
            <th align="centre" class="nt">Modified

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['modified']; ?></td>
            <?php
                        }
                    }
               ?>

        </tr>
       <tr>
            <th align="centre" class="nt">Status

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
				<td><?php 
						if($historyvalue['status'] == 1)
						{
							?>
						<img src="<?php echo base_url(); ?>images/backend/page_right.jpg" alt="Active Record" title="Active Record" border="0" width="18" height="18"/>	
							<?php
						}	
						else
						{
							?>
						<img src="<?php echo base_url(); ?>images/backend/page_wrong.jpg" alt="InActive Record" title="InActive Record" border="0" width="18" height="18"/>	
							<?php
						}	
					?></td>
            <?php
                        }
                    }
               ?>

        </tr>
        <tr>
            <th align="centre" class="nt">Display On footer

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
				   <td><?php 
						if($historyvalue['display_footer'] == 1)
						{
							?>
						<img src="<?php echo base_url(); ?>images/backend/page_right.jpg" alt="Active Record" title="Active Record" border="0" width="18" height="18"/>	
							<?php
						}	
						else
						{
							?>
						<img src="<?php echo base_url(); ?>images/backend/page_wrong.jpg" alt="InActive Record" title="InActive Record" border="0" width="18" height="18"/>	
							<?php
						}	
					?></td>
                         
            <?php
                        }
                    }
               ?>

        </tr>
        <tr>
            <th align="centre" class="nt">Display On frontEnd Menu

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
							<td><?php 
						if($historyvalue['menu_frontend'] == 1)
						{
							?>
						<img src="<?php echo base_url(); ?>images/backend/page_right.jpg" alt="Active Record" title="Active Record" border="0" width="18" height="18"/>	
							<?php
						}	
						else
						{
							?>
						<img src="<?php echo base_url(); ?>images/backend/page_wrong.jpg" alt="InActive Record" title="InActive Record" border="0" width="18" height="18"/>	
							<?php
						}	
					?></td>		 
                        
            <?php
                        }
                    }
               ?>

        </tr>
        <tr>
            <th align="centre" class="nt">Hide From Client

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
						<td><?php 
						if($historyvalue['hide_client'] == 0)
						{
							?>
						<img src="<?php echo base_url(); ?>images/backend/page_right.jpg" alt="Active Record" title="Active Record" border="0" width="18" height="18"/>	
							<?php
						}	
						else
						{
							?>
						<img src="<?php echo base_url(); ?>images/backend/page_wrong.jpg" alt="InActive Record" title="InActive Record" border="0" width="18" height="18"/>	
							<?php
						}	
					?></td>		 			 
                        
            <?php
                        }
                    }
               ?>

        </tr>
        
        <tr>
            <th align="centre" class="nt">Created By

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['created_by'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
       <tr>
            <th align="centre" class="nt">Modified By

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo  $historyvalue['modified_by'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
       
        
       <tr>
            <th align="centre" class="nt">Dpcs ID

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['page_id'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
				<tr>
            <th align="centre" class="nt">IP Address

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td><?php echo $historyvalue['ip_address'];?></td>
            <?php
                        }
                    }
               ?>

        </tr>
				 <tr>
            <th align="centre" class="nt">Comments

             <?php
                    if(is_array($history) && count($history)>0)
                    {
                        foreach($history as $historyvalue)
                        {
                   ?>
                         <td>
													<?php
														if($historyvalue['user_comments'] !='')
														{
													?>
												 <img src="<?php echo base_url()?>/images/backend/CommentsIcon.png" alt="User Comments" title="Comments" style="cursor:pointer" onclick="javascript:show_product_dialog('<?php echo $historyvalue['id']?>');"/>
												 <?php
														}
														?>
														<div id="dialog_product_<?php echo $historyvalue['id']?>" class="version_control_comments" title="Comments from User While Restoring"><?php echo $historyvalue['user_comments'];?></div></td>
            <?php
                        }
                    }
               ?>

        </tr>
        </tbody>
      </table>
         </div>
	<div>
	<br/>
<ul>
					<li class="buttons">
					<a href="#" class="float_left" title="Click here to move up">Go To Top</a>
	<button type="button" class="float_right" onclick="handle_redirect('<?php echo site_url($this->page_name);?>')">  
							<img src="<?php echo base_url();?>images/backend/icons/articles_menu.gif" alt="Back to list" title="Back to list" class="float_right" /> 
							Back to list
						</button>
					</li>
				</ul>
</div>	
<!-- POP UP FORM FOR CONFIRMATION-->
<div id="restore_confirmation_box" title="Restore This Version of Page">
	<form id="popup_confimrmation" name="popup_confimrmation" method="post" class="login" action="<?php echo site_url($this->page_name.'/restore_version')?>">
			<input type="hidden" name="restore_dpcs_id" id="restore_dpcs_id" value=""/>
			<input type="hidden" name="restore_version_id" id="restore_version_id" value=""/>
      <fieldset class="active" style="width:400px">
    <ul>
     <li> 
				<span>
						<label class="field_width2">Restore H1</label>
				</span> 
				<span>
        <label>
						<input id="page_h1" type="checkbox" name="page_h1" value="0" class="checkbox_class"/>
        </label>
        </span> 
			</li>
			<li> 
				<span>
						<label class="field_width2">Restore Title</label>
				</span> 
				<span>
        <label>
						<input id="page_title" type="checkbox" name="page_title" value="0" class="checkbox_class"/>
        </label>
        </span> 
			</li>
			<li> 
				<span>
						<label class="field_width2">Restore Page Name</label>
				</span> 
				<span>
        <label>
						<input id="page_name" type="checkbox" name="page_name" value="0" class="checkbox_class"/>
        </label>
        </span> 
			</li>
     <li> 
				<span>
						<label class="field_width2">Restore Meta Keywords</label>
				</span> 
				<span>
        <label>
						<input id="meta_keywords" type="checkbox" name="meta_keywords" value="0" class="checkbox_class"/>
        </label>
        </span> 
			</li>
			 <li> 
				<span>
						<label class="field_width2">Restore Meta Description</label>
				</span> 
				<span>
        <label>
						<input id="meta_description" type="checkbox" name="meta_description" value="0" class="checkbox_class"/>
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