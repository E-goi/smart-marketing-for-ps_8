<?php
/**
 *  Smart Marketing
 *
 *  @author    E-goi
 *  @copyright 2018 E-goi
 *  @license   LICENSE.txt
 *  @package controllers/admin/SyncController
 */

include_once dirname(__FILE__).'/../SmartMarketingBaseController.php';
include_once dirname(__FILE__).'/../../smartmarketingps.php';

class SyncController extends SmartMarketingBaseController 
{

    /**
     * @var ApiV3 $apiv3
     */
	protected $apiv3;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// instantiate API
        $this->apiv3 = new ApiV3();

		$this->bootstrap = true;
		$this->cfg = 0;
		$this->meta_title = $this->l('E-goi Sync Contacts').' - '.$this->module->displayName;
		
		if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
        $newsletter = isset($_POST['newsletter']) ? $_POST['newsletter'] : false; //Indicates that it is a request related to the newsletter

        $this->retrieveRoles();
        $this->mapFieldsEgoi();
		$this->syncronizeEgoi();
		$this->sincronizeList();
        $this->sincronizeNewsletter();
        $this->countCostumersByShop($newsletter);
    }

	/**
	 * Inject Dependencies
	 *
     * @param $isNewTheme
	 * @return mixed
	 */
	public function setMedia($isNewTheme = false)
	{
		parent::setMedia($isNewTheme);
		$this->addJS($this->_path. '/views/js/sync.js');
	}

	/**
	 * Toolbar settings
	 * 
	 * @return void
	 */
	public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn['save-and-stay'] = array(
		    'short' => $this->l('Save Settings'),
		    'href' => '#',
		    'desc' => $this->l('Save Settings'),
		    'js' => $this->l('$( \'#action_add\' ).click();')
		);
    }

    public function countCostumersByShop($newsletter){
        if(empty(Tools::getValue("size"))) {
            return false;
        }

        echo json_encode(SmartMarketingPs::sizeList($newsletter));
        exit;
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

			if(!empty($_POST)) {
				$this->saveSync();
			}

            $cache_id = 'getLists::'.$this->apiv3->getApiKey();
            if (!Cache::isStored($cache_id)) {
                $lists = $this->apiv3->getLists();
                Cache::store($cache_id, $lists);
            }
			$this->assign('lists', Cache::retrieve($cache_id));

            $cache_id = 'SELECT * FROM '._DB_PREFIX_.'egoi where client_id!="" order by egoi_id DESC';
            if (!Cache::isStored($cache_id)) {
                $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)
                    ->getRow('SELECT * FROM '._DB_PREFIX_.'egoi where client_id!="" order by egoi_id DESC');
                Cache::store($cache_id, $rq);
            }
            $rq = Cache::retrieve($cache_id);

			if(!empty($rq)) {
				$list_id = $rq['list_id'];
				$sync = $rq['sync'];
				$track = $rq['track'];
				$role = $rq['role'];
                $optin = $rq['optin'];
                $newsletter_sync = $rq['newsletter_sync'];
                $track_state = !empty($rq['track_state'])?$rq['track_state']:0;
			}

			if(isset($list_id) && ($list_id)) {

                $cache_id = 'getEgoiSubscribers::'.$this->apiv3->getApiKey().'::'.(int)$list_id;
                if (!Cache::isStored($cache_id)) {
                    $subscribers = $this->getEgoiSubscribers($list_id);
                    Cache::store($cache_id, $subscribers);
                }

				$this->assign('subscribers', Cache::retrieve($cache_id));
				$this->assign('list_id', $list_id);
				$this->assign('sync', $sync);
				$this->assign('track', $track);
				$this->assign('role_id', $role);
				$this->assign('optin', $optin);
				$this->assign('newsletter_sync', $newsletter_sync);
				$this->assign('states', $this->getOrderStates());
                $this->assign('track_state', $track_state);

				//map fields
				$egoi_fields = array(
					'first_name' => 'First name',
					'last_name' => 'Last name',
					'cellphone' => 'Mobile',
					'telephone' => 'Telephone',
					'birth_date' => 'Birth Date'
				);

                $allExtraFields = $this->apiv3->getAllExtraFields($list_id);

                if (!empty($allExtraFields)) {
                    foreach($allExtraFields as $extra_field) {
                        $egoi_fields['extra_'.$extra_field['field_id']] = $extra_field['name'] . ' (ID: ' . $extra_field['field_id'] . ' // Format: ' .$extra_field['format'].')';
                    }
                }
                
				$option = '';
                foreach($egoi_fields as $key => $field) {
                    $option .= '<option value='.$key.'>'.$field.'</option>'.PHP_EOL;
                }

                $this->assign('select', $option);

				$this->assign('mapped_fields', $this->getMappedFields());
			}
            
            $listValue = Db::getInstance()->getValue("SELECT list_id FROM "._DB_PREFIX_."egoi"); 
            if($listValue == 0){
                $list_altered = false;
            }else{
                $list_altered = true;
            }


            $this->assign('list_altered', $list_altered);
            $this->assign('listValue', $listValue);
			$this->assign('token', Tools::getValue('token'));
			$this->assign('content', $this->fetch('sync.tpl'));
		}
	}

	private function getOrderStates(){
        return Db::getInstance()->executeS("select "._DB_PREFIX_."order_state.id_order_state, "._DB_PREFIX_."order_state_lang.name from "._DB_PREFIX_."order_state inner join "._DB_PREFIX_."order_state_lang on "._DB_PREFIX_."order_state.id_order_state = "._DB_PREFIX_."order_state_lang.id_order_state where "._DB_PREFIX_."order_state_lang.id_lang = '".Configuration::get('PS_LANG_DEFAULT')."'");
    }

	/**
	 * Save syncronization data
	 * 
	 * @return mixed
	 */
	protected function saveSync() 
	{
		if(!empty(Tools::getValue('action_add'))) {

			$list = Tools::getValue('list');
			$sync = Tools::getValue('enable');
			$role = Tools::getValue('role');

            $track_state = Tools::getValue('track_state', 0);
            $nsync = Tools::getValue('newsletter_sync', 0);
            $noptin = Tools::getValue('newsletter_optin', 0);
			$track = Tools::getValue('track', 1);

            if(!empty($track) && $track == "1"){
                $code = $this->apiv3->activateConnectedSites(_PS_BASE_URL_ ,$list);
                if(empty($code) || !is_array($code) || empty($code['code'])){
                    $code = $this->apiv3->getConnectedSite(_PS_BASE_URL_);
                }
                if(!empty($code['code'])){
                    Configuration::updateValue(SmartMarketingPs::CONNECTED_SITES_CODE, base64_encode($code['code']));
                }
            }

            // compare client ID -> API with DB
            $cache_id = 'getMyAccount::'.$this->apiv3->getApiKey();
            if (!Cache::isStored($cache_id)) {
                $clientData = $this->apiv3->getMyAccount();
                Cache::store($cache_id, $clientData);
            }

            $clientData = Cache::retrieve($cache_id);
            $client = $clientData['general_info']['client_id'];

            $cache_id = 'QUERY::SELECT * FROM '._DB_PREFIX_.'egoi WHERE client_id='.(int)$client;
            if (!Cache::isStored($cache_id)) {
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)
                    ->getRow('SELECT * FROM '._DB_PREFIX_.'egoi WHERE client_id='.(int)$client);
                Cache::store($cache_id, $res);
            }
            $res = Cache::retrieve($cache_id);

            // temporary - alter table with new column
            if ($nsync) {
                if (is_null($res['newsletter_sync'])) {
                    $query = "ALTER TABLE "._DB_PREFIX_."egoi ADD COLUMN newsletter_sync INT(11) NOT NULL DEFAULT '0' AFTER `optin`";
                    Db::getInstance()->execute($query);
                }
            }

            if (isset($track_state)) {
                if(is_null($res['track_state'])){
                    $query = "ALTER TABLE "._DB_PREFIX_."egoi ADD COLUMN track_state INT(11) NOT NULL DEFAULT '0' AFTER `optin`";
                    Db::getInstance()->execute($query);
                }
            }

            $values = array(
				'list_id' => (int)$list, 
				'client_id' => (int)$client,
				'sync' => (int)$sync,
				'track' => (int)$track,
				'role' => pSQL($role),
				'newsletter_sync' => (int)$nsync,
				'optin' => (int)$noptin,
				'estado' => 1,
                'total' => 0,
                'track_state' => $track_state,
			);
			
			if(isset($res['client_id']) && ($res['client_id'])) {
				return Db::getInstance()->update('egoi', $values, "client_id = ".(int)$client);
			}else{
				$this->assign('success_message', $this->displaySuccess($this->l('Settings saved')));
				return Db::getInstance()->insert('egoi', $values);
			}

		}else{
			return false;
		}
	}

	/**
	 * Assign customers roles
	 * 
	 * @return void
	 */
	protected function retrieveRoles()
	{
		$this->assign('roles', Group::getGroups(Context::getContext()->language->id, true));
	}

    /**
     * Get role from Customer ID
     *
     * @param $id
     * @param $role
     * @return bool
     */
	protected function getRole($id, $role)
    {
        if ($role) {
            $exists = Db::getInstance()
                ->getValue("SELECT COUNT(*) FROM "._DB_PREFIX_."customer_group WHERE id_customer='".$id."' and id_group='$role'");
            if (!$exists) {
                return false;
            }
        }
        return true;
    }

	/**
	 * Map fields
	 * 
	 * @return void
	 */
	protected function mapFieldsEgoi() 
	{
		if (!empty($_POST)) {
			$id = (int)Tools::getValue("id_egoi", '');
			$token = (int)Tools::getValue("token_egoi_api", '');
			$ps = pSQL(Tools::getValue("ps", ''));
			$egoi = pSQL(Tools::getValue("egoi", ''));

			if(($token) && ($ps) && ($egoi)){

				$ps_name = pSQL(Tools::getValue("ps_name"));
				$egoi_name = pSQL(Tools::getValue("egoi_name"));
				$status = 1;

				$sql_exists = "SELECT id FROM "._DB_PREFIX_."egoi_map_fields WHERE ps='".$ps."' OR egoi='".$egoi."'";
				$exists = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_exists);
				$field_exist = isset($exists[0]) ? $exists[0] : false;

				if (!$field_exist){
					// insert new mapped field
					Db::getInstance()->insert('egoi_map_fields', array(
						'ps' => $ps,
						'ps_name' => $ps_name,
						'egoi' => $egoi,
						'egoi_name' => $egoi_name,
						'status' => $status
					));
                    $this->assign('post_id', Db::getInstance()->Insert_ID());
                    $this->assign('ps_name', $ps_name);
	         		$this->assign('egoi_name', $egoi_name);

	         		echo $this->context->smarty->display($this->_path.'/views/templates/admin/fields.tpl');
		        }
				exit;

			} elseif($id) {
				Db::getInstance()->delete('egoi_map_fields', 'id = ' . $id);
				exit;
			}
		}
	}

	/**
	 * Get Subscribers count
	 * 
	 * @param $listID
	 * @return int        
	 */
	protected function getEgoiSubscribers($listID)
	{
        $result = $this->apiv3->getContactsNum($listID);
	    return !empty($result['total_items']) ? $result['total_items'] : 0;
	}

	/**
	 * Join Subscribers count from DB and API
	 * 
	 * @return void
	 */
	protected function syncronizeEgoi()
	{

        if(!empty(Tools::getValue('action')) && (Tools::getValue('action') == 'synchronize')) {

            if (!empty(Tools::getValue('list'))) {
                $total = array();
                $exec = Db::getInstance(_PS_USE_SQL_SLAVE_)
                            ->getRow('SELECT COUNT(*) AS CT FROM '._DB_PREFIX_.'customer WHERE active="1"');

                $list_id = Tools::getValue('list');
                $cache_id = 'getEgoiSubscribers::'.$this->apiv3->getApiKey().'::'.(int)$list_id;
                if (!Cache::isStored($cache_id)) {
                    $subscribers = $this->getEgoiSubscribers($list_id);
                    Cache::store($cache_id, $subscribers);
                }

                $total[] = Cache::retrieve($cache_id);
                $total[] = $exec['CT'];

                echo json_encode($total);
                exit;
            }
        }
	}

    /**
     * @return array
     */
	protected function getMappedFields()
    {
        return (new SmartMarketingPs)->getMappedFields();
    }

	/**
	 * Syncronize all subscribers to list
	 *
	 * @return bool
	 */
	protected function sincronizeList()
	{
		// on a specific POST request
        if(empty(Tools::getValue("token_list"))) {
            return false;
        }

        if(!empty(Tools::getValue("newsletter"))) {
            return false;
        }

        $res = SmartMarketingPs::getClientData();

        $list_id            = $res['list_id'];

        $subs = Tools::getValue("subs");
        $store_id = Tools::getValue("store_id");
        if(empty($store_id)) {
            try {
                $store_id = (int)Context::getContext()->shop->id;
            } catch (Exception $e) {
                $store_id = 1;
            }
        }
        $store_name = SmartMarketingPs::getShopsName($store_id);
        $store_filter = 'AND '._DB_PREFIX_.'customer.id_shop="'.$store_id.'" ';
        // get main customers
        $ts = [];
        if(!empty($store_id) && !empty($store_name)){
            array_push($ts, $store_name);
        }

        $add='';

        $buff = 1000;
        $count = intval($subs);

        $sqlc = 'SELECT email, '._DB_PREFIX_.'customer.firstname, '._DB_PREFIX_.'customer.lastname, birthday, newsletter, optin, id_shop, id_lang, phone, phone_mobile, call_prefix FROM '._DB_PREFIX_.'customer LEFT JOIN '._DB_PREFIX_.'address ON '._DB_PREFIX_.'customer.id_customer = '._DB_PREFIX_.'address.id_customer LEFT JOIN '._DB_PREFIX_.'country ON '._DB_PREFIX_.'country.id_country = '._DB_PREFIX_.'address.id_country WHERE '._DB_PREFIX_.'customer.active="1" '.$add.$store_filter.' GROUP BY '._DB_PREFIX_.'customer.id_customer LIMIT ' . ($count * $buff) . ', ' . $buff;//AND newsletter="1"

        $getcs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sqlc);

        if(empty($getcs)){
            echo json_encode(['error' => 'No users!']);
            exit;
        }

        $importContacts = [
            'mode' => 'update',
            'compare_field' => 'email',
            'contacts' => [],
            'force_empty' => true,
        ];

        $allFields = $this->getMappedFields();

        foreach($getcs as $row){
            $importContacts['contacts'][] = SmartMarketingPs::mapSubscriber($row, $allFields ? $allFields : []);
        }

        $this->apiv3->addSubscriberBulk($list_id, $importContacts);
        Configuration::updateValue(SmartMarketingPs::ADDRESS_CRON_TIME_CONFIGURATION, time());

        $count++;
        echo json_encode(['imported' => $count]);
        exit;
	}


    /**
     * Syncronize all subscribers to list
     *
     * @return bool
     */
    protected function sincronizeNewsletter()
    {

        // on a specific POST request
        if(empty(Tools::getValue("token_list"))) {
            return false;
        }

        if(!empty(Tools::getValue("newsletter")) &&  (Tools::getValue("newsletter")!= true)){
            return false;
        }

        $res = SmartMarketingPs::getClientData();

        $client_id          = $res['client_id'];
        $list_id            = $res['list_id'];

        $subs = Tools::getValue("subs");
        $store_id = Tools::getValue("store_id");

        if(empty($store_id)) {
            try {
                $store_id = (int)Context::getContext()->shop->id;
            } catch (Exception $e) {
                $store_id = 1;
            }
        }

        $store_name = SmartMarketingPs::getShopsName($store_id);
        // get main customers
        $ts = [];
        if(!empty($store_id) && !empty($store_name)){
            array_push($ts, $store_name);
        }

        $buff = 1000;
        $count = intval($subs);

        $sql = 'SELECT `email`, `id_lang`
                FROM ' . _DB_PREFIX_ . 'emailsubscription
                WHERE `active` = 1
                AND id_shop = ' . $store_id . ' LIMIT ' . ($count * $buff) . ', ' . $buff;

        $getcs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if(empty($getcs)){
            echo json_encode(['error' => 'No users!']);
            exit;
        }

        $importContacts = [
            'mode' => 'update',
            'compare_field' => 'email',
            'contacts' => [],
            'force_empty' => true,
        ];

        $allFields = $this->getMappedFields();

        foreach($getcs as $row){
            $importContacts['contacts'][] = SmartMarketingPs::mapSubscriber($row, $allFields ? $allFields : []);
        }

        $this->apiv3->addSubscriberBulk($list_id, $importContacts);

        Configuration::updateValue(SmartMarketingPs::ADDRESS_CRON_TIME_CONFIGURATION, time());

        $cache_id = 'getEgoiSubscribers::'.$this->apiv3->getApiKey().'::'.(int)$list_id;
        if (!Cache::isStored($cache_id)) {
            $subscribers = $this->getEgoiSubscribers($list_id);
            Cache::store($cache_id, $subscribers);
        }

        Db::getInstance()->update(
            'egoi',
            array(
                'total' => Cache::retrieve($cache_id)
            ),
            "client_id = $client_id"
        );

        $count++;
        echo json_encode(['imported' => $count]);
        exit;
    }
	
}
