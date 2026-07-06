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

    .summary-card{
        border:0;
        border-radius:12px;
        box-shadow:0 0.125rem 0.25rem rgba(0,0,0,.075);
    }

    .summary-amount{
        font-size:28px;
        font-weight:700;
    }
</style>

<main class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">
                        Settlement Payments
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">

        <!-- Customer Selection -->

        <div class="card shadow-sm border-0 mb-3">

            <div class="card-header bg-light">

                <strong>
                    Select Customer
                </strong>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">

                        <label class="form-label">
                            Customer
                        </label>

                        <select
                            class="form-control"
                            id="customer_id">

                            <option value="">
                                Select Customer
                            </option>

                        </select>

                    </div>

                    <div class="col-md-2 d-grid">

                        <label class="form-label">
                            &nbsp;
                        </label>

                        <button
                            type="button"
                            class="btn btn-primary"
                            id="btnLoadPending">

                            Load Records

                        </button>

                    </div>

                </div>

            </div>

        </div>

        <!-- Summary -->

        <div class="row mb-3">

            <div class="col-md-4">

                <div class="card summary-card border-start border-primary border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Amount
                        </small>

                        <div
                            class="summary-amount text-primary"
                            id="total_amount">

                            0.00

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card summary-card border-start border-success border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Paid
                        </small>

                        <div
                            class="summary-amount text-success"
                            id="total_paid">

                            0.00

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card summary-card border-start border-danger border-4">

                    <div class="card-body">

                        <small class="text-muted">
                            Total Outstanding
                        </small>

                        <div
                            class="summary-amount text-danger"
                            id="total_balance">

                            0.00

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Pending Records -->

        <div class="card shadow-sm border-0">

            <div class="card-header bg-light">

                <strong>
                    Pending Payment Records
                </strong>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead>

                            <tr>

                                <th width="120">
                                    Ref No
                                </th>

                                <th width="120">
                                    Type
                                </th>

                                <th>
                                    Description
                                </th>

                                <th class="text-end">
                                    Total Amount
                                </th>

                                <th class="text-end">
                                    Paid Amount
                                </th>

                                <th class="text-end">
                                    Balance
                                </th>

                                <th width="120">
                                    Status
                                </th>

                                <th width="150">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody id="pending_table">

                            <tr>

                                <td colspan="8">

                                    <div class="alert alert-info mb-0">

                                        Select a customer to load pending payment records.

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <!-- Payment Summary Footer -->

        <div class="card shadow-sm border-0 mt-3">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-8">

                        <h5 class="mb-0">

                            Outstanding Amount :
                            <span
                                class="text-danger"
                                id="footer_balance">

                                0.00

                            </span>

                        </h5>

                    </div>

                    <div class="col-md-4 text-end">

                        <button
                            type="button"
                            class="btn btn-success"
                            id="btnReceivePayment"
                            disabled>

                            <i class="bi bi-cash-coin"></i>

                            Receive Payment

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>

<!-- Settlement Modal -->

<div
    class="modal fade"
    id="paymentModal">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    Receive Settlement Payment

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Reference No
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="settle_ref"
                            readonly>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Balance Amount
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="settle_balance"
                            readonly>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Payment Amount
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            id="payment_amount"
                            min="0">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Payment Method
                        </label>

                        <select
                            class="form-control"
                            id="payment_method">

                            <option value="CASH">
                                Cash
                            </option>

                            <option value="CARD">
                                Card
                            </option>

                            <option value="BANK">
                                Bank Transfer
                            </option>

                        </select>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Close

                </button>

                <button
                    type="button"
                    class="btn btn-success"
                    id="btnSaveSettlement">

                    Save Payment

                </button>

            </div>

        </div>

    </div>

</div>

<?php include_once('footer.php'); ?>

