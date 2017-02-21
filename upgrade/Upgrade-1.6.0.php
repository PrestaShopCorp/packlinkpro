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

function upgrade_module_1_6_0($object)
{
    if (version_compare(_PS_VERSION_, '1.6.1', '>=')) {
        if (!$object->registerHook('displayAdminOrder')) {
            return false;
        }
    }
    if (!Configuration::updateValue('PL_CREATE_DRAFT_AUTO', 1)) {
        return false;
    }
    if (!$object->registerHook('actionOrderStatusPostUpdate')) {
        return false;
    }
    if (!$object->registerHook('displayHeader')) {
        return false;
    }
    if (!Configuration::updateValue('PL_ST_AWAITING', 0)) {
        return false;
    }
    if (!$object->createTabPdf()) {
        return false;
    }

    $sql = 'SELECT column_name
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE table_name = "'._DB_PREFIX_.'packlink_orders"
                AND table_schema = "'._DB_NAME_.'"
                AND column_name = "pdf"';
    $column = Db::getInstance()->getRow($sql);

    if (!$column) {
        $sql = 'ALTER TABLE '._DB_PREFIX_.'packlink_orders ADD pdf VARCHAR(1500)';
    }

    if (!Db::getInstance()->execute($sql)) {
        return false;
    }

    $sql = 'SELECT id_order
                FROM `'._DB_PREFIX_.'packlink_orders`';
    $ids = Db::getInstance()->ExecuteS($sql);

    foreach ($ids as $key => $id) {
        $object->createPacklinkDetails($id['id_order']);
    }

    return true;
}
