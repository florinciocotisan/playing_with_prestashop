<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/

class LotFeed extends Module {
    
    
    
    
public function eliminateNonUTFChars($string) {
    
    $string = str_replace(chr(0x08),'',$string);
    
    return $string;
    
}    
    

public function backupUpdateTables() {
    
    
    Db::getInstance()->Execute("DROP TABLE IF EXISTS "._DB_PREFIX_."lot_exportergoogle_last_stock_available");
    
    Db::getInstance()->Execute("CREATE TABLE "._DB_PREFIX_."lot_exportergoogle_last_stock_available LIKE "._DB_PREFIX_."stock_available");
    
    Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."lot_exportergoogle_last_stock_available SELECT * FROM "._DB_PREFIX_."stock_available");
    
    
    Db::getInstance()->Execute("DROP TABLE IF EXISTS "._DB_PREFIX_."lot_exportergoogle_last_product");
    
    Db::getInstance()->Execute("CREATE TABLE "._DB_PREFIX_."lot_exportergoogle_last_product LIKE "._DB_PREFIX_."product");
    
    Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."lot_exportergoogle_last_product SELECT * FROM "._DB_PREFIX_."product");
    
    
    Db::getInstance()->Execute("DROP TABLE IF EXISTS "._DB_PREFIX_."lot_exportergoogle_last_specific_price");
    
    Db::getInstance()->Execute("CREATE TABLE "._DB_PREFIX_."lot_exportergoogle_last_specific_price LIKE "._DB_PREFIX_."specific_price");
    
    Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."lot_exportergoogle_last_specific_price SELECT * FROM "._DB_PREFIX_."specific_price");
    
}    

    
public function getLastModified($feedsprods = array()) {    
    
   Db::getInstance()->Execute("TRUNCATE "._DB_PREFIX_."lot_exportergoogle_modified_products");
    
   $modifiedproducts=array();    

   $laststocks=Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_last_stock_available");
    
    
    if (!$laststocks) {
        return false;
    } else {
        
        $collect=Db::getInstance()->ExecuteS("SELECT cur.quantity AS newcant, old.quantity AS oldcant, cur.id_product FROM "._DB_PREFIX_."stock_available cur LEFT JOIN "._DB_PREFIX_."lot_exportergoogle_last_stock_available old ON cur.id_stock_available=old.id_stock_available WHERE cur.quantity<>old.quantity");
        
        if ($collect) {
            foreach ($collect as $c) {
                if (in_array($c['id_product'], $feedsprods) || empty($feedsprods))
                    $modifiedproducts[]=$c['id_product']; 
            }
                         
        }
        
        $collect=Db::getInstance()->ExecuteS("SELECT cur.date_upd AS newupdate, old.date_upd AS newupdate, cur.id_product FROM "._DB_PREFIX_."product cur LEFT JOIN "._DB_PREFIX_."lot_exportergoogle_last_product old ON cur.id_product=old.id_product WHERE cur.date_upd<>old.date_upd OR cur.price<>old.price");
        
        if ($collect) {
            foreach ($collect as $c) {
                if (in_array($c['id_product'], $feedsprods) || empty($feedsprods))
                    $modifiedproducts[]=$c['id_product'];
            }
        }
        
        // get discounts differences
        $oldiscounts=array();
        $modifieddiscounts=array();
        
        $getolddiscounts=Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_last_specific_price");
        if ($getolddiscounts)
        foreach ($getolddiscounts as $g) {
           $oldiscounts[$g['id_specific_price']]=implode(';',$g); 
        }
        
        
        $newdiscounts=Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."specific_price");
        if ($newdiscounts)
        foreach ($newdiscounts as $d) {
            if (in_array($d['id_product'], $feedsprods) || empty($feedsprods)) {
                $newdiscountsimple[$d['id_specific_price']]=implode(';',$d); 
               if (!isset($oldiscounts[$d['id_specific_price']]))               $modifiedproducts[]=$d['id_product']; 
               else {
                   $stringvalues=implode(';',$d); 
                   if ($oldiscounts[$d['id_specific_price']] != $stringvalues) $modifiedproducts[]=$d['id_product']; 
               }    
            }
        }
        
        
        if (($getolddiscounts) and ($newdiscounts)) {
            foreach ($getolddiscounts as $g) {
                if (!isset($newdiscountsimple[$g['id_specific_price']])) {
                    $splituire=explode(';',$oldiscounts[$g['id_specific_price']]);
                    if (in_array($splituire[3], $feedsprods) || empty($feedsprods))
                    $modifiedproducts[]=$splituire[3]; 
                } 
            }
        }
        
        
       if (!empty($modifiedproducts)) {
          $modifiedproducts=array_unique($modifiedproducts);
        foreach ($modifiedproducts as $mid) {
            $action=Db::getInstance()->insert('lot_exportergoogle_modified_products', array(
            'id_product' => pSQL($mid)   
            ));
        } 
              
       } 
        
        
       $this->backupUpdateTables();    
        
       return true;    
        
    
    }
    
    
    
    
}
    
    
    
