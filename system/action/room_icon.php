<?php
/**
 * FuseChat
 *
 * @package FuseChat
 * @author www.nemra-1.com
 * @copyright 2020
 * @terms Unauthorized use of this script without a valid license is prohibited.
 * All content of FuseChat is the property of BoomCoding and cannot be used in another project.
 */
if ($f == 'room_icon') {
    if ($s == 'add_room_icon') {
        echo addRoomIcon();
        exit();
    }
}
if(isset($_POST['remove_icon'])){
	echo removeRoomIcon();
	die();
}
function addRoomIcon() {
    global $mysqli, $data;
    // Check if user has permission to edit room
    if (!canEditRoom()) {
        return boomCode(0);
    }
    // Validate and sanitize the room ID
    $room_id = escape($_POST['admin_add_icon'], true);
    $room = roomDetails($room_id);
    if (empty($room)) {
        return boomCode(0); // Room not found
    }
    // Increase memory limit if necessary
    ini_set('memory_limit', '128M');
    // Validate uploaded file
    if (fileError(4)) {
        return boomCode(1); // File upload error
    }
    // Handle the file and its extension
    $info = pathinfo($_FILES["file"]["name"]);
    $extension = strtolower($info['extension']); // Ensure extension is lowercase
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($extension, $allowed_extensions)) {
        return boomCode(1); // Invalid file extension
    }
    $file_tumb = "room_icon" . $room["room_id"] . "_" . time() . ".jpg";
    $file_icon = "temporary_room_icon_" . $room["room_id"] . "." . $extension;
    // Check if the current room icon is the default image, and don't delete it
    $default_image = 'default_images/rooms/default_room.svg';
    // Don't delete the default image if it's currently set as the room's icon
    if ($room['room_icon'] !== $default_image) {
        unlinkRoomIcon($file_icon); // Remove old file if it's not the default
    }
    // Process image if it's valid
    if (isImage($extension)) {
        $info = getimagesize($_FILES["file"]["tmp_name"]);
        if ($info !== false) {
            $width = $info[0];
            $height = $info[1];
            $type = $info['mime'];
            // Move the uploaded file to the temporary location
            boomMoveFile('upload/room_icon/' . $file_icon);
            // Path for the thumbnail
            $filepath = 'upload/room_icon/' . $file_tumb;
            $filesource = 'upload/room_icon/' . $file_icon;
            // Create the thumbnail
            $create = createTumbnail($filesource, $filepath, $type, $width, $height, 200, 200);
            // Validate file existence and image data
            if (sourceExist($filepath) && sourceExist($filesource)) {
                if (validImageData($filepath)) {
                    // Remove the original icon if it exists, unless it's the default image
                    if ($room['room_icon'] !== $default_image) {
                        unlinkRoomIcon($room['room_icon']);
                    }
                    // Update the database with the new room icon
                    $stmt = $mysqli->prepare("UPDATE boom_rooms SET room_icon = ? WHERE room_id = ?");
                    $stmt->bind_param("ss", $file_tumb, $room['room_id']);
                    $stmt->execute();
                    // Optional: Update the room cache (if needed)
                    // redisUpdateRoom($room['room_id']);
                    // Return the success code with the updated room icon
                    return boomCode(5, array('data' => myRoomIcon($file_tumb)));
                } else {
                    unlinkRoomIcon($file_icon);
                    return boomCode(7); // Invalid image data
                }
            } else {
                unlinkRoomIcon($file_icon);
                return boomCode(7); // File validation failed
            }
        } else {
            return boomCode(7); // Invalid image
        }
    } else {
        return boomCode(1); // Not an image
    }
}



function removeRoomIcon(){
    global $mysqli, $data;
    // Check if user has permission to edit room
    if (!canEditRoom()) {
        return boomCode(0);
    }
    // Fetch room details
    $room = roomDetails($data['user_roomid']);
    if (empty($room)) {
        return boomCode(0); // Room not found
    }
    $default_image = 'default_images/rooms/default_room.svg'; // Path of default image
    // Only remove the icon if it's not the default one
    if ($room['room_icon'] !== $default_image) {
        unlinkRoomIcon($room['room_icon']); // Remove the existing room icon
    }
    // Update the room icon to default
    $mysqli->query("UPDATE boom_rooms SET room_icon = 'default_room.png' WHERE room_id = '{$data['user_roomid']}'");
    // Optional: Update the room cache (if needed)
    //redisUpdateRoom($data['user_roomid']);

    // Return success code with updated room icon
    return boomCode(1, array('data' => myRoomIcon('default_room.png')));
}





// end of functions

//if (isset($_FILES["file"], $_POST['add_icon'])){
//	echo addRoomIcon();
//	die();
//}
//if (isset($_FILES["file"], $_POST['staff_add_icon'])){
//	echo staffAddRoomIcon();
//	die();
//}

//if(isset($_POST['staff_remove_icon'])){
//	echo staffRemoveRoomIcon();
//	die();
//}
//die();
?> 