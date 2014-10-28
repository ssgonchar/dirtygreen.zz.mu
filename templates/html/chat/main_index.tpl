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
            <ul id='myTab' class="nav nav-tabs room-navigatin">
                <li class="active"><a href="#room-chat" data-toggle="tab">Chat</a></li>
                <li><a href="#room-system" data-toggle="tab">System log</a></li>
                <li style='display:none;' class='search-result-tab'><a href="#room-search-place" data-toggle="tab" >Search results</a></li>
                {*<li style='vertical-align: middle'><!-- Button trigger modal -->
                    <span class="btn btn-success btn-xs" data-toggle="modal" data-target="#add-room">
                        <i class="glyphicon glyphicon-plus"></i> Add room
                    </span></li> *}

                <!-- Modal -->
                <div class="modal fade" id="add-room" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel">Add room</h4>
                            </div>
                            <div class="modal-body">
                                <form role="form" id='add-room-form'>
                                    <div class="form-group">
                                        <label for="add-room-biz-title">BIZ</label>
                                        <input type="text" class="form-control" id="add-room-biz-title" placeholder="Start type and select" data-biz-id='0'>
                                    </div>
                                    <div class="form-group">
                                        <label for="add-room-biz-title">Room title</label>
                                        <input type="text" class="form-control" id="add-room-title" placeholder="Free text">
                                    </div>
                                    <div class="form-group">
                                        <label for="add-room-users">Users</label>
                                        <div class=" row">
                                        <div class="add-room-switch-users col-md-12">
                                        {foreach from=$users.staff item=recipient}
                                        {if isset($recipient)}
                                            <div style='float: left; margin-right: 5px; margin-bottom: 5px; cursor: pointer;' class='chat-room-user'>
                                            {if !isset($readonly) || !$readonly}
                                                {if isset($recipient.user.person)} 
                                                    {if isset($recipient.user.person.picture)}
                                                        {picture type="person" size="x" source=$recipient.user.person.picture id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}"}
                                                    {elseif $recipient.user.person.gender == 'f'}
                                                        <img src="/img/layout/anonymf.png" id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}" alt="No Picture" alt="No Picture">
                                                    {else}
                                                        <img src="/img/layout/anonym.png" id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}" alt="No Picture" alt="No Picture">
                                                    {/if}
                                                {else}
                                                    <img src="/img/layout/anonym.png" id="{$recipient.user.id}-user-picture" class="chat-user-{$recipient.chat_status}" alt="No Picture" alt="No Picture">
                                                {/if}
                                                <br>
                                                <span style="color: {$recipient.user.color};">{$recipient.user.login}</span>                                                
                                            {/if}
                                            </div>
                                        {/if}
                                        {/foreach}
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="">Added users</label>
                                        <div class=" row">
                                        <div class="add-room-added-users col-md-12">
                                        </div>
                                        </div>
                                    </div>                                        
                                    {*
                                    <div class="form-group">
                                    <label for="exampleInputPassword1">Пароль</label>
                                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                                    </div>
                                    <div class="form-group">
                                    <label for="exampleInputFile">File input</label>
                                    <input type="file" id="exampleInputFile">
                                    <p class="help-block">Example block-level help text here.</p>
                                    </div>
                                    <div class="checkbox">
                                    <label>
                                    <input type="checkbox"> Проверить меня
                                    </label>
                                    </div>
                                    <button type="submit" class="btn btn-default">Отправить</button>]
                                    *} 
                                </form>                                
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary add-room-submit">Add</button>
                            </div>
                        </div>
                    </div>
                </div>

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
                        {foreach from=$list item=row}
                            {if $row.message.type_id != $smarty.const.MESSAGE_TYPE_AWAY && $row.message.type_id != $smarty.const.MESSAGE_TYPE_ONLINE && ($row.message.type_id == 5 || $row.message.type_id == 4)}
                                {include file='templates/html/chat/control_chat_messagemod.tpl' message=$row}
                            {/if}                                      
                            <!--

                            -->
                        {/foreach}
                    </ul>                  
                </div>
                <div class="tab-pane" id="room-search-place">
                    <div class="row" id="room-search">
                    <ul id="chat-messages" class="timeline chat-messages search-target">
                        {foreach from=$list item=row}
                            {if $row.message.type_id != $smarty.const.MESSAGE_TYPE_AWAY && $row.message.type_id != $smarty.const.MESSAGE_TYPE_ONLINE && ($row.message.type_id == 5 || $row.message.type_id == 4)}
                                {include file='templates/html/chat/control_chat_messagemod.tpl' message=$row}
                            {/if}                                      
                            <!--

                            -->
                        {/foreach}
                        
                    </ul> 
                    </div>
                        <div class="row">
                            <span class="btn btn-default chat-search-show-more" data-start="0" style="display: none;">Show more</span>
                        </div>
                </div>
            </div>



            <!--{*/if*}-->
            {if $page_no == 1}<div id="chat-updater" style="display: none;"></div>{/if}		
        </div>
    </div>
</div>
