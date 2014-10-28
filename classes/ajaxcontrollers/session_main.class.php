<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class MainAjaxController extends ApplicationAjaxController {
    
    function MainAjaxController()
    {
        ApplicationAjaxController::ApplicationAjaxController();
                
        //$this->authorize_before_exec['get_category_list']      = ROLE_STAFF;
    }
        
    /*
     * Сохраняет в массив $_SESSION пару ($key => $value)
     * @param $key ключ
     * @param $value значение
     */
    function savetosession()
    {
        $key   = Request::GetString('key', $_REQUEST);
        $value = $_REQUEST['value'];
        $_SESSION[$key] = $value;
        //debug('1682', $_SESSION);
    }
    
    /*
     * Возвращает из массива $_SESSION значение ключа
     * @param $key ключ
     */
    function returnvalue()
    {
        //debug('1682', $_SESSION);
        $key   = Request::GetString('key', $_REQUEST);
        if (!isset($_SESSION[$key])){       //если в сессии нет искомого ключа - возвращаю ошибку
            $this->_send_json(array(
                'result'    => 'error'
            ));
            return;
        }
        $value = $_SESSION[$key];
        $this->_send_json(array(
            'result'    => 'okay',
            'value'   => $value
        ));
    }
}
