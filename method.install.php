<?php
$gCms = cmsms();
if (!is_object($gCms)) exit;

/**
 * Create all tables
 */
$db = cmsms()->GetDb();
$dict = NewDataDictionary($db);
/**
 * Create all Category related tables
 */
/**
 * Primary Category table
 */
$fields = "category_id I PRIMARY AUTO,
	parent_id I,
	name C(255),
	description C(255),
	image C(60),
	active I2,
	position I2";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . "module_sms_categories", $fields, $this->taboptarray);
$dict->ExecuteSQLArray($sqlarray);

/**
 * Create all Product related tables
 */
/**
 * Primary Product Table
 */
$fields = "product_id I PRIMARY AUTO,
	name C(255),
	description X,
	price F,
	active I2,
	featured I2,
	netweight F,
	vatcode C(1),
	sku C(20),
	itemnumber C(30),
	position I,
	maxattributes I2";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . "module_sms_products", $fields, $this->taboptarray);
$dict->ExecuteSQLArray($sqlarray);
/**
 * Product Images
 */
$fields = "product_images_id I PRIMARY AUTO,
	product_id I,
	image C(30),
	description C(255)";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . "module_sms_product_images", $fields, $this->taboptarray);
$dict->ExecuteSQLArray($sqlarray);
/**
 * Product Attributes
 */
$fields = "attribute_id I PRIMARY AUTO,
	product_id I,
	name C(255),
	description X,
	minallowed I2,
	maxallowed I2,
	priceadjusttype C(1),
	priceadjustment F,
	displayonly I2,
	itemnumber C(30),
	active L";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . "module_sms_product_attributes", $fields, $this->taboptarray);
$dict->ExecuteSQLArray($sqlarray);
/**
 * Product <-> Category Map Table
 */
$fields = "category_product_id I PRIMARY AUTO,
	category_id I,
	product_id I";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . "module_sms_product_category", $fields, $this->taboptarray);
$dict->ExecuteSQLArray($sqlarray);
/**
 * Product Stock Keeping Unit (SKU)
 */
$fields = "sku C(20) KEY,
	description C(100)";
$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . "module_sms_product_skus", $fields, $this->taboptarray);
$dict->ExecuteSQLArray($sqlarray);
$query = 'INSERT INTO ' . cms_db_prefix() . 'module_sms_product_skus (sku, description) VALUES( ?, ?)';
$result = $db->Execute($query, array('PC', 'Piece'));

/**
 * Create all preferences
 */
$this->SetPreference('admin_name', '<Administrator Name>');
$this->SetPreference('shop_name', '<The name of the shop>');
$this->SetPreference('default_language', 'en_US');
$this->SetPreference('pricesinclvat', 0);
$this->SetPreference('weightunitmeasure', 'Kg');
$this->SetPreference('default_currency', 'EUR');
$this->SetPreference('default_symbol', '&euro;');
$this->SetPreference('imagepath_category', '/categories/');
$this->SetPreference('imagepath_product', '/products/');
$this->SetPreference('productpagelimit', 100000);
$this->SetPreference('displayquickselector', true);
$this->SetPreference('inventorytype', 'none');
/**
 * Create an example stylesheet for Shop Made Simple tags
 */
$txt = file_get_contents(cms_join_path(dirname(__FILE__), 'css', 'stylesheet.css'));

$stylesheet = new \CmsLayoutStylesheet;
// dirty test to check if a stylesheet with the same name already exists
try {
	$test = $stylesheet->load('SimpleShop Style');
} catch (Exception $e) {
	# it doesn't exist so create one
	$stylesheet->set_name('SimpleShop Style');
	$stylesheet->set_description('A sample stylesheet for the Shop');
	$stylesheet->set_content($txt);
	$stylesheet->save();
};

/**
 * Create security permissions
 */
$this->CreatePermission('ShopMS_UseSimpleShop', $this->Lang('ShopMS_UseSimpleShop'));
$this->CreatePermission('ShopMS_MaintainCategory', $this->Lang('ShopMS_MaintainCategory'));
$this->CreatePermission('ShopMS_MaintainProducts', $this->Lang('ShopMS_MaintainProducts'));
$this->CreatePermission('ShopMS_MaintainSKUs', $this->Lang('ShopMS_MaintainSKUs'));

/**
 * Create various templates
 */
# Setup category list template
$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_category_list.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('catlist_template', $template);
}

# Setup categories template
$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_categories.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('categories_template', $template);
}

# Setup product detail template
$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_product_detail.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('proddetail_template', $template);
}

# Setup featured products template
$fn = cms_join_path(dirname(__FILE__), 'templates', 'fe_products_featured.tpl');
if (file_exists($fn)) {
	$template = @file_get_contents($fn);
	$this->SetTemplate('prodfeat_template', $template);
}

/**
 * Create directories to hold images
 */
$config = cmsms()->GetConfig();
$path = cms_join_path($config['uploads_path'], 'images', 'categories');
// Make sure the directory can be found. Create it, error handling will cover any problems found
if (!file_exists($path)) mkdir($path, 0777);
$path = cms_join_path($config['uploads_path'], 'images', 'products');
// Make sure the directory can be found. Create it, error handling will cover any problems found
if (!file_exists($path)) mkdir($path, 0777);

$this->RegisterModulePlugin(TRUE);
$this->RegisterSmartyPlugin('simpleshop', 'function', 'function_plugin');

$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('installed', $this->GetVersion()));
