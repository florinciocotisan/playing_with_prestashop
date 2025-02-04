<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/

/* create feed update comparisson tables */

Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."lot_exportergoogle_last_stock_available LIKE "._DB_PREFIX_."stock_available");

Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."lot_exportergoogle_last_stock_available SELECT * FROM "._DB_PREFIX_."stock_available");

Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."lot_exportergoogle_last_product LIKE "._DB_PREFIX_."product");

Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."lot_exportergoogle_last_product SELECT * FROM "._DB_PREFIX_."product");

Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS "._DB_PREFIX_."lot_exportergoogle_last_specific_price LIKE "._DB_PREFIX_."specific_price");

Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."lot_exportergoogle_last_specific_price SELECT * FROM "._DB_PREFIX_."specific_price");


/* create module tables */

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lot_exportergoogle_feeds` (
`id_feed` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(100) NOT NULL,
`type` varchar(20) NOT NULL, 
`fielseparator` varchar(10) NOT NULL, 
`valueseparator` varchar(10) NOT NULL, 
`header` text NULL, `code` text NULL, 
`editcode` text NULL,
`footer` text NULL, 
`idprefix` varchar(50) NULL, 
`currency` varchar(20) NULL, 
`id_lang` int(11) NULL,
`image_formats` varchar(50) NULL, 
`date_add` datetime NULL, 
`date_upd` datetime NULL, 
PRIMARY KEY (`id_feed`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lot_exportergoogle_restrictions` (
`id_feed` int(11) NOT NULL,
`only_active` tinyint(1) NOT NULL,
`combsep` tinyint(1) NOT NULL, 
`filter_category` varchar(100) NOT NULL, 
`filter_manufacturer` varchar(100) NOT NULL, 
`filter_feature` varchar(100) NOT NULL, 
`filter_attribute` varchar(100) NOT NULL,
`limba` tinyint(1) NOT NULL, 
`restrictionshtml` text NULL, 
PRIMARY KEY (`id_feed`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lot_exportergoogle_modified_products` (
`id_product` int(11) NOT NULL,
PRIMARY KEY (`id_product`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lot_exportergoogle_product_feed` (
`id_feed` int(11) NOT NULL, `id_product` int(11) NOT NULL, `id_product_attribute` int(11) NULL, `code` text NOT NULL) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lot_exportergoogle_category_map` (
`id` int(11) NOT NULL AUTO_INCREMENT, `id_category` int(11) NOT NULL, `google_id` int(11) NULL, PRIMARY KEY (`id`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lot_exportergoogle_google_categories` (
`id` int(11) NOT NULL AUTO_INCREMENT, `google_id` int(11) NOT NULL, `google_value` varchar(1000) NULL, `google_parent` int(11) NULL, PRIMARY KEY (`id`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}





