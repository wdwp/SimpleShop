<?php
# Module: Shop Made Simple - 
# A product maintenance module for CMS - CMS Made Simple
# Copyright (c) 2011 by Duketown 
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

class sms_utils
{
	function get_products_subset($itemnumber = -1)
	{
		if ($itemnumber == -1) return false;

		$config = cmsms()->GetConfig();
		$module = cms_utils::get_module('SimpleShop');

		$db = cmsms()->GetDb();
		$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_products
			WHERE UPPER(itemnumber) like "%' . strtoupper($itemnumber) . '%"';
		$dbresult = $db->Execute($query);
		$productsubset = array();
		while ($row = $dbresult->FetchRow()) {
			// Make sure that the description doesn't span multiple lines			
			$query = "SELECT `image` FROM `" . cms_db_prefix() . "module_sms_product_images` WHERE `product_id` = " .
				$row['product_id'] . " LIMIT 1";
			$row['image'] = $config['image_uploads_url'] . $module->GetPreference('imagepath_product') . 'tn_' . $db->GetOne($query);

			$productsubset[] = $row;
		}

		$query = 'SELECT `product_id` FROM ' . cms_db_prefix() . 'module_sms_product_attributes
			WHERE UPPER(itemnumber) like "%' . strtoupper($itemnumber) . '%"';
		$dbresult = $db->Execute($query);

		while ($row = $dbresult->FetchRow()) {

			$query = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_products
			WHERE `product_id` = ' . $row['product_id'];
			$dbresult2 = $db->Execute($query);

			while ($row = $dbresult2->FetchRow()) {
				// Make sure that the description doesn't span multiple lines			
				$query = "SELECT `image` FROM `" . cms_db_prefix() . "module_sms_product_images` WHERE `product_id` = " .
					$row['product_id'] . " LIMIT 1";
				$row['image'] = $config['image_uploads_url'] . $module->GetPreference('imagepath_product') . 'tn_' . $db->GetOne($query);

				$productsubset[] = $row;
			}
		}

		return $productsubset;
	}
}
