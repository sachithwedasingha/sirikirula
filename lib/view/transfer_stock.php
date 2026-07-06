<?php include_once('common.php'); ?>

<main class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">

            <div class="row">

                <div class="col-sm-6">
                    <h3 class="mb-0">Transfer Stock</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Transfer Requests</li>
                    </ol>
                </div>

            </div>

        </div>
    </div>

    <div class="app-content">

        <div class="container-fluid">

            <div class="card shadow-sm border-0">

                <div class="card-header">

                    <div class="row align-items-center">

                        <div class="col-md-6">
                            <strong>Pending Stock Requests</strong>
                        </div>

                        <div class="col-md-3">
                            <select class="form-control" id="request_status">
                                <option value="">All Status</option>
                                <option value="PENDING">Pending</option>
                                <option value="APPROVED">Approved</option>
                                <option value="REJECTED">Rejected</option>
                                <option value="TRANSFERRED">Transferred</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <input type="text" class="form-control" id="search_request" placeholder="Request No">
                        </div>

                    </div>

                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle" id="requestTable">

                            <thead>

                                <tr>
                                    <th width="120">Request No</th>
                                    <th>Requested By</th>
                                    <th class="text-center">Items</th>
                                    <th class="text-end">Qty</th>
                                    <th width="170">Date</th>
                                    <th width="120">Status</th>
                                    <th width="120">Action</th>
                                </tr>

                            </thead>

                            <tbody id="request_list">

                                <tr>
                                    <td colspan="7">
                                        <div class="alert alert-info mb-0">
                                            Loading Requests...
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

<div class="modal fade" id="requestViewModal" tabindex="-1">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Transfer Request Details
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="card border-0 shadow-sm mb-3">

                    <div class="card-header bg-light">

                        <strong>
                            Request Information
                        </strong>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-3 mb-3">

                                <label class="text-muted">
                                    Request No
                                </label>

                                <h6 id="view_request_no">-</h6>

                            </div>

                            <div class="col-md-3 mb-3">

                                <label class="text-muted">
                                    Requested By
                                </label>

                                <h6 id="view_requested_by">-</h6>

                            </div>

                            <div class="col-md-3 mb-3">

                                <label class="text-muted">
                                    Request Date
                                </label>

                                <h6 id="view_request_date">-</h6>

                            </div>

                            <div class="col-md-3 mb-3">

                                <label class="text-muted">
                                    Status
                                </label>

                                <h6 id="view_status">-</h6>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <label class="text-muted">
                                    Remarks
                                </label>

                                <p id="view_remarks" class="mb-0">-</p>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-light">

                        <strong>
                            Requested Products
                        </strong>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered align-middle">

                                <thead>

                                    <tr>
                                        <th>Code</th>
                                        <th>Product</th>
                                        <th class="text-center">Requested Qty</th>
                                        <th class="text-center">Available Qty</th>
                                        <th class="text-center">Transfer Qty</th>
                                    </tr>

                                </thead>

                                <tbody id="request_item_list">

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <input type="hidden" id="request_id">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                    Close

                </button>

                <button type="button" class="btn btn-danger" id="btnRejectRequest">

                    Reject

                </button>

                <button type="button" class="btn btn-info" id="btnApproveRequest">

                    Approve

                </button>

                <button type="button" class="btn btn-success" id="btnTransferRequest">

                    Transfer Stock

                </button>

            </div>

        </div>

    </div>

</div>

<?php include_once('footer.php'); ?>

