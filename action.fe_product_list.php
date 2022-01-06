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

$config = $gCms->GetConfig();
$pathcat = $this->GetPreference('imagepath_category');
$pathproduct = $this->GetPreference('imagepath_product');

// Display information of the category
$query = "SELECT * FROM " . cms_db_prefix() . "module_sms_categories WHERE category_id = ? AND active = 1";
$row = $db->GetRow($query, array($params['category_id']));

if ($row) {

	if ($trans) {
		$cat_name = $trans->Translit($row['name']);
	} else {
		$cat_name = munge_string_to_url($row['name']);
	}
	$canonical = $config['root_url'] . '/SimpleShop/cat/' . $row['category_id'] . '/' . ($detailpage != '' ? $detailpage : $returnid) . '/' . $cat_name . $config['page_extension'];

	$this->smarty->assign('canonical', $canonical);
	$this->smarty->assign('id', $row['category_id']);
	$this->smarty->assign('categoryname', $row['name']);
	$this->smarty->assign('description', $row['description']);
	if (isset($row['image']) && $row['image'] != 'no_image.jpg' && $row['image'] != '') {
		$this->smarty->assign('image', $pathcat . $row['image']);
	} else {
		$this->smarty->assign('image', '*none');
	}
}

if (isset($params['sort']) && !empty($params['sort'])) {
	switch ($params['sort']) {
		case 'position':
			$sort = 'p.position';
			break;
		case 'price':
			$sort = 'p.price';
			break;
		default:
			$sort = 'p.product_id';
			break;
	}
} else {
	$sort = 'p.product_id';
}
$sort_order = (isset($params['sort_order']) && $params['sort_order'] == 'DESC') ? 'DESC' : 'ASC';

// Display information of the connected products
$query = 'SELECT p.name AS prodname, p.product_id, p.description AS proddesc,
	p.price AS price, p.netweight, p.maxattributes FROM '
	. cms_db_prefix() . 'module_sms_product_category c LEFT OUTER JOIN '
	. cms_db_prefix() . 'module_sms_products p ON c.product_id = p.product_id
	WHERE c.category_id = ? AND p.active = 1
	ORDER BY ' . $sort . ' ' . $sort_order;

$query2 = 'SELECT count(*) FROM '
	. cms_db_prefix() . 'module_sms_product_category c LEFT OUTER JOIN '
	. cms_db_prefix() . 'module_sms_products p ON c.product_id = p.product_id
	WHERE c.category_id = ? AND p.active = 1';

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
$row2 = $db->GetRow($query2, array($params['category_id']));
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
	$smarty->assign('prevpage', $this->CreateFrontendLink($id, $returnid, 'fe_product_list', $this->Lang('prevpage'), $params));
	$params['pagenumber'] = 1;
	$smarty->assign('firstpage', $this->CreateFrontendLink($id, $returnid, 'fe_product_list', $this->Lang('firstpage'), $params));
}

if ($pagenumber >= $pagecount) {
	$smarty->assign('nextpage', $this->Lang('nextpage'));
	$smarty->assign('lastpage', $this->Lang('lastpage'));
} else {
	// Make sure next page is selected (also if one product per page) (Bug#: 3887)
	$params['pagenumber'] = $pagenumber + 1;
	$smarty->assign('nextpage', $this->CreateFrontendLink($id, $returnid, 'fe_product_list', $this->Lang('nextpage'), $params));
	$params['pagenumber'] = $pagecount;
	$smarty->assign('lastpage', $this->CreateFrontendLink($id, $returnid, 'fe_product_list', $this->Lang('lastpage'), $params));
}

$smarty->assign('pagenumber', $pagenumber);
$smarty->assign('pagecount', $pagecount);
$smarty->assign('oftext', $this->Lang('prompt_of'));
$smarty->assign('pagetext', $this->Lang('prompt_page'));

$dbresult = '';
if ($pagelimit < 100000 || $startelement > 0) {
	$dbresult = $db->SelectLimit($query, $pagelimit, $startelement, array($params['category_id']));
} else {
	$dbresult = $db->Execute($query, array($params['category_id']));
}

$rowclass = 'row1';
$entryarray = array();
// Retrieve the type of inventory that is in place
$inventorytype = $this->GetPreference('inventorytype', 'none');

