{*debug*}
{if isset($help)}
<!-- Button trigger modal -->
<button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#Help" onClick="return false;" style=" margin-top: -8px">
        Help
      </button>

<!-- Modal -->
<div class="modal fade" id="Help" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="Label">Help</h4>
      </div>
      <div class="modal-body">
        {$help.category.description}
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
{/if}
<strong>Fields</strong> marked as bold are required
<hr>
<table class="form" width="100%">
    <tr>
        <td width="33%" style="vertical-align: top;">
            <table class="form" width="100%">
                {if empty($form.guid) && empty($form.id)}
                <tr>
                    <td class="text-right" width="120px" style="font-weight: bold;">Order for : </td>
                    <td>
                        <select id="order_for" name="form[order_for]" class="normal">
                            <option value="">--</option>
                            <option value="mam"{if isset($form.order_for) && $form.order_for == 'mam'} selected="selected"{/if}>M -a- M</option>
                            <option value="pa"{if isset($form.order_for) && $form.order_for == 'pa'} selected="selected"{/if}>PlatesAhead</option>
                        </select>
                    </td>
                </tr>
                {else}
                <input type="hidden" name="form[order_for]" value="{$form.order_for}">
                {/if}
                <tr>
                    <td class="text-right" style="font-weight: bold; width: 120px;">BIZ : </td>
                    <td>
                    {if isset($order) && isset($order.type) && $order.type == 'so'}
                        <select name="form[biz_id]" class="normal">
                            <option value="">--</option>
                            {foreach from=$bizes item=row}
                            <option value="{$row.biz.id}"{if isset($form.biz_id) && $form.biz_id == $row.biz.id} selected="selected"{/if}>{$row.biz.doc_no_full|escape:'html'}</option>
                            {/foreach}
                        </select>
                    {else}
                        <input type="text" id="order-biz" name="form[biz_title]" class="normal"{if isset($form.biz_title)} value="{$form.biz_title}"{/if}>
                        <input type="hidden" id="order-biz-id" name="form[biz_id]" value="{if isset($form.biz_id)}{$form.biz_id}{else}0{/if}">
                    {/if}
                    </td>
                </tr>
                <tr height="32">
                    <td class="text-right" style="font-weight: bold;">Buyer Company : </td>
                    <td>
                    {if isset($order) && isset($order.type) && $order.type == 'so'}
                        {$order.company.title|escape:'html'}<input type="hidden" name="form[company_id]" value="{$order.company_id}">
                    {else}
                        <select id="companies" name="form[company_id]" class="normal" onchange="get_persons_by_company(this.value);">
                            <option value="0">--</option>
                            {foreach from=$companies item=row}
                            <option value="{$row.company.id}"{if isset($form.company_id) && $row.company.id == $form.company_id} selected="selected"{/if}>{$row.company.title|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                    {/if}
                </tr>                
                <tr>
                    <td class="text-right">Person : </td>
                    <td>
                        <select id="persons" name="form[person_id]" class="normal">
                            <option value="0">--</option>
                            {if isset($persons)}{foreach from=$persons item=row}
                            <option value="{$row.person.id}"{if isset($form.person_id) && $row.person.id == $form.person_id} selected="selected"{/if}>{$row.person.full_name|escape:'html'}</option>
                            {/foreach}{/if}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">Buyer Ref. : </td>
                    <td><input type="text" name="form[buyer_ref]" class="normal"{if isset($form.buyer_ref)} value="{$form.buyer_ref}"{/if}></td>
                </tr>
                <tr>
                    <td class="text-right">Supplier Ref. : </td>
                    <td><input type="text" name="form[supplier_ref]" class="normal"{if isset($form.supplier_ref)} value="{$form.supplier_ref}"{/if}></td>
                </tr>
                <tr>
                    <td class="text-right">Price  Equivalent : </td>
                    <td><input type="text" name="form[price_equivalent]" class="normal"{*if isset($form.supplier_ref)} value="{$form.supplier_ref}"{/if*}></td>
                </tr>
            </table>
        </td>
        <td width="33%" style="vertical-align: top;">
            <table class="form" width="100%">
                <tr>
                    <td class="text-right" width="120px" style="font-weight: bold;">Delivery Basis : </td>
                    <td>
                        <select name="form[delivery_point]" class="normal" onchange="show_delivery_details(this.value);">
                            <option value=""{if !isset($form.delivery_point) || empty($form.delivery_point)} selected="selected"{/if}>--</option>
                            <option value="col"{if isset($form.delivery_point) && $form.delivery_point == 'col'} selected="selected"{/if}>Collection</option>
                            <option value="del"{if isset($form.delivery_point) && $form.delivery_point == 'del'} selected="selected"{/if}>Delivered</option>
                            <!--{*
                            <option value="">--</option>
                            <option value="exw"{if isset($form.delivery_point) && $form.delivery_point == 'exw'} selected="selected"{/if}>EXW</option>
                            <option value="fca"{if isset($form.delivery_point) && $form.delivery_point == 'fca'} selected="selected"{/if}>FCA</option>
                            <option value="fas"{if isset($form.delivery_point) && $form.delivery_point == 'fas'} selected="selected"{/if}>FAS</option>
                            <option value="fob"{if isset($form.delivery_point) && $form.delivery_point == 'fob'} selected="selected"{/if}>FOB</option>
                            <option value="cfr"{if isset($form.delivery_point) && $form.delivery_point == 'cfr'} selected="selected"{/if}>CFR</option>
                            <option value="cif"{if isset($form.delivery_point) && $form.delivery_point == 'cif'} selected="selected"{/if}>CIF</option>
                            <option value="cip"{if isset($form.delivery_point) && $form.delivery_point == 'cip'} selected="selected"{/if}>CIP</option>
                            <option value="cpt"{if isset($form.delivery_point) && $form.delivery_point == 'cpt'} selected="selected"{/if}>CPT</option>
                            <option value="dat"{if isset($form.delivery_point) && $form.delivery_point == 'dat'} selected="selected"{/if}>DAT</option>
                            <option value="dap"{if isset($form.delivery_point) && $form.delivery_point == 'dap'} selected="selected"{/if}>DAP</option>
                            <option value="ddp"{if isset($form.delivery_point) && $form.delivery_point == 'ddp'} selected="selected"{/if}>DDP</option>
                            *}-->
                        </select>
                    </td>
                </tr>                
                <tr id="delivery-details-1"{if isset($form.delivery_point) && ($form.delivery_point == 'col' || $form.delivery_point == 'exw' || $form.delivery_point == 'fca')} style="display: none;"{/if}>
                    <td class="form-td-title-b">Delivery Point : </td>
                    <td><input type="text" name="form[delivery_town]" class="normal"{if isset($form.delivery_town)} value="{$form.delivery_town}"{/if}></td>
                </tr>                
                <tr>
                    <td id="delivery-time" class="form-td-title-b">{if isset($form.delivery_point) && ($form.delivery_point == 'col' || $form.delivery_point == 'exw' || $form.delivery_point == 'fca')}Load Readiness{else}Delivery Time{/if} : </td>
                    <td><input type="text" id="delivery_date" name="form[delivery_date]" class="normal"{if isset($form.delivery_date)} value="{$form.delivery_date}"{/if}></td>
                </tr>
                <tr id="delivery-details-2"{if isset($form.delivery_point) && ($form.delivery_point == 'col' || $form.delivery_point == 'exw' || $form.delivery_point == 'fca')} style="display: none;"{/if}>
                    <td  class="form-td-title">Delivery Cost : </td>
                    <td><input type="text" name="form[delivery_cost]" class="normal"{if isset($form.delivery_cost) && !empty($form.delivery_cost)} value="{$form.delivery_cost}"{/if}>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Order Alert Date : </td>
                    <td><input type="text" id="alert_date" name="form[alert_date]" class="normal" value="{if isset($form.alert_date) && !empty($form.alert_date)}{$form.alert_date|escape:'html'|date_format:'d/m/Y'}{else}{$smarty.now|date_format:'d/m/Y'}{/if}"></td>
                </tr>
            </table>        
        </td>
        <td valign="top" style="vertical-align: top;">
            <table class="form" width="100%">
                {if isset($form.type) && !empty($form.type)}
                <tr height="32">
                    <td class="form-td-title-b">Order Type : </td>
                    <td>{if $form.type == 'so'}Self-service{else}Through dialog{/if}</td>
                </tr>
                {/if}
                {if !empty($form.status)}                
                <tr height="32">
                    <td class="form-td-title-b">Order Status : </td>
                    <td>{if isset($form.status_title)}{$form.status_title}{else}<i>Unregistered</i>{/if}</td>
                </tr>
                {/if}
                <tr>
                    <td class="text-right" width="120px" style="font-weight: bold;">Invoicing Basis : </td>
                    <td>
                        <select id="invoicing_type" name="form[invoicingtype_id]" class="normal">
                            <option value="0">--</option>
                            {foreach from=$invoicingtypes item=row}
                            <option value="{$row.invoicingtype.id}"{if isset($form.invoicingtype_id) && $row.invoicingtype.id == $form.invoicingtype_id} selected="selected"{/if}>{$row.invoicingtype.title|escape:'html'}</option>
                            {/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">or : </td>
                    <td><input type="text" id="invoicing_type_new" name="form[invoicingtype_new]" class="normal"{if isset($form.invoicingtype_new)} value="{$form.invoicingtype_new|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="text-right" style="font-weight: bold;">Payment Term : </td>
                    <td>
                        <select id="payment_type" name="form[paymenttype_id]" class="normal">
                            <option value="0">--</option>
                            {foreach from=$paymenttypes item=row}
                            <option value="{$row.paymenttype.id}"{if isset($form.paymenttype_id) && $row.paymenttype.id == $form.paymenttype_id} selected="selected"{/if}>{$row.paymenttype.title|escape:'html'}</option>
                            {/foreach}                            
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">or : </td>
                    <td><input type="text" id="payment_type_new" name="form[paymenttype_new]" class="normal"{if isset($form.paymenttype_new)} value="{$form.paymenttype_new|escape:'html'}"{/if}></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<input type="hidden" id="dimension_unit" name="form[dimension_unit]" value="{if isset($form.dimension_unit)}{$form.dimension_unit}{/if}">
<input type="hidden" id="weight_unit" name="form[weight_unit]" value="{if isset($form.weight_unit)}{$form.weight_unit}{/if}">
<input type="hidden" id="price_unit" name="form[price_unit]" value="{if isset($form.price_unit)}{$form.price_unit}{/if}">
<input type="hidden" id="currency" name="form[currency]" value="{if isset($form.currency)}{$form.currency}{/if}">
<input type="hidden" id="positions_count" name="form[positions_count]" value="{if !empty($positions)}{count($positions)}{else}0{/if}">
<div class="pad1"></div>

<h3>Positions</h3>
<table id="positions" class="list" width="100%"{if empty($positions)} style="display:none;"{/if}><tbody>
    <tr class="top-table">
        <th width="3%" class="text-center">Id</th>
        <th width="8%">Steel Grade</th>
        <th>Thickness<br><span class="lbl-dim">{if isset($form.dimension_unit)}{$form.dimension_unit}{/if}</span></th>
        <th>Width<br><span class="lbl-dim">{if isset($form.dimension_unit)}{$form.dimension_unit}{/if}</span></th>
        <th>Length<br><span class="lbl-dim">{if isset($form.dimension_unit)}{$form.dimension_unit}{/if}</span></th>
        <th>Unit Weight<br><span class="lbl-wgh">{if isset($form.weight_unit)}{$form.weight_unit}{/if}</span></th>
        <th>Qtty<br>pcs</th>
        <th>Weight<br><span class="lbl-wgh">{if isset($form.weight_unit)}{$form.weight_unit|wunit}{/if}</span></th>
        {*<th>Weighted Weight</th>*}
        <th>Price<br><span class="lbl-price">{if isset($price_unit)}{if isset($form.currency)}{$form.currency|cursign}{/if}/{$price_unit|wunit}{/if}</span></th>
        <th>Value<br><span class="lbl-cur">{if isset($form.currency)}{$form.currency|cursign}{/if}</span></th>
        <th width="8%" id="delivery-time-th">{if isset($form.delivery_point) && ($form.delivery_point == 'col' || $form.delivery_point == 'exw' || $form.delivery_point == 'fca')}Load Readiness{else}Delivery Time{/if}</th>
        <th>Stock<br>Delivery Time</th>
        <th width="8%">Internal Notes</th>
        <th width="8%">Location</th>
        <th width="8%">Plate Ids</th>
        <th></th>
    </tr>
    {foreach name=i from=$positions item=row}
    {include file="templates/html/order/control_position.tpl" row_index=$smarty.foreach.i.index+1 row=$row}
    {/foreach}
    {*if $smarty.session.user.id == '1671'}{debug}{/if*}
</tbody></table>
<span id="lbl-positions"{if !empty($positions)} style="display: none;"{/if}>No positions</span>
<div class="pad1"></div>

<table class="form" width="100%">
    <tr>
        <td class="text-right text-top" width="120px">Order Notes : </td>
        <td><textarea name="form[description]" class="max" rows="5">{if isset($form.description) && !empty($form.description)}{$form.description|replace:'\"':'"'}{/if}</textarea></td>
    </tr>
</table>

