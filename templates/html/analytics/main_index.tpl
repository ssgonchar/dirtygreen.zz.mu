{*debug*}
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
            {if isset($orders)} 
                <table id="orders" class="table table-responsive table-striped"  width="100%">
                    <thead>
                        <tr class="top-table">
                            <th>Id</th>
                            <th>Order for</th>
                            <th>Biz</th>
                            <th>Buyer</th>
                            {*<th>Buyer ref</th>*}
                            <th>Delivery point</th>
                            <th>Delivery date</th>
                            <th>Weight</th>

                            <th>Value</th>
                            <th>Price<br>Equivalent</th>
                            {*<th>Status</th>*}
                            <th>Compleated</th>
                        </tr>
                    </thead>
                    {*debug*}
                    <tbody>
                        {foreach from=$orders item=row} <tr>
                                <td onclick="location.href='/order/{$row.order.id}';" class="view_item" rowspan=''>{$row.order.id}</td>
                                <td class="view_item" rowspan=''>{if isset($row.order.order_for_title)}{$row.order.order_for_title}{else}{''|undef}{/if}</td>
                                <td class="view_item" rowspan=''><a href="/biz/{$row.order.biz_id}/blog" target="_blank">{if isset($row.order.biz)}{$row.order.biz.doc_no}</a>{else}{''|undef}{/if}</td>
                                <td class="view_item" rowspan=''>{if isset($row.order.company)}{$row.order.company.title|escape:'html'}{else}{''|undef}{/if}</td>
                                {*<td class="view_item" rowspan=''>{if !empty($row.order.buyer_ref)}<a href="/company/{$row.order.buyer_ref}">{$row.order.buyer_ref|escape:'html'}</a>{else}{''|undef}{/if}</td>*}
                                <td class="view_item" rowspan=''>
                                    {if isset($row.order.delivery_point_title)}
                                        {$row.order.delivery_point_title}{if !empty($row.order.delivery_town)} {$row.order.delivery_town|escape:'html'}{/if}
                                {else}{''|undef}{/if}
                            </td>
                           <td class="view_item" rowspan=''>{if !empty($row.order.delivery_date)}{$row.order.delivery_date|escape:'html'}{else}{''|undef}{/if}
                            </td>
                            <td class="view_item" rowspan=''>
                                {if !empty($row.order.quick.weight)}
                                    {if $row.order.weight_unit|wunit == 'lb'}
                                        {$row.order.quick.weight|number_format:0} {$row.order.weight_unit|wunit}
                                    {else}
                                        {$row.order.quick.weight|number_format:2} {$row.order.weight_unit|wunit}
                                {/if}{*$row.order.quick.weight|number_format:2} {$row.order.weight_unit|wunit*}{*({$row.order.quick.qtty})*}

                                    {else}
                                        {''|undef}
                                        {/if}
                                        </td>

                                        <td class="view_item" rowspan=''>{if !empty($row.order.quick.value)}{$row.order.quick.value|number_format:2:false} {$row.order.currency|cursign}{/if}</td>
                                        <td class="order-price-td" data-id="{$row.order.id}">{if isset($row.order.price_equivalent) && $row.order.price_equivalent !== "0.00"}{$row.order.price_equivalent} {$row.order.currency|cursign}{else}{''|undef}{/if}</td>
                                        {*<td onclick="location.href = '/order/{$row.order.id}';" class="text-center"{if $row.order.status == 'co'} style="background-color: #00FF66;"{/if}>{if isset($row.order.status_title)}{$row.order.status_title}{else}<i>Unregistered</i>{/if}</td>*}
                                        <td class="view_item" rowspan=''>
                                            {$row.order.modified_at|date_human}<br>
                                            by {$row.order.modifier.login|escape:'html'}
                                        </td>
                                    </tr>  
                                    {/foreach} 
                                    </tbody>
                                </table>
                                {/if}
                                </div>
                            </div>
                        </div>    



                        <!-- Modal -->
                        <div class="modal fade " id="filters" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <form role="form" id="filters" action="" method="POST">
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
                                                                {if isset($stocks)}
                                                                    <div class="form-group"><strong>
                                                                            <label for="exampleInputEmail1">Stocks</label> 
                                                                            <select class="form-control stoks" name="form[stock_id]">
                                                                                {foreach from=$stocks item=row}
                                                                                    <option value="{$row.stock_id}" {if isset($stock_id) && $stock_id>1}selected{/if}>{$row.stock.title}</option>
                                                                                {/foreach}
                                                                            </select></strong>
                                                                    </div>
                                                                {/if}
                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Date start</label>
                                                                    <input class="form-control data_start"  name="form[date_start]" placeholder="Click to select">
                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Date end</label>
                                                                    <input class="form-control data_end"  name="form[date_end]" placeholder="Click to select">
                                                                </div>


                                                                <div class="form-group">
                                                                    <label for="exampleInputPassword1">Locations</label>
                                                                    <div class="location-container">Please select stock first.</div>
                                                               
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="exampleInputPassword1">Stockholders</label>
                                                                    <div class="stockholders-container">Please select stock first.</div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="form[customer_id]">Customer</label>
                                                                    <input type='text' class="form-control customer_title ui-autocomplete-input" name="form[customer_title]" placeholder="Please start type to select" data-id="0"  autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" alt="">
                                                                    <input type="text" value="0" name='form[customer_id]' class="cutomer_id" style= "display: none;">
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="exampleInputPassword1">Delivery point</label>
                                                                    <input type='text' class="form-control deliverypoint_title ui-autocomplete-input" name="form[deliverypoint_title]" placeholder="Please start type to select" data-id="0"  autocomplete="off" role="textbox" aria-autocomplete="list" aria-haspopup="true" alt="" {*{if isset($delivery_town)}value="{$delivery_town}"{/if}*}>
                                                                </div>


                                                            </div>
                                                            <div class="col-md-6">


                                                                <div class="form-group">
                                                                    <label for="exampleInputEmail1">Steelgrades</label>

                                                                    <div class="steelgrades-container" name="form[steelgrade_ids]">Please select stock first.</div>

                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="exampleInputPassword1">Thickness</label>
                                                                    <p>
                                                                    <u>exact value</u> <input class="find-parametr" type="text" name="form[thickness]" size="8" placeholder="number" {if isset($thickness)} value="{$thickness}"{/if}>
                                                                    
                                                                    </p>                    
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="exampleInputPassword1">Lenght</label>
                                                                    <p>
                                                                    <u>exact value</u> <input class="find-parametr" type="text" name="form[lenght]" size="8" placeholder="number" {if isset($lenght)} value="{$lenght}"{/if}>
                                                                    
                                                                    </p>                    
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="exampleInputPassword1">Width</label>
                                                                    <p>
                                                                    <u>exact value</u> <input class="find-parametr" type="text" name="form[width]" size="8" placeholder="number" {if isset($width)} value="{$width}"{/if}>
                                                                    
                                                                    </p>                    
                                                                </div>
                                                                
                                                                <div class="form-group">
                                                                    <label for="exampleInputPassword1">Weight</label>
                                                                    <p>
                                                                    <u>exact value</u> <input class="find-parametr" type="text" name="form[weight]" size="8" placeholder="number" {if isset($weight)} value="{$weight}"{/if}>
                                                                   
                                                                    </p>                    
                                                                </div>

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
                                            <button <input type="submit" value="Send" name="BTN_SUBMIT" class="btn btn-primary">Aplly</button>
                                            <button type="button" class="btn btn-success">Save profile</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>