<?php
// Include your database connection file
include '../db_connect.php';

// Check if section parameter is set
if (isset($_GET['section'])) {
    // Sanitize the input
    $section = mysqli_real_escape_string($conn, $_GET['section']);

    // Query to fetch batch IDs based on section
    $query = "SELECT id FROM batch_list WHERE LEFT(bname, 1) = '$section'";

    // Perform the query
    $result = mysqli_query($conn, $query);

    // Check if query was successful
    if ($result) {
        $batch_ids = array();

        // Fetch batch IDs and store them in an array
        while ($row = mysqli_fetch_assoc($result)) {
            $batch_ids[] = $row['id'];
        }

        // Return the batch IDs as JSON
        echo json_encode($batch_ids);
    } else {
        // Handle query error
        echo json_encode(array('error' => 'Failed to fetch batch IDs'));
    }
} else {
    // Handle missing section parameter
    echo json_encode(array('error' => 'Section parameter is missing'));
}
