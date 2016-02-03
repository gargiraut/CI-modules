<script type="text/javascript" language="javascript">
//<![CDATA[ 
//mass delete or restore
$(document).ready(function (){
	$("#mass_action").change(function(){
		if($("#mass_action").val() == 'mass_delete')
		{
			confirm_delete_sel('<?php echo $this->form_name; ?>','<?php echo site_url($this->page_name."/delete_selected_permenantly")?>');
		}		
		if($("#mass_action").val() == 'mass_restore')
		{
			confirm_restore_sel('<?php echo $this->form_name; ?>','<?php echo site_url($this->page_name."/restore_selected")?>'); 		
		}
	});
});	
//]]>	
</script>
<div style="margin-top:20px" id="filter_users_response">
<?php 
$this->load->helper('form'); 
$attributes = array('id' 	=> $this->form_name,'name' => $this->form_name);
//$hidden 	= array('mode' 	=> '', 'id' => '');
echo form_open($this->page_name, $attributes);
?>
<?php if(!empty($users_result) && is_array($users_result)){ ?>

<div><a href="<?php echo site_url($this->page_name.'/add')?>" class="btn green float_left" title="Click here to add a page">Add Page</a>
<a href="javascript:void(0);" onclick="javascript:confirm_delete_sel('<?php echo $this->form_name; ?>','<?php echo site_url($this->page_name."/delete_selected_permenantly")?>');" class="btn green float_right" title="Click her to delete records">Delete Selected</a> </div>
<br /><br />
<?php if(!empty($pagination)){ ?>
<div class="pagination" align="left">
    <?php echo $pagination;?><!-- this is the number of records part - an example only -->
<span>Display <a href="<?php echo $pbase_url; ?>/10" title="Click here to list 10 records per page">10</a> | <a href="<?php echo$pbase_url; ?>/20" title="Click here to list 20 records per page">20</a> records per page</span>    
</div>
<br/>
<?php } ?>
<table  class="pickme sortable-onload-3 rowstyle-alt no-arrow mytables" id="striped2"> 
	<thead>
	<tr>
		<th width="20" class="center"><input type="checkbox" name="whatever" value="" onclick="CheckUncheckAll('<?php echo $this->form_name; ?>')" /></th> 
		<th width="2%" class="sortable">ID</th>
		<th align="left" width="36%" class="sortable-text">Page Name</th>
		<th align="left" width="35%" class="sortable-text">Page Parent</th>
		<th align="left" width="8%" class="sortable-date">Created</th>		
		<th width="8%">Action</th>
	</tr>	 	
	</thead>
	<tfoot> 
		<tr> 
			<th colspan="6" class="right"> 
				<label for="mass_action">With selected:</label> 
				<select style="text-transform:none!important;width:150px" name="mass_action" id="mass_action" class="element select"> 
					<option value="">Please select action</option> 					
					<option value="mass_delete" id="mass_delete">Delete</option> 
					<option value="mass_restore" id="mass_restore">Restore</option> 
				</select>			
				</th> 
		</tr> 
	</tfoot> 
	
	<tbody>
	<?php foreach($users_result as $key => $user){ ?>
	<tr onclick="lockRowUsingCheckbox();lockRow();">	
		<td><input type="checkbox" name="tablechoice[]" value="<?php echo $user['id']; ?>" /></td>
		<td class="left"><?php echo $user['id']; ?></td>
		<td class="left"><?php echo $user['page_name']; ?></td>
		<td class="left"><?php echo $all_pages[$user['page_parent_id']]; ?></td>
		<td class="left"><?php echo mysql_to_human($user['created'],'Y-m-d'); ?></td>
		<td align="center" valign="middle">
                <img src="<?php echo base_url(); ?>images/backend/page.jpg" title="Restore record with id: <?php echo $user['id']; ?>" alt="Restore record with id: <?php echo $user['id']; ?>" border="0" onclick="javascript:confirm_restore('<?php echo site_url($this->page_name."/restore/".$user['id']); ?>');" class="cursor_pointer" width="18" height="18"  />&nbsp;&nbsp;<img src="<?php echo base_url(); ?>images/backend/page_delete.jpg" title="Delete record with id: <?php echo $user['id']; ?>" alt="Edit record with id: <?php echo $user['id']; ?>" border="0" onclick="javascript:delete_perm('<?php echo site_url($this->page_name."/delete_permenantly/".$user['id']); ?>');" class="cursor_pointer" width="18" height="18"  />
            </td>
	</tr>	
	<?php }//end foreach ?>
	</tbody>
</table>

<?php if(!empty($pagination)){ ?>
<br/>
<div class="pagination" align="left">
    <?php echo $pagination;?><!-- this is the number of records part - an example only -->
<span>Display <a href="<?php echo $pbase_url; ?>/10" title="Click here to list 10 records per page">10</a> | <a href="<?php echo $pbase_url; ?>/20" title="Click here to list 20 records per page">20</a> records per page</span>    
</div>
<?php } ?>

<?php }else{ ?>
<a href="<?php echo site_url($this->page_name.'/add');?>" title="Click here to add an item"><img src="<?php echo base_url(); ?>images/backend/page_plus.jpg" alt="Click here to add an item" title="Click here to add an item" border="0" />&nbsp; Click here to add an record</a>
<?php }//end if	?>

<?php if ($total_rows_items > 0){ ?>
<br />
	<a href="<?php echo site_url($this->page_name);?>" title="Click here to list active records"><img src="<?php echo base_url(); ?>images/backend/view_art.gif" alt="Click here to list active records" title="Click here to list active records" border="0" align="right" /></a>
<?php } 
?>
<input type="hidden" name="mode" id="mode"/>
<input type="hidden" name="id" id="id"/>

<?
echo form_close();
?>

</div>
