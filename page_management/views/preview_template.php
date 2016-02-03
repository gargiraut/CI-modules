<form method="post" action="" name="" id="" class="login">
										<fieldset class="active" style="width:700px">
											<ul>
												<li>
													<span>
															<label class="field_width"><span class="required">&nbsp;</span> ID</label>
													</span>
													<span>
															<label><?php echo $id; ?></label>
													</span>
												</li>
												<li>
													<span>
															<label class="field_width"><span class="required">&nbsp;</span>Page Head</label>
													</span>
													<span>
															<label><?php echo $page_head; ?></label>
													</span>
												</li>
												<li>
													<span>
															<label class="field_width"><span class="required">&nbsp;</span>Page Content</label>
													</span>
													<span>
															<label><?php echo html_entity_decode($page_html_data,ENT_NOQUOTES,'UTF-8'); ?></label>
													</span>
												</li>
												<li>
													<span>
															<label class="field_width"><span class="required">&nbsp;</span><strong>Created Date</strong></label>
													</span>
													<span>
															<label><?php echo date_when(strtotime($created)); ?></label>
													</span>
												</li>
											</ul>	
										</fieldset>	
									</form>	