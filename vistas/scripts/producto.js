var tabla_productos;
var precio_v = 0;
var array_precios_art  =[];
var array_presentaciones_art  =[];
function init() {

  // listar_tabla();

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-producto").submit(); console.log('jjjjjj'); } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/producto.php?op=select_categoria", '#categoria', null);
  lista_select2("../ajax/producto.php?op=select_u_medida", '#u_medida', null);
  lista_select2("../ajax/producto.php?op=select_u_medida", '#u_medida_presentacion', null);
  lista_select2("../ajax/producto.php?op=select_marca", '#marca', null);
  lista_select2("../ajax/producto.php?op=select2_tipo_igv", '#tipo_igv', null);

  lista_select2("../ajax/producto.php?op=select2_filtro_categoria", '#filtro_categoria', null);
  lista_select2("../ajax/producto.php?op=select2_filtro_u_medida", '#filtro_unidad_medida', null);
  lista_select2("../ajax/producto.php?op=select2_filtro_marca", '#filtro_marca', null);

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════ 
  $("#filtro_categoria").select2({ theme: "bootstrap4", placeholder: "Seleccione categoria", allowClear: true, });
  $("#filtro_unidad_medida").select2({ theme: "bootstrap4", placeholder: "Seleccione unidad medida", allowClear: true, });
  $("#filtro_marca").select2({ theme: "bootstrap4", placeholder: "Seleccione marca", allowClear: true, });

  $("#categoria").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#u_medida").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#u_medida_presentacion").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#marca").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#tipo_igv").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

}

//  :::::::::::::::: P R O D U C T O :::::::::::::::: 

// abrimos el navegador de archivos
$("#doc1_i").click(function () { $('#doc1').trigger('click'); });
$("#doc1").change(function (e) { addImageApplication(e, $("#doc1").attr("id"), null, 180, 310, true) });

function doc1_eliminar() {
  $("#doc1").val("");
  $("#doc1_ver").html('<img src="../assets/images/default/product.jpg" alt="" width="100%" >');
  $("#doc1_nombre").html("");
}

// abrimos el navegador de archivos
$("#doc2_i").click(function () { $('#doc2').trigger('click'); });
$("#doc2").change(function (e) { addImageApplication(e, $("#doc2").attr("id"), null, 180, 310, true) });

function doc2_eliminar() {
  $("#doc2").val("");
  $("#doc2_ver").html('<img src="../assets/images/default/img_defecto_rectangulo.png" alt="" width="78%" >');
  $("#doc2_nombre").html("");
}

// abrimos el navegador de archivos
$("#doc3_i").click(function () { $('#doc3').trigger('click'); });
$("#doc3").change(function (e) { addImageApplication(e, $("#doc3").attr("id"), null, 180, 310, true) });

function doc3_eliminar() {
  $("#doc3").val("");
  $("#doc3_ver").html('<img src="../assets/images/default/img_defecto_vert.png" alt="" width="78%" >');
  $("#doc3_nombre").html("");
}

