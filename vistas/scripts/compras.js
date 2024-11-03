var tabla_compras;
var tabla_productos;
var array_data_compra = [];

// ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T C H O I C E ══════════════════════════════════════
//const choice_distrito       = new Choices('#distrito_cp',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );

const choice_distrito       = new Choices('#distrito',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
const choice_tipo_documento = new Choices('#tipo_documento',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
const choice_idbanco        = new Choices('#idbanco',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );

function init(){

  listar_tabla_compra();
  listar_tabla_producto();

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-compra").submit(); }  });
  $("#guardar_registro_proveedor").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-proveedor").submit(); } });
  $("#guardar_registro_producto").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-producto").submit(); } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/compras.php?op=listar_proveedor", '#idproveedor', null);
  lista_select2("../ajax/compras.php?op=listar_crl_comprobante&tipos='00','01','03','12'", '#tipo_comprobante', null);

  lista_select2("../ajax/compras.php?op=select_categoria", '#categoria', null);
  lista_select2("../ajax/compras.php?op=select_u_medida", '#u_medida', null);
  lista_select2("../ajax/compras.php?op=select_marca", '#marca', null);

  lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_distrito", choice_distrito, null);

  lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_tipo_documento", choice_tipo_documento, null);  
  lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_banco", choice_idbanco, null);

  

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════ 
  //$("#idbanco").select2({ templateResult: templateBanco, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#idproveedor").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#tipo_comprobante").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  
}

function show_hide_form(flag) {
	if (flag == 1) {
		$("#div-tabla").show();
		$(".div-formulario").hide();

		$(".btn-agregar").show();
		$(".btn-guardar").hide();
		$(".btn-cancelar").hide();
		
	} else if (flag == 2) {
		$("#div-tabla").hide();
		$(".div-formulario").show();

		$(".btn-agregar").hide();		
		$(".btn-cancelar").show();
	}
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   C O M P R A S :::::::::::::::::::::::::::::::::::::::::::::

// abrimos el navegador de archivos
$("#doc1_i").click(function () { $('#doc1').trigger('click'); });
$("#doc1").change(function (e) { addImageApplication(e, $("#doc1").attr("id"), null, '100%', '300px', true) });

function doc1_eliminar() {
  $("#doc1").val("");
  $("#doc1_ver").html('<img src="../assets/images/default/img_defecto2.png" alt="" width="78%" >');
  $("#doc1_nombre").html("");
}

function limpiar_form_compra(){

  array_data_compra = [];
  $("#idcompra").val('');

  $("#idproveedor").val('').trigger('change');
  $("#tipo_comprobante").val('').trigger('change');
  $("#serie").val('');
  $("#descripcion").val('');
  $("#fecha_compra").val('');
  $("#idproveedor").val('');
  $("#idproveedor").val('');
  doc1_eliminar();

  $("#total_compra").val("");     
  $(".total_compra").html("0");

  $(".subtotal_compra").html("<span>S/</span> 0.00");
  $("#subtotal_compra").val("");

  $(".descuento_compra").html("<span>S/</span> 0.00");
  $("#descuento_compra").val("");

  $(".igv_compra").html("<span>S/</span> 0.00");
  $("#igv_compra").val("");

  $(".total_compra").html("<span>S/</span> 0.00");
  $("#total_compra").val("");

  $("#estado_detraccion").val("0");
  $('#my-switch_detracc').prop('checked', false); 

  $(".filas").remove();

  cont = 0;


  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function listar_tabla_compra(){
  tabla_compras = $("#tabla-compras").dataTable({
    responsive: true, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_compras) { tabla_compras.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3,4,5,6], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de Compras', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5,6], }, title: 'Lista de Compras', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/compras.php?op=listar_tabla_compra`,
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
      },
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
      { targets: [2], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function guardar_editar_compra(e) {
  var formData = new FormData($("#form-agregar-compra")[0]);  

  Swal.fire({
    title: "¿Está seguro que deseas guardar esta compra?",
    html: "Verifica que todos lo <b>campos</b>  esten <b>conformes</b>!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Guardar!",
    preConfirm: (input) => {
      return fetch("../ajax/compras.php?op=guardar_editar_compra", {
        method: 'POST', // or 'PUT'
        body: formData, // data can be `string` or {object}!        
      }).then(response => {
        //console.log(response);
        if (!response.ok) { throw new Error(response.statusText) }
        return response.json();
      }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); });
    },
    showLoaderOnConfirm: true,
  }).then((result) => {
    if (result.isConfirmed) {
      if (result.value.status == true){        
        Swal.fire("Correcto!", "Compra guardada correctamente", "success");

        tabla_compras.ajax.reload(null, false);

        limpiar_form_compra(); show_hide_form(1);
             
      } else {
        ver_errores(result.value);
      }      
    }
  });  
}

function mostrar_detalle_compra(idcompra){
  $("#modal-detalle-compra").modal("show");

  $.post("../ajax/compras.php?op=mostrar_detalle_compra", { idcompra: idcompra }, function (e, status) {          
      
    $('#custom-tabContent').html(e);      
    $('#custom-datos1_html-tab').click(); // click para ver el primer - Tab Panel
    $(".jq_image_zoom").zoom({ on: "grab" });      
    $("#excel_compra").attr("href",`../reportes/export_xlsx_venta_tours.php?id=${idcompra}`);      
    $("#print_pdf_compra").attr("href",`../reportes/comprobante_venta_tours.php?id=${idcompra}`);    
    
  }).fail( function(e) { ver_errores(e); } );

}

function eliminar_papelera_compra(idcompra, nombre){
  $('.tooltip').remove();
	crud_eliminar_papelera(
    "../ajax/compras.php?op=papelera",
    "../ajax/compras.php?op=eliminar", 
    idcompra, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>Compra: ${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_compras.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

function ver_img_comprobante(idcompra) {
  $('#modal-ver-comprobante1').modal('show');
  $("#comprobante-container1").html(`<div class="row" > <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div> </div>`);

  $.post("../ajax/compras.php?op=mostrar_compra", { idcompra: idcompra },  function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {
      if (e.data.comprobante == "" || e.data.comprobante == null) { } else {
        var nombre_comprobante = `${e.data.tipo_comprobante} ${e.data.serie_comprobante}`;
        $('.title-modal-comprobante1').html(nombre_comprobante);
        $("#comprobante-container1").html(doc_view_download_expand(e.data.comprobante, 'assets/modulo/comprobante_compra',nombre_comprobante , '100%', '400px'));
        $('.jq_image_zoom').zoom({ on: 'grab' });
      }
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   P R O D U C T O S :::::::::::::::::::::::::::::::::::::::::::::
function limpiar_form_producto(){

	$('#idproducto').val('');
  
	$('#codigo').val('');
	$('#categoria').val('');
	$('#u_medida').val('58');
	$('#marca').val('');
	$('#nombre').val('');
	$('#descripcion').val('');
	$('#stock').val('');
	$('#stock_min').val('');
	$('#precio_v').val('');
	$('#precio_c').val('');
	$('#precio_x_mayor').val('');
	$('#precio_dist').val('');
	$('#precio_esp').val('');

  $("#imagen").val("");
  $("#imagenactual").val("");
  $("#imagenmuestra").attr("src", "../assets/modulo/productos/no-producto.png");
  $("#imagenmuestra").attr("src", "../assets/modulo/productos/no-producto.png").show();
  var imagenMuestra = document.getElementById('imagenmuestra');
  if (!imagenMuestra.src || imagenMuestra.src == "") {
    imagenMuestra.src = '../assets/modulo/productos/no-producto.png';
  }


  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function listar_tabla_producto(){
  tabla_productos = $("#tabla-productos").dataTable({
    responsive: true, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_productos) { tabla_productos.ajax.reload(null, false); } } },
    ],
    ajax: {
      url: `../ajax/compras.php?op=listar_tabla_producto`,
      type: "get",
      dataType: "json",
      error: function (e) {
        console.log(e.responseText); ver_errores(e);
      },
      complete: function () {
        $(".buttons-reload").attr('data-bs-toggle', 'tooltip').attr('data-bs-original-title', 'Recargar');
        $('[data-bs-toggle="tooltip"]').tooltip();
      },
		},
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-nowrap text-center"); }
      // columna: #
      // if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center") }
      // columna: #
      // if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
      
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
      // { targets: [2], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function listar_producto_x_codigo() {
 
  var codigo = document.getElementById("codigob").value;
  if (codigo == null || codigo == '') { toastr_info('Vacio!!', 'El campo de codigo esta vacío.'); return;  }
  var cantidad = 1; 
  $(`.buscar_x_code`).html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);
  $.post("../ajax/compras.php?op=listar_producto_x_codigo", { codigo: codigo }, function (e, status) {
    e = JSON.parse(e); console.log(e);
    if (e.status == true) {         
      if (e.data == null) {
        toastr_warning('No existe', 'Proporcione un codigo existente o el producto pertenece a otra categoria.');
      } else {
        if ($(`.producto_${e.data.idproducto}`).hasClass("producto_selecionado")) {
          if (document.getElementsByClassName(`producto_${e.data.idproducto}`).length == 1) {
            var cant_producto = $(`.producto_${e.data.idproducto}`).val();
            var sub_total = parseInt(cant_producto, 10) + 1;
            $(`.producto_${e.data.idproducto}`).val(sub_total).trigger('change');
            toastr_success("Agregado!!",`Producto: ${$(`.nombre_producto_${e.data.idproducto}`).text()} agregado !!`, 700);
            modificarSubtotales();          
          }  
                  
          $(`.buscar_x_code`).html(`<i class='bx bx-search-alt'></i>`);
        } else {      
        

          if ($("#tipo_comprobante").select2("val") == "01") {
            var subtotal = cantidad * e.data.precio_venta;
          }else{
            var subtotal = cantidad * e.data.precio_venta;
          }
          
          var img = e.data.imagen == "" || e.data.imagen == null ?img = `../assets/modulo/productos/no-producto.png` : `../assets/modulo/productos/${e.data.imagen}` ;          

          var fila = `
          <tr class="filas" id="fila${cont}"> 

            <td class="py-1">
            <!--  <button type="button" class="btn btn-warning btn-sm" onclick="mostrar_productos(${e.data.idproducto}, ${cont})"><i class="fas fa-pencil-alt"></i></button>-->
              <button type="button" class="btn btn-danger btn-sm btn-file-delete-${cont}" onclick="eliminarDetalle(${e.data.idproducto}, ${cont});"><i class="fas fa-times"></i></button>
            </td>
            <td class="py-1 text-nowrap">
              <i class="bi bi-upc"></i> ${e.data.codigo} <br> <i class="bi bi-person"></i> ${e.data.codigo_alterno}
            </td>
            <td class="py-1">         
              <input type="hidden" name="idproducto[]" value="${e.data.idproducto}">

              <div class="d-flex flex-fill align-items-center">
                <div class="me-2 cursor-pointer" data-bs-toggle="tooltip" title="Ver imagen"><span class="avatar"> <img src="${img}" alt="" onclick="ver_img('${img}', '${encodeHtml(e.data.nombre)}')"> </span></div>
                <div>
                  <h6 class="d-block fw-semibold text-primary">${e.data.nombre}</h6>
                  <span class="d-block fs-12 text-muted">Marca: <b>${e.data.marca}</b> | Categoría: <b>${e.data.categoria}</b></span> 
                </div>
              </div>
            </td>

            <td class="py-1">
              <span class="unidad_medida_${cont}">UNIDAD</span> 
              <input type="hidden" class="unidad_medida_${cont}" name="unidad_medida[]" id="unidad_medida[]" value="UNIDAD">
            </td>

            <td class="py-1 form-group">
              <input type="number" class="w-100px valid_cantidad form-control producto_${e.data.idproducto} producto_selecionado" name="valid_cantidad[${cont}]" id="valid_cantidad_${cont}" value="${cantidad}" min="0.01" required onkeyup="replicar_value_input(this, '#cantidad_${cont}'); update_price(); " onchange="replicar_value_input(this, '#cantidad_${cont}'); update_price(); ">
              <input type="hidden" class="cantidad_${cont}" name="cantidad[]" id="cantidad_${cont}" value="${cantidad}" min="0.01" required  >            
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-135px form-control valid_precio_con_igv" name="valid_precio_con_igv[${cont}]" id="valid_precio_con_igv_${cont}" value="${e.data.precio_venta}" min="0.01" required onkeyup="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); " onchange="replicar_value_input(this, '#precio_con_igv_${cont}'); update_price(); ">
              <input type="hidden" class="precio_con_igv_${cont}" name="precio_con_igv[]" id="precio_con_igv_${cont}" value="${e.data.precio_venta}" onkeyup="modificarSubtotales();" onchange="modificarSubtotales();">              
              <input type="hidden" class="precio_sin_igv_${cont}" name="precio_sin_igv[]" id="precio_sin_igv[]" value="0" min="0" >
              <input type="hidden" class="precio_igv_${cont}" name="precio_igv[]" id="precio_igv[]" value="0"  >
            </td> 

            <td class="py-1 form-group">
              <input type="number" class="w-100px form-control valid_descuento" name="valid_descuento_${cont}" value="0" min="0.00" required onkeyup="replicar_value_input(this, '.descuento_${cont}' ); update_price(); " onchange="replicar_value_input( this, '.descuento_${cont}'); update_price(); ">
              <input type="hidden" class="descuento_${cont}" name="descuento[]" value="0" onkeyup="modificarSubtotales()" onchange="modificarSubtotales()">
            </td>

            <td class="py-1 text-right"><span class="text-right subtotal_producto_${cont}" id="subtotal_producto">${subtotal}</span> <input type="hidden" name="subtotal_producto[]" id="subtotal_producto_${cont}" value="0" > </td>
            <td class="py-1"><button type="button" onclick="modificarSubtotales();" class="btn btn-info btn-sm"><i class="fas fa-sync"></i></button></td>
          </tr>`;

          detalles = detalles + 1;
          $("#tabla-productos-seleccionados tbody").append(fila);
          array_data_compra.push({ id_cont: cont });
          modificarSubtotales();        
          toastr_success("Agregado!!",`Producto: ${e.data.nombre} agregado !!`, 700);

          // reglas de validación     
          $('.valid_precio_con_igv').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0.01, messages: { min:"Mínimo 0.01" } }); 
          });
          $('.valid_cantidad').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0.01, messages: { min:"Mínimo 0.01" } }); 
          });
          $('.valid_descuento').each(function(e) { 
            $(this).rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
            $(this).rules('add', { min:0, messages: { min:"Mínimo {0}" } }); 
          });

          cont++;  
          evaluar();
        }
      }
      $(`.buscar_x_code`).html(`<i class='bx bx-search-alt'></i>`);
      $(`.tooltip`).remove();
      
    } else {
      ver_errores(e);
    } 
  }).fail( function(e) { ver_errores(e); } );

}

