<table class="form" width="100%">
    <tr>
        <td width="50%" style="vertical-align: top;">
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b">Team</td>
                    <td>{$form.team.title|escape:'html'}</td>
                </tr>
                {if isset($form.parent) && !empty($form.parent)}
                <tr>
                    <td class="form-td-title">Product Group</td>
                    <td>{$form.parent.title|escape:'html'}</td>
                </tr>
                {/if}
                <tr>
                    <td class="form-td-title" style="vertical-align: top;">Description</td>
                    <td>{if isset($form.description)}{$form.description|escape:'html'|nl2br}{/if}</td>
                </tr>
            </table>            
        </td>
        <td width="50%" style="vertical-align: top;">
            <h4>Tariff Codes</h4>
            <table class="list" width="100%" id="tc-list">
            <tbody>
                <tr class="top-table">
                    <th width="40%">Code</th>
                    <th width="40%">Description</th>
                <tr>
                {foreach from=$tariffcodes item=row name=tc}
                <tr>
                    <td>{$row.title|escape:'html'}</td>
                    <td>{$row.description|escape:'html'}</td>
                </tr>                
                {/foreach}
            </tbody>                    
            </table>
            
            <div class="pad"></div>
            <h4>Pictures</h4>
            <i>no pictures</i>
        </td>
    </tr>
</table>