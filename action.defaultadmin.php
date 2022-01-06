<?php
# Module: Shop Made Simple - A product maintenance module for CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown
#
# This function supports the admin part of module Shop Made Simple
#
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/sms
#
#-------------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
# Or read it online: http://www.gnu.org/licenses/licenses.html#GPL
#
#-------------------------------------------------------------------------

if (!isset($gCms)) exit;

$themeObject = \cms_utils::get_theme_object();

$active_tab = 'categories';
if (isset($params['active_tab'])) {
	$active_tab = $params['active_tab'];
}
// $user_id = '';
// $user_id = get_userid();

// if (isset($user_id)) {
// 	// If change of category requested
// 	if (isset($params['current_category_id'])) {
// 		set_preference($user_id, 'dtsms_current_category_id', $params['current_category_id']);
// 	}
// 	$params['current_category_id'] = get_preference($user_id, 'dtsms_current_category_id');
// }

// Check if a item search is requested
$itemnumber = '';
if (isset($params['itemnumber'])) {
	$itemnumber = trim($params['itemnumber']);
	// Reset the category to the root
	$params['current_category_id'] = 0;
}
// Prepare categories and products only for allowed users
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('ShopMS_MaintainCategory') ||
	$this->CheckPermission('ShopMS_MaintainProducts')
) {
	if (!isset($params['current_category_id'])) {
		$params['current_category_id'] = 0;
	}
	$current_category = $this->categories->Get($params['current_category_id']);

	$message = '';
	if (isset($params['module_message'])) {
		$message = $params['module_message'];
	}

	/**
	 * Prepare the categories tree for quick selector
	 */
	$smarty->assign('title_hide', $this->Lang('title_hide'));
	$smarty->assign('title_show', $this->Lang('title_show'));
	$smarty->assign('title_quick_selector', $this->Lang('title_quick_selector'));
	if ($this->GetPreference('displayquickselector', true)) {
		$smarty->assign('displayqs', 'block');
	} else {
		$smarty->assign('displayqs', 'none');
	}
	$parentcat = '0';
	// Create a category tree
	$qsc = $this->FillQSCatList($parentcat, 0, $returnid);
	$smarty->assign('qscats', $qsc);

	/**
	 * Prepare the Categories.
	 */
	$categories = array();
	$category_path = $this->CreateLink($id, 'category_list', $returnid, $this->Lang('root'), array('current_category_id' => 0));

	if ($params['current_category_id'] != 0) {
		foreach ($this->categories->BuildPath($current_category) as $category) {
			$category_path .= ' / ' . $this->CreateLink(
				$id,
				'category_list',
				$returnid,
				$category['name'],
				array('current_category_id' => $category['category_id'])
			);
		}
	}
	if (
		$this->CheckPermission('ShopMS_UseSimpleShop') ||
		$this->CheckPermission('ShopMS_MaintainCategory')
	) {
		$categories['current'] = array(
			'label' => $this->Lang('label_current_category'),
			'value' => $category_path,
			'link_add' => $this->CreateLink(
				$id,
				'category_add',
				$returnid,
				$themeObject->DisplayImage('icons/system/newobject.gif', $this->Lang('text_add') . ' ' . $this->Lang('text_category'), '', '', 'systemicon') . ' ' . $this->Lang('text_add') . ' ' . $this->Lang('text_category'),
				array('current_category_id' => $params['current_category_id']),
				'',
				false,
				false,
				'class="pageoptions"'
			)
		);
	} else {
		$categories['current'] = array(
			'label' => $this->Lang('label_current_category'),
			'value' => $category_path,
			'link_add' => '&nbsp;'
		);
	}
	$category_list = array();

	foreach ($this->categories->GetList($params['current_category_id']) as $category) {
		if (
			$this->CheckPermission('ShopMS_UseSimpleShop') ||
			$this->CheckPermission('ShopMS_MaintainCategory')
		) {
			$category['link_delete'] = $this->CreateLink($id, 'category_delete', $returnid, $themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('text_delete'), '', '', 'systemicon'), array('current_category_id' => $category['category_id']), $this->Lang('message_areyousurecategory', $category['name']));
			$category['link_edit'] = $this->CreateLink($id, 'category_edit', $returnid, $themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('text_edit'), '', '', 'systemicon'), array('current_category_id' => $params['current_category_id'], 'category_id' => $category['category_id'], 'parent_id' => $category['parent_id'], 'name' => $category['name'], 'description' => $category['description'], 'image' => $category['image'], 'active' => $category['active'], 'position' => $category['position'],));
			$category['link_enable'] = $this->CreateLink(
				$id,
				'switchstatus',
				$returnid,
				$category['active'] == 1 ? $themeObject->DisplayImage(
					'icons/system/true.gif',
					$this->Lang('text_inactive'),
					'',
					'',
					'systemicon'
				) : $themeObject->DisplayImage('icons/system/false.gif', $this->Lang('text_active'), '', '', 'systemicon'),
				array(
					'table' => 'Categories', 'active' => $category['active'], 'category_id' => $category['category_id'],
					'parent_id' => $category['parent_id']
				)
			);
		}
		$category['name'] = $this->CreateLink($id, 'category_list', $returnid, $category['name'], array('current_category_id' => $category['category_id']));
		$category_list[] = $category;
	}
	$categories['subcategories'] = array(
		'label' => array(
			'id' => $this->Lang('label_category_id'),
			'name' => $this->Lang('label_category_name'),
			'description' => $this->Lang('label_category_description')
		),
		'list' => $category_list
	);

	/**
	 * Prepare the products
	 */
	$products = array();
	$products['startform'] = $this->CreateFormStart($id, 'defaultadmin');
	$products['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('searchitem'));
	$products['endform'] = $this->CreateFormEnd();
	$products['itemsearch'] = $this->CreateInputText($id, 'itemnumber', $itemnumber, 30, 30, 'placeholder="' . $this->Lang('label_product_itemnumber') . '"');

	if (
		$this->CheckPermission('ShopMS_UseSimpleShop') ||
		$this->CheckPermission('ShopMS_MaintainProducts')
	) {
		if ($itemnumber == '') {
			$product_link_add = $this->CreateLink(
				$id,
				'product_add',
				$returnid,
				$themeObject->DisplayImage('icons/system/newobject.gif', $this->Lang('text_add') . ' ' . $this->Lang('text_product'), '', '', 'systemicon') . $this->Lang('text_add') . ' ' . $this->Lang('text_product'),
				array('current_category_id' => $params['current_category_id']),
				'',
				false,
				false,
				'class="pageoptions"'
			);
			$current_category['name'] = $this->Lang('variouscategories');
		} else {
			$current_category['name'] = $this->Lang('variouscategories');
			$product_link_add = '';
		}
		$products['category'] = array(
			'label' => $this->Lang('label_product_category'),
			'value' => $current_category['name'],
			'link_add' => $product_link_add
		);
	} else {
		$products['category'] = array(
			'label' => $this->Lang('label_product_category'),
			'value' => $current_category['name'],
			'link_add' => '&nbsp;'
		);
	}
	// Check if inventory in place. If so use different label
	$inventorytype = $this->GetPreference('inventorytype');
	if ($inventorytype == 'prod') {
		$label_product_maxattributes = $this->Lang('label_onstock');
	} else {
		$label_product_maxattributes = '';
	}
	$product_list = array();
	if ($itemnumber == '') {
		$productsubset = $this->products->GetList($params['current_category_id']);
	} else {
		$productsubset = sms_utils::get_products_subset($itemnumber);
	}
	if ($productsubset) {

		foreach ($productsubset as $product) {
			$productname = $product['name'];
			if (
				$this->CheckPermission('ShopMS_UseSimpleShop') ||
				$this->CheckPermission('ShopMS_MaintainProducts')
			) {
				$product['name'] = $this->CreateLink(
					$id,
					'product_edit',
					$returnid,
					$product['name'],
					array('current_category_id' => $params['current_category_id'], 'product_id' => $product['product_id'])
				);
				#$product['description'] = $this->CreateLink ($id, 'product_edit', $returnid, $product['description'],
				#	array ('current_category_id' => $params['current_category_id'], 'product_id' => $product['product_id']));
				$product['parent_id'] = isset($product['parent_id']) ? $product['parent_id'] : 0;
				$product['inventorytype'] = $inventorytype;
				$product['itemnumber'] = $product['itemnumber'];
				$product['onstock'] = $product['maxattributes'];
				$product['link_delete'] = $this->CreateLink(
					$id,
					'product_delete',
					$returnid,
					$themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('text_delete'), '', '', 'systemicon'),
					array('current_product_id' => $product['product_id'], 'current_category_id' => $params['current_category_id']),
					$this->Lang('message_areyousureproduct', $productname)
				);
				$product['link_edit'] = $this->CreateLink(
					$id,
					'product_edit',
					$returnid,
					$themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('text_edit'), '', '', 'systemicon'),
					array('current_category_id' => $params['current_category_id'], 'product_id' => $product['product_id'])
				);
				$product['link_enable'] = $this->CreateLink(
					$id,
					'switchstatus',
					$returnid,
					$product['active'] == 1 ? $themeObject->DisplayImage(
						'icons/system/true.gif',
						$this->Lang('text_inactive'),
						'',
						'',
						'systemicon'
					) : $themeObject->DisplayImage('icons/system/false.gif', $this->Lang('text_active'), '', '', 'systemicon'),
					array(
						'table' => 'Products', 'active' => $product['active'], 'current_category_id' => $params['current_category_id'],
						'parent_id' => $product['parent_id'], 'product_id' => $product['product_id']
					)
				);
			} else {
				$product['name'] = $product['name'];
				$product['link_delete'] = '';
				$product['link_edit'] = '';
				$product['link_enable'] = '';
			}
			$product_list[] = $product;
		}
	}
	$products['products'] = array(
		'label' => array(
			'id' => $this->Lang('label_product'),
			'name' => $this->Lang('label_category_name'),
			'description' => $this->Lang('label_category_description'),
			'onstock' => $label_product_maxattributes,
			'itemnumber' => $this->Lang('label_product_itemnumber')
		),
		'list' => $product_list
	);
}
/**
 * Prepare the Stock Keeping Units
 */
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('ShopMS_MaintainSKUs')
) {
	$skus = array();
	if (isset($params['skumessage'])) {
		$skus['message'] = $params['skumessage'];
	}
	foreach ($this->products->GetListSKU() as $sku) {
		$sku['name'] = $this->CreateLink($id, 'sku_edit', $returnid, $sku['sku'], array('sku' => $sku['sku']));
		$sku['description'] = $this->CreateLink($id, 'sku_edit', $returnid, $sku['description'], array('sku' => $sku['sku']));
		$sku['link_edit'] = $this->CreateLink(
			$id,
			'sku_edit',
			$returnid,
			$themeObject->DisplayImage('icons/system/edit.gif', $this->Lang('text_edit'), '', '', 'systemicon'),
			array('sku' => $sku['sku'])
		);
		$sku['link_delete'] = $this->CreateLink(
			$id,
			'sku_delete',
			$returnid,
			$themeObject->DisplayImage('icons/system/delete.gif', $this->Lang('text_delete'), '', '', 'systemicon'),
			array('sku' => $sku['sku']),
			$this->Lang('message_areyousuresku', $sku['sku'])
		);
		$sku_list[] = $sku;
	}
	$skus['skus'] = array(
		'label' => array(
			'link_add' => $this->CreateLink(
				$id,
				'sku_add',
				$returnid,
				$themeObject->DisplayImage('icons/system/newobject.gif', $this->Lang('text_add') . ' ' . $this->Lang('text_sku'), '', '', 'systemicon')
			),
			'text_add' => $this->CreateLink($id, 'sku_add', $returnid, $this->Lang('text_add') . ' ' . $this->Lang('text_sku')),
			'sku' => $this->Lang('label_sku_name'),
			'description' => $this->Lang('label_sku_description')
		),
		'list' => $sku_list
	);
}
/**
 * Prepare the templates tab
 */
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('Modify Templates')
) {
	$templates = array();
	$templates['startform'] = $this->CreateFormStart($id, 'templates_update');
	$templates['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('update'));
	$templates['endform'] = $this->CreateFormEnd();

	$templates['catlist_template'] = array(
		'label' => $this->Lang('label_catlist_template'),
		'input' => $this->CreateTextArea(false,	$id, $this->GetTemplate('catlist_template'), 'catlist_template', '', '', '', '', 80, 25)
	);
	$templates['categories_template'] = array(
		'label' => $this->Lang('label_categories_template'),
		'input' => $this->CreateTextArea(false,	$id, $this->GetTemplate('categories_template'), 'categories_template', '', '', '', '', 80, 25)
	);
	$templates['proddetail_template'] = array(
		'label' => $this->Lang('label_proddetail_template'),
		'input' => $this->CreateTextArea(false,	$id, $this->GetTemplate('proddetail_template'), 'proddetail_template', '', '', '', '', 80, 25)
	);
	$templates['prodfeat_template'] = array(
		'label' => $this->Lang('label_prodfeat_template'),
		'input' => $this->CreateTextArea(false,	$id, $this->GetTemplate('prodfeat_template'), 'prodfeat_template', '', '', '', '', 80, 25)
	);
}
/**
 * Prepare the options tab
 */
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('Modify Site Preferences')
) {
	// Check if CartMS has been installed
	$smarty->assign('CartMSInstalled', false);
	$modops = cmsms()->GetModuleOperations();
	$CartMS = $modops->get_module_instance('SimpleCart');
	if ($CartMS) {
		$smarty->assign('CartMSInstalled', true);
	}

	$options = array();
	$options['startform'] = $this->CreateFormStart($id, 'preferences_update');
	$options['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('update'));
	$options['rebuildproductthumbnails'] = $this->CreateInputSubmit($id, 'rebuildproductthumbnails', $this->Lang('rebuildproductthumbnails'));
	$options['searchreindex'] = $this->CreateInputSubmit($id, 'searchreindex', $this->Lang('searchreindex'));
	$options['endform'] = $this->CreateFormEnd();

	$options['admin_name'] = array(
		'label' => $this->Lang('label_admin_name'),
		'input' => $this->CreateInputText($id, 'admin_name', $this->GetPreference('admin_name', ''), 40, 40)
	);
	$options['shop_name'] = array(
		'label' => $this->Lang('label_shop_name'),
		'input' => $this->CreateInputText($id, 'shop_name', $this->GetPreference('shop_name', ''), 40, 40)
	);
	$options['default_maxattributes'] = array(
		'label' => $this->Lang('label_default_maxattributes'),
		'input' => $this->CreateInputText($id, 'default_maxattributes', $this->GetPreference('default_maxattributes', 1), 10, 10)
	);
	$options['pricesinclvat'] = array(
		'label' => $this->Lang('label_prices_incl_vat'),
		'input' => $this->CreateInputCheckbox($id, 'pricesinclvat', 1, $this->GetPreference('pricesinclvat', 0))
	);
	$options['pricesinclvat'] = array(
		'label' => $this->Lang('label_prices_incl_vat'),
		'input' => $this->CreateInputCheckbox($id, 'pricesinclvat', 1, $this->GetPreference('pricesinclvat', 0))
	);
	$options['weightunitmeasure'] = array(
		'label' => $this->Lang('label_weight_unit_measure'),
		'input' => $this->CreateInputText($id, 'weightunitmeasure', $this->GetPreference('weightunitmeasure', 'Kg'), 10, 10)
	);
	$options['itemcapitalonly'] = array(
		'label' => $this->Lang('label_item_capital_only'),
		'input' => $this->CreateInputCheckbox($id, 'itemcapitalonly', 1, $this->GetPreference('itemcapitalonly', false))
	);
	$options['allowdoubleitem'] = array(
		'label' => $this->Lang('label_allowdoubleitem'),
		'input' => $this->CreateInputCheckbox($id, 'allowdoubleitem', 1, $this->GetPreference('allowdoubleitem', false))
	);
	$skudropdown = $this->BuildListSKU();
	$options['default_sku'] = array(
		'label' => $this->Lang('label_default_sku'),
		'input' => $this->CreateInputDropdown($id, 'default_sku', $skudropdown, -1, $this->GetPreference('default_sku', ''))
	);
	$options['default_currency'] = array(
		'label' => $this->Lang('label_default_currency'),
		'input' => $this->CreateInputText($id, 'default_currency', $this->GetPreference('default_currency', ''), 3, 3)
	);
	$options['default_symbol'] = array(
		'label' => $this->Lang('label_default_symbol'),
		'input' => $this->CreateInputText($id, 'default_symbol', $this->GetPreference('default_symbol', ''), 10, 40)
	);
	$options['imagepath_category'] = array(
		'label' => $this->Lang('label_imagepath_category'),
		'input' => $this->CreateInputText($id, 'imagepath_category', $this->GetPreference('imagepath_category', ''), 40, 40)
	);
	$options['imagepath_product'] = array(
		'label' => $this->Lang('label_imagepath_product'),
		'input' => $this->CreateInputText($id, 'imagepath_product', $this->GetPreference('imagepath_product', ''), 40, 40)
	);
	$options['tnheight_product'] = array(
		'label' => $this->Lang('label_tnheight_product'),
		'input' => $this->CreateInputText($id, 'tnheight_product', $this->GetPreference('tnheight_product', '100'), 5, 5)
	);
	$options['tnwidth_product'] = array(
		'label' => $this->Lang('label_tnwidth_product'),
		'input' => $this->CreateInputText($id, 'tnwidth_product', $this->GetPreference('tnwidth_product', '0'), 5, 5)
	);
	$options['productpagelimit'] = array(
		'label' => $this->Lang('label_productpagelimit'),
		'input' => $this->CreateInputText($id, 'productpagelimit', $this->GetPreference('productpagelimit', 100000), 10, 10)
	);
	$options['inventorysettings'] = array(
		'label' => $this->Lang('label_inventorysettings')
	);
	$inventorytypes = $this->BuildListInventoryTypes();
	$usedinventorytype = $this->GetPreference('inventorytype', 'none');
	$options['inventorytype'] = array(
		'label' => $this->Lang('label_inventorytype'),
		'input' => $this->CreateInputRadioGroup($id, 'inventorytype', $inventorytypes, $usedinventorytype)
	);
	// Define at what moment the sales transaction should decrease inventory
	$salesinventtiming = $this->BuildListSalesInventTiming();
	$usedsalesinventtiming = $this->GetPreference('salesinventtiming', 'CNF');
	$options['salesinventtiming'] = array(
		'label' => $this->Lang('label_salesinventtiming'),
		'input' => $this->CreateInputDropdown($id, 'salesinventtiming', $salesinventtiming, -1, $usedsalesinventtiming)
	);
	$options['numberformatting'] = array(
		'label' => $this->Lang('label_numberformatting')
	);
	$options['decimalpositionsprice'] = array(
		'label' => $this->Lang('label_decimalpositionsprice'),
		'input' => $this->CreateInputText($id, 'decimalpositionsprice', $this->GetPreference('decimalpositionsprice', 2), 1, 1)
	);
	$options['decimalseparatorprice'] = array(
		'label' => $this->Lang('label_decimalseparatorprice'),
		'input' => $this->CreateInputText($id, 'decimalseparatorprice', $this->GetPreference('decimalseparatorprice', ','), 1, 1)
	);
	$options['thousandseparatorprice'] = array(
		'label' => $this->Lang('label_thousandseparatorprice'),
		'input' => $this->CreateInputText($id, 'thousandseparatorprice', $this->GetPreference('thousandseparatorprice', '.'), 1, 1)
	);
	$options['decimalpositionsweight'] = array(
		'label' => $this->Lang('label_decimalpositionsweight'),
		'input' => $this->CreateInputText($id, 'decimalpositionsweight', $this->GetPreference('decimalpositionsweight', 3), 1, 1)
	);
	$options['decimalseparatorweight'] = array(
		'label' => $this->Lang('label_decimalseparatorweight'),
		'input' => $this->CreateInputText($id, 'decimalseparatorweight', $this->GetPreference('decimalseparatorweight', ','), 1, 1)
	);
	$options['thousandseparatorweight'] = array(
		'label' => $this->Lang('label_thousandseparatorweight'),
		'input' => $this->CreateInputText($id, 'thousandseparatorweight', $this->GetPreference('thousandseparatorweight', '.'), 1, 1)
	);
	$options['displayquickselector'] = array(
		'label' => $this->Lang('label_displayquickselector'),
		'input' => $this->CreateInputCheckbox($id, 'displayquickselector', 1, $this->GetPreference('displayquickselector', false))
	);
}
echo $this->StartTabHeaders();
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('ShopMS_MaintainCategory') ||
	$this->CheckPermission('ShopMS_MaintainProducts')
) {
	echo $this->SetTabHeader('categories', $this->Lang('label_tab_categories'), ($active_tab == 'categories'));
}
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('ShopMS_MaintainSKUs')
) {
	echo $this->SetTabHeader('skus', $this->Lang('label_tab_skus'), ($active_tab == 'skus'));
}
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('Modify Templates')
) {
	echo $this->SetTabHeader('templates', $this->Lang('label_tab_templates'), ($active_tab == 'templates'));
}
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('Modify Site Preferences')
) {
	echo $this->SetTabHeader('options', $this->Lang('label_tab_options'), ($active_tab == 'options'));
}

