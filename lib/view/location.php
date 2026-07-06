<?php
include_once('common.php');
?>

<style>
  /* =========================================
   PAGE
========================================= */

.app-content {
  padding-bottom: 20px;
}

/* =========================================
   CARDS
========================================= */

.card {
  border: none;
  border-radius: 16px;
  box-shadow: 0 4px 18px rgba(0,0,0,0.06);
  overflow: hidden;
}

.card-header {
  background: #ffffff;
  border-bottom: 1px solid #f1f1f1;
  padding: 16px 20px;
}

.card-header strong {
  font-size: 1rem;
  font-weight: 600;
  color: #222;
}

.card-body {
  padding: 20px;
}

/* =========================================
   FORM
========================================= */

.form-label {
  font-weight: 600;
  margin-bottom: 6px;
  color: #333;
}

.form-control {
  border-radius: 10px;
  min-height: 45px;
  border: 1px solid #dcdcdc;
  transition: all 0.2s ease;
}

.form-control:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.15rem rgba(13,110,253,.15);
}

/* =========================================
   BUTTONS
========================================= */

.btn {
  border-radius: 10px;
  padding: 10px 18px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-1px);
}

#btnSave,
#btnUpdate,
#btnCancel {
  min-width: 110px;
}

/* =========================================
   TABLE
========================================= */

.table-responsive {
  border-radius: 12px;
}

table.dataTable {
  width: 100% !important;
}

.table thead th {
  background: #f8f9fa;
  color: #444;
  font-weight: 600;
  border-bottom: 2px solid #ececec;
  white-space: nowrap;
}

.table td {
  vertical-align: middle;
}

.table-striped > tbody > tr:nth-of-type(odd) > * {
  background-color: rgba(0,0,0,0.015);
}

/* =========================================
   ACTION BUTTONS
========================================= */

.btn-edit,
.btn-delete {
  border-radius: 8px;
  padding: 6px 10px;
  font-size: 13px;
}

/* =========================================
   DATATABLES
========================================= */

.dataTables_wrapper .dataTables_filter input {
  border-radius: 8px;
  border: 1px solid #ddd;
  padding: 6px 10px;
  margin-left: 5px;
}

.dataTables_wrapper .dataTables_length select {
  border-radius: 8px;
  border: 1px solid #ddd;
  padding: 5px;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
  border-radius: 8px !important;
  margin: 2px;
  padding: 5px 12px !important;
}

/* =========================================
   STICKY FORM DESKTOP
========================================= */

@media(min-width: 992px) {

  .sticky-form {
    position: sticky;
    top: 20px;
  }

}

/* =========================================
   MOBILE RESPONSIVE
========================================= */

@media(max-width: 768px) {

  .card-body {
    padding: 15px;
  }

  .btn {
    width: 100%;
  }

  .d-flex.gap-2 {
    flex-direction: column;
  }

  .table {
    font-size: 14px;
  }

  .dataTables_wrapper .dataTables_filter {
    text-align: left;
    margin-top: 10px;
  }

  .dataTables_wrapper .dataTables_length,
  .dataTables_wrapper .dataTables_filter,
  .dataTables_wrapper .dataTables_info,
  .dataTables_wrapper .dataTables_paginate {
    width: 100%;
    text-align: center;
    margin-bottom: 10px;
  }

  .breadcrumb {
    font-size: 13px;
  }

  h3.mb-0 {
    font-size: 22px;
  }

}

/* =========================================
   SMALL MOBILE
========================================= */

@media(max-width: 480px) {

  .form-control {
    min-height: 42px;
    font-size: 14px;
  }

  .btn {
    padding: 10px;
    font-size: 14px;
  }

  .table {
    font-size: 13px;
  }

}
</style>

