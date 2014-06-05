{include file='templates/layouts/controls/control_header.tpl'}
<div class="row ">
    <div class="col-md-3 column-main column-side" style="">
        <div class="panel panel-default column-toolbar">
            <div class="panel-heading" style="vertical-align: middle;">
                    Toolbox
                    <!--<span class="glyphicon glyphicon-chevron-left pull-right btn btn-default btn-xs" onclick="show_hide_column(this);"></span>-->
            </div>
            {if isset($rcontext)}{$rcontext}{/if}
        </div>
    </div>                                    
    <div class="col-md-9 column-main">
        {if !empty($content)}{$content}{/if}
    </div>            
</div>
{include file='templates/layouts/controls/control_footer_mod.tpl'}