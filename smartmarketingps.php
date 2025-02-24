<?php
/**
 * Smart Marketing
 *
 *  @author    E-goi
 *  @copyright 2018 E-goi
 *  @license   LICENSE.txt
 *  @package SmartMarketingPs
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class SmartMarketingPs extends Module
{

    const PLUGIN_KEY = 'b2d226e839b116c38f53204205c8410c';

    const ACTION_CRON_TIME_CONFIGURATION = 'egoi_action_cron_time';
    const ADDRESS_CRON_TIME_CONFIGURATION = 'egoi_address_cron_time';
    const SMS_NOTIFICATIONS_SENDER_CONFIGURATION = 'sms_notifications_sender';
    const SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION = 'sms_notifications_sender_v3';
    const SMS_NOTIFICATIONS_ADMINISTRATOR_CONFIGURATION = 'sms_notifications_administrator';
    const SMS_NOTIFICATIONS_ADMINISTRATOR_PREFIX_CONFIGURATION = 'sms_notifications_administrator_prefix';
    const SMS_NOTIFICATIONS_DELIVERY_ADDRESS_CONFIGURATION = 'sms_notifications_delivery_address';
    const SMS_NOTIFICATIONS_INVOICE_ADDRESS_CONFIGURATION = 'sms_notifications_invoice_address';
    const SMS_REMINDER_DEFAULT_TIME_CONFIG = 'sms_reminder_default_time_config';
    const PLUGIN_VERSION_KEY = 'egoi_plugin_version_req';
    const PLUGIN_VERSION_KEY_TTL = 'egoi_plugin_version_req_ttl';

    const SMS_MESSAGES_DEFAULT_LANG_CONFIGURATION = 'sms_messages_default_lang';
    const SMS_REMINDERS_DEFAULT_LANG_CONFIGURATION = 'sms_reminders_default_lang';

    const CONFIGURED_WEB_PUSH = 'egoi_web_push_config';
    const WEB_PUSH_APP_CODE = 'egoi_web_push_app_code';

    const CONNECTED_SITES_CODE = 'egoi_connected_sites_code';

    const CUSTOM_INFO_DELIMITER = '%';
    const CUSTOM_INFO_ORDER_REFERENCE = self::CUSTOM_INFO_DELIMITER . 'order_reference' . self::CUSTOM_INFO_DELIMITER;
    const CUSTOM_INFO_ORDER_STATUS = self::CUSTOM_INFO_DELIMITER . 'order_status' . self::CUSTOM_INFO_DELIMITER;
    const CUSTOM_INFO_TOTAL_COST = self::CUSTOM_INFO_DELIMITER . 'total_cost' . self::CUSTOM_INFO_DELIMITER;
    const CUSTOM_INFO_CURRENCY = self::CUSTOM_INFO_DELIMITER . 'currency' . self::CUSTOM_INFO_DELIMITER;
    const CUSTOM_INFO_ENTITY = self::CUSTOM_INFO_DELIMITER . 'entity' . self::CUSTOM_INFO_DELIMITER;
    const CUSTOM_INFO_MB_REFERENCE = self::CUSTOM_INFO_DELIMITER . 'mb_reference' . self::CUSTOM_INFO_DELIMITER;
    const CUSTOM_INFO_SHOP_NAME = self::CUSTOM_INFO_DELIMITER . 'shop_name' . self::CUSTOM_INFO_DELIMITER;
    const CUSTOM_INFO_BILLING_NAME = self::CUSTOM_INFO_DELIMITER . 'billing_name' . self::CUSTOM_INFO_DELIMITER;
    const CUSTOM_INFO_CARRIER = self::CUSTOM_INFO_DELIMITER . 'carrier' . self::CUSTOM_INFO_DELIMITER;
    const CUSTOM_INFO_TRACKING_URL = self::CUSTOM_INFO_DELIMITER . 'tracking_url' . self::CUSTOM_INFO_DELIMITER;
    const CUSTOM_INFO_TRACKING_NUMBER = self::CUSTOM_INFO_DELIMITER . 'tracking_number' . self::CUSTOM_INFO_DELIMITER;

    const PAYMENT_MODULE_IFTHENPAY = 'multibanco';
    const PAYMENT_MODULE_EUPAGO = 'eupagomb';
    const PAYMENT_STATUS_WAITING_MB_PAYMENT = 'waiting_mb_payment';

    const LIMIT_HOUR_MIN = 10;
    const LIMIT_HOUR_MAX = 22;

	/**
	 * @var mixed
	 */
	protected $success_msg;

	/**
	 * @var mixed
	 */
	protected $error_msg;

	/**
	 * Custom Override file
	 *
	 * @var string
	 */
	protected $custom_override = '/../../override/classes/WebserviceSpecificManagementEgoi.php';

    /**
     * Dev Index file Cache
     *
     * @var string
     */
	protected $dev_cache;

    /**
     * Production Index file Cache
     *
     * @var string
     */
	protected $prod_cache;

    /**
     * Transactional API
     *
     * @var $transactionalApi
     */
	protected $transactionalApi;

    /**
     * API v3
     *
     * @var $apiv3
     */
    protected $apiv3;

    /**
     * Goidini api
     *
     * @var $goidini
     */
    protected $goidini;

    /**
     * @var array[] $menuTab
     */
    public $menus = [
        [
            'is_root' => true,
            'name' => 'Smart Marketing',
            'class_name' => 'SmartMarketingPs',
            'visible' => true,
            'parent_class_name' => '',
            'icon' => '',
            'position' => 1,
        ],
        [
            'is_root' => false,
            'name' => 'Account',
            'class_name' => 'Account',
            'visible' => true,
            'parent_class_name' => 'SmartMarketingPs',
            'icon' => 'settings_applications',
            'position' => 0,
        ],
        [
            'is_root' => false,
            'name' => 'Sync Contacts',
            'class_name' => 'Sync',
            'visible' => false,
            'parent_class_name' => 'SmartMarketingPs',
            'icon' => 'sync',
            'position' => 0,
        ],
        [
            'is_root' => false,
            'name' => 'E-commerce (New)',
            'class_name' => 'Ecommerce',
            'visible' => false,
            'parent_class_name' => 'SmartMarketingPs',
            'icon' => 'shopping_basket',
            'position' => 0,
        ],
        [
            'is_root' => false,
            'name' => 'SMS Notifications',
            'class_name' => 'SmsNotifications',
            'visible' => false,
            'parent_class_name' => 'SmartMarketingPs',
            'icon' => 'textsms',
            'position' => 0,
        ],
        [
            'is_root' => false,
            'name' => 'Products',
            'class_name' => 'Products',
            'visible' => false,
            'parent_class_name' => 'SmartMarketingPs',
            'icon' => 'shop',
            'position' => 0,
        ],
    ];


	/**
	* Module Constructor
	*/
	public function __construct()
	{
		// Module metadata
		$this->name = 'smartmarketingps';
	    $this->tab = 'advertising_marketing';
	    $this->version = '5.0.0';
	    $this->author = 'E-goi';
	    $this->need_instance = 1;
	    $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
	    $this->bootstrap = true;

	    // module key
	    $this->module_key = '2f50fb7b4988d0a880ac658653d637ad';

        $this->dev_cache = dirname(__FILE__).'/../../app/cache/dev/class_index.php';
        $this->prod_cache = dirname(__FILE__).'/../../app/cache/prod/class_index.php';

	    parent::__construct();

	    // Name & Description
	    $this->displayName = $this->l('Smart Marketing');
	    $this->description = $this->l('Easily sync your Prestashop contacts with E-goi.');

	   	// on uninstall
	    $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        require_once $this->getLocalPath() . 'vendor/autoload.php';

        $this->transactionalApi = new TransactionalApi();
        $this->apiv3 = new ApiV3();
        $this->goidini = new GoidiniApi();

        $warning_message = $this->l('No Apikey provided');
	    if (!Configuration::get('smart_api_key')) {
	      	$this->warning = $warning_message;
	    }

        if(!empty(Tools::getValue('smart_api_key')) && !empty($_POST['egoi_client_id'])) {
            $this->addClientId($_POST);
            $this->enableMenus();
        }

        $this->validateApiKey();

        // check newsletter submissions anywhere
		//$this->checkNewsletterSubmissions();
        $current_context = Context::getContext();
        try {
            // Checks that the controller object is defined and that its controller_type property is 'admin'
            if (isset($current_context->controller) && $current_context->controller->controller_type == 'admin') {
                $this->checkPluginVersion();
            }
        } catch (Exception $e) {
            // If there is an error, it does nothing
        }


	}

    public function checkPluginVersion(){
        $lastVersion = Configuration::get(self::PLUGIN_VERSION_KEY);
        $lastReq = Configuration::get(self::PLUGIN_VERSION_KEY_TTL);

        if (empty($lastVersion) || empty($lastReq) || (isset($lastReq) && $lastReq + 3600 < time() ))
        {
            $lastVersion = $this->goidini->getLatestPluginVersions();
            Configuration::updateValue(self::PLUGIN_VERSION_KEY, $lastVersion);
            Configuration::updateValue(self::PLUGIN_VERSION_KEY_TTL, time());
        }

        $lastVersionObj = json_decode($lastVersion, true);
        $lastReq = Configuration::get(self::PLUGIN_VERSION_KEY_TTL);
        if(empty($lastVersionObj) || empty($lastVersionObj['prestashop']) || (isset($lastReq) && is_numeric($lastReq) && ($lastReq + 3600) < time()) ){
            Configuration::updateValue(self::PLUGIN_VERSION_KEY, 0);
            return;
        }

        // if(version_compare($lastVersionObj['prestashop'], $this->version, '>')) {
        //     $this->createPopupAcknowledge("[E-goi] New Version (".$lastVersionObj['prestashop'].")", "The new version is ready to download, <a href=\"https://goidini.e-goi.com/resources/plugins/prestashop/smart-marketing-ps.1.7.latest.zip\" target=\"_blank\" >click here</a>!");
        // }

    }

    protected function createPopupAcknowledge($title, $message){
        ?>

        <style>
            .egoi-popup-notification>h3>.egoi_close,
            .egoi-popup-notification>p,
            .egoi-popup-notification>h3{
                color: white;
            }

            .egoi-popup-notification>h3>.egoi_close{
                cursor: pointer;
            }

            .egoi-popup-notification>h3{
                display: flex;
                justify-content: space-between;
                margin-top: 0;
            }

            .egoi-popup-notification>p{
                margin-bottom: 0;
            }


            .egoi-popup-notification>p>a{
                color: #2a2a2a !important;
                font-weight: bold;
            }

            .egoi-popup-notification{
                position: fixed;
                top: 40px;
                right: 10px;
                border: 1px solid #dbe6e9;
                border-radius: 5px;
                -webkit-box-shadow: 0 0 4px 0 rgb(0 0 0 / 6%);
                box-shadow: 0 0 4px 0 rgb(0 0 0 / 6%);
                background-color: #25b9d7;
                z-index: 9999;
                padding: 20px;
            }
        </style>

        <script>

            window.addEventListener('load', function () {
                (function (){
                    if( !localStorage.getItem("closed_egoi_update") && document.getElementsByClassName("egoi-popup-notification").length > 0 ){
                        document.getElementsByClassName("egoi-popup-notification")[0].style.display = 'block'
                    }
                    if( document.getElementsByClassName("egoi_close").length >0 ){
                        document.getElementsByClassName("egoi_close")[0].onclick = () => {
                            if( document.getElementsByClassName("egoi-popup-notification").length > 0){
                                document.getElementsByClassName("egoi-popup-notification")[0].style.display = 'none';
                                localStorage.setItem("closed_egoi_update", 'true');
                            }
                        }
                    }
                })();
            });

        </script>

        <div class="egoi-popup-notification" style="display: none;">
            <h3>
                <?php echo $title; ?>
                <a class="egoi_close" >x</a>
            </h3>
            <p>
                <?php echo $message; ?>
            </p>
        </div>

        <?php
    }


	/**
	 * Install App
	 *
	 * @return bool
	 */
	public function install()
	{
        PrestaShopLogger::addLog("[EGOI-PS8]::".__CLASS__."::".__FUNCTION__."::LINE::".__LINE__."::LOG: START INSTALL");

        if (!parent::install()) {
            $this->_errors[] = $this->l("Error: Failed to install from parent.");
            PrestaShopLogger::addLog("[EGOI-PS8]::".__CLASS__."::".__FUNCTION__."::LINE::".__LINE__."::ERROR: Failed to install from parent::" . implode('::', $this->_errors));
            return false;
        }
        if (!$this->installDb()) {
            $this->_errors[] = $this->l("Error: Failed to create e-goi tables.");
            PrestaShopLogger::addLog("[EGOI-PS8]::".__CLASS__."::".__FUNCTION__."::LINE::".__LINE__."::ERROR: Failed to create e-goi tables");
            return false;
        }
        if (!$this->createMenu()) {
            $this->_errors[] = $this->l("Error: Failed to create e-goi menu.");
            PrestaShopLogger::addLog("[EGOI-PS8]::".__CLASS__."::".__FUNCTION__."::LINE::".__LINE__."::ERROR: Failed to create e-goi menu");
            return false;
        }
        if (!$this->registerHooksEgoi()) {
            $this->_errors[] = $this->l("Error: Failed to register webhooks.");
            PrestaShopLogger::addLog("[EGOI-PS8]::".__CLASS__."::".__FUNCTION__."::LINE::".__LINE__."::ERROR: Failed to register webhooks");
            return false;
        }
        if (!$this->addEgoiStates()) {
            $this->_errors[] = $this->l("Error: Failed to add E-goi states.");
            PrestaShopLogger::addLog("[EGOI-PS8]::" . __CLASS__ . "::" . __FUNCTION__ . "::LINE::" . __LINE__ . "::ERROR: Failed to add E-goi states");
            return false;
        }
        if (!$this->mapEgoiToPrestashopStates()) {
            $this->_errors[] = $this->l("Error: Failed to map E-goi states to PrestaShop states.");
            PrestaShopLogger::addLog("[EGOI-PS8]::" . __CLASS__ . "::" . __FUNCTION__ . "::LINE::" . __LINE__ . "::ERROR: Failed to map E-goi states to PrestaShop states");
            return false;
        }


        // register WebService
		$this->registerWebService();
		$this->updateApp();
        PrestaShopLogger::addLog("[EGOI-PS8]::".__CLASS__."::".__FUNCTION__."::LINE::".__LINE__."::INSTALL OK");
	  	return true;
	}

    /**
     * Add initial states to E-goi using PrestaShop functions
     *
     * @return bool
     */
    private function addEgoiStates()
    {
        $states = [
            ['egoi_id' => 1, 'name' => 'created'],
            ['egoi_id' => 2, 'name' => 'pending'],
            ['egoi_id' => 3, 'name' => 'canceled'],
            ['egoi_id' => 4, 'name' => 'completed'],
            ['egoi_id' => 5, 'name' => 'unknown']
        ];

        foreach ($states as $state) {
            if (!Db::getInstance()->insert('egoi_order_states', [
                'egoi_id' => (int)$state['egoi_id'],
                'name' => pSQL($state['name'])
            ])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mapping E-goi states with PrestaShop states
     *
     * @return bool
     */
    private function mapEgoiToPrestashopStates()
    {
        $egoiStateMap = Db::getInstance()->executeS('SELECT egoi_id FROM `' . _DB_PREFIX_ . 'egoi_order_states`');
        if (!$egoiStateMap) {
            return false;
        }

        $validEgoiIds = array_column($egoiStateMap, 'egoi_id');

        $mapping = [
            ['prestashop_state_id' => 13, 'egoi_id' => 2, 'type' => 'order'], // Awaiting Cash On Delivery validation -> pending
            ['prestashop_state_id' => 1, 'egoi_id' => 1, 'type' => 'order'],  // Awaiting check payment -> created
            ['prestashop_state_id' => 6, 'egoi_id' => 3, 'type' => 'order'],  // Cancelled -> cancelled
            ['prestashop_state_id' => 5, 'egoi_id' => 4, 'type' => 'order'],  // Delivered -> completed
            ['prestashop_state_id' => 12, 'egoi_id' => 2, 'type' => 'order'], // On backorder (not paid) -> pending
            ['prestashop_state_id' => 9, 'egoi_id' => 2, 'type' => 'order'],  // On backorder (paid) -> pending
            ['prestashop_state_id' => 10, 'egoi_id' => 2, 'type' => 'order'],  // On backorder (paid) -> pending
            ['prestashop_state_id' => 2, 'egoi_id' => 1, 'type' => 'order'],  // Payment accepted -> created
            ['prestashop_state_id' => 8, 'egoi_id' => 3, 'type' => 'order'],  // Payment error -> cancelled
            ['prestashop_state_id' => 3, 'egoi_id' => 2, 'type' => 'order'],  // Processing in progress -> pending
            ['prestashop_state_id' => 7, 'egoi_id' => 3, 'type' => 'order'],  // Refunded -> cancelled
            ['prestashop_state_id' => 11, 'egoi_id' => 2, 'type' => 'order'], // Remote payment accepted -> pending
            ['prestashop_state_id' => 4, 'egoi_id' => 4, 'type' => 'order'],  // Shipped -> completed
        ];

        $orderStates = OrderState::getOrderStates((int)$this->context->language->id);

        foreach ($orderStates as $state) {
            $map = null;
            foreach ($mapping as $item) {
                if ($item['prestashop_state_id'] == $state['id_order_state']) {
                    $map = $item;
                    break;
                }
            }

            if (!$map) {
                $map = ['prestashop_state_id' => $state['id_order_state'], 'egoi_id' => 5, 'type' => 'order']; // Default to 'unknown' state
            }

            if (!in_array($map['egoi_id'], $validEgoiIds)) {
                continue;
            }

            if (!Db::getInstance()->insert('egoi_prestashop_order_state_map', [
                'prestashop_state_id' => (int)$map['prestashop_state_id'],
                'egoi_state_id' => (int)$map['egoi_id'],
                'type' => pSQL($map['type']),
                'active' => 1,
            ])) {
                return false;
            }
        }

        return true;
    }

    public static function getEgoiOrderStatusName($prestashopStateId)
    {
        // Query para buscar o egoi_state_id correspondente ao prestashopStateId
        $sql = 'SELECT es.name 
            FROM ' . _DB_PREFIX_ . 'egoi_prestashop_order_state_map AS map
            INNER JOIN ' . _DB_PREFIX_ . 'egoi_order_states AS es
                ON map.egoi_state_id = es.egoi_id
            WHERE map.prestashop_state_id = ' . (int)$prestashopStateId . ' 
              AND map.active = 1';

        // Retorna diretamente o nome encontrado ou null
        return Db::getInstance()->getValue($sql);
    }





    function registerHooksEgoi(){
        return $this->registerHook(
            array(
                'cart',
                'actionCartSave',
                'actionDispatcher',
                'actionObjectCustomerAddAfter',
                'actionObjectCustomerUpdateAfter',
                'actionObjectCustomerDeleteAfter',
                'actionOrderStatusPostUpdate',
                'actionObjectCategoryUpdateAfter',
                'actionObjectCategoryDeleteAfter',
                'hookActionObjectProductAddAfter',
                'actionObjectProductUpdateAfter',
                'actionObjectProductDeleteAfter',
                'actionObjectSpecificPriceAddAfter',
                'actionObjectSpecificPriceDeleteAfter',
                'actionObjectSpecificPriceUpdateAfter',
                'actionNewsletterRegistrationAfter',
                'displayHome',
                'displayTop',
                'displayFooter',
                'egoiDisplayTE',
            )
        );
    }

    /**
	 * Install required Tables
	 *
	 * @return bool
	 */
	protected function installDb()
	{
		$return = true;
		$sql = array();
		include dirname(__FILE__) . '/install/install.php';

		foreach ($sql as $s){
		    $return &= Db::getInstance()->execute($s);
		}

		if ($return) {
		    $this->mapOrderStateTemplates();
        }
		return $return;
    }


    /**
     * @return int|true
     */
    protected function updateApp()
    {
        $updateql = array();
        include dirname(__FILE__) . '/install/update.php';
        foreach ($updateql as $u){
            try {
                Db::getInstance()->execute($u);
            } catch (Exception $e) {
                continue;
            }
        }

        return true;
    }


    /**
     * Maps order state templates
     */
    private function mapOrderStateTemplates()
    {
        $orderState = new OrderState($this->context->language->id);
        $orderStates = $orderState->getOrderStates($this->context->language->id);
        $langs = Language::getLanguages(true, $this->context->shop->id);

        $orderStateTemplates = $this->getOrderStateTemplates();
        foreach ($orderStates as $orderState) {
            $paymentMod = self::getPaymentModule($orderState);
            if ($paymentMod) {
                $orderState['template'] = self::PAYMENT_STATUS_WAITING_MB_PAYMENT;
                $this->mapReminderLangs($langs, $orderState);
            }
            if (isset($orderStateTemplates[$orderState['template']])) {
                $this->mapOrderStateTemplateLangs($langs, $orderStateTemplates[$orderState['template']], $orderState);
            }
        }
    }

    /**
     * Maps languages for an order state template
     *
     * @param $langs
     * @param $template
     * @param $orderState
     */
    private function mapOrderStateTemplateLangs($langs, $template, $orderState)
    {
        foreach ($langs as $lang) {
            if (isset($template[$lang['iso_code']])) {
                $clientMessage = $template[$lang['iso_code']]['client'];
                $adminMessage = $template[$lang['iso_code']]['admin'];
            } else {
                $clientMessage = $template['en']['client'];
                $adminMessage = $template['en']['admin'];
            }

            try{
                Db::getInstance()->insert(
                    'egoi_sms_notif_messages',
                    array(
                        'order_status_id' => $orderState['id_order_state'],
                        'lang_id' => $lang['id_lang'],
                        'client_message' => $clientMessage,
                        'admin_message' => $adminMessage
                    )
                );
            }catch(Exception $e){
                //duplicated
            }
        }
    }

    /**
     * Maps languages for reminder
     *
     * @param $langs
     * @param $orderState
     */
    private function mapReminderLangs($langs, $orderState)
    {
        $reminderTemplates = $this->getReminderTemplates();
        foreach ($langs as $lang) {
            if (isset($reminderTemplates[$lang['iso_code']])) {
                $message = $reminderTemplates[$lang['iso_code']];
            } else {
                $message = $reminderTemplates['en'];
            }

            Db::getInstance()->insert(
                'egoi_sms_notif_reminder_messages',
                array(
                    'order_status_id' => $orderState['id_order_state'],
                    'lang_id' => $lang['id_lang'],
                    'message' => $message,
                    'active' => 0
                )
            );
        }
    }

    /**
     * Get reminder templates
     *
     * @return array
     */
    private function getReminderTemplates()
    {
        return array(
            'en' => 'Hi, ' . self::CUSTOM_INFO_BILLING_NAME . '! MB payment details for your order at '
                . self::CUSTOM_INFO_SHOP_NAME . '. Ent.: '
                . self::CUSTOM_INFO_ENTITY . ' Ref.: ' . self::CUSTOM_INFO_MB_REFERENCE . ' Amount: '
                . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' Thanks!',
            'pt' => 'Olá, ' . self::CUSTOM_INFO_BILLING_NAME . '! Aguardamos o pagamento por MB do pedido que fez na '
                . self::CUSTOM_INFO_SHOP_NAME . '. Ent.: '
                . self::CUSTOM_INFO_ENTITY . ' Ref.: ' . self::CUSTOM_INFO_MB_REFERENCE . ' Valor: '
                . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' Obrigado!',
            'es' => 'Hola ' . self::CUSTOM_INFO_BILLING_NAME . ', Datos para el pago vía MB de tu pedido en '
                . self::CUSTOM_INFO_SHOP_NAME . '. Ent.: '
                . self::CUSTOM_INFO_ENTITY . ' Ref.: ' . self::CUSTOM_INFO_MB_REFERENCE . ' Importe: '
                . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' ¡Gracias!'
        );
    }

    /**
     * Get templates for each order state
     *
     * @return array
     */
    private function getOrderStateTemplates()
    {
        return array(
            'cheque' => array(
                'en' => array(
                    'client' => 'Hi, ' . self::CUSTOM_INFO_BILLING_NAME . '! We are waiting for the payment of your order at ' . self::CUSTOM_INFO_SHOP_NAME . '. Amount to pay: ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . '.',
                    'admin' => 'New order ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' amounting ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' is in ' . self::CUSTOM_INFO_ORDER_STATUS . ' status.'
                ),
                'pt' => array(
                    'client' => 'Olá, ' . self::CUSTOM_INFO_BILLING_NAME . '! Obrigado pelo seu pedido na ' . self::CUSTOM_INFO_SHOP_NAME . '. O valor é de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . '. Aguardamos o pagamento.',
                    'admin' => 'O novo pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está no estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                ),
                'es' => array(
                    'client' => 'Hola ' . self::CUSTOM_INFO_BILLING_NAME . ', Gracias por tu pedido en ' . self::CUSTOM_INFO_SHOP_NAME . '. El importe es de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . '. Estamos esperando tu pago.',
                    'admin' => 'El nuevo pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está en el estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                )
            ),
            'bankwire' => array(
                'en' => array(
                    'client' => 'Hi, ' . self::CUSTOM_INFO_BILLING_NAME . '! We are waiting for the payment of your order at ' . self::CUSTOM_INFO_SHOP_NAME . '. Amount to pay: ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . '.',
                    'admin' => 'New order ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' amounting ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' is in ' . self::CUSTOM_INFO_ORDER_STATUS . ' status.'
                ),
                'pt' => array(
                    'client' => 'Olá, ' . self::CUSTOM_INFO_BILLING_NAME . '! Obrigado pelo seu pedido na ' . self::CUSTOM_INFO_SHOP_NAME . '. O valor é de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . '. Aguardamos o pagamento.',
                    'admin' => 'O novo pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está no estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                ),
                'es' => array(
                    'client' => 'Hola ' . self::CUSTOM_INFO_BILLING_NAME . ', Gracias por tu pedido en ' . self::CUSTOM_INFO_SHOP_NAME . '. El importe es de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . '. Estamos esperando tu pago.',
                    'admin' => 'El nuevo pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está en el estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                )
            ),
            'order_canceled' => array(
                'en' => array(
                    'client' => 'Hi, ' . self::CUSTOM_INFO_BILLING_NAME . '! Your order at ' . self::CUSTOM_INFO_SHOP_NAME . ' has been cancelled.',
                    'admin' => 'New order ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' amounting ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' is in ' . self::CUSTOM_INFO_ORDER_STATUS . ' status.'
                ),
                'pt' => array(
                    'client' => 'Olá, ' . self::CUSTOM_INFO_BILLING_NAME . '! O pedido que fez na nossa loja ' . self::CUSTOM_INFO_SHOP_NAME . ' foi cancelado.',
                    'admin' => 'O novo pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está no estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                ),
                'es' => array(
                    'client' => 'Hola ' . self::CUSTOM_INFO_BILLING_NAME . ', Tu pedido en nuestra tienda ' . self::CUSTOM_INFO_SHOP_NAME . ' ha sido cancelado.',
                    'admin' => 'El nuevo pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está en el estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                )
            ),
            'payment' => array(
                'en' => array(
                    'client' => 'Hi, ' . self::CUSTOM_INFO_BILLING_NAME . '! The payment for your order at ' . self::CUSTOM_INFO_SHOP_NAME . ' has been confirmed. We are now processing your order. Thanks!',
                    'admin' => 'New order ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' amounting ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' is in ' . self::CUSTOM_INFO_ORDER_STATUS . ' status.'
                ),
                'pt' => array(
                    'client' => 'Olá, ' . self::CUSTOM_INFO_BILLING_NAME . '! O pagamento do pedido que fez na ' . self::CUSTOM_INFO_SHOP_NAME . ' está confirmado. O pedido já está em processamento. Obrigado!',
                    'admin' => 'O novo pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está no estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                ),
                'es' => array(
                    'client' => 'Hola ' . self::CUSTOM_INFO_BILLING_NAME . ', El pago de tu pedido en ' . self::CUSTOM_INFO_SHOP_NAME . ' fue confirmado. Estamos procesando el pedido. ¡Gracias!',
                    'admin' => 'El nuevo pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está en el estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                )
            ),
            'preparation' => array(
                'en' => array(
                    'client' => 'Hi, ' . self::CUSTOM_INFO_BILLING_NAME . '! We are preparing your order at ' . self::CUSTOM_INFO_SHOP_NAME . ' for shipment. Thanks!',
                    'admin' => ''
                ),
                'pt' => array(
                    'client' => 'Olá, ' . self::CUSTOM_INFO_BILLING_NAME . '! O pedido que fez na ' . self::CUSTOM_INFO_SHOP_NAME . ' está em preparação. Obrigado!',
                    'admin' => ''
                ),
                'es' => array(
                    'client' => 'Hola ' . self::CUSTOM_INFO_BILLING_NAME . ', Estamos preparando el envío de tu pedido en ' . self::CUSTOM_INFO_SHOP_NAME . '. ¡Gracias!',
                    'admin' => ''
                )
            ),
            'refund' => array(
                'en' => array(
                    'client' => 'Hi, ' . self::CUSTOM_INFO_BILLING_NAME . '! We have just refunded your order at ' . self::CUSTOM_INFO_SHOP_NAME . '.',
                    'admin' => 'New order ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' amounting ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' is in ' . self::CUSTOM_INFO_ORDER_STATUS . ' status.'
                ),
                'pt' => array(
                    'client' => 'Olá, ' . self::CUSTOM_INFO_BILLING_NAME . '! Fizemos o reembolso do valor do seu pedido na nossa loja ' . self::CUSTOM_INFO_SHOP_NAME . '.',
                    'admin' => 'O pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está no estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                ),
                'es' => array(
                    'client' => 'Hola ' . self::CUSTOM_INFO_BILLING_NAME . ', Hemos finalizado la devolución del importe de tu pedido en ' . self::CUSTOM_INFO_SHOP_NAME . '.',
                    'admin' => 'El pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está en el estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                )
            ),
            'outofstock' => array(
                'en' => array(
                    'client' => 'Hi, ' . self::CUSTOM_INFO_BILLING_NAME . '! Unfortunately the item you chose at ' . self::CUSTOM_INFO_SHOP_NAME . ' is out of stock. We will contact you soon about it.',
                    'admin' => 'New order ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' amounting ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' is in ' . self::CUSTOM_INFO_ORDER_STATUS . ' status.'
                ),
                'pt' => array(
                    'client' => 'Olá, ' . self::CUSTOM_INFO_BILLING_NAME . '! Infelizmente o produto que escolheu na ' . self::CUSTOM_INFO_SHOP_NAME . ' está esgotado. Aguarde o nosso contato.',
                    'admin' => 'O pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está no estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                ),
                'es' => array(
                    'client' => 'Hola ' . self::CUSTOM_INFO_BILLING_NAME . ', Lamentablemente el producto que elegiste en ' . self::CUSTOM_INFO_SHOP_NAME . ' está agotado. Te contactaremos pronto.',
                    'admin' => 'El pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está en el estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                )
            ),
            self::PAYMENT_STATUS_WAITING_MB_PAYMENT => array(
                'en' => array(
                    'client' => 'Hi, ' . self::CUSTOM_INFO_BILLING_NAME . '! MB payment details for your order at ' . self::CUSTOM_INFO_SHOP_NAME . '. Ent.: ' . self::CUSTOM_INFO_ENTITY . ' Ref.: ' . self::CUSTOM_INFO_MB_REFERENCE . ' Amount: ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' Thanks!',
                    'admin' => 'New order ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' amounting ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' is in %order_status% status.'
                ),
                'pt' => array(
                    'client' => 'Olá, ' . self::CUSTOM_INFO_BILLING_NAME . '! Aguardamos o pagamento por MB do pedido que fez na ' . self::CUSTOM_INFO_SHOP_NAME . '. Ent.: ' . self::CUSTOM_INFO_ENTITY . ' Ref.: ' . self::CUSTOM_INFO_MB_REFERENCE . ' Valor: ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' Obrigado!',
                    'admin' => 'O novo pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está no estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                ),
                'es' => array(
                    'client' => 'Hola ' . self::CUSTOM_INFO_BILLING_NAME . ', Datos para el pago vía MB de tu pedido en ' . self::CUSTOM_INFO_SHOP_NAME . '. Ent.: ' . self::CUSTOM_INFO_ENTITY . ' Ref.: ' . self::CUSTOM_INFO_MB_REFERENCE . ' Importe: ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' ¡Gracias!',
                    'admin' => 'El nuevo pedido ' . self::CUSTOM_INFO_ORDER_REFERENCE . ' de ' . self::CUSTOM_INFO_TOTAL_COST . self::CUSTOM_INFO_CURRENCY . ' está en el estado ' . self::CUSTOM_INFO_ORDER_STATUS . '.'
                )
            )
        );
    }

    private function removeMenu()
    {
        foreach ($this->menus as $menu) {
            $id_tab = (int)Tab::getIdFromClassName($menu['class_name']);

            if ($id_tab) {
                $tab = new Tab($id_tab);
                $tab->delete();
            }
        }
        return true;
    }

    private function createPermissions(){

        $roles = array(
            'ROLE_MOD_TAB_ACCOUNT_READ',
            'ROLE_MOD_TAB_SYNC_READ',
            'ROLE_MOD_TAB_FORMS_READ',
            'ROLE_MOD_TAB_SMSNOTIFICATIONS_READ',
            'ROLE_MOD_TAB_PRODUCTS_READ',
            'ROLE_MOD_TAB_PUSHNOTIFICATIONS_READ',
            'ROLE_MOD_TAB_SMARTMARKETINGPS_READ',
            'ROLE_MOD_TAB_ACCOUNT_CREATE',
            'ROLE_MOD_TAB_SYNC_CREATE',
            'ROLE_MOD_TAB_FORMS_CREATE',
            'ROLE_MOD_TAB_SMSNOTIFICATIONS_CREATE',
            'ROLE_MOD_TAB_PRODUCTS_CREATE',
            'ROLE_MOD_TAB_PUSHNOTIFICATIONS_CREATE',
            'ROLE_MOD_TAB_SMARTMARKETINGPS_CREATE',
            'ROLE_MOD_TAB_ACCOUNT_DELETE',
            'ROLE_MOD_TAB_SYNC_DELETE',
            'ROLE_MOD_TAB_FORMS_DELETE',
            'ROLE_MOD_TAB_SMSNOTIFICATIONS_DELETE',
            'ROLE_MOD_TAB_PRODUCTS_DELETE',
            'ROLE_MOD_TAB_PUSHNOTIFICATIONS_DELETE',
            'ROLE_MOD_TAB_SMARTMARKETINGPS_DELETE',
            'ROLE_MOD_TAB_ACCOUNT_UPDATE',
            'ROLE_MOD_TAB_SYNC_UPDATE',
            'ROLE_MOD_TAB_FORMS_UPDATE',
            'ROLE_MOD_TAB_SMSNOTIFICATIONS_UPDATE',
            'ROLE_MOD_TAB_PRODUCTS_UPDATE',
            'ROLE_MOD_TAB_PUSHNOTIFICATIONS_UPDATE',
            'ROLE_MOD_TAB_SMARTMARKETINGPS_UPDATE'
        );

        foreach ($roles as $val) {
            $id_authorization_role = Db::getInstance()->getValue("SELECT id_authorization_role FROM "._DB_PREFIX_."authorization_role WHERE slug = '".$val."'");

            if (empty($id_authorization_role)) {
                Db::getInstance()->insert('authorization_role',
                    array(
                        'slug' => $val
                    )
                );
                $id_authorization_role = Db::getInstance()->getValue("SELECT id_authorization_role FROM "._DB_PREFIX_."authorization_role WHERE slug = '".$val."'");
            }

            $result = Db::getInstance()->getValue("SELECT id_profile,id_authorization_role FROM "._DB_PREFIX_."access WHERE id_profile = '1' AND id_authorization_role = '".$id_authorization_role."'");
            if (empty($result)) {
                Db::getInstance()->insert('access',
                    array(
                        'id_profile' => '1',
                        'id_authorization_role' => $id_authorization_role
                    )
                );
            }
        }
    }

    private function enableMenus()
    {
        foreach ($this->menus as $menu) {
            if($menu['visible'] === false) {
                $id_tab = (int)Tab::getIdFromClassName($menu['class_name']);

                if ($id_tab) {
                    $tab = new Tab($id_tab);
                    $tab->active = true;
                    $tab->save();
                }
            }
        }
    }

    private function disableMenus()
    {
        foreach ($this->menus as $menu) {
            if($menu['visible'] === false) {
                $id_tab = (int)Tab::getIdFromClassName($menu['class_name']);

                if ($id_tab) {
                    $tab = new Tab($id_tab);
                    $tab->active = $menu['position'];
                    $tab->save();
                }
            }
        }
    }

    /**
	 * Create menu
	 *
	 * @return bool
	 */
	private function createMenu()
	{
        $this->createPermissions();
        foreach ($this->menus as $menu) {
            $this->addTab($menu['class_name'], $menu['name'], $menu['parent_class_name'], $menu['icon'], $menu['visible'], $menu['position']);
        }
		return true;
	}

    /**
     * @param $class_name
     * @param $tab_name
     * @param $parent
     * @param $icon
     * @param bool $visible
     * @param int $position
     * @return bool
     */
    private function addTab($class_name, $tab_name, $parent, $icon, $visible = true, $position = 0)
    {

        $tab = new Tab();
        $tab->class_name = $class_name;
        $tab->id_parent = Tab::getIdFromClassName($parent);

        $tab->position = $position;
        $tab->module = $this->name;
        $tab->active = $visible;

        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->l($tab_name);
        }

        if ($tab->add()) {
            if (!empty($icon)) {
                $this->copyIcon($class_name, $icon);
            }
            return true;
        }

        return false;
    }

    private function copyIcon($class_name, $icon)
    {
        $source = _PS_MODULE_DIR_ . $this->name . '/views/img/' . $icon;
        $destination = _PS_IMG_DIR_ . 't/' . $class_name . '.png';

        @copy($source, $destination);
    }

    /**
     * Uninstall required tables
     *
     * @return bool
     */
    protected function uninstallDb()
	{
		// drop all tables from the plugin
		include dirname(__FILE__) . '/install/uninstall.php';
    	foreach ($sql as $name => $v){
       		Db::getInstance()->execute('DROP TABLE IF EXISTS '.$name);
   		}

        // Drop trigger of the order status from the plugin
        foreach ($sqlTrigger as $triggerName => $value) {
                Db::getInstance()->execute('DROP TRIGGER IF EXISTS ' . $triggerName);
        }

        // remove menus
   		$this->removeMenu();

   		// remove API Key in cache
   		Configuration::deleteByName('smart_api_key');

        Configuration::deleteByName(self::SMS_NOTIFICATIONS_SENDER_CONFIGURATION);

        if (Configuration::hasKey(self::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION)) {
            Configuration::deleteByName(self::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION);
        }

        Configuration::deleteByName(self::SMS_NOTIFICATIONS_ADMINISTRATOR_CONFIGURATION);

		// remove webservice
		$this->uninstallWebService();

   		// remove custom override file
   		@unlink(dirname(__FILE__).$this->custom_override);
    	return true;
    }

	/**
	 * Uninstall App
	 *
	 * @return bool
	 */
	public function uninstall()
	{
	  	if (!parent::uninstall() || !$this->uninstallDb())
	    	return false;

	  	return true;
	}

	/**
	 * Register WebService Overrides
	 *
	 * @return bool
	 */
	public function registerWebService()
	{
        try {
            $row = Db::getInstance()
                ->getRow('SELECT id_webservice_account FROM '._DB_PREFIX_.'webservice_account WHERE description="E-goi"');

            if(empty($row)) {
                Db::getInstance()->insert('webservice_account', array(
                    'key' => md5(time().uniqid('egoi')),
                    'class_name' => "WebserviceRequest",
                    'description' => "E-goi",
                    'active' => 1
                ));

                $row = Db::getInstance()
                    ->getRow('SELECT id_webservice_account FROM '._DB_PREFIX_.'webservice_account WHERE description="E-goi"');
            }

            if(!empty($row)) {
                $id_webservice = $row['id_webservice_account'];

                $row_webservice_account = Db::getInstance()
                    ->getRow('SELECT id_webservice_account FROM '._DB_PREFIX_.'webservice_account_shop WHERE id_webservice_account="'.$id_webservice.'"');

                if (empty($row_webservice_account)) {
                    // add webservice relation
                    Db::getInstance()->insert(
                        'webservice_account_shop',
                        array(
                            'id_webservice_account' => $id_webservice,
                            'id_shop' => 1,
                        )
                    );
                }

                $row_webservice_permission = Db::getInstance()
                    ->getRow(
                        'SELECT id_webservice_account FROM ' . _DB_PREFIX_ . 'webservice_permission WHERE id_webservice_account="' . $id_webservice . '"'
                    );
                if (empty($row_webservice_permission)) {
                    // assign webservice permissions
                    Db::getInstance()->insert(
                        'webservice_permission',
                        array(
                            'id_webservice_account' => $id_webservice,
                            'resource' => 'egoi',
                            'method' => "GET",
                        )
                    );
                }

                return true;
            }
        } catch (Exception $e) {
            PrestaShopLogger::addLog("[EGOI-PS8]::".__CLASS__."::".__FUNCTION__."::LINE::".__LINE__."::ERROR: {$e->getMessage()}");
        }

		return false;
	}

	/**
	 * Uninstall WebService Overrides
	 *
	 * @return bool
	 */
	public function uninstallWebService()
	{
		$row = Db::getInstance()
					->getRow('SELECT id_webservice_account FROM '._DB_PREFIX_.'webservice_account WHERE description="E-goi"');
		if(!empty($row)) {
			$qry_webservice = 'id_webservice_account="'.$row['id_webservice_account'].'"';
			Db::getInstance()->delete('webservice_account', $qry_webservice);

			// remove webservice from Shop
			Db::getInstance()->delete('webservice_account_shop', $qry_webservice);

			// remove webservice permissions
			Db::getInstance()->delete('webservice_permission', $qry_webservice);
			return true;
		}

		return false;
	}

	/**
	 * Filter Request data from Configuration page
	 *
	 * @return string
	 */
	public function getContent()
	{

	    if (Tools::isSubmit('submit_api_key')) {

	    	$api_key = Tools::getValue('smart_api_key');

	    	if (!$api_key)
                $this->error_msg = $this->displayError($this->l('Indicate correct API key.'));

	        if (!sizeof($this->_errors)) {
                Configuration::updateValue('smart_api_key', ($api_key));
                $this->transactionalApi = new TransactionalApi();
                $this->transactionalApi->enableClient();
                $this->success_msg = $this->displayConfirmation($this->l('API Key saved and updated'));
            }
	    }
	    return $this->displayForm();
	}

	/**
	 * Show Configuration Form
	 *
	 * @return mixed
	 */
	public function displayForm()
	{
		//add headers
		$this->context->controller->addJS($this->_path. 'views/js/config.js');
		$this->context->controller->addCSS($this->_path. 'views/css/main.css');

		// assign vars
        $this->assign($this->success_msg, 'success_msg');
        $this->assign($this->error_msg, 'error_msg');
        $this->assign(Configuration::get('smart_api_key') ? false : true, 'smart_api_key_error');

	    return $this->display($this->name, 'views/templates/admin/config.tpl');
	}

	/**
	 * Validate Api Key
	 *
	 * @return void
	 */
	public function validateApiKey()
	{
		if(!empty(Tools::getValue("api_key"))) {
            $this->apiv3->setApiKey(Tools::getValue("api_key"));
            $cache_id = 'getMyAccount::'.$this->apiv3->getApiKey();

            if (!Cache::isStored($cache_id)) {
                $clientData = $this->apiv3->getMyAccount();
                Cache::store($cache_id, $clientData);
            }

            $clientData = Cache::retrieve($cache_id);

            if (!empty($clientData["general_info"]["client_id"])) {
                echo json_encode($clientData);
                exit;
            } else {
                header('HTTP/1.1 403 Forbidden');
                exit;
            }
		}
	}

    public function hookactionNewsletterRegistrationAfter($params)
    {
        return $this->addNewsletterCustomer($params);
    }

	/**
    * Hook for Add customer
    *
    * @param array $params
    * @return bool
    */
    public function hookActionObjectCustomerAddAfter($params)
    {
        return $this->addCustomer($params);
	}

	/**
	 * Hook for Update customer
	 *
	 * @param array $params
	 * @return bool
	 */
	public function hookActionObjectCustomerUpdateAfter($params)
	{
		return $this->updateCustomer($params);
	}

	/**
	 * Hook for Delete customer
	 *
	 * @param array $params
	 * @return bool
	 */
	public function hookActionObjectCustomerDeleteAfter($params)
	{
		return $this->deleteCustomer($params);
	}

    /**
     * Hook for triggering reminders
     *
     * @param $params
     */
    public function hookActionDispatcher($params)
    {
        $timeSaved = Configuration::get(self::ACTION_CRON_TIME_CONFIGURATION);
        if (empty($timeSaved)){
            Configuration::updateValue(self::ACTION_CRON_TIME_CONFIGURATION, time());
            return;
        }
        if(time() - $timeSaved < 60){
            return;
        }
        $this->triggerReminders();
        $this->triggerCellphoneSync();
        Configuration::updateValue(self::ACTION_CRON_TIME_CONFIGURATION, time());
    }

    /**
     * Hook for updating order status
     *
     * @param array $params
     *
     * @return bool
     */
	public function hookActionOrderStatusPostUpdate($params)
    {
        //$this->syncOrderTE($params);
        $this->syncOrderAPI($params);
        return $this->sendSmsNotification($params);
    }

    /**
     * Hook for category update
     *
     * @param $params
     */
    public function hookActionObjectCategoryUpdateAfter($params)
    {
        $this->updateCategories();
    }

    /**
     * Hook for category removal
     *
     * @param $params
     */
    public function hookActionObjectCategoryDeleteAfter($params)
    {
        $this->updateCategories();
    }

    private function updateCategories()
    {
        $catalogsEnabled = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "egoi_active_catalogs ORDER BY catalog_id DESC");
        if(!empty($catalogsEnabled)) {
            Configuration::updateValue('egoi_import_categories', true);
        }
    }


    /**
     * Hook for product create
     *
     * @param array $params
     */
    public function hookActionObjectProductAddAfter($params)
    {
        $product = $params['object'];

        if ($product->active) {
            $languages = Language::getLanguages(true, Context::getContext()->shop->id);
            $currencies = Currency::getCurrencies(true);
            $catalogsEnabled = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "egoi_active_catalogs ORDER BY catalog_id DESC");
            foreach ($catalogsEnabled as $catalog) {
                if (!$this->checkLangCurrency($languages, $langId, $currencies, $currencyId, $catalog) || !$catalog['active']) {
                    continue;
                }

                $selectedCatalog = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "egoi_active_catalogs WHERE catalog_id=".$catalog['catalog_id']);

                $data = static::mapProduct($product, $langId, $currencyId, !empty($selectedCatalog[0]["sync_descriptions"]), !empty($selectedCatalog[0]["sync_categories"]), !empty($selectedCatalog[0]["sync_related_products"]));

                $result = $this->apiv3->createProduct($catalog['catalog_id'], $data);

                if (!empty($result['errors']['product_already_exists'])) {
                    $id = $data['product_identifier'];
                    unset($data['product_identifier']);

                   $this->apiv3->updateProduct($catalog['catalog_id'], $id, $data);

                }
            }
        }
    }

    /**
     * Hook for product update
     *
     * @param array $params
     */
    public function hookActionObjectProductUpdateAfter($params)
    {
        return $this->hookActionObjectProductAddAfter($params);
    }


    /**
     * Hook for product update
     *
     * @param array $params
     */
    public function hookActionObjectSpecificPriceAddAfter($params)
    {

        $specialPrice = $params['object'];
        $productId = $specialPrice->id_product;

        $langId = $this->context->language->iso_code;

        $product = new Product($productId, false, $langId);

        if (!empty($product) && $product->active) {
            $languages = Language::getLanguages(true, Context::getContext()->shop->id);
            $currencies = Currency::getCurrencies(true);
            $catalogsEnabled = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "egoi_active_catalogs ORDER BY catalog_id DESC");
            foreach ($catalogsEnabled as $catalog) {
                if (!$this->checkLangCurrency($languages, $langId, $currencies, $currencyId, $catalog) || !$catalog['active']) {
                    continue;
                }

                $selectedCatalog = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "egoi_active_catalogs WHERE catalog_id=".$catalog['catalog_id']);

                $data = static::mapProduct($product, $langId, $currencyId, !empty($selectedCatalog[0]["sync_descriptions"]), !empty($selectedCatalog[0]["sync_categories"]), !empty($selectedCatalog[0]["sync_related_products"]));

                $result = $this->apiv3->createProduct($catalog['catalog_id'], $data);

                if (!empty($result['errors']['product_already_exists'])) {
                    $id = $data['product_identifier'];
                    unset($data['product_identifier']);
                    $this->apiv3->updateProduct($catalog['catalog_id'], $id, $data);

                }
            }
        }
    }

    public function hookActionObjectSpecificPriceDeleteAfter($params)
    {
        return $this->hookActionObjectSpecificPriceAddAfter($params);
    }

    public function hookActionObjectSpecificPriceUpdateAfter($params)
    {
        return $this->hookActionObjectSpecificPriceAddAfter($params);
    }

    /**
     * Hook for product add
     *
     * @param array $params
     */
    public function hookActionObjectProductDeleteAfter($params)
    {
        $product = $params['object'];
        if ($product->active) {
            $languages = Language::getLanguages(true, Context::getContext()->shop->id);
            $currencies = Currency::getCurrencies(true);
            $catalogsEnabled = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "egoi_active_catalogs ORDER BY catalog_id DESC");
            foreach ($catalogsEnabled as $catalog) {
                if (!$this->checkLangCurrency($languages, $langId, $currencies, $currencyId, $catalog) || !$catalog['active']) {
                    continue;
                }

                $this->apiv3->deleteProduct($catalog['catalog_id'], $product->id);
            }
        }
    }

    private function checkLangCurrency($languages, &$langId, $currencies, &$currencyId, $catalog)
    {
        $langId = 0;
        foreach ($languages as $language) {
            if ($language['iso_code'] === strtolower($catalog['language'])) {
                $langId = $language['id_lang'];
            }
        }
        if ($langId === 0) {
            return false;
        }

        $currencyId = 0;
        foreach ($currencies as $currency) {
            if ($currency->iso_code === $catalog['currency']) {
                $currencyId = $currency->id;
            }
        }
        if ($currencyId === 0) {
            return false;
        }

        return true;
    }

    public static function mapProduct($product, $lang, $currency, $sync_descriptions = true, $sync_categories = true, $sync_related_products = true)
    {
        $desc = '';
        $categories = array();
        $relatedProducts = array();

        if (is_array($product)) {
            $product = new Product($product['id_product'], true, $lang);
        } else {
            $product = new Product($product->id, true, $lang);
        }

        $link = new Link();

        if(!empty($sync_descriptions)) {
            $desc = empty($product->description_short) ? filter_var(substr($product->description,0,800), FILTER_SANITIZE_STRING) : filter_var($product->description_short, FILTER_SANITIZE_STRING);
        }

        $price = Product::getPriceStatic($product->id, true, null, 2, ',', false, false);
        $salePrice = Product::getPriceStatic($product->id, true, null, 2, ',', false, true);

        if ($price == $salePrice) {
            $salePrice = 0;
        }

        $url = $link->getProductLink($product, null, null, null, $lang, null);
        if (strpos($url, '?') !== false) {
            $concatChar = '&';
        } else {
            $concatChar = '?';
        }

        $url = $link->getProductLink($product, null, null, null, $lang, null) . $concatChar . 'SubmitCurrency=1&id_currency=' . $currency;

        $img = $product->getCover($product->id);
        $ssl = empty($_SERVER['HTTPS']) ? 'http://' : 'https://';
        $imageUrl = $ssl . $link->getImageLink(isset($product->link_rewrite) ? $product->link_rewrite : $product->name, (int)$img['id_image'], 'home_default');

        if(!empty($sync_categories)) {
            $categories = static::buildBreadcrumbs($product->getCategories(), $lang);
        }

        if(!empty($sync_related_products)) {
            $acessories = Product::getAccessoriesLight($lang, $product->id);
            foreach ($acessories as $item) {
                $relatedProducts[] = $item['id_product'];
            }
        }

        return array(
            'product_identifier' => $product->id,
            'name' => $product->name,
            'description' => $desc,
            'sku' => $product->reference,
            'upc' => $product->upc,
            'ean' => $product->ean13,
            'link' => $url,
            'image_link' => $imageUrl,
            'price' => $price,
            'sale_price' => $salePrice,
            'brand' => $product->manufacturer_name,
            'categories' => $categories,
            'related_products' => $relatedProducts
        );
    }

    private static function buildBreadcrumbs($categories, $lang)
    {
        $result = array();
        if(!empty($categories))
        foreach ($categories as $category) {
            $category = new Category($category, $lang);
            if(!empty($category->name)) {
                $breadcrumb = $category->name;
                while ($category->id_parent > '1') {
                    $category = new Category($category->id_parent, $lang);
                    $breadcrumb = $category->name . '>' . $breadcrumb;
                }
                $result[] = $breadcrumb;
            }
        }

        return $result;
    }


    /**
     * needed because customer's cellphone is in address and this is
     * created after user
     */
    private function triggerCellphoneSync(){

        $timeSaved = Configuration::get(self::ADDRESS_CRON_TIME_CONFIGURATION);
        if(empty($timeSaved) || ( $timeSaved + (10 * 60) ) > time() ){//working after first sync && in 10 min intervals
            return;
        }

        $res = self::getClientData();

        $list_id            = $res['list_id'];
        $newsletter_sync    = $res['newsletter_sync'];
        $roleSync = $res['role'];
        $store_id = Tools::getValue("store_id");
        if(empty($store_id)) {
            $store_id = (int)Context::getContext()->shop->id;
        }
        if(!empty($store_id)){
            $store_filter = ' AND '._DB_PREFIX_.'customer.id_shop="'.$store_id.'" ';
        }else{
            $store_filter = '';
        }


        $ts = [];
        if(!empty($store_id) && !empty($store_name)){
            array_push($ts, $store_name);
        }

        $add='';
        if(!empty($newsletter_sync) && $newsletter_sync == '1'){
            $add = ' AND '._DB_PREFIX_.'customer.newsletter="1" ';
            array_push($ts, 'newsletter');
        }

        $sqlc = 'SELECT '._DB_PREFIX_.'customer.id_customer, email, '._DB_PREFIX_.'customer.firstname, '._DB_PREFIX_.'customer.lastname, birthday, '._DB_PREFIX_.'customer.newsletter, '._DB_PREFIX_.'customer.optin, id_shop, id_lang, phone, phone_mobile, call_prefix FROM '._DB_PREFIX_.'customer LEFT JOIN '._DB_PREFIX_.'address ON '._DB_PREFIX_.'customer.id_customer = '._DB_PREFIX_.'address.id_customer LEFT JOIN '._DB_PREFIX_.'country ON '._DB_PREFIX_.'country.id_country = '._DB_PREFIX_.'address.id_country WHERE '._DB_PREFIX_.'customer.active="1" AND '._DB_PREFIX_.'address.date_upd >= "'. date('Y-m-d H:i:s', $timeSaved) .'" OR '._DB_PREFIX_.'address.date_add >= "'. date('Y-m-d H:i:s', $timeSaved) .'"'.$add.$store_filter.' GROUP BY '._DB_PREFIX_.'customer.id_customer';
        $getcs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sqlc);


        if(empty($getcs)){
            return;
        }

        $groups = Group::getGroups(Context::getContext()->language->id, true);

        $data = [];
        $allFields = $this->getMappedFields();

        foreach($getcs as $row){
            $row['roles'] = '';
            $customergroups = Customer::getGroupsStatic((int)$row['id_customer']);

            if(!empty($roleSync)) {
                if(!in_array($roleSync, $customergroups)) {
                    continue; // sync only this group
                }
            }

            if(!empty($customergroups) && !empty($groups)) {
                $roles = [];
                foreach ($customergroups as $customergroup) {
                    if(!empty($customergroup)) {
                        foreach ($groups as $r) {
                            if($r['id_group'] == $customergroup) {
                                $roles[] = $r['name'];
                            }
                        }
                    }
                }
            }

            if(!empty($roles)) {
                $row['roles'] = implode(', ', $roles);
            }
            $data[] = SmartMarketingPs::mapSubscriber($row, $allFields);
        }

        if(!empty($data)){
            if(count($data) == 1) {
                $data = $data[0];
                if(!empty($data['base']['email'])) {
                    $uid = Db::getInstance()->getValue('SELECT uid FROM '._DB_PREFIX_."egoi_customer_uid WHERE email='".pSQL($data['base']['email'])."';");
                    if(empty($uid)) {
                        $uid = $this->apiv3->searchContactByEmail($data['base']['email'], $list_id);
                        if(!empty($uid)) {
                            Db::getInstance()->insert('egoi_customer_uid', array(
                                'uid' => $uid,
                                'email' => pSQL($data['base']['email'])
                            ));
                            $this->apiv3->patchContact($list_id, $uid, $data);
                        } else {
                            $contact = $this->apiv3->createContact($list_id, $data);
                            if(!empty($contact['contact_id'])) {
                                Db::getInstance()->insert('egoi_customer_uid', array(
                                    'uid' => $contact['contact_id'],
                                    'email' => pSQL($data['base']['email'])
                                ));
                            }
                        }
                    }
                }
            } else {
                $importContacts = [
                    'mode' => 'update',
                    'compare_field' => 'email',
                    'contacts' => $data,
                    'force_empty' => true,
                ];
                $this->apiv3->addSubscriberBulk($list_id, $importContacts);
            }
        }

        Configuration::updateValue(self::ADDRESS_CRON_TIME_CONFIGURATION, time());

    }


    /**
     * Triggers reminders
     */
    private function triggerReminders()
    {
        //Server time. Perhaps retrieve it from store?
        $time = time();
        $hour = $time / 3600 % 24;

        if ($hour > self::LIMIT_HOUR_MIN && $hour < self::LIMIT_HOUR_MAX) {
            $reminders = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "egoi_sms_notif_order_reminder");
            foreach ($reminders as $reminder) {
                if ($time >= $reminder['send_date']) {
                    $this->sendReminder($reminder['mobile'], $reminder['message'], $reminder['order_id']);
                }
            }
        }
    }

    /**
     * Sends a reminder and removes its entry from database
     *
     * @param $mobile
     * @param $message
     * @param $orderId
     */
    private function sendReminder($mobile, $message, $orderId)
    {
        if (Configuration::hasKey(self::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION)) {
            $senderId = Configuration::get(self::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION);
            $this->transactionalApi->sendSms($mobile, $senderId, $message, true);
        } else{
            $senderHash = Configuration::get(self::SMS_NOTIFICATIONS_SENDER_CONFIGURATION);
            $this->transactionalApi->sendSms($mobile, $senderHash, $message);
        }

        $this->deleteReminder($orderId);
    }

    /**
     * Deletes a reminder
     *
     * @param $orderId
     */
    private function deleteReminder($orderId)
    {
        $orderId = pSQL($orderId);
        Db::getInstance()->delete('egoi_sms_notif_order_reminder', "order_id=$orderId");
    }

    /**
     * Sends sms notification
     *
     * @param  $params
     * @return bool
     */
    private function sendSmsNotification($params)
    {
        if (!Configuration::hasKey(self::SMS_NOTIFICATIONS_SENDER_CONFIGURATION) && !Configuration::hasKey(self::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION)) {
            return false;
        }

        $order = new Order($params['id_order']);
        $newOrderStatus = $params['newOrderStatus'];
        $smsNotif = Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."egoi_sms_notif_order_status WHERE order_status_id=".pSQL($newOrderStatus->id));
        $send = $smsNotif['send_client'] == 1;
        $this->sendClient($newOrderStatus, $order, $send);

        $admin = Configuration::get(self::SMS_NOTIFICATIONS_ADMINISTRATOR_CONFIGURATION);
        if (!empty($admin) && $smsNotif['send_admin'] == 1) {
            return $this->sendAdmin($params['newOrderStatus'], $order, $admin);
        }

        return true;
    }

    /**
     * Sends client message
     *
     * @param $newOrderStatus
     * @param $order
     * @param $send
     *
     * @return bool
     */
    private function sendClient($newOrderStatus, $order, $send)
    {
        if (Configuration::hasKey(self::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION)) {
            $senderId = Configuration::get(self::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION);
        } else{
            $senderHash = Configuration::get(self::SMS_NOTIFICATIONS_SENDER_CONFIGURATION);
        }

        $messages = $this->getNotifMessages($newOrderStatus->id, $order->id_lang);
        $message = $this->parseMessage($messages['client_message'], $newOrderStatus, $order);
        if ($send && empty($message)) {
            return false;
        }

        $addresses = array();
        if (!empty(Configuration::get(self::SMS_NOTIFICATIONS_DELIVERY_ADDRESS_CONFIGURATION))) {
            array_push($addresses, new Address($order->id_address_delivery));
        }
        if (!empty(Configuration::get(self::SMS_NOTIFICATIONS_INVOICE_ADDRESS_CONFIGURATION))) {
            array_push($addresses, new Address($order->id_address_invoice));
        }

        $sent = array();
        foreach ($addresses as $address) {
            $mobile = $this->getPhoneFromOrder($address);
            if (!$mobile) {
                return false;
            }

            $country = new Country($address->id_country);
            $mobile = $country->call_prefix . '-' . $mobile;
            if (isset($sent[$mobile])) {
                continue;
            }

            $sent[$mobile] = 1;
            if ($send) {
                isset($senderId) ? $this->transactionalApi->sendSms($mobile, $senderId, $message, true) : $this->transactionalApi->sendSms($mobile, $senderHash, $message);
            }

            $this->reminder($order, $newOrderStatus, $mobile);
        }

        return true;
    }

    /**
     * Sends admin message
     *
     * @param $newOrderStatus
     * @param $order
     * @param $admin
     *
     * @return bool
     */
    private function sendAdmin($newOrderStatus, $order, $admin)
    {
        if (Configuration::hasKey(self::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION)) {
            $senderId = Configuration::get(self::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION);
        } else{
            $senderHash = Configuration::get(self::SMS_NOTIFICATIONS_SENDER_CONFIGURATION);
        }

        $messages = empty($messages) ? $this->getNotifMessages($newOrderStatus->id, $order->id_lang) : $messages;
        $message = $this->parseMessage($messages['admin_message'], $newOrderStatus, $order);
        if (empty($message)) {
            return false;
        }

        $mobile = Configuration::get(self::SMS_NOTIFICATIONS_ADMINISTRATOR_PREFIX_CONFIGURATION) . '-' . $admin;
        isset($senderId) ? $this->transactionalApi->sendSms($mobile, $senderId, $message, true) : $this->transactionalApi->sendSms($mobile, $senderHash, $message);

        return true;
    }

    /**
     * Get SMS notification messages
     *
     * @param $orderStatusId
     * @param $langId
     *
     * @return mixed
     */
    private function getNotifMessages($orderStatusId, $langId)
    {
        return Db::getInstance()->getRow("SELECT client_message,admin_message FROM "._DB_PREFIX_.
            "egoi_sms_notif_messages WHERE order_status_id=" .pSQL($orderStatusId). " AND lang_id=".pSQL($langId)
        );
    }

    /**
     * Parses custom information and returns the real message
     *
     * @param $message
     * @param $orderStatus
     * @param $order
     *
     * @return mixed
     */
    private function parseMessage($message, $orderStatus, $order)
    {
        $currency = new Currency($order->id_currency);
        $mb = $this->getMbData($order, $orderStatus);
        $customer = new Customer($order->id_customer);
        $carrier = new Carrier($order->id_carrier);

        return str_replace(
            [
                self::CUSTOM_INFO_ORDER_REFERENCE,
                self::CUSTOM_INFO_ORDER_STATUS,
                self::CUSTOM_INFO_TOTAL_COST,
                self::CUSTOM_INFO_CURRENCY,
                self::CUSTOM_INFO_ENTITY,
                self::CUSTOM_INFO_MB_REFERENCE,
                self::CUSTOM_INFO_SHOP_NAME,
                self::CUSTOM_INFO_BILLING_NAME,
                self::CUSTOM_INFO_CARRIER,
                self::CUSTOM_INFO_TRACKING_URL,
                self::CUSTOM_INFO_TRACKING_NUMBER
            ],
            [
                $order->reference,
                $orderStatus->name,
                number_format($mb['total_cost'], 2, '.', ''),
                $currency->sign,
                $mb['entity'],
                $mb['reference'],
                Configuration::get('PS_SHOP_NAME'),
                $customer->firstname . ' ' . $customer->lastname,
                $carrier->name,
                str_replace('@', $order->shipping_number, $carrier->url),
                $order->shipping_number
            ],
            $message
        );
    }

    /**
     * Returns the payment module key from order
     *
     * @param $orderState
     *
     * @return bool|string
     */
    public static function getPaymentModule($orderState)
    {
        if (self::isPaymentIfthenPay($orderState)) {
            return self::PAYMENT_MODULE_IFTHENPAY;
        }

        if (self::isPaymentEuPago($orderState)) {
            return self::PAYMENT_MODULE_EUPAGO;
        }

        return false;
    }

    /**
     * Checks if payment is from ifThenPay module
     *
     * @param $orderState
     *
     * @return bool
     */
    private static function isPaymentIfthenPay($orderState)
    {
        $orderState = (array)$orderState;
        if ($orderState['template'] == 'multibanco' || ($orderState['module_name'] == 'ifthenpay' && stripos($orderState['name'], "Multibanco") !== false) ) {
            return true;
        }

        return false;
    }

    /**
     * Checks if payment is from euPago module
     *
     * @param $orderState
     *
     * @return bool
     */
    private static function isPaymentEuPago($orderState)
    {
        $search = 'euPago';
        $orderState = (array)$orderState;

        if (substr($orderState['name'], 0, strlen($search)) === $search && $orderState['template'] == '') {
            return true;
        }

        return false;
    }

    /**
     * Handles reminders
     *
     * @param $order
     * @param $orderState
     * @param $mobile
     */
    private function reminder($order, $orderState, $mobile)
    {

        if (self::getPaymentModule($orderState)) {
            $message = $this->getReminderMessage($orderState->id, $order->id_lang)['message'];
            if (empty($message)) {
                return;
            }

            $message = $this->parseMessage($message, $orderState, $order);
            
            if (!empty($message)) {
                Db::getInstance()->insert(
                    'egoi_sms_notif_order_reminder',
                    array(
                        'order_id' => pSQL($order->id),
                        'mobile' => pSQL($mobile),
                        'send_date' => time() + Configuration::get(self::SMS_REMINDER_DEFAULT_TIME_CONFIG) * 3600,
                        'message' => pSQL($message)
                    )
                );
            }
        } else {
            $this->deleteReminder($order->id);
        }
    }

    /**
     * Get SMS reminder message
     *
     * @param $orderStatusId
     * @param $langId
     *
     * @return mixed
     */
    private function getReminderMessage($orderStatusId, $langId)
    {
        return Db::getInstance()->getRow("SELECT message FROM "._DB_PREFIX_.
            "egoi_sms_notif_reminder_messages WHERE lang_id=".pSQL($langId)
            ." AND active=1"
        );
    }

    /**
     * Gets phone or cellphone from order
     *
     * @param $address
     *
     * @return bool
     */
    private function getPhoneFromOrder($address)
    {
        if (!empty($address->phone_mobile)) {
            return $address->phone_mobile;
        } elseif (!empty($address->phone)) {
            return $address->phone;
        }

        return false;
    }

    /**
     * Get mb data
     *
     * @param $order
     * @param $orderState
     *
     * @return array
     */
    private function getMbData($order, $orderState)
    {
        $data = array('entity' => '', 'reference' => '', 'total_cost' => $order->total_paid);
        $paymentModule = self::getPaymentModule($orderState);
        switch ($paymentModule) {
            case 'multibanco':
                $data = $this->getIfThenPayData($order);
                break;
            case 'eupagomb':
                $data = $this->getEuPagoData($order);
                break;
            default:
        }

        return $data;
    }

    /**
     * Get IfThenPay data
     *
     * @param $order
     *
     * @return array
     */
    private function getIfThenPayData($order)
    {

        $total = $order->getOrdersTotalPaid();
        if(!empty($total) && is_numeric($total)){
            $total = sprintf('%0.2f', $total);
        }

        if($order->module == 'ifthenpay'){//check ifthenpay module
            $details = (array) unserialize(Configuration::get('IFTHENPAY_USER_ACCOUNT'));
            $ent = $details[0]['Entidade'];
            $ref = $this->IfThenPayGenerateMbRef($ent, $details[0]['SubEntidade'][0], $order->id, $total);
        }else{
            $module = Module::getInstanceByName('multibanco');
            $details = $module->getMBDetails();
            $ent = $details[0];
            $ref = $module->GenerateMbRef($details[0], $details[1], $order->id, $total);
        }
        return $this->buildMbArray($ent, $ref, $total, $order->id_currency);
    }


    /**
     * IFTHENPAY
     * @param $ent_id
     * @param $subent_id
     * @param $order_id
     * @param $order_value
     * @return string
     */
    private function IfThenPayGenerateMbRef($ent_id, $subent_id, $order_id, $order_value)
    {

        $order_id ="0000".$order_id;

        $order_value =  $this->format_number($order_value);

        //Apenas sao considerados os 4 caracteres mais a direita do order_id
        $order_id = substr($order_id, (strlen($order_id) - 4), strlen($order_id));


        while ($order_value >= 1000000){
            $this->IfThenPayGenerateMbRef($order_id++, 999999.99);
            $order_value -= 999999.99;
        }

        //cálculo dos check digits
        $chk_str = sprintf('%05u%03u%04u%08u', $ent_id, $subent_id, $order_id, round($order_value*100));

        $chk_array = array(3, 30, 9, 90, 27, 76, 81, 34, 49, 5, 50, 15, 53, 45, 62, 38, 89, 17, 73, 51);

        $chk_val=0;

        for ($i = 0; $i < 20; $i++)
        {
            $chk_int = substr($chk_str, 19-$i, 1);
            $chk_val += ($chk_int%10)*$chk_array[$i];
        }

        $chk_val %= 97;

        $chk_digits = sprintf('%02u', 98-$chk_val);

        return $subent_id." ".substr($chk_str, 8, 3)." ".substr($chk_str, 11, 1).$chk_digits;

    }


    /**
     * IFTHENPAY number format
     * @param $number
     * @return string
     */
    private function format_number($number)
    {
        $verifySepDecimal = number_format(99,2);

        $valorTmp = $number;

        $sepDecimal = substr($verifySepDecimal, 2, 1);

        $hasSepDecimal = True;

        $i=(strlen($valorTmp)-1);

        for($i;$i!=0;$i-=1)
        {
            if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==","){
                $hasSepDecimal = True;
                $valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
                break;
            }
        }

        if($hasSepDecimal!=True){
            $valorTmp=number_format($valorTmp,2);

            $i=(strlen($valorTmp)-1);

            for($i;$i!=1;$i--)
            {
                if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==","){
                    $hasSepDecimal = True;
                    $valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
                    break;
                }
            }
        }

        for($i=1;$i!=(strlen($valorTmp)-1);$i++)
        {
            if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)=="," || substr($valorTmp,$i,1)==" "){
                $valorTmp = trim(substr($valorTmp,0,$i)).trim(substr($valorTmp,1+$i));
                break;
            }
        }

        if (strlen(strstr($valorTmp,'@'))>0){
            $valorTmp = trim(substr($valorTmp,0,strpos($valorTmp,'@'))).trim($sepDecimal).trim(substr($valorTmp,strpos($valorTmp,'@')+1));
        }

        return $valorTmp;
    }

    /**
     * Get EuPago data
     *
     * @param $order
     *
     * @return array
     */
    private function getEuPagoData($order)
    {
        $module = Module::getInstanceByName('eupagomb');

        if(!$module){
            $module = Module::getInstanceByName('eupago_multibanco');
        }

        $result = $module->GenerateReference((int)$order->id, $order->total_paid);

        $entity = '';
        $ref = '';
        $total = $order->total_paid;
        if ($result->estado == 0) {
            $entity = $result->entidade;
            $ref = $result->referencia;
            $total = $result->valor;
        }

        return $this->buildMbArray($entity, $ref, $total, $order->id_currency);
    }

    /**
     * Returns MB data
     *
     * @param $entity
     * @param $ref
     * @param $total
     * @param $currency
     *
     * @return array
     */
    private function buildMbArray($entity, $ref, $total, $currency)
    {
        $data = array();
        $data['entity'] = $entity;
        $data['reference'] = $ref;
        $data['total_cost'] = $total;

        return $data;
    }


    /**
     * @param $params
     * @return void
     */
    protected function addNewsletterCustomer($params) {

        $fields = array(
            'email' => $params['email'],
            'newsletter' => 1
        );

        $options = self::getClientData();

        if(!empty($options['newsletter_sync'])) {
            $allFields = $this->getMappedFields();
            $data = SmartMarketingPs::mapSubscriber($fields, $allFields);

            $contact = $this->apiv3->createContact($options['list_id'], $data);
            if(!empty($contact['contact_id'])) {
                $this->context->cookie->__set('egoi_uid', $contact['contact_id']);
                $this->context->cookie->write();
                Db::getInstance()->insert('egoi_customer_uid', array(
                    'uid' => $contact['contact_id'],
                    'email' => pSQL($params['email'])
                ));
            }

            $client = $options['client_id'];

            return Db::getInstance()->update(
                'egoi',
                array(
                    'total' => (int)$options['total'] + 1
                ),
                "client_id = $client"
            );
        }

        return true;
    }

	/**
	 * Add customer
	 *
	 * @param  $params
	 * @return bool
	 */
	protected function addCustomer($params)
	{

        $params = json_decode(json_encode ( $params ) , true);

        if(empty($params)) {
            return false;
        }

        $params = $params['object'];
        $options = self::getClientData();
        $roleSync = $options['role'];

        if($options['sync']) {
            // check if is a role defined
            if (!empty($roleSync) && !$this->getRole($params['id'], $roleSync)) {
                return false;
            }
            $allFields = $this->getMappedFields();
            $customergroups = Customer::getGroupsStatic((int)$params['id']);
            $groups = Group::getGroups(Context::getContext()->language->id, true);

            if(!empty($customergroups) && !empty($groups)) {
                $roles = [];
                foreach ($customergroups as $customergroup) {
                    if(!empty($customergroup)) {
                        foreach ($groups as $r) {
                            if($r['id_group'] == $customergroup) {
                                $roles[] = $r['name'];
                            }
                        }
                    }
                }
            }

            if(!empty($roles)) {
                $params['roles'] = implode(', ', $roles);
            }

            $data = SmartMarketingPs::mapSubscriber($params, $allFields);

            $contact = $this->apiv3->createContact($options['list_id'], $data);
            if(!empty($contact['contact_id'])) {
                $this->context->cookie->__set('egoi_uid', $contact['contact_id']);
                $this->context->cookie->write();
                Db::getInstance()->insert('egoi_customer_uid', array(
                    'uid' => $contact['contact_id'],
                    'email' => pSQL($params['email'])
                ));
            }

            $client = $options['client_id'];

            return Db::getInstance()->update(
                'egoi',
                array(
                    'total' => (int)$options['total'] + 1
                ),
                "client_id = $client"
            );
        }
        return true;
	}

    public static function getShopsName($id){
        try{

            $query = 'SELECT * FROM '._DB_PREFIX_.'shop where id_shop = '.$id;
            if (!Cache::isStored($query)) {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                if(empty($result[0]['name'])) {
                    return false;
                }
                Cache::store($query, $result[0]['name']);
            }
            return Cache::retrieve($query);

        }catch (Exception $e){
            return false;
        }
    }

    /*
     * Count size of list by store
     * */
    public static function sizeList($newsletter = false){

        // if it comes from synchronize newsletter, set it to true
        if($newsletter == true){
            $sql = 'SELECT COUNT(*) as total, id_shop FROM '._DB_PREFIX_.'emailsubscription WHERE active="1" group by id_shop';//AND newsletter="1"
        }
        else{
            $sql = 'SELECT COUNT(*) as total, id_shop FROM '._DB_PREFIX_.'customer WHERE active="1" group by id_shop';//AND newsletter="1"

        }

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /*
     * Count size of list of orders by store
     */
    public static function sizeListOrders()
    {
        $sql = 'SELECT COUNT(*) as total, id_shop FROM '. _DB_PREFIX_ . 'orders WHERE current_state != 0 GROUP BY id_shop';

        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }


    /**
	 * Update customer
	 *
     * @param array $params
	 * @return mixed
	 */
    protected function updateCustomer($params)
    {
        $params = json_decode(json_encode($params), true);

        if (empty($params)) {
            return false;
        }

        $params = $params['object'];
        $options = self::getClientData();
        $roleSync = $options['role'];

        if ($options['sync'] && !empty($params['id'])) {
            // check if is a role defined
            if (!empty($roleSync) && !$this->getRole($params['id'], $roleSync)) {
                return false;
            }

            $uid = Db::getInstance()->getValue(
                'SELECT uid FROM ' . _DB_PREFIX_ . "egoi_customer_uid WHERE email='" . pSQL($params['email']) . "';"
            );
            $allFields = $this->getMappedFields();
            $customergroups = Customer::getGroupsStatic((int)$params['id']);
            $groups = Group::getGroups(Context::getContext()->language->id, true);

            if (!empty($customergroups) && !empty($groups)) {
                $roles = [];
                foreach ($customergroups as $customergroup) {
                    if (!empty($customergroup)) {
                        foreach ($groups as $r) {
                            if ($r['id_group'] == $customergroup) {
                                $roles[] = $r['name'];
                            }
                        }
                    }
                }
            }

            if (!empty($roles)) {
                $params['roles'] = implode(', ', $roles);
            }

            $data = SmartMarketingPs::mapSubscriber($params, $allFields);

            if (empty($uid)) {
                $uid = $this->apiv3->searchContactByEmail($params['email'], $options['list_id']);

                if (!empty($uid)) {
                    Db::getInstance()->insert('egoi_customer_uid', array(
                        'uid' => $uid,
                        'email' => pSQL($params['email'])
                    ));
                    $this->apiv3->patchContact($options['list_id'], $uid, $data);
                } else {
                    $contact = $this->apiv3->createContact($options['list_id'], $data);
                    if (!empty($contact['contact_id'])) {
                        Db::getInstance()->insert('egoi_customer_uid', array(
                            'uid' => $contact['contact_id'],
                            'email' => pSQL($data['base']['email'])
                        ));
                    }
                }
            } else {
                $this->apiv3->patchContact($options['list_id'], $uid, $data);
            }
        }
        return true;
    }

	/**
	 * Delete customer
	 *
     * @param array $params
	 * @return bool
	 */
	protected function deleteCustomer($params)
	{
        $params = json_decode(json_encode ( $params ) , true);

        if(empty($params)) {
            return false;
        }
        $params = $params['object'];

        $options = self::getClientData();
        $client = $options['client_id'];

        if ($options['sync']) {
            // check if is a role defined
            if (!$this->getRole($params['id'], $options['role'])) {
                return false;
            }

            $uid = Db::getInstance()->getValue('SELECT uid FROM '._DB_PREFIX_."egoi_customer_uid WHERE email='".pSQL($params['email'])."';");
            if(empty($uid)) {
                $uid = $this->apiv3->searchContactByEmail($params['email'], $options['list_id']);
                if(!empty($uid)) {
                    $data[] = [
                        'contact_id' => $uid,
                        'unsubscription_method' => 'manual',
                        'unsubscription_reason' => 'other',
                        'unsubscription_observation' => 'Prestashop Customer delete'

                    ];
                    $this->apiv3->unsubscribeContact($options['list_id'], $data);
                }
            } else {
                $data[] = [
                    'contact_id' => $uid,
                    'unsubscription_method' => 'manual',
                    'unsubscription_reason' => 'other',
                    'unsubscription_observation' => 'Prestashop Customer delete'

                ];
                $this->apiv3->unsubscribeContact($options['list_id'], $data);
            }
            return Db::getInstance()->update(
                'egoi',
                array(
                    'total' => (int)($options['total'] - 1)
                ),
                "client_id = $client"
            );
        }
        return true;
	}

    /**
     * Get role from Customer
     *
     * @param $customer_id
     * @param $customer_role
     * @return bool
     */
	protected function getRole($customer_id, $customer_role)
    {
        if ($customer_role) {
            $role = Db::getInstance()
                ->getValue("SELECT COUNT(*) FROM "._DB_PREFIX_."customer_group WHERE id_customer='".(int)$customer_id."' and id_group='".(int)$customer_role."'");
            if (!$role) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return Scripts TPL
     *
     * @param array $params
     * @return mixed
     */
    public function egoiScriptsTPL($params){
        $webPush = null;
        $appCode = Configuration::get(static::WEB_PUSH_APP_CODE);
        include_once 'includes/webPush.php';

        $this->assign(
            array(
                'te' => $this->te(),
                'activate' => 1,
                'wp'=> $webPush
            )
        );

        return $this->display(__FILE__, 'ecommerce/front-scripts.tpl');
    }

	/**
   	 * Hook for display content in Top Page
   	 *
   	 * @param array $params
   	 * @return mixed
   	 */
   	public function hookDisplayTop($params)
   	{
        return $this->egoiScriptsTPL($params);
	}

    /**
     * Hook for display TE sccript, use if theme doesn't call hookDisplayTop naturaly
     *
     * @param array $params
     * @return mixed
     */
    public function hookEgoiDisplayTE($params)
    {
        return $this->egoiScriptsTPL($params);
    }

    /**
     * Hook for adding webservice ressources
     *
     * @param array $params
     * @return mixed
     */
    public function hookAddWebserviceResources($params)
    {
        return [
            'egoi' => [
                'description' => 'Manage Product info in JSON',
                'specific_management' => true
            ]
        ];

    }


	/**
	 * Abandoned cart hook
	 *
	 * @param  array $params
	 * @return void
	 */
	public function hookActionCartSave($params)
	{
		$this->addToCart($params);
	}

	/**
	 * Abandoned cart hook
	 *
	 * @param  array $params
	 * @return void
	 */
	public function hookCart($params)
	{
		$this->addToCart($params);
	}

	/**
	 * Track&Engage - check is customer has an Abandoned Cart
	 *
	 * @return string|bool
	 */
	public function te()
	{
		$res = self::getClientData('track', 1);
		if (!empty($res)) {
			$list_id = $res['list_id'];
			$client = $res['client_id'];
			$track = $res['track'];
            $te = '';

			if($client && $list_id && $track) {
				if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
					return false;
				}

				// set customer email var to use in t&e
				$customer = !$this->context->cookie->__isset('egoi_uid')
                    ? $this->context->cookie->email
                    : $this->context->cookie->__get('egoi_uid');

				$cart_id = $this->context->cookie->id_cart;//$this->getCartId($this->getCustomerId());
				$cart = new Cart($cart_id);

				$products = $cart->getProducts();

                $cs_code = Configuration::get(static::CONNECTED_SITES_CODE);
                if(!empty($cs_code)){
                    $cs_code = base64_decode($cs_code);
                    include 'includes/te_cs.php';
                    $te .= $cs_code;
                }else{//retro compatibility
                    include 'includes/te.php';
                }

			}

            return $te;
		}

		return false;
	}

	/**
	 * Add to Cart
	 *
	 * @param array $params
	 */
	protected function addToCart($params)
	{
	 	$json = isset($params['json']) && is_array($params['json']) ? json_decode($params['json']['id']) : $params['cookie']->id_cart;

	 	$res = self::getClientData('track', 1);
		if (!empty($res)) {
			$list_id = $res['list_id'];
			$track = $res['track'];
			$client = $res['client_id'];
			if($client && $track && $list_id){
				// check if customer has products in the cart (has cart ID?)
				if($json) {
					$idc = $this->getCustomerId();

					if(empty($this->getCartId($idc))) {
						Db::getInstance()->insert('egoi_customers', array(
							'customer' => $idc,
							'id_cart' => (int)$json,
							'estado' => 1
						));
					}
				}
			}
		}
	}

    /**
     * Get all mapped fields
     *
     * @return array
     */
    public function getMappedFields()
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "egoi_map_fields order by id DESC";
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

    /**
     * Get field value map
     *
     * @param $name
     * @param $field
     * @return string
     */
    public static function getFieldMap($name = false, $field = false)
    {
        if ($field) {
            $sql = "SELECT * FROM " . _DB_PREFIX_ . "egoi_map_fields WHERE ps='" . pSQL($field) . "'";
        } else {
            $sql = "SELECT * FROM " . _DB_PREFIX_ . "egoi_map_fields WHERE egoi='" . pSQL($name) . "'";
        }
        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        return $rq['egoi'];
    }

    /*
     * Map subscriber to egoi map
     * */
    public static function mapSubscriber($data, $fields = [])
    {
        $subscriber = [
            'base' => [
                'status' => 'active',
                'first_name' => !empty($data['firstname']) ? $data['firstname'] : '',
                'last_name' => !empty($data['lastname']) ? $data['lastname'] : '',
                'birth_date' => isset($data['birthdate']) ? $data['birthdate'] : '',
                'cellphone' => '',
                'phone' => '',
                'email' => $data['email'],
                'language' => !empty($data['id_lang']) ? Language::getLanguage($data['id_lang'])['iso_code'] : '',
            ],
            'extra' => []
        ];

        if (!empty($data['call_prefix']) && !empty($data['phone_mobile'])) {
            $subscriber['base']['cellphone'] = self::parsePhoneNumber($data['call_prefix'], $data['phone_mobile']);
        }

        if (!empty($data['call_prefix']) && !empty($data['phone'])) {
            $subscriber['base']['phone'] = self::parsePhoneNumber($data['call_prefix'], $data['phone']);
        }

        $idShop = $data['id_shop'];
        $idLang = $data['id_lang'];

        if (!empty($fields) && !empty($idShop) && !empty($idLang)) {
            $fieldsIndex = [];
            foreach ($fields as $field) {
                $fieldsIndex[$field['ps']] = $field;
            }

            foreach ($fieldsIndex as $fieldName => $field) {
                if ($fieldName === 'store_language') {
                    $languageName = self::getLangName($idLang);

                    // Verify if the field is extra or base
                    if (strpos($field['egoi'], 'extra') !== false) {
                        $subscriber['extra'][] = [
                            'field_id' => (int) str_replace('extra_', '', $field['egoi']),
                            'value' => $languageName
                        ];
                    } else {
                        $subscriber['base'][$field['egoi']] = $languageName;
                    }
                }

                if ($fieldName === 'store_name') {
                    $storeName = self::getStoreName($idShop);
                    // Verify if the field is extra or base
                    if (strpos($field['egoi'], 'extra') !== false) {
                        $subscriber['extra'][] = [
                            'field_id' => (int) str_replace('extra_', '', $field['egoi']),
                            'value' => $storeName
                        ];
                    } else {
                        $subscriber['base'][$field['egoi']] = $storeName;
                    }
                }
            }

            foreach ($data as $key => $value) {
                if (!isset($fieldsIndex[$key])) {
                    continue;
                }

                $fieldConfig = $fieldsIndex[$key];
                $field = $fieldConfig['egoi'];

                if (strpos($field, 'extra') !== false) {
                    $subscriber['extra'][] = [
                        'field_id' => (int) str_replace('extra_', '', $field),
                        'value' => $value
                    ];
                } else {
                    if ($field === 'telephone') {
                        $field = 'phone';
                    }

                    if ($field === 'phone' || $field === 'cellphone') {
                        $value = self::parsePhoneNumber($data['call_prefix'], $value);
                    }

                    $subscriber['base'][$field] = $value;
                }
            }
        }
        return $subscriber;
    }

    /**
     * Get Store Name from DB
     *
     * @param $id_shop
     * @return string|null
     */
    private static function getStoreName($id_shop)
    {
        if (empty($id_shop)) {
            return null;
        }

        $shopName = Db::getInstance()->getValue(
            'SELECT name FROM ' . _DB_PREFIX_ . 'shop WHERE id_shop = ' . (int)$id_shop
        );

        return $shopName ?: null;
    }

    /**
     * Get Client Language from DB
     *
     * @param $id_lang
     * @return string|null
     */
    private static function getLangName($id_lang)
    {
        if (empty($id_lang)) {
            return null;
        }

        $langName = Db::getInstance()->getValue(
            'SELECT name FROM ' . _DB_PREFIX_ . 'lang WHERE id_lang = ' . (int)$id_lang
        );

        return $langName ?: null;
    }


    /**
	 * Get Client Data from DB
	 *
     * @param $field
     * @param $val
	 * @return array|null
	 */
	public static function getClientData($field = false, $val = false)
	{
        $query = "SELECT * FROM "._DB_PREFIX_."egoi WHERE client_id != '' order by egoi_id DESC";
		if ($field && $val) {
            $query = "SELECT * FROM "._DB_PREFIX_."egoi WHERE client_id != '' and $field='$val' order by egoi_id DESC";
		}
        if (!Cache::isStored($query)) {
            $return = Db::getInstance(_PS_USE_SQL_SLAVE_)
                ->getRow($query);
            Cache::store($query, $return);
        }
        return Cache::retrieve($query);
	}

    /**
     * Clean Index class from cache for Dev && Production
     *
     * @return void
     */
    private function cleanCache()
    {
        if (file_exists($this->dev_cache)) {
            unlink($this->dev_cache);
        }else{
            @unlink(dirname(__FILE__).'/../../var/cache/dev/class_index.php');
        }

        if (file_exists($this->prod_cache)) {
            unlink($this->prod_cache);
        }else{
            @unlink(dirname(__FILE__).'/../../var/cache/prod/class_index.php');
        }
    }

	/**
	 * Get Cart ID from Customer Id
	 *
	 * @param  $customerId
	 * @return mixed
	 */
	private function getCartId($customerId)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)
					->getValue("SELECT id_cart FROM "._DB_PREFIX_."egoi_customers WHERE customer='".(int)$customerId."'");
	}

	/**
	 * Get Order details from ID
	 *
	 * @param int $orderId
	 * @return mixed
	 */
	private function getOrderDetails($orderId)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)
					->getRow("SELECT * FROM "._DB_PREFIX_."orders WHERE id_order='".(int)$orderId."'");
	}

	/**
	 * Get current customer ID from cookie or from session
	 *
	 * @return int|null
	 */
	private function getCustomerId()
	{
		return (int)$this->context->cookie->id_customer;
	}

	/**
	 * Remove customer cart from DB
	 *
	 * @return bool
	 */
	private function removeCart()
	{
		$idc = $this->getCustomerId();
		return Db::getInstance()->delete('egoi_customers', "customer='$idc'");
	}


   	/**
   	 * @param  array|bool $values
   	 * @param $key
   	 * @return void
   	 */
   	private function assign($values, $key = false)
   	{
    	if(!empty($values) && is_array($values)){
	        foreach ($values as $key => $value) {
	        	$this->smarty->assign($key, $value);
	        }

	    }else{
	    	if ($key) {
	    		$this->smarty->assign($key, $values);
			}
	    }
    }

    /**
     * Add new Client ID
     *
     * @param array $post
     * @return bool
     */
    private function addClientId($post)
    {
    	// clean table
		Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'egoi');
		// then insert new data
		Db::getInstance()->insert('egoi',
			array(
				'client_id' => (int)$post['egoi_client_id']
			)
		);
		return true;
	}

    //Function to Sync Order By APIv3
    private function syncOrderAPI($params) {

        $res = self::getClientData('track', 1);
        if (empty($res['track'])) {
            return false;
        }

        $order = new Order($params['id_order']);
        $products = $order->getProducts();

        if(!empty($res['track_state'])){
            if($res['track_state'] != $params['newOrderStatus']->id){
                return false;
            }
        }

        $this->apiv3 = new ApiV3();

        $order = self::formatOrder($order, $products);
        $apiv3 = new ApiV3();

        //Get Domain
        $baseUrl = _PS_BASE_URL_;
        $parsedUrl = parse_url($baseUrl);
        $domain = $parsedUrl['host'] ?? '';

        $apiv3->convertOrder($domain, $order);
    }

    private function formatOrder($order, $products) {
        $customer = new Customer($order->id_customer);

        $formattedOrder = [
            "order_total" => (float)$order->total_paid,
            "order_id" => (string)$order->id,
            "cart_id" => (float)$order->id_cart,
            "order_status" => self::getEgoiOrderStatusName($order->current_state),
            "order_date" => $order->date_add,
            "contact" => $this->formatContact($customer),
            "products" => []
        ];

        foreach ($products as $product) {
            $productTotal = (float) number_format(($product['price'] * $product['product_quantity']), 2, '.', '');

            $formattedOrder['products'][] = [
                "product_identifier" => $product['id_product'] ?? "",
                "name" => $product['product_name'] ?? "",
                "description" => $product['product_description'] ?? "",
                "sku" => $product['reference'] ?? "",
                "price" => $productTotal,
                "sale_price" => (float)($product['reduction_price'] ?? $product['price']),
                "quantity" => (int)$product['product_quantity']
            ];
        }

        return $formattedOrder;
    }

    private function formatContact($customer) {

        return [
            "base" => [
                "status" => "active",
                "first_name" => $customer->firstname ?? '',
                "last_name" => $customer->lastname ?? '',
                "email" => $customer->email ?? '',
            ]
        ];
    }

    private function getEgoiUid($customer) {
        $uid = Db::getInstance()->getValue(
            'SELECT uid FROM ' . _DB_PREFIX_ . "egoi_customer_uid WHERE email='" . pSQL($customer->email) . "';"
        );

        if (!empty($uid)) {
            return $uid;
        }

        $res = self::getClientData();
        $list_id = $res['list_id'];
        $data = $this->formatContact($customer);

        $uid = $this->apiv3->searchContactByEmail($customer->email, $list_id);
        if (!empty($uid)) {
            Db::getInstance()->insert('egoi_customer_uid', [
                'uid' => $uid,
                'email' => pSQL($customer->email)
            ]);
            $this->apiv3->patchContact($list_id, $uid, $data);
        } else {
            $contact = $this->apiv3->createContact($list_id, $data);
            if (!empty($contact['contact_id'])) {
                $uid = $contact['contact_id'];
                Db::getInstance()->insert('egoi_customer_uid', [
                    'uid' => $uid,
                    'email' => pSQL($data['base']['email'])
                ]);
            }
        }

        return $uid ?? null;
    }


    private function syncOrderTE($params) {

        $res = self::getClientData('track', 1);
        if (empty($res['track'])) {
            return false;
        }

        $order = new Order($params['id_order']);
        $products = $order->getProducts();

        $customer = new Customer($order->id_customer);

        $list_id = $res['list_id'];
        $client = $res['client_id'];

        if(!empty($res['track_state'])){
            if($res['track_state'] != $params['newOrderStatus']->id){
                return false;
            }
        }

        $uid = Db::getInstance()->getValue('SELECT uid FROM '._DB_PREFIX_."egoi_customer_uid WHERE email='".pSQL($customer->email)."';");
        $teSdk = new TESDK($client, empty($uid)?$customer->email:$uid ,$list_id);
        $teSdk->convertOrder([
            'order' => $order,
            'products' => $products
        ]);

    }

    /**
     * @param $prefix
     * @param $number
     * @return string
     */
    protected static function parsePhoneNumber($prefix = '', $number = '')
    {
        if (substr($number, 0, 1) === "+" && substr($number, 1, strlen($prefix)) === $prefix) { // The string starts with a + and prefix is in the number
            return $prefix . '-' . substr($number, strlen($prefix) + 1);
        } else {
            if(strpos($number, $prefix) === 0) { // se o numero ja tiver indicativo
                return $number;
            }
            return $prefix . '-' . $number;
        }
        return '';
    }


}
