<script type="text/javascript">
function isNumberKey(evt,error_id)
{
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
		 {
           $("#"+error_id).html('Please Input Number Only !!');
		    return false;
		 }

         return true;
}


function handle_submit_request(formname,action,id)
{
	var numberPages = $("#number_pages").val();
	
	if(numberPages == '' || numberPages == 0)
	{
		$("#div_error_static").show();
	}
	else
	{
		var obj = document.getElementById(formname);	
		if(obj != "" || obj != "undefined")
		{
			obj.action = action;
			if(isNaN(id) == false)
			{
				obj.id.value = id;
			}
				
			obj.submit();
		}
	}	
}
</script>
<div class="red1 gen1" id="div_error_static" style="display:none;">
    <div class="red2 gen2">
        <div class="red3 gen3">
            <div class="gen4 red">
                <h3>Errors Occured!</h3>
				 <ul id="message_error">					
					<li> Please Add number of pages !! </li>
        		</ul>
            </div>
        </div>
    </div>
</div>


<div  class="db_wrapper">
	<div class="top_corners">
		<p class="db_tl"></p>
		<p class="db_tr"></p>
	</div>
	
  <div id="columns">
		  
		<form method="post" action="<?php echo site_url($this->page_name.'/sendrequest')?>" name="upgrade" id="upgrade" class="login">
          
			<fieldset class="active">
			   <ul>
				  <li>
                   <span>
				        <label class="field_width"><span class="required">&nbsp;</span>PM email Address</label>
			       </span>
                   <span>
				   <?php
						$pm_email_address = unserialize(FEATURE_REQUEST);
						
						$pm_email 		  = implode(',',$pm_email_address);
						
				   ?>
				       <input type="text" id="email_address" name="email_address" value="<?php echo isset($pm_email)?$pm_email:$pm_email;?>" readonly="readonly" class="element text text_width" />
			       </span>
                   <br/>
                 </li>
                 <li>
                   <span>
				        <label class="field_width"><span class="required">&nbsp;</span>Subject</label>
			       </span>
                   <span>
				       <input type="text" id="subject" name="subject" value="<?php echo $Subject;?>" readonly="readonly" class="element text text_width" />
			       </span>
                   <br/>
                 </li>
                 <li>
                   <span>
				        <label class="field_width"><span class="required">&nbsp;</span>From email Address</label>
			       </span>
                   <span>
				       <input type="text" id="from" name="from" value="<?php echo $client_email;?>" readonly="readonly" class="element text text_width" />
			       </span>
                   <br/>
                 </li>
                 <li>
                   <span>
				        <label class="field_width"><span class="required">&nbsp;</span>Website Name</label>
			       </span>
                   <span>
				       <input type="text" id="website_name" name="website_name" value="<?php echo $website;?>" readonly="readonly" class="element text text_width" />
			       </span>
                   <br/>
                 </li>
			      <li> 
                  	<span>
				        <label class="field_width"><span class="required">*</span>Number of pages</label>
			        </span>
                    <span>
				       <input type="text" name="number_pages" id="number_pages" value="" onKeyPress="return isNumberKey(event,'error_message');" class="element text " />
					   <!--<textarea cols="80" rows="20" class="element text" name="request" id="request"></textarea>-->
		           </span> 
				   <span id="error_message"></span>
				    <span style="float:right;">Number of New Sub pages needed  example:5</span>
                  </li>
                   <li class="buttons">
			        <button type="button" class="positive" onclick="handle_redirect('<?php echo site_url($this->page_name)?>')">
                     <img src="<?php echo base_url();?>images/backend/icons/articles_menu.gif" alt="Back to list" title="Back to list" /> Back to list </button>
        <button type="button" class="positive" name="insert"  onclick="javascript:handle_submit_request('upgrade','<?php echo site_url($this->page_name.'/sendrequest')?>');" value="upgrade">
         <img src="<?php echo base_url();?>images/backend/icons/categories_add.gif" alt="Back to list" title="Back to list" /> 
		 Submit </button>
      </li>

    			 </ul>
		    	</fieldset>
		  </form>
	</div>
	<div class="bottom_corners">
		<p class="db_bl"></p>
		<p class="db_br"></p>
	</div>
</div>
