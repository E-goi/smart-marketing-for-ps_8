<script>
    function confirmDeleteCatalog(id) {
        $('#confirm-delete').attr('value', id);
        $('#confirm_modal').modal('show');
    }

    var lastPage;

    function syncCatalog(id, language, currency) {
        var catalogSync = $("#catalog_" + id).children();
        $(catalogSync[0]).parent('a').addClass('not-clickable');
        $(catalogSync[1]).show();
        $(catalogSync[0]).hide();


        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var jsonResponse = JSON.parse(xhr.responseText);
                lastPage = jsonResponse.lastPage;
                syncBatch(1, id, language, currency);
            }
        };

        xhr.open('GET', "{$smarty.server.REQUEST_URI}&countProducts=1", true);
        xhr.send();
    }

    function syncBatch(page, id, language, currency) {
        if (page > lastPage) {
            var catalogSync = $("#catalog_" + id).children();
            $(catalogSync[0]).parent('a').removeClass('not-clickable');
            $(catalogSync[1]).hide();
            $(catalogSync[0]).show();
            var msg = '{l s='Products were successfully imported to E-goi' mod='smartmarketingps'}';
            $.growl.notice({ title: '', message: msg });
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                syncBatch(++page, id, language, currency);
            }
        };

        var url = "{$smarty.server.REQUEST_URI}&syncCatalog=" + id + '&language=' + language + '&currency=' + currency + '&page=' + page;
        xhr.open('GET', url, true);
        xhr.send();
    }

    function toggleSync(id, value) {
        window.location.replace("{$smarty.server.REQUEST_URI}&toggleSync=" + id + "&value=" + value);
    }

    function toggleDescriptions(id, value) {
        window.location.replace("{$smarty.server.REQUEST_URI}&toggleDescriptions=" + id + "&value=" + value);
    }

    function toggleCategories(id, value) {
        window.location.replace("{$smarty.server.REQUEST_URI}&toggleCategories=" + id + "&value=" + value);
    }

    function toggleRelated(id, value) {
        window.location.replace("{$smarty.server.REQUEST_URI}&toggleRelated=" + id + "&value=" + value);
    }

    $(document).ready(function () {
        $('#confirm-delete').click(function () {
            window.location.replace("{$smarty.server.REQUEST_URI}&deleteCatalog=" + $('#confirm-delete').attr('value'));
            $('#confirm_modal').modal('hide');
        });
    });
</script>

<div class="modal fade delete-catalog-modal" id="confirm_modal" tabindex="-1">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{l s='Delete catalog?' mod='smartmarketingps'}</h4>
            </div>

            <div class="modal-body">
                {l s='This catalog and its content will be deleted in your E-goi account. Please confirm!' mod='smartmarketingps'}
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-lg uppercase"
                        data-dismiss="modal">{l s='Close' mod='smartmarketingps'}
                </button>
                <button type="button" id="confirm-delete" class="btn btn-primary btn-lg">
                    {l s='Confirm' mod='smartmarketingps'}
                </button>
            </div>
        </div>
    </div>
</div>

{if !empty($importCategories)}
    <div class="panel">
        <div class="egoi panel-heading panel-flex">
            <span>{l s='Update Categories' mod='smartmarketingps'}</span>
            <span class="pr10">
                <button type="button" class="close close-panel-btn" aria-label="Close" onclick='window.location.replace("{$smarty.server.REQUEST_URI}&ignoreCategories=1");'>
                    <span aria-hidden="true">&times;</span>
                </button>
            </span>
        </div>
        <p>
            {l s='A category was updated. Please synchronize your products to E-goi if you need to reflect these changes.' mod='smartmarketingps'}
        </p>
        <div class="mt20">
            <a href="{$smarty.server.REQUEST_URI}&syncAllCatalogs=1"
               class="btn btn-primary btn-lg uppercase">{l s='Sync All Catalogs' mod='smartmarketingps'}
            </a>
        </div>
    </div>
{/if}

