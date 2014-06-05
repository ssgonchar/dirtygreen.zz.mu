UPDATE steelpositions 
SET 
	price_unit	= 'cwt',
	price 		= price * 100
WHERE is_deleted = 0 AND is_locked = 0 
AND stock_id = 2 AND qtty > 0;

UPDATE steelitems
SET 
	price_unit	= (SELECT price_unit FROM steelpositions WHERE id = steelposition_id),
	price 		= (SELECT price FROM steelpositions WHERE id = steelposition_id);

UPDATE orders SET price_unit = weight_unit;


DELIMITER $$

DROP PROCEDURE IF EXISTS sp_steelposition_save$$
CREATE PROCEDURE sp_steelposition_save(param_user_id INT, param_id INT, param_stock_id INT, param_product_id INT, param_biz_id INT, 
                                param_dimension_unit CHAR(10), param_weight_unit CHAR(10), param_price_unit CHAR(10), param_currency CHAR(3),
                                param_steelgrade_id INT, param_thickness CHAR(10), param_thickness_mm DECIMAL(10,4), 
                                param_width CHAR(10), param_width_mm DECIMAL(10,4), 
                                param_length CHAR(10), param_length_mm DECIMAL(10,4), 
                                param_unitweight CHAR(10), param_unitweight_ton DECIMAL(10,4), 
                                param_qtty INT, 
                                param_weight CHAR(10), param_weight_ton DECIMAL(10,4), 
                                param_price DECIMAL(10,4), param_value DECIMAL(20,4), 
                                param_deliverytime_id INT, 
                                param_notes VARCHAR(250), param_internal_notes VARCHAR(250))
sp:
BEGIN

    DECLARE var_qtty INT DEFAULT 0;
    
    IF param_id > 0
    THEN
        
        IF NOT EXISTS (SELECT * FROM steelpositions WHERE id = param_id)
        THEN 
            SELECT -1 as ErrorCode, 'sp_steelposition_save' AS ErrorAt;
            LEAVE sp;
        END IF;

        
        SET var_qtty    = sf_steelposition_get_qtty(param_id);
        SET param_value = param_price * param_unitweight * var_qtty;

        IF param_weight_unit = 'lb' AND param_price_unit = 'cwt'
        THEN
            
            SET param_value = param_value / 100;

        END IF;

        IF var_qtty = 0 
        THEN

            CALL sp_steelposition_remove(param_user_id, param_id);
            LEAVE sp;

        END IF;

        
        UPDATE steelpositions
        SET
            biz_id              = CASE WHEN param_biz_id = 0 THEN biz_id ELSE param_biz_id END,
            steelgrade_id       = param_steelgrade_id,
            thickness           = param_thickness,
            thickness_mm        = param_thickness_mm,
            width               = param_width,
            width_mm            = param_width_mm,
            `length`            = param_length,
            length_mm           = param_length_mm,
            unitweight          = param_unitweight,
            unitweight_ton      = param_unitweight_ton,
            weight              = param_unitweight * var_qtty, 
            weight_ton          = param_unitweight_ton * var_qtty, 
            price               = param_price,
            `value`             = param_value,
            deliverytime_id     = param_deliverytime_id,
            notes               = param_notes,
            internal_notes      = param_internal_notes,
            qtty                = var_qtty,
            modified_at         = NOW(),
            modified_by         = param_user_id
        WHERE id = param_id;

        
        UPDATE steelitems
        SET
            price   = param_price,
            `value` = param_value
        WHERE steelposition_id = param_id;
    
    ELSE

        START TRANSACTION;

            INSERT INTO steelpositions
            SET
                stock_id        = param_stock_id,
                product_id      = param_product_id,
                biz_id          = param_biz_id,
                dimension_unit  = param_dimension_unit,
                weight_unit     = param_weight_unit,
                price_unit      = param_price_unit,
                currency        = param_currency,
                steelgrade_id   = param_steelgrade_id,
                thickness       = param_thickness,
                thickness_mm    = param_thickness_mm,
                width           = param_width,
                width_mm        = param_width_mm,
                length          = param_length,
                length_mm       = param_length_mm,
                unitweight      = param_unitweight,
                unitweight_ton  = param_unitweight_ton,
                qtty            = param_qtty,
                weight          = param_weight,
                weight_ton      = param_weight_ton,
                price           = param_price,
                `value`         = param_value,
                deliverytime_id = param_deliverytime_id,
                notes           = param_notes,
                internal_notes  = param_internal_notes,
                is_from_order   = IF(param_stock_id > 0, 0, 1),
                is_deleted      = 0,
                is_reserved     = 0,
                is_locked       = 0,
                tech_action     = '',
                created_at      = NOW(),
                created_by      = param_user_id,
                modified_at     = NOW(),
                modified_by     = param_user_id;
    
            SET param_id = (SELECT MAX(id) FROM steelpositions WHERE created_by = param_user_id);

        COMMIT;    

    END IF;


    SELECT param_id AS id;

