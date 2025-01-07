{*
* Smart Marketing
*
*  @author    E-goi
*  @copyright 2018 E-goi
*  @license   LICENSE.txt
*}

{include file='./alerts.tpl'}

<div class="panel">
	<div class="egoi panel-heading"><span class="icon-group" id="subs"></span> <span class="baseline">{l s='My Subscribers' mod='smartmarketingps'}</span></div>
    <form method="post">
    	<span id="sub_in_egoi" style="display: none;">{l s='Subscribed in E-goi (Active)' mod='smartmarketingps'}</span>
    	<span id="sub_in_ps" style="display: none;">{l s='Subscribed in PrestaShop' mod='smartmarketingps'}</span>

		{if isset($sync) and ($sync)}
			<div style="border:1px solid #ccc;text-align:center;margin-bottom: 20px;" class="alert alert-info">
				<span style="background:#1e94ab;color:#fff;padding:5px;">{l s='Syncronization ON' mod='smartmarketingps'}</span>
                <p style="padding-top: 20px;"></p>
                <div id="sync">
                    <span id="valid_sync"></span>
                    <p id="egoi_sync_users_ps"></p>
                    <div class="egoi_sync_users">
                        {l s='Loading Subscribers Information...' mod='smartmarketingps'}
                    </div>
                </div>
				<p>{l s='The Module is listening to changes and will automatically keep your customers sync with the selected E-goi list' mod='smartmarketingps'}</p>
			</div>
		{/if}

		<table class="table" id="egoi-subs-table">
			<tr valign="top">
				<th class="egoi-td" scope="row">{l s='Sync customers with this list' mod='smartmarketingps'}</th>
				<td>
					{if !$lists.items}
						{l s='No lists found, are you connected to E-goi and/or have created lists?' mod='smartmarketingps'}
					{else}
						{if isset($list_altered) && $list_altered == false}
							<select name="list" id="egoi_lists" style="border: 1px solid #ccc; -webkit-appearance: none; -moz-appearance: none; appearance: none;">
								{foreach $lists.items as $list}
									{if isset($list_id) && ($list_id eq $list.list_id)}
										<option value="{$list.list_id|escape:'htmlall':'UTF-8'}" selected="selected">{$list.internal_name|escape:'htmlall':'UTF-8'}</option>
									{else}
										<option value="{$list.list_id|escape:'htmlall':'UTF-8'}">{$list.internal_name|escape:'htmlall':'UTF-8'}</option>
									{/if}
								{/foreach}
							</select>
						{else}
							<select disabled name="list_disabled" id="egoi_lists_disabled" style="border: 1px solid #ccc; -webkit-appearance: none; -moz-appearance: none; appearance: none;">
								{foreach $lists.items as $list}
									{if isset($list_id) && ($list_id eq $list.list_id)}
										<option value="{$list.list_id|escape:'htmlall':'UTF-8'}" selected="selected">{$list.internal_name|escape:'htmlall':'UTF-8'}</option>
									{else}
										<option value="{$list.list_id|escape:'htmlall':'UTF-8'}">{$list.internal_name|escape:'htmlall':'UTF-8'}</option>
									{/if}
								{/foreach}
							</select>
    						<input type="hidden" name="list" id="egoi_lists" value="{if isset($listValue) && $listValue !== null}{$listValue|escape:'htmlall':'UTF-8'}{/if}">
						{/if}

						{if isset($list_altered) && $list_altered == false}
							<p>{l s='Choose the list your PrestaShop clients will be synched with.' mod='smartmarketingps'}</p>
						{else}
							<p>{l s='This list will be synchronised with your PrestaShop clients. If you want to modify the list, you will need to remove the current integration and create a new one.' mod='smartmarketingps'}</p>
						{/if}
					{/if}
				</td>
			</tr>

			<tr valign="top">
				<th class="egoi-td" scope="row">{l s='Sync customers with this role' mod='smartmarketingps'}</th>
				<td>
					<select name="role">
						<option value="">{l s='All roles' mod='smartmarketingps'}</option>
						{foreach $roles as $role}?>
							{if isset($role_id) and ($role_id eq $role.id_group)}
								<option value="{$role.id_group|escape:'htmlall':'UTF-8'}" selected="selected">{$role.name|escape:'htmlall':'UTF-8'}</option>
							{else}
								<option value="{$role.id_group|escape:'htmlall':'UTF-8'}">{$role.name|escape:'htmlall':'UTF-8'}</option>
							{/if}	
						{/foreach}?>
					</select>
					<p>{l s='Select the role to synchronize your Subscribers with.' mod='smartmarketingps'}</p>

				</td>
			</tr>

			{if isset($list_id) and ($list_id)}

				<tr>
					<th class="egoi-td" scope="row">{l s='Enable auto-sync' mod='smartmarketingps'}</th>
					<td class="nowrap input-group">
					<span class="switch prestashop-switch fixed-width-lg">
						<input type="radio" name="enable" id="sync0" value="1" {if isset($sync) and ($sync eq '1')} checked {/if}>
						<label for="sync0">{l s='Yes' mod='smartmarketingps'}</label>
						<input type="radio" name="enable" id="sync1" value="0" {if (!isset($sync)) or (isset($sync) and ($sync eq '0'))} checked {/if}>
						<label for="sync1">{l s='No' mod='smartmarketingps'}</label>
						<a class="slide-button btn"></a>
					</span>
						<p class="help">{l s='Select "yes" if you want to "listen" to all changes in your customers and auto-sync them with the selected Egoi list' mod='smartmarketingps'}</p>
					</td>
				</tr>

