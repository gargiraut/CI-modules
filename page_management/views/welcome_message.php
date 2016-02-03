<div><img src="<?php echo base_url(); ?>images/frontend/spacer.gif" alt="" title="" height="15" width="1" /></div>
<?php 
	if( grant_access('easfv935') )
		$this->load->view('product_listing');
		
?>
<div><img src="<?php echo base_url(); ?>images/frontend/spacer.gif" alt="" title="" height="8" width="1" /></div>
<div class="heading_bg">
  <h2 class="h2_heading padding_h2">Welcome to Engaging Concepts</h2>
</div>
<?php if(!empty($fck_data)) 
	{ 
		echo html_entity_decode($fck_data); ?> <br/><br/>
		<?php
	}
	else
	{	
?>
<div class="body_padding"> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eget lacus sit amet mi vehicula consectetur. Duis nec metus. Nam eget quam id nisi vestibulum tempus. Sed ultricies lectus sed ipsum. Duis vel ipsum. Quisque ultricies. Pellentesque volutpat quam nec nisi. Fusce enim lectus, ultricies nec, eleifend eu, placerat eu, arcu. Aliquam est nibh, mollis congue, tincidunt non, lobortis ut, odio. Vestibulum molestie convallis erat. Ut tortor. Nunc sed lacus. Quisque cursus lorem dignissim dolor aliquet porttitor. Vestibulum gravida leo in lacus. Ut id orci vitae quam ultricies pulvinar. Phasellus dictum vehicula diam. <br />
  <br />
  Nam tincidunt volutpat justo. Nulla non nibh. Nunc neque est, dignissim viverra, ornare sed, elementum in, sem. In in nisl vitae diam pellentesque aliquam. Proin vitae risus sit amet leo dapibus hendrerit. Quisque aliquet. Etiam varius enim sed tortor. Donec vel velit ac neque imperdiet consequat. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse vulputate convallis lacus. Cras aliquam. Maecenas mattis elementum nisi. Aenean ultrices magna ut ipsum. <br/>
  <br/>
  <br/>
  <p class="border_bottom"></p>
</div>
<?php 
	}
	if( grant_access('easfv942') )
		 {
		 	$this->load->view('news_article_panel');
	 ?>
<p class="border_bottom divider_padding"></p>
<?php } 	if( grant_access('easfv938') )
		 {
		 	 $this->load->view('testimonials_panel');
			 ?>
<p class="border_bottom divider_padding"></p>
<?php } if( grant_access('easfv970') )
			$this->load->view('video_panel');
			//include("video_panel.php"); ?>