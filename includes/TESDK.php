<?php

class TESDK
{
    const URL = 'https://egoimmerce.e-goi.com/collect';

    protected $clientId;
    protected $subscriber;
    protected $list;
    protected $headers;

    public function __construct($clientId, $subscriber = null, $list = null)
    {
         $this->clientId = $clientId;
         $this->subscriber = $subscriber;
         $this->list = $list;
         $this->headers = ['Referer: '._PS_BASE_URL_.'/'];
    }

    public function convertOrder($order){

        if(empty($order['order']->reference) || empty($order['order']->total_paid)  || !is_array($order['products']) ){
            return false;
        }

        $ec_items = [];

        foreach ($order['products'] as $product){
            $id = empty($product["variant_id"])?$product["id_product"]:$product["variant_id"];
            $product_price = empty($product['product_price_wt'])?$product['product_price']:$product['product_price_wt'];
            $ec_items[] = ["{$id}",htmlentities($product['product_name']),$product['category_names'],number_format($product_price,2), $product['product_quantity']];
        }

        $data = [
            "idgoal" => 0,
            "ec_id" => "{$order['order']->reference}",
            "revenue" => number_format($order['order']->total_paid,2),
            "ec_st" => number_format($order['order']->total_paid_tax_excl,2),
            "ec_tx" => number_format($order['order']->total_shipping, 2),
            "ec_sh" => number_format($order['order']->total_wrapping,2),
            "ec_dt" => number_format($order['order']->total_discounts, 2),
            "ec_items" => json_encode($ec_items),
            "clientid" => $this->clientId,
            "listid" => $this->list,
            "subscriber" => $this->subscriber,
            "campaign" => 0,
            "rec" => 1,
            "r" => 769293,
            "h" => date("H"),
            "m" => date("i"),
            "s" => date("s")
        ];


        $params = '?'.http_build_query($data);
        $this->getMethod(
            self::URL.$params
        );

        return true;
    }

    private function getMethod($url){

        $headers = $this->headers;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 4);
        curl_exec($curl);
        curl_close($curl);
    }

}