function guardar_editar_producto(e){
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
          limpiar_form_producto();
          $("#modal-agregar-producto").modal('hide');
				} else {
					ver_errores(e);
				}				
			} catch (err) { console.log('Error: ', err.message); toastr_error("Error temporal!!",'Puede intentalo mas tarde, o comuniquese con:<br> <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>', 700); }      
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

function cambiarImagenProducto() {
	var imagenInput = document.getElementById('imagenProducto');
	imagenInput.click();
}

function removerImagenProducto() {
	$("#imagenmuestraProducto").attr("src", "../assets/modulo/productos/no-producto.png");
	$("#imagenProducto").val("");
  $("#imagenactualProducto").val("");
}

document.addEventListener('DOMContentLoaded', function () {
	var imagenMuestra = document.getElementById('imagenmuestraProducto');
	var imagenInput = document.getElementById('imagenProducto');

	imagenInput.addEventListener('change', function () {
		if (imagenInput.files && imagenInput.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) { imagenMuestra.src = e.target.result;	}
			reader.readAsDataURL(imagenInput.files[0]);
		}
	});
});


$(document).ready(function () {
  init(); 
});

function mayus(e) { 
  e.value = e.value.toUpperCase(); 
}


// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   P R O V E E D O R :::::::::::::::::::::::::::::::::::::::::::::
function modal_add_trabajador() {
  $("#modal-agregar-proveedor").modal('show');
}

