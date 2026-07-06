<?php include_once('common.php'); ?>
<style>
    .select2-container--default .select2-selection--single{
    height:40px;
    border:1px solid #ced4da;
    border-radius:0.375rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered{
    line-height:43px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow{
    height:43px;
}

.select2-container{
    width:100% !important;
}
</style>
<main class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Sell Products</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
           <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="row">
                   <div class="col-md-7 mb-3">
                        <label class="form-label">
                            Customer
                        </label>

                        <select class="form-select" id="customer_id" name="customer_id"></select>
                    </div>
                     <div class="col-md-1 mt-2 mb-3">
                        <label class="form-label">
                            
                        </label>
                         <button type="button" class="btn btn-success" id="btnNewCustomer">New</button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">
                            Sales Date
                        </label>
                        <input type="date" class="form-control" id="sale_date" readonly value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Service
                        </label>
                        <select class="form-select" id="service_id"></select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            Service Price
                        </label>
                        <input type="number" class="form-control" id="service_price" step="0.01">
                    </div>

                    <div class="col-md-3 mb-3 d-grid">
                        <label class="form-label">
                            &nbsp;
                        </label>

                        <button  type="button" class="btn btn-success" id="btnAddService">
                            <i class="bi bi-plus-circle-fill"></i>
                            Add Service
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Product</label>
                        <select class="form-control" id="product_id"></select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Qty</label>
                        <input type="number" class="form-control" id="qty" value="1" min="1">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Unit Price</label>
                        <input type="number" class="form-control" id="unit_price" step="0.01">
                    </div>
                    <div class="col-md-2 mb-3 d-grid">
                        <label class="form-label">
                            &nbsp;
                        </label>
                        <button type="button" class="btn btn-primary" id="btnAddItem">
                            <i class="bi bi-plus-circle-fill"></i>Add To Bill</button>
                    </div>
                </div>
            </div>
        </div>
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        Sales Bill
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th width="80">Qty</th>
                                    <th width="140">Code</th>
                                    <th>Product Name</th>
                                    <th width="140" class="text-end">Unit Price</th>
                                    <th width="140" class="text-end">Amount</th>
                                    <th width="120">Action</th>
                                </tr>
                            </thead>
                            <tbody id="bill_body">
                                <tr>
                                    <td colspan="7">
                                        <div class="alert alert-info mb-0">No Items Added</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-success">
                            <b>Sale Summery</b>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th>Items Count</th>
                                    <td class="text-end" id="item_count">0</td>
                                </tr>

                                <tr>
                                    <th>Sub Total</th>
                                    <td class="text-end" id="sub_total">0.00</td>
                                </tr>

                                <tr style="background:#eef7ff;">
                                    <th> Grand Total</th>
                                    <td class="text-end fw-bold" id="grand_total">0.00</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                   <canvas id="barcodeCanvas" style="display:none;"></canvas>
                </div>
                <div class="col-md-6">
                    
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-warning">
                            <b>Payment Details</b>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Collection Type</label>
                                <select class="form-select" id="collection_type">
                                    <option value="FULL">Full Complete</option>
                                    <option value="COLLECT_LATER">Collect By Another Day</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method">
                                    <option value="CASH">Cash</option>
                                    <option value="CARD">Card</option>
                                    <option value="MONTH_END">Month End</option>
                                </select>
                            </div>

                            <div id="advanceArea" style="display:none;">
                                <div class="mb-3">
                                    <label class="form-label">Advance Amount</label>
                                    <input type="number" class="form-control" id="advance_amount" value="0" min="0" step="0.01">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Balance Amount</label>
                                    <input type="text" class="form-control" id="balance_amount" readonly value="0.00">
                                </div>
                            </div>
                        </div>
                    </div>
                   <div class="card border-warning mb-3">
                        <div class="card-body">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="create_all_new_items">

                                <label class="form-check-label fw-bold text-warning" for="create_all_new_items">
                                    Create all sold items as newly manufactured items without reducing current stock.
                                </label>
                            </div>
                            <small class="text-muted">
                                When checked, no stock items will be deducted. All sold quantities will be added as production requests.
                            </small>
                        </div>
                    </div>
                    <div class="d-grid mt-3">
                        <button type="button" class="btn btn-success btn-lg" id="btnSaveSale">
                            <i class="bi bi-check-circle-fill"></i>
                            Complete Sale
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<!-- CUSTOMER MODAL -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- HEADER -->
            <div class="modal-header bg-primary text-white border-0">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="bi bi-person-plus-fill me-2"></i>
                        Add New Customer
                    </h5>
                    <small class="opacity-75">
                        Create customer profile for booking
                    </small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <!-- BODY -->
            <div class="modal-body p-4">
                <form id="customerForm">
                    <div class="text-center mb-4">
                        <div class="customer-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                    </div>
                    <!-- TYPE -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Customer Type
                        </label>
                        <select class="form-select custom-input" name="customer_type" id="customer_type">
                            <option value="INDIVIDUAL">
                                Individual Customer
                            </option>
                            <option value="SALON">
                                Salon / Business
                            </option>
                        </select>
                    </div>
                    <!-- NAME -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Customer Name
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-person-fill text-primary"></i>
                            </span>
                            <input type="text" class="form-control custom-input border-start-0" name="customer_name"
                                id="customer_name" placeholder="Enter customer name" required>
                        </div>
                    </div>
                    <!-- PHONE -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Phone Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-telephone-fill text-success"></i>
                            </span>
                            <input type="text" class="form-control custom-input border-start-0" name="phone" id="phone"
                                placeholder="0771234567" required>
                        </div>
                    </div>
                    <!-- NIC -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            NIC Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="bi bi-credit-card-2-front-fill text-warning"></i>
                            </span>
                            <input type="text" class="form-control custom-input border-start-0" name="nic" id="nic"
                                placeholder="Enter NIC number">
                        </div>
                    </div>
                    <!-- ADDRESS -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Address
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 align-items-start pt-3">
                                <i class="bi bi-geo-alt-fill text-danger"></i>
                            </span>
                            <textarea class="form-control custom-input border-start-0" name="address" id="address"
                                rows="3" placeholder="Enter customer address"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" id="btnSaveCustomer">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    Save Customer
                </button>
            </div>
        </div>
    </div>
</div>


<?php include_once('footer.php'); ?>

<script>
    let billItems=[];
    let productionItems=[];
    let editIndex=-1;
    let selectedProductData = null;
    let serviceItems = [];
    let serviceEditIndex = -1;

    function calculateBalance(){

        let total=parseFloat($('#grand_total').text()) || 0;
        let advance=parseFloat($('#advance_amount').val()) || 0;
        if(advance>total){
            advance=total;
            $('#advance_amount').val(total.toFixed(2));
        }
        $('#balance_amount').val(
            (total-advance).toFixed(2)
        );
    }

    $('#collection_type,#payment_method').change(function(){
        let collectionType=$('#collection_type').val();
        let paymentMethod=$('#payment_method').val();
        if(
            collectionType=='COLLECT_LATER'
            ||
            paymentMethod=='MONTH_END'
        ){
            $('#advanceArea').show();
        }else{
            $('#advanceArea').hide();
            $('#advance_amount').val(0);
            $('#balance_amount').val('0.00');
        }
        calculateBalance();
    });

    $('#advance_amount').on('input',function(){
        calculateBalance();
    });

    $('#btnNewCustomer').click(function () {
            $('#customerModal').modal('show');
    });

    $('#customer_id').select2({
        placeholder:'Search Customer Name / Phone / NIC',
        minimumInputLength:3,
        width:'100%',
        ajax:{
            url:'../routes/customer/load_customers.php',
            dataType:'json',
            delay:300,
            data:function(params){
                return {
                    search:params.term
                };
            },
            processResults:function(data){
                return {
                    results:data
                };
            }
        }
    });

    $('#customer_id').on('select2:open', function(){
        setTimeout(function(){
            document.querySelector('.select2-search__field').focus();
        },100);
    });

    $.get("../routes/service/load_services.php",function(res){
        $('#service_id').html(res);
        $('#service_id').select2({
            width:'100%',
            placeholder:'Select Service'
        });
    });

    $('#product_id').select2({
        placeholder:'Search Product Name / Code',
        minimumInputLength:3,
        width:'100%',
        ajax:{
            url:'../routes/product/load_products.php',
            dataType:'json',
            delay:200,
            data:function(params){
                return {
                    search:params.term
                };
            },
            processResults:function(data){
                return {
                    results:data
                };
            }
        }
    });

    $('#product_id').on('select2:open', function(){
        setTimeout(function(){
            document.querySelector('.select2-search__field').focus();
        },100);
    });


    $('#product_id').on('select2:select', function(e){
        selectedProductData = e.params.data;
        $('#unit_price').val(selectedProductData.price);
    });

    $('#btnAddItem').click(function(){

        let productId=$('#product_id').val();

        if(!selectedProductData){

            Swal.fire({
                icon:'info',
                title:'Select Product',
                text:'Please select a product before adding to the bill.'
            });

            return;
        }

        let productName=selectedProductData.name;
        let productCode=selectedProductData.code;
        let qty=parseFloat($('#qty').val())||0;
        let unitPrice=parseFloat($('#unit_price').val())||0;
        let stock=parseInt(selectedProductData.stock||0);

        if(qty<=0){

            Swal.fire({
                icon:'warning',
                title:'Invalid Quantity',
                text:'Please enter a quantity greater than zero.'
            });

            return;
        }

        if(unitPrice<=0){

            Swal.fire({
                icon:'warning',
                title:'Invalid Unit Price',
                text:'Please enter a valid unit price.'
            });

            return;
        }

        function saveItem(normalQty,newQty){

            let itemData={
                product_id:productId,
                product_code:productCode,
                product_name:productName,
                qty:qty,
                unit_price:unitPrice,
                amount:qty*unitPrice,
                item_type:'PRODUCT',
                source_index:billItems.length,
                normal_qty:normalQty,
                new_qty:newQty
            };

            if(editIndex!=-1){

            let oldItem=billItems[editIndex];

                productionItems=productionItems.filter(
                    item=>item.product_id!=oldItem.product_id
                );

                if(newQty>0){

                    productionItems.push({
                        product_id:productId,
                        product_name:productName,
                        qty:newQty
                    });

                }

                billItems[editIndex]=itemData;

                editIndex=-1;

                $('#btnAddItem').html('<i class="bi bi-plus-circle-fill"></i> Add To Bill');

                loadBill();

                clearProduct();

                return;
            }

            let existingIndex=billItems.findIndex(
                item=>item.product_id==productId
            );

            if(existingIndex!=-1){

                billItems[existingIndex].qty+=qty;

                billItems[existingIndex].amount=
                billItems[existingIndex].qty*
                billItems[existingIndex].unit_price;

                billItems[existingIndex].normal_qty=
                (billItems[existingIndex].normal_qty||0)+normalQty;

                billItems[existingIndex].new_qty=
                (billItems[existingIndex].new_qty||0)+newQty;

                loadBill();

                clearProduct();

                return;
            }

            billItems.push(itemData);

            loadBill();

            clearProduct();

        }

        let normalQty=Math.min(stock,qty);

        let newQty=qty-normalQty;

        if(newQty>0){

            Swal.fire({
                icon:'question',
                title:'Production Required',
                html:
                'Required Qty : <b>'+qty+'</b><br><br>'+
                'Available Stock : <b>'+normalQty+'</b><br>'+
                'Need New Production : <b>'+newQty+'</b><br><br>'+
                'Do you want to continue?',
                showCancelButton:true,
                confirmButtonText:'Yes'
            }).then((result)=>{

                if(result.isConfirmed){

                    productionItems.push({
                        product_id:productId,
                        product_name:productName,
                        qty:newQty
                    });

                    saveItem(
                        normalQty,
                        newQty
                    );

                }

            });

            return;
        }

        saveItem(
            normalQty,
            0
        );

    });

    $('#btnAddService').click(function(){

        let serviceId = $('#service_id').val();
        let serviceName = $('#service_id option:selected').text();
        let servicePrice = parseFloat($('#service_price').val()) || 0;

        if(serviceId == ''){
            Swal.fire({
                icon:'warning',
                title:'Select Service'
            });
            return;
        }

        if(servicePrice <= 0){
            Swal.fire({
                icon:'warning',
                title:'Enter Valid Service Price'
            });
            return;
        }

        if(serviceEditIndex != -1){

            serviceItems[serviceEditIndex] = {
                service_id:serviceId,
                service_name:serviceName,
                qty:1,
                unit_price:servicePrice,
                amount:servicePrice,
                item_type:'SERVICE'
            };

            serviceEditIndex = -1;

            $('#btnAddService').html('<i class="bi bi-plus-circle-fill"></i> Add Service');
            loadBill();
            clearService();
            return;
        }

        serviceItems.push({
            service_id:serviceId,
            service_name:serviceName,
            qty:1,
            unit_price:servicePrice,
            amount:servicePrice,
            item_type:'SERVICE',
            source_index:serviceItems.length
        });
        loadBill();
        clearService();

    });

    function clearProduct(){

        selectedProductData = null;

        $('#product_id').val(null).trigger('change');
        $('#qty').val(1);
        $('#unit_price').val('');

    }

    function clearService(){

        $('#service_id').val('').trigger('change');
        $('#service_price').val('');

    }

    $('#btnSaveCustomer').click(function () {
            if ($('#customer_name').val() == '' || $('#phone').val() == '') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Enter Customer Name and Phone number'
                });
                return;
            }
            $.ajax({
                url: "../routes/customer/add_customer.php",
                type: "POST",
                data: $('#customerForm').serialize(),
                success: function (res) {
                    res = res.trim();
                    if (res == "01") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Customer Added'
                        });
                        $('#customerModal').modal('hide');
                        $('#customerForm')[0].reset();
                    } else if (res == "04") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Customer Already Exists'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Save Failed'
                        });
                    }
                }
            });
    });

    function loadBill(){

        let allItems = [...billItems, ...serviceItems];

            let html = '';
            let total = 0;

            allItems.forEach(function(item,index){

                total += parseFloat(item.amount);
                html += '<tr>';
                html += '<td>'+(index+1)+'</td>';
                html += '<td>'+item.qty+'</td>';
                html += '<td>'+(item.item_type=='SERVICE' ? '-' : item.product_code)+'</td>';
            html += '<td>';

                if(item.item_type == 'SERVICE'){

                    html += '<span class="badge bg-success me-2">SERVICE</span>';
                    html += item.service_name;

                }else{

                    html += item.product_name;

                    if(item.need_production == 1){
                        html += '<br><span class="badge bg-warning text-dark">Production Required</span>';
                    }

                }

            
                html += '</td>';
                html += '<td class="text-end">'+parseFloat(item.unit_price).toFixed(2)+'</td>';
                html += '<td class="text-end">'+parseFloat(item.amount).toFixed(2)+'</td>';
                html += '<td>';
                html += '<button type="button" class="btn btn-warning btn-sm btn-edit-item me-1" data-type="'+(item.item_type || 'PRODUCT')+'" data-index="'+index+'"><i class="bi bi-pencil-fill"></i></button>';

                html += '<button type="button" class="btn btn-danger btn-sm btn-remove-item" data-type="'+(item.item_type || 'PRODUCT')+'" data-index="'+index+'"><i class="bi bi-trash-fill"></i></button>';
                    html += '</td>';
                    html += '</tr>';
                });

                if(allItems.length == 0){
                    html = '<tr><td colspan="7"><div class="alert alert-info mb-0">No Items Added</div></td></tr>';
                }

                $('#bill_body').html(html);
                $('#item_count').html(allItems.length);
                $('#sub_total').html(total.toFixed(2));
                $('#grand_total').html(total.toFixed(2));
        }

        $(document).on('click','.btn-remove-item',function(){

            let index = $(this).data('index');
            let type = $(this).data('type');

            if(type == 'SERVICE'){

                serviceItems.splice(index,1);

            }else{

                billItems.splice(index,1);

            }

            loadBill();

    });

    $(document).on('click','.btn-edit-item',function(){

        let type=$(this).data('type');
        let index=$(this).data('index');

        if(type=='SERVICE'){

            let item=serviceItems[index];

            serviceEditIndex=index;

            $('#service_id').val(item.service_id).trigger('change');
            $('#service_price').val(item.unit_price);

            $('#btnAddService').html(
                '<i class="bi bi-check-circle-fill"></i> Update Service'
            );

            return;
        }

        let item=billItems[index];

        editIndex=index;

        selectedProductData={
            id:item.product_id,
            code:item.product_code,
            name:item.product_name,
            price:item.unit_price,
            stock:999999
        };

        let option=new Option(
            item.product_name+' | '+item.product_code,
            item.product_id,
            true,
            true
        );

        $('#product_id').append(option).trigger('change');

        $('#qty').val(item.qty);
        $('#unit_price').val(item.unit_price);

        $('#btnAddItem').html(
            '<i class="bi bi-check-circle-fill"></i> Update Item'
        );

    });

    $('#btnSaveSale').click(function(){

        if($('#customer_id').val()==null){
            Swal.fire({
                icon:'warning',
                title:'Customer Required',
                text:'Please select a customer before continuing.'
            });
            return;
        }

        if(billItems.length==0 && serviceItems.length==0){

            Swal.fire({
                icon:'warning',
                title:'Products Required',
                text:'Please add at least one product or service to the bill.'
            });

            return;
        }

          Swal.fire({
                title: 'Enter PIN',
                html: `<input type="password" id="swal_pin" class="swal2-input" maxlength="6" autocomplete="new-password" autocapitalize="off" autocorrect="off" spellcheck="false" placeholder="Enter 6 Digit PIN">`,
                showConfirmButton: false,
                showCancelButton: true,

                didOpen: () => {
                    $('#swal_pin').focus();
                    $('#swal_pin').on('input', function () {
                        let pin = $(this).val();
                        if (pin.length == 6) {

                            $.ajax({
                                url: '../routes/auth/verify_pin.php',
                                type: 'POST',
                                data: {
                                    pin: pin
                                },
                                success: function (res) {

                                    let data = JSON.parse(res);
                                    if (data.status == "error") {
                                        $('#swal_pin').val('');
                                        Swal.showValidationMessage(
                                            'Wrong PIN');
                                        return;
                                    }
                                    Swal.close();
                                    $.ajax({
                                        url:'../routes/sales/save_sale.php',
                                        type:'POST',
                                        data:{
                                            customer_id:$('#customer_id').val(),
                                            sale_date:$('#sale_date').val(),
                                            createdby:data.id,
                                            products:JSON.stringify(billItems),
                                            services:JSON.stringify(serviceItems),
                                            collection_type:$('#collection_type').val(),
                                            payment_method:$('#payment_method').val(),
                                            advance_amount:$('#advance_amount').val(),
                                            balance_amount:$('#balance_amount').val(),
                                            create_all_new_items: $('#create_all_new_items').is(':checked') ? 1 : 0,
                                        },

                                        success:function(res){

                                            let data=JSON.parse(res);
                                            if(data.status=="success"){
                                                $.get("../routes/sales/get_sale.php",{sale_id:data.sale_id},function(printRes){

                                                        let saleData=JSON.parse(printRes);
                                                        printInvoice(saleData);

                                                        Swal.fire({
                                                            icon:'success',
                                                            title:'Sale Saved Successfully',
                                                            text:'Sales No : '+data.sale_id
                                                        }).then(()=>{

                                                            billItems=[];
                                                            serviceItems=[];
                                                            editIndex=-1;
                                                            serviceEditIndex=-1;

                                                            loadBill();

                                                            $('#customer_id').val('').trigger('change');
                                                            $('#product_id').val('').trigger('change');
                                                            $('#qty').val(1);
                                                            $('#unit_price').val('');
                                                            $('#sale_date').val('<?php echo date('Y-m-d'); ?>');
                                                        });
                                                    }
                                                );
                                            }else{
                                                Swal.fire({
                                                    icon:'error',
                                                    title:'Save Failed',
                                                    text:data.message
                                                });
                                            }
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });
    });

    function printInvoice(data){

        const {
                jsPDF
            } = window.jspdf;

            function checkPageBreak(heightNeeded) {
                if (y + heightNeeded > 270) {
                    doc.addPage();
                    y = 20;
                }
            }
            const doc = new jsPDF();
            let y = 20;
            const img = new Image();
            img.src = '../../assets/ui/logo.png';

            doc.setFillColor(38, 0, 8);
            doc.rect(0, 0, 210, 42, 'F');

            doc.addImage(img, 'PNG', 12, 6, 38, 30);

            doc.setTextColor(255, 255, 255);

            doc.setFontSize(24);
            doc.setFont(undefined, 'bold');
            doc.text("Siri Kirula Pvt. Ltd.", 60, 12, {
                align: 'left'
            });

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
             doc.text(data.station.address, 60, 17, {
                align: 'left'
            });
            doc.text(data.station.contact_no, 60, 22, {
                align: 'left'
            });
            doc.text("sirikirula@gmail.com", 60, 27, {
                align: 'left'
            });

            doc.setDrawColor(255, 215, 0);
            doc.setLineWidth(0.25);
            doc.line(60, 30, 195, 30);

            JsBarcode("#barcodeCanvas",data.sale.barcode,{
            format:"CODE128",
            displayValue:false,
            width:1.5,
            height:25,
            margin:0
            });

            const barcodeCanvas=document.getElementById('barcodeCanvas');
            const barcodeImage=barcodeCanvas.toDataURL("image/png");

            doc.setFillColor(255,255,255);
            doc.setDrawColor(220,220,220);
            doc.setLineWidth(0.2);

            doc.roundedRect(143,9,61,14,1.5,1.5,'FD');
            doc.addImage(barcodeImage,'PNG',146,11,55,10);
            doc.setFontSize(15);
            doc.setFont(undefined, 'bold');
            doc.text("SALES INVOICE", 60, 37, {
                align: 'left'
            });

            doc.setTextColor(0, 0, 0);

            y += 25;

           doc.setDrawColor(38,0,8);
            doc.setLineWidth(0.4);
            doc.roundedRect(10,y,190,45,0,0);
            doc.setTextColor(0,0,0);
            doc.setFontSize(12);
            doc.setFont(undefined,'bold');
            doc.text("SALES INFORMATION",15,y+5);
            doc.setDrawColor(180,180,180);
            doc.setLineWidth(0.2);

            doc.line(15,y+6,195,y+6);
            doc.setFontSize(10);
            doc.setFont(undefined,'normal');
            doc.text("Sales ID",15,y+12);
            doc.text(": "+data.sale.id,45,y+12);
            doc.text("Sales Date",110,y+12);
            doc.text(": "+data.sale.sale_date,145,y+12)
            doc.text("Invoice Time",15,y+18);
            doc.text(": "+data.sale.created_at,45,y+18);
           doc.text("Sales By",110,y+18);
            doc.text(
                ": "+data.sale.emp_FirstName+" "+data.sale.emp_SecondName,
                145,
                y+18
            );

            doc.setFontSize(12);
            doc.setFont(undefined,'bold');
            doc.text("CUSTOMER DETAILS",15,y+27);
            doc.line(15,y+28,195,y+28);
            doc.setFontSize(10);
            doc.setFont(undefined,'normal');
            doc.text("Customer",15,y+34);
            doc.text(": "+data.sale.customer_name,45,y+34);
            doc.text("Phone",15,y+40);
            doc.text(": "+data.sale.phone,45,y+40);
            doc.text("NIC",110,y+40);
            doc.text(": "+data.sale.nic,145,y+40);
            y += 48;

            doc.setFillColor(25, 135, 84);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("Booked Items", 15, y + 4);
            y += 8;
            doc.setTextColor(0,0,0);
            doc.setFillColor(240,240,240);

            doc.rect(10,y,15,7,'FD');
            doc.rect(25,y,75,7,'FD');
            doc.rect(100,y,25,7,'FD');
            doc.rect(125,y,15,7,'FD');
            doc.rect(140,y,30,7,'FD');
            doc.rect(170,y,30,7,'FD');

            doc.setFontSize(9);
            doc.setFont(undefined, 'bold');

            doc.text("#",16,y+5);
            doc.text("Product",45,y+5);
            doc.text("Code",105,y+5);
            doc.text("Qty",130,y+5);
            doc.text("Unit Price",150,y+5);
            doc.text("Amount",185,y+5);

            y+=7;

            doc.setFont(undefined,'normal');

            let count=1;
            let grandTotal=0;

            data.items.forEach(function(item){

                checkPageBreak(20);

                doc.rect(10,y,15,7);
                doc.rect(25,y,75,7);
                doc.rect(100,y,25,7);
                doc.rect(125,y,15,7);
                doc.rect(140,y,30,7);
                doc.rect(170,y,30,7);

                doc.text(String(count),16,y+5);

                let pname=item.product_name;

                if(pname.length>28){
                    pname=pname.substring(0,28)+'...';
                }

                doc.text(pname,28,y+5);

                doc.text(item.product_code,103,y+5);

                doc.text(parseFloat(item.qty).toFixed(0),130,y+5);

                doc.text(
                    parseFloat(item.unit_price).toFixed(2),
                    168,
                    y+5,
                    {align:'right'}
                );

                doc.text(
                    parseFloat(item.amount).toFixed(2),
                    198,
                    y+5,
                    {align:'right'}
                );

                grandTotal+=parseFloat(item.amount);

                y+=7;

                count++;

            });

            doc.setFont(undefined,'bold');

            doc.rect(140,y,30,8);
            doc.rect(170,y,30,8);

            doc.text("TOTAL",145,y+6);

            doc.text(
                grandTotal.toFixed(2),
                198,
                y+6,
                {align:'right'}
            );

            y += 7;

            checkPageBreak(20);
            doc.setFont(undefined, 'bold');

            doc.text("Payment Summary", 118, y + 10);

            doc.text("Full Amount :", 118, y + 15);
            doc.text(parseFloat(data.sale.sale_amount).toFixed(2), 175, y + 15);

            doc.text("Advance Amount :", 118, y + 21);
            doc.text(parseFloat(data.sale.advance_amount).toFixed(2), 175, y + 21);

            doc.text("Balance Amount :", 118, y + 27);
            doc.text(parseFloat(data.sale.balance_amount).toFixed(2), 175, y + 27);
            y += 40;

            doc.setFontSize(8);
            doc.setFont(undefined, 'bold');

            checkPageBreak(20);

            y += 42;

            doc.setDrawColor(120, 120, 120);

            doc.line(20, y, 80, y);
            doc.line(130, y, 190, y);

            doc.setFontSize(9);
            doc.setFont(undefined, 'bold');

            doc.text("Customer Signature", 33, y + 6);

            doc.text("Authorized Signature", 142, y + 6);

            y += 18;
            y += 8;
            // Footer
            // Generate QR Code
            var qr = new QRious({
                value: 'https://sirikirulakandy.lk/',
                size: 100 // size in px
            });
            // Get Base64 image of QR code
            var qrImage = qr.toDataURL(); // default is PNG
            // Existing footer text setup
            const footerY = 280;
            doc.line(15, footerY - 4, 195, footerY - 4);
            doc.setFont('helvetica', 'italic');
            doc.setFontSize(9);
            const footerText =
                "Our Services: Bridal Jewellery Rental • Kandiyan & Indian Wedding Jewellery • Luxury Bridal Sets • Fashion Jewellery Collections • Wedding Accessories • Jewellery Sales & Rental Services • Custom Bridal Packages • QR Enabled Rental Management • Island-wide Customer Support ";
            const wrappedFooter = doc.splitTextToSize(footerText, 160); // Reduced width to allow space for QR
            doc.text(wrappedFooter, 10, footerY);
            // Add QR code to the bottom-right (adjust X and Y for placement)
            doc.addImage(qrImage, 'PNG', 175, footerY, 15, 15); // x, y, width, height
            window.open(doc.output('bloburl'), '_blank');

            ///doc.save("Booking_Receipt_"+data.booking.id+".pdf");

    }
</script>