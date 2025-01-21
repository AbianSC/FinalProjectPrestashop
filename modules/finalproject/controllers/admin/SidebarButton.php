<?php
class SidebarButton extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'ia_sales_data';
        $this->className = 'Points';
        $this->lang = false;
        $this->bootstrap = true;
        parent::__construct();
    }
    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('sidebar_button.tpl');
    }

    public function tableDataForAI(){

        $sql='
        INSERT INTO ps_ia_sales_data (sale_id, product_id, date, quantity, total_price, batch_expiry_date, remaining_stock)
        SELECT 
            o.id_order AS sale_id,
            od.product_id AS product_id,
            o.date_add AS date,
            od.product_quantity AS quantity,
            o.total_paid AS total_price,
            p.available_date AS batch_expiry_date,
            sa.quantity AS remaining_stock
        FROM ps_orders o
        JOIN ps_order_detail od ON o.id_order = od.id_order
        JOIN ps_product p ON od.product_id = p.id_product
        JOIN ps_stock_available sa ON p.id_product = sa.id_product;';
    }

    public function sendProductsToAI(){

    }

}