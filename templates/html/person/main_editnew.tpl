<!-- Nav tabs -->
<ul class="nav nav-tabs">
    <li class="active"><a href="#person_info" data-toggle="tab">Person info</a></li>
    <li><a href="#account_settings" data-toggle="tab">Account settings</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane fade in active" id="person_info">

        <div class="row">

            <div class="col-md-3">
                <h4>Basic Info</h4>

                <select id="title" name="person[title]" class="chosen-select">
                    <option value="">--</option>
                    <option value="dr"{if isset($person.title) && $person.title == 'dr'} selected="selected"{/if}>Dr</option>
                    <option value="miss"{if isset($person.title) && $person.title == 'miss'} selected="selected"{/if}>Miss</option>
                    <option value="mr"{if isset($person.title) && $person.title == 'mr'} selected="selected"{/if}>Mr</option>
                    <option value="mrs"{if isset($person.title) && $person.title == 'mrs'} selected="selected"{/if}>Mrs</option>
                    <option value="sr"{if isset($person.title) && $person.title == 'sr'} selected="selected"{/if}>Sr</option>
                    <option value="sra"{if isset($person.title) && $person.title == 'sra'} selected="selected"{/if}>Sra</option>
                </select>
                <input type="text" name="person[first_name]" class="form-control normal"{if isset($person.first_name)} value="{$person.first_name|escape:'html'}"{/if} placeholder="First name">
                <input type="text" name="person[middle_name]" class="form-control normal"{if isset($person.middle_name)} value="{$person.middle_name|escape:'html'}"{/if} placeholder="Middle name">
                <input type="text" name="person[last_name]" class="form-control normal"{if isset($person.last_name)} value="{$person.last_name|escape:'html'}"{/if} placeholder="Last name">

                <hr>
                <p class="">Birthday  </p>
                    <input type="text" class="form-control normal" id="birthday"</p>
                <hr>     
                <p class="">Name For Label 
                    <input type="text" name="person[name_for_label]" class="form-control normal" value="{if !empty($person.name_for_label)}{$person.name_for_label|escape:'html'}{/if}"></p>
                <hr>
                <p class="">Speaks Languages 
                    <input type="text" name="person[languages]" class="form-control normal"{if isset($person.languages)} value="{$person.languages|escape:'html'}"{/if}></p>
                <hr> 
            </div>
            <div class="col-md-3"id="cd-list" >
                <h4>Contact Info</h4>
                {foreach from=$contactdata item=row name=cd}
                    <p id="cd-{$smarty.foreach.cd.index}">                   

                        <select name="contactdata[{$smarty.foreach.cd.index}][type]" class="chosen-select">
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

                        <input type="text" class="normal" name="contactdata[{$smarty.foreach.cd.index}][title]" value="{$row.title|escape:'html'}">
                        <img src="/img/icons/plus-circle.png" onclick="add_contac();">
                        <img src="/img/icons/cross-circle.png" onclick="remove_contactdata({$smarty.foreach.cd.index});">
                    </p>

                {/foreach}                

                
                <p class="tpl-row" style="display:none;">
                    <select id="cd-type" class="">
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

                    <input type="text" id="cd-title" class="normal">
                    <img src="/img/icons/plus-circle.png" onclick="add_contac();">
                    <img src="/img/icons/cross-circle.png" onclick="remove_contac(this);">


                   <input type="hidden" class="tpl-row" value="{if isset($contact_row)}{$contact_row}{else}0{/if}">            
                </p>                 
            </div>
            <div class="col-md-3">
                <h4>Work</h4>

                <p>Company 
                    {if isset($company)}
                        <a id="company_link" href="/company/{$company.id}">{$company.title|escape:'html'}</a><img id="img_reload" src="/img/icons/reload.png" style="vertical-align: middle; margin-top: -1px; margin-left: 5px;" onclick="find_company();">
                        <input type="text" id="company_title" name="person[company_title]"{if isset($person.company_title)} value="{$person.company_title|escape:'html'}"{/if} placeholder="type free text" class="find-parametr" style="display: none;">
                    {else}
                        <a id="company_link" style="display: none;">{$company.title|escape:'html'}</a><img id="img_reload" src="/img/icons/reload.png" style="vertical-align: middle; margin-top: -1px; margin-left: 5px; display: none;" onclick="find_company();">
                        <input type="text" id="company_title" name="person[company_title]"{if isset($person.company_title)} value="{$person.company_title|escape:'html'}"{/if} placeholder="type free text" class="find-parametr">
                    {/if}
                    <input type="hidden" id="company_id" name="person[company_id]" value="{if isset($person.company_id)}{$person.company_id}{else}0{/if}"></p>
                <hr> 
                <p>Department 

                    <select name="person[department_id]" class="chosen-select normal">
                        <option value="0">--</option>
                        {foreach from=$departments item=row}
                            <option value="{$row.department.id}"{if isset($person) && isset($person.department_id) && $person.department_id == $row.department.id} selected="selected"{/if}>{$row.department.title|escape:'html'}</option>
                        {/foreach}
                    </select>                    
                </p>
                <hr>          
                <p>Position 

                    <select name="person[jobposition_id]" class="chosen-select">
                        <option value="0">--</option>
                        {foreach from=$jobpositions item=row}
                            <option value="{$row.jobposition.id}"{if isset($person) && isset($person.jobposition_id) && $person.jobposition_id == $row.jobposition.id} selected="selected"{/if}>{$row.jobposition.title|escape:'html'}</option>
                        {/foreach}                            
                    </select>                    
                </p>
                <hr>   
                <p>Key Contact  

                    <select name="person[key_contact]" class="chosen-select">
                        <option value="0"{if !isset($person.key_contact) || empty($person.key_contact)} selected="selected"{/if}>No</option>
                        <option value="1"{if isset($person.key_contact) && !empty($person.key_contact) || isset($company.id)} selected="selected"{/if}>Yes</option>
                    </select>                    
                <hr> 
            </div>
            <div class="col-md-3">
                <h4>Living</h4>
                <p>Country  

                    <select id="country" name="person[country_id]" class="chosen-select normal">
                        <option value="0">--</option>
                        {foreach from=$countries item=row}
                            {*{if $row.country.is_primary > 0}*}
                            <option value="{$row.country.id}"{if isset($person) && isset($person.country_id) && $person.country_id == $row.country.id} selected="selected"{/if}>{$row.country.title|escape:'html'}</option>
                            {*{/if}*}
                        {/foreach}
                    </select>
                </p>
                <hr> 
                <p>Region 

                    <select id="region" name="person[region_id]" class="chosen-select normal">
                        <option value="0">--</option>
                        {if isset($regions)}
                            {foreach from=$regions item=row}
                                <option value="{$row.region.id}"{if isset($person) && isset($person.region_id) && $person.region_id == $row.region.id} selected="selected"{/if}>{$row.region.title|escape:'html'}</option>
                            {/foreach}
                        {/if}
                    </select>                    
                </p>

                <hr> 
                <p>City 

                    <select id="city" name="person[city_id]" class="chosen-select normal">
                        <option value="0">--</option>
                        {if isset($cities)}
                            {foreach from=$cities item=row}
                                <option value="{$row.city.id}"{if isset($person) && isset($person.city_id) && $person.city_id == $row.city.id} selected="selected"{/if}>{$row.city.title|escape:'html'}</option>
                            {/foreach}
                        {/if}                            
                    </select>                    
                </p>
                <hr> 
                <p>Zip 
                    <input type="text" name="person[zip]" class="form-control normal"{if isset($person.zip)} value="{$person.zip|escape:'html'}"{/if}></p>
                <hr> 
                <p>Address  <input type="text" name="person[address]" class="form-control normal " value="{if !empty($person.address)}{$person.address|escape:'html'}{/if}"></p>
                <hr>                   
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="account_settings">...
        <h4>Account Info</h4>
        <div class="row">
             <div class="col-md-12">
                 <div class="col-md-3">
            
                <p class="">Login 
                
                    <input type="hidden" name="user[id]" class="form-control normal "{if isset($user.id)} value="{$user.id}"{/if}>
                    <input type="text" name="user[login]" class="form-control normal "{if isset($user.login)} value="{$user.login|escape:'html'}"{/if}>
                </p>
            
            
                <p class="">Password  
                <input type="text" name="user[password]" class="form-control normal "{if isset($user.password)} value="{$user.password|escape:'html'}"{/if}>
                </p>
         
           
                <p class="">User Role 
                
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
                </p>
            
            
                <p class="sites">
                 {if !isset($user.role_id) || ($user.role_id != $smarty.const.ROLE_USER && $user.role_id != $smarty.const.ROLE_LIMITED_USER)} {/if}
                    <label for="se-access"><input type="checkbox" id="se-access" name="user[se_access]" value="1"{if isset($user.se_access) && !empty($user.se_access)} checked="checked"{/if}> www.SteelEmotion.com</label>
                </p>
            
            
                <p class="sites">
                    {if !isset($user.role_id) || ($user.role_id != $smarty.const.ROLE_USER && $user.role_id != $smarty.const.ROLE_LIMITED_USER)}{/if}
                    <label for="pa-access"><input type="checkbox" id="pa-access" name="user[pa_access]" value="1"{if isset($user.pa_access) && !empty($user.pa_access)} checked="checked"{/if}> www.PlatesAhead.com</label>
                </p>
                
               
        
                <p class="">Account Status  
                
                    <select id="title" name="user[status_id]" class="normal">
                        <option value="0">--</option>
                        {if isset($person) && !empty($person.id)}
                            {*<option value="{$smarty.const.USER_INITIAL}"{if isset($user.status_id) && $user.status_id == $smarty.const.USER_INITIAL} selected="selected"{/if}>Just Registered</option>
                            <option value="{$smarty.const.USER_PENDING}"{if isset($user.status_id) && $user.status_id == $smarty.const.USER_PENDING} selected="selected"{/if}>Awaiting Confirmation</option>*}
                        {/if}
                        <option value="{$smarty.const.USER_ACTIVE}"{if isset($user.status_id) && $user.status_id == $smarty.const.USER_ACTIVE} selected="selected"{/if}>Active</option>
                        <option value="{$smarty.const.USER_BLOCKED}"{if isset($user.status_id) && $user.status_id == $smarty.const.USER_BLOCKED} selected="selected"{/if}>Blocked</option>
                    </select>
                </p>
            
                <p class="">Nickname 
                   <input type="text" name="user[nickname]" class="form-control normal "{if isset($user.nickname)} value="{$user.nickname|escape:'html'}"{/if}>
            </p>
          
                <p class="">Color  
                <input type="text" id="picker" name="user[color]" class="form-control normal "{if isset($user.color)} value="{$user.color}"{/if}></p>
               
          
                <p class="">Reg. Email  
               <input type="text" name="user[email]" class="form-control normal "{if isset($user.email)} value="{$user.email}"{/if}></p>
            
            <p id="chat_icon_park"{if empty($user) || empty($user.role_id)} style="display: none;"{/if}>
                <p class="form-td-title">In TL Icon Park :
                <input type="checkbox" name="user[chat_icon_park]" value="1"{if isset($user.chat_icon_park) && !empty($user.chat_icon_park)} checked="checked"{/if}></p>
            
            <p id="chat_icon_park"{if empty($user) || empty($user.role_id)} style="display: none;"{/if}>
                <p class="form-td-title">Driver : </td>
                <input type="checkbox" name="user[driver]" value="1"{if isset($user.driver) && !empty($user.driver)} checked="checked"{/if}></p>
                  
            {if $smarty.session.user.role_id <= $smarty.const.ROLE_ADMIN}
                <p id="last_email_number"{if !isset($user.role_id) || $user.role_id > $smarty.const.ROLE_STAFF} style="display: none;"{/if}>
                    <p class="">Last Email Number :
                    <input type="text" name="user[last_email_number]" class="narrow form-control normal "{if isset($user.last_email_number) && !empty($user.last_email_number)} value="{$user.last_email_number}"{/if}></p>
               
            {/if}                
                </div>
                
                <div class="col-md-3">

        <span {if empty($mailboxes_list) || empty($user.role_id) || $user.role_id > $smarty.const.ROLE_STAFF} style="display: none;"{/if}>
            <h4>Read Mailboxes</h4>
            <div style="height: 10px;">
            </div>
            <div style="padding-left: 50px; height: 20px;"><a class="semiref" onClick="togle_mailboxes(this);">Select All Mailboxes</a></div>
            {foreach $mailboxes_list as $item}
                <div style="padding-left: 50px; height: 20px;"><label><input type="checkbox" class="mailboxes" name="mailboxes_ids[]" value="{$item.id}" style="margin-right: 5px;"{if isset($item.selected)} checked="checked"{/if}>{$item.title|escape:'html'}</label></div>
                    {/foreach}
        </span>
                </div>
        <h4>Notes</h4>
        <div class="col-md-4"
            <tr>
                <td><textarea name="person[notes]" id="notes" class="max" rows="7">{if isset($person.notes)}{$person.notes|escape:'html'}{/if}</textarea></td>
            </tr>
        
    </div>
</div>
    </div>    
</div>
<table class="form" width="100%">
    <tr>
        <td width="33%" class="text-top">		

        </td>
        <td width="33%" class="text-top">

        </td>
        <td width="33%" class="text-top">

        </td>
    </tr>
    <tr>
        <td><div class="pad"></div></td>
    </tr>
    <tr>
        <td width="33%" class="text-top">

        </td>
        <td width="33%" class="text-top">

        </td>
        <td width="33%" class="text-top">

        </td>
    </tr>

    <tr id="mailboxes">
        <td width="33%" class="text-top">

        </td>
        <td width="33%" class="text-top"></td>
        <td width="33%" class="text-top"></td>
    </tr>
</table>

