<?php
class SMSProducts
{

	var $module;
	var $taboptarray;
	var $config;

	function SMSProducts($module)
	{
		$this->module = $module;
		$this->taboptarray = array('mysql' => 'ENGINE=MyISAM');
		$this->config = $this->module->GetConfig();
	}

	function Get($product_id)
	{
		$db = cmsms()->GetDb();
		$sql = "SELECT product_id, name, description, price,
				active, featured, netweight, vatcode, sku,
				itemnumber, position, maxattributes
		    FROM " . cms_db_prefix() . "module_sms_products
				WHERE product_id=?";
		$dbresult = $db->Execute($sql, array($product_id));
		if (!$dbresult) {
			return false;
		}
		$product = $dbresult->FetchRow();
		/**
		 * Get the categories this product belongs to.
		 */
		$product['categories'] = array();
		foreach ($this->GetCategoryMap($product_id) as $category_map) {
			if ($category_map['category_id'] == 0) $product['categories'][] = array(
				'category_id' => 0,
				'parent_id' => 0, 'name' => 'Root', 'description' => '', 'image' => '', 'active' => 1, 'position' => 0
			);
			$product['categories'][] = $this->module->categories->Get($category_map['category_id']);
		}

		/**
		 * Get the images for the product.
		 */
		$sql = "SELECT product_images_id, product_id, image, description
		        FROM " . cms_db_prefix() . "module_sms_product_images
		        WHERE product_id=?";
		$dbresult = $db->Execute($sql, array($product_id));

		while ($dbresult && $row = $dbresult->FetchRow()) {
			$product['images'][] = $row;
		}
		/**
		 * Get the attributes of the product.
		 */
		$sql = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_product_attributes
			WHERE product_id=?';
		$dbresult = $db->Execute($sql, array($product_id));

		while ($dbresult && $row = $dbresult->FetchRow()) {
			$product['attributes'][] = $row;
		}

		return $product;
	}

	function GetList($category_id = 0)
	{
		$db = cmsms()->GetDb();
		$sql = "SELECT " . cms_db_prefix() . "module_sms_products.product_id,
		               " . cms_db_prefix() . "module_sms_products.name,
		               " . cms_db_prefix() . "module_sms_products.description,
		               " . cms_db_prefix() . "module_sms_products.price,
		               " . cms_db_prefix() . "module_sms_products.active,
		               " . cms_db_prefix() . "module_sms_products.featured,
		               " . cms_db_prefix() . "module_sms_products.netweight,
		               " . cms_db_prefix() . "module_sms_products.vatcode,
		               " . cms_db_prefix() . "module_sms_products.sku,
		               " . cms_db_prefix() . "module_sms_products.itemnumber,
		               " . cms_db_prefix() . "module_sms_products.position,
		               " . cms_db_prefix() . "module_sms_products.maxattributes
				FROM " . cms_db_prefix() . "module_sms_products, " . cms_db_prefix() . "module_sms_product_category
				WHERE " . cms_db_prefix() . "module_sms_products.product_id = " . cms_db_prefix() . "module_sms_product_category.product_id
				AND " . cms_db_prefix() . "module_sms_product_category.category_id = ?
				ORDER BY " . cms_db_prefix() . "module_sms_products.position";
		$dbresult = $db->Execute($sql, array($category_id));
		if (!$dbresult) {
			return false;
		}
		$result = array();
		while ($row = $dbresult->FetchRow()) {
			// Make sure that the description doesn't span multiple lines
			$row['description'] = strip_tags($row['description']);
			$query = "SELECT `image` FROM `" . cms_db_prefix() . "module_sms_product_images` WHERE `product_id` = " .
				$row['product_id'] . " LIMIT 1";
			if ($db->GetOne($query)) {
				$row['image'] = $this->config['image_uploads_url'] . $this->module->GetPreference('imagepath_product') . 'tn_' . $db->GetOne($query);
			} else {
				$row['image'] = '';
			}
			$result[] = $row;
		}
		return $result;
	}
	function Create($product)
	{
		$db = cmsms()->GetDb();
		// If according to preferences only uppercase characters in item number, transform it
		if ($this->module->GetPreference('itemcapitalonly', false) == 1) {
			$product['itemnumber'] = strtoupper($product['itemnumber']);
		}
		$sql = 'INSERT INTO `' . cms_db_prefix() . 'module_sms_products` (`name`, `description`,
			`price`, `active`, `featured`, `netweight`, `vatcode`, `sku`, `itemnumber`, `position`,
			`maxattributes`)
			VALUES(?,?,?,?,?,?,?,?,?,?,?)';

