<?php
/**
 * Smart Marketing
 *
 *  @author E-goi
 *  @copyright 2019 E-goi
 *  @license LICENSE.txt
 *  @package controllers/admin/AccountController
 */

include_once dirname(__FILE__).'/../SmartMarketingBaseController.php';

class SmsNotificationsController extends SmartMarketingBaseController
{

    /**
     * @var TransactionalApi $transactionalApi
     */
    protected $transactionalApi;

    /**
     * @var ApiV3 $apiv3
     */
    protected $apiv3;

    /**
     * SmsNotificationsController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->activateApis();

        $this->bootstrap = true;
        $this->cfg = 0;

        $this->meta_title = $this->l('SMS Notifications') . ' - ' . $this->module->displayName;
        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        if (!empty($_POST)) {
            $this->sanitize();
        }
    }

    /**
     * Activate E-goi APIs
     *
     * @return void
     */
    private function activateApis()
    {
        $this->transactionalApi = new TransactionalApi();
        $this->apiv3 = new ApiV3();
    }

    /**
     * Inject Dependencies
     *
     * @param $isNewTheme
     * @return void
     */
    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJS($this->_path. '/views/js/sms-notifications/messages.js');
    }

    /**
     * Toolbar settings
     *
     * @return void
     */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        // Toolbar button for documentation
        $this->page_header_toolbar_btn['egoiDocumentation'] = array(
            'short' => $this->l('E-goi Documentation'),
            'icon' => 'icon-book',
            'href' => $this->doc_url,
            'desc' => $this->l('E-goi Documentation'),
            'js' => $this->l('$( \'#outro-form\' ).click();')
        );

        $this->page_header_toolbar_btn['goto-egoi'] = array(
            'short' => $this->l('Go to E-goi'),
            'icon' => 'icon-external-link',
            'href' => 'https://login.egoiapp.com',
            'desc' => $this->l('Go to E-goi'),
            'js' => $this->l('$( \'#save-form\' ).click();')
        );
    }

    /**
     * Initiate content
     *
     * @return void
     */
    public function initContent()
    {
        parent::initContent();

        if ($this->isValid()) {
            $account = $this->apiv3->getMyAccount();
            $balance = $account['balance_info']['balance'];
            $currency = $account['balance_info']['currency'];
            $this->assign('balance', "$balance $currency");

            if (isset($_GET['messages'])) {
                $this->getMessages();
                $this->assign('menu_tab', 1);
                $this->assign('content', $this->fetch('sms-notifications/messages.tpl'));
            } elseif (isset($_GET['reminders'])) {
                $this->getReminders();
                $this->assign('menu_tab', 2);
                $this->assign('content', $this->fetch('sms-notifications/reminders.tpl'));
            } else {
                $this->getConfiguration();
                $this->assign('menu_tab', 0);
                $this->assign('content', $this->fetch('sms-notifications/configuration.tpl'));
            }
        }
    }

    /**
     * Setups message templates
     *
     * @return void
     */
    private function getMessages()
    {
        if(!empty($_POST)){
            $this->selectTemplates();
            $this->assign('success_msg', $this->displaySuccess($this->l('SMS Messages Saved')));
        }

        $this->languagesMessages(SmartMarketingPs::SMS_MESSAGES_DEFAULT_LANG_CONFIGURATION);
        $this->customInfoMessages();
        $this->orderStatesMessages();
    }

    /**
     * Languages configuration for messages
     *
     * @param $langConfig
     */
    private function languagesMessages($langConfig)
    {
        $locales = array();
        $langIds = array();
        $langs = Language::getLanguages(true, $this->context->shop->id);
        foreach ($langs as $lang) {
            array_push($locales, $lang['locale']);
            array_push($langIds, $lang['id_lang']);
        }

        $this->assign('langIds', $langIds);
        $this->assign('locales', $locales);

        if (!Configuration::get($langConfig)) {
            Configuration::updateValue($langConfig, $this->context->language->id);
        }

        $this->assign('defaultLang',  Configuration::get($langConfig));
    }

    /**
     * Custom info configuration for messages
     */
    private function customInfoMessages()
    {
        $this->assign('order_reference', SmartMarketingPs::CUSTOM_INFO_ORDER_REFERENCE);
        $this->assign('order_status', SmartMarketingPs::CUSTOM_INFO_ORDER_STATUS);
        $this->assign('total_cost', SmartMarketingPs::CUSTOM_INFO_TOTAL_COST);
        $this->assign('currency', SmartMarketingPs::CUSTOM_INFO_CURRENCY);
        $this->assign('entity', SmartMarketingPs::CUSTOM_INFO_ENTITY);
        $this->assign('mb_reference', SmartMarketingPs::CUSTOM_INFO_MB_REFERENCE);
        $this->assign('shop_name', SmartMarketingPs::CUSTOM_INFO_SHOP_NAME);
        $this->assign('billing_name', SmartMarketingPs::CUSTOM_INFO_BILLING_NAME);
        $this->assign('carrier', SmartMarketingPs::CUSTOM_INFO_CARRIER);
        $this->assign('tracking_url', SmartMarketingPs::CUSTOM_INFO_TRACKING_URL);
        $this->assign('tracking_number', SmartMarketingPs::CUSTOM_INFO_TRACKING_NUMBER);
    }

    /**
     * Creates templates for sms order status notifications
     */
    private function selectTemplates()
    {
        foreach ($_POST as $key => &$value) {
            if (strpos($key, 'egoi-sms-messages-client-') === 0) {
                $orderStatus = pSQL(str_replace('egoi-sms-messages-client-', '', $key));
                $client = pSQL($this->transformEOL($value));
                $admin = pSQL($this->transformEOL($_POST["egoi-sms-messages-admin-$orderStatus"]));
                unset($_POST["egoi-sms-messages-admin-$orderStatus"]);
                $this->insertTemplate($orderStatus, $client, $admin);
            } elseif (strpos($key, 'egoi-sms-messages-admin-') === 0) {
                $orderStatus = pSQL(str_replace('egoi-sms-messages-admin-', '', $key));
                $client = pSQL($this->transformEOL($_POST["egoi-sms-messages-client-$orderStatus"]));
                $admin = pSQL($this->transformEOL($value));
                unset($_POST["egoi-sms-messages-client-$orderStatus"]);
                $this->insertTemplate($orderStatus, $client, $admin);
            }
        }

        Configuration::updateValue(SmartMarketingPs::SMS_MESSAGES_DEFAULT_LANG_CONFIGURATION, $_POST['egoi-sms-messages-languages']);
    }

    /**
     * Transform end of line
     *
     * @param $str
     * @param bool $reverse
     *
     * @return mixed
     */
    private function transformEOL($str, $reverse = false)
    {
        if ($reverse) {
            return str_replace("\n\r", "\n", $str);
        }

        return str_replace("\n", "\n\r", $str);
    }

    /**
     * Inserts template
     *
     * @param $orderStatus
     * @param $client
     * @param $admin
     */
    private function insertTemplate($orderStatus, $client, $admin)
    {
        $lang = Configuration::get(SmartMarketingPs::SMS_MESSAGES_DEFAULT_LANG_CONFIGURATION);
        try {
            $insert = $this->insertNotifQuery($orderStatus, $lang, $client, $admin);
        } catch (PrestaShopDatabaseException $exception) {
            $insert = false;
        }

        if (!$insert) {
            Db::getInstance()->update(
                'egoi_sms_notif_messages',
                array(
                    'client_message' => $client,
                    'admin_message' => $admin
                ),
                "order_status_id=$orderStatus AND lang_id=$lang"
            );
        }
    }

    /**
     * Get query to insert notification
     *
     * @param $orderStatus
     * @param $lang
     * @param $client
     * @param $admin
     *
     * @return mixed
     */
    private function insertNotifQuery($orderStatus, $lang, $client, $admin)
    {
        return Db::getInstance()->insert(
            'egoi_sms_notif_messages',
            array(
                'order_status_id' => $orderStatus,
                'lang_id' => $lang,
                'client_message' => $client,
                'admin_message' => $admin
            )
        );
    }

    /**
     * Setups reminders
     *
     * @return void
     */
    private function getReminders()
    {
        if(!empty($_POST)){
            $this->selectReminderTimes();
            $this->selectReminders();
            $this->assign('success_msg', $this->displaySuccess($this->l('Payment Reminders Saved')));
        }

        $this->languagesMessages(SmartMarketingPs::SMS_REMINDERS_DEFAULT_LANG_CONFIGURATION);
        $this->reminderTimes();
        $this->customInfoMessages();
        $this->paymentOrderStatesReminders();
    }

    /**
     * Selects the reminder time
     */
    private function selectReminderTimes()
    {
        Configuration::updateValue(SmartMarketingPs::SMS_REMINDER_DEFAULT_TIME_CONFIG, $_POST['egoi-sms-reminder-time']);
    }

    /**
     * Creates payment reminders
     */
    private function selectReminders()
    {
        $lang = Configuration::get(SmartMarketingPs::SMS_REMINDERS_DEFAULT_LANG_CONFIGURATION);

        foreach ($_POST as $key => &$value) {
            if (strpos($key, 'egoi-sms-reminder-message-') === 0) {
                $orderStatus = pSQL(str_replace('egoi-sms-reminder-message-', '', $key));
                $active = isset($_POST['sms-reminder-active']) ? 1 : 0;
                Db::getInstance()->update(
                    'egoi_sms_notif_reminder_messages',
                    array(
                        'message' => $value,
                        'active' => $active
                    ),
                    "order_status_id=$orderStatus AND lang_id=$lang"
                );
            }
        }

        Configuration::updateValue(SmartMarketingPs::SMS_REMINDERS_DEFAULT_LANG_CONFIGURATION, $_POST['egoi-sms-messages-languages']);
    }

    /**
     * Get reminder times
     */
    private function reminderTimes()
    {
        //time in hours
        $times = array(12, 24, 36, 48, 72);
        $timeNames = array();
        foreach ($times as $time) {
            array_push($timeNames, $time . ' ' . $this->l('hours'));
        }

        if (!Configuration::hasKey(SmartMarketingPs::SMS_REMINDER_DEFAULT_TIME_CONFIG)) {
            Configuration::updateValue(SmartMarketingPs::SMS_REMINDER_DEFAULT_TIME_CONFIG, $times[0]);
        }

        $this->assign('defaultReminderTime', Configuration::get(SmartMarketingPs::SMS_REMINDER_DEFAULT_TIME_CONFIG));
        $this->assign('reminderTimes', $times);
        $this->assign('reminderTimeNames', $timeNames);
    }

    /**
     * Get order state reminders
     */
    private function paymentOrderStatesReminders()
    {
        $lang = Configuration::get(SmartMarketingPs::SMS_REMINDERS_DEFAULT_LANG_CONFIGURATION);
        $orderState = new OrderState($lang);
        $orderStates = array_reverse($orderState->getOrderStates($lang));

        $paymentOrderState = null;
        foreach ($orderStates as $orderState) {
            if (SmartMarketingPs::getPaymentModule($orderState)) {
                $paymentOrderState = $orderState;
                break;
            }
        }

        if (empty($paymentOrderState)) {
            $this->assign('reminder', null);
            return;
        }

        $reminder = array(
            'order_status_id' => pSQL($paymentOrderState['id_order_state']),
            'lang_id' => pSQL($lang),
            'message' => '',
            'active' => 0
        );

        Db::getInstance()->insert(
            'egoi_sms_notif_reminder_messages',
            $reminder,
            false,
            true,
            Db::INSERT_IGNORE
        );

        $reminder = Db::getInstance()->getRow("SELECT * FROM "._DB_PREFIX_."egoi_sms_notif_reminder_messages WHERE lang_id=" .pSQL($lang)
            ." AND order_status_id=".pSQL($paymentOrderState['id_order_state'])
        );

        $this->assign('reminder', $reminder);
    }

    /**
     * Setups configuration to send sms notifications
     *
     * @return void
     */
    private function getConfiguration()
    {
        if(!empty($_POST)){
            $this->assign('success_msg', $this->displaySuccess($this->l('Configuration Saved')));
            $this->selectSender();
            $this->selectAdmin();
            $this->selectAddress();
            $this->selectOrderStatuses();
        }

        $this->sendersConfig();
        $this->adminConfig();
        $this->addressConfig();
        $this->orderStatesConfig();
    }

    /**
     * Select a sender to send notifications
     */
    private function selectSender()
    {
        Configuration::updateValue(SmartMarketingPs::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION, $_POST['egoi-transactional-sms-sender']);
    }

    /**
     * Select order status to send notifications
     */
    private function selectOrderStatuses()
    {
        $smsNotifs = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'egoi_sms_notif_order_status');

        foreach ($smsNotifs as &$notif) {
            $update = false;
            if ($notif['send_client'] == 1 && !isset($_POST['sms-notif-client-'.$notif['order_status_id']])) {
                $update = true;
                $notif['send_client'] = 0;
            } elseif ($notif['send_client'] == 0 && isset($_POST['sms-notif-client-'.$notif['order_status_id']])) {
                $update = true;
                $notif['send_client'] = 1;
            }

            if ($notif['send_admin'] == 1 && !isset($_POST['sms-notif-admin-'.$notif['order_status_id']])) {
                $update = true;
                $notif['send_admin'] = 0;
            } elseif ($notif['send_admin'] == 0 && isset($_POST['sms-notif-admin-'.$notif['order_status_id']])) {
                $update = true;
                $notif['send_admin'] = 1;
            }

            if ($update) {
                Db::getInstance()->update(
                    'egoi_sms_notif_order_status',
                    array('send_client' => pSQL($notif['send_client']), 'send_admin' => pSQL($notif['send_admin'])),
                    'order_status_id = '.pSQL((int)$notif['order_status_id'])
                );
            }
        }
    }

    /**
     * Select administrator to receive sms notifications
     */
    private function selectAdmin()
    {
        Configuration::updateValue(SmartMarketingPs::SMS_NOTIFICATIONS_ADMINISTRATOR_PREFIX_CONFIGURATION, $_POST['egoi-transactional-sms-administrator-prefix']);
        if (isset($_POST['egoi-transactional-sms-administrator'])) {
            Configuration::updateValue(SmartMarketingPs::SMS_NOTIFICATIONS_ADMINISTRATOR_CONFIGURATION, $_POST['egoi-transactional-sms-administrator']);
        }
    }

    /**
     * Select addresses that will receive sms notifications
     */
    private function selectAddress()
    {
        switch ($_POST['egoi-transactional-sms-destination']) {
            case 'delivery-address':
                Configuration::updateValue(SmartMarketingPs::SMS_NOTIFICATIONS_DELIVERY_ADDRESS_CONFIGURATION, true);
                Configuration::updateValue(SmartMarketingPs::SMS_NOTIFICATIONS_INVOICE_ADDRESS_CONFIGURATION, false);
                break;
            case 'invoice-address':
                Configuration::updateValue(SmartMarketingPs::SMS_NOTIFICATIONS_DELIVERY_ADDRESS_CONFIGURATION, false);
                Configuration::updateValue(SmartMarketingPs::SMS_NOTIFICATIONS_INVOICE_ADDRESS_CONFIGURATION, true);
                break;
            default:
                Configuration::updateValue(SmartMarketingPs::SMS_NOTIFICATIONS_DELIVERY_ADDRESS_CONFIGURATION, true);
                Configuration::updateValue(SmartMarketingPs::SMS_NOTIFICATIONS_INVOICE_ADDRESS_CONFIGURATION, true);
        }
    }

    /**
     * Senders configuration
     */
    private function sendersConfig()
    { 
        $senders = $this->apiv3->getCellphoneSenders();

        if (!Configuration::hasKey(SmartMarketingPs::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION)) {
            if(!empty($senders)){
                Configuration::updateValue(SmartMarketingPs::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION, $senders['items'][0]['sender_id']);
            }
        }

        $senderIds = array();
        $senderNames = array();
        foreach ($senders['items'] as $sender) {
            array_push($senderIds, $sender['sender_id']);
            array_push($senderNames, $sender['cellphone']);
        }

        $this->assign('defaultSender', Configuration::get(SmartMarketingPs::SMS_NOTIFICATIONS_SENDER_V3_CONFIGURATION));
        $this->assign('senderIds', $senderIds);
        $this->assign('senderNames', $senderNames);
        
    }

    /**
     * Administrator configuration
     */
    private function adminConfig()
    {
        $countries = Db::getInstance()->executeS("SELECT * FROM "._DB_PREFIX_."country ORDER BY iso_code");
        $isoCodes = array();
        $prefixes = array();
        foreach ($countries as $country) {
            array_push($isoCodes, $country['iso_code'] . ' (+'. $country['call_prefix'] . ')');
            array_push($prefixes, $country['call_prefix']);
        }

        if (!Configuration::hasKey(SmartMarketingPs::SMS_NOTIFICATIONS_ADMINISTRATOR_PREFIX_CONFIGURATION)) {
            Configuration::updateValue(SmartMarketingPs::SMS_NOTIFICATIONS_ADMINISTRATOR_PREFIX_CONFIGURATION, $this->context->country->call_prefix);
        }

        $this->assign('defaultPrefix', Configuration::get(SmartMarketingPs::SMS_NOTIFICATIONS_ADMINISTRATOR_PREFIX_CONFIGURATION));
        $this->assign('prefixOutput', $isoCodes);
        $this->assign('prefixes', $prefixes);
        $this->assign('administrator', Configuration::get(SmartMarketingPs::SMS_NOTIFICATIONS_ADMINISTRATOR_CONFIGURATION));
    }

    /**
     * Addresses configuration
     */
    private function addressConfig()
    {
        $this->setupAddress(SmartMarketingPs::SMS_NOTIFICATIONS_DELIVERY_ADDRESS_CONFIGURATION);
        $this->setupAddress(SmartMarketingPs::SMS_NOTIFICATIONS_INVOICE_ADDRESS_CONFIGURATION);

        $this->assign('deliveryAddress', Configuration::get(SmartMarketingPs::SMS_NOTIFICATIONS_DELIVERY_ADDRESS_CONFIGURATION));
        $this->assign('invoiceAddress', Configuration::get(SmartMarketingPs::SMS_NOTIFICATIONS_INVOICE_ADDRESS_CONFIGURATION));
    }

    /**
     * Init address
     *
     * @param $key
     */
    private function setupAddress($key)
    {
        if (!Configuration::hasKey($key)) {
            Configuration::updateValue($key, true);
        }
    }

    /**
     * Orders configuration for messages
     */
    private function orderStatesMessages()
    {
        $lang = Configuration::get(SmartMarketingPs::SMS_MESSAGES_DEFAULT_LANG_CONFIGURATION);
        $orders = new OrderState($lang);
        $orders = $orders->getOrderStates($lang);
        $smsNotifs = Db::getInstance()->executeS("SELECT * FROM "._DB_PREFIX_."egoi_sms_notif_messages");
        foreach ($orders as &$order) {
            $notif = $this->arraySearch($smsNotifs, array('order_status_id', 'lang_id'), array($order['id_order_state'], $lang));

                $order['sms_notif'] = array();
            if(!empty($notif)){
                $order['sms_notif']['client_message'] = $this->transformEOL($notif['client_message'], true);
                $order['sms_notif']['admin_message'] = $this->transformEOL($notif['admin_message'], true);
            }else{
                $order['sms_notif']['client_message'] = ' ';
                $order['sms_notif']['admin_message'] = ' ';
            }
        }

        $this->assign('orders', $orders);
    }

    /**
     * Orders configuration
     */
    private function orderStatesConfig()
    {
        $orders = new OrderState($this->context->language->id);
        $orders = $orders->getOrderStates($this->context->language->id);
        $smsNotifs = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'egoi_sms_notif_order_status');
        foreach ($orders as &$order) {
            Db::getInstance()->insert(
                'egoi_sms_notif_order_status',
                array('order_status_id' => pSQL($order['id_order_state']), 'send_client' => 0, 'send_admin' => 0),
                false,
                true,
                Db::INSERT_IGNORE
            );

            $notif = $this->arraySearch($smsNotifs, array('order_status_id'), array($order['id_order_state']));
            $order['sms_notif'] = array();
            
            if(!empty($notif)){
                $order['sms_notif']['send_client'] = $notif['send_client'];
                $order['sms_notif']['send_admin'] = $notif['send_admin'];
            }else{
                $order['sms_notif']['send_client'] = ' ';
                $order['sms_notif']['send_admin'] = ' ';
            }
        }

        $this->assign('orders', $orders);
    }

    /**
     * Gets item by values from array
     *
     * @param array $array
     * @param array $keys
     * @param array $values
     *
     * @return mixed|null
     */
    private function arraySearch($array, $keys, $values)
    {
        foreach ($array as $item) {
            if ($this->itemHasKeys($keys, $item, $values)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Checks if an array item has a set of keys
     *
     * @param array $keys
     * @param $item
     * @param array $values
     *
     * @return bool
     */
    private function itemHasKeys($keys, $item, $values)
    {
        $arrLength = count($keys);
        for ($i = 0; $i < $arrLength; $i++) {
            if (!isset($item[$keys[$i]]) || $item[$keys[$i]] != $values[$i]) {
                return false;
            }
        }

        return true;
    }
}
