<?php include_once('common.php'); ?>
<style>
    .customer-avatar {
        width: 90px;
        height: 90px;
        margin: auto;
        border-radius: 50%;
        background: linear-gradient(135deg, #0d6efd, #4ea5ff);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        color: #fff;
        box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
    }

    .custom-input {
        border-radius: 12px;
        padding: 12px;
        transition: 0.3s;
    }

    .custom-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        border-color: #86b7fe;
    }

    .modal-content {
        animation: modalFade 0.25s ease;
    }

    @keyframes modalFade {

        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }

    }

    .input-group-text {
        border-radius: 12px 0 0 12px;
    }

    .border-start-0 {
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;
    }

    @media(max-width:768px) {

        .modal-body {
            padding: 20px !important;
        }

        .customer-avatar {
            width: 75px;
            height: 75px;
            font-size: 30px;
        }

    }
</style>

<main class="app-main">

<style>
   .select2-container .select2-selection--single{
    height:38px !important;
    border:1px solid #ced4da !important;
    border-radius:.375rem !important;
}

.select2-container .select2-selection--single .select2-selection__rendered{
    line-height:36px !important;
    padding-left:12px !important;
}

.select2-container .select2-selection--single .select2-selection__arrow{
    height:36px !important;
}

.select2-container{
    width:100% !important;
}
</style>

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Create Booking</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <form id="bookingForm">
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label">
                                    Booking Date
                                </label>
                                <input type="date" class="form-control" min="<?php echo date('Y-m-d'); ?>"
                                    name="booking_date" id="booking_date" required>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label">
                                    Return Date
                                </label>
                                <input type="date" class="form-control" name="return_date" id="return_date"
                                    min="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="col-md-2 mb-3">
                                <label class="form-label">
                                    Booking Days
                                </label>
                                <input type="text" class="form-control bg-light" id="booking_days" readonly
                                    value="0 Days">
                            </div>

                           <div class="col-md-6 mb-3">
                                <label class="form-label">Customer</label>

                                <div class="d-flex">
                                    <div style="flex:1;">
                                        <select id="customer_id" name="customer_id"></select>
                                    </div>

                                    <button type="button" class="btn btn-success ms-2" id="btnNewCustomer">
                                        New
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Search Product</label>
                                <select id="product_search" style="width:100%;"></select>
                            </div>
                        </div>

                        <hr>
                        <!-- SELECTED ITEMS -->
                        <div class="card border-0 shadow-sm mt-4">
                            <div class="card-header bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bi bi-cart-check-fill text-primary me-2"></i>
                                        Selected Booking Items
                                    </h6>
                                    <span class="badge bg-primary" id="selected_count">
                                        0 Items
                                    </span>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th width="120">Qty</th>
                                                <th width="100">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selected_product_table">
                                            <tr id="emptyRow">
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    <i class="bi bi-bag-x-fill fs-3 d-block mb-2"></i>
                                                    No Items Added
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Collection Type</label>
                                    <select class="form-select" id="collection_type" name="collection_type">
                                        <option value="SELF">
                                            Collect Same Customer
                                        </option>
                                        <option value="OTHER_INVOICE">
                                            Collect By Other Customer (Same Invoice)
                                        </option>
                                        <option value="OTHER_HOLD">
                                            Collect By Other Customer (Hold Amount Only)
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div id="otherCustomerSection" style="display:none;">
                                <div class="card border-warning mb-3">
                                    <div class="card-header">
                                        Other Customer Details
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Customer Name</label>
                                                <input type="text"
                                                    class="form-control"
                                                    name="other_customer_name">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Phone Number</label>
                                                <input type="text"
                                                    class="form-control"
                                                    name="other_customer_phone">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">NIC Number</label>
                                                <input type="text"
                                                    class="form-control"
                                                    name="other_customer_nic">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Booking Amount</label>
                                    <input type="text" id="booking_amount" class="form-control" name="booking_amount">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Advance Amount</label>
                                    <input type="text" id="advance_amount" value="0.00" class="form-control" name="advance_amount">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Balance Amount</label>
                                    <input type="text" id="balance_amount" value="0.00" class="form-control" name="balance_amount" readonly>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-control" id="payment_method" name="payment_method">
                                        <option value="Cash" selected>Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Cash + Card">Cash + Card</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Pay by Month End">Pay by Month End</option>
                                    </select>
                                </div>
                            </div>

                             <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Hold Amount</label>
                                        <input type="text" id="hold_amount" value="0.00" class="form-control" name="hold_amount">
                                    </div>

                                    <div class="col-md-4 mb-3" id="holdTypeDiv" style="display:none;">
                                        <label class="form-label">Hold Amount Type</label>
                                        <select class="form-select" id="hold_amount_type" name="hold_amount_type">
                                            <option value="Cash">Cash</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4 mb-3" id="bankDetailsDiv" style="display:none;">
                                        <label class="form-label">Bank Details</label>
                                        <textarea class="form-control" id="bank_details" name="bank_details" rows="2"
                                            placeholder="Enter Return bank transfer details"></textarea>
                                    </div>
                                </div>

                                <div class="row" id="depositPaymentRow" style="display:none;">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Today Paid Amount</label>
                                        <input type="text" id="today_paid_amount" value="0.00"
                                            class="form-control" name="today_paid_amount">
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Hold Balance Amount</label>
                                        <input type="text" id="hold_balance_amount" value="0.00"
                                            class="form-control" name="hold_balance_amount" readonly>
                                    </div>
                                </div>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                        <label class="form-label">
                                        Additional Remarks
                                        </label>
                                        <textarea class="form-control"
                                                name="remarks"
                                                rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                        <button type="button" class="btn btn-primary" id="btnSaveBooking">
                            Create Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- CUSTOMER MODAL -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- HEADER -->
            <div class="modal-header bg-primary text-white border-0">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="bi bi-person-plus-fill me-2"></i>
                        Add New Customer
                    </h5>
                    <small class="opacity-75">
                        Create customer profile for booking
                    </small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <!-- BODY -->
            <div class="modal-body p-4">
                <form id="customerForm">
                    <div class="text-center mb-4">
                        <div class="customer-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    </div>
                    <!-- TYPE -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Customer Type
                        </label>
                        <select class="form-select custom-input" name="customer_type" id="customer_type">
                            <option value="INDIVIDUAL">
                                Individual Customer
                            </option>
                            <option value="SALON">
                                Salon / Business
                            </option>
                        </select>
                    </div>
                    <!-- NAME -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Customer Name
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person-fill text-primary"></i>
                            </span>
                            <input type="text" class="form-control custom-input border-start-0" name="customer_name"
                                id="customer_name" placeholder="Enter customer name" required>
                        </div>
                    </div>
                    <!-- PHONE -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Phone Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone-fill text-success"></i>
                            </span>
                            <input type="text" class="form-control custom-input border-start-0" name="phone" id="phone"
                                placeholder="0771234567" required>
                        </div>
                    </div>
                    <!-- NIC -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            NIC Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-credit-card-2-front-fill text-warning"></i>
                            </span>
                            <input type="text" class="form-control custom-input border-start-0" name="nic" id="nic"
                                placeholder="Enter NIC number">
                        </div>
                    </div>
                    <!-- ADDRESS -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 align-items-start pt-3">
                                <i class="bi bi-geo-alt-fill text-danger"></i>
                            </span>
                            <textarea class="form-control custom-input border-start-0" name="address" id="address"
                                rows="3" placeholder="Enter customer address"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <canvas id="barcodeCanvas" style="display:none;"></canvas>

            <!-- FOOTER -->
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" id="btnSaveCustomer">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Save Customer
                </button>
            </div>
        </div>
    </div>