<script>
    $(document).ready(function(){

    loadRequests();

    $('#request_status').change(function(){
        loadRequests();
    });

    $('#search_request').keyup(function(){
            loadRequests();
    });

    $('#btnApproveRequest').click(function(){

        let request_id=$('#request_id').val();
        let items=[];
        let hasQty=false;
        $('#request_item_list tr').each(function(){
            let qty=parseInt(
                $(this).find('.transfer_qty').val()
            )||0;
            if(qty>0){ hasQty=true; }
            items.push({
                item_id:$(this).data('item-id'),
                product_id:$(this).data('product-id'),
                qty:qty
            });
        });

        if(!hasQty){
            Swal.fire({
                icon:'warning',
                title:'Please Enter Transfer Qty'
            });
            return;
        }

        $.ajax({
            url:'../routes/stock_transfer/check_transfer.php',
            type:'POST',
            data:{
                request_id:request_id,
                items:JSON.stringify(items)
            },
            success:function(res){
                let data=JSON.parse(res);
                if(data.status=='error'){
                    Swal.fire({
                        icon:'error',
                        title:'Cannot Approve',
                        html:data.message
                    });
                    return;
                }
                $('#requestViewModal').modal('hide');
                Swal.fire({
                    title:'Enter PIN',
                    html:`<input type="password"
                            id="swal_pin"
                            class="swal2-input"
                            maxlength="6"
                            placeholder="Enter PIN">`,
                    showConfirmButton:false,
                    showCancelButton:true,
                    didOpen:()=>{
                        $('#swal_pin').focus();
                        $('#swal_pin').on('input',function(){
                            let pin=$(this).val();
                            if(pin.length==6){
                                $.post( '../routes/auth/verify_pin.php', { pin:pin },
                                    function(pinRes){
                                        let pinData=JSON.parse(pinRes);
                                        if(pinData.status=='error'){
                                            $('#swal_pin').val('');
                                            Swal.showValidationMessage(
                                                'Wrong PIN'
                                            );
                                            return;
                                        }
                                        Swal.close();

                                        $.post(
                                            '../routes/stock_transfer/approve_request.php',
                                            {
                                                request_id:request_id,
                                                approved_by:pinData.id,
                                                items:JSON.stringify(items)
                                            },
                                            function(saveRes){
                                                let saveData=JSON.parse(saveRes);
                                                if(saveData.status=='success'){
                                                    Swal.fire({
                                                        icon:'success',
                                                        title:'Request Approved'
                                                    });
                                                    loadRequests();
                                                }else{
                                                    Swal.fire({
                                                        icon:'error',
                                                        title:saveData.message
                                                    });
                                                }
                                            }
                                        );
                                    }
                                );
                            }
                        });
                    }
                });
            }
        });
    });
});

function loadRequests(){

    $.ajax({
        url:'../routes/stock_transfer/load_requests.php',
        type:'GET',
        data:{
            status:$('#request_status').val(),
            request_no:$('#search_request').val()
        },
        success:function(res){

            if($.fn.DataTable.isDataTable('#requestTable')){
                $('#requestTable').DataTable().destroy();
            }

            $('#request_list').html(res);

            $('#requestTable').DataTable({
                responsive:true,
                pageLength:25,
                order:[[4,'desc']]
            });

        }
    });

}

$(document).on('click','.btn-view-request',function(){

    let id=$(this).data('id');

    $.get(
        '../routes/stock_transfer/view_request2.php',
        {
            id:id
        },
        function(res){

            let data=JSON.parse(res);

            $('#request_id').val(data.request.id);
            $('#view_request_no').text(data.request.request_no);
            $('#view_requested_by').text(data.request.station_name);
            $('#view_request_date').text(data.request.created_at);
            $('#view_status').html(data.status_badge);
            $('#view_remarks').text(data.request.remarks ?? '-');

            $('#request_item_list').html(data.items);

            if(data.request.status=='PENDING'){

                $('#btnApproveRequest').show();
                $('#btnRejectRequest').show();
                $('#btnTransferRequest').hide();

            }else if(data.request.status=='APPROVED'){

                $('#btnApproveRequest').hide();
                $('#btnRejectRequest').hide();
                $('#btnTransferRequest').show();

            }else{

                $('#btnApproveRequest').hide();
                $('#btnRejectRequest').hide();
                $('#btnTransferRequest').hide();

            }

            $('#requestViewModal').modal('show');

        }
    );

});
</script>