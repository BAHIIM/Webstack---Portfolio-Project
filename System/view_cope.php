<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Khdamti Jobs - View Attachment</title>
    <link rel="shortcut icon" href="images/ico/favicon.png">
    <link href="css/main.css" rel="stylesheet">
</head>
<body>
    <?php
    require 'constants/db_config.php';

    // Get file ID from query parameter
    $file_id = $_GET['id'];

    try {
        // Establish database connection
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("SELECT * FROM tbl_other_attachments WHERE id = :fileid");
        $stmt->bindParam(':fileid', $file_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Store certificate and title
            $certificate = $result['attachment'];
            $title = htmlspecialchars($result['title'], ENT_QUOTES, 'UTF-8'); // Secure the title for HTML output

            echo "<iframe style='border:none;' src='ViewerJS/?title=$title#data:application/pdf;base64," . base64_encode($certificate) . "' height='100%' width='100%'></iframe>";
        } else {
            echo "<p>Attachment not found.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>An error occurred while retrieving the attachment: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
    }
    ?>
</body>
</html>

