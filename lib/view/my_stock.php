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

    table img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #ddd;
    }

    .out-stock {
        background: #ffe5e5 !important;
    }

    .low-stock {
        background: #fff5cc !important;
    }

    .badge {
        font-size: 12px;
        padding: 7px 10px;
    }

    @media(max-width:768px) {

        .table {
            font-size: 13px;
        }

    }
</style>
<main class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">My Stock</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="#">Home</a>
                        </li>
                        <li class="breadcrumb-item active">
                            My Stock
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Station Stock</strong>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStockModal">
                        Add New Stock
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle" id="stockTable">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Product Code</th>
                                    <th>Product Name</th>
                                    <th>Total Items</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="stock_list"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- ADD STOCK MODAL -->

<div class="modal fade" id="addStockModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    Add Stock
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="addStockTable">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Product Name</th>
                                <th width="120">Current Qty</th>
                                <th width="180">Add New Stock</th>
                            </tr>
                        </thead>
                        <tbody id="stock_product_list"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveStock">
                    Save Stocks
                </button>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>

<script>
    $(document).ready(function () {

        loadStockList();

        function loadStockList() {

            $.get("../routes/stock/pro_list.php", function (res) {
                if ($.fn.DataTable.isDataTable('#stockTable')) {
                    $('#stockTable').DataTable().destroy();
                }
                $('#stock_list').html(res);
                $('#stockTable').DataTable({
                    responsive: true,
                    pageLength: 15
                });
            });
        }

        $('#addStockModal').on('shown.bs.modal', function () {
            $.get("../routes/stock/add_stock_product_list.php", function (res) {
                $('#stock_product_list').html(res);
                if ($.fn.DataTable.isDataTable('#addStockTable')) {
                    $('#addStockTable').DataTable().destroy();
                }
                $('#addStockTable').DataTable({
                    pageLength: 10,
                    responsive: true
                });
            });
        });

        $('#btnSaveStock').click(function () {
            let products = [];
            $('.stock-qty').each(function () {
                let qty = $(this).val();
                if (qty > 0) {
                    products.push({
                        product_id: $(this).data('id'),
                        qty: qty
                    });
                }
            });

            if (products.length <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Enter Stock Qty'
                });
                return;
            }

            $.ajax({

                url: "../routes/stock/add_multiple_stock.php",
                type: "POST",
                data: {
                    products: JSON.stringify(products)
                },
                success: function (res) {
                    res = res.trim();
                    if (res == "01") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Stocks Added'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Save Failed'
                        });
                    }
                }
            });
        });
    });
</script>