public function isCombPermitted($id_feed, $id_product, $id_product_attribute) {
    
    $sqlrestrictions=Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_restrictions WHERE id_feed='".pSQL($id_feed)."'");
    
    if (!$sqlrestrictions) return true;
    
    $restrictions = array();
    
    if ($sqlrestrictions['filter_attribute'] != '') {
    foreach (explode(',',$sqlrestrictions['filter_attribute']) as $item) {
            $restrictions['attributes'][]=explode('-',$item)[1];
    }
    } else return true;
    
    if (!empty($restrictions['attributes'])) {
            $learepetoate = true;
            foreach ($restrictions['attributes'] as $aa) { 
                
            $hasattribute=Db::getInstance()->getRow("SELECT pac.id_attribute FROM "._DB_PREFIX_."product_attribute pa LEFT JOIN "._DB_PREFIX_."product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute WHERE pac.id_attribute='".pSQL($aa)."' AND pa.id_product='".pSQL($id_product)."' AND pa.id_product_attribute='".pSQL($id_product_attribute)."'");
                
            if (!$hasattribute) $learepetoate = false;
                
            }
              
      return $learepetoate;      
    } else return true;
    
    
    return true;
    
}    
    
    
public function getfeedProducts($id_feed, $active=true, $comb=false) {
    
    
    $sqlrestrictions=Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_restrictions WHERE id_feed='".pSQL($id_feed)."'");
    
    $sqlprods=Db::getInstance()->ExecuteS("SELECT p.id_product, p.id_category_default, p.id_manufacturer, GROUP_CONCAT(cp.id_category) AS categories FROM "._DB_PREFIX_."product p LEFT JOIN "._DB_PREFIX_."category_product cp ON cp.id_product=p.id_product ".(($sqlrestrictions['only_active'] == 1) ? "WHERE p.active='1'" : "")." GROUP BY p.id_product");
    
    $restrictions=array();
    
    if ($sqlrestrictions) { 
    if ($sqlrestrictions['filter_category'] != '') 
    foreach (explode(',',$sqlrestrictions['filter_category']) as $item) {
        $restrictions['categories'][]=$item;
        $cat=new Category($item); 
        foreach ($cat->getChildrenWs() as $c)
            $restrictions['categories'][]=$c['id'];
    }
    
    if ($sqlrestrictions['filter_manufacturer'] != '') 
    foreach (explode(',',$sqlrestrictions['filter_manufacturer']) as $item) {
            $restrictions['manufacturers'][]=$item;
    }
    
    
    if ($sqlrestrictions['filter_feature'] != '') 
    foreach (explode(',',$sqlrestrictions['filter_feature']) as $item) {
            $restrictions['features'][]=explode('-',$item)[1];
    }
    
    
    if ($sqlrestrictions['filter_attribute'] != '') 
    foreach (explode(',',$sqlrestrictions['filter_attribute']) as $item) {
            $restrictions['attributes'][]=explode('-',$item)[1];
    }

    }

    

    if (!empty($restrictions)) {
    $filteredprods=array();    
    foreach ($sqlprods as $sp) {
        $belongs=true;
        
        
        if ($belongs) {
        if (!empty($restrictions['categories'])) {
            
            if (isset($sp['categories'])) $product_cats = explode(',',$sp['categories']);
            else $product_cats=array();
            
            $belongs = false;
            
            foreach ($product_cats as $cp_id_category) {
                
                if (in_array($cp_id_category,$restrictions['categories'])) $belongs=true;
                
            }
            
            
        }
        }
        
        
        if ($belongs) {
            if (!empty($restrictions['manufacturers'])) {
            if (!in_array($sp['id_manufacturer'],$restrictions['manufacturers'])) $belongs=false;
            }
        }
        
        
        
        if ($belongs) {
            if (!empty($restrictions['features'])) {
            foreach ($restrictions['features'] as $ff) {    
            $hasfeatures=Db::getInstance()->getRow("SELECT id_feature_value FROM "._DB_PREFIX_."feature_product WHERE id_product='".pSQL($sp['id_product'])."' AND id_feature_value='".pSQL($ff)."'");
            if ($hasfeatures) break;
            }
                
            if (!$hasfeatures) $belongs=false;
            }
        }
        
        
        
        if ($belongs) {
            if (!empty($restrictions['attributes'])) {
            foreach ($restrictions['attributes'] as $aa) {    
            $hasattribute=Db::getInstance()->getRow("SELECT pac.id_attribute FROM "._DB_PREFIX_."product_attribute pa LEFT JOIN "._DB_PREFIX_."product_attribute_combination pac ON pa.id_product_attribute = pac.id_product_attribute WHERE pac.id_attribute='".pSQL($aa)."' AND pa.id_product='".pSQL($sp['id_product'])."'");
            if ($hasattribute) break;
            }
                
            if (!$hasattribute) $belongs=false;
            }
        }
        
        
        if ($belongs) $filteredprods[]=array('id_product' => $sp['id_product']);
        
        
    }
            
     return $filteredprods;
        
    }
    else return $sqlprods;
    
    
} 
    
    
public function getproductpath($id_product, $id_category_default = 0) {

  if ($id_category_default == 0)  {  
      $smallestcategory=Db::getInstance()->ExecuteS("SELECT cp.id_category FROM "._DB_PREFIX_."category_product cp LEFT JOIN "._DB_PREFIX_."category c ON cp.id_category = c.id_category WHERE cp.id_product='".pSQL($id_product)."' ORDER BY c.level_depth DESC LIMIT 1");

      $id_category = $smallestcategory[0]['id_category'];  
  } else {
      $id_category = $id_category_default;
  }
    
  if (Tools::substr(_PS_VERSION_,0,3) == '1.6')
  return strip_tags(Tools::getPath($id_category));
  else
  return $this->getCategoryBreadcrumb($id_category);
    
}  
  
    
public function getgooglecategory($id_product) {
    
  $smallestcategory=Db::getInstance()->ExecuteS("SELECT cp.id_category FROM "._DB_PREFIX_."category_product cp LEFT JOIN "._DB_PREFIX_."category c ON cp.id_category = c.id_category WHERE cp.id_product='".pSQL($id_product)."' ORDER BY c.level_depth DESC LIMIT 1");
  
  $google_parent = '';
   
    if (Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID')) {
            if (Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID') != '0') {  
                 $google_parent = Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID');   
            }
    }
    
    if ($smallestcategory) {
        
        $parentid = $smallestcategory[0]['id_category']; 
        
        while ($parentid > 2) {
            
            $getmatch = Db::getInstance()->getRow("SELECT google_id FROM "._DB_PREFIX_."lot_exportergoogle_category_map WHERE id_category='".pSQL($parentid)."'");
            
            if ($getmatch) {
               $google_parent = $getmatch['google_id'];
               break;    
            } else {
                
               $parentid = Db::getInstance()->getValue("SELECT id_parent FROM "._DB_PREFIX_."category WHERE id_category='".pSQL($parentid)."'"); 
                
            }
            
        }  
        
    
    }
    
    
    return $google_parent;
    
}      
    
    
   
    
public function getCombinationExtraName($id_product_attribute, $id_lang) {
    
    $extraname = '';
    
    $sql=Db::getInstance()->ExecuteS("SELECT atl.name, atg.name as group_name FROM "._DB_PREFIX_."product_attribute_combination pc LEFT JOIN "._DB_PREFIX_."attribute_lang atl ON atl.id_attribute = pc.id_attribute LEFT JOIN "._DB_PREFIX_."attribute a ON a.id_attribute = pc.id_attribute LEFT JOIN "._DB_PREFIX_."attribute_group_lang atg ON atg.id_attribute_group = a.id_attribute_group WHERE pc.id_product_attribute='".pSQL($id_product_attribute)."' AND atl.id_lang='".pSQL($id_lang)."' AND atg.id_lang='".pSQL($id_lang)."'");
            
    if (!$sql) return '';
        
    foreach ($sql as $s)
        $extraname.=$s['group_name'].': '.$s['name'].', ';
        
    
    
    return ', '.Tools::substr($extraname,0,-2);
    
    
} 
    
    
public function getCombinationReference($id_product_attribute, $mainreference) {
    
    
    $combreference=Db::getInstance()->getValue("SELECT reference FROM "._DB_PREFIX_."product_attribute WHERE id_product_attribute='".pSQL($id_product_attribute)."'");
    
    if (!$combreference) return $mainreference.'_'.$id_product_attribute;
    else return $combreference;
    
    
}    
    
 
public function generateproductcode($type, $id_product, $comb=false, $id_lang, $imageformat = null, $context = null) {
    
    $responsecode = '';
    
    if ($type == 'xml') $responsecode=$this->generateCodeXml($id_product, $comb, $id_lang, $imageformat, $context);
    else if ($type == 'csv') $responsecode=$this->generateCodeCSV($id_product, $comb, $id_lang, $imageformat, $context);
   
    return $responsecode;
}   
    
    
    
    
public function generateCodeXML($id_product, $comb=false, $id_lang, $imageformat = null, $context = null) {
    
    $variables = array();
    
    $proddata=Db::getInstance()->getRow("SELECT p.reference, p.condition, p.id_category_default, pl.id_product, pl.link_rewrite, pl.name, pl.description_short, sp.quantity, sp.out_of_stock, im.id_image, m.name AS brandname FROM "._DB_PREFIX_."product_lang pl LEFT JOIN "._DB_PREFIX_."product p ON pl.id_product = p.id_product LEFT JOIN "._DB_PREFIX_."stock_available sp ON pl.id_product=sp.id_product LEFT JOIN "._DB_PREFIX_."manufacturer m ON p.id_manufacturer = m.id_manufacturer LEFT JOIN "._DB_PREFIX_."image im ON pl.id_product = im.id_product WHERE pl.id_lang='".pSQL($id_lang)."' AND sp.id_product_attribute='".($comb ? $comb : 0)."' AND im.cover='1' AND p.id_product='".pSQL($id_product)."'");
    
    if (isset($context->link)) $link = $context->link;
    else $link = new Link();
    
    if ($comb)
    $variables['id'] = (($id_product > 9) ? $id_product : '0'.$id_product).'_'.$comb;    
    else    
    $variables['id'] = (($id_product > 9) ? $id_product : '0'.$id_product);
    
    if ($comb)
    $variables['name'] = $proddata['name'].$this->getCombinationExtraName($comb,$id_lang);
    else    
    $variables['name'] = $proddata['name'];
    
    $variables['name'] = $this->eliminateNonUTFChars($variables['name']);
    
    $variables['description_short'] = $this->eliminateNonUTFChars($proddata['description_short']);
    
    if ($comb)
    $variables['url'] = $link->getProductLink($id_product,null,null,null,null,null,$comb);
    else {
    $defaultcomb = Db::getInstance()->getValue("SELECT id_product_attribute FROM "._DB_PREFIX_."product_attribute WHERE id_product='".pSQL($id_product)."' AND default_on='1'");
    
    if (!$defaultcomb) $defaultcomb = 0;
    
    $variables['url'] = $link->getProductLink($id_product,null,null,null,null,null,$defaultcomb);
    }


    $variables['cover_photo'] = $this->getMainCoverImage($id_product, $imageformat, $comb);
    
   
    if (Tools::strpos($variables['cover_photo'],'://') > 0) $donothing=1;
    else {
    if (Configuration::get('PS_SSL_ENABLED') == 1) $variables['cover_photo'] = 'https://'.$variables['cover_photo'];
        else $variables['cover_photo'] = 'http://'.$variables['cover_photo']; 
    }
    
    $variables['condition'] = $proddata['condition'];
   
    if ($proddata['quantity'] > 0) 
    $variables['stock'] = 'in stock';
    else { 
        if ($proddata['out_of_stock'] == 0) $variables['stock'] = 'out of stock';
        else if ($proddata['out_of_stock'] == 1) $variables['stock'] = 'in stock';
        else if ($proddata['out_of_stock'] == 2) {
            if (Configuration::get('PS_ORDER_OUT_OF_STOCK') == 0) $variables['stock'] = 'out of stock';
            else $variables['stock'] = 'in stock';
        }
        
    }
    
    if ($comb) $variables['stock'] = $proddata['quantity']; 
    
    
    //$product_type = $this->getproductpath($id_product);
    $product_type = $this->getproductpath($id_product, $proddata['id_category_default']);
    //$product_type = trim(Tools::substr($product_type,Tools::strpos($product_type,'>')+1));
    $variables['type'] = $product_type;
    

    $variables['google_product_category'] = $this->getgooglecategory($id_product);
    
    $specific_price_output = null;
    
    if ($comb) {
    $variables['price'] = number_format(Product::getPriceStatic($id_product, true, $comb, 4, NULL, false, false, 1, false, null, null, null, $specific_price_output, true, false, $context, false),2,'.','');
    
    $variables['sale_price'] = number_format(Product::getPriceStatic($id_product, true, $comb, 4, NULL, false, true, 1, false, null, null, null, $specific_price_output, true, false, $context, false),2,'.','');    
    }
    else {    
    $variables['price'] = number_format(Product::getPriceStatic($id_product, true, NULL, 4, NULL, false, false, 1, false, null, null, null, $specific_price_output, true, false, $context, false),2,'.','');
    
    $variables['sale_price'] = number_format(Product::getPriceStatic($id_product, true, NULL, 4, NULL, false, true, 1, false, null, null, null, $specific_price_output, true, false, $context, false),2,'.','');
    }
    
    if (round((int)$variables['price']) == round((int)$variables['sale_price'])) {
        $variables['sale_price'] = '';
    } 
    
    if ($variables['price']) $variables['price'] = $variables['price'].' RON';
    if ($variables['sale_price']) $variables['sale_price'] = $variables['sale_price'].' RON';
     
    
    
    if ($proddata['brandname'])
    $variables['brand'] = $proddata['brandname'];
    else if ($proddata['reference']) $variables['brand'] = $proddata['reference'];
    else $variables['brand'] = Configuration::get('PS_SHOP_NAME');
    
    if ($comb)
    $variables['reference'] = $this->getCombinationReference($comb,$proddata['reference']);    
    else    
    $variables['reference'] = $proddata['reference'];

   $responsecode=json_encode($variables);
   
    return $responsecode;
    
}    
    
    
    