function limpiar_form_producto() {

  $('#idproducto').val('');
  $('#idsucursal').val('').trigger('change');

  $('#tipo').val('PR');
  $('#codigo').val('');
  $('#codigo_alterno').val('');
  $('#categoria').val('').trigger('change');
  $('#u_medida').val('58').trigger('change'); // por defecto: NIU
  $('#tipo_igv').val('1').trigger('change'); // por defecto: NIU
  $('#marca').val('').trigger('change');
  $('#nombre').val('');
  $('#descripcion').val('');
  $('#stock').val('');
  $('#stock_min').val('');
  $('#precio_v').val('');

  $('#precio_c').val('');
  $('#x_ganancia_max').val('');
  $('#x_ganancia_min').val('');
  $('#precio_v_min').val('');

  $('#Peso_kg').val('');

  $(".new_row_table_precio").html('');
  $(".tabla_new_row").html('');

  doc1_eliminar();
  doc2_eliminar();
  doc3_eliminar();
  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function show_hide_form(flag) {
  if (flag == 1) {
    $(".card-header").show();
    $("#div-tabla").show();
    $(".div-form").hide();

    $(".btn-agregar").show();
    $(".btn-guardar").hide();
    $(".btn-cancelar").hide();

  } else if (flag == 2) {
    $(".card-header").hide();
    $("#div-tabla").hide();
    $(".div-form").show();

    $(".btn-agregar").hide();
    $(".btn-guardar").show();
    $(".btn-cancelar").show();
  }
}

// ::::::::::::::::::::::::::::::::::::::::::::: MOSTRAR ANTICIPOS :::::::::::::::::::::::::::::::::::::::::::::
function multiplecio() {

  if ($(".multi_precio").hasClass("on") == true) {

    $(".data_multi_p").show("slow");

  } else {

    $(".data_multi_p").hide("slow");
  }

}

// calculo de porcentajes y precios de venta
function calculo_precios() {
  // Actualizar cuando cambia el precio de compra
  $("#precio_c").on('input', function () {
    var precio_c = parseFloat($(this).val());
    var x_ganancia_max = parseFloat($("#x_ganancia_max").val());
    var x_ganancia_min = parseFloat($("#x_ganancia_min").val());

    if (!isNaN(precio_c)) {
      $("#x_ganancia_max").removeAttr("readonly");
      $("#x_ganancia_min").removeAttr("readonly");

      var precio_v = precio_c * (1 + x_ganancia_max / 100);
      var precio_v_min = precio_c * (1 + x_ganancia_min / 100);

      $("#precio_v").val(precio_v.toFixed(2));
      $("#precio_v_min").val(precio_v_min.toFixed(2));
    } else {
      $("#x_ganancia_max").attr("readonly", "readonly");
      $("#x_ganancia_min").attr("readonly", "readonly");
    }
  });

  // Actualizar porcentaje de ganancia máximo cuando cambia el precio de venta
  $("#precio_v").on('input', function () {
    var precio_v = parseFloat($(this).val());
    var precio_c = parseFloat($("#precio_c").val());

    if (!isNaN(precio_v) && !isNaN(precio_c) && precio_c !== 0) {
      var x_ganancia_max = ((precio_v - precio_c) / precio_c) * 100;
      var precio_vent_sin_igv = precio_v / 1.18;
      $("#x_ganancia_max").val(x_ganancia_max.toFixed(2));
      //$("#precio_v_sin_igv").val(precio_vent_sin_igv.toFixed(2));
    }
  });

  // Actualizar precio de venta cuando cambia el porcentaje de ganancia máximo
  $("#x_ganancia_max").on('input', function () {
    var x_ganancia_max = parseFloat($(this).val());
    var precio_c = parseFloat($("#precio_c").val());

    if (!isNaN(x_ganancia_max) && !isNaN(precio_c)) {
      var precio_v = precio_c * (1 + x_ganancia_max / 100);
      var precio_vent_sin_igv = precio_v / 1.18;
      $("#precio_v").val(precio_v.toFixed(2));
      //$("#precio_v_sin_igv").val(precio_vent_sin_igv.toFixed(2));
    }
  });

  // Actualizar porcentaje de ganancia máximo cuando cambia el precio de venta
  $("#precio_v_min").on('input', function () {
    var precio_v_min = parseFloat($(this).val());
    var precio_c = parseFloat($("#precio_c").val());

    if (!isNaN(precio_v_min) && !isNaN(precio_c) && precio_c !== 0) {
      var x_ganancia_min = ((precio_v_min - precio_c) / precio_c) * 100;
      $("#x_ganancia_min").val(x_ganancia_min.toFixed(2));
    }
  });

  // Actualizar precio de venta mínima cuando cambia el porcentaje de ganancia mínimo
  $("#x_ganancia_min").on('input', function () {
    var x_ganancia_min = parseFloat($(this).val());
    var precio_c = parseFloat($("#precio_c").val());

    if (!isNaN(x_ganancia_min) && !isNaN(precio_c)) {
      var precio_v_min = precio_c * (1 + x_ganancia_min / 100);
      $("#precio_v_min").val(precio_v_min.toFixed(2));
    }
  });

  // Inicializar valores si ya están presentes en los campos de entrada
  $("#precio_c").trigger('input');
  $("#precio_v").trigger('input');
  $("#precio_v_min").trigger('input');
  $("#x_ganancia_max").trigger('input');
  $("#x_ganancia_min").trigger('input');

}

var cont = 0;

function add_fila() {
  cont++;
  var new_fila = `
      <tr class ="id_${cont}" >
        <td class ="id_${cont}" >
          <button class="btn btn-icon btn-sm btn-danger-light border-danger product-btn id_${cont}" onclick="delete_item(${cont});" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
        </td>
        <td class ="id_${cont}"> <input type="text" class="form-control id_${cont} class_precio_art" placeholder="Agregar nombre" name="nombre_multip[]" > </td>
        <td class ="id_${cont}"> <input type="number" class="form-control id_${cont} class_precio_art" step="0.01" name="monto_multip[]" placeholder="S/. 00.00"> </td>
      </tr>`;

  $(".tbl_multiprecio").show(); $(".message_init").hide();

  $(".new_row_table_precio").append(new_fila);
}
//$(`#cantidad_tup_view_${idproducto}`).rules("add", { required: true, min: 0, messages: { required: `Campo requerido.`, min: "Mínimo {0}", step: "Maximo 2 decimales" } });

function delete_item(id) {
  $(`.id_${id}`).remove();
  if ($(".new_row_table_precio").children().length == 0) {
    $(".tbl_multiprecio").hide(); $(".message_init").show();
  }
}

function select_tipo_igv() {
  var tipo_igv = $('#tipo_igv').select2('val');

  if (tipo_igv == null || tipo_igv == '' || tipo_igv === undefined || tipo_igv != '1') {
    $(".segun_t_igv").hide();
    $(".precio_sin_igv").hide();

  } else {
    $(".segun_t_igv").show();
    $(".precio_sin_igv").show();
  }
}

function capt_nombre_um() {

  var value_medida_p = $('#u_medida').select2('val');
  var u_medida = $('#u_medida :selected').text();
  var nombre = $('#nombre').val();
  precio_v = $('#precio_v').val();


  if (value_medida_p == null || value_medida_p == '' || value_medida_p === undefined) { } else {
    var um_abr = $("#u_medida option:selected").attr("abrv");
    $(".um_antigua").html(`(${um_abr})`);
  }

  if (nombre == null || nombre == "") { $(".view_name").html('Producto : <i class="fas fa-spinner fa-pulse fa-sm"></i>'); } else { $('.view_name').html(`Producto : ${nombre}`); }
  if (u_medida == null || u_medida == "") { $(".view_um").html('U.M : <i class="fas fa-spinner fa-pulse fa-sm"></i>'); } else { $('.view_um').html(`U.M  : ${u_medida}`); }
  if (precio_v == null || precio_v == "") { $(".view_pv").html('P.V : <i class="fas fa-spinner fa-pulse fa-sm"></i>'); } else { $('.view_pv').html(`P.V  : S/. ${precio_v}`); }

}

//Precio oresentacion

function calcular_pr_c() {

  var precio_cmpr = $('#precio_c').val();
  var cant_xpresent = $("#cantidadxpresent").val();

  if (!isNaN(precio_cmpr) && !isNaN(cant_xpresent) && precio_cmpr > 0) {

    var precio_cmpr = precio_cmpr * cant_xpresent;

    $("#precio_c_presentacion").val(precio_cmpr.toFixed(2));

  } else {
    toastr.warning('El precio de compra está vacío.');
  }
}

function limpiar_impresentacion() {

  $("#codigo_alterno_presentacion").val("");
  $("#nombre_presentacion").val("");
  $("#u_medida_presentacion").val("").val('').trigger('change');
  $("#precio_c_presentacion").val("");
  $("#precio_v_presentacion").val("");
  $("#precio_v_min_presentacion").val("");
  $("#cantidadxpresent").val("");

}

var cont = 0;

function add_presentacion(event) {

  // Prevenir el comportamiento por defecto del botón
  event.preventDefault();

  // Seleccionar los campos específicos que quieres validar
  var isValid = false;
  $('#codigo_alterno_presentacion, #nombre_presentacion, #u_medida_presentacion, #precio_c_presentacion, #precio_v_presentacion, #precio_v_min_presentacion, #cantidadxpresent').each(function () {
    if ($(this).val() === '' || !this.checkValidity()) {
      isValid = false;
      $(this).addClass('is-invalid'); // Añadir clase para mostrar que el campo es inválido
      $(this).removeClass('is-valid');
    } else {
      isValid = true;
      $(this).removeClass('is-invalid'); // Quitar clase si el campo es válido
      $(this).addClass('is-valid'); // Añadir clase para mostrar que el campo es válido
    }
  });

  // Mostrar alerta si hay campos inválidos
  if (isValid == false) {
    toastr.warning('Por favor completa todos los campos requeridos.');
  } else {
    //-------------------------
    var u_medida_p = $('#u_medida_presentacion').select2('val');
    var nombre_u_m_p = $('#u_medida_presentacion :selected').text();

    var cod_al_p = $('#codigo_alterno_presentacion').val();
    var nombre_p = $('#nombre_presentacion').val();

    var precio_c_p = $('#precio_c_presentacion').val();
    var precio_v_p = $('#precio_v_presentacion').val();
    var precio_v_min_p = $('#precio_v_min_presentacion').val();
    var cantidadxp = $('#cantidadxpresent').val();


    if (u_medida_p == null || u_medida_p == "" || cod_al_p == null || cod_al_p == ""
      || nombre_p == null || nombre_p == "" || precio_c_p == null || precio_c_p == ""
      || precio_v_p == null || precio_v_p == "" || precio_v_min_p == null || precio_v_min_p == ""
      || cantidadxp == null || cantidadxp == "") {
      toastr.warning('Por favor completa todos los campos requeridos !!');
    } else {
      cont++;
      $(".tabla_sindata").hide();
      var items = "";
      items = `<tr class="delete_${cont}" >
                <td class="delete_${cont}"><span class="badge bg-danger m-r-4px cursor-pointer" onclick="eliminar_items(${cont});" ><i class="ri-delete-bin-line"></i></span></td>
                <th class="delete_${cont}" scope="row">${cod_al_p} <input class="delete_${cont}" type="hidden" name="code_present[]" id="code_present" value="${cod_al_p}"> </th>
                <td class="delete_${cont}">${nombre_p} <input class="delete_${cont}" type="hidden" name="nombre_present[]" id="nombre_present" value="${nombre_p}"></td>
                <td class="delete_${cont}">${nombre_u_m_p} <input class="delete_${cont}" type="hidden" name="u_medida_present[]" id="u_medida_present" value="${u_medida_p}"></td>
                <td class="delete_${cont}">${cantidadxp} <input class="delete_${cont}" type="hidden" name="cant_present[]" id="cant_present" value="${cantidadxp}"></td>
                <td class="delete_${cont}">${precio_c_p} <input class="delete_${cont}" type="hidden" name="precio_c_present[]" id="precio_c_present" value="${precio_c_p}"></td>
                <td class="delete_${cont}">${precio_v_p} <input class="delete_${cont}" type="hidden" name="precio_v_present[]" id="precio_v_present" value="${precio_v_p}"></td>
                <td class="delete_${cont}">${precio_v_min_p} <input class="delete_${cont}" type="hidden" name="precio_vm_present[]" id="precio_vm_present" value="${precio_v_min_p}"></td>
                
              </tr>` ;

      $(".tabla_new_row").append(items);
      limpiar_impresentacion();
      toastr.success("Presentación agregada correctamente!");

    }

  }

}

function eliminar_items(id) {
  console.log(id);
  $(`.delete_${id}`).remove();
  if ($(".tabla_new_row").children().length == 0) {
    $('.tabla_sindata').show();
  }
}

function listar_tabla(filtro_categoria, filtro_unidad_medida, filtro_marca) {

  tabla_productos = $('#tabla-productos').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom: "<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function (e, dt, node, config) { if (tabla_productos) { tabla_productos.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0, 13, 14, 12, 10, 11, 4, 5, 6, 7, 8], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true, },
      { extend: 'excel', exportOptions: { columns: [0, 13, 14, 12, 10, 11, 4, 5, 6, 7, 8], }, title: 'Lista de Productos', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true, },
      { extend: 'pdf', exportOptions: { columns: [0, 13, 14, 12, 10, 11, 4, 5, 6, 7, 8], }, title: 'Lista de Productos', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL', },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    "ajax": {
      url: `../ajax/producto.php?op=listar_tabla_p&categoria=${filtro_categoria}&unidad_medida=${filtro_unidad_medida}&marca=${filtro_marca}`,
      type: "get",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText); ver_errores(e);
      },
      complete: function () {
        $(".buttons-reload").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Recargar');
        $(".buttons-copy").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Copiar');
        $(".buttons-excel").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Excel');
        $(".buttons-pdf").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'PDF');
        $(".buttons-colvis").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Columnas');
        $('[data-bs-toggle="tooltip"]').tooltip();
        $('.buscando_tabla').hide()
      },
      dataSrc: function (e) {
        if (e.status != true) { ver_errores(e); } return e.aaData;
      },
    },
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      // columna: #
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center") }
      // columna: #
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
      // columna: 5
      //if (data[15] == 1) { $("td", row).eq(1).attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'No tienes opcion a modificar'); }
    },
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs: [
      { targets: [10, 11, 12, 13, 14, 15], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function guardar_editar_producto(e) {
  var formData = new FormData($("#form-agregar-producto")[0]);

  $.ajax({
    url: "../ajax/producto.php?op=guardar_editar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      try {
        e = JSON.parse(e);
        if (e.status == true) {
          sw_success('Exito', 'producto guardado correctamente.');
          tabla_productos.ajax.reload(null, false);
          show_hide_form(1);
          limpiar_form_producto();
        } else {
          ver_errores(e);
        }
      } catch (err) { console.log('Error: ', err.message); toastr_error("Error temporal!!", 'Puede intentalo mas tarde, o comuniquese con:<br> <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>', 700); }
      $(".btn-guardar").html('<i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar').removeClass('disabled send-data');
    },
    xhr: function () {
      var xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener("progress", function (evt) {
        if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total) * 100;
          $("#barra_progress_producto").css({ "width": percentComplete + '%' });
          $("#barra_progress_producto div").text(percentComplete.toFixed(2) + " %");
        }
      }, false);
      return xhr;
    },
    beforeSend: function () {
      $(".btn-guardar").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_producto").css({ width: "0%", });
      $("#barra_progress_producto div").text("0%");
      $("#barra_progress_producto_div").show();
    },
    complete: function () {
      $("#barra_progress_producto").css({ width: "0%", });
      $("#barra_progress_producto div").text("0%");
      $("#barra_progress_producto_div").hide();
    },
    error: function (jqXhr, ajaxOptions, thrownError) {
      ver_errores(jqXhr);
    }
  });
}