<script>

    $(document).ready(function(){

        loadPendingCustomers();

    });

    function formatAmount(value){

        return parseFloat(value||0).toLocaleString(
            undefined,
            {
                minimumFractionDigits:2,
                maximumFractionDigits:2
            }
        );

    }

    function loadPendingCustomers(){

        $('#customer_id').select2({
            placeholder:'Search Customer',
            width:'100%',
            ajax:{
                url:'../routes/settlement/load_pending_customers.php',
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

                document.querySelector(
                    '.select2-search__field'
                ).focus();

            },100);

        });

    }

    function loadPendingRecords(){

        let customerId=$('#customer_id').val();

        if(customerId==''){

            Swal.fire({
                icon:'warning',
                title:'Select Customer'
            });

            return;

        }

        Swal.fire({
            title:'Loading...',
            allowOutsideClick:false,
            allowEscapeKey:false,
            didOpen:()=>{
                Swal.showLoading();
            }
        });

        $.get(
            '../routes/settlement/load_pending_payments.php',
            {
                customer_id:customerId
            },
            function(res){

                Swal.close();

                let data=JSON.parse(res);

                $('#pending_table').html(
                    data.table
                );

                $('#total_amount').html(
                    formatAmount(
                        data.total_amount
                    )
                );

                $('#total_paid').html(
                    formatAmount(
                        data.total_paid
                    )
                );

                $('#total_balance').html(
                    formatAmount(
                        data.total_balance
                    )
                );

                $('#footer_balance').html(
                    formatAmount(
                        data.total_balance
                    )
                );

            }
        );

    }

    $('#btnLoadPending').click(function(){

        loadPendingRecords();

    });

    $('#customer_id').change(function(){

        loadPendingRecords();

    });

    let currentRef='';
    let currentType='';
    let currentBalance=0;

    $(document).on(
        'click',
        '.btn-settle-payment',
        function(){

            currentRef=$(this).data('id');
            currentType=$(this).data('type');
            currentBalance=parseFloat(
                $(this).data('balance')
            );

            $('#settle_ref').val(
                currentRef
            );

            $('#settle_balance').val(
                formatAmount(
                    currentBalance
                )
            );

            $('#payment_amount').val(
                currentBalance.toFixed(2)
            );

            $('#paymentModal').modal('show');

        }
    );

    $('#payment_amount').on(
        'input',
        function(){

            let amount=parseFloat(
                $(this).val()
            )||0;

            if(amount>currentBalance){

                $(this).val(
                    currentBalance.toFixed(2)
                );

            }

        }
    );

    $('#btnSaveSettlement').click(function(){

        let amount=parseFloat(
            $('#payment_amount').val()
        )||0;

        if(amount<=0){

            Swal.fire({
                icon:'warning',
                title:'Enter Payment Amount'
            });

            return;

        }
$('#paymentModal').modal('hide');
        Swal.fire({

            title:'Enter PIN',

            html:`
            <input
            type="password"
            id="swal_pin"
            class="swal2-input"
            maxlength="6"
            placeholder="Enter PIN">`,

            showConfirmButton:false,
            showCancelButton:true,

            didOpen:()=>{

                $('#swal_pin').focus();

                $('#swal_pin').on(
                    'input',
                    function(){

                        let pin=$(this).val();

                        if(pin.length==6){

                            $.ajax({

                                url:'../routes/auth/verify_pin.php',

                                type:'POST',

                                data:{
                                    pin:pin
                                },

                                success:function(res){

                                    let data=
                                    JSON.parse(res);

                                    if(
                                        data.status
                                        =='error'
                                    ){

                                        $('#swal_pin')
                                        .val('');

                                        Swal.showValidationMessage(
                                            'Wrong PIN'
                                        );

                                        return;

                                    }

                                    Swal.close();

                                    $.ajax({

                                        url:'../routes/settlement/save_settlement.php',

                                        type:'POST',

                                        data:{

                                            type:currentType,

                                            reference_id:currentRef,

                                            amount:amount,

                                            payment_method:
                                            $('#payment_method').val(),

                                            received_by:data.id

                                        },

                                        success:function(res){

                                            let result=
                                            JSON.parse(res);

                                            if(
                                                result.status
                                                =='success'
                                            ){

                                                $('#paymentModal').modal('hide');

                                                Swal.fire({
                                                    icon:'success',
                                                    title:'Payment Saved'
                                                });

                                                loadPendingRecords();

                                            }else{

                                                Swal.fire({
                                                    icon:'error',
                                                    title:
                                                    result.message
                                                });

                                            }

                                        }

                                    });

                                }

                            });

                        }

                    }
                );

            }

        });

    });

</script>