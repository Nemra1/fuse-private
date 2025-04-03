<?php
require __DIR__ . "./../config_session.php";
// Check if the file is uploaded and there is a self post
if (isset($_FILES["file"]) && isset($_POST["self"])) {
    echo processCover();
    exit;
}
// Check if the file is uploaded and there is a target post
if (isset($_FILES["file"]) && isset($_POST["target"])) {
    echo staffAddCover();
    exit;
}
// Check if delete cover is requested
if (isset($_POST["delete_cover"])) {
    $reset = resetCover($data);
    exit;
}
// Check if remove cover is requested
if (isset($_POST["remove_cover"])) {
    echo staffRemoveCover();
}
function processCover() {
    global $data, $cody, $mysqli;
    // Check if the user has permission to upload a cover
    if (!canCover()) {
        return boomCode(1);
    }
    // Set memory limit for larger file processing
    ini_set("memory_limit", "128M");
    // Get file information
    $file = $_FILES["file"];
    $fileName = basename($file["name"]);
    $fileTmpName = $file["tmp_name"];
    $fileSize = $file["size"];
    $fileError = $file["error"];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    // Check for file upload errors
    if ($fileError !== UPLOAD_ERR_OK) {
        return boomCode(7); // Error code for file upload error
    }
    // Validate the file size (e.g., limit to 5MB)
    $maxFileSize = 5 * 1024 * 1024; // 5 MB
    if ($fileSize > $maxFileSize) {
        return boomCode(7); // Error code for file size exceeded
    }
    // Validate the file type (MIME type and extension)
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($fileExtension, $allowedExtensions) || !in_array(mime_content_type($fileTmpName), $allowedMimeTypes)) {
        return boomCode(7); // Error code for invalid file type
    }
    // Generate a unique file name to prevent overwriting
    $uniqueFileName = uniqid('cover_', true) . '.' . $fileExtension;
    $filePath = 'cover/' . $uniqueFileName;
    // Move the uploaded file to the target directory
    if (!move_uploaded_file($fileTmpName, $filePath)) {
        return boomCode(7); // Error code for failed file move
    }
    // Generate the thumbnail file name
    $fileThumbName = uniqid('cover_tumb_', true) . '.jpg'; // Use JPG for thumbnails
    $fileThumbPath = 'cover/' . $fileThumbName;
    // Create a thumbnail for the image
    if ($fileExtension === 'gif' && canGifCover()) {
        // Handle GIF image creation
        $create = imageTumbGif($filePath, $fileThumbPath, mime_content_type($fileTmpName), 500);
    } else {
        // Handle other image types (JPG, PNG)
        $create = imageTumb($filePath, $fileThumbPath, mime_content_type($fileTmpName), 500);
    }
    // Check if the file and thumbnail were created successfully
    if (sourceExist($filePath) && sourceExist($fileThumbPath)) {
        // Delete the old cover image if exists
        unlinkCover($data["user_cover"]);
        // Update the database with the new cover
        $stmt = $mysqli->prepare("UPDATE boom_users SET user_cover = ? WHERE user_id = ?");
        $stmt->bind_param("si", $fileThumbName, $data["user_id"]);
        $stmt->execute();
        return boomCode(5, ["data" => myCover($fileThumbName)]);
    }
    // Fallback: If creation of the thumbnail or file failed, clean up and return an error
    if (sourceExist($filePath)) {
        unlinkCover($filePath); // Delete the file if the process failed
    }
    return boomCode(7); // Error code for failed processing
}


function staffAddCover(){
    global $data, $cody, $mysqli;
    // Sanitize and validate the target user
    $target = escape($_POST["target"]);
    $user = userDetails($target);
    // Check if the current user has permission to modify the target user's cover
    if (!canModifyCover($user)) {
        return boomCode(1);
    }
    // Set memory limit for larger file processing
    ini_set("memory_limit", "128M");
    // Check if a file is uploaded
    if (!isset($_FILES["file"]) || $_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
        return boomCode(7); // Error code for file upload issue
    }
    // Get the file information
    $file = $_FILES["file"];
    $fileName = basename($file["name"]);
    $fileTmpName = $file["tmp_name"];
    $fileSize = $file["size"];
    $fileError = $file["error"];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    // Validate file extension and MIME type
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($fileExtension, $allowedExtensions) || !in_array(mime_content_type($fileTmpName), $allowedMimeTypes)) {
        return boomCode(7); // Invalid file type
    }
    // Validate file size (e.g., max size 5MB)
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    if ($fileSize > $maxFileSize) {
        return boomCode(7); // File size exceeds the limit
    }
    // Generate a unique filename to avoid overwriting
    $uniqueFileName = uniqid('cover_', true) . '.' . $fileExtension;
    $filePath = 'cover/' . $uniqueFileName;
    // Move the uploaded file to the cover directory
    if (!move_uploaded_file($fileTmpName, $filePath)) {
        return boomCode(7); // Failed to move uploaded file
    }
    // Generate a unique filename for the thumbnail
    $fileThumbName = uniqid('cover_tumb_', true) . '.jpg'; // Thumbnails are saved as JPG
    $fileThumbPath = 'cover/' . $fileThumbName;
    // Create a thumbnail for the image
    $imginfo = getimagesize($fileTmpName);
    if ($imginfo !== false) {
        $type = $imginfo["mime"];
        // Create thumbnail based on image type (handle GIF separately)
        if ($fileExtension === 'gif' && canGifCover()) {
            $create = imageTumbGif($filePath, $fileThumbPath, $type, 500);
        } else {
            $create = imageTumb($filePath, $fileThumbPath, $type, 500);
        }
        // Check if both the original file and the thumbnail exist
        if (sourceExist($filePath) && sourceExist($fileThumbPath)) {
            // Delete old cover image and thumbnail if they exist
            unlinkCover($user["user_cover"]);
            // Update the user cover in the database
            $stmt = $mysqli->prepare("UPDATE boom_users SET user_cover = ? WHERE user_id = ?");
            $stmt->bind_param("si", $fileThumbName, $user["user_id"]);
            $stmt->execute();
            return boomCode(5, ["data" => myCover($fileThumbName)]);
        }
        // Fallback if the thumbnail creation fails
        if (sourceExist($filePath)) {
            unlinkCover($filePath); // Delete the uploaded file if creation failed
        }
        return boomCode(7); // Failed to create thumbnail or upload file
    }
    return boomCode(1); // Invalid image file
}


function staffRemoveCover(){
	global $data,$cody, $mysqli;
    $target = escape($_POST["remove_cover"]);
    $user = userDetails($target);
    if (!canModifyCover($user)) {
        return 0;
    }
    resetCover($user);
    boomConsole("remove_cover", ["target" => $user["user_id"]]);
    return 1;
}

?>