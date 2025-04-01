<?php


require __DIR__ . "/../../../../config_session.php";
if (!boomAllow(100)) {
    exit;
}
echo elementTitle($lang["email_settings"]);
echo "<div class=\"page_full\">\r\n\t<div class=\"page_element\">\r\n\t\t<div class=\"boom_form\">\r\n\t\t\t<div class=\"setting_element \">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["mail_type"];
echo "</p>\r\n\t\t\t\t<select id=\"set_mail_type\">\r\n\t\t\t\t\t<option ";
echo selCurrent($data["mail_type"], "mail");
echo " value=\"mail\">mail</option>\r\n\t\t\t\t\t<option ";
echo selCurrent($data["mail_type"], "smtp");
echo " value=\"smtp\">smtp</option>\r\n\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element\">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["site_email"];
echo "</p>\r\n\t\t\t\t<input id=\"set_site_email\" class=\"full_input\" value=\"";
echo $data["site_email"];
echo "\" type=\"text\"/>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element\">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["email_from"];
echo "</p>\r\n\t\t\t\t<input id=\"set_email_from\" class=\"full_input\" value=\"";
echo $data["email_from"];
echo "\" type=\"text\"/>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element\">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["smtp_host"];
echo "</p>\r\n\t\t\t\t<input id=\"set_smtp_host\" class=\"full_input\" value=\"";
echo $data["smtp_host"];
echo "\" type=\"text\"/>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element\">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["smtp_username"];
echo "</p>\r\n\t\t\t\t<input id=\"set_smtp_username\" class=\"full_input\" value=\"";
echo $data["smtp_username"];
echo "\" type=\"text\"/>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element\">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["smtp_password"];
echo "</p>\r\n\t\t\t\t<input id=\"set_smtp_password\" type=\"password\" class=\"full_input\" value=\"";
echo $data["smtp_password"];
echo "\" type=\"text\"/>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element\">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["smtp_port"];
echo "</p>\r\n\t\t\t\t<input id=\"set_smtp_port\" class=\"full_input\" value=\"";
echo $data["smtp_port"];
echo "\" type=\"text\"/>\r\n\t\t\t</div>\r\n\t\t\t<div class=\"setting_element \">\r\n\t\t\t\t<p class=\"label\">";
echo $lang["smtp_encryption"];
echo "</p>\r\n\t\t\t\t<select id=\"set_smtp_type\">\r\n\t\t\t\t\t<option ";
echo selCurrent($data["smtp_type"], "tls");
echo " value=\"tls\">tls</option>\r\n\t\t\t\t\t<option ";
echo selCurrent($data["smtp_type"], "ssl");
echo " value=\"ssl\">ssl</option>\r\n\t\t\t\t</select>\r\n\t\t\t</div>\r\n\t\t</div>\r\n\t\t<button data=\"email\" type=\"button\" class=\"save_admin reg_button theme_btn\"><i class=\"ri-save-line\"></i> ";
echo $lang["save"];
echo "</button>\r\n\t\t<button type=\"button\" onclick=\"openTestMail();\" class=\"reg_button default_btn\"><i class=\"ri-mail-send-fill-o\"></i> ";
echo $lang["test"];
echo "</button>\r\n\t</div>\r\n</div>";

?>