public function generateCodeCSV($id_product, $comb=false, $id_lang, $imageformat = null, $context = null) {
    
    
    $variables = array();
    
    $proddata=Db::getInstance()->getRow("SELECT p.reference, p.ean13, p.active, p.condition, pl.id_product, pl.link_rewrite, pl.name, pl.description_short, pl.description, sp.quantity, sp.out_of_stock, im.id_image, m.name AS brandname FROM "._DB_PREFIX_."product_lang pl LEFT JOIN "._DB_PREFIX_."product p ON pl.id_product = p.id_product LEFT JOIN "._DB_PREFIX_."stock_available sp ON pl.id_product=sp.id_product LEFT JOIN "._DB_PREFIX_."manufacturer m ON p.id_manufacturer = m.id_manufacturer LEFT JOIN "._DB_PREFIX_."image im ON pl.id_product = im.id_product WHERE pl.id_lang='".pSQL($id_lang)."' AND sp.id_product_attribute='".($comb ? $comb : 0)."' AND im.cover='1' AND p.id_product='".pSQL($id_product)."'");
    
    if (isset($context->link)) $link = $context->link;
    else $link = new Link();
    
    if ($comb)
    $variables['id_product'] = (($proddata['id_product'] > 9) ? $proddata['id_product'] : '0'.$proddata['id_product']).'_'.$comb;    
    else    
    $variables['id_product'] = (($proddata['id_product'] > 9) ? $proddata['id_product'] : '0'.$proddata['id_product']);

    
    if ($proddata['quantity'] > 0) 
    $variables['stock'] = 'in stock';
    else { 
        if ($proddata['out_of_stock'] == 0) $variables['stock'] = 'out of stock';
        else if ($proddata['out_of_stock'] == 1) $variables['stock'] = 'in stock';
        else if ($proddata['out_of_stock'] == 2) {
            if (Configuration::get('PS_ORDER_OUT_OF_STOCK') == 0) $variables['stock'] = 'out of stock';
            else $variables['stock'] = 'in stock';
        }
        
    }
    
    $product_type = $this->getproductpath($id_product);
    $variables['type'] = $product_type;
    
    $variables['google_product_category'] = $this->getgooglecategory($id_product);
    
    $variables['condition'] = $proddata['condition'];
    
    if ($comb)
    $variables['name'] = $proddata['name'].$this->getCombinationExtraName($comb,$id_lang);
    else
    $variables['name'] = $proddata['name'];
    
    $variables['name'] = $this->eliminateNonUTFChars($variables['name']);
    
    $variables['description_short'] = strip_tags($proddata['description_short']);
    
    $variables['description_short'] = $this->eliminateNonUTFChars($variables['description_short']);
    
    $variables['description'] = $this->eliminateNonUTFChars($proddata['description']);
    
    
    if ($comb)
    $variables['url'] = $link->getProductLink($id_product,null,null,null,null,null,$comb);  
    else {
    $defaultcomb = Db::getInstance()->getValue("SELECT id_product_attribute FROM "._DB_PREFIX_."product_attribute WHERE id_product='".pSQL($id_product)."' AND default_on='1'");
    
    if (!$defaultcomb) $defaultcomb = 0;
    
    $variables['url'] = $link->getProductLink($id_product,null,null,null,null,null,$defaultcomb);
    }

    $variables['cover_photo'] = $this->getMainCoverImage($id_product, $imageformat, $comb);
    
    $variables['image_2'] = $this->getImageUrl($id_product, $imageformat, 2);
    $variables['image_3'] = $this->getImageUrl($id_product, $imageformat, 3);
    $variables['image_4'] = $this->getImageUrl($id_product, $imageformat, 4);
    $variables['image_5'] = $this->getImageUrl($id_product, $imageformat, 5);
    $variables['image_6'] = $this->getImageUrl($id_product, $imageformat, 6);
    
    //if (Configuration::get('PS_SSL_ENABLED') == 1) $variables['cover_photo'] = 'https://'.$variables['cover_photo'];
      //  else $variables['cover_photo'] = 'http://'.$variables['cover_photo'];
    
    $specific_price_output = null;
   
    if ($comb) {
    $variables['price'] = number_format(Product::getPriceStatic($id_product, true, $comb, 4, NULL, false, false, 1, false, null, null, null, $specific_price_output, true, false, $context, false),2,'.','').' '.$context->currency->iso_code;
    
    $variables['sale_price'] = number_format(Product::getPriceStatic($id_productd, true, $comb, 4, NULL, false, true, 1, false, null, null, null, $specific_price_output, true, false, $context, false),2,'.','').' '.$context->currency->iso_code;    
    }
    else {
    $variables['price'] = number_format(Product::getPriceStatic($id_product, true, NULL, 4, NULL, false, false, 1, false, null, null, null, $specific_price_output, true, false, $context, false),2,'.','').' '.$context->currency->iso_code;
    
    $variables['sale_price'] = number_format(Product::getPriceStatic($id_product, true, NULL, 4, NULL, false, true, 1, false, null, null, null, $specific_price_output, true, false, $context, false),2,'.','').' '.$context->currency->iso_code;
    }
    
    
    if ($proddata['brandname'])
    $variables['brand'] = $proddata['brandname'];
    else if ($proddata['reference']) $variables['brand'] = $proddata['reference'];
    else $variables['brand'] = Configuration::get('PS_SHOP_NAME');
    
    
    if ($comb)
    $variables['reference'] = $this->getCombinationReference($comb,$proddata['reference']);   
    else
    $variables['reference'] = $proddata['reference'];
    
    $variables['active'] = $proddata['active'];

    if (strpos($variables['name'], 'piese') > 0) $variables['continut'] = substr(0, strpos($variables['name'], 'piese') + 5);
    else if (strpos($variables['name'], 'buc.') > 0) $variables['continut'] = substr(0, strpos($variables['name'], 'buc.') + 4);
    else {
        $variables['continut'] = explode(', ', $variables['name']);
        $variables['continut'] = $variables['continut'][0];
    }
    
    if (strpos($variables['continut'], ' - ') > 0) $variables['continut'] = substr(0, strpos($variables['name'], ' - ') - 3);
    
    $variables['type_emag'] = explode('>', $variables['type']); 
    $variables['main_cat_emag'] = $variables['type_emag'][0]; 
    $variables['type_emag'] = $variables['type_emag'][count($variables['type_emag'])-1]; 
    
    $variables['stock_emag'] = $proddata['quantity']; 
    $variables['extra_gama'] = (bool)Db::getInstance()->getValue("SELECT id_product FROM "._DB_PREFIX_."category_product WHERE id_product='".pSQL($id_product)."' AND id_category=63"); 
    
    if ($proddata['ean13'])
        $variables['ean'] = $proddata['ean13'];
    else $variables['ean'] = '';
    
    
    
    
    
    
    $responsecode=json_encode($variables);
   
    return $responsecode;
    
}        
    
  
   
    
public function saveFeed($feedtype, $header, $content, $footer, $separator = '') {

    return true;
    
}
    
        
   
