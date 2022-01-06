<?php
$delete_category = $this->categories->Get($params['current_category_id']);
/**
 * Get Any Subcategories and Reassign them to this ones parent
 */
foreach ($this->categories->GetList($delete_category['category_id']) as $category) {
	$category['parent_id'] = $delete_category['parent_id'];
	$this->categories->Update($category);
}
/* TODO: Get any Products in this Category and assign to Unassigned. */

$this->categories->Delete($delete_category);

$config = $gCms->GetConfig();
$path = $config['image_uploads_path'] . $this->GetPreference('imagepath_category');
$name = $path . $delete_category['image'];
// Site umask set to 0022 works ok during tests, when permission denied was stated.
$this->delFile($name);

$params = array(
	'active_tab' => 'categories',
	'current_category_id' => $delete_category['parent_id'],
	'tab_message' => $this->Lang('categorydeleted')
);
$this->Redirect($id, 'defaultadmin', $returnid, $params);
