<table style="width: 100%; table-layout: fixed; display:none;">
    <tr>
        <td style="vertical-align: top; width: 32%; table-layout: fixed;">
            <div class="widget blue">
                <div class="widget-title">Old System Reference</div>
                <div class="widget-stat"></div>
                <ul class="widget-list">
                    <li><a href="http://mamtrix.steelemotion.com" target="_blank" title='test'>http://mamtrix.steelemotion.com</a></li>
                </ul>
            </div>        
            <div class="pad"></div>

            <div class="widget">
                <div class="widget-title">Favourite BIZs</div>1
                <div class="widget-stat">{if !empty($bizes_list)}{count($bizes_list)}{/if}</div>
                <ul class="widget-list">
                    {if !empty($bizes_list)}
                        {foreach $bizes_list as $row}
                            <li onclick="location.href = '/biz/{$row.biz.id}';">{$row.biz.doc_no_full|escape:'html'}</li>
                            {/foreach}
                        {else}
                        <li class="empty">There are no BIZs</li>
                        {/if}
                </ul>
            </div>        
        </td>
        <td style="width: 20px;"></td>
        <td style="width: 32%; vertical-align: top; white-space: nowrap;">
            <div class="widget">
                <div class="widget-title">Open Orders</div>
                <div class="widget-stat">{if !empty($orders_list.data)}{count($orders_list.data)}{/if}</div>
                <ul class="widget-list">
                    {if !empty($orders_list.data)}
                        {foreach $orders_list.data as $row}
                            <li onclick="location.href = '/order/{$row.order.id}';" class="tr-order-{$row.order.status}">{$row.order.doc_no_full|escape:'html'} {if isset($row.order.biz)}{$row.order.biz.doc_no}{/if}</li>
                            {/foreach}
                        {else}
                        <li class="empty">There are no orders</li>
                        {/if}
                </ul>
            </div>
        </td>
        <td style="width: 20px;"></td>
        <td style="width: 32%; vertical-align: top; white-space: nowrap;">
            <div class="widget">
                <div class="widget-title">E-mail inbox</div>
                <div class="widget-stat">{if !empty($emails_list.data)}{count($emails_list.data)}{/if}</div>
                <ul class="widget-list">
                    {if !empty($emails_list.data)}
                        {foreach $emails_list.data as $row}
                            <li onclick="location.href = '/email/{$row.email.id}';">
                                {$row.email.sender_address|replace:'<':'&lt;'|replace:'>':'&gt;'} {$row.email.title}
                            </li>
                        {/foreach}
                    {else}
                        <li class="empty">There are no E-mails</li>
                        {/if}
                </ul>
            </div>
        </td>
    </tr>
</table>
<div class="container">
    <div class="row">
        <!--
        <div class="col-md-2">
            <a href="http://mamtrix.steelemotion.com" target="_blank" title="test" class="btn btn-default pull-left">Old System</a>

        </div>
        -->
        	
        <div class="col-md-12">
        <div class="panel panel-default">
        <!-- Default panel contents -->
        <div class="panel-heading"><h5 style="display:inline;"><b>Must do!</b></h5> <span class="badge pull-right">{if !empty($pendings_list.data)}{count($pendings_list.data)}{/if}</span></div>

        <!-- List group -->
        <div style="max-height: 300px; overflow-y:scroll;">
        <ol id="chat-messages" class="chat-messages"{if empty($pendings_list.data)} style="display: none;"{/if}>
        {foreach from=$pendings_list.data item=row}
        {include file='templates/html/chat/control_chat_message.tpl' message=$row itemid="chat-pending-{$row.message.id}"}
        {/foreach}
        </ol>
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
</div>
