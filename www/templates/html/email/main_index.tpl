
<table class="form" width="100%" >
    <tr>
        <td>
	    <div class="input-group">
		<input type="text" name="form[keyword]" class="form-control"{if isset($keyword)} value="{$keyword}"{/if}>
		<span class="input-group-btn">
		<input type="submit" name="btn_find" value="Find" class="btn btn-primary" style="font-weight:bold;">
		{if isset($keyword) && !empty($keyword)}
		    {if $is_dfa}
			<a class="btn btn-default" style="color:#333; font-weight:bold;" href="/emails/dfa">Back to view</a>
		    {elseif $is_dfa_other}
			<a class="btn btn-default" style="color:#333; font-weight:bold;" href="/emails/dfa/other">Back to view</a>
		    {elseif $page == 'deleted_by_user'}
			<a class="btn btn-default" style="color:#333; font-weight:bold;" href="/emails/deleted">Back to view</a>
		    {else}
			<a class="btn btn-default" style="color:#333; font-weight:bold;" href="/emails{if isset($type_id) || (isset($mailbox_id) && $mailbox_id > 0)}/filter/{/if}{if isset($type_id)}type:{$type_id};{/if}{if isset($mailbox_id) && $mailbox_id > 0}mailbox:{$mailbox_id};{/if}">Back to view</a>
		    {/if} 				
		{/if}
		</span>
	    </div>
	</td>
        <td width="20px"></td>
	{if isset($keyword) && !empty($keyword)}
	    <td width="100px" style="display:none;">
		{if $is_dfa}
		    <a class="btn btn-default" style="color:#333; font-weight:bold;" href="/emails/dfa">Back to view</a>
		{elseif $is_dfa_other}
		    <a class="btn btn-default" style="color:#333; font-weight:bold;" href="/emails/dfa/other">Back to view</a>
		{elseif $page == 'deleted_by_user'}
		    <a class="btn btn-default" style="color:#333; font-weight:bold;" href="/emails/deleted">Back to view</a>
		{else}
		    <a class="btn btn-default" style="color:#333; font-weight:bold;" href="/emails{if isset($type_id) || (isset($mailbox_id) && $mailbox_id > 0)}/filter/{/if}{if isset($type_id)}type:{$type_id};{/if}{if isset($mailbox_id) && $mailbox_id > 0}mailbox:{$mailbox_id};{/if}">Back to view</a>
		{/if}     
	    </td>    
	{/if}	   
    </tr>
    
    <tr>
	<td>

	    <div class="btn-group">
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
		    current partition:&nbsp;
		    <strong>
		    {if $type_id == 0 && $page_alias !== "email_main_deletedbyuser"}
			All emails
		    {elseif $type_id  == 1}
			Inbox emails
		    {elseif $type_id == 2}
			Sent emails
		    {elseif $type_id==3 && $pager_path=='/emails/dfa/other'}
			All other drafts
		    {elseif $type_id==3 && $pager_path=='/emails/dfa'}
			Drafts await confirmation		
		    {elseif $type_id==5}
			Spam emails
		    {elseif $type_id==4}
			Corrupted emails	
		    {elseif $page_alias == "email_main_deletedbyuser"}
			Trash
		    {/if}
		    </strong>&nbsp;
		    <span class="caret"></span>
		</button>
		<ul class="dropdown-menu" role="menu">
		  {if $type_id == 0 && $page_alias !== "email_main_deletedbyuser"}
		  {else}
		  <li><a class="i-email-all-normal" href="/emails/filter/type:0">All emails</a></li>
		  {/if}
		  
		  {if $type_id  == 1}
		  {else}
		  <li><a class="i-email-inbox" href="/emails/filter/type:1">Inbox emails</a></li>
		  {/if}
		  
		  {if $type_id == 2}
		  {else}
		  <li><a class="i-email-outbox" href="/emails/filter/type:2">Sent emails</a></li>
		  {/if}
		  
		  {if $type_id==3 && $pager_path=='/emails/dfa'}
		  {else}
		  <li><a class="i-email-dfa" href="/emails/dfa">Drafts await confirmation</a></li>
		  {/if}
		  
		  {if $type_id==3 && $pager_path=='/emails/dfa/other'}
		  {else}
		  <li><a class="i-email-dfa other" href="/emails/dfa/other">All other drafts</a></li>
		  {/if}
		  
		  {if $type_id==4}
		  {else}
		  <li><a class="i-email-error" href="/emails/filter/type:4">Corrupted emails</a></li>
		  {/if}
		  
		  {if $type_id==5}
		  {else}
		  <li><a class="i-email-spam" href="/emails/filter/type:5">Spam emails</a></li>
		  {/if}
		  
		  {if $page_alias == "email_main_deletedbyuser"}
		  {else}
		  <li><a class="i-email-deleted" href="/emails/deleted">Trash</a></li>
		    {/if}
		</ul>
		
	    </div>
	    <div class="btn-group">
		{if $mailbox_id<1}
		<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
		    current mailbox:&nbsp;
		    <strong>
			All boxes
		    </strong>&nbsp;
		    <span class="caret"></span>
		</button>
		{else}
		    {foreach from=$mailboxes item=row}
			{if $mailbox_id>0 && $mailbox_id == $row.mailbox_id}
			    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				current mailbox:&nbsp;
				<strong>
				    {$row.mailbox.title|escape:'html'}
				</strong>&nbsp;
				<span class="caret"></span>
			    </button>		    
			{/if}
		    {/foreach}
		{/if}
		<ul class="dropdown-menu" role="menu" style="z-index:9999999;">
		    {foreach from=$mailboxes item=row}
			{if $mailbox_id !== $row.mailbox_id}
			    <li>
				<a href="/emails/filter/mailbox:{$row.mailbox_id}{if $type_id == $smarty.const.EMAIL_TYPE_ERROR || $page == 'deleted_by_user' || $is_dfa};type:{$smarty.const.EMAIL_TYPE_INBOX}{/if}
				    {if isset($type_id) && $type_id > 0 && in_array($type_id, array($smarty.const.EMAIL_TYPE_INBOX, $smarty.const.EMAIL_TYPE_OUTBOX, $smarty.const.EMAIL_TYPE_SPAM))};type:{$type_id}{/if}{if !empty($keyword_md5)};keyword:{$keyword_md5}{/if}">
				    {$row.mailbox.title|escape:'html'}
				</a>
			    </li>			    
			{/if}
		    {/foreach} 
		</ul>	    
	    </div>
    <br/><br/>
    <div style="color:#333;" class="btn btn-default">
        <a class="i-funnel" style="color:#333;"  href="/email/filters"><strong>Create filter</strong></a>
    </div>
    <br/><br/>
    <div class="text-left">
    <input type="button" name="btn_compose" class="btn btn-primary" value="Compose New eMail" style="margin-top: 4px;" onclick="location.href='{if !empty($object_alias) && !empty($object_alias)}/{$object_alias}/{$object_id}{/if}/email/compose';">
