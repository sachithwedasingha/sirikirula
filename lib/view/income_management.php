<?php include_once('common.php'); ?>

<style>
    .select2-container .select2-selection--single{
        height:38px !important;
        border:1px solid #ced4da !important;
        border-radius:0.375rem !important;
        padding:4px 10px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height:28px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height:36px !important;
    }

    .select2-dropdown{
        border:1px solid #ced4da !important;
        border-radius:0.375rem !important;
    }

    .select2-search__field{
        border:1px solid #ced4da !important;
        border-radius:0.375rem !important;
        padding:6px 10px !important;
    }
</style>

<main class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">
                        Income Management
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">

        <div class="card shadow-sm border-0 mb-3">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-2 mb-3">
                        <label class="form-label">
                            Date
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="single_date"
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">
                            From Date
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="from_date">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">
                            To Date
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="to_date">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">
                            Type
                        </label>

                        <select
                            class="form-control"
                            id="income_type">

                            <option value="">
                                All Types
                            </option>

                            <option value="SALE">
                                Sales
                            </option>

                            <option value="RENTAL">
                                Rental
                            </option>

                            <option value="PENALTY">
                                Penalty
                            </option>

                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">
                            Customer
                        </label>

                        <select
                            class="form-control"
                            id="customer_id">

                            <option value="">
                                All Customers
                            </option>

                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">
                            Reference No
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="reference_id"
                            placeholder="SAL / BOO">
                    </div>

                    <div class="col-md-12 text-end">

                        <button
                            type="button"
                            class="btn btn-primary"
                            id="btnLoadIncome">

                            Search

                        </button>

                        <button
                            type="button"
                            class="btn btn-secondary"
                            id="btnResetIncome">

                            Reset

                        </button>

                    </div>

                </div>

            </div>

        </div>

        <!-- Summary -->

        <div class="row mb-3">

            <div class="col-md-3">

                <div class="card border-success shadow-sm">

                    <div class="card-body text-center">

                        <h6 class="text-muted">
                            Total Income
                        </h6>

                        <h4
                            class="text-success mb-0"
                            id="total_income">

                            0.00

                        </h4>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card border-primary shadow-sm">

                    <div class="card-body text-center">

                        <h6 class="text-muted">
                            Sales Income
                        </h6>

                        <h4
                            class="text-primary mb-0"
                            id="sales_income">

                            0.00

                        </h4>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card border-warning shadow-sm">

                    <div class="card-body text-center">

                        <h6 class="text-muted">
                            Rental Income
                        </h6>

                        <h4
                            class="text-warning mb-0"
                            id="rental_income">

                            0.00

                        </h4>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="card border-danger shadow-sm">

                    <div class="card-body text-center">

                        <h6 class="text-muted">
                            Penalty Income
                        </h6>

                        <h4
                            class="text-danger mb-0"
                            id="penalty_income">

                            0.00

                        </h4>

                    </div>

                </div>

            </div>

        </div>

        <!-- Table -->

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead>

                            <tr>

                                <th>
                                    Ref No
                                </th>

                                <th>
                                    Date Time
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Customer
                                </th>

                                <th>
                                    Part
                                </th>

                                <th class="text-end">
                                    Amount
                                </th>

                                <th>
                                    Received By
                                </th>

                                <th width="120">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody id="income_table">

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</main>

<div class="modal fade" id="incomeViewModal">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Income Details
                </h5>

                <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div id="income_view_content">

                </div>

            </div>

        </div>

    </div>

</div>

<?php include_once('footer.php'); ?>

