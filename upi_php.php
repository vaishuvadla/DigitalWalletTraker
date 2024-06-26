<?php
$host = "127.0.0.1";
$user = "root";
$password = "";
$dbname = "upi_payments";

// Establish database connection
$con = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Get user ID from the form
$userId = $_POST['userId'];

// Queries to fetch user details
$queries = [
    "personal_info" => "SELECT name, email, phone_number, address, date_of_birth, gender, occupation FROM user_details WHERE user_id = $userId;",
    "transaction_count" => "SELECT COUNT(*) AS number_of_transactions FROM transactions_details WHERE user_id = $userId;",
    "max_expenditure" => "SELECT MAX(transaction_amount) AS max_expenditure FROM transactions_details WHERE user_id = $userId;",
    "total_income" => "SELECT SUM(salary + received_cash_from_other_sources) AS total_income FROM income_sources WHERE user_id = $userId;",
    "remaining_money" => "SELECT (SELECT SUM(salary + received_cash_from_other_sources) FROM income_sources WHERE user_id = $userId) - (SELECT SUM(transaction_amount) FROM transactions_details WHERE user_id = $userId) AS remaining_money;",
    "credit_details" => "SELECT * FROM credit_details WHERE user_id = $userId;",
    "debit_details" => "SELECT * FROM debit_details WHERE user_id = $userId;",
    "most_used_platform" => "SELECT platform_used, COUNT(*) AS transaction_count FROM transaction_methods_platform_details WHERE transaction_id IN (SELECT transaction_id FROM transactions_details WHERE user_id = $userId) GROUP BY platform_used ORDER BY transaction_count DESC LIMIT 1;",
    "most_common_transaction" => "SELECT transaction_location, DATE(transaction_time) AS transaction_date, TIME(transaction_time) AS transaction_time, COUNT(*) AS transaction_count FROM transactions_table WHERE user_id = $userId GROUP BY transaction_location, DATE(transaction_time), TIME(transaction_time) ORDER BY transaction_count DESC LIMIT 1;"
];

// Execute queries
$userDetails = [];
foreach ($queries as $key => $query) {
    $result = $con->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userDetails[$key] = $row;
    } else {
        $userDetails[$key] = "N/A";
    }
}

// Close database connection
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f2f2f2;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    width: 80%;
    background-color: white;
    border-radius: 5px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

.header {
    background-color: #3498db;
    color: white;
    padding: 20px;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    text-align: center;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
}

th {
    background-color: rgb(17,107,143);
    color: white;
    text-align: left;
}

tr:nth-child(even) {
    background-color: #f2f2f2; /* Light gray background for even rows */
}

tr:nth-child(odd) {
    background-color: white; /* White background for odd rows */
}

tr:hover {
    background-color: #ddd;
}

td:first-child {
    font-weight: bold;
    color: rgb(17,107,143); /* Blue color for first column */
}

td {
    color: #333;
    background-color: white; /* White background for data cells */
}

</style>
<body>
    <h1>User Details:</h1>
    <table border="1">
        <tr>
            <td>User ID:</td>
            <td><?php echo $userId; ?></td>
        </tr>
        <tr>
            <td>Name:</td>
            <td><?php echo $userDetails['personal_info']['name']; ?></td>
        </tr>
        <tr>
            <td>Email:</td>
            <td><?php echo $userDetails['personal_info']['email']; ?></td>
        </tr>
        <tr>
            <td>Phone Number:</td>
            <td><?php echo $userDetails['personal_info']['phone_number']; ?></td>
        </tr>
        <tr>
            <td>Address:</td>
            <td><?php echo $userDetails['personal_info']['address']; ?></td>
        </tr>
        <tr>
            <td>Date of Birth:</td>
            <td><?php echo $userDetails['personal_info']['date_of_birth']; ?></td>
        </tr>
        <tr>
            <td>Gender:</td>
            <td><?php echo $userDetails['personal_info']['gender']; ?></td>
        </tr>
        <tr>
            <td>Occupation:</td>
            <td><?php echo $userDetails['personal_info']['occupation']; ?></td>
        </tr>
        <tr>
            <td>Number of Transactions:</td>
            <td><?php echo $userDetails['transaction_count']['number_of_transactions']; ?></td>
        </tr>
        <tr>
            <td>Max Expenditure:</td>
            <td><?php echo $userDetails['max_expenditure']['max_expenditure']; ?></td>
        </tr>
        <tr>
            <td>Total Income:</td>
            <td><?php echo $userDetails['total_income']['total_income']; ?></td>
        </tr>
        <tr>
            <td>Remaining Money:</td>
            <td><?php echo $userDetails['remaining_money']['remaining_money']; ?></td>
        </tr>
        <tr>
            <td>Credit Details:</td>
            <td><?php echo $userDetails['credit_details']['credit_limit']; ?></td>
        </tr>
        <tr>
            <td>Debit Details:</td>
            <td><?php echo $userDetails['debit_details']['debit_limit']; ?></td>
        </tr>
        <tr>
            <td>Most Used Platform:</td>
            <td><?php echo $userDetails['most_used_platform']['platform_used']; ?></td>
        </tr>
        <tr>
            <td>Most Common Transaction:</td>
            <td><?php echo $userDetails['most_common_transaction']['transaction_location']; ?> (Date: <?php echo $userDetails['most_common_transaction']['transaction_date']; ?>, Time: <?php echo $userDetails['most_common_transaction']['transaction_time']; ?>)</td>
        </tr>
    </table>
</body>
</html>
