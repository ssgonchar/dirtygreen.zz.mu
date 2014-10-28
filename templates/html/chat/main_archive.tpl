<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12 col-md-12 main">
			<!--{*{if $smarty.session.user.id == '1671'}
                        <ol id="chat-messages" class="chat-messages search-target">
			{foreach from=$list item=row}
			{if $row.message.type_id != $smarty.const.MESSAGE_TYPE_AWAY && $row.message.type_id != $smarty.const.MESSAGE_TYPE_ONLINE}
				{include file='templates/html/chat/control_chat_message.tpl' message=$row}
			{/if}    
			{/foreach}
			</ol>
                        {else}*}-->
                 <ul class="nav nav-tabs">
                    <li class="active"><a href="#room-chat" data-toggle="tab">Chat</a></li>
                    <li><a href="#room-system" data-toggle="tab">System log</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="room-chat">
                              <ul id="chat-messages" class="timeline chat-messages search-target">
                                {foreach from=$list item=row}
                                    {if $row.message.type_id != $smarty.const.MESSAGE_TYPE_AWAY && $row.message.type_id != $smarty.const.MESSAGE_TYPE_ONLINE && $row.message.type_id != 5 && $row.message.type_id != 4}
                                            {include file='templates/html/chat/control_chat_messagemod.tpl' message=$row}
                                    {/if}                                      
                                <!--

                                -->
                                {/foreach}
                            </ul>                  
                    </div>
                    <div class="tab-pane" id="room-system">
                              <ul id="chat-messages" class="timeline chat-messages search-target">
                                {if empty($list)}
                                    {if isset($filter)}Nothing was found{/if}
                                {else}
                                    <ul id="chat-messages" class="timeline chat-messages search-target">
                                    {foreach from=$list item=row}
                                        {include file='templates/html/chat/control_chat_messagemod.tpl' message=$row}
                                    {/foreach}
                                    </ul>       
                                {/if}
                            </ul>                  
                    </div>
                </div>
                            

                            
                         <!--{*/if*}-->
			{if $page_no == 1}<div id="chat-updater" style="display: none;"></div>{/if}		
		</div>
	</div>
</div>


<script type="text/javascript">


(function($) {
$(document).ready(function(){
    $('.chat-archive-dateto').datepicker({
        maxDate     : '+0d',
        showWeek    : true,
        dateFormat  : 'yy-mm-dd',
        defaultDate : new Date($('.chat-archive-dateto').data('date')),
        //gotoCurrent : true,
        changeMonth : true,
        changeYear  : true,
        yearRange   : '-12',
        onSelect: function(dateString)
        {
            document.location.href = '/touchline/archive/' + dateString;
        }
    });
});

})(jQuery);
</script>