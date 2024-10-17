<?php
// Thay đổi các thông tin cơ sở dữ liệu của bạn
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qlbanhang";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý upload khi form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['csvFile'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["csvFile"]["name"]);
    $uploadOk = 1;
    $csvFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra loại tệp
    if ($csvFileType != "csv") {
        echo "Chỉ cho phép các tệp CSV.";
        $uploadOk = 0;
    }

    // Kiểm tra lỗi khi tải lên
    if ($_FILES["csvFile"]["error"] != UPLOAD_ERR_OK) {
        echo "Lỗi tải lên: " . $_FILES["csvFile"]["error"];
        $uploadOk = 0;
    }

    // Kiểm tra loại MIME của tệp
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES["csvFile"]["tmp_name"]);
    finfo_close($finfo);

    if ($mimeType != "text/csv" && $mimeType != "application/csv" && $mimeType != "application/vnd.ms-excel") {
        echo "Tệp không phải là CSV.";
        $uploadOk = 0;
    }

    // Nếu mọi kiểm tra đều ok, di chuyển file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["csvFile"]["tmp_name"], $target_file)) {
            echo "Tệp " . htmlspecialchars(basename($_FILES["csvFile"]["name"])) . " đã được tải lên thành công.";

            // Đọc dữ liệu từ file CSV
            $lines = file($target_file, FILE_IGNORE_NEW_LINES);

            // Xóa dữ liệu cũ trước khi chèn dữ liệu mới (tuỳ chọn)
            $conn->query("TRUNCATE TABLE customers");

            foreach ($lines as $key => $value) {
                // Bỏ qua dòng tiêu đề
                if ($key === 0) continue;

                $data = str_getcsv($value);

                // Chuẩn bị câu lệnh SQL
                $stmt = $conn->prepare("INSERT INTO customers (ID, Name, Email, Phone, Address) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $data[0], $data[1], $data[2], $data[3], $data[4]);

                // Thực hiện câu lệnh SQL
                if ($stmt->execute()) {
                    echo "Dòng dữ liệu ID " . $data[0] . " đã được chèn thành công.<br>";
                } else {
                    echo "Lỗi chèn dữ liệu ID " . $data[0] . ": " . $stmt->error . "<br>";
                }
            }

            // Đóng kết nối
            $stmt->close();

            // Hiển thị thông tin bảng sau khi chèn dữ liệu
            $result = $conn->query("SELECT * FROM customers");

            if ($result->num_rows > 0) {
                echo "<h2>Danh sách khách hàng:</h2>";
                echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th></tr>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row["ID"] . "</td><td>" . $row["Name"] . "</td><td>" . $row["Email"] . "</td><td>" . $row["Phone"] . "</td><td>" . $row["Address"] . "</td></tr>";
                }
                echo "</table>";
            } else {
                echo "Không có dữ liệu trong bảng.";
            }

            $conn->close();
        } else {
            echo "Có lỗi xảy ra khi tải lên tệp.";
        }
    }
}
?>