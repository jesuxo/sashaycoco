/*
 SISDATO - Sistema dado para todos
*/

var perPage = 10;
var editlist = false;

var options = {
    valueNames: [
        "id",
        "codtarj",
        "descrip",
        "bs",
        "dolares",
        "pesos",
        "activo",
    ],
    page: perPage,
    pagination: true,
    plugins: [
        ListPagination({
            left: 2,
            right: 2,
        }),
    ],
};

var tarjetasList = new List("tarjetasList", options).on("updated", function (list) {

    list.matchingItems.length == 0 ?
        (document.getElementsByClassName("noresult")[0].style.display = "block") :
        (document.getElementsByClassName("noresult")[0].style.display = "none");

    var isFirst = list.i == 1;
    var isLast = list.i > list.matchingItems.length - list.page;

    // make the Prev and Nex buttons disabled on first and last pages accordingly
    document.querySelector(".pagination-prev.disabled") ?
        document.querySelector(".pagination-prev.disabled").classList.remove("disabled") : "";
    document.querySelector(".pagination-next.disabled") ?
        document.querySelector(".pagination-next.disabled").classList.remove("disabled") : "";
    if (isFirst) {
        document.querySelector(".pagination-prev").classList.add("disabled");
    }
    if (isLast) {
        document.querySelector(".pagination-next").classList.add("disabled");
    }
    if (list.matchingItems.length <= perPage) {
        document.querySelector(".pagination-wrap").style.display = "none";
    } else {
        document.querySelector(".pagination-wrap").style.display = "flex";
    }

    if (list.matchingItems.length == perPage) {
        document.querySelector(".pagination.listjs-pagination").firstElementChild.children[0].click()
    }

    if (list.matchingItems.length > 0) {
        document.getElementsByClassName("noresult")[0].style.display = "none";
    } else {
        document.getElementsByClassName("noresult")[0].style.display = "block";
    }
});

const xhttp = new XMLHttpRequest();
xhttp.onload = function () {
    var json_records = JSON.parse(this.responseText);
        Array.from(json_records).forEach(function (element) {

            tarjetasList.add({
            id: `<a href="javascript:void(0);" class="fw-medium link-primary">#TB${element.id}</a>`,
                codtarj    : element.codtarj,
                descrip    : element.descrip,
                bs         : (element.bs      == "1")? 1:0,
                dolares    : (element.dolares == "1")? 1:0,
                pesos      : (element.pesos   == "1")? 1:0,
                activo     : isStatus(element.activo)

            });
            tarjetasList.sort('id', { order: "desc" });
            refreshCallbacks();
        });

        tarjetasList.remove("id", `<a href="javascript:void(0);" class="fw-medium link-primary">#TB01</a>`);
}
xhttp.open("GET", "satarj/json");
xhttp.send();

isCount = new DOMParser().parseFromString(
    tarjetasList.items.slice(-1)[0]._values.id,
    "text/html"
);

var isValue = isCount.body.firstElementChild.innerHTML;

function isStatus(val) {

    switch (val) {
        case "Activo":
            return (
                '<span class="badge badge-soft-success text-uppercase">' +
                val +
                "</span>"
            );
        case "Inactivo":
            return (
                '<span class="badge badge-soft-danger text-uppercase">' +
                val +
                "</span>"
            );
    }
}

var idField         = document.getElementById("id-field"),
    codtarj         = document.getElementById("codtarj-field"),
    descrip         = document.getElementById("descrip-field"),
    bsField         = document.getElementById("bs-field"),
    dolaresField    = document.getElementById("dolares-field"),
    pesosField      = document.getElementById("pesos-field"),
    activoField     = document.getElementById("account-status-field"),
    addBtn          = document.getElementById("add-btn"),
    editBtns        = document.getElementsByClassName("edit-item-btn"),
    removeBtns      = document.getElementsByClassName("remove-item-btn");

refreshCallbacks();

var activoVal = new Choices(activoField, {
    searchEnabled: false
});

