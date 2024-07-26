<?php


include "dbconfig.php";


$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");

$customer_id = $_GET['customer_id'];
$sql="select c.name as name from CPS3740.Customers c where c.id='$customer_id'";
$result = mysqli_query($con, $sql); 
        
        echo "<a href='logout.php'>User logout</a>\n";
        echo "<br>\n";
$sql2="select amount,type from CPS3740_2023S.Money_peterjus m,CPS3740.Customers c where m.cid=c.id and c.id='$customer_id'";
        $r=mysqli_query($con, $sql2);
        $total_balance=0;
        while ($row = mysqli_fetch_array($r)) {
                if ($row["type"] == "D") {
                $total_balance+=$row["amount"];
            }
                if ($row["type"] == "W") {
                $total_balance-=$row["amount"];
            }
        }

if ($result) {
    if (mysqli_num_rows($result)>0) {
        $row=mysqli_fetch_array($result);
        $user_name = $row["name"];
        echo "<br><b>Add Transaction</b>\n";
        echo "<br><b>$user_name</b> current balance is <b>$total_balance</b>.\n";
        echo "<br><form action='insert_transaction.php' method='GET' required='required'>Transaction code: <input type='text' name='code' required='required'><br><input type='radio' id='Deposit' name='type' value='D'>Deposit<input type='radio' id='Withdraw' name='type' value='W'>Withdraw<br> Amount: <input type='text' name='amount'><br><input type='hidden' name='customer_id' value=$customer_id>Select a source: <select name='source_id'><option value=''></option>";
        $arrsql="select id,name from CPS3740.Sources";
        $arrresult=mysqli_query($con,$arrsql);
        while ($row=mysqli_fetch_assoc($arrresult)) {
            $name=$row['name'];
            $id=$row['id'];
            echo "<option value=$id>$name</option>";
        }
        
        echo "</select><br>Note: <input type='text' name='cnote'><br><input type='submit' value='Submit'></form>";
     }
    }
else {
  echo "Something is wrong with SQL:" . mysqli_error($con);    
}
mysqli_free_result($result);
mysqli_close($con);
?>