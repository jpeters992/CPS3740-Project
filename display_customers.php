<?php


include "dbconfig.php";


$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");

$sql="select * from CPS3740.Customers";
$result = mysqli_query($con, $sql); 

if ($result) {
    if (mysqli_num_rows($result)>0) {
        echo "The following accounts are in the bank system:\n";
        echo "<TABLE border=1>\n";
        echo "<TR><TH>ID<TH>Name<TH>Login<TH>Password<TH>DOB<TH>Gender<TH>Street<TH>City<TH>State<TH>Zipcode";
        while($row = mysqli_fetch_array($result)){
            $id = $row["id"];
            $name = $row["name"];
            $login = $row["login"];
            $password= $row["password"];
            $DOB = $row["DOB"];
            $gender=$row["gender"];
            $street = $row["street"];
            $city = $row["city"];
            $state = $row["state"];
            $zipcode = $row["zipcode"];
            echo "<TR><TD>$id<TD>$name<TD>$login<TD>$password<TD>$DOB<TD>$gender<TD>$street<TD>$city<TD>$state<TD>$zipcode\n";
        }
        echo "</TABLE>\n";
    }
    else
        echo "<br>No record found\n";
}
else {
  echo "Something is wrong with SQL:" . mysqli_error($con);    
}
mysqli_free_result($result);
mysqli_close($con);
?>