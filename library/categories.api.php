<?php
class Categories
{

	var $module;
	var $taboptarray;

	/*---------------------------------------------------------
	   Categories()
	   This function defines the class Categories
	  ---------------------------------------------------------*/
	function Categories(&$module)
	{
		$this->module = $module;
		$this->taboptarray = array('mysql' => 'ENGINE=MyISAM');
	}

	/*---------------------------------------------------------
	   Get()
	   This function retrieves category information based upon passed category id
	  ---------------------------------------------------------*/
	function Get($category_id)
	{
		// Retrieve information on the category by id
		$db = &$this->module->GetDb();
		$dict = NewDataDictionary($db);
		$sql = "SELECT category_id,
		               parent_id,
		               name,
		               description,
		               image,
		               active,
		               position
		        FROM " . cms_db_prefix() . "module_sms_categories
		        WHERE category_id=?";
		$dbresult = $db->Execute($sql, array($category_id));
		if (!$dbresult) {
			return false;
		}
		return $dbresult->FetchRow();
	}

	/*---------------------------------------------------------
	   GetCatByName()
	   This function retrieves the categories that have a passed name. The first retrieved will be returned
	  ---------------------------------------------------------*/
	function GetCatByName($category_name)
	{
		// Retrieve information on the category by category name
		$db = &$this->module->GetDb();
		$dict = NewDataDictionary($db);
		$sql = "SELECT *
		        FROM " . cms_db_prefix() . "module_sms_categories
		        WHERE name=?";
		$dbresult = $db->Execute($sql, array($category_name));
		if (!$dbresult) {
			return false;
		}
		return $dbresult->FetchRow();
	}

	/*---------------------------------------------------------
	   GetList()
	   This function will return an array filled with categories that belong to the passed parent id.
	   Root id is taken if no parent id passed.
	  ---------------------------------------------------------*/
	function GetList($parent_id = 0)
	{
		$db = &$this->module->GetDb();

		$sql = "SELECT category_id,
		               parent_id,
		               name,
		               description,
		               image,
		               active,
		               position
		        FROM " . cms_db_prefix() . "module_sms_categories
		        WHERE parent_id=? ORDER BY position";
		$dbresult = $db->Execute($sql, array($parent_id));

		if (!$dbresult) {
			return false;
		}
		$result = array();
		while ($row = $dbresult->FetchRow()) {
			// Make sure that the description doesn't span multiple lines
			$row['description'] = strip_tags($row['description']);
			$result[] = $row;
		}

		return $result;
	}

	/*---------------------------------------------------------
	   EnableCategory()
	   This function will save a row with passed category information
	  ---------------------------------------------------------*/
	function Create(&$category)
	{
		$db = &$this->module->GetDb();
		$dict = NewDataDictionary($db);
		if (!is_numeric($category['position'])) $category['position'] = 0;
		$sql = "INSERT INTO " . cms_db_prefix() . "module_sms_categories(parent_id,
		                                                           name,
		                                                           description,
		                                                           image,
		                                                           active,
		                                                           position)
		        VALUES(?,?,?,?,?,?)";
		$dbresult = $db->Execute($sql, array(
			$category['parent_id'],
			trim($category['name']),
			$category['description'],
			$category['image'],
			$category['active'],
			trim($category['position'])
		));
		if (!$dbresult) {
			return false;
		}
		return true;
	}

	/*---------------------------------------------------------
	   Update()
	   This function will use the passed category information and save the values for the passed category id
	  ---------------------------------------------------------*/
	function Update(&$category)
	{

		$db = &$this->module->GetDb();
		$sql = "UPDATE " . cms_db_prefix() . "module_sms_categories
		        SET parent_id=?,
		            name=?,
		            description=?,
		            image=?,
		            active=?,
		            position=?
		        WHERE category_id=?";
		$dbresult = $db->Execute($sql, array(
			$category['parent_id'],
			trim($category['name']),
			$category['cdescription'],
			$category['image'],
			$category['active'],
			trim($category['position']),
			$category['category_id']
		));

		if (!$dbresult) {
			return false;
		}
		return true;
	}

	/*---------------------------------------------------------
	   Delete()
	   This function reset child categories to the root as parent. Next the category is deleted
	  ---------------------------------------------------------*/
	function Delete(&$category)
	{
		$db = &$this->module->GetDb();
		// First reset all connected products to root otherwise they are lost in 'space'
		$sql = 'UPDATE ' . cms_db_prefix() . 'module_sms_product_category SET category_id= 0 WHERE category_id=?';
		$dbresult = $db->Execute($sql, array(0));
		// Now that the category has no connections anymore it can be removed
		$sql = "DELETE FROM " . cms_db_prefix() . "module_sms_categories
		        WHERE category_id=?";
		$dbresult = $db->Execute($sql, array($category['category_id']));
		if (!$dbresult) {
			return false;
		}
		return true;
	}

	/*---------------------------------------------------------
	   BuildList()
	   This function build recursively a path to the category
	  ---------------------------------------------------------*/
	function BuildList($category_id = 0, $category_list = '', $level = 0)
	{
		$pad = str_pad("", $level, "-", STR_PAD_LEFT);
		if ($category_list == '') $category_list = array($this->module->Lang('root') => '0');
		$category = $this->Get($category_id);
		if (isset($category['name']) && $category['name'] != '') {
			$category_list[$category['name']] = $category['category_id'];
		}
		foreach ($this->GetList($category_id) as $category) {
			$name = $pad . " " . $category['name'];
			$category_list[$name] = $category['category_id'];
			$this->BuildList($category['category_id'], $category_list, $level + 1);
		}
		return $category_list;
	}

	/*---------------------------------------------------------
	   BuildTotalList()
	   This function selects all the categories and build a list of their names
	  ---------------------------------------------------------*/
	function BuildTotalList($category_id = -1)
	{
		$db = &$this->module->GetDb();
		$dict = NewDataDictionary($db);
		$sql = 'SELECT category_id, parent_id, name, description,
			image, active, position
			FROM ' . cms_db_prefix() . 'module_sms_categories
			WHERE category_id <> ?
			ORDER BY name';
		$dbresult = $db->Execute($sql, array($category_id));
		if (!$dbresult) {
			return false;
		}
		$category_list = array();
		$category_list[$this->module->Lang('root')] = '0';
		while ($row = $dbresult->FetchRow()) {
			$category_list[$row['name']] = $row['category_id'];
		}
		return $category_list;
	}

	/*---------------------------------------------------------
	   BuildPath()
	   This function builds an array with all information of categories in a path
	  ---------------------------------------------------------*/
	function BuildPath($category)
	{
		if (!$category) return;
		$path_array = array();
		$path_array[] = $category;
		while ($category['category_id'] != 0) {
			$category = $this->Get($category['parent_id']);
			if ($category) {
				$path_array[] = $category;
			} else {
				break;
			}
		}
		return array_reverse($path_array);
	}
}
