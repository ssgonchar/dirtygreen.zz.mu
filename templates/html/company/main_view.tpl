<table class="form" width="100%">
    <tr>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                {if isset($form.title_native) && !empty($form.title_native)}
                <tr>
                    <td class="form-td-title">Native Name : </td>
                    <td>{$form.title_native|undef}</td>
                </tr>
                {/if}
                {if isset($form.title_short) && !empty($form.title_short)}
                <tr>
                    <td class="form-td-title">Short Name : </td>
                    <td>{$form.title_short|undef}</td>
                </tr>
                {/if}
                {if isset($form.title_trade) && !empty($form.title_trade)}
                <tr>
                    <td class="form-td-title">Trade Name : </td>
                    <td>{$form.title_trade|undef}</td>
                </tr>
                {/if}
            </table>        
        </td>
        <td class="text-top">
            <table class="form" width="100%">
                {if isset($parent)}
                <tr>
                    <td class="form-td-title">Head Office : </td>
                    <td>
                        {if isset($parent)}<a href="/company/{$parent.id}">{$parent.title}</a>{else}{''|undef}{/if}
                    </td>
                </tr>
                {/if}
                <tr>
                    <td class="form-td-title">Location : </td>
                    {*<td>{$form.location_title|undef}</td>*}
                    <td>{$form.type_id|co_type_title}</td>
                </tr>
            </table>        
        </td>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Status : </td>
                    {*<td>{$form.status_title|undef}</td>*}
                    <td>{$form.status_id|co_status_title}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Relation : </td>
                    {*<td>{$form.relation_title|undef}</td>                    *}
                    <td>{$form.relation_id|co_relation_title}</td>
                </tr>
                {if $form.relation_id == $smarty.const.CO_RELATION_STOCK_AGENT || $form.relation_id == $smarty.const.CO_RELATION_SUPPLIER ||
				$form.relation_id == $smarty.const.CO_RELATION_SERVICE_PROVIDER}
                {if $form.handling_cost > 0}
                <tr>
                    <td class="form-td-title">Handling Cost : </td>
                    <td>{$form.currency|cursign} {$form.handling_cost|string_format:'%.2f'}</td>
                </tr>
                {/if}
                {if $form.storage_cost > 0}
                <tr>
                    <td class="form-td-title">Storage Cost : </td>
                    <td>{$form.currency|cursign} {$form.storage_cost|string_format:'%.2f'} / month</td>
                </tr>
                {/if}
				{if $form.storage_cost > 0 || $form.handling_cost > 0}
				<tr>
                    <td class="form-td-title"></td>
                    <td><a href="/company/{$form.id}/prices">price timeline</a></td>
                </tr>
				{/if}	
                {/if}
            </table>        
        </td>        
    </tr>
</table>
<div class="pad"></div>

