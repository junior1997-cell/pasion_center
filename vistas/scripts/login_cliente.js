
$(function () {
  $("#frmAcceso_c").on("submit", function (e) {
    e.preventDefault();

    var btnIngresar = $(".login-btn");
    var loginc = $("#loginc").val();
    var clavec = $("#clavec").val();    
    // var st = $("#estadot").val();
    var st = 0;

    btnIngresar.prop("disabled", true).html(`<i class="bx bx-loader-circle bx-spin font-size-15px" ></i> Validando datos<span class="bx-burst">...</span>`).removeClass('btn-primary').addClass('btn-outline-dark');

    
    $.post( "ajax/usuario_cliente.php?op=verificarC", { "loginc": loginc, "clavec": clavec, "st": st },  function (e) {
			try {
				e = JSON.parse(e); //console.log(e);				
				setTimeout(validar_response(e), 1000);
				
			} catch (err) { 
				console.log("Error: ", err.message); 
				toastr.error('<h5 class="font-size-16px">Error temporal!!</h5> puede intentalo mas tarde, o comuniquese con <i><a href="tel:+51921305769" >921-305-769</a></i> ─ <i><a href="tel:+51921487276" >921-487-276</a></i>');
				
				btnIngresar.prop("disabled", false).html("Iniciar sesion");		
				ver_errores(error);		
			}
			
    }).fail( function(e) { 
			btnIngresar.prop("disabled", false).html("Iniciar sesion"); 
			ver_errores(e);
			// const dangert = document.getElementById('error-servidor'); 
			// const toast = new bootstrap.Toast(dangert); toast.show();  
		});    
  });
});

function validar_response(e) {
	if (e.status == true) {
		if (e.data == null) {
			// const dangert = document.getElementById('user-incorrecto'); 
			// const toast = new bootstrap.Toast(dangert); toast.show();
			toastr_error('Acceso denegado', 'Las credenciales proporcionadas son incorrectas. Por favor, verifica tu nombre de usuario y contraseña e intenta nuevamente');
			$('.login-btn').html('Iniciar sesion').prop("disabled", false).removeClass('disabled btn-outline-dark').addClass('btn-primary');
		} else if (e.data.usuario_cliente == null) {
			// const dangert = document.getElementById('user-incorrecto'); 
			// const toast = new bootstrap.Toast(dangert); toast.show();
			toastr_error('Acceso denegado', 'Las credenciales proporcionadas son incorrectas. Por favor, verifica tu nombre de usuario y contraseña e intenta nuevamente');
			$('.login-btn').html('Iniciar sesion').prop("disabled", false).removeClass('disabled btn-outline-dark').addClass('btn-primary');
		} else {
			toastr_success('Bienvenido.', 'Te damos la bienvenida. ¡Esperamos que disfrutes tu experiencia!');
			var redirecinando = varaibles_get();
			$('.login-btn').html('Iniciar sesion').prop("disabled", false).removeClass('disabled btn-outline-dark').addClass('btn-primary');
			localStorage.setItem('nube_id_usuario', e.data.usuario_cliente.idpersona_cliente);

			if (redirecinando.file == '' || redirecinando.file == null) {	$(location).attr("href", "ver_pagos.php");	} else { $(location).attr("href", redirecinando.file); }			      
		}

	} else {
		$('.login-btn').html('Ingresar').removeClass('disabled btn-outline-dark').addClass('btn-primary');
		ver_errores(e);
	}
}

function varaibles_get() {
	var v_args = location.search.substring(1).split("&");
	var param_values = [];
	if (v_args != '' && v_args != 'undefined')
		for (var i = 0; i < v_args.length; i++) {
			var pair = v_args[i].split("=");
			if (typeOfVar(pair) === 'array') {
				param_values[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1]);
			}
		}
	return param_values;
}

function typeOfVar(obj) {
	return {}.toString.call(obj).split(' ')[1].slice(0, -1).toLowerCase();
}

