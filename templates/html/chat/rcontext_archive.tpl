<!--<input type="button" class="btn150o" value="Write Message" onclick="show_chat_modal('chat', 0);"><div class="pad-10"></div>
<div id="chat-icon-park" onclick="show_chat_modal('chat', 0);">-->
<div  class="">
    <div class="btn-group">
        <a href="/touchline" class="btn btn-primary btn-xs">TouchLine</a>
        <a href="/touchline/mustdo" class="btn btn-primary btn-xs">MustDo!</a>
        <a href="/touchline/search" class="btn btn-primary btn-xs">Search</a>
    </div>
    <br/><br/>
    
    <div class="panel-group" id="chat-toolbox" >
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="" href="#collapseCustomers">
                Customers
              </a>
            </h4>
    </div>
    <div id="collapseCustomers" class="panel-collapse collapse in">
      <div class="panel-body">
        <div id="chat-icon-park-customers">
            {include file='templates/html/chat/control_customers.tpl' readonly=true}
        </div>
      </div>
    </div>
  </div>
        
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="" href="#collapseUsers">
                STEELemotioners
              </a>
            </h4>
    </div>
    <div id="collapseUsers" class="panel-collapse collapse in">
      <div class="panel-body">
        <div id="chat-icon-park" >
            {include file='templates/html/chat/control_recipients.tpl' readonly=true}
        </div>
      </div>
    </div>
  </div>        
     <!--   
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="#chat-toolbox" href="#collapseOne">
                View settings
              </a>
            </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse">
      <div class="panel-body">
        <input type="checkbox" onClick="hide_message_by_text('login')"> Hide login messages
        <br/>
        <input type='checkbox'> Hide logout messages
        <br/><br/>
      </div>
    </div>
  </div>-->
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
              <a data-toggle="collapse" data-parent="" href="#collapseThree">
                View by Date
              </a>
            </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
        <div class="chat-archive-dateto" data-date="{$date_to|date_format:'%Y-%m-%d'}"{*style="margin: 0 40%;"*}></div>
        
        </div>
  </div>
</div>
</div>
<input type="hidden" id="chat-object-alias" value="{if isset($chat_object_alias)}{$chat_object_alias}{/if}">
<input type="hidden" id="chat-object-id" value="{if isset($chat_object_id)}{$chat_object_id}{/if}">