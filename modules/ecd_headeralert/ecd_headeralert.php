<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class ECD_HeaderAlert extends Module {
    
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'ecd_headeralert';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Lot.ai';
        $this->need_instance = 1;

    
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Header Alert');
        $this->description = $this->l('');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this module?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        
        $this->design_fields = $this->getDesignFields();    
            
    }
    
    
    public function getDesignFields($default = true) {
        
        
        $data = array();
        $data['smarty'] = array();
        $data['js'] = array();
        
        $data['smarty']['background-color'] = '#000';
        $data['smarty']['color'] = '#fff';
        $data['smarty']['font_size'] = '14px';
        
        $data['js']['date_begin'] = '';
        $data['js']['date_end'] = '';
        
        return $data;
        
    }
    

    
    
    public function install()
    {

        include(dirname(__FILE__).'/sql/install.php');
        

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader');
    }

    
    public function uninstall()
    {
    
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
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
        
        if ($data = $this->getHeaderAlerts()) {
            
            Media::addJsDef($data['js']);  
            $this->context->smarty->assign('design', $data['smarty']);
            
            $this->context->controller->addCSS($this->_path.'/views/css/front.css');
            $this->context->controller->addJS($this->_path.'/views/js/front.js');
            
            return $this->display(__FILE__, 'style.tpl');
            
        }
          
    }
    
    
    public function getHeaderAlerts() {
        
        $nowdatetime = date('Y-m-d H:i:s', time());
        
        $active_entities = Db::getInstance()->getRow("SELECT id FROM "._DB_PREFIX_."ecd_headeralerts WHERE date_begin <='".pSQL($nowdatetime)."' AND date_end >= '".pSQL($nowdatetime)."' AND active=1");
        
        if (!$active_entities) return false;
    
        $data = array();
        $data['smarty'] = array();
        $data['js'] = array();
        
        $general_settings = Db::getInstance()->getRow("SELECT e.*, el.* FROM "._DB_PREFIX_."ecd_headeralerts e LEFT JOIN "._DB_PREFIX_."ecd_headeralerts_lang el ON e.id = el.id WHERE e.id='".pSQL($active_entities['id'])."' AND el.id_lang='".pSQL($this->context->language->id)."'");
        
        //var_dump($general_settings);
        
        $page_design = Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."ecd_headeralerts_design WHERE id='".pSQL($active_entities['id'])."'");
        
        if ($page_design['other_design'] != '' || $page_design['other_design'] != null)
        $custom_design = unserialize($page_design['other_design']);
        else $custom_design = array();
        
        //var_dump($custom_design);
        
        $data['smarty']['background_color'] = ($page_design['background'] ? $page_design['background'] : '#000');
        $data['smarty']['color'] = ($page_design['color'] ? $page_design['color'] : '#fff');
        $data['smarty']['font_size'] = ($page_design['font_size'] ? $page_design['font_size'] : '14px');
        
        $data['js']['date_begin'] = $general_settings['date_begin'];
        $data['js']['date_end'] = $general_settings['date_end'];
        $data['js']['alert_content'] = $general_settings['content'];

        return $data;
             
        
    }
    
    
    public function isCurrentSubcategory() {
        
    }
    
    
    
    public function getContent()
    {
    
        $this->_html='';  
        
       
        if (((bool)Tools::isSubmit('submitLot_Module')) == true) {
         if ($this->postValidation())    
            $this->postProcess();
        }
        
        
        if (Tools::getIsset('deleteecd_headeralerts')) {
            $this->postProcessDeleteListItem(Tools::getValue('id'));
        }
        
        
        if (Tools::getIsset('statusecd_headeralerts')) {
            $this->postProcessActivateDeactivate(Tools::getValue('id'));
        }
        
        
        
        $this->context->smarty->assign('module_dir', $this->_path);
        
        $this->context->smarty->assign('module_uri', _PS_BASE_URL_._MODULE_DIR_.'eventstream/');
        
        $this->context->smarty->assign('secure_key', $this->secure_key);
        
        $this->context->smarty->assign('current_shop_id', $this->context->shop->id);
        

        $this->_html .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        
        if (Tools::getIsset('updateecd_headeralerts') ||    Tools::getIsset('addnewevent'))
            $this->_html .= $this->renderForm();
        else
        $this->_html .= $this->renderList();
        
        

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
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&id='.(Tools::getValue('id') ? Tools::getValue('id') : 0).'&updateecd_headeralerts';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        
       
        
         
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        
        

        return $helper->generateForm(array($this->getConfigForm()));
        
    }
    

    
    public function renderList() {
        
        
		$helperList = new HelperList();
        
        
		$helperList->table = 'ecd_headeralerts';
		$helperList->shopLinkType = '';
		$helperList->identifier = 'id';
		//$helperList->position_identifier = 'id_media';
		$helperList->list_id = 'header_alerts';

		$helperList->module = $this;
		$helperList->_default_pagination = 20;
		$helperList->_pagination = array(20, 50, 100);
		$helperList->title = $this->l('Header Alerts');
		$helperList->token = Tools::getAdminTokenLite('AdminModules');
		$helperList->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

		$helperList->actions = array('edit','delete');
        
		$helperList->show_toolbar = true;
		$helperList->toolbar_scroll = true;
		
        $helperList->_defaultOrderBy = 'position';
        $helperList->orderBy = 'position';
		$helperList->orderWay = 'ASC';
        
        
        $helperList->toolbar_btn = array(
            'new' => array(
            'desc' => $this->l('Add new header alert'),
            'href' => $helperList->currentIndex.'&token='.$helperList->token.'&addnewevent=1',
            )
        );
        
        
        
    
        $count = Db::getInstance()->getValue("SELECT COUNT(distinct id) as totalitems "
				. "FROM " . _DB_PREFIX_ . "ecd_headeralerts");

        $helperList->listTotal = $count;
        
        $helperList->_select = Db::getInstance()->ExecuteS("SELECT ev.*, evl.* FROM "._DB_PREFIX_."ecd_headeralerts ev LEFT JOIN "._DB_PREFIX_."ecd_headeralerts_lang evl ON ev.id = evl.id WHERE evl.id_lang='".pSQL($this->context->language->id)."' ORDER BY ev.id ASC");
        

        $fields_list = array(
			'id' => array('title' => $this->l('ID'), 'orderby' => false, 'search' => false, 'remove_onclick' => true),
			'name' => array('title' => $this->l('Name'), 'orderby' => false, 'search' => false, 'remove_onclick' => true),
			'date_begin' => array('title' => $this->l('Begins on'), 'orderby' => false, 'search' => false, 'remove_onclick' => true),
			'date_end' => array('title' => $this->l('Ends on'), 'orderby' => false, 'search' => false, 'remove_onclick' => true),
			'active' => array('title' => $this->l('Active'), 'orderby' => false, 'search' => false, 'active' => 'status', 'align' => 'center', 'type' => 'bool')
        );

       return $helperList->generateList($helperList->_select, $fields_list);
    
    }
    
 
    protected function getConfigForm()
    {
        
   
        $form = array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Add/edit alert'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    
                    
                    array(
                        'type' => 'hidden',
                        'label' => $this->l('ID'),
                        'name' => 'id',
                        ),
                
                    array(
                        'type' => 'text',
                        'name' => 'name',
                        'label' => $this->l('Nume alert (intern)'),
                        'required' => true,
                        'lang' => false,
                        'class' => 'col-lg-2'
                        ),
                    
                    array(
                        'type' => 'datetime',
                        'name' => 'date_begin',
                        'label' => $this->l('Data incepere'),
                        'required' => true,
                        'class' => 'col-lg-2 datetime datetimepicker'
                        ),
                    
                    
                    array(
                        'type' => 'datetime',
                        'name' => 'date_end',
                        'label' => $this->l('Data sfarsit'),
                        'required' => true,
                        'class' => 'col-lg-2 datetime datetimepicker'
                        ),

                    array(
                        'type' => 'text',
                        'name' => 'background',
                        'label' => $this->l('Background Color'),
                        ),
                    
                    array(
                        'type' => 'text',
                        'name' => 'color',
                        'label' => $this->l('Text Color'),
                        ),
                    
                    array(
                        'type' => 'text',
                        'name' => 'font_size',
                        'label' => $this->l('Text font_size'),
                        ),
 
                    array(
                        'type' => 'textarea',
                        'name' => 'content',
                        'label' => $this->l('Content'),
                        'lang' => true,
                        'cols' => 100,
                        'rows' => 10,
                        'autoload_rte' => 'rte'
                        ),
                    
                    array(
                        'type' => 'switch',
                        'name' => 'active',
                        'label' => $this->l('Activ'),
                        'class' => 'col-lg-2',
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
                    
                    
                   
                    
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                'buttons' => array(
                    array(
                    'href' => $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&token='.Tools::getValue('token'),
                    'title' => $this->l('Back to list'),
                    'icon' => 'process-icon-back'
                    ),
                ),
            ),
        );
        
        /*
            $designfields = $this->getDesignFields();    
                    foreach ($designfields['smarty'] as $key=>$fields) {
                        if (is_array($fields)) {
                            foreach ($fields as $keyf=>$val)
                                $form['form']['input'][] = array(
                                    'type' => 'text',
                                    'name' => $key.'val'.$keyf,
                                    'label' => $this->labelize($key.' '.$keyf),
                                );
                        }
                        
                    }
                 */      
        
       
        return $form;
        
    }
    
    
    
    
    protected function getConfigFormValues()
    {
        
        $content = array();
        
        
        foreach ($this->context->controller->getLanguages() as $lang) {
            
            $content[$lang['id_lang']] = ''; 
        
        }
        
        
        if (Tools::getValue('id')) {
            
            $geteventdata = Db::getInstance()->getRow("SELECT el.*, ell.*, eld.* FROM "._DB_PREFIX_."ecd_headeralerts el LEFT JOIN "._DB_PREFIX_."ecd_headeralerts_lang ell ON el.id=ell.id LEFT JOIN "._DB_PREFIX_."ecd_headeralerts_design eld ON eld.id = el.id WHERE el.id='".pSQL(Tools::getValue('id'))."'");
            
            if ($geteventdata) {
                
                foreach ($this->context->controller->getLanguages() as $lang) {
                    $content[$lang['id_lang']] = Db::getInstance()->getValue("SELECT content FROM "._DB_PREFIX_."ecd_headeralerts_lang WHERE id='".pSQL($geteventdata['id'])."' AND id_lang='".pSQL($lang['id_lang'])."'");   

                }
                
                $design =  Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."ecd_headeralerts_design WHERE id='".pSQL($geteventdata['id'])."'");  
                
                
                
                $form_fields = array(
                'id' => $geteventdata['id'],
                'name' => $geteventdata['name'],
                'content' => $content,
                'date_begin' => $geteventdata['date_begin'],
                'date_end' => $geteventdata['date_end'],
                'background' => $design['background'],    
                'color' => $design['color'],
                'font_size' => $design['font_size'],
                'active' => $geteventdata['active']
                ); 
                
                /*
                if ($geteventdata)
                $geteventdata_decoded = unserialize($geteventdata['other_design']);
                
                $designfields = $this->getDesignFields();    
                    foreach ($designfields['smarty'] as $key=>$fields) {
                        if (is_array($fields)) {
                            foreach ($fields as $keyf=>$val)
                                $form_fields[$key.'val'.$keyf] = isset($geteventdata_decoded[$key.'val'.$keyf]) ? $geteventdata_decoded[$key.'val'.$keyf] : '';
                        }
                        
                    }
                    */
                      
                
                
            }
            
            else {
                
            $form_fields = array(
                'id' => 0,
                'name' => '',
                'content' => $content,
                'date_begin' => '',
                'date_end' => '',
                'background' => '',    
                'color' => '',
                'font_size' => '',
                'active' => false
                ); 
                
                /*
                $designfields = $this->getDesignFields();    
                    foreach ($designfields['smarty'] as $key=>$fields) {
                        if (is_array($fields)) {
                            foreach ($fields as $keyf=>$val)
                                $form_fields[$key.'val'.$keyf] = '';
                        }
                        
                    }
                    
                    */
                
                
                
            }
            
        }
        else {
        
            $form_fields = array(
                'id' => 0,
                'name' => '',
                'content' => $content,
                'date_begin' => '',
                'date_end' => '',
                'background' => '',    
                'color' => '',
                'font_size' => '',
                'active' => false
                ); 
            
            /*
                    $designfields = $this->getDesignFields();    
                    foreach ($designfields['smarty'] as $key=>$fields) {
                        if (is_array($fields)) {
                            foreach ($fields as $keyf=>$val)
                                $form_fields[$key.'val'.$keyf] = '';
                        }
                        
                    }
            
            */
            
            
        }
        
      return $form_fields;  
        
    }
    
    
    

    protected function postValidation() {
        
        if (Tools::getValue('name') == '') {
            $this->_html.=$this->displayError($this->l('Numele intern este gol'));
            return false;
        }
        
        if (strtotime(Tools::getValue('date_begin')) > strtotime(Tools::getValue('date_end'))) {
            $this->_html.=$this->displayError($this->l('Perioada de afisare este invalida'));
            return false;
        }
        
        
        return true;
        
    }
    

  
    protected function postProcess()
    {
    
        if ((int)Tools::getValue('id') == 0) {

        $action=Db::getInstance()->insert('ecd_headeralerts', array(
                'name' => pSQL(Tools::getValue('name'),false),
                'date_begin' => pSQL(Tools::getValue('date_begin'),false),
                'date_end' => pSQL(Tools::getValue('date_end'),false),
                'active' => pSQL(Tools::getValue('active')),
                ));
            
            $id_event = Db::getInstance()->getValue("SELECT id FROM "._DB_PREFIX_."ecd_headeralerts ORDER BY id DESC");
            
            $addingdesign = $this->addDesign($id_event);
        
            foreach ($this->context->controller->getLanguages() as $lang) {
                
                $action_lang=Db::getInstance()->insert('ecd_headeralerts_lang', array(
                'id' => pSQL($id_event),
                'id_lang' => pSQL($lang['id_lang']),
                'content' => pSQL(Tools::getValue('content_'.$lang['id_lang']),true)
                ));   
                
           
            }
            
        }
        else {
            
            $id_event = Tools::getValue('id');
          
            $action=Db::getInstance()->update('ecd_headeralerts', array(
            'name' => pSQL(Tools::getValue('name'),false),
            'date_begin' => pSQL(Tools::getValue('date_begin'),false),
            'date_end' => pSQL(Tools::getValue('date_end'),false),
            'active' => pSQL(Tools::getValue('active'))
            ), "id='".pSQL(Tools::getValue('id'))."'");
            
            $addingdesign = $this->addDesign($id_event);
            
            foreach ($this->context->controller->getLanguages() as $lang) {
                
                $action=Db::getInstance()->update('ecd_headeralerts_lang', array(
                'content' => pSQL(Tools::getValue('content_'.$lang['id_lang']),true)
                ), "id='".pSQL(Tools::getValue('id'))."' AND id_lang='".pSQL($lang['id_lang'])."'");
                
           
            }
            
          
        }
        
        
        if (!$action) {
            $this->_html.=$this->displayError($this->l('Database update error!'));
            return false;
        }
        
        
        $this->_html.=$this->displayConfirmation($this->l('Settings updated!'));
        
        
    }
    
    
    
    public function addDesign($id_event) {
        
        Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ecd_headeralerts_design WHERE id='".pSQL($id_event)."'");
        
        
        $other_design = array();
        
        /*
        $designfields = $this->getDesignFields();    
        foreach ($designfields['smarty'] as $key=>$fields) {
            if (is_array($fields)) {
                foreach ($fields as $keyf=>$val)
                    $other_design[$key.'val'.$keyf] = Tools::getValue($key.'val'.$keyf);
            }

        }
        */
        //$other_design = serialize($other_design);
        
        Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."ecd_headeralerts_design (id, background, color, font_size) VALUES ('".pSQL($id_event)."','".pSQL(Tools::getValue('background'))."','".pSQL(Tools::getValue('color'))."', '".pSQL(Tools::getValue('font_size'))."')");
         
    }
    
    
    public function postProcessDeleteListItem($id) {

        $action = Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ecd_headeralerts WHERE id='".pSQL($id)."'");
        
        $action = Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ecd_headeralerts_lang WHERE id='".pSQL($id)."'");
        
        $action = Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ecd_headeralerts_design WHERE id='".pSQL($id)."'");
                
        if (!$action) {
                $this->_html.=$this->displayError($this->l('Database error!'));
                return false;
        }
        
        $this->_html.=$this->displayConfirmation($this->l('The item was removed from the list!'));
        
    }
    
    
    public function postProcessActivateDeactivate($id) {
        
        $current_status = Db::getInstance()->getValue("SELECT active FROM "._DB_PREFIX_."ecd_headeralerts WHERE id='".pSQL($id)."'");
        
        if ($current_status == 0) $new_status = 1;
        else $new_status = 0;
    
        $action = Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."ecd_headeralerts SET active='".pSQL($new_status)."' WHERE id='".pSQL($id)."'");
                
        if (!$action) {
                $this->_html.=$this->displayError($this->l('Database error!'));
                return false;
        }
          
    }
    

    
 

    public function labelize($string) {
    
        return ucwords(str_replace('_',' ',str_replace('-',' ', $string)));
    
    }
    
 
    
}


