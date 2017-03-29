<?php
/**
* Copyright 2017 OMI Europa S.L (Packlink)

* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at

*  http://www.apache.org/licenses/LICENSE-2.0

* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Packlink extends Module
{

    /**
     * Module link in BO
     * @var String
     */
    private $module_link;

    /**
     * Logs
     * @var array
     */
    public $logs = array();

    /**
     * If debug mod
     * @var boolean
     */
    public $debug = false;

    public $pl_hook = false;

    public $dev = false;

    public $pl_url;

    /**
     * Constructor of module
     */
    public function __construct()
    {
        $this->name = 'packlink';
        $this->tab = 'shipping_logistics';
        $this->version = '1.6.4';
        $this->author = '202-ecommerce';
        $this->module_key = 'a7a3a395043ca3a09d703f7d1c74a107';
        $this->ps_versions_compliancy = array('min' => '1.5', 'max' => '2.0');
        $this->bootstrap = true;

        parent::__construct();

        $this->includeFiles();

        $this->displayName = $this->l('Packlink PRO Shipping');
        $this->description = $this->l('Save up to 70% on your shipping costs. No fixed fees, no minimum shipping volume required. Manage all your shipments in a single platform.');

        $default_language = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
        $language         = Tools::strtolower($default_language->iso_code);
     
        if ($language != "it" && $language != "es" && $language != "fr" && $language != "de") {
            $language = "es";
        }

        if ($this->dev) {
            $this->pl_url = "https://".$language."-profront-integration.packitos.com";
        } else {
            $this->pl_url = "https://pro.packlink.".$language;
        }
    }

    private function includeFiles()
    {
        $path = $this->getLocalPath().'classes'.DIRECTORY_SEPARATOR;
        foreach (scandir($path) as $class) {
            if ($class != "index.php" && is_file($path.$class)) {
                $class_name = Tools::substr($class, 0, -4);
                if ($class_name != 'index' && !preg_match('#\.old#isD', $class) && !class_exists($class_name)) {
                    require_once $path.$class_name.'.php';
                }
            }
        }

        $path .= 'helper'.DIRECTORY_SEPARATOR;

        foreach (scandir($path) as $class) {
            if ($class != "index.php" && is_file($path.$class)) {
                $class_name = Tools::substr($class, 0, -4);
                if ($class_name != 'index' && !preg_match('#\.old#isD', $class) && !class_exists($class_name)) {
                    require_once $path.$class_name.'.php';
                }
            }
        }

        $path .= '..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'api'.DIRECTORY_SEPARATOR;

        foreach (scandir($path) as $class) {
            if ($class != "index.php" && is_file($path.$class)) {
                $class_name = Tools::substr($class, 0, -4);
                if ($class_name != 'index' && !preg_match('#\.old#isD', $class) && !class_exists($class_name)) {
                    require_once $path.$class_name.'.php';
                }
            }
        }
    }


    ############################################################################################################
    # Install / Upgrade / Uninstall
    ############################################################################################################

    /**
     * Module install
     * @return boolean if install was successfull
     */
    public function install()
    {
        // Install default
        if (!parent::install()) {
            return false;
        }

        // install DataBase
        if (!$this->installSQL()) {
            return false;
        }
        if (!Configuration::updateValue('PL_IMPORT', 1)) {
            return false;
        }
        if (!Configuration::updateValue('PL_CREATE_DRAFT_AUTO', 1)) {
            return false;
        }
        if (!Configuration::updateValue('PL_API_KEY', '')) {
            return false;
        }
        if (!Configuration::updateValue('PL_API_KG', '1')) {
            return false;
        }
        if (!Configuration::updateValue('PL_API_CM', '1')) {
            return false;
        }
        if (!Configuration::updateValue('PL_API_VERSION', '')) {
            return false;
        }
        if (!Configuration::updateValue('PL_ST_AWAITING', 0)) {
            return false;
        }
        if (!Configuration::updateValue('PL_ST_PENDING', 3)) {
            return false;
        }
        if (!Configuration::updateValue('PL_ST_READY', 3)) {
            return false;
        }
        if (!Configuration::updateValue('PL_ST_TRANSIT', 4)) {
            return false;
        }
        if (!Configuration::updateValue('PL_ST_DELIVERED', 5)) {
            return false;
        }

        // Install tabs
        if (!$this->installTabs()) {
            return false;
        }

        // Registration hook
        if (!$this->registrationHook()) {
            return false;
        }

        if (!$this->createTab()) {
            return false;
        }
        if (!$this->createTabPdf()) {
            return false;
        }

        return true;
    }

    /**
     * Module uninstall
     * @return boolean if uninstall was successfull
     */
    public function uninstall()
    {

        // Uninstall default
        if (!parent::uninstall()) {
            return false;
        }

        //Uninstall DataBase
        if (!$this->uninstallSQL()) {
            return false;
        }

        if (!PlTotAdminTabHelper::deleteAdminTabs('AdminTabPacklink')) {
            return false;
        }

        if (!PlTotAdminTabHelper::deleteAdminTabs('AdminGeneratePdfPl')) {
            return false;
        }

        // Delete tabs
        if (!$this->uninstallTabs()) {
            return false;
        }

        if (!Configuration::deleteByName('PL_API_KEY')) {
            return false;
        }
        if (!Configuration::deleteByName('PL_IMPORT')) {
            return false;
        }
        if (!Configuration::deleteByName('PL_API_CM')) {
            return false;
        }
        if (!Configuration::deleteByName('PL_API_KG')) {
            return false;
        }
        if (!Configuration::deleteByName('PL_API_VERSION')) {
            return false;
        }
        if (!Configuration::deleteByName('PL_ST_AWAITING')) {
            return false;
        }
        if (!Configuration::deleteByName('PL_ST_PENDING')) {
            return false;
        }
        if (!Configuration::deleteByName('PL_ST_READY')) {
            return false;
        }
        if (!Configuration::deleteByName('PL_ST_TRANSIT')) {
            return false;
        }
        if (!Configuration::deleteByName('PL_ST_DELIVERED')) {
            return false;
        }
        if (!Configuration::deleteByName('PL_CREATE_DRAFT_AUTO')) {
            return false;
        }

        return true;
    }


    ############################################################################################################
    # Tabs
    ############################################################################################################

    public function createTab()
    {
        if (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $parent = 14;
        } else {
            $parent = 13;
        }
        PlTotAdminTabHelper::addAdminTab(array(
            'id_parent' => $parent,
            'className' => 'AdminTabPacklink',
            'default_name' => 'Packlink',
            'name' => 'Packlink PRO',
            'active' => true,
            'module' => $this->name,
        ));

        return true;
    }

    public function createTabPdf()
    {
        PlTotAdminTabHelper::addAdminTab(array(
            'id_parent' => 14,
            'className' => 'AdminGeneratePdfPl',
            'default_name' => 'PacklinkPdf',
            'name' => 'PacklinkPdf',
            'active' => false,
            'module' => $this->name,
        ));

        return true;
    }

    /**
     * Initialisation to install / uninstall
     */
    private function installTabs()
    {
        
        $menu_id = 14;

        // Install All Tabs directly via controller's install function
        $path = $this->getLocalPath().'controllers'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR;
        $controllers = scandir($path);
        foreach ($controllers as $controller) {
            if ($controller != 'index.php' && !preg_match('#\.old#isD', $controller) && is_file($path.$controller)) {
                require_once $path.$controller;
                $controller_name = Tools::substr($controller, 0, -4);
                //Check if class_name is an existing Class or not
                if (class_exists($controller_name)) {
                    if (method_exists($controller_name, 'install')) {
                        if (!call_user_func(array($controller_name, 'install'), $menu_id, $this->name)) {
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }
    

    /**
     * Delete tab
     * @return  boolean if successfull
     */
    public function uninstallTabs()
    {
        return PlTotAdminTabHelper::deleteAdminTabs($this->name);
    }

    ############################################################################################################
    # SQL
    ############################################################################################################
    
    /**
     * Install DataBase table
     * @return boolean if install was successfull
     */
    private function installSQL()
    {
        // Install All Object Model SQL via install function
        $path = $this->getLocalPath().'classes'.DIRECTORY_SEPARATOR;
        $classes = scandir($path);
        foreach ($classes as $class) {
            if ($class != 'index.php' && !preg_match('#\.old#isD', $class) && is_file($path.$class)) {
                $class_name = Tools::substr($class, 0, -4);
                // Check if class_name is an existing Class or not
                if (class_exists($class_name)) {
                    if (method_exists($class_name, 'install')) {
                        if (!call_user_func(array($class_name, 'install'))) {
                            return false;
                        }
                    }
                }
            }
        }
        
        $sql = array();
        $sql[] = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."packlink_orders` (
              `id_order` INT(11) NOT NULL PRIMARY KEY,
              `draft_reference` VARCHAR(255) NOT NULL,
              `postcode` VARCHAR(21),
              `postalzone` INT(11),
              `details` VARCHAR(1500),
              `pdf` VARCHAR(1500)
        ) ENGINE = "._MYSQL_ENGINE_." ";

        $sql[] = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "packlink_wait_draft` (
              `id_order` INT(11) NOT NULL PRIMARY KEY,
              `date_add` DATE
        ) ENGINE = " . _MYSQL_ENGINE_ . " ";

        foreach ($sql as $q) {
            if (!DB::getInstance()->execute($q)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Uninstall DataBase table
     * @return boolean if install was successfull
     */
    private function uninstallSQL()
    {
        // Uninstall All Object Model SQL via install function
        $path = $this->getLocalPath().'classes'.DIRECTORY_SEPARATOR;
        $classes = scandir($path);
        foreach ($classes as $class) {
            if ($class != 'index.php' && !preg_match('#\.old#isD', $class) && is_file($path.$class)) {
                $class_name = Tools::substr($class, 0, -4);
                // Check if class_name is an existing Class or not
                if (class_exists($class_name)) {
                    if (method_exists($class_name, 'uninstall')) {
                        if (!call_user_func(array($class_name, 'uninstall'))) {
                            return false;
                        }
                    }
                }
            }
        }
        
        $sql = "DROP TABLE IF EXISTS `"._DB_PREFIX_."packlink_orders`";
        if (!DB::getInstance()->execute($sql)) {
            return false;
        }

        $sql_wait = "DROP TABLE IF EXISTS `"._DB_PREFIX_."packlink_wait_draft`";
        if (!DB::getInstance()->execute($sql_wait)) {
            return false;
        }
    
        return true;
    }


    ############################################################################################################
    # Hook
    ############################################################################################################

    /**
     * [registrationHook description]
     * @return [type] [description]
     */
    private function registrationHook()
    {
        // Example :
        if (!$this->registerHook('actionObjectOrderHistoryAddAfter')) {
            return false;
        }

        if (!$this->registerHook('actionObjectOrderUpdateAfter')) {
            return false;
        }

        if (!$this->registerHook('actionOrderStatusPostUpdate')) {
            return false;
        }

        if (!$this->registerHook('displayOrderDetail')) {
            return false;
        }

        if (!$this->registerHook('displayBackOfficeHeader')) {
            return false;
        }
        if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
            if (!$this->registerHook('displayAdminOrderContentShip')) {
                return false;
            }
            if (!$this->registerHook('displayAdminOrderTabShip')) {
                return false;
            }
        }

        if (!$this->registerHook('displayAdminOrder')) {
            return false;
        }

        if (!$this->registerHook('displayHeader')) {
            return false;
        }

        return true;
    }

    /*
    ** Hook update carrier
    **
    */

    ############################################################################################################
    # Administration
    ############################################################################################################

    /**
     * Admin display
     * @return String Display admin content
     */
    public function getContent()
    {

        // Suffix to link
        $suffixLink = '&configure='.$this->name.'&token='.Tools::getValue('token');
        $suffixLink .= '&tab_module='.$this->tab.'&module_name='.$this->name;
        $output = '';
        $link = new Link;
        if (Tools::getValue('PL_tab_name')) {
            $tab_name = Tools::getValue('PL_tab_name');
        } else {
            $tab_name = "home_settings";
        }

        $sdk = new PacklinkSDK(Configuration::get('PL_API_KEY'), $this);
        
        $default_language = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
        $language         = Tools::strtolower($default_language->iso_code);
     
        if ($language != "it" && $language != "es" && $language != "fr" && $language != "de") {
            $language = "es";
        }

        // Base
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $this->module_link = 'index.php?controller='.Tools::getValue('controller').$suffixLink;
        } else {
            $this->module_link = 'index.php?tab='.Tools::getValue('tab').$suffixLink;
        }


        if (Tools::getValue('submit-query')) {
            $PL_API_KEY = Tools::getValue('PL_API_KEY');
            $check_key = $this->callAnalitics($PL_API_KEY, "setup");
            $this->callbackEvents($PL_API_KEY);
            Configuration::updateValue('PL_API_KEY', $PL_API_KEY);
            $output .=$this->displayConfirmation($this->l('Settings updated successfully'));
        } else {
            $PL_API_KEY = Configuration::get('PL_API_KEY');
        }

        $warehouses = $sdk->getWarehouses();
        if (isset($warehouses->message)) {
            $show_address = false;
        } else {
            $show_address = true;
        }

        if ($language == "it") {
            $pl_aide = "https://support-pro.packlink.com/hc/it/sections/202755109-Prestashop";
        } elseif ($language == "de") {
            $pl_aide = "https://support-pro.packlink.com/hc/de/sections/202755109-Prestashop";
        } elseif ($language == "es" || $language == "en") {
            $pl_aide = "https://support-pro.packlink.com/hc/es-es/sections/202755109-Prestashop";
        } elseif ($language == "fr") {
            $pl_aide = "https://support-pro.packlink.com/hc/fr-fr/sections/202755109-Prestashop";
        } else {
            $pl_aide = "https://support-pro.packlink.com/hc/es-es/sections/202755109-Prestashop";
        }

        $carrier_link = $this->pl_url. '/prestashop?utm_source=partnerships&utm_content=link&utm_campaign=backoffice';

        $generate_api = $this->pl_url.'/private/settings/integrations/prestashop_module';

        $link_pro_addr = $this->pl_url.'/private/settings/warehouses';

        if (Tools::getValue('submit-conversion')) {
            $length = Tools::getValue('length');
            if (!$length) {
                $length = 1;
            }
            Configuration::updateValue('PL_API_CM', $length);
            $weight = Tools::getValue('weight');
            if (!$weight) {
                $weight = 1;
            }
            Configuration::updateValue('PL_API_KG', $weight);
            $output .=$this->displayConfirmation($this->l('Settings updated successfully'));
        } else {
            $length = Configuration::get('PL_API_CM');
            $weight = Configuration::get('PL_API_KG');
        }

        if (Tools::getValue('submit-create-pl')) {
            $packlink_createPl = Tools::getValue('createPl');
            Configuration::updateValue('PL_CREATE_DRAFT_AUTO', $packlink_createPl);
            if (Tools::getValue('createPl') == 1) {
                $this->callAnalitics($PL_API_KEY, "automatic_export_option");
            } else {
                $this->callAnalitics($PL_API_KEY, "manual_export_option");
            }
            $output .=$this->displayConfirmation($this->l('Settings updated successfully'));
        } else {
            $packlink_createPl = Configuration::get('PL_CREATE_DRAFT_AUTO');
        }

        if (Tools::getValue('submit-import')) {
            $packlink_import = Tools::getValue('import');
            Configuration::updateValue('PL_IMPORT', $packlink_import);
            $output .=$this->displayConfirmation($this->l('Settings updated successfully'));
        } else {
            $packlink_import = Configuration::get('PL_IMPORT');
        }

        $unit_weight = Configuration::get('PS_WEIGHT_UNIT');
        $unit_length = Configuration::get('PS_DIMENSION_UNIT');
        $link_units = $this->context->link->getAdminLink('AdminLocalization').'#PS_CURRENCY_DEFAULT';
        $link_status = $this->context->link->getAdminLink('AdminStatuses');

        $update_msg = '';
        if (Configuration::get('PL_API_VERSION') == '' || version_compare(Configuration::get('PL_API_VERSION'), $this->version, '<')) {
            $update_msg = $this->displayConfirmation($this->l('v1.1: All of your paid orders will now be imported automatically into Packlink PRO').'<br />'.$this->l('v1.2: Sent content(s) will be filled automatically for your Packlink PRO shipments').'<br />'.$this->l('v1.3: Shipment details and tracking number automatically imported into PrestaShop orders. Auto-population of missing product data in catalog (weight/dimensions)').'<br />'.$this->l('v1.4: Synchronization of Packlink PRO shipping statuses with PrestaShop order statuses to keep your orders up-to-date').'<br />'.$this->l('v1.5: Configuration page redesign. Default "ship from" address management from Packlink PRO settings').'<br />'.$this->l('v1.6: New actions buttons which indicates the next action required for each order status. Option to choose between automatic or manual shipment creation.'));
            Configuration::updateValue('PL_API_VERSION', $this->version);
        }

        $default_lang = $this->context->language->id;
        $order_state  = OrderState::getOrderStates($default_lang);

        if (Tools::getValue('submit-status')) {
            $status_awaiting = Tools::getValue('select_awaiting');
            Configuration::updateValue('PL_ST_AWAITING', $status_awaiting);
            $status_pending = Tools::getValue('select_pending');
            Configuration::updateValue('PL_ST_PENDING', $status_pending);
            $status_ready = Tools::getValue('select_ready');
            Configuration::updateValue('PL_ST_READY', $status_ready);
            $status_transit = Tools::getValue('select_transit');
            Configuration::updateValue('PL_ST_TRANSIT', $status_transit);
            $status_delivered = Tools::getValue('select_delivered');
            Configuration::updateValue('PL_ST_DELIVERED', $status_delivered);
            $output .=$this->displayConfirmation($this->l('Settings updated successfully'));
        } else {
            $status_awaiting = Configuration::get('PL_ST_AWAITING');
            $status_pending = Configuration::get('PL_ST_PENDING');
            $status_ready = Configuration::get('PL_ST_READY');
            $status_transit = Configuration::get('PL_ST_TRANSIT');
            $status_delivered = Configuration::get('PL_ST_DELIVERED');
        }

        $this->context->smarty->assign(array(
            'PL_API_KEY' => $PL_API_KEY,
            'carrier_link' => $carrier_link,
            'generate_api' => $generate_api,
            'module_link' => $this->module_link,
            'language' => $language,
            'link' => $link->getAdminLink('AdminPackLink', true).'&ajax=true&action=GetPostCode',
            'weight' => $weight,
            'length' => $length,
            'unit_weight' => $unit_weight,
            'unit_length' => $unit_length,
            'packlink_import' => $packlink_import,
            'simple_link' => $this->_path,
            'update_msg' => $update_msg,
            'order_state' => $order_state,
            'status_awaiting' => $status_awaiting,
            'status_pending' => $status_pending,
            'status_ready' => $status_ready,
            'status_transit' => $status_transit,
            'status_delivered' => $status_delivered,
            'tab_name' => $tab_name,
            'link_units' => $link_units,
            'link_status' => $link_status,
            'link_pro_addr' => $link_pro_addr,
            'warehouses' => $warehouses,
            'show_address'      => $show_address,
            'pl_aide'           => $pl_aide,
            'packlink_createPl' => $packlink_createPl,
        ));
        $this->postProcess();

        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $this->context->controller->addCSS($this->_path.'views/css/bootstrap.min.css', 'all');
            $this->context->controller->addCSS($this->_path.'views/css/style15.css', 'all');
            $this->context->controller->addJS($this->_path.'views/js/bootstrap.min.js', 'all');
            $this->context->controller->addJS(_PS_JS_DIR_.'/jquery/plugins/autocomplete/jquery.autocomplete.js', 'all');
        } else {
            $this->context->controller->addCSS($this->_path.'views/css/style16.css', 'all');
        }
        
        return $update_msg.$output.$this->display(__FILE__, 'back.tpl');
        
    }

    public function callAnalitics($api_key, $event)
    {
        # TODO : call to packlink
        $body = array(
                    'ecommerce' => 'prestashop',
                    'ecommerce_version' => _PS_VERSION_,
                    'event' => $event
                );
        $sdk = new PacklinkSDK($api_key, $this);
        $datas = $sdk->createAnalitics($body, $api_key);

        return $datas;

    }


    public function callbackEvents($api_key)
    {
        # TODO : call to packlink
        if (isset($_SERVER['HTTPS'])) {
            $url_shop = Tools::getShopDomainSsl(true);
        } else {
            $url_shop = Tools::getShopDomain(true);
        }

        $body = array(
                    'url' => $url_shop.'/modules/packlink/status.php'
                );
        $sdk = new PacklinkSDK($api_key, $this);
        $datas = $sdk->createCallback($body, $api_key);

        return $datas;

    }
   
    public function getCartAddressDelivery($id_address_delivery)
    {

        $sql = 'SELECT * FROM '._DB_PREFIX_.'address WHERE id_address = '.(int)$id_address_delivery;
        $id_address_delivery = Db::getInstance()->executeS($sql);

        return $id_address_delivery;

    }

    public function getCartCountryDelivery($country)
    {

        $sql = 'SELECT iso_code FROM '._DB_PREFIX_.'country WHERE id_country = '.(int)$country;
        $country = Db::getInstance()->getValue($sql);

        return $country;

    }

    public function getCartStateDelivery($state)
    {

        $sql = 'SELECT name FROM '._DB_PREFIX_.'state WHERE id_state = '.(int)$state;
        $state = Db::getInstance()->getValue($sql);

        return $state;

    }

    public function getEmailDelivery($customer_id)
    {

        $sql = 'SELECT email FROM '._DB_PREFIX_.'customer WHERE id_customer = '.(int)$customer_id;
        $customer_email = Db::getInstance()->getValue($sql);

        return $customer_email;

    }

    public function getCartProductCat($id_category)
    {

        $sql = 'SELECT name FROM '._DB_PREFIX_.'category_lang WHERE id_category = '.(int)$id_category;
        $id_category_default = Db::getInstance()->getValue($sql);

        return $id_category_default;

    }

    public function convertToDistance($distance)
    {
        $distance = $distance / Configuration::get('PL_API_KG');
        return $distance;
    }

    public function convertToWeight($weight)
    {
        $weight = $weight / Configuration::get('PL_API_CM');
        return $weight;
    }

    

    public function hookdisplayBackOfficeHeader($params)
    {
        if (Tools::strtolower(Tools::getValue('controller')) == "adminorders" && Tools::getValue('id_order')) {
            $this->execCreatePlShipment(Tools::getValue('id_order'));
            $this->createPacklinkDetails(Tools::getValue('id_order'));
        }
    }

    public function hookDisplayHeader($params)
    {

        if (Tools::getValue('controller') == "orderconfirmation" && Tools::getValue('id_order')) {
            $this->execCreatePlShipment();
            $this->createPacklinkDetails(Tools::getValue('id_order'));
        }

    }

    public function hookdisplayAdminOrder($params)
    {
        if (!$this->pl_hook) {
            $this->pl_hook = true;
        } else {
            return '';
        }
        $id_order = $params['id_order'];
        $pl_order = new PLOrder($id_order);

        $order_ps = new Order($id_order);
        $expedition_pl = '';

        $pl_shippement = '';

        if (Tools::getValue('create_pl_draft') == 1) {
            if (Configuration::get('PL_API_KEY')) {
                $this->createPlShippement($id_order);
                Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminOrders')."&id_order=".$id_order."&vieworder");
            } else {
                $this->context->controller->warnings[] = sprintf($this->l('No api key found'));
            }
        }

        if (!$pl_order->id_order) {
            $this->context->smarty->assign(array(
                'simple_link' => $this->_path,
                'reference' => " ",
                'suivi' => $this->l('Create'),
                'iconBtn' => "icon-plus-sign",
                'link_suivi' => Context::getContext()->link->getAdminLink('AdminOrders')."&id_order=".$id_order."&vieworder&create_pl_draft=1",
                'img15' => 'views/img/add.gif',
                'target' => '_self'
            ));

            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $expedition_pl = $this->display(__FILE__, 'expedition15.tpl');
            } else {
                $expedition_pl = $this->display(__FILE__, 'expedition.tpl');
            }

            return $expedition_pl;
        }

        if ($pl_order->details && $pl_order->details != '') {

            $details = Tools::jsonDecode($pl_order->details);
            $sdk = new PacklinkSDK(Configuration::get('PL_API_KEY'), $this);

            
            $this->context->smarty->assign(array(
                'simple_link' => $this->_path,
                'reference' => $this->l('Shipping reference: ').$pl_order->draft_reference
            ));
            
            if ($details->state == "AWAITING_COMPLETION" || $details->state == "READY_TO_PURCHASE") {
                $this->context->smarty->assign(array(
                    'suivi' => $this->l('Send'),
                    'iconBtn' => "icon-truck",
                    'link_suivi' => $details->tracking_url,
                    'img15' => 'views/img/delivery.gif',
                    'target' => '_blank'
                ));
            } else if ($details->state == "READY_TO_PRINT" || $details->state == "READY_FOR_COLLECTION") {
                $pdf_url = $pl_order->pdf;
                if (!$pdf_url || $pdf_url == '') {
                    $url = $sdk->getPdfLabels($pl_order->draft_reference);
                    $pdf_url = $url['0'];
                }
      
                $this->context->smarty->assign(array(
                    'suivi' => $this->l('Print'),
                    'iconBtn' => "icon-print",
                    'link_suivi' => $pdf_url,
                    'img15' => 'views/img/printer.gif',
                    'target' => '_self'
                ));
            } else if ($details->state == "IN_TRANSIT" || $details->state == "DELIVERED" || $details->state == 'PURCHASE_SUCCESS' || $details->state == "CARRIER_PENDING" || $details->state == 'CARRIER_OK' || $details->state == 'CARRIER_KO' || $details->state == 'CANCELED') {
                $this->context->smarty->assign(array(
                    'suivi' => $this->l('View'),
                    'iconBtn' => "icon-search",
                    'link_suivi' => $details->tracking_url,
                    'img15' => 'views/img/search.gif',
                    'target' => '_blank'
                ));
            }
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $expedition_pl = $this->display(__FILE__, 'expedition15.tpl');
            } else {
                $expedition_pl = $this->display(__FILE__, 'expedition.tpl');
            }
            

            if ($details->state == "AWAITING_COMPLETION" || $details->state == "READY_TO_PURCHASE") {
                return $expedition_pl;
            }
       
            $location = '';
            if ($details->dropoff_point_id) {
                $location = $details->location;
            }

            $sdk = new PacklinkSDK(Configuration::get('PL_API_KEY'), $this);

            if ($details->state == "READY_TO_PRINT" || $details->state == "READY_FOR_COLLECTION") {
                $button = $this->l('Print');
                $target = "_self";
                $img_pl = "printer.gif";
                $icon = "icon-print";
                $pdf_url = $pl_order->pdf;
                if (!$pdf_url || $pdf_url == '') {
                    $url = $sdk->getPdfLabels($pl_order->draft_reference);
                    $pdf_url = $url['0'];
                }
                $href = $pdf_url;
            } else {
                $button = $this->l('View');
                $target = "_blank";
                $img_pl = "search.gif";
                $icon = "icon-search";
                $href = $details->tracking_url;
            }
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $this->context->controller->addCSS($this->_path.'views/css/style15.css', 'all');
            } else {
                $this->context->controller->addCSS($this->_path.'views/css/style16.css', 'all');
            }
            $svg_icon = '<svg  class="ic_assignement" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="16" height="16" viewBox="0 0 32 32" style="&#10;">
                <path d="M25.333 4h-5.573c-0.56-1.547-2.027-2.667-3.76-2.667s-3.2 1.12-3.76 2.667h-5.573c-1.467 0-2.667 1.2-2.667 2.667v18.667c0 1.467 1.2 2.667 2.667 2.667h18.667c1.467 0 2.667-1.2 2.667-2.667v-18.667c0-1.467-1.2-2.667-2.667-2.667zM16 4c0.733 0 1.333 0.6 1.333 1.333s-0.6 1.333-1.333 1.333-1.333-0.6-1.333-1.333 0.6-1.333 1.333-1.333zM18.667 22.667h-9.333v-2.667h9.333v2.667zM22.667 17.333h-13.333v-2.667h13.333v2.667zM22.667 12h-13.333v-2.667h13.333v2.667z" style="&#10;"/>
                </svg>';
            if (version_compare(_PS_VERSION_, '1.6.1', '<') && version_compare(_PS_VERSION_, '1.6', '>=')) {
                $pl_shippement .= '<li class="active" id="packlink_tab"><a href="#packlink">'.$svg_icon.$this->l('Tracking with Packlink PRO').'</a></li>
                            <script type="text/javascript">$(function () {
                                $("#packlink_tab").prependTo("#myTab");
                                });
                                $(function () {
                                          $("#packlink_tab").parents("ul").find("li").not("#packlink_tab").removeClass("active");
                                          $("#packlink").parent("div").find(".tab-pane").not("#packlink").removeClass("active");
                                          $("#expeditionPl > div").addClass("active");
                                });

                                </script>';

                $pl_shippement .= '<script type="text/javascript">$(function () {
                                        $("#packlink").insertBefore("#shipping");
                                        });
                                        $("#packlink_tab").click(function() {
                                          $("#packlink_tab").addClass("active");
                                          $("#packlink_tab").parents("ul").find("li").not("#packlink_tab").removeClass("active");
                                          $("#packlink").addClass("active");
                                          $("#packlink").parent("div").find(".tab-pane").not("#packlink").removeClass("active");
                                          $("html,body").animate({
                                               scrollTop: $("#packlink_tab").offset().top - 100
                                           });
                                        });
                                        </script>
                                        <div class="tab-pane active" id="packlink"><div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th><span class="title_box ">'.$this->l('Date').'</span></th>
                                                    <th><span class="title_box ">'.$this->l('Carrier').'</span></th>
                                                    <th><span class="title_box ">'.$this->l('Weight').'</span></th>
                                                    <th><span class="title_box ">'.$this->l('Shipping costs').'</span></th>
                                                    <th><span class="title_box ">'.$this->l('Tracking number').'</span></th>
                                                    <th><span class="title_box "></span></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>'.$details->date.'</td>
                                                    <td>'.$details->carrier.'</br>'.$details->service.'</td>
                                                    <td>'.$details->weight.' '.Configuration::get('PS_WEIGHT_UNIT').'</td>
                                                    <td>'.$details->cost.' '.$this->l('EUR').'</td><td>
                                                    <a style="color:#666;" href="'.$details->tracking_url_fo.'">';
                if ($details->tracking) {
                    foreach ($details->tracking as $key => $value) {
                        $pl_shippement .=  ''.$value.'<br />';
                    }
                }
                    
                    $pl_shippement .= '</a></td><td style="text-align:right;"><a class="btn btn-default" target="'.$target.'" href="'.$href.'"><i class="'.$icon.'"></i>'.$button.'</a></td>                                            
                                                </tr>';
                if ($location != '') {
                    $pl_shippement .= '<tr><td>'.$location->company.'</br>
                                    '.$location->street1.'</br>'.$location->street2.'</br>
                                    '.$location->zip_code.' '.$location->city.'</td></tr>';
                }
                    
                    $pl_shippement .= ' </tbody>
                                        </table>
                                    </div></div>
                                    ';
            } else if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $pl_shippement .= '<fieldset style="margin-top: 12px;">
                                   <legend>'.$svg_icon.$this->l('Tracking with Packlink PRO').'</legend>';
            
                $pl_shippement .= '<table class="table" width="100%" cellspacing="0" cellpadding="0">
                                        <thead>
                                            <tr>
                                                <th><span class="title_box ">'.$this->l('Date').'</span></th>
                                                <th><span class="title_box ">'.$this->l('Carrier').'</span></th>
                                                <th><span class="title_box ">'.$this->l('Weight').'</span></th>
                                                <th><span class="title_box ">'.$this->l('Shipping costs').'</span></th>
                                                <th><span class="title_box ">'.$this->l('Tracking number').'</span></th>
                                                <th><span class="title_box "></span></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>'.$details->date.'</td>
                                                <td>'.$details->carrier.'</br>'.$details->service.'</td>
                                                <td>'.$details->weight.' '.Configuration::get('PS_WEIGHT_UNIT').'</td>
                                                    <td>'.$details->cost.' '.$this->l('EUR').'</td><td>
                                                    <a style="color:#666;" href="'.$details->tracking_url_fo.'">';
                if ($details->tracking) {
                    foreach ($details->tracking as $key => $value) {
                        $pl_shippement .=  ''.$value.'<br />';
                    }
                }
                    
                $pl_shippement .= '</a></td><td style="text-align:right;"><a class="button" target="'.$target.'" href="'.$href.'"><img src="'.$this->_path.'views/img/'.$img_pl.'">'.$button.'</a></td></tr>';

                if ($location != '') {
                    $pl_shippement .= '<tr><td>'.$location->company.'</br>
                                    '.$location->street1.'</br>'.$location->street2.'</br>
                                    '.$location->zip_code.' '.$location->city.'</td></tr>';
                }
                
                $pl_shippement .= ' </tbody>
                                    </table>';
                $pl_shippement .= '</fieldset>';
            }

        }
        return $pl_shippement.$expedition_pl;

    }

    public function hookdisplayAdminOrderTabShip($params)
    {
        $id_order = $params['order']->id;
        $pl_order = new PLOrder($id_order);
        if ($pl_order->details && $pl_order->details != '') {
            $details = Tools::jsonDecode($pl_order->details);
            if ($details->state == "AWAITING_COMPLETION" || $details->state == "READY_TO_PURCHASE") {
                return '';
            }
            if (version_compare(_PS_VERSION_, '1.6', '<')) {
                $this->context->controller->addCSS($this->_path.'views/css/style15.css', 'all');
            } else {
                $this->context->controller->addCSS($this->_path.'views/css/style16.css', 'all');
            }
            return '<li class="active" id="packlink_tab"><a style="padding-top:13px;" href="#packlink"><svg  class="ic_assignement" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="16" height="16" viewBox="0 0 32 32" style="&#10;">
                <path d="M25.333 4h-5.573c-0.56-1.547-2.027-2.667-3.76-2.667s-3.2 1.12-3.76 2.667h-5.573c-1.467 0-2.667 1.2-2.667 2.667v18.667c0 1.467 1.2 2.667 2.667 2.667h18.667c1.467 0 2.667-1.2 2.667-2.667v-18.667c0-1.467-1.2-2.667-2.667-2.667zM16 4c0.733 0 1.333 0.6 1.333 1.333s-0.6 1.333-1.333 1.333-1.333-0.6-1.333-1.333 0.6-1.333 1.333-1.333zM18.667 22.667h-9.333v-2.667h9.333v2.667zM22.667 17.333h-13.333v-2.667h13.333v2.667zM22.667 12h-13.333v-2.667h13.333v2.667z" style="&#10;"/>
                </svg>'.$this->l('Tracking with Packlink PRO').'</a></li>';
        }
    }

    public function createPacklinkDetails($id_order)
    {
        $pl_order = new PLOrder($id_order);
        if ($pl_order->id_order != '' && $pl_order->draft_reference != '') {
            $reference = $pl_order->draft_reference;
            $sdk = new PacklinkSDK(Configuration::get('PL_API_KEY'), $this);
            $datas = $sdk->getShippementDetails($reference);
            $items = '';
            $this->logs[] = '======================================================================================================';
            $this->logs[] = '['.strftime('%Y-%m-%d %H:%M:%S').'] : Shippement state from Packlink Pro : '.$datas->state;

            $default_language = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
            $language = Tools::strtolower($default_language->iso_code);
            if ($language != "it" && $language != "es" && $language != "fr" && $language != "de") {
                $language = "es";
            }

            if ($datas->state) {
                $track = '/'.$reference;
                if (($datas->state == "READY_TO_PRINT" || $datas->state == "READY_FOR_COLLECTION") && !$pl_order->pdf) {
                    $pdf = $sdk->getPdfLabels($pl_order->draft_reference);
                    $pl_order->pdf = $pdf[0];
                }
                $pl_order->details = Tools::jsonEncode(array(
                    'date' => $datas->order_date,
                    'carrier' => $datas->carrier,
                    'service' => $datas->service,
                    'weight' => $datas->packages[0]->weight,
                    'cost' => $datas->price->total_price,
                    'tracking' => $datas->trackings,
                    'tracking_url' => $this->pl_url.'/private/shipments'.$track,
                    'location' => $datas->to,
                    'tracking_url_fo' => $datas->tracking_url,
                    'dropoff_point_id' => $datas->dropoff_point_id,
                    'state' => $datas->state
                ));
                $this->logs[] = '['.strftime('%Y-%m-%d %H:%M:%S').'] : Data from Packlink Pro : '.$pl_order->details;
            
                $pl_order->save();
                if (isset($datas->additional_data->items)) {
                    $items = $datas->additional_data->items;
                }
                $import = Configuration::get('PL_IMPORT');
                if ($import == 1 && count($items) == 1 && $items[0]->quantity == 1) {
                    $order = new Order($id_order);

                    $products = $order->getProducts();

                    if (isset($products) && !empty($products)) {
                        foreach ($products as $key => $value) {
                            $product = new Product($products[$key]['product_id']);
                        }

                        if (!empty($product->link_rewrite)) {
                            if ($product->width == 0) {
                                $product->width = ($datas->packages[0]->width * Configuration::get('PL_API_CM'));
                            }
                            if ($product->depth == 0) {
                                $product->depth = ($datas->packages[0]->length * Configuration::get('PL_API_CM'));
                            }
                            if ($product->weight == 0) {
                                $product->weight = ($datas->packages[0]->weight * Configuration::get('PL_API_KG'));
                            }
                            if ($product->height == 0) {
                                $product->height = ($datas->packages[0]->height * Configuration::get('PL_API_CM'));
                            }
                            $product->save();
                        }
                    }
                }
            }
        }
        if ($this->debug) {
            $this->writeLog();
        }
    }

    public function hookdisplayOrderDetail($params)
    {
        $id_order = $params['order']->id;
        $this->createPacklinkDetails($id_order);

        $order = new Order($id_order);
        $carrier = $order->getShipping();
        if (version_compare(_PS_VERSION_, '1.6', '<')) {
            $version = 0;
        } else {
            $version = 1;
        }

        $pl_order = new PLOrder($id_order);
        if ($pl_order->details && $pl_order->details != '') {
            $details = Tools::jsonDecode($pl_order->details);
            if ($details->carrier) {
                $temp = explode('/', $details->date);
                $date_pl = $temp[2].'/'.$temp[1].'/'.$temp[0];

                $this->context->smarty->assign(array(
                    'details' => $details,
                    'date' => $date_pl,
                    'carrierPL' => $details->carrier,
                    'weight' => $details->weight,
                    'trackings' => $details->tracking,
                    'tracking_url' => $details->tracking_url_fo,
                    'version' => $version,
                    'carr_pl_name' => $this->l('Carrier')
                ));
                
                return $this->display(__FILE__, 'order_details.tpl');
            }
        }
    }

    public function hookdisplayAdminOrderContentShip($params)
    {
        $id_order = $params['order']->id;
        $pl_order = new PLOrder($id_order);
        $sdk = new PacklinkSDK(Configuration::get('PL_API_KEY'), $this);
        

        $pl_shippement = '';
        if ($pl_order->details && $pl_order->details != '') {
            $details = Tools::jsonDecode($pl_order->details);
            if ($details->state == "AWAITING_COMPLETION" || $details->state == "READY_TO_PURCHASE") {
                return $pl_shippement;
            }
            $location = '';
            if ($details->dropoff_point_id) {
                $location = $details->location;
            }
            $pl_shippement .= '<script type="text/javascript">
                                    $(function () {
                                          $("#packlink_tab").parents("ul").find("li").not("#packlink_tab").removeClass("active");
                                          $("#packlink").parent("div").find(".tab-pane").not("#packlink").removeClass("active");
                                    });
                                </script>
                               <div class="tab-pane active" id="packlink"><div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><span class="title_box ">'.$this->l('Date').'</span></th>
                                            <th><span class="title_box ">'.$this->l('Carrier').'</span></th>
                                            <th><span class="title_box ">'.$this->l('Weight').'</span></th>
                                            <th><span class="title_box ">'.$this->l('Shipping costs').'</span></th>
                                            <th><span class="title_box "></span>'.$this->l('Tracking number').'</th>
                                            <th><span class="title_box "></span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>'.$details->date.'</td>
                                            <td>'.$details->carrier.'</br>'.$details->service.'</td>
                                            <td>'.$details->weight.' '.Configuration::get('PS_WEIGHT_UNIT').'</td>
                                                    <td>'.$details->cost.' '.$this->l('EUR').'</td><td>
                                                    <a style="color:#666;" href="'.$details->tracking_url_fo.'">';
            if ($details->tracking) {
                foreach ($details->tracking as $key => $value) {
                    $pl_shippement .=  ''.$value.'<br />';
                }
            }

            if ($details->state == "READY_TO_PRINT" || $details->state == "READY_FOR_COLLECTION") {
                $button = $this->l('Print');
                $target = "_self";
                $icon = "icon-print";
                $pdf_url = $pl_order->pdf;
                if (!$pdf_url || $pdf_url == '') {
                    $url = $sdk->getPdfLabels($pl_order->draft_reference);
                    $pdf_url = $url['0'];
                }
                $href = $pdf_url;
            } else {
                $button = $this->l('View');
                $target = "_blank";
                $icon = "icon-search";
                $href = $details->tracking_url;
            }
            
                $pl_shippement .= '</a></td><td style="text-align:right;"><a target="'.$target.'" class="btn btn-default" href="'.$href.'"><i class="'.$icon.'"></i>  '.$button.'</a></td>                                            
                                                </tr>';
            if ($location != '') {
                $pl_shippement .= '<tr><td>'.$location->company.'</br>
                                '.$location->street1.'</br>'.$location->street2.'</br>
                                '.$location->zip_code.' '.$location->city.'</td></tr>';
            }
            
            $pl_shippement .= ' </tbody>
                                </table>
                            </div></div>';
        }

        return $pl_shippement;
    }

    public function hookactionOrderStatusPostUpdate($params)
    {
        if ($params['newOrderStatus']->id != _PS_OS_WS_PAYMENT_ && $params['newOrderStatus']->id != _PS_OS_PAYMENT_) {
            return false;
        }

        Db::getInstance()->insert(
            'packlink_wait_draft',
            array(
                'id_order' => $params['id_order'],
                'date_add' => date('Y-m-d H:i:s'),
            )
        );
    }

    private function execCreatePlShipment($id_order = null)
    {
        $query = new DbQuery();
        $query->from('packlink_wait_draft');
        $query->where('id_order NOT IN (SELECT id_order FROM '._DB_PREFIX_.'packlink_orders) ');

        $orders = Db::getInstance()->executeS($query);

        $refresh = false;
        if (Configuration::get('PL_CREATE_DRAFT_AUTO')) {
            if ($orders && count($orders)) {
                foreach ($orders as $order) {
                    
                    $this->createPlShippement($order['id_order']);
                    
                    if ($order['id_order'] == $id_order) {
                        $refresh = true;
                    }
                }
            }
        }
        if ($refresh) {
            Tools::redirectAdmin(Context::getContext()->link->getAdminLink('AdminOrders') . "&id_order=" . $id_order . "&vieworder");
        }

    }

    public function createPlShippement($id_order_params)
    {
        $order = new Order((int) $id_order_params);
        
        $address_delivery = $this->getCartAddressDelivery($order->id_address_delivery);
        $country_delivery = $this->getCartCountryDelivery($address_delivery[0]['id_country']);
        $state_delivery = $this->getCartStateDelivery($address_delivery[0]['id_state']);
        if (!$state_delivery) {
            $state_delivery = '';
        }
        if ($address_delivery[0]['phone_mobile'] || $address_delivery[0]['phone_mobile'] != '') {
            $phone_delivery = $address_delivery[0]['phone_mobile'];
        } else {
            $phone_delivery = $address_delivery[0]['phone'];
        }
        $email_delivery = $this->getEmailDelivery($address_delivery[0]['id_customer']);

        $cart_products = array();
        $cart_products = $order->getProducts();


        $sdk = new PacklinkSDK(Configuration::get('PL_API_KEY'), $this);
        $datas_client = $sdk->getCitiesByPostCode($address_delivery[0]['postcode'], $country_delivery);
        if (isset($datas_client->message)) {
            return false;
        }

        $postal_zone_id_to = $datas_client[0]->postalZone->id;
        $zip_code_id_to = $datas_client[0]->id;

        if (count($datas_client) > 1) {
            foreach ($datas_client as $key => $value) {
                $city = Tools::strtolower($address_delivery[0]['city']);
                $arr = array("-", "/", ",", "_");
                $city_formated = str_replace($arr, " ", $city);
                $city_formated_pl = Tools::strtolower(str_replace($arr, " ", $value->city->name));

                if ($city_formated_pl == $city_formated) {
                    $postal_zone_id_to = $value->postalZone->id;
                    $zip_code_id_to = $value->id;
                }
            }
        }

        

        $shipments_datas =
             array(
                'to' => array(
                     'name' => $address_delivery[0]['firstname'],
                     'surname' => $address_delivery[0]['lastname'],
                     'company' => $address_delivery[0]['company'],
                     'street1' => $address_delivery[0]['address1'],
                     'street2' => $address_delivery[0]['address2'],
                     'zip_code' => $address_delivery[0]['postcode'],
                     'city' => $address_delivery[0]['city'],
                     'country' => $country_delivery,
                     'state' => $state_delivery,
                     'phone' => $phone_delivery,
                     'email' => $email_delivery
                ),
                'additional_data' => array(
                     'postal_zone_id_to' => $postal_zone_id_to,
                     'zip_code_id_to' => $zip_code_id_to,
                ),
                'contentvalue' => $order->total_products_wt,
                'source' => 'module_prestashop',
        );

        if (count($cart_products) > 0) {
            if (count($cart_products) == 1) {
                foreach ($cart_products as $key => $value) {
                    if ($cart_products[$key]['product_quantity'] == 1) {
                        $weight = $this->convertToWeight($cart_products[$key]['weight']);
                        $width = $this->convertToDistance($cart_products[$key]['width']);
                        $height = $this->convertToDistance($cart_products[$key]['height']);
                        $depth = $this->convertToDistance($cart_products[$key]['depth']);

                        $packages = array(
                             'weight' => $weight,
                             'length' => $depth,
                             'width' => $width,
                             'height' => $height
                        );
                    } else {
                        $packages = array(
                             'weight' => 0,
                             'length' => 0,
                             'width' => 0,
                             'height' => 0
                        );
                    }
                    
                }
            } else {
                $packages = array(
                     'weight' => 0,
                     'length' => 0,
                     'width' => 0,
                     'height' => 0
                );
            }
            $shipments_datas['packages'][] = $packages;
            $cmpt = 0;
            foreach ($cart_products as $key => $value) {

                $category = $this->getCartProductCat($value['id_category_default']);

                $product_link = $this->context->link->getProductLink($value['product_id']);
                $product = new Product($value['product_id'], false, Context::getContext()->language->id);

                $image = Image::getImages(Context::getContext()->language->id, $value['product_id'], $value['product_attribute_id']);
                if (empty($image)) {
                    $image = Image::getCover($value['product_id']);
                    $product_img_link = $this->context->link->getImageLink($product->link_rewrite, $image['id_image']);
                } else {
                    $product_img_link = $this->context->link->getImageLink($product->link_rewrite, $image[0]['id_image']);
                }

                $weight = $this->convertToWeight($value['weight']);
                $width = $this->convertToDistance($value['width']);
                $height = $this->convertToDistance($value['height']);
                $depth = $this->convertToDistance($value['depth']);
                
                $value['cart_quantity'] = $value['product_quantity'];

                $items =
                    array(
                        'quantity' => $value['cart_quantity'],
                        'category_name'  => $category,
                        'picture_url' => $product_img_link,
                        'item_id' => $value['product_id'],
                        'price' => (float) $value['product_price_wt'],
                        'item_url' => $product_link,
                        'title' => $value['product_name']
                );

                $shipments_datas['additional_data']['items'][] = $items;

                for ($i = 1; $i <= $value['cart_quantity']; $i++) {
                    $packages = array(
                         'weight' => $weight,
                         'length' => $depth,
                         'width' => $width,
                         'height' => $height
                    );
                    $shipments_datas['additional_data']['items'][$cmpt]['package'][] = $packages;
                    
                }
                $cmpt++;
            }
        }

        $name_list = '';
        foreach ($cart_products as $key => $value) {
            $product_info = new Product($cart_products[$key]['product_id']);
            $name_list .= $cart_products[$key]['product_quantity'].' '.$product_info->name[$this->context->language->id]."; ";
        }

        $shipments_datas['content'] = $name_list;


        $this->logs[] = '======================================================================================================';

        $this->logs[] = '['.strftime('%Y-%m-%d %H:%M:%S').'] : Id order : '.$id_order_params;
        $this->logs[] = '['.strftime('%Y-%m-%d %H:%M:%S').'] : Id cart : '.$order->id_cart;
        $this->logs[] = '['.strftime('%Y-%m-%d %H:%M:%S').'] : Id carrier : '.$order->id_carrier;

        foreach ($shipments_datas as $key => $data) {
            $this->logs[] = '['.strftime('%Y-%m-%d %H:%M:%S').'] : Shippement datas : '.$key;
            if (is_array($data)) {
                foreach ($data as $key => $value1) {
                    if (is_array($value1)) {
                        foreach ($value1 as $key => $value2) {
                            if (is_array($value2)) {
                                foreach ($value2 as $key => $value3) {
                                    if (is_array($value3)) {
                                        foreach ($value3 as $key => $value4) {
                                            if (!is_array($value4)) {
                                                $this->logs[] = '['.strftime('%Y-%m-%d %H:%M:%S').'] : Items datas : '.$key.' => '.$value4;
                                            }
                                        }
                                    } else {
                                        $this->logs[] = '['.strftime('%Y-%m-%d %H:%M:%S').'] : Items datas : '.$key.' => '.$value3;
                                    }
                                }
                            } else {
                                $this->logs[] = '['.strftime('%Y-%m-%d %H:%M:%S').'] : Shippement datas : '.$key.' => '.$value2;
                            }
                        }
                    } else {
                        $this->logs[] = '['.strftime('%Y-%m-%d %H:%M:%S').'] : Shippement datas : '.$key.' => '.$value1;
                    }
                }
            }
        }
        
        $this->logs[] = '======================================================================================================';

        if ($this->debug) {
            $this->writeLog();
        }



        $_packlink_orders_old = new PLOrder($order->id);

        if ($_packlink_orders_old->id == '') {

            $pl_reference = $this->callSDK($shipments_datas);

            if ($pl_reference->reference != '') {
                $_packlink_orders = new PLOrder();
                $_packlink_orders->id_order = $order->id;
                $_packlink_orders->draft_reference = $pl_reference->reference;
                $_packlink_orders->postcode = $zip_code_id_to;
                $_packlink_orders->postalzone = $postal_zone_id_to;
                $_packlink_orders->details = '';
                $_packlink_orders->save();

                if (Configuration::get('PL_ST_AWAITING') && Configuration::get('PL_ST_AWAITING') != 0) {
                    $new_state = Configuration::get('PL_ST_AWAITING');
                    $statuses_used = array();
                    $order_statuses = Db::getInstance()->executeS('
                        SELECT `id_order_state`
                        FROM `'._DB_PREFIX_.'order_history`
                        WHERE `id_order` = '.(int)$order->id);
                    foreach ($order_statuses as $key => $order_statuse) {
                        $statuses_used[] = $order_statuse['id_order_state'];
                    }
                    if (!in_array($new_state, $statuses_used)) {
                        $order->setCurrentState($new_state);
                    }
                }

                $this->createPacklinkDetails($order->id);
            }
        }
    }

    public function callSDK($shipments_datas)
    {
        # TODO : call to packlink
        $sdk = new PacklinkSDK(Configuration::get('PL_API_KEY'), $this);
        $datas = $sdk->createDraft($shipments_datas);

        return $datas;

    }

    final public function getLogs()
    {
        return $this->logs;
    }

    final public function writeLog()
    {
        if (!$this->debug) {
            return false;
        }

        $handle = fopen(dirname(__FILE__).'/log_order.txt', 'a+');

        foreach ($this->getLogs() as $value) {
            fwrite($handle, $value."\r");
        }

        $this->logs = array();

        fclose($handle);
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
        return $shipping_cost;
    }

    public function getOrderShippingCostExternal($params)
    {
        return $this->getOrderShippingCost($params);
    }

    /**
     * Processing post in BO
     */
    public function postProcess()
    {
    }
}
