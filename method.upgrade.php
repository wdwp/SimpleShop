<?php
# Module: Shop Made Simple - A product maintenance module for CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown <duketown@mantox.nl>
#
# This function will upgrade the module Shop Made Simple
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

$dict = NewDataDictionary($db);

$current_version = $oldversion;
switch ($current_version) {
	case '0.1.0':
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix() . 'module_sms_products', 'price F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_products', 'netweight F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_products', 'vatcode C(1)');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix() . 'module_sms_options', 'price F');
		$dict->ExecuteSQLArray($sqlarray);
		// Shop policy to be taken from page or content block
		$this->RemovePreference('shop_description');
		// Introduce checkmark to state if prices are in- or excluding VAT
		$this->SetPreference('pricesinclvat', 0);
		// All weight are in unit of measure as prepared in preferences
		$this->SetPreference('weightunitmeasure', 'Kg');

		$current_version = '0.1.1';

	case '0.1.1':

		$current_version = '0.1.2';

	case '0.1.2':

		/**
		 * Create various templates
		 */
		# Setup category list template
		$fn = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'fe_category_list.tpl';
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('catlist_template', $template);
		}

		# Setup categories template
		$fn = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'fe_categories.tpl';
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('categories_template', $template);
		}

		# Setup product detail template
		$fn = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'fe_product_detail.tpl';
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('proddetail_template', $template);
		}

		# Setup featured products template
		$fn = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'fe_products_featured.tpl';
		if (file_exists($fn)) {
			$template = @file_get_contents($fn);
			$this->SetTemplate('prodfeat_template', $template);
		}

		$current_version = '0.1.3';

	case '0.1.3':

		$current_version = '0.1.4';

	case '0.1.4':

		$current_version = '0.1.5';

	case '0.1.5':
		// Alter column to new type
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix() . 'module_sms_products', 'description X');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix() . 'module_sms_product_attributes', 'description X');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix() . 'module_sms_attribute_options', 'description X');
		$dict->ExecuteSQLArray($sqlarray);

		$current_version = '0.1.6';

	case '0.1.6':
		// Add two new fields to products table
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_products', 'sku C(20)');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_products', 'itemnumber C(30)');
		$dict->ExecuteSQLArray($sqlarray);
		/**
		 * Product Stock Keeping Unit (SKU)
		 */
		$fields = "sku C(20) KEY,
			description C(100)";
		$sqlarray = $dict->CreateTableSQL(cms_db_prefix() . "module_sms_product_skus", $fields, $this->taboptarray);
		$dict->ExecuteSQLArray($sqlarray);
		$query = 'INSERT INTO ' . cms_db_prefix() . 'module_sms_product_skus (sku, description) VALUES( ?, ?)';
		$result = $db->Execute($query, array('PC', $this->Lang('piece')));
		$query = 'UPDATE ' . cms_db_prefix() . 'module_sms_products SET sku = ?';
		$result = $db->Execute($query, array('PC'));

		$current_version = '0.1.7';

	case '0.1.7':
		// No database changes only change in programs themselves
		$current_version = '0.1.8';

	case '0.1.8':
		// No database changes only change in programs themselves
		$current_version = '0.1.9';

	case '0.1.9':
		$this->SetPreference('productpagelimit', 100000);
		$current_version = '0.2.0';

	case '0.2.0':
		// No database changes only change in programs themselves
		$current_version = '0.2.1';

	case '0.2.1':
		// Version changes have not been published since testing was not done
		$current_version = '0.2.2';

	case '0.2.2':
		// No database changes only change in programs themselves
		$current_version = '0.2.3';

	case '0.2.3':
		$css_name = $this->Lang('module_example_stylesheet'); // Retrieve the name of the new stylesheet locate in the css directory
		// Check if this model style sheet allready exists
		$query = 'SELECT * FROM ' . cms_db_prefix() . 'css WHERE css_name = ?';
		$dbresult = $db->Execute($query, array($css_name));
		if (!$dbresult || $dbresult->RecordCount() == 0) {
			$new_css_id = $db->GenID(cms_db_prefix() . "css_seq");
			$fn = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'stylesheet.css';
			$css_text = @file_get_contents($fn);
			$media_type = 'screen';
			// Add the stylesheet to the database
			$query = "INSERT INTO " . cms_db_prefix() . "css (css_id, css_name, css_text, media_type, create_date, modified_date) VALUES (?, ?, ?, ?, ?, ?)";
			$result = $db->Execute($query, array($new_css_id, $css_name, $css_text, $media_type, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
		}

		$current_version = '0.2.4';

	case '0.2.4':
		// Bug change only
		$current_version = '0.2.5';

	case '0.2.5':
		// Included only feature request
		$current_version = '0.2.6';

	case '0.2.6':
		$current_version = '0.2.7';

	case '0.2.7':
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix() . 'module_sms_products', 'name C(255)');
		$dict->ExecuteSQLArray($sqlarray);
		$current_version = '0.2.8';

	case '0.2.8':
		// I, Duketown, have decided that attributes are to be extended with price
		// and not a sub table that holds prices per attribute, therefor adding columns
		// to attributes table and dropping of attribute options 
		$dict = NewDataDictionary($db);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_product_attributes', 'minallowed I2');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_product_attributes', 'maxallowed I2');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_product_attributes', 'priceadjusttype C(1)');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_product_attributes', 'priceadjustment F');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_product_attributes', 'displayonly L');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_product_attributes', 'itemnumber C(30)');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_product_attributes', 'active L');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AlterColumnSQL(cms_db_prefix() . 'module_sms_product_attributes', 'name C(255)');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->AddColumnSQL(cms_db_prefix() . 'module_sms_products', 'maxattributes I2');
		$dict->ExecuteSQLArray($sqlarray);
		$sqlarray = $dict->DropTableSQL(cms_db_prefix() . 'module_sms_attribute_options');
		$dict->ExecuteSQLArray($sqlarray);

		// Remove permission that were not working/needed
		$this->RemovePermission('Use SimpleShop', 'Use SimpleShop');
		$this->RemovePermission('Edit Preferences', 'Edit Preferences');
		$this->RemovePermission('Add/Delete Category', 'Add/Delete Category');
		$this->RemovePermission('Edit Category', 'Edit Category');
		$this->RemovePermission('Add/Delete Products', 'Add/Delete Products');
		$this->RemovePermission('Edit Products', 'Edit Products');
		$this->RemovePermission('Add/Delete Translations', 'Add/Delete Translations');
		$this->RemovePermission('Edit Translations', 'Edit Translations');
		$this->RemovePermission('Add/Delete Templates', 'Add/Delete Templates');
		$this->RemovePermission('Edit Templates', 'Edit Templates');
		$this->CreatePermission('ShopMS_UseSimpleShop', $this->Lang('ShopMS_UseSimpleShop'));
		$this->CreatePermission('ShopMS_MaintainCategory', $this->Lang('ShopMS_MaintainCategory'));
		$this->CreatePermission('ShopMS_MaintainProducts', $this->Lang('ShopMS_MaintainProducts'));
		$this->CreatePermission('ShopMS_MaintainSKUs', $this->Lang('ShopMS_MaintainSKUs'));
		// Default language for the shop was not used, so remove it during this major release
		$this->RemovePreference('default_language');

		// Major steps taken here, since extension to attributes is huge.
		$current_version = '0.3.0';

	case '0.3.0':
		$current_version = '0.3.1';

	case '0.3.1':
		$current_version = '0.3.2';

	case '0.3.2':
		$current_version = '0.3.3';

	case '0.3.3':
		$current_version = '0.3.4';

	case '0.3.4':
		$current_version = '0.3.5';

	case '0.3.5':
		$this->SetPreference('displayquickselector', true);
		$current_version = '0.3.6';

	case '0.3.6':
		$current_version = '0.3.7';

	case '0.3.7':
		$current_version = '0.3.8';

	case '0.3.8':
		$current_version = '0.3.9';
}

// Log the upgrade in the admin audit trail
$this->Audit(0, $this->Lang('friendlyname'), $this->Lang('upgraded', $this->GetVersion()));