document.getElementById("showModal").addEventListener("show.bs.modal", function (e) {
    if (e.relatedTarget.classList.contains("edit-item-btn")) {
        document.getElementById("exampleModalLabel").innerHTML = "Modificar Instrumento de Pago";
        document.getElementById("showModal").querySelector(".modal-footer").style.display = "block";
        document.getElementById("add-btn").innerHTML = "Modificar";
        $('#codtarj-field').prop("disabled", true);
    } else if (e.relatedTarget.classList.contains("add-btn")) {
        document.getElementById("exampleModalLabel").innerHTML = "Informaci&oacute;n Instrumento de Pago nuevo";
        document.getElementById("showModal").querySelector(".modal-footer").style.display = "block";
        document.getElementById("add-btn").innerHTML = "+1 Instrumento de Pago";
        $('#codtarj-field').prop("disabled", false);
    } else {
        document.getElementById("exampleModalLabel").innerHTML = "Listado de Instrumento de Pagos";
        document.getElementById("showModal").querySelector(".modal-footer").style.display = "none";
    }
});


function refreshCallbacks() {
    // removeBtns
    if (removeBtns){
        Array.from(removeBtns).forEach(function (btn) {
            btn.addEventListener("click", function (e) {
                e.target.closest("tr").children[1].innerText;
                itemId = e.target.closest("tr").children[1].innerText;

                var itemValues = tarjetasList.get({
                    id: itemId,
                });

                Array.from(itemValues).forEach(function (x) {
                    deleteid = new DOMParser().parseFromString(x._values.id, "text/html");

                    var isElem = deleteid.body.firstElementChild;
                    var isdeleteid = deleteid.body.firstElementChild.innerHTML;

                    if (isdeleteid == itemId) {


                        $('#delete-record').unbind('click').bind('click',function () {
                                itemId = itemId.replace("#TB", "");

                                $.ajax({
                                    type: 'DELETE',
                                    url: 'instpago/'+itemId,
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data:{   },
                                    success: function (data) {
                                        tarjetasList.remove("id", isElem.outerHTML);
                                        document.getElementById("deleteRecord-close").click();
                                        var tarjetascounter = Object.keys(tarjetasList.items).length;

                                        $('.tarjetascounter').html(tarjetascounter);
                                        /*Swal.fire({
                                            position: 'center',
                                            icon: 'success',
                                            title: 'Instrumento borrado!',
                                            showConfirmButton: false,
                                            timer: 2000,
                                            showCloseButton: true
                                        });
*/
                                        Swal.fire({
                                            title: 'Any fool can use a computer',
                                            confirmButtonClass: 'btn btn-primary w-xs mt-2',
                                            buttonsStyling: false,
                                            showCloseButton: true
                                        })
                                    }
                                });

                        });
                    }
                });
            });
        });
    }

    // editBtns
    if (editBtns){
        Array.from(editBtns).forEach(function (btn) {
            btn.addEventListener("click", function (e) {

                e.target.closest("tr").children[1].innerText;
                itemId = e.target.closest("tr").children[1].innerText;
                var itemValues = tarjetasList.get({
                    id: itemId,
                });

                Array.from(itemValues).forEach(function (x) {
                    isid = new DOMParser().parseFromString(x._values.id, "text/html");
                    var selectedid = isid.body.firstElementChild.innerHTML;

                    if (selectedid == itemId) {
                        editlist = true;
                        idField.value         = selectedid;
                        descrip.value         = x._values.descrip;
                        codtarj.value         = x._values.codtarj;
                        bsField.value         = x._values.bs;
                        dolaresField.value    = x._values.dolares;
                        pesosField.value      = x._values.pesos;

                        if(x._values.bs)
                            $('#bs-field').prop( "checked", true );
                        else
                            $('#bs-field').prop( "checked", false );

                        if(x._values.dolares)
                            $('#dolares-field').prop( "checked", true );
                        else
                            $('#dolares-field').prop( "checked", false );

                        if(x._values.pesos)
                            $('#pesos-field').prop( "checked", true );
                        else
                            $('#pesos-field').prop( "checked", false );

                        // statusVal
                        if (activoVal) activoVal.destroy();
                        activoVal = new Choices(activoField, {
                            searchEnabled: false
                        });
                        var val = new DOMParser().parseFromString(x._values.activo, "text/html");
                        var statusSelec = val.body.firstElementChild.innerHTML;
                        activoVal.setChoiceByValue(statusSelec);

                    }
                });
            });
        });
    };
};