END
$$

DROP PROCEDURE IF EXISTS sp_steelposition_reserve_add$$
CREATE PROCEDURE sp_steelposition_reserve_add(param_user_id INT, param_position_id INT, param_qtty INT, param_company_id INT, param_person_id INT, param_period INT, param_order_id INT)
BEGIN

    IF EXISTS (SELECT * FROM steelpositions_reserved WHERE steelposition_id = param_position_id AND company_id = param_company_id AND person_id = param_person_id AND order_id = param_order_id)
    THEN

        UPDATE steelpositions_reserved
        SET
            qtty        = param_qtty,
            period      = param_period,
            expire_at   = TIMESTAMPADD(HOUR, param_period, NOW()),
            modified_at = NOW(),
            modified_by = param_user_id
        WHERE steelposition_id = param_position_id 
        AND company_id = param_company_id 
        AND person_id = param_person_id
        AND order_id = param_order_id;

    ELSE

        INSERT INTO steelpositions_reserved
        SET
            steelposition_id    = param_position_id,
            company_id          = param_company_id,
            person_id           = param_person_id,
            qtty                = param_qtty,
            period              = param_period,
            expire_at           = TIMESTAMPADD(HOUR, param_period, NOW()),
            order_id            = param_order_id,
            created_at          = NOW(),
            created_by          = param_user_id,
            modified_at         = NOW(),
            modified_by         = param_user_id;

    END IF;

    
    CALL sp_steelposition_update_qtty(param_user_id, param_position_id);

    
    SELECT * FROM steelpositions WHERE id = param_position_id;

END
$$

DROP PROCEDURE IF EXISTS sp_steelposition_update_qtty$$
CREATE PROCEDURE sp_steelposition_update_qtty(param_user_id INT, param_id INT)
BEGIN

    DECLARE var_qtty                    INT DEFAULT 0;
    DECLARE var_reserved_qtty           INT DEFAULT 0;
    DECLARE var_unlocked_items_qtty     INT DEFAULT 0;


    IF NOT EXISTS (SELECT * FROM steelitems WHERE steelposition_id = param_id)
    THEN

        CALL sp_steelposition_remove(param_user_id, param_id);

    ELSE

        SET var_qtty                    = sf_steelposition_get_qtty(param_id);
        SET var_reserved_qtty           = IFNULL((SELECT SUM(qtty) FROM steelpositions_reserved WHERE steelposition_id = param_id), 0);
        SET var_unlocked_items_qtty     = (SELECT COUNT(*) FROM steelitems WHERE steelposition_id = param_id AND is_locked = 0);

        UPDATE steelpositions
        SET
            modified_at = NOW(),
            modified_by = param_user_id,
            qtty        = var_qtty,
            weight      = unitweight * var_qtty,
            weight_ton  = unitweight_ton * var_qtty,
            `value`     = IF(weight_unit = 'lb' AND price_unit = 'cwt', price * unitweight * var_qtty / 100, price * unitweight * var_qtty),
            is_reserved = IF(var_reserved_qtty > 0 AND var_reserved_qtty >= var_qtty, 1, 0),
            is_locked   = IF(var_unlocked_items_qtty = 0, 1, 0),
            is_deleted  = IF(var_qtty > 0, 0, is_deleted)
        WHERE id = param_id;
                
    END IF;

END
$$

DROP PROCEDURE IF EXISTS sp_preorder_save$$
CREATE PROCEDURE sp_preorder_save(param_user_id INT, param_guid VARCHAR(32), param_order_for CHAR(5), param_biz_id INT,
    param_company_id INT, param_person_id INT, param_buyer_ref VARCHAR(50), param_supplier_ref VARCHAR(50),
    param_delivery_point CHAR(3), param_delivery_town VARCHAR(250), param_delivery_cost VARCHAR(250),
    param_delivery_date VARCHAR(250), param_alert_date TIMESTAMP, param_invoicingtype_id INT,
    param_paymenttype_id INT, param_status CHAR(2), 
    param_dimension_unit CHAR(10), param_weight_unit CHAR(10), param_price_unit CHAR(10), param_currency CHAR(3),
    param_description TEXT)