<script>

    $(document).ready(function(){

        loadIncome();

        $('#customer_id').select2({
            placeholder:'Search Customer Name / Phone / NIC',
            minimumInputLength:3,
            width:'100%',
            ajax:{
                url:'../routes/customer/load_customers.php',
                dataType:'json',
                delay:300,
                data:function(params){
                    return{
                        search:params.term
                    };
                },
                processResults:function(data){
                    return{
                        results:data
                    };
                }
            }
        });

        $('#customer_id').on('select2:open',function(){
            setTimeout(function(){
                document.querySelector('.select2-search__field').focus();
            },100);
        });

    });

    function loadIncome(){

        Swal.fire({
            title:'Loading...',
            text:'Please wait',
            allowOutsideClick:false,
            allowEscapeKey:false,
            didOpen:()=>{
                Swal.showLoading();
            }
        });

        $.get(
            '../routes/income/load_income.php',
            {
                single_date:$('#single_date').val(),
                from_date:$('#from_date').val(),
                to_date:$('#to_date').val(),
                customer_id:$('#customer_id').val(),
                income_type:$('#income_type').val(),
                reference_id:$('#reference_id').val()
            },
            function(res){

                Swal.close();

                let data=JSON.parse(res);

                $('#income_table').html(data.table);

                $('#total_income').html(
                    parseFloat(data.total_income || 0)
                    .toLocaleString(undefined,{
                        minimumFractionDigits:2,
                        maximumFractionDigits:2
                    })
                );

                $('#sales_income').html(
                    parseFloat(data.sales_income || 0)
                    .toLocaleString(undefined,{
                        minimumFractionDigits:2,
                        maximumFractionDigits:2
                    })
                );

                $('#rental_income').html(
                    parseFloat(data.rental_income || 0)
                    .toLocaleString(undefined,{
                        minimumFractionDigits:2,
                        maximumFractionDigits:2
                    })
                );

                $('#penalty_income').html(
                    parseFloat(data.penalty_income || 0)
                    .toLocaleString(undefined,{
                        minimumFractionDigits:2,
                        maximumFractionDigits:2
                    })
                );

            }
        );

    }

    $('#btnLoadIncome').click(function(){

        loadIncome();

    });

    $('#btnResetIncome').click(function(){

        $('#single_date').val(
            '<?php echo date("Y-m-d"); ?>'
        );

        $('#from_date').val('');
        $('#to_date').val('');

        $('#income_type').val('');

        $('#customer_id').val(null)
        .trigger('change');

        $('#reference_id').val('');

        loadIncome();

    });

    $('#single_date').change(function(){

        loadIncome();

    });

    $('#income_type').change(function(){

        loadIncome();

    });

    $('#customer_id').change(function(){

        loadIncome();

    });

    $('#from_date').change(function(){

        loadIncome();

    });

    $('#to_date').change(function(){

        loadIncome();

    });

    $('#reference_id').keypress(function(e){

        if(e.which==13){

            loadIncome();

        }

    });

    $(document).on(
        'click',
        '.btn-view-income',
        function(){

            let id=$(this).data('id');

            // Open Income View Modal

        }
    );

    $(document).on(
        'click',
        '.btn-reprint-income',
        function(){

            let id=$(this).data('id');

            // Reprint Receipt

        }
    );

    $(document).on('click','.btn-view-income',function(){

        let id=$(this).data('id');
        let type=$(this).data('type');

        $('#incomeViewModal').modal('show');

        $('#income_view_content').html(
            '<div class="text-center p-5">Loading...</div>'
        );

        if(type=='SALE'){

            $.get(
                '../routes/sales/get_sale_details.php',
                {
                    sale_id:id
                },
                function(res){

                    let data=JSON.parse(res);

                    showSaleDetails(data);

                }
            );

        }else if(type=='RENTAL'){

            $.get(
                '../routes/booking/getbooking.php',
                {
                    booking_id:id
                },
                function(res){

                    let data=JSON.parse(res);

                    showBookingDetails(data);

                }
            );

        }else if(type=='PENALTY'){

            $.get(
                '../routes/penalty/get_penalty.php',
                {
                    booking_id:id
                },
                function(res){

                    let data=JSON.parse(res);

                    showPenaltyDetails(data);

                }
            );

        }

    });

    function showSaleDetails(data){

        let html=`

        <div class="card">

            <div class="card-header">
                Sale Details
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">
                        <strong>Sale No</strong><br>
                        ${data.sale.id}
                    </div>

                    <div class="col-md-4">
                        <strong>Customer</strong><br>
                        ${data.sale.customer_name}
                    </div>

                    <div class="col-md-4">
                        <strong>Phone</strong><br>
                        ${data.sale.phone}
                    </div>

                </div>

            </div>

        </div>

        <table class="table table-bordered mt-3">

            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Amount</th>
                </tr>
            </thead>

            <tbody>
        `;

        $.each(data.items,function(i,item){

            html+=`
            <tr>
                <td>${item.product_name}</td>
                <td>${item.qty}</td>
                <td>${parseFloat(item.amount).toFixed(2)}</td>
            </tr>`;
        });

        html+=`</tbody></table>`;

        $('#income_view_content').html(html);

    }

    function showBookingDetails(data){

        let html=`

        <div class="card">

            <div class="card-header">
                Rental Details
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">
                        <strong>Booking No</strong><br>
                        ${data.booking.id}
                    </div>

                    <div class="col-md-3">
                        <strong>Customer</strong><br>
                        ${data.booking.customer_name}
                    </div>

                    <div class="col-md-3">
                        <strong>Booking Date</strong><br>
                        ${data.booking.booking_date}
                    </div>

                    <div class="col-md-3">
                        <strong>Return Date</strong><br>
                        ${data.booking.return_date}
                    </div>

                </div>

            </div>

        </div>
        `;

        $('#income_view_content').html(html);

    }

    function showPenaltyDetails(data){

        let html=`

        <div class="card">

            <div class="card-header bg-danger text-white">
                Penalty Details
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">
                        <strong>Booking No</strong><br>
                        ${data.booking_id}
                    </div>

                    <div class="col-md-4">
                        <strong>Customer</strong><br>
                        ${data.customer_name}
                    </div>

                    <div class="col-md-4">
                        <strong>Amount</strong><br>
                        ${parseFloat(data.amount).toFixed(2)}
                    </div>

                </div>

                <hr>

                <strong>Reason</strong>

                <div class="alert alert-warning mt-2">

                    ${data.reason}

                </div>

            </div>

        </div>
        `;

        $('#income_view_content').html(html);

    }

</script>