echo $this->EndTabHeaders();

// Prepare the categories and products tab
echo $this->StartTabContent();

$smarty->assign('current_category_id', $params['current_category_id']);
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('ShopMS_MaintainCategory') ||
	$this->CheckPermission('ShopMS_MaintainProducts')
) {
	echo $this->StartTab('categories', $params);
	$smarty->assign('categories', $categories);
	$smarty->assign('products', $products);
	$smarty->assign('nocatfound', $this->Lang('nocatfound'));
	if ($itemnumber == '') {
		$smarty->assign('noprodfound', $this->Lang('noprodfound'));
	} else {
		$smarty->assign('noprodfound', $this->Lang('noprodfoundforsearch'));
	}
	echo $this->ProcessTemplate('tab_categories.tpl');
	echo $this->EndTab();
}
// Prepare the tab containing the SKU's
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('ShopMS_MaintainSKUs')
) {
	echo $this->StartTab('skus', $params);
	$smarty->assign('skus', $skus);
	echo $this->ProcessTemplate('tab_skus.tpl');
	echo $this->EndTab();
}
// Prepare the tab containing the templates
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('Modify Templates')
) {
	echo $this->StartTab('templates', $params);
	$smarty->assign('templates', $templates);
	echo $this->ProcessTemplate('tab_templates.tpl');
	echo $this->EndTab();
}
// Prepare options tab
if (
	$this->CheckPermission('ShopMS_UseSimpleShop') ||
	$this->CheckPermission('Modify Site Preferences')
) {
	echo $this->StartTab('options', $params);
	$smarty->assign('options', $options);
	echo $this->ProcessTemplate('tab_options.tpl');
	echo $this->EndTab();
}

echo $this->EndTabContent();
