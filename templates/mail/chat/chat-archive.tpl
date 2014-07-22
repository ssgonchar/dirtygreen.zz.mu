<h3>Chat archive on {$mail.date_to}</h3>
<p><a href="http://home.steelemotion.com/touchline/archive/{$mail.date_to}">on-line version archive &rArr;</a></p>
<p><a href="http://home.steelemotion.com/touchline">on-line chat today &rArr;</a></p>
<hr/>
<table>
	{foreach from=$mail.data item=message}				
	<tr style="vertical-align:top; color: {if $message.message.type_id == $smarty.const.MESSAGE_TYPE_ORDER}red{else}{$message.message.sender.color}{/if};">
		<td style="width:50px;">
			{if $message.message.sender_id == $smarty.const.GNOME_USER}
				<img src="/img/layout/gnome.jpg" alt="Gnome" alt="Gnome">
			{elseif isset($message.message.sender) && isset($message.message.sender.person)}
				{if isset($message.message.sender.person.picture)}{picture type="person" size="x" source=$message.message.sender.person.picture}
				{elseif $message.message.sender.person.gender == 'f'}<img src="http://home.steelemotion.com/img/layout/anonymf.png" alt="{$message.message.sender.login}" alt="{$message.message.sender.login}">
				{else}<img src="http://home.steelemotion.com/img/layout/anonym.png" alt="{$message.message.sender.login}" alt="{$message.message.sender.login}">{/if}
			{else}
				<img src="http://home.steelemotion.com/img/layout/anonym.png" alt="No Picture" alt="No Picture">
			{/if}							
		</td>
		<td style="vertical-align:top;">
			<i>at {$message.message.created_at|date_format:'H:i:s'}</i><br/>
			{if $message.message.type_id == $smarty.const.MESSAGE_TYPE_NORMAL || $message.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $message.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}
				{if $message.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $message.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}<i>(p)</i>&nbsp;{/if}{$message.message.sender.login}&nbsp;&rarr;&nbsp;{if !isset($message.message.recipient) || empty($message.message.recipient)}MaM{else}{foreach from=$message.message.recipient item=r name=r}{$r.user.login}{if !$smarty.foreach.r.last}/{/if}{/foreach}{if !empty($message.message.cc)}.cc.{foreach from=$message.message.cc item=c name=c}{$c.user.login}{if !$smarty.foreach.c.last}/{/if}{/foreach}{/if}{/if}
				<br><b>{$message.message.title|parse|highlight:$keyword:$is_phrase}</b>
			{elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_SERVICE}
				{if $message.message.type_id == $smarty.const.MESSAGE_TYPE_PRIVATE || $message.message.type_id == $smarty.const.MESSAGE_TYPE_PERSONAL}<i>(p)</i>&nbsp;{/if}{$message.message.sender.login}&nbsp;&rarr;&nbsp;{if !isset($message.message.recipient) || empty($message.message.recipient)}MaM{else}{foreach from=$message.message.recipient item=r name=r}{$r.user.login}{if !$smarty.foreach.r.last}/{/if}{/foreach}{if !empty($message.message.cc)}.cc.{foreach from=$message.message.cc item=c name=c}{$c.user.login}{if !$smarty.foreach.c.last}/{/if}{/foreach}{/if}{/if}
				<br><b>{$message.message.title|parse|highlight:$keyword:$is_phrase}</b>            
			{elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN}
				<b>{$message.message.sender.login} logged IN {$message.message.title}</b>
			{elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN_FAILED}
				<span style="color: red;">{$message.message.sender.login}&nbsp;&rarr;&nbsp;MaM
				<br><b>{$message.message.title}</b></span>
			{elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_ONLINE}
				<b>{$message.message.sender.login} is online</b>
			{elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGOUT}
				<b>{$message.message.sender.login} logged OUT {if $message.message.title != 'I left .'}{$message.message.title}{/if}</b>
			{elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_AWAY}
				<b>{$message.message.sender.login} is idle</b>
			{elseif $message.message.type_id == $smarty.const.MESSAGE_TYPE_ORDER}
				<b>{$message.message.title}</b>
			{/if}	
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
			<br/>
			<p>
				{if $message.message.type_id == $smarty.const.MESSAGE_TYPE_LOGIN_FAILED}
				<span style="color:red;">{$message.message.description|parse|highlight:$keyword:$is_phrase|nl2br}</span>
				{else}
				{$message.message.description|parse|highlight:$keyword:$is_phrase}</b></i></a>{* trick for closing unclosed <b>, <i> & <a> tags*}
				{/if}							
			</p>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>
				{if isset($message.message.attachments) && !empty($message.message.attachments)}
			<div class="chat-message-attachments">
				{foreach from=$message.message.attachments item=row}
					{include file='templates/html/dropbox/control_attachment_block_text.tpl' attachment=$row.attachment readonly=true}        
				{/foreach}
			</div>
			{/if}						
		</td>
	</tr>
	<tr>
		<td colspan="2"><hr/></td>
	</tr>
    {/foreach}					
				</table>

