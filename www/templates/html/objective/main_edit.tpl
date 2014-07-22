<table class="form" width="75%">
    <tr>
        <td class="form-td-title-b">Year : </td>
        <td>
        {if isset($form.year) && $form.year < $year}
            <input type="hidden" name="form[year]" value="{$form.year}">{$form.year}
        {else}
            <select name="form[year]" class="narrow">
                <option value="{$year}"{if isset($form.year) && $form.year == $year} selected="selected"{/if}>{$year}</option>
                <option value="{$year+1}"{if isset($form.year) && $form.year == $year+1} selected="selected"{/if}>{$year+1}</option>
            </select>
        {/if}
        </td>
    </tr>
    <tr>
        <td class="form-td-title-b">Quarter : </td>
        <td>
        {if isset($form.year) && $form.year < $year}
            <input type="hidden" name="form[quarter]" value="{$form.quarter}">{if empty($form.quarter)}full year{else}{$form.quarter|quarter}{/if}
        {else}
            <select name="form[quarter]" class="narrow">
                <option value="0">full year</option>
                <option value="1"{if isset($form.quarter) && $form.quarter == 1} selected="selected"{/if}>{1|quarter}</option>
                <option value="2"{if isset($form.quarter) && $form.quarter == 2} selected="selected"{/if}>{2|quarter}</option>
                <option value="3"{if isset($form.quarter) && $form.quarter == 3} selected="selected"{/if}>{3|quarter}</option>
                <option value="4"{if isset($form.quarter) && $form.quarter == 4} selected="selected"{/if}>{4|quarter}</option>
            </select>
        {/if}
        </td>
    </tr>    
    <tr>
        <td class="form-td-title-b">Title : </td>
        <td><input type="text" name="form[title]" class="max"{if isset($form.title) && !empty($form.title)} value="{$form.title|escape:'html'}"{/if}></td>
    </tr>    
    <tr>
        <td class="form-td-title text-top">Description : </td>
        <td class="text-top"><textarea name="form[description]" class="max" rows="10">{if isset($form.description) && !empty($form.description)}{$form.description|escape:'html'}{/if}</textarea></td>
    </tr>    
</table>