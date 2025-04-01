<?php

require __DIR__ . "../../../config_session.php";


if (!boomAllow(70)) {
    exit;
}

echo elementTitle($lang["filter"]);
?>

<div class="page_full">
    <div>
        <div class="tab_menu">
            <ul>
                <li class="tab_menu_item tab_selected" data="filter_tab" data-z="word_filter">
                    <?php echo htmlspecialchars($lang["bad_word_filter"], ENT_QUOTES, 'UTF-8'); ?>
                </li>
                <li class="tab_menu_item" data="filter_tab" data-z="spam_filter">
                    Spam
                </li>
                <?php if (boomAllow(90)) : ?>
                    <li class="tab_menu_item" data="filter_tab" data-z="username_filter">
                        <?php echo htmlspecialchars($lang["username"], ENT_QUOTES, 'UTF-8'); ?>
                    </li>
                    <li class="tab_menu_item" data="filter_tab" data-z="email_filter">
                        <?php echo htmlspecialchars($lang["email"], ENT_QUOTES, 'UTF-8'); ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div id="filter_tab">
        <!-- Word Filter Tab -->
        <div id="word_filter" class="tab_zone">
            <div class="page_element">
                <?php if (boomAllow(90)) : ?>
                    <div class="setting_element">
                        <p class="label"><?php echo htmlspecialchars($lang["do_action"], ENT_QUOTES, 'UTF-8'); ?></p>
                        <select id="set_word_action" onchange="checkWordFilter(); setWordAction();">
                            <option value="0" <?php echo $data["word_action"] == 0 ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($lang["action_none"], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <option value="2" <?php echo $data["word_action"] == 2 ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($lang["mute"], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <option value="3" <?php echo $data["word_action"] == 3 ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($lang["kick"], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        </select>
                    </div>

                    <div id="word_action_delay" class="setting_element <?php echo hideFilters($data["word_action"], [2, 3]); ?>">
                        <p class="label"><?php echo htmlspecialchars($lang["duration"], ENT_QUOTES, 'UTF-8'); ?></p>
                        <select id="set_word_delay" onchange="setWordAction();">
                            <?php echo optionMinutes($data["word_delay"], [1, 2, 5, 10, 15, 30, 60]); ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="setting_element">
                    <p class="label"><?php echo htmlspecialchars($lang["add_word"], ENT_QUOTES, 'UTF-8'); ?></p>
                    <input id="word_add" class="full_input"/>
                </div>

                <div class="tpad5">
                    <button id="add_word" onclick="addWord('word', 'badword_list', 'word_add');" type="button" class="reg_button theme_btn">
                        <i class="ri-save-3-fill"></i> <?php echo htmlspecialchars($lang["add"], ENT_QUOTES, 'UTF-8'); ?>
                    </button>
                </div>
            </div>

            <div class="page_element">
                <div id="badword_list">
                    <?php echo listFilter("word"); ?>
                </div>
            </div>
        </div>

        <!-- Spam Filter Tab -->
        <div id="spam_filter" class="tab_zone hide_zone">
            <div class="page_element">
                <?php if (boomAllow(90)) : ?>
                    <div class="setting_element">
                        <p class="label"><?php echo htmlspecialchars($lang["do_action"], ENT_QUOTES, 'UTF-8'); ?></p>
                        <select id="set_spam_action" onchange="checkSpamFilter(); setSpamAction();">
                            <option value="0" <?php echo $data["spam_action"] == 0 ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($lang["action_none"], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <option value="1" <?php echo $data["spam_action"] == 1 ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($lang["mute"], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <option value="2" <?php echo $data["spam_action"] == 2 ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($lang["ban"], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <option value="3" <?php echo $data["spam_action"] == 3 ? "selected" : ""; ?>>
                                <?php echo htmlspecialchars($lang["ghost"], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            
                        </select>
                    </div>

                    <div id="spam_action_delay" class="setting_element <?php echo hideFilters($data["spam_action"], [1]); ?>">
                        <p class="label"><?php echo htmlspecialchars($lang["duration"], ENT_QUOTES, 'UTF-8'); ?></p>
                        <select id="set_spam_delay" onchange="setSpamAction();">
                            <?php echo optionMinutes($data["spam_delay"], [5, 10, 15, 30, 60, 180, 1440, 10080]); ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="setting_element">
                    <p class="label"><?php echo htmlspecialchars($lang["add_word"], ENT_QUOTES, 'UTF-8'); ?></p>
                    <input id="spam_add" class="full_input"/>
                </div>

                <div class="tpad5">
                    <button id="add_spam" onclick="addWord('spam', 'spam_list', 'spam_add');" type="button" class="reg_button theme_btn">
                        <i class="ri-save-3-fill"></i> <?php echo htmlspecialchars($lang["add"], ENT_QUOTES, 'UTF-8'); ?>
                    </button>
                </div>
            </div>

            <div class="page_element">
                <div id="spam_list">
                    <?php echo listFilter("spam"); ?>
                </div>
            </div>
        </div>

        <!-- Username Filter Tab -->
        <?php if (boomAllow(90)) : ?>
            <div id="username_filter" class="tab_zone hide_zone">
                <div class="page_element">
                    <div class="boom_form">
                        <div class="setting_element">
                            <p class="label"><?php echo htmlspecialchars($lang["add_word"], ENT_QUOTES, 'UTF-8'); ?></p>
                            <input id="username_add" class="full_input"/>
                        </div>
                    </div>

                    <div class="tpad5">
                        <button id="add_username_filter" onclick="addWord('username', 'name_list', 'username_add');" type="button" class="reg_button theme_btn">
                            <i class="ri-save-3-fill"></i> <?php echo htmlspecialchars($lang["add"], ENT_QUOTES, 'UTF-8'); ?>
                        </button>
                    </div>
                </div>

                <div class="page_element">
                    <div id="name_list">
                        <?php echo listFilter("username"); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Email Filter Tab -->
        <?php if (boomAllow(90)) : ?>
            <div id="email_filter" class="tab_zone hide_zone">
                <div class="page_element">
                    <div class="boom_form">
                        <div class="setting_element">
                            <p class="label"><?php echo htmlspecialchars($lang["email_filter"], ENT_QUOTES, 'UTF-8'); ?></p>
                            <select id="set_email_filter" onchange="setEmailFilter();">
                                <?php echo yesNo($data["email_filter"]); ?>
                            </select>
                        </div>

                        <div class="setting_element">
                            <p class="label"><?php echo htmlspecialchars($lang["add_word"], ENT_QUOTES, 'UTF-8'); ?></p>
                            <input id="email_add" class="full_input"/>
                        </div>
                    </div>

                    <div class="tpad5">
                        <button id="add_email_filter" onclick="addWord('email', 'email_list', 'email_add');" type="button" class="reg_button theme_btn">
                            <i class="ri-save-3-fill"></i> <?php echo htmlspecialchars($lang["add"], ENT_QUOTES, 'UTF-8'); ?>
                        </button>
                    </div>
                </div>

                <div class="page_element">
                    <div id="email_list">
                        <?php echo listFilter("email"); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php

/**
 * Generates the HTML for the filter list based on the type.
 *
 * @param string $type The type of filter (e.g., 'word', 'spam', 'username', 'email').
 * @return string HTML of the filter list.
 */
function listFilter($type)
{
    global $data, $mysqli, $lang;

    $list_word = "";
    $query = $mysqli->prepare("SELECT * FROM boom_filter WHERE word_type = ? ORDER BY word ASC");
    $query->bind_param('s', $type);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        while ($word = $result->fetch_assoc()) {
            $list_word .= boomTemplate("element/word", $word);
        }
    } else {
        $list_word .= emptyZone($lang["empty"]);
    }

    $query->close();
    return $list_word;
}

/**
 * Determines the visibility of filter settings based on the action value.
 *
 * @param int $val The action value.
 * @param array $val2 Array of values that should be hidden.
 * @return string Class name for visibility.
 */
function hideFilters($val, $val2)
{
    return !in_array($val, $val2) ? "hidden" : "";
}

?>
