<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khdamti Jobs - View Certificate</title>
    <link rel="shortcut icon" href="images/ico/favicon.png">
    <link href="css/main.css" rel="stylesheet">
</head>
<body>

<?php
// Include database configuration
require 'constants/db_config.php';

// Get the file ID from the URL safely
$file_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($file_id) {
    try {
        // Establish a secure connection to the database
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare the SQL statement to fetch the certificate
        $stmt = $conn->prepare("SELECT certificate, title FROM tbl_professional_qualification WHERE id = :fileid");
        $stmt->bindParam(':fileid', $file_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch results
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the result is found, display the certificate
        if ($result) {
            $certificate = base64_encode($result['certificate']);
            $course = htmlspecialchars($result['title'], ENT_QUOTES, 'UTF-8');
            ?>
            <iframe style="border:none;" src="ViewerJS/?title=<?php echo $course; ?>#data:application/pdf;base64,<?php echo $certificate; ?>" height="100%" width="100%"></iframe>
            <?php
        } else {
            echo "<p>Certificate not found.</p>";
        }
    } catch (PDOException $e) {
        // Log the error message for debugging
        error_log("Database error: " . $e->getMessage());
        echo "<p>An error occurred while fetching the certificate. Please try again later.</p>";
    }
} else {
    echo "<p>Invalid request. No certificate ID provided.</p>";
}
?>

</body>
</html>
