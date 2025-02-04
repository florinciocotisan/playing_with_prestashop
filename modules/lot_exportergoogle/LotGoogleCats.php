<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/
    
class LotGoogleCats extends Module {

    
    public function downloadGoogleCategories($input = null) {

            $pool = array();

            $pool = file('http://www.google.com/basepages/producttype/taxonomy-with-ids.en-US.txt', FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);

            $version = array_shift($pool);

            foreach ($pool as $row) {
                
                list($id, $cat) = explode(' - ', $row);
                        
                $parentparts = explode(' > ', $cat);
                $parentvalue = '';
                if (isset($parentparts[1])) {
                    array_pop($parentparts);
                    $parentvalue = implode(' > ',$parentparts);
                }
                else $parentvalue = $cat;
                
                //add to database
                
                $hasparent = Db::getInstance()->getRow("SELECT google_id FROM "._DB_PREFIX_."lot_exportergoogle_google_categories WHERE google_value='".pSQL($parentvalue)."'");
                
                if ($hasparent) $id_parent = $hasparent['google_id'];
                else $id_parent = 0;
                
                $action=Db::getInstance()->insert('lot_exportergoogle_google_categories', array(
                    'google_id' => pSQL($id),
                    'google_value' => pSQL($cat),
                    'google_parent' => pSQL($id_parent)
                    ));
                
                
            }

            // done with $pool
            unset($pool);
        
        return true;
        
    }
    
    
    
    public function getCatList($input) {
        
        $google_parent = '';
         
        if (Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID')) {
                if (Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID') != '0') {  
                 $google_parent = Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID');   
                }
        }
        
        $parentid = Db::getInstance()->getValue("SELECT id_parent FROM "._DB_PREFIX_."category WHERE id_category='".pSQL($input['id_category'])."'"); 
        
           
        while ($parentid > 2) {
            
            $getmatch = Db::getInstance()->getRow("SELECT google_id FROM "._DB_PREFIX_."lot_exportergoogle_category_map WHERE id_category='".pSQL($parentid)."'");
            
            if ($getmatch) {
               $google_parent = $getmatch['google_id'];
               break;    
            } else {
                
               $parentid =  Db::getInstance()->getValue("SELECT id_parent FROM "._DB_PREFIX_."category WHERE id_category='".pSQL($parentid)."'"); 
                
            }
            
        }
        
        
        $hassubs = Db::getInstance()->getRow("SELECT google_id FROM "._DB_PREFIX_."lot_exportergoogle_google_categories WHERE google_id>'0' AND google_parent='".pSQL($google_parent)."'"); 
        if (!$hassubs) {
            
            $cats = Db::getInstance()->ExecuteS("SELECT google_id AS value, google_value AS text FROM "._DB_PREFIX_."lot_exportergoogle_google_categories WHERE google_id='".pSQL($google_parent)."'");
            
            return $this->convertToDropList($cats);
            
        }
        
        
        if ($google_parent != '') {
            $google_parent = Db::getInstance()->getValue("SELECT google_value FROM "._DB_PREFIX_."lot_exportergoogle_google_categories WHERE google_id='".pSQL($google_parent)."'"); 
        }
        
    
        if ($input['inputval'] != '')
        $cats = Db::getInstance()->ExecuteS("SELECT google_id AS value, google_value AS text FROM "._DB_PREFIX_."lot_exportergoogle_google_categories WHERE google_value LIKE '%".pSQL($input['inputval'])."%'".(($google_parent != '') ? " AND google_value LIKE '".pSQL($google_parent)." >%'" : ''));
        else
        $cats = Db::getInstance()->ExecuteS("SELECT google_id AS value, google_value AS text FROM "._DB_PREFIX_."lot_exportergoogle_google_categories WHERE google_id>'0'".(($google_parent != '') ? " AND google_value LIKE '".pSQL($google_parent)." >%'" : ''));
        
        return $this->convertToDropList($cats);
        
        
    
    }
    
    
    public function getMainCatList($input) {
        
        $google_parent = 0;
    
        if ($input['inputval'] != '')
        $cats = Db::getInstance()->ExecuteS("SELECT google_id AS value, google_value AS text FROM "._DB_PREFIX_."lot_exportergoogle_google_categories WHERE google_value LIKE '%".pSQL($input['inputval'])."%' AND google_parent='0'");
        else
        $cats = Db::getInstance()->ExecuteS("SELECT google_id AS value, google_value AS text FROM "._DB_PREFIX_."lot_exportergoogle_google_categories WHERE google_id>'0' AND google_parent='0'");
        
        return $this->convertToDropList($cats);
        
        
    
    }
    
    
    
    public function convertToDropList($items) {
        
        $output = '';
        foreach ($items as $item) {
            $output .= '<li value="'.pSQL($item['value']).'">'.pSQL($item['text']).'</li>';
        }
        
        return $output;
        
    }
    
    
    
    public function applyMatch($input) {
        
        $search = Db::getInstance()->getRow("SELECT google_id FROM "._DB_PREFIX_."lot_exportergoogle_category_map WHERE id_category='".pSQL($input['id_category'])."'");  
        
        if ((int)$input['id_category'] <= 0) return $this->l('Shop Category ID is invalid!').'error';
        
        if ($search) {
            
            if ((int)$input['google_id'] == 0)
                $action=Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."lot_exportergoogle_category_map WHERE id_category='".pSQL($input['id_category'])."'"); 
            else
                $action=Db::getInstance()->update('lot_exportergoogle_category_map', array(
                    'google_id' => pSQL($input['google_id'])          
                    ), "id_category='".pSQL($input['id_category'])."'");
            
        } else {
            
            if ((int)$input['google_id'] == 0) return $this->l('Google Category is invalid!').'error';
            else    
            $action=Db::getInstance()->insert('lot_exportergoogle_category_map', array(
                    'id_category' => pSQL($input['id_category']),
                    'google_id' => pSQL($input['google_id']),
                    ));
            
        } 
        
        return $this->l('Success: The category was mapped!');
            
        
    }
    
   
    
    
}