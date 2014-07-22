<table class="form" width="100%">
    <tr>
        <td width="75%">
            <table width="100%">
                <tr>
                    <td class="form-td-title">Keyword :</td>
                    <td colspan="3"><input type="text" id="keyword" name="keyword" class="max"{if isset($keyword)} value="{$keyword|escape:'html'}"{/if}></td>
                    {*
                    <td class="form-td-title">
                        <input type="checkbox" id="is_phrase" name="is_phrase" value="1"{if isset($is_phrase) && !empty($is_phrase)} checked="checked"{/if}>
                    </td>
                    <td><label for="is_phrase">Exact phrase</label></td>
                    *}
                    <td class="form-td-title"></td>
                    <td class="form-td-title-left">
                        <select class="normal" name="search_type">
                            <option value="exact"   {if isset($search_type) && $search_type == "exact"}selected=selected{/if}>Exact phrase</option>
                            <option value="all"     {if isset($search_type) && $search_type == "all"}selected=selected{/if}>All Words</option>
                            <option value="any"     {if isset($search_type) && $search_type == "any"}selected=selected{/if}>Any One Word</option>
                        </select>
                    </td>
                </tr>                
                <tr>
                    <td class="form-td-title">Date From :</td>
                    <td><input type="text" id="date-from" name="date_from" class="datepicker" value="{if isset($date_from)}{$date_from|date_format:'d/m/Y'}{/if}"></td>    
                    <td class="form-td-title">Sender :</td>
                    <td>
                        <input type="text" id="sender-title" name="sender_title" class="max" value="{if isset($sender_title)}{$sender_title}{/if}">
                        <input type="hidden" id="sender-id" name="sender_id" class="normal" value="{if isset($sender_id)}{$sender_id}{/if}">
                    </td>  
                    <td class="form-td-title-left"></td>
                    <td class="form-td-title-left">
                        <input type="checkbox" id="is_dialogue" name="is_dialogue" value="1"{if isset($is_dialogue) && !empty($is_dialogue)} checked="checked"{/if}> 
                        <label for="is_dialogue">Dialogues</label>
                    </td>    
                </tr>
                <tr>
                    <td class="form-td-title">Date To :</td>
                    <td><input type="text" id="date-to" name="date_to" class="datepicker" value="{if isset($date_to)}{$date_to|date_format:'d/m/Y'}{/if}"></td>
                    <td class="form-td-title">Recipient :</td>
                    <td>
                        <input type="text" id="recipient-title" name="recipient_title" class="max" value="{if isset($recipient_title)}{$recipient_title}{/if}">
                        <input type="hidden" id="recipient-id" name="recipient_id" class="normal" value="{if isset($recipient_id)}{$recipient_id}{/if}">
                    </td>
                    <td class="form-td-title-left"></td>
                    <td class="form-td-title-left"><input type="checkbox" id="is_mam" name="is_mam" value="1"{if isset($is_mam) && !empty($is_mam)} checked="checked"{/if}>
                        <label for="is_mam">Include Messages to MaM</label>
                    </td>                    
                </tr>
            </table>
        </td>
        <td width="25%" class="text-bottom">
            <input type="submit" name="btn_find" value="Find" class="btn100o">
        </td>        
    </tr>    
</table>

<div class="pad1"></div>
<hr style="width: 100%; color: #dedede;" size="1"/>
<div class="pad1"></div>

{if empty($list)}
    {if isset($filter)}Nothing was found{/if}
{else}
    <ol id="chat-messages" class="chat-messages">
    {foreach from=$list item=row}
        {include file='templates/html/chat/control_chat_message.tpl' message=$row keyword=$keyword is_phrase=$is_phrase}
    {/foreach}
    </ol>
{/if}