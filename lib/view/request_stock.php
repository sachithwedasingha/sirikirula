<?php include_once('common.php'); ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Request Stock</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card">

                <div class="card-header">
                    <strong>Create Stock Request</strong>
                </div>

                <div class="card-body">

                    <form id="requestForm">

                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    Select Branch
                                </label>

                                <select class="form-control" id="station_id" required></select>

                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-bordered">

                                <thead>

                                    <tr>
                                        <th>Select</th>
                                        <th>Image</th>
                                        <th>Code</th>
                                        <th>Product</th>
                                        <th>Available</th>
                                        <th width="120">Qty</th>
                                    </tr>

                                </thead>

                                <tbody id="request_product_list"></tbody>

                            </table>

                        </div>

                        <div class="mt-3">

                            <button type="button" class="btn btn-primary" id="btnSendRequest">

                                Send Request

                            </button>

                        </div>

                    </form>

                </div>

            </div>
            <div class="card mt-3">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <strong>
                                My Sent Stock Requests
                            </strong>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="request_status">
                                <option value="">
                                    All Status
                                </option>
                                <option value="PENDING">
                                    Pending
                                </option>
                                <option value="APPROVED">
                                    Approved
                                </option>
                                <option value="REJECTED">
                                    Rejected
                                </option>
                                <option value="COMPLETED">
                                    Completed
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="search_request" placeholder="Request No">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th width="120">
                                        Request No
                                    </th>
                                    <th width="150">
                                        Request Date
                                    </th>
                                    <th width="180">
                                        Requested From
                                    </th>
                                    <th class="text-center" width="120">
                                        Items
                                    </th>
                                    <th class="text-end" width="120">
                                        Total Qty
                                    </th>
                                    <th width="120">
                                        Status
                                    </th>
                                    <th width="180">
                                        Approved Date
                                    </th>
                                    <th width="120">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="request_table">
                                <tr>
                                    <td colspan="8">
                                        <div class="alert alert-info mb-0">
                                            No Requests Found
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<div class="modal fade" id="requestViewModal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Stock Request Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>
            </div>
            <div class="modal-body">
                <div id="request_view_content">
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>

<script>
 $(document).ready(function(){

    loadStations();
    loadMyRequests();

    $('#station_id').change(function(){

        loadProducts();

    });

    function loadMyRequests(){

        $.get(
            '../routes/stock_transfer/load_my_requests.php',
            function(res){

                $('#request_table').html(res);

            }
        );

        $(document).on('click','.btn-view-request',function(){

        let id=$(this).data('id');

        $.get(
            '../routes/stock_transfer/view_request.php',
            {
                id:id
            },
            function(res){

                $('#request_view_content').html(res);

                $('#requestViewModal').modal('show');

            }
        );

    });

}

    $(document).on('blur','.request-qty',function(){

        let max=parseInt($(this).data('max'))||0;
        let qty=parseInt($(this).val())||0;

        if(qty>max){
            $(this).val(max);
        }else if(qty<0){
            $(this).val(0);
        }else{
            $(this).val(qty);
        }

    });

    $('#btnSendRequest').click(function(){

        let station_id=$('#station_id').val();

        if(station_id==''){

            Swal.fire({
                icon:'warning',
                title:'Please Select Station'
            });

            return;
        }

        let products=[];

        $('#request_product_list tr').each(function(){

            let qty=parseInt(
                $(this).find('.request-qty').val()
            )||0;

            let product_id=$(this)
            .find('.request-product')
            .data('id');

            if(qty>0){

                products.push({
                    product_id:product_id,
                    qty:qty
                });

            }

        });

        if(products.length==0){

            Swal.fire({
                icon:'warning',
                title:'Please Enter Request Qty'
            });

            return;
        }

        Swal.fire({
            title:'Enter PIN',
            html:`<input type="password"
                    id="swal_pin"
                    class="swal2-input"
                    maxlength="6"
                    autocomplete="off"
                    placeholder="Enter 6 Digit PIN">`,
            showConfirmButton:false,
            showCancelButton:true,

            didOpen:()=>{

                $('#swal_pin').focus();

                $('#swal_pin').on('input',function(){

                    let pin=$(this).val();

                    if(pin.length==6){

                        $.ajax({
                            url:'../routes/auth/verify_pin.php',
                            type:'POST',
                            data:{
                                pin:pin
                            },

                            success:function(res){

                                let data=JSON.parse(res);

                                if(data.status=='error'){

                                    $('#swal_pin').val('');

                                    Swal.showValidationMessage(
                                        'Wrong PIN'
                                    );

                                    return;
                                }

                                Swal.close();

                                Swal.fire({
                                    title:'Sending Request...',
                                    allowOutsideClick:false,
                                    didOpen:()=>{
                                        Swal.showLoading();
                                    }
                                });

                                $.ajax({
                                    url:'../routes/stock_transfer/save_request.php',
                                    type:'POST',
                                    data:{
                                        station_id:station_id,
                                        createdby:data.id,
                                        products:JSON.stringify(products)
                                    },

                                    success:function(res){

                                        Swal.close();

                                        let saveData=JSON.parse(res);

                                        if(saveData.status=='success'){

                                            Swal.fire({
                                                icon:'success',
                                                title:'Request Sent',
                                                text:'Request No : '+saveData.request_no
                                            });

                                            $('#station_id').val('');

                                            $('#request_product_list').html('');

                                            loadMyRequests();

                                        }else{

                                            Swal.fire({
                                                icon:'error',
                                                title:saveData.message
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

    });

});

function loadProducts(){

    let stationId=$('#station_id').val();

    if(stationId==''){

        $('#request_product_list').html(`
            <tr>
                <td colspan="6">
                    <div class="alert alert-info mb-0">
                        Please select a station.
                    </div>
                </td>
            </tr>
        `);

        return;
    }

    Swal.fire({
        title:'Loading Products...',
        allowOutsideClick:false,
        allowEscapeKey:false,
        didOpen:()=>{
            Swal.showLoading();
        }
    });

    $.get(
        '../routes/stock_transfer/load_products.php',
        {
            station_id:stationId
        },
        function(res){

            Swal.close();

            $('#request_product_list').html(res);

        }
    );

}

function loadStations(){

    $.get(
        '../routes/stock_transfer/load_stations.php',
        function(res){
            $('#station_id').html(res);
        }
    );

}
</script>