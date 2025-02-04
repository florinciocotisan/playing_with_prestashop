<?php
/**
* 2020 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2020 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0) 
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class ECD_LoginAsCustomer extends Module {
    
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'ecd_loginascustomer';
        $this->tab = 'front_office_features';
        $this->module_key= '673e28ce43d027e09d6ed745ec5138xx';
        $this->version = '1.0.0';
        $this->author = 'EcomdooSoftware.eu';
        $this->need_instance = 1;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Login as customer');
        $this->description = $this->l('Allow shop admin to login to customer account with an universal code');
        
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }


    public function install()
    {
    
        Configuration::updateValue('ECD_ACCESS_CODE','');
        Configuration::updateValue('ECD_ACCESS_CODE_LENGTH',0);
        
        $this->fixPrestashopHookBug();

        return parent::install() &&
            $this->registerHook('actionAuthenticationBefore') && $this->registerHook('actionAdminControllerSetMedia');
    }

    public function uninstall()
    {
       
        
        Configuration::deleteByName('ECD_ACCESS_CODE');
        Configuration::deleteByName('ECD_ACCESS_CODE_LENGTH');
        
        include(dirname(__FILE__).'/sql/uninstall.php');
        
        
        
        return parent::uninstall();
    }

    
    
    public function fixPrestashopHookBug() {
        
        $search = Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."hook WHERE name='actionAuthenticationBefore'");
        
        if (!$search) {
            $searchalias = Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."hook WHERE name='actionbeforeauthentication'");
            
            if ($searchalias) {
                
                Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."hook SET name='actionAuthenticationBefore', title='Before customer authentication' WHERE id_hook='".pSQL($searchalias['id_hook'])."'");
                
            } else {
                
                Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."hook (name, title, position) VALUES ('actionAuthenticationBefore', 'Before customer authentication', '1')");
            }
            
        }
             
        
    }
    
    
    public function getContent()
    {
    
        $this->_html='';
       
        if (((bool)Tools::isSubmit('submitLot_Module')) == true) {
         if ($this->postValidation())    
            $this->postProcess();
        }
        
        
        $this->context->smarty->assign('module_dir', $this->_path);
        

        $this->_html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        
        $this->_html .= $this->renderForm();
        
        return $this->_html;
   
    }
   
    
    
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLot_Module';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        
       
        
         
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), 
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        
        

        return $helper->generateForm(array($this->getConfigForm()));
        
    }
    
    
   
    protected function getConfigForm()
        
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                   
                    array(
                        'type' => 'password',
                        'label' => $this->l('Universal Code'),
                        'name' => 'ECD_ACCESS_CODE',
                    ),
                    
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }
    
    
     protected function getConfigFormValues() {
         
        $access_code_fake = '';
        for ($i=0;$i<(int)Configuration::get('ECD_ACCESS_CODE_LENGTH');$i++)  
            $access_code_fake .= 'x'; 
         
        return array(
            'ECD_ACCESS_CODE' => $access_code_fake,
        );
        
        
        
    }
    
    
    protected function postValidation() {
        
        
        if (Tools::getValue('ECD_ACCESS_CODE') == '') {
            $this->_html.=$this->displayError($this->l('Access code is empty!'));
            return false;
        }
        
        return true;
        
    }
    
    
    protected function postProcess() {
        
         if (Tools::getValue('ECD_ACCESS_CODE')) {
                
             $access_code = Tools::encrypt(Tools::getValue('ECD_ACCESS_CODE'));
             
             Configuration::updateValue('ECD_ACCESS_CODE', $access_code);
             Configuration::updateValue('ECD_ACCESS_CODE_LENGTH', strlen(Tools::getValue('ECD_ACCESS_CODE')));
        }
        
        $this->_html.=$this->displayConfirmation($this->l('Settings updated!'));   
        
    }
    
    
     
    public function hookactionAuthenticationBefore()
    {
      
        if (Tools::encrypt(Tools::getValue('password')) == Configuration::get('ECD_ACCESS_CODE')) {
            
            $cob = new Customer();
            if ($logged_customer = $cob->getByEmail(Tools::getValue('email'))) {
                
                $this->loginAdminToCustomer($logged_customer->id);
                return true;
            }
            return false;
        }
        
        return false;
        
    }

    
    public function hookactionAdminControllerSetMedia($params){
        
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
        
      
        
	}
    
    
    public function loginAdminToCustomer($id_customer) {
    
			$customer = new Customer($id_customer);
			$authentication = $customer;

			if (isset($authentication->active) && !$authentication->active) {
				$this->errors[''][] = $this->translator->trans('Your account isn\'t available at this time, please contact us', [], 'Shop.Notifications.Error');
			} elseif (!$authentication || !$customer->id || $customer->is_guest) {
				$this->errors[''][] = $this->translator->trans('Authentication failed.', [], 'Shop.Notifications.Error');
			} else {
				$this->context->updateCustomer($customer);

				Hook::exec('actionAuthentication', ['customer' => $this->context->customer]);
                
				CartRule::autoRemoveFromCart($this->context);
				CartRule::autoAddToCart($this->context);
			}
			$back = "my-account";
			Tools::redirect('index.php?controller='.urlencode($back));
        
    }

    
    
    
    
}
