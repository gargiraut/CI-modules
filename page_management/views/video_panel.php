<?php
	$query 	= "	SELECT
							*,
							DATE_FORMAT(created,'%D %M %Y') AS CREATED_DATE
						FROM 
							".TBL_VIDEOGALLERY."
						WHERE
							video_convert 	= 1 AND
							status			= 1 AND
							featured		= 1 AND
							video			<> ''
				ORDER BY `created` DESC 
				LIMIT 4
			";										
		$result = $this->db->query($query);
		$i 		= 0;
		$j		= 0;
		if($result->num_rows > 0)
		{
		
		
?>
<div class="video">
  <div class="video_top h2_heading padding_h2"><a class="text_heading" href="<?php echo site_url('videogallery'); ?>">Videos</a></div>
	  <div class="video_bg video_margin">
<?php
				foreach($result->result_array() as $videos){
					if($i >0 && $i%2 == 0)
						echo '</div><div class="video_bg video_divider"><img src="'.base_url().'images/frontend/horizontal_divider.gif" alt="" title="" /></div><div class="video_bg video_margin">';	

					if($j == 0){
						$j		= 1;
						$class	= "video_blk1";
					}
					else{
						$j	= 0;
						$class	= "video_blk2";
					}	
/*					style="background-image:url(<?=base_url()?>/media/videos/jpg_1679091c5a880faf6fb5e6087eb1b2dc.jpg);"
					<img src="<?php echo base_url(); ?>images/video1.jpg" alt="" border="0"  title="" />
*/
					if(strlen($videos["desc"]) > 180)
							$videos_desc	= substr(nl2br($videos["desc"]),0,180)."...";
						else
							$videos_desc	= nl2br($videos["desc"]);
							
					//$video_image	= "jpg_".str_replace('.mp4','',$videos['video']).".jpg";//"jpg_1679091c5a880faf6fb5e6087eb1b2dc.jpg";
					$video_image = "http://i4.ytimg.com/vi/".substr($videos['video'],strpos($videos['video'],"watch?v=")+strlen("watch?v="),strlen($videos['video']))."/default.jpg";
//					echo substr($videos['video'],strpos($videos['video'],"watch?v=")+strlen("watch?v="),strlen($videos['video']));
	//				exit;
					// http://www.youtube.com/watch?v=ksN4-WK22Og
					//http://i4.ytimg.com/vi/ksN4-WK22Og/default.jpg
					//http://i4.ytimg.com/vi/ksN4-WK22Og/default.jpg
?>
	  
		<div class="<?php echo $class?>">
			<div class="prod_img" style="background-image:url(<?php echo $video_image?>);float:left; margin-right:15px;">
			<a href="<?php if( grant_access('easfv970') )echo site_url("videogallery/preview/".$videos['id']); else echo "javascript:access_denied();"; ?>"><img src="<?php echo base_url()?>/images/frontend/play_btn.gif" border="1" style="border: solid 1px #666666;position:" alt="<?php echo $videos['name']?>" title="<?php echo $videos['name']?>" /></a>
			</div>
		  <h3><a href="<?php  if( grant_access('easfv970') )echo site_url("videogallery/preview/".$videos['id']); else echo "javascript:access_denied();"; ?>" class="h3_heading"><?php echo ucfirst($videos['name'])?></a></h3>
		  <p class="date_text padding_b5"><?php echo $videos['CREATED_DATE']?></p>
		 <?php echo $videos_desc?><a href="<?php  if( grant_access('easfv970') )echo site_url("videogallery/preview/".$videos['id']); else echo "javascript:access_denied();"; ?>" title="Click here"><img src="<?php echo base_url(); ?>images/frontend/arrow.gif" alt="Click here" border="0"  title="Click here" hspace="5" /></a></div>
		
		
<?php
			$i++;
				}
?> 
</div>
  <div class="video_bottom"></div>
</div>
<?php
	}
?>
