<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/

require(_PS_MODULE_DIR_.'lot_exportergoogle/LotFeed.php');

class Lot_ExporterGoogleGetDirectLinkModuleFrontController extends ModuleFrontController
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
        
        
        $id_feed=Tools::getValue('id_feed');
       

        $feedsettings=Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_feeds WHERE id_feed='".pSQL($id_feed)."'");
        
        if (file_exists(_PS_MODULE_DIR_.'lot_exportergoogle/feeds/fbfeed_'.$id_feed.'.'.$feedsettings['type'])) {

            $retururl = _PS_BASE_URL_.__PS_BASE_URI__.'modules/lot_exportergoogle/feeds/fbfeed_'.$id_feed.'.'.$feedsettings['type']; 

            $retururl = str_replace('http://',Tools::getShopProtocol(),$retururl);    

            die(json_encode($retururl));

        }     


        else {



                $feedcontent=Db::getInstance()->ExecuteS("SELECT code FROM "._DB_PREFIX_."lot_exportergoogle_product_feed WHERE id_feed='".pSQL($id_feed)."' ORDER BY id_product ASC");

                $beginning=$feedsettings['header'];
                $ending=$feedsettings['footer'];
                $content='';


                // feed settings
                $google_product_category = '';
                $refprefix = ($feedsettings['idprefix'] ? $feedsettings['idprefix'] : '');
                $roundprices = false;
                //$currency = ($feedsettings['currency'] ? $feedsettings['currency'] : Context::getContext()->currency->iso_code);
                $currency = '';

                $title=Configuration::get('PS_SHOP_NAME');

                $description=Db::getInstance()->getValue("SELECT ml.description FROM "._DB_PREFIX_."meta_lang ml INNER JOIN "._DB_PREFIX_."meta m ON ml.id_meta = m.id_meta WHERE m.page='index' AND ml.id_lang='".pSQL($feedsettings['id_lang'])."' AND ml.id_shop='".pSQL(Context::getContext()->shop->id)."'");

                if (!$description) $description = '';

                $shopurl=Tools::substr(Tools::getHttpHost(true).__PS_BASE_URI__,0,-1);
                // end feedsettings





                if ($feedsettings['type'] == 'xml') {


                $xml = new DOMDocument( "1.0", "UTF-8" );

                $xml->formatOutput = true;


                $rss = $xml->createElement('rss');

                $rssatt = $xml->createAttribute('version');
                $rssatt->value = "2.0";
                $rss->appendChild($rssatt);

                $rssatt = $xml->createAttribute('xmlns:g');
                $rssatt->value = "http://base.google.com/ns/1.0";
                $rss->appendChild($rssatt);

                $rssfield = $xml->appendChild($rss);

                $channel = $rssfield->appendChild($xml->createElement('channel'));

                //build header
                $channel->appendChild($xml->createElement('title'))->appendChild($xml->createCDATASection($title));
                $channel->appendChild($xml->createElement('description'))->appendChild($xml->createCDATASection($description));
                $channel->appendChild($xml->createElement('link'))->appendChild($xml->createCDATASection($shopurl));    


                //built feed

                     foreach ($feedcontent as $fp) {   
                        
                        $item=$channel->appendChild($xml->createElement('item')); 

                        $fps = json_decode($fp['code']);  

                        $item->appendChild($xml->createElement('g:id'))->appendChild($xml->createCDATASection($refprefix.$fps->id));

                        $item->appendChild($xml->createElement('g:title'))->appendChild($xml->createCDATASection(pSQL($fps->name)));
                        $item->appendChild($xml->createElement('g:description'))->appendChild($xml->createCDATASection(strip_tags($fps->description_short)));
                        $item->appendChild($xml->createElement('g:link'))->appendChild($xml->createCDATASection(pSQL($fps->url)));
                        $item->appendChild($xml->createElement('g:image_link'))->appendChild($xml->createCDATASection(pSQL($fps->cover_photo)));
                        $item->appendChild($xml->createElement('g:condition'))->appendChild($xml->createCDATASection(pSQL($fps->condition)));
                        $item->appendChild($xml->createElement('g:availability'))->appendChild($xml->createCDATASection(pSQL($fps->stock)));
                        $item->appendChild($xml->createElement('g:product_type'))->appendChild($xml->createCDATASection(pSQL($fps->type)));
                        $item->appendChild($xml->createElement('g:google_product_category'))->appendChild($xml->createCDATASection(pSQL($fps->google_product_category)));
                        $item->appendChild($xml->createElement('g:price'))->appendChild($xml->createCDATASection(pSQL($fps->price)));
                        $item->appendChild($xml->createElement('g:sale_price'))->appendChild($xml->createCDATASection(pSQL($fps->sale_price)));
                        $item->appendChild($xml->createElement('g:brand'))->appendChild($xml->createCDATASection(pSQL($fps->brand)));
                        $item->appendChild($xml->createElement('g:mpn'))->appendChild($xml->createCDATASection(pSQL($fps->reference)));


                     }

                        // end build feed     


                                               
                $xml->save(_PS_MODULE_DIR_.'lot_exportergoogle/feeds/feed_'.$id_feed.'.xml');    
                

                $retururl = _PS_BASE_URL_.__PS_BASE_URI__.'modules/lot_exportergoogle/feeds/feed_'.$id_feed.'.xml';

                $retururl = str_replace('http://',Tools::getShopProtocol(),$retururl);    

                
                die(json_encode($retururl));    



                }


                else if ($feedsettings['type'] == 'csv') {

                $produse=array();

                
                $produse[]=array('id', 'title', 'description', 'availability', 'product_type', 'google_product_category', 'condition', 'price', 'link', 'image_link', 'brand', 'sale_price', 'mpn');    


                    foreach ($feedcontent as $fp) {   

                        $fps = json_decode($fp['code']);
 
                        $rand=array($refprefix.$fps->id_product, $fps->name, $fps->description_short, $fps->stock, $fps->type, $fps->google_product_category, $fps->condition, $fps->price, $fps->url, $fps->cover_photo, $fps->brand, $fps->sale_price, $fps->reference);

                        $produse[]=$rand;

                    }


                                               
                $fp = fopen(_PS_MODULE_DIR_.'lot_exportergoogle/feeds/feed_'.$id_feed.'.csv','w');    
                $buffer = '';

                foreach ($produse as $fields)
                    {
                     fputcsv($fp, $fields, ";");
                     }

                fwrite($fp,$buffer);
                fclose($fp);   

                $retururl = _PS_BASE_URL_.__PS_BASE_URI__.'modules/lot_exportergoogle/feeds/feed_'.$id_feed.'.csv'; 

                $retururl = str_replace('http://',Tools::getShopProtocol(),$retururl);    

                die(json_encode($retururl));          


                }

            }
        
        
    }
    
}


?>