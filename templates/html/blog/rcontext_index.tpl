<h2 style="margin-top: 5px;">Filter</h2>

<table class="form" width="100%">
    <tr>
        <td>Keyword : </td>
    </tr>
    <tr>
        <td>
            <input class="max" type="text" id="blog-keyword" name="form[keyword]"{if isset($filter) && !empty($filter.keyword)} value="{$filter.keyword|escape:'html'}"{/if} />
        </td>
    </tr>
    <tr>
        <td>Period from : </td>
    </tr>
    <tr>
        <td>
            <input class="narrow datepicker" type="text" id="blog-datefrom" name="form[date_from]"{if isset($filter) && !empty($filter.datefrom)} value="{$filter.datefrom|date_format:'d/m/Y'}"{/if} />
        </td>
    </tr>
    <tr>
        <td>Period to : </td>
    </tr>
    <tr>
        <td>
            <input class="narrow datepicker" type="text" id="blog-dateto" name="form[date_to]"{if isset($filter) && !empty($filter.dateto)} value="{$filter.dateto|date_format:'d/m/Y'}"{/if} />
        </td>
    </tr>
    <tr>
        <td>
            <div class="pad1"></div>
            <label for="type-1"><input type="checkbox" id="type-1" name="form[type][1]" value="1" style="vertical-align:-2px; margin-right: 5px;"{if isset($filter) && isset($filter.type) && isset($filter.type[1])} checked="checked"{/if}>Only eMails</label><br><br>
            <label for="type-2"><input type="checkbox" id="type-2" name="form[type][2]" value="2" style="vertical-align:-2px; margin-right: 5px;"{if isset($filter) && isset($filter.type) && isset($filter.type[2])} checked="checked"{/if}>Only TL messages</label><br><br>
            <label for="type-3"><input type="checkbox" id="type-3" name="form[type][3]" value="3" style="vertical-align:-2px; margin-right: 5px;"{if isset($filter) && isset($filter.type) && isset($filter.type[3])} checked="checked"{/if}>Only shared files</label>        
        </td>
    </tr>    

    {if $object_alias == 'biz' && isset($biz) && empty($biz.biz.parent_id)}
    <tr>
        <td>
            <div class="pad1"></div>
            <label for="subbiz"><input type="checkbox" id="subbiz" name="form[subbiz]" value="1" style="vertical-align:-2px; margin-right: 5px;"{if isset($filter) && isset($filter.subbiz)} checked="checked"{/if}>Include subBIZ</label>
        </td>
    </tr>
    {/if}
	{if $object_alias == 'biz' && isset($biz) && $biz.biz.parent_id>0}
    <tr>
        <td>
            <div class="pad1"></div>
            <label for="mainbiz"><input type="checkbox" id="mainbiz" name="form[mainbiz]" value="1" style="vertical-align:-2px; margin-right: 5px;"{if isset($filter) && isset($filter.mainbiz)} checked="checked"{/if}>Include mainBIZ</label>
        </td>
    </tr>    
	<tr>
        <td>
            <div class="pad1"></div>
            <label for="allsubbiz"><input type="checkbox" id="allsubbiz" name="form[allsubbiz]" value="1" style="vertical-align:-2px; margin-right: 5px;"{if isset($filter) && isset($filter.allsubbiz)} checked="checked"{/if}>Include All subBIZ</label>
        </td>
    </tr>	
	{/if}
    <tr>
        <td>
            <div class="pad1"></div>
            <input class="btn150o" type="submit" name="btn_filter" value="Select" />
        </td>
    </tr>
    {if isset($filter)}
    <tr>
        <td>
            <a href="javascript: void(0);" onclick="blog_clear_filter();">clear filter</a>
        </td>
    </tr>
    {/if}
</table>