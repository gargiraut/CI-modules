<div class="video">
  <div class="video_top h2_heading padding_h2"><a class="text_heading" href="<? echo site_url('news'); ?>">News Articles</a></div>
  <div class="video_bg video_margin">
  <?php
  	$n = 1;
  	$CI 	= & get_instance();		
		$reserved_keyword  	   = $CI->config->item('reserved_keyword');
		$reserved_url	   			= $reserved_keyword['news'];
	$query 	= "	SELECT id,news_heading,news_content ,created,news_sef_url
				FROM ".TBL_NEWS." 
				WHERE status = 1 
				ORDER BY `created` DESC 
				LIMIT 4						
			";										
		$result = $CI->db->query($query);
		
		$sef_url_indicator = sef_switch_action();
				
		if($result->num_rows > 0)
		{
			foreach($result->result_array() as $root)
			{   
				$char = array("&nbsp;</p>", "?", "&nbsp;", "&nbsp");
				$root["news_content"] = str_replace($char,'',$root["news_content"]);
				$root['news_content'] = html_entity_decode($root['news_content']);
				if($sef_url_indicator)
				{
					$sef_url = site_url($reserved_url.'/'.$root['news_sef_url']);
				}
				else
				{
					$sef_url = site_url('news/preview/'.$root['id']);
				}
				if($n < 4) {  
	?>	
<div class="panel_blk1"><h3><a href="<?php if( grant_access('easfv942') ) echo $sef_url; else echo "javascript:access_denied();"; ?>" class="h3_heading"><?php echo character_limiter(strip_tags($root['news_heading']),15)?></a></h3>
<p class="date_text padding_b5"><?php echo date('dS F Y',strtotime($root['created']));?></p>
<?php echo character_limiter(strip_tags($root['news_content']),100)?><a href="<?php if( grant_access('easfv942') )echo $sef_url; else echo "javascript:access_denied();"; ?>" title="Click here"><img src="<?php echo base_url(); ?>images/frontend/arrow.gif" alt="Click here" border="0"  title="Click here" hspace="5" /></a></div>
	 <?php
	 			}				
	 			else{
	?>			
<div class="panel_blk4"><h3><a href="<?php if( grant_access('easfv942') )echo $sef_url; else echo "javascript:access_denied();"; ?>" class="h3_heading"><?php echo character_limiter(strip_tags($root['news_heading']),15)?></a></h3>
<p class="date_text padding_b5"><?php echo date('dS F Y',strtotime($root['created']));?></p>
<?php echo character_limiter(strip_tags($root['news_content']),100)?><a href="<?php if( grant_access('easfv942') )echo $sef_url; else echo "javascript:access_denied();"; ?>" title="Click here"><img src="<?php echo base_url(); ?>images/frontend/arrow.gif" alt="Click here" border="0"  title="Click here" hspace="5" /></a></div>
			<?php
				}
				$n++;
			}
			
		}
	?>			
		
  </div>
  <div class="video_bottom"></div>
</div>
