-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 5.0.97.1
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 14/08/2013 09:55:17
-- Версия сервера: 5.5.27
-- Версия клиента: 4.1

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_steelitem_move$$
CREATE PROCEDURE sp_steelitem_move(param_user_id INT, param_id INT, param_dest_stockholder_id INT, param_dest_position_id INT, param_source_position_id INT)
sp:
BEGIN

    DECLARE var_location_id INT DEFAULT 0;
    DECLARE var_biz_id INT DEFAULT 0;
    DECLARE var_dimension_unit CHAR(10) DEFAULT '';
    DECLARE var_weight_unit CHAR(10) DEFAULT '';
    DECLARE var_price_unit CHAR(10) DEFAULT '';
    DECLARE var_currency CHAR(10) DEFAULT '';
    DECLARE var_steelgrade_id INT DEFAULT 0;
    DECLARE var_thickness CHAR(10) DEFAULT '';
    DECLARE var_thickness_mm DECIMAL(10,4) DEFAULT 0;
    DECLARE var_width CHAR(10) DEFAULT '';
    DECLARE var_width_mm DECIMAL(10,4) DEFAULT 0;
    DECLARE var_length CHAR(10) DEFAULT '';
    DECLARE var_length_mm DECIMAL(10,4) DEFAULT 0;
    DECLARE var_unitweight CHAR(10) DEFAULT '';
    DECLARE var_unitweight_ton DECIMAL(10,4) DEFAULT 0;
    DECLARE var_price DECIMAL(10,4) DEFAULT 0;
    DECLARE var_value DECIMAL(10,4) DEFAULT 0;


    IF param_dest_position_id = param_source_position_id
    THEN
        SELECT -1 AS ErrorCode, 'sp_steelitem_move' AS ErrorAt;
        LEAVE sp;
    END IF;

    IF NOT EXISTS (SELECT * FROM steelitems WHERE id = param_id AND is_available = 1 AND is_deleted = 0)
    THEN
        SELECT -2 AS ErrorCode, 'sp_steelitem_move' AS ErrorAt;
        LEAVE sp;
    END IF;


    SET var_location_id = (SELECT location_id FROM companies WHERE id = param_dest_stockholder_id);

    
    
    IF param_source_position_id > 0 AND NOT EXISTS (SELECT * FROM steelitems WHERE id = param_id AND steelposition_id = param_source_position_id)
    THEN
        SELECT -1 AS ErrorCode, 'sp_steelitem_move' AS ErrorAt;
        LEAVE sp;
    END IF;
    

    
    SELECT 
        steelposition_id
    FROM steelitems 
    WHERE id = param_id
    AND (steelposition_id = param_source_position_id OR param_source_position_id = 0);


    SELECT
        biz_id,
        dimension_unit,
        weight_unit,
        price_unit,
        currency,
        steelgrade_id,
        thickness,
        thickness_mm,
        width,
        width_mm,
        `length`,
        length_mm,
        unitweight,
        unitweight_ton,
        price,
        CASE WHEN weight_unit = 'lb' AND price_unit = 'cwt'
        THEN unitweight * price / 100
        ELSE unitweight * price END
    INTO
        var_biz_id,
        var_dimension_unit,
        var_weight_unit,
        var_price_unit,
        var_currency,
        var_steelgrade_id,
        var_thickness,
        var_thickness_mm,
        var_width,
        var_width_mm,
        var_length,
        var_length_mm,
        var_unitweight,
        var_unitweight_ton,
        var_price,
        var_value
    FROM steelpositions 
    WHERE id = param_dest_position_id;

    
    UPDATE steelitems
    SET
        steelposition_id    = param_dest_position_id,
        biz_id              = var_biz_id,
        stockholder_id      = param_dest_stockholder_id,
        location_id         = var_location_id,
        dimension_unit      = var_dimension_unit,
        weight_unit         = var_weight_unit,
        price_unit          = var_price_unit,
        currency            = var_currency,
        steelgrade_id       = var_steelgrade_id,
        thickness           = var_thickness,
        thickness_mm        = var_thickness_mm,
        width               = var_width,
        width_mm            = var_width_mm,
        `length`            = var_length,
        length_mm           = var_length_mm,
        unitweight          = var_unitweight,
        unitweight_ton      = var_unitweight_ton,
        price               = var_price,
        `value`             = var_value
    WHERE id = param_id;

END
$$

