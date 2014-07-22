<div class="row">
    <div class="col-md-7">
        <table class="table table-striped list" width="50%">
            <thead>
                                <tr class="top-table">
                    <th width="5%" style="display: none;">Id</th>
                    <th width="30%">Title</th>
                    <th style="display: none;">Group alias</th>
                    <th>Background color</th>
                    <th>Font color</th>
                    <th></th>
                </tr>
            </thead>           
            <tbody>              
                <tr>
                    <td><input type="text" name="title[0]" value="" class="max" placeholder="New steelgrade"></td>
                    <td style="display: none;"><input type="text" name="alias[0]" value="" class="max"></td>
                    <td><input type="text" name="bgcolor[0]"  placeholder="Background color"  value="" class="picker max"></td>
                    <td><input type="text" name="color[0]" placeholder="Font color"  value="" class="picker-font max"></td>
                    <td></td>
                </tr>                 
                {foreach from=$list item=row}
                    <tr>
                        <td style="display: none;">{$row.steelgrade.id}</td>
                        <td><input type="text" name="title[{$row.steelgrade.id}]" value="{$row.steelgrade.title|escape:'html'}" class="max" placeholder="Title"></td>
                        <td style="display: none;"><input type="text" name="alias[{$row.steelgrade.id}]" value="{$row.steelgrade.alias|escape:'html'}" class="max"></td>
                        <td><input type="text" name="bgcolor[{$row.steelgrade.id}]"  placeholder="Background color" value="{$row.steelgrade.bgcolor|escape:'html'}" class="picker normal" style="background-color: {$row.steelgrade.bgcolor|escape:'html'};"></td>
                        <td><input type="text" name="color[{$row.steelgrade.id}]" placeholder="Font color" value="{$row.steelgrade.color|escape:'html'}" class="picker-font normal"></td>
                        <td><a href="javascript: void(0);" onclick="if (confirm('Am I sure?'))
                        location.href = '/directory/deletesteelgrade/{$row.steelgrade.id}';" class="btn btn-danger btn-xs delete"><i class="glyphicon glyphicon-remove"></i> delete</a></td>
                    </tr>
                {/foreach}           
            </tbody>
        </table>
                <br>
                <br>
                <br>
    </div>
</div>