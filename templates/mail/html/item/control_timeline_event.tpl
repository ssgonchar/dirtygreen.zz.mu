<div class="timeline-event{if !empty($event.object_alias)} {$event.object_alias}{/if}">
    <h3 style="margin-top: 0;">{$event.title|escape:'html'}</h3>
    <span style="color: #777; font-size: 10px;">
    {if empty($event.modified_by)}
        {$event.created_at|date_format:'d/m/Y H:m'}{if !empty($event.author)}, {$event.author.login|escape:'html'}{/if}
    {else}
        {$event.modified_at|date_format:'d/m/Y H:m'}{if !empty($event.modifier)}, {$event.modifier.login|escape:'html'}{/if}
    {/if}
    </span>
    {if !empty($event.object_alias)}
        <div style="padding-top: 10px;"><a href="/{$event.object_alias}/{$event.object_id}">{$event.object.doc_no}</a></div>
    {/if}    
    <div style="position: absolute; top: 5px; right: 5px; font-size: 8px; color: #777;">{$event.no}</div>
</div>