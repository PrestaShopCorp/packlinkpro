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

<div class="container-fluid pl_navigation">
<img src="{$simple_link|escape:'html':'UTF-8'}views/img/logo-pl.svg" width="250px;"><br /><br />
  <h4 class="inscription">{l s='Ship your paid orders easily at the best prices with Packlink PRO. No account yet? It only takes few seconds to ' mod='packlink'}
       <a href="{$carrier_link|escape:'htmlall':'UTF-8'}" target="_blank">{l s='register online' mod='packlink'}</a>{l s=' .' mod='packlink'}</h4><br />
  <ul class="nav nav-pills">
    <li {if $tab_name == "home_settings"} class="active"{/if}><a data-toggle="pill" href="#pl_key"><span>{l s='Setup' mod='packlink'}</span></a></li>
    <li {if $tab_name == "address_form"} class="active"{/if}><a data-toggle="pill" href="#pl_address"><span>{l s='Sender address' mod='packlink'}</span></a></li>
    <li {if $tab_name == "status_form"} class="active"{/if}><a data-toggle="pill" href="#pl_status"><span>{l s='Order statuses' mod='packlink'}</span></a></li>
    <li {if $tab_name == "units_form"} class="active"{/if}  id="li-units"><a data-toggle="pill" href="#pl_units"><span>{l s='Data unit' mod='packlink'}</span></a></li>
    <li id="pl-aide"><a target="_blank" href="{$pl_aide|escape:'htmlall':'UTF-8'}"><span>{l s='Help center' mod='packlink'}</span></a></li>
  </ul>
  <br /><br />
  <div class="tab-content">
    <div id="pl_key" class="tab-pane fade {if $tab_name == 'home_settings'} in active{/if}">
              <p class="tab_title"><span>{l s='Packlink PRO connection' mod='packlink'}</span></p>
              <p class="tab_description">{l s='An API key associated with your Packlink PRO account must be indicated in the field below in order to connect Packlink PRO with PrestaShop. ' mod='packlink'}
                   <a href="{$generate_api|escape:'htmlall':'UTF-8'}" target="_blank">{l s='Generate API key now.' mod='packlink'}</a></p>
                <form class="form-horizontal" role="form" action = "{$module_link|escape:'htmlall':'UTF-8'}" method="post">
                  <div class="form-group col-sm-9">
                        <label class="control-label col-sm-3" for="PL_API_KEY">{l s='Packlink PRO API Key' mod='packlink'}</label>
                        <div class="col-sm-7">
                            <input id="PL_API_KEY" name="PL_API_KEY" value="{$PL_API_KEY|escape:'htmlall':'UTF-8'}" size="100" maxlength="100" class="form-control" required="required"/>
                        </div>
                        <input type="hidden" name="PL_tab_name" value="home_settings" />
                  </div>
                  <div class="form-group col-sm-9"> 
                  <div class="col-sm-offset-3 col-sm-9">
                    <button type="submit" name="submit-query" value="submit" class="btn btn-info">{l s='Save' mod='packlink'}</button>
                  </div></div>
                </form>

        <p class="tab_title"><span>{l s='Shipment creation preference ' mod='packlink'}</span></p>
        <p class="tab_description">{l s='Create shipments in Packlink PRO automatically once orders are paid or manually whenever you want:' mod='packlink'}</p>
        <form class="form-horizontal col-sm-9" role="form" action = "" method="post"> 
          <div class="form-group">              
                <label class="control-label col-sm-3" for="length" style="margin-right: 20px;"></label>
                <div class="col-sm-7 import-radio">
                    <label class="radio"><input type="radio"  value="1" name="createPl" {if $packlink_createPl == 1}checked="checked"{/if}>{l s='Create automatically' mod='packlink'}</label>
                    <label class="radio"><input type="radio" value="0" name="createPl" {if $packlink_createPl == 0}checked="checked"{/if}>{l s='Create manually' mod='packlink'}</label>
                </div>
                <input type="hidden" name="PL_tab_name" value="home_settings" />

          </div>
          <div class="form-group"> 
                  <div class="col-sm-offset-3 col-sm-9">
          <button type="submit" name="submit-create-pl" value="submit" class="btn btn-info">{l s='Save' mod='packlink'}</button>
          </div></div>
        </form>

    </div>
    <div id="pl_address" class="tab-pane fade {if $tab_name == 'address_form'} in active{/if}">
           <p class="tab_title"><span>{l s='“Ship from” address(es)' mod='packlink'}</span></p>
            <p class="tab_description">{l s='“Ship from” address(es) save(s) you time by prefilling shipping details in Packlink PRO. You can configure and edit them from' mod='packlink'} <a href="{$link_pro_addr|escape:'htmlall':'UTF-8'}" target="_blank">{l s='Packlink PRO settings.' mod='packlink'}</a></p>
            {if $show_address}
                {foreach from=$warehouses item=warehouse}
                    <div class="well col-sm-3 warehouse {if $warehouse->default_selection}default-wh {else}other-wh{/if}">
                        <header>
                            <p class="title">{$warehouse->alias|escape:'htmlall':'UTF-8'}</p>
                            {if $warehouse->default_selection|escape:'htmlall':'UTF-8'}
                                <p class="btn-default">{l s='By default' mod='packlink'}</p>
                            {/if}
                        </header>
                        <p>{$warehouse->address|escape:'htmlall':'UTF-8'} {$warehouse->address2|escape:'htmlall':'UTF-8'}</p> 
                        <p>{$warehouse->postal_code|escape:'htmlall':'UTF-8'} {$warehouse->city|escape:'htmlall':'UTF-8'}</p> 
                        <p>{l s='Telephone: ' mod='packlink'}{$warehouse->phone|escape:'htmlall':'UTF-8'}</p>   
                    </div>
                {/foreach}
            {else}
                 
                 <div class="form-group col-sm-8"> 
                  <div class="col-sm-offset-2 col-sm-8">
                      <p>{l s='No "ship from" address configured in Packlink PRO!' mod='packlink'}</p>
                      <a  href="{$link_pro_addr|escape:'htmlall':'UTF-8'}" target="_blank" type="submit" value="submit" class="btn btn-info">{l s='Add' mod='packlink'}</a>
                </div></div>
            {/if}
     
    </div>


    <div id="pl_status" class="tab-pane fade {if $tab_name == 'status_form'} in active{/if}">
          <p class="tab_title"><span>{l s='Status synchronization' mod='packlink'}</span></p>
          <p class="tab_description"><a href="{$link_status|escape:'htmlall':'UTF-8'}" target="_blank">{l s='PrestaShop order statuses' mod='packlink'}</a> {l s='are synchronized with Packlink PRO shipping statuses as configured in the matching table below.' mod='packlink'}<br>
          {l s='Each time a shipment status changes in Packlink PRO, the status of its associated order in PrestaShop is updated accordingly.' mod='packlink'}</p>
            <form class="form-horizontal" role="form" action = "{$module_link|escape:'htmlall':'UTF-8'}" method="post">
              <div class="form-group col-sm-8">
                    <div style="margin-bottom: 35px;"><label class="control-label col-sm-2"></label>
                    <label class="control-label col-sm-4 col_title">{l s='Packlink PRO shipping status' mod='packlink'}</label>
                    <label class="control-label col-sm-1 egal"></label>
                    <label class="control-label col-sm-5 col_title">{l s='PrestaShop order status' mod='packlink'}</label></div>
                    <label class="control-label col-sm-2" for="select_awaiting">{l s='Status #1' mod='packlink'}</label>
                    <div class="col-sm-4"><input value="{l s='Pending' mod='packlink'}" class="form-control" readonly="readonly" /></div>
                    <label class="control-label col-sm-1 egal" for="select_awaiting">=</label>
                    <div class="col-sm-5">
                    <select class="form-control" id="select_awaiting" name="select_awaiting">
                        <option value="0">{l s='(None)' mod='packlink'}</option>
                        {foreach from=$order_state item=state}
                            {html_options values={$state.id_order_state|escape:'htmlall':'UTF-8'} output={$state.name|escape:'htmlall':'UTF-8'} selected={$status_awaiting|escape:"html":"UTF-8"}}
                        {/foreach}
                    </select></div>
                    <label class="control-label col-sm-2" for="pending">{l s='Status #2' mod='packlink'}</label>
                    <div class="col-sm-4"><input value="{l s='Processing' mod='packlink'}" class="form-control" readonly="readonly" /></div>
                    <label class="control-label col-sm-1 egal" for="pending">=</label>
                    <div class="col-sm-5">
                    <select class="form-control" id="select_pending" name="select_pending">
                        <option value="0">{l s='(None)' mod='packlink'}</option>
                        {foreach from=$order_state item=state}
                            {html_options values={$state.id_order_state|escape:'htmlall':'UTF-8'} output={$state.name|escape:'htmlall':'UTF-8'} selected={$status_pending|escape:"html":"UTF-8"}}
                        {/foreach}
                    </select></div>
                    <label class="control-label col-sm-2" for="ready">{l s='Status #3' mod='packlink'}</label>
                    <div class="col-sm-4"><input value="{l s='Ready for shipping' mod='packlink'}" class="form-control" readonly="readonly" /></div>
                    <label class="control-label col-sm-1 egal" for="select_ready">=</label>
                    <div class="col-sm-5">
                    <select class="form-control" id="select_ready" name="select_ready">
                        <option value="0">{l s='(None)' mod='packlink'}</option>
                        {foreach from=$order_state item=state}
                            {html_options values={$state.id_order_state|escape:'htmlall':'UTF-8'} output={$state.name|escape:'htmlall':'UTF-8'} selected={$status_ready|escape:"html":"UTF-8"}}
                        {/foreach}
                    </select></div>
                    <label class="control-label col-sm-2" for="transit">{l s='Status #4' mod='packlink'}</label>
                    <div class="col-sm-4"><input value="{l s='In transit' mod='packlink'}" class="form-control" readonly="readonly" /></div>
                    <label class="control-label col-sm-1 egal" for="select_transit">=</label>
                    <div class="col-sm-5">
                    <select class="form-control" id="select_transit" name="select_transit">
                        <option value="0">{l s='(None)' mod='packlink'}</option>
                        {foreach from=$order_state item=state}
                            {html_options values={$state.id_order_state|escape:'htmlall':'UTF-8'} output={$state.name|escape:'htmlall':'UTF-8'} selected={$status_transit|escape:"html":"UTF-8"}}
                        {/foreach}
                    </select></div>
                    <label class="control-label col-sm-2" for="delivered">{l s='Status #5' mod='packlink'}</label>
                    <div class="col-sm-4"><input value="{l s='Delivered' mod='packlink'}" class="form-control" readonly="readonly" /></div>
                    <label class="control-label col-sm-1 egal" for="select_delivered">=</label>
                    <div class="col-sm-5">
                    <select class="form-control" id="select_delivered" name="select_delivered">
                        <option value="0">{l s='(None)' mod='packlink'}</option>
                        {foreach from=$order_state item=state}
                            {html_options values={$state.id_order_state|escape:'htmlall':'UTF-8'} output={$state.name|escape:'htmlall':'UTF-8'} selected={$status_delivered|escape:"html":"UTF-8"}}
                        {/foreach}
                    </select></div>
                    <input type="hidden" name="PL_tab_name" value="status_form" />


              </div>
              <div class="form-group col-sm-8"> 
                  <div class="col-sm-offset-2 col-sm-8">
                      <button type="submit" name="submit-status" value="submit" class="btn btn-info">{l s='Save' mod='packlink'}</button>
              </div></div>
            </form>
    </div>



    <div id="pl_units" class="tab-pane fade {if $tab_name == 'units_form'} in active{/if}">
        <p class="tab_title"><span>{l s='Data unit conversion ' mod='packlink'}</span></p>
            <p class="tab_description">{l s='Packlink PRO works with kilograms and centimetres. Your PrestaShop might be configured with other' mod='packlink'}<a target="_blank" href="{$link_units|escape:'htmlall':'UTF-8'}"> {l s='data unit.' mod='packlink'}</a><br/>
            {l s='Please make sure the matching table below makes sense so data imported from PrestaShop to Packlink PRO corresponds.' mod='packlink'}</p>
        <form class="form-horizontal col-sm-7 unit-block" role="form" action = "{$module_link|escape:'htmlall':'UTF-8'}" method="post">
          <div class="form-group unit">
              <div style="margin-bottom:35px;">
                    <label class="control-label col-sm-4"></label>
                    <label class="control-label col-sm-2 col_title">{l s='PrestaShop' mod='packlink'}</label>
                    <label class="control-label col-sm-1 small_col"></label>
                    <label class="control-label col-sm-3 col_title">{l s='Packlink PRO' mod='packlink'}</label>
                    <label class="control-label col-sm-1 small_col"></label>
                    </div>
                <label class="control-label col-sm-4" for="weight">{l s='Weight unit ' mod='packlink'}</label>
                <div class="col-sm-2"><input id="weight" name="weight" value="{$weight|escape:'htmlall':'UTF-8'}" class="form-control" /></div>
                <label class="control-label col-sm-1 small_col" for="weight">{$unit_weight|escape:'htmlall':'UTF-8'} = </label>
                <div class="col-sm-2"><input id="weight" name="weight" value="1" class="form-control" disabled/></div>
                <label class="control-label col-sm-1 small_col" for="weight">{l s='kg' mod='packlink'}</label>
                <label class="control-label col-sm-4" for="length">{l s='Dimension unit ' mod='packlink'}</label>
                <div class="col-sm-2"><input id="length" name="length" value="{$length|escape:'htmlall':'UTF-8'}" class="form-control" /></div>
                <label class="control-label col-sm-1 small_col" for="length">{$unit_length|escape:'htmlall':'UTF-8'} = </label>
                <div class="col-sm-2"><input id="length" name="length" value="1" class="form-control" disabled/></div>
                <label class="control-label col-sm-1 small_col" for="length">{l s='cm' mod='packlink'}</label>
                <input type="hidden" name="PL_tab_name" value="units_form" />
          </div>
          <div class="form-group"> 
             <div class="col-sm-offset-4 col-sm-7">
                <button type="submit" name="submit-conversion" value="submit" class="btn btn-info">{l s='Save' mod='packlink'}</button>
          </div></div>
        </form>

        <p class="tab_title"><span>{l s='Auto-populate product data unit ' mod='packlink'}</span></p>
        <p class="tab_description">{l s='Automatically complete weight and dimension in PrestaShop catalog from Packlink PRO when such data is missing for a product you ship:' mod='packlink'}</p>
        <form class="form-horizontal col-sm-7" role="form" action = "{$module_link|escape:'htmlall':'UTF-8'}" method="post"> 
          <div class="form-group">              
                <label class="control-label col-sm-4" for="length" style="margin-right: 20px;"></label>
                <div class="col-sm-7 import-radio">
                    <label class="radio"><input type="radio"  value="1" name="import" {if $packlink_import == 1}checked="checked"{/if}>{l s='Always' mod='packlink'}</label>
                    <label class="radio"><input type="radio" value="0" name="import" {if $packlink_import == 0}checked="checked"{/if}>{l s='Never' mod='packlink'}</label>
                </div>
                <input type="hidden" name="PL_tab_name" value="units_form" />

          </div>
          <div class="form-group"> 
                  <div class="col-sm-offset-4 col-sm-7">
          <button type="submit" name="submit-import" value="submit" class="btn btn-info">{l s='Save' mod='packlink'}</button>
          </div></div>
        </form>
    </div>

  </div>
</div>

<script>
{literal}
function equalHeight(group) {
    tallest = 0;
    group.each(function() {
        $(this).css("width", "");       
        thisHeight = $(this).outerWidth();
        if(thisHeight > tallest) {
            tallest = thisHeight;
        }
    });
    group.css('min-width', tallest);
}

$(window).ready(function() {
    equalHeight($(".small_col"));
});
$("#li-units").click( function() {
    setTimeout(function() {
         equalHeight($(".small_col"));
    }, 220);
});

{/literal}
</script>


