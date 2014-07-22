<table width="100%">
    <tr>
        <td width="50%" style="vertical-align: top;">
            <table width="100%" class="form">
                <tr>
                    <td class="form-td-title-b">Title</td>
                    <td><input type="text" name="form[title]" class="wide"{if isset($form) && !empty($form.title)} value="{$form.title|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Email</td>
                    <td><input type="text" name="form[email]" class="wide"{if isset($form) && !empty($form.email)} value="{$form.email|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title" style="vertical-align: top;">Description</td>
                    <td><textarea name="form[description]" rows="5" class="wide">{if isset($form) && !empty($form.description)}{$form.description|escape:'html'}{/if}</textarea></td>
                </tr>                
            </table>
        </td>
        <td width="50%" style="vertical-align: top;">
            <h4>Team Players</h4>
            <div class="pad1"></div>
            
            {foreach from=$mam item=row}
            <div style="float: left; text-align: center; margin: 0 10px 10px 0;">
                <img id="picture-{$row.user.id}" src="/img/layout/anonym.png" alt="" width="50px" height="50px" class="{if isset($row.selected)}user-pic-s{else}user-pic{/if}" onclick="check_user({$row.user.id});"></img>
                <br><span style="font-weight: bold;{if !empty($row.user.color)} color:{$row.user.color};{/if}">{$row.user.login}</span>
                <input type="hidden" id="user-{$row.user.id}" name="selected_users[{$row.user.id}]" value="{if isset($row.selected)}1{else}0{/if}">
            </div>
            {/foreach}
        </td>
    </tr>
</table>