$(function(){
    //запрет редактирования полей ввода weight & unitweight
    $(".order-unitweight").attr("readonly", true);
    $(".order-weight").attr("readonly", true);
    
    // delivery date
    $('#alert_date').datepicker({
        beforeShow: function(input, inst) 
        { 
            //$('#delivery_date_alt').val(''); 
        },
        showWeek: true
    });
    
    //delivery time
    $('#delivery_date').datepicker({
        beforeShow: function(input, inst) 
        { 
            //
        },
        showWeek: true
    });
    
    // invoicing
    $('#invoicing_type').change(function(){
        $('#invoicing_type_new').val(''); 
    });
    
    $('#invoicing_type_new').keyup(function(){
        $("#invoicing_type [value='0']").attr("selected", "selected");
    });    

    // payment
    $('#payment_type').change(function(){
        $('#payment_type_new').val(''); 
    });
    
    $('#payment_type_new').keyup(function(){
        $("#payment_type [value='0']").attr("selected", "selected");
    });    
    
    $('#order_for').change(function(){
        
        order_for = $('#order_for').val();
        
        if (order_for == '')
        {
            $('#position_controls').hide();
        }
        else
        {
            dimension_unit  = '';
            weight_unit     = '';
            price_unit      = '';
            currency        = '';
            
            if (order_for == 'pa')
            {
                dimension_unit  = 'in';
                weight_unit     = 'lb';
                price_unit      = 'cwt';
                currency        = 'usd';
            }
            else
            {
                dimension_unit  = 'mm';
                weight_unit     = 'mt';
                price_unit      = 'mt';
                currency        = 'eur';
            }

            $('#dimension_unit').val(dimension_unit);
            $('#weight_unit').val(weight_unit);
            $('#price_unit').val(price_unit);
            $('#currency').val(currency);
            
            $('.lbl-dim').html(dimension_unit);
            $('.lbl-wgh').html(weight_unit == 'mt' ? 'ton' : weight_unit);
            $('.lbl-price').html(get_curreny_sign(currency) + '/' + (price_unit == 'mt' ? 'ton' : price_unit));
            $('.lbl-cur').html(get_curreny_sign(currency));
            
            $('#position_controls').show();
        }
    });

    bind_biz_autocomplete('#order-biz', get_companies_by_biz);
});


/**
 * Получает список людей для компании
 */
var get_companies_by_biz = function(biz_id)
{

    if (biz_id > 0)
    {
        $('#companies').prepend($('<option selected="" value="0">Loading...</option>'));        
        error = false;
        
        $.ajax({
            url: '/biz/getcompanies',
            data : {
                biz_id  : biz_id,
                role    : 'buyer'
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    fill_select("#companies", json.companies, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }                
            }
        });        
    }
    
    if (biz_id == 0 || error)
    {
        $('#companies').empty();
        $('#companies').prepend($('<option value="0">--</option>'));
    }    
    
};


/**
 * Получает список людей для компании
 */
var get_persons_by_company = function(company_id)
{
    if (company_id > 0)
    {
        $('#persons').prepend($('<option selected="" value="0">Loading...</option>'));        
        error = false;
        
        $.ajax({
            url: '/company/getpersons',
            data : {
                company_id : company_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    fill_select("#persons", json.persons, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }                
            }
        });        
    }
    
    if (company_id == 0 || error)
    {
        $('#persons').empty();
        $('#persons').prepend($('<option value="0">--</option>'));
    }    
};

/**
 * Добавляет позиции к заказу
 */
