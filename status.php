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

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/packlink.php');

$events = Tools::jsonDecode(Tools::file_get_contents("php://input"));
$sql = 'SELECT id_order FROM '._DB_PREFIX_.'packlink_orders WHERE draft_reference = "'.pSQL($events->data->shipment_reference).'"';
$id = Db::getInstance()->getValue($sql);
$order = new Order($id);

$packlink = new Packlink();
$packlink->createPacklinkDetails($id);
$packlink->logs[] = $events;
$packlink->logs[] = "Order id : ".$id.PHP_EOL;
if ($events->event == "shipment.carrier.success" && Configuration::get('PL_ST_PENDING') && Configuration::get('PL_ST_PENDING') != 0) {
    $new_state = Configuration::get('PL_ST_PENDING');
} elseif ($events->event == "shipment.label.ready" && Configuration::get('PL_ST_READY') && Configuration::get('PL_ST_READY') != 0) {
    sleep(10); //event arrive dans le meme temps
    $new_state = Configuration::get('PL_ST_READY');
} elseif ($events->event == "shipment.tracking.update" && Configuration::get('PL_ST_TRANSIT') && Configuration::get('PL_ST_TRANSIT') != 0) {
    sleep(10); //event arrive dans le meme temps
    $new_state = Configuration::get('PL_ST_TRANSIT');
} elseif ($events->event == "shipment.carrier.delivered" && Configuration::get('PL_ST_DELIVERED') && Configuration::get('PL_ST_DELIVERED') != 0) {
    $new_state = Configuration::get('PL_ST_DELIVERED');
} else {
    $new_state = '';
}

$packlink->logs[] = date('d/m/Y H:i:s').' micro : '.microtime(true).' ['.getmypid(). '] new state : '.$new_state.' old state : '.$order->current_state.PHP_EOL;
$statuses_used = array();
$order_statuses = Db::getInstance()->executeS('
    SELECT `id_order_state`
    FROM `'._DB_PREFIX_.'order_history`
    WHERE `id_order` ='.(int)$id);
foreach ($order_statuses as $key => $order_statuse) {
    $statuses_used[] = $order_statuse['id_order_state'];
}

if ($new_state && !in_array($new_state, $statuses_used)) {
    $order->setCurrentState($new_state);
    $order->save();
    $packlink->logs[] = date('d/m/Y H:i:s').' micro : '.microtime(true).' ['.getmypid(). '] new state : '.$new_state.' old state : '.$order->current_state.PHP_EOL;
}

if ($packlink->debug) {
    $packlink->writeLog();
}
