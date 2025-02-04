<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/

Db::getInstance()->Execute("DROP TABLE IF EXISTS "._DB_PREFIX_."lot_exportergoogle_last_stock_available");

Db::getInstance()->Execute("DROP TABLE IF EXISTS "._DB_PREFIX_."lot_exportergoogle_last_product");

Db::getInstance()->Execute("DROP TABLE IF EXISTS "._DB_PREFIX_."lot_exportergoogle_last_specific_price");


$sql = array();

$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'lot_exportergoogle_feeds`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'lot_exportergoogle_restrictions`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'lot_exportergoogle_modified_products`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'lot_exportergoogle_product_feed`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'lot_exportergoogle_category_map`';
$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'lot_exportergoogle_google_categories`';

//$sql = array();

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