function limpiar_proveedor() {

	$('#idpersona').val('');
  $('#tipo_persona_sunat').val('NATURAL');
  $('#idtipo_persona').val('4');

  $('#tipo_documento').val(null).trigger("change");
  $('#numero_documento').val('');
  $('#nombre_razonsocial').val('');
  $('#apellidos_nombrecomercial').val('');
  $('#correo').val('');
  $('#celular').val('');
  
  $('#direccion').val('');
  $('#distrito').val('').trigger("change");
  $('#departamento').val('');
  $('#provincia').val('');
  $('#ubigeo').val('');
  $('#idbanco').val(null).trigger("change")
  $('#cuenta_bancaria').val('');
  $('#cci').val(''); 

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function guardar_proveedor(e) {

	var formData = new FormData($("#form-agregar-proveedor")[0]);

	$.ajax({
		url: "../ajax/proveedores.php?op=guardar_editar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		success: function (e) {
			try {
				e = JSON.parse(e);  console.log(e); 
        if (e.status == true) {	
					sw_success('Exito', 'proveedor guardado correctamente.');
          $("#modal-agregar-proveedor").modal('hide'); limpiar_proveedor();
          lista_select2("../ajax/compras.php?op=listar_proveedor", '#idproveedor', e.data, '.charge_idproveedor');
				} else {
					ver_errores(e);
				}				
			} catch (err) { console.log('Error: ', err.message); toastr_error("Error temporal!!",'Puede intentalo mas tarde, o comuniquese con:<br> <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>', 700); }      
      $("#guardar_registro_trabajador").html('<i class="ri-save-2-line label-btn-icon me-2" ></i> Guardar').removeClass('disabled send-data');
		},
		xhr: function () {
			var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function (evt) {
				if (evt.lengthComputable) {
					var percentComplete = (evt.loaded / evt.total) * 100;
					/*console.log(percentComplete + '%');*/
					$("#barra_progress_proveedor").css({ "width": percentComplete + '%' });
					$("#barra_progress_proveedor div").text(percentComplete.toFixed(2) + " %");
				}
			}, false);
			return xhr;
		},
		beforeSend: function () {
			$("#guardar_registro_trabajador").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
			$("#barra_progress_proveedor").css({ width: "0%", });
			$("#barra_progress_proveedor div").text("0%");
      $("#barra_progress_proveedor_div").show();
		},
		complete: function () {
			$("#barra_progress_proveedor").css({ width: "0%", });
			$("#barra_progress_proveedor div").text("0%");
      $("#barra_progress_proveedor_div").hide();
		},
		error: function (jqXhr, ajaxOptions, thrownError) {
			ver_errores(jqXhr);
		}
	});
}