DROP PROCEDURE IF EXISTS sp_steelitem_create_alias$$
CREATE PROCEDURE sp_steelitem_create_alias(
    param_user_id INT, 
    param_stock_id INT, 
    param_stockholder_id INT, 
    param_steelitem_id INT, 
    param_steelgrade_id INT, 
    param_thickness CHAR(10), 
    param_thickness_mm DECIMAL(10,4), 
    param_width CHAR(10), 
    param_width_mm DECIMAL(10,4), 
    param_length CHAR(10), 
    param_length_mm DECIMAL(10,4), 
    param_unitweight CHAR(10), 
    param_unitweight_ton DECIMAL(10,4), 
    param_price DECIMAL(10,4), 
    param_deliverytime_id INT, 
    param_notes VARCHAR(250), 
    param_internal_notes VARCHAR(250), 
    param_position_id INT)
sp:
BEGIN

    DECLARE var_dimension_unit  CHAR(5) DEFAULT '';
    DECLARE var_weight_unit     CHAR(5) DEFAULT '';
    DECLARE var_price_unit      CHAR(5) DEFAULT '';
    DECLARE var_currency        CHAR(5) DEFAULT '';
    DECLARE var_location_id     INT DEFAULT 0;
    DECLARE var_steelitem_id    INT DEFAULT 0;
    
    
    IF NOT EXISTS (SELECT * FROM stocks WHERE id = param_stock_id)
    THEN
        SELECT -1 AS ErrorCode, 'sp_steelitem_create_alias' AS ErrorAt;
        LEAVE sp;
    END IF;

    IF NOT EXISTS (SELECT * FROM steelitems WHERE id = param_steelitem_id AND is_available = 1 AND is_deleted = 0 AND parent_id = 0)
    THEN
        SELECT -2 AS ErrorCode, 'sp_steelitem_create_alias' AS ErrorAt;
        LEAVE sp;
    END IF;    



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
    FROM stocks
    WHERE id = param_stock_id;

    
    IF param_position_id = 0
    THEN

        SET param_position_id = IFNULL((
            SELECT 
                id 
            FROM steelpositions 
            WHERE steelgrade_id = param_steelgrade_id 
            AND thickness = param_thickness
            AND width = param_width
            AND `length` = param_length
            AND deliverytime_id = param_deliverytime_id
        ), 0);

        IF param_position_id = 0
        THEN

            SET param_position_id = IFNULL((
                SELECT 
                    MIN(id)
                FROM steelpositions 
                WHERE steelgrade_id = param_steelgrade_id 
                AND thickness = param_thickness
                AND width_mm BETWEEN (param_width_mm - 50) AND (param_width_mm + 50)
                AND length_mm BETWEEN (param_width_mm - 100) AND (param_width_mm + 100)
                AND deliverytime_id = param_deliverytime_id
            ), 0);
    
        END IF;

        IF param_position_id = 0
        THEN

            START TRANSACTION;
    
                INSERT INTO steelpositions
                SET
                    stock_id        = param_stock_id,
                    product_id      = 92,
                    biz_id          = 0,
                    dimension_unit  = var_dimension_unit,
                    weight_unit     = var_weight_unit,
                    price_unit      = var_price_unit,
                    currency        = var_currency,
                    steelgrade_id   = param_steelgrade_id,
                    thickness       = param_thickness,
                    thickness_mm    = param_thickness_mm,
                    width           = param_width,
                    width_mm        = param_width_mm,
                    length          = param_length,
                    length_mm       = param_length_mm,
                    unitweight      = param_unitweight,
                    unitweight_ton  = param_unitweight_ton,
                    qtty            = 1,
                    weight          = param_unitweight,
                    weight_ton      = param_unitweight_ton,
                    price           = param_price,
                    `value`         = IF(var_weight_unit = 'lb' AND var_price_unit = 'cwt', param_price * param_unitweight / 100, param_price * param_unitweight),
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
        
                SET param_position_id = (SELECT MAX(id) FROM steelpositions WHERE created_by = param_user_id);
    
            COMMIT;

        END IF;            
        
    END IF;


    SET var_location_id = (SELECT location_id FROM companies WHERE id = param_stockholder_id);

    START TRANSACTION;
    
        INSERT INTO steelitems (
            guid,
			alias,
            steelposition_id,
            product_id,
            biz_id,
            stockholder_id,
            location_id,
            dimension_unit,
            weight_unit,
            price_unit,
            currency,
            parent_id,
            rel,
            steelgrade_id,
            thickness,
            thickness_mm,
            thickness_measured,
            width,
            width_mm,
            width_measured,
            width_max,
            `length`,
            length_mm,
            length_measured,
            length_max,
            unitweight,
            unitweight_ton,
            price,
            `value`,
            supplier_id,
            supplier_invoice_id,
            purchase_price,
            purchase_value,
            ddt_number,
            ddt_date,
            deliverytime_id,
            notes,
            internal_notes,
            owner_id,
            status_id,
            is_virtual,
            is_available,
            is_deleted,
            is_conflicted,
            is_locked,
            is_from_order,
            order_id,
            created_at,
            created_by,
            modified_at,
            modified_by
        )
        SELECT
            '',
			'',
            param_position_id,
            92,
            0,
            param_stockholder_id,
            var_location_id,
            var_dimension_unit,
            var_weight_unit,
            var_price_unit,
            var_currency,
            param_steelitem_id,
            't',
            param_steelgrade_id,
            param_thickness,
            param_thickness_mm,
            0,
            param_width,
            param_width_mm,
            0,
            0,
            param_length,
            param_length_mm,
            0,
            0,
            param_unitweight,
            param_unitweight_ton,
            param_price,
            IF(var_weight_unit = 'lb' AND var_price_unit = 'cwt', param_price * param_unitweight / 100, param_price * param_unitweight),
            supplier_id,
            supplier_invoice_id,
            purchase_price,
            purchase_value,
            ddt_number,
            ddt_date,
            deliverytime_id,
            notes,
            internal_notes,
            owner_id,
            0,
            1,
            1,
            0,
            0,
            0,
            0,
            0,
            NOW(),
            param_user_id,
            NOW(),
            param_user_id
        FROM steelitems
        WHERE id = param_steelitem_id;

        SET var_steelitem_id = (SELECT MAX(id) FROM steelitems WHERE created_by = param_user_id);

        INSERT INTO steelitem_properties(
            item_id,
            heat_lot,
            c,
            si,
            mn,
            p,
            s,
            cr,
            ni,
            cu,
            al,
            mo,
            nb,
            v,
            n,
            ti,
            sn,
            b,
            ceq,
            tensile_sample_direction,
            tensile_strength,
            yeild_point,
            elongation,
            reduction_of_area,
            test_temp,
            impact_strength,
            hardness,
            ust,
            sample_direction,
            stress_relieving_temp,
            heating_rate_per_hour,
            holding_time,
            cooling_down_rate,
            `condition`,
            normalizing_temp,
            created_at,
            created_by,
            modified_at,
            modified_by
        )
        SELECT
            var_steelitem_id,
            heat_lot,
            c,
            si,
            mn,
            p,
            s,
            cr,
            ni,
            cu,
            al,
            mo,
            nb,
            v,
            n,
            ti,
            sn,
            b,
            ceq,
            tensile_sample_direction,
            tensile_strength,
            yeild_point,
            elongation,
            reduction_of_area,
            test_temp,
            impact_strength,
            hardness,
            ust,
            sample_direction,
            stress_relieving_temp,
            heating_rate_per_hour,
            holding_time,
            cooling_down_rate,
            `condition`,
            normalizing_temp,
            NOW(),
            param_user_id,
            NOW(),
            param_user_id
        FROM steelitem_properties
        WHERE item_id = param_steelitem_id;
    
    COMMIT;        


    SELECT param_position_id AS position_id;

