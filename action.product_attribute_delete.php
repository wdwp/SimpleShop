<?php
// This program is called from product_edit, when icon delete attribute is used

$this->products->DeleteAttribute($params);
$params = array(
	// Product id, always handy to pass. In this case needed to get all the connected information
	'product_id' => $params['product_id'],
	// Pass the current category otherwise user will be brought to root once cancel is pressed
	'current_category_id' => $params['current_category_id'],
	// Prepare a message to be shown on top of the images overview
	'tab_message' => $this->Lang('message_attributedeleted', $params['name']),
	// Redirect back to images, so we can see the result
	'active_tab' => 'attributes'
);
$this->Redirect($id, 'product_edit', $returnid, $params);
