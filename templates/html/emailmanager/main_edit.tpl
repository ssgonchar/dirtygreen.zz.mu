<div class="col-md-12">
    <div class="row">
        <div class="col-md-4" style="padding-right: 8px; border-right: dotted 1px #ccc;">
            <div class="row">
                <div class="col-md-12 col-xs-6">
                    <!-- To -->
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon"><b>To*</b></span>
                        <input type="text" id="recipients" name="form[recipient_address]" placeholder="start typing" class="email-recipient form-control"{if isset($form) && isset($form.recipient_address)} value='{$form.recipient_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}>
                    </div>
                    <!-- CC -->
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon">CC</span>
                        <input type="text" name="form[cc_address]" placeholder="start typing" class="email-recipient form-control"{if isset($form) && isset($form.cc_address)} value='{$form.cc_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}>
                    </div>
                    <!-- BCC -->
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon">BCC</span>
                        <input type="text" name="form[bcc_address]" placeholder="start typing" class="email-recipient form-control"{if isset($form) && isset($form.bcc_address)} value='{$form.bcc_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}>
                    </div>
                    
                    
                    <!-- Recipients -->
                    <div class="panel-group" id="emails-list" style="margin-bottom: 10px;">
                        <div class="panel panel-default" style="position: relative;">
                            <span id="add-emails" class="btn btn-xs btn-primary pull-right" style="cursor: pointer; position: absolute; right: 5px; top: 3px;">Add from system</span>
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#emails-list" href="#collapseTwo">
                                        <i class="glyphicon glyphicon-collapse-down"> </i> Recipients
                                    </a>
                                    <span id="emails-count"></span> 
                                    {*include file='templates/layouts/loader.tpl'*}
                                </h4>
                            </div>
                            <div id="collapseTwo" class="panel-collapse collapse">
                                <div class="panel-body" style="padding: 5px; background-color: #f5f5f5;">
                                    <div style="width: 100%;"> 
                                        <ul id="emails-from-system">
                                            {include file="templates/html/emailmanager/control_recipients_from_controller.tpl"}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Driver (если письмо создается из биза, то выбираю драйвером драйвера биза) -->
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon">Driver</span>
                        <select id="driver-select" name="form[driver]" class="form-control"  style='height: 28px;'>
                            <option value="0">--</option>
                            {if isset($objects) && $objects.0.object_alias == 'biz' && isset($objects.0.biz_driver)}
                                {foreach from=$mam_list item=row}
                                    <option value="{$row.user.id}"{if $objects.0.biz_driver == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                                {/foreach}
                            {else}
                                {foreach from=$mam_list item=row}
                                    <option value="{$row.user.id}"{if isset($form.driver) && $form.driver == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </div>
                    <!-- Navigator -->
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon">Navigator</span>
                        <select id="navigator-select" name="form[navigator]" class="form-control"  style='height: 28px;'>
                            <option value="0">--</option>
                            {foreach from=$team_list item=row}
                                <option value="{$row.user.id}"{if isset($form.navigator) && $form.navigator == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                            {/foreach}
                        </select>
                    </div>
                    <!-- Seek Response -->
                    {*<div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon">Seek Response</span>
                        <input type="text" name="form[seek_response]" class="datepickers form-control" value="{if !empty($form.seek_response) && $form.seek_response > 0}{$form.seek_response|escape:'html'|date_format:'d/m/Y'}{/if}">
                    </div>*}
                </div>
            </div>
            <div class="form-group">
                <div class="separator"></div>
                <div style="display: block; background: #99CCFF; -moz-border-radius: 20px;" >
                    <div class="qq-upload-drop-area"></div>
                </div>
                <!-- Attachments -->
                <div class="panel-group" id="accordion" style="margin-bottom: 10px;">
                    <div class="panel panel-default" style="position: relative;">
                        <span id="add-shared-docs" class="btn btn-xs btn-primary pull-right" style="cursor: pointer; position: absolute; right: 5px; top: 3px;">Add from system</span>
                        <div class="panel-heading"> <!--  style="height: 30px;"-->
                            <h4 class="panel-title"> <!--  style="line-height: 8px;" -->
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                    <i class="glyphicon glyphicon-collapse-down"> </i> Attachments 
                                </a>
                                <span id="attachments-count"></span>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse">
                            <div class="panel-body" style="padding: 5px; background-color: #f5f5f5;">
                                <div id="attachments" style="width: 100%;">
                                    {if isset($attachments) && !empty($attachments)}
                                        {foreach name=i from=$attachments item=row}
                                            {if !empty($row.attachment)}
                                                {include file="templates/html/emailmanager/control_attachment_block_text.tpl" attachment=$row.attachment object_alias=$uploader_object_alias object_id=$uploader_object_id}
                                            {/if}
                                        {/foreach}
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                    
                <div id="fileuploader"></div>
                <input type="hidden" id="uploader_object_alias" name="uploader_object_alias" value="{$uploader_object_alias}">
                <input type="hidden" id="uploader_object_id" name="uploader_object_id" value="{$uploader_object_id}">      
            </div>
        </div>
        <div class="col-md-8 container-fluid" style="padding-left: 8px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon">Attention :</span>
                        <input type="text" id="email-person" name="form[attention]" class="form-control wide"{if isset($form) && isset($form.attention)} value='{$form.attention|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}>
                    </div>
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon">Our Ref :</span>
                        <input type="text" name="form[our_ref]" class="form-control wide"{if isset($form) && isset($form.our_ref)} value="{$form.our_ref|escape:'html'}"{/if}>
                    </div>
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon">Your Ref :</span>
                        <input type="text" name="form[your_ref]" class="form-control wide"{if isset($form) && isset($form.your_ref)} value="{$form.your_ref|escape:'html'}"{/if}>
                    </div>
                    <!-- Subject -->
                    <div class="input-group" style="margin-bottom: 10px;">
                        <span class="input-group-addon"><b>Subject*</b></span>
                        <input type="text" name="form[title]" class="form-control"{if isset($form) && isset($form.title)} value='{$form.title|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}'{/if}>
                    </div>
                    <!-- Email text -->
                    <label for="exampleInputEmail1">{if isset($form) && isset($form.parent_id) && !empty($form.parent_id)}Answer{else}Text{/if}</label>
                    {if isset($form) && isset($form.parent_id) && !empty($form.parent_id) && isset($form.parent)}
                        <span id="show-parent-email" class="btn btn-primary btn-xs">Show parent email</span>
                        <div id="parent-email-block" style="display: none;">
                            <div style="width: 100%; height: 200px; overflow: auto;"> 
                                <blockquote class="email-answer"> 
                                    <span style="font-weight: bold; font-size: 1.1em;">
                                        <<
                                    </span>
                                    <br>
                                    {$form.parent.description|nl2br}
                                    <span style="font-weight: bold; font-size: 1.1em;">
                                        >>
                                    </span>
                                </blockquote>
                                <br>
                                <div style="float: right;">
                                    {$form.parent.date_mail|date_format:"d/m/Y"}
                                    <br>From : {$form.parent.sender_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}
                                    <br>To : {$form.parent.recipient_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}
                                    <br>Subject : {$form.parent.title|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}
                                </div>
                                <br>
                            </div>
                        </div>
                        <br>
                    {/if}
                    <div id="description-block" class="form-group">
                        <textarea id="email_text" name="form[description]" style="width: 100%">
                            
                        </textarea>
                    </div>
                    <div class="col-md-6" style="padding-left: 0px;">
                        <!-- From -->
                        <div class="input-group" style="margin-bottom: 10px;">
                            <span class="input-group-addon" style=""><b>Team*</b></span>
                            <select id="mailbox_id" name="form[sender_address]" class="form-control" style='height: 30px;' onchange="email_select_signature(this.value);">
                                <option value="">--</option>
                                {foreach from=$mailboxes item=row}
                                    <option value="{$row.mailbox.address}"{if isset($form) && isset($form.sender_address) && $form.sender_address == $row.mailbox.address} selected="selected"{/if}>{$row.mailbox.address}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <!-- Signature -->
                    <div class="input-group col-md-6" style="margin-bottom: 10px;">
                        <span class="input-group-addon">Email footer:</span>
                        <input type="text" name="form[signature]" class="form-control " style='height: 30px;'{if isset($form) && isset($form.signature)} value="{$form.signature|escape:'html'}"{/if}>
                    </div>

                    <div id="email-signature-pa1" class="input-group col-md-12" style="margin-bottom: 10px; {if !isset($sender_domain) || $sender_domain != 'platesahead.com'} display: none;{/if}">
                        <span class="input-group-addon" style="line-height: 16px;">
                            <b>PlatesAhead Inc.</b>&nbsp;/&nbsp;
                        </span>
                        <input type="text" name="form[signature3]" class="form-control" style="height: 30px; font-style: italic;" value="{if isset($form) && isset($form.signature3)}{$form.signature3|escape:'html'}{else}{$smarty.session.user.login}{/if}">
                    </div>
                    <div id="email-signature-se1" class="input-group col-md-12" style="margin-bottom: 10px; {if !isset($sender_domain) || $sender_domain != 'steelemotion.com'} display: none;{/if}">
                        <span class="input-group-addon" style="line-height: 16px;">
                            <b>STEELemotion</b>&nbsp;/&nbsp;
                        </span>
                        <input type="text" name="form[signature3]" class="form-control" style="height: 30px; font-style: italic;" value="{if isset($form) && isset($form.signature3)}{$form.signature3|escape:'html'}{else}{$smarty.session.user.login}{/if}">
                    </div>
                    
                    <textarea id="email-signature2" name="form[signature2]" class="max form-control" style="margin: 5px 0;{if !isset($sender_domain)} display: none;{/if}">{if isset($form) && isset($form.signature2)}{$form.signature2|escape:'html'}{/if}</textarea><br>
                    <span id="email-signature-pa2" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'platesahead.com'} display: none;{/if}"><a href="http://www.platesahead.com/">http://www.PlatesAhead.com/</a></span>
                    <!--<span id="email-signature-se2" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'steelemotion.com'} display: none;{/if}"><a href="http://www.steelemotion.com/">http://www.STEELemotion.com/</a></span>-->
                    <span id="email-signature-se2" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'steelemotion.com'} display: none;{/if}"></span>
                </div>
                
                <input type="hidden" name="form[parent_id]" value="{if isset($form) && isset($form.parent_id)}{$form.parent_id}{/if}">
            </div>
        </div>


        {*<table class="form" width="100%">
            {if isset($form) && isset($form.parent_id) && !empty($form.parent_id) && isset($form.parent)}
                <tr>
                    <td class="form-td-title-b text-top">Text : </td>
                    <td>
                        <div style="width: 90%; height: 200px; overflow: auto;">
                            {$form.parent.date_mail|date_format:"d/m/Y"}
                            <br>From : {$form.parent.sender_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}
                            <br>To : {$form.parent.recipient_address|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}
                            <br>Subject : {$form.parent.title|replace:'\'':'"'|replace:'<':'&lt;'|replace:'>':'&gt;'}
                            <br><blockquote class="email-answer">
                                {$form.parent.description|nl2br}
                            </blockquote>
                        </div>
                    </td>
                </tr>    
            {/if}
        </table>*}

        <div id="email-co-select" style="display: none;">
            <div id="overlay"></div>
            <div id="email-co-container">
                <div style="padding: 10px;">
                    <table class="form" width="100%">
                        <tr>
                            <td>Type : </td>
                        </tr>
                        <tr>
                            <td>
                                <select id="email-co-type-alias" class="max" onchange="email_clear_search_results();">
                                    <option value="">--</option>
                                    <option value="biz">Biz</option>
                                    <option value="company">Company</option>
                                    <option value="country">Country</option>
                                    <option value="order">Order</option>
                                    <option value="person">Person</option>
                                    <option value="product">Product</option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <td>Search For : </td>
                        </tr>
                        <tr>
                            <td>Search Result : </td>
                        </tr>
                        <tr>
                            <td>
                                <select id="email-co-search-result" multiple="multiple" size="10" class="max" style="height: 200px;"></select>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-center">
                                <input type="button" class="btn100o" value="Add" style="margin-right: 20px;" onclick="add_email_object();">
                                <input type="button" class="btn100" value="Close" onclick="close_email_co_list();">
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        {*debug*}
        
        <!-- Модальное окно выбора approve by и approve deadline при сохранении черновика -->
        <div class="modal fade" id="modal-approve" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header emailmanager-sidebar-toolbar-header" style="margin-top: 0px;">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Approve options</h4>
                    </div>
                    <div class="row modal-body" style="padding-bottom: 10px;">
                        <div class="col-md-6" style="padding-left: 5px;">
                            <!-- Approve By -->
                            <div class="input-group" style="margin-bottom: 10px;">
                                <span class="input-group-addon">Approve By</span>
                                <select id="approve-by-select" name="form[approve_by]" class="form-control"  style='height: 28px;'>
                                    <option value="0">--</option>
                                    {foreach from=$mam_list item=row}
                                        <option value="{$row.user.id}"{if isset($form.approve_by) && $form.approve_by == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" style="padding-right: 5px;">
                            <!-- Approve deadline -->
                            <div class="input-group" style="margin-bottom: 10px;">
                                <span class="input-group-addon">Approve deadline</span>
                                <input id="approve-deadline-input" class="datepickers form-control" disabled type="text" name="form[approve_deadline]" value="{if !empty($form.approve_deadline) && $form.approve_deadline > 0}{$form.approve_deadline|escape:'html'|date_format:'d/m/Y'}{/if}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding: 10px 20px; margin-top: 0px;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Back</button>
                        <input id="save-dfa" type="button" class="btn btn-success" value="Save draft" style="margin-left: 10px; cursor: pointer;" {*onclick="document.getElementById('mainform').submit();"*}>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Модальное окно выбора документов из системы -->                                        
        <div class="modal fade" id="shared-docs-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <form role="form" id="" action="" method="POST">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <!-- стандартный заголовок для EMM -->
                        <div class="modal-header emailmanager-sidebar-toolbar-header" style="margin-top: 0px;">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" title="Shared documents">Get documents</h4>
                        </div>
                        <div id="shared-docs-modal-body" class="row modal-body" style="padding-bottom: 0px;">
                            <div class="row col-md-12" style="padding-right: 0px;">
                                <!-- Get files from -->
                                <div class="col-md-6">
                                    <div class="input-group" style="margin-bottom: 10px;">
                                        <span class="input-group-addon">From: </span>
                                        <select id="docs-select-from" class="form-control" style='height: 28px;'>
                                            <option value="task-biz">BIZ</option>
                                            <option value="company">Company</option>
                                            <option value="ra">RA</option>
                                            <option value="order">Order</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6" style="padding-right: 0px;">
                                    <!-- BIZ inputs -->
                                    <input id="task-biz" placeholder="start typing to select BIZ" class="shared-files-input form-control" style='height: 28px;'>
                                    <input id="task-biz-id" type="hidden" name="form[biz_id]" value="0">
                                    <!-- Company inputs -->
                                    <input id='company' class="supinv_company shared-files-input max company-role-input form-control" placeholder="start typing to select company" type="text" name="form[company_title]supinv_company []" onKeyDown="company_list($(this));" style='display: none;'>
                                    <input class="supinv_company_id" type="hidden" name="form[company_id][]" value="0">
                                    <div class="input-group" style="width: 100%;">
                                        <!-- RA inputs -->
                                        <input id="ra" class="shared-files-input form-control" placeholder="please enter RA id" style='height: 28px; width: 78.5%; display: none;'>
                                        <span id='find-ra-files' class="find-btn btn btn-primary" style="padding: 3px 12px; display: none;">Find</span>
                                        <!-- Orders inputs -->
                                        <input id="order" class="shared-files-input form-control" placeholder="please enter Order id" style='height: 28px; width: 78.5%; display: none;'>
                                        <span id='find-order-files' class="find-btn btn btn-primary" style="padding: 3px 12px; display: none;">Find</span>
                                    </div>
                                </div>
                                <br>
                                <br>
                            </div>
                            <!-- сюда заполняются shared files -->
                            <div class="col-md-12 shared-files-div">
                                {include file='templates/html/emailmanager/object_shared_files.tpl'}
                            </div>
                        </div>
                        <div class="modal-footer" style="padding: 10px 20px; margin-top: 0px;">
                            <span id='add-doc'   <input class="btn btn-success">Apply</span>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
                            
        <!-- Модальное окно выбора email адресов из системы -->
        <div class="modal fade" id="filters" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <form role="form" id="filters" action="" method="POST">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <!-- стандартный заголовок для EMM -->
                        <div class="modal-header emailmanager-sidebar-toolbar-header" style="margin-top: 0px;">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">
                                Get email addresses
                                {*<span class='search-tools btn btn-xs btn-primary'><i class='glyphicon glyphicon-th'></i> Search tools</span>*}
                            </h4>
                        </div>
                        <div id="emails-search-modal-body" class="modal-body" style='padding-top: 20px;'>
                            <div class="tab-content">
                                <div class="tab-pane active" id="filter-settings">
                                    <div class="row">
                                        <div class="search-tools-div col-md-12">
                                            <div class="col-md-6" style="padding-left: 0px; padding-right: 5px;">
                                                <div class="input-group" style="margin-bottom: 10px;">
                                                    <span class="input-group-addon">From: </span>
                                                    <select id="emails-select-from" class="form-control" style='height: 28px;'>
                                                        <option value="emails-company">Company</option>
                                                        <option value="emails-person">Person</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 input-group" style="padding-left: 5px;">
                                                <!-- Company inputs -->
                                                <input id="emails-company" class="emails-company emails-search-input form-control" placeholder="start typing to select company" type="text" onKeyDown="company_autocomplete_for_emails_search($(this));" style='height: 28px; width: 86.5%; margin-top: 0px;'>
                                                <input class="emails-company-id" type="hidden">
                                                <span id='find_company' class="btn btn-primary" style="padding: 3px 12px;">Find</span>
                                                <!-- Person inputs -->
                                                <input id="emails-person" class="email-person emails-person emails-search-input form-control" placeholder="start typing to select person" style='height: 28px; display: none;'>
                                                <input id='company-id-from-person' class="hidden">
                                                {*<input id="emails-person" class="emails-person emails-search-input form-control" placeholder="start typing to select person" style='height: 28px; display: none;'>
                                                <input id="emails-person-id" type="hidden" name="form[biz_id]" value="0">*}
                                            </div>
                                            
                                        <!-- инструменты расширенного поиска компании -->
                                            {*<div class="row form">
                                                <!-- First column -->
                                                <div class="col-md-6">
                                                <!-- Country -->
                                                    <div class="input-group" style="margin-top: 15px;">
                                                        <span class="input-group-addon" style="">Country: </span>
                                                        <select id="country" name="country_id" class="max form-control" style='height: 28px;'>
                                                            <option value="0">--</option>
                                                            {foreach from=$countries item=row}
                                                                <option value="{$row.country.id}"{if isset($country_id) && $country_id == $row.country.id} selected="selected"{/if}>{$row.country.title}</option>
                                                            {/foreach}
                                                        </select> 
                                                    </div>
                                                <!-- Region -->
                                                    <div class="input-group" style="margin-top: 15px;">
                                                        <span class="input-group-addon" style="">Region: </span>
                                                        <select id="region" name="region_id" class="max form-control" style='height: 28px;'>
                                                            <option value="0">--</option>
                                                            {if isset($regions)}
                                                                {foreach from=$regions item=row}
                                                                    <option value="{$row.region.id}"{if isset($region_id) && $region_id == $row.region.id} selected="selected"{/if}>{$row.region.title}</option>
                                                                {/foreach}
                                                            {/if}
                                                        </select>  
                                                    </div>
                                                <!-- City -->
                                                    <div class="input-group" style="margin-top: 15px;">
                                                        <span class="input-group-addon" style="">City: </span>
                                                        <select id="city" name="city_id" class="max form-control" style='height: 28px;'>
                                                            <option value="0">--</option>
                                                            {if isset($cities)}
                                                                {foreach from=$cities item=row}
                                                                    <option value="{$row.city.id}"{if isset($city_id) && $city_id == $row.city.id} selected="selected"{/if}>{$row.city.title}</option>
                                                                {/foreach}
                                                            {/if}                            
                                                        </select>
                                                    </div>
                                                <!-- Industry -->
                                                    <div class="input-group" style="margin-top: 15px;">
                                                        <span class="input-group-addon" style="">Industry: </span>
                                                        <select id="sel_industry" name="industry_id" class="max form-control" style='height: 28px;' onchange="fill_activities(this.value, 'sel_activity');">
                                                            <option value="0">--</option>
                                                            {foreach from=$industries item=row}
                                                                <option value="{$row.activity.id}"{if isset($industry_id) && $industry_id == $row.activity.id} selected="selected"{/if}>{$row.activity.title}</option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                <!-- Activity -->
                                                    <div class="input-group" style="margin-top: 15px;">
                                                        <span class="input-group-addon" style="">Activity: </span>
                                                        <select id="sel_activity" name="activity_id" class="max form-control" style='height: 28px;' onchange="fill_activities(this.value, 'sel_speciality');">
                                                            <option value="0">--</option>
                                                            {foreach from=$activities item=row}
                                                                <option value="{$row.activity.id}"{if isset($activity_id) && $activity_id == $row.activity.id} selected="selected"{/if}>{$row.activity.title}</option>
                                                            {/foreach}                                
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- Second column -->
                                                <div class="col-md-6">
                                                <!-- Speciality -->
                                                    <div class="input-group" style="margin-top: 15px;">
                                                        <span class="input-group-addon" style="">Speciality: </span>
                                                        <select id="sel_speciality" name="speciality_id" class="max form-control" style='height: 28px;'>
                                                            <option value="0">--</option>
                                                            {foreach from=$specalities item=row}
                                                                <option value="{$row.activity.id}"{if isset($speciality_id) && $speciality_id == $row.activity.id} selected="selected"{/if}>{$row.activity.title}</option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                <!-- Product -->
                                                    <div class="input-group" style="margin-top: 15px;">
                                                        <span class="input-group-addon" style="">Product: </span>
                                                        <select class="max form-control" style='height: 28px;' name="product_id">
                                                            <option value="0">--</option>
                                                            {foreach from=$products item=row}
                                                                <option value="{$row.product.id}"{if isset($product_id) && $product_id == $row.product.id} selected="selected"{/if}>{$row.product.title_list}</option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                <!-- Feedstock -->
                                                    <div class="input-group" style="margin-top: 15px;">
                                                        <span class="input-group-addon" style="">Feedstock: </span>
                                                        <select class="max form-control" style='height: 28px;' name="feedstock_id">
                                                            <option value="0">--</option>
                                                            {foreach from=$products item=row}
                                                                <option value="{$row.product.id}"{if isset($feedstock_id) && $feedstock_id == $row.product.id} selected="selected"{/if}>{$row.product.title_list}</option>
                                                            {/foreach}                                
                                                        </select>
                                                    </div>
                                                <!-- Relation -->
                                                    <div class="input-group" style="margin-top: 15px;">
                                                        <span class="input-group-addon" style="">Relation: </span>
                                                        <select class="max form-control" style='height: 28px;' name="relation_id">
                                                            {include file="templates/controls/html_element_options.tpl" list=$co_relations_list selected=$relation}
                                                        </select>
                                                    </div>
                                                <!-- Status -->
                                                    <div id="last-div" class="input-group" style="margin-top: 15px;">
                                                        <span class="input-group-addon" style="">Status: </span>
                                                        <select class="max form-control" style='height: 28px;' name="status_id">
                                                            {include file="templates/controls/html_element_options.tpl" list=$co_statuses_list selected=$status}
                                                        </select>
                                                    </div>
                                                    <span id='find_company' value="Find" class="btn btn-primary pull-right" style="margin-top: 15px;">Find</span>
                                                </div>        
                                            </div>
                                            <span id='find_company' value="Find" class="btn btn-primary pull-right" style="margin-top: 5px; margin-bottom: 5px; margin-right: 15px;">Find</span>*}
                                        </div>
                                            <!-- С‚СѓС‚ Р·Р°РїРѕР»РЅСЏРµС‚СЃСЏ СЃРїРёСЃРѕРє РєРѕРјРїР°РЅРёР№ -->
                                        <div class="row row-content col-md-12">
                                            {if isset($companies_list)}
                                                {include file="templates/html/emailmanager/control_companies.tpl"}
                                            {/if}
                                        </div>   
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="padding: 10px 20px; margin-top: 0px;">
                             <span id='add_company'   <input class="btn btn-success">Apply</span>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>                            
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
                                        
                                        
                  
    {*<div class="col-md-12 col-xs-6">
    <div class="form-group">
    <label for="exampleInputEmail1">Approve By</label>
    <select name="form[approve_by]" class="form-control">
    <option value="0">--</option>
    {foreach from=$mam_list item=row}
    <option value="{$row.user.id}"{if isset($form.approve_by) && $form.approve_by == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
    {/foreach}
    </select>
    </div>  
    </div>*}
    
    
    {*
    <div class="form-group">
    <label for="exampleInputEmail1">Email group</label>
    <select name="form[doc_type]" class="form-control">
    <option value="0">--</option>
    {foreach from=$doctypes_list item=row}
    <option value="{$row.id}"{if $form.doc_type == $row.id} selected="selected"{/if}>{$row.name|escape:'html'}</option>
    {/foreach}
    </select>
    </div>
    *}
    
    {*<div class="col-xs-12">
        <div class="form-group form-inline">
            <label for="exampleInputEmail1">Signature</label><br>
            <input type="text" name="form[signature]" class="form-control "{if isset($form) && isset($form.signature)} value="{$form.signature|escape:'html'}"{/if}>
            <span id="email-signature-pa1" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'platesahead.com'} display: none;{/if}">
                <b>PlatesAhead Inc.</b>&nbsp;/&nbsp;
                <input type="text" name="form[signature3]" class="form-control" style="font-style: italic;" value="{if isset($form) && isset($form.signature3)}{$form.signature3|escape:'html'}{else}{$smarty.session.user.login}{/if}">
                <br>
            </span>
            <span id="email-signature-se1" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'steelemotion.com'} display: none;{/if}">
                <b>STEELemotion</b>&nbsp;/&nbsp;
                <input type="text" name="form[signature3]" class="form-control" style="font-style: italic;" value="{if isset($form) && isset($form.signature3)}{$form.signature3|escape:'html'}{else}{$smarty.session.user.login}{/if}">
                <br>
            </span>
            <textarea id="email-signature2" name="form[signature2]" class="max form-control" style="margin: 5px 0;{if !isset($sender_domain)} display: none;{/if}">{if isset($form) && isset($form.signature2)}{$form.signature2|escape:'html'}{/if}</textarea><br>
            <span id="email-signature-pa2" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'platesahead.com'} display: none;{/if}"><a href="http://www.platesahead.com/">http://www.PlatesAhead.com/</a></span>
            <!--<span id="email-signature-se2" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'steelemotion.com'} display: none;{/if}"><a href="http://www.steelemotion.com/">http://www.STEELemotion.com/</a></span>-->
            <span id="email-signature-se2" style="line-height: 16px;{if !isset($sender_domain) || $sender_domain != 'steelemotion.com'} display: none;{/if}"></span>                
        </div>
    </div>*}

    {*<div class="row">

    <div class="col-md-2 col-xs-6">
    <div class="form-group">
    <label for="exampleInputEmail1">Tags</label><br/>
    <a class="add" href="javascript: void(0);" onclick="show_email_co_list();">Add Tag</a>
    <div class="email-co-objects-list" style="margin-top: 10px;">
    {if !empty($objects)}
    {foreach $objects as $item}
    <span id="{$item.object_alias|escape:'html'}-{$item.object_id}" style="margin-right: 10px;">
    <input type="hidden" name="objects[{$item.object_alias|escape:'html'}-{$item.object_id}]" class="{$item.object_alias|escape:'html'}-object" value="{$item.object_id}">
    <a class="tag-{if in_array($item.object_alias, array('biz', 'company', 'order', 'person'))}{$item.object_alias}{else}document{/if}" style="vertical-align: top; margin-right: 3px;" href="/{$item.object_alias|escape:'html'}/{$item.object_id}" target="_blank">{$item.title|escape:'html'}</a><img src="/img/icons/cross-small.png" onclick="remove_email_object('{$item.object_alias|escape:'html'}', {$item.object_id});">
    </span><br/>
    {/foreach}
    {/if}
    </div>
    </div>                         
    </div>
    </div>
    </div>*}
    
    <table width="100%">
        <tr>

            <!--    РўРёРї СЃРѕРѕР±С‰РµРЅРёСЏ - РїРµСЂРµРЅРµСЃРµРЅ РІ РІРµСЂС…РЅСЋСЋ С„РѕСЂРјСѓ
            <td class="text-top">
                <table class="form" width="100%">
                    <tr>
                        <td class="form-td-title-b">eMail Type : </td>
                        <td class="text-top">
                            <select name="form[doc_type]" class="narrow">
                                <option value="0">--</option>
            {*{foreach from=$doctypes_list item=row}
            <option value="{$row.id}"{if $form.doc_type == $row.id} selected="selected"{/if}>{$row.name|escape:'html'}</option>
            {/foreach}*}
        </select>
    </td>
    </tr>               
    <tr>

    </tr>
    </table>        
    </td>--> 
            <!--    РЎРїРёСЃРѕРє РґСЂР°Р№РІРµСЂРѕРІ - С‚Рµ Сѓ РєРѕС‚РѕСЂС‹С… РµСЃС‚СЊ is_driver РІ Р‘Р” users
            <td class="text-top" width="33%">
                <table class="form" width="100%">

                    <tr>
                        <td class="form-td-title-b">Driver : </td>
                        <td>

                            <select name="form[driver_id]" class="narrow">
                                <option value="0">--</option>
            {*{foreach from=$mam_list item=row}
            <option value="{$row.user.id}"{if !empty($form.driver_id) && $form.driver_id == $row.user.id} selected="selected"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</option>
            {/foreach}*}
        </select>
    </td>
    </tr>-->

            <!--    <tr>
                            <td class="form-td-title-b text-top">Navigators : </td>
                            <td class="text-top">
            {*{foreach name='navigators' from=$mam_list item=row}
            <div style="float: left; width: 110px; margin-bottom: 2px;"><label for="navigator-{$row.user.id}"><input id="navigator-{$row.user.id}" type="checkbox" name="navigators[{$row.user.id}][user_id]" value="{$row.user.id}" style="margin-right: 5px;"{if isset($row.selected)} checked="checked"{/if}>{$row.user.login|escape:'html'}{if !empty($row.user.nickname)} ({$row.user.nickname}){/if}</label></div>
            {/foreach}*}
        </td>
    </tr>           
    </table>
    </td>--> 
        </tr>
    </table>
        
    {*<tr>
        <td><input id="keyword" type="text" class="max" onkeypress="if (event.keyCode == 13)
            return false;" onkeyup="{literal}if (event.keyCode == 13) {
                        find_email_objects(this.value);
                        return false;
                    }{/literal}"></td>
            {*<td><input type="text" class="max" onkeypress="if(event.keyCode == 13) return false;" onkeyup="find_email_objects(this.value);"></td>
    </tr>*}