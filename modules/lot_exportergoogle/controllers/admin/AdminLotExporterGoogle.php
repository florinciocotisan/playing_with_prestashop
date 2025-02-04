<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

//require(_PS_MODULE_DIR_.'lot_exportergoogle/LotFeed.php');

class AdminLotExporterGoogleController extends ModuleAdminController
{
	
	public function __construct()
	{
		
		$this->module = 'lot_exportergoogle';
        $this->lang = true;
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->html = '';

        $this->token = Tools::getAdminToken(
            $this->module.(int)Tab::getIdFromClassName($this->module).(int)Context::getContext()->cookie->id_employee
        );

        
		parent::__construct();
        
	}

	protected function loadObject($opt = false)
	{
		if ($id = Tools::getValue($this->identifier))
			return new $this->className($id);
		return new $this->className();
	}

	
    
    
    
    public function renderList(){ 

        $this->html='';
        
        $shopbaseurl=_PS_BASE_URL_.__PS_BASE_URI__;
        $shopbaseurl=str_replace('https://','//',$shopbaseurl);
        $shopbaseurl=str_replace('http://','//',$shopbaseurl);
        
        $farahttp=str_replace('http://','',_PS_BASE_URL_);
        $farahttp=str_replace('https://','',$farahttp);
        
        
        if ($_SERVER['HTTP_HOST'] != $farahttp) {
        if (array_key_exists('HTTPS', $_SERVER) and ($_SERVER['HTTPS'] == "on"))    
        Tools::redirect('https:'.$shopbaseurl.Tools::substr($_SERVER['PHP_SELF'],1).'?controller=AdminLotExporterGoogle&token='.Tools::getAdminTokenLite('AdminLotExporterGoogle'));
        else  Tools::redirect('http:'.$shopbaseurl.Tools::substr($_SERVER['PHP_SELF'],1).'?controller=AdminLotExporterGoogle&token='.Tools::getAdminTokenLite('AdminLotExporterGoogle'));   
        }
        
       
        
        $this->context->smarty->assign('module_dir', $shopbaseurl.'modules/lot_exportergoogle/');
        $this->context->smarty->assign('secure_key', $this->module->secure_key);
         $this->context->smarty->assign('pagetoken', Tools::getValue('token'));
        
        $this->context->smarty->assign('languages', Language::getLanguages());
        
        $this->context->smarty->assign('googlmatch_controller_link', $this->context->link->getAdminLink('AdminLotGoogleMatch', false).'&token='.Tools::getAdminTokenLite('AdminLotGoogleMatch'));
    
        
        //set controllers links
        $this->context->smarty->assign('cron_link', $this->context->link->getModuleLink($this->module->name, 'cron', array('key' => $this->module->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->module->secure_key))));
        $this->context->smarty->assign('download_link', $this->context->link->getModuleLink($this->module->name, 'download', array('key' => $this->module->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->module->secure_key))));
        $this->context->smarty->assign('feed_link', $this->context->link->getModuleLink($this->module->name, 'feed', array('key' => $this->module->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->module->secure_key))));
        $this->context->smarty->assign('getdirectlink_link', $this->context->link->getModuleLink($this->module->name, 'getdirectlink', array('key' => $this->module->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->module->secure_key))));
       
    
        
        $catob=new Category(1,$this->context->language->id,true);
        $allcategories=$catob->recurseLiteCategTree(10, 0, $this->context->language->id);
        
        $allmanufacturers=Db::getInstance()->ExecuteS("SELECT id_manufacturer, name FROM "._DB_PREFIX_."manufacturer");
        
        $allfeatures=Feature::getFeatures($this->context->language->id);
        $features=array();
        foreach ($allfeatures as $f) {
            $featurevalues=FeatureValue::getFeatureValuesWithLang($this->context->language->id,$f['id_feature'],false);
            $row['feature']=$f['name'];
            $row['values']=array();
            foreach ($featurevalues as $fv)
            $row['values'][]=array('val'=>$f['id_feature'].'-'.$fv['id_feature_value'],'display'=>$fv['value']); 
            
         $features[]=$row;   
        }
        
        $allattributes=AttributeGroup::getAttributesGroups($this->context->language->id);
        $atributes=array();
        
        foreach ($allattributes as $a) {
            $values=AttributeGroup::getAttributes($this->context->language->id, $a['id_attribute_group']);
            $row['name']=$a['name'];
            $row['values']=array();
            foreach ($values as $v) {
                $row['values'][]=array('val'=>$a['id_attribute_group'].'-'.$v['id_attribute'],'display'=>$v['name']);  
            }
               
            $atributes[]=$row;
            //break;
        }
            
            
            
        //var_dump($allfeatures);
      
        $this->context->smarty->assign('allmanufacturers', $allmanufacturers);
        $this->context->smarty->assign('allcategories', $allcategories['children']);
        $this->context->smarty->assign('allfeatures', $features);
        $this->context->smarty->assign('allattributes', $atributes);
        
        
        //get existing feeds
        
        $existingfeeds=Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_feeds");
        
        $restrictions=array();
        if ($existingfeeds) {
        foreach ($existingfeeds as &$f) {
            $f['header']=Tools::htmlentitiesUTF8($f['header']);
            $f['footer']=Tools::htmlentitiesUTF8($f['footer']);
            $f['editcode']=str_replace('b??e','',Tools::htmlentitiesUTF8($f['editcode']));
            $f['code']=str_replace('b??e','',$f['code']);
        }
        }
        
        
        $restrictions=array();
        if (Tools::getValue('editfeed')) {
            $sqlrestrictions=Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_restrictions WHERE id_feed='".pSQL(Tools::getValue('editfeed'))."'");
        $this->context->smarty->assign('feedid', Tools::getValue('editfeed'));
        
        $feedsettings=Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_feeds WHERE id_feed='".pSQL(Tools::getValue('editfeed'))."'");
            
        $this->context->smarty->assign('feedtype', $feedsettings['type']);    
        $this->context->smarty->assign('feedsettings', $feedsettings);    
            
        $restrictions=$sqlrestrictions; 
            
        $restrictions['filter_category']=explode(',',$restrictions['filter_category']);
        $restrictions['filter_manufacturer']=explode(',',$restrictions['filter_manufacturer']);
        $restrictions['filter_feature']=explode(',',$restrictions['filter_feature']);
        $restrictions['filter_attribute']=explode(',',$restrictions['filter_attribute']);
            
        }
    
        
      
        $this->context->smarty->assign('restrictions', $restrictions);
        $this->context->smarty->assign('existingfeeds', $existingfeeds);
         
        $this->context->smarty->assign('current_currency', $this->context->currency->iso_code);
        $this->context->smarty->assign('image_formats', ImageType::getImagesTypes('products'));
        
        //end get existing feeds
        

        $this->html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.'lot_exportergoogle/views/templates/admin/configure.tpl');
        
        return $this->html;
        
        
    
    }
    
    
    
	
    


    
    
    
    
    
   
    
    
    
}

