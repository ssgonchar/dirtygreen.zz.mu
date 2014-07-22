<table class="form" width="100%">
    <tr>
        <td width="33%" class="text-top">
            <h4>Basic Info</h4>
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Title : </td>
                    <td>
                        <select id="title" name="person[title]" class="narrow">
                            <option value="">--</option>
                            <option value="dr"{if isset($person.title) && $person.title == 'dr'} selected="selected"{/if}>Dr</option>
                            <option value="miss"{if isset($person.title) && $person.title == 'miss'} selected="selected"{/if}>Miss</option>
                            <option value="mr"{if isset($person.title) && $person.title == 'mr'} selected="selected"{/if}>Mr</option>
                            <option value="mrs"{if isset($person.title) && $person.title == 'mrs'} selected="selected"{/if}>Mrs</option>
                            <option value="sr"{if isset($person.title) && $person.title == 'sr'} selected="selected"{/if}>Sr</option>
                            <option value="sra"{if isset($person.title) && $person.title == 'sra'} selected="selected"{/if}>Sra</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">First Name : </td>
                    <td><input type="text" name="person[first_name]" class="max"{if isset($person.first_name)} value="{$person.first_name|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title">Middle Name : </td>
                    <td><input type="text" name="person[middle_name]" class="max"{if isset($person.middle_name)} value="{$person.middle_name|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Last Name : </td>
                    <td><input type="text" name="person[last_name]" class="max"{if isset($person.last_name)} value="{$person.last_name|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title">Birthday : </td>
                    <td><input type="text" id="birthday" name="person[birthday]" class="max" value="{if !empty($person.birthday)}{$person.birthday|escape:'html'|date_format:'d/m/Y'}{/if}"></td>
                </tr>
                <tr>
                    <td class="form-td-title">Name For Label : </td>
                    <td><input type="text" name="person[name_for_label]" class="max" value="{if !empty($person.name_for_label)}{$person.name_for_label|escape:'html'}{/if}"></td>
                </tr>
                <tr>
                    <td class="form-td-title">Speeking Languages : </td>
                    <td><input type="text" name="person[languages]" class="max"{if isset($person.languages)} value="{$person.languages|escape:'html'}"{/if}></td>
                </tr>
            </table>
        </td>
        <td width="33%" class="text-top">
            <h4>Contact Info</h4>
            <table class="form" width="100%" id="cd-list">
                {foreach from=$contactdata item=row name=cd}
                <tr id="cd-{$smarty.foreach.cd.index}">                   
                    <td>
                        <select name="contactdata[{$smarty.foreach.cd.index}][type]" class="max">
                            <option value="aim"{if $row.type == 'aim'} selected="selected"{/if}>AIM</option>
                            <option value="cell"{if $row.type == 'cell'} selected="selected"{/if}>Cell Phone</option>                            
                            <option value="email"{if $row.type == 'email'} selected="selected"{/if}>Email</option>
                            <option value="fax"{if $row.type == 'fax'} selected="selected"{/if}>Fax</option>                            
                            <option value="fb"{if $row.type == 'fb'} selected="selected"{/if}>FaceBook</option>
                            <option value="gt"{if $row.type == 'gt'} selected="selected"{/if}>Google Talk</option>
                            <option value="icq"{if $row.type == 'icq'} selected="selected"{/if}>ICQ</option>
                            <option value="msn"{if $row.type == 'msn'} selected="selected"{/if}>MSN</option>
{*                            
                            <option value="pfax"{if $row.type == 'pfax'} selected="selected"{/if}>Phone / Fax</option>    
*}                            
                            <option value="phone"{if $row.type == 'phone'} selected="selected"{/if}>Phone</option>
                            <option value="skype"{if $row.type == 'skype'} selected="selected"{/if}>Skype</option>                            
{*                            
                            <option value="telex"{if $row.type == 'telex'} selected="selected"{/if}>Telex</option>
                            <option value="ttype"{if $row.type == 'ttype'} selected="selected"{/if}>Teltype</option>
*}                            
                            <option value="www"{if $row.type == 'www'} selected="selected"{/if}>Website</option>
                        </select>
                        <input type="hidden" name="contactdata[{$smarty.foreach.cd.index}][id]" value="{$row.id}">
                    </td>
                    <td><input type="text" class="max dc-titles" name="contactdata[{$smarty.foreach.cd.index}][title]" value="{$row.title|escape:'html'}"></td>
                    <td><img src="/img/icons/cross-circle.png" onclick="remove_contactdata({$smarty.foreach.cd.index});"></td>
                </tr>                
                {/foreach}                
                <tr>                    
                    <td class="form-td-title">
                        <select id="cd-type" class="max">
                            <option value="aim">AIM</option>
                            <option value="bbm">BBM</option>
                            <option value="cell">Cell Phone</option>                            
                            <option value="email">Email</option>
                            <option value="fax">Fax</option>                            
                            <option value="fb">FaceBook</option>
                            <option value="gt">Google Talk</option>
                            <option value="icq">ICQ</option>
                            <option value="msn">MSN</option>
                            <option value="phone">Phone</option>
                            <option value="qq">QQ</option>
                            <option value="skype">Skype</option>                            
                            <option value="www">Website</option>
                        </select>
                    </td>
                    <td><input type="text" id="cd-title" class="max"></td>
                    <td><img src="/img/icons/plus-circle.png" onclick="add_contactdata();"></td>
                </tr>
            </table>
            <input type="hidden" id="cd-index" value="{if isset($cd_index)}{$cd_index}{else}0{/if}">
        </td>
        <td width="33%" class="text-top">
            <h4>Account Info</h4>
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Login : </td>
                    <td>
                        <input type="hidden" name="user[id]" class="normal"{if isset($user.id)} value="{$user.id}"{/if}>
                        <input type="text" name="user[login]" class="normal"{if isset($user.login)} value="{$user.login|escape:'html'}"{/if}>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Password : </td>
                    <td><input type="text" name="user[password]" class="normal"{if isset($user.password)} value="{$user.password|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title">User Role : </td>
                    <td>
                        <select id="title" name="user[role_id]" class="normal" onchange="select_role(this.value, {$smarty.const.ROLE_USER});">
                            <option value="0">--</option>
                            {*<option value="{$smarty.const.ROLE_LIMITED_USER}"{if isset($user.role_id) && $user.role_id == $smarty.const.ROLE_LIMITED_USER} selected="selected"{/if}>Limited User</option>*}
                            <option value="{$smarty.const.ROLE_LIMITED_USER}"{if isset($user.role_id) && $user.role_id == $smarty.const.ROLE_LIMITED_USER} selected="selected"{/if}>Site User</option>
                            <option value="{$smarty.const.ROLE_USER}"{if isset($user.role_id) && $user.role_id == $smarty.const.ROLE_USER} selected="selected"{/if}>Site & WebStock User</option>
                            <option value="0">--</option>
                            <option value="{$smarty.const.ROLE_STAFF}"{if isset($user.role_id) && $user.role_id == $smarty.const.ROLE_STAFF} selected="selected"{/if}>MaM Staff</option>
                            {if $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR}                            
                            <option value="{$smarty.const.ROLE_MODERATOR}"{if isset($user.role_id) && $user.role_id == $smarty.const.ROLE_MODERATOR} selected="selected"{/if}>MaM Moderator</option>
                            {if $smarty.session.user.role_id <= $smarty.const.ROLE_ADMIN}                            
                            <option value="{$smarty.const.ROLE_ADMIN}"{if isset($user.role_id) && $user.role_id == $smarty.const.ROLE_ADMIN} selected="selected"{/if}>MaM Admin</option>
                            {/if}
                            {/if}
                        </select>
                    </td>
                </tr>
                <tr class="sites"{if !isset($user.role_id) || ($user.role_id != $smarty.const.ROLE_USER && $user.role_id != $smarty.const.ROLE_LIMITED_USER)} style="display: none;"{/if}>
                    <td class="form-td-title"></td>
                    <td>
                        <label for="se-access"><input type="checkbox" id="se-access" name="user[se_access]" value="1"{if isset($user.se_access) && !empty($user.se_access)} checked="checked"{/if}> www.SteelEmotion.com</label>
                    </td>
                </tr>
                <tr class="sites"{if !isset($user.role_id) || ($user.role_id != $smarty.const.ROLE_USER && $user.role_id != $smarty.const.ROLE_LIMITED_USER)} style="display: none;"{/if}>
                    <td class="form-td-title"></td>
                    <td>
                        <label for="pa-access"><input type="checkbox" id="pa-access" name="user[pa_access]" value="1"{if isset($user.pa_access) && !empty($user.pa_access)} checked="checked"{/if}> www.PlatesAhead.com</label>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Account Status : </td>
                    <td>
                        <select id="title" name="user[status_id]" class="normal">
                            <option value="0">--</option>
                            {if isset($person) && !empty($person.id)}
                            <option value="{$smarty.const.USER_INITIAL}"{if isset($user.status_id) && $user.status_id == $smarty.const.USER_INITIAL} selected="selected"{/if}>Just Registered</option>
                            <option value="{$smarty.const.USER_PENDING}"{if isset($user.status_id) && $user.status_id == $smarty.const.USER_PENDING} selected="selected"{/if}>Awaiting Confirmation</option>
                            {/if}
                            <option value="{$smarty.const.USER_ACTIVE}"{if isset($user.status_id) && $user.status_id == $smarty.const.USER_ACTIVE} selected="selected"{/if}>Active</option>
                            <option value="{$smarty.const.USER_BLOCKED}"{if isset($user.status_id) && $user.status_id == $smarty.const.USER_BLOCKED} selected="selected"{/if}>Blocked</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Nickname : </td>
                    <td><input type="text" name="user[nickname]" class="normal"{if isset($user.nickname)} value="{$user.nickname|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title">Color : </td>
                    <td><input type="text" name="user[color]" class="normal"{if isset($user.color)} value="{$user.color}"{/if}></td>
                </tr>                
                <tr>
                    <td class="form-td-title">Reg. Email : </td>
                    <td><input type="text" name="user[email]" class="normal"{if isset($user.email)} value="{$user.email}"{/if}></td>
                </tr>
                <tr id="chat_icon_park"{if empty($user) || empty($user.role_id)} style="display: none;"{/if}>
                    <td class="form-td-title">In TL Icon Park : </td>
                    <td><input type="checkbox" name="user[chat_icon_park]" value="1"{if isset($user.chat_icon_park) && !empty($user.chat_icon_park)} checked="checked"{/if}></td>
                </tr>
                {if $smarty.session.user.role_id <= $smarty.const.ROLE_ADMIN}
                <tr id="last_email_number"{if !isset($user.role_id) || $user.role_id > $smarty.const.ROLE_STAFF} style="display: none;"{/if}>
                    <td class="form-td-title">Last Email Number : </td>
                    <td><input type="text" name="user[last_email_number]" class="narrow"{if isset($user.last_email_number) && !empty($user.last_email_number)} value="{$user.last_email_number}"{/if}></td>
                </tr>
                {/if}                
            </table>        
        </td>
    </tr>
    <tr>
        <td><div class="pad"></div></td>
    </tr>
    <tr>
        <td width="33%" class="text-top">
            <h4>Work</h4>
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Company : </td>
                    <td>
                        {if isset($company)}
                        <a id="company_link" href="/company/{$company.id}">{$company.title|escape:'html'}</a><img id="img_reload" src="/img/icons/reload.png" style="vertical-align: middle; margin-top: -1px; margin-left: 5px;" onclick="find_company();">
                        <input type="text" id="company_title" name="person[company_title]"{if isset($person.company_title)} value="{$person.company_title|escape:'html'}"{/if} class="max" style="display: none;">
                        {else}
                        <a id="company_link" style="display: none;">{$company.title|escape:'html'}</a><img id="img_reload" src="/img/icons/reload.png" style="vertical-align: middle; margin-top: -1px; margin-left: 5px; display: none;" onclick="find_company();">
                        <input type="text" id="company_title" name="person[company_title]"{if isset($person.company_title)} value="{$person.company_title|escape:'html'}"{/if} class="max">
                        {/if}
                        <input type="hidden" id="company_id" name="person[company_id]" value="{if isset($person.company_id)}{$person.company_id}{else}0{/if}">
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Department : </td>
                    <td>
                        <select name="person[department_id]" class="max">
                            <option value="0">--</option>
                            {foreach from=$departments item=row}
                            <option value="{$row.department.id}"{if isset($person) && isset($person.department_id) && $person.department_id == $row.department.id} selected="selected"{/if}>{$row.department.title|escape:'html'}</option>
                            {/foreach}
                        </select>                    
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Position : </td>
                    <td>
                        <select name="person[jobposition_id]" class="max">
                            <option value="0">--</option>
                            {foreach from=$jobpositions item=row}
                            <option value="{$row.jobposition.id}"{if isset($person) && isset($person.jobposition_id) && $person.jobposition_id == $row.jobposition.id} selected="selected"{/if}>{$row.jobposition.title|escape:'html'}</option>
                            {/foreach}                            
                        </select>                    
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Key Contact : </td>
                    <td>
                        <select name="person[key_contact]" class="narrow">
                            <option value="0"{if !isset($person.key_contact) || empty($person.key_contact)} selected="selected"{/if}>No</option>
                            <option value="1"{if isset($person.key_contact) && !empty($person.key_contact) || isset($company.id)} selected="selected"{/if}>Yes</option>
                        </select>                    
                    </td>
                </tr>
            </table>            
        </td>
        <td width="33%" class="text-top">
            <h4>Living</h4>
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title">Country : </td>
                    <td>
                        <select id="country" name="person[country_id]" class="max">
                            <option value="0">--</option>
                            {foreach from=$countries item=row}{if $row.country.is_primary > 0}
                            <option value="{$row.country.id}"{if isset($person) && isset($person.country_id) && $person.country_id == $row.country.id} selected="selected"{/if}>{$row.country.title|escape:'html'}</option>
                            {/if}{/foreach}
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Region : </td>
                    <td>
                        <select id="region" name="person[region_id]" class="max">
                            <option value="0">--</option>
                            {if isset($regions)}
                            {foreach from=$regions item=row}
                            <option value="{$row.region.id}"{if isset($person) && isset($person.region_id) && $person.region_id == $row.region.id} selected="selected"{/if}>{$row.region.title|escape:'html'}</option>
                            {/foreach}
                            {/if}
                        </select>                    
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">City : </td>
                    <td>
                        <select id="city" name="person[city_id]" class="max">
                            <option value="0">--</option>
                            {if isset($cities)}
                            {foreach from=$cities item=row}
                            <option value="{$row.city.id}"{if isset($person) && isset($person.city_id) && $person.city_id == $row.city.id} selected="selected"{/if}>{$row.city.title|escape:'html'}</option>
                            {/foreach}
                            {/if}                            
                        </select>                    
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title">Zip : </td>
                    <td><input type="text" name="person[zip]" class="max"{if isset($person.zip)} value="{$person.zip|escape:'html'}"{/if}></td>
                </tr>
                <tr>
                    <td class="form-td-title">Address : </td>
                    <td><input type="text" name="person[address]" class="max" value="{if !empty($person.address)}{$person.address|escape:'html'}{/if}"></td>
                </tr>
            </table>
        </td>
        <td width="33%" class="text-top">
            <h4>Notes</h4>
            <table class="form" width="100%">
                <tr>
                    <td><textarea name="person[notes]" class="max" rows="7">{if isset($person.notes)}{$person.notes|escape:'html'}{/if}</textarea></td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr id="mailboxes"{if empty($mailboxes_list) || empty($user.role_id) || $user.role_id > $smarty.const.ROLE_STAFF} style="display: none;"{/if}>
        <td width="33%" class="text-top">
            <h4>Read Mailboxes</h4>
            <div style="height: 10px;">
			</div>
			<div style="padding-left: 50px; height: 20px;"><a class="semiref" onClick="togle_mailboxes(this);">Select All Mailboxes</a></div>
            {foreach $mailboxes_list as $item}
            <div style="padding-left: 50px; height: 20px;"><label><input type="checkbox" class="mailboxes" name="mailboxes_ids[]" value="{$item.id}" style="margin-right: 5px;"{if isset($item.selected)} checked="checked"{/if}>{$item.title|escape:'html'}</label></div>
            {/foreach}
        </td>
        <td width="33%" class="text-top"></td>
        <td width="33%" class="text-top"></td>
    </tr>
</table>