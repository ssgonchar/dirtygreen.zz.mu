<table class="form" width="75%">
    <tr>
        <td class="form-td-title-b">Period : </td>
        <td>
            {if !empty($form.quarter)}{$form.quarter|quarter}&nbsp;{/if}{$form.year}
        </td>
    </tr>
    <tr>
        <td class="form-td-title-b text-top">Description : </td>
        <td class="text-top">{if !empty($form.description)}{$form.description|escape:'html'|nl2br}{else}<i>not set</i>{/if}</td>
    </tr>    
</table>
<div class="pad"></div>

<h3>Related BIZs</h3>
{if isset($biz_list) && !empty($biz_list)}
{else}
<i>No related BIZ</i>
{/if}