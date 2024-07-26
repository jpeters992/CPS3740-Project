<?php


include "dbconfig.php";


$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");

$customer_id = $_GET['customer_id'];
$sql="select * from CPS3740.Customers c, CPS3740_2023S.Money_peterjus where c.id='$customer_id'";
$result = mysqli_query($con, $sql); 

if ($result) {
    if (mysqli_num_rows($result)>0) {
        $row = mysqli_fetch_array($result);
        $user_name = $row["name"];
        $keyword = mysqli_real_escape_string($con, $_GET['keyword']);

        if($keyword == '*') {
            $all_sql = "select mid,s.id,c.name as name,code,amount,type,sid,mydatetime,note,s.name as source from CPS3740_2023S.Money_peterjus m, CPS3740.Sources s, CPS3740.Customers c where c.id='$customer_id' and s.id=sid and m.cid=c.id";
            $all_result = mysqli_query($con, $all_sql);
        }
        else {
            $all_sql = "select mid,s.id,c.name as name,code,amount,type,sid,mydatetime,note,s.name as source from CPS3740_2023S.Money_peterjus m, CPS3740.Sources s, CPS3740.Customers c where note like '%$keyword%' and c.id='$customer_id' and s.id=sid and m.cid=c.id";
            $all_result = mysqli_query($con, $all_sql);
        }

        echo "The transactions in customer <b>$user_name</b> records matched keyword <b>$keyword</b> are:\n";
            echo "<TABLE border=1>\n";
            echo "<TR><TH>ID<TH>Code<TH>Type<TH>Amount<TH>Source<TH>Date Time<TH>Note";
            while ($row = mysqli_fetch_array($all_result)) { 
            $mid = $row["mid"];
            $name = $row["name"];
            $code = $row["code"];
            $amount = $row["amount"];
            $type = $row["type"];
            $source_id= $row["source"];
            $mydatetime = $row["mydatetime"];
            $cnote=$row["note"];
            if ($type == "D") {     
                $type_color="blue";
                $type="Deposit";
                $amount="$$amount";
            }
            if ($type == "W") {
                $type_color="red";
                $type="Withdraw";
                $amount="-$$amount";
            }

            echo "<TR><TD>$mid<TD>$code<TD>$type<TD><font color='$type_color'> $amount</font><TD>$source_id<TD>$mydatetime<TD>$cnote\n";
        }
           echo "</TABLE>\n";

           if (mysqli_num_rows($all_result) == 0) {
            echo "No records found!<br>\n";
           }
        
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
        
        if ($total_balance < 0) {
            $balance_color="red";
        }
        else {
            $balance_color="blue";
        }
        echo "Total balance: <font color='$balance_color'> $$total_balance</font>\n";
        }
        else {
            echo "No records to search!";
        }
    }

    else {
        echo "Something is wrong with SQL:" . mysqli_error($con);    
}
mysqli_free_result($result);
mysqli_close($con);
?>