<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload SQL File</title>
</head>
<body>
    <h1>Upload SQL File</h1>
    <form action="/upload-and-convert" method="POST" enctype="multipart/form-data">
        @csrf <!-- นี่คือ CSRF Token -->
        <input type="file" name="sql_file">
        <button type="submit">Upload & Convert</button>
    </form>
</body>
</html>