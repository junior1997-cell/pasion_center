var tabla_principal_facturacion;
var tabla_productos;
var form_validate_facturacion;
var tabla_ver_mas_detalle_facturacion;

var array_data_venta = [];
var file_pond_mp_comprobante;
var cambio_de_tipo_comprobante ;

var filtro_estado_sunat = "" ;

var chart_barra;

// ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T C H O I C E ══════════════════════════════════════

// const choice_distrito       = new Choices('#distrito',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_tipo_documento = new Choices('#f_tipo_documento',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );
// const choice_idbanco        = new Choices('#idbanco',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );

// var myElement1 = document.getElementById('recent-jobs');
// myElement1.style.height = '300px'; // Cambia esta altura según tus necesidades
// new SimpleBar(myElement1, { autoHide: true });

async function init(){

  // filtros(); // Listamos la tabla principal
  $(".btn-tiket").click();   // Selecionamos la BOLETA
  //mini_reporte();

  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  $(".btn-guardar").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-venta").submit(); }  });
  $("#guardar_registro_proveedor").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-proveedor").submit(); } });
  $("#guardar_registro_producto").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-producto").submit(); } });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_select2("../ajax/facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_comprobante', null, '.charge_filtro_comprobante');
  lista_select2("../ajax/facturacion.php?op=select2_filtro_cliente", '#filtro_cliente', null, '.charge_filtro_cliente');

  lista_select2("../ajax/facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_md_comprobante', null, '.charge_filtro_md_comprobante');
  lista_select2("../ajax/facturacion.php?op=select2_filtro_cliente", '#filtro_md_cliente', null, '.charge_filtro_md_cliente');

  lista_select2("../ajax/facturacion.php?op=select2_cliente", '#f_idpersona_cliente', null);
  lista_select2("../ajax/facturacion.php?op=select2_codigo_x_anulacion_comprobante", '#f_nc_motivo_anulacion', '01');  
  lista_select2("../ajax/facturacion.php?op=select2_banco", '#f_metodo_pago', null, 'charge_f_metodo_pago');  

  lista_select2("../ajax/facturacion.php?op=select2_filtro_trabajador", '#filtro-trabajador', localStorage.getItem('nube_id_persona_trabajador'), '.charge_filtro_trabajador');


  // lista_select2("../ajax/facturacion.php?op=select_categoria", '#categoria', null);
  // lista_select2("../ajax/facturacion.php?op=select_u_medida", '#u_medida', null);
  // lista_select2("../ajax/facturacion.php?op=select_marca", '#marca', null);

  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_distrito", choice_distrito, null);
  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_tipo_documento", choice_tipo_documento, null);  
  // lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_banco", choice_idbanco, null);

  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  $("#f_idpersona_cliente").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#f_nc_tipo_comprobante").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#f_nc_serie_y_numero").select2({ templateResult: templateSerieNumero, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#f_nc_motivo_anulacion").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#f_metodo_pago").select2({  templateResult: templateBanco, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#filtro_cliente").select2({ templateResult: templateCliente, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_comprobante").select2({ templateResult: templateComprobante, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#filtro_md_cliente").select2({ templateResult: templateCliente, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_md_comprobante").select2({ templateResult: templateComprobante, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#f_nc_tipo_comprobante").val(null).trigger('change'); // Limpiamos aqui para no generar un bucle infinito

  await activar_btn_agregar(); // Esperamos a al carga total de los datos para poder: CREAR
}

async function activar_btn_agregar() {
  $(".btn-agregar").show();
}

function templateCliente (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../assets/modulo/bancos/${state.title}`: '../assets/modulo/bancos/logo-sin-banco.svg'; 
  var onerror = `onerror="this.src='../assets/modulo/bancos/logo-sin-banco.svg';"`;
  var $state = $(`<span class="fs-11" > ${state.text}</span>`);
  return $state;
}

function templateBanco (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../assets/modulo/bancos/${state.title}`: '../assets/modulo/bancos/logo-sin-banco.svg'; 
  var onerror = `onerror="this.src='../assets/modulo/bancos/logo-sin-banco.svg';"`;
  var $state = $(`<span><img src="${baseUrl}" class="img-circle mr-2 w-25px" ${onerror} />${state.text}</span>`);
  return $state;
};

function templateComprobante (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../dist/docs/persona/perfil/${state.title}`: '../dist/svg/user_default.svg'; 
  var onerror = `onerror="this.src='../dist/svg/user_default.svg';"`;
  var $state = $(`<span class="fs-11" > ${state.text}</span>`);
  return $state;
}

function templateSerieNumero (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var dif_fecha_emision = state.title != '' ? `<span class="fs-9">${state.title}</span>`: '-';  
  var $state = $(`<span class="fs-12" >${state.text} &raquo; ${dif_fecha_emision}</span>`);
  return $state;
}

function show_hide_form(flag) {
	if (flag == 1) {        // TABLA PRINCIPAL
    if (localStorage.getItem('nube_cargo') == 'VENDEDOR') {
      $("#div-tabla").show().removeClass('col-xl-9').addClass('col-xl-12');
      $("#div-mini-reporte").hide();
    } else {
      $("#div-tabla").show().removeClass('col-xl-12').addClass('col-xl-9');
      $("#div-mini-reporte").show();      
    }		
    
		$("#div-formulario").hide();
    $("#div-tabla-mas-detalles").hide();

		$(".btn-agregar").show();
		$(".btn-guardar").hide();
		$(".btn-cancelar").hide();
		
	} else if (flag == 2) { // FORMULARIO FACTURACION
		$("#div-tabla").hide();
    $("#div-mini-reporte").hide();
		$("#div-formulario").show();
    $("#div-tabla-mas-detalles").hide();

		$(".btn-agregar").hide();		
		$(".btn-cancelar").show();

  } else if (flag == 3) { // TABLA MAS DETALLE FACTURACION
		$("#div-tabla").hide();
    $("#div-mini-reporte").hide();
		$("#div-formulario").hide();
		$("#div-tabla-mas-detalles").show();

		$(".btn-agregar").hide();		
		$(".btn-cancelar").show();
	}
}

function mini_reporte() {

  $("#html_top_5_productos").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);

  var periodo_facturado = $("#filtro-periodo").val(), filtro_trabajador = $("#filtro-trabajador").val();

  $.getJSON(`../ajax/facturacion.php?op=mini_reporte`, {periodo_facturado, filtro_trabajador}, function (e, textStatus, jqXHR) {
    
    if (e.status == true) {     
      $("#html_top_5_productos").html('');
      e.data.data_producto.forEach( (val, key) => {
        $("#html_top_5_productos").append( ` <div class="card custom-card mb-2">
          <div class="card-body py-2">
            <div class="d-flex align-items-top">
              <div class="me-3">
                <span class="avatar avatar-md p-1 bg-primary">
                  <img src="../assets/modulo/productos/${val.imagen == null || val.imagen == "" ? 'no-producto-white.png' : val.imagen }" alt="">                                  
                </span>
              </div>
              <div class="flex-fill">
                <div class="d-flex mb-1 align-items-top justify-content-between">
                  <h5 class="fw-semibold mb-0 lh-1">${formato_miles(val.total_vendido_mes_actual)}</h5>
                  <div class="text-danger fw-semibold"><i class="ri-arrow-down-s-fill me-1 align-middle"></i>${val.porcentaje_incremento}%</div>
                </div>
                <p class="mb-0 fs-10 op-7 text-muted fw-semibold">${val.nombre_categoria} ${val.nombre_producto} - S/. ${val.precio_promedio_mes_actual} </p>
              </div>
            </div>
          </div>
        </div>` );
      });
      
      if (chart_barra) { chart_barra.destroy(); }

      // MINI CHART
      var options_ultimos_6_meses = {
        series: e.data.data_line,
        chart: { type: 'bar', height: 210, stacked: true },
        plotOptions: { bar: { horizontal: false, columnWidth: '25%', endingShape: 'rounded', }, },
        grid: { borderColor: '#f2f5f7', },
        dataLabels: { enabled: false },
        colors: ["#4b9bfa", "#28d193", "#ffbe14", "#f3f6f8", "#9c27b0"],
        stroke: { show: true, colors: ['transparent'] },
        xaxis: {
          categories: e.data.mes_nombre,
          labels: {
            show: true,
            style: { colors: "#8c9097", fontSize: '11px', fontWeight: 600, cssClass: 'apexcharts-xaxis-label', },
          }
        },
        yaxis: {
          title: { style: { color: "#8c9097", } },
          labels: {
            show: true,
            style: { colors: "#8c9097", fontSize: '11px', fontWeight: 600, cssClass: 'apexcharts-xaxis-label', },
          }
        },
        fill: { opacity: 1 },
        tooltip: { y: {  formatter: function (val) { return "cant " + val ; } } }
      };
      chart_barra = new ApexCharts(document.querySelector("#invoice-list-stats"), options_ultimos_6_meses);
      chart_barra.render();

      
    } else {
      ver_errores(e);
    }

  }).fail( function(e) { ver_errores(e); } );  
}

function mini_reporte_v2() {
  $('#avance-plan tbody').html(`<tr><td colspan="3" class="" ><div class="text-center my-3"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"></div></div></td></tr>`);

  $.getJSON(`../ajax/facturacion.php?op=mini_reporte_v2`, { filtro_periodo:$("#filtro-periodo").val(), filtro_trabajador:$("#filtro-trabajador").val() }, function (e, textStatus, jqXHR) {
    
    $('.total_avance_cobrado').html(`${e.data.total.cant_cobrado}`);
    $('.total_avance_cobrado_porcent').html(`${ redondearExp(e.data.total.avance, 1)}%`);
    $('.total_avance_por_cobrar').html(`${  (e.data.total.cant_total - e.data.total.cant_cobrado) }`);
    $('.total_avance_por_cobrar_porcent').html(`${ redondearExp( (100 - e.data.total.avance), 1) }%`);

    $('#avance-plan tbody').html('');
    e.data.centro_poblado.forEach((val, key) => {
      $('#avance-plan tbody').append(`
        <tr>
          <td class="py-1 ">
            <div class="d-flex align-items-center">
              <span class="avatar avatar-rounded avatar-sm p-2  me-2">
                <i class="ri-map-pin-line fs-15 text-primary"></i>
              </span>
              <div class="fw-semibold fs-10">${val.centro_poblado} (${val.cant_cobrado}/${val.cant_total})</div>
            </div>
          </td>                          
          <td class="py-1 ">
            <div class="progress progress-xs">
              <div class="progress-bar ${(val.avance == 100 ? 'bg-success' : 'bg-primary')}" role="progressbar" style="width: ${val.avance}%" aria-valuenow="${val.avance}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <span class="fs-10"><i class="ri-arrow-up-s-fill me-1 text-success align-middle "></i>${val.avance}%</span>
          </td>
          
        </tr>
      `);
    });

  }).fail( function(e) { ver_errores(e); } );
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   F A C T U R A C I O N :::::::::::::::::::::::::::::::::::::::::::::

// abrimos el navegador de archivos
$("#doc1_i").click(function () { $('#doc1').trigger('click'); });
$("#doc1").change(function (e) { addImageApplication(e, $("#doc1").attr("id"), null, '100%', '300px', true) });

function doc1_eliminar() {
  $("#doc1").val("");
  $("#doc1_ver").html('<img src="../assets/images/default/img_defecto2.png" alt="" width="78%" >');
  $("#doc1_nombre").html("");
}

function limpiar_form_venta(){

  array_data_venta = [];
  $("#f_idventa").val('');
  $("#f_nc_idventa").val('0');

  $("#f_idpersona_cliente").val('').trigger('change'); 
  $("#f_metodo_pago").val('').trigger('change'); 
  $("#f_observacion_documento").val(''); 
  $("#f_periodo_pago").val('');
  $("#codigob").val('');  
  
  $("#f_total_recibido").val(0);
  $("#f_mp_monto").val(0);
  $("#f_total_vuelto").val(0);
  $("#f_ua_monto_usado").val('');
  $("#f_mp_serie_comprobante").val('');
  file_pond_mp_comprobante.removeFiles();
  $("#f_mp_comprobante_old").val('');

  $(".span_dia_cancelacion").html(``);

  $("#f_venta_total").val("");     
  $(".f_venta_total").html("0");

  $(".f_venta_subtotal").html("<span>S/</span> 0.00");
  $("#f_venta_subtotal").val("");

  $(".f_venta_descuento").html("<span>S/</span> 0.00");
  $("#f_venta_descuento").val("");

  $(".f_venta_igv").html("<span>S/</span> 0.00");
  $("#f_venta_igv").val("");

  $(".f_venta_total").html("<span>S/</span> 0.00");
  $("#f_venta_total").val("");

  $(".filas").remove();

  cont = 0;

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function listar_tabla_facturacion(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_comprobante, filtro_estado_sunat){
  
  tabla_principal_facturacion = $("#tabla-ventas").dataTable({
    // responsive: true, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_principal_facturacion) { tabla_principal_facturacion.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3,4,5,6,7,10], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3,4,5,6,7,10], }, title: 'Lista de ventas', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3,4,5,6,7,10], }, title: 'Lista de ventas', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/facturacion.php?op=listar_tabla_facturacion&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}&filtro_cliente=${filtro_cliente}&filtro_comprobante=${filtro_comprobante}&filtro_estado_sunat=${filtro_estado_sunat}`,
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
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
		},
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      // columna: Opciones
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center"); }
      // columna: Cliente
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[5] != '') { $("td", row).eq(5).addClass("text-nowrap"); }
      // columna: Monto
      if (data[7] != '') { $("td", row).eq(7).addClass("text-nowrap"); }
      // columna: Monto
      if (data[8] != '') { $("td", row).eq(8).addClass("text-nowrap text-center"); }
      // columna: Boucher
      if (data[9] != '') { $("td", row).eq(9).addClass("text-nowrap text-center"); }
    },
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 7 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 7 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` );       
    },
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "asc"]],
    columnDefs: [      
      { targets: [3], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [7], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function guardar_editar_facturacion(e) {
  var formData = new FormData($("#form-facturacion")[0]);  

  Swal.fire({
    title: "¿Está seguro que deseas guardar esta Venta?",
    html: "Verifica que todos lo <b>campos</b>  esten <b>conformes</b>!!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si, Guardar!",
    preConfirm: (input) => {
      return fetch("../ajax/facturacion.php?op=guardar_editar_facturacion", {
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
        Swal.fire("Correcto!", "Venta guardada correctamente", "success");
        tabla_principal_facturacion.ajax.reload(null, false);
        limpiar_form_venta(); show_hide_form(1); reload_f_nc_serie_y_numero();
        if ($('#f_crear_y_mostrar').is(':checked')) {
          $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-lg modal-xl modal-xxl").addClass("modal-md");          
          var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + result.value.data;
          $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - FACTURA`);
          $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
          $("#modal-imprimir-comprobante").modal("show");
        }
      } else if ( result.value.status == 'error_personalizado'){        
        tabla_principal_facturacion.ajax.reload(null, false);
        limpiar_form_venta(); show_hide_form(1); reload_f_nc_serie_y_numero(); ver_errores(result.value);
      } else if ( result.value.status == 'error_usuario'){    
        ver_errores(result.value);
      } else {
        ver_errores(result.value);
      }      
    }
  });  
}

