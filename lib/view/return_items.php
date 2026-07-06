<?php include_once('common.php'); ?>
<style>
    .booking-summary-bar {
        height: 56px;
        background: #fff;
        border-radius: 14px;
        padding: 0 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #f1f1f1;
    }

    .summary-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 15px;
        font-weight: 600;
    }

    .summary-label {
        color: #666;
    }

    .summary-value {
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
    }

    .all-bookings .summary-value {
        color: #0d6efd;
    }

    .pending-bookings .summary-value {
        color: #ff9800;
    }

    .ready-bookings .summary-value {
        color: #198754;
    }

    .summary-divider {
        width: 1px;
        height: 28px;
        background: #e5e5e5;
    }


    .booking-item-row {
        cursor: pointer;
        transition: 0.2s;
    }

    .booking-item-row:hover {
        background: #f5f5f5;
    }

    .booking-item-selected {
        background: #d1ffd9 !important;
    }

    .booking-item-row{
    cursor:pointer;
    transition:0.2s;
    }

    .booking-item-row.active-row td:nth-child(1),
    .booking-item-row.active-row td:nth-child(2),
    .booking-item-row.active-row td:nth-child(3),
    .booking-item-row.active-row td:nth-child(4){
    background:#d1ffd6 !important;
    }

    

    @media(max-width:768px) {

        .booking-summary-bar {
            flex-wrap: wrap;
            height: auto;
            padding: 15px;
            gap: 12px;
            justify-content: space-around;
        }

        .summary-divider {
            display: none;
        }

        .summary-item {
            font-size: 14px;
        }

        .summary-value {
            font-size: 22px;
        }

    }
</style>
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Return Renteoutd</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                Scan Barcode
                            </label>
                            <input type="text" class="form-control" id="searchbarcode">
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="booking-summary-bar" style="background:#d1ffd6;">

                                <div class="summary-item all-bookings">
                                    <span class="summary-label">
                                        All Rentouts
                                    </span>
                                    <span class="summary-value" id="all_count">
                                        0
                                    </span>
                                </div>

                                <div class="summary-divider"></div>

                                <div class="summary-item pending-bookings">
                                    <span class="summary-label">
                                        Today Returns
                                    </span>
                                    <span class="summary-value" id="pending_count">
                                        0
                                    </span>
                                </div>

                                <div class="summary-divider"></div>

                                <div class="summary-item ready-bookings">
                                    <span class="summary-label">
                                        Overdue Rentouts
                                    </span>
                                    <span class="summary-value" id="overdue_count">
                                        0
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <strong>Booking List</strong>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" id="bookingTable">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>Booked Date</th>
                                    <th>Booking Period</th>
                                    <th>Customer</th>
                                    <th>Amounts</th>
                                    <th>Status</th>
                                    <th width="250">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="booking_list"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<canvas id="barcodeCanvas" style="display:none;"></canvas>

<div class="modal fade" id="viewBookingModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    Booking Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="booking_view_area"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rentoutreturnModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    Booking Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="booking_view_area2"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-success" id="modalreturnedBtn">
                    Return
                </button>
            </div>
        </div>
    </div>
</div>

<?php include_once('footer.php'); ?>

