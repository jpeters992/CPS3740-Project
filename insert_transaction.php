<?php


include "dbconfig.php";


$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");

$customer_id=$_GET['customer_id'];

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

$code=mysqli_real_escape_string($con, $_GET['code']);
$amount=mysqli_real_escape_string($con, $_GET['amount']);
$sid=mysqli_real_escape_string($con, $_GET['source_id']);
$cnote=mysqli_real_escape_string($con, $_GET['cnote']);
//$type=$_GET['type'];
$curdatetime=date("Y-m-d H:i:s");

$m_code='';
$codesql="select m.code as code,m.cid,c.id from CPS3740_2023S.Money_peterjus m,CPS3740.Customers c where c.id='$customer_id' and m.cid=c.id and code='$code'";
$coderesult=mysqli_query($con,$codesql);
while ($row=mysqli_fetch_assoc($coderesult)) {
        $m_code=$row['code'];
}

if (!isset($_GET['type'])) {
    $type='';
     die("You did not select a transaction type 'Withdraw' or 'Deposit'.");
}
else{
    $type=$_GET['type'];
}
if($amount<=0) {
    echo "Amount must be higher than 0.";
}
else if($sid == "") {
    echo "You did not select a source.";
}
else if($type == "W" && $total_balance<$amount) {
    echo "Insufficient funds";
}
else if($m_code == $code) {
    echo "Transaction code already exists.";
}

else{
    $sql="insert into CPS3740_2023S.Money_peterjus (code, cid, type, amount, mydatetime, note, sid) values('$code', $customer_id, '$type', $amount, '$curdatetime', '$cnote', $sid)";
    $result=mysqli_query($con,$sql);

    if ($result) {
        echo "Transaction was successful.";
        if ($type == "D") {
            $total_balance+=$amount;
        }
        if ($type == "W") {
            $total_balance-=$amount;
        }
        echo "<br> Balance after transaction: <font color='blue'> $$total_balance</font>\n";
    }
    else{
        echo mysqli_error($con);
        echo "<br> Something went wrong.";
    }
}
?>