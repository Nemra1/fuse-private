<?php
require('../config_session.php');

$delay = getDelay();
$online_friend = '';
$offline_friend = '';
$friends = 0;

// Fetch friends where the status is '3' and sort them by user_name
$query = "
    SELECT boom_users.*, boom_friends.* 
    FROM boom_users
    JOIN boom_friends ON boom_friends.target = boom_users.user_id 
    WHERE boom_friends.hunter = '{$data['user_id']}' 
    AND boom_friends.fstatus = '3'
    ORDER BY boom_users.user_rank DESC
";

$find_friend = $mysqli->query($query);

if ($find_friend->num_rows > 0) {                
    while ($find = $find_friend->fetch_assoc()) {
        $friends++;
        if ($find['last_action'] > $delay) {
            $online_friend .= createUserList($find, 0);
        } else {
            $offline_friend .= createUserList($find, 0);
        }
    }
}

// Concatenate online and offline friends
$glob_friend = $online_friend . $offline_friend;
?>

<?php if ($glob_friend == ''): ?>
<div class="boom_keep" id="container_friends">
    <?php echo emptyZone($lang['no_friend']); ?>
</div>
<?php else: ?>
<div class="pad10" id="friend_search_box">
    <div class="search_bar">
        <input id="search_friend" placeholder="💖" class="full_input" type="text"/>
        <div class="clear"></div>
    </div>
</div>
<div class="boom_keep" id="container_friends">
    <?php if ($online_friend != ''): ?>
    <div class="online_user"><?php echo $online_friend; ?></div>
    <?php endif; ?>
    <?php if ($offline_friend != ''): ?>
    <div class="user_count">
        <div class="bcell">
            <?php echo $lang['offline']; ?>
        </div>
    </div>
    <div class="online_user"><?php echo $offline_friend; ?></div>
    <?php endif; ?>
</div>
<?php endif; ?>
