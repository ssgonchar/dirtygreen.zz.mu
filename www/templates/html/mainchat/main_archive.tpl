<div class="chat-archive-dateto" data-date="{$date_to|date_format:'%Y-%m-%d'}"{*style="margin: 0 40%;"*}></div>
<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

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
            document.location.href = '/mainchat/archive/' + dateString;
        }
    });
});
console.log("test");
})(jQuery);
</script>

<!--
{if empty($list)}
    {if isset($filter)}Nothing was found{/if}
{else}
    <ol id="chat-messages" class="chat-messages">
    {foreach from=$list item=row}
        {include file='templates/html/chat/control_chat_message.tpl' message=$row}
    {/foreach}
    </ol>
{/if} -->

<!--\/ Выводит список сообщений index страницы\/ -->
<div class="container-fluid">
    <div class="row">
    	<div class="col-sm-12 col-md-12 main">
    	    <ol id="chat-messages" class="chat-messages">
                {foreach from=$list item=row}
                    {include file='templates/html/chat/control_chat_message.tpl' message=$row}
                {/foreach}
            </ol>
        </div>
    </div>
</div>
<!--/\ Выводит список сообщений index страницы /\-->
