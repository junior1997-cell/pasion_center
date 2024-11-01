var tabla_centro_poblado;

// ══════════════════════════════════════ I N I T I A L I Z E   S E L E C T C H O I C E ══════════════════════════════════════

const choice_distrito       = new Choices('#distrito_cp',  {  removeItemButton: true,noResultsText: 'No hay resultados.', } );


//Función que se ejecuta al inicio
function init_cp() {

  tabla_principal_centro_poblado();
  // ══════════════════════════════════════ G U A R D A R   F O R M ══════════════════════════════════════
  // $("#guardar_registro_centro_poblado").on("click", function (e) { $("#submit-form-plan").submit(); });
  $("#guardar_registro_cp").on("click", function (e) { if ( $(this).hasClass('send-data')==false) { $("#submit-form-cp").submit(); }  });

  // ══════════════════════════════════════ S E L E C T 2 ══════════════════════════════════════
  lista_selectChoice("../ajax/ajax_general.php?op=selectChoice_distrito", choice_distrito, null);
}


//Función limpiar_form
function limpiar_centro_poblado() {
  $("#guardar_registro_cp").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled');
  //Mostramos los Materiales
  $("#idcentro_poblado").val("");
  $("#idubigeo_distrito").val("");

  $("#nombre_cp").val("");
  $("#descripcion_cp").val("");

  choice_distrito.setChoiceByValue('TARAPOTO').passedElement.element.dispatchEvent(new Event('change'));  

  // Limpiamos las validaciones
  $(".form-control").removeClass('is-valid');
  $(".form-control").removeClass('is-invalid');
  $(".error.invalid-feedback").remove();
}

function llenar_dep_prov_ubig(input) {

  $(".chargue-pro").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 
  $(".chargue-dep").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 
  $(".chargue-ubi").html(`<div class="spinner-border spinner-border-sm" role="status" ></div>`); 

  // if ($(input).select2("val") == null || $(input).select2("val") == '') { 
  if ($('#distrito_cp').val() == null || $('#distrito_cp').val() == '') { 
    $("#departamento_cp").val(""); 
    $("#provincia_cp").val(""); 
    $("#idubigeo_distrito").val(""); 

    $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
  } else {
    // var iddistrito =  $(input).select2('data')[0].element.attributes.iddistrito.value;
    var iddistrito = choice_distrito.getValue().customProperties.idubigeo_distrito;
    $("#idubigeo_distrito").val(iddistrito);
    $.post(`../ajax/ajax_general.php?op=select2_distrito_id&id=${iddistrito}`, function (e) {   
      e = JSON.parse(e); console.log(e);
      if (e.status == true) {
        $("#departamento_cp").val(e.data.departamento); 
        $("#provincia_cp").val(e.data.provincia);   
      } else {
        ver_errores(e);
      }
      $(".chargue-pro").html(''); $(".chargue-dep").html(''); $(".chargue-ubi").html('');
      $("#form-agregar-centro-poblado").valid();
      
    });
  }  
}

