UPDATE orders SET price_unit = 'lb' WHERE id = 2046;

UPDATE steelitems_history
SET
    thickness = 125,
    thickness_mm = 125,
    width = 2450,
    width_mm = 2450,
    length = 6120,
    length_mm = 6120,
    unitweight = 14.994,
    unitweight_ton = 14.994,
    weight_unit = 'mt',
    dimension_unit = 'mm',
    currency = 'eur',
    price_unit = 'mt'
WHERE id = 6364;