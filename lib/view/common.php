<?php
// lib/view/index.php
session_start();

// Only allow logged-in users with Admin role
if (empty($_SESSION['user']) || empty($_SESSION['usertype'])) {
    header('Location: /'); // redirect to login page (adjust if your login path differs)
    exit;
}

// Accept either 'Admin' or 'admin' (case-insensitive)
if (strtolower($_SESSION['usertype']) !== 'admin') {
    // Optional: redirect customers to their page
    header('Location: /lib/view/index2.php');
    exit;
}

// Optional: simple user info
$userId = htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8');
$role = htmlspecialchars($_SESSION['usertype'], ENT_QUOTES, 'UTF-8');

$currentpage = basename($_SERVER['PHP_SELF']);
$controlPanelPages = [
'location.php',
'user.php',
'category.php',
'manage_supplier.php',
'manage_product.php',
'manage_service.php'
];

$stockPages = [
'my_stock.php',
'request_stock.php',
'transfer_stock.php',
'pending_Stock.php'
];

$rentoutPages = [
'create_booking.php',
'pending_bookings.php',
'active_rentouts.php',
'return_items.php',
'rentalsummery.php'
];

$salesPages=[
'sell_product.php',
'all_sales.php',
'pending_sales.php'
];

$customerPages=[
    'customer_management.php'
];

$expensePages=[
    'add_expense.php',
    'manage_expenses.php'
];


?>
<!doctype html>
<html lang="en">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>SiriKirula</title>
    

    <link rel="preload" href="../../assets/css/adminlte.css" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous" media="print"
        onload="this.media='all'" />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="../../assets/css/adminlte.css" />
    <link rel="stylesheet" href="../../assets/css/plugins/dataTables/datatables.min.css" />
    <!--end::Required Plugin(AdminLTE)-->

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css" />

    <script src="../../js/jsbarcode.js"></script>


    <script src="../../js/jquery.js"></script>
    <script src="../../js/jspdf.js"></script>
    <script src="../../js/qrjs.js"></script>
    <script src="../../js/jquery.dataTables.min.js"></script>
    <script src="../../assets/js/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</head>
