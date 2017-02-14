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

function upgrade_module_1_6_3($object)
{
    if (Configuration::get('PL_API_VERSION') == "1.6.0" || Configuration::get('PL_API_VERSION') == "1.6.2") {
        $overrides = array('classes/helper/HelperList.php', 'controllers/admin/AdminOrdersController.php');
        foreach ($overrides as $key => $override) {
            if (!is_file(_PS_OVERRIDE_DIR_.$override) || !is_writable(_PS_OVERRIDE_DIR_.$override)) {
                return false;
            }
            rename(_PS_OVERRIDE_DIR_.$override, _PS_OVERRIDE_DIR_.$override.'.Old');
        }
    }
    return true;
}
