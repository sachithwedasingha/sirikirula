<?php include_once('common.php'); ?>
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Rental Summery</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-body">
                    <div class="row">

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Scan Barcode</label>
                            <input type="text" class="form-control" id="searchbarcode" autocomplete="off">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label class="form-label">Select Date</label>
                            <input type="date" class="form-control" id="search_date"
                                value="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Customer</label>
                            <select class="form-control" id="customer_id"></select>
                        </div>

                        <div class="col-md-2 mb-3 d-grid">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary" id="btnSearchRental">
                                Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        Rental Booking List
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th>Booking Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody id="rental_summary_table">
                                <tr>
                                    <td colspan="7">
                                        <div class="alert alert-info mb-0">
                                            Search a booking using Barcode, Date or Customer
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="booking_detail_wrapper" style="border:2px solid #fd7e14;border-radius:12px;background:#fffaf3;padding:15px;">
                <div id="booking_detail_area">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center p-5">
                            <h5 class="text-muted">
                                Booking details will appear here
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    $(document).ready(function () {

        $('#customer_id').select2({
            width: '100%',
            placeholder: 'Search Customer'
        });

        $.get("../routes/customer/dropdown.php", function (res) {

            $('#customer_id').html(res);

        });

    });

    $('#btnSearchRental').click(function () {

        $.get("../routes/booking/search_rental_summary.php", {

            barcode: $('#searchbarcode').val(),
            booking_date: $('#search_date').val(),
            customer_id: $('#customer_id').val()

        }, function (res) {
            let data = JSON.parse(res);
            $('#booking_detail_area').html("");
            $('#rental_summary_table').html(data.table);
        });

    });

    $('#searchbarcode').on('input', function () {
        let barcode = $(this).val().trim();
        if (barcode.length < 10) {
            return;
        }
        $.get("../routes/booking/search_rental_summary.php", {
            barcode: barcode
        }, function (res) {
            let data = JSON.parse(res);
            $('#booking_detail_area').html("");
            $('#rental_summary_table').html(data.table);
            if (data.booking_id != '') {
                loadBookingSummary(data.booking_id);
            }
        });
    });

    $(document).on('click', '.btn-view-summary', function () {
        loadBookingSummary($(this).data('id'));
    });

    function loadBookingSummary(bookingId) {
        $.get("../routes/booking/get_rental_summary.php", {
            booking_id: bookingId
        }, function (res) {
           $('#booking_detail_area')
        .html(res)
        .css({
            'border':'2px solid #fd7e14',
            'border-radius':'12px',
            'background':'#fffaf3',
            'padding':'15px'
        });
        });
    }
</script>
<?php include_once('footer.php'); ?>