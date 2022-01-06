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

$parentcategory = '0';
if (isset($params['parentcategory'])) {
	$parentcategory = $params['parentcategory'];
}

// Select all active categories and show them
foreach ($this->categories->GetList($parentcategory) as $category) {
	if ($category['active'] == 1) {
		$onerow = new stdClass();
		// $onerow->catname = $this->CreateLink($id, 'fe_product_list', $detailpage!=''?$detailpage:$returnid,
		// 	$category['name'], $sendtodetail,'', false, false, '', true, $prettyurl);

		// Check the number of active products connected to this category
		$query = 'SELECT count(*) as num_products FROM ' . cms_db_prefix() . 'module_sms_product_category pc,
			' . cms_db_prefix() . 'module_sms_products p WHERE category_id=? AND pc.product_id = p.product_id AND
			p.active = 1';
		$dbresult = $db->Execute($query, array($category['category_id']));
		$row = $dbresult->FetchRow();
		$num_products = $row['num_products'];
		// Include the number of products in the name of the category
		if ($trans) {

			$cat_name = $trans->Translit($category['name']);
		} else {
			$cat_name = munge_string_to_url($category['name']);
		}
		if ($num_products != 0) {
			$category['name'] = $category['name'] . ' (' . $num_products . ')';
		}
		$onerow->catcount = $num_products;
		$sendtodetail = array('category_id' => $category['category_id']);
		$prettyurl = 'SimpleShop/cat/' . $category['category_id'] . '/' . ($detailpage != '' ? $detailpage : $returnid) . '/' . $cat_name;

		if (isset($sendtodetail['detailtemplate'])) {
			$prettyurl .= '/d,' . $sendtodetail['detailtemplate'];
		}
		// Prepare image of the category
		if (isset($category['image']) && $category['image'] != 'no_image.jpg' && $category['image'] != '') {
			$onerow->image = $this->GetPreference('imagepath_category') . $category['image'];
			$onerow->description = $category['name'];
		} else {
			$onerow->image = '*none';
		}

		$onerow->cursymbol = $this->GetPreference('default_symbol', '');

		$onerow->name = $this->CreateLink(
			$id,
			'fe_product_list',
			$detailpage != '' ? $detailpage : $returnid,
			$category['name'],
			$sendtodetail,
			'',
			false,
			false,
			'',
			true,
			$prettyurl
		);
		//$onerow->rowclass = $rowclass;
		$entryarray[] = $onerow;
	}
}

$entryarray = isset($entryarray) ? $entryarray : array();
$this->smarty->assign('items', $entryarray);

$this->smarty->assign('startform', $this->CreateFrontendFormStart($id, $returnid, 'fe_category_list'));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('label_categories', $this->Lang('label_categories'));
//$this->smarty->assign('categories', $categories);
// Display template
$template = 'categories_template';
// Currently only one template available, so next lines will not perform anything
if (isset($params['categoriestemplate'])) {
	$template = $params['categoriestemplate'];
}
echo $this->ProcessTemplateFromDatabase($template);
