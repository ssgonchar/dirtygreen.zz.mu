-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 5.0.97.1
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 02/08/2013 10:16:15
-- Версия сервера: 5.5.27
-- Версия клиента: 4.1

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_ra_item_add$$
CREATE PROCEDURE sp_ra_item_add(
	IN param_user_id INT,
	IN param_parent_id INT,
	IN param_ra_id INT,
	IN param_steelitem_id INT
)
sp:
BEGIN

	DECLARE ITEM_STATUS_RELEASED            TINYINT DEFAULT 5;

	DECLARE var_ra_stockholder_id 			INT DEFAULT 0;	
    DECLARE var_steelitem_unitweight_ton	DECIMAL(10,4) DEFAULT 0;
	DECLARE var_steelitem_stockholder_id	INT DEFAULT 0;
	DECLARE var_steelitem_owner_id 			INT DEFAULT 0;
    DECLARE var_steelitem_status_id 		TINYINT DEFAULT 0;


    START TRANSACTION;
		
        SET var_ra_stockholder_id = IFNULL((SELECT stockholder_id FROM ra WHERE id = param_ra_id), 0);
		
		SELECT 
            IFNULL(stockholder_id, 0), 
            IFNULL(owner_id, 0),
            IFNULL(status_id, 0),
            IFNULL(unitweight_ton, 0)
		INTO 
            var_steelitem_stockholder_id, 
            var_steelitem_owner_id,
            var_steelitem_status_id,
            var_steelitem_unitweight_ton
		FROM steelitems
		WHERE id = param_steelitem_id;


		IF var_steelitem_stockholder_id != var_ra_stockholder_id 
            OR var_steelitem_owner_id = 0 
            #OR var_steelitem_status_id >= ITEM_STATUS_RELEASED
		THEN
			LEAVE sp;
		END IF;

        
        INSERT IGNORE INTO ra_items
        SET
            ra_id           = param_ra_id, 
            parent_id       = param_parent_id, 
            steelitem_id    = param_steelitem_id, 
			owner_id    	= var_steelitem_owner_id,
			weight    		= var_steelitem_unitweight_ton,
			weighed_weight	= var_steelitem_unitweight_ton,
            created_at      = NOW(), 
            created_by      = param_user_id, 
            modified_at     = NOW(), 
            modified_by     = param_user_id;


        IF param_parent_id = 0
        THEN
            
            INSERT IGNORE INTO attachment_objects(attachment_id, `type`, object_alias, object_id, created_at, created_by)
            SELECT
                attachment_id,
                `type`,
                'steelitem',
                param_steelitem_id,
                NOW(),
                param_user_id
            FROM attachment_objects
            WHERE object_alias = 'ra'
            AND object_id = param_ra_id;


            CALL sp_ra_item_save_timeline(param_user_id, param_steelitem_id, param_ra_id);
            CALL sp_ra_items_recalculate_ww(param_ra_id);
            CALL sp_ra_update_related_docs(param_user_id, param_ra_id);
        
        END IF;
    
    COMMIT;

END
$$

DELIMITER ;
