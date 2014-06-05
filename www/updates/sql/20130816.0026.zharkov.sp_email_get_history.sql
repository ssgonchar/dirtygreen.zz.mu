-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 4.0.224.1
-- Дата: 16.08.2013 0:26:44
-- Версия сервера: 5.1.41
-- Версия клиента: 4.1

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_email_get_history$$
CREATE PROCEDURE sp_email_get_history(IN param_email_id INT)
sp:
BEGIN

    DECLARE var_group_id INT DEFAULT 0;
    
    SET var_group_id = IFNULL((SELECT group_id FROM email_groups WHERE email_id = param_email_id), 0);

    IF var_group_id = 0
    THEN
        SELECT -1 AS ErrorCode, 'sp_email_get_history' AS ErrorAt;
        LEAVE sp;
    END IF;

    
    SELECT 
        eg.id,
        eg.group_id,
        eg.email_id,
        e.created_at,
        e.created_by,
        e.is_sent,
        e.type_id
    FROM email_groups AS eg
    JOIN emails AS e ON eg.email_id = e.id
    WHERE eg.group_id = var_group_id 
    ORDER BY e.created_at DESC;
END
$$

DELIMITER ;
