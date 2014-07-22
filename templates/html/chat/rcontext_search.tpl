<ul class="nav nav-sidebar">
    <div class="col-md-6" style="padding-left: 0px;">
        <!-- Split button -->
        <div class="input-group input-group-sm">
            <input title="Search message in archive" type="text" readonly class="chat-archive-dateto form-control"  data-date="{$date_to|date_format:'%Y-%m-%d'}" style="cursor: pointer" placeholder="Select date">         
            <span class="input-group-btn input-group-sm">
                <button class="btn btn-primary search-chat-archive"><i class="glyphicon glyphicon-search"></i></button>
            </span> 
        </div> 
    </div>
    <div class="col-md-6"  style="padding-left: 0px; padding-right: 0px;">
        <div class="btn-group btn-group-sm">                       
            <button type="button" class="btn btn-primary">Search</button>
            <button type="button" class="btn btn-primary dropdown-toggle btn-sm" data-toggle="dropdown" style="height: 30px;">
                <span class="caret"></span>
                <span class="sr-only">Chat navigate</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="/touchline">TouchLine</a></li>
                <li><a href="/touchline/mustdo">MustDo!</a></li>
            </ul>
        </div>        
    </div> 
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
<input type="hidden" id="chat-object-alias" value="{if isset($chat_object_alias)}{$chat_object_alias}{/if}">
<input type="hidden" id="chat-object-id" value="{if isset($chat_object_id)}{$chat_object_id}{/if}">    
