<?php
/**
 * Smart Marketing
 *
 *  @author    E-goi
 *  @copyright 2018 E-goi
 *  @license   LICENSE.txt
 *  @package controllers/SmartMarketingBaseController
 */

abstract class SmartMarketingBaseController extends ModuleAdminController 
{
	
	/**
	 * @var string
	 */
	protected $module_name = "smartmarketingps";

	/**
	 * @var string
	 */
	protected $admin_path = _PS_ADMIN_DIR_;

	/**
	 * @var string
	 */
	protected $has_api_key;

	/**
	 * @var object
	 */
	protected $api;

    /**
     * @var object ApiV3
     */
    protected $apiv3;

	/**
	 * By default check if is a configuration page
	 * 
	 * @var integer
	 */
	protected $cfg = 1;

    /**
     * User language
     *
     * @var string
     */

    protected $user_language;

    /**
     * Documentation URL based on user language
     *
     * @var string
     */
    protected $doc_url;


    /**
	 * Constructor
	 */
	public function __construct() 
	{
		parent::__construct();
		
		$this->_path = _PS_MODULE_DIR_ . $this->module_name;
		$this->has_api_key = Configuration::get('smart_api_key') ? true : false;

        //Set User Language
        $this->user_language = $this->context->language && $this->context->language->iso_code
            ? $this->context->language->iso_code
            : 'en';

        $this->assign('user_language', $this->user_language);
        $this->assign('has_api_key', $this->has_api_key);
		$this->assign('error_message', '');
		$this->assign('has_error', false);

        // Set documentation URL based on user language
        $this->setDocumentationUrl();

		// on failture redirect to configuration page
        $this->assign('redirect', $this->redirectToConfig());
	}

    /**
     * Set the documentation URL based on user language
     */
    protected function setDocumentationUrl()
    {
        // Define URL based on the language
        switch ($this->user_language) {
            case 'pt':
            case 'br':
                $this->doc_url = 'https://helpdesk.e-goi.com/135674-Integrar-o-E-goi-com-o-PrestaShop';
                break;
            case 'en':
                $this->doc_url = 'https://helpdesk.e-goi.com/525554-Integrating-E-goi-with-PrestaShop';
                break;
            case 'es':
                $this->doc_url = 'https://helpdesk.e-goi.com/538572-Integrar-E-goi-con-PrestaShop';
                break;
            default:
                $this->doc_url = 'https://helpdesk.e-goi.com/538572-Integrar-E-goi-con-PrestaShop';
                break;
        }
    }

    /**
     * Sanitizes input
     */
    protected function sanitize()
    {
        $_POST = array_map('strip_tags', $_POST);
    }

	/**
     * Inject Dependencies
     *
     * @param $isNewTheme
     * @return void
     */
	public function setMedia($isNewTheme = false)
	{
		$this->addJquery();
		$this->addCSS($this->_path. '/views/css/main.css');
		return parent::setMedia($isNewTheme);
    }

	/**
	 * Activate E-goi API
	 * 
	 * @return void
	 */
	protected function activateApi()
	{
		$this->api = new SmartApi;
	}

	/**
	 * Validate errors from base content
	 * 
	 * @return bool
	 */
	protected function isValid()
	{
		$this->assign('config', $this->cfg);

		// check if not have account configured
		if (!$this->has_api_key) {
			$this->assign('smart_api_key_error', true);
			$this->assign('content', $this->fetch('alerts.tpl'));
			return false;
		}

		return true;
	}
	
	/**
	 * Fetch a view
	 * 
	 * @param  string $template
	 * @return mixed  
	 */
	protected function fetch($template) 
	{
		return $this->context->smarty->fetch(
			$this->module->getTemplatePath('views/templates/admin/'.$template)
		);
	}
	
	/**
	 * Assign a variable to a view
	 * 
	 * @param  string $key
	 * @param  string|array $value
	 * @return object
	 */
	protected function assign($key, $value) 
	{
		return $this->context->smarty->assign($key, $value);
	}

	/**
	 * Display a closable Success
	 * 
	 * @param  string $message
	 */
	protected function displaySuccess($message)
	{
		$this->confirmations = array($message);
	}

    /**
     * Display a closable Error
     *
     * @param  string $message
     */
    protected function displayError($message)
    {
        $this->errors = array($message);
    }

	/**
	 * Display a closable Warning
	 * 
	 * @param  string $message
	 * @return mixed
	 */
	protected function displayWarning($message)
	{
		return parent::displayWarning($message);
	}

	/**
	 * Get custom Controller Route
	 * 
	 * @param  string $controller
	 * @param $withAdmin
	 * @return string
	 */
	protected function getControllerRoute($controller, $withAdmin = false)
	{
		if ($withAdmin) {
			$path = explode('/', $this->admin_path);
		 	$adminPath = array_pop($path);
		 	return $adminPath.'/index.php?controller='.$controller.'&token='.Tools::getAdminTokenLite($controller);
		}

		return 'index.php?controller='.$controller.'&token='.Tools::getAdminTokenLite($controller);
	}

	/**
	 * Redirect to custom Controller
	 * 
	 * @param url
	 * @return mixed
	 */
	protected function redirectTo($url)
    {
        return Tools::redirectAdmin($url);
    }

	/**
	 * Redirect to Configuration Page on Failure
	 * 
	 * @return string
	 */
	private function redirectToConfig() 
	{
        $url = 'index.php?controller=AdminModules';
        $url .= '&token='.Tools::getAdminTokenLite('AdminModules');
        $url .= '&configure=' . $this->module_name . '&tab_module=advertising_marketing&module_name=' . $this->module_name;
        return $url;
    }
	
}
