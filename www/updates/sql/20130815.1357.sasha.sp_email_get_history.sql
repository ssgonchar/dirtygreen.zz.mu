delimiter $$

DROP PROCEDURE IF EXISTS `sp_email_get_history`$$
CREATE PROCEDURE `sp_email_get_history`(IN param_email_id INT)
BEGIN

    DECLARE var_group_id INT DEFAULT 0;
    
    SET var_group_id = (SELECT group_id FROM email_groups WHERE email_id = param_email_id);

    SELECT eg.*, e.is_sent 
    FROM email_groups AS eg
    INNER JOIN emails AS e ON e.id = eg.email_id 
    WHERE eg.group_id = var_group_id 
    ORDER BY eg.created_at DESC;
END$$

delimiter ;