</div>
	</td>
	<td>
	    
	</td>
    </tr>
</table>

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>
{if empty($list)}
    Nothing was found on my request
{else}
    <div class=' search-target' style="font-weight: bold; color: black;">
	{if $list[0].email.type_id == $smarty.const.EMAIL_TYPE_SPAM || (!empty($page) && $page == 'deleted_by_user')}
	    <a class="group-checkbox gc-all btn btn-primary" href="javascript:void(0);" onclick="return false;" title="Select all emails">select all</a>&nbsp;&nbsp;&nbsp;
            <a class="group-checkbox gc-unselect btn btn-default" href="javascript:void(0);" onclick="return false;" title="Unselect emails">unselect all</a>
	{else}
	    <div class="btn-group">
		<a class="group-checkbox gc-all btn btn-primary" href="javascript:void(0);" onclick="return false;" title="Select all emails">select all</a>   
		<a class="group-checkbox gc-unread btn btn-primary" href="javascript:void(0);" onclick="return false;" title="Select unreaded emails">select unread</a>
		<a class="group-checkbox gc-read btn btn-primary" href="javascript:void(0);" onclick="return false;" title="Select readed emails">select read</a>
	    </div>
	    &nbsp;&nbsp;&nbsp;
	    <a class="group-checkbox gc-unselect btn btn-default" href="javascript:void(0);" onclick="return false;" title="Unselect emails">unselect all</a>
        {/if}
    </div>
    <div class="pad1"></div>
    
    <table class="emails" style="table-layout: fixed; width: 100%;">

	{foreach from=$list item=row name=emails}
	
	<!--
	    <tr  style="display:none;"  id="email-{$row.email.id}" class="email-type-{if isset($row.email.userdata) && $row.email.userdata.read_at > 0}read{else}{$row.email.type_id}{/if}{if !empty($row.email.is_biz_tag_exists)} biz-tag-exists{/if} {if $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT && $row.email.is_sent > 0} email-type-{$row.email.type_id}-{$row.email.is_sent}{/if}">
		<td style="width: 20px; text-align: center;">
		    
		</td>
		<td style="width: 20px; text-align: center;" onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';">

		</td>
		<td style="width: 250px; padding: 0 0 0 5px;" onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';">

		</td>
		<td style="" onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';">
		    {strip}
			<span></span>
			{if !empty($row.email.description)}
			    
			{/if}
		    {/strip}
		</td>
		<td style="width: 20px; text-align: center;" onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';" class="text-center">
		    {if empty($row.email.is_biz_tag_exists) && isset($row.email.attachments)}<img src="/img/icons/tag-minus.png" alt="HasNoBizTags" title="Has no biz tags"/>{/if}
		</td>
		<td style="width: 20px; text-align: center;" onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';" class="text-center">
		    {if isset($row.email.attachments)}<img src="/img/icons/paper-clip.png" />{elseif empty($row.email.is_biz_tag_exists)}<img src="/img/icons/tag-minus.png" alt="HasNoBizTags" title="Has no biz tags"/>{/if}
		</td>
		<td  style="width: 70px;"onclick="location.href='{if !empty($object_alias) && !empty($object_id)}/{$object_alias}/{$object_id}{/if}/email/{$row.email.id}~tid{$token}';" class="email-date">
		    {if empty($row.email.is_today)}
			{$row.email.date_mail|date_format:'d/m/Y'}<br>{$row.email.date_mail|date_format:'H:i:s'}
		    {else}
			{$row.email.date_mail|date_format:'H:i:s'}
		    {/if}            
		</td>
	    </tr>
	-->
	
	    <tr>
		<td colspan="7" >
		
		    <div class="panel-group" id="accordion">
			<div class="panel 
			    {if $row.email.type_id == $smarty.const.EMAIL_TYPE_INBOX}
				panel-primary
			    {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
				panel-success 
			    {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_ERROR}
				panel-danger
			    {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}
				panel-info
			    {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_SPAM}
				panel-danger
			    {/if}">
			    <div class="panel-heading" style="cursor:pointer;">
				<input type="checkbox" name="selected_ids[]" class="single-checkbox{if isset($row.email.userdata)} et-read{else} et-unread{/if}{if $row.email.type_id != $smarty.const.EMAIL_TYPE_SPAM} et-notspam{else} et-spam{/if}" value="{$row.email.id}" />&nbsp;					
				<span class="pull-right">
				{if $row.email.type_id == $smarty.const.EMAIL_TYPE_INBOX}
				    <span class="badge">INBOX
					{if !isset($row.email.userdata)}
					    <b><i>NEW</i></b>
					{/if}
				    </span>
				    {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
					<span class="badge">SENT</span>
				    {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_ERROR}
					<span class="badge">ERROR</span>
				    {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}
					<span class="badge">DRAFT</span>
				    {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_SPAM}
					<span class="badge">SPAM</span>
				    {/if}
				    <span class="badge" style="">
					{if empty($row.email.is_today)}
					    <i><b>{$row.email.date_mail|date_format:'d/m/Y'}&nbsp;{$row.email.date_mail|date_format:'H:i:s'}</b></i>
					{else}
					    <i><b>{$row.email.date_mail|date_format:'H:i:s'}</b></i>
					{/if}  
				    </span>		
				</span>
				<span>
				    <span class="panel-title" style="display:inline-block; font-size:100%;">
					<a data-toggle="collapse" data-parent="#accordion" href="#collapse{$row.email_id}">
					    <b>
						<span class="pull-left" style="width:200px; overflow:hidden; margin-right:10px;">
						    {if $row.email.type_id == $smarty.const.EMAIL_TYPE_INBOX || $row.email.type_id == $smarty.const.EMAIL_TYPE_ERROR || $row.email.type_id == $smarty.const.EMAIL_TYPE_SPAM}
							{$row.email.sender_address|human_email|truncate:40:' ...'}
						    {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
							{if isset($row.person)}
							    {$row.person.full_name}
							{elseif isset($row.company)}
							    {$row.company.title}
							{else}
							    {$row.email.recipient_address|escape:'html'|truncate:40:' ...'}
							{/if}
						    {elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}
							{$row.email.author.full_login}
						    {/if}
						</span>
						{if empty($row.email.title)}
						    (no subject)
						{else}
						    {$row.email.title|truncate:50:' ...'}
						{/if}
					    </b>
					</a>
				    </span>
				</span>
				<span style="display:inline-block;float:right; text-align: right;">
				    {if isset($row.email.userdata) && $row.email.userdata.read_at > 0}
				    {else}
					{*NEW $row.email.type_id*}
				    {/if}
				    {if !empty($row.email.is_biz_tag_exists)} 
					{*biz-tag-exists*}
				    {/if}
				</span>
			    </div>
			    <div id="collapse{$row.email_id}" class="panel-collapse collapse">
				<div class="panel-body" style="cursor:default;">
				    <div class="btn-group">
					<!--<a class="btn btn-default" href="/email/{$row.email_id}" class="i-email-type{$row.type_id}">Read mail</a>-->
					{if !empty($row.email.is_biz_tag_exists)}
					    {foreach from=$row.email.biz_tags item=item name=bizes}
						<a  class="btn btn-default"  href="/{$item.object_alias}/{$item.object_id}/emails/filter/type:0;">
						    View correspondence (BIZ blog #{$item.biz.number})
						</a>
					    {/foreach}
					{/if}
					{if !empty($row.email.is_biz_tag_exists)}
					    {foreach from=$row.email.biz_tags item=item name=bizes}
						<a  class="btn btn-default"  href="/{$item.object_alias}/{$item.object_id}/blog">
						    View BIZ blog #{$item.biz.number}
						</a>
					    {/foreach}
					{/if}
					<!-- Button trigger modal 
					<button class="btn btn-primary" data-toggle="modal" data-target="#myModal{$row.email_id}">
					  Test modal
					</button>
					-->
					<!-- Modal -->
					<div class="modal fade" id="myModal{$row.email_id}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					  <div class="modal-dialog">
					    <div class="modal-content">
					      <div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title" id="myModalLabel">{$row.email.title}</h4>
					      </div>
					      <div class="modal-body">
						<pre class="pre-scrollable"><xmp>{*$row.email.description_html|replace:'<!--':' '|replace:'-->':' '|replace:'<head>':'<!--<head>'|replace:'</head>':'</head>-->'*}</xmp></pre>
						
						<xmp>{$row.email.description_html}</xmp>
						
					      </div>
					      <div class="modal-footer">
						<!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
						<button type="button" class="btn btn-primary">Save changes</button>
					      </div>
					    </div>
					  </div>
					</div>
					
				    </div>
				    <br/>
				    <br/>
				    {*{if $row.email.driver.person_id>1}<p><h5><b>Driver:</b> <i>{$row.email.driver.person.full_name} @ {$row.email.driver.person.company.title}</i></h5>{/if}
				    {if $row.email.navigator|@count>0}<p><h5><b>Navigators:</b> {foreach from=$row.email.navigator item=navigator name=person}<i>{$navigator.person.full_name}{if $smarty.foreach.person.last}{else}, {/if}</i>{/foreach}</h5>{/if}	*}	
				    {if $row.email.attachments|@count>0}
					<br/>
					<ul class="list-group">
					    <li class="list-group-item">Attachments:</li>
					    {foreach name=i from=$row.email.attachments item=atach}
						{if !empty($atach.attachment)}
						    {if strstr($atach.attachment.content_type, 'image')}
							{*<a class="attachment-{$atach.attachment.ext|lower}" href="/file/{$row.attachment.secret_name}/{$row.attachment.original_name}" target="_blank">{$row.attachment.original_name} ({$row.attachment.size|human_filesize})</a>*}
							<li class="list-group-item"><a class="attachment-{$atach.attachment.ext|lower}" href="/file/{$atach.attachment.secret_name}/{$atach.attachment.original_name}" rel="pp_attachments[]">{$atach.attachment.original_name} ({$atach.attachment.size|human_filesize})</a></li>
						    {else}
							<li class="list-group-item"><a class="attachment-{$atach.attachment.ext|lower}" href="/file/{$atach.attachment.secret_name}/{$atach.attachment.original_name}" target="_blank">{$atach.attachment.original_name} ({$atach.attachment.size|human_filesize})</a></li>
						    {/if}
						{/if}
					    {/foreach}
					</ul>		
				    {/if}			
				    <table class="table" style="display:none;">
					<tr style="display:none;">
					    <td style="width:10%">From:</td>
					    <td>            
						{if $row.email.type_id == $smarty.const.EMAIL_TYPE_INBOX || $row.email.type_id == $smarty.const.EMAIL_TYPE_ERROR || $row.email.type_id == $smarty.const.EMAIL_TYPE_SPAM}
						    {$row.email.sender_address|human_email|truncate:40:' ...'}
						{elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_OUTBOX}
						    {if isset($row.person)}
							{$row.person.full_name}
						    {elseif isset($row.company)}
							{$row.company.title}
						    {else}
							{$row.email.recipient_address|escape:'html'|truncate:40:' ...'}
						    {/if}
						{elseif $row.email.type_id == $smarty.const.EMAIL_TYPE_DRAFT}
						    {$row.email.author.full_login}
						{/if}
					    </td>
					</tr>
					<tr>
					    <td style="width:10%">
						Open as:
					    </td>
					    <td>
						<br/>
					    </td>
					</tr>
					<tr style="width:10%">
					    <td style="width:10%">
						Attachments:
					    </td>
					    <td>
						{foreach name=i from=$row.email.attachments item=atach}
						    {if !empty($atach.attachment)}
							{if strstr($atach.attachment.content_type, 'image')}
							    {*<a class="attachment-{$atach.attachment.ext|lower}" href="/file/{$row.attachment.secret_name}/{$row.attachment.original_name}" target="_blank">{$row.attachment.original_name} ({$row.attachment.size|human_filesize})</a>*}
							    <a class="attachment-{$atach.attachment.ext|lower}" href="/file/{$atach.attachment.secret_name}/{$atach.attachment.original_name}" rel="pp_attachments[]">{$atach.attachment.original_name} ({$atach.attachment.size|human_filesize})</a><br/>
							{else}
							    <a class="attachment-{$atach.attachment.ext|lower}" href="/file/{$atach.attachment.secret_name}/{$atach.attachment.original_name}" target="_blank">{$atach.attachment.original_name} ({$atach.attachment.size|human_filesize})</a> <br/>
							{/if}
						    {/if}
						{/foreach}
					    </td>
					</tr>
				    </table>
				    
				    <div class="panel-body">
					<h3 class="email-title">{$row.email.title}</h3>
					<p class="email-desription-body" style="font-size: medium;">
					<p class="email-description" style="padding:1px;">
					    {if $row.email.description|count_characters:true >= 1000}
						{$row.email.description|strip_tags|truncate:300:' ...'}
						<br/>
						<button class="btn btn-default btn-read-more" data-email-id="{$row.email_id}">Full view</button>
					    {else}
						{$row.email.description}
					    {/if}
					</p>
					</p>
				    </div>
				    
				</div>
			    </div>
			</div>
		    
		</div>
		</td>
	    </tr>
	{/foreach}
    </table>
{/if}


