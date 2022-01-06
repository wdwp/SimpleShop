<?php
$gCms = cmsms();
if (!is_object($gCms)) exit;

// Drop all tables
$db = cmsms()->GetDb();
$dict = NewDataDictionary($db);

$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_sms_categories');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_sms_products');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_sms_product_images');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_sms_product_attributes');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_sms_product_skus');
$dict->ExecuteSQLArray($sqlarray);
$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_sms_product_category');
$dict->ExecuteSQLArray($sqlarray);

// Remove all preferences
$this->RemovePreference();

// Remove security permissions
$this->RemovePermission('ShopMS_UseSimpleShop');
$this->RemovePermission('ShopMS_MaintainCategory');
$this->RemovePermission('ShopMS_MaintainProducts');
$this->RemovePermission('ShopMS_MaintainSKUs');

$css = new \CmsLayoutStylesheet;
// dirty test to check if the stylesheet exists
try {
    $stylesheet = $css->load('SimpleShop Style');
    $stylesheet->delete();
} catch (Exception $e) {
    # do nothing: it doesn't exist anyway...
};

// Remove all the search index entries
$modops = cmsms()->GetModuleOperations();
$searchmodule = $modops->get_module_instance('Search');
$searchmodule->DeleteWords($this->GetName());

// Remove templates that belong to this module
$this->DeleteTemplate('', 'SimpleShop');

// Remove files
$dirname = __DIR__ . '/../../uploads/images/categories';
array_map('unlink', glob("$dirname/*.*"));
rmdir($dirname);

$dirname = __DIR__ . '/../../uploads/images/products';
array_map('unlink', glob("$dirname/*.*"));
rmdir($dirname);

$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('uninstalled', $this->GetVersion()));
