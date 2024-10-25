<?php
/**
 * Smart Marketing
 *
 *  @author    E-goi
 *  @copyright 2018 E-goi
 *  @license   LICENSE.txt
 *  @package controllers/admin/AccountController
 */

include_once dirname(__FILE__).'/../SmartMarketingBaseController.php';

class AccountController extends SmartMarketingBaseController 
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		$this->bootstrap = true;
		$this->cfg = 0;
		
		$this->meta_title = $this->l('My Account').' - '.$this->module->displayName;
		if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
        $this->apiv3 = new ApiV3();
	}

	/**
     * Inject Dependencies
     *
     * @param $isNewTheme
     * @return void
     */
	public function setMedia($isNewTheme = false)
	{
		parent::setMedia($isNewTheme);
    }
	
	 /**
	 * Toolbar settings
	 * 
	 * @return void
	 */
    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        // Botão da barra de ferramentas para documentação
        $this->page_header_toolbar_btn['novo-botao'] = array(
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

	/**
	 * Initiate content
	 * 
	 * @return void
	 */
	public function initContent()
	{
		parent::initContent();
        if ($this->isValid()) {
            if($data = $this->apiv3->getMyAccount()) {
				$this->assign('clientData', $data);
			} else {
				$this->assign('clientData', false);
			}

			$this->getLists();
			$this->assign('content', $this->fetch('account.tpl'));
		}
	}

	/**
	 * Get all lists from this Account
	 * 
	 * @return void
	 */
	protected function getLists() 
	{
		$msg = '';
		if(!empty($_POST)){
			$msg = $this->postList();
		}

		$this->assign('lists', $this->apiv3->getLists() ?: false);

		if($msg) {
			if(is_numeric($msg)){
				$this->assign('success_msg', $this->displaySuccess('ok'));
			}else{
				$this->assign('error_msg', $this->displayWarning($msg));
			}
		}

		$this->assign('url_list', '/?action=lista_definicoes_principal&list=');
	}
	
	/**
	 * Create list
	 * 
	 * @return mixed
	 */
	protected function postList() 
	{
		if (!empty(Tools::getValue('add-list'))) {
			$name = trim(Tools::getValue('egoi_ps_title'));
            $result = $this->apiv3->createList(["internal_name" => $name, "public_name" => $name]);
            if (isset($result['ERROR'])) {
                $this->assign('error_msg', $this->displayWarning(
                    $this->l('Error on creating this list') . $result['ERROR']));
                return null;
            }
			if($result) {
                $this->assign('success_msg', $this->displaySuccess(
                    $this->l('List "') . $name . $this->l('" successfully created')));
				return null;
			}
		}
		return false;
	}
	
}
