{if isset($total)}
<table>
    <tr>
        <td style="font-weight: bold;" class="text-top">Total : </td>
        <td style="padding-left: 20px;" class="text-top">
            <table style="float: left; margin-right: 20px;">
                {if isset($total.0)}
                <tr class="{$total.0.unit}-{$total.0.currency}-items" style="cursor: pointer;" onclick="inout_select_items(this, '{$total.0.unit}-{$total.0.currency}');">
                    <td class="text-right">{$total.0.qtty} pcs</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.0.weight|number_format:3:true} {$total.0.unit|wunit}</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.0.value|number_format:2:false} {$total.0.currency|cursign}</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.0.currency|cursign} {($total.0.price/$total.0.qtty)|number_format:2:false}/{$total.0.unit|wunit}</td>
                </tr>
                {/if}
                {if isset($total.1)}
                <tr class="{$total.1.unit}-{$total.1.currency}-items" style="cursor: pointer;" onclick="inout_select_items(this, '{$total.1.unit}-{$total.1.currency}');">
                    <td class="text-right">{$total.1.qtty} pcs</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.1.weight|number_format:3:true} {$total.1.unit|wunit}</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.1.value|number_format:2:false} {$total.1.currency|cursign}</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.1.currency|cursign} {($total.1.price/$total.1.qtty)|number_format:2:false}/{$total.1.unit|wunit}</td>
                </tr>
                {/if}                
            </table>            
        </td>
        {if count($total) > 2}
        <td style="padding-left: 50px;" class="text-top">
            <table style="float: left; margin-right: 20px;">
                {if isset($total.2)}
                <tr class="{$total.2.unit}-{$total.2.currency}-items" style="cursor: pointer;" onclick="inout_select_items(this, '{$total.2.unit}-{$total.2.currency}');">
                    <td class="text-right">{$total.2.qtty} pcs</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.2.weight|number_format:3:true} {$total.2.unit|wunit}</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.2.value|number_format:2:false} {$total.2.currency|cursign}</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.2.currency|cursign} {($total.2.price/$total.2.qtty)|number_format:2:false}/{$total.2.unit|wunit}</td>
                </tr>
                {/if}
                {if isset($total.3)}
                <tr class="{$total.3.unit}-{$total.3.currency}-items" style="cursor: pointer;" onclick="inout_select_items(this, '{$total.3.unit}-{$total.3.currency}');">
                    <td class="text-right">{$total.3.qtty} pcs</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.3.weight|number_format:3:true} {$total.3.unit|wunit}</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.3.value|number_format:2:false} {$total.3.currency|cursign}</td>
                    <td class="text-right" style="padding-left: 20px;">{$total.3.currency|cursign} {($total.3.price/$total.3.qtty)|number_format:2:false}/{$total.3.unit|wunit}</td>
                </tr>
                {/if}                
            </table>            
        </td>        
        {/if}
    </tr>
</table>
{/if}


{* if isset($total)}
<table>
    <tr>
        <td>
            <table style="float: left; margin-right: 20px;">
                {foreach from=$total item=row}
                <tr class="{$row.unit}-{$row.currency}-items" style="cursor: pointer;" onclick="inout_select_items(this, '{$row.unit}-{$row.currency}');">
                    <td class="text-right">{$row.qtty} pcs</td>
                    <td class="text-right" style="padding-left: 20px;">{$row.weight|number_format:3:true} {$row.unit|wunit}</td>
                    <td class="text-right" style="padding-left: 20px;">{$row.value|number_format:2:false} {$row.currency|cursign}</td>
                    <td class="text-right" style="padding-left: 20px;">{$row.currency|cursign} {($row.price/$row.qtty)|number_format:2:false}/{$row.unit|wunit}</td>
                </tr>        
                {/foreach}
            </table>            
        </td>
        <td style="padding-left: 50px;">
            <table style="float: left; margin-right: 20px;">
                {foreach from=$total item=row}
                <tr class="{$row.unit}-{$row.currency}-items" style="cursor: pointer;" onclick="inout_select_items(this, '{$row.unit}-{$row.currency}');">
                    <td class="text-right">{$row.qtty} pcs</td>
                    <td class="text-right" style="padding-left: 20px;">{$row.weight|number_format:3:true} {$row.unit|wunit}</td>
                    <td class="text-right" style="padding-left: 20px;">{$row.value|number_format:2:false} {$row.currency|cursign}</td>
                    <td class="text-right" style="padding-left: 20px;">{$row.currency|cursign} {($row.price/$row.qtty)|number_format:2:false}/{$row.unit|wunit}</td>
                </tr>        
                {/foreach}
            </table>            
        </td>            
    </tr>
</table>
{/if *}