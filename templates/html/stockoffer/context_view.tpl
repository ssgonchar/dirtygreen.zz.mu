<div class="nav navbar-nav navbar-left">
    {include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
</div>
<div class="nav navbar-nav navbar-right">
    <input type="button" value="To List" class="btn btn-default" style=''onclick="location.href='/stockoffer';">
    <input type="button" class="btn btn-primary" value="Edit" style="margin: 7px; cursor: pointer;" onclick="location.href='/stockoffer/{$form.id}/edit'">
</div>