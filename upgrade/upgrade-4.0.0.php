<?php
function upgrade_module_4_0_0($module)
{
    PrestaShopLogger::addLog("[EGOI-PS8]::" . __FUNCTION__ . "::LOG: START UPGRADE TO 4.0.0");

    $return = true;
    $sql = array();

    // Update the DBs
    $installFile = dirname(__FILE__) . '/../install/install.php';
    if (file_exists($installFile)) {
        include $installFile;
    } else {
        PrestaShopLogger::addLog("[EGOI-PS8]::" . __FUNCTION__ . "::ERROR: install.php not found!");
        return false;
    }

    foreach ($sql as $s) {
        if (!Db::getInstance()->execute($s)) {
            PrestaShopLogger::addLog("[EGOI-PS8]::" . __FUNCTION__ . "::ERROR: Failed to execute SQL query: " . $s);
            $return = false;
        }
    }

    if (!$return) {
        PrestaShopLogger::addLog("[EGOI-PS8]::" . __FUNCTION__ . "::ERROR: Stopping upgrade due to previous errors.");
        return false;
    }

    // Insert Egoi Basic Fields
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
            PrestaShopLogger::addLog("[EGOI-PS8]::" . __FUNCTION__ . "::ERROR: Failed to insert egoi_order_state: " . $state['name']);
            return false;
        }
    }

    //Mapping Egoi States with Prestashop States
    $egoiStateMap = Db::getInstance()->executeS('SELECT egoi_id FROM `' . _DB_PREFIX_ . 'egoi_order_states`');
    if (!$egoiStateMap) {
        PrestaShopLogger::addLog("[EGOI-PS8]::" . __FUNCTION__ . "::ERROR: Failed to fetch egoi_order_states");
        return false;
    }

    $validEgoiIds = array_column($egoiStateMap, 'egoi_id');

    $mapping = [
        ['prestashop_state_id' => 13, 'egoi_id' => 2, 'type' => 'order'],
        ['prestashop_state_id' => 1, 'egoi_id' => 1, 'type' => 'order'],
        ['prestashop_state_id' => 6, 'egoi_id' => 3, 'type' => 'order'],
        ['prestashop_state_id' => 5, 'egoi_id' => 4, 'type' => 'order'],
        ['prestashop_state_id' => 12, 'egoi_id' => 2, 'type' => 'order'],
        ['prestashop_state_id' => 9, 'egoi_id' => 2, 'type' => 'order'],
        ['prestashop_state_id' => 10, 'egoi_id' => 2, 'type' => 'order'],
        ['prestashop_state_id' => 2, 'egoi_id' => 1, 'type' => 'order'],
        ['prestashop_state_id' => 8, 'egoi_id' => 3, 'type' => 'order'],
        ['prestashop_state_id' => 3, 'egoi_id' => 2, 'type' => 'order'],
        ['prestashop_state_id' => 7, 'egoi_id' => 3, 'type' => 'order'],
        ['prestashop_state_id' => 11, 'egoi_id' => 2, 'type' => 'order'],
        ['prestashop_state_id' => 4, 'egoi_id' => 4, 'type' => 'order'],
    ];

    $orderStates = OrderState::getOrderStates((int)Context::getContext()->language->id);

    foreach ($orderStates as $state) {
        $map = null;
        foreach ($mapping as $item) {
            if ($item['prestashop_state_id'] == $state['id_order_state']) {
                $map = $item;
                break;
            }
        }

        if (!$map) {
            $map = ['prestashop_state_id' => $state['id_order_state'], 'egoi_id' => 5, 'type' => 'order']; // Estado 'unknown' por padrão
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
            PrestaShopLogger::addLog("[EGOI-PS8]::" . __FUNCTION__ . "::ERROR: Failed to insert order state mapping for Prestashop ID " . $map['prestashop_state_id']);
            return false;
        }
    }

    if (!$module->updateMenu()) {
        PrestaShopLogger::addLog("[EGOI-PS8]::" . __FUNCTION__ . "::ERROR: Failed to update menu.");
        return false;
    }

    PrestaShopLogger::addLog("[EGOI-PS8]::" . __FUNCTION__ . "::UPGRADE TO 4.0.0 SUCCESSFUL");
    return true;
}
?>
