var subCategoriesData = [];

var cateField = document.getElementById("categorySelect");
var searchResultList = document.getElementById("searchResultList");
var categoryInput = new Choices(cateField, {
    searchEnabled: false,
    shouldSort: false,
});

var categoryList = null;

var editList = false;

const xhttp = new XMLHttpRequest();

xhttp.onload = function () {
    var json_records = JSON.parse(this.responseText);

    Array.from(json_records).forEach(function (element) {

        subCategoriesData.push( {
            id            : element.id,
            subcategory   : element.subcategory,
            category      : element.category,
            hijos         : element.hijos,
            desseri       : element.desseri
        });

    });
    loaddata()
}

xhttp.open("GET", "sainsta/json");
xhttp.send();

function loaddata(){
    if (document.getElementById("product-sub-categories")) {
        categoryList = new gridjs.Grid({
            columns: [
                {
                    name: 'Id',
                    width: '80px',
                    data: (function (row) {
                        return gridjs.html('<div class="fw-medium">' + row.id + '</div>');
                    })
                },
                {
                    name: 'Subcategory',
                    width: '120px'
                },
                {
                    name: 'Category',
                    width: '160px'
                },
                ,{
                    name: 'Action',
                    width: '80px',
                    data: (function (row) {

                        if(row.hijos){
                            return gridjs.html(
                                '<ul class="hstack gap-2 list-unstyled mb-0">\
                                    <li>\
                                        <a href="#" class="badge badge-soft-success" onClick="editCategoryList('+ row.id + ')">Editar</a>\
                                    </li>\
                            </ul>');
                        }else{
                            return gridjs.html(
                                '<ul class="hstack gap-2 list-unstyled mb-0">\
                                    <li>\
                                        <a href="#" class="badge badge-soft-success" onClick="editCategoryList('+ row.id + ')">Editar</a>\
                                </li>\
                                <li>\
                                    <a href="#removeItemModal" data-bs-toggle="modal" class="badge badge-soft-danger"\
                                    onClick="removeItem('+ row.id + ')">Borrar</a>\
                                </li>\
                            </ul>');
                        }
                    })
                },
            ],
            sort: true,
            pagination: {
                limit: 10
            },
            data: subCategoriesData,
        }).render(document.getElementById("product-sub-categories"));
    }

    sortElementsById();

    searchResultList.addEventListener("keyup", function () {
        var inputVal = searchResultList.value.toLowerCase();
        function filterItems(arr, query) {
            return arr.filter(function (el) {
                return el.subcategory.toLowerCase().indexOf(query.toLowerCase()) !== -1 || el.category.toLowerCase().indexOf(query.toLowerCase())   !== -1
            })
        }

        var filterData = filterItems(subCategoriesData, inputVal);

        categoryList.updateConfig({
            data: filterData
        }).forceRender();
    });
}

var createCategoryForm = document.querySelectorAll('.createCategory-form')
Array.prototype.slice.call(createCategoryForm).forEach(function (form) {

    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
        } else {
            event.preventDefault();
            var subcategoryTitle = document.getElementById('SubcategoryTitle').value;
            var categoryInputVal = categoryInput.getValue(true);
            var desseri          = 0;

            if(document.getElementById('desseri').checked){
                desseri = 1;
            }

            if (subcategoryTitle !== "" && categoryInputVal !== ""  && !editList) {

                $.ajax({
                    type: 'POST',
                    url: 'instancias',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{   descrip: subcategoryTitle, insPadre  : categoryInputVal, desseri: desseri},
                    success: function (data) {

                        var newCategoryId = data.id;

                        var newCategory = {
                            'id'          : newCategoryId,
                            "subcategory" : subcategoryTitle,
                            "category"    : categoryInputVal,
                            "desseri"     : desseri,
                        };

                        subCategoriesData.push(newCategory);

                        window.location.href='instancias';
                        categoryList.updateConfig({
                            data: subCategoriesData
                        }).forceRender();
                        clearVal();
                        form.classList.remove('was-validated');
                    }
                });


                //&& categoryDesc !== ""
            }else if(subcategoryTitle !== "" && categoryInputVal !== "" && editList){
                var getEditid = document.getElementById("categoryid-input").value;

                $.ajax({
                    type: 'PUT',
                    url: 'instancias/'+getEditid,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{   descrip: subcategoryTitle, insPadre  : categoryInputVal, desseri: desseri},
                    success: function (data) {

                        window.location.href='instancias';
                        subCategoriesData = subCategoriesData.map(function (item) {
                            if (item.id == getEditid) {
                                var editObj = {
                                    'id': getEditid,
                                    "subcategory": subcategoryTitle,
                                    "category": categoryInputVal,
                                    "desseri" : desseri
                                }
                                return editObj;
                            }
                            return item;
                        });

                        categoryList.updateConfig({
                            data: subCategoriesData
                        }).forceRender();
                        clearVal();
                        form.classList.remove('was-validated');
                        editList = false;
                    }
                });

            } else {
                form.classList.add('was-validated');
            }
            sortElementsById();
        }
    }, false)
});

function editCategoryList(elem){
    var getEditid = elem;
    subCategoriesData = subCategoriesData.map(function (item) {
        if (item.id == getEditid) {
            var setchoice = (item.category)? item.category : '0'
            editList = true;
            document.getElementById("addCategoryLabel").innerHTML = "Editar Instancia";
            document.getElementById("addNewCategory").innerHTML   = "Guardar";
            document.getElementById("categoryid-input").value     = item.id;
            document.getElementById("SubcategoryTitle").value     = item.subcategory;
            categoryInput.setChoiceByValue(setchoice);

            let toggleButton = document.getElementById("desseri");
            if(item.desseri == 1){
                toggleButton.checked = true;
            }else{
                toggleButton.checked = false;
            }

        }
        return item;
    });
}

function removeItem(elem) {
    var getid = elem;
    document.getElementById("remove-category").addEventListener("click", function () {
        function arrayRemove(arr, value) {
            return arr.filter(function (ele) {
                return ele.id != value;
            });
        }
        var filtered = arrayRemove(subCategoriesData, getid);

        subCategoriesData = filtered;
        categoryList.updateConfig({
            data: subCategoriesData
        }).forceRender();

        document.getElementById("close-removecategoryModal").click();


        $.ajax({
            type: 'DELETE',
            url: 'instancias/'+getEditid,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{   },
            success: function (data) {

            }
        });

    });
}

function clearVal() {
    document.getElementById("addCategoryLabel").innerHTML = "Crear Instancia";
    document.getElementById("addNewCategory").innerHTML   = "Ingresar";
    document.getElementById('SubcategoryTitle').value     = "";
    categoryInput.removeActiveItems();
    categoryInput.setChoiceByValue("");
}

function fetchIdFromObj(category) {
    return parseInt(category.id);
}

function findNextId() {
    if (subCategoriesData.length === 0) {
        return 0;
    }
    var lastElementId = fetchIdFromObj(subCategoriesData[subCategoriesData.length - 1]),
        firstElementId = fetchIdFromObj(subCategoriesData[0]);
    return (firstElementId >= lastElementId) ? (firstElementId + 1) : (lastElementId + 1);
}

function sortElementsById() {
    var categories = subCategoriesData.sort(function (a, b) {
        var x = fetchIdFromObj(a);
        var y = fetchIdFromObj(b);

        if (x > y) {
            return -1;
        }
        if (x < y) {
            return 1;
        }
        return 0;
    })
}

