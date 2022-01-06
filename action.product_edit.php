<?php
// Make it possible to use Inventory Management fields
$modops = cmsms()->GetModuleOperations();
$CartMS = $modops->get_module_instance('SimpleCart');
$DTI = $modops->get_module_instance('DTInventory');

$themeObject = \cms_utils::get_theme_object();

if (isset($params['cancel']) && $params['cancel'] == $this->Lang('cancel')) {
	$params = array(
		'active_tab' => 'categories',
		'current_category_id' => $params['current_category_id'],
		'tab_message' => $this->Lang('cancel')
	);
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
if (
	isset($params['submit']) && $params['submit'] == $this->Lang('update')
	|| isset($params['done'])
) {
	$errorfnd = false;
	$doubleitem = $this->products->CheckDoubleItemNumber($params['itemnumber'], $params['product_id']);
	if (!$this->GetPreference('allowdoubleitem', 0) && $doubleitem != false) {
		$params['message'] = $this->Lang('message_doubleitemnumberfound', $doubleitem);
		$errorfnd = true;
	}
	if (!isset($params['name']) ||  $params['name'] == '') {
		$params['message'] = $this->Lang('message_noattributenamegiven');
		$errorfnd = true;
	}

	if (!$errorfnd) {

		$params['name'] = trim($params['name']);
		$params['active'] = !empty($params['active']) ? $params['active'] : 0;
		$params['featured'] = !empty($params['featured']) ? $params['featured'] : 0;
		$params['maxattributes'] = !empty($params['maxattributes']) ? trim($params['maxattributes']) : 0;
		$params['position'] = !empty($params['position']) ? trim($params['position']) : 0;
		$params['price'] = !empty($params['price']) ? trim($params['price']) : 0;
		$params['netweight'] = !empty($params['netweight']) ? trim($params['netweight']) : 0;
		$params['description'] = !empty($params['description']) ? trim($params['description']) : '';
		$params['itemnumber'] = !empty($params['itemnumber']) ? trim($params['itemnumber']) : 0;
		$params['cost_price'] = !empty($params['cost_price']) ? trim($params['cost_price']) : 0;
		$params['barcode'] = !empty($params['barcode']) ? trim($params['barcode']) : '';

		if ($this->products->Update($params)) $params['message'] = $this->Lang('productedited');

		if ($DTI) {
			dti_utils::update_itemextension(
				-1,
				$params['cost_price'],
				'A',
				$params['barcode'],
				$params['product_id']
			);
		}
	}
	if (isset($params['done'])) {
		$params = array(
			'active_tab' => 'categories',
			'current_category_id' => $params['current_category_id']
		);
		$params['tab_message'] = $this->Lang('productedited');
		$this->Redirect($id, 'defaultadmin', $returnid, $params);
	}
	$params['submit'] = NULL;
	$params['tab_message'] = $params['message'];
	$this->Redirect($id, 'product_edit', $returnid, $params);
} else {
	if (isset($params['submit']) && $params['submit'] == $this->Lang('addimage')) {
		$this->Redirect($id, 'product_images', $returnid, $params);
	}
	if (isset($params['submit']) && $params['submit'] == $this->Lang('addattribute')) {
		$this->Redirect($id, 'product_attributes', $returnid, $params);
	}
	if (isset($params['submit']) && $params['submit'] == $this->Lang('addcategory')) {
		$this->products->CreateCategoryMap($params['category_id'], $params['product_id']);
		$params['active_tab'] = 'categories';
	}
	if (isset($params['submit']) && $params['submit'] == $this->Lang('deletecategory')) {
		$this->products->DeleteCategoryMap($params['category_id'], $params['product_id']);
		$params['active_tab'] = 'categories';
	}
	// All actions handled, now get all the information linked to the product
	if (isset($params['product_id'])) {
		$product_data = $this->products->Get($params['product_id']);
	}
}
$active_tab = 'product';
if (isset($params['active_tab'])) {
	$active_tab = $params['active_tab'];
}

/**
 * Prepare Product Tab
 */
$product = array();
$product['startform'] = $this->CreateFormStart($id, 'product_edit', $returnid);
$product['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('update'));
$product['cancel'] = $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel'));
$product['done'] = $this->CreateInputSubmit($id, 'done', $this->Lang('savereturn'));
$product['add_new'] = $this->CreateLink(
	$id,
	'product_add',
	'',
	$this->Lang('text_add') . ' new ' . $this->Lang('label_product'),
	array('current_category_id' => $params['current_category_id'])
);
$product['endform'] = $this->CreateFormEnd();

$params['current_category_id'] = isset($params['current_category_id']) ? $params['current_category_id'] : 0;

// Messages come from deletion (or other places). So show it.
$product['message'] = isset($params['message']) ? $params['message'] : '';
$product['current_category_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'current_category_id', $params['current_category_id'])
);
$product['product_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'product_id', $params['product_id'])
);

$product['name'] = array(
	'label' => $this->Lang('label_product_name'),
	'input' => $this->CreateInputText($id, 'name', $product_data['name'], 40, 255, 'required')
);
$product['description'] = array(
	'label' => $this->Lang('label_product_description'),
	'input' => $this->CreateTextArea(true, $id, $product_data['description'], 'pdescription', 'pagesmalltextarea', '', '', '', 40, 40)
);
$product['price'] = array(
	'label' => $this->Lang('label_product_price'),
	'input' => $this->CreateInputText($id, 'price', $this->FormatPrice($product_data['price']), 40, 40)
);
$product['active'] = array(
	'label' => $this->Lang('label_product_active'),
	'input' => $this->CreateInputCheckbox($id, 'active', 1, $product_data['active'])
);
$product['featured'] = array(
	'label' => $this->Lang('label_product_featured'),
	'input' => $this->CreateInputCheckbox($id, 'featured', 1, $product_data['featured'])
);
$product['netweight'] = array(
	'label' => $this->Lang('label_product_netweight'),
	'input' => $this->CreateInputText($id, 'netweight', $this->FormatWeight($product_data['netweight']), 10, 20),
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
		'input' => $this->CreateInputDropdown($id, 'vatcode', $listvatcode, $product_data['vatcode'], $vatcode)
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
	'input' => $this->CreateInputDropdown($id, 'sku', $skudropdown, -1, $product_data['sku'])
);

$product['itemnumber'] = array(
	'label' => $this->Lang('label_product_itemnumber'),
	'input' => $this->CreateInputText($id, 'itemnumber', $product_data['itemnumber'], 30, 30, 'required')
);

$product['position'] = array(
	'label' => $this->Lang('label_product_position'),
	'input' => $this->CreateInputText($id, 'position', $product_data['position'], 10, 10)
);
// Check if inventory in place. If so use different label
$inventorytype = $this->GetPreference('inventorytype');
if ($inventorytype == 'prod') {
	$label_product_maxattributes = $this->Lang('label_quantity_onstock');
	$tooltip_maxattributes = $this->Lang('tooltip_quantity_onstock');
} else {
	$label_product_maxattributes = $this->Lang('label_product_maxattributes');
	$tooltip_maxattributes = $this->Lang('tooltip_maxattributes');
}
$product['maxattributes'] = array(
	'label' => $label_product_maxattributes,
	'input' => $this->CreateInputText(
		$id,
		'maxattributes',
		$product_data['maxattributes'],
		10,
		10,
		'title="' . $tooltip_maxattributes . '"'
	)
);
if ($DTI) {
	$smarty->assign('DTIavailable', true);
	$DTInventoryversion = dti_utils::get_DTInventoryversion();
	$smarty->assign('DTInventoryversion', $DTInventoryversion);
	$item_ext_info = array();
	$item_ext_info = dti_utils::get_itemextension($params['product_id']);
	$label = dti_utils::get_fieldtitle('title_cost_price');
	$item_ext_info['barcode'] = isset($item_ext_info['barcode']) ? $item_ext_info['barcode'] : '';
	$item_ext_info['cost_price'] = isset($item_ext_info['cost_price']) ? $item_ext_info['cost_price'] : 0;

	$product['cost_price'] = array(
		'label' => $label,
		'input' => $this->CreateInputText($id, 'cost_price', $item_ext_info['cost_price'], 15, 15)
	);
	$label = dti_utils::get_fieldtitle('title_barcode');
	$product['barcode'] = array(
		'label' => $label,
		'input' => $this->CreateInputText($id, 'barcode', $item_ext_info['barcode'], 25, 25)
	);
	if ($DTInventoryversion != 'free') {
		$label = dti_utils::get_fieldtitle('title_item_class');
		$default_item_class = dti_utils::get_default_item_class();
		$product['item_class'] = array(
			'label' => $label,
			'input' => $this->CreateInputText($id, 'item_class', $item_ext_info['item_class'], 3, 3)
		);
	}
} else {
	$smarty->assign('DTIavailable', false);
}

/**
 * Prepare Categories Tab
 */
$categories['startform'] = $this->CreateFormStart($id, 'product_edit', $returnid, 'post', 'multipart/form-data');
//$categories['submit'] = $this->CreateInputSubmit ($id, 'submit', $this->Lang('done'));
$categories['cancel'] = $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel'));
$categories['done'] = $this->CreateInputSubmit($id, 'done', $this->Lang('savereturn'));
$categories['endform'] = $this->CreateFormEnd();

$categories['current_category_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'current_category_id', $params['current_category_id'])
);
$categories['product_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'product_id', $params['product_id'])
);

$category_list = array();

foreach ($product_data['categories'] as $category) {
	if ($category) {
		if (empty($category['name'])) {
			$category['name'] = $this->Lang('root');
		} else {
			$category['name'] = $category['name'];
		}
		$category['link_delete'] = $this->CreateLink(
			$id,
			'product_edit',
			$returnid,
			$themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('text_delete'), '', '', 'systemicon'),
			array(
				'category_id' => $category['category_id'],
				'product_id' => $product_data['product_id'],
				'current_category_id' => $params['current_category_id'],
				'submit' => $this->Lang('deletecategory')
			),
			$this->Lang('message_areyousureprodcat', $product_data['name'], $category['name'])
		);
		$category_list[] = $category;
	}
}
$categories['list'] = $category_list;

$categories['categories'] = array(
	'label' => $this->Lang('addcategory'),
	'submit' => $this->CreateInputSubmit($id, 'submit', $this->Lang('addcategory')),
	'list' => $this->CreateInputDropdown($id, 'category_id', $this->categories->BuildTotalList(), -1, '')
);

/**
 * Prepare Attributes Tab
 */
$attributes['startform'] = $this->CreateFormStart($id, 'product_edit', $returnid, 'post', 'multipart/form-data');
$attributes['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('addattribute'));
$attributes['cancel'] = $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel'));
$attributes['endform'] = $this->CreateFormEnd();

// Messages come from deletion (or other places). So show it.
if (isset($params['message'])) {
	$attributes['message'] = $params['message'];
}
// Prepare name of product so that can be shown above images. Is easy for user to see for which product one is busy
$attributes['prodname'] = array(
	'label' => $this->Lang('label_attributes_product_name'),
	'output' => $product_data['name'] . ' ' .
		$this->Lang('baseproductprice', $this->FormatPrice($product_data['price']))
);
$product['attributes']['name'] = isset($product['attributes']['name']) ? $product['attributes']['name'] : '';
$attributes['name'] = array(
	'label' => $this->Lang('column_attribute_name'),
	'input' => $this->CreateInputText($id, 'name', $product['attributes']['name'], 40, 40)
);
$attributes['description'] = array(
	'label' => $this->Lang('label_attribute_description'),
	'input' => $this->CreateInputText($id, 'adescription', '', '', 255, 'style="width:95%"')
);
// Prepare some default values for a new attribute
if (!isset($product['attributes']['attribute_id'])) {
	$product['attributes']['minallowed'] = '0';
	$product['attributes']['active'] = true;
	$product['attributes']['maxallowed'] = 1;
}
$attributes['minallowed'] = array(
	'label' => $this->Lang('label_attribute_minallowed'),
	'input' => $this->CreateInputText($id, 'minallowed', $product['attributes']['minallowed'], 5, 5)
);
// Check if inventory in place. If so use different label
if ($inventorytype == 'attr') {
	$label_attribute_maxallowed = $this->Lang('label_quantity_onstock');
} else {
	$label_attribute_maxallowed = $this->Lang('label_attribute_maxallowed');
}
$attributes['maxallowed'] = array(
	'label' => $label_attribute_maxallowed,
	'input' => $this->CreateInputText($id, 'maxallowed', $product['attributes']['maxallowed'], 5, 5)
);
// Prepare the various pricing adjustment types
$product['attributes']['priceadjusttype'] = isset($product['attributes']['priceadjusttype']) ? $product['attributes']['priceadjusttype'] : '';
$attributes['priceadjusttype'] = array(
	'label' => $this->Lang('label_attribute_priceadjusttype'),
	'input' => $this->CreateInputDropdown(
		$id,
		'priceadjusttype',
		$this->BuildListOfAdjustTypes(),
		-1,
		$product['attributes']['priceadjusttype']
	)
);
$product['attributes']['priceadjustment'] = isset($product['attributes']['priceadjustment']) ? $product['attributes']['priceadjustment'] : 0;
$attributes['priceadjustment'] = array(
	'label' => $this->Lang('label_attribute_priceadjustment'),
	'input' => $this->CreateInputText($id, 'priceadjustment', $this->FormatPrice($product['attributes']['priceadjustment']), 10, 40)
);
$product['attributes']['displayonly'] = isset($product['attributes']['displayonly']) ? $product['attributes']['displayonly'] : false;
$attributes['displayonly'] = array(
	'label' => $this->Lang('label_attribute_displayonly'),
	'input' => $this->CreateInputCheckbox($id, 'displayonly', 1, $product['attributes']['displayonly'])
);
$product['attributes']['itemnumber'] = isset($product['attributes']['itemnumber']) ? $product['attributes']['itemnumber'] : '';
$attributes['itemnumber'] = array(
	'label' => $this->Lang('column_attribute_itemnumber'),
	'input' => $this->CreateInputText($id, 'itemnumber', $product['attributes']['itemnumber'], 30, 30)
);
$attributes['active'] = array(
	'label' => $this->Lang('label_attribute_active'),
	'input' => $this->CreateInputCheckbox($id, 'active', 1, $product['attributes']['active'])
);
$attributes['current_category_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'current_category_id', $params['current_category_id'])
);
$attributes['product_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'product_id', $params['product_id'])
);
// Build list of available attributes
$attributes['namecolumn'] = $this->Lang('column_attribute_name');
$attributes['descriptioncolumn'] = $this->Lang('column_attribute_description');
$attributes['adjustmentcolumn'] = $this->Lang('column_attribute_adjustment');
$attributes['pricecolumn'] = $this->Lang('column_attribute_price');
$attributes['displaycolumn'] = $this->Lang('column_attribute_display');
$attributes['activecolumn'] = $this->Lang('active');
$attributes['minallowedcolumn'] = $this->Lang('column_attribute_minallowed');
// Check if inventory in place. If so use different label
if ($inventorytype == 'attr') {
	$attributes['maxallowedcolumn'] = $this->Lang('label_onstock');
} else {
	$attributes['maxallowedcolumn'] = $this->Lang('column_attribute_maxallowed');
}
$attributes['itemnumbercolumn'] = $this->Lang('column_attribute_itemnumber');
$attributes['no_attributes_available'] = $this->Lang('no_attributes_available');
$desclength = 80;
$product_attribute_list = array();
// Check if there are any attributes for the current product. If not checked, the foreach runs into an error
$params['current_attribute_id'] = isset($params['current_attribute_id']) ? $params['current_attribute_id'] : NULL;
$attributes['itemcount'] = (isset($product_data['attributes']) && is_array($product_data['attributes'])) ? count($product_data['attributes']) : 0;
if ($attributes['itemcount'] > 0) {
	foreach ($product_data['attributes'] as $attribute) {
		$attributename = $attribute['name'];
		$attribute['name'] = $this->CreateLink(
			$id,
			'product_attribute_edit',
			$returnid,
			$attributename,
			array(
				'current_attribute_id' => $params['current_attribute_id'],
				'current_category_id' => $params['current_category_id'],
				'prodname' => $product_data['name'],
				'attribute_id' => $attribute['attribute_id']
			)
		);
		if (strlen($attribute['description']) > $desclength) {
			$attribute['description'] = substr($attribute['description'], 0, $desclength) . '...';
		} else {
			$attribute['description'] = $attribute['description'];
		}
		if ($attribute['displayonly']) {
			$attribute['adjustment'] = '---';
			$attribute['price'] = '---';
			$attribute['minallowed'] = '---';
			$attribute['maxallowed'] = '---';
		} else {
			switch ($attribute['priceadjusttype']) {
				case 'P':
					$attribute['adjustment'] = '+ ' . $attribute['priceadjustment'];
					break;
				case 'M':
					$attribute['adjustment'] = '- ' . $attribute['priceadjustment'];
					break;
				case 'T':
					$attribute['adjustment'] = '* ' . $attribute['priceadjustment'];
					break;
				case 'V':
					$attribute['adjustment'] = '';
					break;
			}
			$attribute['price'] = $this->FormatPrice($this->CalculateAttributePrice(
				$product_data['price'],
				$attribute['priceadjusttype'],
				$attribute['priceadjustment']
			));
		}
		$attribute['link_display'] = $this->CreateLink(
			$id,
			'switchstatus',
			$returnid,
			$attribute['displayonly'] == 0 ? $themeObject->DisplayImage(
				'icons/system/true.gif',
				$this->Lang('text_nocalculation'),
				'',
				'',
				'systemicon'
			) : $themeObject->DisplayImage('icons/system/false.gif', $this->Lang('text_calculation'), '', '', 'systemicon'),
			array(
				'table' => 'AttributesDisplay', 'displayonly' => $attribute['displayonly'], 'attribute_id' => $attribute['attribute_id'],
				'product_id' => $params['product_id'], 'current_category_id' => $params['current_category_id']
			)
		);
		$attribute['link_enable'] = $this->CreateLink(
			$id,
			'switchstatus',
			$returnid,
			$attribute['active'] == 1 ? $themeObject->DisplayImage(
				'icons/system/true.gif',
				$this->Lang('text_inactive'),
				'',
				'',
				'systemicon'
			) : $themeObject->DisplayImage('icons/system/false.gif', $this->Lang('text_active'), '', '', 'systemicon'),
			array(
				'table' => 'Attributes', 'active' => $attribute['active'], 'attribute_id' => $attribute['attribute_id'],
				'product_id' => $params['product_id'], 'current_category_id' => $params['current_category_id']
			)
		);
		$attribute['link_edit'] = $this->CreateLink(
			$id,
			'product_attribute_edit',
			$returnid,
			$themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('text_edit'), '', '', 'systemicon'),
			array(
				'current_attribute_id' => $params['current_attribute_id'],
				'current_category_id' => $params['current_category_id'],
				'prodname' => $product_data['name'],
				'attribute_id' => $attribute['attribute_id']
			)
		);
		$attribute['link_delete'] = $this->CreateLink(
			$id,
			'product_attribute_delete',
			$returnid,
			$themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('text_delete'), '', '', 'systemicon'),
			array(
				'attribute_id' => $attribute['attribute_id'],
				'product_id' => $attribute['product_id'],
				'current_category_id' => $params['current_category_id'],
				'name' => $attributename
			),
			$this->Lang('message_areyousureattribute', $attributename)
		);
		$product_attributes_list[] = $attribute;
	}
	$attributes['list'] = $product_attributes_list;
}
/**
 * Prepare Images Tab
 */