BEGIN


    IF EXISTS (SELECT * FROM preorders WHERE guid = param_guid)
    THEN

        UPDATE preorders
        SET
            order_for           = param_order_for,
            biz_id              = param_biz_id,
            company_id          = param_company_id,
            person_id           = param_person_id,
            buyer_ref           = param_buyer_ref,
            supplier_ref        = param_supplier_ref,
            delivery_point      = param_delivery_point,
            delivery_town       = param_delivery_town,
            delivery_cost       = param_delivery_cost,
            delivery_date       = param_delivery_date,
            alert_date          = param_alert_date,
            invoicingtype_id    = param_invoicingtype_id,
            paymenttype_id      = param_paymenttype_id,
            dimension_unit      = param_dimension_unit,
            weight_unit         = param_weight_unit,
            price_unit          = param_price_unit,
            currency            = param_currency,
            description         = param_description,
            modified_at         = NOW(),
            modified_by         = param_user_id
        WHERE guid = param_guid;
    
    ELSE

        INSERT INTO preorders
        SET
            guid                = param_guid,
            order_for           = param_order_for,
            biz_id              = param_biz_id,
            company_id          = param_company_id,
            person_id           = param_person_id,
            buyer_ref           = param_buyer_ref,
            supplier_ref        = param_supplier_ref,
            delivery_point      = param_delivery_point,
            delivery_town       = param_delivery_town,
            delivery_cost       = param_delivery_cost,
            delivery_date       = param_delivery_date,
            alert_date          = param_alert_date,
            invoicingtype_id    = param_invoicingtype_id,
            paymenttype_id      = param_paymenttype_id,
            `status`            = '',
            dimension_unit      = param_dimension_unit,
            weight_unit         = param_weight_unit,
            price_unit          = param_price_unit,
            currency            = param_currency,
            description         = param_description,
            created_at          = NOW(),
            created_by          = param_user_id,
            modified_at         = NOW(),
            modified_by         = param_user_id;
    
    END IF;



    SELECT param_guid AS guid;

END
$$

DROP PROCEDURE IF EXISTS sp_preorder_position_add_from_stock$$
CREATE PROCEDURE sp_preorder_position_add_from_stock(param_user_id INT, param_guid VARCHAR(32), param_position_id INT)
sp:
BEGIN

    DECLARE var_qtty        INT DEFAULT 0;
    DECLARE var_weight_unit CHAR(10) DEFAULT '';
    DECLARE var_price_unit  CHAR(10) DEFAULT '';

    
    SELECT
        weight_unit,
        price_unit
    INTO
        var_weight_unit,
        var_price_unit
    FROM steelpositions
    WHERE id = param_position_id;
    

    IF EXISTS (SELECT * FROM preorder_positions WHERE guid = param_guid AND position_id = param_position_id)
    THEN

        SET var_qtty = IFNULL((SELECT COUNT(*) FROM preorder_items WHERE guid = param_guid AND position_id = param_position_id), 0);

        IF var_qtty = 0
        THEN
            SET var_qtty = (SELECT qtty FROM steelpositions WHERE id = param_position_id);
        END IF;

        UPDATE preorder_positions
        SET
            qtty        = var_qtty,
            weight      = unitweight * var_qtty,
            weight_ton  = unitweight_ton * var_qtty,
            `value`     = IF (var_weight_unit = 'lb' AND var_price_unit = 'cwt', unitweight * var_qtty * price / 100, unitweight * var_qtty * price)
        WHERE guid = param_guid 
        AND position_id = param_position_id;

    ELSE

        IF EXISTS (SELECT * FROM preorder_items WHERE guid = param_guid AND position_id = param_position_id)
        THEN
            SET var_qtty = (SELECT COUNT(*) FROM preorder_items WHERE guid = param_guid AND position_id = param_position_id);
        ELSE
            SET var_qtty = (SELECT qtty FROM steelpositions WHERE id = param_position_id);
        END IF;
        
        INSERT IGNORE INTO preorder_positions(
            guid,
            position_id,
            steelgrade_id,
            thickness,
            thickness_mm,
            width,
            width_mm,
            length,
            length_mm,
            unitweight,
            unitweight_ton,
            qtty,
            weight,
            weight_ton,
            price,
            `value`,
            internal_notes,
            is_saved,
            created_at,
            created_by,
            modified_at,
            modified_by)
        SELECT 
            param_guid,  
            id,
            steelgrade_id,
            thickness,
            thickness_mm,
            width,
            width_mm,
            length,
            length_mm,
            unitweight,
            unitweight_ton,
            var_qtty,
            unitweight * var_qtty,
            unitweight_ton * var_qtty,
            price,
            IF (var_weight_unit = 'lb' AND var_price_unit = 'cwt', unitweight * var_qtty * price / 100, unitweight * var_qtty * price),
            internal_notes,
            0,
            NOW(),
            param_user_id,
            NOW(),
            param_user_id
        FROM steelpositions
        WHERE id = param_position_id;

    END IF;

