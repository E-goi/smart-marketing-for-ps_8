<?php
/**
 *  Smart Marketing
 *
 *  @author    E-goi
 *  @copyright 2024 E-goi
 *  @license   LICENSE.txt
 */

$updateql = array();

//for version 3.0.6
$updateql[_DB_PREFIX_.'egoi_active_catalogs_306_1'] = 'ALTER TABLE `'._DB_PREFIX_.'egoi_active_catalogs` ADD `sync_descriptions` int(1) NOT NULL DEFAULT \'1\';';
$updateql[_DB_PREFIX_.'egoi_active_catalogs_306_2'] = 'ALTER TABLE `'._DB_PREFIX_.'egoi_active_catalogs` ADD `sync_categories` int(1) NOT NULL DEFAULT \'1\';';
$updateql[_DB_PREFIX_.'egoi_active_catalogs_306_3'] = 'ALTER TABLE `'._DB_PREFIX_.'egoi_active_catalogs` ADD `sync_related_products` int(1) NOT NULL DEFAULT \'1\';';
$updateql[_DB_PREFIX_.'egoi_active_catalogs_306_4'] = 'UPDATE `'._DB_PREFIX_.'tab` SET `icon`=\'settings\' where `module`=\'smartmarketingps\' AND `class_name`=\'Account\';';
$updateql[_DB_PREFIX_.'egoi_active_catalogs_306_5'] = 'UPDATE `'._DB_PREFIX_.'tab` SET `icon`=\'sync\' where `module`=\'smartmarketingps\' AND `class_name`=\'Sync\';';
$updateql[_DB_PREFIX_.'egoi_active_catalogs_306_6'] = 'UPDATE `'._DB_PREFIX_.'tab` SET `icon`=\'textsms\' where `module`=\'smartmarketingps\' AND `class_name`=\'SmsNotifications\';';
$updateql[_DB_PREFIX_.'egoi_active_catalogs_306_7'] = 'UPDATE `'._DB_PREFIX_.'tab` SET `icon`=\'shop\' where `module`=\'smartmarketingps\' AND `class_name`=\'Products\';';
$updateql[_DB_PREFIX_.'egoi_active_catalogs_306_8'] = 'UPDATE `'._DB_PREFIX_.'tab` SET `icon`=\'shopping_basket\' where `module`=\'smartmarketingps\' AND `class_name`=\'Ecommerce\';';
