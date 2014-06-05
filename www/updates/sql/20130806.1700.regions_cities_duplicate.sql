
DROP TABLE IF EXISTS`regions_temp`;
CREATE TABLE `regions_temp` (
  `region` varchar(255) DEFAULT 'NULL',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3954 DEFAULT CHARSET=utf8;


INSERT INTO regions_temp(id, region)
SELECT
id,
title
FROM `regions`
GROUP BY title;

UPDATE `regions` SET alias = 'duplicate' WHERE id NOT IN (SELECT id FROM regions_temp);

delimiter $$

DROP PROCEDURE IF EXISTS `sp_region_duplicate_remove`$$
CREATE PROCEDURE `sp_region_duplicate_remove`()
BEGIN

    DECLARE var_duplicate_count INT DEFAULT 0;
    DECLARE var_duplicate_id INT DEFAULT 0;
    DECLARE var_duplicate_title VARCHAR(250) DEFAULT '';
    DECLARE var_region_id INT DEFAULT 0;
    
    SET var_duplicate_count = (SELECT COUNT(*) FROM regions WHERE alias = 'duplicate');
    
    WHILE var_duplicate_count > 0 DO

        SET var_duplicate_id = (SELECT id FROM regions WHERE alias = 'duplicate' LIMIT 0, 1);
        SET var_duplicate_title  = (SELECT title FROM regions WHERE id = var_duplicate_id);
        SET var_region_id = (SELECT id FROM regions_temp WHERE region = var_duplicate_title);
        
        UPDATE cities
        SET 
            region_id = var_region_id
        WHERE region_id = var_duplicate_id;

        UPDATE companies
        SET 
            region_id = var_region_id
        WHERE region_id = var_duplicate_id;

        UPDATE persons
        SET 
            region_id = var_region_id
        WHERE region_id = var_duplicate_id;

        UPDATE users
        SET 
            region_id = var_region_id
        WHERE region_id = var_duplicate_id;

        DELETE FROM regions WHERE id = var_duplicate_id;

        SET var_duplicate_count = var_duplicate_count - 1;

    END WHILE;
    

END$$

delimiter ;

call `sp_region_duplicate_remove`();


SELECT COUNT( title ) AS title_count, id
FROM  `regions` 
GROUP BY  `title` 
HAVING title_count >1;

DROP PROCEDURE IF EXISTS `sp_region_duplicate_remove`;


DROP TABLE IF EXISTS `cities_temp`;
CREATE TABLE `cities_temp` (
  `city` varchar(255) DEFAULT 'NULL',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39339 DEFAULT CHARSET=utf8;


INSERT INTO cities_temp(id, city)
SELECT
id,
title
FROM `cities`
GROUP BY title;

UPDATE `cities` SET alias = 'duplicate' WHERE id NOT IN (SELECT id FROM cities_temp);


delimiter $$

DROP PROCEDURE IF EXISTS `sp_city_duplicate_remove`$$
CREATE PROCEDURE `sp_city_duplicate_remove`()
BEGIN
    DECLARE var_duplicate_count INT DEFAULT 0;
    DECLARE var_duplicate_id INT DEFAULT 0;
    DECLARE var_duplicate_title VARCHAR(250) DEFAULT '';
    DECLARE var_city_id INT DEFAULT 0;
    
    SET var_duplicate_count = (SELECT COUNT(*) FROM cities WHERE alias = 'duplicate');
    
    WHILE var_duplicate_count > 0 DO

        SET var_duplicate_id = (SELECT id FROM cities WHERE alias = 'duplicate' LIMIT 0, 1);
        SET var_duplicate_title  = (SELECT title FROM cities WHERE id = var_duplicate_id);
        SET var_city_id = (SELECT id FROM cities_temp WHERE city = var_duplicate_title);

        UPDATE companies
        SET 
            city_id = var_city_id
        WHERE city_id = var_duplicate_id;

        UPDATE persons
        SET 
            city_id = var_city_id
        WHERE city_id = var_duplicate_id;

        DELETE FROM cities WHERE id = var_duplicate_id;

        SET var_duplicate_count = var_duplicate_count - 1;

    END WHILE;
    
END$$

delimiter ;

call `sp_city_duplicate_remove`();


SELECT COUNT( title ) AS title_count, id
FROM  `cities` 
GROUP BY  `title` 
HAVING title_count >1;

DROP PROCEDURE IF EXISTS `sp_city_duplicate_remove`;


UPDATE `countries` SET `title`='Morocco', `title2`='Morocco' WHERE `title`='Marocco';
