<?php
// This program is called from product_edit, when button add attribute is used
if (isset($params['submit']) && $params['submit'] == $this->Lang('addattribute')) {
	$errorfnd = false;
	$doubleitem = $this->products->CheckDoubleItemNumber(trim($params['itemnumber']), -1);
	if (!$this->GetPreference('allowdoubleitem', 0) && $doubleitem != false) {
		$params['message'] = $this->Lang('message_doubleitemnumberfound', $doubleitem);
		$errorfnd = true;
	}
	if (!isset($params['name']) ||  $params['name'] == '') {
		$params['message'] = $this->Lang('message_noattributenamegiven');
		$errorfnd = true;
	}

	if (!$errorfnd) {
		// Now that all info is known, insert a record in the attributes database
		$this->products->CreateAttribute($params);
		// Prepare a message to be shown on top of the attributes overview
		$params['message'] = $this->Lang('message_attributeadded', $params['name']);
	}
} else {
	$params['message'] = $this->Lang('message_noattributetosave');
}
// Redirect back to attributes, so we can see the result
$params = array(
	'product_id' => $params['product_id'],
	'current_category_id' => $params['current_category_id'],
	'active_tab' => 'attributes',
	'tab_message' => $params['message']
);
$this->Redirect($id, 'product_edit', $returnid, $params);


if (isset($params['cancel']) && $params['cancel'] == $this->Lang('cancel')) {
	$params = array(
		'active_tab' => 'categories',
		'current_category_id' => $params['current_category_id']
	);
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
