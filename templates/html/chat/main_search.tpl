	<div class="panel panel-primary" style="width:50%;">
		  <!-- Default panel contents -->
		  <div class="panel-heading"><b>Search settings</b>
			<span class="pull-right">
			
			<input type="checkbox" id="is_phrase" class="" name="is_phrase" value="1"{if isset($is_phrase) && !empty($is_phrase)} checked="checked"{/if}>
			Exact phrase
			&nbsp;&nbsp;&nbsp;
			<input type="checkbox" id="is_mam" name="is_mam" value="1"{if isset($is_mam) && !empty($is_mam)} checked="checked"{/if}>
			Include Messages to MaM			
			</span>
		  </div>
		  <div class="panel-body">
			<div class="input-group">
				<span class="input-group-addon">Keyword</span>
				<input type="text" id="keyword" name="keyword" class="form-control"{if isset($keyword)} value="{$keyword|escape:'html'}"{/if} placeholder="Enter keyword">
				<span class="input-group-btn">
					<input type="submit" name="btn_find" value="Find" class="btn btn-primary">
				</span>
			</div>
		  </div>
		  <!-- Table -->
		  <table class="table">
			<tr>
				<td style="border: none;">
					<div class="input-group">
						<span class="input-group-addon">Date from</span>
						<input type="text" id="date-from" name="date_from" class="datepicker form-control" value="{if isset($date_from)}{$date_from|date_format:'d/m/Y'}{/if}" placeholder="Click here and select date">
					</div>		
				</td>
<td style="border-left: none; border-right: none;"><h3 style="color:#357ebd; display: inline;"><b>&rarr;</b></h3></td>
				<td style="border: none;">
					<div class="input-group">
						<span class="input-group-addon">Date to</span>
						<input type="text" id="date-to" name="date_to" class="datepicker form-control" value="{if isset($date_to)}{$date_to|date_format:'d/m/Y'}{/if}"  placeholder="Click here and select date">
					</div>		
				</td>					    
					
			<tr>
			</tr>

				<td style="border-right: none;">
	
					<div class="input-group">
						<span class="input-group-addon">Sender</span>
						<input type="text" id="sender-title" name="sender_title" class="form-control" value="{if isset($sender_title)}{$sender_title}{/if}" placeholder="Enter sender name">
                        <input type="hidden" id="sender-id" name="sender_id" class="normal" value="{if isset($sender_id)}{$sender_id}{/if}">
					</div>					
				</td>
		<td style="border-left: none; border-right: none;"><h3 style="color:#357ebd; display: inline;"><b>&rarr;</b></h3></td>
				<td style="border-left: none; ">
					<div class="input-group">
						<span class="input-group-addon">Recipient</span>
                        <input type="text" id="recipient-title" name="recipient_title" class="form-control" value="{if isset($recipient_title)}{$recipient_title}{/if}"  placeholder="Enter recipient name">
                        <input type="hidden" id="recipient-id" name="recipient_id" class="normal" value="{if isset($recipient_id)}{$recipient_id}{/if}">
					</div>
					</td>
			</tr>
		  </table>
		  <div class="panel-body">
			<p style="display: none;">
					<input type="checkbox" id="is_phrase" class="" name="is_phrase" value="1"{if isset($is_phrase) && !empty($is_phrase)} checked="checked"{/if}>
					Exact phrase
			</p>
			<p style="display: none;">
					<input type="checkbox" id="is_mam" name="is_mam" value="1"{if isset($is_mam) && !empty($is_mam)} checked="checked"{/if}>
					Include Messages to MaM
			</p>
			<p>
					<input type="checkbox" id="is_dialogue" name="is_dialogue" value="1"{if isset($is_dialogue) && !empty($is_dialogue)} checked="checked"{/if}> 
					In both directions			
			</p>	
			</div>

		</div>
<!--
<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>
-->

{if empty($list)}
    {if isset($filter)}Nothing was found{/if}
{else}
    <ol id="chat-messages" class="chat-messages">
    {foreach from=$list item=row}
        {include file='templates/html/chat/control_chat_message.tpl' message=$row keyword=$keyword is_phrase=$is_phrase}
    {/foreach}
    </ol>
{/if}