END
$$

DROP PROCEDURE IF EXISTS sp_steelitem_cut$$
CREATE PROCEDURE sp_steelitem_cut(
    param_user_id INT, 
    param_id INT, 
    param_item_id INT, 
    param_stockholder_id INT, 
    param_position_id INT,
    param_width CHAR(10), 
    param_width_mm DECIMAL(10,4), 
    param_length CHAR(10), 
    param_length_mm DECIMAL(10, 4), 
    param_unitweight CHAR(10), 
    param_unitweight_ton DECIMAL(10, 4), 
    param_guid VARCHAR(32),
    param_alias VARCHAR(32),
    param_notes TEXT
)
sp:
BEGIN
    
    DECLARE var_item_id             INT DEFAULT 0;
    DECLARE var_stock_id            INT DEFAULT 0;
    DECLARE var_biz_id              INT DEFAULT 0;
    DECLARE var_location_id         INT DEFAULT 0;
    DECLARE var_dimension_unit      CHAR(10) DEFAULT '';
    DECLARE var_weight_unit         CHAR(10) DEFAULT '';
    DECLARE var_price_unit          CHAR(10) DEFAULT '';
    DECLARE var_currency            CHAR(3) DEFAULT '';
    DECLARE var_steelgrade_id       INT DEFAULT 0;
    DECLARE var_thickness           CHAR(10) DEFAULT '';
    DECLARE var_thickness_mm        DECIMAL (10, 4) DEFAULT 0;

    DECLARE var_in_ddt_number       VARCHAR(50) DEFAULT '';
    DECLARE var_in_ddt_date         TIMESTAMP DEFAULT NULL;
    DECLARE var_in_ddt_company_id   INT DEFAULT 0;
    DECLARE var_ddt_number          VARCHAR(50) DEFAULT '';
    DECLARE var_ddt_date            TIMESTAMP DEFAULT NULL;
    DECLARE var_ddt_company_id      INT DEFAULT 0;
    DECLARE var_stockholder_id      INT DEFAULT 0;
    DECLARE var_status_id           INT DEFAULT 0;
    DECLARE var_owner_id            INT DEFAULT 0;



    CREATE TEMPORARY TABLE IF NOT EXISTS tmp_items LIKE steelitems;


    IF NOT EXISTS (SELECT * FROM steelitems WHERE id = param_item_id AND is_available = 1 AND is_deleted = 0 AND parent_id = 0)
    THEN
        SELECT -1 AS ErrorCode, 'sp_steelitem_cut' AS ErrorAt;
        LEAVE sp;
    END IF;

    
    SELECT
        biz_id,
        dimension_unit,
        weight_unit,
        price_unit,
        currency,
        steelgrade_id,
        thickness,
        thickness_mm
    INTO
        var_biz_id,
        var_dimension_unit,
        var_weight_unit,
        var_price_unit,
        var_currency,
        var_steelgrade_id,
        var_thickness,
        var_thickness_mm
    FROM steelitems
    WHERE id = param_item_id;
    
    SET var_location_id = (SELECT location_id FROM companies WHERE id = param_stockholder_id);
    SET var_stock_id    = (SELECT stock_id FROM steelpositions WHERE id = (SELECT steelposition_id FROM steelitems WHERE id = param_item_id));

    
    START TRANSACTION;

        IF param_position_id = 0
        THEN
    
            INSERT INTO steelpositions 
            SET
                stock_id            = var_stock_id,
                product_id          = 92,
                biz_id              = var_biz_id,
                dimension_unit      = var_dimension_unit,
                weight_unit         = var_weight_unit,
                price_unit          = var_price_unit,
                currency            = var_currency,
                steelgrade_id       = var_steelgrade_id,
                thickness           = var_thickness,
                thickness_mm        = var_thickness_mm,
                width               = param_width,
                width_mm            = param_width_mm,
                length              = param_length,
                length_mm           = param_length_mm,
                unitweight          = param_unitweight,
                unitweight_ton      = param_unitweight_ton,
                qtty                = 1,
                weight              = param_unitweight,
                weight_ton          = param_unitweight_ton,
                price               = 0,
                `value`             = 0,
                deliverytime_id     = 0,
                notes               = '',
                internal_notes      = param_notes,
                is_from_order       = 0,
                is_deleted          = 0,
                is_reserved         = 0,
                is_locked           = 0,
                tech_action         = '',
                tech_object_alias   = '',
                tech_object_id      = 0,
                tech_data           = '',
                created_at          = NOW(),
                created_by          = param_user_id,
                modified_at         = NOW(),
                modified_by         = param_user_id,
                mam_deliverytime    = '';

            SET param_position_id = (SELECT MAX(id) FROM steelpositions WHERE created_by = param_user_id);
    
        END IF;

    
        IF param_id > 0
        THEN
            
            INSERT INTO tmp_items 
            SELECT * FROM steelitems WHERE id = param_item_id;
            
            UPDATE tmp_items SET id = param_id;

            UPDATE steelitems AS s JOIN tmp_items AS t ON s.id = t.id 
            SET 
                s.guid                      = param_guid,
    			s.alias                     = param_alias,
                s.steelposition_id          = param_position_id,
                s.biz_id                    = t.biz_id,
                s.stockholder_id            = param_stockholder_id,
                s.location_id               = t.location_id,
                s.dimension_unit            = t.dimension_unit,
                s.weight_unit               = t.weight_unit,        
                s.price_unit                = t.price_unit,        
                s.currency                  = t.currency,
                s.parent_id                 = 0,
                s.rel                       = '',
                s.steelgrade_id             = t.steelgrade_id,
                s.thickness                 = t.thickness,
                s.thickness_mm              = t.thickness_mm,
                s.thickness_measured        = t.thickness_measured,
                s.width                     = param_width,
                s.width_mm                  = param_width_mm,
                s.width_measured            = 0,
                s.width_max                 = 0,
                s.`length`                  = param_length,
                s.length_mm                 = param_length_mm,
                s.length_measured           = 0,
                s.length_max                = 0,
                s.unitweight                = param_unitweight,
                s.unitweight_ton            = param_unitweight_ton,
                s.price                     = t.price,
                s.`value`                   = IF(var_weight_unit = 'lb' AND var_price_unit = 'cwt', param_unitweight * t.price / 100, param_unitweight * t.price),
                s.supplier_id               = t.supplier_id,
                s.supplier_invoice_id       = t.supplier_invoice_id,
                s.purchase_price            = t.purchase_price,
                s.purchase_value            = param_unitweight * t.purchase_price,
                s.purchase_currency         = t.purchase_currency,
                
                s.in_ddt_id                 = t.in_ddt_id,
                s.in_ddt_number             = t.in_ddt_number,
                s.in_ddt_date               = t.in_ddt_date,
                s.in_ddt_company_id         = t.in_ddt_company_id,
                
                s.ddt_number                = t.ddt_number,
                s.ddt_date                  = t.ddt_date,
                s.ddt_company_id            = t.ddt_company_id,
                
                s.deliverytime_id           = t.deliverytime_id,
                s.notes                     = param_notes,
                s.internal_notes            = param_notes,
                s.owner_id                  = t.owner_id,
                s.mill                      = t.mill,
                s.system                    = t.system,
                s.current_cost              = t.current_cost,
                s.pl                        = t.pl,
                s.load_ready                = t.load_ready,

                s.is_virtual                = 0,
                
                s.modified_at               = NOW(),
                s.modified_by               = param_user_id
            WHERE s.id = param_id;

            DELETE FROM steelitem_properties WHERE item_id = param_id;

            SET var_item_id = param_id;
            
        ELSE

            INSERT INTO steelitems(
                guid,
                alias,
                steelposition_id,
                product_id,
                biz_id,
                stockholder_id,
                location_id,
                dimension_unit,
                weight_unit,
                price_unit,
                currency,
                parent_id,
                rel,
                steelgrade_id,
                thickness,
                thickness_mm,
                thickness_measured,
                width,
                width_mm,
                width_measured,
                width_max,
                `length`,
                length_mm,
                length_measured,
                length_max,
                unitweight,
                unitweight_ton,
                price,
                `value`,
                supplier_id,
                supplier_invoice_id,
                purchase_price,
                purchase_value,
                purchase_currency,
                in_ddt_id,
                in_ddt_number,
                in_ddt_date,
                in_ddt_company_id,
                ddt_number,
                ddt_date,
                ddt_company_id,
                deliverytime_id,
                notes,
                internal_notes,
                owner_id,
                status_id,
                mill,
                system,
                unitweight_measured,
                unitweight_weighed,
                current_cost,
                pl,
                load_ready,
                is_from_order,
                order_id,
                invoice_id,
                is_available,
                is_virtual,
                is_deleted,
                is_conflicted,
                is_locked,
                tech_action,
                tech_object_alias,
                tech_object_id,
                tech_data,
                created_at,
                created_by,
                modified_at,
                modified_by            
            )
            SELECT
                param_guid,
                param_alias,
                param_position_id,
                product_id,
                biz_id,
                param_stockholder_id,
                location_id,
                dimension_unit,
                weight_unit,
                price_unit,
                currency,
                0,
                '',
                steelgrade_id,
                thickness,
                thickness_mm,
                0,
                param_width,
                param_width_mm,
                0,
                0,
                param_length,
                param_length_mm,
                0,
                0,
                param_unitweight,
                param_unitweight_ton,
                price,
                IF(var_weight_unit = 'lb' AND var_price_unit = 'cwt', price * param_unitweight / 100, price * param_unitweight),
                supplier_id,
                supplier_invoice_id,
                purchase_price,
                purchase_price * param_unitweight,
                purchase_currency,
                in_ddt_id,
                in_ddt_number,
                in_ddt_date,
                in_ddt_company_id,
                ddt_number,
                ddt_date,
                ddt_company_id,
                deliverytime_id,
                param_notes,
                param_notes,
                owner_id,
                status_id,
                mill,
                system,
                param_unitweight,
                param_unitweight,
                current_cost,
                pl,
                load_ready,
                is_from_order,
                0,
                invoice_id,
                1,
                0,
                0,
                0,
                0,
                '',
                '',
                0,
                '',
                NOW(),
                param_user_id,
                NOW(),
                param_user_id
            FROM steelitems
            WHERE id = param_item_id;

            SET var_item_id = (SELECT MAX(id) FROM steelitems WHERE created_by = param_user_id);
    
        END IF;


        INSERT INTO steelitem_properties(
            item_id,
            heat_lot,
            c,
            si,
            mn,
            p,
            s,
            cr,
            ni,
            cu,
            al,
            mo,
            nb,
            v,
            n,
            ti,
            sn,
            b,
            ceq,
            tensile_sample_direction,
            tensile_strength,
            yeild_point,
            elongation,
            reduction_of_area,
            test_temp,
            impact_strength,
            hardness,
            ust,
            sample_direction,
            stress_relieving_temp,
            heating_rate_per_hour,
            holding_time,
            cooling_down_rate,
            `condition`,
            normalizing_temp,
            created_at,
            created_by,
            modified_at,
            modified_by
        )
        SELECT
            var_item_id,
            heat_lot,
            c,
            si,
            mn,
            p,
            s,
            cr,
            ni,
            cu,
            al,
            mo,
            nb,
            v,
            n,
            ti,
            sn,
            b,
            ceq,
            tensile_sample_direction,
            tensile_strength,
            yeild_point,
            elongation,
            reduction_of_area,
            test_temp,
            impact_strength,
            hardness,
            ust,
            sample_direction,
            stress_relieving_temp,
            heating_rate_per_hour,
            holding_time,
            cooling_down_rate,
            `condition`,
            normalizing_temp,
            NOW(),
            param_user_id,
            NOW(),
            param_user_id
        FROM steelitem_properties
        WHERE item_id = param_item_id;


        SELECT 
            in_ddt_number,
            in_ddt_date,
            in_ddt_company_id,
            ddt_number,
            ddt_date,
            ddt_company_id,
            stockholder_id,
            status_id,
            owner_id
        INTO
            var_in_ddt_number,
            var_in_ddt_date,
            var_in_ddt_company_id,
            var_ddt_number,
            var_ddt_date,
            var_ddt_company_id,
            var_stockholder_id,
            var_status_id,
            var_owner_id            
        FROM steelitems
        WHERE id = var_item_id;


        CALL sp_steelitem_timeline_save(param_user_id, var_item_id, '', 0, var_in_ddt_number, var_in_ddt_date, var_in_ddt_company_id,
	                                    var_ddt_number, var_ddt_date, var_ddt_company_id, var_stockholder_id, var_status_id, var_owner_id);

    COMMIT;


    SELECT 
        var_item_id         AS item_id,
        param_position_id   AS position_id;

    
    DROP TEMPORARY TABLE IF EXISTS tmp_items;   

