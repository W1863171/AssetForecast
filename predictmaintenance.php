<?php
// Check if the timeframe is sent via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['timeframe'])) {
    // Get the selected timeframe from the POST data
    $timeframe = $_POST['timeframe'];

    // Construct the command to invoke the Python script
    // Pass the selected timeframe as an argument to the Python script
    $command = '../AssetForecast/python ../AssetForecast/predictmaintenance.py ' . escapeshellarg($timeframe);

    // Start output buffering
    ob_start();

    // Execute the command and capture the output
    $output = shell_exec($command);

    // Check if the Python script execution completed
    if (strpos($output, 'Python script execution completed.') !== false) {
        // Python script execution completed
        // Send a message to JavaScript
        echo "Prediction completed."; // Send success message to JavaScript
    } else {
        // Python script execution did not complete successfully
        // Handle error or incomplete execution
        echo "Prediction failed."; // Send failure message to JavaScript
    }

    // Flush the output buffer and send the response to JavaScript
    ob_end_flush();
}
?>
