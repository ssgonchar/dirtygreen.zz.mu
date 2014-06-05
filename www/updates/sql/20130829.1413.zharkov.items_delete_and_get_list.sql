-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 4.50.303.1
-- Дата: 29.08.2013 14:13:06
-- Версия сервера: 5.1.41
-- Версия клиента: 4.1

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_steelitems_erase$$
CREATE PROCEDURE sp_steelitems_erase(param_ids VARCHAR(1100))
BEGIN

    SET @var_stmt = CONCAT("
        DELETE FROM steelitems 
        WHERE id IN (", param_ids, ");
    ");
    
    PREPARE stmt FROM @var_stmt;
    EXECUTE stmt;        


    SET @var_stmt = CONCAT("
        DELETE FROM steelitems_history
        WHERE steelitem_id IN (", param_ids, ");
    ");
    
    PREPARE stmt FROM @var_stmt;
    EXECUTE stmt;        


    SET @var_stmt = CONCAT("
        DELETE FROM steelitem_timeline 
        WHERE steelitem_id IN (", param_ids, ");
    ");
    
    PREPARE stmt FROM @var_stmt;
    EXECUTE stmt;        


    SET @var_stmt = CONCAT("
        DELETE FROM steelitem_properties
        WHERE item_id IN (", param_ids, ");
    ");
    
    PREPARE stmt FROM @var_stmt;
    EXECUTE stmt;        


    SET @var_stmt = CONCAT("
        DELETE FROM steelitem_properties_history
        WHERE item_id IN (", param_ids, ");
    ");
    
    PREPARE stmt FROM @var_stmt;
    EXECUTE stmt;

END
$$

DROP PROCEDURE IF EXISTS sp_steelitem_get_list$$
CREATE PROCEDURE sp_steelitem_get_list(param_user_id INT, param_stock_id INT, param_locations VARCHAR(100), param_deliverytimes VARCHAR(100), 
                                        param_is_real TINYINT, param_is_virtual TINYINT, param_is_twin TINYINT, param_is_cut TINYINT, 
                                        param_steelgrade_id INT, 
                                        param_thickness_from DECIMAL(10,4), param_thickness_to DECIMAL(10,4), 
                                        param_width_from DECIMAL(10,4), param_width_to DECIMAL(10,4), 
                                        param_length_from DECIMAL(10,4), param_length_to DECIMAL(10,4), 
                                        param_weight_from DECIMAL(10,4), param_weight_to DECIMAL(10,4), 
                                        param_keyword VARCHAR(50), param_plateid VARCHAR(32), param_available TINYINT, param_order_id INT,
										param_revision VARCHAR(12))
sp:
BEGIN
    
    DECLARE var_where           VARCHAR(4000) DEFAULT '';
    DECLARE var_type            VARCHAR(1000) DEFAULT '';
    DECLARE var_rev             VARCHAR(50) DEFAULT '';
    DECLARE var_order_ids       VARCHAR(4000) DEFAULT '';
    DECLARE var_location_ids    VARCHAR(4000) DEFAULT '';
    

    IF param_stock_id = 0 AND TRIM(param_plateid) = ''
    THEN
        SELECT -1 AS ErrorCode, 'sp_steelitem_get_list' AS ErrorAt;
        LEAVE sp;
    END IF;


    SET var_rev     = IF(TRIM(param_revision), CONCAT("_history_", param_revision), "");
    SET var_where   = " WHERE si.is_deleted = 0 ";  

    
    IF TRIM(param_locations) = '' 
    THEN 
        IF param_order_id = 0 AND TRIM(param_plateid) = ''
        THEN
            SET var_location_ids = (
                SELECT 
                    GROUP_CONCAT(DISTINCT si.stockholder_id SEPARATOR ",") 
                FROM steelitems AS si 
                JOIN steelpositions AS sp ON sp.id = si.steelposition_id 
                WHERE sp.stock_id = param_stock_id 
                AND si.stockholder_id > 0 
                AND si.is_deleted = 0
            );
            
            IF TRIM(var_location_ids) != ''
            THEN            
                SET var_where = CONCAT(var_where, " AND si.stockholder_id IN (", var_location_ids, ")");            
            END IF;            
        END IF;        
    ELSE
        SET var_where = CONCAT(var_where, " AND si.stockholder_id IN (", param_locations, ")");     
    END IF;


    IF TRIM(param_plateid) != '' 
    THEN 
        SET var_where = CONCAT(var_where, " AND (si.guid LIKE '%", param_plateid, "%' OR id = ", param_plateid, ")"); 
    ELSE

        IF TRIM(param_deliverytimes) != '' 
        THEN 
            SET var_where = CONCAT(var_where, " AND si.deliverytime_id IN (", param_deliverytimes, ")"); 
        END IF;
    
        IF param_steelgrade_id > 0 
        THEN 
            SET var_where = CONCAT(var_where, " AND si.steelgrade_id = ", param_steelgrade_id); 
        END IF;
    
        IF param_is_real > 0
        THEN
            IF TRIM(var_type) != '' THEN SET var_type = CONCAT(var_type, " OR "); END IF;
            SET var_type = CONCAT(var_type, "si.is_virtual = 0"); 
        END IF;
    
        IF param_is_virtual > 0
        THEN
            IF TRIM(var_type) != '' THEN SET var_type = CONCAT(var_type, " OR "); END IF;
            SET var_type = CONCAT(var_type, "(si.is_virtual = 1 AND si.parent_id = 0)"); 
        END IF;
    
        IF param_is_twin > 0
        THEN
            IF TRIM(var_type) != '' THEN SET var_type = CONCAT(var_type, " OR "); END IF;
            SET var_type = CONCAT(var_type, "(si.parent_id > 0 AND rel = 't')"); 
        END IF;
    
        IF param_is_cut > 0
        THEN
            IF TRIM(var_type) != '' THEN SET var_type = CONCAT(var_type, " OR "); END IF;
            SET var_type = CONCAT(var_type, "(si.parent_id > 0 AND rel = 'c')"); 
        END IF;
    
        IF TRIM(var_type) != ''
        THEN
            SET var_where = CONCAT(var_where, " AND (", var_type, ")"); 
        END IF;
    
    	IF param_order_id > 0
        THEN
            SET var_where = CONCAT(var_where, " AND si.order_id = ", param_order_id); 
        ELSEIF param_available > 0
        THEN
            SET var_where = CONCAT(var_where, " AND si.is_available = 1"); 
        ELSE
            SET var_order_ids = IFNULL((SELECT GROUP_CONCAT(id SEPARATOR ",") FROM orders WHERE `status` = 'ip'), '0');
            
            IF var_order_ids = '0'
            THEN
                SET var_where = CONCAT(var_where, " AND si.is_available = 1");    
            ELSE
                SET var_where = CONCAT(var_where, " AND (si.is_available = 1 OR order_id IN (", var_order_ids, "))");
            END IF;
        END IF;
    
        IF param_thickness_from > 0 OR param_thickness_to > 0
        THEN 
            
            IF param_thickness_from > 0 AND param_thickness_to > 0
            THEN
                SET var_where = CONCAT(var_where, " AND (si.thickness_mm >= ", param_thickness_from, " AND si.thickness_mm <= ", param_thickness_to, ")"); 
            ELSEIF param_thickness_from > 0
            THEN
                SET var_where = CONCAT(var_where, " AND si.thickness_mm >= ", param_thickness_from); 
            ELSE
                SET var_where = CONCAT(var_where, " AND si.thickness_mm <= ", param_thickness_to); 
            END IF;
                    
        END IF;
        
        IF param_width_from > 0 OR param_width_to > 0
        THEN 
            
            IF param_width_from > 0 AND param_width_to > 0
            THEN
                SET var_where = CONCAT(var_where, " AND (si.width_mm >= ", param_width_from, " AND si.width_mm <= ", param_width_to, ")"); 
            ELSEIF param_width_from > 0
            THEN
                SET var_where = CONCAT(var_where, " AND si.width_mm >= ", param_width_from); 
            ELSE
                SET var_where = CONCAT(var_where, " AND si.width_mm <= ", param_width_to); 
            END IF;
                    
        END IF;
    
        IF param_length_from > 0 OR param_length_to > 0
        THEN 
            
            IF param_length_from > 0 AND param_length_to > 0
            THEN
                SET var_where = CONCAT(var_where, " AND (si.length_mm >= ", param_length_from, " AND si.length_mm <= ", param_length_to, ")"); 
            ELSEIF param_length_from > 0
            THEN
                SET var_where = CONCAT(var_where, " AND si.length_mm >= ", param_length_from); 
            ELSE
                SET var_where = CONCAT(var_where, " AND si.length_mm <= ", param_length_to); 
            END IF;
                    
        END IF;
    
        IF param_weight_from > 0 OR param_weight_to > 0
        THEN 
            
            IF param_weight_from > 0 AND param_weight_to > 0
            THEN
                SET var_where = CONCAT(var_where, " AND (si.unitweight_ton >= ", param_weight_from, " AND si.unitweight_ton <= ", param_weight_to, ")"); 
            ELSEIF param_weight_from > 0
            THEN
                SET var_where = CONCAT(var_where, " AND si.unitweight_ton >= ", param_weight_from); 
            ELSE
                SET var_where = CONCAT(var_where, " AND si.unitweight_ton <= ", param_weight_to); 
            END IF;
                    
        END IF;
    
        IF TRIM(param_keyword) != ''
        THEN
            SET var_where = CONCAT(var_where, " AND (si.notes LIKE '%", param_keyword, "%' OR si.internal_notes LIKE '%", param_keyword, "%')"); 
        END IF;

    END IF;

    
    DROP TEMPORARY TABLE IF EXISTS t_items;
    CREATE TEMPORARY TABLE t_items(id INT, steelgrade VARCHAR(50), thickness DECIMAL(10,4), width DECIMAL(10, 4), `length` DECIMAL(10,4));

    SET @var_stmt = CONCAT("
        INSERT INTO t_items(id, steelgrade, thickness, width, `length`)
        SELECT 
            si.id,
            (SELECT alias FROM steelgrades WHERE id = si.steelgrade_id),
            si.thickness_mm,
            si.width_mm,
            si.length_mm
        FROM steelitems", var_rev, " AS si", var_where
    );

    PREPARE stmt FROM @var_stmt;
    EXECUTE stmt;

    SELECT 
        id AS steelitem_id 
    FROM t_items
    ORDER BY steelgrade, thickness, width, `length`;


    DROP TEMPORARY TABLE IF EXISTS t_items;


END
$$

DROP PROCEDURE IF EXISTS sp_steelitem_remove$$
CREATE PROCEDURE sp_steelitem_remove(param_user_id INT, param_id INT)
sp:
BEGIN

    DECLARE var_steelitem_ids       VARCHAR(1100) DEFAULT '';
    DECLARE var_parent_id           INT DEFAULT 0;
    DECLARE ITEM_STATUS_RELEASED    TINYINT DEFAULT 5;

    IF NOT EXISTS (SELECT * FROM steelitems WHERE id = param_id)
    THEN
        SELECT -1 AS ErrorCode, 'sp_steelitem_remove' AS ErrorAt;
        LEAVE sp;
    END IF;

    IF EXISTS (SELECT * FROM steelitems WHERE id = param_id AND is_locked = 1)
    THEN
        SELECT -2 AS ErrorCode, 'sp_steelitem_remove' AS ErrorAt;
        LEAVE sp;
    END IF;

    IF EXISTS (SELECT * FROM steelitems WHERE parent_id = param_id AND is_locked = 1)
    THEN
        SELECT -3 AS ErrorCode, 'sp_steelitem_remove' AS ErrorAt;
        LEAVE sp;
    END IF;

    IF EXISTS (SELECT * FROM steelitems WHERE id = param_id AND (TRIM(guid) != '' OR in_ddt_id > 0 OR supplier_invoice_id > 0 OR status_id >= ITEM_STATUS_RELEASED))
    THEN
        SELECT -4 AS ErrorCode, 'sp_steelitem_remove' AS ErrorAt;
        LEAVE sp;
    END IF;

    
    SET var_parent_id = (SELECT parent_id FROM steelitems WHERE id = param_id);


    IF var_parent_id = 0
    THEN
        
        SELECT 
            id AS steelitem_id,
            steelposition_id,
            order_id
        FROM steelitems
        WHERE parent_id = param_id
        OR id = param_id;

        SET var_steelitem_ids = (SELECT GROUP_CONCAT(id) FROM steelitems WHERE parent_id = param_id OR id = param_id);
        CALL sp_steelitems_erase(var_steelitem_ids);
/*        
        DELETE FROM steelitems WHERE parent_id = param_id;
        DELETE FROM steelitems WHERE id = param_id;
*/

    ELSE
        
        SELECT 
            id AS steelitem_id,
            steelposition_id,
            order_id 
        FROM steelitems
        WHERE parent_id = var_parent_id 
        OR id = var_parent_id;
        
        CALL sp_steelitems_erase(param_id);
        #DELETE FROM steelitems WHERE id = param_id;

        UPDATE steelitems
        SET
            is_locked       = sf_item_check_lock(id, parent_id),
            is_conflicted   = sf_item_check_conflict(id, parent_id)
        WHERE parent_id = var_parent_id 
        OR id = var_parent_id;

    END IF;

END
$$

DELIMITER ;
