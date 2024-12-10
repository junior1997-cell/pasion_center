    <!-- MODAL - AGREGAR CATEGORIA -->
    <div class="modal fade modal-effect" id="modal-agregar-categoria" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-categoriaLabel">
      <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title" id="modal-agregar-categoriaLabel1">Registrar Categoría</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form name="formulario-categoria" id="formulario-categoria" method="POST" class="needs-validation" novalidate>
              <div class="row gy-2" id="cargando-3-fomulario">
                <input type="hidden" name="idcategoria" id="idcategoria">

                <div class="col-md-12">
                  <div class="form-label">
                    <label for="nombre_cat" class="form-label">Nombre(*)</label>
                    <input type="text" class="form-control" name="nombre_cat" id="nombre_cat" onkeyup="mayus(this);" />
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="descr_cat" class="form-label">Descripción(*)</label>
                    <input type="text" class="form-control" name="descr_cat" id="descr_cat" onkeyup="mayus(this);" />
                  </div>
                </div>
              </div>
              <div class="row" id="cargando-4-fomulario" style="display: none;">
                <div class="col-lg-12 text-center">
                  <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                  <h4 class="bx-flashing">Cargando...</h4>
                </div>
              </div>
              <button type="submit" style="display: none;" id="submit-form-categoria">Submit</button>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_cat();"><i class="las la-times fs-lg"></i> Close</button>
            <button type="button" class="btn btn-primary" id="guardar_registro_categoria"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- End::Modal-Agregar-Cartegoria -->


    <!-- MODAL - AGREGAR MARCA -->
    <div class="modal fade modal-effect" id="modal-agregar-marca" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-marcaLabel">
      <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title" id="modal-agregar-marcaLabel1">Registrar Marca</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form name="formulario-marca" id="formulario-marca" method="POST" class="needs-validation" novalidate>
              <div class="row gy-2" id="cargando-5-fomulario">
                <input type="hidden" name="idmarca" id="idmarca">

                <div class="col-md-12">
                  <div class="form-label">
                    <label for="nombre_marca" class="form-label">Nombre(*)</label>
                    <input type="text" class="form-control" name="nombre_marca" id="nombre_marca" onkeyup="mayus(this);" />
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="descr_marca" class="form-label">Descripción(*)</label>
                    <input type="text" class="form-control" name="descr_marca" id="descr_marca" onkeyup="mayus(this);" />
                  </div>
                </div>
              </div>
              <div class="row" id="cargando-6-fomulario" style="display: none;">
                <div class="col-lg-12 text-center">
                  <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                  <h4 class="bx-flashing">Cargando...</h4>
                </div>
              </div>
              <button type="submit" style="display: none;" id="submit-form-marca">Submit</button>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_marca();"><i class="las la-times fs-lg"></i> Close</button>
            <button type="button" class="btn btn-primary" id="guardar_registro_marca"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- End::Modal-Agregar-Marca -->

    <!-- MODAL - AGREGAR UM -->
    <div class="modal fade modal-effect" id="modal-agregar-u-m" role="dialog" tabindex="-1" aria-labelledby="modal-agregar-u-mLabel">
      <div class="modal-dialog modal-md modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title" id="modal-agregar-u-mLabel1">Registrar Unidad de Medida</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form name="formulario-u-m" id="formulario-u-m" method="POST" class="row needs-validation" novalidate>
              <div class="row gy-2" id="cargando-1-fomulario">
                <input type="hidden" name="idsunat_unidad_medida" id="idsunat_unidad_medida">


                <div class="col-md-6">
                  <div class="form-label">
                    <label for="nombre_um" class="form-label">Nombre(*)</label>
                    <input type="text" class="form-control" name="nombre_um" id="nombre_um" onkeyup="mayus(this);" />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="descr_um" class="form-label">Descripción(*)</label>
                    <input type="text" class="form-control" name="descr_um" id="descr_um" onkeyup="mayus(this);" />
                  </div>
                </div>
              </div>
              <div class="row" id="cargando-2-fomulario" style="display: none;">
                <div class="col-lg-12 text-center">
                  <div class="spinner-border me-4" style="width: 3rem; height: 3rem;" role="status"></div>
                  <h4 class="bx-flashing">Cargando...</h4>
                </div>
              </div>
              <button type="submit" style="display: none;" id="submit-form-u-m">Submit</button>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal" onclick="limpiar_form_um();"><i class="las la-times fs-lg"></i> Close</button>
            <button type="button" class="btn btn-primary" id="guardar_registro_u_m"><i class="bx bx-save bx-tada fs-lg"></i> Guardar</button>
          </div>
        </div>
      </div>
    </div>
    <!-- End::Modal-registrar-unidad-medida -->

    <script>

      // ini
      $("#guardar_registro_categoria").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-categoria").submit(); } });
      $("#guardar_registro_marca").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-marca").submit(); } });
      $("#guardar_registro_u_m").on("click", function (e) { if ($(this).hasClass('send-data') == false) { $("#submit-form-u-m").submit(); } });

      //  :::::::::::::::: C A T E G O R I A :::::::::::::::: 

      function limpiar_form_cat() {
        $("#guardar_registro_categoria").html('Guardar Cambios').removeClass('disabled');

        $("#idcategoria").val("");
        $("#nombre_cat").val("");
        $("#descr_cat").val("");

        $(".form-control").removeClass('is-valid');
        $(".form-control").removeClass('is-invalid');
        $(".error.invalid-feedback").remove();
      }

      function guardar_editar_categoria(e) {
        var formData = new FormData($("#formulario-categoria")[0]);
        $.ajax({
          url: "../ajax/categoria.php?op=guardar_editar_cat",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,

          success: function(e) {
            e = JSON.parse(e);
            console.log(e);
            if (e.status == true) {
              lista_select2("../ajax/producto.php?op=select_categoria", '#categoria', e.data, '.charge_idcategoria');
              Swal.fire("Correcto!", "Categoría registrada correctamente.", "success");
              limpiar_form_cat();
              $("#modal-agregar-categoria").modal("hide");
            } else {
              ver_errores(e);
            }
            $("#guardar_registro_categoria").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');

          },
          error: function(jqXhr) {
            ver_errores(jqXhr);
          },
        });
      }

      // :::::::::::: M A R C A :::::::::::::::::::

      function limpiar_form_marca() {
        $("#guardar_registro_marca").html('Guardar Cambios').removeClass('disabled');

        $("#idmarca").val("");
        $("#nombre_marca").val("");
        $("#descr_marca").val("");

        $(".form-control").removeClass('is-valid');
        $(".form-control").removeClass('is-invalid');
        $(".error.invalid-feedback").remove();
      }

      function guardar_editar_marca(e) {
        var formData = new FormData($("#formulario-marca")[0]);
        $.ajax({
          url: "../ajax/marca.php?op=guardar_editar_marca",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,

          success: function(e) {
            e = JSON.parse(e);
            console.log(e);
            if (e.status == true) {
              lista_select2("../ajax/producto.php?op=select_marca", '#marca', e.data, '.charge_idmarca');
              Swal.fire("Correcto!", "Marca registrada correctamente.", "success");
              limpiar_form_marca();
              $("#modal-agregar-marca").modal("hide");
            } else {
              ver_errores(e);
            }
            $("#guardar_registro_marca").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');

          },
          error: function(jqXhr) {
            ver_errores(jqXhr);
          },
        });
      }

      // :::::::::::: Unidad Medida :::::::::::::::::::

      function limpiar_form_um() {
        $("#guardar_registro_u_m").html('Guardar Cambios').removeClass('disabled');

        $("#idsunat_unidad_medida").val("");
        $("#nombre_um").val("");
        $("#descr_um").val("");

        $(".form-control").removeClass('is-valid');
        $(".form-control").removeClass('is-invalid');
        $(".error.invalid-feedback").remove();
      }

      function guardar_editar_UM(e) {
        var formData = new FormData($("#formulario-u-m")[0]);
        $.ajax({
          url: "../ajax/unidad_medida.php?op=guardar_editar_UM",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,

          success: function(e) {
            e = JSON.parse(e);
            if (e.status == true) {
              Swal.fire("Correcto!", "Unidad de medida registrado correctamente.", "success");
              limpiar_form_um();
              $("#modal-agregar-u-m").modal("hide");
            } else {
              ver_errores(e);
            }
            $("#guardar_registro_u_m").html('<i class="bx bx-save bx-tada"></i> Guardar').removeClass('disabled send-data');

          },
          error: function(jqXhr) {
            ver_errores(jqXhr);
          },
        });
      }

      $(function() {

        //  :::::::::::::::::::: F O R M U L A R I O   C A T E G O R I A ::::::::::::::::::::

        $("#formulario-categoria").validate({
          rules: {
            nombre_cat: {
              required: true
            },
            descr_cat: {
              required: true
            }
          },
          messages: {
            nombre_cat: {
              required: "Campo requerido.",
            },
            descr_cat: {
              required: "Campo requerido.",
            },
          },

          errorElement: "span",

          errorPlacement: function(error, element) {
            error.addClass("invalid-feedback");
            element.closest(".form-group").append(error);
          },

          highlight: function(element, errorClass, validClass) {
            $(element).addClass("is-invalid").removeClass("is-valid");
          },

          unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("is-invalid").addClass("is-valid");
          },
          submitHandler: function(e) {
            $(".modal-body").animate({
              scrollTop: $(document).height()
            }, 600);
            guardar_editar_categoria(e);
          },

        });

        //  :::::::::::::::::::::: F O R M U L A R I O   M A R C A :::::::::::::::::::::::::::

        $("#formulario-marca").validate({
          rules: {
            nombre_marca: {
              required: true
            },
            descr_marca: {
              required: true
            }
          },
          messages: {
            nombre_marca: {
              required: "Campo requerido.",
            },
            descr_marca: {
              required: "Campo requerido.",
            },
          },

          errorElement: "span",

          errorPlacement: function(error, element) {
            error.addClass("invalid-feedback");
            element.closest(".form-group").append(error);
          },

          highlight: function(element, errorClass, validClass) {
            $(element).addClass("is-invalid").removeClass("is-valid");
          },

          unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("is-invalid").addClass("is-valid");
          },
          submitHandler: function(e) {
            $(".modal-body").animate({
              scrollTop: $(document).height()
            }, 600);
            guardar_editar_marca(e);
          },

        });

        //  :::::::::::::::::::::: F O R M U L A R I O   U.   M E D I D A :::::::::::::::::::::::::::

        $("#formulario-u-m").validate({
          rules: {
            nombre_um: {
              required: true
            },
            descr_um: {
              required: true
            }
          },
          messages: {
            nombre_um: {
              required: "Campo requerido.",
            },
            descr_um: {
              required: "Campo requerido.",
            },
          },

          errorElement: "span",

          errorPlacement: function(error, element) {
            error.addClass("invalid-feedback");
            element.closest(".form-group").append(error);
          },

          highlight: function(element, errorClass, validClass) {
            $(element).addClass("is-invalid").removeClass("is-valid");
          },

          unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("is-invalid").addClass("is-valid");
          },
          submitHandler: function(e) {
            $(".modal-body").animate({
              scrollTop: $(document).height()
            }, 600);
            guardar_editar_UM(e);
          },

        });

      });
    </script>