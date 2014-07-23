<ul class="nav navbar-nav footer_panel">
    <li style='margin-top: 7px;'>	
    </li>
</ul>
<ul class="nav navbar-nav navbar-right" style='margin-top: 7px;'>
    <li>
        {if !empty($item.parent_id)}<span>I can't cut virtual position</span>{/if}
        <input type="button" class="btn btn-default" value="Go Back" onclick="location.href='/{$back_url}';" style="margin-left: 20px;">

        {if empty($item.parent_id)}
            <input type="button" id="btn_add_piece" class="btn btn-success" value="Add Piece" onclick="cut_add_piece({$item.id});" style="margin-left: 10px;">
        {/if}

        {if empty($item.parent_id)}
            <input type="submit" name="btn_save" class="btn btn-primary" value="Save" style="margin-left: 10px;">
        {/if}
    </li>	
</ul>