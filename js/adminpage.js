$(document).ready(function(){
  
   

      
    //lode product Count
    $.get("../routes/users/packcount.php", function (res) {
        //display data 
        $("#admin_product_count").html(res);
      });
      //load user count
    $.get("../routes/users/usercount.php", function (res) {
         //display data 
        $("#admin_user_count").html(res);
      });
    
    $.get("../routes/users/ordercount.php", function (res) {
      //display data 
     $("#admin_order_count").html(res);
   });

   $.get("../routes/users/ordercount2.php", function (res) {
    //display data 
   $("#admin_order_count2").html(res);
 });



    //load content to page admin page
    $('#add_employer').click(function(){
        $('#adminloadContent').load('emp/addemployer.php');
    });
    
    $('#edit_employer').click(function(){
      $('#adminloadContent').load('emp/editemployer.php');
     });

     $('#cardadmin02').click(function(){
      $('#adminloadContent').load('emp/editemployer.php');
     });

    $('#add_Customer').click(function(){
        $('#adminloadContent').load('user/adduser.php');
    });

    $('#edit_Customer').click(function(){
      $('#adminloadContent').load('user/edit_user.php');
    });

    $('#cardadmin01').click(function(){
      $('#adminloadContent').load('user/edit_user.php');
    });

    $('#activate_Customer').click(function(){
      $('#adminloadContent').load('user/activate_user.php');
    });

    $('#activate_Customer2').click(function(){
      $('#adminloadContent').load('user/activate_user2.php');
    });

    $('#add_pack').click(function(){
      $('#adminloadContent').load('pack/addpack.php');
    });

    $('#edit_pack').click(function(){
      $('#adminloadContent').load('pack/editpack.php');
    });

    $('#cardadmin03').click(function(){
      $('#adminloadContent').load('pack/editpack.php');
    });

    $('#addworkout').click(function(){
      $('#adminloadContent').load('plan/addplan.php');
    });

    $('#editworkout').click(function(){
      $('#adminloadContent').load('plan/editplan.php');
    });

    $('#addmeal').click(function(){
      $('#adminloadContent').load('meal/addmeal.php');
    });

    $('#editmeal').click(function(){
      $('#adminloadContent').load('meal/editmeal.php');
    });

    $('#addworkshe').click(function(){
      $('#adminloadContent').load('shedule/addshedule.php');
    });

    $('#editworkshe').click(function(){
      $('#adminloadContent').load('shedule/editshedule.php');
    });

    $('#addmembergroup').click(function(){
      $('#adminloadContent').load('group/addgroup.php');
    });

    $('#editmembergroup').click(function(){
      $('#adminloadContent').load('group/editgroup.php');
    });

    $('#cardadmin04').click(function(){
      $('#adminloadContent').load('group/editgroup.php');
    });

    $('#allres').click(function(){
      $('#adminloadContent').load('log/addlog.php');
    });

    $('#allresbyp').click(function(){
      $('#adminloadContent').load('log/editlog.php');
    });

    $('#addattencdance').click(function(){
      $('#adminloadContent').load('attendance/addatt.php');
    });

    $('#viewattendance').click(function(){
      $('#adminloadContent').load('attendance/viewatt.php');
    });

    $('#addsalery').click(function(){
      $('#adminloadContent').load('salery/addsalery.php');
    });

    $('#salaryhistory').click(function(){
      $('#adminloadContent').load('salery/history.php');
    });

    $('#addmemberfee').click(function(){
      $('#adminloadContent').load('fee/addfee.php');
    });

    $('#feehistory').click(function(){
      $('#adminloadContent').load('fee/history.php');
    });

    $('#pendingleaves').click(function(){
      $('#adminloadContent').load('leave/allleaves.php');
    });

    $('#leavehistoryadmin').click(function(){
      $('#adminloadContent').load('leave/allleaves2.php');
    });


    //load content to the member page
    $('#membermealplan').click(function(){
      $('#adminloadContent').load('meal/member.php');
    });

    $('#memberpayment').click(function(){
      $('#adminloadContent').load('fee/member.php');
    });

    $('#membershedule').click(function(){
      $('#adminloadContent').load('plan/member.php');
    });

    $('#memberprofile').click(function(){
      $('#adminloadContent').load('user/member.php');
    });

    $('#editmydata').click(function(){
      $('#adminloadContent').load('user/editdata.php');
    });

    $('#memberattendence').click(function(){
      $('#adminloadContent').load('attendance/member.php');
    });


    //load content to the trainer page
    $('#trainershedule').click(function(){
      $('#adminloadContent').load('plan/trainer.php');
    });

    $('#trainerattendance').click(function(){
      $('#adminloadContent').load('attendance/trainer.php');
    });

    $('#trainersalery').click(function(){
      $('#adminloadContent').load('salery/trainer.php');
    });

    $('#addleave').click(function(){
      $('#adminloadContent').load('leave/addleave.php');
    });

    $('#leavehistory').click(function(){
      $('#adminloadContent').load('leave/leavehistory.php');
    });

    $('#assignusers').click(function(){
      $('#adminloadContent').load('usergroup/usergroup.php');
    });

    $('#viewattendancemember').click(function(){
      $('#adminloadContent').load('attendance/memberatt.php');
    });

    $('#addsup').click(function(){
      $('#adminloadContent').load('sup/addsup.php');
    });

    $('#editsup').click(function(){
      $('#adminloadContent').load('sup/editsup.php');
    });

    $('#asignsup').click(function(){
      $('#adminloadContent').load('sup/asignsup.php');
    });

    $('#asignsupliments').click(function(){
      $('#adminloadContent').load('sup/asignsup.php');
    });

    $('#suplementplan').click(function(){
      $('#adminloadContent').load('sup/asignsup2.php');
    });


    $('#addbook').click(function(){
      $('#adminloadContent').load('book/addbook.php');
    });

    $('#editbook').click(function(){
      $('#adminloadContent').load('book/editbook.php');
    });

});

