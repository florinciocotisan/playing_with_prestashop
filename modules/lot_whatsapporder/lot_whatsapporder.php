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

class Lot_WhatsappOrder extends Module {
    
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'lot_whatsapporder';
        $this->tab = 'front_office_features';
        $this->module_key= '5be2fabe1bcf17adca5493a8ace756f9';
        $this->version = '1.0.0';
        $this->author = 'Lot.ai';
        $this->need_instance = 1;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('WhatsApp Order Button');
        $this->description = $this->l('This module allows client to order through WhatsApp');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

   
    public function install()
    {
    
        Configuration::updateValue('LOT_WHATSAPP_PRODUCT_ACTIVE',true);
        Configuration::updateValue('LOT_WHATSAPP_CART_ACTIVE',true);
        Configuration::updateValue('LOT_WHATSAPP_CHECKOUT_ACTIVE',true);
        Configuration::updateValue('LOT_WHATSAPP_NUMBERO','');
        Configuration::updateValue('LOT_WHATSAPP_STICKY',false);
    
        
        include(dirname(__FILE__).'/sql/install.php');
        

        if (Tools::substr(_PS_VERSION_,0,3) == '1.6') 
            
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayShoppingCart') &&
            $this->registerHook('displayProductButtons');    
            
        else
            
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayProductAdditionalInfo') &&
            $this->registerHook('displayReassurance');
        
    }

