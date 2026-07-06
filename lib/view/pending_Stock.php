<?php
include_once('common.php');
?>

<style>

.request-age-green{
    background:#d1e7dd;
    color:#0f5132;
    font-weight:600;
}

.request-age-orange{
    background:#fff3cd;
    color:#664d03;
    font-weight:600;
}

.request-age-red{
    background:#f8d7da;
    color:#842029;
    font-weight:600;
}

.group-header{
    background:#212529 !important;
    color:#fff !important;
    font-weight:600;
}

</style>

<main class="app-main">
<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Pending Stock Requests</h3>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item">
                        <a href="#">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Pending Requests
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <strong>
                    Pending Stock Request List
                </strong>
            </div>
            <div class="card-body">
                <div class="col-md-3 mb-3">
    <label class="form-label">
        Group By
    </label>
    <select class="form-control" id="group_by">
        <option value="REFERENCE">
            Order / Booking Wise
        </option>
        <option value="PRODUCT">
            Product Wise
        </option>
    </select>
</div>
                <div class="table-responsive">
                    <table id="pendingTable" class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th width="120">Product Code</th>
                                <th>Product Name</th>
                                <th width="80">Qty</th>
                                <th width="140">Request Date</th>
                                <th width="100">Days Left</th>
                                <th width="150">Request Type</th>
                                <th width="150">Reference No</th>
                                <th width="120">Status</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody id="pending_request_list">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="viewModal" tabindex="-1">

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">
                Pending Request Details
            </h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal">
            </button>
        </div>
        <div class="modal-body" id="modalBody">
        </div>
    </div>
</div>


</main>

<?php
include_once('footer.php');
?>

<script>

$(document).ready(function(){

    $('#group_by').change(function(){
        loadPendingRequests();
    });

    loadPendingRequests();

 

    $(document).on('click','.btn-view',function(){
        let id=$(this).data('id');
        $.ajax({
            url:'../routes/pending_request/view_pending_request.php',
            type:'GET',
            data:{
                id:id
            },
            success:function(res){
                $('#modalBody').html(res);
                $('#viewModal').modal('show');
            }
        });
    });

    $(document).on( 'click', '.btn-complete-request', function(){

            let id=$(this).data('id');

            Swal.fire({
                icon:'question',
                title:'Mark As Ready?',
                showCancelButton:true
            }).then((result)=>{

                if(result.isConfirmed){

                    $.post(
                        '../routes/pending_request/complete_request.php',
                        {
                            id:id
                        },
                        function(res){

                            let data=JSON.parse(res);

                            if(data.status=='success'){

                                Swal.fire({
                                    icon:'success',
                                    title:'Request Completed'
                                });

                                loadPendingRequests();

                            }else{

                                Swal.fire({
                                    icon:'error',
                                    title:data.message
                                });

                            }

                        }
                    );

                }

            });

        }
    );
});

$(document).on(
    'click',
    '.btn-complete-product',
    function(){

        let productId=$(this).data('product');

        Swal.fire({
            icon:'question',
            title:'Complete All Requests?',
            text:'This will complete all pending requests for this product.',
            showCancelButton:true
        }).then((result)=>{

            if(result.isConfirmed){

                $.post(
                    '../routes/pending_request/complete_product.php',
                    {
                        product_id:productId
                    },
                    function(res){

                        let data=JSON.parse(res);

                        if(data.status=='success'){

                            Swal.fire({
                                icon:'success',
                                title:'Completed',
                                text:data.completed+' requests updated'
                            });

                            loadPendingRequests();

                        }

                    }
                );

            }

        });

    }
);

function loadPendingRequests(){

    $.ajax({
        url:'../routes/pending_request/pending_request_list.php',
        type:'GET',
        data:{
            group_by:$('#group_by').val()
        },
        success:function(res){

           $('#pending_request_list').html(res);

        }
    });

}

</script>


</div>