<!--begin::App Main-->
<main class="app-main">

  <!-- Header -->
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Manage Branches</h3>
        </div>

        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Branches</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Content -->
  <div class="app-content">
    <div class="container-fluid">

      <div class="row">

        <!-- LEFT SIDE -->
        <div class="col-lg-5 col-md-12 sticky-form">

          <div class="card mb-4">

            <div class="card-header">
              <strong id="form-title">Add Branches</strong>
            </div>

            <div class="card-body">

              <form id="stationForm" class="needs-validation" novalidate>

                <input type="hidden" id="stationId" name="id" value="">

                <!-- Branch Name -->
                <div class="mb-3">
                  <label for="stationName" class="form-label">
                    Branches Name <span class="text-danger">*</span>
                  </label>

                  <input
                    type="text"
                    class="form-control"
                    id="stationName"
                    name="name"
                    maxlength="100"
                    required
                    placeholder="Example: Colombo">

                  <div class="invalid-feedback">
                    Please enter branch name
                  </div>
                </div>

                <!-- Branch Description -->
                <div class="mb-3">
                  <label for="stationDesc" class="form-label">
                    Branches Details <span class="text-danger">*</span>
                  </label>

                  <input
                    type="text"
                    class="form-control"
                    id="stationDesc"
                    name="discription"
                    maxlength="255"
                    required
                    placeholder="Example: Western Province">

                  <div class="invalid-feedback">
                    Please enter branch details
                  </div>
                </div>

                <div class="mb-3">
                  <label for="stationAddress" class="form-label">
                      Address <span class="text-danger">*</span>
                  </label>

                  <textarea
                      class="form-control"
                      id="stationAddress"
                      name="address"
                      rows="3"
                      required></textarea>
              </div>

              <div class="mb-3">
                  <label for="stationContact" class="form-label">
                      Contact Number <span class="text-danger">*</span>
                  </label>

                  <input
                      type="text"
                      class="form-control"
                      id="stationContact"
                      name="contact_no"
                      required>
              </div>

                <!-- Buttons -->
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

        <!-- RIGHT SIDE -->
        <div class="col-lg-7 col-md-12">
          <div class="card mb-4">
            <div class="card-header">
              <strong>Branches List</strong>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="stationsTable" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                        <th>Branch</th>
                        <th>Description</th>
                        <th>Address</th>
                        <th>Contact No</th>
                        <th style="width:150px">Action</th>
                    </tr>
                  </thead>
                  <tbody id="station_list">
                    <!-- AJAX -->
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

