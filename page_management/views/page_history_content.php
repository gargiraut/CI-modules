<style type="text/css">
.module_notes {border:2px solid #658CB3; background-color:#FFFFFF;background-color:#FFFFFF;height:530px;margin:0;overflow:auto;position:relative}
</style>

<div class="module_notes">

<img alt="Close" title="Close Window" class="close-window" src="<?php echo basE_url();?>/images/backend/close_pop.png" onclick="self.parent.modalWindow.close();" style="cursor:pointer;position:absolute;right:0;"/>
		<p class="info_head">Page Content for <?php echo isset($page_data['page_head'])?ucfirst($page_data['page_head']):'';?></p>
					<div style="margin-left:15px;">
        <?php 
				if(is_array($page_data) && count($page_data)>0)
				{
					   echo html_entity_decode($page_data['page_html_data'],ENT_NOQUOTES,'UTF-8');  
					
				}//END OF if(is_array($notes_history) && count($notes_history)>0)
			?>
			
		</div>
	</div>