function mostrar_detalle_venta(idventa){
  $("#modal-detalle-venta").modal("show");

  $.post("../ajax/facturacion.php?op=mostrar_detalle_venta", { idventa: idventa }, function (e, status) {          
      
    $('#custom-tabContent').html(e);      
    $('#custom-datos1_html-tab').click(); // click para ver el primer - Tab Panel
    $(".jq_image_zoom").zoom({ on: "grab" });      
    $("#excel_venta").attr("href",`../reportes/export_xlsx_venta_tours.php?id=${idventa}`);      
    $("#print_pdf_venta").attr("href",`../reportes/comprobante_venta_tours.php?id=${idventa}`);    
    
  }).fail( function(e) { ver_errores(e); } );

}

function eliminar_papelera_venta(idventa, nombre){
  $('.tooltip').remove();
	crud_eliminar_papelera(
    "../ajax/facturacion.php?op=papelera",
    "../ajax/facturacion.php?op=eliminar", 
    idventa, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>venta: ${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_principal_facturacion.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}


// ::::::::::::::::::::::::::::::::::::::::::::: MOSTRAR SERIES :::::::::::::::::::::::::::::::::::::::::::::

function ver_series_comprobante(input) {
  if (cambio_de_tipo_comprobante == '07') { limpiar_form_venta(); } // Limpiamos si el comprobante anterior era: Nota de Credito

  $("#f_serie_comprobante").html('');
  $(".f_charge_serie_comprobante").html(`<div class="spinner-border spinner-border-sm" role="status"></div>`);

  var tipo_comprobante = $(input).val() == ''  || $(input).val() == null ? '' : $(input).val();
  var nc_tp = ""; // En caso de ser Nota de Credito
  $('#f_tipo_comprobante_hidden').val(tipo_comprobante);

  $(".div_idpersona_cliente").show();
  $(".div_nc_tipo_comprobante").hide();
  $(".div_nc_serie_y_numero").hide();
  $(".div_nc_motivo_anulacion").hide();
  $(".div_es_cobro").show();
  $(".datos-de-cobro-mensual").show();
  $(".div_agregar_producto").show();
  $(".div_pago_rapido").show();
  $(".div_m_pagos").show();
  $(".div_usar_anticipo").show();
  $(".datos-de-saldo").hide();

  // VALIDANDO SEGUN: TIPO DE COMPROBANTE
  if (form_validate_facturacion) { // FORM-VALIDATE
  
    if ( tipo_comprobante == '01') {   
      $("#f_idsunat_c01").val(2); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      $("#f_periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_metodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } });       
    } else if ( tipo_comprobante == '03') {
      $("#f_idsunat_c01").val(3); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      $("#f_periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_metodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
    } else if ( tipo_comprobante == '07') {
      var nc_tipo_comprobante = $('#f_nc_tipo_comprobante').val() == '' || $('#f_nc_tipo_comprobante').val() == null ? '' : $('#f_nc_tipo_comprobante').val();
      if (nc_tipo_comprobante == '01') {
        $("#f_idsunat_c01").val(7); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      } else if (nc_tipo_comprobante == '03') {
        $("#f_idsunat_c01").val(8); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante
      }
      
      var nc_tp = $('#f_nc_tipo_comprobante').val() == ''  || $('#f_nc_tipo_comprobante').val() == null ? '' : $('#f_nc_tipo_comprobante').val();
      $("#f_periodo_pago").rules('remove', 'required');
      $("#f_metodo_pago").rules('remove', 'required');
      $('#f_es_cobro_inp').val('NO'); $('#f_usar_anticipo').val('NO');

      $(".div_idpersona_cliente").hide();
      $(".div_nc_tipo_comprobante").show();
      $(".div_nc_serie_y_numero").show();
      $(".div_nc_motivo_anulacion").show();
      $(".div_es_cobro").hide();
      $(".datos-de-cobro-mensual").hide();
      $(".div_agregar_producto").hide();
      $(".div_pago_rapido").hide();
      $(".div_m_pagos").hide();
      $(".div_usar_anticipo").hide();
      $(".datos-de-saldo").hide();
    } else if ( tipo_comprobante == '12') {
      $("#f_idsunat_c01").val(12); // Asginamos el ID manualmente de: sunat_c01_tipo_comprobante      
      $("#f_periodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
      $("#f_metodo_pago").rules('add', { required: true, messages: { required: 'Campo requerido' } }); 
    }
  }

  $.getJSON("../ajax/facturacion.php?op=select2_series_comprobante", { tipo_comprobante: tipo_comprobante, nc_tp:nc_tp },  function (e, status) {    
    if (e.status == true) {      
      $("#f_serie_comprobante").html(e.data);
      $(".f_charge_serie_comprobante").html('');
      $("#form-facturacion").valid();
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );

  cambio_de_tipo_comprobante = tipo_comprobante;
}

// ::::::::::::::::::::::::::::::::::::::::::::: MOSTRAR COBROS :::::::::::::::::::::::::::::::::::::::::::::
function es_cobro_valid() { console.log($(".es_cobro").hasClass("on"));
  var tipo_comprobante = $('#f_tipo_comprobante_hidden').val() == ''  || $('#f_tipo_comprobante_hidden').val() == null ? '' : $('#f_tipo_comprobante_hidden').val();
  if ($(".es_cobro").hasClass("on") == true) {
    $("#f_es_cobro_inp").val("SI");
    $(".datos-de-cobro-mensual").show("slow");   
    if (form_validate_facturacion) { 
      if ( tipo_comprobante == '07') { } else{
        $("#f_periodo_pago").rules('add', { required: true, messages: {  required: "Campo requerido" } });
      }
    }
  } else {
    $("#f_es_cobro_inp").val("NO");
    $(".datos-de-cobro-mensual").hide("slow");
    if (form_validate_facturacion) { $("#f_periodo_pago").rules('remove', 'required'); }
  }
  $("#form-facturacion").valid();
}

// ::::::::::::::::::::::::::::::::::::::::::::: MOSTRAR ANTICIPOS :::::::::::::::::::::::::::::::::::::::::::::
function usar_anticipo_valid() { 
  $("#f_ua_monto_usado").val('');

  var id_cliente = $('#f_idpersona_cliente').val() == ''  || $('#f_idpersona_cliente').val() == null ? '' : $('#f_idpersona_cliente').val(); 

  if ($(".f_usar_anticipo").hasClass("on") == true) {

    $("#f_usar_anticipo").val("SI");
    $(".datos-de-saldo").show("slow");
    if (id_cliente != '') {
      $.getJSON(`../ajax/facturacion.php?op=mostrar_anticipos`, {id_cliente:id_cliente}, function (e, textStatus, jqXHR) {
        $("#f_ua_monto_disponible").val(e.data.total_anticipo);
        if (form_validate_facturacion) { $("#f_ua_monto_usado").rules('add', { required: true, max: parseFloat(e.data.total_anticipo) , messages: {  required: "Campo requerido", max: "Saldo disponible: {0}" } }); }
        $("#form-facturacion").valid();
      }).fail( function(e) { ver_errores(e); } );
    }
  } else {
    $("#f_usar_anticipo").val("NO");
    $(".datos-de-saldo").hide("slow");
    if (form_validate_facturacion) { $("#f_ua_monto_usado").rules('remove', 'required'); }
    $("#form-facturacion").valid();
  }
}

// ::::::::::::::::::::::::::::::::::::::::::::: CLIENTE VALIDO :::::::::::::::::::::::::::::::::::::::::::::
function es_valido_cliente() {

  var id_cliente = $('#f_idpersona_cliente').val() == ''  || $('#f_idpersona_cliente').val() == null ? '' : $('#f_idpersona_cliente').val();
  $(".span_dia_cancelacion").html(``);

  if (id_cliente != null && id_cliente != '') {

    var tipo_comprobante = $('#f_tipo_comprobante_hidden').val() == ''  || $('#f_tipo_comprobante_hidden').val() == null ? '' : $('#f_tipo_comprobante_hidden').val();
    var tipo_documento    = $('#f_idpersona_cliente').select2('data')[0].element.attributes.tipo_documento.value;
    var numero_documento  = $('#f_idpersona_cliente').select2('data')[0].element.attributes.numero_documento.value;
    var direccion         = $('#f_idpersona_cliente').select2('data')[0].element.attributes.direccion.value;  
    // var dia_cancelacion = $('#f_idpersona_cliente').select2('data')[0].element.attributes.dia_cancelacion.value;  
    var campos_requeridos = ""; 
    var es_valido = true; 
    
    if (id_cliente != '1') {
      $(".span_dia_cancelacion").html(`(${dia_cancelacion} de cada mes.)`); // obtenemos la fecha de cancelacion
    }    

    if (tipo_comprobante == '01') {       // FACTURA
      
      if ( tipo_documento == '6'  ) { }else{ campos_requeridos = campos_requeridos.concat(`<li>Tipo de Documento: RUC</li>`);  }
      if ( numero_documento != '' ) { }else{ campos_requeridos = campos_requeridos.concat(`<li>Numero de Documento</li>`);  }
      if ( direccion != '' ) {    }else{  campos_requeridos = campos_requeridos.concat(`<li>Direccion</li>`);  }
      if (tipo_documento == '6' && numero_documento != '' && direccion != '' ) {  es_valido = true;  } else {   es_valido = false; }

    } else if (tipo_comprobante == '03' || id_cliente == '1') {  // BOLETA
      
      if ( tipo_documento == '1' || tipo_documento == '6' ) {  }else{  campos_requeridos = campos_requeridos.concat(`<li>Tipo de Documento: DNI o RUC</li>`);  }
      if ( numero_documento != '' ) {  }else{  campos_requeridos = campos_requeridos.concat(`<li>Numero de Documento</li>`);  }
      if ( direccion == '' || direccion == null ) {  campos_requeridos = campos_requeridos.concat(`<li>Direccion</li>`);  }else{    }
      if ( (tipo_documento == '1' || tipo_documento == '6' || tipo_documento == '0' ) && numero_documento != ''  ) { es_valido = true; } else {  es_valido = false; }

    } else if (tipo_comprobante == '12' ) { // TICKET
      es_valido = true;
    }

    if (es_valido == true) {
     
    } else {
      sw_cancelar('Cliente no permitido', `El cliente no cumple con los siguientes requsitos:  <ul class="pt-3 text-left font-size-13px"> ${campos_requeridos} </ul>`, 10000);
      $("#f_idpersona_cliente").val('').trigger('change'); 
      $(".span_dia_cancelacion").html(``);
    }   
    
    console.log(tipo_comprobante, tipo_documento, numero_documento, direccion, es_valido);
  }    
}

// ::::::::::::::::::::::::::::::::::::::::::::: FORMATOS DE IMPRESION :::::::::::::::::::::::::::::::::::::::::::::

function ver_formato_ticket(idventa, tipo_comprobante) {
  $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-lg modal-xl modal-xxl").addClass("modal-md");
  if (tipo_comprobante == '01') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - FACTURA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '03') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - BOLETA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '07') {
    var rutacarpeta = "../reportes/TicketNotaCredito.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - NOTA CREDITO`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '12') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");

  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
  }
}

function ver_formato_a4_comprimido(idventa, tipo_comprobante) {
  $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-md modal-lg modal-xxl").addClass("modal-xl");
  if (tipo_comprobante == '01') {
    var rutacarpeta = "../reportes/A4Comprimido.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - FACTURA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '03') {
    var rutacarpeta = "../reportes/A4Comprimido.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - BOLETA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '07') {
    toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
  } else if (tipo_comprobante == '12') {
    var rutacarpeta = "../reportes/A4Comprimido.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");

  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
  }
}

function ver_formato_a4_completo(idventa, tipo_comprobante) {  
  $("#modal-imprimir-comprobante .modal-dialog").removeClass("modal-sm modal-md modal-lg modal-xxl").addClass("modal-xl");
  if (tipo_comprobante == '01') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - FACTURA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '03') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - BOLETA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '07') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '12') {
    var rutacarpeta = "../reportes/A4FormatHtml.php?id=" + idventa;
    $("#modal-imprimir-comprobante-label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir A4" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO A4 - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");

  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
  }  
  
}


// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   N O T A   D E   C R E D I T O :::::::::::::::::::::::::::::::::::::::::::::

function buscar_comprobante_anular() { 
  $('.charge_f_nc_serie_y_numero').html('<div class="spinner-border spinner-border-sm" role="status"></div>');
  var tipo_comprobante = $('#f_nc_tipo_comprobante').val() == '' || $('#f_nc_tipo_comprobante').val() == null ? '' : $('#f_nc_tipo_comprobante').val();
  var tipo_comprobante_hidden = $('#f_tipo_comprobante_hidden').val() == '' || $('#f_tipo_comprobante_hidden').val() == null ? '' : $('#f_tipo_comprobante_hidden').val();

  if (tipo_comprobante_hidden == '07') {   
    ver_series_comprobante('#f_tipo_comprobante07');
  }  

  $.getJSON(`../ajax/facturacion.php?op=select2_comprobantes_anular`, {tipo_comprobante: tipo_comprobante}, function (e, textStatus, jqXHR) {
    if (e.status == true) {
      $("#f_nc_serie_y_numero").html(e.data);
      $("#f_nc_serie_y_numero").val(null).trigger('change');
      $('.charge_f_nc_serie_y_numero').html('');
    } else {
      ver_errores(e);
    }
    
  }).fail( function(e) { ver_errores(e); } );
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   V E R   E S T A D O   S U N A T  :::::::::::::::::::::::::::::::::::::::::::::

function ver_estado_documento(idventa, tipo_comprobante) {
  if (tipo_comprobante == '01'  || tipo_comprobante == '03' || tipo_comprobante == '07' ) {
   
    $("#html-ver-estado").html(`<div class="row" >
      <div class="col-lg-12 text-center">
        <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
        <h4 class="bx-flashing">Cargando...</h4>
      </div>
    </div>`);
    $("#modal-ver-estado").modal('show');
    $.getJSON(`../ajax/facturacion.php?op=ver_estado_documento`, {idventa: idventa}, function (e, textStatus, jqXHR) {
      if (e.status == true) {
        $("#modal-ver-estado-label").html(`─ VER ESTADO: ${e.data.serie_comprobante}-${e.data.numero_comprobante}`);
        $("#html-ver-estado").html(`
          <b>Estado:</b> ${e.data.sunat_estado} <br>
          <b>Mensaje:</b> ${e.data.sunat_mensaje} <br>
          <b>Observacion:</b> ${e.data.sunat_observacion} <br>
          <b>Codigo:</b> ${e.data.sunat_code} <br>
          <b>Error:</b> ${e.data.sunat_error} <br>
        `);
      } else {
        ver_errores(e);
      }      
    }).fail( function(e) { ver_errores(e); } );
  } else {  
    toastr_warning('Sin estado SUNAT', 'Este documento no tiene una respuesta de sunat, teniendo en cuenta que es un documento interno de control de la empresa.');
  }
}

function reenviar_doc_a_sunat(idventa, tipo_comprobante) {
  if (tipo_comprobante == '01'  || tipo_comprobante == '03' || tipo_comprobante == '07' ) {
    
    Swal.fire({
      title: "¿Está seguro de reenviar a SUNAT?",
      html: "Verifica que todos lo <b>campos</b> hayan sido actualizados o esten <b>conformes</b>!!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#28a745",
      cancelButtonColor: "#d33",
      confirmButtonText: "Si, Enviar!",
      preConfirm: (input) => {
        return fetch(`../ajax/facturacion.php?op=reenviar_sunat&idventa=${idventa}&tipo_comprobante=${tipo_comprobante}`).then(response => {
          //console.log(response);
          if (!response.ok) { throw new Error(response.statusText) }
          return response.json();
        }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); })        
      },
      showLoaderOnConfirm: true,
    }).then((result) => {
      if (result.isConfirmed) {
        if (result.value.status == true){        
          Swal.fire("Correcto!", "Documento actualizado correctamente.", "success");
          tabla_principal_facturacion.ajax.reload(null, false);               
        } else {
          ver_errores(result.value);
        }      
      }
    }); 
    
  } else {  
    toastr_warning('Sin respuesta!!', 'Este documento no tiene una respuesta de sunat, teniendo en cuenta que es un documento interno de control de la empresa.');
  }
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

function listar_tabla_producto(tipo = 'PR'){
  $("#modal-producto").modal('show');
  $("#title-modal-producto-label").html( (tipo == 'PR' ? 'Seleccionar Producto' : 'Seleccionar Servicio') );
  tabla_productos = $("#tabla-productos").dataTable({
    responsive: false, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_productos) { tabla_productos.ajax.reload(null, false); } } },
    ],
    ajax: {
      url: `../ajax/facturacion.php?op=listar_tabla_producto&tipo_producto=${tipo}`,
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
  filtros();
});

