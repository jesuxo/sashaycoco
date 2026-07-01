/*
Template Name: Toner SISDATO - Sistema dado para todos
File: transferencias init js
*/

function getTime(params) {
    params = new Date(params);
    if (params.getHours() != null) {
        var hour = params.getHours();
        var minute = (params.getMinutes()) ? params.getMinutes() : 00;
        return hour + ":" + minute;
    }
}

function tConvert(time) {
    var d = new Date(time);
    time_s = (d.getHours() + ':' + d.getMinutes());
    var t = time_s.split(":");
    var hours = t[0];
    var minutes = t[1];
    var newformat = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    return (hours + ':' + minutes + ' ' + newformat);
}

var str_dt = function formatDate(date) {
    var monthNames = ["Ene", "Feb", "Mar", "Abr", "May", "Jun",
        "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"
    ];
    var d = new Date(date),
        month = '' + monthNames[(d.getMonth())],
        day   = '' + d.getDate(),
        year  = d.getFullYear();
    if (month.length < 2)
        month = '0' + month;
    if (day.length < 2)
        day = '0' + day;
    return [day + " " + month, year].join(', ');
};

var date = new Date();
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();

var Invoices = [];

const xhttp = new XMLHttpRequest();

xhttp.onload = function () {
    var json_records = JSON.parse(this.responseText);

    Array.from(json_records).forEach(function (element) {

        Invoices.push( {
            id            : (element.id)         ? element.id          : 1,
            fecha         : (element.fecha)      ? element.fecha       : '',
            numero        : (element.numero)     ? element.numero      : '',
            observacion   : (element.observacion)? element.observacion : '',
            titular       :  element.titular,
            monto         : (element.monto)      ? element.monto       : 0,
            status        : (element.status)     ? element.status      : 0,
            bancoquery    : (element.bancoquery) ? element.bancoquery  : 0,
            currency      : (element.currency)   ? element.currency    : 0,
            telefono      : (element.telefono)   ? element.telefono    : '',
            enlace        : (element.enlace)     ? element.enlace      : '',
            observacion   : (element.observacion)? element.observacion      : '',

        });
    });
    loaddata();
}

var fechas = document.getElementById('fechas').value;
if(!fechas)
    fechas = 0;

var status = document.getElementById('status').value;
if(!status)
    status = 22222;

var busquedatransf = document.getElementById('busquedatransf').value;
if(!busquedatransf)
    busquedatransf = 0;

if(fechas) {
    fechas = fechas.replace(/\//g, "-");
    fechas = fechas.replace(/ to /g, "--");
}

xhttp.open("POST", "/transferencias/json/"+busquedatransf+"/"+status+"/"+fechas);
xhttp.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
xhttp.send();


function loaddata() {

    Array.from(Invoices).forEach(function (raw) {
        let badge = '';
        let etiqueta = '';
        switch (raw.status) {
            case 1:
                etiqueta = "Aprobada";
                badge = "success";
                break;
            case 0:
                etiqueta = "Pendiente";
                badge = "primary";
                break;
            case 2:
                etiqueta = "Rechazada";
                badge = "danger";
        }

        var avtar_title = (raw.titular).split(" ");
        var letters = null;
        var first_letter = avtar_title[0].slice(0, 1);
        letters = first_letter

        var avatar_ = `<div class="flex-shrink-0 avatar-xs me-2"><div class="avatar-title bg-success-subtle text-success rounded-circle fs-12">` + letters + `</div></div>`;
                /*<th scope="row">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="chk_child" value="#TTB${raw.id}">
                    </div>
                </th>*/

        var tableRawData = `<tr class="tr` + raw.id + `">

                <td class="numero"><a href="transferencias/edit/` + raw.id + `"  class="fw-medium link-primary">` + raw.numero + `</a></td>
                <td class="titular">
                    <div class="d-flex align-items-center">
                        ` + avatar_ + raw.titular + `
                    </div>
                </td>
                <td class="banco">`    + raw.bancoquery + `</td>
                <td class="date">`     + raw.fecha + `</td>
                <td class="monto" align="right">` + raw.currency + (raw.monto) + `</td>
                <td class="status"> <span class="badge badge-soft-` + badge + ` text-uppercase">` + etiqueta + `</span>
                </td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-soft-secondary btn-sm dropdown btn-icon" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-more-fill align-middle"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" >`;
                 if(raw.status == 0){
                     tableRawData = tableRawData +
                         `  <li>
                                <a class="dropdown-item" target="_blank" href="`+ raw.enlace +`">
                                    Aprobaci&oacute;n
                                </a>
                            </li>
`;
                       }
        tableRawData = tableRawData +   `
                            <li class="dropdown-divider"></li>
                            <li>
                                <a  onclick="$('#deleterecord').data('id',` + raw.id + `)" class="dropdown-item remove-item-btn" data-bs-toggle="modal" href="#deleteOrder">
                                    <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                    Eliminar
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr class="tr` + raw.id + `">
             <td class="titular" colspan="7" style="font-size: 11px; color: darkgray;">
                        Observacion: ` +  raw.observacion + `
                </td>
            </tr>
`;

        document.getElementById('transferencias-list-data').innerHTML += tableRawData;
    });

}

