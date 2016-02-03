<div style="width:950px"> 
<form method="post" action="<?php echo site_url($this->page_name);?>" name="<?php echo $this->form_name; ?>" id="<?php echo $this->form_name; ?>" class="login">
<input type="hidden" name="mode" value="<?php echo $submit_name; ?>" />
<table width="950"  class="pickme rowstyle-alt no-arrow mytables" id="striped2"> 
	<thead>
	<tr>
		<th width="16%" class="sortable-numeric">ID</th>
		<th width="16%" class="sortable-text">Page Name</th>
		<th align="left">Meta Keywords</th>
		<th align="left">Meta Description</th>
		<th align="left">H1</th>
		<th align="left">Title Tag</th>
		<th align="left">Site Map Content</th>
	</tr>
	</thead>
	<tbody>
	<?
		$idsArray = array();
		foreach($result_data as $rows)
		{
		$idsArray[] = $rows['id'];
	?>
	
	<tr class="pane">
		<td valign="top" align="left"><?php echo $rows['id'];?></td>
		<td valign="top" align="left"><?php echo $rows['page_name'];?></td>
		<td>
			<textarea cols="22" rows="4" class="element text" name="<?php echo $rows['id'];?>meta_keywords" ><?php echo $rows['page_meta_keywords'];?></textarea>
		</td>
		<td><textarea cols="22" rows="4" class="element text" name="<?php echo $rows['id'];?>meta_description" ><?php echo $rows['page_meta_description'];?></textarea></td>
		<td><textarea cols="22" rows="4" class="element text" name="<?php echo $rows['id'];?>h1" ><?php echo $rows['page_h1']; ?></textarea></td>
		<td><textarea cols="22" rows="4" class="element text" name="<?php echo $rows['id'].'title_tag';?>" ><?php echo $rows['page_title_tag']; ?></textarea></td>
		<td><textarea cols="22" rows="4" class="element text" name="<?php echo $rows['id'].'seo_content';?>" ><?php echo $rows['seo_content']; ?></textarea></td>
	</tr>	
	<?
		}
	?>

	</tbody>
</table> 
<ul>
<li class="buttons"> 
		<button type="button" class="positive" onclick="handle_submit('<?php echo $this->form_name; ?>','<?php echo site_url($this->page_name)?>/show_list');"> 
		<img src="<?php echo base_url();?>images/backend/icons/articles_menu.gif" alt="Back to list" title="Back to list" /> 
			Back to list
		</button>
		
		<button type="button" class="positive" name="<?php echo $submit_name; ?>" onclick="handle_submit('<?php echo $this->form_name ?>','<?php echo site_url($this->page_name."/".$submit_name)?>');" value="<?php echo $submit_value; ?>"> 
		<img src="<?php echo base_url();?>images/backend/icons/categories_add.gif" alt="Seo Update" title="Seo Update" /> 
			<?php echo $submit_value; ?>
		</button>  
	</li> 
</ul>	
	<input type="hidden" name="idsArray" value="<?=implode(',',$idsArray)?>" />
</form> 
</div>