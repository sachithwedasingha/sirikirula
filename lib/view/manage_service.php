<?php
include_once('common.php');
?>

<main class="app-main">

<div class="app-content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <h3 class="mb-0">Manage Services</h3>
            </div>

            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                    <li class="breadcrumb-item">
                        <a href="#">Home</a>
                    </li>
                    <li class="breadcrumb-item active">
                        Services
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-5 col-md-12 sticky-form">
                <div class="card mb-4">
                    <div class="card-header">
                        <strong id="form-title">
                            Add Service
                        </strong>
                    </div>

                    <div class="card-body">
                        <form id="serviceForm" class="needs-validation" novalidate>
                            <input type="hidden" id="serviceId" name="id">
                            <div class="mb-3">
                                <label class="form-label">
                                    Service Name
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="service_name"
                                    name="service_name"
                                    maxlength="150"
                                    required
                                    placeholder="Example: Delivery Service">
                                <div class="invalid-feedback">
                                    Please enter service name
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button
                                    id="btnSave"
                                    type="button"
                                    class="btn btn-primary">
                                    Save
                                </button>
                                <button
                                    id="btnUpdate"
                                    type="button"
                                    class="btn btn-success"
                                    style="display:none;">
                                    Update
                                </button>
                                <button
                                    id="btnCancel"
                                    type="button"
                                    class="btn btn-secondary">
                                    Clear
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Services List</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="serviceTable" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Service Name</th>
                                        <th width="150">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="service_list">

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

<?php
include_once('footer.php');
?>

<script>

$(document).ready(function(){

    loadServiceList();

    function loadServiceList(){

        $.ajax({
            url:'../routes/service/service_list.php',
            type:'GET',
            success:function(res){

                if($.fn.DataTable.isDataTable('#serviceTable')){
                    $('#serviceTable').DataTable().destroy();
                }

                $('#service_list').html(res);

                $('#serviceTable').DataTable({
                    responsive:true,
                    pageLength:10,
                    order:[[0,'asc']]
                });

            }
        });

    }

    function clearForm(){

        $('#serviceId').val('');
        $('#service_name').val('');

        $('#form-title').html('Add Service');

        $('#btnSave').show();
        $('#btnUpdate').hide();

    }

    $('#btnSave').click(function(){
        let service_name = $('#service_name').val().trim();
        if(service_name==''){
            Swal.fire({
                icon:'warning',
                title:'Please Enter Service Name'
            });
            return;
        }

        $.ajax({
            url:'../routes/service/addservice.php',
            type:'POST',
            data:{
                service_name:service_name
            },
            success:function(res){
                res = res.trim();
                if(res=="01"){
                    Swal.fire({
                        icon:'success',
                        title:'Service Added Successfully',
                        timer:1500,
                        showConfirmButton:false
                    });
                    clearForm();
                    loadServiceList();

                }else if(res=="04"){
                    Swal.fire({
                        icon:'warning',
                        title:'Service Already Exists'
                    });
                }else{
                    Swal.fire({
                        icon:'error',
                        title:'Failed To Save Service'
                    });
                }
            }
        });
    });

    $(document).on('click','.btn-edit',function(){

        let uid = $(this).data('id');
        $.ajax({
            url:'../routes/service/getservicedata.php',
            type:'GET',
            data:{
                uid:uid
            },
            success:function(res){

                let data = JSON.parse(res);
                $('#serviceId').val(data.id);
                $('#service_name').val(data.service_name);

                $('#form-title').html('Edit Service');

                $('#btnSave').hide();
                $('#btnUpdate').show();

                $('html,body').animate({
                    scrollTop:0
                },300);
            }
        });

    });

    $('#btnUpdate').click(function(){

        let id = $('#serviceId').val();
        let service_name = $('#service_name').val().trim();
        if(service_name==''){

            Swal.fire({
                icon:'warning',
                title:'Please Enter Service Name'
            });

            return;
        }
        $.ajax({
            url:'../routes/service/editservice.php',
            type:'POST',
            data:{
                id:id,
                service_name:service_name
            },
            success:function(res){
                res = res.trim();
                if(res=="01"){

                    Swal.fire({
                        icon:'success',
                        title:'Service Updated Successfully',
                        timer:1500,
                        showConfirmButton:false
                    });
                    clearForm();
                    loadServiceList();

                }else{
                    Swal.fire({
                        icon:'error',
                        title:'Failed To Update Service'
                    });
                }
            }
        });
    });

    $('#btnCancel').click(function(){
        clearForm();
    });

    $(document).on('click','.btn-delete',function(){
        let uid = $(this).data('id');
        Swal.fire({
            title:'Delete Service?',
            text:'This action cannot be undone.',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Yes, Delete',
            cancelButtonText:'Cancel'
        }).then((result)=>{

            if(result.isConfirmed){

                $.ajax({
                    url:'../routes/service/deleteservice.php',
                    type:'POST',
                    data:{
                        uid:uid
                    },
                    success:function(res){
                        if(res.trim()=='ok'){
                            Swal.fire({
                                icon:'success',
                                title:'Service Deleted',
                                timer:1500,
                                showConfirmButton:false
                            });
                            loadServiceList();
                        }else{
                            Swal.fire({
                                icon:'error',
                                title:'Delete Failed'
                            });
                        }
                    }
                });
            }
        });
    });
});

</script>
