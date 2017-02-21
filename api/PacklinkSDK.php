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

class PacklinkSDK extends PacklinkApiCall
{

    protected $api_key;

    public function __construct($api_key, $module)
    {
        if ($module->dev) {
            $url = "https://apisandbox.packlink.com/";
        } else {
            $url = "https://api.packlink.com/";
        }
        parent::__construct($url, $module);

        $this->api_key = $api_key;
    }

    public function createAnalitics($body, $api_key)
    {
        $this->action = 'POST';
        $this->endpoint = 'v1/analytics';
        $this->makeCall($this->getBody($body), $api_key);

        return $this->response;
    }

    public function createCallback($body, $api_key)
    {
        $this->action = 'POST';
        $this->endpoint = 'v1/shipments/callback';
        $this->makeCall($this->getBody($body), $api_key);

        return $this->response;
    }

    public function createDraft($params)
    {
        $this->action = 'POST';
        $this->endpoint = 'v1/shipments';
        $this->makeCall($this->getBody($params));

        return $this->response;
    }



    protected function getBody(array $fields)
    {
        $return = true;

        // if fields not empty
        if (empty($fields)) {
            $return = false;
        }

        // if not empty
        if ($return) {
            return json_encode($fields);
        }

        return $return;
    }

    /**
    *
    * @return $postal_zones : array(
        array('id' => '76', 'name' => 'France métropolitaine'),
        array('id' => '77', 'name' => 'France Corse')
    */

    public function getPdfLabels($params)
    {
        $this->action = 'GET';
        $this->endpoint = 'v1/shipments/'.$params.'/labels';
        $this->makeCall();

        return $this->response;
    }
    public function getPdfLabelsFile($params)
    {
        $this->action = 'GET';
        $this->endpoint = 'v1/shipments/'.$params.'/customs';
        $this->makeCall();

        return $this->response;
    }

    public function getWarehouses()
    {
        $this->action = 'GET';
        $this->endpoint = 'v1/clients/warehouses';
        $this->makeCall();

        return $this->response;
    }

    public function getCitiesByPostCode($postcode, $iso_code)
    {
        $this->action = 'GET';
        $language = $iso_code.'_'.strtoupper($iso_code);
        $this->endpoint = 'v1/locations/postalcodes/'.$iso_code.'/'.$postcode.'?language='.$language.'&platform=PRO&platform_country='.$iso_code;

        $this->makeCall();

        return $this->response;
    }

    public function getShippementDetails($reference)
    {
        $this->action = 'GET';
        $this->endpoint = 'v1/shipments/'.$reference;

        $this->makeCall();

        return $this->response;
    }
}
