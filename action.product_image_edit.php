<?php
// This program allows the user to change image information
if (isset($params['submit']) && $params['submit'] == $this->Lang('submit')) {
	$this->products->UpdateImage($params);
	$params['active_tab'] = 'images';
	$params['tab_message'] = $this->Lang('imageedited');
	$this->Redirect($id, 'product_edit', $returnid, $params);
}
if (isset($params['cancel']) && $params['cancel'] == $this->Lang('cancel')) {
	$this->Redirect($id, 'product_edit', $returnid, array(
		'tab_message' => $this->Lang('cancel'),
		'product_id' => $params['product_id'],
		'active_tab' => 'images',
		'current_category_id' => $params['current_category_id']
	));
}
// Retrieve information for passed image id. Array will hold information afterwards
if (isset($params['product_images_id'])) {
	$product_image_data = $this->products->GetImage($params['product_images_id']);
}

// Prepare next screen information
$image = array();
$image['startform'] = $this->CreateFormStart($id, 'product_image_edit', $returnid, 'post', '');
$image['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('submit'));
$image['cancel'] = $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel'));
$image['endform'] = $this->CreateFormEnd();

$image['name'] = array(
	'label' => $this->Lang('column_image_name'),
	'output' => $product_image_data['image']
);
$image['description'] = array(
	'label' => $this->Lang('label_image_description'),
	'input' => $this->CreateInputText($id, 'description', $product_image_data['description'], 40, 255, 'style="width:95%"')
);
$image['product_images_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'product_images_id', $params['product_images_id'])
);
$image['current_category_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'current_category_id', $params['current_category_id'])
);
$image['product_id'] = array(
	'label' => '',
	'input' => $this->CreateInputHidden($id, 'product_id', $product_image_data['product_id'])
);

// Build the template to show
$smarty->assign('image', $image);
echo $this->ProcessTemplate('product_image_edit.tpl');