function mayus(e) { 
  e.value = e.value.toUpperCase(); 
}

// ::::::::::::::::::::::::::::::::::::::::::::: S E C C I O N   MAS DETALLES FACTURACION :::::::::::::::::::::::::::::::::::::::::::::
var active_filtro_md = false;
var filtro_estado_sunat_md = '';
function view_mas_detalle() {
  active_filtro_md = true;
  var filtro_fecha_i      = $("#filtro_md_fecha_i").val();
  var filtro_fecha_f      = $("#filtro_md_fecha_f").val();  
  var filtro_cliente      = $("#filtro_md_cliente").select2('val') == null ? '' : $("#filtro_md_cliente").select2('val');
  var filtro_comprobante  = $("#filtro_md_comprobante").select2('val')== null ? '' : $("#filtro_md_comprobante").select2('val');
  show_hide_form(3);
  tbla_mas_detalle(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_comprobante, filtro_estado_sunat_md)
}

function tbla_mas_detalle(filtro_fecha_i='', filtro_fecha_f='', filtro_cliente='', filtro_comprobante='', filtro_estado_sunat='') {
  tabla_ver_mas_detalle_facturacion = $("#tabla-facturacion-detalle").dataTable({
    // responsive: true, 
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]], //mostramos el menú de registros a revisar
    aProcessing: true, //Activamos el procesamiento del datatables
    aServerSide: true, //Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>", //Definimos los elementos del control de tabla
    buttons: [  
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', classtbla_mas_detalle: "thf buttons-reload btn btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_ver_mas_detalle_facturacion) { tabla_ver_mas_detalle_facturacion.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13], }, title: 'Lista de ventas', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13], }, title: 'Lista de ventas', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/facturacion.php?op=listar_tabla_ver_mas_detalle_facturacion&filtro_fecha_i=${filtro_fecha_i}&filtro_fecha_f=${filtro_fecha_f}&filtro_cliente=${filtro_cliente}&filtro_comprobante=${filtro_comprobante}&filtro_estado_sunat=${filtro_estado_sunat}`,
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
    createdRow: function (row, data, ixdex) {
      // columna: #
      if (data[0] != '') { $("td", row).eq(0).addClass("text-center"); }
      // columna: Opciones
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center"); }
      // columna: Opciones
      if (data[2] != '') { $("td", row).eq(2).addClass("fs-11 text-nowrap text-center"); }
      // columna: Cliente
      if (data[4] != '') { $("td", row).eq(4).addClass("text-nowrap"); }
      // columna: Monto
      if (data[6] != '') { $("td", row).eq(6).addClass("text-nowrap"); }
      // columna: Monto
      if (data[7] != '') { $("td", row).eq(7).addClass("text-nowrap text-center"); }
    },
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    footerCallback: function( tfoot, data, start, end, display ) {
      var api1 = this.api(); var total1 = api1.column( 9 ).data().reduce( function ( a, b ) { return  (parseFloat(a) + parseFloat( b)) ; }, 0 )
      $( api1.column( 9 ).footer() ).html( `<span class="float-start">S/</span> <span class="float-end">${formato_miles(total1)}</span> ` );       
    },
    "bDestroy": true,
    "iDisplayLength": 25,
    "order": [[0, "desc"]],
    columnDefs: [      
      { targets: [2], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [9,10,11], render: function (data, type) { var number = $.fn.dataTable.render.number(',', '.', 2).display(data); if (type === 'display') { let color = ''; if (data < 0) {color = 'numero_negativos'; } return `<span class="float-start">S/</span> <span class="float-end ${color} "> ${number} </span>`; } return number; }, },      

      // { targets: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19], visible: false, searchable: false, },
    ],
  }).DataTable();
}

