
var productListData = [];
var productList = null;

const xhttp = new XMLHttpRequest();

var slider = document.getElementById('product-price-range');

xhttp.onload = function () {
    var json_records = JSON.parse(this.responseText);

    Array.from(json_records).forEach(function (element) {

        productListData.push( {
            id            : (element.id)   ?element.id    : 1,
            productImg    : (element.image)?element.image : 0,
            price         : (element.price)? element.price: 0,
            fijo          : (element.fijo)? '#' + element.fijo : 0,
            exdecimal     :  element.exdecimal,
            productTitle  : (element.productTitle)? element.productTitle: 0,
            category      : (element.category)? element.category: 0
        });
    });

    loaddata();

}


var editinputValueJson = sessionStorage.getItem('editInputValue');
if (editinputValueJson) {
    editinputValueJson = JSON.parse(editinputValueJson);
    productListData = productListData.map(function (item) {
        if (item.id == editinputValueJson.id) {
            return editinputValueJson;
        }
        return item;
    });
}

function loaddata(){

    if (document.getElementById("product-list")) {
        productList = new gridjs.Grid({
            columns: [
                {
                    name: '#',
                    width: '40px',
                    sort: {
                        enabled: false
                    },
                    data: (function (row) {
                        return gridjs.html('<div class="form-check checkbox-product-list">\
                            <input class="form-check-input" type="checkbox" value="'+ row.id + '" id="checkbox-' + row.id + '">\
                            <label class="form-check-label" for="checkbox-'+ row.id + '"></label>\
                        </div>');
                    })
                },
                {
                    name: 'Producto',
                    sort: {
                        enabled: true
                    },
                    data: (function (row) {
                        var num = 1;
                        /*if (row.color) {
                            var colorElem = '<ul class="clothe-colors list-unstyled hstack gap-1 mb-0 flex-wrap d-none">';
                            Array.from(row.color).forEach(function (elem) {
                                num++;
                                colorElem += '<li>\
                                                    <input type="radio" name="color'+ row.id + '" id="product-color-' + row.id + num + '">\
                                                    <label class="avatar-xxs border border-2 border-white btn btn-'+ elem + ' p-0 d-flex align-items-center justify-content-center rounded-circle" for="product-color-' + row.id + num + '"></label>\
                                                </li>';
                            })
                            colorElem += '</ul>';
                        } else {
                            var colorElem = '';
                        }*/

                        /*if (row.size) {
                            var sizeElem = '<ul class="clothe-size list-unstyled hstack gap-2 mb-0 flex-wrap d-none">';
                            Array.from(row.size).forEach(function (elem) {
                                num++;
                                sizeElem += '<li>\
                                                    <input type="radio" name="sizes'+ row.id + '" id="product-size-' + row.id + num + '">\
                                                    <label class="avatar-xxs border border-2 border-white btn btn-soft-primary text-uppercase p-0 fs-13 d-flex align-items-center justify-content-center rounded-circle" for="product-size-'+ row.id + num + '">' + elem + '</label>\
                                                </li>';
                            })
                            sizeElem += '</ul>';
                        } else {
                            var sizeElem = '';
                        }*/
                        var decimal = (row.exdecimal == "1")? 'Si' : 'No';
                        return gridjs.html('<div class="d-flex align-items-center">\
                            <div class="flex-shrink-0 me-2 avatar-sm">\
                                <div class="avatar-title bg-light rounded">\
                                    <a href="/productos/' + row.id + '/edit" class="d-block text-reset">\
                                    <img src="'+ row.productImg + '" alt="" class="avatar-xs" />\
                                    </a>\
                                </div>\
                            </div>\
                            <div class="flex-grow-1">\
                                <h6 class="mb-1"><a href="/productos/' + row.id + '/edit" class="d-block text-reset">'+row.fijo+' '+ row.productTitle +  ' ExDec:'+ decimal +'</a></h6>\
                                <p class="mb-0 text-muted">Instancia : <span class="fw-medium">'+ row.category + '</span></p>\
                            </div>\
                        </div>'); //+ colorElem + sizeElem
                    }),
                    width: '400px',
                },

                {
                    name: 'Precio',
                    sort: {
                        enabled: true
                    },
                    data: (function (row) {
                        var discount = 0;
                        /* var text = row.discount;
                         var myArray = text.split("%");
                         var discount = myArray[0];
                         var afterDiscount = row.price - (row.price * discount / 100);*/
                        var afterDiscount = row.price;

                        if (discount > 0) {
                            var afterDiscountElem = '<div>$' + afterDiscount.toFixed(2) + ' <span class="text-muted fs-14"><del>$' + row.price + '</del></span></div>'
                        } else {
                            var afterDiscountElem = '<div>$' + row.price + '</div>'
                        }
                        return gridjs.html(afterDiscountElem);
                    }),
                    width: '60px',
                },
                {
                    name: 'Opciones',
                    width: '80px',
                    data: (function (row) {
                        return gridjs.html('<div class="text-center dropdown">\
                        <a href="javascript:void(0);" class="btn btn-ghost-primary btn-icon btn-sm" data-bs-toggle="dropdown" aria-expanded="false" class=""><i class="mdi mdi-dots-horizontal"></i></a>\
                        <ul class="dropdown-menu dropdown-menu-end">\
                            <li>\
                                <a class="dropdown-item" onClick="editProductList(' + row.id + ')" href="/productos/' + row.id + '/edit">\
                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Modificar</a>\
                            </li>\
                            <li>\
                                <a class="dropdown-item remove-list" onClick="removeItem(' + row.id + ')" data-bs-toggle="modal" href="#removeItemModal">\
                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>Inactivar</a>\
                            </li>\
                        </ul>\
                    </div>');
                    })
                },
            ],
            sort: true,
            pagination: {
                limit: 10
            },
            data: productListData,
        }).render(document.getElementById("product-list"));
    };



}




document.getElementById("addproduct-btn").addEventListener("click", function () {
    sessionStorage.setItem('editInputValue', "")
})


function isStatus(val) {
    switch (val) {
        case "In stock":
            return ('<span class="badge badge-soft-success align-middle ms-1">' + val + '</span>');
        case "Out of stock":
            return ('<span class="badge badge-soft-danger align-middle ms-1">' + val + '</span>');
    }
}

function editProductList(elem) {
    var getEditid = elem;
    productListData = productListData.map(function (item) {
        if (item.id == getEditid) {
            sessionStorage.setItem('editInputValue', JSON.stringify(item));
        }
        return item;
    });
};

// removeItem event
function removeItem(elem) {
    var getid = elem;
    document.getElementById("remove-product").addEventListener("click", function () {
        function arrayRemove(arr, value) {
            return arr.filter(function (ele) {
                return ele.id != value;
            });
        }
        var filtered = arrayRemove(productListData, getid);

        productListData = filtered;
        productList.updateConfig({
            data: productListData
        }).forceRender();

        document.getElementById("close-removeproductModal").click();
    });
}