while ($dbresult && $row = $dbresult->FetchRow()) {
	$onerow = new stdClass();

	$onerow->prodid = $row['product_id'];

	if ($trans) {
		$aliased_title = $trans->Translit($row['prodname']);
	} else {
		$aliased_title = munge_string_to_url($row['prodname']);
	}
	$sendtodetail = array('category_id' => $params['category_id'], 'product_id' => $row['product_id']);

	$prettyurl = 'SimpleShop/prod/' . $row['product_id'] . '/' .
		($detailpage != '' ? $detailpage : $returnid) . '/' . $aliased_title;
	if (isset($sendtodetail['detailtemplate'])) {
		$prettyurl .= '/d,' . $sendtodetail['detailtemplate'];
	}
	$onerow->prodname = $this->CreateLink(
		$id,
		'fe_product_detail',
		$detailpage != '' ? $detailpage : $returnid,
		$row['prodname'],
		$sendtodetail,
		'',
		false,
		false,
		'',
		true,
		$prettyurl
	);
	// Show name without link to detail on product listing (feature request# 2986)
	$onerow->prodnamenolink = $row['prodname'];
	$onerow->proddesc = strip_tags($row['proddesc']);
	$onerow->price = $row['price'];
	if ($CartMS) {
		$onerow->price = $CartMS->orders->FormatAmount($row['price']);
	} else {
		$onerow->price = $this->FormatPrice($row['price']);
	}
	$onerow->netweight = $this->FormatWeight($row['netweight']);
	// Check if inventory in place. If so show add to cart options
	if ($inventorytype == 'prod' && $row['maxattributes'] > 0 || $inventorytype == 'none') {
		$onerow->availablestock = $row['maxattributes'];
		// Build link to cart
		if ($CartMS) {

			// Prepare pretty URL to add product to cart
			$prettyurl = 'SimpleCart/addproduct/' . $params['category_id'] . '/' .
				$row['product_id'] . '/0/1' . '/' . ($detailpage != '' ? $detailpage : $returnid);
			$sendtodetail = array(
				'name' => $row['prodname'], 'perfaction' => 'add_product',	'category_id' => $params['category_id'],
				'product_id' => $row['product_id'],	'qty' => 1,	'returnmod' => 'SimpleShop'
			);
			// Prepare a hyperlink to add the product to the cart
			$linkname = $this->Lang('addtocart');
			$onerow->addproduct = $CartMS->CreateLink(
				$id,
				'cart',
				$returnid,
				$linkname,
				$sendtodetail,
				'',
				false,
				true,
				'',
				true,
				$prettyurl
			);
			// Prepare link with image (Feat Req#3324)
			$imagesrc = cms_join_path(
				$config['root_url'],
				'modules',
				$this->GetName(),
				'images',
				'addtocart.gif'
			);
			$onerow->addproductimage = $CartMS->CreateLink(
				$id,
				'cart',
				$returnid,
				'<img src="' . $imagesrc . '" alt="' . $this->Lang('addtocart') . '" title="' . $this->Lang('addtocart') . '">',
				$sendtodetail,
				'',
				false,
				true,
				'',
				true,
				$prettyurl
			);
		}
		$onerow->onstock = true;
	} else {
		$onerow->onstock = false;
		// $onerow->price = $this->Lang('productsoldout');
	}

	// Get the first picture available for the product found
	$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_product_images WHERE product_id = ?';
	$picture = $db->GetRow($query, array($row['product_id']));
	if ($picture) {
		if (isset($picture['image']) && $picture['image'] != 'no_image.jpg' && $picture['image'] != '') {
			$onerow->prodimage = $pathproduct . 'tn_' . $picture['image'];
			if (isset($picture['description'])) {
				$onerow->imagedesc = strip_tags($picture['description']);
			} else {
				$onerow->imagedesc = $row['prodname'];
			}
			// For feature request# 3083: prepare also a link from image to detail
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
$template = 'catlist_template';
// Currently only one template available, so next lines will not perform anything
if (isset($params['catlisttemplate'])) {
	$template = $params['catlisttemplate'];
}
echo $this->ProcessTemplateFromDatabase($template);
