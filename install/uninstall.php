<?php
/**
 *  Smart Marketing
 *
 *  @author    E-goi
 *  @copyright 2018 E-goi
 *  @license   LICENSE.txt
 */
//Array for SQL Tables Names
$sql = array();
$sql[_DB_PREFIX_.'egoi'] = true;
$sql[_DB_PREFIX_.'egoi_forms'] = true;
$sql[_DB_PREFIX_.'egoi_customers'] = true;
$sql[_DB_PREFIX_.'egoi_map_fields'] = true;
$sql[_DB_PREFIX_.'egoi_sms_notif_order_status'] = true;
$sql[_DB_PREFIX_.'egoi_sms_notif_messages'] = true;
$sql[_DB_PREFIX_.'egoi_sms_notif_order_reminder'] = true;
$sql[_DB_PREFIX_.'egoi_sms_notif_reminder_messages'] = true;
$sql[_DB_PREFIX_.'egoi_active_catalogs'] = true;
$sql[_DB_PREFIX_.'egoi_customer_uid'] = true;
$sql[_DB_PREFIX_.'egoi_order_states'] = true;
$sql[_DB_PREFIX_.'egoi_prestashop_order_state_map'] = true;

//Array for SQL Triggers Names
$sqlTrigger[_DB_PREFIX_.'egoi_after_order_state_insert'] = true;
