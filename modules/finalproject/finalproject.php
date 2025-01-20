<?php
if(!defined('_PS_VERSION_')){
    exit;
}
class FinalProject extends Module {
    public function __construct() {
        $this->name = "finalproject";
        $this->tab = 'front_office_features';
        $this->version = "1.0.0";
        $this->author = 'AbianCristian';
        $this->need_instance = 0;
        $this->bootstrap = true;

        $this->displayName = 'Final Project';
        $this->description = 'Sugerencia de promociones con caducidad';
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        parent::__construct();
    }

    public function install()
    {
        if (!parent::install() ||
            !Configuration::updateValue('FINAL_PROJECT_CONFIG', 'value') ||
            !$this->registerTab()
        )
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

    private function registerTab()
    {
        $tab = new Tab();
        $tab->class_name = 'SidebarButton'; // Nombre del controlador
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentCustomer');
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Envio productos';
        }

        return $tab->save();
    }


}

