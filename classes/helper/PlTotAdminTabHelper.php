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

class PlTotAdminTabHelper
{
    /**
     * Function to delete admin tabs from a menu with the module name
     * @param  string $name name of the module to delete
     * @return void       
     */
    public static function deleteAdminTabs($name)
    {
        // Get collection from module if tab exists
        $tabs = Tab::getCollectionFromModule($name);
        // Initialize result
        $result = true;
        // Check tabs
        if ($tabs && count($tabs)) {
            // Loop tabs for delete
            foreach ($tabs as $tab) {
                $result &= $tab->delete();
            }
        }

        return $result;
    }

    /**
     * Add admin tabs in the menu
     * @param Array $tabs 
     *        Array[
     *          Array[
     *              id_parent => 0 || void
     *              className => Controller to link to
     *              module => modulename to easily delete when uninstalling
     *              name => name to display
     *              position => position
     *          ]
     *        ]
     */
    public static function addAdminTab($data)
    {
        if (is_array(current($data))) {
            $ids = array();

            foreach ($data as $tab) {
                $ids[] = PlTotAdminTabHelper::addAdminTab($tab);
            }

            return $ids;
        }

        // Get ID Parent
        if (isset($data['id_parent'])) {
            $id_parent = $data['id_parent'];
        } else {
            $id_parent = (int)Tab::getIdFromClassName($data['classNameParent']);
        }

        // Tab
        $tab = Tab::getInstanceFromClassName($data['className']);

        $tab->id_parent  = (int)$id_parent;
        $tab->class_name = $data['className'];
        $tab->module     = $data['module'];
        $tab->position   = Tab::getNewLastPosition((int)$id_parent);
        $tab->active     = $data['active'];

        $languages = Language::getLanguages(false);

        foreach ($languages as $lang) {
            $tab->name[(int)$lang['id_lang']] = $data['name'];
        }

        if (!$tab->save()) {
            return false;
        }

        return $tab->id;
    }
}
