<span class="navbar-form navbar-right">
    {if isset($page)}
        {if $page == 'reply' || $page == 'sendagain'}
            <input type="button" class="btn btn-default" value="Cancel" onclick="location.href = '{$backurl}';" style="cursor: pointer;">
        {/if}
    {else}
        <input type="button" class="btn btn-default" value="Cancel" onclick="location.href = '/email/{$email.id}';" style="cursor: pointer;">
    {/if}
    {*<input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 10px; cursor: pointer;">*}
    <input id="btn_submit_form" type="hidden" name="btn_save" value="Save" class="btn btn-success"/>

    {if !empty($page) && in_array($page, array('compose', 'reply', 'sendagain'))}
        <span class="btn btn-success" data-toggle="modal" data-target="#modal-approve">Save draft</span>
        <!-- кнопку сохранения черновика перенес в модальное окно для выбора apprive by и approve deadline -->
        {*<input type="button" class="btn btn-success" value="Save DFA" style="margin-left: 10px; cursor: pointer;" onclick="document.getElementById('mainform').submit();">*}
        <input type="button" class="btn btn-primary" value="Send" style="cursor: pointer;" onclick="{literal}if (confirm('Am I sure ?')) {
                    $('#btn_submit_form').attr('name', 'btn_send');
                    document.getElementById('mainform').submit();
                }{/literal}">
    {else}
        <!-- кнопку сохранения черновика перенес в модальное окно для выбора apprive by и approve deadline -->
        <span class="btn btn-success" data-toggle="modal" data-target="#modal-approve">Save draft</span>
        {*<input type="button" class="btn btn-success" value="Save DFA" style="margin-left: 10px; cursor: pointer;" onclick="document.getElementById('mainform').submit();">*}
        <input type="button" class="btn btn-primary" value="Send" style="cursor: pointer;" onclick="{literal}if (confirm('Am I sure ?')) {
            $('#btn_submit_form').attr('name', 'btn_send');
            document.getElementById('mainform').submit();
        }{/literal}">
    {/if}
</span>
{if !empty($email_id)}
    <ul class="nav navbar-nav navbar-left">
        <li><a href="javascript:void();"><b>Created: </b>{if isset($email.author)}{$email.author.login}, {/if}{$email.created_at|date_human:false}</a></li>
        <li><a href="javascript:void();">{if isset($email.modifier)}<b>Modified: </b>{$email.modifier.login}, {$email.modified_at|date_human:false}{/if}</a></li>
    </ul>
{/if}    

