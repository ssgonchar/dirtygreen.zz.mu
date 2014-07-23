<div class="pad1"></div>

{if empty($list)}
    {if isset($filter)}Nothing was found{/if}
{else}
    <ol id="chat-messages" class="chat-messages search-target">
    {foreach from=$list item=row}
        {include file='templates/html/chat/control_chat_message.tpl' message=$row}
    {/foreach}
    </ol>
{/if}
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