function mostrar_producto(idproducto) {
  limpiar_form_producto();
  show_hide_form(2);
  $('#cargando-1-fomulario').hide(); $('#cargando-2-fomulario').show();
  $.post("../ajax/producto.php?op=mostrar", { idproducto: idproducto }, function (e, status) {
    e = JSON.parse(e); console.log(e.data);

    $('#idproducto').val(e.data.prod.idproducto);
    $('#categoria').val(e.data.prod.idproducto_categoria).trigger('change');
    $('#u_medida').val(e.data.prod.idsunat_c03_unidad_medida).trigger('change');
    $('#marca').val(e.data.prod.idproducto_marca).trigger('change');

    $('#codigo').val(e.data.prod.codigo);
    $('#codigo_alterno').val(e.data.prod.codigo_alterno);
    $('#nombre').val(e.data.prod.nombre);
    $('#descripcion').val(e.data.prod.descripcion);
    $('#stock').val(e.data.prod.stock);
    $('#stock_min').val(e.data.prod.stock_minimo);
    $('#precio_c').val(e.data.prod.precio_compra);
    $('#precio_v').val(e.data.prod.precio_venta);
    //$('#precio_v_sin_igv').val(e.data.prod.precio_v_sin_igv); 
    $('#precio_v_min').val(e.data.prod.precio_venta_minima);
    $('#x_ganancia_max').val(e.data.prod.ganancia_maxima);
    $('#x_ganancia_min').val(e.data.prod.ganacia_minima);

    $('#Peso_kg').val(e.data.prod.peso);

    if (e.data.present.length === 0) { } else {

      $(".multi_precio").addClass("on");

      $(".data_multi_p").show("slow");
      var items = "";
      $(".tabla_sindata").hide();

      $.each(e.data.present, function(index, val0) {
       
        items = `<tr class="delete_${val0.idproducto}_${val0.idpp}" >
                  <td class="delete_${val0.idproducto}_${val0.idpp}"><span class="badge bg-danger m-r-4px cursor-pointer" onclick="eliminar_items('${val0.idproducto}_${val0.idpp}');" ><i class="ri-delete-bin-line"></i></span></td>
                  <th class="delete_${val0.idproducto}_${val0.idpp}" scope="row">${val0.codigo} <input class="delete_${val0.idproducto}_${val0.idpp}" type="hidden" name="code_present[]" id="code_present" value="${val0.codigo}"> </th>
                  <td class="delete_${val0.idproducto}_${val0.idpp}">${val0.nombre} <input class="delete_${val0.idproducto}_${val0.idpp}" type="hidden" name="nombre_present[]" id="nombre_present" value="${val0.nombre}"></td>
                  <td class="delete_${val0.idproducto}_${val0.idpp}">${val0.unidad_medida_present} <input class="delete_${val0.idproducto}_${val0.idpp}" type="hidden" name="u_medida_present[]" id="u_medida_present" value="${val0.idsunat_c03_unidad_medida}"></td>
                  <td class="delete_${val0.idproducto}_${val0.idpp}">${val0.cantidad} <input class="delete_${val0.idproducto}_${val0.idpp}" type="hidden" name="cant_present[]" id="cant_present" value="${val0.cantidad}"></td>
                  <td class="delete_${val0.idproducto}_${val0.idpp}">${val0.precio_compra} <input class="delete_${val0.idproducto}_${val0.idpp}" type="hidden" name="precio_c_present[]" id="precio_c_present" value="${val0.precio_compra}"></td>
                  <td class="delete_${val0.idproducto}_${val0.idpp}">${val0.precio_venta} <input class="delete_${val0.idproducto}_${val0.idpp}" type="hidden" name="precio_v_present[]" id="precio_v_present" value="${val0.precio_venta}"></td>
                  <td class="delete_${val0.idproducto}_${val0.idpp}">${val0.precio_minimo} <input class="delete_${val0.idproducto}_${val0.idpp}" type="hidden" name="precio_vm_present[]" id="precio_vm_present" value="${val0.precio_minimo}"></td>
                  
                </tr>` ;
  
        $(".tabla_new_row").append(items);

      });

    }

    if (e.data.mult_p.length === 0) { } else {

      $(".multi_precio").addClass("on");

      $(".data_multi_p").show("slow");

      $.each(e.data.mult_p, function(index, val) {

        var new_fila = `
            <tr class ="id_${val.idproducto}" >
              <td class ="id_${val.idproducto}" >
                <button class="btn btn-icon btn-sm btn-danger-light border-danger product-btn id_${val.idproducto}" onclick="delete_item(${val.idproducto});" data-bs-toggle="tooltip" title="Eliminar"><i class="ri-delete-bin-line"></i></button>
              </td>
              <td class ="id_${val.idproducto}"> <input type="text" class="form-control id_${val.idproducto}" placeholder="Agregar nombre" name="nombre_multip[]" value="${val.nombre}" > </td>
              <td class ="id_${val.idproducto}"> <input type="number" class="form-control id_${val.idproducto}" step="0.01" name="nomto_multip[]" value="${val.precio_venta}"> </td>
            </tr>`;
      
        $(".tbl_multiprecio").show(); $(".message_init").hide();
      
        $(".new_row_table_precio").append(new_fila);

      });

    }

    // ------------ IMAGEN 1 -----------
    if (e.data.prod.imagen_cuadrado == "" || e.data.prod.imagen_cuadrado == null) { } else {
      $("#doc_old_1").val(e.data.prod.imagen_cuadrado);
      $("#doc1_nombre").html(`<div class="row"> <div class="col-md-12"><i>imagen.${extrae_extencion(e.data.prod.imagen_cuadrado)}</i></div></div>`);
      // cargamos la imagen adecuada par el archivo
      $("#doc1_ver").html(doc_view_extencion(e.data.prod.imagen_cuadrado, '../assets/modulo/productos', '50%', '110'));   //ruta imagen          
    }

    // ------------ IMAGEN 2 -----------
    if (e.data.prod.imagen_horizontal == "" || e.data.prod.imagen_horizontal == null) { } else {
      $("#doc_old_2").val(e.data.prod.imagen_horizontal);
      $("#doc2_nombre").html(`<div class="row"> <div class="col-md-12"><i>imagen.${extrae_extencion(e.data.prod.imagen_horizontal)}</i></div></div>`);
      // cargamos la imagen adecuada par el archivo
      $("#doc2_ver").html(doc_view_extencion(e.data.prod.imagen_horizontal, '../assets/modulo/productos', '50%', '110'));   //ruta imagen          
    }

    // ------------ IMAGEN 3 -----------
    if (e.data.prod.imagen_vertical == "" || e.data.prod.imagen_vertical == null) { } else {
      $("#doc_old_3").val(e.data.prod.imagen_vertical);
      $("#doc3_nombre").html(`<div class="row"> <div class="col-md-12"><i>imagen.${extrae_extencion(e.data.prod.imagen_vertical)}</i></div></div>`);
      // cargamos la imagen adecuada par el archivo
      $("#doc3_ver").html(doc_view_extencion(e.data.prod.imagen_vertical, '../assets/modulo/productos', '50%', '110'));   //ruta imagen          
    }

    $('#cargando-1-fomulario').show(); $('#cargando-2-fomulario').hide();
    $('#form-agregar-producto').valid();
  });
}

