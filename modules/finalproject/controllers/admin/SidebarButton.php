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

        // Configurar el título de la página en el backoffice
        $this->page_header_toolbar_title = $this->l('Optimize Profits');

    }
    public function initContent()
    {
        parent::initContent();
        $this->context->smarty->assign(['process_url' => self::$currentIndex . '&token=' . $this->token . '&processData=1',]);
        $this->setTemplate('sidebar_button.tpl');
    }


    /**
     * Dispara la acción al pulsar el botón.
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
        $sql = '
        INSERT INTO `' . _DB_PREFIX_ . 'ia_sales_data` (sale_id, product_id, date, quantity, total_price, batch_expiry_date, remaining_stock)
        SELECT 
            o.id_order AS sale_id,
            od.product_id AS product_id,
            o.date_add AS date,
            od.product_quantity AS quantity,
            o.total_paid AS total_price,
            p.available_date AS batch_expiry_date,
            sa.quantity AS remaining_stock
        FROM `' . _DB_PREFIX_ . 'orders` o
        JOIN `' . _DB_PREFIX_ . 'order_detail` od ON o.id_order = od.id_order
        JOIN `' . _DB_PREFIX_ . 'product` p ON od.product_id = p.id_product
        JOIN `' . _DB_PREFIX_ . 'stock_available` sa ON p.id_product = sa.id_product;
        ';

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
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'ia_sales_data`';
        $data = Db::getInstance()->executeS($sql);

        if (!$data) {
            $this->errors[] = $this->l('No data found to send.');
            return;
        }

        // Convertir los datos a JSON
        $jsonData = json_encode($data);

        // Configurar la URL de la API
        $apiUrl = 'https://example.com/api/receive-data';

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
            $this->confirmations[] = $this->l('Data sent successfully.');
        } else {
            $this->errors[] = $this->l('Failed to send data. API responded with HTTP Code: ') . $httpCode;
        }
    }
}