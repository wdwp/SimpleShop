<?php
// Make it possible to use Inventory Management fields
$modops = cmsms()->GetModuleOperations();
$CartMS = $modops->get_module_instance('SimpleCart');
$DTI = $modops->get_module_instance('DTInventory');

if (isset($params['submit'])) {
	$doubleitem = $this->products->CheckDoubleItemNumber(trim($params['itemnumber']), -1);
	if (!$this->GetPreference('allowdoubleitem', 0) && $doubleitem != false) {
		$this->smarty->assign('message', $this->Lang('message_doubleitemnumberfound', $doubleitem));
	} elseif (!isset($params['name']) ||  $params['name'] == '') {
		$this->smarty->assign('message', $this->Lang('message_noattributenamegiven'));
	} else {

		$params['name'] = trim($params['name']);
		$params['category_id'] = isset($params['category_id']) ? $params['category_id'] : 0;
		$params['active'] = !empty($params['active']) ? $params['active'] : 0;
		$params['featured'] = !empty($params['featured']) ? $params['featured'] : 0;
		$params['maxattributes'] = !empty($params['maxattributes']) ? trim($params['maxattributes']) : 1;
		$params['position'] = !empty($params['position']) ? trim($params['position']) : 0;
		$params['price'] = !empty($params['price']) ? trim($params['price']) : 0;
		$params['netweight'] = !empty($params['netweight']) ? trim($params['netweight']) : 0;
		$params['description'] = !empty($params['description']) ? trim($params['description']) : '';
		$params['itemnumber'] = !empty($params['itemnumber']) ? trim($params['itemnumber']) : 0;
		$params['cost_price'] = !empty($params['cost_price']) ? trim($params['cost_price']) : 0;
		$params['barcode'] = !empty($params['barcode']) ? trim($params['barcode']) : '';

		// User pressed continue so save the product information and after that the connection to the category
		$params['product_id'] = $this->products->Create($params);

		$this->products->CreateCategoryMap($params['category_id'], $params['product_id']);
		if ($DTI) {
			dti_utils::insert_itemextension(
				$params['product_id'],
				$params['cost_price'],
				$params['item_class'],
				$params['barcode']
			);
		}

		$params = array(
			'active_tab' => 'categories', 'current_category_id' => $params['category_id'],
			'product_id' => $params['product_id']
		);
		$params['tab_message'] = $this->Lang('productadded');

		$this->Redirect($id, 'product_edit', $returnid, $params);
	}
}

if (isset($params['cancel'])) {
	$params = array(
		'active_tab' => 'categories', 'current_category_id' => $params['current_category_id'],
		'tab_message' => $this->Lang('cancel')
	);
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

$product = array();
$product['startform'] = $this->CreateFormStart($id, 'product_add', $returnid, 'post');
$product['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('continue'));
$product['cancel'] = $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel'));
$product['endform'] = $this->CreateFormEnd();

$product['current_category_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'current_category_id', $params['current_category_id'])
);

$categorydropdown = $this->categories->BuildList($params['current_category_id'], '', 0);
$product['category_id'] = array(
	'label' => $this->Lang('label_product_category_id'),
	'input' => $this->CreateInputDropdown($id, 'category_id', $categorydropdown, -1, intval($params['current_category_id']))
);

$params['name'] = isset($params['name']) ? $params['name'] : '';

$product['name'] = array(
	'label' => $this->Lang('label_product_name'),
	'input' => $this->CreateInputText($id, 'name', $params['name'], 40, 255)
);
$product['description'] = array(
	'label' => $this->Lang('label_product_description'),
	'input' => $this->CreateTextArea(true, $id, '', 'description', 'pagesmalltextarea', '', '', '', 40, 40)
);
$product['price'] = array(
	'label' => $this->Lang('label_product_price'),
	'input' => $this->CreateInputText($id, 'price', '', 40, 40)
);
$product['active'] = array(
	'label' => $this->Lang('label_product_active'),
	'input' => $this->CreateInputCheckbox($id, 'active', true, true)
);
$product['featured'] = array(
	'label' => $this->Lang('label_product_featured'),
	'input' => $this->CreateInputCheckbox($id, 'featured', true)
);
$product['netweight'] = array(
	'label' => $this->Lang('label_product_netweight'),
	'input' => $this->CreateInputText($id, 'netweight', '', 10, 20),
	'unit' => $this->GetPreference('weightunitmeasure', 'Kg')
);
// Prepare list of possible VAT codes as set up in Cart Made Simple
if ($CartMS) {
	$vatcode = '';
	$listvatcode[$CartMS->GetPreference('vat0name', '')] = '0';
	$listvatcode[$CartMS->GetPreference('vat1name', '')] = '1';
	$listvatcode[$CartMS->GetPreference('vat2name', '')] = '2';
	$listvatcode[$CartMS->GetPreference('vat3name', '')] = '3';
	$listvatcode[$CartMS->GetPreference('vat4name', '')] = '4';
	$product['vatcode'] = array(
		'label' => $this->Lang('label_product_vatcode'),
		'input' => $this->CreateInputDropdown($id, 'vatcode', $listvatcode, -1, $vatcode)
	);
} else {
	$product['vatcode'] = array(
		'label' => $this->Lang('label_product_vatcode'),
		'input' => $this->CreateInputText($id, 'vatcode', '', 1, 1)
	);
}

$skudropdown = $this->BuildListSKU();
$product['sku'] = array(
	'label' => $this->Lang('label_product_sku'),
	'input' => $this->CreateInputDropdown($id, 'sku', $skudropdown, -1, $this->GetPreference('default_sku', ''))
);

$product['itemnumber'] = array(
	'label' => $this->Lang('label_product_itemnumber'),
	'input' => $this->CreateInputText($id, 'itemnumber', '', 30, 30)
);

$product['position'] = array(
	'label' => $this->Lang('label_product_position'),
	'input' => $this->CreateInputText($id, 'position', '', 10, 10)
);
// Check if inventory in place. If so use different label
$inventorytype = $this->GetPreference('inventorytype');
if ($inventorytype == 'prod') {
	$label_product_maxattributes = $this->Lang('label_quantity_onstock');
} else {
	$label_product_maxattributes = $this->Lang('label_product_maxattributes');
}
$product['maxattributes'] = array(
	'label' => $label_product_maxattributes,
	'input' => $this->CreateInputText($id, 'maxattributes', $this->GetPreference('default_maxattributes', 1), 10, 10)
);
if ($DTI) {
	$smarty->assign('DTIavailable', true);
	$DTInventoryversion = dti_utils::get_DTInventoryversion();
	$smarty->assign('DTInventoryversion', $DTInventoryversion);
	$label = dti_utils::get_fieldtitle('title_cost_price');
	$product['cost_price'] = array(
		'label' => $label,
		'input' => $this->CreateInputText($id, 'cost_price', '', 15, 15)
	);
	$label = dti_utils::get_fieldtitle('title_barcode');
	$product['barcode'] = array(
		'label' => $label,
		'input' => $this->CreateInputText($id, 'barcode', '', 25, 25)
	);
	if ($DTInventoryversion != 'free') {
		$label = dti_utils::get_fieldtitle('title_item_class');
		$default_item_class = dti_utils::get_default_item_class();
		$product['item_class'] = array(
			'label' => $label,
			'input' => $this->CreateInputText($id, 'item_class', $default_item_class, 3, 3)
		);
	}
} else {
	$smarty->assign('DTIavailable', false);
}

$smarty->assign('product', $product);
echo $this->ProcessTemplate('product_add.tpl');
