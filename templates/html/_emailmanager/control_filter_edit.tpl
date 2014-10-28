<div class="col-md-12">
    <br/>
    <form id="formEditFilter">
        <label for="exampleInputPassword1">BIZes</label>
        <div class="{if $item.efilter.id>0}input-group input-group-sm{else}form-group{/if}">

            <input id="biz" placeholder="start typing to select BIZ" class="input-biz-title form-control" style="width:100%;">

            {if $item.efilter.id>0}
            <span class="input-group-btn">
                <span class="btn btn-success btn-biz-add" value="Add" name="btn_find">+</span>
            </span>
            {/if}
            <input id="biz-id" class="add-biz-id" type="hidden" name="form[biz_id]" value="0">
            <input id="" class="add-filter-id" type="hidden" name="" value="{$item.efilter.id}">       
            <input type="hidden" class="form-control add-tags" id="exampleInputPassword1" placeholder="@example.com or name@example.com" 
                   {if isset($item.efilter.tags)}value="{$item.efilter.tags}"{/if}>             
        </div>
        <p class="form-group">

            {foreach from=$item.efilter.tags_array item=item_biz}
                {if $item_biz.object_alias=='biz'}
                    <span id="{$item_biz.object_alias|escape:'html'}-{$item_biz.object_id}" style="margin-right: 10px;">
                        <input type="hidden" name="objects[{$item_biz.object_alias|escape:'html'}-{$item_biz.object_id}]" class="{$item_biz.object_alias|escape:'html'}-object" value="{$item_biz.object_id}">
                        <a class="tag-{if in_array($item_biz.object_alias, array('biz', 'company', 'order', 'person'))}{$item_biz.object_alias}{else}document{/if}"data-biz-id="{$item_biz.object_id}" style="vertical-align: top; margin-right: 3px;" href="/{$item_biz.object_alias|escape:'html'}/{$item_biz.object_id}/emailmanager/filter/type:0;" target="_blank">
                            {$item_biz.biz.title}
                        </a>
                        <img src="/img/icons/cross-small.png" style="cursor: pointer;" class="remove-filter-alias" data-object-alias="{$item_biz.object_alias|escape:'html'}" data-objectId="{$item_biz.object_id}" data-email-id="{$item.efilter.id}">
                    </span><br/>
                {/if}                    
            {/foreach}        
        </p>
        <p class="form-group">
            <label for="exampleInputPassword1">From</label>
            <input type="text" class="form-control add-from" id="exampleInputPassword1" placeholder="@example.com or name@example.com" 
                   {if isset($item.efilter.params_array.from)}value="{$item.efilter.params_array.from}"{/if}>
        </p>
        <p class="form-group">
            <label for="exampleInputPassword1">To</label>
            <input type="text" class="form-control add-to" id="exampleInputPassword1" placeholder="@example.com or name@example.com" 
                   {if isset($item.efilter.params_array.to)}value="{$item.efilter.params_array.to}"{/if}>
        </p>
        <p class="form-group">
            <label for="exampleInputPassword1">Keyword</label>
            <input type="text" class="form-control add-keyword" id="exampleInputPassword1" placeholder="Free text" 
                   {if isset($item.efilter.params_array.subject)}value="{$item.efilter.params_array.subject}"{/if}>
        </p>

        <p class="checkbox">
            <label>
                <input type="checkbox"  class="is-sheduled" {if $item.efilter.is_scheduled < 1}checked="true"{/if}> Only for new emails
            </label>
        </p>
        <p class="checkbox">
            <label>
                <input type="checkbox" class="add-attachments" {if isset($item.efilter.params_array.attachment) && $item.efilter.params_array.attachment>0}checked="true"{/if}> Only for emails with attachments
            </label>
        </p>
        {if $item.efilter.id>0}
            <span class="btn btn-success apply-edit">Save</span>
        {else}
            <span class="btn btn-success apply-add">Add</span>
        {/if}
        <span class="btn btn-default close-edit-form">Back</span>
    </form>
</div>