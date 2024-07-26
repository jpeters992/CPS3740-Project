<?php


include "dbconfig.php";


$con = mysqli_connect($host, $username, $password, $dbname) 
      or die("<br>Cannot connect to DB:$dbname on $host\n");

if (isset($_POST['username']))
	$browser_username=$_POST["username"];
else
	die("Please go to index.html first\n");

$browser_password=$_POST["password"];

$sql="select login,password,name,DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), DOB)), '%Y') + 0 as ageconv,img,street,city,zipcode,id from CPS3740.Customers where login='$browser_username'";
$result = mysqli_query($con, $sql); 


if ($result) {
	if (mysqli_num_rows($result)>0) {
        $row=mysqli_fetch_array($result);
        $user_password=$row["password"];
        if($browser_password==$user_password) {
           $user_name=$row["name"];
           $customer_id=$row['id'];
           $os_browser=$_SERVER["HTTP_USER_AGENT"];
           $ip=$_SERVER["REMOTE_ADDR"];
           $img=$row["img"];
           $age=$row["ageconv"];
           $street=$row["street"];
           $city=$row["city"];
           $zipcode=$row["zipcode"];
           echo "<a href='logout.php'>User logout</a>\n";
           echo "<br> Your IP: $ip\n";
           echo "<br> Your browser and OS: $os_browser\n";
           if($ip!="10.*.*.*" && "131.125.*.*") { 
            echo "<br> You are NOT from Kean University.\n";
        } 
           else{ 
            echo "<br> You are from Kean University.\n";
        }
        $count_sql="select count(mid) as count from CPS3740_2023S.Money_peterjus m, CPS3740.Customers c where m.cid=c.id and login='$browser_username'";
        $count_result=mysqli_query($con, $count_sql);
        $row=mysqli_fetch_array($count_result);
        $transaction_count=$row['count'];
           echo "<br> Welcome Customer: <b>$user_name</b>\n";
           echo "<br> age: $age\n";
           echo "<br> Address: $street,$city,$zipcode\n";
           echo "<br>" . '<img src="data:image/jpeg;base64,'.base64_encode($img).'"/>' . "\n";
           echo "<hr>";
           echo "There are <b>$transaction_count</b> transactions for customer <b>$user_name</b>\n"; 
          echo "<TABLE border=1>\n";
        echo "<TR><TH>ID<TH>Code<TH>Type<TH>Amount<TH>Source<TH>Date Time<TH>Note";
        $money_sql="select mid,s.id,c.name as name,code,amount,type,sid,mydatetime,note,s.name as source from CPS3740_2023S.Money_peterjus m, CPS3740.Sources s, CPS3740.Customers c where login='$browser_username' and s.id=sid and m.cid=c.id";
        $money_result=mysqli_query($con, $money_sql);
        while($row = mysqli_fetch_array($money_result)){
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
        $sql2="select amount,type from CPS3740_2023S.Money_peterjus m,CPS3740.Customers c where m.cid=c.id and login='$browser_username'";
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
        
        if ($total_balance < 0)
            $balance_color="red";
        else
            $balance_color="blue";
        echo "Total balance: <font color='$balance_color'> $$total_balance</font>\n";

        $csql="select cid,id from CPS3740_2023S.Money_peterjus, CPS3740.Customers where cid=id and login='$browser_username'";
        $cresult=mysqli_query($con, $csql);
        while ($crow = mysqli_fetch_array($cresult)) {
            $money_id = $crow["cid"];
            $c_id = $crow["id"];

            if ($money_id == $c_id) {
                $c_id = $money_id;
            }
        }

        echo "<br><form action='add_transaction.php' method='GET'><input type='hidden' name='customer_id' value=$customer_id><br><input type='submit' value='Add transaction'></form>\n";
        echo "<a href='display_transaction.php?customer_id=$customer_id'>Display and update transaction</a>";
        echo "<form action='search_transaction.php' method='GET'><br>Keyword: <input type='text' name='keyword' required><input type='hidden' name='customer_id' value=$customer_id><input type='submit' value='Search transaction'></form>";
        }
        else{
        	die("Login $browser_username exists, but password doesn't match.");
       }
	}
	else
		die("Login $browser_username doesn't exist in the database.\n");
}
else {
  echo "Something is wrong with SQL:" . mysqli_error($con);	

}
mysqli_free_result($result);
mysqli_close($con);
?>