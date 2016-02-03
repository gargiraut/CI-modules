<div style="width:950px"> 
<form method="post" action="<?php echo site_url($this->page_name);?>" name="<?php echo $this->form_name; ?>" id="<?php echo $this->form_name; ?>" class="login">
<input type="hidden" name="mode" value="<?php echo $submit_name; ?>" />
<input type="hidden" name="datatype" value="<?php echo $datatype; ?>" />
<table width="950"  class="pickme rowstyle-alt no-arrow mytables" id="striped2"> 
	<thead>
	<tr>
		<th align="left">Meta Keywords</th>
		<th align="left">Meta Description</th>
		<th align="left">H1</th>
		<th align="left">Title Tag</th>
	</tr>
	</thead>
	<tbody>
	<tr class="pane">
		<td align="left"><textarea cols="22" rows="4" class="element text" name="meta_keywords" ><?php echo $meta_keywords?></textarea></td>
		<td align="left"><textarea cols="22" rows="4" class="element text" name="meta_description" ><?php echo $meta_description?></textarea></td>
		<td align="left"><textarea cols="22" rows="4" class="element text" name="h1" ><?php echo $h1?></textarea></td>
		<td align="left"><textarea cols="22" rows="4" class="element text" name="title_tag" ><?php echo $title_tag?></textarea></td>
	</tr>	
	</tbody>
</table> 
<li class="buttons"> 
		<button type="button" class="positive" name="<?php echo $submit_name; ?>" onclick="handle_submit('<?php echo $this->form_name ?>','<?php echo site_url($this->page_name."/".$submit_name)?>');" value="<?php echo $submit_value; ?>"> 
		<img src="<?php echo base_url();?>images/backend/icons/categories_add.gif" alt="<?php echo $submit_value; ?>" title="<?php echo $submit_value; ?>" /> 
			<?php echo $submit_value; ?>
		</button>  
	</li> 
</form> 
</div>