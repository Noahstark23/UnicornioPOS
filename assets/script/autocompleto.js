// FUNCION AUTOCOMPLETE 
$(function() {  
    var animales = ["Ardilla roja", "Gato", "Gorila occidental",  
      "Leon", "Oso pardo", "Perro", "Tigre de Bengala"];  
      
    $("#prueba").autocomplete({  
      source: animales  
    });  
}); 

$(function() {
    $("#busquedakardex").autocomplete({
        source: "class/buscakardex.php",
        minLength: 1,
        select: function(event, ui) {
            $('#codproducto').val(ui.item.codproducto);
        }
    });
});

$(function() {
    $("#busquedaproductoc").autocomplete({
        source: "class/buscaproductoc.php",
        minLength: 1,
        select: function(event, ui) {
            $('#codproducto').val(ui.item.codproducto);
            $('#producto').val(ui.item.producto);
            $('#fabricante').val(ui.item.fabricante);
            $('#codfamilia').val(ui.item.codfamilia);
            $('#codsubfamilia').val(ui.item.codsubfamilia);
            $('#codmarca').val(ui.item.codmarca);
            $('#marcas').val(ui.item.nommarca);
            $('#codmodelo').val(ui.item.codmodelo);
            $('#modelos').val((ui.item.nommodelo == "") ? "*****" : ui.item.nommodelo);
            $('#codpresentacion').val(ui.item.codpresentacion);
            $('#codorigen').val(ui.item.codorigen);
            $('#preciocompra').val(ui.item.preciocompra);
            $('#precioxmenor').val(ui.item.precioxmenor);
            $('#precioxmayor').val(ui.item.precioxmayor);
            $('#precioxpublico').val(ui.item.precioxpublico);
            $('#precioconiva').val((ui.item.ivaproducto == "SI") ? ui.item.preciocompra : "0.00");
            $('#existencia').val(ui.item.existencia);
            $('#ivaproducto').val(ui.item.ivaproducto);
            $('#descproducto').val(ui.item.descproducto);
            $("#cantidad").focus();
        }
    });
});

$(function() {
    $("#busquedaproductov").autocomplete({
        source: "class/buscaproductov.php",
        minLength: 1,
        select: function(event, ui) {
            $('#codproducto').val(ui.item.codproducto);
            $('#producto').val(ui.item.producto);
            $('#fabricante').val(ui.item.fabricante);
            $('#codfamilia').val(ui.item.codfamilia);
            $('#codsubfamilia').val(ui.item.codsubfamilia);
            $('#codmarca').val(ui.item.codmarca);
            $('#marcas').val(ui.item.nommarca);
            $('#codmodelo').val(ui.item.codmodelo);
            $('#modelos').val((ui.item.nommodelo == "") ? "*****" : ui.item.nommodelo);
            $('#codpresentacion').val(ui.item.codpresentacion);
            $('#codorigen').val(ui.item.codorigen);
            $('#preciocompra').val(ui.item.preciocompra);
            $('#precioxmenor').val(ui.item.precioxmenor);
            $('#precioxmayor').val(ui.item.precioxmayor);
            $('#precioxpublico').val(ui.item.precioxpublico);
            $('#existencia').val(ui.item.existencia);
            $('#ivaproducto').val(ui.item.ivaproducto);
            $('#descproducto').val(ui.item.descproducto);
            $("#cantidad").focus();
            $('#precioventa').load("funciones.php?BuscaPreciosProductos=si&codproducto="+ui.item.codproducto);

        }
    });
});


$(function() {
           $("#marcas").autocomplete({
           source: "class/buscamarca.php",
           minLength: 1,
           select: function(event, ui) { 
           $('#codmarca').val(ui.item.codmarca);
           }  
        });
 });

$(function() {
           $("#modelos").autocomplete({
           source: "class/buscamodelo.php",
           minLength: 1,
           select: function(event, ui) { 
           $('#codmodelo').val(ui.item.codmodelo);
           $("#cantidad").focus();
           }  
        });
 });


$(function() {
           $("#busqueda").autocomplete({
           source: "class/buscacliente.php",
           minLength: 1,
           select: function(event, ui) { 
          $('#codcliente').val(ui.item.codcliente);
          $('#cliente').val(ui.item.codcliente);
          $('#creditoinicial').val(ui.item.limitecredito);
          $('#montocredito').val(ui.item.creditodisponible);
          $('#creditodisponible').val(ui.item.creditodisponible);
          $('#TextCliente').text(ui.item.nomcliente);
          $('#TextCredito').text(ui.item.creditodisponible);
           }  
      });
 });