function filtrar_solo_estado_sunat_md(estado, etiqueta) {  
  $(".md-otros-filtros").find("li>a").removeClass("active");
  $(".md-otros-filtros").find(`li>a${etiqueta}`).addClass("active"); 
  filtro_estado_sunat_md = estado; filtros_md();
}

function filtros_md() {  
  if (active_filtro_md == true) {    
  
    var filtro_fecha_i      = $("#filtro_md_fecha_i").val();
    var filtro_fecha_f      = $("#filtro_md_fecha_f").val();  
    var filtro_cliente      = $("#filtro_md_cliente").select2('val');
    var filtro_comprobante  = $("#filtro_md_comprobante").select2('val');
    
    var nombre_filtro_fecha_i     = $('#filtro_md_fecha_i').val();
    var nombre_filtro_fecha_f     = ' ─ ' + $('#filtro_md_fecha_f').val();
    var nombre_filtro_cliente     = ' ─ ' + $('#filtro_md_cliente').find(':selected').text();
    var nombre_filtro_comprobante = ' ─ ' + $('#filtro_md_comprobante').find(':selected').text();

    // filtro de fechas
    if (filtro_fecha_i == '' || filtro_fecha_i == 0 || filtro_fecha_i == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }
    if (filtro_fecha_f == '' || filtro_fecha_f == 0 || filtro_fecha_f == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }

    // filtro de cliente
    if (filtro_cliente == '' || filtro_cliente == 0 || filtro_cliente == null) { filtro_cliente = ""; nombre_filtro_cliente = ""; }

    // filtro de comprobante
    if (filtro_comprobante == '' || filtro_comprobante == 0 || filtro_comprobante == null) { filtro_comprobante = ""; nombre_filtro_comprobante = ""; }

    $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${filtro_fecha_i} ${filtro_fecha_f} ${nombre_filtro_cliente}...`);
    //console.log(filtro_categoria, fecha_2, filtro_marca, comprobante);

    tbla_mas_detalle(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_comprobante, filtro_estado_sunat_md);
  }
}

// .....::::::::::::::::::::::::::::::::::::: V A L I D A T E   F O R M  :::::::::::::::::::::::::::::::::::::::..

$(function(){

  form_validate_facturacion = $("#form-facturacion").validate({
    ignore: '',
    rules: {
      f_idpersona_cliente:      { required: true },
      f_tipo_comprobante:       { required: true },
      f_serie_comprobante:      { required: true, },
      f_observacion_documento:  { minlength: 4 },      
      f_metodo_pago:            { required: true},
      f_total_recibido:         { required: true, min: 0, step: 0.01},      
      f_mp_monto:               { required: true, min: 0, step: 0.01},
      f_total_vuelto:           { required: true, step: 0.01},
      f_ua_monto_usado:         { required: true, min: 1, step: 0.01},
      f_mp_serie_comprobante:   { minlength: 4},
      // mp_comprobante:         { extension: "png|jpg|jpeg|webp|svg|pdf",  }, 
    },
    messages: {
      f_idpersona_cliente:      { required: "Campo requerido", },
      f_tipo_comprobante:       { required: "Campo requerido", },      
      f_serie_comprobante:      { required: "Campo requerido", },
      f_observacion_documento:  { minlength: "Minimo {0} caracteres", },
      // mp_comprobante:         { extension: "Ingrese imagenes validas ( {0} )", },
      f_total_recibido:         { step: "Solo 2 decimales."},      
      f_mp_monto:               { step: "Solo 2 decimales."},
      f_total_vuelto:           { step: "Solo 2 decimales."},
      f_ua_monto_usado:         { step: "Solo 2 decimales."},
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
      guardar_editar_facturacion(form);
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

function cargando_search() {
  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ...`);
}

