<!--<div class="footer-left">
</div>
<div class="footer-right">
    {if !empty($item.parent_id)}<span>I can't cut virtual position</span>{/if}
    <input type="button" class="btn100" value="Go Back" onclick="location.href='/{$back_url}';" style="margin-left: 20px;">

    {if empty($item.parent_id)}
    <input type="button" id="btn_add_piece" class="btn100" value="Add Piece" onclick="cut_add_piece({$item.id});" style="margin-left: 10px;">
    {/if}

    {if empty($item.parent_id)}
    <input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 10px;">
    {/if}
</div>-->

<ul class="nav navbar-nav footer_panel">
    <li style='margin-top: 7px;'>	
    </li>
</ul>
<ul class="nav navbar-nav navbar-right">
    <li>
        {if !empty($item.parent_id)}<span>I can't cut virtual position</span>{/if}
        <input type="button" class="btn100" value="Go Back" onclick="location.href='/{$back_url}';" style="margin-left: 20px;">

        {if empty($item.parent_id)}
            <input type="button" id="btn_add_piece" class="btn100" value="Add Piece" onclick="cut_add_piece({$item.id});" style="margin-left: 10px;">
        {/if}

        {if empty($item.parent_id)}
            <input type="submit" name="btn_save" class="btn100o" value="Save" style="margin-left: 10px;">
        {/if}
    </li>	
</ul>