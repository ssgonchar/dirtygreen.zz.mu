{include file='templates/layouts/controls/control_header.tpl'}
<div class="row " >
    <!--<div class="col-md-3 column-main column-side" style="">
        <div class="panel panel-default column-toolbar">
            <div class="panel-heading" style="vertical-align: middle;">
                    Toolbox
                    <!--<span class="glyphicon glyphicon-chevron-left pull-right btn btn-default btn-xs" onclick="show_hide_column(this);"></span>-->
            <!--</div>
            
        </div>
    </div>      -->
            <div class="col-sm-2 col-md-3 sidebar column-side" style="overflow-y: auto; max-height: 600px;">
        {if isset($rcontext)}{$rcontext}{/if}
    </div>            
    
   <div class="col-md-9 column-main" style="border-left: 1px dashed #ccc; padding-left: 0px;">
        {if !empty($content)}{$content}{/if}
    </div>            
</div>
{include file='templates/layouts/controls/control_footer_mod.tpl'}