</div>

<div id="barcodePrintArea" style="display:none;">
    <svg id="barcode"></svg>
    <div id="barcodeText"></div>
</div>


<?php include_once('footer.php'); ?>

<script>
    let selectedProducts = [];

    $(document).ready(function () {

        function getNumber(value){
            return parseFloat(String(value).replace(/,/g,'')) || 0;
        }

        function calculateHoldBalance(){

            let holdAmount = getNumber($('#hold_amount').val());
            let paidAmount = getNumber($('#today_paid_amount').val());

            if(paidAmount > holdAmount){

                paidAmount = holdAmount;

                $('#today_paid_amount').val(
                    holdAmount.toLocaleString('en-US',{
                        minimumFractionDigits:2,
                        maximumFractionDigits:2
                    })
                );
            }

            let balance = holdAmount - paidAmount;

            $('#hold_balance_amount').val(
                balance.toLocaleString('en-US',{
                    minimumFractionDigits:2,
                    maximumFractionDigits:2
                })
            );
        }

        $('#hold_amount').on('input', function(){

            let holdAmount = getNumber($(this).val());

            if(holdAmount > 0){

                $('#holdTypeDiv').show();
                $('#depositPaymentRow').show();

            }else{

                $('#holdTypeDiv').hide();
                $('#bankDetailsDiv').hide();
                $('#depositPaymentRow').hide();

                $('#today_paid_amount').val('0.00');
                $('#hold_balance_amount').val('0.00');
            }

            calculateHoldBalance();
        });

        $('#today_paid_amount').on('input', function(){
            calculateHoldBalance();
        });

        $('#customer_id').on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        function toggleHoldFields(){

            let holdAmount = $('#hold_amount').val().replace(/,/g,'');

            if(parseFloat(holdAmount) > 0){
                $('#holdTypeDiv').show();
            }else{
                $('#holdTypeDiv').hide();
                $('#bankDetailsDiv').hide();
                $('#hold_amount_type').val('Cash');
                $('#bank_details').val('');
            }
        }

        $('#hold_amount').on('input', function(){
            toggleHoldFields();
        });

        $('#hold_amount_type').on('change', function(){

            if($(this).val() === 'Bank Transfer'){
                $('#bankDetailsDiv').show();
            }else{
                $('#bankDetailsDiv').hide();
                $('#bank_details').val('');
            }
        });

        $('#product_search').on('select2:open', function () {
            document.querySelector('.select2-search__field').focus();
        });

        $('#product_search').prop('disabled', true);

        $('#booking_date,#return_date').on('change', function(){

            let booking_date = $('#booking_date').val();
            let return_date = $('#return_date').val();

            if(booking_date && return_date){
                $('#product_search').prop('disabled', false);
            }else{
                $('#product_search').prop('disabled', true);
                $('#product_search').val(null).trigger('change');
            }
        });

        $('#btnNewCustomer').click(function () {
            $('#customerModal').modal('show');
        });

        $("#collection_type").on("change", function () {
                var type = $(this).val();

                if (type === "SELF") {
                    $("#otherCustomerSection").hide();
                    $("#booking_amount").prop("readonly", false);
                    $("#advance_amount").prop("readonly", false);
                    $("#hold_amount").prop("readonly", false);
                }
                else if (type === "OTHER_INVOICE") {
                    $("#otherCustomerSection").show();
                    $("#booking_amount").prop("readonly", false);
                    $("#advance_amount").prop("readonly", false);
                    $("#hold_amount").prop("readonly", false);
                }
                else if (type === "OTHER_HOLD") {
                    $("#otherCustomerSection").show();
                    $("#advance_amount").val("0").prop("readonly", true);
                    $("#hold_amount").prop("readonly", false);
                }
            });
            // Run once when page loads
            $("#collection_type").trigger("change");
        });

        function getNumber(value){
            return parseFloat(String(value).replace(/,/g,'')) || 0;
        }

        $(document).on('input','#booking_amount,#advance_amount,#hold_amount',function(){

            let value = $(this).val().replace(/,/g,'');

            // Allow only digits and decimal point
            value = value.replace(/[^\d.]/g,'');

            // Allow only one decimal point
            let parts = value.split('.');
            if(parts.length > 2){
                value = parts[0] + '.' + parts.slice(1).join('');
                parts = value.split('.');
            }

            // Limit to 2 decimal places
            if(parts.length === 2){
                parts[1] = parts[1].substring(0,2);
                value = parts[0] + '.' + parts[1];
            }

            // Add commas to integer part
            if(value !== ''){
                parts = value.split('.');
                parts[0] = Number(parts[0] || 0).toLocaleString('en-US');
                value = parts.length > 1 ? parts[0] + '.' + parts[1] : parts[0];
            }

            $(this).val(value);

            calculateBalance();
        });

        $('#btnSaveCustomer').click(function () {
            if ($('#customer_name').val() == '' || $('#phone').val() == '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Enter Customer Name and Phone number'
                });
                return;
            }
            $.ajax({
                url: "../routes/customer/add_customer.php",
                type: "POST",
                data: $('#customerForm').serialize(),
                success: function (res) {
                    res = res.trim();
                    if (res == "01") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Customer Added'
                        });
                        $('#customerModal').modal('hide');
                        $('#customerForm')[0].reset();
                    } else if (res == "04") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Customer Already Exists'
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

        function calculateBalance(){
            let booking = getNumber($('#booking_amount').val());
            let advance = getNumber($('#advance_amount').val());

            if(advance > booking){
                advance = booking;
                $('#advance_amount').val(booking.toLocaleString('en-US',{
                    minimumFractionDigits:2,
                    maximumFractionDigits:2
                }));
            }

            let balance = booking - advance;

            $('#balance_amount').val(balance.toLocaleString('en-US',{
                minimumFractionDigits:2,
                maximumFractionDigits:2
            }));
        }

        function calculateBookingDays() {
            let bookingDate = $('#booking_date').val();
            let returnDate = $('#return_date').val();

            if (bookingDate != '' && returnDate != '') {

                let start = new Date(bookingDate);
                let end = new Date(returnDate);

                let diff = end - start;

                let days = Math.floor(diff / (1000 * 60 * 60 * 24)) + 1;

                if (days < 1) {
                    days = 1;
                }

                $('#booking_days').val(days + ' Days');

            }
        }

        $('#booking_date').change(function () {
            let bookingDate = $(this).val();
            $('#return_date').attr('min', bookingDate);
            let currentReturn = $('#return_date').val();

            if (currentReturn == '' || currentReturn < bookingDate) {
                $('#return_date').val(bookingDate);
            }

            calculateBookingDays();
        });

        $('#return_date').change(function () {
            calculateBookingDays();
        });

        $('#customer_id').select2({
            placeholder: 'Search Name / Phone / NIC',
            minimumInputLength: 3,
            width: '100%',
            ajax: {
                url: '../routes/booking/search_customer.php',
                dataType: 'json',
                delay: 300,
                data: function(params){
                    return {
                        search: params.term
                    };
                },
                processResults: function(data){
                    return {
                        results: data
                    };
                }
            }
        });

        $('#product_search').select2({
            placeholder: 'Search Product Name or Code',
            minimumInputLength: 3,
            width: '100%',
            ajax: {
                url: '../routes/booking/search_products.php',
                dataType: 'json',
                delay: 100,
                data: function(params){
                    return {
                        search: params.term,
                        booking_date: $('#booking_date').val(),
                        return_date: $('#return_date').val()
                    };
                },
                processResults: function(data){
                    return {
                        results: data
                    };
                }
            }
        });

        $('#product_search').on('select2:select', function(e){

            let data = e.params.data;

            let pid = data.id;
            let pname = data.product_name;

            let booking_date = $('#booking_date').val();
            let return_date = $('#return_date').val();

            let exists = selectedProducts.find(
                item => item.product_id == pid
            );

            let nextQty = exists ? exists.qty + 1 : 1;

            $.ajax({
                url: "../routes/booking/check_product_qty.php",
                type: "POST",
                data: {
                    product_id: pid,
                    qty: nextQty,
                    booking_date: booking_date,
                    return_date: return_date
                },
                success: function(res){

                    res = res.trim();

                    if(res == "01"){

                        if(exists){
                            exists.qty++;
                        }else{
                            selectedProducts.push({
                                product_id: pid,
                                product_name: pname,
                                qty: 1
                            });
                        }

                        renderSelectedProducts();

                        $('#product_search').val(null).trigger('change');

                    }else{

                        Swal.fire({
                            icon: 'question',
                            title: 'Out of Stock',
                            html: `
                                This item is not available in your stock for the selected period.<br><br>
                                Do you want to add this item to the booking anyway?<br>
                                You can purchase or create the item before the booking date.
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Add Item',
                            cancelButtonText: 'No'
                        }).then((result) => {

                            if(result.isConfirmed){

                                if(exists){
                                    exists.qty++;
                                }else{
                                    selectedProducts.push({
                                        product_id: pid,
                                        product_name: pname,
                                        qty: 1,
                                        out_of_stock: 1
                                    });
                                }

                                renderSelectedProducts();

                                $('#product_search').val(null).trigger('change');
                            }
                        });
                    }
                }
            });
        });

        $('#btnSaveBooking').click(function () {
            if ($('#booking_date').val() == '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Booking Date'
                });
                return;
            }
            if ($('#return_date').val() == '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Return Date'
                });
                return;
            }
            if ($('#customer_id').val() == '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Customer'
                });
                return;
            }
            if (selectedProducts.length <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Products'
                });
                return;
            }
            if ($('input[name=booking_amount]').val() == '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Enter Booking Amount'
                });
                return;
            }
            if ($('input[name=advance_amount]').val() == '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Enter Advance Amount'
                });
                return;
            }
            if ($('input[name=hold_amount]').val() == '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Enter Hold Amount'
                });
                return;
            }

           
            Swal.fire({
                title: 'Enter PIN',
                html: `<input type="password" id="swal_pin" class="swal2-input" maxlength="6" autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false" placeholder="Enter 6 Digit PIN">`,
                showConfirmButton: false,
                showCancelButton: true,

                didOpen: () => {
                    $('#swal_pin').focus();
                    $('#swal_pin').on('input', function () {
                        let pin = $(this).val();
                        if (pin.length == 6) {

                            $.ajax({
                                url: '../routes/auth/verify_pin.php',
                                type: 'POST',
                                data: {
                                    pin: pin
                                },
                                success: function (res) {

                                    let data = JSON.parse(res);
                                    if (data.status == "error") {
                                        $('#swal_pin').val('');
                                        Swal.showValidationMessage(
                                            'Wrong PIN');
                                        return;
                                    }
                                    Swal.close();
                                    Swal.fire({
                                        title: 'Confirm Booking?',
                                        html: 'Booking create By : <b>' +
                                            data.name + '</b>',
                                        icon: 'question',
                                        showCancelButton: true,
                                        confirmButtonText: 'Save Booking'
                                    }).then((confirmResult) => {
                                        if (confirmResult
                                            .isConfirmed) {

                                            Swal.fire({
                                                title: 'Saving Booking...',
                                                allowOutsideClick: false,
                                                didOpen: () => {
                                                    Swal
                                                .showLoading();
                                                }
                                            });

                                            $.ajax({
                                                url: "../routes/booking/save_booking.php",
                                                type: "POST",
                                                data: {
                                                    booking_date: $('#booking_date').val(),
                                                    return_date: $('#return_date').val(),
                                                    customer_id: $('#customer_id').val(),

                                                    collection_type: $('#collection_type').val(),
                                                    other_customer_name: $('input[name=other_customer_name]').val(),
                                                    other_customer_phone: $('input[name=other_customer_phone]').val(),
                                                    other_customer_nic: $('input[name=other_customer_nic]').val(),

                                                    booking_amount: $('#booking_amount').val().replace(/,/g,''),
                                                    advance_amount: $('#advance_amount').val().replace(/,/g,''),
                                                    balance_amount: $('#balance_amount').val().replace(/,/g,''),
                                                    payment_method: $('#payment_method').val(),

                                                    hold_amount: $('#hold_amount').val().replace(/,/g,''),
                                                    paid_amount: $('#today_paid_amount').val().replace(/,/g,''),
                                                    hold_amount_type: $('#hold_amount_type').val(),
                                                    bank_details: $('#bank_details').val(),

                                                    remarks: $('textarea[name=remarks]').val(),
                                                    createdby: data.id,
                                                    products: JSON
                                                        .stringify(
                                                            selectedProducts
                                                            )
                                                },
                                                success: function (res) {
                                                    let response =JSON.parse(res);
                                                    if (response.status =="success") {
                                                        Swal.fire({
                                                                icon: 'success',
                                                                title: 'Booking Created',
                                                                html: 'Booking ID : <b>' + response.booking_id +'</b>'
                                                            }).then((result) => {
                                                                    if (result.isConfirmed) {
                                                                        // location
                                                                        //     .reload();
                                                                        $.get("../routes/booking/getbooking.php",{ booking_id: response.booking_id },function(res){
                                                                            const jdata=JSON.parse(res);
                                                                           // printbarcode(jdata.booking.barcode);
                                                                            generateBookingReceiptPDF(jdata);
                                                                            if(response.printtype == "two"){
                                                                                generateBookingReceipt2PDF(jdata);
                                                                            }
                                                                        });
                                                                    }
                                                                }
                                                            );
                                                    } else {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: response.message
                                                        });
                                                    }
                                                }
                                            });
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });
        });

        function generateBookingReceiptPDF(data) {

            const {
                jsPDF
            } = window.jspdf;

            function checkPageBreak(heightNeeded) {
                if (y + heightNeeded > 270) {
                    doc.addPage();
                    y = 20;
                }
            }
            const doc = new jsPDF();
            let y = 20;
            const img = new Image();
            img.src = '../../assets/ui/logo.png';

            doc.setFillColor(38, 0, 8);
            doc.rect(0, 0, 210, 42, 'F');

            doc.addImage(img, 'PNG', 12, 6, 38, 30);

            doc.setTextColor(255, 255, 255);

            doc.setFontSize(24);
            doc.setFont(undefined, 'bold');
            doc.text("Siri Kirula Pvt. Ltd.", 60, 12, {
                align: 'left'
            });

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text(data.station.address, 60, 17, {
                align: 'left'
            });
            doc.text(data.station.contact_no, 60, 22, {
                align: 'left'
            });
            doc.text("sirikirula@gmail.com", 60, 27, {
                align: 'left'
            });

            doc.setDrawColor(255, 215, 0);
            doc.setLineWidth(0.25);
            doc.line(60, 30, 195, 30);

            JsBarcode("#barcodeCanvas",data.booking.barcode,{
            format:"CODE128",
            displayValue:false,
            width:1.5,
            height:25,
            margin:0
            });

            const barcodeCanvas=document.getElementById('barcodeCanvas');
            const barcodeImage=barcodeCanvas.toDataURL("image/png");

            doc.setFillColor(255,255,255);
            doc.setDrawColor(220,220,220);
            doc.setLineWidth(0.2);

            doc.roundedRect(143,9,61,14,1.5,1.5,'FD');

            doc.addImage(barcodeImage,'PNG',146,11,55,10);

            doc.setFontSize(15);
            doc.setFont(undefined, 'bold');
            doc.text("BOOKING CONFIRMATION", 60, 37, {
                align: 'left'
            });

            doc.setTextColor(0, 0, 0);

            y += 25;

            doc.setDrawColor(38, 0, 8);
            doc.setLineWidth(0.4);
            doc.roundedRect(10, y, 190, 45, 0, 0);
            doc.setTextColor(0, 0, 0);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("BOOKING INFORMATION", 15, y + 5);
            doc.setDrawColor(180, 180, 180);
            doc.setLineWidth(0.2);

            doc.line(15, y + 6, 195, y + 6);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Booking ID", 15, y + 12);
            doc.text(": " + data.booking.id, 45, y + 12);
            doc.text("Booked Time", 110, y + 12);
            doc.text(": " + data.booking.created_at, 145, y + 12);
            doc.text("Booking Date", 15, y + 18);
            doc.text(": " + data.booking.booking_date, 45, y + 18);
            doc.text("Return Date", 110, y + 18);
            doc.text(": " + data.booking.return_date, 145, y + 18);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("CUSTOMER INFORMATION", 15, y + 27);
            doc.line(15, y + 28, 195, y + 28);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Customer", 15, y + 34);
            doc.text(": " + data.booking.customer_name, 45, y + 34);
            doc.text("Phone", 15, y + 40);
            doc.text(": " + data.booking.phone, 45, y + 40);
            doc.text("NIC", 110, y + 40);
            doc.text(": " + data.booking.nic, 145, y + 40);
            y += 48;

            doc.setFillColor(25, 135, 84);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');

            doc.text("Booked Items", 15, y + 4);
            y += 8;
            doc.setTextColor(0, 0, 0);

            doc.setFillColor(240, 240, 240);

            doc.rect(10, y, 15, 7, 'FD');
            doc.rect(25, y, 90, 7, 'FD');
            doc.rect(115, y, 35, 7, 'FD');
            doc.rect(150, y, 50, 7, 'FD');

            doc.setFontSize(9);
            doc.setFont(undefined, 'bold');

            doc.text("#", 16, y + 5);
            doc.text("Product", 60, y + 5);
            doc.text("Code",120,y+5);
            doc.text("Qty",170,y+5);

            y += 7;

            doc.setFont(undefined, 'normal');

            let count = 1;

            data.items.forEach(function (item) {

                checkPageBreak(20);
                doc.rect(10, y, 15, 7);
                doc.rect(25, y, 90, 7);
                doc.rect(115, y, 35, 7);
                doc.rect(150, y, 50, 7);
                doc.text(String(count), 16, y + 5);
                let pname = item.product_name;
                if (pname.length > 38) {
                    pname = pname.substring(0, 38) + '...';
                }
                doc.text(pname, 28, y + 5);
                doc.text(item.product_code,120,y+5);
                doc.text(String(item.qty),172,y+5);
                y += 7;
                count++;
            });

            y += 7;

            checkPageBreak(20);
            doc.setFont(undefined, 'bold');

            doc.text("Payment Summary", 118, y + 10);

            doc.text("Full Amount :", 118, y + 15);
            doc.text(parseFloat(data.booking.booking_amount).toFixed(2), 175, y + 15);

            doc.text("Advance Amount :", 118, y + 21);
            doc.text(parseFloat(data.booking.advance_amount).toFixed(2), 175, y + 21);

            doc.text("Diposit Amount :", 118, y + 27);
            doc.text(parseFloat(data.booking.hold_amount).toFixed(2), 175, y + 27);

            doc.text("Diposit Advance :", 118, y + 33);
            doc.text(parseFloat(data.booking.hold_payed_amount).toFixed(2), 175, y + 33);

            y += 40;

            doc.setFontSize(8);
            doc.setFont(undefined, 'bold');

            checkPageBreak(20);

            doc.text("Important Terms & Conditions :", 15, y);

            doc.setFont(undefined, 'normal');

            doc.text("• All rented jewellery items must be returned on or before the agreed return date.", 15,
                y + 5);
            doc.text("• Late returns will be charged with additional penalty payments.", 15, y + 9);
            doc.text("• Customers are responsible for damages, missing items, breakages or losses.", 15, y +
                13);
            doc.text("• Repair or replacement charges will apply according to item condition.", 15, y + 17);
            doc.text("• Advance and hold payments are non-refundable under company policy.", 15, y + 21);

            y += 42;

            doc.setDrawColor(120, 120, 120);

            doc.line(20, y, 80, y);
            doc.line(130, y, 190, y);

            doc.setFontSize(9);
            doc.setFont(undefined, 'bold');

            doc.text("Customer Signature", 33, y + 6);

            doc.text("Authorized Signature", 142, y + 6);

            y += 18;
            y += 8;
            // Footer
            // Generate QR Code
            var qr = new QRious({
                value: 'https://sirikirulakandy.lk/',
                size: 100 // size in px
            });
            // Get Base64 image of QR code
            var qrImage = qr.toDataURL(); // default is PNG
            // Existing footer text setup
            const footerY = 280;
            doc.line(15, footerY - 4, 195, footerY - 4);
            doc.setFont('helvetica', 'italic');
            doc.setFontSize(9);
            const footerText =
                "Our Services: Bridal Jewellery Rental • Kandiyan & Indian Wedding Jewellery • Luxury Bridal Sets • Fashion Jewellery Collections • Wedding Accessories • Jewellery Sales & Rental Services • Custom Bridal Packages • QR Enabled Rental Management • Island-wide Customer Support ";
            const wrappedFooter = doc.splitTextToSize(footerText, 160); // Reduced width to allow space for QR
            doc.text(wrappedFooter, 10, footerY);
            // Add QR code to the bottom-right (adjust X and Y for placement)
            doc.addImage(qrImage, 'PNG', 175, footerY, 15, 15); // x, y, width, height
            window.open(doc.output('bloburl'), '_blank');

            ///doc.save("Booking_Receipt_"+data.booking.id+".pdf");

        }

        function generateBookingReceipt2PDF(data) {

            const {
                jsPDF
            } = window.jspdf;

            function checkPageBreak(heightNeeded) {
                if (y + heightNeeded > 270) {
                    doc.addPage();
                    y = 20;
                }
            }
            const doc = new jsPDF();
            let y = 20;
            const img = new Image();
            img.src = '../../assets/ui/logo.png';

            doc.setFillColor(38, 0, 8);
            doc.rect(0, 0, 210, 42, 'F');

            doc.addImage(img, 'PNG', 12, 6, 38, 30);

            doc.setTextColor(255, 255, 255);

            doc.setFontSize(24);
            doc.setFont(undefined, 'bold');
            doc.text("Siri Kirula Pvt. Ltd.", 60, 12, {
                align: 'left'
            });

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text(data.station.address, 60, 17, {
                align: 'left'
            });
            doc.text(data.station.contact_no, 60, 22, {
                align: 'left'
            });
            doc.text("sirikirula@gmail.com", 60, 27, {
                align: 'left'
            });

            doc.setDrawColor(255, 215, 0);
            doc.setLineWidth(0.25);
            doc.line(60, 30, 195, 30);

            JsBarcode("#barcodeCanvas",data.booking.barcode,{
            format:"CODE128",
            displayValue:false,
            width:1.5,
            height:25,
            margin:0
            });

            const barcodeCanvas=document.getElementById('barcodeCanvas');
            const barcodeImage=barcodeCanvas.toDataURL("image/png");

            doc.setFillColor(255,255,255);
            doc.setDrawColor(220,220,220);
            doc.setLineWidth(0.2);

            doc.roundedRect(143,9,61,14,1.5,1.5,'FD');

            doc.addImage(barcodeImage,'PNG',146,11,55,10);

            doc.setFontSize(15);
            doc.setFont(undefined, 'bold');
            doc.text("BOOKING CONFIRMATION", 60, 37, {
                align: 'left'
            });

            doc.setTextColor(0, 0, 0);

            y += 25;

            doc.setDrawColor(38, 0, 8);
            doc.setLineWidth(0.4);
            doc.roundedRect(10, y, 190, 45, 0, 0);
            doc.setTextColor(0, 0, 0);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("BOOKING INFORMATION", 15, y + 5);
            doc.setDrawColor(180, 180, 180);
            doc.setLineWidth(0.2);

            doc.line(15, y + 6, 195, y + 6);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Booking ID", 15, y + 12);
            doc.text(": " + data.booking.id, 45, y + 12);
            doc.text("Booked Time", 110, y + 12);
            doc.text(": " + data.booking.created_at, 145, y + 12);
            doc.text("Booking Date", 15, y + 18);
            doc.text(": " + data.booking.booking_date, 45, y + 18);
            doc.text("Return Date", 110, y + 18);
            doc.text(": " + data.booking.return_date, 145, y + 18);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("CUSTOMER INFORMATION", 15, y + 27);
            doc.line(15, y + 28, 195, y + 28);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Customer", 15, y + 34);
            doc.text(": " + data.booking.other_customer_name, 45, y + 34);
            doc.text("Phone", 15, y + 40);
            doc.text(": " + data.booking.other_customer_phone, 45, y + 40);
            doc.text("NIC", 110, y + 40);
            doc.text(": " + data.booking.other_customer_nic, 145, y + 40);
            y += 48;

            doc.setFillColor(25, 135, 84);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');

            doc.text("Booked Items", 15, y + 4);
            y += 8;
            doc.setTextColor(0, 0, 0);

            doc.setFillColor(240, 240, 240);

            doc.rect(10, y, 15, 7, 'FD');
            doc.rect(25, y, 90, 7, 'FD');
            doc.rect(115, y, 35, 7, 'FD');
            doc.rect(150, y, 50, 7, 'FD');

            doc.setFontSize(9);
            doc.setFont(undefined, 'bold');

            doc.text("#", 16, y + 5);
            doc.text("Product", 60, y + 5);
            doc.text("Code",120,y+5);
            doc.text("Qty",170,y+5);

            y += 7;

            doc.setFont(undefined, 'normal');

            let count = 1;

            data.items.forEach(function (item) {

                checkPageBreak(20);
                doc.rect(10, y, 15, 7);
                doc.rect(25, y, 90, 7);
                doc.rect(115, y, 35, 7);
                doc.rect(150, y, 50, 7);
                doc.text(String(count), 16, y + 5);
                let pname = item.product_name;
                if (pname.length > 38) {
                    pname = pname.substring(0, 38) + '...';
                }
                doc.text(pname, 28, y + 5);
                doc.text(item.product_code,120,y+5);
                doc.text(String(item.qty),172,y+5);
                y += 7;
                count++;
            });

            y += 7;

            checkPageBreak(20);
            doc.setFont(undefined, 'bold');

            doc.text("Payment Summary", 118, y + 10);

            doc.text("Diposit Amount :", 118, y + 15);
            doc.text(parseFloat(data.booking.hold_amount).toFixed(2), 175, y + 15);

            doc.text("Diposit Advance :", 118, y + 21);
            doc.text(parseFloat(data.booking.hold_payed_amount).toFixed(2), 175, y + 21);

            y += 40;

            doc.setFontSize(8);
            doc.setFont(undefined, 'bold');

            checkPageBreak(20);

            doc.text("Important Terms & Conditions :", 15, y);

            doc.setFont(undefined, 'normal');

            doc.text("• All rented jewellery items must be returned on or before the agreed return date.", 15,
                y + 5);
            doc.text("• Late returns will be charged with additional penalty payments.", 15, y + 9);
            doc.text("• Customers are responsible for damages, missing items, breakages or losses.", 15, y +
                13);
            doc.text("• Repair or replacement charges will apply according to item condition.", 15, y + 17);
            doc.text("• Advance and hold payments are non-refundable under company policy.", 15, y + 21);

            y += 42;

            doc.setDrawColor(120, 120, 120);

            doc.line(20, y, 80, y);
            doc.line(130, y, 190, y);

            doc.setFontSize(9);
            doc.setFont(undefined, 'bold');

            doc.text("Customer Signature", 33, y + 6);

            doc.text("Authorized Signature", 142, y + 6);

            y += 18;
            y += 8;
            // Footer
            // Generate QR Code
            var qr = new QRious({
                value: 'https://sirikirulakandy.lk/',
                size: 100 // size in px
            });
            // Get Base64 image of QR code
            var qrImage = qr.toDataURL(); // default is PNG
            // Existing footer text setup
            const footerY = 280;
            doc.line(15, footerY - 4, 195, footerY - 4);
            doc.setFont('helvetica', 'italic');
            doc.setFontSize(9);
            const footerText =
                "Our Services: Bridal Jewellery Rental • Kandiyan & Indian Wedding Jewellery • Luxury Bridal Sets • Fashion Jewellery Collections • Wedding Accessories • Jewellery Sales & Rental Services • Custom Bridal Packages • QR Enabled Rental Management • Island-wide Customer Support ";
            const wrappedFooter = doc.splitTextToSize(footerText, 160); // Reduced width to allow space for QR
            doc.text(wrappedFooter, 10, footerY);
            // Add QR code to the bottom-right (adjust X and Y for placement)
            doc.addImage(qrImage, 'PNG', 175, footerY, 15, 15); // x, y, width, height
            window.open(doc.output('bloburl'), '_blank');

            ///doc.save("Booking_Receipt_"+data.booking.id+".pdf");

        }

        function renderSelectedProducts() {

            let html = "";

            if(selectedProducts.length <= 0){

                html = `
                <tr id="emptyRow">
                    <td colspan="3" class="text-center text-muted py-4">
                        <i class="bi bi-bag-x-fill fs-3 d-block mb-2"></i>
                        No Items Added
                    </td>
                </tr>`;
                
            }else{

                selectedProducts.forEach((item,index) => {

                    let stockBadge = '';

                    if(item.out_of_stock == 1){
                        stockBadge = `
                        <span class="badge bg-warning text-dark ms-2">
                            Pending Stock
                        </span>`;
                    }

                    html += `
                    <tr>
                        <td>
                            <div class="fw-semibold">
                                ${item.product_name}
                                ${stockBadge}
                            </div>
                        </td>

                        <td>
                            <div class="input-group input-group-sm">
                                <button 
                                    type="button"
                                    class="btn btn-outline-secondary btn-minus"
                                    data-index="${index}">
                                    -
                                </button>

                                <input 
                                    type="text"
                                    class="form-control text-center"
                                    value="${item.qty}"
                                    readonly>

                                <button 
                                    type="button"
                                    class="btn btn-outline-secondary btn-plus"
                                    data-index="${index}">
                                    +
                                </button>
                            </div>
                        </td>

                        <td>
                            <button 
                                type="button"
                                class="btn btn-danger btn-sm btn-remove-item"
                                data-index="${index}">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </td>
                    </tr>`;
                });
            }

            $('#selected_product_table').html(html);

            let pendingCount = selectedProducts.filter(
                item => item.out_of_stock == 1
            ).length;

            if(pendingCount > 0){
                $('#selected_count').html(
                    selectedProducts.length +
                    ' Items <span class="badge bg-warning text-dark ms-1">' +
                    pendingCount +
                    ' Pending Stock</span>'
                );
            }else{
                $('#selected_count').html(
                    selectedProducts.length + ' Items'
                );
            }
        }

        $(document).on('click', '.btn-plus', function () {
            let index = $(this).data('index');
            let item = selectedProducts[index];
            let nextQty = item.qty + 1;
            $.ajax({
                url: "../routes/booking/check_product_qty.php",
                type: "POST",
                data: {
                    product_id: item.product_id,
                    qty: nextQty,
                    booking_date: $('#booking_date').val(),
                    return_date: $('#return_date').val()
                },
                success: function (res) {
                    res = res.trim();
                    if (res == "01") {
                        selectedProducts[index].qty++;
                        renderSelectedProducts();
                    } else {
                         Swal.fire({
                            icon: 'question',
                            title: 'Out of Stock',
                            html: `
                                The requested quantity is not available for the selected booking period.<br><br>
                                Do you want to continue anyway?<br>
                                You can purchase or create additional stock before the booking date.
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Yes, Continue',
                            cancelButtonText: 'No'
                        }).then((result) => {

                            if(result.isConfirmed){

                                selectedProducts[index].qty++;

                                selectedProducts[index].out_of_stock = 1;

                                renderSelectedProducts();
                            }
                        });
                    }
                }
            });
        });

        $(document).on('click', '.btn-minus', function () {
            let index = $(this).data('index');
            if (selectedProducts[index].qty > 1) {
                selectedProducts[index].qty--;
                renderSelectedProducts();
            }
        });

        $(document).on('click', '.btn-remove-item', function () {
            let index = $(this).data('index');
            selectedProducts.splice(index, 1);
            renderSelectedProducts();
        });

        function printbarcode(productCode){
           
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
                                height: 20,
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
        }
    
</script>