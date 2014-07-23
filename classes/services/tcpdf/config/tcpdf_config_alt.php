<?php

/**
 * Installation path (/var/www/tcpdf/).
 * By default it is automatically calculated but you can also set it as a fixed string to improve performances.
 */
define ('K_PATH_MAIN', APP_PATH . 'classes/services/tcpdf/');

/**
 * URL path to tcpdf installation folder (http://localhost/tcpdf/).
 * By default it is automatically calculated but you can also set it as a fixed string to improve performances..
 */
define ('K_PATH_URL', APP_HOST . 'classes/services/tcpdf/');

/**
 * path for PDF fonts
 * use K_PATH_MAIN.'fonts/old/' for old non-UTF8 fonts
 */
define ('K_PATH_FONTS', K_PATH_MAIN. 'fonts/');

/**
 * cache directory for temporary files (full path)
 */
define ('K_PATH_CACHE', APP_TEMP . 'cache/');

/**
 * cache directory for temporary files (url path)
 */
define ('K_PATH_URL_CACHE', APP_TEMP . 'cache/');

/**
 *images directory
 */
define ('K_PATH_IMAGES', APP_PATH . 'img/');

/**
 * blank image
 */
define ('K_BLANK_IMAGE', K_PATH_IMAGES . '_blank.png');

/**
 * page format
 */
define ('PDF_PAGE_FORMAT', 'A4');

/**
 * page orientation (P=portrait, L=landscape)
 */
define ('PDF_PAGE_ORIENTATION', 'P');

/**
 * document creator
 */
define ('PDF_CREATOR', 'TCPDF');

/**
 * document author
 */
define ('PDF_AUTHOR', 'TCPDF');

/**
 * header title
 */
define ('PDF_HEADER_TITLE', 'TCPDF Example');

/**
 * header description string
 */
define ('PDF_HEADER_STRING', "by Nicola Asuni - Tecnick.com\nwww.tcpdf.org");

/**
 * image logo
 */
define ('PDF_HEADER_LOGO', 'tcpdf_logo.jpg');

/**
 * header logo image width [mm]
 */
define ('PDF_HEADER_LOGO_WIDTH', 30);

/**
 *  document unit of measure [pt=point, mm=millimeter, cm=centimeter, in=inch]
 */
define ('PDF_UNIT', 'mm');

/**
 * header margin
 */
define ('PDF_MARGIN_HEADER', 20);

/**
 * footer margin
 */
define ('PDF_MARGIN_FOOTER', 20);

/**
 * top margin
 */
define ('PDF_MARGIN_TOP', 90);

/**
 * bottom margin
 */
define ('PDF_MARGIN_BOTTOM', 20);

/**
 * left margin
 */
define ('PDF_MARGIN_LEFT', 60);

/**
 * right margin
 */
define ('PDF_MARGIN_RIGHT', 30);

/**
 * default main font name
 */
define ('PDF_FONT_NAME_MAIN', 'helvetica');

/**
 * default main font size
 */
define ('PDF_FONT_SIZE_MAIN', 12);

/**
 * default data font name
 */
define ('PDF_FONT_NAME_DATA', 'helvetica');

/**
 * default data font size
 */
define ('PDF_FONT_SIZE_DATA', 8);

/**
 * default monospaced font name
 */
define ('PDF_FONT_MONOSPACED', 'courier');

/**
 * ratio used to adjust the conversion of pixels to user units
 */
define ('PDF_IMAGE_SCALE_RATIO', 1); //1.25

/**
 * magnification factor for titles
 */
define('HEAD_MAGNIFICATION', 1.1);  //1.1

/**
 * height of cell repect font height
 */
define('K_CELL_HEIGHT_RATIO', 1.25);    //1.25

/**
 * title magnification respect main font size
 */
define('K_TITLE_MAGNIFICATION', 1.3);   //1.3

/**
 * reduction factor for small font
 */
define('K_SMALL_RATIO', 2/3);

/**
 * set to true to enable the special procedure used to avoid the overlappind of symbols on Thai language
 */
define('K_THAI_TOPCHARS', true);

/**
 * if true allows to call TCPDF methods using HTML syntax
 * IMPORTANT: For security reason, disable this feature if you are printing user HTML content.
 */
define('K_TCPDF_CALLS_IN_HTML', true);
