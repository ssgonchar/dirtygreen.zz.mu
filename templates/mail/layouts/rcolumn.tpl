{include file='templates/layouts/controls/control_header.tpl'}
<div class="colmask rightmenu">
    <div class="colleft">
        <div class="col1wrap">
            <div class="col1">
            {if !empty($content)}{$content}{/if}
            </div>
        </div>
        <div class="col2">
        {if isset($rcontext)}{$rcontext}{/if}
        </div>
    </div>
</div>
{include file='templates/layouts/controls/control_footer.tpl'}