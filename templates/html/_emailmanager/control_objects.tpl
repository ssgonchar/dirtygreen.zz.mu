<div class="row search-biz">
    <div class="col-md-12">
        <div class="input-group input-group-sm">
            <input type="text" class="form-control input-biz-title biz-autocomplete" id="{$row.email.id}email_biz" placeholder="Start type to select">
            <input type="hidden" id="{$row.email.id}email_biz-id" name="form[biz_id]" value="0">
            <span class="input-group-btn">
                <button class="btn btn-sm btn-success add-biz-object" type="button" data-selector_biz_id="#{$row.email.id}email_biz-id" data-email_id="{$row.email.id}">+</button>
            </span>
        </div><!-- /input-group -->
    </div>
    <br>
</div>

<div class="row added-bizes">
    <div class="col-md-12">
        {foreach $row.email.objects as $item}
            {if $item.object_alias=='biz'}
                <span id="{$item.object_alias|escape:'html'}-{$item.object_id}" style="margin-right: 10px;">
                    <input type="hidden" name="objects[{$item.object_alias|escape:'html'}-{$item.object_id}]" class="{$item.object_alias|escape:'html'}-object" value="{$item.object_id}">
                    <a class="tag-{if in_array($item.object_alias, array('biz', 'company', 'order', 'person'))}{$item.object_alias}{else}document{/if}" style="vertical-align: top; margin-right: 3px;" href="/{$item.object_alias|escape:'html'}/{$item.object_id}/emailmanager/filter/type:0;" target="_blank">{$item.biz.title}</a><img src="/img/icons/cross-small.png" style="cursor: pointer;" class="remove-object-alias" data-object-alias="{$item.object_alias|escape:'html'}" data-object-id="{$item.object_id}" data-email-id="{$row.email.id}">
                </span><br/>
            {/if}
        {/foreach}   
    </div>
</div>