function filtros() {  

  var filtro_fecha_i      = $("#filtro_fecha_i").val();
  var filtro_fecha_f      = $("#filtro_fecha_f").val();  
  var filtro_cliente      = $("#filtro_cliente").select2('val');
  var filtro_comprobante  = $("#filtro_comprobante").select2('val');
  
  var nombre_filtro_fecha_i     = $('#filtro_fecha_i').val();
  var nombre_filtro_fecha_f     = ' ─ ' + $('#filtro_fecha_f').val();
  var nombre_filtro_cliente     = ' ─ ' + $('#filtro_cliente').find(':selected').text();
  var nombre_filtro_comprobante = ' ─ ' + $('#filtro_comprobante').find(':selected').text();

  // filtro de fechas
  if (filtro_fecha_i == '' || filtro_fecha_i == 0 || filtro_fecha_i == null) { filtro_fecha_i = ""; nombre_filtro_fecha_i = ""; }
  if (filtro_fecha_f == '' || filtro_fecha_f == 0 || filtro_fecha_f == null) { filtro_fecha_f = ""; nombre_filtro_fecha_f = ""; }

  // filtro de cliente
  if (filtro_cliente == '' || filtro_cliente == 0 || filtro_cliente == null) { filtro_cliente = ""; nombre_filtro_cliente = ""; }

  // filtro de comprobante
  if (filtro_comprobante == '' || filtro_comprobante == 0 || filtro_comprobante == null) { filtro_comprobante = ""; nombre_filtro_comprobante = ""; }

  $('.buscando_tabla').show().html(`<i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${filtro_fecha_i} ${filtro_fecha_f} ${nombre_filtro_cliente}...`);
  //console.log(filtro_categoria, fecha_2, filtro_marca, comprobante);

  listar_tabla_facturacion(filtro_fecha_i, filtro_fecha_f, filtro_cliente, filtro_comprobante, filtro_estado_sunat);
}

