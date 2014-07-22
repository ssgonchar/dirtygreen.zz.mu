{*debug*}
<!-- Modal --> 

<div class="modal fade" id="myModal{$position.id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"> 
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"  style="margin-left: 10px;">&times;</button>
                <div class="btn-group pull-right">
                    <span id="prev" class="btn btn-xs btn-default mirror_prev_position" title="Previous position">
                        <span class="btn-link glyphicon glyphicon-arrow-left"></span>
                        &nbsp
                    </span>
                    <span id="current-id" class="btn btn-xs btn-default" title="Click to enter Pos ID">
                        <span><small>current id #</small>{$position.id}</span>
                        <input id="input-id" type="text" style="display: none; width: 85px; height: 18px;">
                    </span>
                    <span id="next" class="btn btn-xs btn-default mirror_next_position" title="Next position">
                        &nbsp
                        <span class="btn-link glyphicon glyphicon-arrow-right"></span>
                    </span>
                </div>
                <h4 class="modal-title" id="myModalLabel">Mirror manager</h4>
            </div>
            <div class="modal-body">
                
                {if !empty($position.steelgrade.title)}
                    <div class="highlight">
                        <h4 style="display:inline-block;">
                            <span class="label label-default" style="color: #333333; background: {$position.steelgrade.bgcolor};">{$position.steelgrade.title}</span> Position <b>{$position.id}</b>
                            <small> in {$position.quick.locations} {if !empty($position.quick.int_locations)}({$position.quick.int_locations}){/if}</small>
                        </h4>
                    </div>
                {/if}
                <div class="row">
                    <div class="col-md-4 col-xs-4">
                        <i>length, {$position.dimension_unit}:</i> {$position.length}<br/>
                        <i>width, {$position.dimension_unit}:</i> &nbsp;&nbsp;{$position.width}<br/><br/>

                    </div>                
                    <div class="col-md-4 col-xs-4">
                        <i>thickness, {$position.dimension_unit}:</i> {$position.thickness}<br/>
                        <i>weight, {if $position.weight_unit=='mt'}ton{else}{$position.weight_unit}{/if}:</i> 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{if $position.weight_unit == 'lb'}{$position.unitweight|escape:'html'|string_format:'%d'|wunit}{else}{$position.unitweight|escape:'html'|string_format:'%.2f'|wunit}{/if}
                        <br/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <u>total weight, {if $position.weight_unit=='mt'}ton{else}{$position.weight_unit}{/if}</u>:
                        <b>{if $position.weight_unit == 'lb'}{$position.weight|escape:'html'|string_format:'%d'|wunit}{else}{$position.weight|escape:'html'|string_format:'%.2f'|wunit}{/if}
                        </b>
                        <br/>
                        <u>total price, {$position.currency}</u>: <b>{$position.value|escape:'html'|string_format:'%.2f'|wunit}</b><br/>                                
                    </div>
                </div>
            </div>
<!--
            <div class="row">
    <div class="col-md-12">
-->    
            <table id="mirrors-edit" class="table table-striped table-bordered" style="width:100%;">
                <tr class="">                               
                    <td id='td-stock' style="width:25%">
                    <!-- $position_id записываем в скрытый span -->
                            <span class="mirror-id" style="display: none;" type="text"></span>
                            <span class="position-id" style="display: none;" type="text">{$position.id}</span>
                        <select class="select-stock chosen-select" style="width:100%">
                            {foreach from=$stocks item=row}
                                <option value='{$row.stock.id}' {if $position.stock_id == $row.stock.id}selected{/if}>{$row.stock.title}</option>
                            {/foreach}
                        </select>
                    </td>                                
                    <td class='td-location' style="width:25%">
                        <select class="select-location chosen-select" style="width:100%">
                            {if $position.stock_id == "1"}
                                {foreach from=$locations_eur item=row}
                                    <option value="{$row.location.id}" {if $position.quick.locations == $row.location.title}selected{/if}>{$row.location.title}</option>
                                {/foreach}
                            {else}
                                {foreach from=$locations_usa item=row}
                                    <option value="{$row.location.id}" {if $position.quick.locations == $row.location.title}selected{/if}>{$row.location.title}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </td>
                    <td class='td-deliverytime' style="width:20%">
                        <select class="select-deliverytime chosen-select" style="width:100%">
                            {if $position.stock_id == "1"}
                                {foreach from=$deliverytimes_eur item=row}
                                    <option value="{$row.deliverytime_id}" {if $position.deliverytime_id == $row.deliverytime_id}selected{/if}>{$row.deliverytime.title}</option>
                                {/foreach}
                            {else}
                                {foreach from=$deliverytimes_usa item=row}
                                    <option value="{$row.deliverytime_id}" {if $position.deliverytime_id == $row.deliverytime_id}selected{/if}>{$row.deliverytime.title}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </td>
                    <td style="width:15%">
                        <input class="mirror-price form-control input-lg" style="width:100%; height: 20px;" type="text" placeholder="price*">
                    </td> 
                    <td style="width:10%">
                        <button id="del-mirror" value="{$mirror_id}" style="width:100%" class="btn btn-primary btn-xs" onClick="mirror_del_row(this);"><span  class="glyphicon glyphicon-remove"></span> Delete</button>
                    </td>
                </tr>
            </table>
                        <div class="row" style="margin-bottom: 15px;">
                            <div class="container">
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-xs" onclick="mirror_del_all()" ><span  class="glyphicon glyphicon-trash"></span> Delete all mirrors</button>
                                    <button id="add-mirror" class="btn btn-primary btn-xs" onclick="mirror_add_row();" ><span  class="glyphicon glyphicon-plus"></span> Add mirror</button>
                                </div>
                            </div>
                        </div>
            <!--                                
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span  class="glyphicon glyphicon-chevron-left"></span> Close</button>
                <button id="button-save" type="button" class="btn btn-primary" disabled><span  class="glyphicon glyphicon-save"></span> Save</button>
            </div>-->
        </div>
    </div>
</div>