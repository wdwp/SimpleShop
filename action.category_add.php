<?php
if (isset($params['submit'])) {
	/* TODO: Scale and do checks on filename on uploaded image */
	/* Build the path we will be uploading the image to */
	$config = $gCms->GetConfig();
	$path = $config['image_uploads_path'] . $this->GetPreference('imagepath_category');

	$this->createFolder($path);
	foreach ($_FILES as $uploadfile) {
		$this->copyFile($uploadfile['tmp_name'], $path, $uploadfile['name'], true);
		//print_r($uploadfile);
		$params['image'] = $uploadfile['name'];
	}
	$this->categories->Create($params);

	$params = array(
		'active_tab' => 'categories',
		'current_category_id' => $params['parent_id'],
		'tab_message' => $this->Lang('categoryadded')
	);
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
if (isset($params['cancel'])) {
	$params = array(
		'active_tab' => 'categories',
		'current_category_id' => $params['parent_id']
	);
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

$category = array();
$category['startform'] = $this->CreateFormStart($id, 'category_add', $returnid, 'post', 'multipart/form-data');
$category['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('submit'));
$category['cancel'] = $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel'));
$category['endform'] = $this->CreateFormEnd();
$category['id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'category_id', 0)
);
$category['parent_id'] = array(
	'label' => $this->Lang('label_category_parent'),
	'input' => $this->CreateInputDropdown($id, 'parent_id', $this->categories->BuildList($params['current_category_id'], '', 0), -1, intval($params['current_category_id']))
);
$category['name'] = array(
	'label' => $this->Lang('label_category_name'),
	'input' => $this->CreateInputText($id, 'name', '', 40, 255)
);
$category['description'] = array(
	'label' => $this->Lang('label_category_description'),
	'input' => $this->CreateTextArea(true, $id, '', 'description', 'pagesmalltextarea', '', '', '', 40, 40)
);
$category['image'] = array(
	'label' => $this->Lang('label_category_image'),
	'input' => $this->CreateInputFile($id, 'image', '', 40)
);
$category['active'] = array(
	'label' => $this->Lang('label_category_active'),
	'input' => $this->CreateInputCheckbox($id, 'active', 1)
);
$category['position'] = array(
	'label' => $this->Lang('label_category_position'),
	'input' => $this->CreateInputText($id, 'position', '', 40, 40)
);

$smarty->assign('category', $category);
echo $this->ProcessTemplate('category_edit.tpl');
