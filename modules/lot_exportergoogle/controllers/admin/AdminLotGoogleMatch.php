<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

class AdminLotGoogleMatchController extends ModuleAdminController
{
	
	public function __construct()
	{
		
		$this->module = 'lot_exportergoogle';
        $this->lang = true;
        $this->context = Context::getContext();
        $this->bootstrap = true;
        $this->html = '';
        
        $this->allow_export = true;

        $this->token = Tools::getAdminToken(
            $this->module.(int)Tab::getIdFromClassName($this->module).(int)Context::getContext()->cookie->id_employee
        );
        
        
        $this->gcats = Db::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."lot_exportergoogle_google_categories");
        
        $this->maincat = array();
        
        $this->levels = array('2' => 2, '3' => 3, '4' => 4, '5' => 5);
        
        
		parent::__construct();
        
	}

	protected function loadObject($opt = false)
	{
		if ($id = Tools::getValue($this->identifier))
			return new $this->className($id);
		return new $this->className();
	}

    
    
    public function getGoogleCategory($content, $tr) {
        
        $getGoogleCat = Db::getInstance()->getRow("SELECT gm.google_id, gc.google_value FROM "._DB_PREFIX_."lot_exportergoogle_category_map gm LEFT JOIN "._DB_PREFIX_."lot_exportergoogle_google_categories gc ON gc.google_id=gm.google_id WHERE gm.id_category='".pSQL($content)."'");
        
        if ($getGoogleCat) return '<input type="text" id="gcatselect_'.$content.'" class="gcatselect" data-id_category="'.$content.'" data-google_id="'.$getGoogleCat['google_id'].'" value="'.$getGoogleCat['google_value'].'" title="'.$getGoogleCat['google_value'].'" placeholder="'.$this->l('click to show all options, type to filter').'" autocomplete="off" />';
        else {  
        return '<input type="text" id="gcatselect_'.$content.'" class="gcatselect" data-id_category="'.$content.'" data-google_id="0" value="" placeholder="'.$this->l('click to show all options, type to filter').'" autocomplete="off" />';
        }
        
    }
    
    
    public function getCategoryPath($content, $tr) {
        
        $id_category = $content;
        
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
           $breadcrumb[] = '/'; 
        }
    
    
        return implode(' > ',$breadcrumb);
        
    }
    
    
   
    public function getActionButtons($content, $tr) {
        
        $buttons = array();
        $buttons['apply_gcat'] = '<button class="button btn apply_gcat" data-id_category="'.(int)trim($content).'">'.$this->l('Apply').'</button>'; 
        
        $haschildren = Db::getInstance()->getRow("SELECT id_category FROM "._DB_PREFIX_."category WHERE id_parent='".pSQL($content)."' AND active='1'");
        
        if ($haschildren) $haschildreninput = '<input type="hidden" id="haschildren_'.$content.'" value="1">';
        else $haschildreninput = '<input type="hidden" id="haschildren_'.$content.'" value="0">';
        
        
        return implode('',$buttons).$haschildreninput;
        
    }
    
    
    
    
    
    
    public function clearfilters() {
        
        foreach (Tools::getAllValues() as $key=>$filtru)
            
            if (Tools::strpos($key,'Filter_')) {
            
                $_POST[$key] = '';
                
            }
        
       $_POST['CategoryListFilter_level_depth'] = 2; 
        
    }
    
    
    public function getFiltreSintax($idlist) {
        
        $filtre = '';
        
        if (Tools::isSubmit('submitReset'.$idlist)) { $filtre .= " AND t.level_depth = '2'"; $_POST[$idlist.'Filter_level_depth'] = 2; return $filtre." "; }
        
       
        foreach (Tools::getAllValues() as $key=>$filtru)
            
            if (Tools::strpos($key,'Filter_')) {    
                   
                        if ($filtru != '') {
                   
                            $field = Tools::str_replace_once($idlist.'Filter_','',$key);

                            if ($field == 'name') $prefix = 'tl.';
                            else $prefix = 't.';
                            
                            if ( $prefix == 'tl.' )
                            $filtre .= " AND ".$prefix.$field." LIKE '%".$filtru."%'";
                            else $filtre .= " AND ".$prefix.$field." = '".$filtru."'";
                            
                            }
                    
                        }
        
    
        
        if (Tools::getValue('id_category')) {
            
            $current_level_depth = Db::getInstance()->getValue("SELECT level_depth FROM "._DB_PREFIX_."category WHERE id_category='".pSQL(Tools::getValue('id_category'))."'");
            
            $current_level_depth = $current_level_depth + 1;
            
            $filtre .= " AND t.id_parent = '".pSQL(Tools::getValue('id_category'))."' AND t.level_depth = '".pSQL($current_level_depth)."'";
            
            $_POST[$idlist.'Filter_level_depth'] = $current_level_depth;
            
        } else if (!Tools::getValue($idlist.'Filter_level_depth')) {
            $filtre .= " AND t.level_depth = '2'";
            $_POST[$idlist.'Filter_level_depth'] = 2;
        }
        
        
        if  ($filtre == '') return " "; 
       
        return $filtre." ";
        
    }
    
    
    public function getOrderSintax($idlist) {
        
        $ordine = '';
        
        if ( Tools::getValue($idlist.'Orderby') ) $ordine .= ' ORDER BY '.Tools::getValue($idlist.'Orderby')." ".Tools::getValue($idlist.'Orderway')." "; 
        
        return $ordine;
        
    }
    
    
    
    
    
	
    
    
    
    public function renderList(){ 
        
        $_html='';
        
        //var_dump(Tools::getAllValues());
        
        if (Tools::isSubmit('submitResetCategoryList'))
        $this->clearfilters();
        
        if (Tools::isSubmit('savegeneralsettins'))
            if ($this->validateForm())
                $this->processForm();
        
        $shopbaseurl=_PS_BASE_URL_.__PS_BASE_URI__;
        $shopbaseurl=str_replace('https://','//',$shopbaseurl);
        $shopbaseurl=str_replace('http://','//',$shopbaseurl);
       
        $this->context->smarty->assign('module_dir', $shopbaseurl.'modules/lot_exportergoogle/');
        
        
         $_html .= $this->context->smarty->fetch(_PS_MODULE_DIR_.'lot_exportergoogle/views/templates/admin/googlematch.tpl');
        
        if ($this->hasGoogleLists()) {
        
        $_html .= $this->renderForm();
    
    
        $this->current_selection = $this->renderCategoryList();
        
        /*
        if ( Tools::isSubmit('CategoryListFilter_level_depth') &&  (Tools::getValue('CategoryListFilter_level_depth') > 2) )
        $_html .= '<button class="back btn btn-default">'.$this->l('Back').'</button>';
        */
        
        $_html .= $this->showlist($this->current_selection);
        }
        else $_html .='<div class="loading download">'.$this->l('Downloading Google Taxonomy... Please wait').'</div>';
    
        return $_html;
     }
    
    
    
    public function renderCategoryList() {
        
        
		$helperList = new HelperList();
        
        
		$helperList->table = 'category';
		$helperList->table_lang = 'category_lang';
		$helperList->shopLinkType = '';
		$helperList->identifier = 'id_category';
		$helperList->position_identifier = 'id_category';
		$helperList->list_id = 'CategoryList';
        
        $helperList->allow_export = true;

		$helperList->module = $this->module;
		$helperList->_default_pagination = 50;
		$helperList->_pagination = array(50, 100, 500, 1000, 5000);
		
		$helperList->token = Tools::getValue('token');
		$helperList->currentIndex = $this->context->link->getAdminLink('AdminLotGoogleMatch', false);

		$helperList->actions = array('view');
        
		$helperList->show_toolbar = true;
		$helperList->toolbar_scroll = true;
		
        $helperList->_defaultOrderBy = 'id_category';
        $helperList->orderBy = 'id';
		$helperList->orderWay = 'ASC';
        
        /*
        $helperList->toolbar_btn = array(
            'export' => array(
                'desc' => $this->l('Export'),
                'href' => $helperList->currentIndex.'&token='.$helperList->token.'&export=1'.(trim($filtreforexport) != '' ? '&filtreexport='.base64_encode($filtreforexport) : '').($orderforexport != '' ? '&orderexport='.base64_encode($orderforexport) : ''),
            ),
            'import' => array(
                'desc' => $this->l('Import borderou'),
                'href' => $helperList->currentIndex.'&token='.$helperList->token.'&import=1',
            )
        );
        */
        
    
        
        if ($this->getOrderSintax($helperList->list_id) != '') {
            
            $count = Db::getInstance()->getValue("SELECT COUNT(distinct t.id_category) as totalitems ". "FROM " . _DB_PREFIX_ .$helperList->table." t INNER JOIN "._DB_PREFIX_.$helperList->table_lang." tl ON tl.id_category=t.id_category WHERE t.id_category>2 AND tl.id_lang='".pSQL($this->context->language->id)."'".$this->getFiltreSintax($helperList->list_id).$this->getOrderSintax($helperList->list_id));
            
        }
        
        else {
            
            $count = Db::getInstance()->getValue("SELECT COUNT(distinct t.id_category) as totalitems ". "FROM " . _DB_PREFIX_ .$helperList->table." t INNER JOIN "._DB_PREFIX_.$helperList->table_lang." tl ON tl.id_category=t.id_category WHERE t.id_category>2 AND tl.id_lang='".pSQL($this->context->language->id)."'".$this->getFiltreSintax($helperList->list_id));
             
        }
        
    
        
        $helperList->listTotal = $count;
        
        if (Tools::getValue($helperList->list_id.'_pagination'))
			$prodsperpage = (int) Tools::getValue($helperList->list_id.'_pagination');
		else
			$prodsperpage = $helperList->_default_pagination;
        
        if (Tools::getValue('submitFilter'.$helperList->list_id))
			$currentpage = (int) Tools::getValue('submitFilter'.$helperList->list_id);
		else
			$currentpage = 1;
        
        if ($currentpage > 1) {
			$totalnumberofpages = floor($helperList->listTotal / $prodsperpage);
			if ($helperList->listTotal % $prodsperpage > 0)
				$totalnumberofpages = $totalnumberofpages + 1;

			if ($currentpage > $totalnumberofpages)
				$currentpage = 1;
		}

        
        
        if ($this->getOrderSintax($helperList->list_id) != '') {
            
            $helperList->_select = Db::getInstance()->ExecuteS("SELECT t.id_category, t.id_parent, t.level_depth, tl.name, t.id_category AS google_category, t.id_category AS action_id ". "FROM " . _DB_PREFIX_ .$helperList->table." t LEFT JOIN "._DB_PREFIX_.$helperList->table_lang." tl ON tl.id_category=t.id_category WHERE t.id_category>2 AND tl.id_lang='".pSQL($this->context->language->id)."'".$this->getFiltreSintax($helperList->list_id).$this->getOrderSintax($helperList->list_id)." LIMIT " . (pSQL($currentpage - 1) * $prodsperpage) . ',' . pSQL($prodsperpage));
            
        }
        
        else {
            
            $helperList->_select = Db::getInstance()->ExecuteS("SELECT t.id_category, t.id_parent, t.level_depth, tl.name, t.id_category AS google_category, t.id_category AS action_id ". "FROM " . _DB_PREFIX_ .$helperList->table." t LEFT JOIN "._DB_PREFIX_.$helperList->table_lang." tl ON tl.id_category=t.id_category WHERE t.id_category>2 AND tl.id_lang='".pSQL($this->context->language->id)."'".$this->getFiltreSintax($helperList->list_id)." LIMIT " . (pSQL($currentpage - 1) * $prodsperpage) . ',' . pSQL($prodsperpage));
            
        }
        
        
        return $helperList; 
    
    }
    
    
    public function showlist($helperList) {
        
        $fields_list = array(
			'id_category' => array('title' => $this->l('ID Category'), 'orderby' => false, 'search' => true, 'remove_onclick' => true,  'class' => 'col-lg-2 nopointer'),
            'id_parent' => array('title' => $this->l('Parent'), 'orderby' => false, 'search' => false, 'remove_onclick' => true, 'callback' => 'getCategoryPath', 'class' => 'nopointer'),
			'name' => array('title' => $this->l('Category name'), 'orderby' => false, 'search' => true, 'remove_onclick' => true, 'class' => 'nopointer'),
            'google_category' => array('title' => $this->l('Google Category'), 'orderby' => false, 'search' => false, 'remove_onclick' => true, 'callback' => 'getGoogleCategory'),
            'level_depth' => array('title' => $this->l('Level'), 'orderby' => false, 'search' => true, 'class' => 'level_depth', 'remove_onclick' => true, 'type' => 'select', 'list' => $this->levels, 'filter_key' => 'level_depth', 'filter_type' => 'int', 'order_key' => 'level_depth', 'class' => 'nopointer'),
			'action_id' => array('title' => $this->l('Actions'), 'orderby' => false, 'search' => false, 'remove_onclick' => false, 'callback' => 'getActionButtons')
        );
         
    

       return $helperList->generateList($helperList->_select, $fields_list);
    
    }
    
    
    
    
    public function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this->module;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'savegeneralsettins';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminLotGoogleMatch', false);
        $helper->token = Tools::getValue('token');
        
       
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
                'title' => $this->l('General Settigs'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    
                    array(
                        'type' => 'hidden',
                        'name' => 'google_id',
                        'id' => 'google_id'
                        ),
                    
                    array(
                        'type' => 'text',
                        'name' => 'google_category_main',
                        'id' => 'google_category_main',
                        'autocomplete' => false,
                        'placeholder' => $this->l('click to show all options, type to filter'),
                        'label' => $this->l('Main Google Category'),
                        'desc' => $this->l('0 for unset. By setting the Main Google Category you filter the list of Google categories displayed on mapping options, only it\'s subcategories will be displayed'),
                        'class' => 'col-lg-2'
                        ),
                    
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
                /*
                'buttons' => array(
                    array(
                    'href' => $modellink,
                    'title' => $this->l('Apply to all'),
                    'icon' => 'process-icon-download'
                    )
                ),
                */
                
            ),
        );
        
        
    }
    
   
    protected function getConfigFormValues()
    {
    
        if (Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID')) {
            
            if (Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID') != 0) {
            
            $google_category_main = Db::getInstance()->getValue("SELECT google_value FROM "._DB_PREFIX_."lot_exportergoogle_google_categories WHERE google_id='".pSQL(Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID'))."'");
            
            return array(
            'google_id' => Configuration::get('LOT_EXPORTERGOOGLE_MAIN_GCID'),
            'google_category_main' => $google_category_main
            );
                
            }
            
        }
        
        return array(
            'google_id' => 0,
            'google_category_main' => ''
        );
        
    }

    
    protected function validateForm()
    {
        
        if (Tools::getValue('google_category_main') != '0') {
        
        if ((Tools::getValue('google_id') == 0) || !Tools::getValue('google_id')) {
            $this->errors[] = $this->l('Empty Google Category. Please select one!');
            return false; 
        }
            
        }
        
        return true;
        
    }

    
    
    protected function processForm() {
        
        if (Tools::getValue('google_category_main') == '0')
        Configuration::updateValue('LOT_EXPORTERGOOGLE_MAIN_GCID', 0);    
        else    
        Configuration::updateValue('LOT_EXPORTERGOOGLE_MAIN_GCID', Tools::getValue('google_id'));
    
        $this->confirmations[] = $this->l('Settings updated!');
        
    }
    
    
    
   public function hasGoogleLists() {
       
       $gcats = Db::getInstance()->ExecuteS("SELECT google_id FROM "._DB_PREFIX_."lot_exportergoogle_google_categories WHERE google_id>'0'");
       
       if (count($gcats) > 100) return true;
       else return false;
       
   }
    
     
    
}