END
$$

DROP PROCEDURE IF EXISTS sp_order_position_add_from_stock$$
CREATE PROCEDURE sp_order_position_add_from_stock(param_user_id INT, param_order_id INT, param_position_id INT, param_qtty INT)
sp:
BEGIN

    DECLARE var_biz_id          INT DEFAULT 0;
    DECLARE var_order_status    CHAR(2) DEFAULT '';
    DECLARE var_dimension_unit  CHAR(10) DEFAULT '';
    DECLARE var_weight_unit     CHAR(10) DEFAULT '';
    DECLARE var_price_unit      CHAR(10) DEFAULT '';
    DECLARE var_currency        CHAR(3) DEFAULT '';


    SELECT 
        biz_id,
        `status`
    INTO
        var_biz_id,
        var_order_status
    FROM orders 
    WHERE id = param_order_id;


    SELECT 
        dimension_unit,
        weight_unit,
        price_unit,
        currency
    INTO
        var_dimension_unit,
        var_weight_unit,
        var_price_unit,
        var_currency
    FROM steelpositions 
    WHERE id = param_position_id;


    IF EXISTS (SELECT * FROM order_positions WHERE order_id = param_order_id AND position_id = param_position_id)
    THEN

        SELECT
            var_biz_id          AS biz_id,
            var_order_status    AS order_status,
            var_dimension_unit  AS dimension_unit,
            var_weight_unit     AS weight_unit,
            var_price_unit      AS price_unit,
            var_currency        AS currency,
            steelgrade_id,
            thickness,
            thickness_mm,
            width,
            width_mm,
            `length`,
            length_mm,
            unitweight,
            unitweight_ton,
            (qtty + param_qtty) AS qtty,
            ((qtty + param_qtty) * unitweight) AS weight,
            ((qtty + param_qtty) * unitweight_ton) AS weight_ton,
            price,
            CASE WHEN var_weight_unit = 'lb' AND var_price_unit = 'cwt'
            THEN ((qtty + param_qtty) * unitweight) * price  / 100
            ELSE ((qtty + param_qtty) * unitweight) * price
            END AS `value`,            
            deliverytime,
            internal_notes
        FROM order_positions
        WHERE order_id = param_order_id
        AND position_id = param_position_id;

    ELSE

        SELECT
            var_biz_id          AS biz_id,
            var_order_status    AS order_status,
            var_dimension_unit  AS dimension_unit,
            var_weight_unit     AS weight_unit,
            var_price_unit      AS price_unit,
            var_currency        AS currency,
            steelgrade_id,
            thickness,
            thickness_mm,
            width,
            width_mm,
            `length`,
            length_mm,
            unitweight,
            unitweight_ton,
            param_qtty AS qtty,
            param_qtty * unitweight AS weight,
            param_qtty * unitweight_ton AS weight_ton,
            price,
            CASE WHEN var_weight_unit = 'lb' AND var_price_unit = 'cwt'
            THEN param_qtty * unitweight * price  / 100
            ELSE param_qtty * unitweight * price
            END AS `value`,
            '' AS deliverytime,
            '' AS internal_notes
        FROM steelpositions
        WHERE id = param_position_id;
        
    END IF;

END
$$