$('#tipo_documento').change(function() {
  var tipo = $(this).val();

  if (tipo !== null && tipo !== '' && tipo == '6') {
    $('.label-nom-raz').html('Razón Social <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Nombre comercial <sup class="text-danger">*</sup>');
  }else{
    $('.label-nom-raz').html('Nombres <sup class="text-danger">*</sup>');
    $('.label-ape-come').html('Apellidos <sup class="text-danger">*</sup>');
  }

});

function cambiarImagenProveedor() {
	var imagenInput = document.getElementById('imagen');
	imagenInput.click();
}

function removerImagenProveedor() {
	$("#imagenmuestra").attr("src", "../assets/proveedor/no-proveedor.png");
	$("#imagen").val("");
  $("#imagenactual").val("");
}

document.addEventListener('DOMContentLoaded', function () {
	var imagenMuestra = document.getElementById('imagenmuestra');
	var imagenInput = document.getElementById('imagen');

	imagenInput.addEventListener('change', function () {
		if (imagenInput.files && imagenInput.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) { imagenMuestra.src = e.target.result;	}
			reader.readAsDataURL(imagenInput.files[0]);
		}
	});
});


function ver_img_proveedor(img, nombre) {
	$(".title-foto-proveedor").html(`-${nombre}`);
  $('#modal-ver-foto-proveedor').modal("show");
  $('.html_ver_foto_proveedor').html(doc_view_extencion(img, 'assets/modulo/proveedor', '100%', '550'));
  $(`.jq_image_zoom`).zoom({ on:'grab' });
}

