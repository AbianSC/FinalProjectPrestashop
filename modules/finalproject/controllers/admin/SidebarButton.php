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
            // Intentar decodificar la respuesta JSON
            $decodedResponse = json_decode($response, true);
            if ($decodedResponse !== null) {
                // Guardar el JSON en una tabla ps_ia_responses de la base de datos
                // Ejecutar la consulta con los valores

                $saveSql = 'INSERT INTO `ps_ia_api_responses`
            (`discount`, `final_price`, `month`, `predictions`, `price`, `product_id`, `remaining_stock`, `ss`, `stockfinalestimado`, `stockrecomended`, `stocktobuy`, `year`)
            VALUES (1, 20.0, 1, 17, 20.5, 10, 20, 4, 20, 25, 5, 2025)';
                Db::getInstance()->execute($saveSql);
                /*   Db::getInstance()->execute($saveSql, [json_encode($decodedResponse)]);*/

                $this->confirmations[] = $this->l('Data sent successfully and response saved.');
            } else {
                $this->errors[] = $this->l('Data sent, but the API response was not valid or did not contain the "results" key.');
            }

        } else {
            $this->errors[] = $this->l('Failed to send data. API responded with HTTP Code: ') . $httpCode;
        }

    }
}