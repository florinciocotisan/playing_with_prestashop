<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

require(_PS_MODULE_DIR_.'lot_exportergoogle/LotFeed.php');

class Lot_ExporterGoogleCronModuleFrontController extends ModuleFrontController
{
    
    public function __construct()
    {
        
        $token = Tools::getValue('token');
        if (!Tools::getValue('token') || empty($token)
            || !Tools::getValue('key')
            || md5(_COOKIE_KEY_ . Tools::getValue('key')) != Tools::getValue('token')
        ) {
            die($this->module->l('Bad token!'));
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
        
        $yesterday = date('Y-m-d H:i:s',time());
        
        $getfeeds=Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_feeds lf LEFT JOIN "._DB_PREFIX_."lot_exportergoogle_restrictions lr ON lf.id_feed=lr.id_feed");

            $combsep=false;
            $onlyactive=true;
            $action=true;


            //check if there are remaining not updated products
            $lastmodifieds =  false;

            $lastmodifieds = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_modified_products");
        
            $feedprods_all = array();    
                
            foreach ($getfeeds as $f) {
               $feedprods=$lot_feed->getfeedProducts($f['id_feed']); 
               $feedprods_all[$f['id_feed']] = array_column($feedprods, 'id_product');
            }    
                
        
            $allfeeds = call_user_func_array('array_merge', $feedprods_all);

            if (!empty($lastmodifieds)) $lastmodifieds = true;
            else $lastmodifieds = $lot_feed->getLastModified(array_unique($allfeeds));

            if ($lastmodifieds) {

               $getmodifiedprods=Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_modified_products LIMIT 100"); 

               foreach ($getmodifiedprods as $p) {
                   foreach ($getfeeds as $f) {
                    if (in_array($p['id_product'],$feedprods_all[$f['id_feed']])) {
                        if ($f['combsep'] == 0) $combsep=false;
                        else $combsep=true;

                        if ($f['only_active'] == 0) $onlyactive=false;
                        else $onlyactive=true;

                        $id_lang=$f['id_lang'];

                        Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."lot_exportergoogle_product_feed WHERE id_feed='".pSQL($f['id_feed'])."' AND id_product='".pSQL($p['id_product'])."'");

                         if ((int)$combsep == 1) {  

                $pob=new Product($p['id_product'],$id_lang,true);     
                $combinations=$pob->getAttributeCombinations($id_lang,false);
                if ($combinations) {  
                 foreach ($combinations as $key=>$c) { 


                     $res=$lot_feed->generateproductcode($f['type'], $p['id_product'], $c['id_product_attribute'], $id_lang, $f['image_formats']);

                     $action=Db::getInstance()->insert('lot_exportergoogle_product_feed', array(
                        'id_feed' => pSQL($f['id_feed']),
                        'id_product' => pSQL($p['id_product']),
                        'id_product_attribute' => pSQL($c['id_product_attribute']),
                        'code' => pSQL($res)
                        ));

                 }
                }
                else {

                    $res=$lot_feed->generateproductcode($f['type'],$p['id_product'],false,$id_lang, $f['image_formats']);

                     $action=Db::getInstance()->insert('lot_exportergoogle_product_feed', array(
                        'id_feed' => pSQL($f['id_feed']),
                        'id_product' => pSQL($p['id_product']),
                        'id_product_attribute' => 0,
                        'code' => pSQL($res)
                        ));

                }    

                }
                else { 

                $res=$lot_feed->generateproductcode($f['type'],$p['id_product'],false,$id_lang, $f['image_formats']);    

                     $action=Db::getInstance()->insert('lot_exportergoogle_product_feed', array(
                        'id_feed' => pSQL($f['id_feed']),
                        'id_product' => pSQL($p['id_product']),
                        'id_product_attribute' => 0,
                        'code' => pSQL($res)
                        ));
                }




                    }   

                       
                   $this->reparseFeed($f);       

                   }
                   
                   $today = date('Y-m-d H:i:s',time());
                   
                   Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."lot_exportergoogle_feeds SET date_upd='".pSQL($today)."' WHERE id_feed='".pSQL($f['id_feed'])."'");

                    Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."lot_exportergoogle_modified_products WHERE id_product='".pSQL($p['id_product'])."'");

               } 
            }

        
        
        $output = array('success' => 1, 'message' => $this->module->l('Cron job executed succesfully!'));
        
        die(json_encode($output));
        //$this->setTemplate('module:lot_exportergoogle/views/templates/front/cron.tpl');
    
        //return true;
    }
    
    
    public function reparseFeed($feedsettings) {
        
        $url = $this->context->link->getModuleLink($this->module->name, 'feed', array('key' => $this->module->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->module->secure_key))).'&id_feed='.$feedsettings['id_feed'];
        
        $this->recacheFeed($url);
        
    }
    
    
    public function recacheFeed($url, $agent = "desktop")
    {
        // return;
        if($agent == "desktop")
            $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0 EXPRESSCACHE_BOT';
        else 
            $user_agent = 'Mozilla/5.0 (Linux; U; Android 4.0.3; ko-kr; LG-L160L Build/IML74K) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30 EXPRESSCACHE_BOT';

        $options = array(

            CURLOPT_CUSTOMREQUEST => 'GET',        //set request type post or get
            CURLOPT_POST => false,        //set to GET
            CURLOPT_USERAGENT => $user_agent, //set user agent
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING => '',       // handle all encodings
            CURLOPT_AUTOREFERER => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT => 120,      // timeout on response
            CURLOPT_MAXREDIRS => 5,       // stop after 10 redirects
        );

        $url .= (parse_url($url, PHP_URL_QUERY) ? '&' : '?').'refresh_cache=1';

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $err = curl_errno($ch);
        $errmsg = curl_error($ch);
        $header = curl_getinfo($ch);
        curl_close($ch);

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;
        // var_dump($header);
        //return $header;
    }    
 
    
    
}

    
    
?>