function ver_producto(idproducto,nombre, tipo) {
  console.log(tipo);
  $(".name_producto").html(nombre);
  $("#modal-ver-detalle-producto").modal('show');
  $.post("../ajax/producto.php?op=mostrar_detalle_producto", { idproducto: idproducto ,tipo : tipo}, function (e, status) {
    e = JSON.parse(e); console.log(e.data);
    if (e.status == true) {

      $("#html-detalle-producto").html(e.data);
     // $("#html-detalle-imagen").html(doc_view_download_expand(e.imagen, 'assets/modulo/productos/', e.nombre_doc, '100%', '400px'));

    } else {
      ver_errores(e);
    }
  }).fail(function (e) { ver_errores(e); });
}

function eliminar_papelera_producto(idproducto, nombre) {
  $('.tooltip').remove();
  crud_eliminar_papelera(
    "../ajax/producto.php?op=papelera",
    "../ajax/producto.php?op=eliminar",
    idproducto,
    "!Elija una opción¡",
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`,
    function () { sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado.") },
    function () { sw_success('Eliminado!', 'Tu registro ha sido Eliminado.') },
    function () { tabla_productos.ajax.reload(null, false); },
    false,
    false,
    false,
    false
  );
}


//  :::::::::::::::: C A T E G O R I A :::::::::::::::: 

function modal_add_categoria() {
  $("#modal-agregar-categoria").modal('show');
}

// :::::::::::: U N I D A D    M E D I D A  :::::::::::::::::::

function modal_add_u_medida() {
  $("#modal-agregar-u-m").modal('show');
}

// :::::::::::: M A R C A :::::::::::::::::::

function modal_add_marca() {
  $("#modal-agregar-marca").modal('show');
}

$(document).ready(function () {
  init();
});

function mayus(e) {
  e.value = e.value.toUpperCase();
}

function generarcodigonarti(data) {
  var text_add = "";
  if (data != null && data == '_presentacion') {
    text_add = "_presentacion";
  }

  var name_producto = $(`#nombre${text_add}`).val() == null || $(`#nombre${text_add}`).val() == '' ? '' : $(`#nombre${text_add}`).val();
  if (name_producto == '') { toastr_warning('Vacio!!', 'El nombre esta vacio, digita para completar el codigo aletarorio.', 700); }
  name_producto = name_producto.substring(-3, 3);
  var cod_letra = Math.random().toString(36).substring(2, 5);
  var cod_number = Math.floor(Math.random() * 10) + '' + Math.floor(Math.random() * 10);
  $(`#codigo_alterno${text_add}`).val(`${name_producto.toUpperCase()}${cod_number}${cod_letra.toUpperCase()}`);
}

function create_code_producto(pre_codigo) {
  $('.charge_codigo').html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);

  $.getJSON(`../ajax/ajax_general.php?op=create_code_producto&pre_codigo=${pre_codigo}`, function (e, textStatus, jqXHR) {
    if (e.status == true) {
      $('#codigo').val(e.data.nombre_codigo);
      $('#codigo').attr('readonly', 'readonly').addClass('bg-light'); // Asegura que el campo esté como solo lectura
      add_tooltip_custom('#codigo', 'No se puede editar');            //  Agrega tooltip personalizado a un element
      $('.charge_codigo').html('')                                    // limpiamos la carga
    } else {
      ver_errores(e);
    }
  }).fail(function (jqxhr, textStatus, error) { ver_errores(jqxhr); });

}


