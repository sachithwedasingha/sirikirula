<?php include_once('common.php'); ?>

<style>
    .select2-container .select2-selection--single{
        height:38px !important;
        border:1px solid #ced4da !important;
        border-radius:0.375rem !important;
        padding:4px 10px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height:28px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height:36px !important;
    }

    .select2-dropdown{
        border:1px solid #ced4da !important;
        border-radius:0.375rem !important;
    }

    .select2-search__field{
        border:1px solid #ced4da !important;
        border-radius:0.375rem !important;
        padding:6px 10px !important;
    }
</style>


<main class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">All Sales</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
       <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-2 mb-3">
                        <label class="form-label">
                            From Date
                        </label>
                        <input type="date"
                            class="form-control"
                            id="from_date"
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label">
                            To Date
                        </label>
                        <input type="date"
                            class="form-control"
                            id="to_date"
                            value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="col-md-2 mb-3 d-grid">
                        <label class="form-label">
                            &nbsp;
                        </label>
                        <button type="button"
                                class="btn btn-primary"
                                id="btnLoadSales">
                            Search
                        </button>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            Customer
                        </label>
                        <select class="form-control" id="customer_id">
                            <option value="">All Customers</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="form-label">
                            Sales ID
                        </label>
                        <input type="text"
                            class="form-control"
                            id="sale_id"
                            placeholder="SAL00001">
                    </div>

                    

                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Date Time</th>
                                    <th>Customer</th>
                                    <th>Phone</th>
                                    <th class="text-end">
                                        Amount
                                    </th>
                                    <th class="text-end">
                                        Paymnt Type
                                    </th>
                                    <th width="120">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="sales_table">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <canvas id="barcodeCanvas" style="display:none;"></canvas>
    </div>
</main>

<?php include_once('footer.php'); ?>

<script>
    $(document).ready(function(){
        loadSales();

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

        $('#customer_id').on('select2:select', function(e){
            let customerId = e.params.data.id;
            loadSalesforcus(); // or your function
        });

        $('#sale_id').on('keydown', function(e){
            if(e.key === 'Enter'){
                e.preventDefault();
                loadSalesforbarcode();
            }
        });

        $('#btnLoadSales').click(function(){
            loadSales();
        });
    });

    function loadSales(){
        $.get(
            "../routes/sales/load_sales.php",
            {
                from_date:$('#from_date').val(),
                to_date:$('#to_date').val(),
                customer_id:'',
                sale_id:''
            },
            function(res){
                let data=JSON.parse(res);

                $('#sales_table').html(data.table);
            }
        );
    }

    function loadSalesforbarcode(){
        $.get(
            "../routes/sales/load_sales.php",
            {
                from_date:'',
                to_date:'',
                customer_id:'',
                sale_id:$('#sale_id').val()
            },
            function(res){
                let data=JSON.parse(res);

                $('#sales_table').html(data.table);
            }
        );
    }

    function loadSalesforcus(){
        $.get(
            "../routes/sales/load_sales.php",
            {
                from_date:'',
                to_date:'',
                customer_id:$('#customer_id').val(),
                sale_id:''
            },
            function(res){
                let data=JSON.parse(res);

                $('#sales_table').html(data.table);
            }
        );
    }

    $(document).on('click','.btn-reprint-sale',function(){

        let saleId=$(this).data('id');
        Swal.fire({
            title:'Reprint Invoice?',
            icon:'question',
            showCancelButton:true,
            confirmButtonText:'Yes, Reprint'
        }).then((result)=>{

            if(result.isConfirmed){

                $.get(
                    "../routes/sales/get_sale.php",{sale_id:saleId}, function(res){
                        let data=JSON.parse(res);
                        if(data.sale){
                            printInvoice(data);
                        }else{
                            Swal.fire({
                                icon:'error',
                                title:'Invoice Not Found'
                            });
                        }
                    }
                );
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

            y+=15;

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