<div class="panel">
    <div class="egoi panel-heading">
        <span class="baseline">{l s='Catalogs' mod='smartmarketingps'}</span>
    </div>
    <table class="grid-table js-grid-table table" id="language_grid_table">
        <thead class="thead-default">
        <tr class="column-headers ">
            <th>
                <div class="text-center">
                    <b>{l s='ID' mod='smartmarketingps'}</b>
                </div>
            </th>

            <th>
                <div class="text-center">
                    <b>{l s='Name' mod='smartmarketingps'}</b>
                </div>
            </th>

            <th>
                <div class="text-center">
                    <b>{l s='Language' mod='smartmarketingps'}</b>
                </div>
            </th>

            <th>
                <div class="text-center">
                    <b>{l s='Currency' mod='smartmarketingps'}</b>
                </div>
            </th>

            <th>
                <div class="text-center">
                    <b>{l s='Sync Descriptions' mod='smartmarketingps'}</b>
                </div>
            </th>

            <th>
                <div class="text-center">
                    <b>{l s='Sync Categories' mod='smartmarketingps'}</b>
                </div>
            </th>

            <th>
                <div class="text-center">
                    <b>{l s='Sync Related Products' mod='smartmarketingps'}</b>
                </div>
            </th>


            <th>
                <div class="text-center">
                    <b>{l s='Automatically Sync Enabled' mod='smartmarketingps'}</b>
                </div>
            </th>

            <th>
                <div class="grid-actions-header-text text-center">
                    <b>{l s='Actions' mod='smartmarketingps'}</b>
                </div>
            </th>
        </tr>

        </thead>
        <tbody>
        {if $catalogs}
            {foreach $catalogs as $catalog}
                <tr>
                    <td class="data-type text-center">
                        {l s=$catalog.catalog_id mod='smartmarketingps'}
                    </td>
                    <td class="link-type text-center egoi-catalog-name-listing">
                        {l s=$catalog.title mod='smartmarketingps'}
                    </td>
                    <td class="data-type text-center">
                        {l s=$catalog.language mod='smartmarketingps'}
                    </td>
                    <td class="data-type text-center">
                        {l s=$catalog.currency mod='smartmarketingps'}
                    </td>

                    <td class="text-center">
                        {if isset($catalog.sync_descriptions) && $catalog.sync_descriptions == 1}
                            <span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Disable Descriptions Sync' mod='smartmarketingps'}">
                                <a class="btn tooltip-link js-submit-row-action dropdown-item"
                                   onclick="toggleDescriptions({$catalog.catalog_id}, 1)">
                                    <i class="material-icons action-enabled egoi-enabled">
                                        check
                                    </i>
                                </a>
                            </span>
                        {else}
                            <span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Enable Descriptions Sync' mod='smartmarketingps'}">
                                <a class="btn tooltip-link js-submit-row-action dropdown-item"
                                   onclick="toggleDescriptions({$catalog.catalog_id}, 0)">
                                    <i class="material-icons action-disabled egoi-disabled">
                                        clear
                                    </i>
                                </a>
                            </span>
                        {/if}
                    </td>

                    <td class="text-center">
                        {if isset($catalog.sync_categories) && $catalog.sync_categories == 1}
                            <span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Disable Categories Sync' mod='smartmarketingps'}">
                                <a class="btn tooltip-link js-submit-row-action dropdown-item"
                                   onclick="toggleCategories({$catalog.catalog_id}, 1)">
                                    <i class="material-icons action-enabled egoi-enabled">
                                        check
                                    </i>
                                </a>
                            </span>
                        {else}
                            <span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Enable Categories Sync' mod='smartmarketingps'}">
                                <a class="btn tooltip-link js-submit-row-action dropdown-item"
                                   onclick="toggleCategories({$catalog.catalog_id}, 0)">
                                    <i class="material-icons action-disabled egoi-disabled">
                                        clear
                                    </i>
                                </a>
                            </span>
                        {/if}
                    </td>

                    <td class="text-center">
                        {if isset($catalog.sync_related_products) && $catalog.sync_related_products == 1}
                            <span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Disable Related Products Sync' mod='smartmarketingps'}">
                                <a class="btn tooltip-link js-submit-row-action dropdown-item"
                                   onclick="toggleRelated({$catalog.catalog_id}, 1)">
                                    <i class="material-icons action-enabled egoi-enabled">
                                        check
                                    </i>
                                </a>
                            </span>
                        {else}
                            <span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Enable Related Products Sync' mod='smartmarketingps'}">
                                <a class="btn tooltip-link js-submit-row-action dropdown-item"
                                   onclick="toggleRelated({$catalog.catalog_id}, 0)">
                                    <i class="material-icons action-disabled egoi-disabled">
                                        clear
                                    </i>
                                </a>
                            </span>
                        {/if}
                    </td>


                    <td class="text-center">
                        {if isset($catalog.active) && $catalog.active == 1}
                            <span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Turn auto-sync off' mod='smartmarketingps'}">
                                <a class="btn tooltip-link js-submit-row-action dropdown-item"
                                   onclick="toggleSync({$catalog.catalog_id}, 1)">
                                    <i class="material-icons action-enabled egoi-enabled">
                                        check
                                    </i>
                                </a>
                            </span>
                        {else}
                            <span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Turn auto-sync on' mod='smartmarketingps'}">
                                <a class="btn tooltip-link js-submit-row-action dropdown-item"
                                   onclick="toggleSync({$catalog.catalog_id}, 0)">
                                    <i class="material-icons action-disabled egoi-disabled">
                                        clear
                                    </i>
                                </a>
                            </span>
                        {/if}
                    </td>
                    <td class="action-type text-center">
                        <span id="sync-catalog{$catalog.catalog_id}" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Sync now' mod='smartmarketingps'}">
                            <a class="btn tooltip-link js-submit-row-action dropdown-item"
                               onclick="syncCatalog({$catalog.catalog_id}, '{$catalog.language}', '{$catalog.currency}')" id="catalog_{$catalog.catalog_id}">
                                <i class="material-icons">refresh</i>
                                <div class="loader" style="display: none;"></div>
                            </a>
                        </span>
                        <span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Delete catalog' mod='smartmarketingps'}">
                            <a class="btn tooltip-link js-submit-row-action dropdown-item"
                               onclick="confirmDeleteCatalog({$catalog.catalog_id})">
                                <i class="material-icons">delete</i>
                            </a>
                        </span>
                    </td>
                </tr>
            {/foreach}
        {else}
            <tr>
                <td class="list-empty" colspan="8">
                    <div class="list-empty-msg">
                        <i class="icon-warning-sign list-empty-icon"></i>
                        {l s='No records found' mod='smartmarketingps'}
                    </div>
                </td>
            </tr>
        {/if}
        </tbody>
    </table>

    <div class="mt20 align-right">
        <a href="{$smarty.server.REQUEST_URI}&createCatalog=1"
           class="btn btn-primary btn-lg uppercase">{l s='Add Catalog' mod='smartmarketingps'}
        </a>
    </div>
</div>
