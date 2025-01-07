<?php
/**
 * Smart Marketing
 *
 *  @author    E-goi
 *  @copyright 2024 E-goi
 *  @license   LICENSE.txt
 *  @package controllers/admin/EcommerceController
 */

include_once dirname(__FILE__).'/../SmartMarketingBaseController.php';
include_once dirname(__FILE__).'/../../smartmarketingps.php';

class EcommerceController extends SmartMarketingBaseController
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
        $this->meta_title = $this->l('Ecommerce').' - '.$this->module->displayName;

        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        $this->countOrdersByShop();
        $this->syncOrders();

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
        $this->addJS($this->_path. '/views/js/ecommerce.js');
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

        // Toolbar button for documentation
        $this->page_header_toolbar_btn['egoiDocumentation'] = array(
            'short' => $this->l('E-goi Documentation'),
            'icon' => 'icon-book',
            'href' => $this->doc_url,
            'desc' => $this->l('E-goi Documentation'),
            'js' => $this->l('$( \'#outro-form\' ).click();')
        );
    }

    public function countOrdersByShop(){
        if(empty(Tools::getValue("size"))) {
            return false;
        }

        echo json_encode(SmartMarketingPs::sizeListOrders());
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
                $this->saveOrdersSync();
            }

            $statesData = $this->collectOrderStatesData();
            // Carregar os dados das orders no template ecommerce
            $this->context->smarty->assign(['statesData' => $statesData,]);

            // Carregar a página
            $this->assign('content', $this->fetch('ecommerce.tpl'));
        }
    }

    private function collectOrderStatesData()
    {
        // Get all Prestashop States

        $orderStates = OrderState::getOrderStates((int)$this->context->language->id);


        // Get Egoi Order States
        $egoiStates = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'egoi_order_states`');

        $mappedStates = [];
        foreach ($orderStates as $state) {
            $mappedStates[] = [
                'type' => $this->l('Order State'),
                'id' => $state['id_order_state'],
                'name' => $state['name'],
                'color' => $state['color'],
                'egoi_state_id' => $this->getEgoiStateIdForState($state['id_order_state']),
                'egoi_states' => $egoiStates, // Passar todos os estados do E-goi
            ];
        }

        // Order the Ids by asc
        usort($mappedStates, function ($a, $b) {
            return $a['id'] <=> $b['id'];
        });

        return $mappedStates;
    }

    /**
     * Get the E-goi state ID corresponding to a PrestaShop state ID
     *
     * @param int $prestashopStateId
     * @return int|null
     */
    private function getEgoiStateIdForState($prestashopStateId)
    {
        $result = Db::getInstance()->executeS('SELECT egoi_state_id FROM `' . _DB_PREFIX_ . 'egoi_prestashop_order_state_map` WHERE prestashop_state_id = ' . (int)$prestashopStateId);

        return isset($result[0]['egoi_state_id']) ? $result[0]['egoi_state_id'] : null;
    }

    /**
     * Save the Prestashop Status Mapped Fields to Egoi
     *
     * @return bool
     */
    protected function saveOrdersSync()
    {
        if (empty(Tools::getValue('action_add'))) {
            return false;
        }

        $stateMappings = Tools::getValue('egoi_state_mappings');
        if (empty($stateMappings)) {
            return false;
        }

        $cacheKey = 'prestashop_order_state_map_' . md5(serialize($stateMappings));
        if (!Cache::isStored($cacheKey)) {
            // Recuperar registros existentes e armazenar em cache
            $existingRecords = Db::getInstance()->executeS(
                'SELECT prestashop_state_id, id 
            FROM ' . _DB_PREFIX_ . 'egoi_prestashop_order_state_map 
            WHERE prestashop_state_id IN (' . implode(',', array_map('intval', array_keys($stateMappings))) . ')'
            );
            Cache::store($cacheKey, $existingRecords);
        } else {
            $existingRecords = Cache::retrieve($cacheKey);
        }

        foreach ($stateMappings as $prestashopStateId => $egoiStateId) {
            $prestashopStateId = (int)$prestashopStateId;
            $egoiStateId = (int)$egoiStateId;

            if ($prestashopStateId <= 0 || $egoiStateId <= 0) {
                continue; // Ignorar entradas inválidas
            }

            $values = [
                'prestashop_state_id' => $prestashopStateId,
                'egoi_state_id' => $egoiStateId,
                'active' => 1
            ];

            $existingRecord = null;
            foreach ($existingRecords as $record) {
                if ($record['prestashop_state_id'] == $prestashopStateId) {
                    $existingRecord = $record;
                    break;
                }
            }

            if ($existingRecord) {
                // Atualizar registro existente
                Db::getInstance()->update(
                    'egoi_prestashop_order_state_map',
                    $values,
                    "prestashop_state_id = " . (int)$prestashopStateId
                );
            }
        }

        Cache::clean($cacheKey);
        return true;
    }

    /**
     * Synchronize all orders to E-goi
     *
     * @return bool
     */
    protected function syncOrders()
    {
        if (empty(Tools::getValue("token_list"))) {
            return false;
        }

        $res = SmartMarketingPs::getClientData();
        $list_id = $res['list_id'] ?? null;

        if (!$list_id) {
            echo json_encode(['error' => 'List ID not found!']);
            exit;
        }

        $store_id = (int) Tools::getValue("store_id") ?: Context::getContext()->shop->id ?? 1;
        $current_page = (int) Tools::getValue("orders", 0);
        $buff = 1000;

        // Primeiro, vamos obter o total de pedidos para esta loja
        $count_sql = 'SELECT COUNT(DISTINCT o.id_order) as total 
                  FROM ' . _DB_PREFIX_ . 'orders o 
                  WHERE o.id_shop = ' . $store_id;

        $total_orders = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($count_sql);

        // Query SQL with proper pagination
        $sql = 'SELECT o.id_order, o.reference, o.total_paid, o.date_add, o.current_state, o.id_shop AS store_id, 
                c.email AS customer_email, od.product_id, od.product_name, 
                od.product_quantity, od.unit_price_tax_incl AS product_price, 
                p.id_category_default 
        FROM ' . _DB_PREFIX_ . 'orders o
        LEFT JOIN ' . _DB_PREFIX_ . 'customer c ON o.id_customer = c.id_customer
        LEFT JOIN ' . _DB_PREFIX_ . 'order_detail od ON o.id_order = od.id_order
        LEFT JOIN ' . _DB_PREFIX_ . 'product p ON od.product_id = p.id_product
        WHERE o.id_shop = ' . $store_id . ' 
        GROUP BY o.id_order, od.id_order_detail
        LIMIT ' . ($current_page * $buff) . ', ' . $buff;


        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (empty($results)) {
            echo json_encode(['error' => 'No orders found!']);
            exit;
        }

        $ordersGrouped = [];
        foreach ($results as $row) {
            $orderId = $row['id_order'];

            if (empty($row['id_order']) || empty($row['customer_email']) || empty($row['total_paid'])) {
                continue;
            }

            if (!isset($ordersGrouped[$orderId])) {
                $ordersGrouped[$orderId] = [
                    'order_id' => (string) $row['id_order'],
                    'order_status' => SmartMarketingPs::getEgoiOrderStatusName($row['current_state']),
                    'revenue' => (float) $row['total_paid'],
                    'contact_id' => $row['customer_email'],
                    'store_url' => 'https://www.e-goi.com',
                    'date' => $row['date_add'],
                    'items' => [],
                ];
            }

            if (!empty($row['product_id']) && !empty($row['product_name'])) {
                $ordersGrouped[$orderId]['items'][] = [
                    'id' => (string) $row['product_id'],
                    'name' => $row['product_name'],
                    'category' => (string) ($row['id_category_default'] ?? ''),
                    'price' => (float) $row['product_price'],
                    'quantity' => (int) $row['product_quantity'],
                ];
            }
        }

        $filteredOrders = array_filter($ordersGrouped, function ($order) {
            return !empty($order['items']);
        });

        $importOrders = array_values($filteredOrders);

        try {
            $this->apiv3->importOrders($list_id, $importOrders);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Failed to import orders: ' . $e->getMessage()]);
            exit;
        }

        $processed_count = ($current_page + 1) * $buff;
        $has_more = $processed_count < $total_orders;

        echo json_encode([
            'imported' => $current_page + 1,  // Retorna o número da próxima página
            'count' => count($importOrders),  // Número real de pedidos processados neste lote
            'total_orders' => $total_orders,  // Total de pedidos para esta loja
            'has_more' => $has_more          // Indica se ainda há mais pedidos para processar
        ]);
        exit;
    }
}