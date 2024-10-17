<?php
// Kiểm tra xem form có được submit và có file được tải lên hay không
if (isset($_POST["submit"]) && isset($_FILES["fileToUpload"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra xem file có phải là hình ảnh không
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        echo "File is an image - " . $check["mime"] . ".<br>";
        $uploadOk = 1;
    } else {
        echo "File is not an image.<br>";
        $uploadOk = 0;
    }

    // Kiểm tra nếu file đã tồn tại
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.<br>";
        $uploadOk = 0;
    }

    // Kiểm tra kích thước file (giới hạn là 500KB)
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.<br>";
        $uploadOk = 0;
    }

    // Chỉ cho phép các định dạng tệp nhất định (JPG, JPEG, PNG, GIF)
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
        $uploadOk = 0;
    }

    // Kiểm tra nếu có lỗi trong quá trình tải lên
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.<br>";
    } else {
        // Cố gắng tải file lên thư mục đích
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.<br>";

            // Thông tin kết nối cơ sở dữ liệu
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "qlbanhang";

            // Kết nối đến cơ sở dữ liệu
            $conn = new mysqli($servername, $username, $password, $dbname);

            // Kiểm tra kết nối
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Lấy ID từ cookie
            if (isset($_COOKIE['id'])) {
                $id = $_COOKIE['id'];

                // Cập nhật tên ảnh vào cơ sở dữ liệu
                $sql = "UPDATE customers SET img_profile = '" . $conn->real_escape_string($_FILES["fileToUpload"]["name"]) . "' WHERE ID='" . $conn->real_escape_string($id) . "'";

                // Thực thi truy vấn
                if ($conn->query($sql) === TRUE) {
                    echo 'Cập nhật cơ sở dữ liệu thành công!<br>';
                    echo '<a href="homepage.php">Trang chủ</a>';
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "ID không được xác định trong cookie.<br>";
            }

            // Đóng kết nối
            $conn->close();
        } else {
            echo "Sorry, there was an error uploading your file.<br>";
        }
    }
} else {
    echo "Không có file nào được tải lên hoặc form chưa được gửi.<br>";
}
?>