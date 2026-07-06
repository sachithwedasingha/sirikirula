<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Siri Kirula | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon (use your Siri Kirula icon here) -->
    <link rel="icon" href="assets/img/sirikirula_icon.ico" type="image/x-icon">

    <!-- Styles -->
    <link rel="stylesheet" href="assets/css/plugins/toastr/toastr.min.css">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/animate.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: "Poppins", sans-serif;
            background:
                linear-gradient(135deg, #1a0004 0%, #4a0010 45%, #000000 100%);
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 25px 20px;
        }

        .login-card {
            background: rgba(0, 0, 0, 0.78);
            border-radius: 24px;
            padding: 35px 30px 25px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.8);
            border-top: 3px solid #f7c843; /* gold */
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: "";
            position: absolute;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, #f7c843 0%, transparent 65%);
            opacity: 0.12;
            top: -40px;
            right: -60px;
            pointer-events: none;
        }

        .brand-logo {
            width: 160px;
            margin: 0 auto 10px;
            display: block;
        }

        .brand-name {
            font-size: 20px;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 600;
            color: #f7c843;
        }

        .brand-subtitle {
            font-size: 13px;
            opacity: 0.85;
        }

        .form-control {
            background: #1a0509;
            border: 1px solid #3a0a12;
            border-radius: 12px;
            color: #fff;
        }

        .form-control:focus {
            background: #1a0509;
            border-color: #f7c843;
            box-shadow: 0 0 0 0.15rem rgba(247, 200, 67, 0.35);
            color: #fff;
        }

        .btn-sirikirula {
            background: #f7c843;
            border-color: #f7c843;
            color: #260502;
            border-radius: 999px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .btn-sirikirula:hover {
            background: #e0b12e;
            border-color: #e0b12e;
            color: #260502;
        }

        .footer-text {
            margin-top: 18px;
            font-size: 11px;
            opacity: 0.8;
        }

        .forgot-link {
            color: #f7c843;
            font-size: 12px;
        }

        .forgot-link:hover {
            color: #ffe073;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="login-wrapper animated fadeInDown">
        <div class="login-card text-center">
            <!-- Logo -->
            <!-- Put your Siri Kirula logo here -->
            <img src="assets/ui/logo.png" alt="Siri Kirula Logo" class="brand-logo">

            <!-- <div class="brand-name">Siri Kirula</div> -->
            

            <form class="m-t">
                <div class="form-group text-left mt-4">
                    <label>Username</label>
                    <input type="email" name="userName" class="form-control" placeholder="Enter your email"
                        required="">
                </div>
                <div class="form-group text-left">
                    <label>Password</label>
                    <input type="password" name="userPwd" class="form-control" placeholder="Enter your password"
                        required="">
                </div>
                <button value="Login" name="btnLogin"
                    class="btn btn-sirikirula btn-block m-b mt-3">Login</button>

                <div class="text-right">
                    <a href="lib/view/passwordreset.php" class="forgot-link">
                        <i class="fa fa-unlock-alt"></i> Forgot password?
                    </a>
                </div>
            </form>

            <p class="footer-text" id="footerYear"></p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/jquery-3.1.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/plugins/toastr/toastr.min.js"></script>

    <script>
        var currentYear = new Date().getFullYear();
        $('#footerYear').html('Siri Kirula &copy; ' + currentYear + ' - Powered by CodeCraft');
    </script>
</body>

<script>

/* Add a CSS class for invalid outline (inject if not present) */
(function(){
  const css = `
    .input-invalid {
      outline: 2px solid #d9534f !important;
      box-shadow: 0 0 0 0.2rem rgba(217,83,79,0.25) !important;
      border-color: #d9534f !important;
    }
  `;
  const style = document.createElement('style');
  style.appendChild(document.createTextNode(css));
  document.head.appendChild(style);
})();

$(function() {
  const $form = $('form.m-t');
  const $email = $form.find('input[name="userName"]');
  const $pwd   = $form.find('input[name="userPwd"]');
  const $btn   = $form.find('button[name="btnLogin"]');

  // remove red outline on input
  $form.on('input', 'input', function() {
    $(this).removeClass('input-invalid');
  });

  function isValidEmail(email) {
    // simple email regex — enough for basic validation
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  $form.on('submit', function(e) {
    e.preventDefault();

    // clear previous
    $email.removeClass('input-invalid');
    $pwd.removeClass('input-invalid');

    const emailVal = $email.val() ? $email.val().trim() : '';
    const pwdVal   = $pwd.val() ? $pwd.val().trim() : '';

    let invalid = false;

    if (!emailVal) {
      $email.addClass('input-invalid');
      invalid = true;
    } else if (!isValidEmail(emailVal)) {
      $email.addClass('input-invalid');
      toastr.warning('Enter a valid email address');
      invalid = true;
    }

    if (!pwdVal) {
      $pwd.addClass('input-invalid');
      invalid = true;
    }

    if (invalid) {
      // focus first invalid field
      const $firstInvalid = $form.find('.input-invalid').first();
      if ($firstInvalid.length) $firstInvalid.focus();
      return;
    }

    // disable button to prevent double clicks
    $btn.prop('disabled', true).text('Logging in…');


    $.ajax({
      url: "lib/routes/auth/authentication.php",
      method: 'POST',
      dataType: 'json',
      data: {
        userName: emailVal,
        userPwd: pwdVal,
      },
      success: function(res) {
        if (res && res.ok) {
          toastr.success(res.message || 'Login successful');
          // if server returns a redirect URL, use it; else reload or go to dashboard
          const dest = res.path;
          // small delay so user sees toastr
          setTimeout(function(){ window.location.href = dest; }, 500);
        } else {
          // show server error and mark fields if server says so
          toastr.error((res && res.error) || 'Login failed');
          // example: server could return fields: ['userName','userPwd']
          if (res && res.invalidFields && Array.isArray(res.invalidFields)) {
            res.invalidFields.forEach(f => {
              $form.find('[name="'+f+'"]').addClass('input-invalid');
            });
          }
        }
      },
      error: function(xhr, status, err) {
        toastr.error('Network or server error');
      },
      complete: function() {
        $btn.prop('disabled', false).text('Login');
      }
    });

  });

  // Also allow manual click on button (in case form has no type="submit")
  $btn.on('click', function(e){
    // trigger form submit
    e.preventDefault();
    $form.trigger('submit');
  });

});
</script>


</html>
