function pulsar(e, valor) {
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 13) comprueba(valor)
}

$(document).ready(function() {

    $('#AgregaPedido').click(function() {
        AgregaPedidos();
    });

    $('.agregapedido').keypress(function(e) {
        var keycode = (e.keyCode ? e.keyCode : e.which);
        if (keycode == '13') {
          AgregaPedidos();
          e.preventDefault();
          return false;
      }
  });

    function AgregaPedidos () {        

        var code = $('input#codproducto').val();
        var prod = $('input#producto').val();
        var marc = $('input#marcas').val();
        var cantp = $('input#cantidad').val();
        var tip = $('input#codmarca').val();
        var er_num = /^([0-9])*[.]?[0-9]*$/;
        cantp = parseInt(cantp);
            //exist = parseInt(exist);
            cantp = cantp;

            if (code == "") {
                $("#codproducto").focus();
                $("#codproducto").css('border-color', '#ff7676');
                swal("Oops", "POR FAVOR INGRESE EL CÓDIGO DE PRODUCTO!", "error");
                return false;

            } else if (prod == "") {
                $("#producto").focus();
                $("#producto").css('border-color', '#ff7676');
                swal("Oops", "POR FAVOR INGRESE NOMBRE O DESCRIPCIÓN DE PRODUCTOS!", "error");
                return false;

            } else if (marc == "") {
                $("#marcas").focus();
                $("#marcas").css('border-color', '#ff7676');
                $("#marcas").val("");
                swal("Oops", "POR FAVOR REALICE LA BÚSQUEDA DE MARCA DE PRODUCTO CORRECTAMENTE!", "error");
                return false;

            } else if (tip == "") {
                $("#marcas").focus();
                $("#marcas").css('border-color', '#ff7676');
                $("#marcas").val("");
                swal("Oops", "POR FAVOR REALICE LA BÚSQUEDA DE MARCA DE PRODUCTO CORRECTAMENTE!", "error");
                return false;

            } else if ($('#cantidad').val() == "" || $('#cantidad').val() == "0") {
                $("#cantidad").focus();
                $("#cantidad").css('border-color', '#ff7676');
                swal("Oops", "POR FAVOR INGRESE UNA CANTIDAD VALIDA PARA PEDIDOS!", "error");
                return false;

            } else if (isNaN($('#cantidad').val())) {
                $("#cantidad").focus();
                $("#cantidad").css('border-color', '#ff7676');
                swal("Oops", "POR FAVOR INGRESE SOLO DIGITOS EN CANTIDAD PARA PEDIDOS!", "error");
                return false;

            } else {

                var Carrito = new Object();
                Carrito.Codigo = $('input#codproducto').val();
                Carrito.Producto = $('input#producto').val();
                Carrito.Marcas = $('input#marcas').val();
                Carrito.Codmarca = $('input#codmarca').val();
                Carrito.Modelo = $('input#modelos').val();
                Carrito.Codmodelo = $('input#codmodelo').val();
                Carrito.Cantidad = $('input#cantidad').val();
                Carrito.opCantidad = '+=';
                var DatosJson = JSON.stringify(Carrito);
                $.post('carritopedido.php', {
                    MiCarrito: DatosJson
                },
                function(data, textStatus) {
                    $("#carrito tbody").html("");
                    var SubtotalFact = 0;
                    var BaseImpIva1 = 0;
                    var contador = 0;
                    var iva = 0;
                    var total = 0;
                    var TotalCompra = 0;

                    $.each(data, function(i, item) {
                        var cantsincero = item.cantidad;
                        cantsincero = parseInt(cantsincero);
                        if (cantsincero != 0) {
                            contador = contador + 1;

            var nuevaFila =
            "<tr align='center'>" +
            "<td>" +
            '<button class="btn btn-info btn-xs" style="cursor:pointer;border-radius:5px 5px 5px 5px;" onclick="addItem(' +
            "'" + item.txtCodigo + "'," +
            "'-1'," +
            "'" + item.producto + "'," +
            "'" + item.marcas + "'," +
            "'" + item.codmarca + "', " +
            "'" + item.modelo + "', " +
            "'" + item.codmodelo + "', " +
            "'-'" +
            ')"' +
            " type='button'><span class='fa fa-minus'></span></button>" +
            "<input type='text' id='" + item.cantidad + "' style='width:25px;height:24px;border:#ff7676;' value='" + item.cantidad + "'>" +
            '<button class="btn btn-info btn-xs" style="cursor:pointer;border-radius:5px 5px 5px 5px;" onclick="addItem(' +
            "'" + item.txtCodigo + "'," +
            "'+1'," +
            "'" + item.producto + "'," +
            "'" + item.marcas + "'," +
            "'" + item.codmarca + "', " +
            "'" + item.modelo + "', " +
            "'" + item.codmodelo + "', " +
            "'+'" +
            ')"' +
            " type='button'><span class='fa fa-plus'></span></button></td>" +
            "<td>" + item.txtCodigo + "<input type='hidden' value='" + item.codmarca + "'></td>" +
            "<td><h5>" + item.producto + "</h5></td>" +
            "<td>" + item.marcas + "</td>" +
            "<td>" + item.modelo + "</td>" +
            "<td>" +
            '<button class="btn btn-dark btn-xs" style="cursor:pointer;border-radius:5px 5px 5px 5px;color:#fff;" ' +
            'onclick="addItem(' +
            "'" + item.txtCodigo + "'," +
            "'0'," +
            "'" + item.producto + "'," +
            "'" + item.marcas + "'," +
            "'" + item.codmarca + "', " +
            "'" + item.modelo + "', " +
            "'" + item.codmodelo + "', " +
            "'='" +
            ')"' +
            ' type="button"><span class="fa fa-trash-o"></span></button>' +
            "</td>" +
            "</tr>";
            $(nuevaFila).appendTo("#carrito tbody");

                        }

                    });

                    $("#busquedaproductoc").focus();
                    LimpiarTexto();
                },
                "json"
                );
                return false;
            }
        }

        $("#vaciarp").click(function() {
            var Carrito = new Object();
            Carrito.Codigo = "vaciar";
            Carrito.Producto = "vaciar";
            Carrito.Marcas = "vaciar";
            Carrito.Codmarca = "vaciar";
            Carrito.Modelo = "vaciar";
            Carrito.Codmodelo = "vaciar";
            Carrito.Cantidad = "0";
            var DatosJson = JSON.stringify(Carrito);
            $.post('carritopedido.php', {
                MiCarrito: DatosJson
            },
            function(data, textStatus) {
                $("#carrito tbody").html("");
                var nuevaFila =
                "<tr>"+"<td class='text-center' colspan=6><h4>NO HAY DETALLES AGREGADOS</h4></td>"+"</tr>";
                $(nuevaFila).appendTo("#carrito tbody");
                LimpiarTexto();
            },
            "json"
            );
            return false;
        });

        $(document).ready(function() {
            $('#vaciarp').click(function() {
                $("#carrito tbody").html("");
                var nuevaFila =
                "<tr>"+"<td class='text-center' colspan=6><h4>NO HAY DETALLES AGREGADOS</h4></td>"+"</tr>";
                $(nuevaFila).appendTo("#carrito tbody");
                $("#savepedidos")[0].reset();
            });
        });

        $("#carrito tbody").on('keydown', 'input', function(e) {
            var element = $(this);
            var pvalue = element.val();
            var code = e.charCode || e.keyCode;
            var avalue = String.fromCharCode(code);
            var action = element.siblings('button').first().attr('onclick');
            var params;
            if (code !== 6 && /[^\d]/ig.test(avalue)) {
                e.preventDefault();
                return;
            }
            if (element.attr('data-proc') == '1') {
                return true;
            }
            element.attr('data-proc', '1');
            params = action.match(/\'([^\']+)\'/g).map(function(v) {
                return v.replace(/\'/g, '');
            });
            setTimeout(function() {
                if (element.attr('data-proc') == '1') {
                    var value = element.val() || 0;
                    addItem(
                        params[0],
                        value,
                        params[2],
                        params[3],
                        params[4],
                        params[5],
                        params[6],
                        '='
                        );
                    element.attr('data-proc', '0');
                }
            }, 500);
        });
    });

