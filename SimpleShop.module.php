<?php
#-------------------------------------------------------------------------
# Fork of Module: Shop Made Simple - An Order Intake module for CMS - CMS Made Simple
# Copyright (c) 2008 by Duketown
# Forked by Yuri Haperski (wdwp@yandex.ru)
#-------------------------------------------------------------------------
# CMS - CMS Made Simple is (c) 2005 by Ted Kulp (wishy@cmsmadesimple.org)
# This project's homepage is: http://www.cmsmadesimple.org
# The module's homepage is: http://dev.cmsmadesimple.org/projects/cartms/
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

include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'categories.api.php');
include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'products.api.php');

class SimpleShop extends CMSModule
{

	var $categories;
	var $products;

	function SimpleShop()
	{
		parent::CMSModule();
		$this->categories = new Categories($this);
		$this->products = new SMSProducts($this);
		$this->InitializeFrontend();
	}
	function GetName()
	{
		return 'SimpleShop';
	}
	function GetFriendlyName()
	{
		return $this->Lang('friendlyname');
	}
	function GetVersion()
	{
		return '1.0';
	}
	function GetAuthor()
	{
		return 'Duketown';
	}
	function GetAuthorEmail()
	{
		// To reduce spam this email field left blank
		return '';
	}
	function GetHelp()
	{
		return $this->Lang('help');
	}
	function GetChangeLog()
	{
		return file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'changelog.inc');
	}
	function IsPluginModule()
	{
		return true;
	}
	function HasAdmin()
	{
		return true;
	}
	function GetAdminSection()
	{
		return 'ecommerce';
	}
	function GetAdminDescription()
	{
		return $this->Lang('admin_description');
	}
	/*---------------------------------------------------------
	VisibleToAdminUser()
	---------------------------------------------------------*/
	function VisibleToAdminUser()
	{
		if (
			$this->CheckPermission('Modify Site Preferences') ||
			$this->CheckPermission('Modify Modules') ||
			$this->CheckPermission('ShopMS_UseSimpleShop') ||
			$this->CheckPermission('ShopMS_MaintainCategory') ||
			$this->CheckPermission('ShopMS_MaintainProducts') ||
			$this->CheckPermission('ShopMS_MaintainSKUs')
		) {
			return true;
		}
		return false;
	}

	function GetDependencies()
	{
		return [];
	}
	function MinimumCMSVersion()
	{
		return '2.1.0';
	}

	function MaximumCMSVersion()
	{
		return '3';
	}

	function LazyLoadFrontend()
	{
		return false;
	}

	public function HasCapability($capability, $params = array())
	{
		switch ($capability) {
			case CmsCoreCapabilities::PLUGIN_MODULE:
				return TRUE;
		}
		return FALSE;
	}

	function InitializeFrontend()
	{
		$this->RegisterRoute('/[sS]imple[sS]hop\/cat\/(?P<category_id>[0-9]+)\/(?P<returnid>[0-9]+)\/(.+)$/', array('action' => 'fe_product_list'));
		$this->RegisterRoute('/[sS]imple[sS]hop\/prod\/(?P<product_id>[0-9]+)\/(?P<returnid>[0-9]+)\/(.+)$/', array('action' => 'fe_product_detail'));
	}

	function SetParameters()
	{
		$this->RestrictUnknownParams();

		# The top most parameter will be shown in the bottom and vice versa
		$this->CreateParameter('start', '', $this->Lang('helpstart'));
		$this->CreateParameter('pagenumber', '', $this->Lang('helppagenumber'));
		$this->CreateParameter('prodpagelimit', '', $this->Lang('helpprodpagelimit'));
		$this->CreateParameter('parentcategory', '', $this->Lang('helpparentcategory'));
		$this->CreateParameter('display', '', $this->Lang('helpdisplay'));
		$this->CreateParameter('detailpage', '', $this->Lang('helpdetailpage'));
		$this->CreateParameter('catname', '', $this->Lang('helpcatname'));
		$this->CreateParameter('category_id', '', $this->Lang('helpcategory_id'));

		$this->SetParameterType('start', CLEAN_STRING);
		$this->SetParameterType('pagenumber', CLEAN_STRING);
		$this->SetParameterType('product_id', CLEAN_STRING);
		$this->SetParameterType('prodpagelimit', CLEAN_STRING);
		$this->SetParameterType('parentcategory', CLEAN_STRING);
		$this->SetParameterType('display', CLEAN_STRING);
		$this->SetParameterType('sort', CLEAN_STRING);
		$this->SetParameterType('sort_order', CLEAN_STRING);
		$this->SetParameterType('detailpage', CLEAN_STRING);
		$this->SetParameterType('catname', CLEAN_STRING);
		$this->SetParameterType('category_id', CLEAN_STRING);
	}

	function GetEventDescription($eventname)
	{
		return $this->Lang('event_info_' . $eventname);
	}

	function GetEventHelp($eventname)
	{
		return $this->Lang('event_help_' . $eventname);
	}

	function InstallPostMessage()
	{
		return $this->Lang('message_postinstall');
	}
	function UninstallPostMessage()
	{
		return $this->Lang('message_postuninstall');
	}
	function UninstallPreMessage()
	{
		return $this->Lang('message_confirmuninstall');
	}

	// Prepare a list of the possible price adjustment types
	function BuildListOfAdjustTypes()
	{
		/*	P - Adds the adjustment to the product price
			M - Subtracts the adjustment from the product price
			T - Multiplies the product price time the adjustment factor (might be minus factor!)
			V - Overrides the product price with given factor
		*/
		$adjusttypedropdown = array();
		$adjusttypedropdown[$this->Lang('adjusttypeplus')] = 'P';
		$adjusttypedropdown[$this->Lang('adjusttypemin')] = 'M';
		$adjusttypedropdown[$this->Lang('adjusttypetimes')] = 'T';
		$adjusttypedropdown[$this->Lang('adjusttypevalue')] = 'V';

		return $adjusttypedropdown;
	}

	function BuildListInventoryTypes()
	{
		$inventorytypes = array();
		$inventorytypes[$this->Lang('inventorytype_none')] = 'none';
		$inventorytypes[$this->Lang('inventorytype_prod')] = 'prod';
		$inventorytypes[$this->Lang('inventorytype_attr')] = 'attr';

		return $inventorytypes;
	}

	function BuildListSKU()
	{
		$db = cmsms()->GetDb();
		$sql = 'SELECT sku, description FROM ' . cms_db_prefix() . 'module_sms_product_skus
		        ORDER BY sku';
		$dbresult = $db->Execute($sql);
		if (!$dbresult) {
			return false;
		}
		$sku_list = array();
		while ($row = $dbresult->FetchRow()) {
			$sku_list[$row['sku'] . ' - ' . $row['description']] = $row['sku'];
		}
		return $sku_list;
	}

	function BuildListSalesInventTiming()
	{
		$salesinventtiming = array();
		//$salesinventtiming[$this->Lang('salesinventtiming_int')] = 'INT';
		$salesinventtiming[$this->Lang('salesinventtiming_cnf')] = 'CNF';
		$salesinventtiming[$this->Lang('salesinventtiming_pay')] = 'PAY';
		$salesinventtiming[$this->Lang('salesinventtiming_shp')] = 'SHP';
		$salesinventtiming[$this->Lang('salesinventtiming_inv')] = 'INV';

		return $salesinventtiming;
	}

	function BuildThumb($image, $sourcedir, $thumbwidth = 0,  $thumbheight = 0)
	{

		ini_set("memory_limit", "30M");
		$imgsize = getimagesize($sourcedir . DIRECTORY_SEPARATOR . $image);
		if (!$imgsize) {
			return false;
		}

		if ($thumbwidth == 0 && $thumbheight == 0) {
			$targetsize[0] = $imgsize[0];	// Target Height
			$targetsize[1] = $imgsize[1];	// Target Width
		} elseif ($thumbwidth == 0 && $thumbheight != 0) {
			$targetsize[0] = round($thumbheight * ($imgsize[0] / $imgsize[1]));
			$targetsize[1] = $thumbheight;
		} elseif ($thumbwidth != 0 && $thumbheight == 0) {
			$targetsize[0] = $thumbwidth;
			$targetsize[1] = round($thumbwidth * ($imgsize[0] / $imgsize[1]));
		} else {
			if (round($thumbwidth * ($imgsize[0] / $imgsize[1])) > $thumbheight) {
				$targetsize[0] = $thumbheight;
				$targetsize[1] = round($thumbheight * ($imgsize[1] / $imgsize[0]));
			} else {
				$targetsize[0] = round($thumbwidth * ($imgsize[0] / $imgsize[1]));
				$targetsize[1] = $thumbwidth;
			}
		}
		$imagetype = $imgsize[2];
		// Type of image can have the following values
		// 1 = GIF 2 = JPG 3 = PNG
		// For other types see http://www.php.net/manual/en/function.exif-imagetype.php#

		$properties = array($targetsize[0], $targetsize[1], $imgsize[0], $imgsize[1]);

		switch ($imagetype) {
			case 1:
				$srcimg = imagecreatefromgif($sourcedir . DIRECTORY_SEPARATOR . $image);
				break;
			case 2:
				$srcimg = imagecreatefromjpeg($sourcedir . DIRECTORY_SEPARATOR . $image);
				break;
			case 3:
				$srcimg = imagecreatefrompng($sourcedir . DIRECTORY_SEPARATOR . $image);
				imagealphablending($srcimg, true);
				imagesavealpha($srcimg, true);
				break;
			default:
				return false;
		}

		$destinationimg = imagecreatetruecolor($properties[0], $properties[1]);

		if (
			imagecopyresampled($destinationimg, $srcimg, 0, 0, 0, 0, $properties[0], $properties[1], $properties[2], $properties[3])
			&&
			imagejpeg($destinationimg, $sourcedir . DIRECTORY_SEPARATOR . 'tn_' . $image)
		) {
			return true;
		} else {
			return false;
		}
	}

	function CalculateAttributePrice($price, $priceadjusttype, $priceadjustment)
	{
		switch ($priceadjusttype) {
			case 'P':
				$price = $price + $priceadjustment;
				break;
			case 'M':
				$price = $price - $priceadjustment;
				break;
			case 'T':
				$price = $price * $priceadjustment;
				break;
			case 'V':
				$price = $priceadjustment;
				break;
		}
		return $price;
	}

	/*---------------------------------------------------------
		 CheckItemOnStock($itemnumber, $itemtype)
		 Using this function a check is done if inventory is available
		 use: if ($this->CheckItemOnStock($attribute['itemnumber'], 'attr')) {
	        }
	     An array is returned with boolean if stock is available and
	     quantity available is also included
	  ---------------------------------------------------------*/
	function CheckItemOnStock($itemnumber, $itemtype)
	{
		// Initialize the Database
		$db = cmsms()->GetDb();
		$ItemOnStock = array();
		$ItemOnStock['onstock'] = false;

		// Function not in use. Will be used if inventory file will be included
		switch ($itemtype) {
			case 'prod':
				$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_products
					WHERE itemnumber = ?';
				$row = $db->GetRow($query, array($itemnumber));
				if ($row && $row['maxattributes'] > 0) {
					$ItemOnStock['onstock'] = true;
					$ItemOnStock['quantityonstock'] = $row['maxattributes'];
				}
				break;
			case 'attr':
				$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_product_attributes
					WHERE itemnumber = ?';
				$row = $db->GetRow($query, array($itemnumber));
				if ($row && $row['maxallowed'] > 0) {
					$ItemOnStock['onstock'] = true;
					$ItemOnStock['quantityonstock'] = $row['maxallowed'];
				}
				break;
		}
		return $ItemOnStock;
	}
	/*---------------------------------------------------------
	   DisplayErrorPage()
	   This is a simple function for generating error pages.
	  ---------------------------------------------------------*/
	function DisplayErrorPage($id, &$params, $return_id, $message = '')
	{
		$this->smarty->assign('title_error', $this->Lang('error'));
		$this->smarty->assign('message', $message);

		// Display the populated template
		echo $this->ProcessTemplate('error.tpl');
	}

	// Completely expand category tree
	function FillCatList($parent, $level, $returnid, $id)
	{
		$db = cmsms()->GetDb();

		$trans = cms_utils::get_module('TranslitAlias');

		$query = "SELECT `category_id`, `name`, `parent_id`
			FROM `" . cms_db_prefix() . "module_sms_categories`
			WHERE `category_id` <> 0 AND `parent_id` = ? AND `active` = 1
			ORDER BY `position`, `category_id`";
		$dbresult = $db->Execute($query, array($parent));
		$a = array();
		if (!$dbresult) {
			echo 'Error found: result = ' . $db->ErrorMsg() . '<br/>&nbsp;&nbsp;' . $db->sql . '<br/>'; #die();
		} else {
			$row = array();
			while ($dbresult && $row = $dbresult->FetchRow()) {

				// Check the number of active products connected to this category
				$query2 = 'SELECT count(*) as num_products FROM ' . cms_db_prefix() . 'module_sms_product_category pc,
					' . cms_db_prefix() . 'module_sms_products p WHERE pc.category_id=? AND pc.product_id = p.product_id AND
					p.active = 1';
				$dbresult2 = $db->Execute($query2, array($row['category_id']));
				$prodrow = $dbresult2->FetchRow();
				$row['num_products'] = $prodrow['num_products'];

				if ($trans) {

					$cat_name = $trans->Translit($row['name']);
				} else {
					$cat_name = munge_string_to_url($row['name']);
				}

				$destpage = (isset($detailpage) && $detailpage != '') ? $detailpage : $returnid;
				$sendtodetail = array('category_id' => $row['category_id'],	'detailpage' => $destpage);
				$prettyurl = 'SimpleShop/cat/' . $row['category_id'] . '/' . $destpage . '/' . $cat_name;
				if (isset($sendtodetail['detailtemplate'])) {
					$prettyurl .= '/d,' . $sendtodetail['detailtemplate'];
				}
				if ($row['num_products'] != 0) {
					$row['link'] = $this->CreateLink(
						$id,
						'fe_product_list',
						$destpage,
						$row['name'],
						$sendtodetail,
						'',
						false,
						false,
						'',
						true,
						$prettyurl
					);
				}
				$row['level'] = $level;

				$a[] = $row;

				// Process subcategories
				$b = $this->FillCatList($row['category_id'], $level + 1, $returnid, $id);
				// Add $b[] to the end of $a[]
				for ($j = 0; $j < count($b); $j++) {
					$a[] = $b[$j];
				}
			}
		}

		return $a;
	}

	// Completely expand category tree for use in the Quick Selector
	function FillQSCatList($parent, $level, $returnid)
	{
		$db = cmsms()->GetDb();
		//$old_fetchmode = $db->SetFetchMode('ADODB_FETCH_NUM');

		$query = "SELECT category_id, name, parent_id
			FROM " . cms_db_prefix() . "module_sms_categories
			WHERE category_id<>0 AND parent_id = ? AND active = 1
			ORDER BY position, name";
		$dbresult = $db->Execute($query, array($parent));
		$a = array();
		if (!$dbresult) {
			echo 'Error found: result = ' . $db->ErrorMsg() . '<br/>&nbsp;&nbsp;' . $db->sql . '<br/>'; #die();
		} else {
			$row = array();
			$id = 'm1_';
			while ($dbresult && $cat_row = $dbresult->FetchRow()) {
				$row[0] = $cat_row['category_id'];
				$row[1] = $cat_row['name'];
				$row[2] = $cat_row['parent_id'];
				// Check the number of active products connected to this category
				$query2 = 'SELECT count(*) as num_products FROM ' . cms_db_prefix() . 'module_sms_product_category pc,
					' . cms_db_prefix() . 'module_sms_products p WHERE category_id=? AND pc.product_id = p.product_id AND
					p.active = 1';
				$dbresult2 = $db->Execute($query2, array($row[0]));
				if ($dbresult2) $prodrow = $dbresult2->FetchRow();
				$row[4] = isset($prodrow['num_products']) ? $prodrow['num_products'] : 0;
				$row[1] = $this->CreateLink(
					$id,
					'category_list',
					$returnid,
					$row[1],
					array('current_category_id' => $row[0])
				);
				$row[3] = $level;

				$a[] = $row;
				// Process subcategories
				$b = $this->FillQSCatList($row[0], $level + 1, $returnid);
				// Add $b[] to the end of $a[]
				for ($j = 0; $j < count($b); $j++) {
					$a[] = $b[$j];
				}
			}
		}

		//$db->SetFetchMode($old_fetchmode);
		return $a;
	}

	function FormatPrice($price)
	{
		// Check if CartMS has been installed
		$modops = cmsms()->GetModuleOperations();
		$CartMS = $modops->get_module_instance('SimpleCart');
		if ($CartMS) {
			$decimalpositions = $CartMS->GetPreference('numberformatdecimals', 2);
			$decimalseperator = $CartMS->GetPreference('numberformatdec_point', '.');
			$thousandseperator = $CartMS->GetPreference('numberformatthousand_sep', '');
		} else {
			$decimalpositions = $this->GetPreference('decimalpositionsprice', 2);
			$decimalseperator = $this->GetPreference('decimalseparatorprice', '.');
			$thousandseperator = $this->GetPreference('thousandseparatorprice', '');
		}
		return number_format($price, $decimalpositions, $decimalseperator, $thousandseperator);
	}

	function FormatWeight($weight)
	{
		$decimalpositions = $this->GetPreference('decimalpositionsweight', 3);
		$decimalseperator = $this->GetPreference('decimalseparatorweight', '.');
		$thousandseperator = $this->GetPreference('thousandseparatorweight', '');
		return number_format($weight, $decimalpositions, $decimalseperator, $thousandseperator);
	}

	/*---------------------------------------------------------
	   GetHeaderHTML()
	   This function inserts javascript (and links) into header of HTML
	  ---------------------------------------------------------*/
	function GetHeaderHTML()
	{
		// Include script so sorting of tables in backend is possible
		$javascript = '<script src="/modules/SimpleShop/js/jquery.tablesorter.min.js"></script>' . "\n";
		$javascript .= '<link href="/modules/SimpleShop/css/theme.metro-dark.min.css" rel="stylesheet">' . "\n";
		$javascript .= '<script id="js">jQuery(document).ready(function()
		{
			jQuery(".tablesorter")
				.tablesorter({theme: "metro-dark"});
		}
		);
		</script>';

		return $javascript;
	}

	function ImageAddToCartSource()
	{
		$config = cmsms()->GetConfig();
		// Prepare link with image (Feat Req#3324)
		return cms_join_path(
			$config['root_url'],
			'modules',
			$this->GetName(),
			'images',
			'addtocart.gif'
		);
	}

	function RebuildThumbnails($sourcedir, $type = 'products')
	{
		$images = array();
		// Set pattern
		$pattern = "tn_*";
		// Change to named directory
		chdir($sourcedir);
		// Find files matching pattern
		$images = glob($pattern);
		// Iterate over files array and delete existing thumbnails
		foreach ($images as $f) {
			unlink($f);
		}

		// Prepare an array with the available images
		$pattern = "*";
		$images = glob($pattern);
		$thumbwidth = $this->GetPreference('tnwidth_product', '100');
		$thumbheight = $this->GetPreference('tnheight_product', '100');
		// Use next image found in list of available images and prepare thumbnail
		foreach ($images as $f) {
			$this->BuildThumb($f, $sourcedir, $thumbwidth, $thumbheight);
		}

		return;
	}

	// Function that is used by the search module
	function SearchResultWithParams($returnid, $product_id, $attr = '', $parms)
	{
		$result = array();
		if ($attr == 'product') {
			$db = cmsms()->GetDb();
			$q = "SELECT name FROM " . cms_db_prefix() . "module_sms_products WHERE product_id = ?";
			$dbresult = $db->Execute($q, array($product_id));
			if ($dbresult) {
				$row = $dbresult->FetchRow();
				// 0 position is the prefix displayed in the list results.
				$result[0] = $this->GetFriendlyName();

				// 1 position is the title
				$result[1] = $row['name'];


				// Page to use for the product-details:
				if (isset($parms['detailpage'])) {
					$manager = cmsms()->GetHierarchyManager();
					$node = $manager->sureGetNodeByAlias($parms['detailpage']);
					if (isset($node)) {
						$detailpage = $node->getID();
					} else {
						$node = $manager->sureGetNodeById($parms['detailpage']);
						if (isset($node)) {
							$detailpage = $parms['detailpage'];
						}
					}
				}
				if (!isset($detailpage) || $detailpage == '') $detailpage = $returnid;

				$trans = cms_utils::get_module('TranslitAlias');

				if ($trans) {
					$aliased_title = $trans->Translit($row['name']);
				} else {
					$aliased_title = munge_string_to_url($row['name']);
				}

				$prettyurl = 'SimpleShop' . '/prod/' . $product_id . '/' . $detailpage . "/$aliased_title";

				// 2 position is the URL to the title.
				$parms = array();
				$parms['product_id'] = $product_id;

				$result[2] = $this->CreateLink('cntnt01', 'fe_product_detail', $detailpage, '', $parms, '', true, false, '', true, $prettyurl);
			}
		}
		return $result;
	}

	/*---------------------------------------------------------
		This function will remove all the search index entries of this module
		and after rebuild the index
	  ---------------------------------------------------------*/
	function SearchReindex()
	{

		$modops = cmsms()->GetModuleOperations();
		$searchmodule = $modops->get_module_instance('Search');
		if ($searchmodule != FALSE) {
			$db = cmsms()->GetDb();
			// First remove all the search index entries
			$searchmodule->DeleteWords($this->GetName());
			// Add index entries for the active products
			$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_products
				WHERE active = 1';
			$dbresult = &$db->Execute($query);
			while ($dbresult && $row = $dbresult->FetchRow()) {
				$searchmodule->AddWords($this->GetName(), $row['product_id'], 'product', $row['name'] . ' ' . $row['description']);
			}
			// Attributes are not included in the search since they are only available
			// via products (there doesn't exist a attribute detail page)
		}
	}

	/*---------------------------------------------------------
		This function allow retrieval from preferences from this module
		when needed in another module
	  ---------------------------------------------------------*/
	function GetSMSPreference($preference, $default = '')
	{
		return $this->GetPreference($preference, $default);
	}

	/**
	 * Create a new folder.
	 * @param string $newFolder specifiy the full path of the new folder.
	 * @return boolean true if the new folder is created, false otherwise.
	 */
	function createFolder($newFolder)
	{
		@mkdir($newFolder, 0777);
		return chmod($newFolder, 0777);
	}

	/**
	 * Delete a file.
	 * @param string $file file to be deleted
	 * @return boolean true if deleted, false otherwise.
	 */
	function delFile($file)
	{
		if (is_file($file))
			return unlink($file);
		else
			return false;
	}

	/**
	 * Append a / to the path if required.
	 * @param string $path the path
	 * @return string path with trailing /
	 */
	function fixPath($path)
	{
		//append a slash to the path if it doesn't exists.
		if (!(substr($path, -1) == '/'))
			$path .= '/';
		return $path;
	}


	/**
	 * Copy a file from source to destination. If unique == true, then if
	 * the destination exists, it will be renamed by appending an increamenting
	 * counting number.
	 * @param string $source where the file is from, full path to the files required
	 * @param string $destination_file name of the new file, just the filename
	 * @param string $destination_dir where the files, just the destination dir,
	 * e.g., /www/html/gallery/
	 * @param boolean $unique create unique destination file if true.
	 * @return string the new copied filename, else error if anything goes bad.
	 */
	function copyFile($source, $destination_dir, $destination_file, $unique = true)
	{
		if (!(file_exists($source) && is_file($source)))
			return false;

		$destination_dir = $this->fixPath($destination_dir);

		if (!is_dir($destination_dir))
			return false;

		$filename = $destination_file;

		if ($unique) {
			$dotIndex = strrpos($destination_file, '.');
			$ext = '';
			if (is_int($dotIndex)) {
				$ext = substr($destination_file, $dotIndex);
				$base = substr($destination_file, 0, $dotIndex);
			}
			$counter = 0;
			while (is_file($destination_dir . $filename)) {
				$counter++;
				$filename = $base . '_' . $counter . $ext;
			}
		}

		if (!copy($source, $destination_dir . $filename))
			return false;

		//verify that it copied, new file must exists
		if (is_file($destination_dir . $filename))
			return $filename;
		else
			return false;
	}
}
