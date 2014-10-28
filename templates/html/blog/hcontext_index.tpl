<div class="pad"></div>
<table class="form" style="width: 100%;">
    <tr>
        <td><input type="text" name="form[keyword]" class="max"{if !empty($filter.keyword)} value="{$filter.keyword|escape:'html'}"{/if}></td>
        <td><input type="submit" name="btn_filter" value="Find" class="btn btn-primary"></td>
    </tr>
</table>
<div class="pad1"></div>