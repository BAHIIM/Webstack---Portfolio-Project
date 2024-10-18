<!DOCTYPE html>
<html lang="en">

<?php
// Required files
require 'constants/settings.php';
require 'constants/check-login.php';
require 'constants/db_config.php';

// Validate company reference ID
if (!isset($_GET['ref'])) {
    header("location:./");
    exit();
}

$company_id = $_GET['ref'];

// Fetch company details using PDO
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM tbl_users WHERE member_no = :memberno AND role = 'employer'");
    $stmt->bindParam(':memberno', $company_id, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        header("location:./");
        exit();
    }

    // Extract company details
    $compname = $result['first_name'];
    $compesta = $result['byear'];
    $compmail = $result['email'];
    $comptype = $result['title'];
    $compphone = $result['phone'];
    $compcity = $result['city'];
    $compstreet = $result['street'];
    $compzip = $result['zip'];
    $compcountry = $result['country'];
    $compbout = $result['about'];
    $complogo = $result['avatar'];
    $compserv = $result['services'];
    $compexp = $result['expertise'];
    $compweb = $result['website'];
    $comppeopl = $result['people'];
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Handle pagination
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$items_per_page = 5;
$start_index = ($page - 1) * $items_per_page;
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Khdamti Jobs - <?php echo htmlspecialchars($compname); ?></title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <!-- Navigation bar and branding -->
    </header>

    <div class="container">
        <div class="company-details">
            <h2><?php echo htmlspecialchars($compname); ?></h2>
            <p><?php echo htmlspecialchars("$compstreet, $compcity, $compzip, $compcountry"); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($compphone); ?></p>
            <p><strong>Website:</strong> <a href="https://<?php echo htmlspecialchars($compweb); ?>" target="_blank"><?php echo htmlspecialchars($compweb); ?></a></p>
            <h3>Company Overview</h3>
            <p><?php echo nl2br(htmlspecialchars($compbout)); ?></p>
            <h4>Services</h4>
            <p><?php echo nl2br(htmlspecialchars($compserv)); ?></p>
            <h4>Expertise</h4>
            <p><?php echo nl2br(htmlspecialchars($compexp)); ?></p>
        </div>

        <div class="job-listings">
            <h4>Jobs at <?php echo htmlspecialchars($compname); ?></h4>
            <?php
            try {
                $stmt = $conn->prepare("SELECT * FROM tbl_jobs WHERE company = :compid ORDER BY enc_id DESC LIMIT :start, :limit");
                $stmt->bindParam(':compid', $company_id, PDO::PARAM_STR);
                $stmt->bindParam(':start', $start_index, PDO::PARAM_INT);
                $stmt->bindParam(':limit', $items_per_page, PDO::PARAM_INT);
                $stmt->execute();

                $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($jobs as $job) {
                    echo "<div class='job-item'>";
                    echo "<h5>" . htmlspecialchars($job['title']) . "</h5>";
                    echo "<p>" . htmlspecialchars($job['description']) . "</p>";
                    echo "<p><strong>Location:</strong> " . htmlspecialchars($job['city']) . ", " . htmlspecialchars($job['country']) . "</p>";
                    echo "<p><strong>Experience:</strong> " . htmlspecialchars($job['experience']) . " years</p>";
                    echo "<p><strong>Deadline:</strong> " . htmlspecialchars($job['closing_date']) . "</p>";
                    echo "</div>";
                }
            } catch (PDOException $e) {
                echo "Error fetching jobs: " . $e->getMessage();
            }
            ?>
        </div>

        <!-- Pagination logic -->
        <div class="pagination">
            <?php
            // Calculate total pages
            $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_jobs WHERE company = :compid");
            $stmt->bindParam(':compid', $company_id, PDO::PARAM_STR);
            $stmt->execute();
            $total_jobs = $stmt->fetchColumn();
            $total_pages = ceil($total_jobs / $items_per_page);

            for ($i = 1; $i <= $total_pages; $i++) {
                echo "<a href='company.php?ref=" . urlencode($company_id) . "&page=$i'>" . $i . "</a> ";
            }
            ?>
        </div>
    </div>

    <footer>
        <!-- Footer content -->
    </footer>

    <script src="js/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>

</html>