function llenar_dep_prov_ubig(input) {

  $(".chargue-pro").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 
  $(".chargue-dep").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 
  $(".chargue-ubi").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 

  // if ($(input).select2("val") == null || $(input).select2("val") == '') { 
  if ($('#distrito').val() == null || $('#distrito').val() == '') { 
    $("#departamento").val(""); 
    $("#provincia").val(""); 
    $("#ubigeo").val(""); 

    $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
  } else {
     //var iddistrito =  $(input).select2('data')[0].element.attributes.idubigeo_distrito.value;
     var iddistrito = choice_distrito.getValue().customProperties.idubigeo_distrito;
     //$(input).select2('data')[0].element.attributes.iddistrito.value;
    //var iddistrito = $('#distrito').val(); 
    console.log(iddistrito);
    
    $.post(`../ajax/ajax_general.php?op=select2_distrito_id&id=${iddistrito}`, function (e) {   
      e = JSON.parse(e); console.log(e);
      if (e.status == true) {
        $("#departamento").val(e.data.departamento); 
        $("#provincia").val(e.data.provincia); 
        $("#ubigeo").val(e.data.ubigeo_inei);       
      } else {
        ver_errores(e);
      }
      $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
      $("#form-agregar-proveedor").valid();
      
    });
  }  
}


// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  :::::::::::::::::::::::::::::::::::::::..

