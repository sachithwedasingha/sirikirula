<?php include_once('common.php'); ?>

<main class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Manage Suppliers</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Manage Suppliers
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">

            <div class="row">

                <div class="col-lg-4 col-md-12">

                    <div class="card mb-4">

                        <div class="card-header">
                            <strong id="form-title">Add Supplier</strong>
                        </div>

                        <div class="card-body">

                            <form id="supplierForm">

                                <input type="hidden" id="supplierId" name="id">

                                <div class="mb-3">
                                    <label class="form-label">Supplier Name</label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                        maxlength="200">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3" required
                                        maxlength="500"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" maxlength="12"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" maxlength="120"
                                        required>
                                </div>

                                <div class="d-grid gap-2">

                                    <button type="button" class="btn btn-primary" id="btnSave">Save Supplier</button>

                                    <button type="button" class="btn btn-success" id="btnUpdate"
                                        style="display:none;">Update Supplier</button>

                                    <button type="button" class="btn btn-secondary" id="btnClear">Clear</button>

                                </div>

                            </form>

                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-md-12">

                    <div class="card">

                        <div class="card-header">
                            <strong>Supplier List</strong>
                        </div>

                        <div class="card-body">

                            <div class="table-responsive">

                                <table class="table table-bordered table-striped" id="supplierTable">

                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th width="180">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody id="supplier_list"></tbody>

                                </table>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</main>

<?php include_once('footer.php'); ?>

<script>
    $(document).ready(function () {

        loadSupplierList();

        function loadSupplierList() {

            $.get("../routes/supplier/pro_list.php", function (res) {

                if ($.fn.DataTable.isDataTable('#supplierTable')) {
                    $('#supplierTable').DataTable().destroy();
                }

                $('#supplier_list').html(res);

                $('#supplierTable').DataTable({
                    responsive: true,
                    pageLength: 10
                });

            });

        }

        function clearForm() {

            $('#supplierId').val('');
            $('#name').val('');
            $('#address').val('');
            $('#phone').val('');
            $('#email').val('');

            $('#btnSave').show();
            $('#btnUpdate').hide();

            $('#form-title').text('Add Supplier');

        }

        $('#btnClear').click(function () {

            clearForm();

        });

        $('#btnSave').click(function () {

            var formData = new FormData($('#supplierForm')[0]);

            $.ajax({

                url: "../routes/supplier/addsupplier.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,

                success: function (res) {

                    res = res.trim();

                    if (res == "01") {

                        Swal.fire({
                            icon: 'success',
                            title: 'Supplier Added'
                        });

                        clearForm();

                        loadSupplierList();

                    } else if (res == "04") {

                        Swal.fire({
                            icon: 'warning',
                            title: 'Supplier Already Exists'
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

            $.get("../routes/supplier/getprodata.php", {
                uid: uid
            }, function (res) {

                var jdata = $.parseJSON(res);

                $('#supplierId').val(jdata.id);
                $('#name').val(jdata.name);
                $('#address').val(jdata.address);
                $('#phone').val(jdata.phone);
                $('#email').val(jdata.email);

                $('#btnSave').hide();
                $('#btnUpdate').show();

                $('#form-title').text('Edit Supplier');

            });

        });

        $('#btnUpdate').click(function () {

            $.ajax({

                url: "../routes/supplier/editdata.php",
                type: "POST",
                data: $('#supplierForm').serialize(),

                success: function (res) {

                    res = res.trim();

                    if (res == "01") {

                        Swal.fire({
                            icon: 'success',
                            title: 'Supplier Updated'
                        });

                        clearForm();

                        loadSupplierList();

                    } else if (res == "04") {

                        Swal.fire({
                            icon: 'warning',
                            title: 'Email Already Exists'
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
                text: 'This supplier will be deleted',
                icon: 'warning',
                showCancelButton: true

            }).then((result) => {

                if (result.isConfirmed) {

                    $.ajax({

                        url: "../routes/supplier/delete_pro.php",
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

                                loadSupplierList();

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

    });
</script>

<style>
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        background: #fff;
        font-weight: 600;
    }

    .form-control {
        border-radius: 10px;
    }

    .btn {
        border-radius: 10px;
    }

    @media(max-width:768px) {

        .table {
            font-size: 13px;
        }

        .btn {
            width: 100%;
        }

    }
</style>