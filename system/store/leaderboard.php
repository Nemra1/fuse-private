<?php
require('../config_session.php');

$leader_list = '';

function quizLeader($add, $rank){
    global $lang,$data;
    $add_me = '';
    $frame = '';
	// Handle frame and avatar styling
	if($data['use_frame']==1){
		$safe_frame = htmlspecialchars($add['photo_frame'] ?? '', ENT_QUOTES, 'UTF-8');
		$allowed_ext = [ 'gif', 'jpg', 'jpeg', 'png', 'bmp', 'webp', 'svg', ];
		$frame_ext = strtolower(pathinfo($safe_frame, PATHINFO_EXTENSION));
		// Validate the image format
		if (in_array($frame_ext, $allowed_ext)) {
			$frame = 'system/store/frames/' . $safe_frame;
		}	
	}
	
	return '<div class="bp-top-users__row bp-top-users__row_1" onclick="getProfile(' . $add["user_id"] . ')">
		<div class="bp-top-users__place bp-top-users__place_gold">
			'. showRanksEmojy($rank) .'
		</div>
		<div class="bp-top-users__user ">
			' . getRankIcon($add, 'list_rank') . '
			<img src="' . myavatar($add['user_tumb']) . '" class="bp-top-users__user-img"  data="' . $add['user_id'] . '" style="background-image: url(' . $frame . ');"/>
			<div class="bp-top-users__user-data">
				<div class="bp-top-users__user-name ' . myColorFont($add) . '">' . $add["user_name"] . '</div>
			</div>
		</div>
		<div class="bp-top-users__lvl bp-top-users__lvl_premium ' . showRankColors($rank) . '">
			' . $add['user_level'] . '
		</div>
	</div>';

}


$get_leader = $mysqli->query("SELECT * FROM boom_users WHERE user_level > 0 AND user_bot = 0 ORDER BY user_level DESC, user_rank DESC LIMIT 50");
if ($get_leader->num_rows > 0) {
    $rank = 1;
    while ($add = $get_leader->fetch_assoc()) {
        $leader_list .= quizLeader($add, $rank);
        $rank++;
    }
} else {
    $leader_list .= emptyZone($lang['no_data']);
}


function showRankColors($icon)
{
    switch ($icon) {
        case 1:
            return 'backgrad1';
        case 2:
            return 'border2';
        case 3:
            return 'border3';
        default:
            return 'border4';
    }
}
function showRanksEmojy($icon)
{
    switch ($icon) {
        case 1:
            return '1';
        case 2:
            return '2';
        case 3:
            return '3';
        default:
            return '🔥';
    }
}
function showRanksIcon($icon)
{
    switch ($icon) {
        case 1:
            return 'Chat King';
        case 2:
            return 'Member of royal family';
        case 3:
            return 'One of the chat princes';
        default:
            return 'Classy';
    }
}
?>
<style>
.bp-top-users__body { gap: 10px; }
.bp-top-users__row { width: 100%; height: 50px; }
.bp-top-users__row { position: relative; display: flex; align-items: center; background-color: #0f1223; border: 1px solid #1e2339; }
.bp-top-users__place { padding-right: 5px; clip-path: polygon(0 0, 100% 0, 85% 100%, 0 100%); width: 46px; font-size: 22px; }
.bp-top-users__place { display: flex; align-items: center; justify-content: center; height: 100%; margin-right: 10px; flex: none; background: linear-gradient(180deg, rgba(65, 73, 107, 0), rgba(65, 73, 107, .5)); font-family: Druk Text Cyr; font-style: italic; color: #b7bcdb; }
.bp-top-users__user { display: flex; align-items: center; transition: .2s ease; text-decoration: none; gap:0 12px; }
.bp-top-users__user-img { width: 45px; height: 45px; background-size: contain; background-repeat: no-repeat; padding: 7px; border-radius: 100%; }
.bp-top-users__user-data { display: flex; align-items: center; gap: 4px; margin-right: 6px; }
.bp-top-users__user-name { text-overflow: ellipsis; overflow: hidden; white-space: nowrap; transition: .2s ease; font-size: 14px; max-width: 100px; }
.bp-top-users__lvl_premium { color: #fdd08d; }
.bp-top-users__lvl { width: 45px; height: 45px; font-size: 18px; display: flex; align-items: center; justify-content: center; margin-right: 4px; margin-left: auto; flex: none; background-image: url(system/store/level_background.gif); background-repeat: no-repeat; background-size: 100%; background-position: 50%; font-family: Druk Text Cyr; font-style: italic; border-radius: 50%; }
.logo-container { display: flex; justify-content: center; align-items: center; height: 100%; position: relative; }
.leader_logo { max-width: 100%; height: auto; max-height: 143px; width: 100%; }
.centered-text { font-size: 24px !important; text-align: center; position: absolute; color: #ffcc33; width: 100%; height: 100%; top: 0; background: #f12711; background: -webkit-linear-gradient(to right, #f5af19, #f12711); background: linear-gradient(to right, #fb832e4f, #0f1223); }
.top_ldb { position: absolute; left: 0; right: 0; top: 60%; font-size: x-large !important; font-weight: bolder; }
</style>
<div class="bp-top-users__body">
    <div class="logo-container">
        <img src="system/store/logo.gif" alt="Logo" class="leader_logo">
		 <div class="centered-text bcolor1 bnfont15" title="Top LeaderBoard"><div class="top_ldb bnfont15 bgif20">Top LeaderBoard</div></div>
    </div>

<?php echo $leader_list; ?>
</div>