$(document).ready(function () {

  var stationTable;

  // =========================================
  // LOAD TABLE
  // =========================================
  function loadStationList() {

    $.get("../routes/station/pro_list.php", function (res) {
      try {
        if ($.fn.DataTable.isDataTable('#stationsTable')) {
          stationTable = $('#stationsTable').DataTable();
          stationTable.clear().destroy();
        }

        $('#station_list').html(res);
        stationTable = $('#stationsTable').DataTable({
          pageLength: 25,
          responsive: true,
          stateSave: false,
          columnDefs: [
            {
              orderable: false,
              targets: 2
            }
          ]
        });

      } catch (err) {

        console.error("loadStationList error:", err);
        $('#station_list').html(res);
        if (!$.fn.DataTable.isDataTable('#stationsTable')) {
          $('#stationsTable').DataTable();
        }
      }
    }, 'html')

    .fail(function (xhr) {
      console.error(xhr.responseText);
      Swal.fire({
        icon: 'error',
        title: 'Could not load branches'
      });

    });
  }

  // =========================================
  // INITIAL LOAD
  // =========================================
  loadStationList();

  // =========================================
  // VALIDATION
  // =========================================
  function validateFormFields() {

    var form = document.getElementById('stationForm');
    var name = $('#stationName').val().trim();
    var desc = $('#stationDesc').val().trim();
    var address = $('#stationAddress').val().trim();
    var contact = $('#stationContact').val().trim();

    $('#stationName').val(name);
    $('#stationDesc').val(desc);

    if(name=='' || desc=='' || address=='' || contact=='') {
      $(form).addClass('was-validated');
      Swal.fire({
        icon: 'warning',
        title: 'Please fill all required fields'
      });
      return false;
    }

    if (!form.checkValidity()) {
      $(form).addClass('was-validated');
      return false;
    }
    return true;
  }

  // =========================================
  // CLEAR FORM
  // =========================================
  function clearForm() {

    $('#stationId').val('');
    $('#stationName').val('');
    $('#stationDesc').val('');
    $('#stationAddress').val('');
    $('#stationContact').val('');

    $('#form-title').text('Add Branches');

    $('#btnSave').show();
    $('#btnUpdate').hide();

    $('#stationForm').removeClass('was-validated');

    $('#stationName').removeClass('is-invalid');
    $('#stationDesc').removeClass('is-invalid');
  }

  // =========================================
  // SAVE
  // =========================================
  $('#btnSave').on('click', function () {

    if (!validateFormFields()) {
      return;
    }
    var form = $('#stationForm')[0];
    var formData = new FormData(form);
    $('#btnSave').prop('disabled', true);
    $.ajax({
      url: "../routes/station/addstation.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false
    })

    .done(function (data) {
      data = data.trim();
      if (data === "01") {
        Swal.fire({
          icon: 'success',
          title: 'Branch added',
          timer: 1400,
          showConfirmButton: false
        });
        clearForm();
        loadStationList();
      } else if (data === "04") {
        Swal.fire({
          icon: 'warning',
          title: 'Branch already exists',
          timer: 1400,
          showConfirmButton: false
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Add failed',
          text: data
        });
      }
    })

    .fail(function (xhr) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: xhr.responseText
      });
    })

    .always(function () {
      $('#btnSave').prop('disabled', false);
    });

  });

  // =========================================
  // EDIT
  // =========================================
  $(document).on('click', '.btn-edit', function () {

    var uid = $(this).data('id');

    if (!uid) {
      return;
    }

    $.get("../routes/station/getprodata.php", { uid: uid }, function (res) {

      try {

        var jdata = $.parseJSON(res);

      } catch (err) {

        Swal.fire({
          icon: 'error',
          title: 'Invalid server response'
        });

        return;
      }

      $('#stationId').val(jdata.id);
      $('#stationName').val(jdata.name);
      $('#stationAddress').val(jdata.address);
      $('#stationContact').val(jdata.contact_no);

      $('#stationDesc').val(
        jdata.details
          ? jdata.details
          : (jdata.discription ? jdata.discription : '')
      );

      $('#form-title').text('Edit Branch');

      $('#btnSave').hide();
      $('#btnUpdate').show();

      $('html, body').animate({
        scrollTop: $('#stationForm').offset().top - 60
      }, 300);

    })

    .fail(function (xhr) {

      Swal.fire({
        icon: 'error',
        title: 'Could not fetch data',
        text: xhr.responseText
      });

    });

  });

  // =========================================
  // UPDATE
  // =========================================
  $('#btnUpdate').on('click', function () {

    if (!validateFormFields()) {
      return;
    }

    var formData = $('#stationForm').serialize();

    $('#btnUpdate').prop('disabled', true);

    $.ajax({

      url: "../routes/station/editdata.php",
      type: "POST",
      data: formData

    })

    .done(function (res) {

      res = res.trim();

      if (
        res === "01" ||
        res.toLowerCase().indexOf('success') !== -1
      ) {

        Swal.fire({
          icon: 'success',
          title: 'Update successful',
          timer: 1400,
          showConfirmButton: false
        });

        clearForm();

        loadStationList();

      } else {

        Swal.fire({
          icon: 'error',
          title: 'Update failed',
          text: res
        });
      }

    })

    .fail(function (xhr) {

      Swal.fire({
        icon: 'error',
        title: 'Error updating',
        text: xhr.responseText
      });

    })

    .always(function () {

      $('#btnUpdate').prop('disabled', false);

    });

  });

  // =========================================
  // CLEAR
  // =========================================
  $('#btnCancel').on('click', function () {

    clearForm();

  });

  // =========================================
  // DELETE
  // =========================================
  $(document).on('click', '.btn-delete', function () {

    var uid = $(this).data('id');

    if (!uid) {
      return;
    }

    Swal.fire({

      title: 'Are you sure?',
      text: 'This will delete the Branch.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete',
      cancelButtonText: 'Cancel'

    }).then((result) => {

      if (result.isConfirmed) {

        $.ajax({

          url: "../routes/station/delete_pro.php",
          type: "POST",
          data: { uid: uid }

        })

        .done(function (res) {

          res = res.trim();

          if (
            res === "ok" ||
            res.toLowerCase().indexOf('ok') !== -1
          ) {

            Swal.fire({
              icon: 'success',
              title: 'Deleted',
              timer: 1200,
              showConfirmButton: false
            });

            if (
              parseInt($('#stationId').val()) === parseInt(uid)
            ) {
              clearForm();
            }

            loadStationList();

          } else {

            Swal.fire({
              icon: 'error',
              title: 'Delete failed',
              text: res
            });
          }

        })

        .fail(function (xhr) {

          Swal.fire({
            icon: 'error',
            title: 'Delete failed',
            text: xhr.responseText
          });

        });

      }

    });

  });

  // =========================================
  // REMOVE INVALID STATE ON INPUT
  // =========================================
  $('#stationForm').on('input', 'input', function () {

    if ($(this).val().trim() !== '') {

      $(this).removeClass('is-invalid');

    }

  });

});

</script>

<style>

.card-header strong {
  font-size: 1rem;
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
  padding: .2rem .6rem;
}

</style>