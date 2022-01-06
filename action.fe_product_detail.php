<?php

$gCms = cmsms();
if (!is_object($gCms)) exit;

$detailpage = '';
if (isset($params['detailpage'])) {
	$manager = cmsms()->GetHierarchyManager();
	$node = &$manager->sureGetNodeByAlias($params['detailpage']);
	if (isset($node)) {
		$content = &$node->GetContent();
		if (isset($content)) {
			$detailpage = $content->Id();
		}
	} else {
		$node = &$manager->sureGetNodeById($params['detailpage']);
		if (isset($node)) {
			$detailpage = $params['detailpage'];
		}
	}
}

// Prepare settings for usage of cart
$modops = cmsms()->GetModuleOperations();
$CartMS = $modops->get_module_instance('SimpleCart');

$pathcat = $this->GetPreference('imagepath_category');
$pathproduct = $this->GetPreference('imagepath_product');

// Save the product id for correct usage throughout all queries
$product_id = '';
if (isset($params['product_id'])) $product_id = $params['product_id'];

if (isset($params['category_id'])) {
	$category_id = $params['category_id'];
} else {
	$query = "SELECT category_id FROM " . cms_db_prefix() . "module_sms_product_category WHERE product_id = ?";
	$category_id = $db->GetOne($query, array($product_id));
}
// Display information of the product
$query = "SELECT * FROM " . cms_db_prefix() . "module_sms_products WHERE product_id = ?";
$row = $db->GetRow($query, array($product_id));