function LimpiarTexto() {
    $("#busquedaproductoc").val("");
    $("#codproducto").val("");
    $("#producto").val("");
    $("#marcas").val("");
    $("#codmarca").val("");
    $("#modelos").val("");
    $("#codmodelo").val("");
    $("#cantidad").val("");
}

function addItem(codigo, cantidad, producto, marcas, codmarca, modelo, codmodelo, opCantidad) {
    var Carrito = new Object();
    Carrito.Codigo = codigo;
    Carrito.Producto = producto;
    Carrito.Marcas = marcas;
    Carrito.Codmarca = codmarca;
    Carrito.Modelo = modelo;
    Carrito.Codmodelo = codmodelo;
    Carrito.Cantidad = cantidad;
    Carrito.opCantidad = opCantidad;
    var DatosJson = JSON.stringify(Carrito);
    $.post('carritopedido.php', {
        MiCarrito: DatosJson
    },
    function(data, textStatus) {
        $("#carrito tbody").html("");
        var SubtotalFact = 0;
        var BaseImpIva1 = 0;
        var contador = 0;
        var iva = 0;
        var total = 0;
        var TotalCompra = 0;

        $.each(data, function(i, item) {
            var cantsincero = item.cantidad;
            cantsincero = parseInt(cantsincero);
            if (cantsincero != 0) {
                contador = contador + 1;


        var nuevaFila =
        "<tr align='center'>" +
        "<td>" +
        '<button class="btn btn-info btn-xs" style="cursor:pointer;border-radius:5px 5px 5px 5px;" onclick="addItem(' +
        "'" + item.txtCodigo + "'," +
        "'-1'," +
        "'" + item.producto + "'," +
        "'" + item.marcas + "'," +
        "'" + item.codmarca + "', " +
        "'" + item.modelo + "', " +
        "'" + item.codmodelo + "', " +
        "'-'" +
        ')"' +
        " type='button'><span class='fa fa-minus'></span></button>" +
        "<input type='text' id='" + item.cantidad + "' style='width:25px;height:24px;border:#ff7676;' value='" + item.cantidad + "'>" +
        '<button class="btn btn-info btn-xs" style="cursor:pointer;border-radius:5px 5px 5px 5px;" onclick="addItem(' +
        "'" + item.txtCodigo + "'," +
        "'+1'," +
        "'" + item.producto + "'," +
        "'" + item.marcas + "'," +
        "'" + item.codmarca + "', " +
        "'" + item.modelo + "', " +
        "'" + item.codmodelo + "', " +
        "'+'" +
        ')"' +
        " type='button'><span class='fa fa-plus'></span></button></td>" +
        "<td>" + item.txtCodigo + "<input type='hidden' value='" + item.codmarca + "'></td>" +
        "<td><h5>" + item.producto + "</h5></td>" +
        "<td>" + item.marcas + "</td>" +
        "<td>" + item.modelo + "</td>" +
        "<td>" +
        '<button class="btn btn-dark btn-xs" style="cursor:pointer;border-radius:5px 5px 5px 5px;color:#fff;" ' +
        'onclick="addItem(' +
        "'" + item.txtCodigo + "'," +
        "'0'," +
        "'" + item.producto + "'," +
        "'" + item.marcas + "'," +
        "'" + item.codmarca + "', " +
        "'" + item.modelo + "', " +
        "'" + item.codmodelo + "', " +
        "'='" +
        ')"' +
        ' type="button"><span class="fa fa-trash-o"></span></button>' +
        "</td>" +
        "</tr>";
        $(nuevaFila).appendTo("#carrito tbody");

            }
        });
        if (contador == 0) {

            $("#carrito tbody").html("");

            var nuevaFila =
            "<tr>"+"<td class='text-center' colspan=6><h4>NO HAY DETALLES AGREGADOS</h4></td>"+"</tr>";
            $(nuevaFila).appendTo("#carrito tbody");

                //alert("ELIMINAMOS TODOS LOS SUBTOTAL Y TOTALES");
                $("#savepedidos")[0].reset();

            }
            LimpiarTexto();
        },
        "json"
        );
    return false;
}