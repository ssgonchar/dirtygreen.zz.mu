<!--
<div class="footer-left">
    {include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
</div>
<div class="footer-right">
    <input type="button" class="btn100o" value="Edit" style="margin-left: 20px;" onclick="location.href='/item/edit/{$form.id}';">
</div>
-->

<ul class="nav navbar-nav footer_panel">
	<li style='margin-top: 6px;'>
        {include file='templates/layouts/controls/control_document_timestamp.tpl' doc=$form}
	</li>
</ul>
<ul class="nav navbar-nav navbar-right">
	<li>
        <input type="button" class="btn100o" value="Edit" style="margin-left: 20px;" onclick="location.href='/item/edit/{$form.id}';">
	</li>	
</ul>