var app_position = function()
{
    next_row_index  = 1 + parseInt($("#positions_count").val());
    price_unit      = $("#price_unit").val();
    
    $.ajax({
        url: '/order/addposition',
        data : {
            next_row_index  : next_row_index,
            price_unit      : price_unit
        },
        success: function(json){
            if (json.result == 'okay') 
            {
                $('#positions > tbody tr:last').after(json.position);
                $("#positions_count").val(next_row_index);
                
                if ($("#positions").is(":hidden")) $("#positions").show();
                if ($("#lbl-positions").is(":visible")) $("#lbl-positions").hide();
                
                var_currency = $('#currency').val();
                if (var_currency == 'usd') var_currency = '$';
                else if (var_currency == 'eur') var_currency = '&euro;';
                
                var_price_unit = $('#price_unit').val();
                if (var_price_unit == 'mt') var_price_unit = 'Ton';
                
                $('#lbl-price-unit-' + next_row_index).html(var_currency + '/' + var_price_unit);
            }
            else
            {
                alert('Error receiving data !');
            }
            
            // прячет модальное окно loading...
            //hide_idle();
        }
    });    
};

/**
 * Удаляет позицию из списка позиций заказа
 */
var position_delete = function(index)
{
    position_id = parseInt($('#position-id-' + index).val());
    
    if (position_id > 0)
    {
        is_deleted = $('#position-deleted-' + index).val();
        if (is_deleted > 0)
        {
            $('#pic-delete-' + index).attr('src', '/img/icons/cross.png');
            $('#position-deleted-' + index).val(0);
        }
        else
        {
            $('#pic-delete-' + index).attr('src', '/img/icons/reload.png');
            $('#position-deleted-' + index).val(1);
        }
        
        $('#position-' + index).toggleClass('deleted');            
    }
    else
    {
        if (!confirm('Am I sure ?')) return;        
        $('#position-' + index).remove();
    }

    if ($('#positions > tbody tr').length <= 1) 
    {
        $("#positions").hide();
        $("#lbl-positions").show();        
    }
    
    calc_total_pos();
};

/**
 * Подсчитывает тотал позиций
 */
var calc_total_pos = function()
{
    var qtty_obj    = '#qtty-';
    var weight_obj  = '#weight-';
    var value_obj   = '#value-';
    var p_value_obj = '#purchasevalue-';
    var deleted_obj = '#position-deleted-'
        
    // total
    if ($('#lbl-total-qtty') && $('#lbl-total-weight') && $('#lbl-total-value'))
    {
        var qtty    = 0;
        var weight  = 0;
        var value   = 0;
        var p_value = 0;

        $('.cb-row').each(function(){        
            
            i = $(this).val();
        
            if (parseNumber(getVal($(deleted_obj + i))) == 0)
            {
                if ($(qtty_obj + i))    qtty += parseNumber(getVal($(qtty_obj + i)));
                if ($(weight_obj + i))  weight += parseNumber(getVal($(weight_obj + i)));
                if ($(value_obj + i))   value += parseNumber(getVal($(value_obj + i)));
                if ($(p_value_obj + i)) p_value += parseNumber(getVal($(p_value_obj + i)));                
            }
        });
/*        
        if ($('#weight_unit').val() == 'lb' && $('#price_unit').val() == 'cwt')
        {
            value = value / 100;
        }
*/        
        $('#lbl-total-qtty').html(qtty);    
        $('#lbl-total-weight').html(numberRound(weight, 2));    
        $('#lbl-total-value').html(numberRound(value, 2));
        if ($('#lbl-total-purchasevalue')) $('#lbl-total-purchasevalue').html(numberRound(p_value, 2));
    }
};

/**
 * Выделяет или не выделяет Delivery Point
 * @version 20120424, zharkov
 */
var show_delivery_details = function(delivery_basis)
{
    if (delivery_basis == 'col' || delivery_basis == 'exw' || delivery_basis == 'fca')
    {
        $('#delivery-details-1').hide();
        $('#delivery-details-2').hide();
        
        $('#delivery-time-th').html('Load Readiness');
        $('#delivery-time').html('Load Readiness : ');
    }
    else
    {
        $('#delivery-details-1').show();
        $('#delivery-details-2').show();

        $('#delivery-time-th').html('Delivery Time');
        $('#delivery-time').html('Delivery Time : ');
    }
}
