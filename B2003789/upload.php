<?php
// Kiểm tra xem form đã gửi và có file được tải lên hay chưa
if (isset($_POST["submit"]) && isset($_FILES["fileToUpload"])) {
    $target_dir = "uploads/";
    
    // Kiểm tra nếu thư mục không tồn tại thì tạo mới
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Kiểm tra xem file có phải là hình ảnh không
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if ($check !== false) {
        echo "File là hình ảnh - " . $check["mime"] . ".<br>";
        $uploadOk = 1;
    } else {
        echo "File không phải là hình ảnh.<br>";
        $uploadOk = 0;
    }

    // Kiểm tra xem file đã tồn tại chưa
    if (file_exists($target_file)) {
        echo "Xin lỗi, file đã tồn tại.<br>";
        $uploadOk = 0;
    }

    // Kiểm tra kích thước file
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Xin lỗi, file của bạn quá lớn.<br>";
        $uploadOk = 0;
    }

    // Chỉ cho phép các định dạng file nhất định
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Xin lỗi, chỉ các file JPG, JPEG, PNG & GIF được cho phép.<br>";
        $uploadOk = 0;
    }

    // Kiểm tra nếu $uploadOk bằng 0 do lỗi
    if ($uploadOk == 0) {
        echo "Xin lỗi, file của bạn không được tải lên.<br>";
    } else {
        // Cố gắng tải file lên
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "File " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " đã được tải lên.<br>";
        } else {
            echo "Xin lỗi, đã có lỗi xảy ra khi tải file của bạn lên.<br>";
        }
    }
} else {
    echo "Không có file nào được tải lên hoặc form chưa được gửi.<br>";
}
?>