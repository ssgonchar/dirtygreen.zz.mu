{*debug*}
<!-- Modal -->

<div class="modal fade" id="myModal{$position.id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <div class="btn-group pull-right">
                    <a class="btn btn-default btn-link glyphicon glyphicon-arrow-left mirror_prev_position btn-xs" href="mirror/create_mirror_from_selected/{*$prev_id*}"></a>
                    <button type="button" class="btn btn-default btn-xs" disabled><small>current id #</small>{$position.id}</button>
                    <a class="btn btn-default btn-link glyphicon glyphicon-arrow-right mirror_next_position btn-xs" href="mirror/create_mirror_from_selected/{*$next_id*}"></a>                    
                &nbsp;
                </div>                
                <h4 class="modal-title" id="myModalLabel">Mirror manager</h4>
            </div>
            <div class="modal-body">

                <br/>
                
                {if !empty($position.steelgrade.title)}
                    <h4 style="display:inline-block;">
                        <span class="label label-default" style="color: #333333; background: {$position.steelgrade.bgcolor};">{$position.steelgrade.title}</span> Position #{$position.id}
                    </h4>
                {/if}
                
                <h4 style="display:inline-block; vertical-align: middle;">
                    in {$position.quick.locations} {if !empty($position.quick.int_locations)}({$position.quick.int_locations}){/if}
                </h4>

                <div class="row">
                    <div class="col-md-4 col-xs-4">
                        length, {$position.dimension_unit}: {$position.length}<br/>
                        width, {$position.dimension_unit}: {$position.width}<br/><br/>

                    </div>                
                    <div class="col-md-4 col-xs-4">
                        thickness, {$position.dimension_unit}: {$position.thickness}<br/>
                        weight, {if $position.weight_unit=='mt'}ton{else}{$position.weight_unit}{/if}: 
                        {if $position.weight_unit == 'lb'}{$position.unitweight|escape:'html'|string_format:'%d'|wunit}{else}{$position.unitweight|escape:'html'|string_format:'%.2f'|wunit}{/if}
                        <br/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <b>total weight, {if $position.weight_unit=='mt'}ton{else}{$position.weight_unit}{/if}: 
                        {if $position.weight_unit == 'lb'}{$position.weight|escape:'html'|string_format:'%d'|wunit}{else}{$position.weight|escape:'html'|string_format:'%.2f'|wunit}{/if}
                        </b>
                        <br/>
                        <b>total price, {$position.currency}: {$position.value|escape:'html'|string_format:'%.2f'|wunit}</b><br/>                                
                    </div>
                </div>
            </div>
<!--
            <div class="row">
    <div class="col-md-12">
-->    
            <table id="mirrors-edit" class="table table-striped" style="width:100%;">
                <tr class="test">                               
                    <td id='td-stock' style="width:25%">
                        <select class="select-stock" style="width:100%">
                            {foreach from=$stocks item=row}
                                <option value='{$row.stock.id}' {if $position.stock_id == $row.stock.id}selected{/if}>{$row.stock.title}</option>
                            {/foreach}
                        </select>
                    </td>                                
                    <td class='td-location' style="width:25%">
                        <select style="width:100%">
                            {foreach from=$locations item=row}
                                <option {if $position.quick.locations == $row.location.title}selected{/if}>{$row.location.title}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td class='td-deliverytime' style="width:20%">
                        <select style="width:100%">
                            {foreach from=$deliverytimes item=row}
                                <option {if $position.deliverytime_id == $row.deliverytime_id}selected{/if}>{$row.deliverytime.title}</option>
                            {/foreach}
                        </select>
                    </td>
                    <td style="width:15%">
                        <input class="mirror-price" style="width:100%; height: 20px;" type="text" placeholder="price*">
                    </td> 
                    <td style="width:10%">
                        <button style="width:100%" class="btn btn-primary btn-xs" onClick="mirror_del_row(this);" disabled="true"><span  class="glyphicon glyphicon-remove"></span> Delete</button>
                    </td>
                </tr>
                <!--
                <tr>
                    <td colspan="2">
                       <hr/>
                    </td>
                </tr>
                -->
                {*
                {foreach $mirrors item=row}
                    <tr class="test">                               
                        <td id='td-stock' style="width:25%">
                            <select class="select-stock" style="width:100%">
                                {foreach from=$stocks item=row}
                                    <option value='{$row.stock.id}' {if $position.stock_id == $row.stock.id}selected{/if}>{$row.stock.title}</option>
                                {/foreach}
                            </select>
                        </td>                                
                        <td class='td-location' style="width:25%">
                            <select style="width:100%">
                                {foreach from=$locations item=row}
                                    <option {if $position.quick.locations == $row.location.title}selected{/if}>{$row.location.title}</option>
                                {/foreach}
                            </select>
                        </td>
                        <td class='td-deliverytime' style="width:20%">
                            <select style="width:100%">
                                {foreach from=$deliverytimes item=row}
                                    <option {if $position.deliverytime_id == $row.deliverytime_id}selected{/if}>{$row.deliverytime.title}</option>
                                {/foreach}
                            </select>
                        </td>
                        <td style="width:15%">
                            <input class="mirror-price {if empty($row.mirror.price)}empty{/if}}" style="width:100%; height: 20px;" type="text" placeholder="price">
                        </td> 
                        <td style="width:10%">
                            <button style="width:100%" class="btn btn-primary btn-xs" onClick="mirror_del_row(this);" disabled="true"><span  class="glyphicon glyphicon-remove"></span> Delete</button>
                        </td>
                    </tr>                    
                {/foreach}
                *}
            </table>
                        <div class="row">
                            <div class="container">
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-xs" onclick="mirror_del_all()" ><span  class="glyphicon glyphicon-trash"></span> Delete all mirrors</button>
                                    <button class="btn btn-primary btn-xs" onclick="mirror_add_row();" ><span  class="glyphicon glyphicon-plus"></span> Add mirror</button>
                                </div>
                            </div>
                        </div>
                                            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><span  class="glyphicon glyphicon-chevron-left"></span> Close</button>
                <button id="button-save" type="button" class="btn btn-primary" disabled><span  class="glyphicon glyphicon-save"></span> Save</button>
            </div>
        </div>
    </div>
</div>