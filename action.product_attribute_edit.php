<?php
// This program allows the user to change attribute information
if (isset($params['cancel']) && $params['cancel'] == $this->Lang('cancel')) {
	$this->Redirect($id, 'product_edit', $returnid, array(
		'tab_message' => $this->Lang('cancel'),
		'product_id' => $params['product_id'],
		'active_tab' => 'attributes',
		'current_category_id' => $params['current_category_id']
	));
}

if (isset($params['submit']) && $params['submit'] == $this->Lang('submit')) {
	$errorfnd = false;
	$doubleitem = $this->products->CheckDoubleItemNumber($params['itemnumber'], $params['attribute_id']);
	if (!$this->GetPreference('allowdoubleitem', 0) && $doubleitem != false) {
		$params['message'] = $this->Lang('message_doubleitemnumberfound', $doubleitem);
		$errorfnd = true;
	}
	if (!isset($params['name']) ||  $params['name'] == '') {
		$params['message'] = $this->Lang('message_noattributenamegiven');
		$errorfnd = true;
	}

	if (!$errorfnd) {
		$this->products->UpdateAttribute($params);
	}
	$params['active_tab'] = 'attributes';
	$params['tab_message'] = $this->Lang('attributeupdated');
	$this->Redirect($id, 'product_edit', $returnid, $params);
} else {
	// Retrieve information for passed attribute id. Array will hold information afterwards
	if (isset($params['attribute_id'])) {
		$product_attribute_data = $this->products->GetAttribute($params['attribute_id']);
	}
}
// Prepare next screen information
$attribute = array();
$attribute['startform'] = $this->CreateFormStart($id, 'product_attribute_edit', $returnid, 'post', '');
$attribute['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('submit'));
$attribute['cancel'] = $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel'));
$attribute['endform'] = $this->CreateFormEnd();

$attribute['prodname'] = array(
	'label' => $this->Lang('label_attribute_product_name'),
	'output' => $params['prodname']
);
$attribute['name'] = array(
	'label' => $this->Lang('column_attribute_name'),
	'input' => $this->CreateInputText($id, 'name', $product_attribute_data['name'], 40, 40)
);
$attribute['description'] = array(
	'label' => $this->Lang('label_attribute_description'),
	'input' => $this->CreateInputText($id, 'description', $product_attribute_data['description'], 40, 255)
);
$attribute['minallowed'] = array(
	'label' => $this->Lang('label_attribute_minallowed'),
	'input' => $this->CreateInputText($id, 'minallowed', $product_attribute_data['minallowed'], 5, 5)
);
// Check if inventory in place. If so use different label
$inventorytype = $this->GetPreference('inventorytype');
if ($inventorytype == 'attr') {
	$label_attribute_maxallowed = $this->Lang('label_quantity_onstock');
} else {
	$label_attribute_maxallowed = $this->Lang('label_attribute_maxallowed');
}
$attribute['maxallowed'] = array(
	'label' => $label_attribute_maxallowed,
	'input' => $this->CreateInputText($id, 'maxallowed', $product_attribute_data['maxallowed'], 5, 5)
);

// Prepare the various pricing adjustment types
$attribute['priceadjusttype'] = array(
	'label' => $this->Lang('label_attribute_priceadjusttype'),
	'input' => $this->CreateInputDropdown(
		$id,
		'priceadjusttype',
		$this->BuildListOfAdjustTypes(),
		-1,
		$product_attribute_data['priceadjusttype']
	)
);
$attribute['priceadjustment'] = array(
	'label' => $this->Lang('label_attribute_priceadjustment'),
	'input' => $this->CreateInputText($id, 'priceadjustment', number_format($product_attribute_data['priceadjustment'], 2, ".", ""), 10, 40)
);
$attribute['displayonly'] = array(
	'label' => $this->Lang('label_attribute_displayonly'),
	'input' => $this->CreateInputCheckbox($id, 'displayonly', 1, $product_attribute_data['displayonly'])
);
$attribute['itemnumber'] = array(
	'label' => $this->Lang('label_attribute_itemnumber'),
	'input' => $this->CreateInputText($id, 'itemnumber', $product_attribute_data['itemnumber'], 30, 30)
);
$attribute['active'] = array(
	'label' => $this->Lang('label_attribute_active'),
	'input' => $this->CreateInputCheckbox($id, 'active', 1, $product_attribute_data['active'])
);

// Supporting fields
$attribute['attribute_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'attribute_id', $params['attribute_id'])
);
$attribute['current_category_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'current_category_id', $params['current_category_id'])
);
$attribute['product_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'product_id', $product_attribute_data['product_id'])
);

// Build the template to show
$smarty->assign('attribute', $attribute);
echo $this->ProcessTemplate('product_attribute_edit.tpl');
