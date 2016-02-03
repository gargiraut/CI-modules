<script type="text/javascript" language="javascript"> 
//<![CDATA[ 
  $(document).ready(function(){
   
		$( "#tabs" ).tabs();
  });
	
  //]]>	
</script> 
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Log of DPCS SUB PAGE COUNT </a></li>
		
	</ul>
	<div id="tabs-1">
	<table border="0" cellspacing="2" cellpadding="2" class="pickme sortable-onload-1 rowstyle-alt no-arrow mytables" >	
			<tr>
				<td width="780px" style="float:left">	
					<table border="1" cellspacing="2" cellpadding="2" class="pickme sortable-onload-1 rowstyle-alt no-arrow mytables">
						<?php
						if(is_array($log_data) && count($log_data) > 0)
					{
					?>
					
						<tr>
							<td width="25%" class="nt"><b>User </b></td>
							<td width="25%" class="nt"><b>Number of Subpages</b></td>
							<td width="25%" class="nt"><b>Modified</b></td>
							<td width="25%" class="nt"><b>Modified By</b></td>
							<td width="25%" class="nt"><b>IP Address</b></td>
						
						</tr>
						<tr>
						<?php
						foreach($log_data as $key => $log)
						{?>
								<td class="nt">
						    		<label><?php echo $log['email']; ?></label>
								</td>
								
								<td class="nt">
									<label><?php echo $log['no_of_subpages']; ?></label>
								</td>
									<td class="nt">
									<label><?php echo date_when(strtotime($log['created'])); ?></label>
								</td>
								<td class="nt">
									<label><?php echo $log['created_by']; ?></label>
								</td>
								<td class="nt">
									<label><?php echo $log['ipaddress']; ?></label>
								</td>
							 </tr>	
							<?
								} //end for loop
						}//end of if loop
							else
							{
							?>
							<tr>
								<td width="65%" class="nt"><b>No History Found !</b></td>
						  </tr>
							<?
							}
							?>
						
					</table>
					
				</td>
				</tr>
			</table>	
	</div>
	
</div>
<div>
	
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
 
 
 
