<table id="newmessage-form-text" style="width: 100%; margin-top: 10px;" id="tbl">
    <tr>
        <td id="message-modal-text" style="vertical-align: top;">
            <table id="message-modal-text-params" class="chat-form" width="100%">
                <tr>
                    <td width="35px" style="background: #F0F0EE; padding: 0; padding-left: 10px;">Title : </td>
                    <td width="18px" style="background: #F0F0EE;">
                    <div style="position: relative;">
                        <img src="/img/icons/choose_biz.png" onclick="chat_modal_show_biz();" style="cursor: pointer;">
                        <div class="chat-message-biz" id="chat-message-biz-tip" style="left: 0px; top: 17px;">
                            <table class="form" width="100%">
                                <tr>
                                    <td class="text-right" nowrap="nowrap">Team : </td>
                                    <td>
                                        <select id="chat-message-team-select" class="normal" onchange="chat_fill_biz_select(this.value);">
                                            <option value="0">--</option>
                                            {foreach from=$teams item=row}
                                            <option value="{$row.team.id}">{$row.team.title|escape:'html'}</option>
                                            {/foreach}
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right" nowrap="nowrap">BIZ Search : </td>
                                    <td>
                                        <input type="text" id="chat-biz" name="form[biz_title]" data-titlefield="doc_no_chat" class="wide"{if isset($biz_title)} value="{$biz_title|escape:'html'}"{/if}>
                                        <input type="hidden" id="chat-biz-id" name="form[biz_id]" value="{if isset($biz_id)}{$biz_id}{else}0{/if}">

                                        <select id="chat-message-biz-select" class="wide" style="display: none;">
                                            <option>--</option>
                                        </select>                            
                                    </td>
                                </tr>
                                <tr>
                                    <td><a href="javascript: void(0);" onclick="chat_modal_hide_biz();">close</a></td>
                                    <td class="text-right"><a href="javascript: void(0);" onclick="chat_modal_select_biz();">select biz</a></td>
                                </tr>        
                            </table>
                        </div>            
                    </div>
                    </td>            
                    <td colspan="2" style="background: #F0F0EE;"><input type="text" id="chat-title" class="max"{if isset($chat_newmessage.title) && !empty($chat_newmessage.title)} value="{$chat_newmessage.title|escape:'html'}"{/if}></td>
                    <td width="20px" class="text-center" style="background: #F0F0EE;">
                        <img src={if isset($chat_newmessage.is_alert) && $chat_newmessage.is_alert > 0}"/img/icons/soundon.png"{else}"/img/icons/soundoff.png"{/if} id="chat-sa-switch" onclick="chat_modal_switch_sound_alert();" style="cursor: pointer;">
                        <input type="hidden" id="chat-sa" value="{if isset($chat_newmessage.is_alert)}{$chat_newmessage.is_alert}{else}0{/if}">
                    </td>
                    <td width="20px" class="text-center" style="background: #F0F0EE;">
                        <img src={if isset($chat_newmessage.is_pending) && $chat_newmessage.is_pending ==0}"/img/icons/pendingoff.png"{else}"/img/icons/pendingon.png"{/if} id="chat-p-switch" onclick="chat_modal_switch_pending();" style="cursor: pointer;">
                        <input type="hidden" id="chat-p" value="{if isset($chat_newmessage.is_pending)}{$chat_newmessage.is_pending}{else}1{/if}">
                    </td>
                    <td width="60px" class="text-right" style="background: #F0F0EE;">Deadline :</td>
                    <td width="80px" style="background: #F0F0EE;"><input type="text" id="chat-deadline" value="{if isset($chat_newmessage.deadline) && !empty($chat_newmessage.deadline) && $chat_newmessage.deadline > 0}{$chat_newmessage.deadline|date_format:"%m/%d/%Y"}{/if}" style="width:80px;"></td>
                </tr>
            </table>
			<input type="text" id="font-color" value="{$colordesc}" style="display:none;"/>
            <textarea id="chat-description" name="chat-description" rows="5" style="width: 100%; color:{$font-color};">
                {if isset($chat_newmessage.description) && !empty($chat_newmessage.description)}
                    {$chat_newmessage.description}
                {/if}
            </textarea>        
        </td>
        <td style="width: 180px; vertical-align: top; overflow-y:auto; padding-left: 10px;">
            {include file='templates/html/chat/control_recipients.tpl'}    
        </td>
    </tr>
    <tr>
        <td colspan="2" id="newmessage-form-attachments" style="padding-left: 10px; height: 30px;">
            <div style="display: inline-block;">
                <div id="attachments" style="min-height: 20px;">
                    {if isset($attachment_list) && !empty($attachment_list)}
                        {foreach from=$attachment_list item=row}  
                            {include file='/templates/controls/attachment_text.tpl' attachment=$row.attachment}
                        {/foreach}
                    {/if}
                </div>
                <div class="separator"></div>
				<div class="pad1"></div> 
                <div style="text-align: right; vertical-align: top; position: absolute; bottom: 0; right: 0;">
                    <input type="button" name="btn_save" class="btn100b save" value="Share" style="margin-right: 10px; cursor: pointer; display: none;" onclick="uploader_save_form();">
                    <div  style="display: inline-block; height: 34px; vertical-align: top;">
                        <div class="qq-fileuploader"></div>
                    </div>
                </div>
            </div>
            <div class="qq-upload-drop-area" style="display: none;"><span style="color: grey; font-size: 16px;">Drop files here</span></div>
            <div><ul id="qq-upload-list"></ul></div>
            <div style="display: none;"><div class="qq-upload-drop-area"></div></div>
            <input type="hidden" id="qq_object_alias" value="{if isset($object_alias)}{$object_alias}{/if}{if isset($object_id) && !empty($object_id)}{$object_id}{/if}message">
            <input type="hidden" id="qq_object_id" value="{$smarty.session.user.id}">
        </td>
    </tr>
    <tr>
        <td colspan="2" id="newmessage-form-buttons" style="text-align: right; height: 40px; padding-left: 10px;">
			<div style="float: left;">
            <input type="button" class="btn100" value="Close" onclick="{*chat_modal_clear_fields();*} close_window();">
			</div>
			{*<input type="button" class="btn150" value="Send Private" onclick="chat_modal_send_message('{if isset($object_alias)}{$object_alias}{/if}', {if isset($object_id)}{$object_id}{else}0{/if}, {$smarty.const.MESSAGE_TYPE_PRIVATE});" style="margin-right: 20px;">*}
			<div class="float-right">
				<input type="button" id="post-message" class="btn100o" style="margin-right: 10px;" value="Post" onclick="chat_modal_send_message('{if isset($object_alias)}{$object_alias}{/if}', {if isset($object_id)}{$object_id}{else}0{/if}, {$smarty.const.MESSAGE_TYPE_NORMAL});">
			</div>
			<div id="fileuploader"></div>
		 </td>
    </tr>    
</table>
<input type="hidden" id="newmessage_object_alias" value="{$object_alias}"><input type="hidden" id="newmessage_object_id" value="{$object_id}">