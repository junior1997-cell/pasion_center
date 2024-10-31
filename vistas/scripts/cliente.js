var tabla_cliente;
var tabla_pagos_all_cliente;

var form_validate_facturacion;
var array_data_venta = [];
var cambio_de_tipo_comprobante ;
var file_pond_mp_comprobante;

//Función que se ejecuta al inicio
function init() {

  $(".btn-tiket").click();   // Selecionamos la BOLETA

  $(".btn-guardar").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-cliente").submit(); } else { toastr_warning("Espera", "Procesando Datos", 3000); } });
  $(".btn-guardar-cobro").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-venta").submit(); } else { toastr_warning("Espera", "Procesando Datos", 3000); } });

  // ══════════════════════════════════════  S E L E C T 2 ══════════════════════════════════════ 

  lista_select2("../ajax/cliente.php?op=select2_filtro_mes_afiliacion",   '#filtro_mes_afiliacion',       null, '.charge_filtro_mes_afiliacion');
  lista_select2("../ajax/cliente.php?op=select2_filtro_distrito", '#filtro_distrito', null, '.charge_filtro_distrito');  

  //lista_select2("../ajax/facturacion.php?op=select2_banco", '#f_metodo_pago', null, 'charge_f_metodo_pago');

  lista_select2("../ajax/ajax_general.php?op=select2_tipo_documento", '#tipo_documento', null);
  lista_select2("../ajax/ajax_general.php?op=select2_distrito", '#distrito', null);

  lista_select2("../ajax/cliente.php?op=selec_centroProbl", '#idselec_centroProbl', null);
  
  // ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T 2 ══════════════════════════════════════  
  
  $("#filtro_mes_afiliacion").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_distrito").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#filtro_p_all_trabajador").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_anio_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_dia_pago").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_plan").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#filtro_p_all_zona_antena").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  
  $("#tipo_documento").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#distrito").select2({ templateResult: templateDistrito, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  $("#tipo_persona_sunat").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
  $("#idselec_centroProbl").select2({ theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });

  //$("#f_metodo_pago").select2({ templateResult: templateBanco, theme: "bootstrap4", placeholder: "Seleccione", allowClear: true, });
}

function templateDistrito (state) {
  //console.log(state);
  if (!state.id) { return state.text; } 
  var $state = $(`<span class="fs-11" > ${state.text}</span><span class="fs-9" > (${state.title})</span>`);
  return $state;
}

/*function templateBanco (state) {
  //console.log(state);
  if (!state.id) { return state.text; }
  var baseUrl = state.title != '' ? `../assets/modulo/bancos/${state.title}`: '../assets/modulo/bancos/logo-sin-banco.svg'; 
  var onerror = `onerror="this.src='../assets/modulo/bancos/logo-sin-banco.svg';"`;
  var $state = $(`<span><img src="${baseUrl}" class="img-circle mr-2 w-25px" ${onerror} />${state.text}</span>`);
  return $state;
};*/

//Función limpiar
function limpiar_cliente() {

  $("#guardar_registro_cliente").html('Guardar Cambios').removeClass('disabled');

  $("#idpersona").val('');
  $("#idpersona_cliente").val('');

  $("#tipo_persona_sunat").val('').trigger("change");
  $("#tipo_documento").val('').trigger("change");
  $("#numero_documento").val("");
  $("#nombre_razonsocial").val("");
  $("#apellidos_nombrecomercial").val("");
  $("#fecha_nacimiento").val("");
  $("#celular").val("");
  $("#correo").val("");

  $("#direccion").val("");
  $("#distrito").val('TARAPOTO').trigger("change");
  

  $("#idselec_centroProbl").val('').trigger("change");
  $("#fecha_afiliacion").val("");
  $("#nota").val("");

  $("#imagen").val("");
  $("#imagenactual").val("");
  $("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/no-perfil.jpg");
  $("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/no-perfil.jpg").show();
  var imagenMuestra = document.getElementById('imagenmuestra');
  if (!imagenMuestra.src || imagenMuestra.src == "") {
    imagenMuestra.src = '../assets/modulo/usuario/perfil/no-perfil.jpg';
  }

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();

}

