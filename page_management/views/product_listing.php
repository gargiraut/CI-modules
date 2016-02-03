
<script  type='text/javascript' src='<?php echo base_url(); ?>js/modal.js'></script>
<script type="text/javascript" src="<?php echo base_url(); ?>js/addtocart.js"></script>
<script type="text/javascript">
//------------------------------------------------------------------------------------
 var openMyModal = function(source)
    {
        modalWindow.windowId = "myModal";
        randnum = Math.random(); 
        modalWindow.width = 700;
        modalWindow.height = 700;
        modalWindow.content = "<iframe id='"+randnum+"' width='480' height='237' frameborder='0' scrolling='no' allowtransparency='true' src='" + source + "'><\/iframe>";
        modalWindow.open();
    }; 
</script>
<div id="prod_listing">
<?php
$cnt = 1; 
if(is_array($product) && count($product)>0)
{
	for($i=0;$i<count($product);$i++)
	{		
		$class1='';
		$class2='';
		if($cnt > 4)
			$class1 = 'padding_top15';
		
		if($cnt%4 == 0)
			$class2 = 'border_right0';	

		/* if($cnt > 4)
			$class3 = 'border_bottom0';
		else */
			$class3 = '';
		
		if(!empty($product[$i]['id']))
		{ 
			if($product[$i]["special"] == '0' && $product[$i]["special_price"] !='' && $product[$i]["special_price"] != '0')
			{
			$price	= $product[$i]["product_price"];
			}
			else
			{
				$price	= $product[$i]["special_price"];
			}	
	
			/*if(strlen($product[$i]["product_name"]) > 18)
				$product_name	= substr($product[$i]["product_name"],0,18)."...";
			else*/
				$product_name	= $product[$i]["product_name"];
				
					
			//-------------CONDITION FOR SEF URL ---------------------------
				$sef_url_indicator = sef_switch_action();
				if($sef_url_indicator)
				{
					$sef_url = site_url($product[$i]["product_name"]);
				}
				else
				{
					$sef_url = site_url('product/preview/'.$product[$i]['id']);
				}
			//-------------CONDITION FOR SEF URL ---------------------------	
	
?>
  <div class="prod_div <?php echo $class1 ?> <?php echo $class2 ?> <?php echo $class3?>">
		<div style="padding: 0pt 15px; height: 35px; overflow:hidden;">
			
			<a class="prod_heading_frontend" href="<?php if( grant_access('easfv935') ) echo $sef_url; else echo "javascript:access_denied();"; ?>">
						<?php echo $product_name?>
			</a>
		</div>
		<div class="wraptocenter"><span>&nbsp;</span>
				<a href="<?php if( grant_access('easfv935') ) echo $sef_url; else echo "javascript:access_denied();"; ?>">
			<?php
				if(check_file_exists($image_path . 'medium/' . $product[$i]['image1']))
				{
			?>
					<img src="<?php echo base_url(); ?>media/images/product/medium/<?php echo $product[$i]['image1'] ?>" alt="<?php echo ucfirst($product[$i]["product_name"])?>" title="<?php echo ucfirst($product[$i]["product_name"])?>" class="mar_auto" />
			<?php 
				}
				else
				{ 
			?>
					<img src="<?php echo base_url(); ?>images/frontend/no_image_thumb.gif" alt="<?php echo ucfirst($product[$i]["product_name"])?>" title="<?php echo ucfirst($product[$i]["product_name"])?>" class="prod_img" />
			<?php
				} 
			?>
				</a>
		</div>
		<div class="padding_price">
			<span class="prod_price">Price : </span>
				<span class="prod_price_dollars">
					<b id="product_price_<?php echo $product[$i]['id'];?>">$<?php echo $price?></b>
					<input type="hidden" name="product_org_price_<?php echo $product[$i]['id']?>" id="product_org_price_<?php echo $product[$i]['id']?>" value="<?php echo $price?>"/>
				</span>
		</div>
		<div class="prod_details_tab">
			<div class="view_prod">
				<a href="<?php if( grant_access('easfv935') ) echo $sef_url; else echo "javascript:access_denied();"; ?>">
				<img height="23" border="0" width="42" title="View product 'Bracelet 04'" alt="View product 'Bracelet 04'" class="cursor_pointer" src="<?php echo base_url(); ?>images/frontend/view.jpg"/>	 				</a>
			</div>
		<?php
			if($product[$i]["hide_viewcart"] == 0)
			{
		?>
			<div class="qtytext">Qty :&nbsp;</div>
			<div class="add_cart">
				<div class="sc_info">
					<div class="add_float" id="scinfo<?php echo $product[$i]["id"]?>"></div>
				</div>
				<div class="cart_img">
					<img height="23" border="0" align="top" width="67" title="Add '<?php echo $product_name?>' to Cart" alt="Add '<?php echo $product_name?>' to Cart" class="cursor_pointer" src="<?php echo base_url(); ?>images/frontend/add_to_bag.gif" <?php if( grant_access('easfv935') ){ ?>onclick="call('<?php echo $product[$i]["id"]?>','<?php echo base_url()?>');" <?php }else echo "onclick='javascript:access_denied();'"; ?>  />
				</div>
				<div class="qty">          
						<input type="text" value="1" maxlength="3" class="qty_text" name="prod<?php echo $product[$i]["id"]?>" id="prod<?php echo $product[$i]["id"]?>"/>
				</div>
			</div>
		<?php
			}//END OF if($product[$i]["hide_viewcart"] == 0)
			else
			{
		?>
				<div style="float:right;width:78px;">
						<img border="0" align="top" title="Enquire About <?php echo $product_name?>" alt="Enquire About <?php echo $product_name?>" class="cursor_pointer" src="<?php echo base_url(); ?>images/frontend/enquire.gif" onclick="javascript: product_enquire(<?php echo $product[$i]["id"]?>,'<?php echo site_url()?>');" />
				</div>
		<?php
			}
		?>
		</div>
		<div class="sc_info">
						<div class="add_float" id="wishlist<?php echo $product[$i]['id']?>"></div>
				</div>
		<p class="row">
		
			<a href="<?php echo $sef_url."#product_add_review"?>" title="Add review" class="ico_review">Add review</a> <span class="sep">|</span>
			<?php
			if(!empty($_SESSION['memberid']) && ($_SESSION['memberid'] > 0)) 
			 {
			   $function_call  = "add_wishlist('".$product[$i]['id']."','".site_url()."');";
		   }
			 else
			 {
					$function_call  = "openMyModal('".site_url('member/member_login')."');";
		   }
			?>
			<a href="javascript:<?php echo $function_call;?>" title="Add <?php echo $product_name?> to Wishlist" class="ico_wl">Add to Wishlist</a>

		</p>
			
  </div>
	<?php 
		}//END OF if(!empty($product[$i]['id']))
	$cnt++; 
	}//END OF for($i=0;$i<count($product);$i++) 
  }//END OF if(is_array($product) && count($product)>0)
 ?>
<div style="clear: both;"></div>

</div>
