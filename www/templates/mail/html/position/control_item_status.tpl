<tr id="t-is-{$next_row_index}">
    <td class="guid-{$next_row_index}">{''|undef}</td>
    <td><input type="text" name="item[{$next_row_index}][mill]" value="" class="max"></td>
    <td>{''|undef}</td>
    <td>{''|undef}</td>
    <td><input type="text" name="item[{$next_row_index}][pl]" value="" class="max"></td>
    <td>0</td>
    <td><input type="text" name="item[{$next_row_index}][load_ready]" value="" class="max"></td>
    <td><input type="text" name="item[{$next_row_index}][internal_notes]" value="" class="max"></td>
    <td>
        <select name="item_property[{$next_row_index}][condition]" class="max">
            <option value="">--</option>
            <option value="ar">As Rolled</option>
            <option value="n">Normalized</option>
            <option value="nr">Normalizing Rolling</option>
        </select>
    </td>
    <td><input type="checkbox" name="item[{$next_row_index}][is_ce_mark]" value="1"></td>
</tr>
