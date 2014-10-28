<h1>111 TEST giT</h1>
<div class="col-md-12" style="padding-bottom: 60px;"> 
    <form>
        <div class="row">
            <div class="col-md-3"> 
                <div class="row" style="padding-top: 5px; padding-left: 5px;">
                    <div class="text-left pull-left">
                        <input id="new-email" type="button" name="btn_compose" class="btn btn-primary btn-sm" value="Write email"  onclick="window.open('{if !empty($object_alias) && !empty($object_alias)}/{$object_alias}/{$object_id}{/if}/emailmanager/compose', 'email_html_new', 'fullscreen=yes,scrollbars=yes,resizable=yes');">
                    </div>&nbsp;     
                    <!-- Button trigger modal -->
                    <button class="btn btn-default btn-sm" data-toggle="modal" data-target="#filterModal">
                        Edit filters
                    </button> 

                    <!-- Modal -->
                    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header emailmanager-sidebar-toolbar-header" style="margin-top: 0px;">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="filterModalLabel">Edit filters</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row row-edit-form" style="display:none;">

                                        <div class="col-md-12">
                                            <br/>
                                            <form id="formEditFilter">
                                                <label for="exampleInputPassword1">BIZes</label>
                                                <div class="input-group input-group-sm">

                                                    <input id="biz" placeholder="start typing to select BIZ" class="input-biz-title form-control">
                                                    <input id="biz-id" class="add-biz-id" type="hidden" name="form[biz_id]" value="0">
                                                    <input id="" class="add-filter-id" type="hidden" name="" value="{$item.efilter.id}">
                                                    <span class="input-group-btn">
                                                        <span class="btn btn-success btn-biz-add" value="Add" name="btn_find">+</span>
                                                    </span>
                                                    <input type="hidden" class="form-control add-tags" id="exampleInputPassword1" placeholder="@example.com or name@example.com" 
                                                           {if isset($item.efilter.tags)}value="{$item.efilter.tags}"{/if}>             
                                                </div>
                                                <p class="form-group">

                                                    {foreach from=$item.efilter.tags_array item=item_biz}
                                                        {if $item_biz.object_alias=='biz'}
                                                            <span id="{$item_biz.object_alias|escape:'html'}-{$item_biz.object_id}" style="margin-right: 10px;">
                                                                <input type="hidden" name="objects[{$item_biz.object_alias|escape:'html'}-{$item_biz.object_id}]" class="{$item_biz.object_alias|escape:'html'}-object" value="{$item_biz.object_id}">
                                                                <a class="tag-biz" style="vertical-align: top; margin-right: 3px;">
                                                                    {$item_biz.biz.title}
                                                                </a>
                                                                <img src="/img/icons/cross-small.png" style="cursor: pointer;" class="remove-filter-alias" data-object-alias="{$item_biz.object_alias|escape:'html'}" data-objectId="{$item_biz.object_id}" data-email-id="{$item.efilter.id}">
                                                            </span><br/>
                                                        {/if}                    
                                                    {/foreach}        
                                                </p>
                                                <p class="form-group">
                                                    <label for="exampleInputPassword1">From</label>
                                                    <input type="text" class="form-control add-from" id="exampleInputPassword1" placeholder="@example.com or name@example.com" 
                                                           {if isset($item.efilter.params_array.from)}value="{$item.efilter.params_array.from}"{/if}>
                                                </p>
                                                <p class="form-group">
                                                    <label for="exampleInputPassword1">To</label>
                                                    <input type="text" class="form-control add-to" id="exampleInputPassword1" placeholder="@example.com or name@example.com" 
                                                           {if isset($item.efilter.params_array.to)}value="{$item.efilter.params_array.to}"{/if}>
                                                </p>
                                                <p class="form-group">
                                                    <label for="exampleInputPassword1">Keyword</label>
                                                    <input type="text" class="form-control add-keyword" id="exampleInputPassword1" placeholder="Free text" 
                                                           {if isset($item.efilter.params_array.subject)}value="{$item.efilter.params_array.subject}"{/if}>
                                                </p>

                                                <p class="checkbox">
                                                    <label>
                                                        <input type="checkbox"  class="is-sheduled" {if $item.efilter.is_scheduled > 0}checked="true"{/if}> Only for new emails
                                                    </label>
                                                </p>
                                                <p class="checkbox">
                                                    <label>
                                                        <input type="checkbox" class="add-attachments" {if isset($item.efilter.params_array.attachment) && $item.efilter.params_array.attachment>0}checked="true"{/if}> Only for emails with attachments
                                                    </label>
                                                </p>

                                                <span class="btn btn-success apply-edit">Save</span>
                                                <span class="btn btn-default close-edit-form">Back</span>
                                            </form>
                                        </div>

                                    </div>
                                    <div class="row row-filter-table">

                                    </div>
                                </div>
                                <!--
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary btn-filter-create">Add</button>
                                </div>
                                -->
                            </div>
                        </div>
                    </div>                    
                </div>      
                <div class="row row-form ">
                    <div class="emailmanager-sidebar-toolbar-header">Search</div>
                    <div class="col-md-12 emailmanager-sidebar-toolbar">
                        <div class="input-group input-group-sm">
                            <input id="" placeholder="keyword" class="form-control find-keyword">
                            <span class="input-group-btn">
                                <span class="btn btn-success btn-find-keyword">
                                    <i class="glyphicon glyphicon-search"></i>
                                </span>
                            </span>
                        </div>                        
                    </div>
                </div>
                <div class="row row-form ">
                    <div class="emailmanager-sidebar-toolbar-header">Filters by types</div>
                    <div class="col-md-12 emailmanager-sidebar-toolbar">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="select-email-type" data-type-id="1"> Inbox
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="select-email-type" data-type-id="2"> Sent
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="select-email-type" data-type-id="3"> Draft
                                        </label>
                                    </div>            
                                </div>
                                <div class="col-md-6">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="select-email-type" data-type-id="5"> Spam
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="select-email-type" data-type-id="4"> Error
                                        </label>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <span class="btn btn-default select-email-trash">Trash</span>
                            </div>
                        </div>
                    </div> 
                </div>
                <div class="row row-form ">
                    <div class="emailmanager-sidebar-toolbar-header">Filters by BIZs</div>
                    <div class="col-md-12 emailmanager-sidebar-toolbar">
                        <p class="explanation"><i class="glyphicon glyphicon-question-sign"></i> Use to select the emails, related to selected BIZs.</p>
                        {*
                        <div class="input-group input-group-sm">
                        <input id="biz" placeholder="start typing to select BIZ" class="input-biz-title form-control">
                        <input id="biz-id" type="hidden" name="form[biz_id]" value="0">
                        <span class="input-group-btn">
                        <span class="btn btn-success" value="Add" name="btn_find">+</span>
                        </span>
                        </div>
                        
                        <p class="explanation"><i class="glyphicon glyphicon-question-sign"></i> Selected BIZ will be added to the toolbar.</p>
                        *}

                        <ul class="nav nav-list" style="padding-left: 0px;">
                            {* <li>
                            <label class="tree-toggler nav-header"  style="cursor:pointer;">All BIZs</label>
                            <ul class="nav nav-list tree" style="overflow: hidden; border-left: 1px dotted #cccccc;">
                            <li>
                            <p id="biz-1626" style="border-bottom: 1px dotted #cccccc;">
                            <input type="hidden" name="objects[biz-1626]" class="biz-object" value="1626" alt="">
                            <a class="tag-biz" style="vertical-align: top; margin-right: 3px;" data-biz-id="1626" href="/biz/1626/emailmanager/filter/type:0;" target="_blank">Plates for PIONEER</a><img src="/img/icons/cross-small.png" style="cursor: pointer;" class="remove-object-alias" data-object-alias="biz" data-object-id="1626" data-email-id="213537">
                            </p>
                            </li>
                            <li>
                            <p id="biz-4916" style="border-bottom: 1px dotted #cccccc;">
                            <input type="hidden" name="objects[biz-4916]" class="biz-object" value="4916" alt="">
                            <a class="tag-biz" style="vertical-align: top; margin-right: 3px;" data-biz-id="4916" href="/biz/4916/emailmanager/filter/type:0;" target="_blank">STEELemotionSYSTEM</a><img src="/img/icons/cross-small.png" style="cursor: pointer;" class="remove-object-alias" data-object-alias="biz" data-object-id="4916" data-email-id="213536">
                            </p>
                            </li>
                            <li>
                            <p id="biz-3420" style="border-bottom: 1px dotted #cccccc;">
                            <input type="hidden" name="objects[biz-3420]" class="biz-object" value="3420" alt="">
                            <a class="tag-biz" style="vertical-align: top; margin-right: 3px;" data-biz-id="3420" href="/biz/3420/emailmanager/filter/type:0;" target="_blank">TEAMWORK - coordination &amp; productivity</a><img src="/img/icons/cross-small.png" style="cursor: pointer;" class="remove-object-alias" data-object-alias="biz" data-object-id="3420" data-email-id="19401">
                            </p>
                            </li>
                            </ul>

                            </li>   *} 
                            <li>
                                <label class="tree-toggler nav-header" style='cursor:pointer;'>My groups of BIZs:</label>
                                <ul class="nav nav-list tree" style="overflow: hidden; border-left: 1px dotted #cccccc;">
                                    <!-- Button trigger modal -->
                                    <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModalSearchBiz">
                                        Find & Add
                                    </button>
                                    <span class="btn btn-default btn-xs clear-biz-filter" style="display: none;">
                                        Remove filter
                                    </span>
                                    <div class="biz-menu-place">
                                        {if !empty($biz_menu)}
                                            {foreach from=$biz_menu item=group}
                                                <ul class="nav nav-list tree" style="border-left: 1px dotted #cccccc;">
                                                    <li>
                                                        <img src="/img/icons/cross-small.png" style="cursor: pointer; margin-top:5px;" class="find-biz-remove-group pull-left" data-group-id="{$group.id}">                                                    
                                                        <label class="tree-toggler nav-header" style='cursor:pointer;'>
                                                            {$group.title} <a data-group-id="{$group.id}" class="find-biz-group-link">Show for all BIZs</a>
                                                        </label>


                                                        <ul class="nav nav-list tree" style="border-left: 1px dotted #cccccc; display: none;">
                                                            {foreach from=$group.bizs item=biz}
                                                                <li>

                                                                    <a class="tag-biz" style="vertical-align: top; margin-right: 3px;" data-biz-id="{$biz.biz_id}" href="/biz/{$biz.biz_id}/emailmanager/filter/type:0;" target="_blank">{$biz.biz.doc_no_full}</a>
                                                                    <img src="/img/icons/cross-small.png" style="cursor: pointer;" class="find-biz-remove-biz" data-biz-id="{$biz.biz_id}" data-group-id="{$group.id}">
                                                                </li>
                                                            {/foreach}
                                                        </ul>

                                                    </li>
                                                </ul>
                                            {/foreach}
                                        {/if}
                                    </div>
                                    <!-- Modal -->
                                    <div class="modal fade" id="myModalSearchBiz" tabindex="-1" role="dialog" aria-labelledby="myModalSearchBizLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header emailmanager-sidebar-toolbar-header" style="margin-top: 0px;">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title" id="myModalLabel">Find & add BIZs</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row find-biz-search-form">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">Keyword</label>
                                                                <input type="email" class="form-control find-biz-keyword" id="" placeholder="Enter keyword">
                                                            </div>                                                        
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">Status</label>
                                                                <select class="form-control find-biz-status" id="">
                                                                    <option value="">All</option>
                                                                    {if isset($data.status_list) && !empty($data.status_list)}
                                                                        {foreach from=$data.status_list item=row}
                                                                            <option value="{$row|escape:'html'}"{if isset($form.status) && $form.status == $row} selected="selected"{/if}>{$row|escape:'html'}</option>
                                                                        {/foreach}	
                                                                    {/if}                                                                    
                                                                </select>
                                                            </div>                                                        
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">Market</label>
                                                                <select class="form-control find-biz-market" id="">
                                                                    <option value="0">All</option>
                                                                    {if isset($data.markets) && !empty($data.markets)}
                                                                        {foreach from=$data.markets item=row}
                                                                            {if isset($row.market.id)}
                                                                                <option value="{$row.market.id}"{if isset($form.market_id) && $form.market_id == $row.market.id} selected="selected"{/if}>{$row.market.title|escape:'html'}</option>
                                                                            {/if}
                                                                        {/foreach}
                                                                    {/if}
                                                                </select>
                                                            </div>                                                        
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">Objective</label>
                                                                <select class="form-control find-biz-objective" id="">
                                                                    <option value="0"{if !isset($form.objective_id) || empty($form.objective_id)} selected="selected"{/if}>All</option>
                                                                    {if isset($data.objectives) && !empty($data.objectives)}
                                                                        {foreach from=$data.objectives item=row}{if isset($row.objective)}
                                                                                <option value="{$row.objective.id}"{if isset($form.objective_id) && $form.objective_id == $row.objective.id} selected="selected"{/if}{if isset($row.objective.expired) && false} style="color: #999; font-style: italic;"{/if}>{$row.objective.title|escape:'html'}</option>
                                                                        {/if}{/foreach}
                                                                    {/if}                                                                    
                                                                </select>
                                                            </div>                                                       
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">Team</label>
                                                                <select class="form-control find-biz-team" id="">
                                                                    <option value="0">All</option>
                                                                    {if isset($data.teams) && !empty($data.teams)}
                                                                        {foreach from=$data.teams item=row}
                                                                            <option value="{$row.team.id}"{if isset($form.team_id) && $form.team_id == $row.team.id} selected="selected"{/if}>{$row.team.title|escape:'html'}</option>
                                                                        {/foreach}
                                                                    {/if}                                                                    
                                                                </select>
                                                            </div>                                                       
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">Product</label>
                                                                <select class="form-control find-biz-product" id="">
                                                                    {if $products['0'].product.id < 1}
                                                                        <option value="0">First select team</option>
                                                                    {else}
                                                                        {foreach from=$products item=row}
                                                                            <option value="{$row.product.id}"{if isset($form.product_id) && $form.product_id == $row.product.id} selected="selected"{/if}>{$row.product.title_list}</option>
                                                                        {/foreach}                                    
                                                                    {/if}                                                                    
                                                                </select>
                                                            </div>                                                       
                                                        </div>
                                                        {*<div class="col-md-4">                                          
                                                        <div class="form-group">
                                                        <label for="exampleInputEmail1">User</label>
                                                        <select class="form-control find-biz-user" id="">
                                                        <option value="0">All</option>
                                                        {if isset($data.users) && !empty($data.users)}
                                                        {foreach from=$data.users item=row}
                                                        <option value="{$row.user.id}"{if isset($form.driver_id) && $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                                                        {/foreach}
                                                        {/if}	
                                                        </select>
                                                        </div>                                                        
                                                        <div class="form-group">
                                                        <label for="exampleInputEmail1">Company</label>
                                                        <input class="form-control find-biz-company company-role-input supinv_company" type="text" name="form[company_title][]" onKeyDown="company_list($(this));" {if !empty($row.company.title)} value="{$row.company.title|escape:'html'}"{/if}>
                                                        <input class="supinv_company_id find-biz-company-id" type="hidden" name="form[company_id][]"{if isset($row.company.id)} value="{$row.company.id}"{/if}>    
                                                        </div>                                                        
                                                        </div>*}
                                                    </div>
                                                    <div class="row find-biz-find-btn-row">
                                                        <div class="col-md-12">
                                                            <span class="btn btn-primary btn-sm find-biz-for-nav">Find</span>      

                                                        </div>
                                                    </div>
                                                    <div class="row find-biz-search-result-row">
                                                        <div class="col-md-12 find-biz-search-result">

                                                        </div>
                                                    </div>
                                                    <div class="row find-biz-manage-group" style="display:none;">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <b>You can add selected BIZs to the group.</b>
                                                                    <br/>                                                                      
                                                                    <br/>                                                                      
                                                                </div>
                                                            </div>
                                                            <div class="row">

                                                                {if !empty($biz_menu)}
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputEmail1">My groups:</label>
                                                                            <div class="find-biz-group-select-place">
                                                                                <select class="form-control find-biz-group-id" id="">
                                                                                    <option value="0">/</option>
                                                                                    {if isset($biz_menu) && !empty($biz_menu)}
                                                                                        {foreach from=$biz_menu item=row}
                                                                                            <option value="{$row.id}">{$row.title}</option>
                                                                                        {/foreach}
                                                                                    {/if}	
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                {/if}

                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="exampleInputEmail1">Create a new group</label>
                                                                        <input class="form-control find-biz-new-group-title" type="text">
                                                                    </div>                                                                  
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer find-biz-search-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary find-biz-for-nav-save" style="display:none;">Create menu</button>
                                                </div>
                                                <div class="modal-footer find-biz-add-footer" style="display:none;">
                                                    <button type="button" class="btn btn-default find-biz-btn-back" >Back</button>
                                                    <button type="button" class="btn btn-success find-biz-menu-save">Save</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </ul>

                            </li>  
                            <!--
                            <li>
                                <label class="tree-toggler nav-header" style='cursor:pointer;'>Last 20 BIZs</label>
                                <ul class="nav nav-list tree" style="border-left: 1px dotted #cccccc; display: none;">

                            {if !empty($bizes_list)}
                                {$max_displayed_items=10}
                                <div class="email-menu bizes-container">
                                    <div class="bc-list ">
                                {foreach from=$bizes_list.data item=row name=bizes}
                                    {if $object_alias == 'biz' && $row.biz.id == $object_id}
                                        <div class="bc-list-row" style="border-bottom: 1px dotted #1098f7;"><a href="/biz/{$row.biz.id}/emailmanager/filter/type:0" title="{$row.biz.doc_no_full|escape:'html'}"><b>{$row.biz.doc_no_full|escape:'html'}</b></a></div>
                                    {else}
                            <div class="bc-list-row" style="border-bottom: 1px dotted #cccccc;"><a href="/biz/{$row.biz.id}/emailmanager/filter/type:0" title="{$row.biz.doc_no_full|escape:'html'}">{$row.biz.doc_no_full|escape:'html'}</a></div>
                                    {/if}
                                {/foreach}
                        </div>

                    </div>
                            {/if}    

                        </ul>

                    </li>    -->

                        </ul>
                    </div>
                </div>
                {*
                <div class="alert fade in">
                <button data-dismiss="alert" class="close" type="button">×
                </button>
                <strong>Внимание!</strong> Это уведомление будет 
                оформлено оттенками желтого и плавно закроется.
                </div>*}
                <div class="row row-form" style="display: none;">
                    <div class="emailmanager-sidebar-toolbar-header">Type of emails</div>

                    <div class="col-md-12 emailmanager-sidebar-toolbar">
                        <p class="explanation"><i class="glyphicon glyphicon-question-sign"></i> Use to select the type of emails that are displayed.</p>
                        <p>
                            Current: 
                            {if $type_id == 0 && $page_alias !== "email_main_deletedbyuser"}

                                <a class="i-email-all-normal" href="/emailmanager/filter/{if $mailbox_id>0}mailbox:{$mailbox_id};{/if}type:0">All emails</a>
                            {elseif $type_id  == 1}

                                <a class="i-email-all-normal" href="/emailmanager/filter/{if $mailbox_id>0}mailbox:{$mailbox_id};{/if}type:1">Inbox emails</a>
                            {elseif $type_id == 2}

                                <a class="i-email-all-normal" href="/emailmanager/filter/{if $mailbox_id>0}mailbox:{$mailbox_id};{/if}type:2">Sent emails</a>
                            {elseif $type_id==3 && $pager_path=='/emailmanager/dfa/other'}

                                <a class="i-email-all-normal" href="/emailmanager/dfa/other">Drafts of other users</a>
                            {elseif $type_id==3 && $pager_path=='/emailmanager/dfa'}

                                <a class="i-email-all-normal" href="/emailmanager/dfa/other">My drafts</a>
                            {elseif $type_id==5}

                                <a class="i-email-all-normal" href="/emailmanager/filter/{if $mailbox_id>0}mailbox:{$mailbox_id};{/if}type:2">Spam emails</a>
                            {elseif $type_id==4}

                                <a class="i-email-all-normal" href="/emailmanager/filter/{if $mailbox_id>0}mailbox:{$mailbox_id};{/if}type:4">Corrupted emails</a>
                            {elseif $page_alias == "email_main_deletedbyuser"}

                                <a class="i-email-all-normal" href="/emailmanager/emailmanager/deleted">Trash</a>
                            {/if}
                        </p>
                        <ul class="" role="menu">
                            {if $type_id == 0 && $page_alias !== "email_main_deletedbyuser"}
                            {else}
                                <li><a class="i-email-all-normal" href="/emailmanager/filter/{if $mailbox_id>0}mailbox:{$mailbox_id};{/if}type:0">All emails</a></li>
                                {/if}   

                            {if $type_id  == 1}
                            {else}
                                <li><a class="i-email-inbox" href="/emailmanager/filter/{if $mailbox_id>0}mailbox:{$mailbox_id};{/if}type:1">Inbox emails</a></li>
                                {/if}

                            {if $type_id == 2}
                            {else}
                                <li><a class="i-email-outbox" href="/emailmanager/filter/{if $mailbox_id>0}mailbox:{$mailbox_id};{/if}type:2">Sent emails</a></li>
                                {/if}

                            {if $type_id==3 && $pager_path=='/emailmanager/dfa'}
                            {else}
                                <li><a class="i-email-dfa" href="/emailmanager/dfa">My drafts</a></li>
                                {/if}

                            {if $type_id==3 && $pager_path=='/emailmanager/dfa/other'}
                            {else}
                                <li><a class="i-email-dfa other" href="/emailmanager/dfa/other">Drafts of other users</a></li>
                                {/if}

                            {if $type_id==5}
                            {else}
                                <li><a class="i-email-spam" href="/emailmanager/filter/{if $mailbox_id>0}mailbox:{$mailbox_id};{/if}type:5">Spam emails</a></li>
                                {/if}

                            {if $page_alias == "email_main_deletedbyuser"}
                            {else}
                                <li><a class="i-email-deleted" href="/emailmanager/deleted">Trash</a></li>
                                {/if}

                        </ul>
                    </div>
                </div>                        

            </div>


            <div class="col-md-9" style="padding-left: 10px; padding-right: 2px;">  
                {*
                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                <li class="active"><a href="#emails" data-toggle="tab">All emails</a></li>
                <li><a href="#emails-starr" data-toggle="tab">Favorites emails</a></li>
                {*<li><a href="#filters" data-toggle="tab">Filters</a></li>
                <li><a href="#saved-settings" data-toggle="tab">Saved settings</a></li>*}
                {*</ul>*}
                <div class="col-md-12">
                    <table id="jsonmap"></table>
                    <div id="pjmap"></div>
                </div>


            </div>
        </div>    
    </form>
</div>