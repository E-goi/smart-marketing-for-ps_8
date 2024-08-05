<div class="panel">
    <div class="egoi panel-heading"><span class="icon-folder" id="subs"></span>
        <span class="baseline">{l s='Create Catalog' mod='smartmarketingps'}</span>
    </div>

    <form id="egoi-catalog-form" method="post">
        <table class="table" id="egoi-subs-table">
            <tr valign="top">
                <th class="egoi-td" scope="row">{l s='Name' mod='smartmarketingps'}</th>
                <td>
                    <input type="text" id="egoi-catalog-name" name="egoi-catalog-name"
                           placeholder="{l s='Catalog name...' mod='smartmarketingps'}">
                    <p>{l s='Insert the name of your catalog.' mod='smartmarketingps'}</p>
                </td>
            </tr>

            <tr valign="top">
                <th class="egoi-td" scope="row">{l s='Language' mod='smartmarketingps'}</th>
                <td>
                    <select id="egoi-catalog-language" class="form-control" name="egoi-catalog-language">
                        {html_options values=$languages output=$languages selected=$defaultLanguage}
                    </select>
                    <p>{l s='Select the language of your catalog.' mod='smartmarketingps'}</p>
                </td>
            </tr>

            <tr valign="top">
                <th class="egoi-td" scope="row">{l s='Currency' mod='smartmarketingps'}</th>
                <td>
                    <select id="egoi-catalog-currency" class="form-control" name="egoi-catalog-currency">
                        {html_options values=$currencies output=$currencies selected=$defaultCurrency}
                    </select>
                    <p>{l s='Select the currency of your catalog.' mod='smartmarketingps'}</p>
                </td>
            </tr>

            <tr>
                <th class="egoi-td" scope="row">{l s='Enable product descriptions synchronization' mod='smartmarketingps'}</th>
                <td class="nowrap input-group">
						<span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="descriptionsSync" id="descriptionsSync0" value="1" checked>
							<label for="catalog-sync0">{l s='Yes' mod='smartmarketingps'}</label>
							<input type="radio" name="descriptionsSync" id="descriptionsSync1" value="0">
							<label for="catalog-sync1">{l s='No' mod='smartmarketingps'}</label>
							<a class="slide-button btn"></a>
						</span>
                    <p class="help">{l s='Select "yes" if you want to activate synchronization of porducts descriptions for this catalog' mod='smartmarketingps'}</p>
                </td>
            </tr>

            <tr>
                <th class="egoi-td" scope="row">{l s='Enable product categories synchronization' mod='smartmarketingps'}</th>
                <td class="nowrap input-group">
						<span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="categoriesSync" id="categoriesSync0" value="1" checked>
							<label for="catalog-sync0">{l s='Yes' mod='smartmarketingps'}</label>
							<input type="radio" name="categoriesSync" id="categoriesSync1" value="0">
							<label for="catalog-sync1">{l s='No' mod='smartmarketingps'}</label>
							<a class="slide-button btn"></a>
						</span>
                    <p class="help">{l s='Select "yes" if you want to activate synchronization of porducts categories for this catalog' mod='smartmarketingps'}</p>
                </td>
            </tr>

            <tr>
                <th class="egoi-td" scope="row">{l s='Enable related products synchronization' mod='smartmarketingps'}</th>
                <td class="nowrap input-group">
						<span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="relatedSync" id="relatedSync0" value="1" checked>
							<label for="catalog-sync0">{l s='Yes' mod='smartmarketingps'}</label>
							<input type="radio" name="relatedSync" id="relatedSync1" value="0">
							<label for="catalog-sync1">{l s='No' mod='smartmarketingps'}</label>
							<a class="slide-button btn"></a>
						</span>
                    <p class="help">{l s='Select "yes" if you want to activate synchronization of related porducts for this catalog' mod='smartmarketingps'}</p>
                </td>
            </tr>

            <tr>
                <th class="egoi-td" scope="row">{l s='Synchronization Active' mod='smartmarketingps'}</th>
                <td class="nowrap input-group">
						<span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" name="catalogSync" id="catalog-sync0" value="1" checked>
							<label for="catalog-sync0">{l s='Yes' mod='smartmarketingps'}</label>
							<input type="radio" name="catalogSync" id="catalog-sync1" value="0">
							<label for="catalog-sync1">{l s='No' mod='smartmarketingps'}</label>
							<a class="slide-button btn"></a>
						</span>
                    <p class="help">{l s='Select "yes" if you want to activate automatic synchronization of porducts for this catalog' mod='smartmarketingps'}</p>
                </td>
            </tr>
        </table>
        <div class="mt20 align-right">
            <a href="{$smarty.server.REQUEST_URI|replace:'&createCatalog=1':''|regex_replace:"#deleteCatalog=[0-9]+#":''}"
               class="btn btn-default btn-lg uppercase">{l s='Cancel' mod='smartmarketingps'}</a>
            <input type="hidden" name="token" value="{$token|escape:'htmlall':'UTF-8'}">
            <input type="submit" id="create-catalog" name="create-catalog"
                   value="{l s='Create Catalog' mod='smartmarketingps'}" class="btn btn-primary btn-lg">
        </div>
    </form>
</div>
