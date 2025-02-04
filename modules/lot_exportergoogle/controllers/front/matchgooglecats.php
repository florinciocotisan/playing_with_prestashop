<?php
/**
* 2018 Ecomdoo Software SRL
*
* @author Ecomdoo Software SRL (LOT.ai)
* @copyright 2018 Ecomdoo Software SRL
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

//require(_PS_MODULE_DIR_.'lot_exportergoogle/LotGoogleCats.php');

class Lot_ExporterGoogleMatchGoogleCatsModuleFrontController extends ModuleFrontController
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
        
        $shop_link = Tools::getHttpHost(true).__PS_BASE_URI__;

        $idshop=(int)Context::getContext()->shop->id;
        $shop=new Shop($idshop);
        
        $output='';
        
        $callingfunction = (string)Tools::getValue('function');
    
        $output = $this->module->matchclass->$callingfunction(Tools::getAllValues());
        
        $output=array('success' => 1, 'result' => $output);
        
        die(json_encode($output));

    
    }
    
    
    
    
 
}



    
?>