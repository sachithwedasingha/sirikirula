<?php
include_once('common.php');
?>

<main class="app-main">

  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">

        <div class="col-sm-6">
          <h3 class="mb-0">Manage Categories</h3>
        </div>

        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Categories</li>
          </ol>
        </div>

      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">

      <div class="row">

        <!-- LEFT -->
        <div class="col-lg-4 col-md-12 sticky-form">

          <div class="card mb-4">

            <div class="card-header">
              <strong id="form-title">Add Category</strong>
            </div>

            <div class="card-body">

              <form id="categoryForm" novalidate>

                <input type="hidden" id="categoryId" name="id">

                <div class="mb-3">

                  <label class="form-label">
                    Category Name
                    <span class="text-danger">*</span>
                  </label>

                  <input
                    type="text"
                    class="form-control"
                    id="categoryName"
                    name="name"
                    maxlength="100"
                    required
                    placeholder="Example: Hematology">

                  <div class="invalid-feedback">
                    Please enter category name
                  </div>

                </div>

                <div class="d-flex gap-2">

                  <button
                    type="button"
                    id="btnSave"
                    class="btn btn-primary">
                    Save
                  </button>

                  <button
                    type="button"
                    id="btnUpdate"
                    class="btn btn-success"
                    style="display:none;">
                    Update
                  </button>

                  <button
                    type="button"
                    id="btnClear"
                    class="btn btn-secondary">
                    Clear
                  </button>

                </div>

              </form>

            </div>

          </div>

        </div>

        <!-- RIGHT -->
        <div class="col-lg-8 col-md-12">

          <div class="card mb-4">

            <div class="card-header">
              <strong>Category List</strong>
            </div>

            <div class="card-body">

              <div class="table-responsive">

                <table id="categoryTable" class="table table-striped table-bordered">

                  <thead>
                    <tr>
                      <th>Category Name</th>
                      <th style="width:150px;">Action</th>
                    </tr>
                  </thead>

                  <tbody id="category_list"></tbody>

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

  var categoryTable;

  // LOAD TABLE
  function loadCategoryList(){

    $.get("../routes/category/pro_list.php", function(res){
      $('#category_list').html(res);
    });

  }

  loadCategoryList();

  // VALIDATION
  function validateForm(){

    var name = $('#categoryName').val().trim();

    $('#categoryName').val(name);

    if(name == ''){

      $('#categoryForm').addClass('was-validated');

      Swal.fire({
        icon:'warning',
        title:'Please enter category name'
      });

      return false;
    }

    return true;
  }

  // CLEAR
  function clearForm(){

    $('#categoryId').val('');
    $('#categoryName').val('');

    $('#btnSave').show();
    $('#btnUpdate').hide();

    $('#form-title').text('Add Category');

    $('#categoryForm').removeClass('was-validated');
  }

  // SAVE
  $('#btnSave').click(function(){

    if(!validateForm()){
      return;
    }

    var formData = new FormData($('#categoryForm')[0]);

    $('#btnSave').prop('disabled', true);

    $.ajax({

      url:"../routes/category/addcategory.php",
      type:"POST",
      data:formData,
      processData:false,
      contentType:false

    })

    .done(function(res){

      res = res.trim();

      if(res == "01"){

        Swal.fire({
          icon:'success',
          title:'Category added',
          timer:1200,
          showConfirmButton:false
        });

        clearForm();

        loadCategoryList();

      }else if(res == "04"){

        Swal.fire({
          icon:'warning',
          title:'Category already exists'
        });

      }else{

        Swal.fire({
          icon:'error',
          title:'Add failed',
          text:res
        });

      }

    })

    .always(function(){

      $('#btnSave').prop('disabled', false);

    });

  });

  // EDIT
  $(document).on('click','.btn-edit',function(){

    var uid = $(this).data('id');

    $.get("../routes/category/getprodata.php",{uid:uid},function(res){

      var jdata = $.parseJSON(res);

      $('#categoryId').val(jdata.id);
      $('#categoryName').val(jdata.name);

      $('#btnSave').hide();
      $('#btnUpdate').show();

      $('#form-title').text('Edit Category');

    });

  });

  // UPDATE
  $('#btnUpdate').click(function(){

    if(!validateForm()){
      return;
    }

    $('#btnUpdate').prop('disabled', true);

    $.ajax({

      url:"../routes/category/editdata.php",
      type:"POST",
      data:$('#categoryForm').serialize()

    })

    .done(function(res){

      res = res.trim();

      if(res == "01"){

        Swal.fire({
          icon:'success',
          title:'Updated successfully',
          timer:1200,
          showConfirmButton:false
        });

        clearForm();

        loadCategoryList();

      }else{

        Swal.fire({
          icon:'error',
          title:'Update failed',
          text:res
        });

      }

    })

    .always(function(){

      $('#btnUpdate').prop('disabled', false);

    });

  });

  // DELETE
  $(document).on('click','.btn-delete',function(){

    var uid = $(this).data('id');

    Swal.fire({

      title:'Are you sure?',
      text:'This category will be deleted',
      icon:'warning',
      showCancelButton:true

    }).then((result)=>{

      if(result.isConfirmed){

        $.ajax({

          url:"../routes/category/delete_pro.php",
          type:"POST",
          data:{uid:uid}

        })

        .done(function(res){

          res = res.trim();

          if(res == "ok"){

            Swal.fire({
              icon:'success',
              title:'Deleted',
              timer:1000,
              showConfirmButton:false
            });

            clearForm();

            loadCategoryList();

          }else{

            Swal.fire({
              icon:'error',
              title:'Delete failed'
            });

          }

        });

      }

    });

  });

  // CLEAR
  $('#btnClear').click(function(){

    clearForm();

  });

});

</script>