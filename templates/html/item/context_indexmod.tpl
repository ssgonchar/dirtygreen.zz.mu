<ul class="nav navbar-nav footer_panel">
        <li style='margin-top: 7px;'>
                <table style="float: left; vertical-align: middle;">
                        <tr>
                                <td style="font-weight: bold;" width="70px">Selected : </td>
                                <td width="50px" class="text-right"><span id="lbl-selected-qtty">0</span> pcs</td>
                                <td width="100px" class="text-right"><span id="lbl-selected-weight">0</span><span class="lbl-wgh">{if $item_weight_unit_count == 1} {$item_weight_unit|wunit}{/if}</span></td>
                                <td width="100px" class="text-right"><span id="lbl-selected-value">0</span><span class="lbl-value">{if $item_currency_count == 1} {$item_currency|cursign}{/if}</span></td>
                                {* <td width="150px" class="text-right"><span id="lbl-selected-purchasevalue">0</span><span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span> (purchase)</td> -- закомментировал, потому что тут показывается каша ищ разных валют *}
                        </tr>
                        <tr>
                                <td style="font-weight: bold;" width="75px">From Total : </td>
                                <td width="30px" class="text-right">{if isset($total_qtty)}{$total_qtty}{else}0{/if} pcs</td>
                                <td width="100px" class="text-right">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if} <span class="lbl-wgh">{if $item_weight_unit_count == 1} {$item_weight_unit|wunit}{/if}</span></td>
                                <td width="100px" class="text-right">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if} <span class="lbl-value">{if $item_currency_count == 1} {$item_currency|cursign}{/if}</span></td>
                                {* <td width="150px" class="text-right">{if isset($total_purchase_value)}{$total_purchase_value|string_format:'%.2f'}{else}0{/if} <span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span> (purchase)</td> *}
                        </tr>
                </table>
        </li>
</ul>
<ul class="nav navbar-nav navbar-right">
        <li style='margin-top: 6px;'>
                {if !isset($is_revision) || empty($is_revision)}
                        {if isset($target_doc)}
                                <input type="button" class="btn150" value="{$back_title}" onclick="location.href='/{if $target_doc == 'supinvoice'}supplierinvoice{else}{$target_doc}{/if}/{if empty($target_doc_id)}add{else}{$target_doc_id}/edit{/if}';">
                                <input type="button" class="btn150 selected-control" value="Add & Go Back" onclick="add_selected_to_document('{$target_doc}', {$target_doc_id}, 'yes');" style="display: none; margin-left: 10px;">
                                <input type="button" class="btn150o selected-control" value="{$save_title}" onclick="add_selected_to_document('{$target_doc}', {$target_doc_id}, 'no');" style="display: none; margin-left: 10px;">
                        {else}
                                {* <input type="button" id="btn_to_order" class="btn150 selected-control" value="Add To Order" onclick="show_order_select();" style="margin-left: 10px; display: none;"> *}
                                {* временно <input type="button" class="btn100 selected-control" value="Add to QC" onclick="show_qc_select();" style="margin-left: 10px; display: none;"> *} 
                                <input type="button" class="btn btn-default selected-control" value="Create Alias" title="Please, select an item" onclick="add_selected_to_document('createalias', '0', 'yes');" style="margin-left: 10px;">
                                <input type="button" class="btn btn-default selected-control" value="Create RA" title="Please, select an item" onclick="add_selected_to_document('ra', '0', 'yes');" style="margin-left: 10px;">
                                <input type="button" class="btn btn-default selected-control" value="Edit" title="Please, select an item" onclick="edit_items();" style="margin-left: 10px;">
                                <input type="button" class="btn btn-default selected-control" value="Remove" title="Please, select an item" onclick="if (confirm('Items will be deleted permanently. Are you sure?')) remove_item();" style="margin-left: 10px;">
                        {/if}
                {/if}
        </li>	
</ul>