END
$$

DROP PROCEDURE IF EXISTS sp_steelitem_twin$$
CREATE PROCEDURE sp_steelitem_twin(param_user_id INT, param_item_id INT, param_stockholder_id INT, param_position_id INT)
sp:
BEGIN

    DECLARE var_new_item_id INT DEFAULT 0;
    DECLARE var_location_id INT DEFAULT 0;

    DECLARE var_biz_id          INT DEFAULT 0;
    DECLARE var_dimension_unit  CHAR(10) DEFAULT '';
    DECLARE var_weight_unit     CHAR(10) DEFAULT '';
    DECLARE var_price_unit      CHAR(10) DEFAULT '';
    DECLARE var_currency        CHAR(10) DEFAULT '';
    DECLARE var_steelgrade_id   INT DEFAULT 0;
    DECLARE var_thickness       CHAR(10) DEFAULT '';
    DECLARE var_thickness_mm    DECIMAL(10,4) DEFAULT 0;
    DECLARE var_width           CHAR(10) DEFAULT '';
    DECLARE var_width_mm        DECIMAL(10,4) DEFAULT 0;
    DECLARE var_length          CHAR(10) DEFAULT '';
    DECLARE var_length_mm       DECIMAL(10,4) DEFAULT 0;
    DECLARE var_unitweight      CHAR(10) DEFAULT '';
    DECLARE var_unitweight_ton  DECIMAL(10,4) DEFAULT 0;
    DECLARE var_price           DECIMAL(10,4) DEFAULT 0;


    IF NOT EXISTS (SELECT * FROM steelitems WHERE id = param_item_id AND is_available = 1 AND is_deleted = 0 AND parent_id = 0)
    THEN
        SELECT -1 AS ErrorCode, 'sp_steelitem_twin' AS ErrorAt;
        LEAVE sp;
    END IF;    
    
    IF EXISTS (SELECT * FROM steelitems WHERE steelposition_id = param_position_id AND id IN (SELECT id FROM steelitems WHERE parent_id = param_item_id))
    THEN
        SELECT -1 AS ErrorCode, 'sp_steelitem_twin' AS ErrorAt;
        LEAVE sp;
    END IF;


    SET var_location_id = (SELECT location_id FROM companies WHERE id = param_stockholder_id);


    START TRANSACTION;

        SELECT
            biz_id,
            dimension_unit,
            weight_unit,
            price_unit,
            currency,
            steelgrade_id,
            thickness,
            thickness_mm,
            width,
            width_mm,
            `length`,
            length_mm,
            unitweight,
            unitweight_ton,
            price
        INTO
            var_biz_id,
            var_dimension_unit,
            var_weight_unit,
            var_price_unit,
            var_currency,
            var_steelgrade_id,
            var_thickness,
            var_thickness_mm,
            var_width,
            var_width_mm,
            var_length,
            var_length_mm,
            var_unitweight,
            var_unitweight_ton,
            var_price
        FROM steelpositions
        WHERE id = param_position_id;

    
        INSERT INTO steelitems (
            guid,
			alias,
            steelposition_id,
            product_id,
            biz_id,
            stockholder_id,
            location_id,
            dimension_unit,
            weight_unit,
            price_unit,
            currency,
            parent_id,
            rel,
            steelgrade_id,
            thickness,
            thickness_mm,
            thickness_measured,
            width,
            width_mm,
            width_measured,
            width_max,
            `length`,
            length_mm,
            length_measured,
            length_max,
            unitweight,
            unitweight_ton,
            price,
            `value`,
            supplier_id,
            supplier_invoice_id,
            purchase_price,
            purchase_value,
            ddt_number,
            ddt_date,
            deliverytime_id,
            notes,
            internal_notes,
            owner_id,
            status_id,
            is_virtual,
            is_available,
            is_deleted,
            is_conflicted,
            is_locked,
            is_from_order,
            order_id,
            created_at,
            created_by,
            modified_at,
            modified_by
        )
        SELECT
            '',
			'',
            param_position_id,
            product_id,
            var_biz_id,
            param_stockholder_id,
            var_location_id,
            var_dimension_unit,
            var_weight_unit,
            var_price_unit,
            var_currency,
            param_item_id,
            't',
            var_steelgrade_id,
            var_thickness,
            var_thickness_mm,
            0,
            var_width,
            var_width_mm,
            0,
            0,
            var_length,
            var_length_mm,
            0,
            0,
            var_unitweight,
            var_unitweight_ton,
            var_price,
            IF (var_weight_unit = 'lb' AND var_price_unit = 'cwt', var_unitweight * var_price / 100, var_unitweight * var_price),
            supplier_id,
            supplier_invoice_id,
            purchase_price,
            purchase_value,
            ddt_number,
            ddt_date,
            deliverytime_id,
            notes,
            internal_notes,
            owner_id,
            0,
            1,
            1,
            0,
            0,
            0,
            0,
            0,
            NOW(),
            param_user_id,
            NOW(),
            param_user_id
        FROM steelitems
        WHERE id = param_item_id;

        SET var_new_item_id = (SELECT MAX(id) FROM steelitems WHERE created_by = param_user_id);

        INSERT INTO steelitem_properties(
            item_id,
            heat_lot,
            c,
            si,
            mn,
            p,
            s,
            cr,
            ni,
            cu,
            al,
            mo,
            nb,
            v,
            n,
            ti,
            sn,
            b,
            ceq,
            tensile_sample_direction,
            tensile_strength,
            yeild_point,
            elongation,
            reduction_of_area,
            test_temp,
            impact_strength,
            hardness,
            ust,
            sample_direction,
            stress_relieving_temp,
            heating_rate_per_hour,
            holding_time,
            cooling_down_rate,
            `condition`,
            normalizing_temp,
            created_at,
            created_by,
            modified_at,
            modified_by
        )
        SELECT
            var_new_item_id,
            heat_lot,
            c,
            si,
            mn,
            p,
            s,
            cr,
            ni,
            cu,
            al,
            mo,
            nb,
            v,
            n,
            ti,
            sn,
            b,
            ceq,
            tensile_sample_direction,
            tensile_strength,
            yeild_point,
            elongation,
            reduction_of_area,
            test_temp,
            impact_strength,
            hardness,
            ust,
            sample_direction,
            stress_relieving_temp,
            heating_rate_per_hour,
            holding_time,
            cooling_down_rate,
            `condition`,
            normalizing_temp,
            NOW(),
            param_user_id,
            NOW(),
            param_user_id
        FROM steelitem_properties
        WHERE item_id = param_item_id;

    COMMIT;
	

    SELECT param_item_id AS item_id;

