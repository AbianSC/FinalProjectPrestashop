<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class FinalProject extends Module
{
    public function __construct()
    {
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
            !$this->installDb() ||
            !$this->installFetchApiTable() ||
            !$this->registerTab()
        ) {
            return false;
        }
        return true;
    }

    private function installDb()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ia_sales_data` (
            `sale_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `date` DATETIME,
            `quantity` INT,
            `total_price` DECIMAL(10,2),
            `batch_expiry_date` DATE,
            `remaining_stock` INT,
            PRIMARY KEY (`sale_id`, `product_id`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        try {
            if (!Db::getInstance()->execute($sql)) {
                throw new Exception('Table creation failed');
            }
            return true;
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'Failed to create table `' . _DB_PREFIX_ . 'ia_sales_data`. Error: ' . $e->getMessage() . 'SQL: ' . $sql,
                3,
                null,
                (int)$this->id
            );
            return false;
        }
    }

    private function installFetchApiTable(){
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'ia_api_responses` (
            `product_id` INT NOT NULL,
            `discount` DECIMAL(10,2),
            PRIMARY KEY (`product_id`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        try {
            if (!Db::getInstance()->execute($sql)) {
                throw new Exception('Table creation failed');
            }
            return true;
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'Failed to create table `' . _DB_PREFIX_ . 'ps_ia_api_responses`. Error: ' . $e->getMessage() . 'SQL: ' . $sql,
                3,
                null,
                (int)$this->id
            );
            return false;
        }

    }

    public function uninstall()
    {
        if (!$this->uninstallDb() ||
            !parent::uninstall() ||
            !Configuration::deleteByName('FINAL_PROJECT_CONFIG')) {
            return false;
        }
        return true;
    }

    private function uninstallDb()
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ia_sales_data`;';
        return Db::getInstance()->execute($sql);
    }

    private function registerTab()
    {
        $tab = new Tab();
        $tab->class_name = 'SidebarButton'; // Nombre del controlador
        $tab->module = $this->name;
        // $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentCustomer');
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentCustomerThreads');
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Optimize Profits';
        }
        return $tab->save();
    }

}

