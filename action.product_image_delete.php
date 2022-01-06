<?php
// This program is called from product_edit, when icon delete image is used

// Build the path the image was uploaded to
$config = $gCms->GetConfig();

$path = $config['image_uploads_path'] . '/' . $this->GetPreference('imagepath_product');

$name = $path . $params['image'];
// Site umask set to 0022 works ok during tests, when permission denied was stated.
if ($this->delFile($name)) {
	echo 'File ' . $name . ' deleted<br>';
} else {
	echo 'Error during deletion of file: ' . $name . '<br>';
}
$name = $path . 'tn_' . $params['image'];
if ($this->delFile($name)) {
	echo 'Thumbnail ' . $name . ' deleted<br>';
} else {
	echo 'Error during deletion of thumbnail: ' . $name . '<br>';
}

// Now that all info is known, insert a record in the images database
$this->products->DeleteImage($params);
$params = array(
	// Product id, always handy to pass. In this case needed to get all the connected information
	'product_id' => $params['product_id'],
	// Pass the current category otherwise user will be brought to root once cancel is pressed
	'current_category_id' => $params['current_category_id'],
	// Prepare a message to be shown on top of the images overview
	'tab_message' => $this->Lang('message_imagedeleted', $params['image']),
	// Redirect back to images, so we can see the result
	'active_tab' => 'images'
);
$this->Redirect($id, 'product_edit', $returnid, $params);