$(function () {
  $('#categoria').on('change', function () { $(this).trigger('blur'); });
  $('#u_medida').on('change', function () { $(this).trigger('blur'); });
  $('#marca').on('change', function () { $(this).trigger('blur'); });

  //  :::::::::::::::::::: F O R M U L A R I O   P R O D U C T O ::::::::::::::::::::
  $("#form-agregar-producto").validate({
    ignore: "",
    rules: {
      codigo: { required: true, minlength: 2, maxlength: 20, },
      categaria: { required: true },
      u_medida: { required: true },
      marca: { required: true },
      nombre: { required: true, minlength: 2, maxlength: 250, },
      descripcion: { minlength: 2, maxlength: 500, },
      stock: { required: true, min: 0, step: 0.01, },
      stock_min: { required: true, min: 0, step: 0.01, },
      precio_v: { required: true, min: 0, step: 0.01, },
      precio_c: { required: true, min: 0, step: 0.01, },
      codigo_alterno: {
        required: true, minlength: 4, maxlength: 20,
        remote: {
          url: "../ajax/producto.php?op=validar_code_producto",
          type: "get",
          data: {
            action: function () { return "validar_codigo"; },
            idproducto: function () { var idproducto = $("#idproducto").val(); return idproducto; }
          }
        }
      },
    },
    messages: {
      cogido: { required: "Campo requerido", },
      categaria: { required: "Seleccione una opción", },
      u_medida: { required: "Seleccione una opción", },
      marca: { required: "Seleccione una opción", },
      nombre: { required: "Campo requerido", },
      descripcion: { minlength: "Minimo {0} caracteres.", },
      stock: { required: "Campo requerido", step: 'Maximo 2 decimales.' },
      stock_min: { required: "Campo requerido", step: 'Maximo 2 decimales.' },
      precio_v: { required: "Campo requerido", step: 'Maximo 2 decimales.' },
      precio_c: { required: "Campo requerido", step: 'Maximo 2 decimales.' },
      codigo_alterno: { required: "Campo requerido", remote: "Código en uso." },
    },

    errorElement: "span",

    errorPlacement: function (error, element) {
      error.addClass("invalid-feedback");
      element.closest(".form-group").append(error);
    },

    highlight: function (element, errorClass, validClass) {
      $(element).addClass("is-invalid").removeClass("is-valid");
    },

    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass("is-invalid").addClass("is-valid");
    },
    submitHandler: function (e) {
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600);
      guardar_editar_producto(e);
    },
  });

  $('#categoria').rules('add', { required: true, messages: { required: "Campo requerido" } });
  $('#u_medida').rules('add', { required: true, messages: { required: "Campo requerido" } });
  $('#marca').rules('add', { required: true, messages: { required: "Campo requerido" } });
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..

