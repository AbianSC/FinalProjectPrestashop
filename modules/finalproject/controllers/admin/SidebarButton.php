<?php
class SidebarButton extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->title= 'Final Project';
        $this->table = 'sidebar_button';
    }
    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('sidebar_button.tpl');
    }

    public function sendProductsToAI(){

    }

}