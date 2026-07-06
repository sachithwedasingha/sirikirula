$(document).ready(function () {
     // Remove the is-invalid class on input
     $('input, select').on('input change', function () {
        if ($(this).val().trim() !== "") {
            $(this).removeClass('is-invalid');
        }
    });

     //contact number validation
     $('#userPhone').on('input', function() {
        var phone = $(this).val();
        var firstChar = phone.charAt(0);

        // Allow only numbers and the plus sign
        phone = phone.replace(/[^0-9+]/g, '');

        // Apply character limit rules based on the first character
        if (firstChar === '0' && phone.length > 10) {
            phone = phone.substring(0, 10);
        } else if (firstChar === '+' && phone.length > 12) {
            phone = phone.substring(0, 12);
        } else if (phone.length > 12) {
            phone = phone.substring(0, 12);
        }

        // Update the input field with the modified value
        $(this).val(phone);

        // Validate based on the rules
        var validPattern = /^(\+?[0-9]{1,12})$/;
        if (!validPattern.test(phone) ||
            (firstChar === '0' && phone.length !== 10) ||
            (firstChar === '+' && phone.length !== 12) ||
            (firstChar !== '0' && firstChar !== '+' && phone.length > 12)) {
                $(this).removeClass('form-control is-valid');
                $(this).addClass('form-control is-invalid');
        } else {
            $(this).removeClass('form-control is-invalid');
            $(this).addClass('form-control is-valid');
     }
});

$('#userNIC').on('input', function() {
    var nic = $(this).val();

    // If length is less than 9, allow only numbers
    if (nic.length <= 9) {
        nic = nic.replace(/[^0-9]/g, ''); // Allow only numbers for the first 9 characters

    // If length is exactly 10, allow a number or a letter at the 10th character
    } else if (nic.length === 10) {
        var lastChar = nic.charAt(9);
        if (isNaN(lastChar)) {
            // If the 10th character is a letter, limit to 10 characters
            nic = nic.substring(0, 9) + lastChar.replace(/[^a-zA-Z]/g, '');
        } else {
            // If the 10th character is a number, allow it
            nic = nic.substring(0, 10).replace(/[^0-9a-zA-Z]/g, '');
        }

    // If length is greater than 10, allow only numbers and limit to 12 characters
    } else if (nic.length > 10) {
        if(isNaN(nic.charAt(9))) {
            nic = nic.substring(0, 10)
        } else {
            nic = nic.replace(/[^0-9]/g, ''); // Allow only numbers
            if (nic.length > 12) {
                nic = nic.substring(0, 12); // Limit to 12 characters
            }
        }
    }

    // Update the input field with the modified value
    $(this).val(nic);

    // Final validation
    var validPattern9Digit = /^[0-9]{9}[a-zA-Z]$/;
    var validPattern12Digit = /^[0-9]{12}$/;

    if (validPattern9Digit.test(nic) || validPattern12Digit.test(nic)) {
        $(this).removeClass('form-control is-invalid');
        $(this).addClass('form-control is-valid');
    } else {
        $(this).removeClass('form-control is-valid');
        $(this).addClass('form-control is-invalid');
    }
});

$('#userEmail').on('input', function() {
    var email = $(this).val();

    // Basic email validation pattern
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Update the input field and apply validation
    if (emailPattern.test(email)) {
        $(this).removeClass('form-control is-invalid');
        $(this).addClass('form-control is-valid');
    } else {
        $(this).removeClass('form-control is-valid');
        $(this).addClass('form-control is-invalid');
    }
});

    $('#btnSave').click(function (e) {
        e.preventDefault();
        
        // Get input values
        var name = $("#userName").val();
        var email = $("#userEmail").val();
        var phone = $("#userPhone").val();
        var nic = $("#userNIC").val();
        var pwd = $("#userPwd").val();
        var repwd = $("#reuserPwd").val();
        var weight = $("#weight").val();
        var height = $("#height").val();
        
        // Validation rules
        if (name.length == "" || email.length == "" || pwd.length == "" || repwd.length == "" || phone.length < 10 ||
            nic.length < 10 || height.length == "" || weight.length == "") {
            
            // Display appropriate error messages for invalid inputs
            
            if (name.length == "") {
                $("#userName").attr('placeholder', "Please Enter Your Name");
                $("#userName").addClass("form-control is-invalid");
            }
            
            if (email.length == "") {
                $("#userEmail").attr('placeholder',"Please Enter Your Email");
                $("#userEmail").attr('class',"form-control is-invalid");
            }

            if (nic.length < 10) {
                $("#userNIC").attr('placeholder',"Please Enter valide NIC Number");
                $("#userNIC").attr('class',"form-control is-invalid");
            }

            if (phone.length < 10) {
                $("#userPhone").attr('placeholder',"Please Enter valide Phone Number");
                $("#userPhone").attr('class',"form-control is-invalid");
            }

            if (pwd.length == "") {
                $("#pwd_errorMsg").html("Please Retypr Your Password");
                $("#userPwd").attr('class',"form-control is-invalid");
            }

            if (pwd.length < 5) {
                $("#pwd_errorMsg").html("Password must be up to 5 characters");
                $("#userPwd").attr('class',"form-control is-invalid");
            }

            if (repwd.length == "") {
                $("#repwd_errorMsg").html("Please Retypr Your Password");
                $("#reuserPwd").attr('class',"form-control is-invalid");
            }

            if (height.length == "") {
                $("#height").attr('class',"form-control is-invalid");
            }

            if (weight.length == "") {
                $("#weight").attr('class',"form-control is-invalid");
            }
            // (Other error handling for input fields...)
            
        } else {
            if (pwd === repwd) {
                // Show loading spinner
                $("#loadingSpinner").show();
                
                // AJAX request
                var form = $("#registrationForm")[0];
                var formData = new FormData(form);
                
                $.ajax({
                    url: "../routes/users/register.php",
                    processData: false,
                    contentType: false,
                    type: "POST",
                    data: formData,
                    success: function (res) {
                        // Hide loading spinner
                        $("#loadingSpinner").hide();
                        
                        // Handle response
                        if (res == "Message has been sent01") {
                            Swal.fire({
                                icon: 'success',
                                text: 'Successfully Registered, Please Check Your Email Account',
                            });
                            // Clear input fields
                            $("#registrationForm")[0].reset();
                        } else {
                            // Handle other responses
                            // ...
                        }
                    },
                    error: function () {
                        // Hide loading spinner
                        $("#loadingSpinner").hide();
                        Swal.fire({
                            icon: 'error',
                            text: 'An error occurred, please try again later.',
                        });
                    }
                });
            } else {
                $("#repwd_errorMsg").html("Password mismatch");
            }
        }
    });
});
