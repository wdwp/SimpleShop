<?php
if (!isset($gCms)) exit;

$trans = cms_utils::get_module('TranslitAlias');

$detailpage = '';
if (isset($params['detailpage'])) {
    $manager = cmsms()->GetHierarchyManager();
    $node = $manager->sureGetNodeByAlias($params['detailpage']);
    if (isset($node)) {
        $content = &$node->GetContent();
        if (isset($content)) {
            $detailpage = $content->Id();
        }
    } else {
        $node = &$manager->sureGetNodeById($params['detailpage']);
        if (isset($node)) {
            $detailpage = $params['detailpage'];
        }
    }
}

// Display information of products
$query = 'SELECT `product_id`, `name`  FROM `' . cms_db_prefix() . 'module_sms_products` WHERE `active` = 1	ORDER BY `product_id`';

$dbresult = $db->Execute($query);

$entryarray = array();

while ($dbresult && $row = $dbresult->FetchRow()) {
    $onerow = new stdClass();

    if ($trans) {
        $prod_name = $trans->Translit($row['name']);
    } else {
        $prod_name = munge_string_to_url($row['name']);
    }

    $prettyurl = 'SimpleShop/prod/' . $row['product_id'] . '/' . ($detailpage != '' ? $detailpage : $returnid) . '/' . $prod_name;
    if (isset($params['detailtemplate'])) {
        $prettyurl .= '/d,' . $params['detailtemplate'];
    }

    $onerow->detail_url = $this->CreateLink(
        $id,
        'fe_product_detail',
        $detailpage != '' ? $detailpage : $returnid,
        '',
        array('product_id' => $row['product_id']),
        '',
        true,
        false,
        '',
        true,
        $prettyurl
    );

    $entryarray[] = $onerow;
}

// Display information of categories

$query2 = 'SELECT `category_id`, `name`  FROM `' . cms_db_prefix() . 'module_sms_categories` WHERE `active` = 1	ORDER BY `category_id`';

$dbresult2 = $db->Execute($query2);

while ($dbresult2 && $row2 = $dbresult2->FetchRow()) {

    $onerow = new stdClass();

    if ($trans) {
        $cat_name = $trans->Translit($row2['name']);
    } else {
        $cat_name = munge_string_to_url($row2['name']);
    }

    $prettyurl = 'SimpleShop/cat/' . $row2['category_id'] . '/' . ($detailpage != '' ? $detailpage : $returnid) . '/' . $cat_name;
    if (isset($params['detailtemplate'])) {
        $prettyurl .= '/d,' . $params['detailtemplate'];
    }

    $onerow->detail_url = $this->CreateLink(
        $id,
        'fe_product_list',
        $detailpage != '' ? $detailpage : $returnid,
        '',
        array('category_id' => $row2['category_id']),
        '',
        true,
        false,
        '',
        true,
        $prettyurl
    );

    $entryarray[] = $onerow;
}

$this->smarty->assign('items', $entryarray);

// Display template
cmsms()->set_content_type('text/xml');
echo $this->ProcessTemplate('sitemap.tpl');
