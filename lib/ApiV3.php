<?php

/**
 * @package lib/ApiV3
 */
class ApiV3 extends EgoiRestApi
{

    /**
     * @var string API_URL
     */
    const API_URL = 'https://api.egoiapp.com';


    public function setApiKey($apikey)
    {
        $this->apiKey = $apikey;
    }

    public function getApiKey()
    {
        return $this->apiKey = $this->apiKey ?: Configuration::get('smart_api_key');
    }

    /**
     * Returns user account information
     *
     * @return mixed
     */
    public function getMyAccount()
    {
        return $this->call('GET', '/my-account');
    }

    /**
     * Returns all lists
     *
     * @return mixed
     */
    public function getLists()
    {
        return $this->call('GET', '/lists');
    }

    /**
     * Creates a list
     *
     * @param $data
     *
     * @return mixed
     */
    public function createList($data)
    {
        return $this->call('POST', '/lists', $data);
    }

    /**
     * Gets all Fields
     *
     * @param $data
     *
     * @return mixed
     */
    public function getAllFields($listId)
    {
        return $this->call('GET', '/lists/' . $listId . '/fields');
    }

    /**
     * Gets all Fields
     *
     * @param $data
     *
     * @return mixed
     */
    public function getContactsNum($listId)
    {
        return $this->call('GET', '/lists/' . $listId . '/contacts', ['limit' => 1]);
    }

    /**
     * @param $email
     * @param $listId
     * @return mixed|void
     */
    public function searchContactByEmail($email, $listId)
    {
        $contacts = $this->call('GET', '/contacts/search', ['type' => 'email', 'contact' => $email]);
        if (!empty($contacts['items'])) {
            foreach ($contacts['items'] as $contact) {
                if ($contact['list_id'] == $listId) {
                    return $contact['contact_id'];
                }
            }
        }
    }

    /**
     * @param $listId
     * @param $contactId
     * @param $data
     * @return int|mixed
     */
    public function patchContact($listId, $contactId, $data)
    {
        return $this->call('PATCH', '/lists/' . $listId . '/contacts/' . $contactId, $data);
    }

    /**
     * @param $listId
     * @param $data
     * @return int|mixed
     */
    public function createContact($listId, $data)
    {
        return $this->call('POST', '/lists/' . $listId . '/contacts', $data);
    }

    /**
     * @param $listId
     * @param $data
     * @return int|mixed
     */
    public function unsubscribeContact($listId, $data)
    {
        return $this->call('POST', '/lists/' . $listId . '/contacts/actions/unsubscribe', $data);
    }

    /**
     * Imports products to a catalog
     *
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function addSubscriberBulk($listId, $data)
    {
        return $this->call('POST', '/lists/' . $listId . '/contacts/actions/import-bulk', $data);
    }

    /**
     * Gets all extra Fields
     *
     * @param $data
     *
     * @return mixed
     */
    public function getAllExtraFields($listId)
    {
        $allFields = $this->getAllFields($listId);
        $extraFields = [];
        foreach ($allFields as $field) {
            if ($field['type'] == 'extra') {
                $extraFields[] = $field;
            }
        }
        return $extraFields;
    }

    /**
     * Returns all catalogs
     *
     * @return mixed
     */
    public function getCatalogs()
    {
        return $this->call('GET', '/catalogs');
    }

    /**
     * Creates a catalog
     *
     * @param $data
     *
     * @return mixed
     */
    public function createCatalog($data)
    {
        return $this->call('POST', '/catalogs', $data);
    }

    /**
     * Deletes a catalog
     *
     * @param $id
     *
     * @return mixed
     */
    public function deleteCatalog($id)
    {
        return $this->call('DELETE', '/catalogs/' . $id);
    }

    /**
     * Imports products to a catalog
     *
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function importProducts($id, $data)
    {
        return $this->call('POST', '/catalogs/' . $id . '/products/actions/import', $data);
    }

    /**
     * Creates a product
     *
     * @param $catalogId
     * @param $data
     *
     * @return mixed
     */
    public function createProduct($catalogId, $data)
    {
        return $this->call('POST', '/catalogs/' . $catalogId . '/products', $data);
    }

    /**
     * Updates a product
     *
     * @param $catalogId
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function updateProduct($catalogId, $id, $data)
    {
        return $this->call('PATCH', '/catalogs/' . $catalogId . '/products/' . $id, $data);
    }

    /**
     * Deletes a product
     *
     * @param $catalogId
     * @param $id
     * @param $data
     *
     * @return mixed
     */
    public function deleteProduct($catalogId, $id)
    {
        return $this->call('DELETE', '/catalogs/' . $catalogId . '/products/' . $id);
    }

    /**
     * Returns all web push sites
     *
     * @param $listId
     *
     * @return mixed
     */
    public function getWebPushSites($listId)
    {
        $url = '/webpush/sites';

        if ($listId) {
            $url .= '?' . http_build_query(array($listId));
        }

        return $this->call('GET', $url);
    }

    /**
     * Creates a web push site
     *
     * @param $data
     *
     * @return mixed
     */
    public function createWebPushSite($data)
    {
        return $this->call('POST', '/webpush/sites', $data);
    }

    /**
     * Creates a new Order
     *
     * @param $data
     *
     * @return mixed
     */
    public function convertOrder($domain, $order)
    {
        $domain = "localhost";
        return $this->call('POST', '/'. $domain . '/orders' , $order);
    }

    /**
     * Import Current Orders
     *
     * @param $data
     *
     * @return mixed
     */
    public function importOrders($listId, $orders)
    {
        return $this->call('POST', '/lists/'. $listId . '/orders' , $orders);
    }

    /**
     * Creates a TE domain
     *
     * @param $domain
     * @param $list
     *
     * @return mixed
     */
    public function activateTrackingEngage($domain, $list){
        $domain = str_replace('http://', '',
            str_replace('https://', '', $domain)
        );

        return $this->call('POST', '/my-account/actions/enable-te', [
            'domain'      => $domain,
            'list_id'     =>  $list
        ]);
    }

    public function activateConnectedSites($domain, $list){
        $domain = empty(parse_url($domain)['host'])?$domain:parse_url($domain)['host'];


        return $this->call('POST', '/connectedsites', [
            'domain'      => $domain,
            'list_id'     =>  $list
        ]);
    }

    public function getConnectedSite($domain){
        $domain = empty(parse_url($domain)['host'])?$domain:parse_url($domain)['host'];
        return $this->call('GET', "/connectedsites/$domain");
    }

    public function getCellphoneSenders()
    {
        return $this->call('GET', '/senders/cellphone');
    }

    /**
     * Calls api v3
     *
     * @param $method
     * @param $url
     * @param array $data
     *
     * @return mixed
     */
    protected function call($method, $url, $data = array())
    {
        $curl = curl_init();
        $url = self::API_URL . $url;
        $headers = array(
            "Apikey: $this->apiKey",
            'Pluginkey: ' . SmartMarketingPs::PLUGIN_KEY
        );

        switch ($method) {
            case 'GET':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
                $url = sprintf("%s?%s", $url, http_build_query($data));
                break;
            case 'POST':
            case 'PATCH':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
                $headers[] = 'Content-Type: application/json';
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
                break;
            case 'DELETE':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($result && $httpCode !== 500) {
            return json_decode($result, true);
        }

        return $httpCode;
    }
}
