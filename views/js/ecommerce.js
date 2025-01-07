/**
 * Smart Marketing
 *
 *  @author    E-goi
 *  @copyright 2018 E-goi
 *  @license   LICENSE.txt
 */

jQuery.fn.show = function() {
    return this.prop('style', 'display:inline-block');
};

$(document).ready(function () {
    var totalPages = 0;
    var currentPage = 0;
    var pagesStores = [];
    var btn_sync_orders = $("#sync_old_orders");


    function interactionOrders(store, page) {
        var token_list = '1';

        $.ajax({
            type: 'POST',
            data: {
                token_list: token_list,
                orders: page,
                store_id: store
            },
            success: function (data, status) {
                var json = JSON.parse(data);

                if (json.hasOwnProperty("error")) {
                    btn_sync_orders.prop('disabled', false);
                    $('.sync_orders').hide();
                    $('#sync_success').hide();
                    $('#progressbarSync').hide();
                    totalPages = 0;
                    currentPage = 0;
                    pagesStores = [];

                    $('#sync_noorders').show();
                    window.setTimeout(function () {
                        $("#sync_noorders").fadeOut(400);
                    }, 6000);
                    return;
                }

                // Atualiza o progresso
                currentPage++;
                $('#progressbarValues').attr("aria-valuenow", currentPage);
                $('#progressbarValues').width(Math.ceil((currentPage * 100) / totalPages) + "%");
                $('#progressbarValues').text(currentPage + "/" + totalPages);

                // Verifica se ainda há páginas para processar na loja atual
                if (json.has_more) {
                    interactionOrders(store, json.imported);
                } else {
                    // Passa para a próxima loja
                    var next = getNextStore(store);
                    if (next) {
                        interactionOrders(next, 0);
                    } else {
                        // Finaliza a sincronização
                        setTimeout(function () {
                            $('#progressbarSync').hide();
                            $('#progressbarValues').width("0%");
                            btn_sync_orders.prop('disabled', false);
                            $('.sync_orders').hide();
                            $('#sync_success').show();
                            window.setTimeout(function () {
                                $("#sync_success").fadeOut(400);
                            }, 6000);
                            totalPages = 0;
                            currentPage = 0;
                            pagesStores = [];
                        }, 1000);
                    }
                }
            },
            error: function (status) {
                btn_sync_orders.prop('disabled', false);
                $('.sync_orders').hide();
                $('#sync_success').hide();
                $('#progressbarSync').hide();
                totalPages = 0;
                currentPage = 0;
                pagesStores = [];
            }
        });
    }

    btn_sync_orders.on('click', function () {
        $('.sync_orders').show();
        $('#sync_success').hide();
        btn_sync_orders.prop('disabled', true);

        $.ajax({
            type: 'POST',
            data:({
                size: 1,
            }),
            success: function (data, status) {
                if (data !== "" && data !== "No orders!") {
                    var json = JSON.parse(data);
                    json = pagesStores = calcPages(json);

                    $('#progressbarSync').show();
                    $('#progressbarValues').attr("aria-valuemax", totalPages);
                    $('#progressbarValues').width("0%");
                    $('#progressbarValues').text("0/" + totalPages);

                    if (json.length > 0) {
                        interactionOrders(json[0].id_shop, 0);
                    } else {
                        btn_sync_orders.prop('disabled', false);
                        $('.sync_orders').hide();
                        $('#sync_success').hide();
                        $('#progressbarSync').hide();

                        $('#sync_noorders').show();
                        window.setTimeout(function () {
                            $("#sync_noorders").fadeOut(400);
                            btn_sync_orders.prop('disabled', false);
                        }, 6000);

                        return false;
                    }
                } else {
                    btn_sync_orders.prop('disabled', false);
                    $('.sync_orders').hide();
                    $('#sync_success').hide();
                }
            },
            error: function (status) {
                btn_sync_orders.prop('disabled', false);
                $('.sync_orders').hide();
                $('#sync_success').hide();
            }
        });
    });

    function calcPages(arr) {
        var tot = 0;
        for (var i = 0; i < arr.length; i++) {
            arr[i]['total'] = Math.ceil(arr[i]['total'] / 1000); // Ajusta o tamanho da página
            tot += arr[i]['total'];
        }
        totalPages = tot;
        return arr;
    }

    function getNextStore(store) {
        for (var i = 0; i < pagesStores.length; i++) {
            if (pagesStores[i]['id_shop'] === store) {
                i++;
                if (pagesStores.length === i) {
                    return false;
                }
                return pagesStores[i]['id_shop'];
            }
        }
    }
});
