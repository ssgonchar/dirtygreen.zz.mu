DELIMITER $$

DROP TRIGGER IF EXISTS t_steelitem_update$$
CREATE TRIGGER `t_steelitem_update`
AFTER UPDATE ON `steelitems`
FOR EACH ROW
BEGIN

    IF  @ENABLE_TRIGGERS = TRUE AND
    (
        old.guid                       != new.guid
		OR old.alias                   != new.alias
        OR old.steelposition_id        != new.steelposition_id
        OR old.product_id              != new.product_id
        OR old.biz_id                  != new.biz_id
        OR old.stockholder_id          != new.stockholder_id
        OR old.location_id             != new.location_id
        OR old.dimension_unit          != new.dimension_unit
        OR old.weight_unit             != new.weight_unit
        OR old.price_unit              != new.price_unit
        OR old.currency                != new.currency
        OR old.parent_id               != new.parent_id
        OR old.rel                     != new.rel
        OR old.steelgrade_id           != new.steelgrade_id
        OR old.thickness               != new.thickness
        OR old.thickness_mm            != new.thickness_mm
        OR old.thickness_measured      != new.thickness_measured
        OR old.width                   != new.width
        OR old.width_mm                != new.width_mm
        OR old.width_measured          != new.width_measured
        OR old.width_max               != new.width_max
        OR old.`length`                != new.`length`
        OR old.length_mm               != new.length_mm
        OR old.length_measured         != new.length_measured
        OR old.length_max              != new.length_max
        OR old.unitweight              != new.unitweight
        OR old.unitweight_ton          != new.unitweight_ton
        OR old.price                   != new.price
        OR old.`value`                 != new.`value`
        OR old.supplier_id             != new.supplier_id
        OR old.supplier_invoice_id     != new.supplier_invoice_id
        OR old.purchase_price          != new.purchase_price
        OR old.purchase_value          != new.purchase_value
		OR old.purchase_currency       != new.purchase_currency
        OR old.in_ddt_id           	   != new.in_ddt_id
        OR old.in_ddt_number           != new.in_ddt_number
        OR old.in_ddt_date             != new.in_ddt_date
		OR old.in_ddt_company_id       != new.in_ddt_company_id
        OR old.ddt_number              != new.ddt_number
        OR old.ddt_date                != new.ddt_date
		OR old.ddt_company_id          != new.ddt_company_id
        OR old.deliverytime_id         != new.deliverytime_id
        OR old.notes                   != new.notes
        OR old.internal_notes          != new.internal_notes
        OR old.owner_id                != new.owner_id
        OR old.status_id               != new.status_id
        OR old.mill                    != new.mill
        OR old.system                  != new.system
        OR old.unitweight_measured     != new.unitweight_measured
        OR old.unitweight_weighed      != new.unitweight_weighed
        OR old.current_cost            != new.current_cost
        OR old.pl                      != new.pl
        OR old.load_ready              != new.load_ready
        OR old.order_id                != new.order_id
        OR old.invoice_id              != new.invoice_id
        OR old.is_available            != new.is_available
		OR old.is_virtual              != new.is_virtual        
        OR old.is_deleted              != new.is_deleted
		OR old.is_conflicted           != new.is_conflicted
		OR old.is_locked           	   != new.is_locked
        OR old.tech_action             != new.tech_action
        OR old.nominal_thickness_mm        != new.nominal_thickness_mm
        OR old.nominal_width_mm            != new.nominal_width_mm
        OR old.nominal_length_mm           != new.nominal_length_mm
        OR old.is_ce_mark                  != new.is_ce_mark
        OR old.is_mec_prop_not_required    != new.is_mec_prop_not_required

    )
    THEN

        INSERT INTO steelitems_history
        SET
            steelitem_id            = new.id,
            guid                    = new.guid,
			alias                   = new.alias,
            steelposition_id        = new.steelposition_id,
            product_id              = new.product_id,
            biz_id                  = new.biz_id,
            stockholder_id          = new.stockholder_id,
            location_id             = new.location_id,
            dimension_unit          = new.dimension_unit,
            weight_unit             = new.weight_unit,
            price_unit              = new.price_unit,
            currency                = new.currency,
            parent_id               = new.parent_id,
            rel                     = new.rel,
            steelgrade_id           = new.steelgrade_id,
            thickness               = new.thickness,
            thickness_mm            = new.thickness_mm,
            thickness_measured      = new.thickness_measured,
            width                   = new.width,
            width_mm                = new.width_mm,
            width_measured          = new.width_measured,
            width_max               = new.width_max,
            `length`                = new.`length`,
            length_mm               = new.length_mm,
            length_measured         = new.length_measured,
            length_max              = new.length_max,
            unitweight              = new.unitweight,
            unitweight_ton          = new.unitweight_ton,
            price                   = new.price,
            `value`                 = new.`value`,
            supplier_id             = new.supplier_id,
            supplier_invoice_id     = new.supplier_invoice_id,
            purchase_price          = new.purchase_price,
            purchase_value          = new.purchase_value,
			purchase_currency       = new.purchase_currency,
            in_ddt_id           	= new.in_ddt_id,
            in_ddt_number           = new.in_ddt_number,
            in_ddt_date             = new.in_ddt_date,
			in_ddt_company_id       = new.in_ddt_company_id,
            ddt_number              = new.ddt_number,
            ddt_date                = new.ddt_date,
			ddt_company_id       	= new.ddt_company_id,
            deliverytime_id         = new.deliverytime_id,
            notes                   = new.notes,
            internal_notes          = new.internal_notes,
            owner_id                = new.owner_id,
            status_id               = new.status_id,
            mill                    = new.mill,
            system                  = new.system,
            unitweight_measured     = new.unitweight_measured,
            unitweight_weighed      = new.unitweight_weighed,
            current_cost            = new.current_cost,
            pl                      = new.pl,
            load_ready              = new.load_ready,
            is_from_order           = new.is_from_order,
            order_id                = new.order_id,
            invoice_id              = new.invoice_id,
            is_available            = new.is_available, 
            is_virtual              = new.is_virtual,
            is_deleted              = new.is_deleted,
			is_conflicted          	= new.is_conflicted,
			is_locked          		= new.is_locked,
            nominal_thickness_mm        = new.nominal_thickness_mm,
            nominal_width_mm            = new.nominal_width_mm,
            nominal_length_mm           = new.nominal_length_mm,            
            is_ce_mark                  = new.is_ce_mark, 
            is_mec_prop_not_required    = new.is_mec_prop_not_required,
            tech_action             = CASE 
                                        WHEN old.is_deleted = 0 AND new.is_deleted = 1 THEN 'delete' 
                                        WHEN old.order_id = 0 AND new.order_id > 0 THEN 'toorder' 
                                        WHEN old.order_id > 0 AND new.order_id = 0 THEN 'fromorder' 
                                        WHEN old.steelposition_id != new.steelposition_id THEN 'move' 
                                        ELSE IF(new.tech_action = '', 'edit', new.tech_action) END,
            tech_object_alias       = new.tech_object_alias,
            tech_object_id          = new.tech_object_id,
            tech_data               = new.tech_data,
            created_at              = new.created_at,
            created_by              = new.created_by,
            modified_at             = new.modified_at,
            modified_by             = new.modified_by,
            record_at               = new.modified_at,
            record_by               = new.modified_by;

     END IF;

    IF @ENABLE_TRIGGERS = TRUE AND old.unitweight != new.unitweight
    THEN
        CALL sp_ra_items_weight_update(old.id, new.unitweight);
    END IF;

END$$

delimiter ;
