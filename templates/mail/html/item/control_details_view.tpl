<div id="overlay" onclick="hide_item_details(this);"></div>
<div id="ra-steelitem-tip" style="padding: 5px;">
    <div style="width: 700px; height: 300px; margin: 10px; overflow-x: hidden; overflow-y: auto;">
        <table class="list" style="width: 100%;">
            <tr class="top-table" style="height: 25px;">
                <th colspan="3" style="width: 40%;">Chemical Analysis</th>
                <th colspan="3" style="width: 60%;">Mechanical Properties</th>
            </tr>
            <tr>
                <td>Heat / Lot</td>
                <td>{$steelitem.properties.heat_lot|escape:'html'}</td>
                <td rowspan="18" style="width:10px;"></td>
                <td rowspan="4" style="width:10px; vertical-align: middle; text-align: center;">&nbsp;T &nbsp;E &nbsp;N &nbsp;S &nbsp;I &nbsp;L &nbsp;E</td>
                <td>Sample Direction</td>
                <td>{$steelitem.properties.tensile_sample_direction|escape:'html'}</td>
            </tr>
            <tr>
                <td>%C</td>
                <td>{$steelitem.properties.c|escape:'html'}</td>
                <td>Strength, N/mm<sup>2</sup></td>
                <td>{$steelitem.properties.tensile_strength|escape:'html'}</td>
            </tr>
            <tr>
                <td>%Si</td>
                <td>{$steelitem.properties.si|escape:'html'}</td>
                <td>Yield Point, N/mm<sup>2</sup></td>
                <td>{$steelitem.properties.yeild_point|escape:'html'}</td>
            </tr>
            <tr>
                <td>%Mn</td>
                <td>{$steelitem.properties.mn|escape:'html'}</td>
                <td>Elongation, %</td>
                <td>{$steelitem.properties.elongation|escape:'html'}</td>
            </tr>
            <tr>
                <td>%P</td>
                <td>{$steelitem.properties.p|escape:'html'}</td>
                <td></td>
                <td>Z-test, %</td>
                <td>{$steelitem.properties.reduction_of_area|escape:'html'}</td>
            </tr>
            <tr>
                <td>%Cr</td>
                <td>{$steelitem.properties.cr|escape:'html'}</td>
                <td rowspan="3" style="width:10px; vertical-align: middle; text-align: center;">&nbsp;I &nbsp;M &nbsp;P &nbsp;A &nbsp;C &nbsp;T</td>
                <td>Sample Direction</td>
                <td>{$steelitem.properties.sample_direction|escape:'html'}</td>
            </tr>
            <tr>
                <td>%Ni</td>
                <td>{$steelitem.properties.ni|escape:'html'}</td>
                <td>Strength, J/cm<sup>2</sup></td>
                <td>{$steelitem.properties.impact_strength|escape:'html'}</td>
            </tr>
            <tr>
                <td>%Cu</td>
                <td>{$steelitem.properties.cu|escape:'html'}</td>
                <td>Test Temp, deg. C</td>
                <td>{$steelitem.properties.test_temp|escape:'html'}</td>
            </tr>
            <tr>
                <td>%Al</td>
                <td>{$steelitem.properties.al|escape:'html'}</td>
                <td rowspan="10"></td>
                <td>tensile_strength</td>
                <td>{$steelitem.properties.tensile_strength}</td>
            </tr>
            <tr>
                <td>%Mo</td>
                <td>{$steelitem.properties.mo|escape:'html'}</td>
                <td>Hardness, HD</td>
                <td>{$steelitem.properties.hardness|escape:'html'}</td>
            </tr>
            <tr>
                <td>%Nb</td>
                <td>{$steelitem.properties.nb|escape:'html'}</td>
                <td>UST</td>
                <td>{$steelitem.properties.ust|escape:'html'}</td>
            </tr>
            <tr>
                <td>%V</td>
                <td>{$steelitem.properties.v|escape:'html'}</td>
                <td>Stress Relieving Temp, deg. C</td>
                <td>{$steelitem.properties.stress_relieving_temp|escape:'html'}</td>
            </tr>
            <tr>
                <td>%N</td>
                <td>{$steelitem.properties.n|escape:'html'}</td>
                <td>Heating Rate Per Hour, deg. C</td>
                <td>{$steelitem.properties.heating_rate_per_hour|escape:'html'}</td>
            </tr>
            <tr>
                <td>%Ti</td>
                <td>{$steelitem.properties.ti|escape:'html'}</td>
                <td>Holding Time, Hours</td>
                <td>{$steelitem.properties.holding_time|escape:'html'}</td>
            </tr>
            <tr>
                <td>%Sn</td>
                <td>{$steelitem.properties.sn|escape:'html'}</td>
                <td>Cooling Down Rate Per Hour, deg. C</td>
                <td>{$steelitem.properties.cooling_down_rate|escape:'html'}</td>
            </tr>
            <tr>
                <td>%B</td>
                <td>{$steelitem.properties.b|escape:'html'}</td>
                <td>Normalizing Temp, deg. C</td>
                <td>{$steelitem.properties.normalizing_temp|escape:'html'}</td>
            </tr>
            <tr>
                <td>%Ti</td>
                <td>{$steelitem.properties.ti|escape:'html'}</td>
                <td>Condition</td>
                <td>
                    {if $row.steelitem.properties.condition == 'ar'}As Rolled
                    {elseif $row.steelitem.properties.condition == 'n'}Normalized
                    {elseif $row.steelitem.properties.condition == 'nr'}Normalizing Rolling
                    {/if}
                </td>
            </tr>
            <tr>
                <td>CEQ</td>
                <td>{$steelitem.properties.ceq}</td>
                <td></td>
                <td></td>
            </tr>
        </table>
    </div>
    <a href="javascript: void(0);" onclick="hide_item_details(this);" style="color: black;">close window</a>
</div>