//Función Listar
function tabla_principal_centro_poblado() {

  tabla_centro_poblado = $('#tabla-centro-poblado').dataTable({
    lengthMenu: [[ -1, 5, 10, 25, 75, 100, 200,], ["Todos", 5, 10, 25, 75, 100, 200, ]],//mostramos el menú de registros a revisar
    "aProcessing": true,//Activamos el procesamiento del datatables
    "aServerSide": true,//Paginación y filtrado realizados por el servidor
    dom:"<'row'<'col-md-4'B><'col-md-2 float-left'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",//Definimos los elementos del control de tabla
    buttons: [
      { text: '<i class="fa-solid fa-arrows-rotate"></i> ', className: "buttons-reload px-2 btn btn-sm btn-outline-info btn-wave ", action: function ( e, dt, node, config ) { if (tabla_centro_poblado) { tabla_centro_poblado.ajax.reload(null, false); } } },
      { extend: 'copy', exportOptions: { columns: [0,2,3], }, text: `<i class="fas fa-copy" ></i>`, className: "px-2 btn btn-sm btn-outline-dark btn-wave ", footer: true,  }, 
      { extend: 'excel', exportOptions: { columns: [0,2,3], }, title: 'Lista de centro poblado', text: `<i class="far fa-file-excel fa-lg" ></i>`, className: "px-2 btn btn-sm btn-outline-success btn-wave ", footer: true,  }, 
      { extend: 'pdf', exportOptions: { columns: [0,2,3], }, title: 'Lista de centro poblado', text: `<i class="far fa-file-pdf fa-lg"></i>`, className: "px-2 btn btn-sm btn-outline-danger btn-wave ", footer: false, orientation: 'landscape', pageSize: 'LEGAL',  },
      { extend: "colvis", text: `<i class="fas fa-outdent"></i>`, className: "px-2 btn btn-sm btn-outline-primary", exportOptions: { columns: "th:not(:last-child)", }, },
    ],
    ajax:{
      url: '../ajax/centro_poblado.php?op=tabla_principal_centro_poblado',
      type : "get",
      dataType : "json",						
      error: function(e){
        console.log(e.responseText);	ver_errores(e);
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
      if (data[1] != '') { $("td", row).eq(1).addClass("text-nowrap text-center"); }
      // columna: #
      if (data[2] != '') { $("td", row).eq(2).addClass("text-nowrap"); }
      // columna: #
      if (data[3] != '') { $("td", row).eq(3).addClass("text-nowrap"); }
    },
		language: {
      lengthMenu: "_MENU_ ", search: "Buscar:",
      buttons: { copyTitle: "Tabla Copiada", copySuccess: { _: "%d líneas copiadas", 1: "1 línea copiada", }, },
      loadingRecords: '<i class="fas fa-spinner fa-pulse fa-lg"></i> Cargando datos...', paginate: { first: "Primero", last: "Último", next: "Siguiente", previous: "Anterior" },
      emptyTable: "Ningún dato disponible en esta tabla", zeroRecords: "No se encontraron resultados",
    },
    "bDestroy": true,
    "iDisplayLength": 5,//Paginación
    "order": [[0, "asc"]]//Ordenar (columna,orden)
  }).DataTable();
}

//Función para guardar o editar
function guardar_y_editar_centro_poblado(e) {
  // e.preventDefault(); //No se activará la acción predeterminada del evento
  var formData = new FormData($("#form-agregar-centro-poblado")[0]);
 
  $.ajax({
    url: "../ajax/centro_poblado.php?op=guardar_y_editar_centro_poblado",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (e) {
      e = JSON.parse(e);  console.log(e);  
      if (e.status == true) {
        Swal.fire("Correcto!", "Centro poblado registrado correctamente.", "success");
	      tabla_centro_poblado.ajax.reload(null, false);         
				limpiar_centro_poblado();
        $("#modal-agregar-centro-poblado").modal("hide");        
			}else{
				ver_errores(e);
			}
      $("#guardar_registro_cp").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');
    },
    xhr: function () {
      var xhr = new window.XMLHttpRequest();
      xhr.upload.addEventListener("progress", function (evt) {
        if (evt.lengthComputable) {
          var percentComplete = (evt.loaded / evt.total)*100;
          /*console.log(percentComplete + '%');*/
          $("#barra_progress_centro_poblado").css({"width": percentComplete+'%'});
          $("#barra_progress_centro_poblado").text(percentComplete.toFixed(2)+" %");
        }
      }, false);
      return xhr;
    },
    beforeSend: function () {
      $("#guardar_registro_cp").html('<i class="fas fa-spinner fa-pulse fa-lg"></i>').addClass('disabled');
      $("#barra_progress_centro_poblado").css({ width: "0%",  });
      $("#barra_progress_centro_poblado").text("0%");
    },
    complete: function () {
      $("#barra_progress_centro_poblado").css({ width: "0%", });
      $("#barra_progress_centro_poblado").text("0%");
    },
    error: function (jqXhr) { ver_errores(jqXhr); },
  });
}

function mostrar_centro_poblado(idcentro_poblado) {
  $(".tooltip").remove();
  $("#cargando-1-fomulario").hide();
  $("#cargando-2-fomulario").show();
  
  limpiar_centro_poblado();

  $("#modal-agregar-centro-poblado").modal("show")

  $.post("../ajax/centro_poblado.php?op=mostrar_centro_poblado", { idcentro_poblado: idcentro_poblado }, function (e, status) {

    e = JSON.parse(e);  console.log(e);  

    if (e.status) {
      $("#idcentro_poblado").val(e.data.idcentro_poblado);
      $("#nombre_cp").val(e.data.nombre);        
      $("#descripcion_cp").val(e.data.descripcion);
      choice_distrito.setChoiceByValue('TARAPOTO');

      $("#idubigeo_distrito").val(e.data.idubigeo_distrito);
      $("#provincia_cp").val(e.data.nombre_provincia);
      $("#departamento_cp").val(e.data.nombre_departamento);

      $("#cargando-1-fomulario").show();
      $("#cargando-2-fomulario").hide();
    } else {
      ver_errores(e);
    }
    
  }).fail( function(e) { ver_errores(e); } );
}

//Función para desactivar registros
function eliminar_centro_poblado(idcentro_poblado, nombre) {

  crud_eliminar_papelera(
    "../ajax/centro_poblado.php?op=desactivar",
    "../ajax/centro_poblado.php?op=eliminar", 
    idcentro_poblado, 
    "!Elija una opción¡", 
    `<b class="text-danger"><del>${nombre}</del></b> <br> En <b>papelera</b> encontrará este registro! <br> Al <b>eliminar</b> no tendrá acceso a recuperar este registro!`, 
    function(){ sw_success('♻️ Papelera! ♻️', "Tu registro ha sido reciclado." ) }, 
    function(){ sw_success('Eliminado!', 'Tu registro ha sido Eliminado.' ) }, 
    function(){ tabla_centro_poblado.ajax.reload(null, false); },
    false, 
    false, 
    false,
    false
  );

}

/*==========================================================================================
------------------------------------------- Z O N A S -------------------------------------
==========================================================================================*/

$(document).ready(function () {
  init_cp();
});

$(function () {

  $("#form-agregar-centro-poblado").validate({
    rules: {
      nombre_cp: { required: true, maxlength: 60, } ,     // terms: { required: true },
      descripcion_cp: {  maxlength: 200, }      // terms: { required: true },
    },
    messages: {
      nombre_cp: {  required: "Campo requerido.", },
      descripcion_cp: {  maxlength: "Maximo {0} caracteres.", },
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
      guardar_y_editar_centro_poblado(e);      
    },

  });
});

