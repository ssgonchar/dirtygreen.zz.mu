<ul class="nav nav-sidebar">
    <div class="col-md-6" style="padding-left: 0px;">
        <!-- Split button -->
        {*
        <div class="input-group input-group-sm">
        <input title="Search message in archive" type="text" readonly class="chat-archive-dateto form-control"  data-date="{$date_to|date_format:'%Y-%m-%d'}" style="cursor: pointer" placeholder="Select date">         
        <span class="input-group-btn input-group-sm">
        <button class="btn btn-primary search-chat-archive"><i class="glyphicon glyphicon-search"></i></button>
        </span> 
        </div> 
        *}
        <!-- Button trigger modal -->
        <span class="btn btn-default" data-toggle="modal" data-target="#myModal">
            Search in chat
        </span>

        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Search in chat</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label for="exampleInputEmail1">Keyword</label>
                                <input type="email" class="form-control" id="keyword" placeholder="Enter keyword">
                            </div> 
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Date from</label>
                                <input type="text" id="date-from" name="date_from" class="datepicker form-control" value="{if isset($date_from)}{$date_from|date_format:'d/m/Y'}{/if}" placeholder="Click here and select date">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Date to</label>
                                <input type="text" id="date-to" name="date_to" class="datepicker form-control" value="{if isset($date_to)}{$date_to|date_format:'d/m/Y'}{/if}"  placeholder="Click here and select date">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Sender</label>
                                <input type="text" id="sender-title" name="sender_title" class="form-control" value="{if isset($sender_title)}{$sender_title}{/if}" placeholder="Enter sender name">
                                <input type="hidden" id="sender-id" name="sender_id" class="normal" value="{if isset($sender_id)}{$sender_id}{/if}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1">Recipient</label>
                                <input type="text" id="recipient-title" name="recipient_title" class="form-control" value="{if isset($recipient_title)}{$recipient_title}{/if}"  placeholder="Enter recipient name">
                                <input type="hidden" id="recipient-id" name="recipient_id" class="normal" value="{if isset($recipient_id)}{$recipient_id}{/if}">
                            </div>
                        </div>    
                        <div class="row">
                            <div class="form-group col-md-12" style="margin-bottom: 0px; padding-bottom: 0px;">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="is-dialogue"> In both directions
                                    </label>
                                </div>
                                
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="is-phrase"> Any one word
                                    </label>
                                </div>                
                                
                            </div>      
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary find-messages">Search</button>
                        <button type="button" class="btn btn-success find-messages-in" style="display: none;">Search in found</button>
                        <input type="hidden" id="msg-ids" value="">
                    </div>
                </div>
            </div>
        </div>

    </div>
                            {*
    <div class="col-md-6"  style="padding-left: 0px; padding-right: 0px;">
        <div class="btn-group">                       
            <button type="button" class="btn btn-primary">Touchline</button>
            <button type="button" class="btn-primary dropdown-toggle btn" data-toggle="dropdown" style="height:34px;">
                <span class="caret"></span>
                <span class="sr-only">Chat navigate</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="/touchline/mustdo">MustDo!</a></li>
                {*<li><a href="/touchline/search">Search</a></li>*}
            {*</ul>
        </div>        
    </div> *}
</ul> 
<br>       
<div class="panel panel-default">
    <div class="panel-heading">Customers</div>
    <div class="panel-body" style="padding: 13px;">
        {include file='templates/html/chat/control_customers.tpl' readonly=true}
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-heading">Steelemotioners</div>
    <div class="panel-body" style="padding: 13px;">
        {include file='templates/html/chat/control_recipients.tpl' readonly=true}
    </div>
</div>
<span class="btn btn-primary btn-sm" data-toggle="modal" data-target="#filters">
    Last 20 biz
</span>


<input type="hidden" id="chat-object-alias" value="{if isset($chat_object_alias)}{$chat_object_alias}{/if}">
<input type="hidden" id="chat-object-id" value="{if isset($chat_object_id)}{$chat_object_id}{/if}">   

<div class="modal fade " id="filters" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form role="form" id="filters" action="" method="POST">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Last 20 biz's</h4>
                </div>
                <div class="modal-body">
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="filter-settings">
                            <div class="row">
                                <div class="col-md-12">
                                    {if !empty($bizes_list)}
                                        {$max_displayed_items=20}

                                        <div class="message-menu bizes-container">
                                            <div class="bc-list {if $bizes_list.count > $max_displayed_items}collapsed{else}expanded{/if}">
                                                {foreach $bizes_list.data as $row}
                                                    {if $chat_object_alias == 'biz' && $row.biz.id == $chat_object_id}
                                                        <div class="bc-list-row"><a href="/biz/{$row.biz.id}/touchline" title="{$row.biz.doc_no_full|escape:'html'}"><b>{$row.biz.doc_no_full|escape:'html'}</b></a></div>
                                                                {else}
                                                        <div class="bc-list-row"><a href="/biz/{$row.biz.id}/touchline" title="{$row.biz.doc_no_full|escape:'html'}">{$row.biz.doc_no_full|escape:'html'}</a></div>
                                                        {/if}
                                                    {/foreach}
                                            </div>
                                        </div>
                                        <div class="pad" style="height: 70px;"></div>
                                    {/if}  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </form>
</div>
