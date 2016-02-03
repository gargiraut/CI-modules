<div class="video">
  <div class="video_top h2_heading padding_h2"><a class="text_heading" href="<? echo site_url('testimonials'); ?>">Testimonials</a></div>
  <div class="video_bg video_margin">
   <?php
  	$n = 1;
  	$CI 	= & get_instance();		
	$query 	= "	SELECT id,testimonial_company,testimonial_description ,created
				FROM ".TBL_TESTIMONIALS." 
				WHERE status = 1 
				ORDER BY `created` DESC 
				LIMIT 4						
			";										
		$result = $CI->db->query($query);
		if($result->num_rows > 0)
		{
			foreach($result->result_array() as $root)
			{
				if($n < 4) { 
	?>		
	<div class="panel_blk1"><h3><a href="<?php if( grant_access('easfv938') ) echo site_url('testimonials'); else echo "javascript:access_denied();"; ?>" class="h3_heading"><?php echo character_limiter(strip_tags($root['testimonial_company']),15)?></a></h3>
      <p class="date_text padding_b5"><?php echo date('dS F Y',strtotime($root['created']));?></p>
      <?php echo character_limiter(strip_tags($root['testimonial_description']),100)?><a href="<?php if( grant_access('easfv938') ) echo site_url('testimonials'); else echo "javascript:access_denied();"; ?>" title="Click here"><img src="<?php echo base_url(); ?>images/frontend/arrow.gif" alt="Click here" border="0"  title="Click here" hspace="5" /></a></div>
	  <?php
	 			}				
	 			else{
	?>	
	 
    <div class="panel_blk4"><h3><a href="<?php if( grant_access('easfv938') ) echo site_url('testimonials'); else echo "javascript:access_denied();"; ?>" class="h3_heading"><?php echo character_limiter(strip_tags($root['testimonial_company']),15)?></a></h3>
      <p class="date_text padding_b5"><?php echo date('dS F Y',strtotime($root['created']));?></p>
      <?php echo character_limiter(strip_tags($root['testimonial_description']),100)?><a href="<?php if( grant_access('easfv938') ) echo site_url('testimonials'); else echo "javascript:access_denied();"; ?>" title="Click here"><img src="<?php echo base_url(); ?>images/frontend/arrow.gif" alt="Click here" border="0"  title="Click here" hspace="5" /></a></div>
	 <?php
				}
				$n++;
			}
			
		}
	?>			 
  </div>
  <div class="video_bottom"></div>
</div>
