<div class="heading_bg">
  <div class="pagetitle">
    <h2 class="h2_heading padding_h2"><?php echo $page_title_tag ?></h2>	
  </div>  
  <div class="bread">
	<span class="link2">You are here : </span>
	  <h2 align="right" class="link_breadcrum display_inline">
       <a class="link_breadcrum" href="<?php echo base_url();?>">EASFv9</a>
       	 <?php 
		 	if(isset($bread_crumb)) 
			 {
				if(is_array($bread_crumb) && count($bread_crumb)>0)
				{
					foreach($bread_crumb as $page_name)
					{
			?>
				         <span class="breadcrumb_span">  > </span> 
			        <span class="link_breadcrum">
					  <?php 
					 $page_temp_name = explode('~',$page_name);
					if($page_temp_name[0] == $page_title_tag)
					{
					echo (strlen($page_temp_name[1])<7)?$page_temp_name[1]:substr($page_temp_name[1],0,7).'...';
                    }
					else
					{
					?>
                   <a class="link_breadcrum" href="<?php echo site_url($page_temp_name[0]);?>" title="<?php echo $page_temp_name[1]?>"><?php echo (strlen($page_temp_name[1])<7)?$page_temp_name[1]:substr($page_temp_name[1],0,7).'...'; ?></a>
                    <?php
                    }
					?></span></h2>
               <?php
					}
				}
			}
			else
			{		
			 ?>
				  <span class="breadcrumb_span"> &raquo; </span> 
				  <!--<span class="link_breadcrum"><?php //echo $page_title_tag; ?></span>-->
         		  <span class="link_breadcrum"><?php echo $page_head; ?></span>
			<?php
			  }
			?>
	 </div>
</div>
<div class="body_padding_dynamic"  >
<?php echo html_entity_decode($fck_data,ENT_NOQUOTES,'UTF-8'); 

	if(isset($version_date))
		echo '<br/>Date Modified : '.date('d-M-Y',strtotime($version_date)); 
	else
		echo '<br/>Date Modified : '.date('d-M-Y',strtotime($modified)); 
	?>
</div>