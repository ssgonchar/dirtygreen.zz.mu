<ul class="product-item">
    {foreach from=$products item=product}
        {if isset($product.product.children) && !empty($product.product.children)}
        <li><img id="img-{$product.product.id}" src="/img/icons/plus-white.png" style="vertical-align: -3px;" onclick="expand_node({$product.product.id});">
            <a href="/product/{$product.product.id}/edit">{$product.product.title|escape:'html'}</a>
            {if isset($product.product.children) && !empty($product.product.children)}
            <ul id="children-{$product.product.id}" style="display: none;">
                {foreach from=$product.product.children item=child}
                <li><a href="/product/{$child.product.id}/edit">{$child.product.title|escape:'html'}</a></li>
                {/foreach}
            </ul>
            {/if}
        </li>
        {else}
        <li style="padding-left: 20px;"><a href="/product/{$product.product.id}/edit">{$product.product.title|escape:'html'}</a></li>
        {/if}
    {/foreach}
</ul>
