<!DOCTYPE html>
<html>
<head>
  <title>PengYu-DestinationPackages</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>PengYu</title>

  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link rel="stylesheet" type="text/css" href="css/style.css"> 
  <link rel="stylesheet" type="text/css" href="font-awesome-4.7.0/css/font-awesome.min.css">
  <script type="text/javascript" src="datepicker/js/formden.js"></script>
  <link rel="stylesheet" href="datepicker/css/bootstrap-iso.css" /> 

</head>
<body>
  <?php include('functions.php') ?>
  <?php getHeader(); ?>

  <form id="Destinationform" method="post">
    <div class="container">
     <div class="row">
      <div class="col-md-8">
       <h2>Pick your desired destination now</h2>
       <div class="form-group">
         <label for="destination">Destination:</label>
         <select class="form-control" id="desti" name="Desti" values="<?php if(isset($_POST['Desti']))echo $_POST['Desti'];?>">
          <option>Choose destination</option>
          <?php
          $sql = "SELECT * from packages";
          $destinations = $dbcon->query($sql);
          if($destinations->num_rows > 0) {
            while($row = $destinations->fetch_assoc()){
              ?>
              <option value="<?php echo $row['Package_ID'];?>"  <?php echo isset($_REQUEST['package']) && $row['Package_ID'] == $_REQUEST['package'] ? 'selected' : '';?>><?php echo $row['Destination']; ?> 
              </option>
              <?php 
            }
          }
          ?>
        </select>
      </div>

      <div class="form-group">
       <label for="destination">Pickup Location:</label>
       <input class="form-control" id="Pickup" name="Pickup" placeholder="Enter Pickup Location" type="text" maxlength="70" value="<?php if(isset($_POST['Pickup']))echo $_POST['Pickup'];?>" Required />
     </div>

   </div>


   <div class="bootstrap-iso">
     <div class="col-md-4 col-sm-4 col-xs-12">

       <div class="form-group ">
        <label class="control-label" for="date">
          From:
        </label>
        <div class="input-group">
         <div class="input-group-addon">
          <i class="fa fa-calendar">
          </i>
        </div>
        <input class="form-control" id="Fdate" name="Fdate" placeholder="MM/DD/YYYY" type="text" maxlength="10" value="<?php if(isset($_POST['Fdate']))echo $_POST['Fdate'];?>"/ Required>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-sm-4 col-xs-12">

   <div class="form-group ">
    <label class="control-label " for="date">
     To
   </label>
   <div class="input-group">
     <div class="input-group-addon">
      <i class="fa fa-calendar">
      </i>
    </div>
    <input class="form-control" id="date" name="Tdate" placeholder="MM/DD/YYYY" type="text" maxlength="10"  value="<?php if(isset($_POST['Tdate']))echo $_POST['Tdate'];?>" Required />
  </div>
</div>


<div class="form-group price">
  <label>Total Price:</label>
  <div class="input-group">
   <div class="input-group-addon">
    <i class="fa fa-dollar">

    </i>
  </div>
  <input class="form-control value" id="price" name="price" type="text" maxlength="10"  value="<?php if(isset($_POST['Tdate']))echo $_POST['Tdate'];?>" Required />
</div>
<!-- <p class="price"><span class="currency">P</span><span class="value"></span></p> -->
</div>
</div>
</div>

<div class="col-md-8 align-right">
  <button class="btn btn-primary" type="submit" name="submit" id="submit" disabled>Submit</button>
</div>
</div>
</div>

</div>
</form>
<?php getFooterAssets(); ?>
<script type="text/javascript" src="datepicker/js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="datepicker/js/bootstrap-datepicker.min.js"></script>
<link rel="stylesheet" href="datepicker/css/bootstrap-datepicker3.css"/>


<script>
  $(document).ready(function(){

    var package_price = 1100;

    var packageId = $("#desti option:selected").val();
    $.ajax({
      type: "post",
      url: "lib/getpackageprice.php",
      data: {
        packID: packageId
      },
      success: function(data){  
        if(parseInt(data) != NaN){
          package_price = data;
        }            
      }
    });

    var date_input1=$('input[name="Fdate"]');
        var date_input2=$('input[name="Tdate"]'); //our date input has the name "date"
        var container=$('.bootstrap-iso form').length>0 ? $('.bootstrap-iso form').parent() : "body";
        date_input1.datepicker({
          format: 'mm/dd/yyyy',
          container: container,
          todayHighlight: true,
          autoclose: true,
        });
        date_input2.datepicker({
          format: 'mm/dd/yyyy',
          container: container,
          todayHighlight: true,
          autoclose: true,
        });
        var days = 0;

        $('input[name="Fdate"], input[name="Tdate"]').on('change', function(){  
          var a = moment(date_input1.val());
          var b = moment(date_input2.val());
          days = b.diff(a, 'days') + 1;
          console.log(days);
          if(date_input1.val() != "" && date_input2.val() != ""){
            var price = computePricing();
            $(".price .value").val(price <= 0 ? "Invalid Dates" : price);
            var theprice = $(".price .value").val();
            if(theprice == "Invalid Dates" || theprice == ""){
              $("#submit").attr("disabled", "disabled");   
            }else{      
              $("#submit").removeAttr("disabled");     
            }
          }
        });

        function computePricing(){
          return days * package_price;
        }

      });
    </script>


    <?php

    if (isset($_POST['submit'])){
      session_start();
      if(!isset($_SESSION["pengyu_details"])){
        echo "<script type='text/javascript'>alert('Login Required.Please Login.');</script>";
      }else{ 
        $dest=$_POST['Desti'];
        $pl=$_POST['Pickup'];
        $Fd=$_POST['Fdate'];
        $Td=$_POST['Tdate'];
        $ID=$_SESSION['pengyu_details']['UserID'];
        $FN=$_SESSION['pengyu_details']['Fname'];
        $LN=$_SESSION['pengyu_details']['Lname'];
        $Cn=$_SESSION['pengyu_details']['ContactNumber'];
        $q="INSERT INTO reservation(ClientID,ClientFname,ClientLname, ContactNumber,Destination,DateofRent,EndofRent,PickupLocation,StartingPrice,TotalPrice)values('$ID','$FN','$LN','$dest','$Cn','$Fd','$Td','$pl','ss','ss');";
        $result=$dbcon->query($q);
        // to know result
        if($result === TRUE){
          echo "<script type='text/javascript'>alert('Registration Success!');</script>";

        }else{
          echo "<script type='text/javascript'>alert('Registration failed due to system failure. Please try again. Sorxy for the inconvience.');</script>";
        }

        $dbcon->close();
      }
    }

    ?>
    <?php getFooter(); ?>
  </body>

  </html>