function filtrar_solo_estado_sunat(estado, etiqueta) {  
  $(".otros-filtros").find("li>a").removeClass("active");
  $(".otros-filtros").find(`li>a${etiqueta}`).addClass("active"); 
  filtro_estado_sunat = estado; filtros();
}

function reload_f_idpersona_cliente(){ lista_select2("../ajax/facturacion.php?op=select2_cliente", '#f_idpersona_cliente', null, '.charge_f_idpersona_cliente'); }
function reload_f_nc_serie_y_numero(){ buscar_comprobante_anular() }
function reload_f_nc_motivo_anulacion(){ lista_select2("../ajax/facturacion.php?op=select2_codigo_x_anulacion_comprobante", '#f_nc_motivo_anulacion', '01', '.charge_f_nc_motivo_anulacion'); }
function reload_f_metodo_pago(){ lista_select2("../ajax/facturacion.php?op=select2_banco", '#f_metodo_pago', null, 'charge_f_metodo_pago');   }

function reload_filtro_fecha_i(){ $('#filtro_fecha_i').val("").trigger("change") } 
function reload_filtro_fecha_f(){ $('#filtro_fecha_f').val("").trigger("change") } 
function reload_filtro_cliente(){ lista_select2("../ajax/facturacion.php?op=select2_filtro_cliente", '#filtro_cliente', null, '.charge_filtro_cliente'); } 
function reload_filtro_comprobante(){ lista_select2("../ajax/facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_comprobante', null, '.charge_filtro_comprobante'); }

function reload_filtro_md_fecha_i(){ $('#filtro_md_fecha_i').val("").trigger("change") } 
function reload_filtro_md_fecha_f(){ $('#filtro_md_fecha_f').val("").trigger("change") } 
function reload_filtro_md_cliente(){ lista_select2("../ajax/facturacion.php?op=select2_filtro_cliente", '#filtro_md_cliente', null, '.charge_filtro_md_cliente'); } 
function reload_filtro_md_comprobante(){ lista_select2("../ajax/facturacion.php?op=select2_filtro_tipo_comprobante&tipos='01','03','07','12'", '#filtro_md_comprobante', null, '.charge_filtro_md_comprobante'); }


function printIframe(id) {
  var iframe = document.getElementById(id);
  iframe.focus(); // Para asegurarse de que el iframe está en foco
  iframe.contentWindow.print(); // Llama a la función de imprimir del documento dentro del iframe
}

function ver_img_pefil(id_cliente) {
  $('#modal-ver-imgenes').modal('show');
  $(".html_modal_ver_imgenes").html(`<div class="row" > <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div> </div>`);

  $.post("../ajax/facturacion.php?op=mostrar_cliente", { idcliente: id_cliente },  function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {
      // if (e.data.foto_perfil == "" || e.data.foto_perfil == null) { } else {
        var nombre_comprobante = `${e.data.cliente_nombre_completo} - ${e.data.numero_documento}`;
        var file_comprobante = e.data.foto_perfil ==''||  e.data.foto_perfil == null ? 'no-perfil.jpg' : e.data.foto_perfil;
        $('.title-ver-imgenes').html(nombre_comprobante);
        $(".html_modal_ver_imgenes").html(doc_view_download_expand(file_comprobante, 'assets/modulo/persona/perfil',nombre_comprobante , '100%', '400px'));
        $('.jq_image_zoom').zoom({ on: 'grab' });
      // }
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );
}

function ver_comprobante_pago(id_venta) {
  $('#modal-ver-imgenes').modal('show');
  $(".html_modal_ver_imgenes").html(`<div class="row" > <div class="col-lg-12 text-center"> <div class="spinner-border me-4" style="width: 3rem; height: 3rem;"role="status"></div> <h4 class="bx-flashing">Cargando...</h4></div> </div>`);

  $.post("../ajax/facturacion.php?op=mostrar_venta", { idventa: id_venta },  function (e, status) {
    e = JSON.parse(e);
    if (e.status == true) {
      if (e.data.mp_comprobante == "" || e.data.mp_comprobante == null) { } else {
        var nombre_comprobante = `${e.data.metodo_pago} - ${e.data.mp_serie_comprobante}`;
        var file_comprobante = e.data.mp_comprobante ==''||  e.data.mp_comprobante == null ? '' : e.data.mp_comprobante;
        $('.title-ver-imgenes').html(nombre_comprobante);
        $(".html_modal_ver_imgenes").html(doc_view_download_expand(file_comprobante, 'assets/modulo/facturacion/ticket',nombre_comprobante , '100%', '400px'));
        $('.jq_image_zoom').zoom({ on: 'grab' });
      }
    } else { ver_errores(e); }
  }).fail( function(e) { ver_errores(e); } );
  
}

(function () {
  "use strict"  

  // UPLOADS ===================================

  /* filepond */
  FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginImageExifOrientation,
    FilePondPluginFileValidateSize,
    FilePondPluginFileEncode,
    FilePondPluginImageEdit,
    FilePondPluginFileValidateType,
    FilePondPluginImageCrop,
    FilePondPluginImageResize,
    FilePondPluginImageTransform,
      
  );

  /* multiple upload */
  const MultipleElement = document.querySelector('.multiple-filepond');
  file_pond_mp_comprobante = FilePond.create(MultipleElement, FilePond_Facturacion_LabelsES );

  // Ensure mediumZoom is available before using it
  document.addEventListener("DOMContentLoaded", function() {
    file_pond_mp_comprobante.on('addfile', (error, file) => {
      if (!error) {
        setTimeout(() => {
          mediumZoom('.filepond--image-preview');
        }, 100); // Delay to ensure image is rendered
      }
    });
  });

})();
