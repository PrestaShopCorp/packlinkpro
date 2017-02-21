{*
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
*}

<fieldset id="expeditionTab" style="margin-top: 15px;">
    <legend>{l s='Ship with Packlink PRO' mod='packlink'}</legend>
    <table id="expeditionPl">
        <tbody>
            <tr>
                <th style="border:none;"><img src="{$simple_link|escape:'html':'UTF-8'}views/img/logo-pl.svg" width="150px;"></th>
                <th style="border:none;"><span style="font-weight: normal;">{$reference|escape:'html':'UTF-8'}</span></th>
                <th style="border:none;padding-left: 15px;"><a class="button" target="{$target|escape:'html':'UTF-8'}" href="{$link_suivi|escape:'html':'UTF-8'}"><img src="{$simple_link|escape:'html':'UTF-8'}{$img15|escape:'html':'UTF-8'}"> {$suivi|escape:'html':'UTF-8'}</a></th>
            </tr>
        </tbody>
   </table> 
</fieldset>