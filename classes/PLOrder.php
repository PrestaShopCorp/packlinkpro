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

class PLOrder extends ObjectModel
{
    
    public $id_order;
    
    public $draft_reference;

    public $postcode;

    public $postalzone;

    public $details;

    public $pdf;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'packlink_orders',
        'primary' => 'id_order',
        'multilang' => false,
        'fields' => array(
            'id_order' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'draft_reference' => array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true),
            'postcode' => array('type' => self::TYPE_STRING),
            'postalzone' => array('type' => self::TYPE_STRING),
            'details' => array('type' => self::TYPE_STRING),
            'pdf' => array('type' => self::TYPE_STRING),
        )
    );
}