DROP PROCEDURE IF EXISTS sp_order_save_position$$
CREATE PROCEDURE sp_order_save_position(param_user_id INT, param_order_id INT, param_position_id INT, param_steelgrade_id INT, 
                            param_thickness CHAR(10), param_thickness_mm DECIMAL(10, 4), 
                            param_width CHAR(10), param_width_mm DECIMAL(10, 4), 
                            param_length CHAR(10), param_length_mm DECIMAL(10, 4),
                            param_unitweight CHAR(10), param_unitweight_ton DECIMAL(10, 4),
                            param_qtty INT, param_weight CHAR(10), param_weight_ton DECIMAL(10, 4),
                            param_price DECIMAL(10, 4), param_value DECIMAL(10, 4), 
                            param_deliverytime VARCHAR(250), param_internal_notes VARCHAR(250), param_order_status CHAR(2))
sp:
BEGIN

    DECLARE var_prev_order_qtty INT DEFAULT 0;
    DECLARE var_order_qtty      INT DEFAULT 0;
    DECLARE var_delta_qtty      INT DEFAULT 0;

    
    IF EXISTS (SELECT * FROM order_positions WHERE order_id = param_order_id AND position_id = param_position_id)
    THEN
        
        SET var_prev_order_qtty = (SELECT qtty FROM order_positions WHERE order_id = param_order_id AND position_id = param_position_id);

        UPDATE order_positions
        SET
            steelgrade_id   = param_steelgrade_id,
            thickness       = param_thickness,
            thickness_mm    = param_thickness_mm,
            width           = param_width,
            width_mm        = param_width_mm,
            `length`        = param_length,
            length_mm       = param_length_mm,
            unitweight      = param_unitweight,
            unitweight_ton  = param_unitweight_ton,
            qtty            = param_qtty,
            weight          = param_weight,
            weight_ton      = param_weight_ton,
            price           = param_price,
            `value`         = param_value,
            deliverytime    = param_deliverytime,
            internal_notes  = param_internal_notes,
            modified_at     = NOW(),
            modified_by     = param_user_id
        WHERE order_id = param_order_id
        AND position_id = param_position_id;
        
    ELSE
        
        INSERT order_positions
        SET
            order_id        = param_order_id,
            position_id     = param_position_id,
            steelgrade_id   = param_steelgrade_id,
            thickness       = param_thickness,
            thickness_mm    = param_thickness_mm,
            width           = param_width,
            width_mm        = param_width_mm,
            `length`        = param_length,
            length_mm       = param_length_mm,
            unitweight      = param_unitweight,
            unitweight_ton  = param_unitweight_ton,
            qtty            = param_qtty,
            weight          = param_weight,
            weight_ton      = param_weight_ton,
            price           = param_price,
            `value`         = param_value,
            deliverytime    = param_deliverytime,
            internal_notes  = param_internal_notes,
            created_at      = NOW(),
            created_by      = param_user_id,
            modified_at     = NOW(),
            modified_by     = param_user_id;

    END IF;
    
    
    SET var_order_qtty = IFNULL((SELECT COUNT(*) FROM steelitems WHERE order_id = param_order_id AND steelposition_id = param_position_id), 0);
    SET var_delta_qtty = param_qtty - var_order_qtty;


    IF var_prev_order_qtty != param_qtty
    THEN

        SET @ENABLE_TRIGGERS = FALSE;
        IF (SELECT `status` FROM orders WHERE id = param_order_id) = ''
        THEN
            
            UPDATE steelpositions
            SET
                tech_action         = 'toorder',
                tech_object_alias   = 'order',
                tech_object_id      = param_order_id,
                tech_data           = param_qtty
            WHERE id = param_position_id;
    
        ELSE
    
            UPDATE steelpositions
            SET
                tech_action         = IF(param_qtty > var_prev_order_qtty, 'toorder', IF(is_from_order > 0, 'removed', 'tostock')),
                tech_object_alias   = 'order',
                tech_object_id      = param_order_id,
                tech_data           = ABS(param_qtty - var_prev_order_qtty)
            WHERE id = param_position_id;
        
        END IF;        
        SET @ENABLE_TRIGGERS = TRUE;

    END IF;


    IF var_delta_qtty != 0 
    THEN
        
        
        SELECT var_delta_qtty AS delta_qtty;
    
        
        IF var_delta_qtty > 0
        THEN

            SELECT 
                id
            FROM steelitems
            WHERE steelposition_id = param_position_id
            AND is_available = 1
            ORDER BY is_virtual DESC;

        ELSE

            SELECT
                id
            FROM steelitems
            WHERE steelposition_id = param_position_id
            AND order_id = param_order_id
            ORDER BY is_virtual;

        END IF;

    END IF;

    
    UPDATE order_positions 
    SET 
        is_saved = 1 
    WHERE order_id = param_order_id 
    AND position_id = param_position_id;