END
$$

DROP PROCEDURE IF EXISTS sp_order_update_position_qtty$$
CREATE PROCEDURE sp_order_update_position_qtty(param_user_id INT, param_order_id INT, param_position_id INT)
BEGIN

    DECLARE var_qtty        INT DEFAULT 0;
    DECLARE var_prev_qtty   INT DEFAULT 0;
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

    
    SET var_prev_qtty   = IFNULL((
        SELECT 
            qtty 
        FROM order_positions 
        WHERE order_id = param_order_id
        AND position_id = param_position_id
    ), 0);


    SET var_qtty = IFNULL((
        SELECT
            COUNT(*)
        FROM steelitems
        WHERE steelposition_id = param_position_id
        AND order_id = param_order_id
    ), 0);

    
    IF var_qtty = 0
    THEN
        
        DELETE FROM order_positions
        WHERE order_id = param_order_id
        AND position_id = param_position_id;

    ELSE

        UPDATE order_positions
        SET
            qtty        = var_qtty,
            weight      = unitweight * qtty,
            weight_ton  = unitweight_ton * qtty,
            `value`     = IF(var_weight_unit = 'lb' AND var_price_unit = 'cwt', price * unitweight * qtty / 100, price * unitweight * qtty),
            modified_at = NOW(),
            modified_by = param_user_id
        WHERE order_id = param_order_id
        AND position_id = param_position_id;

    END IF;


    
    SET @ENABLE_TRIGGERS = FALSE;
    UPDATE steelpositions
    SET
        tech_action         = IF(var_qtty > var_prev_qtty, 'toorder', 'tostock'),
        tech_object_alias   = 'order',
        tech_object_id      = param_order_id,
        tech_data           = ABS(var_qtty - var_prev_qtty)
    WHERE id = param_position_id;
    SET @ENABLE_TRIGGERS = TRUE;

