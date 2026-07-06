<?php
include_once('common.php');   // your header / nav / styles
?>
<!--begin::App Main-->
<main class="app-main">
  <!-- App Content Header -->
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Manage User</h3></div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">User</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- App Content -->
  <div class="app-content">
    <div class="container-fluid">
      <div class="row">

        <!-- LEFT: Form (Add / Edit) -->
        <div class="col-md-5">
          <div class="card mb-4">
            <div class="card-header">
              <strong id="form-title">Add User</strong>
            </div>
            <div class="card-body">
              <form id="userForm" class="needs-validation" novalidate>
                <!-- Hidden ID for edit -->
                <input type="hidden" id="userId" name="idCustomer" value="">

                <div class="mb-3">
                  <label for="userFname" class="form-label">First Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="userFname" name="Customer_fname" required>
                </div>

                <div class="mb-3">
                  <label for="userLname" class="form-label">Last Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="userLname" name="Customer_lname" required>
                </div>

                <div class="mb-3">
                  <label for="userPhone" class="form-label">Contact Number <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="userPhone" name="Customer_telnum" required>
                </div>

                <div class="mb-3">
                  <label for="userEmail" class="form-label">Email <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="userEmail" name="Customer_email" required>
                </div>

                 <div class="mb-3">
                  <label for="usernic" class="form-label">NIC Number <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="usernic" name="Customer_nic" required>
                  
                </div>

                <div class="mb-3">
                  <label for="userBirthday" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                  <input type="date" class="form-control" id="userBirthday" name="Customer_birthday" required>
                  
                </div>

                <div class="mb-3">
                  <label for="userGender" class="form-label">Gender <span class="text-danger">*</span></label>
                  <select class="form-control" id="userGender" name="gender" required>
                    <option value="" selected>Select Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="userType" class="form-label">Type <span class="text-danger">*</span></label>
                  <select class="form-control" id="userType" name="userType" required>
                    <option value="" selected>Select User Type</option>
                    <option value="Admin">Admin</option>
                    <option value="Other">Other</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="userLocation" class="form-label">Branche <span class="text-danger">*</span></label>
                  <select class="form-control" id="userLocation" name="location" required>
                    <!-- loaded by AJAX from stationdrop.php -->
                  </select>
                </div>

                <div class="mb-3">
                  <label for="userAddress" class="form-label">Address <span class="text-danger">*</span></label>
                  <textarea class="form-control" id="userAddress" name="Customer_address" required></textarea>
                </div>

                <div class="d-flex gap-2">
                  <button id="btnSave" type="button" class="btn btn-primary">Save</button>
                  <button id="btnUpdate" type="button" class="btn btn-success" style="display:none;">Update</button>
                  <button id="btnCancel" type="button" class="btn btn-secondary">Clear</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <!-- RIGHT: Table -->
        <div class="col-md-7">
          <div class="card mb-4">
            <div class="card-header">
              <strong>User List</strong>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="usersTable" class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>NIC</th>
                      <th>Contact</th>
                      <th>Gender</th>
                      <th>E-mail</th>
                      <th>Age</th>
                      <th>Location</th>
                      <th style="width:150px">Action</th>
                    </tr>
                  </thead>
                  <tbody id="user_list">
                    <!-- loaded by AJAX from ../routes/emp/user_list.php -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div> <!-- /.row -->
    </div> <!-- /.container-fluid -->
  </div> <!-- /.app-content -->
</main>

<?php
include_once('footer.php'); // includes jQuery/Bootstrap etc in your app already
?>

