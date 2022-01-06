<?php

// No direct access
$gCms = cmsms();
if (!is_object($gCms)) exit;

// Check permission
if (
	!$this->CheckPermission('ShopMS_UseSimpleShop') &&
	!$this->CheckPermission('ShopMS_MaintainProducts')
) {
	// Show an error message
	echo $this->ShowError($this->Lang('access_denied'));
}
// User has sufficient privileges
else {
	$redirectto = 'defaultadmin';
	switch ($params['table']) {
		case 'Attributes':
			$active = ($params['active'] == 0) ? 1 : 0;
			$query = 'UPDATE ' . cms_db_prefix() . 'module_sms_product_attributes SET active = ? 
				WHERE attribute_id = ?';
			$dbresult = $db->Execute($query, array($active, $params['attribute_id']));
			$redirectto = 'product_edit';
			$params = array('active_tab' => 'attributes', 'product_id' => $params['product_id'], 'current_category_id' => $params['current_category_id']);
			break;
		case 'AttributesDisplay';
			$displayonly = ($params['displayonly'] == 0) ? 1 : 0;
			$query = 'UPDATE ' . cms_db_prefix() . 'module_sms_product_attributes SET displayonly = ? 
				WHERE attribute_id = ?';
			$dbresult = $db->Execute($query, array($displayonly, $params['attribute_id']));
			$redirectto = 'product_edit';
			$params = array('active_tab' => 'attributes', 'product_id' => $params['product_id'], 'current_category_id' => $params['current_category_id']);
			break;
		case 'Categories':
			$active = ($params['active'] == 0) ? 1 : 0;
			$query = 'UPDATE ' . cms_db_prefix() . 'module_sms_categories SET active = ? 
				WHERE category_id = ?';
			$dbresult = $db->Execute($query, array($active, $params['category_id']));
			$params = array('active_tab' => 'categories', 'current_category_id' => $params['parent_id']);
			break;
		case 'Products':
			$active = ($params['active'] == 0) ? 1 : 0;
			$query = 'UPDATE ' . cms_db_prefix() . 'module_sms_products SET active = ? 
				WHERE product_id = ?';
			$dbresult = $db->Execute($query, array($active, $params['product_id']));
			$params = array(
				'active_tab' => 'categories', 'current_category_id' => $params['current_category_id'],
				'current_product_id' => $params['parent_id']
			);
			break;
		default:
			break;
	}

	// Redirect the user to the admin screen that one came from
	$this->Redirect($id, $redirectto, $returnid, $params);
}
