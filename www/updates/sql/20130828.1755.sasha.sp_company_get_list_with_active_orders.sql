delimiter $$

DROP PROCEDURE IF EXISTS `sp_company_get_list_with_active_orders`$$
CREATE PROCEDURE `sp_company_get_list_with_active_orders`(IN param_start INT, IN param_end INT)
BEGIN

    SELECT DISTINCT(o.company_id)
    FROM orders o 
    JOIN companies c ON c.id = o.company_id
    WHERE o.status IN ('nw', 'ip', 'de')
    LIMIT param_start, param_end;

    SELECT COUNT(DISTINCT(o.company_id)) AS count
    FROM orders o 
    JOIN companies c ON c.id = o.company_id
    WHERE o.status IN ('nw', 'ip', 'de');

END$$

delimiter ;
