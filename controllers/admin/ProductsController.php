<?php
/**
 * Smart Marketing
 *
 * @author E-goi
 * @copyright 2019 E-goi
 * @license LICENSE.txt
 * @package controllers/admin/AccountController
 */

include_once dirname(__FILE__) . '/../SmartMarketingBaseController.php';

class ProductsController extends SmartMarketingBaseController
{

    protected $apiv3;

    public function __construct()
    {
        parent::__construct();

        $this->apiv3 = new ApiV3();
        $this->bootstrap = true;
        $this->cfg = 0;

        $this->meta_title = $this->l('Products') . ' - ' . $this->module->displayName;
        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }

        if (!empty($_POST)) {
            $this->sanitize();
        }
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        // Toolbar button for documentation
        $this->page_header_toolbar_btn['egoiDocumentation'] = array(
            'short' => $this->l('E-goi Documentation'),
            'icon' => 'icon-book',
            'href' => $this->doc_url,
            'desc' => $this->l('E-goi Documentation'),
            'js' => $this->l('$( \'#outro-form\' ).click();')
        );

        $this->page_header_toolbar_btn['goto-egoi'] = array(
            'short' => $this->l('Go to E-goi'),
            'icon' => 'icon-external-link',
            'href' => 'https://login.egoiapp.com',
            'desc' => $this->l('Go to E-goi'),
            'js' => $this->l('$( \'#save-form\' ).click();')
        );
    }

    public function initContent()
    {
        parent::initContent();

        if ($this->isValid()) {
            if (!empty($_GET['countProducts'])) {
                $productCount = (int)Db::getInstance()->getValue('SELECT COUNT(id_product) FROM '._DB_PREFIX_.'product WHERE active=1;');
                $productCount = ceil($productCount/100);
                
                echo json_encode(array('lastPage' => $productCount));

                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }

                exit;
            } elseif (!empty($_GET['createCatalog'])) {
                $this->createCatalog();
            } elseif (!empty($_GET['deleteCatalog'])) {
                $this->deleteCatalog($_GET['deleteCatalog']);
            } elseif (!empty($_GET['toggleSync']) && isset($_GET['value'])) {
                $this->toggleSync($_GET['toggleSync']);
            } elseif (!empty($_GET['toggleDescriptions']) && isset($_GET['value'])) {
                $this->toggleDescriptions($_GET['toggleDescriptions']);
            } elseif (!empty($_GET['toggleCategories']) && isset($_GET['value'])) {
                $this->toggleCategories($_GET['toggleCategories']);
            } elseif (!empty($_GET['toggleRelated']) && isset($_GET['value'])) {
                $this->toggleRelated($_GET['toggleRelated']);
            } elseif (!empty($_GET['syncCatalog']) && !empty($_GET['language']) && !empty($_GET['currency'])) {
                $this->syncCatalog($_GET['syncCatalog'], $_GET['language'], $_GET['currency'], true, $_GET['page']);
                exit;
            } elseif (!empty($_GET['syncAllCatalogs'])) {
                $this->syncProducts(true);
            } else {
                if (!empty($_GET['ignoreCategories'])) {
                    Configuration::updateValue('egoi_import_categories', false);
                }

                $this->syncProducts();
            }
        }
    }

    private function createCatalog()
    {
        if (!empty($_POST)) {
            $catalogSync = 0;
            $descriptionsSync = 0;
            $categoriesSync = 0;
            $relatedSync = 0;
            if ($_POST['catalogSync']) {
                $catalogSync = 1;
            }
            if ($_POST['descriptionsSync']) {
                $descriptionsSync = 1;
            }
            if ($_POST['categoriesSync']) {
                $categoriesSync = 1;
            }
            if ($_POST['relatedSync']) {
                $relatedSync = 1;
            }
            $data = array(
                'title' => 'Prestashop_' . $_POST['egoi-catalog-name'],
                'language' => $_POST['egoi-catalog-language'],
                'currency' => $_POST['egoi-catalog-currency']
            );

            $result = $this->apiv3->createCatalog($data);
            if (!is_int($result)) {
                Db::getInstance()->insert(
                    'egoi_active_catalogs',
                    array(
                        'catalog_id' => $result['catalog_id'],
                        'active' => $catalogSync,
                        'sync_descriptions' => $descriptionsSync,
                        'sync_categories' => $categoriesSync,
                        'sync_related_products' => $relatedSync,
                        'language' => $result['language'],
                        'currency' => $result['currency']
                    )
                );

                /*if (!empty($catalogSync)) {
                    $this->syncCatalog($result['catalog_id'], $result['language'], $result['currency'], false);
                }*/

                $this->redirectProducts('catalog_created');
            } else {
                $this->errors[] = $this->l('Error creating catalog');
            }
            
            return;
        }

        $languages = array();
        foreach (Language::getLanguages(true, $this->context->shop->id) as $language) {
            $input = strtoupper($language['iso_code']);
            if (in_array($language['iso_code'], ['cb', 'co'])) {
                $input = 'ES';
            }
            $languages[] = $input;
        }
        $defaultLanguage = $this->context->language->iso_code;
        $this->assign('languages', $languages);
        $this->assign('defaultLanguage', $defaultLanguage);

        $currencies = array();
        foreach (Currency::getCurrencies(true) as $currency) {
            $currencies[] = $currency->iso_code;
        }
        $defaultCurrency = $this->context->currency->iso_code;
        $this->assign('currencies', $currencies);
        $this->assign('defaultCurrency', $defaultCurrency);

        $this->assign('content', $this->fetch('create-catalog.tpl'));
    }

    private function deleteCatalog($id)
    {
        $this->checkCatalogValid($id);

        $result = $this->apiv3->deleteCatalog($id);
        if ($result === 204) {
            Db::getInstance()->delete('egoi_active_catalogs', 'catalog_id = ' . (int)$id);
            $this->redirectProducts('catalog_delete_success');
        } else {
            $this->redirectProducts('catalog_delete_error');
        }
    }

    private function syncCatalog($id, $lang, $curr, $skip = false, $page = 0)
    {
        $this->checkCatalogValid($id);

        $data = array('products' => []);

        $languages = Language::getLanguages(true, $this->context->shop->id);
        $langId = 0;
        foreach ($languages as $language) {
            if ($language['iso_code'] === strtolower($lang) || in_array($language['iso_code'], ['cb','co'])) {
                $langId = $language['id_lang'];
            }
        }
        if ($langId === 0) {
            $this->redirectProducts('lang_not_active');
        }

        $currencies = Currency::getCurrencies(true);
        $currencyId = 0;
        foreach ($currencies as $currency) {
            if ($currency->iso_code === $curr) {
                $currencyId = $currency->id;
            }
        }

        if ($currencyId === 0) {
            $this->redirectProducts('currency_not_active');
        }
        $selectedCatalog = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "egoi_active_catalogs WHERE catalog_id=".$id);

        if ($page != 0) {
            $page = ($page - 1) * 100;
        }

        $products = Product::getProducts($langId, $page, 100, 'id_product', 'DESC', false, true);

        foreach ($products as $product) {
            $data['products'][] = SmartMarketingPs::mapProduct($product, $langId, $currencyId, !empty($selectedCatalog[0]["sync_descriptions"]), !empty($selectedCatalog[0]["sync_categories"]), !empty($selectedCatalog[0]["sync_related_products"]));
        }

        $result = $this->apiv3->importProducts($id, $data);

        if ($skip) return;

        if (isset($result['result']) && $result['result'] === 'success') {
            $this->redirectProducts('import_success');
        }

        $this->redirectProducts('import_failed');
    }

    private function toggleSync($id)
    {
        $this->checkCatalogValid($id, $_GET['value'] != 0 && $_GET['value'] != 1);

        $newValue = (int)!$_GET['value'];
        Db::getInstance()->update(
            'egoi_active_catalogs',
            array(
                'active' => $newValue
            ),
            'catalog_id = ' . (int)$id
        );

        if ($newValue === 1) {
            $this->redirectProducts('catalog_toggle_sync_true');
        }

        $this->redirectProducts('catalog_toggle_sync_false');
    }

    private function toggleDescriptions($id)
    {
        $this->checkCatalogValid($id, $_GET['value'] != 0 && $_GET['value'] != 1);

        $newValue = (int)!$_GET['value'];
        Db::getInstance()->update(
            'egoi_active_catalogs',
            array(
                'sync_descriptions' => $newValue
            ),
            'catalog_id = ' . (int)$id
        );

        if ($newValue === 1) {
            $this->redirectProducts('catalog_toggle_descriptions_true');
        }

        $this->redirectProducts('catalog_toggle_descriptions_false');
    }


    function toggleCategories($id)
    {
        $this->checkCatalogValid($id, $_GET['value'] != 0 && $_GET['value'] != 1);

        $newValue = (int)!$_GET['value'];
        Db::getInstance()->update(
            'egoi_active_catalogs',
            array(
                'sync_categories' => $newValue
            ),
            'catalog_id = ' . (int)$id
        );

        if ($newValue === 1) {
            $this->redirectProducts('catalog_toggle_categories_true');
        }

        $this->redirectProducts('catalog_toggle_categories_false');
    }
    function toggleRelated($id)
    {
        $this->checkCatalogValid($id, $_GET['value'] != 0 && $_GET['value'] != 1);

        $newValue = (int)!$_GET['value'];
        Db::getInstance()->update(
            'egoi_active_catalogs',
            array(
                'sync_related_products' => $newValue
            ),
            'catalog_id = ' . (int)$id
        );

        if ($newValue === 1) {
            $this->redirectProducts('catalog_toggle_related_true');
        }

        $this->redirectProducts('catalog_toggle_related_false');
    }

    private function syncProducts($syncAll = false)
    {
        $this->checkNotifications();

        $catalogs = $this->apiv3->getCatalogs();
        $showCatalogs = array();
        if (!is_int($catalogs)) {
            $catalogs = $catalogs['items'];
            $catalogCount = count($catalogs);
            $catalogEnabled = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "egoi_active_catalogs ORDER BY catalog_id DESC");

            for ($i = 0; $i < $catalogCount; $i++) {
                $key = $this->searchForId($catalogs[$i]['catalog_id'], $catalogEnabled);
                if ($key !== false) {
                    $catalogs[$i]['active'] = $catalogEnabled[$key]['active'];
                    $catalogs[$i]['sync_descriptions'] = $catalogEnabled[$key]['sync_descriptions'];
                    $catalogs[$i]['sync_categories'] = $catalogEnabled[$key]['sync_categories'];
                    $catalogs[$i]['sync_related_products'] = $catalogEnabled[$key]['sync_related_products'];
                    $showCatalogs[] = $catalogs[$i];
                    if ($syncAll) {
                        $this->syncCatalog($catalogs[$i]['catalog_id'], $catalogs[$i]['language'], $catalogs[$i]['currency'], true);
                    }
                }
            }

            if ($syncAll) {
                Configuration::updateValue('egoi_import_categories', false);
            }
        } else {
            $this->errors[] = $this->l('Error loading catalogs');
        }

        if (Configuration::get('egoi_import_categories')) {
            $this->assign('importCategories', true);
        }

        $this->assign('catalogs', $showCatalogs);
        $this->assign('content', $this->fetch('sync-products.tpl'));
    }

    private function checkCatalogValid($id, $condition = false)
    {
        if (!is_numeric($id) || $condition) {
            $this->redirectProducts('invalid_catalog');
        }
    }

    private function checkNotifications()
    {
        switch (Context::getContext()->cookie->notificacion) {
            case 'catalog_created':
                $this->assign('success_msg', $this->displaySuccess($this->l('Catalog created')));
                break;
            case 'invalid_catalog':
                $this->errors[] = $this->l('Invalid catalog');
                break;
            case 'catalog_delete_error':
                $this->errors[] = $this->l('Error deleting catalog');
                break;
            case 'catalog_delete_success':
                $this->assign('success_msg', $this->displaySuccess($this->l('Catalog deleted')));
                break;
            case 'catalog_toggle_sync_true':
                $this->assign('success_msg', $this->displaySuccess($this->l('Automatic sync enabled for the selected catalog')));
                break;
            case 'catalog_toggle_sync_false':
                $this->assign('success_msg', $this->displaySuccess($this->l('Automatic sync disabled for the selected catalog')));
                break;
            case 'catalog_toggle_descriptions_true':
                $this->assign('success_msg', $this->displaySuccess($this->l('Sync descriptions enabled for the selected catalog')));
                break;
            case 'catalog_toggle_descriptions_false':
                $this->assign('success_msg', $this->displaySuccess($this->l('Sync descriptions disabled for the selected catalog')));
                break;
            case 'catalog_toggle_categories_true':
                $this->assign('success_msg', $this->displaySuccess($this->l('Sync categories enabled for the selected catalog')));
                break;
            case 'catalog_toggle_categories_false':
                $this->assign('success_msg', $this->displaySuccess($this->l('Sync categories disabled for the selected catalog')));
                break;
            case 'catalog_toggle_related_true':
                $this->assign('success_msg', $this->displaySuccess($this->l('Sync related products enabled for the selected catalog')));
                break;
            case 'catalog_toggle_related_false':
                $this->assign('success_msg', $this->displaySuccess($this->l('Sync related products disabled for the selected catalog')));
                break;
            case 'lang_not_active':
                $this->errors[] = $this->l('The language of the selected catalog is not active in your store');
                break;
            case 'currency_not_active':
                $this->errors[] = $this->l('The currency of the selected catalog is not active in your store');
                break;
            case 'import_success':
                $this->assign('success_msg', $this->displaySuccess($this->l('Products were successfully imported to E-goi')));
                break;
            case 'import_failed':
                $this->errors[] = $this->l('Error! Some products were not imported to E-goi');
                break;
            default:
        }

        Context::getContext()->cookie->notificacion = '';
    }

    private function redirectProducts($message)
    {
        $link = $this->context->link->getAdminLink('Products');
        Context::getContext()->cookie->notificacion = $message;
        Tools::redirectAdmin($link);
    }

    private function searchForId($id, $array)
    {
        foreach ($array as $key => $val) {
            if ($val['catalog_id'] == $id) {
                return $key;
            }
        }

        return false;
    }
}