{*			{if isset($sync) and ($sync)}*}
				<tr valign="top">
					<th class="egoi-td" scope="row">{l s='Sync existing customers' mod='smartmarketingps'}</th>
					<td>
						<div style="display: flex;flex-direction: row;align-items: center;justify-content: flex-start">
							<button type="button" class="btn btn-info" id="sync_old_subs">
								{l s='Sync Customers' mod='smartmarketingps'}
							</button>
							<div class="sync_customers" style="display: none;"></div>
							<i class="material-icons" id="sync_success" style="display: none;">beenhere</i>
							<i class="material-icons" id="sync_nousers" style="display: none;">no_accounts</i>
							<span id="spanprogress" style="width: 200px; height: 20px;">
								<div id="progressbarSync" class="progress" style="display: none;margin: 0;">
									<div id="progressbarValues" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 0px;width:0%">
									</div>
								</div>
							</span>
						</div>
						<p class="help">{l s='Sync already existing PrestaShop customers to E-goi List' mod='smartmarketingps'}</p>
					</td>
				</tr>

				<tr valign="top">
					<th class="egoi-td" scope="row">{l s='Sync custom fields' mod='smartmarketingps'}</th>
					<td>
						<button type="button" class="btn btn-info" data-toggle="modal" data-target="#CustomFields">{l s='Map Custom Fields' mod='smartmarketingps'}</button>
						<p class="help">
							{l s='Synchronize PrestaShop custom fields with your E-goi list, including the possibility of mapping ' mod='smartmarketingps'}<br>
							{l s='the Store Name and the Language Selected by the customer as extra E-goi text fields, for a more personalized synchronization.' mod='smartmarketingps'}<br>
						</p>
					</td>
				</tr>

				<tr>
					<th class="egoi-td" scope="row">
						<span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Newsletter module block' mod='smartmarketingps'}">
							<b>{l s='Sync ONLY Newsletter Subscribers' mod='smartmarketingps'}</b>
						</span>
					</th>
					<td class="nowrap input-group">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="newsletter_sync"
								   id="newsletter_sync0"
								   value="1"
								{if isset($newsletter_sync) and ($newsletter_sync eq '1')} checked {/if}>
							<label for="newsletter_sync0">{l s='Yes' mod='smartmarketingps'}</label>

							<input type="radio" name="newsletter_sync"
								   id="newsletter_sync1"
								   value="0"
								{if $newsletter_sync eq '0' or $newsletter_sync eq ''} checked {/if}>
							<label for="newsletter_sync1">{l s='No' mod='smartmarketingps'}</label>
							<a class="slide-button btn"></a>
						</span>
						<p class="help">{l s='Select "yes" if you want to enable ONLY newsletter subscribers synchronization from this ' mod='smartmarketingps'}
							<a target="_blank" href="index.php?controller=AdminModules&configure=ps_emailsubscription&token={$token|escape:'htmlall'}">
								{l s='module' mod='smartmarketingps'}
							</a>
						</p>
					</td>
				</tr>



				<tr valign="top">
					<th class="egoi-td" scope="row">{l s='Sync existing Newsletter Subscribers' mod='smartmarketingps'}</th>
					<td>
						<div style="display: flex;flex-direction: row;align-items: center;justify-content: flex-start">
							<button type="button" class="btn btn-info" id="sync_old_news_subs">
								{l s='Sync Newsletter Subscribers' mod='smartmarketingps'}
							</button>
							<div class="sync_customers2" style="display: none;"></div>
							<i class="material-icons" id="sync_success2" style="display: none;">beenhere</i>
							<i class="material-icons" id="sync_nousers2" style="display: none;">no_accounts</i>
							<span id="spanprogress2" style="width: 200px; height: 20px;">
								<div id="progressbarSync2" class="progress" style="display: none;margin: 0;">
									<div id="progressbarValues2" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-width: 0px;width:0%">
									</div>
								</div>
							</span>
						</div>
						<p class="help">{l s='Sync already existing Newsletter Subscribers to E-goi List' mod='smartmarketingps'}</p>
					</td>
				</tr>


				<tr>
					<th class="egoi-td" scope="row">
						<span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Newsletter module block' mod='smartmarketingps'}">
							<b>{l s='Double Optin Registrations' mod='smartmarketingps'}</b>
						</span>
					</th>
					<td class="nowrap input-group">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="newsletter_optin"
								   id="optin0"
								   value="1"
								{if isset($optin) and ($optin eq '1')} checked {/if} >
							<label for="optin0">{l s='Yes' mod='smartmarketingps'}</label>

							<input type="radio" name="newsletter_optin"
								   id="optin1"
								   value="0"
								{if $optin eq '0' or $optin eq ''} checked {/if} >
							<label for="optin1">{l s='No' mod='smartmarketingps'}</label>
							<a class="slide-button btn"></a>
						</span>
						<p class="help">{l s='Select "yes" if you want to enable double optin when your subscribers register in this' mod='smartmarketingps'}
							<a target="_blank" href="index.php?controller=AdminModules&configure=ps_emailsubscription&token={$token|escape:'htmlall'}">
								{l s='module' mod='smartmarketingps'}
							</a>
						</p>
					</td>
				</tr>








				<tr>
					<th class="egoi-td" scope="row">{l s='Enable Connected Sites' mod='smartmarketingps'}</th>
					<td class="nowrap input-group">
						<span class="switch prestashop-switch fixed-width-lg">
							<input type="radio" name="track" id="track0" value="1" {if $track eq '1'} checked {/if}>
							<label for="track0">{l s='Yes' mod='smartmarketingps'}</label>
							<input type="radio" name="track" id="track1" value="0" {if $track eq '0' or $track eq ''} checked {/if}>
							<label for="track1">{l s='No' mod='smartmarketingps'}</label>
							<a class="slide-button btn"></a>
						</span>
						<p class="help">{l s='With Connected Sites active, you will be able to activate Track&Engage as well as insert forms and pop-ups.' mod='smartmarketingps'}</p>
					</td>
				</tr>
			{/if}

















			<tr>
				<td colspan="2" style="text-align: right;">
					<input type="hidden" name="token" value="{$token|escape:'htmlall':'UTF-8'}">
					<input type='submit' name='action_add' id='action_add' value="1" style="display: none;">
				</td>
			</tr>
		</table>
	</form>

	<!-- Custom Fields -->
	<div class="modal fade" id="CustomFields" role="dialog">
	    <div class="modal-dialog">
	        <div class="modal-content">
                <div class="modal-header egoi-map-header">
                    <h1 class="modal-title egoi-title"><span class="icon-exchange" id="egoi-map-icon"></span> {l s='Map Custom Fields' mod='smartmarketingps'}</h1>
                </div>
                <div class="modal-body">
                    <div class="row" style="padding-bottom: 20px;">
                        <div class="col-md-5 col-sm-5">
                            <label style="display: block;">{l s='PrestaShop Fields' mod='smartmarketingps'}</label>
                            <select name="ps_fields" id="ps_fields" class="form-control" style="width:180px;">
                                <option value="">{l s='Select Field' mod='smartmarketingps'}</option>
                                <option value="firstname">{l s='First Name' mod='smartmarketingps'}</option>
                                <option value="lastname">{l s='Last Name' mod='smartmarketingps'}</option>
                                <option value="birthday">{l s='Birthday' mod='smartmarketingps'}</option>
                                <option value="newsletter">{l s='Newsletter' mod='smartmarketingps'}</option>
                                <option value="optin">{l s='Optin' mod='smartmarketingps'}</option>
                                <option value="roles">{l s='Roles' mod='smartmarketingps'}</option>
								<option value="store_name">{l s='Store Name' mod='smartmarketingps'}</option>
								<option value="store_language">{l s='Store Language' mod='smartmarketingps'}</option>
                            </select>
                        </div>

                        <div class="col-md-5 col-sm-5">
                            <label style="display: block;">E-goi Fields</label>
                            <select name="egoi" id="egoi" style="width:180px;display:inline;">
                                <option value="">{l s='Select Field' mod='smartmarketingps'}</option>
                                {if isset($select) and ($select)}
                                    {$select}
                                {/if}
                            </select>
                        </div>
                        <div class="col-md-2 col-sm-2" style="padding-top: 20px;">
                            <button class="btn btn-primary" type="button" id="save_map_fields" disabled style="text-transform: none;">
                                <span class="icon-plus"></span> {l s='Assign' mod='smartmarketingps'}
                                <div id="load_map" style="display:none;"></div>
                            </button>
                        </div>
                    </div>
                    <hr style="margin:0;">

                    <div class="row" id="all_fields_mapped" style="padding-top: 15px;">
                        <div class="col-md-12 col-sm-12" style="border-bottom: 1px solid #e5e5e5;">
                            <b>{l s='Mapped Fields' mod='smartmarketingps'}</b>
                        </div>
                        {if isset($mapped_fields) and (!empty($mapped_fields))}
                            {foreach $mapped_fields as $row}
                                <div id="egoi_fields_{$row['id']}">
                                    <div class="col-sm-5" style='font-size: 14px;padding-top: 10px;'>{$row['ps_name']|escape:'htmlall':'UTF-8'}</div>
                                    <div class="col-sm-5" style='font-size: 14px;padding-top: 10px;'>{$row['egoi_name']|escape:'htmlall':'UTF-8'}</div>
                                    <div class="col-sm-2" style="padding-top: 10px;">
                                        <button type='button' id="field_{$row['id']|escape:'htmlall':'UTF-8'}" class='egoi_fields btn btn-default' data-target="{$row['id']|escape:'htmlall':'UTF-8'}">
                                        <span class="icon-trash"></span></button>
                                    </div>
                                </div>
                            {/foreach}
                        {/if}
                    </div>
                    <div id="error_map" class="alert alert-danger col-md-8 col-md-offset-2" style="display:none;">
                    	{l s='The selected fields are already mapped!' mod='smartmarketingps'}
                    </div>
                </div>

	            <div class="modal-footer">
	        	    <button type="button" class="btn btn-default" id="close_fields" data-dismiss="modal">Close</button>
	            </div>
	        </div>
	    </div>
	</div>
