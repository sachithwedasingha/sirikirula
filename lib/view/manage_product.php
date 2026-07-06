<?php include_once('common.php'); ?>

<style>
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        background: #fff;
    }

    .form-control {
        border-radius: 10px;
    }

    .btn {
        border-radius: 10px;
    }

    table img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #ddd;
    }

    @media(max-width:768px) {

        .table {
            font-size: 13px;
        }

        .btn {
            width: 100%;
        }

    }
    #label{
    width:25.4mm;
    height:20.3mm;
    text-align:center;
    overflow:hidden;
}
#barcodePrintArea{
    text-align:center;
    padding:5px;
}

#barcodeText{
    font-size:10px;
    font-weight:bold;
}

@media print {

    body * {
        visibility: hidden;
    }

    #barcodePrintArea,
    #barcodePrintArea * {
        visibility: visible;
    }

    #barcodePrintArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 25.4mm;
        height: 20.3mm;
        display:block !important;
    }

    @page {
        size: 25.4mm 20.3mm;
        margin: 0;
    }
}
</style>
<main class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Manage Products</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Manage Products
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <!-- LEFT -->
                <div class="col-lg-4 col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong id="form-title">Add Product</strong>
                        </div>
                        <div class="card-body">
                            <form id="productForm" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="productId">
                                 <div class="mb-3">
                                    <label class="form-label">
                                        Category <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control" name="category" id="category" required></select>
                                    <div class="invalid-feedback">
                                        Please select category
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        Product Code <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="product_code" id="product_code"
                                        maxlength="50" required>
                                    <div class="invalid-feedback">
                                        Please enter product code
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        Product Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" name="product_name" id="product_name"
                                        maxlength="255" required>
                                    <div class="invalid-feedback">
                                        Please enter product name
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        Product Details
                                    </label>
                                    <textarea class="form-control" name="product_details" id="product_details" rows="3"
                                        maxlength="300"></textarea>
                                </div>
                               
                                <div class="mb-3">
                                    <label class="form-label">
                                        Supplier
                                    </label>
                                    <select class="form-control" name="supplier" id="supplier"></select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Unit Price <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="0.01" class="form-control" name="unit_price"
                                            id="unit_price" required>
                                        <div class="invalid-feedback">
                                            Please enter unit price
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">
                                            Retail Price <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="0.01" class="form-control" name="retail_price"
                                            id="retail_price" required>
                                        <div class="invalid-feedback">
                                            Please enter retail price
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        Product Image
                                    </label>
                                    <input type="file" class="form-control" name="product_image" id="product_image"
                                        accept="image/png,image/jpeg,image/jpg,image/webp">
                                    <small class="text-muted">
                                        Optional image upload
                                    </small>
                                </div>
                                <div class="mb-3 text-center">
                                    <img src="../../assets/ui/images.png" id="previewImage"
                                        style="width:130px;height:130px;object-fit:cover;border-radius:10px;border:1px solid #ddd;">
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-primary" id="btnSave">
                                        Save Product
                                    </button>
                                    <button type="button" class="btn btn-success" id="btnUpdate" style="display:none;">
                                        Update Product
                                    </button>
                                    <button type="button" class="btn btn-secondary" id="btnClear">
                                        Clear
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="col-lg-8 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <strong>Product List</strong>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                               <table class="table table-bordered table-striped" id="productTable1">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Supplier</th>
                                            <th>Unit</th>
                                            <th>Retail</th>
                                            <th width="180">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product_list"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 

<div id="barcodePrintArea" style="display:none;">
    <svg id="barcode"></svg>
    <div id="barcodeText"></div>
</div>

</main>

<?php include_once('footer.php'); ?>

