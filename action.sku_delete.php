<?php
$skumessage = $this->products->DeleteSKU($params['sku']);
$params = array('active_tab' => 'skus', 'skumessage' => $skumessage);
$params['tab_message'] = $skumessage;
$this->Redirect($id, 'defaultadmin', $returnid, $params);
