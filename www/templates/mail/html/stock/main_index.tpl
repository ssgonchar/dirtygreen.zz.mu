<table class="list" width="100%">
    <tbody>
        <tr class="top-table">
            <th width="5%">Id</th>
            <th class="text-left">Title</th>
            <th width="15%">Invoicing Basis</th>
            <th width="15%">Payment Term</th>
            {*<th width="10%">Positions</th>*}
            <th width="10%">Add Positions</th>
            {*<th width="10%">Orders</th>*}
            <th width="10%">Edit</th>
        </tr>
        {foreach from=$list item=row}
        <tr>
            <td>{$row.stock.id}</td>
            <td class="text-left"><a href="/positions/filter/stock:{$row.stock.id}" style="font-weight: bold;">{$row.stock.title|escape:'html'}</a></td>
            <td>{if isset($row.stock.invoicingtype)}{$row.stock.invoicingtype.title|escape:'html'}{else}<i>none</i>{/if}</td>
            <td>{if isset($row.stock.paymenttype)}{$row.stock.paymenttype.title|escape:'html'}{else}<i>none</i>{/if}</td>
            {*<td><a href="/positions/filter/stock:{$row.stock.id}">{number1 value=$row.stock.quick.positions zero='no positions' e0='positions' e1='position'}</a></td>*}
            <td><a href="/position/add/{$row.stock.id}" class="add">add positions</a></td>
            {*<td><a href="/orders/filter/stock:{$row.stock.id}">{number1 value=$row.stock.quick.orders zero='no orders' e0='orders' e1='order'}</a></td>*}
            <td><a href="/stock/{$row.stock.id}/edit" class="edit">edit stock</a></td>
        </tr>
        {/foreach}
    </tbody>    
</table>
