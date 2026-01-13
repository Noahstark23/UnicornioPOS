//FUNCION PARA AGREGAR Y ELIMINAR CAMPOS DINAMICAMENTE

    var cont=1;
    
    //FUNCION AGREGA CAPACIDAD
    function AddCapacidad()  //Esta la funcion que agrega las filas segunda parte :
    {
    cont++;
    //autocompletar
    var indiceFila=1;
    myNewRow = document.getElementById('tabla1').insertRow(-1);
    myNewRow.id=indiceFila;
    myNewCell=myNewRow.insertCell(-1);
    myNewCell.innerHTML='<div class="col-md-12"><div class="form-group has-feedback2"><label class="control-label">Capacidad: <span class="symbol required"></span></label><textarea class="form-control" name="capacidad[]'+cont+'" id="capacidad'+cont+'" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Capacidad para Eje Temático" title="Ingrese Capacidad para Eje Temático" rows="2" autocomplete="off" required="" aria-required="true"></textarea><i class="fa fa-pencil form-control-feedback2"></i></div></div>';
    indiceFila++;
    }

    //FUNCION BORRAR CAPACIDAD
    function BorrarCapacidad() {
        var table = document.getElementById('tabla1');
        if(table.rows.length > 1)
        {
            table.deleteRow(table.rows.length -1);
            cont--;
        }
    }
    
    //FUNCION AGREGA TEMA
    function AddTema()  //Esta la funcion que agrega las filas segunda parte :
    {
    cont++;
    //autocompletar
    var indiceFila=1;
    myNewRow = document.getElementById('tabla2').insertRow(-1);
    myNewRow.id=indiceFila;
    myNewCell=myNewRow.insertCell(-1);
    myNewCell.innerHTML='<div class="col-md-12"><div class="form-group has-feedback2"><label class="control-label">Tema: <span class="symbol required"></span></label><textarea class="form-control" name="tema[]'+cont+'" id="tema'+cont+'" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Tema para Capacidad" title="Ingrese Tema para Capacidad" rows="2" autocomplete="off" required="" aria-required="true"></textarea><i class="fa fa-pencil form-control-feedback2"></i></div></div>';
    indiceFila++;
    }

    //FUNCION BORRAR TEMA
    function BorrarTema() {
        var table = document.getElementById('tabla2');
        if(table.rows.length > 1)
        {
            table.deleteRow(table.rows.length -1);
            cont--;
        }
    }

    //FUNCION AGREGA INDICADOR
    function AddIndicador()  //Esta la funcion que agrega las filas segunda parte :
    {
    cont++;
    //autocompletar
    var indiceFila=1;
    myNewRow = document.getElementById('tabla3').insertRow(-1);
    myNewRow.id=indiceFila;
    myNewCell=myNewRow.insertCell(-1);
    myNewCell.innerHTML='<div class="col-md-12"><div class="form-group has-feedback2"><label class="control-label">Indicador: <span class="symbol required"></span></label><textarea class="form-control" name="indicador[]'+cont+'" id="indicador'+cont+'" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Indicador para Tema" title="Ingrese Indicador para Tema" rows="2" autocomplete="off" required="" aria-required="true"></textarea><i class="fa fa-pencil form-control-feedback2"></i></div></div>';
    indiceFila++;
    }

    ///FUNCION BORRAR INDICADOR
    function BorrarIndicador() {
        var table = document.getElementById('tabla3');
        if(table.rows.length > 1)
        {
            table.deleteRow(table.rows.length -1);
            cont--;
        }
    }


    //FUNCION AGREGA ESTRATEGIA
    function AddEstrategia()  //Esta la funcion que agrega las filas segunda parte :
    {
    cont++;
    //autocompletar
    var indiceFila=1;
    myNewRow = document.getElementById('tabla4').insertRow(-1);
    myNewRow.id=indiceFila;
    myNewCell=myNewRow.insertCell(-1);
    myNewCell.innerHTML='<div class="col-md-12"><div class="form-group has-feedback2"><label class="control-label">Procedimiento: <span class="symbol required"></span></label><textarea class="form-control" name="estrategia[]'+cont+'" id="estrategia'+cont+'" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Procedimiento para Indicador" title="Ingrese Procedimiento para Indicador" rows="2" autocomplete="off" required="" aria-required="true"></textarea><i class="fa fa-pencil form-control-feedback2"></i></div></div>';
    indiceFila++;
    }

    //FUNCION BORRAR ESTRATEGIA
    function BorrarEstrategia() {
        var table = document.getElementById('tabla4');
        if(table.rows.length > 1)
        {
            table.deleteRow(table.rows.length -1);
            cont--;
        }
    }

    //FUNCION AGREGA EVALUACION
    function AddEvaluacion()  //Esta la funcion que agrega las filas segunda parte :
    {
    cont++;
    //autocompletar
    var indiceFila=1;
    myNewRow = document.getElementById('tabla5').insertRow(-1);
    myNewRow.id=indiceFila;
    myNewCell=myNewRow.insertCell(-1);
    myNewCell.innerHTML='<div class="col-md-12"><div class="form-group has-feedback"><label class="control-label">Instrumento: <span class="symbol required"></span></label><input type="text" class="form-control" name="evaluacion[]'+cont+'" id="evaluacion'+cont+'" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Instrumento para Procedimiento" title="Ingrese Instrumento para Procedimiento" autocomplete="off" required="" aria-required="true"><i class="fa fa-pencil form-control-feedback"></i></div></div>';
    myNewCell=myNewRow.insertCell(-1);
    myNewCell.innerHTML='<div class="col-md-12"><div class="form-group has-feedback"><label class="control-label">Tiempo: <span class="symbol required"></span></label><input type="text" class="form-control" name="tiempo[]'+cont+'" id="tiempo'+cont+'" onKeyUp="this.value=this.value.toUpperCase();" placeholder="Ingrese Tiempo para Instrumento" title="Ingrese Tiempo para Instrumento" autocomplete="off" required="" aria-required="true"><i class="fa fa-pencil form-control-feedback"></i></div></div>';
    indiceFila++;
    }

    //FUNCION BORRAR EVALUACION
    function BorrarEvaluacion() {
        var table = document.getElementById('tabla5');
        if(table.rows.length > 1)
        {
            table.deleteRow(table.rows.length -1);
            cont--;
        }
    }

    ////////////FUNCION ASIGNA VALOR DE CONT PARA EL FOR DE MOSTRAR DATOS MP-MOD-TT////////
    function asigna()
    {
        valor=document.form.var_cont.value=cont;
    }

$(document).ready(function () {
                var doc = $(document);
                $('a.add-type').die('click').live('click', function (e) {
                    e.preventDefault();
                    var content = $('#type-container .type-row'),
                        element = null;
                    for (var i = 0; i < 1; i++) {
                        element = content.clone();
                        var type_div = 'teams_' + now();
                        element.attr('id', type_div);
                        element.find('.remove-type').attr('targetDiv', type_div);
                        element.appendTo('#type_container');

                    }
                });

                $(".remove-type").die('click').live('click', function (e) {
                    //var didConfirm = confirm("Are you sure You want to delete");
                    //if (didConfirm == true) {
                        var id = $(this).attr('data-id');
                        var targetDiv = $(this).attr('targetDiv');
                        //if (id == 0) {
                        //var trID = jQuery(this).parents("tr").attr('id');
                        $('#' + targetDiv).remove();
                        // }
                        //return true;
                    //} else {
                        //return false;
                    //}
                });

            });