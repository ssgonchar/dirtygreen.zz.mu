ALTER TABLE `service` ADD COLUMN `sentemail_id` INT(11) NULL DEFAULT NULL  AFTER `bizblog_message_id` ;

delimiter $$

DROP PROCEDURE IF EXISTS `sp_email_get_list_attach_to_biz`$$
CREATE PROCEDURE `sp_email_get_list_attach_to_biz`()
BEGIN

    DECLARE var_sentemail_id INT DEFAULT 0;
    DECLARE EMAIL_TYPE_OUTBOX INT DEFAULT 2;
    DECLARE EMAIL_TYPE_DRAFT INT DEFAULT 3;

    SET var_sentemail_id = (SELECT sentemail_id FROM service); 

    IF var_sentemail_id IS NULL
    THEN
        SET var_sentemail_id = (SELECT MIN(id) 
                                FROM emails 
                                WHERE type_id = EMAIL_TYPE_OUTBOX 
                                OR type_id = EMAIL_TYPE_DRAFT); 

        UPDATE service
        SET 
            sentemail_id = var_sentemail_id;
    END IF;

    SELECT 
        id AS email_id 
    FROM emails 
    WHERE id >= var_sentemail_id 
    AND (type_id = EMAIL_TYPE_OUTBOX OR type_id = EMAIL_TYPE_DRAFT) 
    ORDER BY id 
    LIMIT 0, 50;
END$$

delimiter ;


