<?php

if (isset($params['submit'])) {

	$params['image'] = (isset($params['image']) && !empty($params['image'])) ? $params['image'] : $params['old_image'];

	foreach ($_FILES as $uploadfile) {
		if ($uploadfile['size'] == 0) continue;

		$config = $gCms->GetConfig();
		$path = $config['image_uploads_path'] . $this->GetPreference('imagepath_category');

		$this->createFolder($path);

		$this->copyFile($uploadfile['tmp_name'], $path, $uploadfile['name'], true);
		$params['image'] = $uploadfile['name'];

		$this->delFile($path . $params['old_image']);
	}

	// Restore the name of the image if no new image has been uploaded
	$this->categories->Update($params);

	$params = array(
		'active_tab' => 'categories',
		'current_category_id' => $params['parent_id'],
		'tab_message' => $this->Lang('categoryupdate')
	);
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
// All actions handled, now get all the information linked to the product
if (isset($params['category_id'])) {
	$category_data = $this->categories->Get($params['category_id']);
}
if (isset($params['cancel'])) {
	$params = array(
		'active_tab' => 'categories',
		'current_category_id' => $params['parent_id'],
		'tab_message' => $this->Lang('cancel')
	);
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

$category = array();
$category['startform'] = $this->CreateFormStart($id, 'category_edit', $returnid, 'post', 'multipart/form-data');
$category['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('submit'));
$category['cancel'] = $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel'));
$category['endform'] = $this->CreateFormEnd();
$category['current_category_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'current_category_id', $params['current_category_id'])
);
$category['category_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'category_id', $params['category_id'])
);
$category['parent_id'] = array(
	'label' => $this->Lang('label_category_parent'),
	'input' => $this->CreateInputDropdown($id, 'parent_id', $this->categories->BuildTotalList($params['category_id']), -1, intval($category_data['parent_id']))
);
$category['name'] = array(
	'label' => $this->Lang('label_category_name'),
	'input' => $this->CreateInputText($id, 'name', $category_data['name'], 40, 40, 'required')
);
$category['description'] = array(
	'label' => $this->Lang('label_category_description'),
	'input' => $this->CreateTextArea(true, $id, $category_data['description'], 'cdescription', 'pagesmalltextarea', '', '', '', 40, 40)
);
$category['image'] = array(
	'label' => $this->Lang('label_category_image'),
	'input' => $this->CreateInputText($id, 'image', $category_data['image'], 40, 40) . ' ' . $this->CreateInputFile($id, 'new_image', '', 40)
);
$category['old_image'] = array(
	'input' => $this->CreateInputHidden($id, 'old_image', $category_data['image'])
);
$category['active'] = array(
	'label' => $this->Lang('label_category_active'),
	'input' => $category_active = $this->CreateInputCheckbox($id, 'active', 1, $category_data['active'])
);
$category['position'] = array(
	'label' => $this->Lang('label_category_position'),
	'input' => $this->CreateInputText($id, 'position', $category_data['position'], 40, 40)
);
$smarty->assign('category', $category);
echo $this->ProcessTemplate('category_edit.tpl');
