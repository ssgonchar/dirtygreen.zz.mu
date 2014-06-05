-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 4.0.224.1
-- Дата: 29.08.2013 0:35:04
-- Версия сервера: 5.1.41
-- Версия клиента: 4.1

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_company_get_list_with_active_orders$$
CREATE PROCEDURE sp_company_get_list_with_active_orders(IN param_start INT, IN param_end INT)
BEGIN

    DECLARE var_stmt VARCHAR(1000) DEFAULT '';

    SET @var_stmt = CONCAT("
    SELECT 
        DISTINCT(o.company_id)
    FROM orders o 
    JOIN companies c ON c.id = o.company_id
    WHERE o.status IN ('nw', 'ip', 'de')
    ORDER BY o.created_at DESC
    LIMIT ?, ?;");


    PREPARE stmt FROM @var_stmt;

    SET @stmt_from  = param_start;
    SET @stmt_count = param_end;
    
    EXECUTE stmt USING @stmt_from, @stmt_count;    
    

    SELECT 
        COUNT(DISTINCT(o.company_id)) AS count
    FROM orders o 
    JOIN companies c ON c.id = o.company_id
    WHERE o.status IN ('nw', 'ip', 'de');

END
$$

DELIMITER ;
