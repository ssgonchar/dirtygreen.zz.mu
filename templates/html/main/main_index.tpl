<div class="row">
    <div class="col-md-12">
    <!-- Nav tabs -->
    <ul class="nav nav-tabs fixed" style="top:50px; background: #fff; height: 60px; width: 100%; padding-top: 10px;">
        <li class="active"><a href="#open-orders" data-toggle="tab">Open Orders</a></li>
        <li><a href="#must-do" data-toggle="tab">Must Do!</a></li>        
        <li><a href="#email-inbox" data-toggle="tab">E-mail inbox</a></li>
    </ul>
</div>
    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="open-orders">
            <br>
            <div class="col-md-12">
                <ul class="list-group">
                    {if !empty($orders_list.data)}
                        {foreach $orders_list.data as $row}
                            <li onclick="location.href = '/order/{$row.order.id}';" class="tr-order-{$row.order.status} list-group-item">{$row.order.doc_no_full|escape:'html'} {if isset($row.order.biz)}{$row.order.biz.doc_no}{/if}</li>
                            {/foreach}
                        {else}
                        <li class="list-group-item">There are no orders</li>
                        {/if}
                </ul>
            </div>        
        </div>
        <div class="tab-pane" id="must-do">
            <div class="col-md-12">
                <ul id="chat-messages" class="timeline chat-messages search-target" {if empty($pendings_list.data)} style="display: none;"{/if}>
                    {foreach from=$pendings_list.data item=row}
                        {if $row.message.type_id != $smarty.const.MESSAGE_TYPE_AWAY && $row.message.type_id != $smarty.const.MESSAGE_TYPE_ONLINE}
                            {include file='templates/html/chat/control_chat_messagemod.tpl' message=$row}
                        {/if}                                      
                        <!--

                        -->
                    {/foreach}
                </ul>        

                <div id="chat-no-pendings" {if !empty($pendings_list.data)} style="display: none;"{/if}>There are no MustDO messages.</div>
            </div>        
        </div>
        <div class="tab-pane" id="email-inbox">
            <br>
            <ul class="list-group">
                {if !empty($emails_list.data)}
                    {foreach $emails_list.data as $row}
                        <li onclick="location.href = '/email/{$row.email.id}';" class="list-group-item">
                            <span class="badge" style="">
                                {if empty($row.email.is_today)}
                                    <i><b>{$row.email.date_mail|date_format:'d/m/Y'}&nbsp;{$row.email.date_mail|date_format:'H:i:s'}</b></i>
                                {else}
                                    <i><b>{$row.email.date_mail|date_format:'H:i:s'}</b></i>
                                {/if}  
                            </span>					
                            <i>{$row.email.sender_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</i><p><b>{$row.email.title}</b></p>
                        </li>
                    {/foreach}
                {else}
                    <li class="list-group-item">There are no E-mails</li>
                    {/if}
            </ul>        
        </div>
    </div>    
</div>
{*
<div class="row">        	
    <div class="col-md-6">
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading"><h5 style="display:inline;"><b>Must do!</b></h5> <span class="badge pull-right">{if !empty($pendings_list.data)}{count($pendings_list.data)}{/if}</span></div>

            <!-- List group -->
            <div style="max-height: 300px; overflow-y:scroll;">

                <ul id="chat-messages" class="timeline chat-messages search-target" {if empty($pendings_list.data)} style="display: none;"{/if}>
                    {foreach from=$pendings_list.data item=row}
                        {if $row.message.type_id != $smarty.const.MESSAGE_TYPE_AWAY && $row.message.type_id != $smarty.const.MESSAGE_TYPE_ONLINE}
                            {include file='templates/html/chat/control_chat_messagemod.tpl' message=$row}
                        {/if}                                      
                        <!--

                        -->
                    {/foreach}
                </ul>        

                <div id="chat-no-pendings" {if !empty($pendings_list.data)} style="display: none;"{/if}>There are no MustDO messages.</div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading"><h5 style="display:inline;"><b>Open orders</b></h5> <span class="badge pull-right">{if !empty($orders_list.data)}{count($orders_list.data)}{/if}</span></div>

            <!-- List group -->
            <div style="max-height: 300px; overflow-y:scroll;">
                <ul class="list-group">
                    {if !empty($orders_list.data)}
                        {foreach $orders_list.data as $row}
                            <li onclick="location.href = '/order/{$row.order.id}';" class="tr-order-{$row.order.status} list-group-item">{$row.order.doc_no_full|escape:'html'} {if isset($row.order.biz)}{$row.order.biz.doc_no}{/if}</li>
                            {/foreach}
                        {else}
                        <li class="list-group-item">There are no orders</li>
                        {/if}
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default" >
            <!-- Default panel contents -->
            <div class="panel-heading"><h5 style="display:inline;"><b>E-mail inbox</b></h5> <span class="badge pull-right">{if !empty($emails_list.data)}{count($emails_list.data)}{/if}</span></div>

            <!-- List group -->
            <div style="max-height: 300px; overflow-y:scroll;">
                <ul class="list-group">
                    {if !empty($emails_list.data)}
                        {foreach $emails_list.data as $row}
                            <li onclick="location.href = '/email/{$row.email.id}';" class="list-group-item">
                                <span class="badge" style="">
                                    {if empty($row.email.is_today)}
                                        <i><b>{$row.email.date_mail|date_format:'d/m/Y'}&nbsp;{$row.email.date_mail|date_format:'H:i:s'}</b></i>
                                    {else}
                                        <i><b>{$row.email.date_mail|date_format:'H:i:s'}</b></i>
                                    {/if}  
                                </span>					
                                <i>{$row.email.sender_address|replace:'<':'&lt;'|replace:'>':'&gt;'}</i><p><b>{$row.email.title}</b></p>
                            </li>
                        {/foreach}
                    {else}
                        <li class="list-group-item">There are no E-mails</li>
                        {/if}
                </ul>
            </div>
        </div>
    </div>
</div>

*}