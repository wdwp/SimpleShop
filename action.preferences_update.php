<?php

// Reindexing requested of the products in the Search Module
if (isset($params['searchreindex'])) {
	$this->SearchReindex();
	$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('productsreindexed'));
	$params['tab_message'] = $this->Lang('productsreindexed');
	$params['active_tab'] = 'options';
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

// Rebuild the thumbnails
if (isset($params['rebuildproductthumbnails'])) {
	$this->SetPreference('tnheight_product', trim($params['tnheight_product']));
	$this->SetPreference('tnwidth_product', trim($params['tnwidth_product']));
	$config = $gCms->GetConfig();
	$path = $config['image_uploads_path'] . $this->GetPreference('imagepath_product');
	$this->RebuildThumbnails($path, 'products');
	$params['tab_message'] = $this->Lang('productthumbnailsrebuild');
	$params['active_tab'] = 'options';
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

// In other cases, the preferences are saved
$this->SetPreference('admin_name', trim($params['admin_name']));
$this->SetPreference('shop_name', trim($params['shop_name']));
$this->SetPreference('pricesinclvat', 0);
if (isset($params['pricesinclvat'])) {
	$this->SetPreference('pricesinclvat', 1);
}
$this->SetPreference('weightunitmeasure', trim($params['weightunitmeasure']));
$this->SetPreference('itemcapitalonly', 0);
if (isset($params['itemcapitalonly'])) {
	$this->SetPreference('itemcapitalonly', 1);
}
$this->SetPreference('allowdoubleitem', 0);
if (isset($params['allowdoubleitem'])) {
	$this->SetPreference('allowdoubleitem', 1);
}
$this->SetPreference('default_sku', $params['default_sku']);
$this->SetPreference('default_currency', $params['default_currency']);
$this->SetPreference('default_symbol', trim($params['default_symbol']));
$this->SetPreference('imagepath_category', trim($params['imagepath_category']));
$this->SetPreference('imagepath_product', trim($params['imagepath_product']));
$this->SetPreference('tnheight_product', trim($params['tnheight_product']));
$this->SetPreference('tnwidth_product', trim($params['tnwidth_product']));
$this->SetPreference('productpagelimit', (empty($params['productpagelimit']) || $params['productpagelimit'] == 0) ? 1 : trim($params['productpagelimit']));
$this->SetPreference('default_maxattributes', (empty($params['default_maxattributes'])) ? 1 : trim($params['default_maxattributes']));
$this->SetPreference('inventorytype', $params['inventorytype']);
$this->SetPreference('salesinventtiming', $params['salesinventtiming']);
$this->SetPreference('decimalpositionsprice', $params['decimalpositionsprice']);
$this->SetPreference('decimalseparatorprice', $params['decimalseparatorprice']);
$this->SetPreference('thousandseparatorprice', $params['thousandseparatorprice']);
$this->SetPreference('decimalpositionsweight', (int) $params['decimalpositionsweight']);
$this->SetPreference('decimalseparatorweight', $params['decimalseparatorweight']);
$this->SetPreference('thousandseparatorweight', $params['thousandseparatorweight']);
$this->SetPreference('displayquickselector', 0);
if (isset($params['displayquickselector'])) {
	$this->SetPreference('displayquickselector', 1);
}

$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('optionsupdated'));
$params['active_tab'] = 'options';
$params['tab_message'] = $this->Lang('optionsupdated');
$this->Redirect($id, 'defaultadmin', $returnid, $params);