<!-- If SweetAlert2 is not globally loaded, keep this. If it is, you can remove this line. -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Page specific scripts -->
<script>
$(document).ready(function() {

  // load locations for dropdown
  function loadLocationDrop() {
    $.get("../routes/station/stationdrop.php", function (res1) {
      $("#userLocation").html(res1);
    });
  }

  // Load user list and bind DataTable
  function loadUserList() {
    $.get("../routes/emp/user_list.php", function (res) {

      // Destroy existing DataTable if any
      if ($.fn.DataTable.isDataTable('#usersTable')) {
        $('#usersTable').DataTable().clear().destroy();
      }

      $("#user_list").html(res);

      // Initialise DataTable
      $('#usersTable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
          { extend: 'copy' },
          { extend: 'csv' },
          { extend: 'excel', title: 'UserList' },
          { extend: 'pdf', title: 'UserList' },
          {
            extend: 'print',
            customize: function (win) {
              $(win.document.body).addClass('white-bg');
              $(win.document.body).css('font-size', '10px');

              $(win.document.body).find('table')
                .addClass('compact')
                .css('font-size', 'inherit');
            }
          }
        ]
      });
    });
  }

  // Max DOB: at least 16 years old
  (function setMaxBirthday() {
    var today = new Date();
    var sixteenYearsAgo = new Date(today.getFullYear() - 16, today.getMonth(), today.getDate());
    var maxDate = sixteenYearsAgo.toISOString().split('T')[0];
    $('#userBirthday').attr('max', maxDate);
  })();

  // generic form validation
  function validateFormFields() {
    var form = document.getElementById('userForm');
    if (!form.checkValidity()) {
      $(form).addClass('was-validated');
      return false;
    }
    return true;
  }

  function clearForm() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#form-title').text('Add User');
    $('#btnSave').show();
    $('#btnUpdate').hide();
    $('#userForm').removeClass('was-validated');
    $('#userForm .is-invalid').removeClass('is-invalid');
    $('#userForm .is-valid').removeClass('is-valid');
  }

  /** --------------------------
   *  FIELD-LEVEL VALIDATION
   *  -------------------------- */

  // Contact number validation (same logic as your modal page)
  $('#userPhone').on('input', function () {
    var phone = $(this).val();
    var firstChar = phone.charAt(0);

    // Allow only numbers and plus
    phone = phone.replace(/[^0-9+]/g, '');

    // Length rules
    if (firstChar === '0' && phone.length > 10) {
      phone = phone.substring(0, 10);
    } else if (firstChar === '+' && phone.length > 12) {
      phone = phone.substring(0, 12);
    } else if (phone.length > 12) {
      phone = phone.substring(0, 12);
    }

    $(this).val(phone);

    var validPattern = /^(\+?[0-9]{1,12})$/;
    if (!validPattern.test(phone) ||
      (firstChar === '0' && phone.length !== 10) ||
      (firstChar === '+' && phone.length !== 12) ||
      (firstChar !== '0' && firstChar !== '+' && phone.length > 12)) {

      $(this).removeClass('is-valid').addClass('is-invalid');
    } else {
      $(this).removeClass('is-invalid').addClass('is-valid');
    }
  });

  // Email validation
  $('#userEmail').on('input', function () {
    var email = $(this).val();
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emailPattern.test(email)) {
      $(this).removeClass('is-invalid').addClass('is-valid');
    } else {
      $(this).removeClass('is-valid').addClass('is-invalid');
    }
  });

  // Remove red border when typing or changing
  $('#userForm').on('input change', 'input, select, textarea', function () {
    if ($(this).val().trim() !== '') {
      $(this).removeClass('is-invalid');
    }
  });


  $('#btnSave').on('click', function (e) {
    e.preventDefault();

    var isValid = validateFormFields();
    if (!isValid) {
      Swal.fire({
        position: 'center',
        icon: 'error',
        title: 'Please fill in all required fields',
        showConfirmButton: false,
        timer: 1500
      });
      return;
    }

    var formData = $('#userForm').serialize();
    $('#btnSave').prop('disabled', true);

    $.ajax({
      type: 'POST',
      url: '../routes/emp/adddata.php', // 🔁 change if your add endpoint is different
      data: formData,
      success: function (response) {
        $('#btnSave').prop('disabled', false);
        var trimmed = $.trim(response);

        if (trimmed === "ok" || trimmed === "01") {
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'User added successfully!',
            showConfirmButton: false,
            timer: 1500
          });
          clearForm();
          loadUserList();
        } else if (trimmed === "04") {
          Swal.fire({
            position: 'center',
            icon: 'info',
            title: 'This Email already exists!',
            showConfirmButton: false,
            timer: 1500
          });
        } else {
          Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Something went wrong!',
            text: trimmed,
            showConfirmButton: true
          });
        }
      },
      error: function (xhr) {
        $('#btnSave').prop('disabled', false);
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: 'An error occurred!',
          text: xhr.responseText,
          showConfirmButton: true
        });
      }
    });
  });

  $('#btnUpdate').on('click', function (e) {
    e.preventDefault();

    var isValid = validateFormFields();
    if (!isValid) {
      Swal.fire({
        position: 'center',
        icon: 'error',
        title: 'Please fill in all required fields',
        showConfirmButton: false,
        timer: 1500
      });
      return;
    }

    var formData = $('#userForm').serialize();
    $('#btnUpdate').prop('disabled', true);

    $.ajax({
      type: 'POST',
      url: '../routes/emp/editdata.php',
      data: formData,
      success: function (response) {
        $('#btnUpdate').prop('disabled', false);
        var trimmedResponse = $.trim(response);

        if (trimmedResponse === "ok" || trimmedResponse === "01") {
          Swal.fire({
            position: 'center',
            icon: 'success',
            title: 'Edit successful!',
            showConfirmButton: false,
            timer: 1500
          });
          clearForm();
          loadUserList();
        } else if (trimmedResponse === "04") {
          Swal.fire({
            position: 'center',
            icon: 'info',
            title: 'This Email already exists!',
            showConfirmButton: false,
            timer: 1500
          });
        } else {
          Swal.fire({
            position: 'center',
            icon: 'error',
            title: 'Something went wrong!',
            text: trimmedResponse,
            showConfirmButton: true
          });
        }
      },
      error: function (xhr) {
        $('#btnUpdate').prop('disabled', false);
        Swal.fire({
          position: 'center',
          icon: 'error',
          title: 'An error occurred!',
          text: xhr.responseText,
          showConfirmButton: true
        });
      }
    });
  });


  $('#btnCancel').on('click', function(){
    clearForm();
  });


  // Expose globally so existing onclick="editacc(1)" still works
  window.deleteuser = function (oid) {
    if (!oid) return;

    Swal.fire({
      title: 'Are you sure?',
      text: "Do you want to delete this user permanently?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.get("../routes/emp/delete_user.php", { uid: oid }, function (res) {
          var trimmed = $.trim(res);
          if (trimmed === "ok") {
            Swal.fire(
              'Successful!',
              'User has been deleted successfully!',
              'success'
            );
            setTimeout(function () {
              loadUserList();
              // if editing same user, clear form
              if ($('#userId').val() == oid) {
                clearForm();
              }
            }, 1600);
          } else {
            Swal.fire(
              'Something went wrong!',
              'Can not delete user.',
              'error'
            );
          }
        });
      }
    });
  };

  window.editacc = function (uid) {
    if (!uid) return;

    $.get("../routes/emp/getuserdata.php", { uid: uid }, function (res) {
      var jdata;
      try {
        jdata = jQuery.parseJSON(res);
      } catch (e) {
        Swal.fire({
          icon: 'error',
          title: 'Unexpected response!',
          text: res
        });
        return;
      }

      $('#userId').val(jdata.loginId);
      $('#userFname').val(jdata.emp_FirstName);
      $('#userLname').val(jdata.emp_SecondName);
      $('#userPhone').val(jdata.emp_Phone);
      $('#userEmail').val(jdata.emp_Email);
      $('#userBirthday').val(jdata.emp_Birthday);
      $('#userAddress').val(jdata.emp_Address);
      $('#userType').val(jdata.emp_JobTitle);
      $('#userGender').val(jdata.emp_Gender);
      $('#userLocation').val(jdata.station);
      $('#usernic').val(jdata.emp_Nic);
      $('#form-title').text('Edit User');
      $('#btnSave').hide();
      $('#btnUpdate').show();

      $('html, body').animate({
        scrollTop: $('#userForm').offset().top - 60
      }, 300);
    });
  };

  // Also support buttons with data-id if you change user_list.php later
  $(document).on('click', '.btn-edit', function () {
    var id = $(this).data('id');
    window.editacc(id);
  });

  $(document).on('click', '.btn-delete', function () {
    var id = $(this).data('id');
    window.deleteuser(id);
  });

  loadLocationDrop();
  loadUserList();

});


  function resetPassword(uid){

    Swal.fire({

      title: 'Reset Password?',
      text: 'Password will reset to ABC@123',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes Reset'

    }).then((result)=>{

      if(result.isConfirmed){

        $.ajax({

          url:'../routes/emp/reset_password.php',
          type:'POST',
          data:{uid:uid},

          success:function(res){

            res = res.trim();

            if(res == "01"){

              Swal.fire({
                icon:'success',
                title:'Password Reset Successful',
                html:'New Password : <b>ABC@123</b>'
              });

            }else{

              Swal.fire({
                icon:'error',
                title:'Reset Failed'
              });

            }

          }

        });

      }

    });

  }

  function setPin(uid){

    Swal.fire({
      title:'Set 6 Digit PIN',
      input:'password',
      inputAttributes:{
        maxlength:6,
        autocapitalize:'off',
        autocorrect:'off',
        autocomplete:'new-password',
      },
      inputPlaceholder:'Enter 6 digit PIN',
      showCancelButton:true,
      confirmButtonText:'Save PIN',

      preConfirm:(pin)=>{
        if(!pin){
          Swal.showValidationMessage('PIN required');
          return false;
        }
        if(!/^[0-9]{6}$/.test(pin)){
          Swal.showValidationMessage('PIN must be exactly 6 digits');
          return false;
        }
        return pin;
      }

    }).then((result)=>{

      if(result.isConfirmed){
        $.ajax({
          url:'../routes/emp/set_pin.php',
          type:'POST',
          data:{
            uid:uid,
            pin:result.value
          },
          success:function(res){
            res = res.trim();
            if(res == "01"){
              Swal.fire({
                icon:'success',
                title:'PIN Saved Successfully'
              });
              loadUserList();
            }else if(res == "04"){
              Swal.fire({
                icon:'warning',
                title:'PIN Already Exists'
              });
            }else{
              Swal.fire({
                icon:'error',
                title:'PIN Save Failed'
              });
            }
          }
        });
      }
    });
  }

</script>

<style>
  /* small visual tweaks */
  .card-header strong { font-size:1rem; }
  .dataTables_wrapper .dataTables_paginate .paginate_button { padding: .2rem .6rem; }

  /* keep same invalid style you used before */
  .is-invalid {
    border-color: #dc3545;
  }
  .is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 6px #f5c6cb;
  }
</style>