END
$$

DROP PROCEDURE IF EXISTS sp_order_remove_item$$
CREATE PROCEDURE sp_order_remove_item(param_user_id INT, param_order_id INT, param_item_id INT, param_leave_history BOOLEAN)
sp:
BEGIN


    DECLARE var_position_id     INT DEFAULT 0;
    DECLARE var_parent_id       INT DEFAULT 0;
    DECLARE var_is_from_order   INT DEFAULT 0;
    DECLARE ITEM_STATUS_STOCK   TINYINT DEFAULT 3;


    IF NOT EXISTS (SELECT * FROM steelitems WHERE id = param_item_id)
    THEN
        SELECT -1 AS ErrorCode, 'sp_order_remove_item' AS ErrorAt;
        LEAVE sp;
    END IF;

    
    IF NOT EXISTS (SELECT * FROM steelitems WHERE id = param_item_id AND order_id = param_order_id)
    THEN
        SELECT -2 AS ErrorCode, 'sp_order_remove_item' AS ErrorAt;
        LEAVE sp;
    END IF;

    
    SELECT 
        steelposition_id,
        parent_id,
        is_from_order
    INTO
        var_position_id,
        var_parent_id,
        var_is_from_order
    FROM steelitems 
    WHERE id = param_item_id;


    IF var_is_from_order > 0
    THEN
        
        
        SELECT
            id AS steelitem_id,
            steelposition_id
        FROM steelitems
        WHERE id = param_item_id;


        UPDATE steelitem_properties SET modified_at = NOW(), modified_by = param_user_id WHERE item_id = param_item_id;
        DELETE FROM steelitem_properties WHERE item_id = param_item_id;
            
        UPDATE steelitems SET modified_at = NOW(), modified_by = param_user_id WHERE id = param_item_id;
        DELETE FROM steelitems WHERE id = param_item_id;

    ELSE
        
        UPDATE steelitems
        SET
            is_available    = 1,
            order_id        = 0,
            status_id       = ITEM_STATUS_STOCK,
            modified_at     = NOW(),
            modified_by     = param_user_id
        WHERE id = param_item_id;


        IF var_parent_id = 0
        THEN

            IF NOT EXISTS (SELECT * FROM steelitems WHERE parent_id = param_item_id AND order_id > 0)
            THEN
                
                UPDATE steelitems
                SET
                    is_locked       = sf_item_check_lock(id, parent_id),
                    is_conflicted   = sf_item_check_conflict(id, parent_id)
                WHERE id = param_item_id
                OR parent_id = param_item_id;


                
                SELECT
                    id AS steelitem_id,
                    steelposition_id
                FROM steelitems
                WHERE id = param_item_id
                OR parent_id = param_item_id;

            END IF;

        ELSE

            IF NOT EXISTS (SELECT * FROM steelitems WHERE (parent_id = var_parent_id OR id = var_parent_id) AND order_id > 0)
            THEN                
                
                UPDATE steelitems
                SET
                    is_locked       = sf_item_check_lock(id, parent_id),
                    is_conflicted   = sf_item_check_conflict(id, parent_id)
                WHERE parent_id = var_parent_id 
                OR id = var_parent_id;

                
                
                SELECT
                    id AS steelitem_id,
                    steelposition_id
                FROM steelitems
                WHERE parent_id = var_parent_id 
                OR id = var_parent_id;

            END IF;

        END IF;

    END IF;

    
    CALL sp_steelitem_timeline_remove(param_user_id, param_item_id, 'order', param_order_id);
    CALL sp_steelitem_update_from_timeline(param_user_id, param_item_id);


    IF param_leave_history = FALSE AND NOT EXISTS (SELECT * FROM steelitems WHERE steelposition_id = var_position_id AND order_id = param_order_id)
    THEN

        DELETE FROM order_positions 
        WHERE order_id = param_order_id 
        AND position_id = var_position_id;

    END IF;

END
$$

DELIMITER ;
