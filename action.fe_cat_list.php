<?php

$gCms = cmsms();
if (!is_object($gCms)) exit;

$trans = cms_utils::get_module('TranslitAlias');

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

if ($trans) {

	$cat_name = $trans->Translit($this->Lang('root'));
} else {
	$cat_name = munge_string_to_url($this->Lang('root'));
}

$parentcategory = '0';
if (isset($params['parentcategory'])) {
	$parentcategory = $params['parentcategory'];
}
// Check the number of active products connected to the root category
$query2 = 'SELECT count(*) as num_products FROM ' . cms_db_prefix() . 'module_sms_product_category pc,
	' . cms_db_prefix() . 'module_sms_products p WHERE category_id = 0 AND pc.product_id = p.product_id AND	p.active = 1';
$dbresult2 = $db->Execute($query2);

$numprods = 0;
if ($dbresult2 && $prodrow = $dbresult2->FetchRow()) {
	$numprods = $prodrow['num_products'];
}

$destpage = (isset($detailpage) && $detailpage != '') ? $detailpage : $returnid;

$sendtodetail = array('category_id' => '0',	'detailpage' => $destpage);

$prettyurl = 'SimpleShop/cat/0/' . $destpage . '/' . $cat_name;
if (isset($sendtodetail['detailtemplate'])) {
	$prettyurl .= '/d,' . $sendtodetail['detailtemplate'];
}
if ($numprods != 0) {
	$this->smarty->assign('rootcat', $this->CreateLink(
		$id,
		'fe_product_list',
		$destpage,
		$this->Lang('root'),
		$sendtodetail,
		'',
		false,
		false,
		'',
		true,
		$prettyurl
	));
	$this->smarty->assign('products_in_root_category', $numprods);
}

// Create a category tree
$c = $this->FillCatList($parentcategory, 0, $returnid, $id);

$this->smarty->assign('categories', $c);

$this->smarty->assign('startform', $this->CreateFrontendFormStart($id, $returnid, 'fe_cat_list'));
$this->smarty->assign('endform', $this->CreateFormEnd());
$this->smarty->assign('label_categories', $this->Lang('label_categories'));

// Display template
$template = 'categories_template';
// Currently only one template available, so next lines will not perform anything
if (isset($params['categoriestemplate'])) {
	$template = $params['categoriestemplate'];
}
echo $this->ProcessTemplateFromDatabase($template);
