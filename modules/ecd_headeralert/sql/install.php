<?php
/**
* 2020 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2020 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ecd_headeralerts` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(200) NULL,
`date_begin` datetime NULL,
`date_end` datetime NULL, 
`active` tinyint(1) NOT NULL DEFAULT 0, 
PRIMARY KEY (`id`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ecd_headeralerts_lang` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`id_lang` int(11) NOT NULL,
`content` text NULL,
PRIMARY KEY (`id`, `id_lang`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ecd_headeralerts_design` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`background` varchar(50) NULL,
`color` varchar(50) NULL,
`font_size` varchar(50) NULL,
`other_design` TEXT NULL,
PRIMARY KEY (`id`)) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
