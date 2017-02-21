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
<script type="text/javascript">$(function () {
    $('#expeditionTab').insertBefore('#myTab');
    $('#expeditionPl').insertAfter('#expeditionTab');
});
  //  $('#expeditionPl').after('hr');
</script>
<ul class="nav nav-tabs" id="expeditionTab">
    <li class="active">
        <a href="#shipping">
            <i class="icon-truck "></i>
            {l s='Ship with Packlink PRO' mod='packlink'}
        </a>
    </li>
</ul>
<div class="tab-content panel" id="expeditionPl">
    <div class="tab-pane active">                           
        <div class="table-responsive">
            <table class="table">
                <tbody>
                    <tr>
                        <td style="border:none;"><img src="{$simple_link|escape:'html':'UTF-8'}views/img/logo-pl.svg" width="150px;"></td>
                        <td style="border:none;"><span style="font-weight: normal;">{$reference|escape:'html':'UTF-8'}</span></td>
                        <td style="border:none;text-align:right;"><a class="btn btn-default" target="{$target|escape:'html':'UTF-8'}" href="{$link_suivi|escape:'html':'UTF-8'}"><i class="{$iconBtn|escape:'html':'UTF-8'}"></i>  {$suivi|escape:'html':'UTF-8'}</a></td>
                    </tr>
                </tbody>
           </table>          
        </div>
    </div>
</div>
