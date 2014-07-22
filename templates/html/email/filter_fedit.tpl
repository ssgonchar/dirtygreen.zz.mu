<table class="form" width="100%">
    <tr>
        <td class="form-td-title" style="width: 160px;">From : </td>
        <td><input class="wide" type="text" name="form[from]" value="{if !empty($form.from)}{$form.from|escape:'html'}{/if}" /></td>
    </tr>
    <tr>
        <td class="form-td-title">To : </td>
        <td><input class="wide" type="text" name="form[to]" value="{if !empty($form.to)}{$form.to|escape:'html'}{/if}" /></td>
    </tr>
    <tr>
        <td class="form-td-title">Subject : </td>
        <td><input class="wide" type="text" name="form[subject]" value="{if !empty($form.subject)}{$form.subject|escape:'html'}{/if}" /></td>
    </tr>
    <tr>
        <td class="form-td-title">Text : </td>
        <td><input class="wide" type="text" name="form[text]" value="{if !empty($form.text)}{$form.text|escape:'html'}{/if}" /></td>
    </tr>
    <tr>
        <td></td>
        <td>
            <label for="form-attachment">
            <input id="form-attachment" type="checkbox" name="form[attachment]" value="yes"{if !empty($form.attachment) && $form.attachment == 'yes'} checked="checked"{/if} />
            eMail has attachments</label>
        </td>
    </tr>
    {if !isset($form.id) || empty($form.id)}
    <tr>
        <td class="form-td-title"></td>
        <td>
            <label for="form-is-scheduled">
            <input id="form-is-scheduled" type="checkbox" name="form[is_scheduled]" value="1"{if !empty($form.is_scheduled) && $form.is_scheduled == 1} checked="checked"{/if} />
            Apply this filter for old eMails</label>
        </td>
    </tr>
    {/if}
</table>
    
<div class="pad"></div>
    
<table class="form" width="100%">
    <tr>
        <td class="form-td-title text-top" style="width: 160px;">Applied Tags : </td>
        <td>
            <a class="add" href="javascript: void(0);" onclick="show_email_co_list();">Add Tag</a>
            <div class="email-co-objects-list" style="margin-top: 10px;">
            {if !empty($tags)}
            {foreach $tags as $item}
            <span id="{$item.object_alias|escape:'html'}-{$item.object_id}" style="margin-right: 10px;">
                <input type="hidden" name="objects[{$item.object_alias|escape:'html'}-{$item.object_id}]" class="{$item.object_alias|escape:'html'}-object" value="{$item.object_id}">
                <a class="tag-{if in_array($item.object_alias, array('biz', 'company', 'order', 'person'))}{$item.object_alias}{else}document{/if}" style="vertical-align: top; margin-right: 3px;" href="/{$item.object_alias|escape:'html'}/{$item.object_id}" target="_blank">{$item.title|escape:'html'}</a><img src="/img/icons/cross-small.png" onclick="remove_email_object('{$item.object_alias|escape:'html'}', {$item.object_id});">
            </span>
            {/foreach}
            {/if}
            </div>
        </td>
    </tr>
</table>
    
<div id="email-co-select" style="display: none;">
    <div id="overlay"></div>
    <div id="email-co-container">
    <div style="padding: 10px;">
        <table class="form" width="100%">
            <tr>
                <td>Type : </td>
            </tr>
            <tr>
                <td>
                    <select id="email-co-type-alias" class="max" onchange="email_clear_search_results();">
                        <option value="">--</option>
                        <option value="biz">Biz</option>
                        <option value="company">Company</option>
                        <option value="country">Country</option>
                        <option value="order">Order</option>
                        <option value="person">Person</option>
                        <option value="product">Product</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <td>Search For : </td>
            </tr>
            <tr>
                <td><input id="keyword" type="text" class="max" onkeypress="if(event.keyCode == 13) return false;" onkeyup="{literal}if(event.keyCode == 13) {find_email_objects(this.value); return false;}{/literal}"></td>
                {*<td><input type="text" class="max" onkeypress="if(event.keyCode == 13) return false;" onkeyup="find_email_objects(this.value);"></td>*}
            </tr>
            <tr>
                <td>Search Result : </td>
            </tr>
            <tr>
                <td>
                    <select id="email-co-search-result" multiple="multiple" size="10" class="max" style="height: 200px;"></select>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <input type="button" class="btn100o" value="Add" style="margin-right: 20px;" onclick="add_email_object();">
                    <input type="button" class="btn100" value="Close" onclick="close_email_co_list();">
                </td>
            </tr>
        </table>
    </div>
    </div>
</div>