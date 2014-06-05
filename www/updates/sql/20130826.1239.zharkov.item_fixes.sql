
update steelitems 
set
    dimension_unit  = (select dimension_unit from steelpositions where id = steelitems.steelposition_id),
    weight_unit     = (select weight_unit from steelpositions where id = steelitems.steelposition_id),
    price_unit      = (select price_unit from steelpositions where id = steelitems.steelposition_id),
    currency        = (select currency from steelpositions where id = steelitems.steelposition_id)
where (weight_unit = '' OR dimension_unit = '' OR currency = '');


update steelitems
set
    dimension_unit  = (select dimension_unit from orders where id = steelitems.order_id),
    weight_unit     = (select weight_unit from orders where id = steelitems.order_id),
    price_unit      = (select price_unit from orders where id = steelitems.order_id),
    currency        = (select currency from orders where id = steelitems.order_id)
where (weight_unit = '' OR dimension_unit = '' OR currency = '')
AND is_from_order > 0
and order_id > 0;

update steelitems_history 
set
    dimension_unit  = (select dimension_unit from steelpositions where id = steelitems_history.steelposition_id),
    weight_unit     = (select weight_unit from steelpositions where id = steelitems_history.steelposition_id),
    price_unit      = (select price_unit from steelpositions where id = steelitems_history.steelposition_id),
    currency        = (select currency from steelpositions where id = steelitems_history.steelposition_id)
where (weight_unit = '' OR dimension_unit = '' OR currency = '');


update steelitems_history
set
    dimension_unit  = (select dimension_unit from orders where id = steelitems_history.order_id),
    weight_unit     = (select weight_unit from orders where id = steelitems_history.order_id),
    price_unit      = (select price_unit from orders where id = steelitems_history.order_id),
    currency        = (select currency from orders where id = steelitems_history.order_id)
where (weight_unit = '' OR dimension_unit = '' OR currency = '')
AND is_from_order > 0
and order_id > 0;


#select * from steelitems where (weight_unit = '' OR dimension_unit = '' OR currency = '');