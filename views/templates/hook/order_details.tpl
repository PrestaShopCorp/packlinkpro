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
    <script type="text/javascript">
    $(function () {
        var footab;
        if ({$version|escape:"html":"UTF-8"}) {
            footab = $(".footab").last();
            var info = $(".info-order").find('p').first();
            $("#add_carrier").insertAfter(info);
        } else {
            footab = $(".table_block").last();
        } 
        $("#ps_table").insertBefore(footab);
        $("#packlink").insertAfter(footab);
        $("#pl_table").insertBefore("#packlink");


    });
    </script>
    <p id="add_carrier"><span style="font-weight: bold;color: #333;">{l s='Carrier substitute' mod='packlink'}</span> {$carrierPL|escape:"html":"UTF-8"}</p>

    <p id="ps_table" style="font-weight: bold;color: #333;">{l s='Here is carrier you selected :' mod='packlink'}</p>
    <p id="pl_table" style="font-weight: bold;color: #333;">{l s='Here is carrier subsitute we selected :' mod='packlink'}</p>
    <table id="packlink" class="{if $version}table table-bordered{else}std{/if}">
        <thead>
            <tr>
                <th class="first_item">{l s='Date' mod='packlink'}</th>
                <th class="item" data-sort-ignore="true">{l s='Carrier' mod='packlink'}</th>
                <th data-hide="phone" class="item">{l s='Weight' mod='packlink'}</th>
                <th data-hide="phone" class="last_item" data-sort-ignore="true">{l s='Tracking number' mod='packlink'}</th>
            </tr>
        </thead>
        <tbody>
            <tr class="item">
                <td>{$date|escape:'html':'UTF-8'}</td>
                <td>{$carrierPL|escape:'html':'UTF-8'}</td>  
                <td>{if $weight > 0}{$weight|escape:"html":"UTF-8"|string_format:"%.3f"} {Configuration::get('PS_WEIGHT_UNIT')|escape:'html':'UTF-8'}{else}-{/if}</td>
                <td><a href="{$tracking_url|escape:'html':'UTF-8'}" target="_blank">
                    {foreach from=$trackings item=tracking}
                       {$tracking|escape:'html':'UTF-8'}<br />
                    {/foreach}
                </a></td>
            </tr>
        </tbody>
    </table>