$images['startform'] = $this->CreateFormStart($id, 'product_images', $returnid, 'post', 'multipart/form-data');
$images['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('addimage'));
$images['cancel'] = $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel'));
$images['endform'] = $this->CreateFormEnd();

// Messages come from deletion (or other places). So show it.
$images['message'] = isset($images['message']) ? $images['message'] : '';
// Prepare name of product so that can be shown above images. Is easy for user to see for which product one is busy
$images['name'] = array(
	'label' => $this->Lang('label_image_product_name'),
	'output' => $product_data['name']
);
$images['image'] = array(
	'label' => $this->Lang('column_image_name'),
	'input' => $this->CreateInputFile($id, 'image', '', 40)
);
$images['description'] = array(
	'label' => $this->Lang('label_image_description'),
	'input' => $this->CreateInputText($id, 'idescription', $product_data['name'], '', 255, 'style="width:95%"')
);
$images['current_category_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'current_category_id', $params['current_category_id'])
);
$images['product_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'product_id', $params['product_id'])
);
// Build list of available images
$images['namecolumn'] = $this->Lang('column_image_name');
$images['descriptioncolumn'] = $this->Lang('column_image_description');
$images['no_images_available'] = $this->Lang('no_images_available');

$product_image_list = array();
// Check if there are any images for the current product. If not checked, the foreach runs into an error
$images['itemcount'] = 0;
if (isset($product_data['images'])) $images['itemcount'] = count($product_data['images']);
if ($images['itemcount'] > 0) {
	foreach ($product_data['images'] as $image) {

		$params['current_product_images_id'] = isset($params['current_product_images_id']) ? $params['current_product_images_id'] : NULL;

		$image['productimage'] = $config['image_uploads_url'] . '/' . $this->GetPreference('imagepath_product') . $image['image'];
		$image['imageheight'] = '70'; // <-- Should be moved into a preference
		$image['image'] = $image['image'];
		$image['description'] = $image['description'];
		$image['link_edit'] = $this->CreateLink(
			$id,
			'product_image_edit',
			$returnid,
			$themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('text_edit'), '', '', 'systemicon'),
			array(
				'current_product_images_id' => $params['current_product_images_id'],
				'current_category_id' => $params['current_category_id'],
				'product_images_id' => $image['product_images_id']
			)
		);
		$image['link_delete'] = $this->CreateLink(
			$id,
			'product_image_delete',
			$returnid,
			$themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('text_delete'), '', '', 'systemicon'),
			array(
				'product_images_id' => $image['product_images_id'],
				'product_id' => $image['product_id'],
				'current_category_id' => $params['current_category_id'],
				'image' => $image['image']
			),
			$this->Lang('message_areyousureimage', $image['image'])
		);
		$product_images_list[] = $image;
	}
	$images['list'] = $product_images_list;
}

