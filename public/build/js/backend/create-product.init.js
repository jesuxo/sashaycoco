 //* choices category input
var productCategoryInput = new Choices('#choices-category-input', {
    searchEnabled: false,
    shouldSort: false,
});

var editinputValueJson = sessionStorage.getItem('editInputValue');
if (editinputValueJson) {
    var editinputValueJson = JSON.parse(editinputValueJson);
    document.getElementById("formAction").value = "edit";
    document.getElementById("product-id-input").value = editinputValueJson.id;
    productCategoryInput.setChoiceByValue(editinputValueJson.category);
    myDropzone.options.addedfile.call(myDropzone, mockFile);
    myDropzone.options.thumbnail.call(myDropzone, mockFile, editinputValueJson.productImg);
    thumbnailArray.push(editinputValueJson.productImg)
    document.getElementById("descrip").value = editinputValueJson.productTitle;
    document.getElementById("stocks-input").value = editinputValueJson.stock;
    document.getElementById("product-price-input").value = editinputValueJson.price;
    document.getElementById("product-discount-input").value = editinputValueJson.discount;
    document.getElementById("orders-input").value = editinputValueJson.orders;

    // clothe-colors
    Array.from(document.querySelectorAll(".clothe-colors li")).forEach(function (subElem) {
        var nameelem = subElem.querySelector('[type="checkbox"]');
        editinputValueJson.color.map(function(subItem){
            if (subItem == nameelem.value) {
                nameelem.setAttribute("checked", "checked");
            }
        })
    })

    // clothe-size
    Array.from(document.querySelectorAll(".clothe-size li")).forEach(function (subElem) {
        var nameelem = subElem.querySelector('[type="checkbox"]');
        if(editinputValueJson.size){
            editinputValueJson.size.map(function(subItem){
                if (subItem == nameelem.value) {
                    nameelem.setAttribute("checked", "checked");
                }
            })
        }
    })
}

var forms = document.querySelectorAll('.needs-validation')


Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
        $('#invalidcodprod').html('C&oacute;digo');
        $('#invalidcodprod').removeClass('text-danger');

        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            event.preventDefault();

            var productCategoryValue = productCategoryInput.getValue(true);

            var codprod    = document.getElementById("codprod").value;
            var formAction = document.getElementById("formAction").value;

            if (formAction == "add" && productCategoryValue !== "" ) {
                var check = 0;

                $.ajax({
                    type: 'POST',
                    url: '/saprod/check/codprod/'+codprod,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{ },
                    success: function (data) {
                        check = data.check;
                        if(check)
                            document.getElementById("createproduct-form").submit();
                        else{
                            $('#invalidcodprod').html('C&oacute;digo -- ERROR :: '+codprod+' Ya Existente');
                            $('#invalidcodprod').addClass('text-danger');
                            $('#invalidcodprod').select();
                            document.getElementById("codprod").value = '';
                        }
                    }
                });

            }else if (formAction == "edit" && productCategoryValue !== ""  ) {

                document.getElementById("createproduct-form").submit();
            }else {
                console.log('Form Action Not Found.');
            }

            return false;
        }

        form.classList.add('was-validated');

    }, false)
});
