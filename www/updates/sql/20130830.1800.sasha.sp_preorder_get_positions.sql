delimiter $$

DROP PROCEDURE IF EXISTS `sp_preorder_get_positions`$$
CREATE PROCEDURE `sp_preorder_get_positions`(param_guid VARCHAR(32))
BEGIN

    SELECT
        p.*
    FROM preorder_positions p
    JOIN steelpositions s ON p.position_id = s.id
    JOIN steelgrades sg ON p.steelgrade_id = sg.id
    WHERE p.guid = param_guid
    ORDER BY 
        sg.alias,
        s.thickness_mm,
        s.width_mm,
        s.length_mm;

END$$

delimiter ;