/**
 * Show pages
 */
echo $this->StartTabHeaders();
echo $this->SetTabHeader('product', $this->Lang('label_tab_product'), ($active_tab == 'product'));
echo $this->SetTabHeader('categories', $this->Lang('label_tab_product_categories'), ($active_tab == 'categories'));
echo $this->SetTabHeader('attributes', $this->Lang('label_tab_attributes'), ($active_tab == 'attributes'));
echo $this->SetTabHeader('images', $this->Lang('label_tab_images'), ($active_tab == 'images'));
echo $this->EndTabHeaders();

echo $this->StartTabContent();
echo "\n<!-- From product -->\n";
echo $this->StartTab('product', $params);
$smarty->assign('product', $product);
echo $this->ProcessTemplate('product_edit.tpl');
echo $this->EndTab();

echo "\n<!-- From categories -->\n";
echo $this->StartTab('categories', $params);
$smarty->assign('categories', $categories);
echo $this->ProcessTemplate('product_categories.tpl');
echo $this->EndTab();

echo "\n<!-- From attributes -->\n";
echo $this->StartTab('attributes', $params);
$smarty->assign('attributes', $attributes);
echo $this->ProcessTemplate('product_attributes.tpl');
echo $this->EndTab();

echo "\n<!-- From images -->\n";
echo $this->StartTab('images', $params);
$smarty->assign('images', $images);
echo $this->ProcessTemplate('product_images.tpl');
echo $this->EndTab();

echo $this->EndTabContent();
