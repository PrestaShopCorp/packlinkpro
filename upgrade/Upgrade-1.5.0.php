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

function upgrade_module_1_5_0($object)
{
    if (!Configuration::deleteByName('PL_API_FIRSTNAME')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_LASTNAME')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_EMAIL')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_BOUTIQUE')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_ADD1')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_ADD2')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_PHONE')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_CITY_NAME')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_COUNTRY_NAME')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_STATE')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_CITY')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_CITY_ID')) {
        return false;
    }
    if (!Configuration::deleteByName('PL_API_COUNTRY')) {
        return false;
    }

    if (!$object->createTab()) {
        return false;
    }

    if (!Configuration::updateValue('PL_ST_AWAITING', 0)) {
        return false;
    }

    $object->registerHook('actionObjectOrderUpdateAfter');
    $object->registerHook('actionOrderHistoryAddAfter');
    $object->registerHook('actionOrderStatusPostUpdate');

    if (!Db::getInstance()->Execute('
                    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'packlink_wait_draft` (
                    `id_order` int(11) NOT NULL AUTO_INCREMENT,
                    `date_add` DATE,
                    PRIMARY KEY (`id_order`) )')) {
        return false;
    }
    if (!Db::getInstance()->Execute('
                    CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'packlink_done_draft` (
                    `id_order` int(11) NOT NULL AUTO_INCREMENT,
                    `date_add` DATE,
                    PRIMARY KEY (`id_order`) )')) {
        return false;
    }
    
    return true;
}
