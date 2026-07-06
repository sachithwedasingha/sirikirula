<?php include_once('common.php'); ?>

<main class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Manage Expenses</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" id="from_date" value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" id="to_date" value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">Expense Category</label>
                        <select class="form-control" id="expense_category">
                            <option value="">All Categories</option>
                            <option value="Fuel">Fuel</option>
                            <option value="Transport">Transport</option>
                            <option value="Electricity">Electricity</option>
                            <option value="Water">Water</option>
                            <option value="Telephone">Telephone</option>
                            <option value="Internet">Internet</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Office Supplies">Office Supplies</option>
                            <option value="Refreshments">Refreshments</option>
                            <option value="Cleaning">Cleaning</option>
                            <option value="Salary Advance">Salary Advance</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-control" id="payment_method">
                            <option value="">All Methods</option>
                            <option value="CASH">Cash</option>
                            <option value="CARD">Card</option>
                            <option value="BANK">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-grid">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-primary" id="btnLoadExpenses">
                            <i class="bi bi-search"></i>
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <small class="text-muted">Total Expenses</small>
                        <h2 class="text-danger mb-0" id="total_expense">0.00</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <small class="text-muted">Record Count</small>
                        <h2 class="text-primary mb-0" id="total_records">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <small class="text-muted">Average Expense</small>
                        <h2 class="text-success mb-0" id="average_expense"> 0.00</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light">
                <strong>Expense Records</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <th width="80">ID</th>
                                <th width="120">Date</th>
                                <th width="150">Category</th>
                                <th width="120">Method</th>
                                <th>Description</th>
                                <th width="120">Created By </th>
                                <th class="text-end" width="150">Amount</th>
                                <th width="120">Action</th>
                            </tr>
                        </thead>
                        <tbody id="expense_table">
                            <tr>
                                <td colspan="8">Select filters and click Search.</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="expenseViewModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Expense Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="expense_view_content">
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>

<script>

$(document).ready(function(){
    loadExpenses();
});

function formatAmount(amount){
    return parseFloat(amount || 0).toLocaleString(undefined,{
            minimumFractionDigits:2,
            maximumFractionDigits:2
        }
    );
}

$('#btnLoadExpenses').click(function(){
    loadExpenses();
});

$('#expense_category').change(function(){
    loadExpenses();
});

$('#payment_method').change(function(){
    loadExpenses();
});

function loadExpenses(){
    Swal.fire({
        title:'Loading...',
        allowOutsideClick:false,
        allowEscapeKey:false,
        didOpen:()=>{
            Swal.showLoading();
        }
    });
    $.get(
        '../routes/expense/load_expenses.php',{
            from_date:$('#from_date').val(),
            to_date:$('#to_date').val(),
            expense_category:$('#expense_category').val(),
            payment_method:$('#payment_method').val()},
        function(res){
            Swal.close();
            let data=JSON.parse(res);
            $('#expense_table').html(data.table);
            $('#total_expense').html(formatAmount(data.total_expense));
            $('#total_records').html(data.total_records);
            $('#average_expense').html(formatAmount(data.average_expense));
        }
    );
}

$(document).on('click','.btn-view-expense',function(){

        let expenseId=$(this).data('id');
        $('#expenseViewModal').modal('show');
        $('#expense_view_content').html('<div class="text-center p-5">Loading...</div>');

        $.get('../routes/expense/get_expense.php',{expense_id:expenseId},
            function(res){

                let data=JSON.parse(res);
                let html=`<div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">
                            Date
                        </label>
                        <h6>${data.expense_date}</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">
                            Category
                        </label>
                        <h6>${data.expense_category}</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">
                            Payment Method
                        </label>
                        <h6>${data.payment_method}</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">
                            Amount
                        </label>
                        <h5 class="text-danger">${formatAmount(data.amount)}</h5>
                    </div>
                    <div class="col-md-12">
                        <label class="text-muted">
                            Description
                        </label>
                        <div class="alert alert-light border">${data.description}</div>
                    </div>
                </div>`;

                $('#expense_view_content').html(html);
            }
        );
    }
);

</script>