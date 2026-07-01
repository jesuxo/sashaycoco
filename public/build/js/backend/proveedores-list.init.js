



var perPage = 10;
var editlist = false;

var options = {
    valueNames: [
        "id",
        "codprov",
        "descrip",
        "telef",
        "date"
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


var proveedorList = new List("proveedorList", options).on("updated", function (list) {
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

        console.log(json_records);
        Array.from(json_records).forEach(function (element) {

            proveedorList.add({
                id: `<a href="javascript:void(0);" class="fw-medium link-primary">#VZ${element.id}</a>`,
                codprov  : element.codprov,
                descrip  : '<div class="d-flex align-items-center gap-2">\
                            <div class="flex-shrink-0"><img src="build/images/users/user-dummy-img.jpg" alt="" class="avatar-xs rounded-circle user-profile-img"></div>\
                            <div class="flex-grow-1 ms-2 user_name">'+element.descrip+'</div>\
                            </div>',
                telef      : element.telef,
                date      : element.datelabel,
                datelabel : element.datelabel,
            });

            proveedorList.sort('id', { order: "desc" });

        });

    proveedorList.remove("id", `<a href="javascript:void(0);" class="fw-medium link-primary">#VZ01</a>`);
}
xhttp.open("GET", "saprov/json");
xhttp.send();

var count = 11;

/*
isCount = new DOMParser().parseFromString(
    proveedorList.items.slice(-1)[0]._values.id,
    "text/html"
);

var isValue = isCount.body.firstElementChild.innerHTML;
*/
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
