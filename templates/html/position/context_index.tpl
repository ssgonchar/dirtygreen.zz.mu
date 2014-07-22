<!-- <div class="footer-left"{if !isset($target_doc)} style="width: 350px;"{/if}>
    <table style="float: left; margin-right: 10px;">
        <tr>
            <td style="font-weight: bold;" width="70px">Selected : </td>
            <td width="60px" class="text-right"><span id="lbl-selected-qtty">0</span> pcs</td>
            <td width="100px" class="text-right"><span id="lbl-selected-weight">0</span><span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
            <td width="100px" class="text-right"><span id="lbl-selected-value">0</span><span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
        </tr>
        <tr>
            <td style="font-weight: bold;" width="50px">Total : </td>
            <td width="60px" class="text-right">{if isset($total_qtty)}{$total_qtty}{else}0{/if} pcs</td>
            <td width="100px" class="text-right">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if} <span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
            <td width="100px" class="text-right">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if} <span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
        </tr>
    </table>
</div>
<div class="footer-right">
{if empty($is_revision)}
    {if isset($target_doc)}
        <input type="button" class="btn150" value="Back to {$target_doc_no}" onclick="location.href='{if $target_doc == 'neworder'}/order/{$target_doc}/{$target_doc_id}{else}/{$target_doc}/{if empty($target_doc_id)}add{else}{$target_doc_id}/edit{/if}{/if}';">
        <input type="button" class="btn150o selected-control" value="Add to {$target_doc_no}" onclick="add_selected_to_document('{$target_doc}', '{$target_doc_id}');" style="display: none; margin-left: 10px;">
    {else}        
        {if empty($is_revision)}
            <input type="button" id="btn-reserve" class="btn100 selected-control" value="Reserve" onclick="reserve_selected();" style="margin-left: 10px; display: none;">
            <input type="button" id="btn-alias" class="btn100 selected-control" value="Create alias" onclick="add_selected_to_document('alias', '0');" style="margin-left: 10px; display: none;">
            {* <input type="button" id="btn-order" class="btn150 selected-control" value="Add To Order" onclick="show_order_select();" style="margin-left: 10px; display: none;"> *}
            {*  <input type="button" id="btn-qc" class="btn100 selected-control" value="Add To QC" onclick="show_qc_select();" style="margin-left: 10px; display: none;"> *}
            <input type="button" id="btn-ra" class="btn100 selected-control" value="Create RA" onclick="add_selected_to_document('newra', '0');" style="margin-left: 10px; display: none;">
            <input type="button" id="btn-ra" class="btn100 selected-control" value="Create QC" onclick="add_selected_to_document('newqc', '0');" style="margin-left: 10px; display: none;">
            <input type="button" id="btn-positions-edit" class="btn100" value="Edit" onclick="redirect_selected('position', '/position/groupedit/');" style="display: none; margin-left: 10px;">
        {/if}
        <input type="button" class="btn150o" value="Create Positions" onclick="goto_position_add();" style="margin-left: 10px;">
    {/if}
{/if}
</div>
-->

<ul class="nav navbar-nav footer_panel">
    <li style="margin-top: 7px; margin-right: 5px;">
        <input type="button" class="btn btn-success" value="Create Positions" onclick="goto_position_add();">
    </li>
    <li style='margin-top: 7px;'>

        <table style="float: left; margin-right: 10px;">
            <tr>
                <td style="font-weight: bold;" width="70px">Selected : </td>
                <td width="60px" class="text-right"><span id="lbl-selected-qtty">0</span> pcs</td>
                <td width="90px" class="text-right"><span id="lbl-selected-weight">0</span><span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
                <td width="90px" class="text-right"><span id="lbl-selected-value">0</span><span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
            </tr>
            <tr>
                <td style="font-weight: bold;" width="75px">From Total : </td>
                <td width="60px" class="text-right">{if isset($total_qtty)}{$total_qtty}{else}0{/if} pcs</td>
                <td width="90px" class="text-right">{if isset($total_weight)}{$total_weight|string_format:'%.2f'}{else}0{/if} <span class="lbl-wgh">{if isset($stock)} {$stock.weight_unit|wunit}{/if}</span></td>
                <td width="90px" class="text-right">{if isset($total_value)}{$total_value|string_format:'%.2f'}{else}0{/if} <span class="lbl-value">{if isset($stock)} {$stock.currency_sign}{/if}</span></td>
            </tr>
        </table>
    </li>
