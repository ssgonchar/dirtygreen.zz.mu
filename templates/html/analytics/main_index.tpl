
<!-- Button trigger modal -->
<h2>Analytics</h2>

{*<div class="row">
    <div id='chart' class="col-md-6"></div>
    <div id='circle-chart' class="col-md-6"></div>
</div>*}
<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <h3>Sales</h3>
            <span class="btn btn-primary btn-sm" data-toggle="modal" data-target="#filters">
    Filters settings
            </span><br><br>
            {if isset($orders)} <table class="table">
                    <tr>
                        <th>Id</th>
                        <th>Order for</th>
                        <th>Biz</th>
                        <th>Buyer</th>
                        <th>Buyer ref</th>
                        <th>Delivery point</th>
                        <th>Delivery date</th>
                        <th>Weight</th>
                        <th>Balance to deliver</th>
                        <th>Value</th>
                        <th>Price<br>Equivalent</th>
                        <th>Status</th>
                        <th>Modified</th>
                    </tr>
                    {foreach from=$orders item=row} <tr>
                            <td>{$row.order_id}</td>
                            <td>{$row.order.order_for_title}</td>
                            <td>{$row.order.biz_id}</td>
                            <td>{if isset($row.order.company)}{$row.order.company.title|escape:'html'}{else}{''|undef}{/if}</td>
                            <td>{if !empty($row.order.buyer_ref)}{$row.order.buyer_ref|escape:'html'}{else}{''|undef}{/if}</td>
                            <td>{if isset($row.order.delivery_point_title)}
                        {$row.order.delivery_point_title}{if !empty($row.order.delivery_town)} {$row.order.delivery_town|escape:'html'}{/if}
                    {else}{''|undef}{/if}</td>
                            <td>{if !empty($row.order.delivery_date)}{$row.order.delivery_date|escape:'html'}{else}{''|undef}{/if}</td>
                            <td>{if !empty($row.order.quick.weight)}
                        {if $row.order.weight_unit|wunit == 'lb'}
                            {$row.order.quick.weight|number_format:0} {$row.order.weight_unit|wunit}
                        {else}
                            {$row.order.quick.weight|number_format:2} {$row.order.weight_unit|wunit}
                        {/if}
                        {else}
                        {''|undef}
                    {/if}</td>
                            <td>Balance to deliver</td>
                            <td>{if !empty($row.order.quick.value)}{$row.order.quick.value|number_format:2:false} {$row.order.currency|cursign}{/if}</th>
                            <td>{if isset($row.order.price_equivalent) && $row.order.price_equivalent !== "0.00"}{$row.order.price_equivalent} {$row.order.currency|cursign}{else}{''|undef}{/if}</td>
                            <td{if $row.order.status == 'nw'} style="background-color: #ffffff; font-weight: bold;"{/if}>{if isset($row.order.status_title)}{$row.order.status_title}{else}<i>Unregistered</i>{/if}</td>
                            <td>{$row.order.modified_at|date_human}<br>
                    by {$row.order.modifier.login|escape:'html'}</td>
                        </tr>  {/foreach}            
                    </table>{/if}
                </div>
            </div>
        </div>    
    </div>
</div>

<!-- Modal -->
<div class="modal fade " id="filters" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Filters</h4>
            </div>
            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#filter-settings" data-toggle="tab">Settings</a></li>
                    <li><a href="#filter-profiles" data-toggle="tab">Profiles</a></li>

                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="filter-settings">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <form role="form">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Date start</label>
                                            <input class="form-control data_start"  name="date_start" placeholder="Click to select">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Date end</label>
                                            <input class="form-control data_end"  name="date_end" placeholder="Click to select">
                                        </div>
                                        {if isset($stocks)}
                                            <div class="form-group"><strong>
                                                    <label for="exampleInputEmail1">Stocks</label>
                                                    <select class="form-control stoks" name="stock_id">
                                                        {foreach from=$stocks item=row}
                                                            <option value="{$row.stock_id}">{$row.stock.title}</option>
                                                        {/foreach}
                                                    </select></strong>
                                            </div>
                                        {/if}
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Locations</label>
                                            <div class="location-container"><select class="form-control locations" name="sent_location_ids"></select></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Stockholders</label>
                                            <select class="form-control" name="stockholders_ids"></select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Customers</label>
                                            <select class="form-control" name="customer_ids"></select>
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Delivery point</label>
                                            <select class="form-control" name="deliver_location_ids"></select>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <form role="form">
                                        {if isset($steelgrades)}
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Steelgrades</label>

                                                <select class="form-control steelgrades" name="steelgrade_ids">
                                                    {foreach from=$steelgrades item=row}
                                                        <option>{$row.steelgrade.title}</option>
                                                    {/foreach}
                                                </select>

                                            </div>
                                        {/if}
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Thickness</label>
                                            <p>
                                            <u>exact value</u> <input class="find-parametr" type="text" name="form[thickness]" size="8" placeholder="number" {if isset($thickness)} value="{$thickness}"{/if}>
                                            or range
                                            <input class="find-parametr" type="text" name="form[thicknessmin]" placeholder="min" size="6" {if isset($thicknessmin)} value="{$thicknessmin}"{/if}>
                                            &mdash; <input class="find-parametr" type="text" name="form[thicknessmax]" placeholder="max" size="6" {if isset($thicknessmax)} value="{$thicknessmax}"{/if}>
                                            </p>                    
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Width</label>
                                            <p>
                                            <u>exact value</u> <input class="find-parametr" type="text" name="form[width]" size="8" placeholder="number" {if isset($width)} value="{$width}"{/if}>
                                            or range
                                            <input class="find-parametr" type="text" name="form[width]" placeholder="min" size="6" {if isset($width)} value="{$width}"{/if}>
                                            &mdash; <input class="find-parametr" type="text" name="form[width]" placeholder="max" size="6" {if isset($width)} value="{$width}"{/if}>
                                            </p>                    
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Height</label>
                                            <p>
                                            <u>exact value</u> <input class="find-parametr" type="text" name="form[height]" size="8" placeholder="number" {if isset($height)} value="{$height}"{/if}>
                                            or range
                                            <input class="find-parametr" type="text" name="form[height]" placeholder="min" size="6" {if isset($height)} value="{$height}"{/if}>
                                            &mdash; <input class="find-parametr" type="text" name="form[height]" placeholder="max" size="6" {if isset($height)} value="{$height}"{/if}>
                                            </p>                    
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Weight</label>
                                            <p>
                                            <u>exact value</u> <input class="find-parametr" type="text" name="form[weight]" size="8" placeholder="number" {if isset($weight)} value="{$weight}"{/if}>
                                            or range
                                            <input class="find-parametr" type="text" name="form[width]" placeholder="min" size="6" {if isset($weight)} value="{$weight}"{/if}>
                                            &mdash; <input class="find-parametr" type="text" name="form[width]" placeholder="max" size="6" {if isset($weight)} value="{$weight}"{/if}>
                                            </p>                    
                                        </div>
                                    </form>
                                </div>        
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="filter-profiles">

                        2</div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Aplly</button>
                <button type="button" class="btn btn-success">Save profile</button>
            </div>
        </div>
    </div>
</div>