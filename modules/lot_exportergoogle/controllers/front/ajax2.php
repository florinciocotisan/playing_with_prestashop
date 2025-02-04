<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

require(_PS_MODULE_DIR_.'lot_exportergoogle/LotFeed.php');

class Lot_ExporterGoogleAjax2ModuleFrontController extends ModuleFrontController
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
        $id_lang=(int)Context::getContext()->cookie->id_lang;    
        $shop=new Shop($idshop);
        
        $output = '';
        
        if (Tools::getValue('test_feed')) {
 
            $total=15;
            //Configuration::updateValue('LOT_PROCESSED',0);       
            if (!Configuration::get('LOT_PROCESSED'))    
            $processedproducts=10;
            else $processedproducts=(int)Configuration::get('LOT_PROCESSED')+10;

            Configuration::updateValue('LOT_PROCESSED',$processedproducts);    



            $output=$total.'_'.$processedproducts;


            die(json_encode($output));
            
            }

            else if (Tools::getValue('initialize_feed')) {


                $id_feed=Tools::getValue('initialize_feed');
                $combsep=false;
                $onlyactive=true;
                $action=true;


                $getfeedsettings=Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_feeds WHERE id_feed='".pSQL($id_feed)."'");

                $getfeedrestrictions=Db::getInstance()->getRow("SELECT only_active, combsep, limba FROM "._DB_PREFIX_."lot_exportergoogle_restrictions WHERE id_feed='".pSQL($id_feed)."'");


                $id_lang = $getfeedsettings['id_lang'];

                $context = Context::getContext();
                if ((int)$getfeedsettings['currency'] > 0)
                $context->currency = new Currency((int)$getfeedsettings['currency']);


                if ($getfeedrestrictions) {
                if ($getfeedrestrictions['combsep'] == 0) $combsep=false;
                else $combsep=true;

                if ($getfeedrestrictions['only_active'] == 0) $onlyactive=false;
                else $onlyactive=true;

                }


                $processedproducts=Db::getInstance()->ExecuteS("SELECT DISTINCT id_product FROM "._DB_PREFIX_."lot_exportergoogle_product_feed WHERE id_feed='".pSQL($id_feed)."'"); 

                $prodsincludedinfeed=$lot_feed->getfeedProducts($id_feed,$onlyactive,$combsep);

                $total=count($prodsincludedinfeed);

                $prodsincludedinfeed=array_slice($prodsincludedinfeed,count($processedproducts),10);

                if ($total > 0) {
                foreach ($prodsincludedinfeed as $p) {

                if ((int)$combsep == 1) {  

                $pob=new Product($p['id_product'],$id_lang,true);     
                $combinations=$pob->getAttributeCombinations($id_lang,false);

                $uniquecombinations = array(); //fix for Prestashop 1.6    
                if ($combinations) {  
                 foreach ($combinations as $key=>$c) 
                    if (!in_array($c['id_product_attribute'], $uniquecombinations)) {
                        
                    if ($lot_feed->isCombPermitted($id_feed, $p['id_product'], $c['id_product_attribute'])) {   

                     $res=$lot_feed->generateproductcode($getfeedsettings['type'], $p['id_product'], $c['id_product_attribute'], $id_lang, $getfeedsettings['image_formats'], $context);

                     $action=Db::getInstance()->insert('lot_exportergoogle_product_feed', array(
                        'id_feed' => pSQL($id_feed),
                        'id_product' => pSQL($p['id_product']),
                        'id_product_attribute' => pSQL($c['id_product_attribute']),
                        'code' => pSQL($res)
                        ));
                    }

                    $uniquecombinations[] = $c['id_product_attribute'];    

                 }
                }
                else {

                    $res=$lot_feed->generateproductcode($getfeedsettings['type'], $p['id_product'], false, $id_lang, $getfeedsettings['image_formats'], $context);

                     $action=Db::getInstance()->insert('lot_exportergoogle_product_feed', array(
                        'id_feed' => pSQL($id_feed),
                        'id_product' => pSQL($p['id_product']),
                        'id_product_attribute' => 0,
                        'code' => pSQL($res)
                        ));

                }    

                }
                else { 

                $res=$lot_feed->generateproductcode($getfeedsettings['type'],$p['id_product'],false,$id_lang, $getfeedsettings['image_formats'], $context);

                     $action=Db::getInstance()->insert('lot_exportergoogle_product_feed', array(
                        'id_feed' => pSQL($id_feed),
                        'id_product' => pSQL($p['id_product']),
                        'id_product_attribute' => 0,
                        'code' => pSQL($res)
                        ));
                }

                }

                if ($action) {

                       $processedproducts=count($processedproducts)+10;
                       if ($processedproducts > $total) $processedproducts=$total; 

                       $output=$total.'_'.$processedproducts;


                   }  else $output=0;
                } else $output=1;

            
            die(json_encode($output));    

            }
        
        
    }
    
    
}

    
?>