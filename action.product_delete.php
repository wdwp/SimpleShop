<?php
$delete_product = $this->products->Get($params['current_product_id']);

$config = $gCms->GetConfig();
$path = $config['image_uploads_path'] . $this->GetPreference('imagepath_product');

$query = "SELECT `image` FROM `" . cms_db_prefix() . "module_sms_product_images` WHERE `product_id` = " . $params['current_product_id'];

$images = $db->GetAll($query);

foreach ($images as $image) {
    $name = $path . $image['image'];
    $tn = $path . 'tn_' . $image['image'];
    $this->delFile($name);
    $this->delFile($tn);
}

$this->products->Delete($delete_product);

$params = array('active_tab' => 'categories', 'current_category_id' => $params['current_category_id']);
$params['tab_message'] = $this->Lang('productdeleted');
$this->Redirect($id, 'defaultadmin', $returnid, $params);
