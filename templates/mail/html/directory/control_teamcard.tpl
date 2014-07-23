<div style="background: #ECECEC; padding: 10px; -moz-border-radius: 4px; -webkit-border-radius: 4px; border-radius: 4px;">
    <table class="form" width="100%">
        <tr>
            <td><h4 style="font-weight: bold;">{$row.team.title|escape:'html'}</h4></td>
            <td width="30%" style="text-align: right;">
                <a href="/directory/editteam/{$row.team.id}" class="edit">edit</a>
                <a href="javascript:void(0);" class="delete" onclick="if(confirm('Am I sure ?')) location.href='/directory/deleteteam/{$row.team.id}';">delete</a>            
            </td>
        </tr>
        {if !empty($row.team.description)}
        <tr>
            <td colspan="2">{$row.team.description|escape:'html'|nl2br}</td>
        </tr>
        {/if}
        <tr>
            <td colspan="2">
            {foreach name=emails from=$row.team.emails item=email}
                <a href="mailto:{$email|escape:'html'}">{$email|escape:'html'}</a>{if !$smarty.foreach.emails.last} , {/if}
            {/foreach}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="pad1"></div>
                {foreach from=$row.team.members item=member}
                <div style="float: left; text-align: center; margin-right: 10px;">
                    {if isset($member.user.picture) && !empty($member.user.picture)}
                    {picture type="user" size="x" source=$member.user.picture style=""}
                    {else}
                    <img id="picture-{$member.user.id}" src="/img/layout/anonym.png" alt=""></img>
                    {/if}
                    <br><span style="font-weight: bold;{if !empty($member.user.color)} color:{$member.user.color};{/if}">{$member.user.login}</span>
                </div>
                {/foreach}
                <div class="separator"></div>            
            </td>
        </tr>
    </table>
</div>
<div class="pad"></div>