public function getCategoryBreadcrumb($id_category) {
    
   $categoryDefault = new Category($id_category, Context::getContext()->language->id);
   $breadcrumb = array();

    foreach ($categoryDefault->getAllParents() as $category) {
            if ($category->id_parent != 0 && !$category->is_root_category) {
                $breadcrumb[] = $category->name;
            }
        }

        if (!$categoryDefault->is_root_category) {
            $breadcrumb[] = $categoryDefault->name;
        } else {
           $breadcrumb[] = Configuration::get('PS_SHOP_NAME'); 
        }
    
    
    return implode('>',$breadcrumb);

    
}    
    
   public function getProductSales($id_product, $period=30, $output = 'text') {
       
       $date_start = date('Y-m-d', time() - $period*24*3600);
       
       $sales = Db::getInstance()->getValue("SELECT SUM(quantity) FROM "._DB_PREFIX_."product_sale WHERE id_product='".pSQL($id_product)."' AND date_upd>'".pSQL($date_start)."'"); 
       
       if ($output == 'text') {
           if ($sales < 2) return 'LOW';
           else if ($sales > 3) return 'HIGH';
           else return 'MID';
       } else return $sales;
       
   
   }
    
    
    public function getMainCoverImage($id_product, $image_format = 'large_default', $id_product_attribute = 0) {
    
        if ($id_product_attribute > 0) {
            $id_image = Db::getInstance()->getValue("SELECT pai.id_image FROM "._DB_PREFIX_."product_attribute_image pai INNER JOIN "._DB_PREFIX_."image i ON pai.id_image = i.id_image WHERE pai.id_product_attribute='".pSQL($id_product_attribute)."' ORDER BY i.position ASC");
            
            if (!$id_image)
            $id_image = Db::getInstance()->getValue("SELECT id_image FROM "._DB_PREFIX_."image WHERE id_product='".pSQL($id_product)."' AND cover='1'");
            
            if (!$id_image) $id_image = Db::getInstance()->getValue("SELECT id_image FROM "._DB_PREFIX_."image WHERE id_product='".pSQL($id_product)."' ORDER BY id_image ASC");
        }
        else {    
            $id_image = Db::getInstance()->getValue("SELECT id_image FROM "._DB_PREFIX_."image WHERE id_product='".pSQL($id_product)."' AND cover='1'");
            if (!$id_image) $id_image = Db::getInstance()->getValue("SELECT id_image FROM "._DB_PREFIX_."image WHERE id_product='".pSQL($id_product)."' ORDER BY id_image ASC");
        }
        
        
        if (!$id_image) return false;
        
        $link_rewrite = Db::getInstance()->getValue("SELECT link_rewrite FROM "._DB_PREFIX_."product_lang WHERE id_product='".pSQL($id_product)."' AND id_lang='".pSQL(Context::getContext()->language->id)."'");
        
        return Context::getContext()->link->getImageLink($link_rewrite, $id_image, $image_format);
        
    }    
    
    
    public function getImageUrl($id_product, $image_format = 'large_default', $position) { 
    
        $id_image = Db::getInstance()->getValue("SELECT id_image FROM "._DB_PREFIX_."image WHERE id_product='".pSQL($id_product)."' AND position='".pSQL($position)."'");
        
        if (!$id_image) return '';
        
        $link_rewrite = Db::getInstance()->getValue("SELECT link_rewrite FROM "._DB_PREFIX_."product_lang WHERE id_product='".pSQL($id_product)."' AND id_lang='".pSQL(Context::getContext()->language->id)."'");
        
        return Context::getContext()->link->getImageLink($link_rewrite, $id_image, $image_format);
        
    }

    
}