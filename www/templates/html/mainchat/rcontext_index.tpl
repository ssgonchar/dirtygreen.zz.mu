<!--<input type="button" class="btn150o" value="Write Message" onclick="show_chat_modal('chat', 0);"><div class="pad-10"></div>
<div id="chat-icon-park" onclick="show_chat_modal('chat', 0);">-->
<!-- DIV правой колонки с position: fixed -->
<div id="chat-icon-park"  data-spy="affix" data-offset-top="10" data-offset-bottom="50">
    {include file='templates/html/chat/control_recipients.tpl' readonly=true}
    
    <!--\/ Выводит Datepicker \/
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
                document.location.href = '/touchline/archive/' + dateString;
            }
        });
    });
    console.log("test");
    })(jQuery);
    </script>-->
    <!--/\ Выводит Datepicker /\-->
    <script>
        $(function() {
          $( "#datepicker" ).datepicker();
        });
    </script>
    <p>Date: <input type="text" id="datepicker"></p>
    
</div>
<input type="hidden" id="chat-object-alias" value="{if isset($chat_object_alias)}{$chat_object_alias}{/if}">
<input type="hidden" id="chat-object-id" value="{if isset($chat_object_id)}{$chat_object_id}{/if}">

