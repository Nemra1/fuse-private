<?php
/**
 * Codychat
 *
 * @package Codychat
 * @author www.boomcoding.com
 * @copyright 2020
 * @terms any use of this script without a legal license is prohibited
 * all the content of Codychat is the propriety of BoomCoding and cannot be 
 * used for another project.
 */
require_once('../config_session.php');

$news_content = '';
$news_count = 0;

$query = "
    SELECT boom_news.*, boom_users.*,
        (SELECT COUNT(id) FROM boom_news) AS news_count,
        (SELECT COUNT(parent_id) FROM boom_news_reply WHERE parent_id = boom_news.id) AS reply_count,
        (SELECT like_type FROM boom_news_like WHERE uid = '{$data['user_id']}' AND like_post = boom_news.id) AS liked
    FROM boom_news
    JOIN boom_users ON boom_news.news_poster = boom_users.user_id
    ORDER BY news_date DESC
    LIMIT 10
";

$get_news = $mysqli->query($query);

if ($get_news->num_rows > 0) {
    while ($news = $get_news->fetch_assoc()) {
        $news_count = $news['news_count'];
        $news_content .= boomTemplate('element/news', $news);
    }
    $mysqli->query("UPDATE boom_users SET user_news = '" . time() . "' WHERE user_id = '{$data['user_id']}'");
} else {
    $news_content .= emptyZone($lang['no_news']);
}

mysqli_close($mysqli);
?>
<div class="boom_keep">
    <div class="pad20">
        <?php if (canPostNews()) { ?>
        <div class="vpad10">
            <div class="add_post_container">
                <div id="add_wall_form">
                    <div class="post_input_container">
                        <textarea
                            onkeyup="textArea(this, 60);"
                            id="news_data"
                            maxlength="3000"
                            spellcheck="false"
                            placeholder="<?php echo $lang['start_new_post']; ?>"
                            class="full_textarea"
                        ></textarea>
                        <div id="post_file_data" class="pad10 main_post_data hidden" data-key=""></div>
                    </div>
                    <div class="main_post_control">
                        <div class="main_post_item">
                            <i class="ri-image-circle-line"></i>
                            <input id="news_file" onchange="uploadNews();" type="file"/>
                        </div>
                        <div class="main_post_button">
                            <button onclick="sendNews();" class="small_button rounded_button theme_btn">
                                <i class="ri-send-plane-fill"></i> <?php echo $lang['post']; ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <div id="container_news">
            <?php echo $news_content; ?>
        </div>
        <?php if ($news_count > 10) { ?>
        <div class="load_more_news centered_element">
            <button onclick="moreNews(this);" class="theme_btn small_button rounded_button load_more">
                <i class="ri-add-circle-fill"></i> <?php echo $lang['load_more']; ?>
            </button>
        </div>
        <?php } ?>
    </div>
</div>
