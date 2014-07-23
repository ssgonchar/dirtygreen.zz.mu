<table class="form" width="100%">
    <tr>
        <td width="33%" class="text-top">
            <div id="person-pic" onclick="person_change_pic();" style="cursor: pointer;">
                {if isset($person.picture)}
                    {picture type="person" size="m" source=$person.picture}
                {else}
                    <img src="/img/nopicture{if $person.gender == 'f'}f{/if}.gif" width="350px">
                {/if}
            </div>
            <div id="person-pic-new" style="display: none;">
                <div style="margin-bottom: 10px;">New Picture</div>
                <input type="file" name="person_picture" style="height: 25px;">
                <div style="margin-top: 10px;">
                    <input type="submit" class="btn100o" value="Upload" name="btn_upload_image">
                    <a href="javascript: void(0);" onclick="person_change_pic_cancel();" style="margin-left: 10px;">cancel</a>
                </div>
            </div>
            
            <div class="pad2"></div>
            <table class="form" width="100%">                
                <tr>
                    <td class="form-td-title">Created : </td>
                    <td>{$person.created_at|date_human:false}, {if isset($person.author)}{$person.author.login}{else}<i>author unknown</i>{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Modified : </td>
                    <td>{$person.modified_at|date_human:false}, {if isset($person.modifier)}{$person.modifier.login}{else}<i>modifier unknown</i>{/if}</td>
                </tr>
                {if !empty($user)}
                <tr>
                    <td class="form-td-title">Last Visit : </td>
                    <td>{if $user.last_visited_at > 0}{$user.last_visited_at|date_human:false}{else}<i>has not visited yet</i>{/if}</td>
                </tr>
                {/if}
            </table>            
        </td>
        <td width="33%" class="text-top">
            <h4>Basic Info</h4>
            <table class="form" width="100%">
                {if !empty($person.birthday)}
                <tr>
                    <td class="form-td-title">Birthday : </td>
                    <td>{$person.birthday|escape:'html'|date_format:'d/m/Y'}</td>
                </tr>
                {/if}
                {if !empty($person.name_for_label)}
                <tr>
                    <td class="form-td-title">Name For Label : </td>
                    <td>{$person.name_for_label|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($person.languages)}
                <tr>
                    <td class="form-td-title">Languages : </td>
                    <td>{$person.languages|escape:'html'}</td>
                </tr>
                {/if}
            </table>
            
            {if !empty($contactdata)}
            <div class="pad"></div>
            <h4>Contact Info</h4>
            <table class="form" width="100%">
                {foreach from=$contactdata item=row}
                <tr>
                    <td class="form-td-title">{$row.type_text} : </td>
                    <td>{$row.title|escape:'html'}</td>
                </tr>
                {/foreach}
            </table>
            {/if}
            
            {if isset($person.company) || isset($person.department) || isset($person.jobposition)}
            <div class="pad"></div>
            <h4>Work</h4>
            <table class="form" width="100%">
                {if isset($person.company)}
                <tr>
                    <td class="form-td-title">Company : </td>
                    <td><a href="/company/{$person.company.id}">{$person.company.title|escape:'html'}</a></td>
                </tr>
                {/if}
                {if isset($person.department)}
                <tr>
                    <td class="form-td-title">Department : </td>
                    <td>{$person.department.title|escape:'html'}</td>
                </tr>
                {/if}
                {if isset($person.jobposition)}
                <tr>
                    <td class="form-td-title">Position : </td>
                    <td>{$person.jobposition.title|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($person.is_key_contact)}
                <tr>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="form-td-title"></td>
                    <td>Key Contact</td>
                </tr>
                {/if}                
            </table>
            {/if}            
        </td>
        <td width="33%" class="text-top">
            <h4>Account Info</h4>
            <table class="form" width="100%">
                {if empty($user) || empty($user.role_id)}
                <tr>
                    <td class="form-td-title">Status : </td>
                    <td>Access denied</td>
                </tr>
                {else}
                <tr>
                    <td class="form-td-title">Login : </td>
                    <td>{$user.login}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Password : </td>
                    <td>{if $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR}{$user.password|escape:'html'}{else}<i>hidden</i>{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Role : </td>
                    <td>
                        {if $user.role_id == $smarty.const.ROLE_LIMITED_USER}Site User
                        {elseif $user.role_id == $smarty.const.ROLE_USER}Site & WebStock User
                        {elseif $user.role_id == $smarty.const.ROLE_STAFF}MaM Staff
                        {elseif $user.role_id == $smarty.const.ROLE_MODERATOR}MaM Moderator
                        {elseif $user.role_id == $smarty.const.ROLE_ADMIN}MaM Admin
                        {/if}
                    </td>
                </tr>
                {if ($user.role_id == $smarty.const.ROLE_USER || $user.role_id == $smarty.const.ROLE_LIMITED_USER) && !empty($user.se_access)}
                <tr>
                    <td></td>
                    <td>www.SteelEmotion.com</td>
                </tr>
                {/if}
                {if $user.role_id == $smarty.const.ROLE_USER && !empty($user.pa_access)}
                <tr>
                    <td></td>
                    <td>www.PlatesAhead.com</td>
                </tr>
                {/if}
                <tr>
                    <td class="form-td-title">Status : </td>
                    <td>
                        {if $user.status_id == $smarty.const.USER_INITIAL}Just Registered
                        {elseif $user.status_id == $smarty.const.USER_PENDING}Awaiting Confirmation
                        {elseif $user.status_id == $smarty.const.USER_ACTIVE}Active
                        {elseif $user.status_id == $smarty.const.USER_BLOCKED}Blocked{/if}
                    </td>
                </tr>
                {if !empty($user.nickname)}
                <tr>
                    <td class="form-td-title">Nickname : </td>
                    <td>{$user.nickname|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($user.color)}
                <tr>
                    <td class="form-td-title">Color : </td>
                    <td style="color: {$user.color}">{$user.color|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($user.email)}
                <tr>
                    <td class="form-td-title">Reg. Email : </td>
                    <td>{$user.email|escape:'html'}</td>
                </tr>
                {/if}
                <tr>
                    <td class="form-td-title">In TL Icon Park : </td>
                    <td>{if !empty($user.chat_icon_park)}yes{else}no{/if}</td>
                </tr>
                <tr>
                    <td class="form-td-title">Driver : </td>
                    <td>{if !empty($user.driver)}yes{else}no{/if}</td>
                </tr>                
                {if !empty($user.role_id) && $user.role_id <= $smarty.const.ROLE_STAFF}
                <tr>
                    <td class="form-td-title text-top">Read Mailboxes : </td>
                    <td>
                        {if $user.role_id <= $smarty.const.ROLE_ADMIN}
                        All Mailboxes
                        {else}
                            {if empty($mailboxes)}
                                {''|undef}
                            {else}
                                {foreach from=$mailboxes item=row}
                                <div style="height: 22px;">{$row.mailbox.title|escape:'html'}</div>
                                {/foreach}
                            {/if}
                        {/if}
                    </td>
                </tr>
                {/if}
                {/if}
            </table>
            
            {if isset($person.country) || isset($person.region) || isset($person.city) || !empty($person.zip) || !empty($person.address)}
            <div class="pad"></div>
            <h4>Living</h4>
            <table class="form" width="100%">
                {if isset($person.country)}
                <tr>
                    <td class="form-td-title">Country : </td>
                    <td>{$person.country.title|escape:'html'}</td>
                </tr>
                {/if}
                {if isset($person.region)}
                <tr>
                    <td class="form-td-title">Region : </td>
                    <td>{$person.region.title|escape:'html'}</td>
                </tr>
                {/if}
                {if isset($person.city)}
                <tr>
                    <td class="form-td-title">City : </td>
                    <td>{$person.city.title|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($person.zip)}
                <tr>
                    <td class="form-td-title">Zip : </td>
                    <td>{$person.zip|escape:'html'}</td>
                </tr>
                {/if}
                {if !empty($person.address)}
                <tr>
                    <td class="form-td-title">Address : </td>
                    <td>{$person.address|escape:'html'}</td>
                </tr>
                {/if}
            </table>
            {/if}
            
            {if !empty($person.notes)}
            <div class="pad"></div>
            <h4>Notes</h4>
            <table class="form" width="100%">
                <tr>
                    <td>{$person.notes|escape:'html'}</td>
                </tr>
            </table>
            {/if}            
        </td>
    </tr>
</table>

<div class="pad"></div>
{include file='templates/controls/object_shared_files.tpl' object_alias='person' object_id=$person.id}