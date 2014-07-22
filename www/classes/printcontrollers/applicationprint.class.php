<?php

class ApplicationPrintController extends PrintController
{
    protected $page_no = 1;
    
    function ApplicationPrintController()
    {
        PrintController::PrintController();
    }
}