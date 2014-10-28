    <h3>Search requests from the Bistro</h3>
    <p>select form is not yet ready</p>
<table class="table table-striped table-responsive">
    <thead>
        <tr>
            <th>Company</th>
            <th>User</th>
            <th>Created at</th>
            <th>Location</th>
            <th>Deliverytimes</th>
            <th>Steelgrade</th>
            <th>Thickness</th>
            <th>Width</th>
            <th>Length</th>
            <th>Weight</th>
            
            
            
        </tr>
    </thead>
    <tbody>
       {foreach from=$list item=row}
        <tr>
            <td>{$row.autor.user.person.company.title}</td>            
            <td>{$row.autor.user.person.full_name}</td>      
            <td>{$row['created_at']}</td>
            <td>
                {if $row.locations == ''}
                    <i><span style="color: #cccccc">not defined</span></i>
                {else}
                {foreach from=$row.locations_list item=row_location name=locations}
                    {$row_location.location.title}{if !$smarty.foreach.locations.last},{/if}
                    
                        
                {/foreach}   
                {/if}
            </td>
            <td>
                {if $row.deliverytimes == ''}
                    <i><span style="color: #cccccc">not defined</span></i>
                {else}                
                {foreach from=$row.deliverytimes_list item=row_deliverytime name=deliverytimes}
                    {$row_deliverytime.deliverytime.title}{if !$smarty.foreach.deliverytimes.last},{/if}
                {/foreach}    
                {/if}
            </td>
            <td>
                {if $row.steelgrades == ''}
                    <i><span style="color: #cccccc">not defined</span></i>
                {else}                 
                {foreach from=$row.steelgrades_list item=row_steelgrade name=steelgrades}
                    {$row_steelgrade.steelgrade.title}{if !$smarty.foreach.steelgrades.last},{/if}
                {/foreach} 
                {/if}
            </td>
            <td>
                {if $row.stock.stock.dimension_unit == 'in'}
                    {$row.thickness_from} {$row.stock.stock.dimension_unit}
                    {else}
                        {$row.thickness_from_mm} {$row.stock.stock.dimension_unit}
                        {/if}
            </td>
            <td>
                {if $row.stock.stock.dimension_unit == 'in'}
                    {$row.width_from} {$row.stock.stock.dimension_unit}
                    {else}
                        {$row.width_from_mm} {$row.stock.stock.dimension_unit}
                        {/if}            
            </td>
            <td>
                {if $row.stock.stock.dimension_unit == 'in'}
                    {$row.length_from} {$row.stock.stock.dimension_unit}
                    {else}
                        {$row.length_from_mm} {$row.stock.stock.dimension_unit}
                        {/if}                              
            </td>
            <td>
                {if $row.stock.stock.weight_unit == 'lb'}
                    {$row.weight_to} {$row.stock.stock.weight_unit}
                    {else}
                        {$row.weight_to_ton} {$row.stock.stock.weight_unit}
                        {/if}              
            </td>
            

        </tr>   
        {/foreach}
    </tbody>
</table>
