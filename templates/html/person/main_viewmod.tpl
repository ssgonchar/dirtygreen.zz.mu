<div class="row">
<div class="col-md-12">
    <div class="col-md-3"> 
            <div id="person-pic" onclick="person_change_pic();" style="cursor: pointer;">
            {if isset($person.picture)}
            {picture type="person" size="s" source=$person.picture}
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

            
                    <p class="">Created :
                    {$person.created_at|date_human:false}, {if isset($person.author)}{$person.author.login}{else}<i>author unknown</i>{/if}</p>
              
                    <p class="">Modified :
                  {$person.modified_at|date_human:false}, {if isset($person.modifier)}{$person.modifier.login}{else}<i>modifier unknown</i>{/if}</p>
             
                {if !empty($user)}
              
                    <p class="">Last Visit :
                    {if $user.last_visited_at > 0}{$user.last_visited_at|date_human:false}{else}<i>has not visited yet</i>{/if}</p>
                
                {/if}
       </div>  

            <div class="col-md-3">
            <h4>Basic Info</h4>
         
                {if !empty($person.birthday)}
               
                    <p class="">Birthday :
                   {$person.birthday|escape:'html'|date_format:'d/m/Y'}</p>
               
                {/if}
                {if !empty($person.name_for_label)}
              
                    <p class="">Name For Label : 
                   {$person.name_for_label|escape:'html'}</p>
               
                {/if}
                {if !empty($person.languages)}
              
                    <p class="">Languages :
                 {$person.languages|escape:'html'}</p>
               
                {/if}
           <h4>Contact Info</h4>
            
            {if !empty($contactdata)}
        
            
       
                {foreach from=$contactdata item=row}
                
                    <p class="">{$row.type_text} : 
                    {$row.title|escape:'html'}</p>
               
                {/foreach}
           
            {/if}
            </div>
           <div class="col-md-3"> 
            {if isset($person.company) || isset($person.department) || isset($person.jobposition)}
            
            <h4>Work</h4>
            
                {if isset($person.company)}
              
                    <p class="">Company : 
                  <a href="/company/{$person.company.id}">{$person.company.title|escape:'html'}</a></p>
                
                {/if}
                {if isset($person.department)}
              
                    <p class="">Department : 
                   {$person.department.title|escape:'html'}</p>
               
                {/if}
                {if isset($person.jobposition)}
               
                    <p class="">Position :
                    {$person.jobposition.title|escape:'html'}</p>
               
                {/if}
             
                {if !empty($person.is_key_contact)}
                    
                    
                    <p>Key Contact</p>
            
                {/if}                
           
            {/if}  
            {if isset($person.country) || isset($person.region) || isset($person.city) || !empty($person.zip) || !empty($person.address)}
            <div class="pad"></div>
            <h4>Living</h4>
           
                {if isset($person.country)}
             
                    <p class="">Country : 
                    {$person.country.title|escape:'html'}</p>
              
                {/if}
                {if isset($person.region)}
              
                    <p class="">Region :
                    {$person.region.title|escape:'html'}</p>
               
                {/if}
                {if isset($person.city)}
               
                    <p class="">City : 
                   {$person.city.title|escape:'html'}</p>
              
                {/if}
                {if !empty($person.zip)}
              
                    <p class="">Zip :
                  {$person.zip|escape:'html'}</p>
               
                {/if}
                {if !empty($person.address)}
               
                    <p class="">Address :
                   {$person.address|escape:'html'}</p>
              
                {/if}
            
            {/if}
      </div>
            <div class="col-md-3">
            <h4>Account Info</h4>
        
                {if empty($user) || empty($user.role_id)}
             
                    <p class="">Status : 
                   Access denied</p>
               
                {else}
              
                    <p class="">Login : 
                    {$user.login}</p>
               
              
                    <p class="">Password :
                   {if $smarty.session.user.role_id <= $smarty.const.ROLE_MODERATOR}{$user.password|escape:'html'}{else}<i>hidden</i>{/if}</p>
             
                    <p class="">Role : 
                  
                        {if $user.role_id == $smarty.const.ROLE_LIMITED_USER}Site User
                        {elseif $user.role_id == $smarty.const.ROLE_USER}Site & WebStock User
                        {elseif $user.role_id == $smarty.const.ROLE_STAFF}MaM Staff
                        {elseif $user.role_id == $smarty.const.ROLE_MODERATOR}MaM Moderator
                        {elseif $user.role_id == $smarty.const.ROLE_ADMIN}MaM Admin
                        {/if}
                    </p>
                
                {if ($user.role_id == $smarty.const.ROLE_USER || $user.role_id == $smarty.const.ROLE_LIMITED_USER) && !empty($user.se_access)}
            
                    <p>www.SteelEmotion.com</p>
                
                {/if}
                {if $user.role_id == $smarty.const.ROLE_USER && !empty($user.pa_access)}
              
                    <p>www.PlatesAhead.com</p>
               
                {/if}
                
                    <p class="">Status : 
                 
                        {if $user.status_id == $smarty.const.USER_INITIAL}Just Registered
                        {elseif $user.status_id == $smarty.const.USER_PENDING}Awaiting Confirmation
                        {elseif $user.status_id == $smarty.const.USER_ACTIVE}Active
                        {elseif $user.status_id == $smarty.const.USER_BLOCKED}Blocked{/if}
                    </p>
             
                {if !empty($user.nickname)}
             
                    <p class="">Nickname : 
                  {$user.nickname|escape:'html'}</p>
               
                {/if}
                {if !empty($user.color)}
             
                    <p class=""  style="color: {$user.color}">Color: {$user.color|escape:'html'}  </p>
                      
               
                {/if}
                {if !empty($user.email)}
               
                    <p class="">Reg. Email : 
                   {$user.email|escape:'html'}</p>
                
                {/if}
               
                    <p class="">In TL Icon Park : 
                  {if !empty($user.chat_icon_park)}yes{else}no{/if}</p>
               
                    <p class="">Driver : 
                  {if !empty($user.driver)}yes{else}no{/if}</p>
                      
                {if !empty($user.role_id) && $user.role_id <= $smarty.const.ROLE_STAFF}
           
                    <p class="text-top" style="height: 22px;">{$row.mailbox.title|escape:'html'}Read Mailboxes : 
                   
                        {if $user.role_id <= $smarty.const.ROLE_ADMIN}
                              All Mailboxes
                        {else}
                            {if empty($mailboxes)}
                                {''|undef}
                            {else}
                                {foreach from=$mailboxes item=row}
                              
                                {/foreach}
                            {/if}
                        {/if}
                
                    {/if}</p>
                
                {/if}
            </div>
</div>
</div>
            
            
            
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