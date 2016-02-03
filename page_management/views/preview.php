<div style="width:900px;"> 
<form method="post" action="<?php echo site_url($this->page_name);?>" name="<?php echo $this->form_name; ?>" id="<?php echo $this->form_name; ?>" class="login">
<input type="hidden" name="mode" />
<h3 class="headers gray"><?php echo $pagetitle_sub; ?></h3>
<fieldset class="active"> 
<ul>	
	<li>
		<span> 			
			<label for="id" class="field_width"><span class="required">&nbsp;</span> ID</label> 
		</span>		
		<span> 
			<label><?php echo $id; ?></label>
		</span>		 
	</li>
	
	<li>
		<span> 			
			<label for="page_parent" class="field_width"><span class="required">&nbsp;</span> Parent Page</label> 
		</span>		
		<span> 
			<label><?php echo ($page_parent)?$page_parent:"N/A"; ?></label>
		</span>		 
	</li>
	
	<li>
		<span> 			
			<label for="page_parent" class="field_width"><span class="required">&nbsp;</span> Link Page</label> 
		</span>		
		<span> 
			<label><?php echo ($page_link)?$page_link:"N/A"; ?></label>
		</span>		 
	</li>
	
	<li>
		<span> 			
			<label for="page_name" class="field_width"><span class="required">&nbsp;</span> Page Name</label> 
		</span>		
		<span> 
			<label><?php echo ($page_name)?$page_name:"N/A"; ?></label>
		</span>		 
	</li>
	
	<li>
		<span> 			
			<label for="page_title_tag" class="field_width"><span class="required">&nbsp;</span> Page Title Tag</label> 
		</span>		
		<span> 
			<label><?php echo ($page_title_tag)?$page_title_tag:"N/A"; ?></label>
		</span>		 
	</li>
	
	<li>
		<span> 			
			<label for="page_header" class="field_width"><span class="required">&nbsp;</span> Page Header</label> 
		</span>		
		<span> 
			<label><?php echo ($page_header)?$page_header:"N/A"; ?></label>
		</span>		 
	</li>
	
	<li>
		<span> 			
			<label for="page_footer" class="field_width"><span class="required">&nbsp;</span> Page Footer</label> 
		</span>		
		<span> 
			<label><?php echo ($page_footer)?$page_footer:"N/A"; ?></label>
		</span>		 
	</li> 
	
	<li>
		<span> 			
			<label for="page_meta_keywords" class="field_width"><span class="required">&nbsp;</span> Meta Keywords</label> 
		</span>		
		<span> 
			<label><?php echo ($page_meta_keywords)?$page_meta_keywords:"N/A"; ?></label>
		</span>		 
	</li>
	
	<li>
		<span> 			
			<label for="page_meta_description" class="field_width"><span class="required">&nbsp;</span> Meta Description</label> 
		</span>		
		<span> 
			<label><?php echo ($page_meta_description)?$page_meta_description:"N/A"; ?></label>
		</span>		 
	</li> 
	
	<li>
		<span> 			
			<label for="page_h1" class="field_width"><span class="required">&nbsp;</span> H1</label> 
		</span>		
		<span> 
			<label><?php echo ($page_h1)?$page_h1:"N/A"; ?></label>
		</span>		 
	</li> 
	
	<li>
		<span> 			
			<label for="page_html_data" class="field_width"><span class="required">&nbsp;</span> Page Content</label> 
		</span>		
		<span> 
			<label><?php echo ($page_html_data)?$page_html_data:"N/A"; ?></label>
		</span>		 
	</li> 
	
	<li>
		<span> 			
			<label for="page_include_file" class="field_width"><span class="required">&nbsp;</span> Include External File</label> 
		</span>		
		<span> 
			<label><?php echo ($page_include_file)?$page_include_file:"N/A"; ?></label>
		</span>		 
	</li> 
	
	 
	<li class="buttons"> 
		<button type="button" class="positive" onClick="handle_submit('<?php echo $this->form_name; ?>','<?php echo site_url($this->page_name)?>/show_list');"> 
		<img src="<?php echo base_url();?>images/backend/icons/articles_menu.gif" alt="Back to list" title="Back to list" /> 
			Back to list
		</button>
		</button>  
	</li> 
</ul>			
</fieldset> 
</form> 
</div>