<?php include_once('common.php'); ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">
                        Add Expense
                    </h3>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <strong>
                    Expense Information
                </strong>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            Expense Date
                        </label>
                        <input type="date" class="form-control" id="expense_date" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            Expense Category
                        </label>
                        <select
                            class="form-control"
                            id="expense_category">
                            <option value="">Select Category</option>
                            <option value="Fuel"> Fuel</option>
                            <option value="Transport">Transport</option>
                            <option value="Electricity">Electricity</option>
                            <option value="Water">Water</option>
                            <option value="Telephone">Telephone</option>
                            <option value="Internet">Internet</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Office Supplies"> Office Supplies</option>
                            <option value="Refreshments">Refreshments</option>
                            <option value="Cleaning">Cleaning</option>
                            <option value="Salary Advance">Salary Advance</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            Payment Method
                        </label>
                        <select class="form-control" id="payment_method">
                            <option value="CASH">Cash</option>
                            <option value="CARD">Card</option>
                            <option value="BANK">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            Amount
                        </label>
                        <input type="number" class="form-control" id="amount" min="0" step="0.01" placeholder="0.00">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">
                            Description
                        </label>
                        <textarea class="form-control" id="description" rows="4" placeholder="Enter expense description..."></textarea>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12 text-end">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            id="btnClear">
                            <i class="bi bi-arrow-clockwise"></i>
                            Clear
                        </button>
                        <button
                            type="button"
                            class="btn btn-success"
                            id="btnSaveExpense">
                            <i class="bi bi-check-circle-fill"></i>
                            Save Expense
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Today's Expenses -->

        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-light">
                <strong>
                    Today's Expenses
                </strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table
                        class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="120">
                                    Time
                                </th>
                                <th width="180">
                                    Category
                                </th>
                                <th>
                                    Description
                                </th>
                                <th width="150"
                                    class="text-end">
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody id="today_expense_table">
                            <tr>
                                <td colspan="4">
                                    <div class="alert alert-info mb-0">
                                        No Expenses Found
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end"> Today's Total </th>
                                <th class="text-end text-danger" id="today_total"> 0.00 </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include_once('footer.php'); ?>

<script>

    $(document).ready(function(){
        loadTodayExpenses();
    });

    function formatAmount(value){
        return parseFloat(value || 0).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
    }

    $('#btnClear').click(function(){
        $('#expense_date').val('<?php echo date('Y-m-d'); ?>');
        $('#expense_category').val('');
        $('#payment_method').val('CASH');
        $('#amount').val('');
        $('#description').val('');
        $('#expense_category').focus();

    });

    $('#btnSaveExpense').click(function(){

        let expense_date=$('#expense_date').val();
        let expense_category=$('#expense_category').val();
        let payment_method=$('#payment_method').val();
        let amount=parseFloat($('#amount').val()) || 0;
        let description=$('#description').val();
        if(expense_date==''){
            Swal.fire({
                icon:'warning',
                title:'Select Expense Date'
            });
            return;
        }
        if(expense_category==''){
            Swal.fire({
                icon:'warning',
                title:'Select Expense Category'
            });
            return;
        }
        if(amount<=0){
            Swal.fire({
                icon:'warning',
                title:'Enter Valid Amount'
            });
            return;
        }
        Swal.fire({

            title:'Enter PIN',

            html:`
            <input
            type="password"
            id="swal_pin"
            class="swal2-input"
            maxlength="6"
            placeholder="Enter PIN">`,

            showConfirmButton:false,
            showCancelButton:true,

            didOpen:()=>{
                $('#swal_pin').focus();
                $('#swal_pin').on(
                    'input',
                    function(){
                        let pin=$(this).val();
                        if(pin.length==6){
                            $.ajax({
                                url:'../routes/auth/verify_pin.php',
                                type:'POST',
                                data:{pin:pin},
                                success:function(res){
                                    let data=JSON.parse(res);
                                    if(data.status=='error'){
                                        $('#swal_pin').val('');
                                        Swal.showValidationMessage('Wrong PIN');
                                        return;
                                    }
                                    Swal.close();
                                    $.ajax({
                                        url:'../routes/expense/save_expense.php',
                                        type:'POST',
                                        data:{
                                            expense_date:expense_date,
                                            expense_category:expense_category,
                                            payment_method:payment_method,
                                            amount:amount,
                                            description:description,
                                            createdby:data.id
                                        },success:function(res){
                                            let result=JSON.parse(res);
                                            if(result.status=='success'){
                                                Swal.fire({
                                                    icon:'success',
                                                    title:'Expense Saved Successfully'
                                                });
                                                $('#btnClear').click();
                                                loadTodayExpenses();
                                            }else{
                                                Swal.fire({
                                                    icon:'error',
                                                    title:result.message
                                                });
                                            }
                                        }
                                    });
                                }
                            });
                        }
                    }
                );
            }
        });
    });

    function loadTodayExpenses(){
        $.get('../routes/expense/load_today_expenses.php',function(res){
                let data=JSON.parse(res);
                $('#today_expense_table').html(data.table);
                $('#today_total').html(formatAmount(data.total));
            }
        );
    }
</script>