function wiev_tabla_formulario(flag) {

  if (flag == 1) {                     // LISTA CLIENTES   
    $("#div-tabla-principal").show();
    $("#div-form-cliente").hide();
    $("#div-ver-cobro-x-cliente").hide();
    $("#div-ver-cobro-all-cliente").hide();
    $("#div-realizar-pago").hide();

    $(".btn-agregar").show();
    $(".btn-pagos-all").show();
    $(".btn-guardar").hide();
    $(".btn-guardar-cobro").hide();
    $(".btn-cancelar").hide();
    limpiar_cliente();
    $('.title-body-pagina').html(`Lista de clientes!`);

  } else if (flag == 2) {               // EDITAR O REGISTRA UN CLIENTE   
    $("#div-tabla-principal").hide();
    $("#div-form-cliente").show();
    $("#div-ver-cobro-x-cliente").hide();
    $("#div-ver-cobro-all-cliente").hide();
    $("#div-realizar-pago").hide();

    $(".btn-agregar").hide();
    $(".btn-pagos-all").hide();
    $(".btn-guardar").show();
    $(".btn-guardar-cobro").hide();
    $(".btn-cancelar").show();
  }

}

//nombres segun el tipo de doc
$('#tipo_documento').change(function () {
  var tipo = $(this).val() == '' ||  $(this).val() == null ? '' : $(this).val() ;

  if (tipo == '' || tipo == '0' ) { 
    $("#numero_documento").rules('remove', 'required minlength');
    $('.nombre_razon').html('Nombres <sup class="text-danger">*</sup>');
    $('.apellidos_nombrecomer').html('Apellidos <sup class="text-danger">*</sup>');
  }else if (tipo == '1') {
    $("#numero_documento").rules('add', { required: true, minlength: 8, maxlength: 8, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 
    $('.nombre_razon').html('Nombres <sup class="text-danger">*</sup>');
    $('.apellidos_nombrecomer').html('Apellidos <sup class="text-danger">*</sup>');
  }else if (tipo == '6') {
    $("#numero_documento").rules('remove', 'required');
    $('.nombre_razon').html('Razón Social <sup class="text-danger">*</sup>');
    $('.apellidos_nombrecomer').html('Nombre comercial <sup class="text-danger">*</sup>');
    $("#numero_documento").rules('add', { required: true, minlength: 11, maxlength: 11, messages: { required: 'Campo requerido', minlength: 'Mínimo {0}', maxlength: 'Máximo {0}' } }); 

  } 

  $("#form-agregar-cliente").valid();

});


function llenar_dep_prov_ubig(input) {

  $(".chargue-pro").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);
  $(".chargue-dep").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);
  $(".chargue-ubi").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`);

  if ($(input).select2("val") == null || $(input).select2("val") == '') {
    $("#departamento").val("");
    $("#provincia").val("");
    $("#ubigeo").val("");

    $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
  } else {
    var iddistrito = $(input).select2('data')[0].element.attributes.iddistrito.value;
    $.post(`../ajax/ajax_general.php?op=select2_distrito_id&id=${iddistrito}`, function (e) {
      e = JSON.parse(e); 
      $("#departamento").val(e.data.departamento);
      $("#provincia").val(e.data.provincia);
      $("#ubigeo").val(e.data.ubigeo_inei);

      $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
      $("#form-agregar-cliente").valid();
    });
  }
}

//Función Listar
function tabla_principal_cliente( filtro_mes_afiliacion, filtro_distrito) {

  tabla_cliente = $('#tabla-cliente').dataTable({
    lengthMenu: [[-1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200,]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom: "<'row'<'col-md-3'B><'col-md-3 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload btn btn-outline-info btn-wave ", action: function (e, dt, node, config) { if (tabla_cliente) { tabla_cliente.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,9, 10, 11, 3, 5, 6, 7], }, text: `<i class="fas fa-copy" ></i>`, className: "btn btn-outline-dark btn-wave ", footer: true, },
      { extend: 'excel', exportOptions: { columns: [0,9, 10, 11, 3, 5, 6, 7], }, title: 'Lista de Clientes', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "btn btn-outline-success btn-wave ", footer: true, },
      { extend: 'pdf', exportOptions: { columns: [0,9, 10, 11, 3, 5, 6, 7], }, title: 'Lista de Clientes', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "btn btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL', },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "btn btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax: {
      url: `../ajax/cliente.php?op=tabla_principal_cliente&filtro_mes_afiliacion=${filtro_mes_afiliacion}&filtro_distrito=${filtro_distrito}`,
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
        $('#id_buscando_tabla').remove();
      },
      dataSrc: function (e) {
				if (e.status != true) {  ver_errores(e); }  return e.aaData;
			},
    },
    createdRow: function (row, data, ixdex) {
      // columna: Acciones
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap"); }
      // columna: Cliente
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
    },
    language: {
      lengthMenu: "Mostrar: _MENU_ registros",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      sLoadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...'
    },
    "bDestroy": true,
    "iDisplayLength": 10,//Paginación
    "order": [[0, "asc"]], //Ordenar (columna,orden)
    columnDefs: [
      // { targets: [5], render: $.fn.dataTable.render.moment('YYYY-MM-DD', 'DD/MM/YYYY'), },
      { targets: [9,10, 11], visible: false, searchable: false, },
    ],
  }).DataTable();
}

//Función para guardar o editar
function guardar_y_editar_cliente(e) {
  // e.preventDefault(); //No se activará la acción predeterminada del evento
  var formData = new FormData($("#form-agregar-cliente")[0]);

  $.ajax({
    url: "../ajax/cliente.php?op=guardar_y_editar_cliente",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      try {
        e = JSON.parse(e); console.log(e);
        if (e.status == true) {
          Swal.fire("Correcto!", "Registrado correctamente.", "success");
          tabla_cliente.ajax.reload(null, false);
          limpiar_cliente();
          wiev_tabla_formulario(1);
          $("#guardar_registro_cliente").html('Guardar Cambios').removeClass('disabled');
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
          /*console.log(percentComplete + '%');*/
          $("#barra_progress_cliente").css({ "width": percentComplete + '%' });
          $("#barra_progress_cliente").text(percentComplete.toFixed(2) + " %");
        }
      }, false);
      return xhr;
    },
    beforeSend: function () {
      $(".btn-guardar").html('<i class="ri-save-2-line label-btn-icon me-2" ></i> <i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled send-data');
      $("#barra_progress_cliente").css({ width: "0%", });
      $("#barra_progress_cliente").text("0%");
    },
    complete: function () {
      $("#barra_progress_cliente").css({ width: "0%", });
      $("#barra_progress_cliente").text("0%");
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function mostrar_cliente(idpersona_cliente) {

  $("#cargando-1-formulario").hide();
  $("#cargando-2-formulario").show();

  limpiar_cliente();

  wiev_tabla_formulario(2);

  $.post("../ajax/cliente.php?op=mostrar_cliente", { idpersona_cliente: idpersona_cliente }, function (e, status) {

    e = JSON.parse(e); console.log(e);

    if (e.status) {

      $("#idpersona").val(e.data.idpersona);
      $("#idtipo_persona").val(e.data.idtipo_persona);
      $("#idbancos").val(e.data.idbancos);
      $("#idcargo_trabajador").val(e.data.idcargo_trabajador);
      $("#idpersona_cliente").val(e.data.idpersona_cliente);

      $("#tipo_persona_sunat").val(e.data.tipo_persona_sunat).trigger("change");
      $("#tipo_documento").val(e.data.tipo_documento).trigger("change");
      $("#numero_documento").val(e.data.numero_documento);
      $("#nombre_razonsocial").val(e.data.nombre_razonsocial);
      $("#apellidos_nombrecomercial").val(e.data.apellidos_nombrecomercial);
      $("#fecha_nacimiento").val(e.data.fecha_nacimiento);
      $("#celular").val(e.data.celular);
      $("#direccion").val(e.data.direccion);
      $("#distrito").val(e.data.distrito).trigger("change");
      $("#departamento").val(e.data.departamento);
      $("#provincia").val(e.data.provincia);
      $("#ubigeo").val(e.data.cod_ubigeo);
      $("#correo").val(e.data.correo);


      $("#idselec_centroProbl").val(e.data.idcentro_poblado).trigger("change");
      $("#fecha_afiliacion").val(e.data.fecha_afiliacion);
      $("#nota").val(e.data.nota);  

      $("#imagenmuestra").show();
      $("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/" + e.data.foto_perfil);
      $("#imagenactual").val(e.data.foto_perfil);

      $("#cargando-1-formulario").show();
      $("#cargando-2-formulario").hide();

    } else {
      ver_errores(e);
    }

  }).fail(function (e) { ver_errores(e); });
}

//Función para activar registros
function activar(idusuario, nombre) {
	crud_simple_alerta(
		`../ajax/cliente.php?op=activar_cliente&descripcion=`,
    idusuario, 
    "!Reactivar¡", 
    `<b class="text-success">${nombre}</b> <br> Se <b>eliminara</b> la NOTA que ha sido registrado!`, 
		`Aceptar`,
    function(){ sw_success('Recuperado', "Tu cliente ha sido restaurado." ) }, 
    function(){ tabla_cliente.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );
}

//Función para desactivar registros
function eliminar_cliente(idtrabajador, nombre) {
  $(".tooltip").removeClass("show").addClass("hidde");
  Swal.fire({
    title: "!Elija una opción¡",
    html: `<b class="text-danger"><del>${nombre}</del></b> <br> Al <b>dar de baja</b> Padrá encontrar el registro en la papelera! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`,
    icon: "warning",
    showCancelButton: true,
    showDenyButton: true,
    confirmButtonColor: "#17a2b8",
    denyButtonColor: "#d33",
    cancelButtonColor: "#6c757d",    
    confirmButtonText: `<i class="fas fa-times"></i> Dar de Baja`,
    denyButtonText: `<i class="fas fa-skull-crossbones"></i> Eliminar`,    
    showLoaderOnDeny: true,
    preDeny: (input) => {       
      return fetch(`../ajax/cliente.php?op=eliminar_cliente&id_tabla=${idtrabajador}`).then(response => {
        console.log(response);
        if (!response.ok) { throw new Error(response.statusText) }
        return response.json();
      }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); })
    },
    allowOutsideClick: () => !Swal.isLoading()
  }).then((result) => {
    
    if (result.isConfirmed) {    
      Swal.fire({
        icon: "warning",
        title: 'Antes de dar de baja ingrese una Observación',
        input: 'textarea',        
        inputAttributes: { autocapitalize: 'off', Class: 'form-control', Placeholder: 'ejemp: Corte de servicio por falta de pago.',  },
        customClass: {
          validationMessage: 'my-validation-message',
        },
        showCancelButton: true,
        cancelButtonColor: "#d33",
        confirmButtonText: 'Si, dar de baja!',
        confirmButtonColor: "#28a745",
        showLoaderOnConfirm: true,
        preConfirm: (value) => {
          console.log(value);
          if (!value) {
            Swal.showValidationMessage('La <i class="fw-bold">&nbsp;NOTA&nbsp;</i> es requerido.')
          }else{            
            return fetch(`../ajax/cliente.php?op=desactivar_cliente&id_tabla=${idtrabajador}&descripcion=${value}`).then(response => {
              console.log(response);
              if (!response.ok) { throw new Error(response.statusText); }
              return response.json();
            }).catch(error => { Swal.showValidationMessage(`<b>Solicitud fallida:</b> ${error}`); });
          }          
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {       
        if (result.isConfirmed) {
          if (result.value.status) {
            Swal.fire("Expulsado!", "Tu trabajador ha sido expulsado.", "success");
            tabla_cliente.ajax.reload(null, false); 
          }else{
            ver_errores(result.value);
          }     
        }
      });

    }else if (result.isDenied) {
      //op=eliminar
      if (result.value.status) {
        Swal.fire("Eliminado!", "Tu trabajador ha sido Eliminado.", "success");
        tabla_cliente.ajax.reload(null, false); 
      }else{
        ver_errores(result.value);
      }      
    }
  });
}


// .....::::::::::::::::::::::::::::::::::::: I M P R I M I R   T I C K E T :::::::::::::::::::::::::::::::::::::::..

function TickcetPagoCliente(idventa, tipo_comprobante){

  $("#pago-cliente-mes").modal('hide');
  // console.log(idventa);
  if (tipo_comprobante == '01') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $("#modal-imprimir-comprobante-Label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - FACTURA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '03') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $("#modal-imprimir-comprobante-Label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - BOLETA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else if (tipo_comprobante == '12') {
    var rutacarpeta = "../reportes/TicketFormatoGlobal.php?id=" + idventa;
    $("#modal-imprimir-comprobante-Label").html(`<button type="button" class="btn btn-icon btn-sm btn-primary btn-wave" data-bs-toggle="tooltip" title="Imprimir Ticket" onclick="printIframe('iframe_format_ticket')"><i class="ri-printer-fill"></i></button> FORMATO TICKET - NOTA DE VENTA`);
    $("#html-imprimir-comprobante").html(`<iframe name="iframe_format_ticket" id="iframe_format_ticket" src="${rutacarpeta}" border="0" frameborder="0" width="100%" style="height: 450px;" marginwidth="1" src=""> </iframe>`);
    $("#modal-imprimir-comprobante").modal("show");
  } else  {
    // toastr_warning('No Disponible', 'Tenga paciencia el formato de impresión estara listo pronto.');
    toastr_error('No Existe!!', 'Este tipo de documeno no existe en mi registro.');
  }
  
}


$(document).ready(function () {
  init();
});

$(function () {
  $('#tipo_persona_sunat').on('change', function () { $(this).trigger('blur'); });
  $('#tipo_documento').on('change', function () { $(this).trigger('blur'); });
  $('#distrito').on('change', function () { $(this).trigger('blur'); });
  $('#idselec_centroProbl').on('change', function () { $(this).trigger('blur'); });

  $("#form-agregar-cliente").validate({
    rules: {

      tipo_persona_sunat:   { required: true },
      tipo_documento:       { required: true, minlength: 1, maxlength: 2, },
      numero_documento:     { required: true, minlength: 8, maxlength: 20, number: true, },
      nombre_razonsocial:   { required: true, minlength: 2, maxlength: 200, },
      apellidos_nombrecomercial: { required: true, minlength: 4, maxlength: 200, },
      correo:    			      { minlength: 4, maxlength: 100, },       
      celular:    			    { minlength: 8, maxlength: 9, },
      fecha_nacimiento:    	{  },  

      direccion:    			  { minlength: 4, maxlength: 200, },
      distrito:             { required: true },
      departamento:         { required: true },
      provincia:            { required: true },
      ubigeo:               { required: true },

      idselec_centroProbl:  { required: true },
      nota:                 { minlength: 4, maxlength: 400, },

    },
    messages: {

      tipo_persona_sunat:   { required: "Campo requerido.", },
      tipo_documento:       { required: "Campo requerido.", },
      numero_documento:     { required: "Campo requerido.", },
      nombre_razonsocial:   { required: "Campo requerido.", },
      apellidos_nombrecomercial: { required: "Campo requerido.", },
      distrito:             { required: "Campo requerido.", },
      departamento:         { required: "Campo requerido.", },
      provincia:            { required: "Campo requerido.", },
      ubigeo:               { required: "Campo requerido.", },

      idselec_centroProbl:  { required: "Campo requerido.", },      

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
      guardar_y_editar_cliente(e);
    },

  });

  $('#tipo_persona_sunat').rules('add', { required: true, messages: { required: "Campo requerido" } });
  $('#tipo_documento').rules('add', { required: true, messages: { required: "Campo requerido" } });
  $('#distrito').rules('add', { required: true, messages: { required: "Campo requerido" } });

  $('#idselec_centroProbl').rules('add', { required: true, messages: { required: "Campo requerido" } });
  
});

// .....::::::::::::::::::::::::::::::::::::: F U N C I O N E S    A L T E R N A S  :::::::::::::::::::::::::::::::::::::::..
function cargando_search() {
  if ($('#id_buscando_tabla').length) { } else {
    $('.buscando_tabla').prepend(`<tr id="id_buscando_tabla"> 
      <th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando... </th>
    </tr>`);
  }  
}

function filtros() {  

  var filtro_mes_afiliacion     = $("#filtro_mes_afiliacion").select2('val');  
  var filtro_distrito         = $("#filtro_distrito").select2('val');

  var nombre_dia_pago     = ' ─ ' + $('#filtro_mes_afiliacion').find(':selected').text();
  var nombre_distrito         = ' ─ ' + $('#filtro_distrito').find(':selected').text();
  
  if (filtro_mes_afiliacion == '' || filtro_mes_afiliacion == 0 || filtro_mes_afiliacion == null) { filtro_mes_afiliacion = ""; nombre_dia_pago = ""; }                 // filtro de dia pago  
  if (filtro_distrito == '' || filtro_distrito == 0 || filtro_distrito == null) { filtro_distrito = ""; nombre_distrito = ""; }                                     // filtro de plan

  $('#id_buscando_tabla').html(`<th colspan="20" class="bg-danger " style="text-align: center !important;"><i class="fas fa-spinner fa-pulse fa-sm"></i> Buscando ${nombre_dia_pago} ${nombre_distrito}...</th>`);
  //console.log(filtro_categoria, fecha_2, filtro_distrito, comprobante);
  
  tabla_principal_cliente( filtro_mes_afiliacion, filtro_distrito);
  
}

function cambiarImagen() {
  var imagenInput = document.getElementById('imagen');
  imagenInput.click();
}

function removerImagen() {
  // var imagenMuestra = document.getElementById('imagenmuestra');
  // var imagenActualInput = document.getElementById('imagenactual');
  // var imagenInput = document.getElementById('imagen');
  // imagenMuestra.src = '../assets/images/faces/9.jpg';
  $("#imagenmuestra").attr("src", "../assets/modulo/persona/perfil/no-perfil.jpg");
  // imagenActualInput.value = '';
  // imagenInput.value = '';
  $("#imagen").val("");
  $("#imagenactual").val("");
}

// Esto se encarga de mostrar la imagen cuando se selecciona una nueva
document.addEventListener('DOMContentLoaded', function () {
  var imagenMuestra = document.getElementById('imagenmuestra');
  var imagenInput = document.getElementById('imagen');

  imagenInput.addEventListener('change', function () {
    if (imagenInput.files && imagenInput.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) { imagenMuestra.src = e.target.result; }
      reader.readAsDataURL(imagenInput.files[0]);
    }
  });
});

function ver_img(img, nombre) {
  $(".title-ver-imgenes").html(`- ${nombre}`);
  $('#modal-ver-imgenes').modal("show");
  $('.html_modal_ver_imgenes').html(doc_view_extencion(img, 'assets/modulo/persona/perfil', '100%', '550'));
  $(`.jq_image_zoom`).zoom({ on: 'grab' });
}

function reload_select(r_text) {

  switch (r_text) {
    
    case 'filtro_dia':
      lista_select2("../ajax/cliente.php?op=select2_filtro_mes_afiliacion",   '#filtro_mes_afiliacion',       null, '.charge_filtro_mes_afiliacion');
    break;
    case 'filtro_distrito':
      lista_select2("../ajax/cliente.php?op=select2_filtro_distrito",       '#filtro_distrito',           null, '.charge_filtro_distrito');
    break;
    case 'filtro_zona_antena':
      lista_select2("../ajax/cliente.php?op=select2_filtro_zona_antena",'#filtro_zona_antena',    null, '.charge_filtro_zona_antena');
    break;   

    
    case 'filtro_mes_afiliacion':
      lista_select2("../ajax/cliente.php?op=select2_filtro_mes_afiliacion",   '#filtro_p_all_dia_pago',   null, '.charge_filtro_p_all_dia_pago');
    break;
    case 'filtro_anio_pago':
      lista_select2("../ajax/cliente.php?op=select2_filtro_anio_pago",  '#filtro_p_all_anio_pago',  moment().format('YYYY'), '.charge_filtro_p_all_anio_pago');
    break;    
    case 'filtro_p_all_plan':
      lista_select2("../ajax/cliente.php?op=select2_filtro_distrito",       '#filtro_p_all_plan',       null, '.charge_filtro_p_all_plan');
    break;
    case 'filtro_p_all_zona_antena':
      lista_select2("../ajax/cliente.php?op=select2_filtro_zona_antena",'#filtro_p_all_zona_antena',null, '.charge_filtro_p_all_zona_antena');
    break;   

    
    case 'centroPbl':
      lista_select2("../ajax/cliente.php?op=selec_centroProbl", '#idselec_centroProbl', null, '.charge_idctroPbl');
    break;    

    default:
      console.log('Caso no encontrado.');
  }
 
}

function crear_dias_pagos(input) {
  $(input).append(`<option value="TODOS">TODOS</option>`);
  for (let index = 1; index <= 31; index++) {
    $(input).append(`<option value="${index}">Día ${index}</option>`); 
  }
  
}

function crear_anio_pagos(input) {
  var anio =parseFloat(moment().format('YYYY')) + (1);
  var html = "";  
  for (let i = 1; i < 6; i++) { anio--; html = html.concat(`<option value="${anio}">${anio}</option> `); }
  $(input).html(html);
}

function printIframe(id) {
  var iframe = document.getElementById(id);
  iframe.focus(); // Para asegurarse de que el iframe está en foco
  iframe.contentWindow.print(); // Llama a la función de imprimir del documento dentro del iframe
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

})();

