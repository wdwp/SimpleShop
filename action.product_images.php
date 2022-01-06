<?php
// This program is called from product_edit, when button add image is used
if (isset($params['submit']) && $params['submit'] == $this->Lang('addimage')) {

	// Build the path we will be uploading the image to
	$config = $gCms->GetConfig();
	$path = $config['image_uploads_path'] . $this->GetPreference('imagepath_product');


	// Make sure the directory can be found. Create it, error handling will cover any problems found
	$folderexists = (is_dir($path)) ? true : $this->createFolder($path);

	if ($folderexists && count($_FILES) > 0) {
		foreach ($_FILES as $uploadfile) {
			if (empty($uploadfile['name'])) continue;
			// Replace any non-word characters by underscores
			// And make sure that name is not to long (bug# 7315)
			$exp_name = explode('.', $uploadfile['name']);
			$exp_ext = $exp_name[count($exp_name) - 1];
			$exp_name[count($exp_name) - 1] = "";
			$uploadfile['name'] = substr(
				implode('.', $exp_name),
				0,
				20
			) . $exp_ext;
			$res = $this->copyFile($uploadfile['tmp_name'], $path, $uploadfile['name'], true);

			if (!$res) {
				$params['message'] = $this->Lang('errorimageupload');
				continue;
			}

			$params['image'] = $uploadfile['name'];
			if ($uploadfile['name'] != '') {
				// Now that all info is known, insert a record in the images database
				$this->products->CreateImage($params);
				// Prepare the connected thumbnail so it can be requested at front end.
				$thumbwidth = $this->GetPreference('tnwidth_product', '100');
				$thumbheight = $this->GetPreference('tnheight_product', '100');
				$this->BuildThumb($uploadfile['name'], $path, $thumbwidth,  $thumbheight);
				// Prepare a message to be shown on top of the images overview
				$params['message'] = $this->Lang('message_imageadded', $uploadfile['name']);
			} else {
				$params['message'] = $this->Lang('message_noimagetoupload');
			}
		}
	} else {
		$params['message'] = $this->Lang('no_folder_available', $path);
	}
	// Redirect back to images, so we can see the result
	$params = array(
		'product_id' => $params['product_id'],
		'current_category_id' => $params['current_category_id'],
		'active_tab' => 'images',
		'tab_message' => $params['message']
	);
	$this->Redirect($id, 'product_edit', $returnid, $params);
}

if (isset($params['cancel']) && $params['cancel'] == $this->Lang('cancel')) {
	$params = array(
		'active_tab' => 'categories',
		'current_category_id' => $params['current_category_id']
	);
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}
