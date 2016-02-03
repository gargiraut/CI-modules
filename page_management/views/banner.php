<?php if (isset($banner_image)){ ?>
<a href="javascript:void(0);"  onclick="javascript:update_click('<?php echo site_url('page_management/ajax_call/update_clicks'); ?>','banner_id=<?php echo $banner_id; ?>','<?php echo $banner_url; ?>');" ><img src="<?php echo base_url()."media/images/banner/medium/". $banner_image ?>" border="0" title="<?php $banner_title ?>" alt="View image" /></a>
<?php } ?>