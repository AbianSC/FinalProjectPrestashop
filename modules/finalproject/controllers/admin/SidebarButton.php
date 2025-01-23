<?php

class SidebarButtonController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;

        $this->table = 'ia_sales_data';
        $this->className = 'IA_Sales_Data';
        $this->identifier = 'sale_id';
        $this->page_header_toolbar_title = $this->l('Optimize Profits');
    }

    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(['process_url' => self::$currentIndex . '&token=' . $this->token . '&processData=1',]);
        $this->setTemplate('sidebar_button.tpl');
    }

    /**
     * Dispara la acción al pulsar el botón. Relleno la tabla y creo el JSON
     */
    public function postProcess()
    {
        if (Tools::getValue('processData')) {
            if ($this->tableDataForAI()) {
                $this->sendDataAsJson();
            }
        }
    }

    /**
     * Rellena la tabla `ps_ia_sales_data` mediante una consulta.
     */
    public function tableDataForAI()
    {
        $sql = 'INSERT INTO `ps_ia_sales_data` (product_id, price, batch_expiry_date, remaining_stock)
             SELECT
                 p.id_product AS product_id,
                 p.price AS price,
                 p.available_date AS batch_expiry_date,
                 sa.quantity AS remaining_stock
             FROM `ps_product` p
             LEFT JOIN `ps_stock_available` sa ON p.id_product = sa.id_product';

        try {
            if (!Db::getInstance()->execute($sql)) {
                throw new Exception('Error inserting data into `ps_ia_sales_data`.');
            }
            return true;
        } catch (Exception $e) {
            PrestaShopLogger::addLog(
                'Error while populating `ps_ia_sales_data`: ' . $e->getMessage(),
                3
            );
            $this->errors[] = $this->l('Failed to populate data for AI.');
            return false;
        }
    }

    /**
     * Enviar datos de la tabla `ps_ia_sales_data` como JSON a una API.
     */
    private function sendDataAsJson()
    {
        $sql = 'SELECT * FROM `ps_ia_sales_data`';
        $data = Db::getInstance()->executeS($sql);

        if (!$data) {
            $this->errors[] = $this->l('No data found to send.');
            return;
        }

        //Fomarteamos los datos: pasar de string a int, float
        foreach ($data as $row) {
            $row['data_id'] = (int)$row['data_id'];
            $row['product_id'] = (int)$row['product_id'];
            $row['price'] = (float)$row['price'];
            $row['batch_expiry_date'] = "2025-01-01";
            $row['remaining_stock'] = (int)$row['remaining_stock'];
            $proccessData[] = $row;
        }

        // Convertir el array en JSON
        $jsonData = json_encode($proccessData, JSON_PRETTY_PRINT);

        // Configurar la URL de la API
        $apiUrl = 'https://api-pharmacy-project.onrender.com/predict';

        // Enviar los datos a la API
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
        ]);

        $response = curl_exec($ch);

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {

            $decodedResponse = json_decode($response, true);

            try {
                //Insertamos los datos de respuesta de la API en la tabla ps_ai_api_responses
                $db = Db::getInstance();
                $db->insert('ia_api_responses', $decodedResponse['results']);
                $this->confirmations[] = $this->l('Data sent successfully and response saved.');
                $this->updateSpecificPrices();
            } catch (PDOException $e) {
                $this->errors[] = $this->l('Error to insert data.');
            }

        }
    }

    private function updateSpecificPrices()
    {
        $sql = 'SELECT product_id, discount FROM ' . _DB_PREFIX_ . 'ia_api_responses';
        $discounts = Db::getInstance()->executeS($sql);

        if (!$discounts) {
            die('No se encontraron datos en la tabla ps_ia_api_responses.');
        }

        foreach ($discounts as $row) {
            $productId = (int)$row['product_id'];
            $discountInt = (int)$row['discount'];

            $discountFloat = $discountInt / 100;

            $updateSql = 'UPDATE ' . _DB_PREFIX_ . 'specific_price 
                          SET reduction = ' . (float)$discountFloat . ' 
                          WHERE id_product = ' . (int)$productId;

            Db::getInstance()->execute($updateSql);
        }

    }
}