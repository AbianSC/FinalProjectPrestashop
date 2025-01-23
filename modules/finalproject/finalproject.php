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
        $this->author = 'Abian && Cristian';
        $this->need_instance = 0;
        $this->bootstrap = true;

        $this->displayName = 'PharmacIA';
        $this->description = 'Optimize the sale of pharmaceutical products with artificial intelligence';
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
            `data_id` INT NOT NULL AUTO_INCREMENT,
            `product_id` INT NOT NULL,
            `price` DECIMAL(10,2),
            `batch_expiry_date` DATE,
            `remaining_stock` INT,
            PRIMARY KEY (`data_id`)
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

    private function installFetchApiTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS`' . _DB_PREFIX_ . 'ia_api_responses` (
                    id INT AUTO_INCREMENT PRIMARY KEY, 
                    discount INT,
                    final_price FLOAT,
                    month INT,
                    predictions INT,
                    price FLOAT,
                    product_id INT,
                    remaining_stock INT,
                    ss INT,       
                    stockfinalestimado INT,
                    stockrecomended INT,
                    stocktobuy INT,
                    year INT
             ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        try {
            if (!Db::getInstance()->execute($sql)) {
                throw new Exception('Table creation failed');
            }
            return true;
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'Failed to create table `' . _DB_PREFIX_ . 'ia_api_responses`. Error: ' . $e->getMessage() . 'SQL: ' . $sql,
                3,
                null,
                (int)$this->id
            );
            return false;
        }

    }

    public function uninstall()
    {
        if (!$this->uninstallFetchApiTable() ||
            !$this->uninstallDb() ||
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

    private function uninstallFetchApiTable()
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'ia_api_responses`;';
        return Db::getInstance()->execute($sql);
    }

    private function registerTab()
    {
        $tab = new Tab();
        $tab->class_name = 'SidebarButton'; // Nombre del controlador
        $tab->module = $this->name;
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentCustomerThreads');
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Optimize Profits';
        }
        return $tab->save();
    }

}

