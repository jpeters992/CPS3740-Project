<?php

include "dbconfig.php";


$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");


$notes = $_GET['cnote']; 
$mids = $_GET['mid']; 
$updated_rows=0;
$deleted_rows=0;

for ($i = 0; $i < count($notes); $i++) {

    $new_note = mysqli_real_escape_string($con, $notes[$i]);
    $updated_mid = $mids[$i];
    
    $sql = "select note, code from CPS3740_2023S.Money_peterjus where mid=$updated_mid";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($result);
    
    if ($new_note != $row['note']) {
        
        $sql = "update CPS3740_2023S.Money_peterjus set note='$new_note' where mid=$updated_mid";
        
        if (mysqli_query($con, $sql)) {
            echo "The Note for code " . $row['code'] . " has been updated in the database.<br>";
            $updated_rows++;
        } else {
            echo "Something went wrong: " . mysqli_error($con);
        }
    }
}

if (isset($_GET['cdelete'])) {
    $cdelete = $_GET['cdelete'];
    $get_deleted_rows = implode(",", $cdelete);
    $count = count(explode(",", $get_deleted_rows));
    $deletd_mid = explode(",", $get_deleted_rows);
    $deleted_rows = 0;

    for ($i = 0; $i < $count; $i++) {
        $selectsql="select code from CPS3740_2023S.Money_peterjus where mid='".$deletd_mid[$i]."'";
        $selectres=mysqli_query($con, $selectsql);
        $row2=mysqli_fetch_array($selectres);
        $deletesql = "delete FROM CPS3740_2023S.Money_peterjus where mid='".$deletd_mid[$i]."'";
        $delresult = mysqli_query($con, $deletesql);
        
        if ($delresult) {
            echo "The code " . $row2['code'] . " has been deleted from the database.<br>";
            $deleted_rows++;
            
        } 
        else {
            echo "Something went wrong: " . mysqli_error($con);
        }
    }
}

echo "<br> $updated_rows records updated.\n";
echo "<br> $deleted_rows deletd records.";

mysqli_close($con);

?>