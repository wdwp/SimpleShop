<?php
if (isset($params['submit'])) {
	// User pressed continue so save the sku information
	$this->products->CreateSKU($params);

	$params = array('active_tab' => 'skus');
	$params['tab_message'] = $this->Lang('skuadded');
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

if (isset($params['cancel'])) {
	$params = array('active_tab' => 'skus',);
	$this->Redirect($id, 'defaultadmin', $returnid, $params);
}

$sku = array();
$sku['startform'] = $this->CreateFormStart($id, 'sku_add', $returnid, 'post');
$sku['submit'] = $this->CreateInputSubmit($id, 'submit', $this->Lang('continue'));
$sku['cancel'] = $this->CreateInputSubmit($id, 'cancel', $this->Lang('cancel'));
$sku['endform'] = $this->CreateFormEnd();


$sku['sku'] = array(
	'label' => $this->Lang('label_sku_name'),
	'input' => $this->CreateInputText($id, 'sku', '', 20, 20, 'required')
);
$sku['description'] = array(
	'label' => $this->Lang('label_sku_description'),
	'input' => $this->CreateInputText($id, 'description', '', 60, 100, 'required')
);

$smarty->assign('sku', $sku);
echo $this->ProcessTemplate('sku_add.tpl');