<!--end::Head-->
<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body">
            <!--begin::Container-->
            <div class="container-fluid">
                <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                </ul>
                <!--end::Start Navbar Links-->

                <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">
                  
                    <!--begin::Notifications Dropdown Menu-->
                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link" data-bs-toggle="dropdown" href="#">
                            <i class="bi bi-bell-fill"></i>
                            <span class="navbar-badge badge text-bg-warning">15</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <span class="dropdown-item dropdown-header">15 Notifications</span>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-envelope me-2"></i> 4 new messages
                                <span class="float-end text-secondary fs-7">3 mins</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-people-fill me-2"></i> 8 friend requests
                                <span class="float-end text-secondary fs-7">12 hours</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="bi bi-file-earmark-fill me-2"></i> 3 new reports
                                <span class="float-end text-secondary fs-7">2 days</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item dropdown-footer"> See All Notifications </a>
                        </div>
                    </li> -->
                    <!--end::Notifications Dropdown Menu-->

                    <!--begin::Fullscreen Toggle-->
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                        </a>
                    </li>
                    <!--end::Fullscreen Toggle-->

                    <!--begin::User Menu Dropdown-->
                    <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="../../assets/ui/png-clipart-computer-icons-avatar-icon-design-avatar-heroes-computer-wallpaper-thumbnail-removebg-preview.png" class="user-image rounded-circle shadow"
                                alt="User Image" />
                            <span class="d-none d-md-inline" id="navUserName"></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <!--begin::User Image-->
                            <li class="user-header text-bg-primary">
                                <img src="../../assets/ui/png-clipart-computer-icons-avatar-icon-design-avatar-heroes-computer-wallpaper-thumbnail-removebg-preview.png" class="rounded-circle shadow"
                                    alt="User Image" />
                                <p id="navUserName2">
                                <p id="navUsestation">
                                </p>
                            </li>
                           
                            <li class="user-footer">
                                <!-- <a href="#" class="btn btn-default btn-flat">Profile</a> -->
                                <a href="logout.php" style="color:white;" class="btn btn-default btn-flat float-end bg tn btn-danger">Sign out</a>
                            </li>
                            <!--end::Menu Footer-->
                        </ul>
                    </li>
                    <!--end::User Menu Dropdown-->
                </ul>
                <!--end::End Navbar Links-->
            </div>
            <!--end::Container-->
        </nav>
        <!--end::Header-->
        <!--begin::Sidebar-->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <!--begin::Sidebar Brand-->
            <div class="sidebar-brand">
                <!--begin::Brand Link-->
                <a href="./index.html" class="brand-link">
                    <!--begin::Brand Image-->
                    <img src="../../assets/ui/logo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
                    <!--end::Brand Image-->
                    <!--begin::Brand Text-->
                    <!-- <span class="brand-text fw-light">Sirikirula</span> -->
                    <!--end::Brand Text-->
                </a>
                <!--end::Brand Link-->
            </div>
            <!--end::Sidebar Brand-->
            <!--begin::Sidebar Wrapper-->
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <!--begin::Sidebar Menu-->
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                        aria-label="Main navigation" data-accordion="false" id="navigation">
                         <li class="nav-item">
                            <a href="index.php" class="nav-link <?php echo $currentpage == 'index.php' ? 'active'  : '' ?>">
                                <i class="nav-icon bi bi-palette"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item <?php echo in_array($currentpage,$controlPanelPages) ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link <?php echo in_array($currentpage,$controlPanelPages) ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>
                                    Control Panel
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                               <li class="nav-item">
                                <a href="location.php" class="nav-link <?php echo $currentpage == 'location.php' ? 'active'  : '' ?>">
                                    <i class="nav-icon bi bi-geo-alt-fill text-primary"></i>
                                    <p>Branches</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="user.php" class="nav-link <?php echo $currentpage == 'user.php' ? 'active'  : '' ?>">
                                    <i class="nav-icon bi bi-people-fill text-success"></i>
                                    <p>User</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="category.php" class="nav-link <?php echo $currentpage == 'category.php' ? 'active'  : '' ?>">
                                    <i class="nav-icon bi bi-tags-fill text-warning"></i>
                                    <p>Category</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="manage_supplier.php" class="nav-link <?php echo $currentpage == 'manage_supplier.php' ? 'active'  : '' ?>">
                                    <i class="nav-icon bi bi-truck-flatbed text-info"></i>
                                    <p>Supplier</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="manage_product.php" class="nav-link <?php echo $currentpage == 'manage_product.php' ? 'active'  : '' ?>">
                                    <i class="nav-icon bi bi-box-seam-fill text-danger"></i>
                                    <p>Products</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="manage_service.php" class="nav-link <?php echo $currentpage == 'manage_service.php' ? 'active' : '' ?>">
                                    <i class="nav-icon bi bi-tools text-primary"></i>
                                    <p>Services</p>
                                </a>
                            </li>
                               
                            </ul>
                        </li>

                        <li class="nav-item">
                            <a href="customer_management.php" class="nav-link <?php echo $currentpage == 'customer_management.php' ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-people-fill text-info"></i>
                                <p>Customer Management</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="income_management.php" class="nav-link <?php echo $currentpage == 'income_management.php' ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-cash-stack text-success"></i>
                                <p>Income Management</p>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="settlement_payments.php" class="nav-link <?php echo $currentpage=='settlement_payments.php'?'active':''; ?>">
                                <i class="nav-icon bi bi-cash-coin text-warning"></i>
                                <p>Settlement Payments</p>
                            </a>
                        </li>

                        <!-- STOCK MENU -->
                        <li class="nav-item <?php echo in_array($currentpage,$stockPages) ? 'menu-open' : ''; ?>">

                            <a href="#" class="nav-link <?php echo in_array($currentpage,$stockPages) ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-boxes text-warning"></i>
                                <p>
                                    Stock Management
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="my_stock.php" class="nav-link <?php echo $currentpage == 'my_stock.php' ? 'active'  : '' ?>">
                                       <i class="nav-icon bi bi-boxes text-warning"></i>
                                        <p>My Stock</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="pending_Stock.php" class="nav-link <?php echo $currentpage == 'pending_Stock.php' ? 'active' : '' ?>">
                                        <i class="nav-icon bi bi-hourglass-split text-danger"></i>
                                        <p>Pending Stock</p>
                                    </a>
                                </li>


                                <li class="nav-item">
                                    <a href="request_stock.php" class="nav-link <?php echo $currentpage == 'request_stock.php' ? 'active'  : '' ?>">
                                       <i class="nav-icon bi bi-boxes text-warning"></i>
                                        <p>Request Stock</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="transfer_stock.php" class="nav-link <?php echo $currentpage == 'transfer_stock.php' ? 'active'  : '' ?>">
                                       <i class="nav-icon bi bi-boxes text-warning"></i>
                                        <p>Transfer Stock</p>
                                    </a>
                                </li>

                            </ul>

                        </li>

                        <li class="nav-item <?php echo in_array($currentpage,$expensePages) ? 'menu-open' : ''; ?>">
                            <a href="#" class="nav-link <?php echo in_array($currentpage,$expensePages) ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-cash-stack text-danger"></i>
                                <p>
                                    Daily Expenses
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="add_expense.php"
                                        class="nav-link <?php echo $currentpage=='add_expense.php' ? 'active' : ''; ?>">
                                        <i class="nav-icon bi bi-plus-circle-fill text-success"></i>
                                        <p>
                                            Add Expense
                                        </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="manage_expenses.php"
                                        class="nav-link <?php echo $currentpage=='manage_expenses.php' ? 'active' : ''; ?>">
                                        <i class="nav-icon bi bi-list-ul text-primary"></i>
                                        <p>
                                        Manage Expenses
                                        </p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item <?php echo in_array($currentpage,$rentoutPages) ? 'menu-open' : ''; ?>">

                            <a href="#" class="nav-link <?php echo in_array($currentpage,$rentoutPages) ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-calendar-check-fill text-danger"></i>
                                <p>
                                    Rent Out Management
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="create_booking.php" class="nav-link <?php echo $currentpage == 'create_booking.php' ? 'active'  : '' ?>">
                                        <i class="nav-icon bi bi-plus-circle-fill text-danger"></i>
                                        <p>Create Booking</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="pending_bookings.php" class="nav-link <?php echo $currentpage == 'pending_bookings.php' ? 'active'  : '' ?>">
                                        <i class="nav-icon bi bi-clock-history text-danger"></i>
                                        <p>Pending Bookings</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="return_items.php" class="nav-link <?php echo $currentpage == 'return_items.php' ? 'active'  : '' ?>">
                                        <i class="nav-icon bi bi-arrow-return-left text-danger"></i>
                                        <p>Return Items</p>
                                    </a>
                                </li>

                                 <li class="nav-item">
                                    <a href="rentalsummery.php" class="nav-link <?php echo $currentpage == 'rentalsummery.php' ? 'active'  : '' ?>">
                                        <i class="nav-icon bi bi-ticket-detailed text-danger"></i>
                                        <p>Rental Summerys</p>
                                    </a>
                                </li>

                            </ul>

                        </li>

                        <li class="nav-item <?php echo in_array($currentpage,$salesPages) ? 'menu-open' : ''; ?>">

                            <a href="#" class="nav-link <?php echo in_array($currentpage,$salesPages) ? 'active' : ''; ?>">
                                <i class="nav-icon bi bi-cart-check-fill text-success"></i>
                                <p>
                                    Sales Management
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">

                                <li class="nav-item">
                                    <a href="sell_product.php" class="nav-link <?php echo $currentpage == 'sell_product.php' ? 'active' : ''; ?>">
                                        <i class="nav-icon bi bi-cart-plus-fill text-success"></i>
                                        <p>Sell Products</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="pending_sales.php" class="nav-link <?php echo $currentpage == 'pending_sales.php' ? 'active' : ''; ?>">
                                        <i class="nav-icon bi bi-cart-plus-fill text-success"></i>
                                        <p>Pending Sales</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="all_sales.php" class="nav-link <?php echo $currentpage == 'all_sales.php' ? 'active' : ''; ?>">
                                        <i class="nav-icon bi bi-receipt-cutoff text-success"></i>
                                        <p>All Sales</p>
                                    </a>
                                </li>

                            </ul>

                        </li>
                       
                        <li class="nav-header">Products</li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" id="btnOpenProducts">
                                <i class="nav-icon bi bi-circle text-danger"></i>
                                <p class="text">View Products</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle text-warning"></i>
                                <p>Warning</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon bi bi-circle text-info"></i>
                                <p>Informational</p>
                            </a>
                        </li> -->
                    </ul>
                    <!--end::Sidebar Menu-->
                </nav>
            </div>
            <!--end::Sidebar Wrapper-->
        </aside>
        <!--end::Sidebar-->

        <div class="modal fade" id="productListModal" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Product List</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                        <div class="modal-body">
                        <!-- PRODUCT LIST VIEW -->
                        <div id="productListSection">
                            <table class="table table-bordered table-striped"  id="productTable">
                                <thead>
                                    <tr>
                                        <th>image</th>
                                        <th>Item Code</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Unit Price</th>
                                        <th width="100">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="productListBody"></tbody>
                            </table>
                        </div>

                        <!-- PRODUCT DETAILS VIEW -->
                        <div id="productViewSection" style="display:none;">
                            <div class="mb-3">
                                <button type="button"
                                        class="btn btn-secondary btn-sm"
                                        id="btnBackToProducts">
                                    <i class="bi bi-arrow-left"></i> Back to Product List
                                </button>
                            </div>
                            <div id="productViewContent">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


         <script>
             $.get("../routes/emp/show_current_user.php", function (res) {
              let data = JSON.parse(res);
                if (data.status === "success" && data.data.length > 0) {

                    let user = data.data[0];
                    // Name
                    $("#navUserName").text(user.emp_FirstName + " " + user.emp_SecondName );
                    $("#navUserName2").text(user.emp_FirstName + " " + user.emp_SecondName + " - " + user.emp_JobTitle);
                    $("#navUsestation").text(user.station_name);
                    // Profile image (optional)
                    if (user.profile_image) {
                        $("#navUserImage").attr("src", user.profile_image);
                        $("#dropdownUserImage").attr("src", user.profile_image);
                    }
                }

             })

             $("#btnOpenProducts").click(function(e){
                e.preventDefault();
                $("#productViewSection").hide();
                $("#productListSection").show();
                $("#productListModal").modal("show");
                   $.get("../routes/product/product_list_modal.php",function(res){

                    $("#productListBody").html(res);

                    if($.fn.DataTable.isDataTable('#productTable')){
                        $('#productTable').DataTable().destroy();
                    }

                    $('#productTable').DataTable({
                        pageLength:10,
                        order:[[2,'asc']],
                        responsive:true
                    });

                });

            });

            function viewProduct(id){
                $("#productListSection").hide();
                $("#productViewSection").show();
                $("#productViewContent").html(
                    '<div class="text-center p-5">Loading...</div>'
                );
                $.get(
                    "../routes/product/view_product_full.php",
                    {id:id},
                    function(res){

                        $("#productViewContent").html(res);

                    }
                );
            }

            $(document).on("click","#btnBackToProducts",function(){
                $("#productViewSection").hide();
                $("#productListSection").show();
            });
        </script>