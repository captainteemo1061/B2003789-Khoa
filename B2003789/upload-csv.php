<!DOCTYPE html>
<html>
<head>
    <title>Upload CSV</title>
</head>
<body>
    <h1>Upload CSV File</h1>
    <form action="upload-csv-processing.php" method="post" enctype="multipart/form-data">
        <input type="file" name="csvFile" id="csvFile" required>
        <input type="submit" value="Upload CSV" name="submit">
    </form>
</body>
</html>