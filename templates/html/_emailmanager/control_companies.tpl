<table class="table table-bordered" style="margin-left: 15px;">
    <thead>
        <tr class="top-table active" style="width: 50%;">
            <th class="company-name emailmanager-sidebar-toolbar-header">Company name</th>
            <th class="emailmanager-sidebar-toolbar-header">
                Emails
                <span class="select-all btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-ok"></i></span>
                <span class="unselect-all btn btn-default btn-xs pull-right"><i class="glyphicon glyphicon-remove"></i></span>
            </th>
        </tr>
    </thead>
    {*debug*}
    
    <tbody>
        {foreach from=$companies_list item=row name="list"}
            <tr>
                <!-- Company title -->
                <td style="width: 50%; background-color: #f5f5f5;">
                    <a href="/company/{$row.company_id}">
                        {$row.company_title}
                    </a>
                    <span class="email-search-keyword hidden" style="border: solid 1px black;"></span>
                </td>
                <!-- Email adress -->
                <td style="background-color: #f5f5f5;">
                    <!-- From company contacts -->
                    {if isset($row.company_contacts)}
                        <ul class="company-emails">Company emails: 
                            {foreach name="subrow" from=$row.company_contacts item=subrow}
                                <li  style="border: solid 1px #ccc; cursor: pointer; margin-top: 5px; padding-left: 5px; padding-bottom: 3px; background-color: #eeeeee;">
                                    <span class="email-adress">{$subrow}</span>
                                    <input type="checkbox" class="pull-right" style="margin-right: 5px;">
                                    <span class="company-id" style="display: none;">{$row.company_id}</span>
                                </li>
                            {/foreach}
                        </ul>
                    {/if}
                    <!-- From personal contacts -->
                    {if isset($row.personal_contacts)}
                        <ul class="personal-emails">{$row.personal_contacts.name}: 
                            {foreach name='subrow' from=$row.personal_contacts.email key=key item=subrow}
                                <li style="border: solid 1px #ccc; cursor: pointer; margin-top: 5px; padding-left: 5px; padding-bottom: 3px; background-color: #eeeeee;">
                                    <span class="email-adress">{$subrow}</span>
                                    <input type="checkbox" class="pull-right" style="margin-right: 5px;">
                                    <span class="company-id" style="display: none;">{$row.company_id}</span>
                                </li>
                            {/foreach}
                        </ul>
                    {/if}
                </td>
            </tr>
        {/foreach} 
    </tbody>
    
    {*
    <tbody style="border: 1px solid black;">
        {$counter = 1}
        {foreach from=$companies_list item=row name="list"}
            {$display = 'no'} <!-- Идентификатор. Если у компании есть емэйлы, то принимает значение "yes" -->
            {if isset($row.company.key_contact) && !empty($row.company.key_contact)}
                {foreach name="kcc" from=$row.company.key_contact_contacts item=kcc}
                    {if $kcc.type == 'email'}
                        {$display = 'yes'}
                    {/if}
                {/foreach}
            {elseif isset($row.companycontacts) && !empty($row.companycontacts)}
                {foreach name='contacts' from=$row.companycontacts item=contact}
                    {if $contact.type == 'email'}
                        {$display = 'yes'}
                    {/if}                    
                {/foreach}
            {/if}
            {if $display == 'yes'}
                <tr style="border: 1px solid black;">
                    <td style="border: 1px solid black;">
                        {$counter++}
                    </td>
                    <!-- Company title -->
                    <td style="border: 1px solid black;">
                        <a href="/company/{$row.company.id}">
                            {$row.company.title}
                            {if !empty($row.company.title_short)}
                                {$row.company.title_short}
                            {/if}
                            {if !empty($row.company.title_trade)}
                                {$row.company.title_trade}
                            {/if}
                        </a>
                    </td>
                    <!-- Email adress -->
                    <td style='width: 35%; border: 1px solid black;'>
                    <!-- Person email -->
                        {if isset($row.company.key_contact) && !empty($row.company.key_contact)}
                            {foreach name="kcc" from=$row.company.key_contact_contacts item=kcc}
                                {if $kcc.type == 'email'}
                                        <input class="company-id-hidden hidden" value="{$row.company.id}">
                                        <span class="person-email hidden">{$kcc.title}</span>
                                    <nobr>
                                        <a style="color: black;" href="/email/compose/company:{$row.company.id};person:{$row.company.key_contact.id};recipient:{$kcc.title}">
                                            {$kcc.title}
                                        </a>
                                        <input type="checkbox" data-checked="true" class="company-select pull-right" data-email="{$row.company.id}">
                                    </nobr>
                                        <br>
                                {/if}
                            {/foreach}
                    <!-- Company email -->
                        {elseif isset($row.companycontacts) && !empty($row.companycontacts)}
                            {foreach name='contacts' from=$row.companycontacts item=contact}
                                {if $contact.type == 'email'}
                                        <input class="company-id-hidden hidden" value="{$row.company.id}">
                                        <span class="company-email hidden">{$contact.title}</span>
                                    <nobr>
                                        <a style="color: black;" href="/email/compose/company:{$row.company.id};recipient:{$contact.title}">
                                            {$contact.title}
                                        </a>
                                        <input type="checkbox" data-checked="true" class="company-select pull-right" data-email="{$row.company.id}"> 
                                    </nobr>
                                        <br>
                                {/if}                    
                            {/foreach}
                        {/if}
                    </td>
                </tr>
            {/if}
        {/foreach} 
    </tbody> *}
</table>