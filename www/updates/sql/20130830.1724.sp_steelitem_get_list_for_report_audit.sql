-- Скрипт сгенерирован Devart dbForge Studio for MySQL, Версия 5.0.97.1
-- Домашняя страница продукта: http://www.devart.com/ru/dbforge/mysql/studio
-- Дата скрипта: 30/08/2013 15:23:49
-- Версия сервера: 5.5.27
-- Версия клиента: 4.1

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_steelitem_get_list_for_report_audit$$
CREATE PROCEDURE sp_steelitem_get_list_for_report_audit(param_stockholder_id INT, param_owner_id INT, param_stock_date TIMESTAMP)
sp:
BEGIN

    DECLARE ITEM_STATUS_UNDEFINED   TINYINT DEFAULT 0;
    DECLARE ITEM_STATUS_PRODUCTION  TINYINT DEFAULT 1;
    DECLARE ITEM_STATUS_TRANSFER    TINYINT DEFAULT 2;
    DECLARE ITEM_STATUS_STOCK       TINYINT DEFAULT 3;
    DECLARE ITEM_STATUS_ORDERED     TINYINT DEFAULT 4;
    DECLARE ITEM_STATUS_RELEASED    TINYINT DEFAULT 5;
    DECLARE ITEM_STATUS_DELIVERED   TINYINT DEFAULT 6;
    DECLARE ITEM_STATUS_INVOICED    TINYINT DEFAULT 7;


    DROP TEMPORARY TABLE IF EXISTS history_ids;
    CREATE TEMPORARY TABLE history_ids(id INT);

    INSERT INTO history_ids(id) 
    SELECT 
        MAX(id) 
    FROM steelitems_history 
    WHERE record_at < param_stock_date
    GROUP BY steelitem_id;


    DROP TEMPORARY TABLE IF EXISTS history_items;
    CREATE TEMPORARY TABLE history_items(id INT, guid VARCHAR(32), stockholder_id INT, owner_id INT, steelgrade_id INT, thickness CHAR(10), thickness_mm DECIMAL(10,4),
                                        width CHAR(10), width_mm DECIMAL(10,4), `length` CHAR(10), length_mm DECIMAL(10,4), unitweight CHAR(10),
                                        unitweight_ton DECIMAL(10,4), price DECIMAL(10,4), weight_unit CHAR(10), currency CHAR(10), status_id TINYINT,
                                        in_ddt_id INT, in_ddt_date TIMESTAMP, in_ddt_number VARCHAR(50), in_ddt_company_id INT, 
                                        supplier_id INT, supplier_invoice_id INT, purchase_price DECIMAL(10,4), purchase_value DECIMAL(10,4), 
                                        purchase_currency CHAR(10), internal_notes TEXT, is_virtual TINYINT, is_deleted TINYINT, invoice_id INT,
                                        price_unit CHAR(10), dimension_unit CHAR(10));

    INSERT INTO history_items(id, guid, stockholder_id, owner_id, steelgrade_id, thickness, thickness_mm, width, width_mm, 
                                `length`, length_mm, unitweight, unitweight_ton, price, weight_unit, currency, 
                                status_id, in_ddt_id, in_ddt_date, in_ddt_number, in_ddt_company_id, 
                                        supplier_id, supplier_invoice_id, purchase_price, purchase_value, purchase_currency,    
                                        internal_notes, is_virtual, is_deleted, invoice_id, price_unit, dimension_unit)
    SELECT 
        steelitem_id,
        guid,
        stockholder_id, 
        owner_id, 
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
        weight_unit, 
        currency, 
        status_id, 
        
        in_ddt_id,
        in_ddt_date, 
        in_ddt_number, 
        in_ddt_company_id, 

        supplier_id, 
        supplier_invoice_id, 

        purchase_price, 
        purchase_value, 
        purchase_currency,    
        internal_notes, 
        is_virtual, 
        is_deleted,
        invoice_id,
        price_unit,
        dimension_unit
    FROM history_ids
    JOIN steelitems_history USING (id);


    SET @var_stmt = CONCAT("
        SELECT
            
            s.id AS steelitem_id,
            sh.guid,
    
            sh.stockholder_id,
            IF(sh.owner_id > 0, sh.owner_id, s.owner_id) AS owner_id,
    
            sh.steelgrade_id,
            sh.thickness,
            sh.width,
            sh.`length`,
            sh.unitweight,
            sh.unitweight_ton,
            
            sh.dimension_unit,
            sh.weight_unit,
            
            (SELECT price FROM steelpositions WHERE id = s.steelposition_id) AS price,
            (SELECT price_unit FROM steelpositions WHERE id = s.steelposition_id) AS price_unit,
            #(SELECT weight_unit FROM steelpositions WHERE id = s.steelposition_id) AS weight_unit,
            (SELECT currency FROM steelpositions WHERE id = s.steelposition_id) AS currency,
            
            IFNULL((SELECT price FROM order_positions WHERE order_id = s.order_id AND position_id = s.steelposition_id), 0) AS order_price,
            IFNULL((SELECT price_unit FROM orders WHERE id = s.order_id), '') AS order_price_unit,
            IFNULL((SELECT weight_unit FROM orders WHERE id = s.order_id), '') AS order_weight_unit,
            IFNULL((SELECT currency FROM orders WHERE id = s.order_id), '') AS order_currency,
            
            CASE WHEN s.status_id IN (", ITEM_STATUS_ORDERED, ",", ITEM_STATUS_RELEASED, ",", ITEM_STATUS_DELIVERED, ",", ITEM_STATUS_INVOICED, ")
            THEN IFNULL((SELECT price FROM order_positions WHERE order_id = s.order_id AND position_id = s.steelposition_id), sh.price)
            ELSE sh.price
            END AS real_price,

            CASE WHEN s.status_id IN (", ITEM_STATUS_ORDERED, ",", ITEM_STATUS_RELEASED, ",", ITEM_STATUS_DELIVERED, ",", ITEM_STATUS_INVOICED, ")
            THEN IFNULL((SELECT price_unit FROM orders WHERE id = s.order_id), sh.price_unit)
            ELSE sh.price_unit
            END AS real_price_unit,

            DATEDIFF(NOW(), sh.in_ddt_date) AS days_on_stock,
            YEAR(NOW()) - YEAR(sh.in_ddt_date) AS years_on_stock,
            s.status_id,
    
            sh.in_ddt_id,
            sh.in_ddt_company_id,
            sh.in_ddt_number,
            sh.in_ddt_date,
    
            sh.supplier_id,
            sh.supplier_invoice_id,
            (SELECT number FROM supplier_invoices WHERE id = sh.supplier_invoice_id) AS supplier_invoice_number,
            (SELECT date FROM supplier_invoices WHERE id = sh.supplier_invoice_id) AS supplier_invoice_date,

            sh.purchase_price,
            sh.purchase_value,
            sh.purchase_currency,
    
            sh.invoice_id AS invoice_id,
            s.internal_notes
    
        FROM history_items AS sh
        JOIN steelitems AS s USING(id)
        WHERE sh.is_virtual = 0 
        AND sh.is_deleted = 0
        AND YEAR(sh.in_ddt_date) not in (0)
        AND sh.in_ddt_date < '", param_stock_date, "' 
        AND sh.status_id IN (", ITEM_STATUS_UNDEFINED, ",", ITEM_STATUS_PRODUCTION, ",", ITEM_STATUS_TRANSFER, ",", ITEM_STATUS_STOCK, ",", ITEM_STATUS_ORDERED, ",", ITEM_STATUS_RELEASED, ") ", 
        IF(param_stockholder_id > 0, CONCAT(" AND sh.stockholder_id = ", param_stockholder_id), ""), 
        IF(param_owner_id > 0, CONCAT(" AND sh.owner_id = ", param_owner_id), ""),         
        " ORDER BY sh.owner_id, sh.thickness_mm, sh.width_mm, sh.length_mm",
    ";");
    
    PREPARE stmt FROM @var_stmt;
    EXECUTE stmt;

    
    DROP TEMPORARY TABLE IF EXISTS history_ids;
    DROP TEMPORARY TABLE IF EXISTS history_items;

END
$$

DELIMITER ;
