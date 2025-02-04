<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/

$sql = array();

$sql[] = 'DROP TABLE `'._DB_PREFIX_.'ecd_headeralerts`';
$sql[] = 'DROP TABLE `'._DB_PREFIX_.'ecd_headeralerts_lang`';
$sql[] = 'DROP TABLE `'._DB_PREFIX_.'ecd_headeralerts_design`';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}