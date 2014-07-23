<?php
    global $__picture_settings;
    //debug('1671', $__picture_settings);
/**
 *   Параметры настройки изображения
 *   'quality'   => 0..100, default : 100
 *   'width'     => 0 | (int), default : 0
 *   'maxside'   => 0 | (int), default : 0
 *   'height'    => 0 | (int), default : 0
 *   'crop'      => true | false, default : false
 *   'watermark' => true | false, default : false
 *   'ext'       => image extension eg.: 'jpg', default : 'jpg'
 */
    
    $__picture_settings = array(
    
        'default' => array(
            'x' => array('width' => 40, 'height' => 40, 'crop' => true),
            's' => array('maxside' => 100),
            'm' => array('maxside' => 350),
            'l' => array('maxside' => 500),
            'g' => array('maxside' => 800)
        ),

        'biz' => array(
            'x' => array('width' => 40, 'height' => 40, 'crop' => true),
            's' => array('maxside' => 100),
            'm' => array('maxside' => 350),
            'l' => array('maxside' => 500),
            'g' => array('maxside' => 800)
        ),

        'company' => array(
            'x' => array('width' => 40, 'height' => 40, 'crop' => true),
            's' => array('maxside' => 100),
            'm' => array('maxside' => 350),
            'l' => array('maxside' => 500),
            'g' => array('maxside' => 800)
        ),
    
        'album' => array(
            'x' => array('maxside' => 75),
            's' => array('maxside' => 100),
            'm' => array('maxside' => 350),
            'l' => array('maxside' => 500),
            'g' => array('maxside' => 800),
            'z' => array('maxside' => 200)
        ),
        
        'person' => array(
            'x' => array('width' => 40, 'height' => 40, 'crop' => true),
            's' => array('width' => 150, 'height' => 150, 'crop' => true),
            'm' => array('maxside' => 350),
            'l' => array('maxside' => 500),
            'g' => array('maxside' => 800)
        ),        
    );
