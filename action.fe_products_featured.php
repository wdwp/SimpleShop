<?php

if (!isset($gCms)) exit;

$trans = cms_utils::get_module('TranslitAlias');

$detailpage = '';
if (isset($params['detailpage'])) {
	$manager = cmsms()->GetHierarchyManager();
	$node = $manager->sureGetNodeByAlias($params['detailpage']);
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

$pathproduct = $this->GetPreference('imagepath_product');

if (isset($params['sort']) && !empty($params['sort'])) {
	switch ($params['sort']) {
		case 'position':
			$sort = 'position';
			break;
		case 'price':
			$sort = 'price';
			break;
		default:
			$sort = 'product_id';
			break;
	}
} else {
	$sort = 'product_id';
}
$sort_order = (isset($params['sort_order']) && $params['sort_order'] == 'DESC') ? 'DESC' : 'ASC';

// Display information of the product
$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_products
	WHERE active = 1 AND featured = 1
	ORDER BY ' . $sort . ' ' . $sort_order;
$query2 = 'SELECT count(*) FROM ' . cms_db_prefix() . 'module_sms_products
	WHERE active = 1 AND featured = 1
	ORDER BY position';
// Set the page hyperlinks if needed
$pagelimit = $this->GetPreference('productpagelimit', 100000);

if (isset($params['prodpagelimit'])) {
	$pagelimit = intval($params['prodpagelimit']);
}

// Get the number of rows (so we can determine the numer of pages)
$pagecount = -1;
$startelement = 0;
$pagenumber = 1;
// Get the total number of items that match the query
// and determine a number of pages
$row2 = $db->GetRow($query2);
$count = intval($row2['count(*)']);

if (isset($params['start'])) {
	$count -= (int)$params['start'];
}
$pagecount = (int)($count / $pagelimit);
if (($count % $pagelimit) != 0) $pagecount++;

if (isset($params['pagenumber']) && $params['pagenumber'] != '') {
	// If given a page number, determine a start element
	$pagenumber = (int)$params['pagenumber'];
	$startelement = ($pagenumber - 1) * $pagelimit;
}
if (isset($params['start'])) {
	// Given a start element, determine a page number
	$startelement = $startelement + (int)$params['start'];
}
if ($startelement == $pagelimit) {
	// This happens when there are less results then one page. Reset to starting position.
	#$startelement = 0;
}
// Assign some pagination variables to smarty
if ($pagenumber == 1) {
	$smarty->assign('prevpage', $this->Lang('prevpage'));
	$smarty->assign('firstpage', $this->Lang('firstpage'));
} else {
	$params['pagenumber'] = $pagenumber - 1;
	$smarty->assign('prevpage', $this->CreateFrontendLink($id, $returnid, 'fe_products_featured', $this->Lang('prevpage'), $params));
	$params['pagenumber'] = 1;
	$smarty->assign('firstpage', $this->CreateFrontendLink($id, $returnid, 'fe_products_featured', $this->Lang('firstpage'), $params));
}

if ($pagenumber >= $pagecount) {
	$smarty->assign('nextpage', $this->Lang('nextpage'));
	$smarty->assign('lastpage', $this->Lang('lastpage'));
} else {
	// Make sure next page is selected (also if one product per page)
	$params['pagenumber'] = $pagenumber + 1;
	$smarty->assign('nextpage', $this->CreateFrontendLink($id, $returnid, 'fe_products_featured', $this->Lang('nextpage'), $params));
	$params['pagenumber'] = $pagecount;
	$smarty->assign('lastpage', $this->CreateFrontendLink($id, $returnid, 'fe_products_featured', $this->Lang('lastpage'), $params));
}

$smarty->assign('pagenumber', $pagenumber);
$smarty->assign('pagecount', $pagecount);
$smarty->assign('oftext', $this->Lang('prompt_of'));
$smarty->assign('pagetext', $this->Lang('prompt_page'));

$dbresult = '';
if ($pagelimit < 100000 || $startelement > 0) {
	$dbresult = $db->SelectLimit($query, $pagelimit, $startelement);
} else {
	$dbresult = $db->Execute($query);
}

$row = array();
$rowclass = 'row1';
$entryarray = array();

while ($dbresult && $row = $dbresult->FetchRow()) {
	$onerow = new stdClass();
	$onerow->prodid = $row['product_id'];

	if ($trans) {
		$aliased_title = $trans->Translit($row['name']);
	} else {
		$aliased_title = munge_string_to_url($row['name']);
	}

	$prettyurl = 'SimpleShop/prod/' . $row['product_id'] . '/' .
		($detailpage != '' ? $detailpage : $returnid) . '/' . $aliased_title;
	if (isset($sendtodetail['detailtemplate'])) {
		$prettyurl .= '/d,' . $sendtodetail['detailtemplate'];
	}
	$sendtodetail = array('product_id' => $row['product_id']);
	$onerow->prodname = $this->CreateLink(
		$id,
		'fe_product_detail',
		$detailpage != '' ? $detailpage : $returnid,
		$row['name'],
		$sendtodetail,
		'',
		false,
		false,
		'',
		true,
		$prettyurl
	);

	$onerow->proddesc = strip_tags($row['description']);
	if ($CartMS) {
		$onerow->price = $CartMS->orders->FormatAmount($row['price']);
	} else {
		$onerow->price = $this->FormatPrice($row['price']);
	}
	$onerow->netweight = $this->FormatWeight($row['netweight']);
	$onerow->sku = $row['sku'];

	// Check if inventory in place. If so don't show any add to cart option
	$inventorytype = $this->GetPreference('inventorytype');
	if ($inventorytype == 'prod' && $row['maxattributes'] > 0 || $inventorytype == 'none') {
		// Build link to cart
		if ($CartMS) {

			$query = "SELECT category_id FROM " . cms_db_prefix() . "module_sms_product_category WHERE product_id = ?";
			$category_id = $db->GetOne($query, array($row['product_id']));

			$prettyurl = 'SimpleCart/addproduct/' . $category_id . '/' .
				$row['product_id'] . '/0/1' . '/' . ($detailpage != '' ? $detailpage : $returnid);
			$onerow->addproduct = $CartMS->CreateLink(
				$id,
				'cart',
				$returnid,
				$this->Lang('addtocart'),
				array(
					'name' => $row['name'],
					'perfaction' => 'add_product',
					'product_id' => $row['product_id'],
					'qty' => 1,
					'returnmod' => 'SimpleShop'
				),
				'',
				false,
				true,
				'',
				true,
				$prettyurl
			);
			// Prepare link with image (Feat Req#3324)
			$imagesrc = $config['root_url'] . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR .
				$this->GetName() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'addtocart.gif';
			$onerow->addproductimage = $CartMS->CreateLink(
				$id,
				'cart',
				$returnid,
				'<img src="' . $imagesrc . '" alt="' . $this->Lang('addtocart') . '" title="' . $this->Lang('addtocart') . '">',
				array(
					'name' => $row['name'],
					'perfaction' => 'add_product',
					'product_id' => $row['product_id'],
					'qty' => 1,
					'returnmod' => 'SimpleShop'
				)
			);
		}
	}

	// Get the first picture available for the product found
	$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_product_images WHERE product_id = ?';
	$picture = $db->GetRow($query, array($row['product_id']));
	if ($picture) {
		if (isset($picture['image']) && $picture['image'] != 'no_image.jpg') {
			$onerow->prodimage = $pathproduct . $picture['image'];
			$onerow->prodimagelink = $this->CreateLink(
				$id,
				'fe_product_detail',
				$returnid,
				'<img src=\'' . $config['root_url'] . '/uploads/images' . $pathproduct . 'tn_' . $picture['image'] .
					'\' title=\'' . $picture['description'] . '\' >',
				array('product_id' => $row['product_id'])
			);
		} else {
			$this->smarty->assign('prodimage', '*none');
		}
	}

	$onerow->rowclass = $rowclass;

	$entryarray[] = $onerow;

	($rowclass == "row1" ? $rowclass = "row2" : $rowclass = "row1");
}

$this->smarty->assign('products', $entryarray);
$this->smarty->assign('lable_product_count', $this->Lang('textproductcount', count($entryarray)));

// State the currency symbol and that prices are in/ex VAT/TAX
$inexvat = (int) $this->GetPreference('pricesinclvat', 0);
if ($inexvat != 0) {
	$this->smarty->assign('currency', $this->Lang('pricesincurinvat', $this->GetPreference('default_currency')));
} else {
	$this->smarty->assign('currency', $this->Lang('pricesincurexvat', $this->GetPreference('default_currency')));
}
$this->smarty->assign('cur_symbol', $this->GetPreference('default_symbol'));

// Display template
$template = 'prodfeat_template';
// Currently only one template available, so next lines will not perform anything
if (isset($params['prodfeattemplate'])) {
	$template = $params['prodfeattemplate'];
}
echo $this->ProcessTemplateFromDatabase($template);
