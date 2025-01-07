<?php
/**
 *  Smart Marketing
 *
 *  @author    E-goi
 *  @copyright 2018 E-goi
 *  @license   LICENSE.txt
 */

$sql = array();
$sql[_DB_PREFIX_.'egoi'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'egoi` (
			  `egoi_id` int(11) NOT NULL AUTO_INCREMENT,
			  `list_id` int(11) NOT NULL,
			  `client_id` int(11) NOT NULL,
			  `sync` int(11) NOT NULL,
			  `total` varchar(255) NOT NULL,
			  `track` varchar(255) NOT NULL DEFAULT \'1\',
			  `role` varchar(255) NOT NULL,
			  `newsletter_sync` int(11) NOT NULL DEFAULT \'0\',
			  `optin` int(11) NOT NULL DEFAULT \'0\',
			  `track_state` int(11) NOT NULL DEFAULT \'0\',
			  `estado` int(1) NOT NULL,
			  `social_track` int(1) DEFAULT \'0\',
			  `social_track_json` int(1) DEFAULT \'0\',
			  `social_track_id` varchar(50) DEFAULT \'0\',
			  PRIMARY KEY (`egoi_id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'egoi_customers'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'egoi_customers` (
			  `egoi_id` int(11) NOT NULL AUTO_INCREMENT,
			  `customer` varchar(255) NOT NULL,
			  `id_cart` int(11) NOT NULL,
			  `estado` int(1) NOT NULL,
			  PRIMARY KEY (`egoi_id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'egoi_map_fields'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'egoi_map_fields` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ps` varchar(255) NOT NULL, 
			  `ps_name` varchar(255) NOT NULL,
			  `egoi` varchar(255) NOT NULL,
			  `egoi_name` varchar(255) NOT NULL,
			  `status` int(1) NOT NULL, 
			  PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'egoi_sms_notif_order_status'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'egoi_sms_notif_order_status` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
			  `order_status_id` int(11) NOT NULL UNIQUE,
			  `send_client` int(1) NOT NULL DEFAULT \'0\',
			  `send_admin` int(1) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'egoi_sms_notif_messages'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'egoi_sms_notif_messages` (
			  `order_status_id` int(11) NOT NULL,
			  `lang_id` int(11) NOT NULL,
			  `client_message` varchar(2048),
			  `admin_message` varchar(2048),
			  PRIMARY KEY (`order_status_id`,`lang_id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'egoi_sms_notif_order_reminder'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'egoi_sms_notif_order_reminder` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `order_id` int(11) NOT NULL UNIQUE,
			  `send_date` varchar(512),
			  `mobile` varchar(32) NOT NULL,
			  `message` varchar(2048),
			  PRIMARY KEY (`id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'egoi_sms_notif_reminder_messages'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'egoi_sms_notif_reminder_messages` (
			  `order_status_id` int(11) NOT NULL,
			  `lang_id` int(11) NOT NULL,
			  `message` varchar(2048),
			  `active` int(1) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`order_status_id`,`lang_id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'egoi_active_catalogs'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'egoi_active_catalogs` (
              `catalog_id` int(11) NOT NULL UNIQUE,
              `language` varchar(2) NOT NULL,
              `currency` char(3) NOT NULL,
			  `active` int(1) NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`catalog_id`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'egoi_customer_uid'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'egoi_customer_uid` (
              `uid` varchar(11) NOT NULL UNIQUE,
              `email` varchar(255) NOT NULL,
			  PRIMARY KEY (`uid`),
			  INDEX store_email (email)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'egoi_order_states'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'egoi_order_states` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `egoi_id` int(11) NOT NULL,
              `name` varchar(255) NOT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY (`egoi_id`)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'egoi_prestashop_order_state_map'] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'egoi_prestashop_order_state_map` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `prestashop_state_id` int(11) NOT NULL,
    `egoi_state_id` int(11) NOT NULL,
    `type` varchar(20) NOT NULL,  
    `active` int(1) NOT NULL DEFAULT \'1\',
    PRIMARY KEY (`id`)
) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[_DB_PREFIX_.'egoi_after_order_state_insert'] = '
    CREATE TRIGGER '._DB_PREFIX_.'egoi_after_order_state_insert
    AFTER INSERT ON '._DB_PREFIX_.'order_state_lang
    FOR EACH ROW
    BEGIN
        INSERT INTO '._DB_PREFIX_.'egoi_prestashop_order_state_map 
        (prestashop_state_id, egoi_state_id, type, active)
        VALUES (NEW.id_order_state, 1, "order", 1);
    END;
';





