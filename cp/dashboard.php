<?php 
// include("access.php");
include("../includes/db.conn.php");
// include("language.php");
// $path=pathinfo($_SERVER['PHP_SELF']);
// $filename=$path['basename'];
// $get_sub_title=mysql_query("select * from bsi_adminmenu where url='".$filename."'");
// if(mysql_num_rows($get_sub_title)){
// 	$get_sub_title_row=mysql_fetch_array($get_sub_title);
// 	$get_parent_title=mysql_query("select * from bsi_adminmenu where id='".$get_sub_title_row['parent_id']."'");
// 	$get_parent_title_row=mysql_fetch_array($get_parent_title);
// 	$main_title=$get_parent_title_row['name'].' > '.$get_sub_title_row['name'];
// 	$_SESSION['main_title']=$main_title;
// }
// if($filename=='admin-home.php')
// $main_title="Home";
// elseif($filename=='change_password.php')
// $main_title="Change Password";
// else
// $main_title=$_SESSION['main_title'];
 
// // include("header.php"); 
// include("../includes/conf.class.php");	
// include("../includes/admin.class.php");

// getting the preferred rooms
$get_preferences = mysql_query("select count(bsi_reservation.id) as `selections`, bsi_roomtype.type_name from bsi_reservation left join bsi_roomtype on bsi_reservation.room_id = bsi_roomtype.roomtype_ID group by `type_name` order by `selections` desc");
$count = 0;
if (mysql_num_rows($get_preferences)) {
	while($row_user = mysql_fetch_assoc($get_preferences)){
		if ($row_user['type_name']==null || $row_user['type_name']=='null') {
			// $preferences[$count]['y'] = 'No data';
   //          $preferences[$count]['a'] = (int) $row_user['selections'];
		}else {
			$preferences[$count]['y'] = $row_user['type_name'];
            $preferences[$count]['a'] = (int) $row_user['selections'];
		}
		
		$count++;
	}
}else {
	$preferences[$count]['y'] = 'No Data';
	$preferences[$count]['a'] = (int) 0;
}
$preferences = array_values($preferences);
// getting the bookings data
$get_bookings=mysql_query("select count(bsi_bookings.booking_id) as `bookings`, monthname(bsi_bookings.start_date) as `month` from bsi_bookings left join bsi_clients on bsi_bookings.client_id = bsi_clients.client_id group by `month`");
$count = 0;
if(mysql_num_rows($get_bookings)){
	while($row_user = mysql_fetch_assoc($get_bookings)){
		$bookings[$count]['y'] = $row_user['month'];
		$bookings[$count]['a'] = (int) $row_user['bookings'];
		$count++;
	}
}else {
	$bookings[$count]['y'] = 'No Data';
	$bookings[$count]['a'] = (int) 0;
}

// getting the data table for frequent clients
$count = 1;
$table_data = '';
$get_clients = mysql_query("select count(bsi_bookings.booking_id) as `visits`, bsi_clients.first_name, bsi_clients.surname, bsi_clients.email, bsi_clients.phone from bsi_bookings left join bsi_clients on bsi_bookings.client_id = bsi_clients.client_id group by bsi_bookings.client_id order by `visits` desc");

if(mysql_num_rows($get_clients)){
	while($row_user = mysql_fetch_assoc($get_clients)){
		// echo "<pre>";print_r($row_user);die();
		$table_data .= '<tr>
                			<td>'.$count.'</td>
                			<td>'.$row_user['first_name'].'</td>
                			<td>'.$row_user['surname'].'</td>
                			<td>'.$row_user['email'].'</td>
                			<td>'.$row_user['phone'].'</td>
                			<td>'.$row_user['visits'].'</td>
                		</tr>';
		$count++;
	}
}
?>      
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>DusitD2</title>

    <!-- Bootstrap Core CSS -->
    <link href="../admin/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../admin/vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="../admin/vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="../admin/vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../admin/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../admin/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery -->
    <script src="../admin/vendor/jquery/jquery.min.js"></script>
</head>

<body>

    <div id="wrapper">

        <!-- Navigation -->
        <?php include_once('admin_nav.php');?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Popular Suites
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="preference-bar-chart"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-6 -->
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Booking Trends
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="bookings-bar-chart"></div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-6 -->
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Frequent Clients
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                            	<thead>
                            		<tr>
                            			<th>#</th>
                            			<th>First Name</th>
                            			<th>Last Name</th>
                            			<th>Email</th>
                            			<th>Phone</th>
                            			<th>Visits</th>
                            		</tr>
                            	</thead>
                            	<tbody>
                            		<?php echo $table_data;?>
                            	</tbody>
                            </table>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.col-lg-6 -->
               <!--  <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Donut Chart Example
                        </div>
                        <div class="panel-body">
                            <div id="morris-donut-chart"></div>
                        </div>
                        
                    </div>
                    
                </div> -->
                <!-- /.col-lg-6 -->
                <!-- <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Morris.js Usage
                        </div>
                        
						<div class="panel-body">
                            <p>Morris.js is a jQuery based charting plugin created by Olly Smith. In SB Admin, we are using the most recent version of Morris.js which includes the resize function, which makes the charts fully responsive. The documentation for Morris.js is available on their website, <a target="_blank" href="http://morrisjs.github.io/morris.js/">http://morrisjs.github.io/morris.js/</a>.</p>
                            <a target="_blank" class="btn btn-default btn-lg btn-block" href="http://morrisjs.github.io/morris.js/">View Morris.js Documentation</a>
                        </div> -->
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div> -->
                <!-- /.col-lg-6 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

    

   <!-- Bootstrap Core JavaScript -->
    <script src="../admin/vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../admin/vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Morris Charts JavaScript -->
    <script src="../admin/vendor/raphael/raphael.min.js"></script>
    <script src="../admin/vendor/morrisjs/morris.min.js"></script>
    <!-- <script src="../admin/data/morris-data.js"></script> -->
     <!-- DataTables JavaScript -->
    <script src="../admin/vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../admin/vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../admin/vendor/datatables-responsive/dataTables.responsive.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../admin/dist/js/sb-admin-2.js"></script>
    <script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    });

    $(function() {

    Morris.Bar({
        element: 'preference-bar-chart',
        data: <?php echo(json_encode($preferences));?>,
		  xkey: 'y',
		  ykeys: ['a'],
		  labels: ['Bookings'],
        resize: true
    });

    Morris.Bar({
        element: 'bookings-bar-chart',
        data: <?php echo(json_encode($bookings));?>,
		  xkey: 'y',
		  ykeys: ['a'],
		  labels: ['Bookings'],
        resize: true
    });
    
});

    </script>

</body>

</html>
