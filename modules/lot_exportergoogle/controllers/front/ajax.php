<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

require(_PS_MODULE_DIR_.'lot_exportergoogle/LotFeed.php');

class Lot_ExporterGoogleAjaxModuleFrontController extends ModuleFrontController
{
    
    public function __construct()
    {
        $token = Tools::getValue('token');
        if (!Tools::getValue('token') || empty($token)
            || !Tools::getValue('key')
            || md5(_COOKIE_KEY_ . Tools::getValue('key')) != Tools::getValue('token')
        ) {
            die($this->l('Bad token!'));
        }

        parent::__construct();
    }

    
    public function initContent()
    {
        
        $lot_exportergoogle = $this->module;
        $lot_feed = new LotFeed();
        $shop_link = Tools::getHttpHost(true).__PS_BASE_URI__;

        $idshop=(int)Context::getContext()->shop->id;
        $shop=new Shop($idshop);
        
        $output='';
        
            if (Tools::getValue('savefeed')) { 
        
            $restrictions=json_decode(Tools::getValue('restrictions'));  
            $feedsettings=json_decode(Tools::getValue('feedsettings'));


            if (Tools::getValue('capuritabel')) $header=Tools::getValue('capuritabel');
                else $header=Tools::getValue('feedheader');

            $dateupd=date('Y-m-d H:i:s',time());    

            if (Tools::getValue('feedid') > 0) {


                    $action=Db::getInstance()->update('lot_exportergoogle_feeds', array(
                    'name' => pSQL(Tools::getValue('feedname')),
                    'type' => pSQL(Tools::getValue('type')),
                    'fielseparator' => pSQL(Tools::getValue('fieldseparator')),
                    'valueseparator' => pSQL(Tools::getValue('valueseparator')),
                    'header' => pSQL($header,true),
                    'footer' => pSQL(Tools::getValue('feedfooter'),true),
                    'code' => pSQL(Tools::getValue('feedcontent'),true),
                    'editcode' => pSQL(Tools::getValue('feeddatahtml'),true),
                    'idprefix' => pSQL($feedsettings->idprefix),
                    'currency' => pSQL($feedsettings->currency),
                    'currency' => pSQL($feedsettings->currency),
                    'id_lang' => pSQL($feedsettings->id_lang),
                    'image_formats' => pSQL($feedsettings->image_formats),   
                    'date_upd' => pSQL($dateupd)          
                    ), "id_feed='".pSQL(Tools::getValue('feedid'))."'");


                $action=Db::getInstance()->update('lot_exportergoogle_restrictions', array(
                    'only_active' => pSQL($restrictions->only_active),
                    'combsep' => pSQL($restrictions->combsep),
                    'filter_category' => (count($restrictions->filter_category) > 1 ? pSQL(implode(',',$restrictions->filter_category)) : pSQL($restrictions->filter_category[0])),
                    'filter_manufacturer' => (count($restrictions->filter_manufacturer) > 1 ? pSQL(implode(',',$restrictions->filter_manufacturer)) : pSQL($restrictions->filter_manufacturer[0])),
                    'filter_feature' => (count($restrictions->filter_feature) > 1 ? pSQL(implode(',',$restrictions->filter_feature)) : pSQL($restrictions->filter_feature[0])),
                    'filter_attribute' => (count($restrictions->filter_attribute) > 1 ? pSQL(implode(',',$restrictions->filter_attribute)) : pSQL($restrictions->filter_attribute[0]))
                    ), "id_feed='".pSQL(Tools::getValue('feedid'))."'");



            }

                else {

                    $action=Db::getInstance()->insert('lot_exportergoogle_feeds', array(
                    'name' => pSQL(Tools::getValue('feedname')),
                    'type' => pSQL(Tools::getValue('type')),
                    'fielseparator' => pSQL(Tools::getValue('fieldseparator')),
                    'valueseparator' => pSQL(Tools::getValue('valueseparator')),
                    'header' => pSQL($header,true),
                    'footer' => pSQL(Tools::getValue('feedfooter'),true),
                    'code' => pSQL(Tools::getValue('feedcontent'),true),
                    'editcode' => pSQL(Tools::getValue('feeddatahtml'),true),
                    'idprefix' => pSQL($feedsettings->idprefix),
                    'currency' => pSQL($feedsettings->currency),
                    'id_lang' => pSQL($feedsettings->id_lang),
                    'image_formats' => pSQL($feedsettings->image_formats),      
                    'date_add' => pSQL($dateupd),
                    'date_upd' => pSQL($dateupd)     
                    ));

                    $lastidfeed=Db::getInstance()->getRow("SELECT id_feed FROM "._DB_PREFIX_."lot_exportergoogle_feeds ORDER BY id_feed DESC");


                    $action=Db::getInstance()->insert('lot_exportergoogle_restrictions', array(
                    'id_feed' => pSQL($lastidfeed['id_feed']),    
                    'only_active' => pSQL($restrictions->only_active),
                    'combsep' => pSQL($restrictions->combsep),
                    'filter_category' => (count($restrictions->filter_category) > 1 ? pSQL(implode(',',$restrictions->filter_category)) : pSQL($restrictions->filter_category[0])),
                    'filter_manufacturer' => (count($restrictions->filter_manufacturer) > 1 ? pSQL(implode(',',$restrictions->filter_manufacturer)) : pSQL($restrictions->filter_manufacturer[0])),
                    'filter_feature' => (count($restrictions->filter_feature) > 1 ? pSQL(implode(',',$restrictions->filter_feature)) : pSQL($restrictions->filter_feature[0])),
                    'filter_attribute' => (count($restrictions->filter_attribute) > 1 ? pSQL(implode(',',$restrictions->filter_attribute)) : pSQL($restrictions->filter_attribute[0]))
                    ));


                }


               if ($action) {

                   if (Tools::getValue('feedid') > 0)
                   $output=(int)Tools::getValue('feedid');
                   else $output=(int)$lastidfeed['id_feed'];

                   Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."lot_exportergoogle_product_feed WHERE id_feed='".pSQL($output)."'");

               }
               else $output=0;    

            //$res=$lot_feed->saveFeed($feedtype, $header, $content, $footer, $seperator);    


            }



            if (Tools::getValue('putrestrictions')) {

            $restrictionshtml=Tools::getValue('feedrestrictionshtml');

            //var_dump($restrictionshtml);    

            if (Tools::getValue('updatedfeed') == 0)  {
                $lastidfeed=Db::getInstance()->getRow("SELECT id_feed FROM "._DB_PREFIX_."lot_exportergoogle_feeds ORDER BY id_feed DESC");

            $updatefeed=$lastidfeed['id_feed'];
            } else $updatefeed=Tools::getValue('updatedfeed');

             $action=Db::getInstance()->update('lot_exportergoogle_restrictions', array(
                    'restrictionshtml' => pSQL($restrictionshtml,true)
                    ), "id_feed='".pSQL($updatefeed)."'");    

                //if ($action) $output=1;
               //else $output=0; 
            }




            if (Tools::getValue('deletefeed')) { 

                
                $action=Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."lot_exportergoogle_feeds WHERE id_feed='".pSQL(Tools::getValue('deletefeed'))."'");

                $action=Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."lot_exportergoogle_restrictions WHERE id_feed='".pSQL(Tools::getValue('deletefeed'))."'");

                $action=Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."lot_exportergoogle_product_feed WHERE id_feed='".pSQL(Tools::getValue('deletefeed'))."'");


                if ($action) $output=1;
               else $output=0;   
               
            }


            die(json_encode($output));

    
        }
    
    
    
    
 
}



    
?>