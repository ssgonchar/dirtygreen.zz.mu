<table width="100%">
    <tr>
        <td style="width: 33%" class="text-top">
            <h3>Plate Dimensions to be Rolled</h3>
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b" style="width: 200px;">Dimensions  : </td>
                    <td>
                        <select id="dimension_type" class="narrow" onchange="check_pp();">
                            <option value="mm" selected="selected">mm</option>
                            <option value="in">in</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Thickness<span class="dim_type">, mm</span> : </td>
                    <td><input type="text" id="plate_thickness" class="narrow" onkeyup="check_pp();" size="10" tabindex="1"></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Width<span class="dim_type">, mm</span> : </td>
                    <td><input type="text" id="plate_width" class="narrow" onkeyup="check_pp();" size="10" tabindex="2"></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Length<span class="dim_type">, mm</span> : </td>
                    <td><input type="text" id="plate_length" class="narrow" onkeyup="check_pp();" size="10" tabindex="3"></td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">Weight<span class="wgt_type">, ton</span> : </td>
                    <td><span id="plate_weight">{''|undef}</span></td>
                </tr>                
                <tr style="height: 32px;">
                    <td class="form-td-title">Max Plate Length<span class="dim_type">, mm</span> : </td>
                    <td><span id="max_plate_length">{''|undef}</span></td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">Production Range : </td>
                    <td><span id="production_range">{''|undef}</span></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Quantity, pcs : </td>
                    <td><input type="text" id="qtty" class="narrow" onkeyup="check_pp();" onchange="check_pp();" size="10" tabindex="4"></td>
                </tr>
                <tr style="height: 32px;">
                   <td class="form-td-title">Weight<span class="wgt_type">, ton</span> : </td>
                   <td><span id="weight">{''|undef}</span></td>
                </tr>                
            </table>
        </td>
        <td style="width: 33%" class="text-top">
            <h3>Slab Choice / Rolling Feedstock</h3>
            <table class="form" width="100%">
                <tr>
                    <td class="form-td-title-b" style="width: 200px;">Slab Thickness, mm : </td>
                    <td><input type="text" id="slab_thickness" class="narrow" value="250" onkeyup="check_pp();" size="10" tabindex="5"></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Slab Width, mm : </td>
                    <td><input type="text" id="slab_width" class="narrow" value="2000" onkeyup="check_pp();" size="10" tabindex="6"></td>
                </tr>
                <tr>
                    <td class="form-td-title-b">Slab Length, mm : </td>
                    <td><input type="text" id="slab_length" class="narrow" onkeyup="check_pp();" size="10" tabindex="7"></td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">Slab Weight, ton : </td>
                    <td><span id="slab_weight">{''|undef}</span></td>
                </tr>                
                <tr style="height: 32px;">
                    <td class="form-td-title">Slab Cut REQUIRED, mm : </td>
                    <td><span id="slab_cut_required">{''|undef}</span></td>
                </tr>
            </table>
            <div class="pad"></div>
            
            <div style="position: absolute;">
                <img src="/img/layout/gnome.jpg" style="position: absolute; top: 0; left: 0;">
            </div>
            <div class="bubble" style="margin-left: 55px;" id="gnome_text">
                Please give me more information for calculation
            </div>
        </td>
        <td class="text-top">
            <h3>Production Notes</h3>
            <table class="form" width="100%">
                <tr style="height: 32px;">
                    <td class="form-td-title" style="width: 200px;">Max Slab Cut, mm : </td>
                    <td><span id="max_slab_cut">{''|undef}</span></td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">Pref Max Slab Cut, mm : </td>
                    <td><span id="pref_max_slab_cut">{''|undef}</span></td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">Min Slab Cut, mm : </td>
                    <td><span id="min_slab_cut">{''|undef}</span></td>
                </tr>
                <tr style="height: 32px;">
                    <td class="form-td-title">Possibility To Obtain Width : </td>
                    <td><span id="poss_to_obtain_width">{''|undef}</span></td>
                </tr>            
            </table>
        </td>
    </tr>
    <tr>
        <td  class="text-top">
            <div id='pp_resiult_wrapper'>
            <span id="pp_result" style="font-size: 18px;"></span>
            <br><span id="pp_result_reason"></span>
            <br><span id="pp_result_suggestion"></span>
            </div>
        </td>
        <td colspan="2" class="text-top">
        </td>        
    </tr>
</table>