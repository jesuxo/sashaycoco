 //* choices category input
var productCategoryInput = new Choices('#choices-category-input', {
    searchEnabled: false,
    shouldSort: false,
});

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

            var formAction = document.getElementById("formAction").value;

            if (formAction == "edit" && productCategoryValue !== ""  ) {
                document.getElementById("editproduct-form").submit();
            }else {
                console.log('Form Action Not Found.');
            }

            return false;
        }

        form.classList.add('was-validated');

    }, false)
});