function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {

  var filtro_categoria = $("#filtro_categoria").select2('val');
  var filtro_unidad_medida = $("#filtro_unidad_medida").select2('val');
  var filtro_marca = $("#filtro_marca").select2('val');

  var nombre_categoria = $('#filtro_categoria').find(':selected').text();
  var nombre_um = ' ─ ' + $('#filtro_unidad_medida').find(':selected').text();
  var nombre_marca = ' ─ ' + $('#filtro_marca').find(':selected').text();
  console.log(filtro_categoria);
  console.log(filtro_unidad_medida);
  console.log(filtro_marca);
  // filtro de fechas
  if (filtro_categoria == '' || filtro_categoria == 0 || filtro_categoria == null) { filtro_categoria = ""; nombre_categoria = ""; }

  // filtro de proveedor
  if (filtro_unidad_medida == '' || filtro_unidad_medida == 0 || filtro_unidad_medida == null) { filtro_unidad_medida = ""; nombre_um = ""; }

  // filtro de trabajdor
  if (filtro_marca == '' || filtro_marca == 0 || filtro_marca == null) { filtro_marca = ""; nombre_marca = ""; }

  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_categoria} ${nombre_um} ${nombre_marca}...`);
  //console.log(filtro_categoria, fecha_2, filtro_marca, comprobante);

  listar_tabla(filtro_categoria, filtro_unidad_medida, filtro_marca);

}


function reload_idcategoria() { lista_select2("../ajax/producto.php?op=select_categoria", '#categoria', null, '.charge_idcategoria'); }
function reload_idmarca() { lista_select2("../ajax/producto.php?op=select_marca", '#marca', null, '.charge_idmarca'); }
function reload_idunidad_medida() { lista_select2("../ajax/producto.php?op=select_u_medida", '#u_medida', null, '.charge_idunidad_medida'); }

function reload_filtro_categoria() { lista_select2("../ajax/producto.php?op=select2_filtro_categoria", '#filtro_categoria', null, '.charge_filtro_categoria'); }
function reload_filtro_unidad_medida() { lista_select2("../ajax/producto.php?op=select2_filtro_u_medida", '#filtro_unidad_medida', null, '.charge_filtro_unidad_medida'); }
function reload_filtro_marca() { lista_select2("../ajax/producto.php?op=select2_filtro_marca", '#filtro_marca', null, '.charge_filtro_marca'); }
