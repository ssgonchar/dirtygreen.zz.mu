<div class="search-biz" style="display: none;">
    <div class="input-group input-group-sm">
        <input type="text" class="form-control input-biz-title biz-autocomplete" id="{$row.id}email_biz" placeholder="Start type to select">
        <input type="hidden" id="{$row.id}email_biz-id" name="form[biz_id]" value="0">
        <span class="input-group-btn">
            <button class="btn btn-sm btn-success add-biz-object" type="button" data-selector_biz_id="#{$row.id}email_biz-id" data-email_id="{$row.id}">+</button>
        </span>
    </div><!-- /input-group -->
</div>

<div class="added-bizes">
    {*
    {foreach $row.objects as $item}
        {if $item.object_alias=='biz'}
            <p>
            <span id="{$item.object_alias|escape:'html'}-{$item.object_id}">
                <nobr>
                <input type="hidden" name="objects[{$item.object_alias|escape:'html'}-{$item.object_id}]" class="{$item.object_alias|escape:'html'}-object" value="{$item.object_id}">
                
                <a class="tag-{if in_array($item.object_alias, array('biz', 'company', 'order', 'person'))}{$item.object_alias}{else}document{/if} pull-left" data-biz-id="{$item.object_id}" style="vertical-align: top; margin-right: 3px; float: left;" href="/{$item.object_alias|escape:'html'}/{$item.object_id}/emailmanager/filter/type:0;" target="_blank">{$item.biz.title}</a>
                <img src="/img/icons/cross-small.png" style="cursor: pointer;" class="remove-object-alias pull-left" data-object-alias="{$item.object_alias|escape:'html'}" data-object-id="{$item.object_id}" data-email-id="{$row.id}">&nbsp;
                </nobr>
            </span> 
        </p>
            
            {/if}
    {/foreach}   
    *}
    {foreach $row.biz_tags as $item}
        {if $item.object_alias=='biz' && $item.biz.title !== ''}
            <p>
            <span id="{$item.object_alias|escape:'html'}-{$item.object_id}">
                <nobr>
                <input type="hidden" name="objects[{$item.object_alias|escape:'html'}-{$item.object_id}]" class="{$item.object_alias|escape:'html'}-object" value="{$item.object_id}">
                
                <a class="tag-{if in_array($item.object_alias, array('biz', 'company', 'order', 'person'))}{$item.object_alias}{else}document{/if} pull-left" data-biz-id="{$item.object_id}" style="vertical-align: top; margin-right: 3px; float: left;" href="/{$item.object_alias|escape:'html'}/{$item.object_id}/emailmanager/filter/type:0;" target="_blank">{$item.biz.title}</a>
                <img src="/img/icons/cross-small.png" style="cursor: pointer;" class="remove-object-alias pull-left" data-object-alias="{$item.object_alias|escape:'html'}" data-object-id="{$item.object_id}" data-email-id="{$row.id}">&nbsp;
                </nobr>
            </span> 
        </p>
            
            {/if}
    {/foreach}   
</div>
{**}