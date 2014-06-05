{if !isset($hide_first_option)}<option value="-1"{if !isset($selected) || empty($selected)} selected="selected"{/if}>{if !empty($first_option_name)}{$first_option_name|escape:'html'}{else}--{/if}</option>{/if}
{if isset($list) && !empty($list)}
{foreach $list as $item}
    <option value="{$item.id}"{if isset($selected) && $item.id == $selected} selected="selected"{/if}>
        {if isset($tree) && $tree == true && isset($item.level)}{custom_repeat symbol="&middot;&nbsp;" count=$item.level}{/if}{$item.name|escape:'html'}
    </option>
{/foreach}
{/if}