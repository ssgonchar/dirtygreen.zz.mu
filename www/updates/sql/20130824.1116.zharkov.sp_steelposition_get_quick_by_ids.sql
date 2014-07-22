-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 4.0.224.1
-- Дата: 24.08.2013 11:16:26
-- Версия сервера: 5.1.41
-- Версия клиента: 4.1

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_steelposition_get_quick_by_ids$$
CREATE PROCEDURE sp_steelposition_get_quick_by_ids(IN param_ids VARCHAR(1100))
sp:
BEGIN

    IF param_ids = '' 
    THEN
        SELECT -1 AS ErrorCode, 'sp_steelposition_get_quick_by_ids' AS ErrorAt;
        LEAVE sp;
    END IF;
    
    SET @var_stmt := CONCAT(    
        "SELECT 
            id,
            (SELECT SUM(qtty) FROM steelpositions_reserved WHERE steelposition_id = steelpositions.id) AS reserved,
            IFNULL((
                SELECT
                    GROUP_CONCAT(
                        CASE 
                            WHEN parent_id = 0 AND guid NOT IN ('') THEN guid
                            WHEN parent_id > 0 THEN (SELECT IF(guid != '', CONCAT('alias ', guid), '') FROM steelitems WHERE id = s.parent_id)
                            ELSE ''
                        END 
                    SEPARATOR ',')
                FROM steelitems s
                WHERE s.is_locked = 0 
                AND s.is_available = 1 
                AND s.is_deleted = 0 
                AND s.steelposition_id = steelpositions.id 
            ), '') AS plate_ids,
            IFNULL((
				SELECT 
					GROUP_CONCAT(DISTINCT locations.title SEPARATOR ', ') 
				FROM steelitems 
				JOIN locations ON locations.id = steelitems.location_id
				WHERE steelitems.is_locked = 0 
				AND steelitems.is_available = 1 
                AND steelitems.is_deleted = 0 
				AND steelposition_id = steelpositions.id
			), '') AS locations,
            IFNULL((
				SELECT 
					GROUP_CONCAT(DISTINCT companies.int_location_title SEPARATOR ', ') 
				FROM steelitems 
				JOIN companies ON companies.id = steelitems.stockholder_id
				WHERE steelitems.is_locked = 0 
				AND steelitems.is_available = 1 
                AND steelitems.is_deleted = 0 
				AND steelposition_id = steelpositions.id
			), '') AS int_locations,
            IFNULL((
				SELECT 
					GROUP_CONCAT(DISTINCT supplier_id SEPARATOR ', ') 
				FROM steelitems 
				WHERE is_locked = 0 
				AND is_available = 1 
                AND is_deleted = 0 
				AND steelposition_id = steelpositions.id 
				AND supplier_id NOT IN (0)
			), '') AS supplier_ids
        FROM steelpositions
        WHERE id IN (", param_ids, ")
        LIMIT 100;");


    PREPARE stmt FROM @var_stmt;
    EXECUTE stmt;    

END
$$

DELIMITER ;