if ($row) {
	$trans = cms_utils::get_module('TranslitAlias');
	if ($trans) {
		$aliased_title = $trans->Translit($row['name']);
	} else {
		$aliased_title = munge_string_to_url($row['name']);
	}
	$canonical = $config['root_url'] . '/SimpleShop/prod/' . $row['product_id'] . '/' .
		($detailpage != '' ? $detailpage : $returnid) . '/' . $aliased_title . $config['page_extension'];

	$smarty->assign('canonical', $canonical);

	$productname = $row['name'];
	$productprice = $row['price'];
	$quantityonstock = $row['maxattributes'];
	$weightunitmeasure = $this->GetPreference('weightunitmeasure', 'gr');
	$smarty->assign('id', $product_id);
	$smarty->assign('productname', $row['name']);
	$smarty->assign('description', $row['description']);
	if ($CartMS) {
		$smarty->assign('price', $CartMS->orders->FormatAmount($row['price']));
	} else {
		$smarty->assign('price', $this->FormatPrice($row['price']));
	}
	$smarty->assign('sku', $row['sku']);
	$smarty->assign('itemnumber', $row['itemnumber']);
	$smarty->assign('netweight', $this->FormatWeight($row['netweight']) . ' ' . $weightunitmeasure);
	// Get the first picture available for the product found
	$query = "SELECT * FROM " . cms_db_prefix() . "module_sms_product_images WHERE product_id = ?";
	$picture = $db->GetRow($query, array($product_id));
	if ($picture) {
		if (isset($picture['image']) && $picture['image'] != 'no_image.jpg') {
			$smarty->assign('prodimage', $pathproduct . $picture['image']);
			$smarty->assign('imagedesc', $row['description']);
		} else {
			$smarty->assign('prodimage', '*none');
		}
	}
	// When the {image} tag of smarty is not in use, the complete path must be passed
	if (isset($picture['image'])) {
		$smarty->assign('pimage', $config['root_url'] . DIRECTORY_SEPARATOR . 'uploads' .
			DIRECTORY_SEPARATOR . 'images' . $pathproduct . $picture['image']);
	}
	// Build array that holds the images connected to the product
	$dbresult = $db->Execute($query, array($product_id));
	$entryarray = array();
	while ($dbresult && $row = $dbresult->FetchRow()) {
		$onerow = new stdClass();
		if (isset($picture['image']) && $picture['image'] != 'no_image.jpg') {
			$onerow->image = $pathproduct . $row['image'];
			$onerow->imagethumb = $pathproduct . 'tn_' . $row['image'];
			$onerow->imagedesc = $row['description'];
			$onerow->fullpathimage = $config['root_url'] . DIRECTORY_SEPARATOR . 'uploads' .
				DIRECTORY_SEPARATOR . 'images' . $pathproduct . $row['image'];
		}

		$entryarray[] = $onerow;
	}
	$smarty->assign('items', $entryarray);
	$smarty->assign('itemcount', count($entryarray));
	// Check if inventory in place. If so don't show any add to cart option
	$onstock = 'No';
	$inventorytype = $this->GetPreference('inventorytype', 'none');
	if ($inventorytype == 'prod' && $quantityonstock > 0 || $inventorytype == 'none') {
		$onstock = 'Yes';
	}
	if ($onstock == 'Yes') {
		// Build link to cart
		if ($CartMS) {
			$smarty->assign('addproduct', $CartMS->CreateLink(
				$id,
				'cart',
				$returnid,
				$this->Lang('addtocart'),
				array(
					'name' => $productname,
					'perfaction' => 'add_product',
					'category_id' => $category_id,
					'product_id' => $product_id,
					'qty' => 1,
					'returnmod' => 'SimpleShop'
				)
			));
			$imagesrc = $this->ImageAddToCartSource();
			$smarty->assign('addproductimage', $CartMS->CreateLink(
				$id,
				'cart',
				$returnid,
				'<img src="' . $imagesrc . '" alt="' . $this->Lang('addtocart') . '" title="' . $this->Lang('addtocart') . '">',
				array(
					'name' => $productname,
					'perfaction' => 'add_product',
					'category_id' => $category_id,
					'product_id' => $product_id,
					'qty' => 1,
					'returnmod' => 'SimpleShop'
				)
			));
		}
	}

	$product_data = $this->products->Get($product_id);
	$attribute = array();
	$attributes = array();
	$product_attributes_list = array();
	// Check if there are any attributes for the current product. If not checked, the foreach runs into an error
	$attributes['itemcount'] = isset($product_data['attributes']) ? count($product_data['attributes']) : 0;
	if ($attributes['itemcount'] > 0) {
		foreach ($product_data['attributes'] as $attribute) {
			if ($attribute['active']) {
				$attribute['name'] = $attribute['name'];
				$attribute['description'] = strip_tags($attribute['description']);
				// Check if inventory in place. If so don't show any add to cart option
				$attribute['onstock'] = true;
				$inventorytype = $this->GetPreference('inventorytype');
				if (($inventorytype == 'attr' && $attribute['maxallowed'] > 0)
					|| $onstock == 'Yes'
				) {
					$attribute['price'] = '';
					$price = $this->CalculateAttributePrice($productprice, $attribute['priceadjusttype'], $attribute['priceadjustment']);
					if ($CartMS) {
						$attribute['price'] = $CartMS->orders->FormatAmount($price);
					} else {
						$attribute['price'] = $this->FormatPrice($price);
					}

					if (!$attribute['displayonly']) {

						$inventorytype = $this->GetSMSPreference('inventorytype', 'none');
						if ($inventorytype == 'attr') {
							$attribute_qty = ($attribute['minallowed'] > 0) ? $attribute['minallowed'] : 1;
						} else {
							$attribute_qty = 1;
						}

						// Build link to cart
						if ($CartMS) {
							$attribute['addattribute'] = $CartMS->CreateLink(
								$id,
								'cart',
								$returnid,
								$this->Lang('addtocart'),
								array(
									'name' => $product_data['name'],
									'perfaction' => 'add_product', 'category_id' => $category_id,
									'product_id' => $product_id, 'attribute_id' => $attribute['attribute_id'],
									'qty' => $attribute_qty, 'returnmod' => 'SimpleShop'
								)
							);
							$imagesrc = $this->ImageAddToCartSource();
							$attribute['addattributeimage'] = $CartMS->CreateLink(
								$id,
								'cart',
								$returnid,
								'<img src="' . $imagesrc . '" alt="' . $this->Lang('addtocart') . '" title="' . $this->Lang('addtocart') . '">',
								array(
									'name' => $product_data['name'],
									'perfaction' => 'add_product', 'category_id' => $category_id,
									'product_id' => $product_id, 'attribute_id' => $attribute['attribute_id'],
									'qty' => $attribute_qty, 'returnmod' => 'SimpleShop'
								)
							);
						}
					}
				} else {
					$attribute['price'] = $this->Lang('productsoldout');
					$attribute['onstock'] = false;
					$attribute['addattribute'] = '';
				}
				$product_attributes_list[] = $attribute;
			}
		}
		$attributes['list'] = $product_attributes_list;
		// Now all the attributes are found, connect them for the front end
		$attributes['namecolumn'] = $this->Lang('column_attribute_name');
		$attributes['descriptioncolumn'] = $this->Lang('column_attribute_description');
		$attributes['pricecolumn'] = $this->Lang('column_attribute_price');
		$attributes['addattributecolumn'] = '';
		$smarty->assign('attributes', $attributes);
	}
}

$cur_symbol = $this->GetPreference('default_symbol');
$smarty->assign('cur_symbol', $this->GetPreference('default_symbol'));

// Display template
$template = 'proddetail_template';
// Currently only one template available, so next lines will not perform anything
if (isset($params['proddetailtemplate'])) {
	$template = $params['proddetailtemplate'];
}
echo $this->ProcessTemplateFromDatabase($template);
