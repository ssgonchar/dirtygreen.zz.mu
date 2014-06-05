<div class="row">
     <div class="col-md-12" style="text-align: center;">
        <br/>
        <div class="btn-group">
            <a href="/touchline" class="btn btn-primary btn-xs active">TouchLine</a>
            <a href="/touchline/mustdo" class="btn btn-primary btn-xs">MustDo!</a>
            <a href="/touchline/search" class="btn btn-primary btn-xs">Search</a>
        </div>
        <hr>
    </div>
</div>       
<div class="row">
     <div class="col-md-12" style="text-align: center;">
        <h4>Customers</h4>
        <div class="col-md-12">{include file='templates/html/chat/control_customers.tpl' readonly=true}</div>
    </div>
</div>
<hr>    
<div class="row">
     <div class="col-md-12" style="text-align: center;">    
        <h4>Steelemotioners</h4>
        <div class="col-md-12">{include file='templates/html/chat/control_recipients.tpl' readonly=true}</div>
    </div>
</div>  
<hr>         
<div class="row box-shadow">
     <div class="col-md-12" style="text-align: center;">         
        <h4>View by Date</h4>
        <div class="chat-archive-dateto" data-date="{$date_to|date_format:'%Y-%m-%d'}" style="margin: 0px; margin-right: 0px; display: inline-block;"></div>   
        <br/>
     </div>
</div> 
<input type="hidden" id="chat-object-alias" value="{if isset($chat_object_alias)}{$chat_object_alias}{/if}">
<input type="hidden" id="chat-object-id" value="{if isset($chat_object_id)}{$chat_object_id}{/if}">     