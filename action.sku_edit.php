<?php
if (isset($params['submit'])) {
	$this->products->UpdateSKU($params);
	
	$params = array('active_tab'=>'skus', 'tab_message' => $this->Lang('skuupdated'));	
	$this->Redirect( $id, 'defaultadmin', $returnid, $params );
}
if (isset($params['cancel'])) {
	$params = array('active_tab'=>'skus');
	$this->Redirect( $id, 'defaultadmin', $returnid, $params );
}
// All actions handled, now get all the information linked to the Stock Keeping Unit
if (isset($params['sku'])) {
	$sku_data = $this->products->GetSKU($params['sku']);
}

$sku = array();
$sku['startform'] = $this->CreateFormStart( $id, 'sku_edit', $returnid, 'post');
$sku['submit'] = $this->CreateInputSubmit ($id, 'submit', $this->Lang('submit'));
$sku['cancel'] = $this->CreateInputSubmit ($id, 'cancel', $this->Lang('cancel'));
$sku['endform'] = $this->CreateFormEnd();
$sku['sku'] = array(
	'label'=>'',
	'input'=>$this->CreateInputHidden( $id, 'sku', $params['sku'])
);
$sku['name'] = array(
	'label'=>$this->Lang('label_sku_name'),
	'input'=>$sku_data['sku']
);
$sku['description'] = array(
	'label'=>$this->Lang('label_sku_description'),
	'input'=>$this->CreateInputText( $id, 'description', $sku_data['description'], 60, 100, 'class="defaultfocus"')
);
$smarty->assign('sku', $sku);
echo $this->ProcessTemplate('sku_edit.tpl');