<script>
    $(document).ready(function () {

        $(document).on('click','.booking-item-row',function(){
            $(this).toggleClass('active-row');
        });

        loadBookings();

        function loadBookings() {
            $.get("../routes/booking/pending_return_list.php", function (res) {

                let data = JSON.parse(res);

                if ($.fn.DataTable.isDataTable('#bookingTable')) {
                    $('#bookingTable').DataTable().destroy();
                }

                $('#booking_list').html(data.table);

                $('#all_count').html(data.all);
                $('#pending_count').html(data.pending);
                $('#overdue_count').html(data.overdue);

                setTimeout(function () {

                    $('#bookingTable').DataTable({
                        responsive: true,
                        pageLength: 10,
                        destroy: true
                    });

                }, 100);

            });
        }

        function calculateReturnAmounts(){

            let holdAmount = parseFloat($('#hold_amount_available').val()) || 0;

            let lateDays = parseInt($('#late_days').val()) || 0;

            let dayPenalty = parseFloat($('#one_day_penalty').val()) || 0;

            let claimAmount = parseFloat($('#claim_amount').val()) || 0;

            let totalPenalty = lateDays * dayPenalty;

            $('#total_penalty').val(totalPenalty.toFixed(2));

            let finalBalance = holdAmount - totalPenalty - claimAmount;

            $('#final_hold_balance').val(finalBalance.toFixed(2));
        }

        $('#searchbarcode').on('input',function(){
            let barcode=$(this).val().trim();
            if(barcode.length<10){
                return;
            }

            $.get("../routes/booking/search_barcode_rentout.php",{
                barcode:barcode
                },function(res){
                    let data = JSON.parse(res);
                    if(data.status === false){
                        Swal.fire({
                            icon:'warning',
                            title:'Warning',
                            text:data.message
                        });
                        $('#searchbarcode').val('').focus();
                        return;
                    }
                    $('#booking_list').html(data.table);
            });
        });

        let currentBookingId = '';

        $(document).on('click', '.btn-view-booking', function () {

            let bookingId = $(this).data('id');
            currentBookingId = bookingId;
            let row=$(this).closest('tr');
            let canReady=row.find('.btn-ready').length>0;

            $.get("../routes/booking/view_booking.php", {
                booking_id: bookingId
            }, function (res) {

                $('#booking_view_area').html(res);

                 if(canReady){
                    $('#modalReadyBtn').show();
                }else{
                    $('#modalReadyBtn').hide();
                }
                $('#viewBookingModal').modal('show');

            });

        });

        $(document).on('click', '.btn-return', function () {

            let bookingId = $(this).data('id');
            currentBookingId = bookingId;
            let row=$(this).closest('tr');
            let canReady=row.find('.btn-ready').length>0;

            $.get("../routes/booking/view_bookingreturn.php", {
                booking_id: bookingId
            }, function (res) {

                $('#booking_view_area2').html(res);
                $('#rentoutreturnModal').modal('show');

                $(document).on('input','#one_day_penalty,#claim_amount',function(){
                    calculateReturnAmounts();
                });

                calculateReturnAmounts();

            });

        });

        $(document).on('input','#claim_amount',function(){

            let hold=parseFloat($('#hold_amount_view').data('hold')) || 0;
            let claim=parseFloat($(this).val()) || 0;
            let balance=hold-claim;

            if(balance<0){
                balance=0;
            }

            $('#balance_hold_view').html(balance.toFixed(2));

            if(claim>0){
                $('#balance_hold_view')
                .css({
                    'background':'#fff5f5',
                    'color':'#dc3545'
                });
            }else{
                $('#balance_hold_view')
                .css({
                    'background':'#f0fff4',
                    'color':'#198754'
                });
            }

        });

        $('#modalreturnedBtn').click(function(){
            if(currentBookingId==''){
                return;
            }
            $('#rentoutreturnModal').modal('hide');
            Swal.fire({
                title:'Return Rental Items?',
                text:'Are you sure you have collected all rented items, checked for any missing items, damages, defects, or other issues before completing the return process?',
                icon:'question',
                showCancelButton:true,
                confirmButtonText:'Yes, collect Items'
            }).then((result)=>{
                if(result.isConfirmed){
                    Swal.fire({
                        title:'Enter PIN',
                        html:`
                        <input 
                        type="password"
                        id="swal_pin"
                        class="swal2-input"
                        maxlength="6"
                        autocomplete="new-password"
                        placeholder="Enter 6 Digit PIN">
                        `,
                        showConfirmButton:false,
                        showCancelButton:true,

                        didOpen:()=>{
                            $('#swal_pin').focus();
                            $('#swal_pin').on('input',function(){
                                let pin=$(this).val();
                                if(pin.length==6){
                                    $.ajax({
                                        url:'../routes/auth/verify_pin.php',
                                        type:'POST',
                                        data:{
                                            pin:pin
                                        },
                                        success:function(res){

                                            let data=JSON.parse(res);
                                            if(data.status=="error"){
                                                $('#swal_pin').val('');
                                                Swal.showValidationMessage('Wrong PIN');
                                                return;
                                            }

                                            Swal.close();
                                            Swal.fire({
                                                title:'Updating Booking...',
                                                allowOutsideClick:false,
                                                didOpen:()=>{
                                                    Swal.showLoading();
                                                }
                                            });

                                            $.ajax({
                                                url:'../routes/booking/return_booking.php',
                                                type:'POST',
                                                data:{
                                                    booking_id:currentBookingId,
                                                    handoverby:data.id,
                                                    return_note:$('#damage_note').val(),
                                                    claim_amount:(parseFloat($('#claim_amount').val())||0)+(parseFloat($('#total_penalty').val())||0)
                                                },
                                                success:function(res){
                                                    res=res.trim();
                                                    if(res=="01"){
                                                        Swal.fire({
                                                                icon: 'success',
                                                                title:'Rentout Returned',
                                                                html: 'Booking ID : <b>' + currentBookingId +'</b>'
                                                            })
                                                            .then(
                                                                (
                                                                    result) => {
                                                                    if (result
                                                                        .isConfirmed
                                                                        ) {
                                                                        ocation.reload();
                                                                        // $.get("../routes/booking/getbooking.php",{ booking_id: currentBookingId },function(res){

                                                                        //     const jdata=JSON.parse(res);

                                                                        //    generateRentedReceiptPDF(jdata);

                                                                        // });
                                                                    }
                                                                }
                                                                );
                                                        loadBookings();
                                                    }else{
                                                        Swal.fire({
                                                            icon:'error',
                                                            title:'Update Failed'
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
                }
            });
        });

        $(document).on('click', '.booking-item-row', function () {
            $(this).toggleClass('booking-item-selected');
        });

        $(document).on('click', '.btn-reprint', function () {

            let bookingId = $(this).data('id');
            $.get("../routes/booking/getbooking.php", {
                booking_id: bookingId
            }, function (res) {
                const jdata = JSON.parse(res);
                generateBookingReceiptPDF(jdata);
            });
        });

        $(document).on('click', '.btn-reprint2', function () {

            let bookingId = $(this).data('id');
            $.get("../routes/booking/getbooking.php", {
                booking_id: bookingId
            }, function (res) {
                const jdata = JSON.parse(res);
                generateBookingReceipt2PDF(jdata);
            });
        });

        $(document).on('click', '.btn-reprintrent', function () {

            let bookingId = $(this).data('id');
            $.get("../routes/booking/getbooking.php", {
                booking_id: bookingId
            }, function (res) {
                const jdata = JSON.parse(res);
                generateRentedReceiptPDF(jdata);
                
            });
        });

        $(document).on('click', '.btn-reprintrent2', function () {

            let bookingId = $(this).data('id');
            $.get("../routes/booking/getbooking.php", {
                booking_id: bookingId
            }, function (res) {
                const jdata = JSON.parse(res);
                generateRentedReceipt2PDF(jdata);
                
            });
        });


        function generateBookingReceiptPDF(data) {

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

            JsBarcode("#barcodeCanvas",data.booking.barcode,{
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
            doc.text("BOOKING CONFIRMATION", 60, 37, {
                align: 'left'
            });

            doc.setTextColor(0, 0, 0);

            y += 25;

            doc.setDrawColor(38, 0, 8);
            doc.setLineWidth(0.4);
            doc.roundedRect(10, y, 190, 45, 0, 0);
            doc.setTextColor(0, 0, 0);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("BOOKING INFORMATION", 15, y + 5);
            doc.setDrawColor(180, 180, 180);
            doc.setLineWidth(0.2);

            doc.line(15, y + 6, 195, y + 6);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Booking ID", 15, y + 12);
            doc.text(": " + data.booking.id, 45, y + 12);
            doc.text("Booked Time", 110, y + 12);
            doc.text(": " + data.booking.created_at, 145, y + 12);
            doc.text("Booking Date", 15, y + 18);
            doc.text(": " + data.booking.booking_date, 45, y + 18);
            doc.text("Return Date", 110, y + 18);
            doc.text(": " + data.booking.return_date, 145, y + 18);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("CUSTOMER INFORMATION", 15, y + 27);
            doc.line(15, y + 28, 195, y + 28);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Customer", 15, y + 34);
            doc.text(": " + data.booking.customer_name, 45, y + 34);
            doc.text("Phone", 15, y + 40);
            doc.text(": " + data.booking.phone, 45, y + 40);
            doc.text("NIC", 110, y + 40);
            doc.text(": " + data.booking.nic, 145, y + 40);
            y += 48;

            doc.setFillColor(25, 135, 84);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');

            doc.text("Booked Items", 15, y + 4);
            y += 8;
            doc.setTextColor(0, 0, 0);

            doc.setFillColor(240, 240, 240);

            doc.rect(10, y, 15, 7, 'FD');
            doc.rect(25, y, 90, 7, 'FD');
            doc.rect(115, y, 35, 7, 'FD');
            doc.rect(150, y, 50, 7, 'FD');

            doc.setFontSize(9);
            doc.setFont(undefined, 'bold');

            doc.text("#", 16, y + 5);
            doc.text("Product", 60, y + 5);
            doc.text("Code",120,y+5);
            doc.text("Qty",170,y+5);

            y += 7;

            doc.setFont(undefined, 'normal');

            let count = 1;

            data.items.forEach(function (item) {

                checkPageBreak(20);
                doc.rect(10, y, 15, 7);
                doc.rect(25, y, 90, 7);
                doc.rect(115, y, 35, 7);
                doc.rect(150, y, 50, 7);
                doc.text(String(count), 16, y + 5);
                let pname = item.product_name;
                if (pname.length > 38) {
                    pname = pname.substring(0, 38) + '...';
                }
                doc.text(pname, 28, y + 5);
                doc.text(item.product_code,120,y+5);
                doc.text(String(item.qty),172,y+5);
                y += 7;
                count++;
            });

            y += 7;

            checkPageBreak(20);
            doc.setFont(undefined, 'bold');

            doc.text("Payment Summary", 118, y + 10);

            doc.text("Full Amount :", 118, y + 15);
            doc.text(parseFloat(data.booking.booking_amount).toFixed(2), 175, y + 15);

            doc.text("Advance Amount :", 118, y + 21);
            doc.text(parseFloat(data.booking.advance_amount).toFixed(2), 175, y + 21);

            doc.text("Diposit Amount :", 118, y + 27);
            doc.text(parseFloat(data.booking.hold_amount).toFixed(2), 175, y + 27);

            doc.text("Diposit Advance :", 118, y + 33);
            doc.text(parseFloat(data.booking.hold_payed_amount).toFixed(2), 175, y + 33);
            y += 40;

            doc.setFontSize(8);
            doc.setFont(undefined, 'bold');

            checkPageBreak(20);

            doc.text("Important Terms & Conditions :", 15, y);

            doc.setFont(undefined, 'normal');

            doc.text("• All rented jewellery items must be returned on or before the agreed return date.", 15,
                y + 5);
            doc.text("• Late returns will be charged with additional penalty payments.", 15, y + 9);
            doc.text("• Customers are responsible for damages, missing items, breakages or losses.", 15, y +
                13);
            doc.text("• Repair or replacement charges will apply according to item condition.", 15, y + 17);
            doc.text("• Advance and hold payments are non-refundable under company policy.", 15, y + 21);

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

        function generateBookingReceipt2PDF(data) {

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

            JsBarcode("#barcodeCanvas",data.booking.barcode,{
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
            doc.text("BOOKING CONFIRMATION", 60, 37, {
                align: 'left'
            });

            doc.setTextColor(0, 0, 0);

            y += 25;

            doc.setDrawColor(38, 0, 8);
            doc.setLineWidth(0.4);
            doc.roundedRect(10, y, 190, 45, 0, 0);
            doc.setTextColor(0, 0, 0);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("BOOKING INFORMATION", 15, y + 5);
            doc.setDrawColor(180, 180, 180);
            doc.setLineWidth(0.2);

            doc.line(15, y + 6, 195, y + 6);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Booking ID", 15, y + 12);
            doc.text(": " + data.booking.id, 45, y + 12);
            doc.text("Booked Time", 110, y + 12);
            doc.text(": " + data.booking.created_at, 145, y + 12);
            doc.text("Booking Date", 15, y + 18);
            doc.text(": " + data.booking.booking_date, 45, y + 18);
            doc.text("Return Date", 110, y + 18);
            doc.text(": " + data.booking.return_date, 145, y + 18);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("CUSTOMER INFORMATION", 15, y + 27);
            doc.line(15, y + 28, 195, y + 28);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Customer", 15, y + 34);
            doc.text(": " + data.booking.other_customer_name, 45, y + 34);
            doc.text("Phone", 15, y + 40);
            doc.text(": " + data.booking.other_customer_phone, 45, y + 40);
            doc.text("NIC", 110, y + 40);
            doc.text(": " + data.booking.other_customer_nic, 145, y + 40);
            y += 48;

            doc.setFillColor(25, 135, 84);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');

            doc.text("Booked Items", 15, y + 4);
            y += 8;
            doc.setTextColor(0, 0, 0);

            doc.setFillColor(240, 240, 240);

            doc.rect(10, y, 15, 7, 'FD');
            doc.rect(25, y, 90, 7, 'FD');
            doc.rect(115, y, 35, 7, 'FD');
            doc.rect(150, y, 50, 7, 'FD');

            doc.setFontSize(9);
            doc.setFont(undefined, 'bold');

            doc.text("#", 16, y + 5);
            doc.text("Product", 60, y + 5);
            doc.text("Code",120,y+5);
            doc.text("Qty",170,y+5);

            y += 7;

            doc.setFont(undefined, 'normal');

            let count = 1;

            data.items.forEach(function (item) {

                checkPageBreak(20);
                doc.rect(10, y, 15, 7);
                doc.rect(25, y, 90, 7);
                doc.rect(115, y, 35, 7);
                doc.rect(150, y, 50, 7);
                doc.text(String(count), 16, y + 5);
                let pname = item.product_name;
                if (pname.length > 38) {
                    pname = pname.substring(0, 38) + '...';
                }
                doc.text(pname, 28, y + 5);
                doc.text(item.product_code,120,y+5);
                doc.text(String(item.qty),172,y+5);
                y += 7;
                count++;
            });

            y += 7;

            checkPageBreak(20);
            doc.setFont(undefined, 'bold');

            doc.text("Payment Summary", 118, y + 10);

            doc.text("Diposit Amount :", 118, y + 15);
            doc.text(parseFloat(data.booking.hold_amount).toFixed(2), 175, y + 15);

            doc.text("Diposit Advance :", 118, y + 21);
            doc.text(parseFloat(data.booking.hold_payed_amount).toFixed(2), 175, y + 21);

            y += 40;

            doc.setFontSize(8);
            doc.setFont(undefined, 'bold');

            checkPageBreak(20);

            doc.text("Important Terms & Conditions :", 15, y);

            doc.setFont(undefined, 'normal');

            doc.text("• All rented jewellery items must be returned on or before the agreed return date.", 15,
                y + 5);
            doc.text("• Late returns will be charged with additional penalty payments.", 15, y + 9);
            doc.text("• Customers are responsible for damages, missing items, breakages or losses.", 15, y +
                13);
            doc.text("• Repair or replacement charges will apply according to item condition.", 15, y + 17);
            doc.text("• Advance and hold payments are non-refundable under company policy.", 15, y + 21);

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

        function generateRentedReceiptPDF(data) {

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

            JsBarcode("#barcodeCanvas",data.booking.barcode,{
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
            doc.text("RENTOUT CONFIRMATION", 60, 37, {
                align: 'left'
            });

            doc.setTextColor(0, 0, 0);

            y += 25;

            doc.setDrawColor(38, 0, 8);
            doc.setLineWidth(0.4);
            doc.roundedRect(10, y, 190, 45, 0, 0);
            doc.setTextColor(0, 0, 0);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("RENTOUT INFORMATION", 15, y + 5);
            doc.setDrawColor(180, 180, 180);
            doc.setLineWidth(0.2);

            doc.line(15, y + 6, 195, y + 6);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Booking ID", 15, y + 12);
            doc.text(": " + data.booking.id, 45, y + 12);
            doc.text("Booked Time", 110, y + 12);
            doc.text(": " + data.booking.created_at, 145, y + 12);
            doc.text("Booking Date", 15, y + 18);
            doc.text(": " + data.booking.booking_date, 45, y + 18);
            doc.text("Return Date", 110, y + 18);
            doc.text(": " + data.booking.return_date, 145, y + 18);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("CUSTOMER INFORMATION", 15, y + 27);
            doc.line(15, y + 28, 195, y + 28);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Customer", 15, y + 34);
            doc.text(": " + data.booking.customer_name, 45, y + 34);
            doc.text("Phone", 15, y + 40);
            doc.text(": " + data.booking.phone, 45, y + 40);
            doc.text("NIC", 110, y + 40);
            doc.text(": " + data.booking.nic, 145, y + 40);
            y += 48;

            doc.setFillColor(25, 135, 84);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');

            doc.text("Rented Items", 15, y + 4);
            y += 8;
            doc.setTextColor(0, 0, 0);

            doc.setFillColor(240, 240, 240);

            doc.rect(10, y, 15, 7, 'FD');
            doc.rect(25, y, 90, 7, 'FD');
            doc.rect(115, y, 35, 7, 'FD');
            doc.rect(150, y, 50, 7, 'FD');

            doc.setFontSize(9);
            doc.setFont(undefined, 'bold');

            doc.text("#", 16, y + 5);
            doc.text("Product", 60, y + 5);
            doc.text("Code",120,y+5);
            doc.text("Qty",170,y+5);

            y += 7;

            doc.setFont(undefined, 'normal');

            let count = 1;

            data.items.forEach(function (item) {

                checkPageBreak(20);
                doc.rect(10, y, 15, 7);
                doc.rect(25, y, 90, 7);
                doc.rect(115, y, 35, 7);
                doc.rect(150, y, 50, 7);
                doc.text(String(count), 16, y + 5);
                let pname = item.product_name;
                if (pname.length > 38) {
                    pname = pname.substring(0, 38) + '...';
                }
                doc.text(pname, 28, y + 5);
                doc.text(item.product_code,120,y+5);
                doc.text(String(item.qty),172,y+5);
                y += 7;
                count++;
            });

            y += 7;

            checkPageBreak(20);
            doc.setFont(undefined, 'bold');

            doc.text("Payment Summary", 118, y + 10);

            doc.text("Full Amount :", 118, y + 15);
            doc.text(parseFloat(data.booking.booking_amount).toFixed(2), 175, y + 15);

            doc.text("Advance Amount :", 118, y + 21);
            doc.text(parseFloat(data.booking.advance_amount).toFixed(2), 175, y + 21);

            doc.text("Balance Amount :",118,y+27);
            doc.text(
                (
                    parseFloat(data.booking.booking_amount)-
                    parseFloat(data.booking.advance_amount)
                ).toFixed(2),
                175,
                y+27
            );

            doc.text("Hold Amount :", 118, y + 33);
            doc.text(parseFloat(data.booking.hold_amount).toFixed(2), 175, y + 33);

            y += 40;

            doc.setFontSize(8);
            doc.setFont(undefined, 'bold');

            checkPageBreak(20);

            doc.text("Important Terms & Conditions :", 15, y);

            doc.setFont(undefined, 'normal');

            doc.text("• All rented jewellery items must be returned on or before the agreed return date.", 15,
                y + 5);
            doc.text("• Late returns will be charged with additional penalty payments.", 15, y + 9);
            doc.text("• Customers are responsible for damages, missing items, breakages or losses.", 15, y +
                13);
            doc.text("• Repair or replacement charges will apply according to item condition.", 15, y + 17);
            doc.text("• Advance and hold payments are non-refundable under company policy.", 15, y + 21);

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

        function generateRentedReceipt2PDF(data) {

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

            JsBarcode("#barcodeCanvas",data.booking.barcode,{
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
            doc.text("RENTOUT CONFIRMATION", 60, 37, {
                align: 'left'
            });

            doc.setTextColor(0, 0, 0);

            y += 25;

            doc.setDrawColor(38, 0, 8);
            doc.setLineWidth(0.4);
            doc.roundedRect(10, y, 190, 45, 0, 0);
            doc.setTextColor(0, 0, 0);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("RENTOUT INFORMATION", 15, y + 5);
            doc.setDrawColor(180, 180, 180);
            doc.setLineWidth(0.2);

            doc.line(15, y + 6, 195, y + 6);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Booking ID", 15, y + 12);
            doc.text(": " + data.booking.id, 45, y + 12);
            doc.text("Booked Time", 110, y + 12);
            doc.text(": " + data.booking.created_at, 145, y + 12);
            doc.text("Booking Date", 15, y + 18);
            doc.text(": " + data.booking.booking_date, 45, y + 18);
            doc.text("Return Date", 110, y + 18);
            doc.text(": " + data.booking.return_date, 145, y + 18);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');
            doc.text("CUSTOMER INFORMATION", 15, y + 27);
            doc.line(15, y + 28, 195, y + 28);

            doc.setFontSize(10);
            doc.setFont(undefined, 'normal');
            doc.text("Customer", 15, y + 34);
            doc.text(": " + data.booking.other_customer_name, 45, y + 34);
            doc.text("Phone", 15, y + 40);
            doc.text(": " + data.booking.other_customer_phone, 45, y + 40);
            doc.text("NIC", 110, y + 40);
            doc.text(": " + data.booking.other_customer_nic, 145, y + 40);
            y += 48;

            doc.setFillColor(25, 135, 84);
            doc.setFontSize(12);
            doc.setFont(undefined, 'bold');

            doc.text("Rented Items", 15, y + 4);
            y += 8;
            doc.setTextColor(0, 0, 0);

            doc.setFillColor(240, 240, 240);

            doc.rect(10, y, 15, 7, 'FD');
            doc.rect(25, y, 90, 7, 'FD');
            doc.rect(115, y, 35, 7, 'FD');
            doc.rect(150, y, 50, 7, 'FD');

            doc.setFontSize(9);
            doc.setFont(undefined, 'bold');

            doc.text("#", 16, y + 5);
            doc.text("Product", 60, y + 5);
            doc.text("Code",120,y+5);
            doc.text("Qty",170,y+5);

            y += 7;

            doc.setFont(undefined, 'normal');

            let count = 1;

            data.items.forEach(function (item) {

                checkPageBreak(20);
                doc.rect(10, y, 15, 7);
                doc.rect(25, y, 90, 7);
                doc.rect(115, y, 35, 7);
                doc.rect(150, y, 50, 7);
                doc.text(String(count), 16, y + 5);
                let pname = item.product_name;
                if (pname.length > 38) {
                    pname = pname.substring(0, 38) + '...';
                }
                doc.text(pname, 28, y + 5);
                doc.text(item.product_code,120,y+5);
                doc.text(String(item.qty),172,y+5);
                y += 7;
                count++;
            });

            y += 7;

            checkPageBreak(20);
            doc.setFont(undefined, 'bold');

            doc.text("Payment Summary", 118, y + 10);

            doc.text("Diposit Amount :", 118, y + 15);
            doc.text(parseFloat(data.booking.hold_amount).toFixed(2), 175, y + 15);

            doc.text("Advance Diposit :", 118, y + 21);
            doc.text(parseFloat(data.booking.hold_payed_amount).toFixed(2), 175, y + 21);

            doc.text("Balance Diposit :",118,y+27);
            doc.text(
                (
                    parseFloat(data.booking.hold_amount)-
                    parseFloat(data.booking.hold_payed_amount)
                ).toFixed(2),
                175,
                y+27
            );

            y += 40;

            doc.setFontSize(8);
            doc.setFont(undefined, 'bold');

            checkPageBreak(20);

            doc.text("Important Terms & Conditions :", 15, y);

            doc.setFont(undefined, 'normal');

            doc.text("• All rented jewellery items must be returned on or before the agreed return date.", 15,
                y + 5);
            doc.text("• Late returns will be charged with additional penalty payments.", 15, y + 9);
            doc.text("• Customers are responsible for damages, missing items, breakages or losses.", 15, y +
                13);
            doc.text("• Repair or replacement charges will apply according to item condition.", 15, y + 17);
            doc.text("• Advance and hold payments are non-refundable under company policy.", 15, y + 21);

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
    });
</script>