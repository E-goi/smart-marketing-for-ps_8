{*
* Smart Marketing
*
*  @author    E-goi
*  @copyright 2018 E-goi
*  @license   LICENSE.txt
*}

{include file='./alerts.tpl' config='true'}

<form method="post" action="" autocomplete="off">
	<div class="panel" id="panel_egbody">
		<div class="egoi panel-heading">
			<span class="img_forms">&nbsp;</span> <span class="baseline">{l s='Configuration' mod='smartmarketingps'}</span>
		</div>
		
		<div class="form-group">
			<label class="control-label col-lg-3">
				<span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='This key is used in your E-goi Account API' mod='smartmarketingps'}">
					{l s='Your API Key' mod='smartmarketingps'}
				</span>
			</label>
			
			<div class="col-lg-9">	
				<input type="hidden" id="egoi_client_id" name="egoi_client_id">
				<input type="password" style="width:50%;display:-webkit-inline-box;" name="smart_api_key" id="smart_api_key" size="55" value="{Configuration::get('smart_api_key')|escape:'htmlall':'UTF-8'}" disabled />

				<div class="sync_api_key" style="display:none;"></div>
				<div data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='This key is invalid!' mod='smartmarketingps'}" id="error" style="display:none;">
					<i class="material-icons action-enabled">error</i>
				</div>
				<a class="btn btn-info" id="edit_key">{l s='Change API Key' mod='smartmarketingps'}</a>
				<input type="submit" name="submit_api_key" id="apikey_submit" value="{l s='Save' mod='smartmarketingps'}" class="btn btn-primary" style="display: none;">
			</div>
		</div>

		<div class="form-group">
			<p>&nbsp;</p>
			{l s='To get your API Key, login into your' mod='smartmarketingps'} <a target="_blank" href="https://login.egoiapp.com">BO</a> {l s='go to your user menu (upper right corner), select "Integrations" and copy the account API key' mod='smartmarketingps'}
		</div>

		<div class="form-group">
			<a class="btn btn-default" id="egoiDocumentation_button"
			   href="{l s='egoiDocumentationLink' mod='smartmarketingps'}"
			   target="_blank">
				<div style="font-size: 13px; display: inline-block; vertical-align: super;">
					{l s='E-goi Documentation' mod='smartmarketingps'}
				</div>
				<i class="material-icons action-enabled" style="font-size: 18px;">navigate_next</i>
			</a>

			<a class="btn btn-default" id="account_button">
				<div style="font-size: 13px;display: inline-block;vertical-align: super;">{l s='Go to My Account' mod='smartmarketingps'}</div>
				<i class="material-icons action-enabled" style="font-size: 18px;">navigate_next</i>
			</a>
		</div>

	</div>
</form>