<table class="form" width="100%">
    <tr>
        <td width="33%" class="text-top">
            <h4>Contact Data</h4>
            <table class="form" width="100%">
                {foreach from=$contactdata item=row name=cd}
                <tr id="cd-{$smarty.foreach.cd.index}">                   
                    <td class="form-td-title">{$row.type_text} : </td>
                    <td>
                        {if $row.type == 'email'}<a href="/email/compose/company:{$form.id};recipient:{$row.title}">{$row.title}</a>
                        {elseif $row.type == 'www'}<a href="{$row.title|http}" target="_blank">{$row.title}</a>
                        {else}{$row.title}{/if}{if !empty($row.description)} ({$row.description}){/if}</td>
                </tr>                
                {/foreach}                
            </table>            
        </td>
        <td class="text-top">
            <h4>Key Contact</h4>
            <table class="form">
                <tr>
                    <td class="form-td-title">{if !empty($form.key_contact.jobposition)}{$form.key_contact.jobposition.title}{else}Employee{/if} : </td>
                    <td>
                        {if isset($form.key_contact) && !empty($form.key_contact)}
                            <a href="/person/{$form.key_contact.id}">{$form.key_contact.full_name}</a>
                        {else}
                            {''|undef}
                        {/if}
                    </td>
                </tr>
                {if isset($form.key_contact_contacts)}
                {foreach from=$form.key_contact_contacts item=row}
                <tr>
                    <td class="form-td-title">{$row.type_text} : </td>
                    <td>{$row.title}</td>
                </tr>
                {/foreach}
                <tr><td><div class="pad1"></div></td></tr>
                {/if}                
                <tr>
                    <td class="form-td-title">MaM Genius : </td>
                    <td>{if isset($form.mam_genius)}{$form.mam_genius.full_login}{else}{''|undef}{/if}</td>
                </tr>                
            </table>            
        </td>
        <td width="33%" class="text-top">
            <h4>Address</h4>
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Country : </td>
                    <td>{if isset($form.country)}{$form.country.title|undef}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Region : </td>
                    <td>{if isset($form.region)}{$form.region.title|undef}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">City : </td>
                    <td>{if isset($form.city)}{$form.city.title|undef}{else}{''|undef}{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Zip : </td>
                    <td>{$form.zip|undef}</td>
                </tr>
                {if !empty($form.address)}
                <tr>
                    <td class="form-td-title text-top">Address : </td>
                    <td class=" text-top">{$form.address|undef}</td>
                </tr>
                {/if}
                {if !empty($form.pobox)}
                <tr>
                    <td class="form-td-title">P. O. Box : </td>
                    <td>{$form.pobox|undef}</td>
                </tr>
                {/if}
                {if !empty($form.delivery_address)}
                <tr>
                    <td class="form-td-title text-top">Delivery Address : </td>
                    <td class=" text-top">{$form.delivery_address|nl2br|undef}</td>
                </tr>
                {/if}
                <tr>
                    <td class="form-td-title">Rail Access : </td>
                    <td>{if isset($form.rail_access)}{$form.rail_access|undef}{else}{''|undef}{/if}</td>
                </tr>
            </table>            
        </td>        
    </tr>
</table>
<div class="pad"></div>

<table class="form" width="100%">
    <tr>
        <td width="33%" class="text-top">
            <h4>Activities</h4>
            {if empty($activities)}{''|undef}{else}
            <table class="form" width="100%">
                {foreach from=$activities item=row name=ac}
                <tr>
                    <td>
                        {if isset($row.speciality)}{$row.speciality.title}
                        {elseif isset($row.activity)}{$row.activity.title}
                        {else}{$row.industry.title}
                        {/if}<br>
                    </td>
                </tr>                
                {/foreach}                
            </table>
            {/if}
        </td>
        <td class="text-top">
            <h4>Products</h4>
            {if empty($co_products)}{''|undef}{else}
            <table class="form" width="100%">
                {foreach from=$co_products item=row name=p}
                <tr>
                    <td>
                        {if isset($row.product)}{$row.product.title}
                        {else}{$row.group.title}{/if}
                    </td>
                </tr>                
                {/foreach}                
            </table>
            {/if}
        </td>
        <td width="33%" class="text-top">
            <h4>Feedstock</h4>
            {if empty($co_feedstocks)}{''|undef}{else}
            <table class="form" width="100%">
                {foreach from=$co_feedstocks item=row}
                <tr>
                    <td>
                        {if isset($row.product)}{$row.product.title}
                        {else}{$row.group.title}{/if}
                    </td>
                </tr>                
                {/foreach}                
            </table>
            {/if}
        </td>        
    </tr>
</table>
<div class="pad"></div>

<table class="form" width="100%">
    <tr>
        <td width="33%" class="text-top">
            <h4>Active BIZ</h4>
            {if empty($bizes)}{''|undef}{else}
            <table class="form" width="100%">
                {foreach from=$bizes item=row name=ac}
                <tr>
                    <td><a href="/biz/{$row.biz.id}">{$row.biz.doc_no} {$row.biz.title}</a></td>
                    <td>{* $row.role *}
                        {* if $row.role == 'producer'}Producer
                        {elseif $row.role == 'seller'}Seller
                        {elseif $row.role == 'transport'}Transport
                        {elseif $row.role == 'user'}User
                        {elseif $row.role == 'buyer'}Buyer
                        {elseif $row.role == 'competitor'}Competitor
                        {elseif $row.role == 'pproducer'}Potential Producer
                        {elseif $row.role == 'pbuyer'}Potential Buyer{/if *}
                    </td>
                </tr>                
                {/foreach}                
            </table>            
            {if $bizes_count > count($bizes)}<div class="pad1"></div><a class="gotolist" href="/bizes/filter/company:{$form.id}">List of all {$bizes_count} bizes</a>{/if}
            {/if}
        </td>
        <td class="text-top">
            <h4>Open Orders</h4>
            {if empty($orders)}{''|undef}{else}
            <table class="form" width="100%">
                {foreach from=$orders item=row name=p}
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>                
                {/foreach}                
            </table>
            {/if}
        </td>
        <td width="33%" class="text-top">
            {if !empty($subsidiaries)}
            <h4>Subsidiaries</h4>
            <table class="form" width="100%">
                {foreach from=$subsidiaries item=row}
                <tr>
                    <td></td>
                    <td></td>
                </tr>                
                {/foreach}                
            </table>
            <div class="pad"></div>
            {/if}            
            
            <h4>Employees</h4>
            {if empty($persons)}{''|undef}{else}
            <table class="form" width="100%">
                {foreach from=$persons item=row}
                <tr>
                    <td>
                        <a href="/person/{$row.person.id}">{$row.person.full_name}</a>
                        {if isset($row.person.jobposition)}&nbsp;&nbsp;{$row.person.jobposition.title}{/if}
                    </td>
                </tr>                
                {/foreach}                
            </table>
            {if $persons_count > count($persons)}<div class="pad1"></div><a class="gotolist" href="/persons/filter/company:{$form.id}">List of all {$persons_count} company employees</a>{/if}
            {/if}            
        </td>        
    </tr>
</table>
<div class="pad"></div>

<table class="form" width="100%">     
    <tr>
        <td width="33%" class="text-top">
            <h4>Notes</h4>
            <div style="line-height: 16px;">{if !empty($form.notes)}{$form.notes|nl2br}{else}{''|undef}{/if}</div>
        </td>
        <td width="33%" class="text-top">
            <h4>Bank Data</h4>
            <div style="line-height: 16px;">{if !empty($form.bank_data)}{$form.bank_data|nl2br}{else}{''|undef}{/if}</div>
        </td>
        <td width="33%" class="text-top">
            <h4>Registration Data</h4>
            <div style="line-height: 16px;">{if !empty($form.reg_data)}{$form.reg_data|nl2br}{else}{''|undef}{/if}</div>
        </td>
    </tr>
</table>    

<div class="pad"></div>

<table class="form" width="100%">     
    <tr>
        <td width="33%" class="text-top">
        </td>
        <td width="33%" class="text-top">
        </td>
        <td width="33%" class="text-top">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">VAT / IVA : </td>
                    <td>
                        {if isset($form.vat) && !empty($form.vat)}
                            {$form.vat}
                        {else}
                            {''|undef}                            
                        {/if}
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">ALBO : </td>
                    <td>
                        {if isset($form.albo) && !empty($form.albo)}
                            {$form.albo}
                        {else}
                            {''|undef}                            
                        {/if}
                    </td>
                </tr>
            </table>            
        </td>
    </tr>
</table>
{if !empty($chart_data.matrix)}
<div class="pad"></div>
<h3 id="chart">Orders</h3>
<div id="orders"></div>
<script type="text/javascript">
    var dataTable =  {ldelim}
        cols: [{ldelim}label: 'Year', type: 'string', p: {ldelim}role:'domain'{rdelim}{rdelim},
                {ldelim}label: 'Sales Value', type: 'number', p: {ldelim}role:'data'{rdelim}{rdelim},
                {ldelim}label: null, type: 'string', p: {ldelim}role:'tooltip'{rdelim}{rdelim},
                {ldelim}label: 'Weight', type: 'number', p: {ldelim}role:'data'{rdelim}{rdelim},
                {ldelim}label: null, type: 'string', p: {ldelim}role:'tooltip'{rdelim}{rdelim}],
        rows: [{foreach $chart_data.matrix as $row}
            {ldelim}c:[{ldelim}v: '{$row.year}'{rdelim},
            {ldelim}v: {$row.sales_value}{rdelim}, {ldelim}f:"Year: {$row.year}\nSales Value: {$row.currency_sign} {$row.sales_value|number_format}"{rdelim}, {ldelim}v: {$row.total_weight}{rdelim}, {ldelim}f:"Year : {$row.year}\nWeight : {if $row.weight_unit == 'lbs'}{$row.total_weight|number_format:0}{else}{$row.total_weight|number_format:3}{/if} {$row.weight_unit}"{rdelim}]{rdelim}
            {if !$row@last},{/if}
        {/foreach}],
        p:null
    {$smarty.rdelim};
    
    var chart_options = {ldelim}
        title   : '',
        legend  : {ldelim}position: 'in', alignment: 'center'{rdelim},
        vAxes   : [{ldelim}title: "{$chart_data.vAxis.title}", format:'#,###'{rdelim}, {ldelim}title: 'Weight, tons'{rdelim}],
        hAxis   : {ldelim}title: '{$chart_data.hAxis.title}'{rdelim},
        series: [{ldelim}type: 'line', targetAxisIndex: 0, format:'#,###%'{rdelim}, {ldelim}type: 'line', targetAxisIndex: 1{rdelim}],
        pointSize: 5
    {rdelim};
</script>
<div class="chart" style="width: 1000px; height: 480px; margin: 0 auto;"></div>
{/if}


<div class="pad"></div>
{include file='templates/controls/object_shared_files.tpl' object_alias='company' object_id=$form.id}