<script>
    
    $(document).ready(function () {

        loadProductList();
        loadCategoryDropdown();
        loadSupplierDropdown();

        console.log($.fn.DataTable);

        $('#category').change(function(){
            let categoryId=$(this).val();
            if(categoryId==''){
                $('#product_code').val('');
                return;
            }
            $.get(
                '../routes/product/get_next_product_code.php',
                {
                    category_id:categoryId
                },
                function(res){
                    let data=JSON.parse(res);
                    $('#product_code').val(data.product_code);
                }
            );

        });

        function loadCategoryDropdown() {
            $.get("../routes/category/dropdown.php", function (res) {
                $('#category').html(res);
            });
        }

        function loadSupplierDropdown() {
            $.get("../routes/supplier/dropdown.php", function (res) {
                $('#supplier').html(res);
            });
        }

        function loadProductList(){

            $.get("../routes/product/pro_list.php",function(res){
                $('#product_list').html(res);
                if($.fn.DataTable.isDataTable('#productTable1')){
                    $('#productTable1').DataTable().destroy();
                }
                $('#productTable1').DataTable({
                    pageLength:10
                });
            });
        }

        $('#product_image').change(function (e) {
            const file = e.target.files[0];
            if (file) {
                $('#previewImage').attr('src', URL.createObjectURL(file));
            }
        });

        function clearForm() {

            $('#productId').val('');
            $('#product_code').val('');
            $('#product_name').val('');
            $('#product_details').val('');
            $('#category').val('');
            $('#supplier').val('');
            $('#unit_price').val('');
            $('#retail_price').val('');
            $('#product_image').val('');

            $('#previewImage').attr('src', '../../assets/ui/images.png');

            $('#btnSave').show();
            $('#btnUpdate').hide();

            $('#form-title').text('Add Product');

            // REMOVE VALIDATION STATES
            $('#productForm').removeClass('was-validated');

            $('#productForm .form-control').removeClass('is-invalid');
            $('#productForm .form-control').removeClass('is-valid');

            $('#productForm .form-select').removeClass('is-invalid');
            $('#productForm .form-select').removeClass('is-valid');

        }

        function validateProductForm() {
            let form = document.getElementById('productForm');
            if (!form.checkValidity()) {
                $('#productForm').addClass('was-validated');
                return false;
            }
            return true;
        }

        $('#btnClear').click(function () {
            clearForm();
        });

        $('#btnSave').click(function () {
            if (!validateProductForm()) {
                return;
            }
            var formData = new FormData($('#productForm')[0]);
            $.ajax({
                url: "../routes/product/addproduct.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,

                success: function (res) {
                    res = res.trim();
                    if (res == "01") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Product Added'
                        });
                        clearForm();
                        loadProductList();
                    } else if (res == "04") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Product Code Already Exists'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Add Failed'
                        });
                    }
                }
            });
        });

        $(document).on('click', '.btn-edit', function () {
            
            var uid = $(this).data('id');

            $.get("../routes/product/getprodata.php", {
                uid: uid
            }, function (res) {

                var jdata = $.parseJSON(res);

                $('#productId').val(jdata.id);
                $('#product_code').val(jdata.product_code);
                $('#product_name').val(jdata.product_name);
                $('#product_details').val(jdata.product_details);
                $('#category').val(jdata.category);
                $('#supplier').val(jdata.supplier);
                $('#unit_price').val(jdata.unit_price);
                $('#retail_price').val(jdata.retail_price);

                if (jdata.product_image != '') {

                    $('#previewImage').attr('src', '../uploads/product/' + jdata.product_image);

                }

                $('#btnSave').hide();
                $('#btnUpdate').show();
                $('#form-title').text('Edit Product');

            });

        });

        $('#btnUpdate').click(function () {

            if (!validateProductForm()) {
                    return;
                }

            var formData = new FormData($('#productForm')[0]);

            $.ajax({

                url: "../routes/product/editdata.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,

                success: function (res) {
                    res = res.trim();
                    if (res == "01") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Product Updated'
                        });
                        clearForm();
                        loadProductList();
                    } else if (res == "04") {

                        Swal.fire({
                            icon: 'warning',
                            title: 'Product Code Already Exists'
                        });

                    } else {

                        Swal.fire({
                            icon: 'error',
                            title: 'Update Failed'
                        });
                    }
                }
            });
        });

        $(document).on('click', '.btn-delete', function () {

            var uid = $(this).data('id');

            Swal.fire({

                title: 'Are you sure?',
                text: 'This product will be deleted',
                icon: 'warning',
                showCancelButton: true

            }).then((result) => {

                if (result.isConfirmed) {
                    $.ajax({
                        url: "../routes/product/delete_pro.php",
                        type: "POST",
                        data: {
                            uid: uid
                        },

                        success: function (res) {

                            if (res.trim() == "ok") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted'
                                });
                                loadProductList();

                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Delete Failed'
                                });
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click', '.btn-barcode', function () {

            var productCode = $(this).data('code');

            var printWindow = window.open('', '_blank', 'width=400,height=250');

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Barcode Print</title>

                    <script src="../../js/jsbarcode.js"><\/script>

                    <style>
                        @page{
                            margin:0;
                        }

                        body{
                            margin:0;
                            padding:5px;
                            text-align:center;
                            font-family:Arial, sans-serif;
                        }

                        #barcode{
                            margin-top:5px;
                        }

                        .barcode-text{
                            font-size:9px;
                            margin-top:2px;
                        }
                    </style>
                </head>

                <body>

                    <svg id="barcode"></svg>
                    <div class="barcode-text">Siri Kirula -${productCode}</div>

                    <script>

                        window.onload = function(){

                            JsBarcode("#barcode", "${productCode}", {
                                format: "CODE128",
                                width: 0.9,
                                height: 35,
                                margin: 0.5,
                                displayValue: false
                            });

                            setTimeout(function(){

                                window.focus();

                                window.print();

                            }, 1500);

                        };

                        window.onafterprint = function(){
                            window.close();
                        };

                    <\/script>

                </body>
                </html>
            `);

            printWindow.document.close();

        });

        
});
</script>
