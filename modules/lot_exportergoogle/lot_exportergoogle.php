<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

require(_PS_MODULE_DIR_.'lot_exportergoogle/LotGoogleCats.php');

class Lot_ExporterGoogle extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'lot_exportergoogle';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = 'LOT.ai';
        $this->module_key = 'eac9c8ca0333b5a40686f55f96843d37';
        $this->need_instance = 0;
        $this->bootstrap = true;
        
        $this->matchclass = new LotGoogleCats();

        parent::__construct();
        
        $this->secure_key = Tools::encrypt($this->name);

        $this->displayName = $this->l('Exporter & Feed Management for Google Merchant Center');
        $this->description = $this->l('This module allows you to export your products with for Google Merchant Center (xml,csv).');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
    
    include(dirname(__FILE__).'/sql/install.php');
        
        
       if (!parent::install() || !$this->registerHook('actionAdminControllerSetMedia')) 
			return false; 
        
        
        Configuration::updateValue('LOT_EXPORTERGOOGLE_MAIN_GCID', '');
		
	    
		$tab = new Tab();

        foreach (Language::getLanguages() as $language) {

         $tab->name[$language['id_lang']] = 'Google Feed Management';

        }

	   $tab->id_parent = Tab::getIdFromClassName('AdminCatalog');

	   $tab->class_name = 'AdminLotExporterGoogle'; 

       $tab->module = 'lot_exportergoogle'; 

       $tab->position=Tab::getNewLastPosition($tab->id_parent);

       $r = $tab->save(); 
        
       Configuration::updateValue('LOT_EXPORTER_TAB_ID', $tab->id); 
        
        
      $tab = new Tab();

        foreach (Language::getLanguages() as $language) {

         $tab->name[$language['id_lang']] = 'Category Match for Google Merchant Center';

        }

	   $tab->id_parent = Tab::getIdFromClassName('AdminCatalog');

	   $tab->class_name = 'AdminLotGoogleMatch'; 

       $tab->module = 'lot_exportergoogle'; 

       $tab->position=Tab::getNewLastPosition($tab->id_parent);

       $r = $tab->save(); 
        
       Configuration::updateValue('LOT_EXPORTER_TAB_ID_MATCH', $tab->id);    
        
        
		return true;
    }

    
    
    public function uninstall()
    {
        
        include(dirname(__FILE__).'/sql/uninstall.php');
        
        $tab = new Tab(Configuration::get('LOT_EXPORTER_TAB_ID'));

         $tab->delete();
        
        
        $tab = new Tab(Configuration::get('LOT_EXPORTER_TAB_ID_MATCH'));

        $tab->delete();
         
        return parent::uninstall();
    }

  
    public function getContent()
    {
        
        
        $redirectionurl='index.php?controller=AdminLotExporterGoogle&token='.Tools::getAdminTokenLite('AdminLotExporterGoogle');
    
        Tools::redirectAdmin($redirectionurl);
        
    }
    
    
    public function hookactionAdminControllerSetMedia($params){
        
        if (Tools::getValue('controller') == 'AdminLotExporterGoogle') {
            
        Media::addJsDef(array('modulepath' => $this->_path));    
        Media::addJsDef(array('itemslang' => $this->l('items')));    
        Media::addJsDef(array('name_error' => $this->l('Name field is empty!')));    
        Media::addJsDef(array('feedcontent_error' => $this->l('Feed structure is empty!')));
            
        
        //set controllers links
        Media::addJsDef( array('ajax_link' => $this->context->link->getModuleLink($this->name, 'ajax', array('key' => $this->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->secure_key)))) );    
        Media::addJsDef( array('ajax2_link' => $this->context->link->getModuleLink($this->name, 'ajax2', array('key' => $this->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->secure_key)))) );    
        Media::addJsDef( array('cron_link' => $this->context->link->getModuleLink($this->name, 'cron', array('key' => $this->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->secure_key)))) );    
        Media::addJsDef( array('download_link' => $this->context->link->getModuleLink($this->name, 'download', array('key' => $this->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->secure_key)))) );    
        Media::addJsDef( array('feed_link' => $this->context->link->getModuleLink($this->name, 'feed', array('key' => $this->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->secure_key)))) );    
        Media::addJsDef( array('getdirectlink_link' => $this->context->link->getModuleLink($this->name, 'getdirectlink', array('key' => $this->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->secure_key)))) );    
            
            
  
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
        
        $this->context->controller->addCSS($this->_path.'views/css/prestaro_be.css');
        
        $this->context->controller->addCSS($this->_path.'views/css/custombootstrap.css');
        $this->context->controller->addCSS($this->_path.'views/css/custombootstrap_select.css');
        $this->context->controller->addCSS($this->_path.'views/css/bootstrap-multiselect.css');

        //$this->context->controller->addJS($this->_path.'views/js/custombootstrap.js');
        $this->context->controller->addJS($this->_path.'views/js/custombootstrap_select.js');
            
        $this->context->controller->addJS($this->_path.'views/js/back.js');
        $this->context->controller->addJS($this->_path.'views/js/prestaro_be.js');
            
     
        }
        
        else if (Tools::getValue('controller') == 'AdminLotGoogleMatch') {
            
            Media::addJsDef(array('modulepath' => $this->_path));    
            
            Media::addJsDef( array('ajax_link_match' => $this->context->link->getModuleLink($this->name, 'matchgooglecats', array('key' => $this->secure_key, 'token' => md5(_COOKIE_KEY_ . $this->secure_key)))) );  
            
            $this->context->controller->addCSS($this->_path.'views/css/categorymatch.css');
            $this->context->controller->addJS($this->_path.'views/js/categorymatch.js');
            
            Media::addJsDef(array('view_button' => $this->l('View subcategories')));   
            Media::addJsDef(array('not_g_cats_found' => $this->l('No matching google categories found!')));   
            Media::addJsDef(array('error_google_matching' => $this->l('Error: The categories could not be mapped!')));   
            
        }
    
   
    }
    
    
    
    
}