</ul>
<ul class="nav navbar-nav navbar-right" style='margin-top: 7px;'>
    <li>
        {if empty($is_revision)}
            {if isset($target_doc)}
                <input type="button" class="btn btn-default" value="Back to {$target_doc_no}" onclick="location.href = '{if $target_doc == 'neworder'}/order/{$target_doc}/{$target_doc_id}{else}/{$target_doc}/{if empty($target_doc_id)}add{else}{$target_doc_id}/edit{/if}{/if}';">
                <input type="button" class="btn btn-primary selected-control" value="Add to {$target_doc_no}" onclick="add_selected_to_document('{$target_doc}', '{$target_doc_id}');" style="margin-left: 10px;">
                <!--<input type="submit" name="btn_setfilter" value="Find" class="btn100b" style=''>-->
            {else}        
                {if empty($is_revision)}
                    <!--									�����  display: none;, ������� disabled="disabled"
                                                                            <input type="button" id="btn-reserve" class="btn100 selected-control" value="Reserve" onclick="reserve_selected();" style="margin-left: 10px; display: none;">
                                                                            <input type="button" id="btn-alias" class="btn100 selected-control" value="Create alias" onclick="add_selected_to_document('alias', '0');" style="margin-left: 10px; display: none;">
                    {* <input type="button" id="btn-order" class="btn150 selected-control" value="Add To Order" onclick="show_order_select();" style="margin-left: 10px; display: none;"> *}
                    {*  <input type="button" id="btn-qc" class="btn100 selected-control" value="Add To QC" onclick="show_qc_select();" style="margin-left: 10px; display: none;"> *}
                    <input type="button" id="btn-ra" class="btn100 selected-control" value="Create RA" onclick="add_selected_to_document('newra', '0');" style="margin-left: 10px; display: none;">
                    <input type="button" id="btn-ra" class="btn100 selected-control" value="Create QC" onclick="add_selected_to_document('newqc', '0');" style="margin-left: 10px; display: none;">
                    <input type="button" id="btn-positions-edit" class="btn100" value="Edit" onclick="redirect_selected('position', '/position/groupedit/');" style="display: none; margin-left: 10px;">
                    -->
                    <!--<input type="button" id="btn-mirror" class="btn btn-default selected-control" title="Please, select a position" value="Mirror" onclick="create_mirror_from_selected();" style="margin-left: 0px;">-->
                    <input type="button" id="btn-reserve" class="btn btn-default selected-control" title="Please, select a position" value="Reserve" onclick="reserve_selected();" style="margin-left: 3px;">
                    <input type="button" id="btn-alias" class="btn btn-default selected-control" title="Please, select a position" value="Create alias" onclick="add_selected_to_document('alias', '0');" style="margin-left: 3px;">
                    {* <input type="button" id="btn-order" class="btn150 selected-control" value="Add To Order" onclick="show_order_select();" style="margin-left: 10px; display: none;"> *}
                    {*  <input type="button" id="btn-qc" class="btn100 selected-control" value="Add To QC" onclick="show_qc_select();" style="margin-left: 10px; display: none;"> *}
                    <input type="button" id="btn-ra" class="btn btn-default selected-control" title="Please, select a position" value="Create RA" onclick="add_selected_to_document('newra', '0');" style="margin-left: 3px;">
                    <input type="button" id="btn-ra" class="btn btn-default selected-control" title="Please, select a position" value="Create QC" onclick="add_selected_to_document('newqc', '0');" style="margin-left: 3px;">
                    <input type="button" id="btn-positions-edit" class="btn btn-default" title="Please, select a position" value="Edit" onclick="redirect_selected('position', '/position/groupedit/');" style="margin-left: 3px;">
                    <!--<input type="submit" name="btn_setfilter" value="Find" class="btn100b" style=''>-->
                {/if}

            {/if}
        {/if}
    </li>	
</ul>
