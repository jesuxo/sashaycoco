 //* choices category input
var sucursal = new Choices('#fksucursal', {
    searchEnabled: false,
    shouldSort: false,
});


var forms = document.querySelectorAll('.needs-validation')


Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {


        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            event.preventDefault();

            var fksucursal  = sucursal.getValue(true);
            var fkbanco     = document.getElementById("bancosucursal").value;
            var monto       = document.getElementById("monto").value;
            var fecha       = document.getElementById("fecha").value;
            var numero      = document.getElementById("numero").value;

            $('.alertatransferencia').html('');
            var valid = 0;

                 $.ajax({
                    type: 'POST',
                    url: '/transferencias/verificar',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{  fkbanco: fkbanco, monto:monto, fecha:fecha, numero: numero  },
                    success: function (data) {
                        valid = data.valid;

                        if(valid) {
                             document.getElementById("createtransf-form").submit();
                        }else{
                            $('.alertatransferencia').html('Transferencia -- ERROR :: '+numero+' Ya Existente');

                            document.getElementById("numero").value = '';
                        }
                    }
                });

            return false;
        }

        form.classList.add('was-validated');

    }, false)
});
