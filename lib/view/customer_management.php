<?php include_once('common.php'); ?>

<main class="app-main">

<div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Customer Management</h3>
                </div>

                <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item">
                        <a href="#">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Customer Management
                    </li>
                </ol>
            </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <!-- FORM -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0" id="form_title">
                                Customer Registration
                            </h5>
                        </div>
                        <div class="card-body">
                            <input type="hidden" id="customer_id">
                            <div class="mb-3">
                                <label class="form-label">
                                    Customer Name
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="customer_name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Customer Type
                                </label>
                                <select class="form-control" id="customer_type">
                                    <option value="INDIVIDUAL">
                                        Individual
                                    </option>
                                    <option value="SALON">
                                        Salon
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    Phone Number
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="phone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">
                                    NIC Number
                                </label>
                                <input type="text"
                                       class="form-control"
                                       id="nic">
                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Address
                                </label>
                                <textarea class="form-control"
                                          id="address"
                                          rows="3"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button"
                                        class="btn btn-success"
                                        id="btnSaveCustomer">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Save Customer
                                </button>
                                <button type="button"
                                        class="btn btn-secondary"
                                        id="btnClearCustomer">
                                    Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LIST -->

                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="mb-0">
                                        Customer List
                                    </h5>
                                </div>
                                <div class="col-md-6">
                                    <input type="text"
                                           class="form-control"
                                           id="search_customer"
                                           placeholder="Search Name / Phone / NIC">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>NIC</th>
                                            <th width="90">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="customer_table">
                                    </tbody>
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
    $(document).ready(function(){

    loadCustomers();

});

function loadCustomers(){

    $.get(
        "../routes/customer/load_customers_table.php",
        {
            search:$('#search_customer').val()
        },
        function(res){
            let data=JSON.parse(res);
            $('#customer_table').html(data.table);
        }
    );
}

$('#search_customer').on('keyup',function(){
    loadCustomers();
});

$('#btnClearCustomer').click(function(){
    $('#customer_id').val('');
    $('#customer_type').val('INDIVIDUAL');
    $('#customer_name').val('');
    $('#phone').val('');
    $('#nic').val('');
    $('#address').val('');
    $('#form_title').html('Customer Registration');
});

$('#btnSaveCustomer').click(function(){

    if($('#customer_name').val()==''){

        Swal.fire({
            icon:'warning',
            title:'Customer Name Required'
        });

        return;

    }

    let url='../routes/customer/add_customer.php';

    if($('#customer_id').val()!=''){
        url='../routes/customer/update_customer.php';
    }

    $.ajax({

        url:url,

        type:'POST',

        data:{
            customer_id:$('#customer_id').val(),
            customer_type:$('#customer_type').val(),
            customer_name:$('#customer_name').val(),
            phone:$('#phone').val(),
            nic:$('#nic').val(),
            address:$('#address').val()
        },

        success:function(res){

            let data=JSON.parse(res);

            if(data.status=='01'){

                Swal.fire({
                    icon:'success',
                    title:'Customer Saved'
                });

                $('#btnClearCustomer').click();

                loadCustomers();

            }else{

                Swal.fire({
                    icon:'error',
                    title:data.message
                });

            }

        }

    });

});

$(document).on('click','.btn-edit-customer',function(){
    let customerId=$(this).data('id');

    $.get(
        "../routes/customer/get_customer.php",
        {
            customer_id:customerId
        },
        function(res){
            let data=JSON.parse(res);

            $('#customer_id').val(data.id);
            $('#customer_name').val(data.customer_name);
            $('#customer_type').val(data.customer_type);
            $('#phone').val(data.phone);
            $('#nic').val(data.nic);
            $('#address').val(data.address);
            $('#form_title').html('Edit Customer');
            window.scrollTo({
                top:0,
                behavior:'smooth'
            });
        }
    );
});
</script>