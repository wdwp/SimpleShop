<?php
$gCms = cmsms();
if (!is_object($gCms)) exit;

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

$db = &$gCms->GetDb();
$display = '';
if (isset($params['display'])) {
	$display = $params['display'];
} else {
	$display = 'categorylist';
}

if (isset($params['catname'])) {
	$catname = $params['catname'];
} else {
	$catname = 'root';
}
//if (isset($params['category_id'])) $catname = '';

switch (trim($display)) {
	case 'catlist':
		// Prepare a complete list of categories starting at top level
		$display = 'fe_cat_list';
		// Prepare a list of products for given category
		if ($catname == 'root') {
			$params['parentcategory'] = 0;
		} else {
			$catinfo = array();
			$catinfo = $this->categories->GetCatByName($catname);
			$params['parentcategory'] = $catinfo['category_id'];
		}
		break;
	case 'categorylist':
		// Prepare a list of categories at top level
		$display = 'fe_category_list';
		break;
	case 'productlist':
		// Prepare a list of products for given category
		if ($catname == 'root') {
			$params['category_id'] = isset($params['category_id']) ? $params['category_id'] : 0;
		} else if (!empty($catname)) {
			$catinfo = array();
			$catinfo = $this->categories->GetCatByName($catname);
			$params['category_id'] = isset($params['category_id']) ? $params['category_id'] : $catinfo['category_id'];
		} else {
			//nothing todo
		}
		$display = 'fe_product_list';
		break;
	case 'featured':
		// Prepare list of all featured products
		$params['action'] = '';
		$display = 'fe_products_featured';
		break;
	case 'productdetail':
		// Prepare details for given product
		break;
	case 'sitemap':
		// Prepare a list of categories at top level
		$display = 'sitemap';
		break;
	default:
		echo $this->Lang('unknown_display', $display);
		return;
}

require(dirname(__FILE__) . '/action.' . $display . '.php');