END
$$

DROP PROCEDURE IF EXISTS sp_basket_proceed_order_position$$
CREATE PROCEDURE sp_basket_proceed_order_position(param_user_id INT, param_basket_id INT, param_order_id INT, param_position_id INT)
BEGIN

    DECLARE var_qtty INT DEFAULT 0;
    SET var_qtty = (SELECT qtty FROM basket_positions WHERE basket_id = param_basket_id AND position_id = param_position_id);


    INSERT order_positions(
        order_id,
        position_id,
        steelgrade_id,
        thickness,
        thickness_mm,
        width,
        width_mm,
        `length`,
        length_mm,
        unitweight,
        unitweight_ton,
        qtty,
        weight,
        weight_ton,
        price,
        `value`,
        internal_notes,
        created_at,
        created_by,
        modified_at,
        modified_by
    )
    SELECT
        param_order_id,
        param_position_id,
        steelgrade_id,
        thickness,
        thickness_mm,
        width,
        width_mm,
        `length`,
        length_mm,
        unitweight,
        unitweight_ton,
        var_qtty,
        unitweight * var_qtty,
        unitweight_ton * var_qtty,
        price,
        IF(weight_unit = 'lb' AND price_unit = 'cwt', unitweight * var_qtty * price / 100, unitweight * var_qtty * price),
        internal_notes,
        NOW(),
        param_user_id,
        NOW(),
        param_user_id
    FROM steelpositions 
    WHERE id = param_position_id;

END
$$

DELIMITER ;
