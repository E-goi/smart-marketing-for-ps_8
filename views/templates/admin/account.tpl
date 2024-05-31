{*
* Smart Marketing
*
*  @author    E-goi
*  @copyright 2018 E-goi
*  @license   LICENSE.txt
*}

{include file='./alerts.tpl'}

<div class="panel">
	<div class="egoi panel-heading">
		<span class="icon-user" id="account"></span> <span class="baseline">{l s='Account Details' mod='smartmarketingps'}</span>
	</div>

	{if $clientData}
		<table class="table">
			<thead>
				<th>
					{l s='Client ID' mod='smartmarketingps'}
				</th>
				<th>
					{l s='Company Name' mod='smartmarketingps'}
				</th>
				<th>
					{l s='E-goi Plan' mod='smartmarketingps'}
				</th>
				<th></th>
			</thead>
			<tbody>
				<tr>
					<td>{if isset($clientData['general_info']['client_id'])} {$clientData['general_info']['client_id']|escape:'htmlall':'UTF-8'} {/if}</td>
					<td>{if isset($clientData['general_info']['name'])} {$clientData['general_info']['name']|escape:'htmlall':'UTF-8'} {/if}</td>
					<td>{if isset($clientData['plan_info']['type'])} {$clientData['plan_info']['type']|escape:'htmlall':'UTF-8'} {/if}</td>
					<td>
						<a href="{$redirect|escape:'htmlall':'UTF-8'}" class="btn btn-primary" style="font-size:13px;text-transform:none;">
							{l s='Change E-goi API Key' mod='smartmarketingps'} <span class="icon-edit"></span>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	{else}
		{l s='Error retrieving client data' mod='smartmarketingps'}
	{/if}
</div>