var count = 12;
var forms = document.querySelectorAll('.tablelist-form')

Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {

        event.preventDefault();

        var errorMsg = document.getElementById("alert-error-msg");
        errorMsg.classList.remove("d-none");

        setTimeout(() => errorMsg.classList.add("d-none"), 2000);

        var text;

        if (codtarj.value == "") {
            text = "Codigo del instrumento de pago  es requerido";
            errorMsg.innerHTML = text;
            return false;
        }else if (descrip.value == "") {
            text = "Nombre instrumento de pago es requerido";
            errorMsg.innerHTML = text;
            return false;
        }else if (bsField.value == "" && dolaresField.value == "" && pesosField.value == "") {
            text = "Debe seleccionar el tipo de moneda que representa el inst de pago";
            errorMsg.innerHTML = text;
            return false;
        }
        if (
            codtarj.value !== "" &&
            descrip.value !== "" &&
            !editlist
        ) {



            var codtarjdata = codtarj.value;
            var descripdata = descrip.value;
            var bs          = (bsField.checked == true)?      1: '0';
            var dolares     = (dolaresField.checked == true)? 1: '0';
            var pesos       = (pesosField.checked == true)?   1: '0';


            $.ajax({
                type: 'POST',
                url: 'instpago',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data:{   codtarj: codtarjdata, descrip: descripdata, dolares  : dolares, bs  : bs, pesos  : pesos},
                success: function (data) {
                     window.location.href="instpago";

                }
            });


        }
        else if (
            codtarj.value     !== ""    &&
            descrip.value     !== ""    &&
            activoField.value !== ""    &&
            editlist
        ) {
            var editValues = tarjetasList.get({
                id: idField.value,
            });

            Array.from(editValues).forEach(function (x) {
                isid = new DOMParser().parseFromString(x._values.id, "text/html");

                var selectedid = isid.body.firstElementChild.innerHTML;
                if (selectedid == itemId) {

                    itemId = itemId.replace("#TB", "");
                    x.values({
                        id: '<a href="javascript:void(0);" class="fw-medium link-primary">' + idField.value + "</a>",
                        codtarj      : codtarj.value,
                        descrip      : descrip.value,
                        bs           : bsField.value,
                        dolares      : dolaresField.value,
                        pesos        : pesosField.value,
                        activo       : isStatus(activoField.value),
                    });

                    $.ajax({
                        type: 'PUT',
                        url: 'instpago/'+itemId,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data:{codtarj      : codtarj.value,
                              descrip      : descrip.value,
                              bs           : bsField.value,
                              dolares      : dolaresField.value,
                              pesos        : pesosField.value,
                              activo       : activoField.value },
                        success: function (data) {

                        }
                    });

                }
            });

            document.getElementById("alert-error-msg").classList.add("d-none");
            document.getElementById("close-modal").click();

            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Instrumento de pago actualizado exitosamente!',
                showConfirmButton: false,
                timer: 2000,
                showCloseButton: true
            });

            clearFields();
        }
        return true;
    })
});

// choices status
var statusInput = new Choices(document.getElementById('idStatus'), {
    searchEnabled: false,
    shouldSort: false,
});

statusInput.passedElement.element.addEventListener('change', function (event) {
    var statusInputValue = event.detail.value;
    tarjetasList.filter(function (data) {
        matchData = new DOMParser().parseFromString(
            data.values().activo,
            "text/html"
        );
        var status = matchData.body.firstElementChild.innerHTML;
        var statusFilter = false;

        if (status == "All" || statusInputValue == "All") {
            statusFilter = true;
        } else {
            statusFilter = status == statusInputValue;
        }
        if (statusFilter) {
            return statusFilter;
        }
    });

    tarjetasList.update();
}, false);

function clearFields() {
    codtarj.value            = "";
    descrip.value            = "";
    bsField.value            = "";
    dolaresField.value       = "";
    pesosField.value         = "";
    activoField.value        = "";
    if (activoVal) activoVal.destroy();
        activoVal = new Choices(activoField);
}

document.getElementById("showModal").addEventListener("hidden.bs.modal", function () {
    clearFields();
});

document.querySelector(".pagination-next").addEventListener("click", function () {
    document.querySelector(".pagination.listjs-pagination") ?
        document.querySelector(".pagination.listjs-pagination").querySelector(".active") ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").nextElementSibling.children[0].click() : "" : "";
});

document.querySelector(".pagination-prev").addEventListener("click", function () {
    document.querySelector(".pagination.listjs-pagination") ?
        document.querySelector(".pagination.listjs-pagination").querySelector(".active") ?
            document.querySelector(".pagination.listjs-pagination").querySelector(".active").previousSibling.children[0].click() : "" : "";
});
