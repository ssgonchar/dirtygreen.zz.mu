                    
<!-- DASHBOARD.TOUCH LINE -->                   
<div class="container">
    <div class="row">
        <span class="glyphicon glyphicon-comment h3"></span><span class="h2">&nbsp;Touchline</span>
    </div>
    {*debug*}
    <div class="qa-message-list" id="wallmessages">
        {foreach from=$pendings_list.data item=row}
            <div class="message-item" id="m16">
                <div class="message-inner">
                    <div class="message-head clearfix">
                        <div class="avatar pull-left">
                            {if $row.message.sender_id == $smarty.const.GNOME_USER}
                                <img src="/img/layout/gnome.jpg" alt="Gnome" alt="System">
                            {elseif isset($row.message.sender) && isset($row.message.sender.person)}
                                {if isset($row.message.sender.person.picture)}{picture type="person" size="x" source=$row.message.sender.person.picture}
                                {elseif $row.message.sender.person.gender == 'f'}<img src="/img/layout/anonymf.png" alt="{$row.message.sender.login}" alt="{$row.message.sender.login}">
                                {else}<img src="/img/layout/anonym.png" alt="{$row.message.sender.login}" alt="{$row.message.sender.login}">{/if}
                            {else}
                                <img src="/img/layout/anonym.png" alt="No Picture" alt="No Picture">
                            {/if}                                                                
                        </div>

                        <div class="user-detail">
                            <h5 class="handle">
                                {if $row.message.type_id == $smarty.const.MESSAGE_TYPE_NORMAL || $row.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $row.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}
                                    <b>{$row.message.title|parse:7:'_blank'|highlight:$keyword:$is_phrase}</b>
                                {elseif $row.message.type_id == $smarty.const.MESSAGE_TYPE_SERVICE}
                                    {if $row.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $row.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}
                                        <i>(p)</i>&nbsp;
                                    {/if}
                                    {$row.message.sender.login}&nbsp;&rarr;&nbsp;
                                    {if !isset($row.message.recipient) || empty($row.message.recipient)}
                                        System
                                    {else}
                                        {foreach from=$row.message.recipient item=r name=r}
                                            {$r.user.login}
                                            {if !$smarty.foreach.r.last}
                                                /
                                            {/if}
                                        {/foreach}
                                        {if !empty($row.message.cc)}
                                            .cc.
                                            {foreach from=$row.message.cc item=c name=c}
                                                {$c.user.login}
                                                {if !$smarty.foreach.c.last}
                                                    /
                                                {/if}
                                            {/foreach}
                                        {/if}
                                    {/if}
                                    <br/>
                                    <b>{$row.message.title|parse|highlight:$keyword:$is_phrase}</b>            
                                {elseif $row.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN}
                                    <b>{$row.message.sender.login} logged IN {$row.message.title}</b>
                                {elseif $row.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN_FAILED}
                                    <span style="color: red;">{$row.message.sender.login}&nbsp;&rarr;&nbsp;MaM
                                        <br><b>{$row.message.title}</b></span>
                                    {elseif $row.message.type_id == $smarty.const.MESSAGE_TYPE_ONLINE}
                                    <b>{$row.message.sender.login} is online</b>
                                {elseif $row.message.type_id == $smarty.const.MESSAGE_TYPE_LOGOUT}
                                    <b>{$row.message.sender.login} logged OUT {if $row.message.title != 'I left .'}{$row.message.title}{/if}</b>
                                {elseif $row.message.type_id == $smarty.const.MESSAGE_TYPE_AWAY}
                                    <b>{$row.message.sender.login} is idle</b>
                                {elseif $row.message.type_id == $smarty.const.MESSAGE_TYPE_ORDER}
                                    <b>{$row.message.title}</b>
                                {/if}            
                                {if $smarty.session.user.role_id <= $smarty.const.ROLE_STAFF && !empty($row.message.is_pending) && isset($row.message.is_pending_recipient) && (empty($row.message.userdata) || empty($row.message.userdata.done_at))}
                                    <div id="message-{$row.message.id}-pending" class="panding-label" style="position: absolute; top: 0; right: 0; cursor: pointer;" onclick="mark_message_as_done({$row.message.id});">
                                        {if !empty($row.message.deadline)}deadline : {$row.message.deadline|date_format:'d/m/Y'}{else}MustDO !{/if}
                                    </div>
                                {/if}
                            </h5>

                            <div class="post-meta">
                                <div class="asker-meta">
                                    <span class="qa-message-what"></span>

                                    <span class="qa-message-who">
                                        <span class="qa-message-who-pad">by </span>
                                        <span class="qa-message-who-data">
                                            <i>
                                                {if $row.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $row.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}
                                                    <span class="label label-warning">(p)</span>&nbsp;
                                                {/if}
                                                {$row.message.sender.login} &nbsp;<span class="glyphicon glyphicon-arrow-right"></span>&nbsp;                                                                            
                                            </i>                                                                                                    
                                        </span>
                                    </span>
                                    <span class="qa-message-who">
                                        <span class="qa-message-who-pad">to </span>
                                        <span class="qa-message-who-data">
                                            <i>

                                                {if $row.message.type_id == $smarty.const.MESSAGE_TYPE_NORMAL || $row.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $row.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}
                                                    {if !isset($row.message.recipient) || empty($row.message.recipient)}
                                                        All
                                                    {else}
                                                        {foreach from=$row.message.recipient item=r name=r}
                                                            {$r.user.login}
                                                            {if !$smarty.foreach.r.last}
                                                                /
                                                            {/if}
                                                        {/foreach}
                                                        {if !empty($row.message.cc)}
                                                            .cc.
                                                            {foreach from=$row.message.cc item=c name=c}
                                                                {$c.user.login}
                                                                {if !$smarty.foreach.c.last}
                                                                    /
                                                                {/if}
                                                            {/foreach}
                                                        {/if}
                                                    {/if}
                                                    <!--<br/>
                                                    <b>{$row.message.title|parse|highlight:$keyword:$is_phrase}</b>
                                                    -->
                                                {/if}                                                                          
                                            </i>
                                            <div style="text-align: left;">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                                <span class="qa-message-when">
                                                    <span class="qa-message-when-data">Jan 21</span>
                                                </span>
                                            </div>

                                            </div>
                                            </div>
                                            </div>
                                            </div>
                                            <div class="qa-message-content">
                                                {if $row.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN_FAILED}
                                                    <span style="color:red;">{$row.message.description|parse|highlight:$keyword:$is_phrase|nl2br}</span>
                                                {else}

                                                    {$row.message.description|parse:'7':'_blank'|highlight:$keyword:$is_phrase}trim:300}</b></i></a>{* trick for closing unclosed <b>, <i> & <a> tags*}
                                                {/if}
                                            </div>
                                            </div></div>                                
                                        {/foreach}
                                        </div>
                                        </div>