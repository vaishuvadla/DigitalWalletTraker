
<!DOCTYPE html>
<html>
<head>
    <title>UPI Payments Database Results</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php
    $host = "127.0.0.1";
    $user = "root";
    $password = "";
    $dbname = "upi_payments";

    // Establish database connection
    $conn = new mysqli($host, $user, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Process the query parameter
    $query = $_GET["query"];

    // Execute the selected query and display the results
    switch ($query) {
        case 1:
            $sql = "SELECT user_id, AVG(transaction_amount) AS avg_transaction_amount FROM transactions_table GROUP BY user_id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<h2>Average Transaction Amount for Each User</h2>";
                echo "<table>";
                echo "<tr><th>User ID</th><th>Average Transaction Amount</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["user_id"]. "</td><td>" . $row["avg_transaction_amount"]. "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
            break;
        case 2:
            $sql = "SELECT u.occupation, SUM(t.transaction_amount) AS total_spent FROM user_details u JOIN transactions_table t ON u.user_id = t.user_id GROUP BY u.occupation";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<h2>Total Amount Spent by Users in Each Occupation</h2>";
                echo "<table>";
                echo "<tr><th>Occupation</th><th>Total Spent</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["occupation"]. "</td><td>" . $row["total_spent"]. "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
            break;
        case 3:
            $sql = "SELECT transaction_method, COUNT(*) AS total_transactions FROM transaction_methods_platform_details GROUP BY transaction_method ORDER BY total_transactions DESC LIMIT 1";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<h2>Most Common Transaction Method Used by Users</h2>";
                echo "<table>";
                echo "<tr><th>Transaction Method</th><th>Total Transactions</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["transaction_method"]. "</td><td>" . $row["total_transactions"]. "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
            break;
        case 4:
            $sql = "SELECT u.user_id, u.name, SUM(t.transaction_amount) AS total_spent FROM user_details u JOIN transactions_table t ON u.user_id = t.user_id GROUP BY u.user_id, u.name ORDER BY total_spent DESC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<h2>Total Amount Spent by Each User (Ordered by Total Spent Amount)</h2>";
                echo "<table>";
                echo "<tr><th>User ID</th><th>Name</th><th>Total Spent</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["user_id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["total_spent"]. "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
            break;
        case 5:
            $sql = "SELECT u.user_id, u.name, c.credit_limit FROM user_details u JOIN credit_details c ON u.user_id = c.user_id ORDER BY c.credit_limit DESC LIMIT 5";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<h2>Top 5 Users with the Highest Credit Limits</h2>";
                echo "<table>";
                echo "<tr><th>User ID</th><th>Name</th><th>Credit Limit</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["user_id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["credit_limit"]. "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
            break;
        case 6:
            $sql = "SELECT u.name, SUM(t.transaction_amount) AS total_transactions, c.available_credit FROM user_details u JOIN transactions_details t ON u.user_id = t.user_id JOIN credit_details c ON u.user_id = c.user_id GROUP BY u.name, c.available_credit";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<h2>Total Transactions Amount and Available Credit per User</h2>";
                echo "<table>";
                echo "<tr><th>Name</th><th>Total Transactions</th><th>Available Credit</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["name"]. "</td><td>" . $row["total_transactions"]. "</td><td>" . $row["available_credit"]. "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
            break;
        case 7:
            $sql = "SELECT b.account_number, SUM(t.transaction_amount) AS total_transactions, b.account_balance FROM bank_account_details b JOIN transactions_details t ON b.account_id = t.account_id GROUP BY b.account_number, b.account_balance";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<h2>Total Transactions Amount and Balance per Account</h2>";
                echo "<table>";
                echo "<tr><th>Account Number</th><th>Total Transactions</th><th>Account Balance</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["account_number"]. "</td><td>" . $row["total_transactions"]. "</td><td>" . $row["account_balance"]. "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
            break;
        case 8:
            $sql = "SELECT u.user_id, u.name, t.transaction_date, COUNT(t.transaction_id) AS total_transactions, SUM(t.transaction_amount) AS total_amount FROM transactions_table t JOIN user_details u ON t.user_id = u.user_id GROUP BY u.user_id, u.name, t.transaction_date";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<h2>Total Transactions Count and Amount by Transaction Date</h2>";
                echo "<table>";
                echo "<tr><th>User ID</th><th>Name</th><th>Transaction Date</th><th>Total Transactions</th><th>Total Amount</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["user_id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["transaction_date"]. "</td><td>" . $row["total_transactions"]. "</td><td>" . $row["total_amount"]. "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
            break;

        case 9:
        $sql = "SELECT u.user_id, u.name, d.debit_limit FROM user_details u JOIN debit_details d ON u.user_id = d.user_id ORDER BY d.debit_limit DESC LIMIT 5";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<h2>Top 5 Users with the Highest Debit Limits</h2>";
            echo "<table>";
            echo "<tr><th>User ID</th><th>Name</th><th>Debit Limit</th></tr>";
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["user_id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["debit_limit"]. "</td></tr>";
            }
            echo "</table>";
        } else {
            echo "0 results";
        }
        break;

        case 10:
            $sql = "SELECT u.user_id, u.name, SUM(t.transaction_amount) AS total_spent FROM user_details u JOIN transactions_table t ON u.user_id = t.user_id GROUP BY u.user_id, u.name ORDER BY total_spent DESC";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo "<h2>Total Amount Spent by Each User (Ordered by Total Spent Amount)</h2>";
                echo "<table>";
                echo "<tr><th>User ID</th><th>Name</th><th>Total Spent</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["user_id"]. "</td><td>" . $row["name"]. "</td><td>" . $row["total_spent"]. "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "0 results";
            }
            break;
        default:
            echo "Invalid query";
    }

    $conn->close();
    ?>
</body>
</html>
