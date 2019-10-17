INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'catmain', 'yes', 0, 'all_cat_main', 'echo "<select name=\\"set[catmain]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''catmain'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''catmain'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["catmain"] = ($set["catmain"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'multcat', 'yes', 0, 'multi_category', 'echo "<select name=\\"set[multcat]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''multcat'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''multcat'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";\r\n$tm->outhint($lang[''multi_category_help'']);', '$set["multcat"] = ($set["multcat"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'iconcat', 'yes', 0, 'cat_icon', 'echo "<select name=\\"set[iconcat]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''iconcat'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''iconcat'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["iconcat"] = ($set["iconcat"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'letter', 'yes', 0, 'allow_letter', 'echo "<select name=\\"set[letter]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''letter'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''letter'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["letter"] = ($set["letter"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'catcol', '2', 1, 'who_col_cat', 'echo "<input type=\\"text\\" name=\\"set[catcol]\\" value=\\"".$conf[''{mod}''][''catcol'']."\\" size=\\"25\\" maxlength=\\"2\\" required=\\"required\\">";', '$set["catcol"] = preparse($set["catcol"],THIS_INT);');

INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'main', 'all', 0, 'home_view', 'echo "<select name=\\"set[main]\\" class=\\"sw165\\">".\r\n"<option value=\\"last\\"".(($conf[''{mod}''][''main'']=="last") ? " selected" : "").">".$lang[''public_last'']."</option>\\n".\r\n"<option value=\\"all\\"".(($conf[''{mod}''][''main'']=="all") ? " selected" : "").">".$lang[''all_all'']."</option>\\n".\r\n"</select>";', '$set["main"] = ($set["main"] == "all") ? "all" : "last";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'lastcol', '5', 0, 'who_page_main', 'echo "<input type=\\"text\\" name=\\"set[lastcol]\\" value=\\"".$conf[''{mod}''][''lastcol'']."\\" size=\\"25\\" maxlength=\\"2\\">";', '$set["lastcol"] = (!preg_match("/^(?:[1-9]\\d*)$/", $set["lastcol"])) ? 5 : $set["lastcol"];');

INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'linkcat', 'yes', 0, 'cat_link', 'echo "<select name=\\"set[linkcat]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''linkcat'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''linkcat'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["linkcat"] = ($set["linkcat"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'linkicon', 'yes', 0, 'cat_icon_link', 'echo "<select name=\\"set[linkicon]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''linkicon'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''linkicon'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["linkicon"] = ($set["linkicon"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'date', 'yes', 0, 'show_date', 'echo "<select name=\\"set[date]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''date'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''date'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["date"] = ($set["date"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'author', 'yes', 0, 'show_author', 'echo "<select name=\\"set[author]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''author'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''author'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["author"] = ($set["author"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'tags', 'yes', 0, 'allow_tags', 'echo "<select name=\\"set[tags]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''tags'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''tags'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["tags"] = ($set["tags"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'search', 'hide', 0, 'all_search', 'echo "<select name=\\"set[search]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''search'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''search'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"<option value=\\"hide\\"".(($conf[''{mod}''][''search'']=="hide") ? " selected" : "").">".$lang[''no_form'']."</option>\\n".\r\n"</select>";\r\n$tm->outhint($lang[''help_no_form'']);', 'if ($set["search"] == "yes") {\r\n$set["search"] = "yes";\r\n} elseif ($set["search"] == "no") {   \r\n$set["search"] = "no";\r\n} else { \r\n$set["search"] = "hide";\r\n}');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'print', 'yes', 0, 'all_print', 'echo "<select name=\\"set[print]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''print'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''print'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["print"] = ($set["print"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'yandex', 'yes', 0, '{mod}_yandex', 'echo "<select name=\\"set[yandex]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''yandex'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''yandex'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["yandex"] = ($set["yandex"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'pagcol', '10', 0, 'who_page_all', 'echo "<input type=\\"text\\" name=\\"set[pagcol]\\" value=\\"".$conf[''{mod}''][''pagcol'']."\\" size=\\"25\\" maxlength=\\"2\\">";', '$set["pagcol"] = (!preg_match("/^(?:[1-9]\\d*)$/", $set["pagcol"])) ? 10 : $set["pagcol"];');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'indcol', '1', 0, 'who_col_all', 'echo "<input type=\\"text\\" name=\\"set[indcol]\\" value=\\"".$conf[''{mod}''][''indcol'']."\\" size=\\"25\\" maxlength=\\"2\\">";', '$set["indcol"] = (!preg_match("/^(?:[1-9]\\d*)$/", $set["indcol"]) || $set["indcol"] > 5) ? 1 : $set["indcol"];');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'admin_mail', '', 0, 'moderator_mail', 'echo "<input type=\\"email\\" name=\\"set[admin_mail]\\" size=\\"25\\" value=\\"".$conf[''{mod}''][''admin_mail'']."\\">";\r\n$tm->outhint($lang[''help_moderator_mail'']);', '$set["admin_mail"] = ($set["admin_mail"] != "") ? $set["admin_mail"] : $conf["site_mail"];');

INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'addit', 'yes', 0, 'all_rul_add', 'echo "<select name=\\"set[addit]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''addit'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''addit'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["addit"] = ($set["addit"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'adduse', 'user', 0, 'who_add', 'echo "<select name=\\"set[adduse]\\" class=\\"group-sel sw165\\" id=\\"sel-add\\">".    \r\n     "<option value=\\"all\\"".(($conf[''{mod}''][''adduse'']=="all") ? " selected" : "").">".$lang[''all_all'']."</option>\\n";\r\nif (in_array(''user'', $realmod)) {\r\necho "<option value=\\"user\\"".(($conf[''{mod}''][''adduse'']=="user" && empty($conf[''{mod}''][''groups''])) ? " selected" : "").">".$lang[''all_user_only'']."</option>\\n";    \r\necho (($conf[''user''][''groupact''] == "yes") ? "<option value=\\"group\\"".(($conf[''{mod}''][''adduse''] == "user" && !empty($conf[''{mod}''][''groups'']))  ? " selected" : "").">".$lang[''all_groups_only'']."</option>\\n" : "");\r\necho "</select>\\n";\r\necho "<div class=\\"group\\" id=\\"div-add\\"".(($conf[''{mod}''][''adduse''] == "all" || $conf[''{mod}''][''adduse''] == "user" && empty($conf[''{mod}''][''groups''])) ? " style=\\"display: none;\\"" : "").">\\n";\r\nif ($conf[''user''][''groupact''] == "yes") {\r\n    $inqs = $db->query("SELECT * FROM ".$basepref."_user_group");\r\n    $group = Json::decode($conf[''{mod}''][''groups'']); \r\n    $group_out = "";\r\n    while ($items = $db->fetchrow($inqs)) {\r\n        $group_out.= "<input type=\\"checkbox\\" name=\\"group[".$items[''gid'']."]\\" value=\\"yes\\"".(isset($group[$items[''gid'']]) ? " checked" : "")."><span>".$items[''title'']."</span>,"; \r\n    }\r\n    echo chop($group_out,",");\r\n}\r\n}\r\necho "</div>\\n";', '$set[''groups''] = ($conf[''user''][''groupact''] == ''yes'' && $set[''adduse''] == ''group'' && is_array($group)) ? Json::encode($group) : '''';\r\n$set[''adduse''] = ($set[''adduse''] == ''user'' || $set[''adduse''] == ''group'') ? ''user'' : ''all'';');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'modadd', 'yes', 0, 'moderate_add', 'echo "<select name=\\"set[modadd]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''modadd'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''modadd'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["modadd"] = ($set["modadd"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'bigimage', 'yes', 0, 'allow_bigimage', 'echo "<select name=\\"set[bigimage]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''bigimage'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''bigimage'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["bigimage"] = ($set["bigimage"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'mailadd', 'yes', 0, 'all_mail_now', 'echo "<select name=\\"set[mailadd]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''mailadd'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''mailadd'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["mailadd"] = ($set["mailadd"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'addeditor', 'yes', 0, 'comment_editor_tags', 'echo "<select name=\\"set[addeditor]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''addeditor'']=="yes") ? " selected" : "").">".$lang[''included'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''addeditor'']=="no") ? " selected" : "").">".$lang[''not_included'']."</option>\\n".\r\n"</select>";', '$set["addeditor"] = ($set["addeditor"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'addtime', '10', 0, '{mod}_add_time', 'echo "<input type=\\"text\\" name=\\"set[addtime]\\" size=\\"25\\" value=\\"".$conf[''{mod}''][''addtime'']."\\" required=\\"required\\">";', '$set["addtime"] = (!preg_match("/^(?:[1-9]\\d*)$/", $set["addtime"])) ? 10 : $set["addtime"];');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'groups', '', 0, '', '', '');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'addfile', 'yes', 0, 'allow_files', 'echo "<select name=\\"set[addfile]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''addfile'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''addfile'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["addfile"] = ($set["addfile"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'extfile', 'zip,rar,xls,xlsx,doc,docx,pdf,rtf,txt', 1, 'mail_mime_type', 'echo "<input name=\\"set[extfile]\\" size=\\"25\\" type=\\"text\\" value=\\"".$conf[''{mod}''][''extfile'']."\\" required=\\"required\\">";\r\n$tm->outhint($lang[''help_ext_files'']);', '$set["extfile"] = ($set["extfile"]) ? preg_replace(''/,+/'', '','', preg_replace(''/[^a-z\\,]/'', '''', preg_replace(''/\\s+/'', '''', trim($set["extfile"], '','')))) : "zip,rar,xls,xlsx,doc,docx,pdf,rtf,txt";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'maxfile', '2048', 1, 'max_file', 'echo "<input name=\\"set[maxfile]\\" size=\\"25\\" maxlength=\\"25\\" type=\\"text\\" value=\\"".$conf[''{mod}''][''maxfile'']."\\" required=\\"required\\">";', '$set["maxfile"] = (!empty($set["maxfile"])) ? $set["maxfile"] : "2097152";');

INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'edit', 'yes', 0, 'all_ruledit', 'echo "<select name=\\"set[edit]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''edit'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''edit'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["edit"] = ($set["edit"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'whoedit', 'user', 0, 'all_whoedit', 'echo "<select name=\\"set[whoedit]\\" class=\\"group-sel sw165\\" id=\\"sel-edit\\">".    \r\n     "<option value=\\"user\\"".(($conf[''{mod}''][''whoedit'']=="user" && empty($conf[''{mod}''][''egroups''])) ? " selected" : "").">".$lang[''all_user_only'']."</option>\\n";    \r\necho (($conf[''user''][''groupact''] == "yes") ? "<option value=\\"egroup\\"".(($conf[''{mod}''][''whoedit''] == "user" && !empty($conf[''{mod}''][''egroups'']))  ? " selected" : "").">".$lang[''all_groups_only'']."</option>\\n" : "");\r\necho "</select>\\n";\r\necho "<div class=\\"group\\" id=\\"div-edit\\"".(($conf[''{mod}''][''whoedit''] == "all" || $conf[''{mod}''][''whoedit''] == "user" && empty($conf[''{mod}''][''egroups''])) ? " style=\\"display: none;\\"" : "").">\\n";\r\nif ($conf[''user''][''groupact''] == "yes") {\r\n    $einqs = $db->query("SELECT * FROM ".$basepref."_user_group");\r\n    $egroup = Json::decode($conf[''{mod}''][''egroups'']); \r\n    $egroup_out = "";\r\n    while ($eitems = $db->fetchrow($einqs)) {\r\n        $egroup_out.= "<input type=\\"checkbox\\" name=\\"egroup[".$eitems[''gid'']."]\\" value=\\"yes\\"".(isset($egroup[$eitems[''gid'']]) ? " checked" : "")."><span>".$eitems[''title'']."</span>,"; \r\n    }\r\n    echo chop($egroup_out,",");\r\n}\r\necho "</div>\\n";', '$set[''egroups''] = ($conf[''user''][''groupact''] == ''yes'' && $set[''whoedit''] == ''egroup'' && is_array($egroup)) ? Json::encode($egroup) : '''';\r\n$set[''whoedit''] = ($set[''whoedit''] == ''user'' || $set[''whoedit''] == ''egroup'') ? ''user'' : ''all'';');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'modedit', 'yes', 0, 'post_moderate', 'echo "<select name=\\"set[modedit]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''modedit'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''modedit'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["modedit"] = ($set["modedit"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'delete', 'yes', 0, 'all_ruldel', 'echo "<select name=\\"set[delete]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''delete'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''delete'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["delete"] = ($set["delete"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'whodel', 'user', 0, 'all_whodel', 'echo "<select name=\\"set[whodel]\\" class=\\"group-sel sw165\\" id=\\"sel-del\\">".    \r\n     "<option value=\\"user\\"".(($conf[''{mod}''][''whodel'']=="user" && empty($conf[''{mod}''][''dgroups''])) ? " selected" : "").">".$lang[''all_user_only'']."</option>\\n";    \r\necho (($conf[''user''][''groupact''] == "yes") ? "<option value=\\"dgroup\\"".(($conf[''{mod}''][''whodel''] == "user" && !empty($conf[''{mod}''][''dgroups'']))  ? " selected" : "").">".$lang[''all_groups_only'']."</option>\\n" : "");\r\necho "</select>\\n";\r\necho "<div class=\\"group\\" id=\\"div-del\\"".(($conf[''{mod}''][''whodel''] == "all" || $conf[''{mod}''][''whodel''] == "user" && empty($conf[''{mod}''][''dgroups''])) ? " style=\\"display: none;\\"" : "").">\\n";\r\nif ($conf[''user''][''groupact''] == "yes") {\r\n    $einqs = $db->query("SELECT * FROM ".$basepref."_user_group");\r\n    $dgroup = Json::decode($conf[''{mod}''][''dgroups'']); \r\n    $dgroup_out = "";\r\n    while ($eitems = $db->fetchrow($einqs)) {\r\n        $dgroup_out.= "<input type=\\"checkbox\\" name=\\"dgroup[".$eitems[''gid'']."]\\" value=\\"yes\\"".(isset($dgroup[$eitems[''gid'']]) ? " checked" : "")."><span>".$eitems[''title'']."</span>,"; \r\n    }\r\n    echo chop($dgroup_out,",");\r\n}\r\necho "</div>\\n";', '$set[''dgroups''] = ($conf[''user''][''groupact''] == ''yes'' && $set[''whodel''] == ''dgroup'' && is_array($dgroup)) ? Json::encode($dgroup) : '''';\r\n$set[''whodel''] = ($set[''whodel''] == ''user'' || $set[''whodel''] == ''dgroup'') ? ''user'' : ''all'';');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'egroups', '', 0, '', '', '');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'dgroups', '', 0, '', '', '');

INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'photo_add', 'yes', 0, 'photo_allow', 'echo "<select name=\\"set[photo_add]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''photo_add'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''photo_add'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["photo_add"] = ($set["photo_add"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'photo_who', 'user', 0, 'photo_who_add', 'echo "<select name=\\"set[photo_who]\\" class=\\"group-sel sw165\\" id=\\"sel-photo\\">".    \r\n     "<option value=\\"all\\"".(($conf[''{mod}''][''photo_who'']=="all") ? " selected" : "").">".$lang[''all_all'']."</option>\\n";\r\nif (in_array(''user'', $realmod)) {\r\necho "<option value=\\"user\\"".(($conf[''{mod}''][''photo_who'']=="user" && empty($conf[''{mod}''][''agroups''])) ? " selected" : "").">".$lang[''all_user_only'']."</option>\\n";    \r\necho (($conf[''user''][''groupact''] == "yes") ? "<option value=\\"agroup\\"".(($conf[''{mod}''][''photo_who''] == "user" && !empty($conf[''{mod}''][''agroups'']))  ? " selected" : "").">".$lang[''all_groups_only'']."</option>\\n" : "");\r\necho "</select>\\n";\r\necho "<div class=\\"group\\" id=\\"div-photo\\"".(($conf[''{mod}''][''photo_who''] == "all" || $conf[''{mod}''][''photo_who''] == "user" && empty($conf[''{mod}''][''agroups''])) ? " style=\\"display: none;\\"" : "").">\\n";\r\nif ($conf[''user''][''groupact''] == "yes") {\r\n    $inqs = $db->query("SELECT * FROM ".$basepref."_user_group");\r\n    $agroup = Json::decode($conf[''{mod}''][''agroups'']); \r\n    $agroup_out = "";\r\n    while ($items = $db->fetchrow($inqs)) {\r\n        $agroup_out.= "<input type=\\"checkbox\\" name=\\"agroup[".$items[''gid'']."]\\" value=\\"yes\\"".(isset($agroup[$items[''gid'']]) ? " checked" : "")."><span>".$items[''title'']."</span>,"; \r\n    }\r\n    echo chop($agroup_out,",");\r\n}\r\n}\r\necho "</div>\\n";', '$set[''agroups''] = ($conf[''user''][''groupact''] == ''yes'' && $set[''photo_who''] == ''agroup'' && is_array($agroup)) ? Json::encode($agroup) : '''';\r\n$set[''photo_who''] = ($set[''photo_who''] == ''user'' || $set[''photo_who''] == ''agroup'') ? ''user'' : ''all'';');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'photo_del', 'yes', 0, 'photo_allow_del', 'echo "<select name=\\"set[photo_del]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''photo_del'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''photo_del'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["photo_del"] = ($set["photo_del"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'photo_whodel', 'user', 0, 'photo_who_del', 'echo "<select name=\\"set[photo_whodel]\\" class=\\"group-sel sw165\\" id=\\"sel-delphoto\\">".    \r\n     "<option value=\\"user\\"".(($conf[''{mod}''][''photo_whodel'']=="user" && empty($conf[''{mod}''][''dagroups''])) ? " selected" : "").">".$lang[''all_user_only'']."</option>\\n";    \r\necho (($conf[''user''][''groupact''] == "yes") ? "<option value=\\"dagroup\\"".(($conf[''{mod}''][''photo_whodel''] == "user" && !empty($conf[''{mod}''][''dagroups'']))  ? " selected" : "").">".$lang[''all_groups_only'']."</option>\\n" : "");\r\necho "</select>\\n";\r\necho "<div class=\\"group\\" id=\\"div-delphoto\\"".(($conf[''{mod}''][''photo_whodel''] == "all" || $conf[''{mod}''][''photo_whodel''] == "user" && empty($conf[''{mod}''][''dagroups''])) ? " style=\\"display: none;\\"" : "").">\\n";\r\nif ($conf[''user''][''groupact''] == "yes") {\r\n    $einqs = $db->query("SELECT * FROM ".$basepref."_user_group");\r\n    $dagroup = Json::decode($conf[''{mod}''][''dagroups'']); \r\n    $dagroup_out = "";\r\n    while ($eitems = $db->fetchrow($einqs)) {\r\n        $dagroup_out.= "<input type=\\"checkbox\\" name=\\"dagroup[".$eitems[''gid'']."]\\" value=\\"yes\\"".(isset($dagroup[$eitems[''gid'']]) ? " checked" : "")."><span>".$eitems[''title'']."</span>,"; \r\n    }\r\n    echo chop($dagroup_out,",");\r\n}\r\necho "</div>\\n";', '$set[''dagroups''] = ($conf[''user''][''groupact''] == ''yes'' && $set[''photo_whodel''] == ''dagroup'' && is_array($dagroup)) ? Json::encode($dagroup) : '''';\r\n$set[''photo_whodel''] = ($set[''photo_whodel''] == ''user'' || $set[''photo_whodel''] == ''dagroup'') ? ''user'' : ''all'';');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'agroups', '', 0, '', '', '');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'dagroups', '', 0, '', '', '');

INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'video_add', 'yes', 0, 'video_allow', 'echo "<select name=\\"set[video_add]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''video_add'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''video_add'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["video_add"] = ($set["video_add"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'video_who', 'user', 0, 'video_who_add', 'echo "<select name=\\"set[video_who]\\" class=\\"group-sel sw165\\" id=\\"sel-video\\">".    \r\n     "<option value=\\"all\\"".(($conf[''{mod}''][''video_who'']=="all") ? " selected" : "").">".$lang[''all_all'']."</option>\\n";\r\nif (in_array(''user'', $realmod)) {\r\necho "<option value=\\"user\\"".(($conf[''{mod}''][''video_who'']=="user" && empty($conf[''{mod}''][''vgroups''])) ? " selected" : "").">".$lang[''all_user_only'']."</option>\\n";    \r\necho (($conf[''user''][''groupact''] == "yes") ? "<option value=\\"vgroup\\"".(($conf[''{mod}''][''video_who''] == "user" && !empty($conf[''{mod}''][''vgroups'']))  ? " selected" : "").">".$lang[''all_groups_only'']."</option>\\n" : "");\r\necho "</select>\\n";\r\necho "<div class=\\"group\\" id=\\"div-video\\"".(($conf[''{mod}''][''video_who''] == "all" || $conf[''{mod}''][''video_who''] == "user" && empty($conf[''{mod}''][''vgroups''])) ? " style=\\"display: none;\\"" : "").">\\n";\r\nif ($conf[''user''][''groupact''] == "yes") {\r\n    $inqs = $db->query("SELECT * FROM ".$basepref."_user_group");\r\n    $vgroup = Json::decode($conf[''{mod}''][''vgroups'']); \r\n    $vgroup_out = "";\r\n    while ($items = $db->fetchrow($inqs)) {\r\n        $vgroup_out.= "<input type=\\"checkbox\\" name=\\"vgroup[".$items[''gid'']."]\\" value=\\"yes\\"".(isset($vgroup[$items[''gid'']]) ? " checked" : "")."><span>".$items[''title'']."</span>,"; \r\n    }\r\n    echo chop($vgroup_out,",");\r\n}\r\n}\r\necho "</div>\\n";', '$set[''vgroups''] = ($conf[''user''][''groupact''] == ''yes'' && $set[''video_who''] == ''vgroup'' && is_array($vgroup)) ? Json::encode($vgroup) : '''';\r\n$set[''video_who''] = ($set[''video_who''] == ''user'' || $set[''video_who''] == ''vgroup'') ? ''user'' : ''all'';');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'video_del', 'yes', 0, 'video_allow_del', 'echo "<select name=\\"set[video_del]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''video_del'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''video_del'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["video_del"] = ($set["video_del"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'video_whodel', 'user', 0, 'video_who_del', 'echo "<select name=\\"set[video_whodel]\\" class=\\"group-sel sw165\\" id=\\"sel-delvideo\\">".    \r\n     "<option value=\\"user\\"".(($conf[''{mod}''][''video_whodel'']=="user" && empty($conf[''{mod}''][''dvgroups''])) ? " selected" : "").">".$lang[''all_user_only'']."</option>\\n";    \r\necho (($conf[''user''][''groupact''] == "yes") ? "<option value=\\"dvgroup\\"".(($conf[''{mod}''][''video_whodel''] == "user" && !empty($conf[''{mod}''][''dvgroups'']))  ? " selected" : "").">".$lang[''all_groups_only'']."</option>\\n" : "");\r\necho "</select>\\n";\r\necho "<div class=\\"group\\" id=\\"div-delvideo\\"".(($conf[''{mod}''][''video_whodel''] == "all" || $conf[''{mod}''][''video_whodel''] == "user" && empty($conf[''{mod}''][''dvgroups''])) ? " style=\\"display: none;\\"" : "").">\\n";\r\nif ($conf[''user''][''groupact''] == "yes") {\r\n    $einqs = $db->query("SELECT * FROM ".$basepref."_user_group");\r\n    $dvgroup = Json::decode($conf[''{mod}''][''dvgroups'']); \r\n    $dvgroup_out = "";\r\n    while ($eitems = $db->fetchrow($einqs)) {\r\n        $dvgroup_out.= "<input type=\\"checkbox\\" name=\\"dvgroup[".$eitems[''gid'']."]\\" value=\\"yes\\"".(isset($dvgroup[$eitems[''gid'']]) ? " checked" : "")."><span>".$eitems[''title'']."</span>,"; \r\n    }\r\n    echo chop($dvgroup_out,",");\r\n}\r\necho "</div>\\n";', '$set[''dvgroups''] = ($conf[''user''][''groupact''] == ''yes'' && $set[''video_whodel''] == ''dvgroup'' && is_array($dvgroup)) ? Json::encode($dvgroup) : '''';\r\n$set[''video_whodel''] = ($set[''video_whodel''] == ''user'' || $set[''video_whodel''] == ''dvgroup'') ? ''user'' : ''all'';');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'vgroups', '', 0, '', '', '');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'dvgroups', '', 0, '', '', '');

INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'resact', 'yes', 0, 'reviews_allow', 'echo "<select name=\\"set[resact]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''resact'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''resact'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["resact"] = ($set["resact"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'resadd', 'all', 0, 'who_add', 'echo "<select name=\\"set[resadd]\\" class=\\"sw165\\">".\r\n"<option value=\\"all\\"".(($conf[''{mod}''][''resadd'']=="all") ? " selected" : "").">".$lang[''all_all'']."</option>\\n";\r\necho "<option value=\\"user\\"".(($conf[''{mod}''][''resadd'']=="user") ? " selected" : "").">".$lang[''all_user_only'']."</option>\\n";\r\necho "</select>";', '$set["resadd"] = ($set["resadd"] == "user") ? "user" : "all";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'resmoder', 'yes', 0, 'response_moderate', 'echo "<select name=\\"set[resmoder]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''resmoder'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''resmoder'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["resmoder"] = ($set["resmoder"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'restime', '8', 0, 'reviews_time', 'echo "<input type=\\"text\\" name=\\"set[restime]\\" size=\\"25\\" maxlength=\\"10\\" value=\\"".$conf[''{mod}''][''restime'']."\\"> ";\r\n$tm->outhint($lang[''ratetime_help'']);', '$set["restime"] = preparse($set["restime"],THIS_INT);');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'respage', '10', 1, 'reviews_page', 'echo "<input type=\\"text\\" name=\\"set[respage]\\" size=\\"25\\" maxlength=\\"2\\" value=\\"".$conf[''{mod}''][''respage'']."\\" required=\\"required\\">";', '$set["respage"] = preparse($set["respage"],THIS_INT);');

INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'rec', 'yes', 0, 'all_rec', 'echo "<select name=\\"set[rec]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''rec'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''rec'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["rec"] = ($set["rec"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'lastrec', '5', 0, 'col_rec', 'echo "<input type=\\"text\\" name=\\"set[lastrec]\\" value=\\"".$conf[''{mod}''][''lastrec'']."\\" size=\\"25\\" maxlength=\\"2\\">";', '$set["lastrec"] = (!preg_match("/^(?:[1-9]\\d*)$/", $set["lastrec"])) ? 5 : $set["lastrec"];');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'rating', 'yes', 0, 'rul_rating', 'echo "<select name=\\"set[rating]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''rating'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''rating'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";', '$set["rating"] = ($set["rating"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'rateuse', 'all', 0, 'rating_user', 'echo "<select name=\\"set[rateuse]\\" class=\\"sw165\\">".\r\n"<option value=\\"all\\"".(($conf[''{mod}''][''rateuse'']=="all") ? " selected" : "").">".$lang[''all_all'']."</option>\\n".\r\n"<option value=\\"user\\"".(($conf[''{mod}''][''rateuse'']=="user") ? " selected" : "").">".$lang[''all_user_only'']."</option>\\n".\r\n"</select>";', '$set["rateuse"] = ($set["rateuse"] == "user") ? "user" : "all";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'ratetime', '86400', 0, 'rating_time', 'echo "<input type=\\"text\\" name=\\"set[ratetime]\\" size=\\"25\\" maxlength=\\"10\\" value=\\"".$conf[''{mod}''][''ratetime'']."\\"> ";\r\n$tm->outhint($lang[''ratetime_help'']);', '$set["ratetime"] = preparse($set["ratetime"],THIS_INT);');

INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'rss', 'yes', 0, 'rss_feed', 'echo "<select name=\\"set[rss]\\" class=\\"sw165\\">".\r\n"<option value=\\"yes\\"".(($conf[''{mod}''][''rss'']=="yes") ? " selected" : "").">".$lang[''all_yes'']."</option>\\n".\r\n"<option value=\\"no\\"".(($conf[''{mod}''][''rss'']=="no") ? " selected" : "").">".$lang[''all_no'']."</option>\\n".\r\n"</select>";echo "<a class=\\"pad\\" href=\\"".$conf[''site_url''].$ro->seo(''index.php?dn={mod}&amp;re=rss'')."\\" target=\\"_blank\\">".$conf[''site_url''].$ro->seo(''index.php?dn={mod}&amp;re=rss'')."</a>";', '$set["rss"] = ($set["rss"] == "yes") ? "yes" : "no";');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'rsskey', 'yandex', 0, 'rss_key', 'echo "<input type=\\"text\\" name=\\"set[rsskey]\\" size=\\"25\\" maxlength=\\"20\\" value=\\"".$conf[''{mod}''][''rsskey'']."\\" required=\\"required\\">";\r\necho "<a class=\\"pad\\" href=\\"".$conf[''site_url''].$ro->seo("index.php?dn={mod}&amp;re=rss&amp;ya=".$conf[''{mod}''][''rsskey''])."\\" target=\\"_blank\\">".$conf[''site_url''].$ro->seo("index.php?dn={mod}&amp;re=rss&amp;ya=".$conf[''{mod}''][''rsskey''])."</a>";', '');
INSERT INTO {pref}_settings VALUES (NULL, '{mod}', 'rsslast', '5', 1, 'rss_last', 'echo "<input type=\\"text\\" name=\\"set[rsslast]\\" value=\\"".$conf[''{mod}''][''rsslast'']."\\" size=\\"25\\" maxlength=\\"2\\" required=\\"required\\">";', '$set["rsslast"] = preparse($set["rsslast"],THIS_INT);');