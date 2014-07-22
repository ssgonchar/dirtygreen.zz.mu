<table width="100%">
    <tr>
        <td width="50%" class="text-top">
            <table class="form">
                <tr>
                    <td class="form-td-title-b">Title</td>
                    <td><input type="text" name="form[title]" class="wide"{if isset($form) && !empty($form.title)} value="{$form.title|escape:'html'}"{/if}></td>
                </tr>
                {*
                <tr>
                    <td class="form-td-title text-top">Description</td>
                    <td><textarea name="form[description]" class="wide" rows="3">{if isset($form) && !empty($form.description)}{$form.description|escape:'html'}{/if}</textarea></td>
                </tr>
                *}
                <tr>
                    <td class="form-td-title-b">Dimensions</td>
                    <td>
                        <select name="form[dimensions]" class="narrow">
                            <option value=""{if !isset($form)} selected="selected"{/if}>--</option>
                            <option value="mm/mt"{if isset($form) && $form.dimensions == 'mm/mt'} selected="selected"{/if}>mm & ton</option>
                            <option value="in/lb"{if isset($form) && $form.dimensions == 'in/lb'} selected="selected"{/if}>inch & lb</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Currency</td>
                    <td>
                        <select name="form[currency]" class="narrow">
                            <option value=""{if !isset($form)} selected="selected"{/if}>--</option>
                            <option value="usd"{if isset($form) && $form.currency == 'usd'} selected="selected"{/if}>$</option>
                            <option value="eur"{if isset($form) && $form.currency == 'eur'} selected="selected"{/if}>&euro;</option>
                        </select>
                    </td>
                </tr>
                <tr><td><div class="pad"></div></td></tr>
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
                <tr><td><div class="pad"></div></td></tr>
                <tr>
                    <td class="form-td-title-b">Email For Orders : </td>
                    <td><input type="text" name="form[email_for_orders]" class="normal"{if isset($form.email_for_orders)} value="{$form.email_for_orders|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Stock For : </td>
                    <td>
                        <select name="form[order_for]" class="normal">
                            <option value="">--</option>
                            <option value="mam"{if isset($form.order_for) && $form.order_for == 'mam'} selected="selected"{/if}>M -a- M</option>
                            <option value="pa"{if isset($form.order_for) && $form.order_for == 'pa'} selected="selected"{/if}>PlatesAhead</option>
                            {*foreach from=$mam_companies item=row}
                            <option value="{$row.company.alias}"{if isset($form.order_for) && $form.order_for == $row.company.alias} selected="selected"{/if}>{$row.company.title|escape:'html'}</option>
                            {/foreach *}
                        </select>                    
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div class="pad"></div>
                        <h4>Locations</h4>
                        <table class="form">
                        {foreach from=$locations item=row}            
                            <tr>
                                <td width="1%"><input type="checkbox" id="location-{$row.company_id}" name="location[{$row.company_id}]" value="{$row.company_id}"{if isset($row.checked)} checked="checked"{/if}></td>
                                <td><label for="location-{$row.company_id}">{$row.company.title|escape:'html'} ({$row.company.location.title|escape:'html'})</label></td>
                            </tr>
                        {/foreach}
                        </table>                    
                    </td>
                </tr>    
            </table>        
        </td>
        <td width="50%" class="text-top">
            <!-- 
			@ 02-05-2014 SG
			убрал проверку тут: http://myroom.platesahead.com/stock
			теперь в этом блоке нет необходимостим
			
			<h4>Delivery Times Shown On WebStock</h4>
            <table class="form">
            {foreach from=$deliverytimes item=row}            
                <tr>
                    <td width="1%"><input type="checkbox" id="deliverytime-{$row.deliverytime_id}" name="deliverytime[{$row.deliverytime_id}]" value="{$row.deliverytime_id}"{if isset($row.checked)} checked="checked"{/if}></td>
                    <td><label for="deliverytime-{$row.deliverytime_id}">{$row.deliverytime.title|escape:'html'}</label></td>
                </tr>
            {/foreach}
            </table>
            <div class="pad"></div>
			-->
            <h4>Columns Shown On WebStock</h4>
            <table class="form">
                <tr>
                    <td width="1%"><input type="checkbox" id="column-unitweight" name="column[unitweight]" value="unitweight"{if isset($column_unitweight)} checked="checked"{/if}></td>
                    <td><label for="column-unitweight">Unit Weight</label></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="column-qtty" name="column[qtty]" value="qtty"{if isset($column_qtty)} checked="checked"{/if}></td>
                    <td><label for="column-qtty">Quantity</label></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="column-weight" name="column[weight]" value="weight"{if isset($column_weight)} checked="checked"{/if}></td>
                    <td><label for="column-weight">Weight</label></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="column-price" name="column[price]" value="price"{if isset($column_price)} checked="checked"{/if}></td>
                    <td><label for="column-price">Price</label></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="column-value" name="column[value]" value="value"{if isset($column_value)} checked="checked"{/if}></td>
                    <td><label for="column-value">Value</label></td>
                </tr>                
                <tr>
                    <td><input type="checkbox" id="column-deliverytime" name="column[deliverytime]" value="deliverytime"{if isset($column_deliverytime)} checked="checked"{/if}></td>
                    <td><label for="column-deliverytime">Delivery Time</label></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="column-notes" name="column[notes]" value="notes"{if isset($column_notes)} checked="checked"{/if}></td>
                    <td><label for="column-notes">Notes</label></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="column-location" name="column[location]" value="location"{if isset($column_location)} checked="checked"{/if}></td>
                    <td><label for="column-location">Location</label></td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="column-pictures" name="column[pictures]" value="pictures"{if isset($column_pictures)} checked="checked"{/if}></td>
                    <td><label for="column-pictures">Pictures</label></td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div class="separator pad"><!-- --></div>