		$dbresult = $db->Execute($sql, array(

			trim($product['name']),
			trim($product['description']),
			(float) trim($product['price']),
			(int) $product['active'],
			(int) $product['featured'],
			(float) trim($product['netweight']),
			$product['vatcode'],
			trim($product['sku']),
			trim($product['itemnumber']),
			(int) trim($product['position']),
			(int) trim($product['maxattributes'])
		));

		$product['product_id'] = $db->Insert_ID();

		if (!$dbresult) {
			return false;
		}

		// Add to search index
		$modops = cmsms()->GetModuleOperations();
		$searchmodule = $modops->get_module_instance('Search');
		if ($searchmodule != FALSE) {
			$text = $product['name'] . ' ' . $product['description'];
			$searchmodule->AddWords($this->module->GetName(), $product['product_id'], 'product', $text);
		}

		return $product['product_id'];
	}
	function CreateCategoryMap($category_id, $product_id)
	{
		$db = cmsms()->GetDb();
		// Don't allow twice the same entry, since deletion of one of the category/products is not possible anymore
		$sql = "SELECT * FROM " . cms_db_prefix() . "module_sms_product_category WHERE category_id=? AND product_id=?";
		$dbresult = $db->Execute($sql, array($category_id, $product_id));
		if (!$dbresult->FetchRow()) {
			$sql = "INSERT INTO " . cms_db_prefix() . "module_sms_product_category(category_id, product_id)
			        VALUES(?,?)";
			$dbresult = $db->Execute($sql, array($category_id, $product_id));
			if (!$dbresult) {
				return false;
			}
		}
		return true;
	}
	function DeleteCategoryMap($category_id, $product_id)
	{
		$db = cmsms()->GetDb();
		// Check if this is the last category that is connected.
		// If that is true, reset category to root, else product can't be found anymore
		$sql = "SELECT COUNT(category_id) as cnt FROM " . cms_db_prefix() . "module_sms_product_category WHERE product_id=?";
		$dbresult = $db->Execute($sql, array($product_id));
		$row = $dbresult->FetchRow();
		if ($row['cnt'] == 1) {
			// Reset to root
			$sql = "UPDATE " . cms_db_prefix() . "module_sms_product_category SET category_id = 0 WHERE product_id=?";
			$dbresult = $db->Execute($sql, array($product_id));
		} else {
			$sql = "DELETE FROM " . cms_db_prefix() . "module_sms_product_category WHERE category_id=? AND product_id=?";
			$dbresult = $db->Execute($sql, array(
				$category_id,
				$product_id
			));
			if (!$dbresult) return false;
		}
		return true;
	}

	function GetCategoryMap($product_id)
	{
		$db = cmsms()->GetDb();
		$sql = 'SELECT category_id
		        FROM ' . cms_db_prefix() . 'module_sms_product_category
		        WHERE product_id=?';
		$dbresult = $db->Execute($sql, array($product_id));
		if (!$dbresult) {
			return false;
		}
		$result = array();
		while ($row = $dbresult->FetchRow()) {
			$result[] = $row;
		}

		return $result;
	}

	function Update($product)
	{
		$db = cmsms()->GetDb();
		// If according to preferences only uppercase characters in item number, transform it
		if ($this->module->GetPreference('itemcapitalonly', false) == 1) {
			$product['itemnumber'] = strtoupper($product['itemnumber']);
		}
		$product['price'] = strtr($product['price'], ',', '.');
		$product['netweight'] = strtr($product['netweight'], ',', '.');
		$sql = 'UPDATE `' . cms_db_prefix() . 'module_sms_products` SET `name` = ?,
			`description` = ?, `price` = ?,	`active` = ?, `featured` = ?, `netweight` = ?,
			`vatcode` = ?, `sku` = ?, `itemnumber` = ?, `position` = ?, `maxattributes` = ?
			WHERE `product_id` = ?';
		$dbresult = $db->Execute($sql, array(
			trim($product['name']),
			$product['pdescription'],
			(float) trim($product['price']),
			(int) $product['active'],
			(int) $product['featured'],
			(float) trim($product['netweight']),
			$product['vatcode'],
			$product['sku'],
			trim($product['itemnumber']),
			(int) trim($product['position']),
			(int) trim($product['maxattributes']),
			(int) $product['product_id']
		));

		if (!$dbresult) {
			return false;
		}
		// Update search index (AddWords first deletes the old ones)
		$modops = cmsms()->GetModuleOperations();
		$searchmodule = $modops->get_module_instance('Search');
		if ($searchmodule != FALSE) {
			if ($product['active'] == 0) {
				$searchmodule->DeleteWords($this->module->GetName(), $product['product_id']);
			} else {
				$text = $product['name'] . ' ' . $product['pdescription'];
				$searchmodule->AddWords($this->module->GetName(), $product['product_id'], 'product', $text);
			}
		}

		return true;
	}
	function Delete($product)
	{
		$db = cmsms()->GetDb();
		// Delete the uploaded picture(s) for this product
		// Delete the picture(s)
		$sql = "DELETE FROM " . cms_db_prefix() . "module_sms_product_images
				WHERE product_id = ?";
		$dbresult = $db->Execute($sql, array($product['product_id']));
		// Delete the connection to the category
		$sql = "DELETE FROM " . cms_db_prefix() . "module_sms_product_category
				WHERE product_id = ?";
		$dbresult = $db->Execute($sql, array($product['product_id']));
		// Delete the attributes of this product
		$sql = "DELETE FROM " . cms_db_prefix() . "module_sms_product_attributes
				WHERE product_id = ?";
		$dbresult = $db->Execute($sql, array($product['product_id']));
		// Now it is possible to delete the product it self
		// Should a check been done to the Cart Made Simple tables? So delete orders????
		$sql = "DELETE FROM " . cms_db_prefix() . "module_sms_products
				WHERE product_id = ?";
		$dbresult = $db->Execute($sql, array($product['product_id']));
		if (!$dbresult) {
			return false;
		}
		// Delete from search index
		$modops = cmsms()->GetModuleOperations();
		$searchmodule = $modops->get_module_instance('Search');
		if ($searchmodule != FALSE) {
			$searchmodule->DeleteWords($this->module->GetName(), $product['product_id'], 'product');
		}

		return true;
	}

	// Check if item number has been used
	// The internal id is either the product or the attribute id
	// In case it is a new product or attricute, the internal id passed is -1
	function CheckDoubleItemNumber($itemnumber, $internal_id)
	{
		$db = cmsms()->GetDb();
		if ($itemnumber == '') {
			return false;
		}
		// Definition of a double number is that the passed item number doesn't exist
		// exept with the same product/attribute id
		$itemcapitalonly = $this->module->GetPreference('itemcapitalonly', false);
		if ($itemcapitalonly == 1) {
			$query = 'SELECT itemnumber, name FROM ' . cms_db_prefix() . 'module_sms_products
				WHERE UPPER(itemnumber) = UPPER(?) AND product_id <> ?';
		} else {
			$query = 'SELECT itemnumber, name FROM ' . cms_db_prefix() . 'module_sms_products
				WHERE itemnumber = ? AND product_id <> ?';
		}
		$row = $db->GetRow($query, array($itemnumber, $internal_id));
		if (!$row) {
			// No product found with the same number, let's check the attributes
			if ($itemcapitalonly == 1) {
				$query = 'SELECT itemnumber, name FROM ' . cms_db_prefix() . 'module_sms_product_attributes
					WHERE UPPER(itemnumber) = UPPER(?) AND attribute_id <> ?';
			} else {
				$query = 'SELECT itemnumber, name FROM ' . cms_db_prefix() . 'module_sms_product_attributes
					WHERE itemnumber = ? AND attribute_id <> ?';
			}
			$row = $db->GetRow($query, array($itemnumber, $internal_id));
			if (!$row) {
				return false;
			} else {
				return $row['name']  . ' (' . $this->module->Lang('label_attribute') . ')';
			}
		} else {
			// Part of message send back. Calling routine will prepare message to display
			return $row['name'] . ' (' . $this->module->Lang('label_product') . ')';
		}
	}

	// Attributes related functions
	// Retrieve the information related to a product attribute
	function GetAttribute($attribute_id)
	{
		$db = cmsms()->GetDb();
		$sql = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_product_attributes
		        WHERE attribute_id = ?';
		$dbresult = $db->Execute($sql, array($attribute_id));
		if (!$dbresult) {
			return false;
		}
		$result = '';
		while ($row = $dbresult->FetchRow()) {
			$result = $row;
		}
		return $result;
	}

	// Save new product attribute information
	function CreateAttribute($attribute)
	{
		$db = cmsms()->GetDb();
		// If according to preferences only uppercase characters in item number, transform it
		if ($this->module->GetPreference('itemcapitalonly', false) == 1) {
			$attribute['itemnumber'] = strtoupper($attribute['itemnumber']);
		}
		$sql = 'INSERT INTO `' . cms_db_prefix() . 'module_sms_product_attributes`
			(`product_id`, `name`, `description`, `minallowed`, `maxallowed`,
			`priceadjusttype`, `priceadjustment`, `displayonly`, `itemnumber`, `active`)
			VALUES(?,?,?,?,?,?,?,?,?,?)';
		$dbresult = $db->Execute($sql, array(
			(int) $attribute['product_id'],
			trim($attribute['name']),
			trim($attribute['adescription']),
			(int) trim($attribute['minallowed']),
			(int) trim($attribute['maxallowed']),
			$attribute['priceadjusttype'],
			(float) trim($attribute['priceadjustment']),
			(int) $attribute['displayonly'],
			trim($attribute['itemnumber']),
			(int) $attribute['active']
		));
		if (!$dbresult) {
			return false;
		}
		return true;
	}

	// Delete product attribute information
	function DeleteAttribute($params)
	{
		$db = cmsms()->GetDb();
		$sql = 'DELETE FROM ' . cms_db_prefix() . 'module_sms_product_attributes
			WHERE attribute_id = ?';
		$dbresult = $db->Execute($sql, array($params['attribute_id']));
		if (!$dbresult) {
			return false;
		}
		return true;
	}

	// Save changed product attribute information
	function UpdateAttribute($attribute)
	{
		$db = cmsms()->GetDb();
		// If according to preferences only uppercase characters in item number, transform it
		if ($this->module->GetPreference('itemcapitalonly', false) == 1) {
			$attribute['itemnumber'] = strtoupper($attribute['itemnumber']);
		}
		$sql = 'UPDATE ' . cms_db_prefix() . 'module_sms_product_attributes SET name = ?,
			description = ?, minallowed = ?, maxallowed = ?, priceadjusttype = ?,
			priceadjustment = ?, displayonly = ?, itemnumber = ?, active = ?
			WHERE attribute_id = ?';
		$dbresult = $db->Execute($sql, array(
			trim($attribute['name']),
			trim($attribute['description']),
			(int) trim($attribute['minallowed']),
			(int) trim($attribute['maxallowed']),
			$attribute['priceadjusttype'],
			(float) trim($attribute['priceadjustment']),
			(int) $attribute['displayonly'],
			trim($attribute['itemnumber']),
			(int) $attribute['active'],
			(int) $attribute['attribute_id']
		));
		if (!$dbresult) {
			return false;
		}
		return true;
	}

	// Images related functions
	// Retrieve the information related to a product image
	function GetImage($product_images_id)
	{
		$db = cmsms()->GetDb();
		$sql = "SELECT product_id, image, description
		        FROM " . cms_db_prefix() . "module_sms_product_images
		        WHERE product_images_id = ?";
		$dbresult = $db->Execute($sql, array($product_images_id));
		if (!$dbresult) {
			return false;
		}
		$result = '';
		while ($row = $dbresult->FetchRow()) {
			$result = $row;
		}
		return $result;
	}

	// Save new product image information
	function CreateImage($image)
	{
		$db = cmsms()->GetDb();
		$sql = "INSERT INTO " . cms_db_prefix() . "module_sms_product_images(product_id,
		                                                                 image,
		                                                                 description)
		        VALUES(?,?,?)";
		$dbresult = $db->Execute($sql, array(
			$image['product_id'],
			$image['image'],
			trim($image['idescription'])
		));
		if (!$dbresult) {
			echo "FALSE";
			return false;
		}
		return true;
	}

	// Delete image from folder and its product image information
	function DeleteImage($params)
	{
		// Now delete the image record
		$db = cmsms()->GetDb();
		$sql = "DELETE FROM " . cms_db_prefix() . "module_sms_product_images WHERE product_images_id = ? ";
		$dbresult = $db->Execute($sql, array($params['product_images_id']));
		if (!$dbresult) {
			echo "FALSE";
			return false;
		}
		return true;
	}
	// Save changed product image information
	function UpdateImage($params)
	{
		$db = cmsms()->GetDb();
		$sql = "UPDATE " . cms_db_prefix() . "module_sms_product_images SET description = ? WHERE product_images_id = ? ";
		$dbresult = $db->Execute($sql, array(
			trim($params['description']),
			$params['product_images_id']
		));
		if (!$dbresult) {
			echo "FALSE";
			return false;
		}
		return true;
	}

	// Save new product Stock Keeping Unit information
	function CreateSKU($sku)
	{
		$db = cmsms()->GetDb();
		$sql = 'INSERT INTO ' . cms_db_prefix() . 'module_sms_product_skus (sku, description)
		        VALUES(?,?)';
		$dbresult = $db->Execute($sql, array(trim($sku['sku']), $sku['description']));
		if (!$dbresult) {
			echo "FALSE";
			return false;
		}
		return true;
	}

	// Delete Stock Keeping Unit information
	function DeleteSKU($sku)
	{
		$db = cmsms()->GetDb();
		// It is not valid to delete if in use so check usage
		$sql = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_products WHERE sku = ? ';
		$dbresult = $db->Execute($sql, array($sku));
		$row = $dbresult->FetchRow();
		if (!isset($row['name'])) {
			$sql = 'DELETE FROM ' . cms_db_prefix() . 'module_sms_product_skus WHERE sku = ? ';
			$dbresult = $db->Execute($sql, array($sku));
			if (!$dbresult) {
				return $this->module->Lang('skuerror');
			}
		} else {
			// Let user know which product is the first that uses this SKU
			return $this->module->Lang('skuinuse');
		}
		return $this->module->Lang('skudeleted', $sku);
	}
	// Save changed Stock Keeping Unit information
	function UpdateSKU($params)
	{
		$db = cmsms()->GetDb();
		$sql = 'UPDATE ' . cms_db_prefix() . 'module_sms_product_skus SET description = ? WHERE sku = ? ';
		$dbresult = $db->Execute($sql, array($params['description'], $params['sku']));
		if (!$dbresult) {
			echo "FALSE";
			return false;
		}
		return true;
	}
	function GetSKU($sku)
	{
		$db = cmsms()->GetDb();
		$sql = 'SELECT * FROM ' . cms_db_prefix() . 'module_sms_product_skus WHERE sku = ? ';
		$dbresult = $db->Execute($sql, array($sku));
		$row = $dbresult->FetchRow();
		if (!$dbresult) {
			return false;
		}
		return $row;
	}
	function GetListSKU()
	{
		$db = cmsms()->GetDb();
		$sql = "SELECT * FROM " . cms_db_prefix() . "module_sms_product_skus ORDER BY sku";
		$dbresult = $db->Execute($sql);
		if (!$dbresult) {
			return false;
		}
		$result = array();
		while ($row = $dbresult->FetchRow()) {
			$result[] = $row;
		}
		return $result;
	}
}
