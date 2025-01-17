<?php
if(!defined('_PS_VERSION_')){
    exit;
}
class FinalProject extends Module {
    public function __construct() {
        $this->name = "finalproject";
        $this->version = "1.0.0";
        $this->author = 'AbianCristian';
        $this->need_instance = 0;
        $this->displayName = 'Final Project';
        $this->description = 'Sugerencia de promociones con caducidad';
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        parent::__construct();
    }

    public function install()
    {
        if (!parent::install() || !Configuration::updateValue('FINAL_PROJECT_CONFIG', 'value'))
        {
            return false;
        }
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !Configuration::deleteByName('FINAL_PROJECT_CONFIG'))
        {
            return false;
        }
        return true;
    }
}