$(function(){

  $("#form-agregar-compra").validate({
    ignore: '',
    rules: {
      idproveedor:        { required: true },
      tipo_comprobante:   { required: true },
      serie:              { required: true, minlength: 4 },
      descripcion:        { minlength: 4 },
      fecha_compra:       { required: true},
      impuesto:           { min: 0, max:100}
    },
    messages: {
      idproveedor:        { required: "Campo requerido", },
      tipo_comprobante:   { required: "Campo requerido", },
      fecha_compra:       { required: "Campo requerido", },
      serie:              { minlength: "Minimo {0} caracteres", },
      descripcion:        { minlength: "Minimo {0} caracteres", },
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

    submitHandler: function (form) {
      guardar_editar_compra(form);
    },
  }); 

  $('#distrito').on('change', function() { $(this).trigger('blur'); });
  $("#form-agregar-proveedor").validate({
    ignore: "",
    rules: {           
      tipo_documento:           { required: true, minlength: 1, maxlength: 2, },       
      numero_documento:    			{ required: true, minlength: 8, maxlength: 20, },       
      nombre_razonsocial:    		{ required: true, minlength: 4, maxlength: 200, },       
      apellidos_nombrecomercial:{ required: true, minlength: 4, maxlength: 200, },       
      correo:    			          { minlength: 4, maxlength: 100, },       
      celular:    			        { minlength: 8, maxlength: 9, },       

      direccion:    			      { minlength: 4, maxlength: 200, },       
      distrito:    			        { required: true, },       
      departamento:    			    { required: true, },       
      provincia:    			      { required: true, },  
      ubigeo:    			          { required: true, },

      idbanco:    			        { required: true, },
      cuenta_bancaria:    			{ minlength: 4, maxlength: 45, },
      cci:    			            { minlength: 4, maxlength: 45, },
			
    },
    messages: {     
      tipo_documento:    			  { required: "Campo requerido", },
      numero_documento:    			{ required: "Campo requerido", }, 
      nombre_razonsocial:    		{ required: "Campo requerido", }, 
      apellidos_nombrecomercial:{ required: "Campo requerido", }, 
      correo:    			          { minlength: "Mínimo {0} caracteres.", }, 
      celular:    			        { minlength: "Mínimo {0} caracteres.", }, 

      direccion:    			      { minlength: "Mínimo {0} caracteres.", },
      distrito:    			        { required: "Campo requerido", }, 
      departamento:    			    { required: "Campo requerido", }, 
      provincia:    			      { required: "Campo requerido", }, 
      ubigeo:    			          { required: "Campo requerido", },

      idbanco:    			        { required: "Campo requerido", }, 
      cuenta_bancaria:    			{ minlength: "Mínimo {0} caracteres.", }, 
      cci:    			            { minlength: "Mínimo {0} caracteres.", }, 
      titular_cuenta:    			  { minlength: "Mínimo {0} caracteres.", },  

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
      $(".modal-body").animate({ scrollTop: $(document).height() }, 600); // Scrollea hasta abajo de la página
      guardar_proveedor(e);      
    },
  });
  $('#distrito').rules('add', { required: true, messages: {  required: "Campo requerido" } });

  $("#form-agregar-producto").validate({
    ignore: "",
    rules: {           
      codigo:         { required: true, minlength: 2, maxlength: 20, },       
      categaria:    	{ required: true },       
      u_medida:    		{ required: true },       
      marca:    			{ required: true },       
      nombre:    			{ required: true, minlength: 2, maxlength: 20,  },       
      descripcion:    { required: true, minlength: 2, maxlength: 500, },       
      stock:          { required: true, min: 0,  },       
      stock_min:      { required: true, min: 0,  }, 
      precio_v:       { required: true, min: 0,  },       
      precio_c:       { required: true, min: 0,  },	
    },
    messages: {     
      cogido:    			{ required: "Campo requerido", },
      categaria:    	{ required: "Seleccione una opción", },
      u_medida:    		{ required: "Seleccione una opción", },
      marca:    			{ required: "Seleccione una opción", },
      nombre:    			{ required: "Campo requerido", }, 
      descripcion:    { required: "Campo requerido", },       
      stock:          { required: "Campo requerido", },       
      stock_min:      { required: "Campo requerido", }, 
      precio_v:       { required: "Campo requerido", },       
      precio_c:       { required: "Campo requerido", },	
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

});




// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..

function reload_idproveedor(){ lista_select2("../ajax/compras.php?op=listar_proveedor", '#idproveedor', null, '.charge_idproveedor'); }