    public function uninstall()
    {
        
        Configuration::deleteByName('LOT_WHATSAPP_PRODUCT_ACTIVE');
        Configuration::deleteByName('LOT_WHATSAPP_CART_ACTIVE');
        Configuration::deleteByName('LOT_WHATSAPP_CHECKOUT_ACTIVE');
        Configuration::deleteByName('LOT_WHATSAPP_NUMBERO');
        Configuration::deleteByName('LOT_WHATSAPP_STICKY');
        
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
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
                        'type' => 'switch',
                        'label' => $this->l('Active on Product Page'),
                        'name' => 'LOT_WHATSAPP_PRODUCT_ACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active on Cart Page'),
                        'name' => 'LOT_WHATSAPP_CART_ACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Active on Checkout Page'),
                        'name' => 'LOT_WHATSAPP_CHECKOUT_ACTIVE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    
                    
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Sticky Fixed Button'),
                        'name' => 'LOT_WHATSAPP_STICKY',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    
                    
                    array(
                        'type' => 'text',
                        'label' => $this->l('WhatsApp Phone Number (IMPORTANT: must include country prefix)'),
                        'name' => 'LOT_WHATSAPP_NUMBERO',
                        'id' => 'lotcallnumber',
                        'desc' => $this->l('')
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'button_text',
                        'label' => $this->l('Button text'),
                        'lang' => true
                        ),
                    array(
                        'type' => 'textarea',
                        'desc' => $this->l('This message will appear on your WhatsApp account when a customer orders a product from product page.'),
                        'name' => 'lot_whatsapp_text',
                        'label' => $this->l('Predefined message for product (followed by product reference/id)'),
                        'lang' => true,
                        'cols' => 100,
                        'rows' => 5
                        ),
                    array(
                        'type' => 'textarea',
                        'desc' => $this->l('This message will appear on your WhatsApp account when a customer orders product(s) from cart/checkout.'),
                        'name' => 'lot_whatsapp_text_cart',
                        'label' => $this->l('Predefined message for cart (followed by products list)'),
                        'lang' => true,
                        'cols' => 100,
                        'rows' => 5
                        ),
                    
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    
    protected function getConfigFormValues()
    {
        
        $mesaje=array();
        $mesage_cart = array();
        $button_text = array();
        
        foreach ($this->context->controller->getLanguages() as $lang) {
            
            $mesajdata=Db::getInstance()->getRow("SELECT button_text, message, message_cart FROM "._DB_PREFIX_."lot_whatsup_lang WHERE id_lang='".pSQL($lang['id_lang'])."' AND id_shop='".pSQL($this->context->shop->id)."'");
            
            if ($mesajdata) {
            $mesaje[$lang['id_lang']]=$mesajdata['message']; 
            $mesage_cart[$lang['id_lang']]=$mesajdata['message_cart']; 
            $button_text[$lang['id_lang']] =$mesajdata['button_text'];     
            }
            else {
             $mesaje[$lang['id_lang']]=$this->l('I want to buy this product:');  
             $mesage_cart[$lang['id_lang']]=$this->l('I want to buy these products:'); 
             $button_text[$lang['id_lang']] =$this->l('Order with WhatsApp');    
            }
            
        }
        

        return array(
            'LOT_WHATSAPP_PRODUCT_ACTIVE' => Configuration::get('LOT_WHATSAPP_PRODUCT_ACTIVE'),
            'LOT_WHATSAPP_CART_ACTIVE' => Configuration::get('LOT_WHATSAPP_CART_ACTIVE'),
            'LOT_WHATSAPP_CHECKOUT_ACTIVE' => Configuration::get('LOT_WHATSAPP_CHECKOUT_ACTIVE'),
            'LOT_WHATSAPP_STICKY' => Configuration::get('LOT_WHATSAPP_STICKY'),
            'LOT_WHATSAPP_NUMBERO' => Configuration::get('LOT_WHATSAPP_NUMBERO'),
            'lot_whatsapp_text' => $mesaje,
            'lot_whatsapp_text_cart' => $mesage_cart,
            'button_text' => $button_text
        );
        
        
        
    }
    

    protected function postValidation() {
        
            if (Tools::getValue('LOT_WHATSAPP_NUMBERO') == '') {

                    $this->_html.=$this->displayError($this->l('WhatsApp Phone Number is empty!'));
                    return false;

            } else if ( !Validate::isPhoneNumber(Tools::getValue('LOT_WHATSAPP_NUMBERO')) ) {

                    $this->_html.=$this->displayError($this->l('WhatsApp Phone Number is not valid!'));
                    return false;
            }

            else if (Tools::getValue('button_text_'.$this->context->language->id) == '') {
                    $this->_html.=$this->displayError($this->l('Button text field is empty for language ').$this->context->language->iso_code);
                    return false; 
            }
        
            else if (Tools::getValue('lot_whatsapp_text_'.$this->context->language->id) == '') {
                    $this->_html.=$this->displayError($this->l('Predefined message for product field is empty for language ').$this->context->language->iso_code);
                    return false; 
            }
        
            else if (Tools::getValue('lot_whatsapp_text_cart_'.$this->context->language->id) == '') {
                    $this->_html.=$this->displayError($this->l('Predefined message for cart field is empty for language ').$this->context->language->iso_code);
                    return false; 
            }
                
                   
                
        
        
    return true;
        
    }

    
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
    
        foreach (array_keys($form_values) as $key) {  
            Configuration::updateValue($key, Tools::getValue($key));
        }
        
        
        foreach ($this->context->controller->getLanguages() as $lang) {
            
            $search=Db::getInstance()->getRow("SELECT id_lang FROM "._DB_PREFIX_."lot_whatsup_lang WHERE id_lang='".pSQL($lang['id_lang'])."' AND id_shop='".pSQL($this->context->shop->id)."'");
            
            
            if ($search) {
            $action=Db::getInstance()->update('lot_whatsup_lang', array(
            'message' => pSQL(Tools::getValue('lot_whatsapp_text_'.$lang['id_lang']),true),
            'message_cart' => pSQL(Tools::getValue('lot_whatsapp_text_cart_'.$lang['id_lang']),true),
            'button_text' => pSQL(Tools::getValue('button_text_'.$lang['id_lang']))
            ), "id_lang='".pSQL($lang['id_lang'])."' AND id_shop='".pSQL($this->context->shop->id)."'");
            }
            
            else {
        
            $action=Db::getInstance()->insert('lot_whatsup_lang', array(
            'id_lang' => pSQL($lang['id_lang']),
            'id_shop' => pSQL($this->context->shop->id),
            'message' => pSQL(Tools::getValue('lot_whatsapp_text_'.$lang['id_lang']),true),
            'message_cart' => pSQL(Tools::getValue('lot_whatsapp_text_cart_'.$lang['id_lang']),true),
            'button_text' => pSQL(Tools::getValue('button_text_'.$lang['id_lang']))
            ));
            
            
            }
            
            
            if (!$action) {
                    $this->_html.=$this->displayError($this->l('Database update error!'));
                    return false;
                    }
            
        }
        
    
       
       $this->_html.=$this->displayConfirmation($this->l('Settings updated!')); 
        
    }

   
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    
    public function hookHeader()
    {
        
        if (Configuration::get('LOT_WHATSAPP_STICKY') == true)
            Media::addJsDef(array('lot_whatsapp_sticky' => 1));
        else Media::addJsDef(array('lot_whatsapp_sticky' => 0));
        
        if (Tools::substr(_PS_VERSION_,0,3) == '1.6') Media::addJsDef(array('lot_whatsapp_16' => 1));
        else Media::addJsDef(array('lot_whatsapp_16' => 0));
        
        if (Tools::getValue('controller') == 'product')
            Media::addJsDef(array('whatsup_text' => $this->getWhatsUpMessage($this->context->language->id, $this->context->shop->id)));
        else Media::addJsDef(array('whatsup_text' => $this->getWhatsUpMessageForCart($this->context->cart->id, $this->context->language->id, $this->context->shop->id)));
        
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
   
        
        
    }
    
    
    public function getWhatsUpMessage($id_lang, $id_shop) {
        
        
        $message=Db::getInstance()->getValue("SELECT message FROM "._DB_PREFIX_."lot_whatsup_lang WHERE id_lang='".pSQL($id_lang)."' AND id_shop='".pSQL($id_shop)."'");
        
        if (!$message)
            $message=Db::getInstance()->getValue("SELECT message FROM "._DB_PREFIX_."lot_whatsup_lang WHERE id_lang='1' AND id_shop='1'");    
            
        if (!$message) return '';
        else return $message;
       
        
    }
    
    
    public function getWhatsUpMessageForCart($id_cart, $id_lang, $id_shop) {
        
        $cart = new Cart($id_cart);
        
        $message = '';
        
        
        $message=Db::getInstance()->getValue("SELECT message_cart FROM "._DB_PREFIX_."lot_whatsup_lang WHERE id_lang='".pSQL($id_lang)."' AND id_shop='".pSQL($id_shop)."'");
        
        if (!$message)
            $message=Db::getInstance()->getValue("SELECT message_cart FROM "._DB_PREFIX_."lot_whatsup_lang WHERE id_lang='1' AND id_shop='1'");    
        
        foreach ($cart->getProducts() as $p) {
            
            $message.= '%0a'.'%0a'.' - '.($p['reference'] ? $this->l('ref:').$p['reference'] : $this->l('id:').$p['id_product']).' '.$p['name'].' '.$p['attributes'].' - '.$this->l('Qty:').$p['cart_quantity'];
            
        }
        
        return $message;
        
    }
    
    
    public function getButtonText($id_lang, $id_shop) {
        
        $button_text=Db::getInstance()->getValue("SELECT button_text FROM "._DB_PREFIX_."lot_whatsup_lang WHERE id_lang='".pSQL($id_lang)."' AND id_shop='".pSQL($id_shop)."'");
        
        if (!$button_text)
            $button_text=Db::getInstance()->getValue("SELECT button_text FROM "._DB_PREFIX_."lot_whatsup_lang WHERE id_lang='1' AND id_shop='1'"); 
        
        if (!$button_text) return false;
        else return $button_text;
        
    }

    
    
    public function hookdisplayProductAdditionalInfo()
    {
      
        if (Tools::getValue('id_product') && (Tools::getValue('controller') == 'product')) {
            
            if (Configuration::get('LOT_WHATSAPP_PRODUCT_ACTIVE') == true)
            
            if (Configuration::get('LOT_WHATSAPP_NUMBERO') != '') {        
            
                $prod=new Product(Tools::getValue('id_product'),$this->context->language->id, true);
            
                $this->context->smarty->assign('whatsupprod', $prod);
                
                if ($prod->reference) $codeproduct = $this->l('ref:').$prod->reference;
                else $codeproduct = $this->l('id:').$prod->id;
                
                $itemname = $prod->name;
                    
                $this->context->smarty->assign('codeproduct', $codeproduct);
                $this->context->smarty->assign('itemname', $itemname);
                    
    
                $this->context->smarty->assign('LOT_WHATSAPP_NUMBER', Configuration::get('LOT_WHATSAPP_NUMBERO')); 
    
                $this->context->smarty->assign('LOT_WHATSAPP_BUTTON_TEXT', $this->getButtonText($this->context->language->id, $this->context->shop->id));
                
                $this->context->smarty->assign('LOT_WHATSAPP_TEXT', $this->getWhatsUpMessage($this->context->language->id, $this->context->shop->id)); 
        
    
                return $this->display(__FILE__, 'whatsup_product.tpl');
            }
    
        }
        
        
    
    }
    
    
    public function hookdisplayProductButtons()
    {
      
    
        if (Tools::getValue('id_product') && (Tools::getValue('controller') == 'product')) {
            
            if (Configuration::get('LOT_WHATSAPP_PRODUCT_ACTIVE') == true)
        
            if (Configuration::get('LOT_WHATSAPP_NUMBERO') != '') {    
            
                $prod=new Product(Tools::getValue('id_product'),$this->context->language->id, true);
                $this->context->smarty->assign('whatsupprod', $prod); 
            
                $this->context->smarty->assign('whatsupprod', $prod); 
                
                if ($prod->reference) $codeproduct = $this->l('ref:').$prod->reference;
                else $codeproduct = $this->l('id:').$prod->id;
                
                $itemname = $prod->name;
                    
                $this->context->smarty->assign('codeproduct', $codeproduct);
                $this->context->smarty->assign('itemname', $itemname);
                    
    
    
                $this->context->smarty->assign('LOT_WHATSAPP_NUMBER', Configuration::get('LOT_WHATSAPP_NUMBERO')); 
                
                $this->context->smarty->assign('LOT_WHATSAPP_BUTTON_TEXT', $this->getButtonText($this->context->language->id, $this->context->shop->id)); 
    
                $this->context->smarty->assign('LOT_WHATSAPP_TEXT', $this->getWhatsUpMessage($this->context->language->id, $this->context->shop->id));         
    
                return $this->display(__FILE__, 'whatsup_product.tpl');
    
            }
        }
        
        
    
    }
    
    
    public function hookdisplayReassurance() {
        
        
        if (Tools::getValue('controller') == 'cart') {
            
            if (Configuration::get('LOT_WHATSAPP_CART_ACTIVE') == true)
        
            if (Configuration::get('LOT_WHATSAPP_NUMBERO') != '') {                
    
    
                $this->context->smarty->assign('LOT_WHATSAPP_NUMBER', Configuration::get('LOT_WHATSAPP_NUMBERO')); 
                
                $this->context->smarty->assign('LOT_WHATSAPP_BUTTON_TEXT', $this->getButtonText($this->context->language->id, $this->context->shop->id));
    
                $this->context->smarty->assign('LOT_WHATSAPP_TEXT', $this->getWhatsUpMessageForCart($this->context->cart->id, $this->context->language->id, $this->context->shop->id));         
    
                return $this->display(__FILE__, 'whatsup_cart.tpl');
    
            }
          
        } else if (Tools::getValue('controller') == 'order') {
            
                if (Configuration::get('LOT_WHATSAPP_CHECKOUT_ACTIVE') == true) {
                    
                $this->context->smarty->assign('LOT_WHATSAPP_NUMBER', Configuration::get('LOT_WHATSAPP_NUMBERO')); 
                
                $this->context->smarty->assign('LOT_WHATSAPP_BUTTON_TEXT', $this->getButtonText($this->context->language->id, $this->context->shop->id));
    
                $this->context->smarty->assign('LOT_WHATSAPP_TEXT', $this->getWhatsUpMessageForCart($this->context->cart->id, $this->context->language->id, $this->context->shop->id));         
    
                return $this->display(__FILE__, 'whatsup_cart.tpl');
                    
                }
        
        }
        
        
        
         
        
    
    
    }
    
    
    
    public function hookdisplayShoppingCart() {
        
        if ((Configuration::get('LOT_WHATSAPP_CHECKOUT_ACTIVE') == true) || (Configuration::get('LOT_WHATSAPP_CART_ACTIVE') == true))  {
                    
                $this->context->smarty->assign('LOT_WHATSAPP_NUMBER', Configuration::get('LOT_WHATSAPP_NUMBERO')); 
                
                $this->context->smarty->assign('LOT_WHATSAPP_BUTTON_TEXT', $this->getButtonText($this->context->language->id, $this->context->shop->id));
    
                $this->context->smarty->assign('LOT_WHATSAPP_TEXT', $this->getWhatsUpMessageForCart($this->context->cart->id, $this->context->language->id, $this->context->shop->id));         
    
                return $this->display(__FILE__, 'whatsup_cart.tpl');
                    
        }
        
    }
    
    
    
    
}
