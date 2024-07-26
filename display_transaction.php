<?php


include "dbconfig.php";


$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");

$customer_id = $_GET['customer_id'];
$sql="select mid,s.id,code,amount,type,sid,cid,c.id as id,mydatetime,note,s.name as source from CPS3740_2023S.Money_peterjus m, CPS3740.Sources s, CPS3740.Customers c where c.id='$customer_id' and s.id=sid and m.cid=c.id";
$result = mysqli_query($con, $sql); 
$sql2="select name from CPS3740.Customers where id='$customer_id'";
$result2=mysqli_query($con,$sql2);
$row2=mysqli_fetch_array($result2);
$name=$row2['name'];        
        echo "<a href='logout.php'>User logout</a>\n";
        echo "<br><form action='update_transaction.php' method='GET'>\n";

        echo "You can only update the <b>Note</b> column.\n";
        echo "<br>\n";
        echo "<b> $name Transactions:</b>";

        echo "<TABLE border=1>\n";
            
            echo "<TR><TH>ID<TH>Code<TH>Type<TH>Amount<TH>Source<TH>Date Time<TH>Note<TH>Delete\n";
            $i=0;
            while ($row = mysqli_fetch_array($result)) { 
            $mid = $row["mid"];
            $cid=$row["cid"];
            $sid=$row["sid"];
            $code = $row["code"];
            $amount = $row["amount"];
            $type = $row["type"];
            $source_name= $row["source"];
            $mydatetime = $row["mydatetime"];
            $cnote = $row["note"];
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

            echo "<TR><TD><input type='hidden' value=$customer_id name='customer_id[$i]'><input type='hidden' value=$mid name='mid[$i]'>$mid<TD>$code<TD>$type<TD><font color='$type_color'> $amount</font><TD><input type='hidden' value=$sid name='sid[$i]'>$source_name<TD>$mydatetime<TD bgcolor='yellow'><input type='text' style='background-color:yellow' value='$cnote' name='cnote[$i]'><TD><input type='checkbox' name='cdelete[$i]' value='$mid'>\n";
            $i++;
        }
            echo "</TABLE>\n";
        
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
        echo "<br><input type='submit' value='Update transaction'></form>";

mysqli_free_result($result);
mysqli_close($con);
?>