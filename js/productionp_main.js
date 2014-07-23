/**
 * Проверяет возможность производства материала
 * @version 20130227, zharkov
 */
var check_pp = function()
{
    var dimension_type  = $('#dimension_type').val();

    var plate_thickness = $('#plate_thickness').val();
    var plate_width     = $('#plate_width').val();
    var plate_length    = $('#plate_length').val();
    var qtty            = $('#qtty').val();
    var plate_weight    = 0;
    
    var slab_thickness  = parseNumber($('#slab_thickness').val());
    var slab_width      = parseNumber($('#slab_width').val());
    var slab_length     = parseNumber($('#slab_length').val());
    var slab_weight     = 0;

       
    if (dimension_type == 'in')
    {
        plate_thickness = InchToMM(plate_thickness);
        plate_width     = InchToMM(plate_width);
        plate_length    = InchToMM(plate_length);
            
        $('.dim_type').text(', in');
        $('.wgt_type').text(', lb');
    }
    else
    {
        $('.dim_type').text(', mm');
        $('.wgt_type').text(', ton');        
    }
    

    plate_thickness = parseNumber(plate_thickness);
    plate_width     = parseNumber(plate_width);
    plate_length    = parseNumber(plate_length);

    plate_weight    = plate_thickness * plate_width * plate_length * 7.85 / 1000000000;
    slab_weight     = slab_thickness * slab_width * slab_length * 7.85 / 1000000000;
        
    if (dimension_type == 'in')
    {
        plate_weight    = plate_weight  * 1.1023;
        slab_weight     = slab_weight  * 1.1023;
    }
    
    plate_weight    = numberRound(plate_weight, 3);
    slab_weight     = numberRound(slab_weight, 3);
    
    $('#plate_weight').text(plate_weight);
    $('#slab_weight').text(slab_weight);
    
    
    // slab cut required    
    slab_cut_required = plate_thickness * plate_width * plate_length * (plate_weight == slab_weight ? 7.92 : 8.72) / (slab_thickness * slab_width * 7.85);    
    $('#slab_cut_required').text(parseInt(slab_cut_required));
    
    
    // max slab cut    
    if (plate_width > slab_width)
    {
        max_slab_cut = 3000;
    }
    else  if (plate_width == slab_width)
    {
        max_slab_cut = 5500;
    }
    else
    {
        max_slab_cut = parseInt(plate_width);
    }
    
    $('#max_slab_cut').text(max_slab_cut);
    
    
    // min slab cut
    if (slab_thickness <= 260)
    {
        min_slab_cut = 650;
    }
    else if (slab_thickness > 260 && slab_thickness <= 350)
    {
        min_slab_cut = 960;
    }
    else
    {
        min_slab_cut = 1200;
    }
    
    $('#min_slab_cut').text(min_slab_cut);
    
    
    // pref max slab cut
    if (plate_width >= slab_width)
    {
        pref_max_slab_cut = 3000;
    }
    else
    {
        pref_max_slab_cut = parseInt(plate_width);
    }
    
    $('#pref_max_slab_cut').text(pref_max_slab_cut);
    
    
    // possible to obtain width
    if (plate_width == slab_width)
    {
        poss_to_obtain_width = true;
    }
    else if (plate_width > slab_width && slab_thickness * slab_width / (plate_width + 70) > plate_thickness)
    {
        poss_to_obtain_width = true;
    }
    else if (plate_width < slab_width && slab_thickness * slab_cut_required / (plate_width + 70) > plate_thickness)
    {
        poss_to_obtain_width = true;
    }
    else
    {
        poss_to_obtain_width = false;
    }
    
    $('#poss_to_obtain_width').text(poss_to_obtain_width ? 'Possible' : 'Impossible');
    
    
    // max plate length
    if (slab_length == 0 && plate_width == slab_width)
    {
        max_plate_length = parseInt(max_slab_cut * slab_thickness * slab_width * 7.85 / (plate_thickness * plate_width * 7.92));
    }
    else if (slab_length != 0 && plate_width == slab_width)
    {
        max_plate_length = parseInt(slab_length * slab_thickness * slab_width * 7.85 / (plate_thickness * plate_width * 7.92));
    }
    else if (slab_length == 0 && plate_width != slab_width)
    {
        max_plate_length = parseInt(max_slab_cut * slab_thickness * slab_width * 7.85 / (plate_thickness * plate_width * 8.72));
    }
    else
    {
        max_plate_length = parseInt(slab_length * slab_thickness * slab_width * 7.85 /(plate_thickness * plate_width * 8.72));
    }
        
    max_plate_length = max_plate_length <= 24000 ? max_plate_length : 24000;
    if (dimension_type == 'in')
    {
        max_plate_length = MMToInch(max_plate_length);
    }
    
    $('#max_plate_length').text(max_plate_length);
    
    
    // production range
    if (plate_thickness < 6 || plate_thickness > 400 || plate_width < 650 || plate_width > 3050 || plate_length > 24000)
    {
        production_range = false;
    }
    else
    {
        production_range = true;
    }
    
    $('#production_range').text(production_range ? 'In Production Range' : 'Out Of Production Range');
    

    // weight
    weigth = numberRound(plate_weight * qtty, 3);
    $('#weight').text(weigth);
    
    
    // reason & suggestion
    reason      = '';
    suggestion  = '';
    if (!production_range)
    {
        reason = 'Out Of Production Range';
    }
    else if (!poss_to_obtain_width)
    {
        reason      = 'Width cannot be obtained';
        suggestion  = 'Please decrease width';
    }
    else if (slab_cut_required < min_slab_cut)
    {
        reason      = 'Slab Cut < Min Slab Cut';
        suggestion  = 'Please increase width or length, try multiples';
    }
    else if (slab_cut_required > max_slab_cut)
    {
        reason      = 'Slab Cut > Max Slab Cut';
        suggestion  = 'Please decrease width or length';
    }
    else if ((slab_thickness - plate_thickness) < 50)
    {
        reason      = 'Slab thickness is not sufficient to obtain rolled structure';
        suggestion  = 'Please use thicker slab or decrease plate thickness';
    }
    
    //$('#pp_result_reason').text(reason);
    //$('#pp_result_suggestion').text(suggestion);
    
    
    // normalizing
    if (plate_width > 2000 || plate_length > 6000)
    {
        normalizing = 'Furnance normalising impossible';
    }
    else
    {
        normalizing = 'Furnance normalising possible';
    }
    
    
    // structure
    structure = '';
    if ((slab_thickness * slab_width) / (plate_thickness * plate_width) < 2) 
    {
        structure = 'No ultrasonic guarantee';
    }
    
    if (slab_thickness > 380 && slab_thickness < 450) 
    {
        struct = "Minimum plate thickness = 50 mm (cooling limitation) .";
    }
    else if (slab_thickness >= 450) 
    {
        struct = "Minimum plate thickness = 80 mm (cooling limitation) .";
    }
    else if (slab_thickness == 300) 
    {
        struct = "Minimum plate thickness = 7 mm (cooling limitation) Ultrasonic problems on plates 50-100 mm thic in case Azovstal slabs are used .";
    }
    else if (slab_thickness > 150 && slab_thickness < 300) 
    {
        struct = "Minimum plate thickness = 7 mm .";
    }
    else if (slab_thickness > 300 && slab_thickness <= 380) 
    {
        struct = "Minimum plate thickness = 20 mm .";    
    }
    else if (slab_thickness <= 150) 
    {
        struct = "Minimum plate thickness = 6 mm .";
    }
    else
    {
        struct = "Slab thickness was not included in the table. Please check with technical department .";
    }
    
    
    // width
    width = '';
    if (plate_width <= 1200) 
    {
        width = "250 mm thick slab can be cut to width, leaving min. 650 mm. In case production is impossible try slab width = plate width .";
    }
    else if (plate_thickness >= 6 && plate_thickness < 8 && plate_width > 2000) 
    {
        width = "Max width 2000 mm .";    
    }
    else if (plate_thickness >= 8 && plate_thickness < 10 && plate_width > 2500) 
    {
        width = "Max width 2500 mm .";
    }
    else if (plate_thickness >= 10 && plate_thickness < 12 && plate_width > 3000) 
    {
        width = "Max width 3000 mm .";
    }
    else if (plate_thickness >= 12 && plate_thickness < 100 && plate_width > 3050) 
    {
        width = "Max width 3050 mm .";
    }
    else if (plate_thickness > 100 && plate_width > 3000)
    {
        width = "Max width 3000 mm .";
    }
    
    pp_warning = normalizing + (structure != '' ? '<br>' : '') + structure + (width != '' ? '<br>' : '') + width;
    
    
    // result
    result = true;
    if (!poss_to_obtain_width || !production_range)
    { 
        result = false;
    }
    else if (slab_cut_required < min_slab_cut)
    { 
        result = false;
    }
    else if (slab_cut_required > max_slab_cut)
    {
        result = false;
    }
    else if (slab_length != 0 && slab_cut_required >= slab_length) 
    {
        result = false;
    }
    else if ((slab_thickness - plate_thickness) < 50) 
    {
        result = false;
    }
    else if (slab_thickness >= 180 && plate_thickness < 6) 
    {
        result = false;
    }
    else if (plate_thickness >= 6 && plate_thickness < 8 && plate_weight > 2000) 
    {
        result = false;
    }
    else if (plate_thickness >= 8 && plate_thickness < 10 && plate_width > 2500) 
    {
        result = false;
    }
    else if (plate_thickness >= 10 && plate_thickness < 12 && plate_width > 3000) 
    {
        result = false;
    }
    else if (plate_thickness >= 12 && plate_thickness <= 100 && plate_width > 3050) 
    {
        result = false;
    }
    else if (plate_thickness > 100 && plate_width > 3000) 
    {
        result = false;
    }
        
    if (result)
    {        
/*
        $('#pp_result').text('Possible');
        $('#pp_resiult_wrapper').css('color', 'green');
*/        
        gnome_text = '<span style="color:green; font-size: 16px; font-weight: bold;">Production Possible</span><br><br>';
    }
    else
    {
/*        
        $('#pp_result').text('Impossible');
        $('#pp_resiult_wrapper').css('color', 'red');
*/        
        gnome_text = '<span style="color:red; font-size: 16px; font-weight: bold;">Production Impossible</span><br><br>';
        gnome_text += reason + (suggestion != '' ? '<br>' : '') + suggestion;
    }
    
    if (reason != '' || suggestion != '')
    {
        gnome_text += '<br><br>';
    }
    
    gnome_text += pp_warning;
    
    if (plate_thickness > 0 && plate_width > 0)
    {
        $('#gnome_text').html(gnome_text);
    }
    
};

/*
pwg     plate_weight
mpl     max_plate_length
swg     slab_weight
scr     slab_cut_required
qpcs    qtty
msc     max_slab_cut
pmsc    pref_max_slab_cut
minsc   min_slab_cut
ptow    poss_to_obtain_width
pr      production_range
qmt     weight
w1      pp_warnings
wgt     pp_warnings
struct  pp_warnings
norm    pp_warnings
res     pp_result
reas    pp_result_reason
sug     pp_result_suggestion

*/