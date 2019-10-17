<?php
/**
 * File:        /admin/mod/firms/index.php
 *
 * @package     Danneo Basis kernel
 * @version     Danneo CMS (Next) v1.5.4
 * @copyright   (c) 2005-2017 Danneo Team
 * @link        http://danneo.ru
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Базовые константы
 */
define('READCALL', 1);
define('PERMISS', basename(__DIR__));

/**
 * Инициализация ядра
 */
require_once __DIR__.'/../../init.php';

/**
 * Авторизация
 */
if ($ADMIN_AUTH == 1 AND $sess['hash'] == $ops)
{
	global $ADMIN_ID, $CHECK_ADMIN, $AJAX, $db, $basepref, $tm, $conf, $wysiwyg, $modname, $lang, $sess, $ops, $cache, $a;

	if ( ! isset($modname[PERMISS]))
	{
		redirect(ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash']);
	}

	$template['breadcrumb'] = array
		(
			'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
			'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
			$modname[PERMISS]
		);

	/**
	 *  Список разрешенных админов
	 */
	if ($ADMIN_PERM == 1 OR in_array($ADMIN_ID, $CHECK_ADMIN['admid']))
	{
		/**
		 * Массив доступных $_REQUEST['dn']
		 */
		$legaltodo = array
			(
				'index', 'optsave', 'cat', 'catadd',
				'catedit', 'catdel', 'cataddsave', 'catup', 'cateditsave',
				'list', 'work', 'arrdel', 'arrmove', 'arract', 'arracc',
				'add', 'addsave', 'edit', 'editsave', 'act', 'up', 'del',
				'new', 'newadd', 'newdel', 'newarrdel', 'post', 'postarract', 'newclear', 'newwork', 'workarrdel',
				'reviews', 'newreviews', 'firmreviews', 'reviewedit', 'revieweditsave', 'reviewdel', 'reviewwork', 'reviewarrdel', 'reviewarract',
				'photo', 'photoaddsave', 'photoedit', 'photoeditsave', 'photoact', 'photodel', 'workphoto', 'photoarrdel', 'photoarract',
				'video', 'videoaddsave', 'videoedit', 'videoeditsave', 'videoact', 'videodel', 'workvideo', 'videoarrdel', 'videoarract',
				'ajaxedittitle', 'ajaxsavetitle', 'ajaxeditcat', 'ajaxsavecat', 'ajaxeditdate', 'ajaxsavedate', 'autocomplete',
				'tag', 'tagsetsave', 'tagedit', 'tageditsave', 'tagaddsave', 'tagdel', 'getcat', 'thumb', 'view'
			);

		/**
		 * Проверка $_REQUEST['dn']
		 */
		$_REQUEST['dn'] = (isset($_REQUEST['dn']) AND in_array(preparse_dn($_REQUEST['dn']), $legaltodo)) ? preparse_dn($_REQUEST['dn']) : 'index';

		/**
		 * Доп. функции мода
		 */
		include('mod.function.php');

		/**
		 * Массив сортировок для категорий
		 */
		$catsort = array
			(
				'posit'  => $lang['all_posit'],
				'public' => $lang['all_data'],
				'newsid' => $lang['all_id'],
				'title'  => $lang['all_name'],
				'hits'   => $lang['all_hits']
			);

		/**
		 * Массив литер
		 */
		$letter = letters();

		/**
		 * Меню
		 */
		function this_menu()
		{
			global $a, $conf, $db, $basepref, $tm, $sess, $lang, $pid, $AJAX;

			$link = '<a'.cho('index').' href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['all_set'].'</a>'
					.'<a'.cho('list').' href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>'
					.'<a'.cho('cat').' href="index.php?dn=cat&amp;ops='.$sess['hash'].'">'.$lang['all_cat'].'</a>'
					.'<a'.cho('catadd').' href="index.php?dn=catadd&amp;ops='.$sess['hash'].'">'.$lang['all_add_cat'].'</a>'
					.'<a'.cho('add').' href="index.php?dn=add&amp;ops='.$sess['hash'].'">'.$lang['firms_add'].'</a>';

			if (isset($conf[PERMISS]['add']) AND $conf[PERMISS]['add'] == 'yes')
			{
				$newcount = $db->fetchassoc($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_".PERMISS."_user"));
				$newcount = ($newcount['total'] > 0) ? ' ('.$newcount['total'].')' : '';
				$link.= '<a'.cho('new, newadd').' href="index.php?dn=new&amp;ops='.$sess['hash'].'">'.$lang['all_new_firms'].$newcount.'</a>';

				if ($conf[PERMISS]['edit'] == 'yes')
				{
					$postcount = $db->fetchassoc($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_".PERMISS." WHERE pid = '1'"));
					$postcount = ($postcount['total'] > 0) ? ' ('.$postcount['total'].')' : '';
					$link.= '<a'.cho('post, postarract').((isset($pid) AND $pid == 1) ? ' class="current"' : '').' href="index.php?dn=post&amp;ops='.$sess['hash'].'">'.$lang['post_moderate'].$postcount.'</a>';
				}
			}

			if (isset($conf[PERMISS]['resact']) AND $conf[PERMISS]['resact'] == 'yes')
			{
				$r = $db->fetchassoc($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_reviews WHERE file = '".PERMISS."' AND active = '1'"));
				$rnew = ($r['total'] > 0) ? ' ('.$r['total'].')' : '';
				if ($AJAX) {
					$link.= '<a class="all-comments" href="index.php?dn=reviews&amp;ajax=1&amp;ops='.$sess['hash'].'">'.$lang['review_all'].$rnew.'</a>';
				} else {
					$link.= '<a href="index.php?dn=reviews&amp;ajax=0&amp;ops='.$sess['hash'].'">'.$lang['review_all'].$rnew.'</a>';
				}
			}

			if (isset($conf[PERMISS]['resmoder']) AND $conf[PERMISS]['resmoder'] == 'yes')
			{
				$c = $db->fetchassoc($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_reviews WHERE file = '".PERMISS."' AND active = '0'"));
				$cnew = ($c['total'] > 0) ? ' ('.$c['total'].')' : '';
				if ($AJAX) {
					$link.= '<a class="all-comments" href="index.php?dn=newreviews&amp;ajax=1&amp;ops='.$sess['hash'].'">'.$lang['reviews_new'].$cnew.'</a>';
				} else {
					$link.= '<a href="index.php?dn=newreviews&amp;ajax=0&amp;ops='.$sess['hash'].'">'.$lang['reviews_new'].'&nbsp; &#8260; &nbsp;'.$c['total'].$cnew.'</a>';
				}
			}

			if ($conf[PERMISS]['tags'] == 'yes')
			{
				$link.= '<a'.cho('tag, tagedit').' href="index.php?dn=tag&amp;ops='.$sess['hash'].'">'.$lang['all_tags'].'</a>';
			}

			$filter = null;
			if (cho('list')) {
				$filter = '<a'.cho('list', 1).' href="#" onclick="$(\'#filter\').slideToggle();" title="'.$lang['search_in_section'].'">'.$lang['all_filter'].'</a>';
			}

			$tm->this_menu($link, $filter);
		}

		/**
		 * Вывод меню
		 */
		this_menu();

		/**
		 * Настройки
		 --------------*/
		if ($_REQUEST['dn'] == 'index')
		{
			global $ro, $realmod;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					$lang['all_set']
				);

			$tm->header();

			require_once(WORKDIR.'/core/classes/Router.php');
			$ro = new Router();

			echo "	<script>
					$(function(){
						$('#sel-add').bind('change', function() {
							if ($(this).val() == 'group') {
								$('#div-add').slideDown();
							} else {
								$('#div-add').slideUp();
							}
						});
						$('#sel-edit').bind('change', function() {
							if ($(this).val() == 'egroup') {
								$('#div-edit').slideDown();
							} else {
								$('#div-edit').slideUp();
							}
						});
						$('#sel-del').bind('change', function() {
							if ($(this).val() == 'dgroup') {
								$('#div-del').slideDown();
							} else {
								$('#div-del').slideUp();
							}
						});
						$('#sel-photo').bind('change', function() {
							if ($(this).val() == 'agroup') {
								$('#div-photo').slideDown();
							} else {
								$('#div-photo').slideUp();
							}
						});
						$('#sel-delphoto').bind('change', function() {
							if ($(this).val() == 'dagroup') {
								$('#div-delphoto').slideDown();
							} else {
								$('#div-delphoto').slideUp();
							}
						});
						$('#sel-video').bind('change', function() {
							if ($(this).val() == 'vgroup') {
								$('#div-video').slideDown();
							} else {
								$('#div-video').slideUp();
							}
						});
						$('#sel-delvideo').bind('change', function() {
							if ($(this).val() == 'dvgroup') {
								$('#div-delvideo').slideDown();
							} else {
								$('#div-delvideo').slideUp();
							}
						});
					});
					</script>";

			echo '	<div class="section">
					<form action="index.php" method="post">
					<table class="work">
						<caption>'.$lang[PERMISS].': '.$lang['all_set'].'</caption>';
			$inqset = $db->query("SELECT * FROM ".$basepref."_settings WHERE setopt = '".PERMISS."'");
			while ($itemset = $db->fetchassoc($inqset))
			{
				if ( ! empty($itemset['setcode']))
				{
					echo	in_array($itemset['setname'], array('linkcat', 'main', 'addit', 'photo_add', 'video_add', 'edit', 'resact', 'rec', 'rating', 'video', 'rss')) ? '<tr><th colspan="2"></th></tr>' : '';
					echo '	<tr>
								<td class="first">'.(($itemset['setmark'] == 1) ? '<span>*</span> ' : '').((isset($lang[$itemset['setlang']])) ? $lang[$itemset['setlang']] : $itemset['setlang']).'</td>
								<td>';
					echo eval($itemset['setcode']);
					echo '		</td>
							</tr>';
				}
			}
			echo '		<tr class="tfoot">
							<td colspan="2">
								<input type="hidden" name="dn" value="optsave" />
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input class="main-button" value="'.$lang['all_save'].'" type="submit" />
							</td>
						</tr>
					</table>
					</form>
					</div>';

			$tm->footer();
		}

		/**
		 * Настройки (сохранение)
		 ---------------------------*/
		if ($_REQUEST['dn'] == 'optsave')
		{
			global $set, $cache;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					$lang['all_set']
				);

			$inq = $db->query("SELECT * FROM ".$basepref."_settings WHERE setopt = '".PERMISS."'");

			while ($item = $db->fetchassoc($inq))
			{
				if (isset($set[$item['setname']]))
				{
					if ($item['setmark'] == 1 AND preparse($set[$item['setname']], THIS_EMPTY) == 1)
					{
						$tm->header();
						$tm->error($modname[PERMISS], $lang['all_save'].' '.$lang['all_set'], $lang['forgot_name']);
						$tm->footer();
					}
					if (preparse($item['setvalid'], THIS_EMPTY) == 0)
					{
						@eval($item['setvalid']);
					}
					$db->query("UPDATE ".$basepref."_settings SET setval = '".$db->escape(preparse($set[$item['setname']], THIS_TRIM))."' WHERE setid = '".$item['setid']."'");
				}
			}
			$cache->cachesave(1);

			redirect('index.php?dn=index&amp;ops='.$sess['hash']);
		}

		/**
		 * Категории
		 --------------*/
		if ($_REQUEST['dn'] == 'cat')
		{
			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					$lang['all_cat']
				);

			$tm->header();

			echo '	<div class="section">
					<form action="index.php" method="post">
					<table id="list" class="work">
						<caption>'.$lang[PERMISS].': '.$lang['all_cat'].'</caption>
						<tr>
							<th>ID</th>
							<th>'.$lang['all_name'].'</th>
							<th>'.$lang['all_cat_access'].'</th>
							<th>'.$lang['all_col'].'</th>
							<th>'.$lang['all_icon'].'</th>
							<th>'.$lang['all_posit'].'</th>
							<th>'.$lang['sys_manage'].'</th>
						</tr>';
			$inquiry = $db->query("SELECT * FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");
			$catcache = array();
			while ($item = $db->fetchassoc($inquiry))
			{
				$catcache[$item['parentid']][$item['catid']] = $item;
			}
			print_cat(0, 0);
			echo '		<tr class="tfoot">
							<td colspan="7">
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input type="hidden" name="dn" value="catup" />
								<input class="main-button" value="'.$lang['all_save'].'" type="submit" />
							</td>
						</tr>
					</table>
					</form>
					</div>';
			$tm->footer();
		}

		/**
		 * Категории (сохранение позиций)
		 ----------------------------------*/
		if ($_REQUEST['dn'] == 'catup')
		{
			global $posit;

			if (preparse($posit, THIS_ARRAY) == 1)
			{
				this_catup($posit, PERMISS);
			}

			redirect('index.php?dn=cat&amp;ops='.$sess['hash']);
		}

		/**
		 * Добавить категорию
		 ----------------------*/
		if ($_REQUEST['dn'] == 'catadd')
		{
			global $catid, $selective;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=cat&amp;ops='.$sess['hash'].'">'.$lang['all_cat'].'</a>',
					$lang['all_add']
				);

			$tm->header();

			$catcache = array();
			$inquiry = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");
			while ($item = $db->fetchassoc($inquiry))
			{
				$catcache[$item['parentid']][$item['catid']] = $item;
			}

			this_selectcat(0);

			echo '	<div class="section">
					<form action="index.php" method="post">
					<table class="work">
						<caption>'.$lang[PERMISS].': '.$lang['all_add_cat'].'</caption>
						<tr>
							<td class="first"><span>*</span> '.$lang['all_name'].'</td>
							<td><input type="text" name="catname" id="catname" size="70" required="required" /></td>
						</tr>
						<tr>
							<td>'.$lang['sub_title'].'</td>
							<td><input type="text" name="subtitle" size="70" /> <span class="light">&lt;h2&gt;</span></td>
						</tr>';
			if($conf['cpu'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['all_cpu'].'</td>
							<td>
								<input type="text" name="cpu" id="cpu" size="70" />';
								$tm->outtranslit('catname', 'cpu', $lang['cpu_int_hint']);
				echo '		</td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['custom_title'].'</td>
							<td><input type="text" name="catcustom" size="70" /> <span class="light">&lt;title&gt;</span></td>
						</tr>
						<tr>
							<td>'.$lang['all_descript'].'</td>
							<td><input type="text" name="descript" size="70" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_keywords'].'</td>
							<td>
								<input type="text" name="keywords" size="70" />';
								$tm->outhint($lang['keyword_hint']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_in_cat'].'</td>
							<td>
								<select name="catid" class="sw250">
									<option value="0">'.$lang['all_cat_new'].'</option>
									'.$selective.'
								</select>
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_sorting'].'</td>
							<td>
								<select name="sort" class="sw165">';
			foreach ($catsort as $k => $v) {
				echo '				<option value="'.$k.'">'.$v.'</option>';
            }
			echo '				</select> &nbsp;&#247;&nbsp;
								<select name="ord" class="sw150">
									<option value="desc">'.$lang['all_desc'].'</option>
									<option value="asc">'.$lang['all_acs'].'</option>
								</select>
							</td>
						</tr>';
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['all_cat_access'].'</td>
							<td>
								<select class="group-sel sw165" name="acc" id="acc">
									<option value="all">'.$lang['all_all'].'</option>
									<option value="user">'.$lang['all_user_only'].'</option>';
				echo '				'.((isset($conf['user']['groupact']) AND $conf['user']['groupact'] == 'yes') ? '<option value="group">'.$lang['all_groups_only'].'</option>' : '');
				echo '			</select>
								<div id="group" class="group" style="display: none;">';
				if (isset($conf['user']['groupact']) AND $conf['user']['groupact'] == 'yes')
				{
					$inqs = $db->query("SELECT * FROM ".$basepref."_user_group");
					$group_out = '';
					while ($items = $db->fetchassoc($inqs)) {
						$group_out.= '<input type="checkbox" name="group['.$items['gid'].']" value="yes" /><span>'.$items['title'].'</span>,';
					}
					echo chop($group_out, ',');
				}
				echo '			</div>
							</td>
						</tr>';
			}
			echo '		<tr>
							<td>RSS</td>
							<td>
								<select name="rss" class="sw165">
									<option value="yes">'.$lang['all_yes'].'</option>
									<option value="no">'.$lang['all_no'].'</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_icon'].'</td>
							<td>
								<input name="icon" id="icon" size="47" type="text" />&nbsp;
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/icon/\',\'&amp;field[1]=icon\')" value="'.$lang['filebrowser'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_decs'].'</td>
							<td>';
								$tm->textarea('descr', 5, 50, '', 1);
			echo '			</td>
						</tr>
						<tr class="tfoot">
							<td colspan="2">';
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'no')
			{
				echo '			<input type="hidden" name="acc" value="all" />';
			}
			echo '				<input type="hidden" name="dn" value="cataddsave" />
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input class="main-button" value="'.$lang['all_submint'].'" type="submit" />
							</td>
						</tr>
					</table>
					</form>
					</div>';

			$tm->footer();
		}

		/**
		 * Добавление категории (сохранение)
		 ------------------------------------*/
		if ($_REQUEST['dn'] == 'cataddsave')
		{
			global $catid, $catname, $subtitle, $cpu, $catcustom, $keywords, $descript, $icon, $descr, $acc, $group, $sort, $ord, $rss;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=cat&amp;ops='.$sess['hash'].'">'.$lang['all_cat'].'</a>',
					$lang['all_add']
				);

			$catname = preparse($catname, THIS_TRIM, 0, 255);
			$subtitle = preparse($subtitle, THIS_TRIM, 0, 255);
			$cpu = preparse($cpu, THIS_TRIM, 0, 255);
			$icon = preparse($icon, THIS_TRIM);

			if (preparse($catname, THIS_EMPTY) == 1)
			{
				$tm->header();
				$tm->error($modname[PERMISS], $lang['all_add_cat'], $lang['forgot_name']);
				$tm->footer();
			}
			else
			{
				if (preparse($cpu, THIS_EMPTY) == 1)
				{
					$cpu = cpu_translit($catname);
				}

				$inqure = $db->query("SELECT catname, catcpu FROM ".$basepref."_".PERMISS."_cat WHERE catname = '".$db->escape($catname)."' OR catcpu = '".$db->escape($cpu)."'");
				if ($db->numrows($inqure) > 0)
				{
					$tm->header();
					$tm->error($lang['all_add_cat'], $catname, $lang['cpu_error_isset']);
					$tm->footer();
				}
			}

			if (
				isset($conf['user']['groupact']) AND
				$conf['user']['groupact'] == 'yes' AND
				$acc == 'group' AND is_array($group)
			)
			{
				$group = Json::encode($group);
			}

			$sort = isset($catsort[$sort]) ? $sort : 'public';
			$acc = ($acc == 'user' OR $acc == 'group') ? 'user' : 'all';
			$ord = ($ord == 'asc') ? 'asc' : 'desc';
			$rss = ($rss == 'yes') ? 'yes' : 'no';

			$db->query
				(
					"INSERT INTO ".$basepref."_".PERMISS."_cat VALUES (
					 NULL,
					 '".$catid."',
					 '".$db->escape($cpu)."',
					 '".$db->escape(preparse_sp($catname))."',
					 '".$db->escape(preparse_sp($subtitle))."',
					 '".$db->escape($descr)."',
					 '".$db->escape($catcustom)."',
					 '".$db->escape($keywords)."',
					 '".$db->escape($descript)."',
					 '0',
					 '".$db->escape($icon)."',
					 '".$acc."',
					 '".$db->escape($group)."',
					 '".$sort."',
					 '".$ord."',
					 '".$rss."',
					 '0'
					 )"
				);

			redirect('index.php?dn=cat&amp;ops='.$sess['hash']);
		}

		/**
		 * Удаление категории
		 ----------------------*/
		if ($_REQUEST['dn'] == 'catdel')
		{
			global $catid, $ok;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=cat&amp;ops='.$sess['hash'].'">'.$lang['all_cat'].'</a>',
					$lang['all_delet']
				);

			$catid = preparse($catid, THIS_INT);

			if ($ok == 'yes')
			{
				$del = this_delcat($catid, PERMISS);
				if ($del > 0) {
					$db->query("DELETE FROM ".$basepref."_".PERMISS." WHERE catid = '".$catid."'");
					$db->query("DELETE FROM ".$basepref."_".PERMISS."_cat WHERE catid = '".$catid."'");
				}
				$counts = new Counts(PERMISS, 'id');
				$cache->cachesave(1);
				redirect('index.php?dn=cat&amp;ops='.$sess['hash']);
			}
			else
			{
				$item = $db->fetchassoc($db->query("SELECT catname FROM ".$basepref."_".PERMISS."_cat WHERE catid = '".$catid."'"));

				$yes = 'index.php?dn=catdel&amp;catid='.$catid.'&amp;ok=yes&amp;ops='.$sess['hash'];
				$not = 'index.php?dn=cat&amp;ops='.$sess['hash'];

				$tm->header();
				$tm->shortdel($lang['del_cat'], preparse_un($item['catname']), $yes, $not, $lang['del_cat_alert']);
				$tm->footer();
			}
		}

		/**
		 * Редактировать категорию
		 ---------------------------*/
		if ($_REQUEST['dn'] == 'catedit')
		{
			global $catid, $selective;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=cat&amp;ops='.$sess['hash'].'">'.$lang['all_cat'].'</a>',
					$lang['all_edit']
				);

			$catid = preparse($catid, THIS_INT);
			$inquiry = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");
			$catcache = array();
			while ($item = $db->fetchassoc($inquiry))
			{
				$catcache[$item['parentid']][$item['catid']] = $item;
			}
			this_selectcat(0);

			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_cat WHERE catid = '".$catid."'"));

			$tm->header();

			echo '	<div class="section">
					<form action="index.php" method="post">
					<table class="work">
						<caption>'.$lang['cat_edit'].': '.$item['catname'].'</caption>
						<tr>
							<td class="first"><span>*</span> '.$lang['all_name'].'</td>
							<td>
								<input type="text" name="catname" id="catname" size="70" value="'.preparse_un($item['catname']).'" required="required" /> <span class="light">&lt;h1&gt;</span>
							</td>
						</tr>
						<tr>
							<td>'.$lang['sub_title'].'</td>
							<td><input type="text" name="subtitle" size="70" value="'.preparse_un($item['subtitle']).'" /> <span class="light">&lt;h2&gt;</span></td>
						</tr>';
			if ($conf['cpu'] == 'yes') {
			echo '		<tr>
							<td>'.$lang['all_cpu'].'</td>
							<td>
								<input type="text" name="cpu" id="cpu" size="70" value="'.$item['catcpu'].'" />';
								$tm->outtranslit('catname', 'cpu', $lang['cpu_int_hint']);
			echo '        </td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['custom_title'].'</td>
							<td><input type="text" name="catcustom" size="70" value="'.preparse_un($item['catcustom']).'" /> <span class="light">&lt;title&gt;</span></td>
						</tr>
						<tr>
							<td>'.$lang['all_descript'].'</td>
							<td><input type="text" name="descript" size="70" value="'.preparse_un($item['descript']).'" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_keywords'].'</td>
							<td>
								<input type="text" name="keywords" size="70" value="'.preparse_un($item['keywords']).'" />';
								$tm->outhint($lang['keyword_hint']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_in_cat'].'</td>
							<td>
								<select name="parentid" class="sw250">
									<option value="0">'.$lang['all_cat_new'].'</option>
									'.$selective.'
								</select>
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_sorting'].'</td>
							<td>
								<select name="sort" class="sw165">';
			foreach ($catsort as $k => $v)
			{
				echo '				<option value="'.$k.'"'.(($item['sort'] == $k) ? ' selected' : '').'>'.$v.'</option>';
			}
			echo '				</select> &nbsp;&#247;&nbsp;
								<select name="ord" class="sw150">
									<option value="desc"'.(($item['ord'] == 'desc') ? ' selected' : '').'>'.$lang['all_desc'].'</option>
									<option value="asc"'.(($item['ord'] == 'asc') ? ' selected' : '').'>'.$lang['all_acs'].'</option>
								</select>
							</td>
						</tr>';
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['all_cat_access'].'</td>
							<td>
								<select class="group-sel sw165" name="acc" id="acc">
									<option value="all"'.(($item['access'] == 'all') ? ' selected' : '').'>'.$lang['all_all'].'</option>
									<option value="user"'.(($item['access'] == 'user' AND empty($item['groups'])) ? ' selected' : '').'>'.$lang['all_user_only'].'</option>';
				echo '				'.(($conf['user']['groupact'] == 'yes') ? '<option value="group"'.(($item['access'] == 'user' AND ! empty($item['groups']))  ? ' selected' : '').'>'.$lang['all_groups_only'].'</option>' : '');
				echo '			</select>
								<div class="group" id="group"'.(($item['access'] == 'all' OR $item['access'] == 'user' AND empty($item['groups'])) ? ' style="display: none;"' : '').'>';
				if (isset($conf['user']['groupact']) AND $conf['user']['groupact'] == 'yes')
				{
					$inqs = $db->query("SELECT * FROM ".$basepref."_user_group");
					$group = Json::decode($item['groups']);
					$group_out = '';
					while ($items = $db->fetchassoc($inqs))
					{
						$group_out.= '<input type="checkbox" name="group['.$items['gid'].']" value="yes"'.(isset($group[$items['gid']]) ? ' checked' : '').'><span>'.$items['title'].'</span>,';
					}
					echo chop($group_out, ',');
				}
				echo '			</div>
							</td>
						</tr>';
			}
			echo '		<tr>
							<td>RSS</td>
							<td>
								<select name="rss" class="sw165">
									<option value="yes"'.(($item['rss'] == 'yes') ? ' selected' : '').'>'.$lang['all_yes'].'</option>
									<option value="no"'.(($item['rss'] == 'no') ? ' selected' : '').'>'.$lang['all_no'].'</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_icon'].'</td>
							<td>
								<input name="icon" id="icon" size="47" type="text" value="'.$item['icon'].'" />&nbsp;
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/icon/\',\'&amp;field[1]=icon\')" value="'.$lang['filebrowser'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_decs'].'</td>
							<td>';
								$tm->textarea('descr', 5, 50, $item['catdesc'], 1);
			echo '			</td>
						</tr>
						<tr class="tfoot">
							<td colspan="2">';
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'no')
			{
				echo '			<input type="hidden" name="acc" value="all" />';
			}
			echo '				<input type="hidden" name="dn" value="cateditsave" />
								<input type="hidden" name="catid" value="'.$catid.'" />
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input class="main-button" value="'.$lang['all_save'].'" type="submit" />
							</td>
						</tr>
					</table>
					</form>
					</div>';

			$tm->footer();
		}

		/**
		 * Редактировать категорию (сохранение)
		 ----------------------------------------*/
		if ($_REQUEST['dn'] == 'cateditsave')
		{
			global $parentid, $catid, $catname, $subtitle, $cpu, $catcustom, $keywords, $descript, $icon, $descr, $acc, $group, $sort, $ord, $rss;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=cat&amp;ops='.$sess['hash'].'">'.$lang['all_cat'].'</a>',
					$lang['all_edit']
				);

			$catname = preparse($catname, THIS_TRIM, 0, 255);
			$subtitle = preparse($subtitle, THIS_TRIM, 0, 255);
			$cpu = preparse($cpu, THIS_TRIM, 0, 255);
			$icon = preparse($icon, THIS_TRIM);
			$parentid = preparse($parentid, THIS_INT);
			$catid = preparse($catid, THIS_INT);
			$err = this_councat($catid, $parentid, PERMISS);

			if (preparse($catname, THIS_EMPTY) == 1)
			{
				$tm->header();
				$tm->error($modname[PERMISS], $lang['cat_edit'], $lang['forgot_name']);
				$tm->footer();
			}
			else
			{
				if (preparse($cpu, THIS_EMPTY) == 1)
				{
					$cpu = cpu_translit($catname);
				}

				$inqure = $db->query
							(
								"SELECT catname, catcpu FROM ".$basepref."_".PERMISS."_cat
								 WHERE (catname = '".$db->escape($catname)."' OR catcpu = '".$db->escape($cpu)."')
								 AND catid <> '".$catid."'
								"
							);

				if ($db->numrows($inqure) > 0)
				{
					$tm->header();
					$tm->error($lang['cat_edit'], $catname, $lang['cpu_error_isset']);
					$tm->footer();
				}
			}

			if ($err == 1)
			{
				$tm->header();
				$tm->error($lang['cat_edit'], $catname, $lang['move_cat_alert']);
				$tm->footer();
			}

			if (
				isset($conf['user']['groupact']) AND
				$conf['user']['groupact'] == 'yes' AND
				$acc == 'group' AND is_array($group)
			)
			{
				$group = Json::encode($group);
			}

			$sort = isset($catsort[$sort]) ? $sort : 'public';
			$acc = ($acc == 'user' OR $acc == 'group') ? 'user' : 'all';
			$ord = ($ord == 'asc') ? 'asc' : 'desc';
			$rss = ($rss == 'yes') ? 'yes' : 'no';
			$upparentid = ($catid != $parentid) ? "parentid = '".$parentid."'," : "";

			$db->query
				(
					"UPDATE ".$basepref."_".PERMISS."_cat SET ".$upparentid."
					 catcpu    = '".$db->escape($cpu)."',
					 catname   = '".$db->escape(preparse_sp($catname))."',
					 subtitle  = '".$db->escape(preparse_sp($subtitle))."',
					 catdesc   = '".$db->escape($descr)."',
					 catcustom = '".$db->escape(preparse_sp($catcustom))."',
					 keywords  = '".$db->escape(preparse_sp($keywords))."',
					 descript  = '".$db->escape(preparse_sp($descript))."',
					 icon      = '".$db->escape($icon)."',
					 access    = '".$acc."',
					 groups    = '".$db->escape($group)."',
					 sort      = '".$sort."',
					 ord       = '".$ord."',
					 rss       = '".$rss."'
					 WHERE catid = '".$catid."'"
				);

			$counts = new Counts(PERMISS, 'id');

			redirect('index.php?dn=cat&amp;ops='.$sess['hash']);
		}

		/**
		 * Все организации (листинг)
		 ------------------------------*/
		if ($_REQUEST['dn'] == 'list')
		{
			global $realmod, $selective, $catid, $nu, $p, $cat, $s, $l, $ajax, $filter, $fid;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					$lang['firms_all']
				);

			$ajaxlink = (defined('ENABLE_AJAX') AND ENABLE_AJAX == 'yes') ? 1 : 0;

			if (preparse($ajax, THIS_INT) == 0)
			{
				$tm->header();
				echo '<div id="ajaxbox">';
			}
			else
			{
				echo '<script>$(function(){$("img, a").tooltip();});</script>';
			}

			if (isset($conf['userbase']))
			{
				if ($conf['userbase'] == 'danneo') {
					require_once(WORKDIR.'/core/userbase/danneo/danneo.user.php');
				} else {
					require_once(WORKDIR.'/core/userbase/'.$conf['userbase'].'/danneo.user.php');
				}

				$userapi = new userapi($db, false);
			}

			if(isset($nu) AND ! empty($nu)) {
				echo '<script>cookie.set("num", "'.$nu.'", { path: "'.ADMPATH.'/" });</script>';
			}
			$nu = isset($nu) ? $nu : (isset($_COOKIE['num']) ? $_COOKIE['num'] : null);
			$nu = ( ! is_null($nu) AND in_array($nu, $conf['num'])) ? $nu : $conf['num'][0];
			$p = ( ! isset($p) OR $p <= 1) ? 1 : $p;

			$sort = array('id', 'title', 'public', 'hits', 'posit', 'reviews');
			$limit = array('desc', 'asc');
			$s = (in_array($s, $sort)) ? $s : 'posit';
			$l = (in_array($l, $limit)) ? $l : 'desc';

			$groups_only = array();
			if (isset($conf['user']['groupact']) AND $conf['user']['groupact'] == 'yes')
			{
				$inqs = $db->query("SELECT * FROM ".$basepref."_user_group");
				while ($items = $db->fetchassoc($inqs)) {
				$groups_only[] =  $items['title'];
				}
			}

			$catcache = $catcaches = array();
			$inquiry = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");
			while ($item = $db->fetchassoc($inquiry))
			{
				$catcache[$item['parentid']][$item['catid']] = $item;
				$catcaches[$item['catid']] = array($item['parentid'], $item['catid'], $item['catname']);
			}

			if (isset($cat) AND isset($catcaches[$cat]) OR isset($cat) AND $cat == 0 AND $cat != 'all')
			{
				$sql = " WHERE catid = '".preparse($cat, THIS_INT)."' AND pid = '0'";
				$link = "&amp;cat=".preparse($cat, THIS_INT);
				$catid = $cat;
			}
			else
			{
				$sql = " WHERE pid = '0'";
				$link = '&amp;cat=all';
				$cat = 'all';
				$catid = 0;
			}

			$fu = '';
			$fid = preparse($fid, THIS_INT);
			$myfilter = array
				(
					'title'  => array('title', 'all_name', 'input'),
					'userid' => array('userid', 'User ID', 'input'),
					'public' => array('public', 'all_data', 'date'),
					'acc'    => array('acc', 'all_access', 'access')
				);

			if ($fid > 0)
			{
				$inq = $db->query("SELECT * FROM ".$basepref."_admin_filter WHERE fid = '".$fid."'");
				if ($db->numrows($inq) > 0)
				{
					$item = $db->fetchassoc($inq);
					$insert = Json::decode($item['filter']);
					$sql.= ' AND '.implode(' AND ', $insert);
					$fu = '&amp;fid='.$item['fid'];
				}
			}
			else
			{
				if (isset($filter) AND is_array($filter))
				{
					$sw = array();
					foreach ($filter as $k => $v)
					{
						if (isset($myfilter[$k]))
						{
							$f = $myfilter[$k];
							if ($f[2] == 'input' AND !empty($v)) {
								$v = str_replace(array('"', "'"), '', strip_tags($v));
								$sw[] = $f[0]." LIKE '%".$db->escape($v)."%'";
							}
							if ($f[2] == 'access' AND !empty($v)) {
								$v = str_replace(array('"', "'"), '', strip_tags($v));
								$sw[] = $f[0]." LIKE '%".$db->escape($v)."%'";
							}
							if ($f[2] == 'checkbox' AND !empty($v)) {
								$v = str_replace(array('"', "'"), '', strip_tags($v));
								$sw[] = $f[0]." LIKE '%".$db->escape($v)."%'";
							}
							if ($f[2] == 'date' AND is_array($v))
							{
								if(isset($v[0]) AND !empty($v[0])){
									$sw[] = $f[0]." > '".$db->escape(ReDate($v[0]))."'";
								}
								if(isset($v[1]) AND !empty($v[1])){
									$sw[] = $f[0]." < '".$db->escape(ReDate($v[1]))."'";
								}
							}
						}
					}
					if (sizeof($sw) > 0)
					{
						$sql.= (($sql == '') ? ' WHERE ' : ' AND ').implode(' AND ', $sw);
						$insert = Json::encode($sw);
						$db->query("DELETE FROM ".$basepref."_admin_filter WHERE start < '".(NEWTIME - 360)."'");
						$db->query("INSERT INTO ".$basepref."_admin_filter VALUES (NULL, '".NEWTIME."', '".$db->escape($insert)."')");
						$fid = $db->insertid();
						if ($fid > 0) {
							$fu = '&amp;fid='.$fid;
						}
					}
				}
			}

			$link.= $fu;
			$a = ($ajaxlink) ? '&amp;ajax=1' : '';
			$revs = $link.$a.'&amp;nu='.$nu.'&amp;s='.$s.'&amp;l='.(($l=='desc') ? 'asc' : 'desc');
			$rev =  $link.$a.'&amp;nu='.$nu.'&amp;l=desc&amp;s=';
			$link.= $a.'&amp;s='.$s.'&amp;l='.$l;

			$c = $db->fetchassoc($db->query("SELECT COUNT(id) AS total FROM ".$basepref."_".PERMISS.$sql));
			if ($nu > 10 AND $c['total'] <= (($nu * $p) - $nu))
			{
				$p = 1;
			}

			$sf = $nu * ($p - 1);
			$inq = $db->query("SELECT * FROM ".$basepref."_".PERMISS.$sql." ORDER BY ".$s." ".$l." LIMIT ".$sf.", ".$nu);

			$pages  = $lang['pages'].':&nbsp;&nbsp;'.adm_pages(PERMISS.$sql, 'id', 'index', 'list'.$link, $nu, $p, $sess, $ajaxlink);
			$amount = $lang['amount_on_page'].':&nbsp; '.amount_pages("index.php?dn=list&amp;p=".$p."&amp;ops=".$sess['hash'].$link, $nu, $ajaxlink);

			this_selectcat(0);

			$tm->filter('index.php?dn=list&amp;ops='.$sess['hash'], $myfilter, $modname[PERMISS]);

			echo '	<div class="section">
					<form action="index.php" method="post">
					<table id="list" class="work">
						<caption>'.$lang[PERMISS].': '.$lang['firms_all'].'</caption>
						<tr>
							<td class="vm">
								'.$lang['all_cat_one'].':&nbsp;
								<form action="index.php" method="post">
									<select name="cat">
										<option value="all">'.$lang['all_all'].'</option>
										<option value="0"'.(($cat != 'all' AND $cat == 0) ? ' selected' : '').'>'.$lang['cat_not'].'</option>
										'.$selective.'
									</select>
									<input type="hidden" name="dn" value="list" />
									<input type="hidden" name="ops" value="'.$sess['hash'].'" />
									<input class="side-button" value="'.$lang['all_go'].'" type="submit" />
								</form>
							</td>
						</tr>
					</table>
					<div class="upad"></div>
					<form action="index.php" method="post">
					<table id="list" class="work">
						<tr><td colspan="12">'.$amount.'</td></tr>
						<tr>
							<th'.listsort('id').'>ID</th>
							<th'.listsort('title').'>'.$lang['all_name'].'&nbsp; &#8260; &nbsp;'.$lang['all_cat_one'].'</th>
							<th'.listsort('public').'>'.$lang['all_data'].'</th>
							<th'.listsort('hits').'>'.$lang['all_hits'].'</th>
							<th'.listsort('posit').'>'.$lang['all_posit'].'</th>
							<th'.listsort('reviews').'>'.$lang['review_all'].'</th>
							<th class="work-no-sort">'.$lang['all_access'].'</th>
							<th class="work-no-sort">'.$lang['author'].'</th>
							<th class="work-no-sort">'.$lang['photo_one'].'</th>
							<th class="work-no-sort">'.$lang['all_video'].'</th>
							<th class="work-no-sort">'.$lang['sys_manage'].'</th>
							<th class="work-no-sort ac"><input name="checkboxall" id="checkboxall" value="yes" type="checkbox" /></th>
						</tr>';
			while ($item = $db->fetchassoc($inq))
			{
				$style = ($item['act'] == 'no') ? 'no-active' : '';
				$stylework = ($item['act'] == 'no') ? 'no-active' : '';

				$groupact = NULL;
				if (isset($conf['user']['groupact']) AND $conf['user']['groupact'] == 'yes')
				{
					if ( ! empty($item['groups']))
					{
						$groups = Json::decode($item['groups']);
						reset($groups);
						while (list($key, $val) = each($groups))
						{
							$groupact.=  ' '.$groups_only[$key - 1].',';
						}
						$groupact = chop($groupact, ',');
					}
				}

				$author = '—';
				if ( ! empty($item['author']))
				{
					$author = preg_replace('/[^\pL\pNd\pZs\pP\pM]/us', '', $item['author']);
					if (in_array('user', $realmod))
					{
						$udata = $userapi->userdata('uname', $author);
						if ( ! empty($udata))
						{
							require_once(WORKDIR.'/core/classes/Router.php');
							$ro = new Router();
							$author = '<a href="'.$conf['site_url'].$ro->seo($userapi->data['linkprofile'].$udata['userid']).'" title="'.$lang['profile'].' - '.$author.'" target="_blank">'.$author.'</a>';
						}
					}
				}

				echo '	<tr id="lists" class="list">
							<td class="'.$style.' ac sw50">'.$item['id'].'</td>
							<td class="'.$style.' pw20">';
				if ($item['pid'] == 1) {
					echo '		<img src="'.ADMURL.'/template/images/totalinfo.gif" style="float: right; padding: 1px;" alt="'.$lang['post_moderate'].'" />';
                }
				if ($ajaxlink == 1) {
					echo '		<div id="te'.$item['id'].'">
									<a class="notooltip" href="javascript:$.ajaxeditor(\'index.php?dn=ajaxedittitle&amp;id='.$item['id'].$link.'&amp;ops='.$sess['hash'].'\',\'te'.$item['id'].'\',\'\')" title="'.$lang['all_change'].'">
										'.preparse_un($item['title']).'
									</a>
								</div>';
				} else {
					echo '		<a href="index.php?dn=edit&amp;p='.$p.'&amp;nu='.$nu.$link.'&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'">'.preparse_un($item['title']).'</a>';
				}
				if ($ajaxlink == 1 AND $conf[PERMISS]['multcat'] == 'no') {
					echo '		<div id="ce'.$item['id'].'" class="cats">
									<a class="notooltip" href="javascript:$.ajaxeditor(\'index.php?dn=ajaxeditcat&amp;id='.$item['id'].$link.'&amp;ops='.$sess['hash'].'\',\'ce'.$item['id'].'\',\'\')" title="'.$lang['all_change'].'">
										'.preparse_un(linecat($item['catid'], $catcaches)).'
									</a>
								</div>';
				} else {
					echo '		<span class="cats">'.preparse_un(linecat($item['catid'], $catcaches)).'</span>';
				}
				echo '		</td>
							<td class="'.$style.' pw10">';
				if ($ajaxlink == 1) {
					echo '		<div id="de'.$item['id'].'">
									<a class="notooltip" href="javascript:$.ajaxeditor(\'index.php?dn=ajaxeditdate&amp;id='.$item['id'].$link.'&amp;ops='.$sess['hash'].'\',\'de'.$item['id'].'\',\'\')" title="'.$lang['all_change'].'">
										'.format_time($item['public'], 0, 1).'
									</a>
								</div>';
				} else {
					echo			format_time($item['public'], 0, 1);
				}
				echo '		</td>
							<td class="'.$style.' pw10">'.$item['hits'].'</td>
							<td class="'.$style.' pw10">'.$item['posit'].'</td>
							<td class="'.$style.' pw10 gov">
								<a href="index.php?dn=firmreviews&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/edit.png" alt="'.$lang['reviews_firm'].'" /></a>
								&nbsp; ('.$item['reviews'].')';
				echo '		</td>
							<td class="'.$style.' pw10">';
				echo '				'.(($item['acc'] == 'user') ? (!empty($item['groups']) ? $lang['all_groups_only'].': <span class="server">'.$groupact.'</span>' : $lang['all_user_only']) : $lang['all_all']);
				echo '		</td>
							<td class="'.$style.' gov pw10">'.$author.'</td>
							<td class="'.$style.' gov pw10">';
				echo			'<a href="index.php?dn=photo&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'""><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/photo.png" alt="'.$lang['photo_album'].'" /></a>
								&nbsp; ('.$item['photos'].')';
				echo '		</td>
							<td class="'.$style.' gov pw10">';
				echo			'<a href="index.php?dn=video&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'""><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/video.png" alt="'.$lang['video_album'].'" /></a>
								&nbsp; ('.$item['videos'].')';
				echo '		</td>
							<td class="'.$style.' gov pw10">';
				echo '			<a href="index.php?dn=edit&amp;p='.$p.'&amp;nu='.$nu.$link.'&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/edit.png" alt="'.$lang['all_edit'].'" /></a>';
				if ($item['act'] == 'yes') {
					echo '		<a href="index.php?dn=act&amp;act=no&amp;cat='.$cat.'&amp;fid='.$fid.'&amp;id='.$item['id'].'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/act.png" alt="'.$lang['not_included'].'" /></a>';
				} else {
					echo '		<a class="inact" href="index.php?dn=act&amp;act=yes&amp;cat='.$cat.'&amp;fid='.$fid.'&amp;id='.$item['id'].'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/act.png" alt="'.$lang['included'].'" /></a>';
				}
				echo '			<a href="index.php?dn=up&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/up.png" alt="'.$lang['rise_up'].'" /></a>
								<a href="index.php?dn=del&amp;p='.$p.'&amp;nu='.$nu.$link.'&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/del.png" alt="'.$lang['all_delet'].'" /></a>
							</td>
							<td class="'.$style.' mark">
								<input type="checkbox" name="array['.$item['id'].']" value="yes" />
							</td>
						</tr>';
			}
			echo '		<tr>
							<td colspan="12">'.$lang['all_mark_work'].':&nbsp;
								<select name="workname">';
			if ($conf[PERMISS]['multcat'] == 'no')
			{
				echo '				<option value="move">'.$lang['all_move'].'</option>';
			}
			echo '					<option value="del">'.$lang['all_delet'].'</option>
									<option value="active">'.$lang['included'].'&nbsp; &#8260; &nbsp;'.$lang['not_included'].'</option>';
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'yes')
			{
				echo '				<option value="access">'.$lang['all_access'].'</option>';
			}
			echo '				</select>
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input type="hidden" name="p" value="'.$p.'" />
								<input type="hidden" name="cat" value="'.$cat.'" />
								<input type="hidden" name="nu" value="'.$nu.'" />
								<input type="hidden" name="s" value="'.$s.'" />
								<input type="hidden" name="l" value="'.$l.'" />';
			if ($fid > 0) {
				echo '			<input type="hidden" name="fid" value="'.$fid.'" />';
            }
			echo '				<input type="hidden" name="dn" value="work" />
								<input id="button" class="side-button" value="'.$lang['all_go'].'" type="submit" />
							</td>
						</tr>
						<tr><td colspan="12">'.$pages.'</td></tr>
					</table>
					</form>
					</div>';

			if (preparse($ajax, THIS_INT) == 0)
			{
				echo '	</div>';
				$tm->footer();
			}
		}

		/**
		 * Поднять на верхнюю позицию
		 ------------------------------*/
		if ($_REQUEST['dn'] == 'up')
		{
			global $id;

			$id = preparse($id, THIS_INT);

			$item = $db->fetchassoc($db->query("SELECT posit FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'"));

			$max = $db->fetchassoc($db->query("SELECT MAX(posit) AS posit FROM ".$basepref."_".PERMISS.""));
			$posit = $max['posit'] + 1;

			if ($item['posit'] < $max['posit'])
			{
				$db->query("UPDATE ".$basepref."_".PERMISS." SET posit = '".$posit."' WHERE id = '".$id."'");
			}

			redirect('index.php?dn=list&amp;ops='.$sess['hash']);
		}

		/**
		 * Изображения организации (листинг)
		 ------------------------------------*/
		if ($_REQUEST['dn'] == 'photo')
		{
			global $nu, $p, $id, $ajax;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					$lang['photos']
				);

			$ajaxlink = (defined('ENABLE_AJAX') AND ENABLE_AJAX == 'yes') ? 1 : 0;

			if (preparse($ajax, THIS_INT) == 0)
			{
				$tm->header();
				echo '<div id="ajaxbox">';
			}
			else
			{
				echo '<script>$(function(){$("img, a").tooltip();});</script>';
			}

			if(isset($nu) AND ! empty($nu)) {
				echo '<script>cookie.set("num", "'.$nu.'", { path: "'.ADMPATH.'/" });</script>';
			}
			$nu = isset($nu) ? $nu : (isset($_COOKIE['num']) ? $_COOKIE['num'] : null);
			$nu = ( ! is_null($nu) AND in_array($nu, $conf['num'])) ? $nu : $conf['num'][0];
			$p  = ( ! isset($p) OR $p <= 1) ? 1 : $p;

			$id = preparse($id, THIS_INT);
			$items = $db->fetchassoc($db->query("SELECT title FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'"));

			$a = ($ajaxlink) ? '&amp;ajax=1' : '';
			$sql  = " WHERE firm_id = '".$id."'";
			$c = $db->fetchassoc($db->query("SELECT COUNT(id) AS total FROM ".$basepref."_".PERMISS."_photo".$sql));
			if ($nu > 10 AND $c['total'] <= (($nu * $p) - $nu))
			{
				$p = 1;
			}
			$sf = $nu * ($p - 1);
			$inq = $db->query("SELECT * FROM ".$basepref."_".PERMISS."_photo WHERE firm_id = '".$id."' ORDER BY posit DESC LIMIT ".$sf.", ".$nu);
			$pages  = $lang['all_pages'].':&nbsp; '.adm_pages(PERMISS."_photo".$sql, 'id', 'index', 'photo&amp;id='.$id.$a, $nu, $p, $sess, $ajaxlink);
			$amount = $lang['amount_on_page'].':&nbsp; '.amount_pages("index.php?dn=photo&amp;id=".$id."&amp;p=".$p."&amp;ops=".$sess['hash'].$a, $nu, $ajaxlink);

			echo '	<script src="'.ADMPATH.'/js/jquery.apanel.tabs.js"></script>
					<script>
					$(document).ready(function()
					{
						$(".media-view").colorbox({
							initialWidth  : 450,
							initialHeight : 338,
							maxHeight     : 600,
							maxWidth      : 800,
							onLoad: function() {
								$("#cboxClose").hide();
							},
							onComplete: function () {
								$("#cboxClose").hide();
							}
						});
						$("#form-add").one("submit", function() {
							var link = $("#title").val(),
								thumb = $("#image_thumb").val(),
								image = $("#image").val();
							if(link.length > 0 && link.length > 0 && image.length > 0) {
								$("#load").html(\'<div class="save"></div>\');
							}
						});
					});
					$(window).bind("pageshow", function(event) {
						if (event.originalEvent.persisted) {
							$("#load").empty();
							window.location.reload();
						}
					});
					</script>';
			echo '	<div class="section">
					<form action="index.php" method="post" id="form">
					<table id="list" class="work">
						<caption>'.$lang['photos'].': '.$items['title'].'</caption>
						<tr><td colspan="7">'.$amount.'</td></tr>
						<tr>
							<th>ID</th>
							<th class="al">'.$lang['all_name'].'</th>
							<th>'.$lang['descript'].'</th>
							<th>'.$lang['all_posit'].'</th>
							<th>'.$lang['photo_one'].'</th>
							<th>'.$lang['sys_manage'].'</th>
							<th class="ac"><input name="checkboxall" id="checkboxall" value="yes" type="checkbox"></th>
						</tr>';
			while ($item = $db->fetchassoc($inq))
			{
				$style = ($item['act'] == 'no') ? 'no-active' : '';
				echo '	<tr class="list">
							<td class="'.$style.' ac pw5">'.$item['id'].'</td>
							<td class="'.$style.' pw15 al site">'.preparse_un($item['title']).'</td>
							<td class="'.$style.' pw25">'.preparse_un($item['descript']).'</td>
							<td class="'.$style.' pw15"><input type="text" name="posit['.$item['id'].']" size="3" value="'.$item['posit'].'" maxlength="3"></td>
							<td class="'.$style.' pw15">';
				if ( ! empty($item['image'])) {
					echo '		<a class="media-view" href="'.$conf['site_url'].'/'.$item['image'].'"><img src="index.php?dn=thumb&amp;type=photo&amp;id='.$item['id'].'&amp;x=36&amp;h=27&amp;r=yes&amp;ops='.$sess['hash'].'" alt="'.$lang['file_view'].'" /></a>';
				} else {
					echo '		<img src="index.php?dn=thumb&amp;type=photo&amp;id='.$item['id'].'&amp;x=36&amp;h=27&amp;r=yes&amp;ops='.$sess['hash'].'">';
				}
				echo '		</td>
							<td class="'.$style.' gov pw10">
								<a href="index.php?dn=photoedit&amp;id='.$item['id'].'&amp;fid='.$id.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/edit.png" alt="'.$lang['all_edit'].'" /></a>';
				if ($item['act'] == 'yes') {
					echo '		<a href="index.php?dn=photoact&amp;act=no&amp;id='.$item['id'].'&amp;fid='.$id.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'].'"><img alt="'.$lang['not_included'].'" src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/act.png"></a>';
				} else {
					echo '		<a class="inact" href="index.php?dn=photoact&amp;act=yes&amp;id='.$item['id'].'&amp;fid='.$id.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'].'"><img alt="'.$lang['included'].'" src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/act.png"></a>';
				}
					echo '		<a href="index.php?dn=photodel&amp;id='.$item['id'].'&amp;fid='.$id.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/del.png" alt="'.$lang['all_delet'].'" /></a>
							</td>
							<td class="'.$style.' mark pw5"><input type="checkbox" name="array['.$item['id'].']" value="'.$id.'"></td>
						</tr>';
			}
			echo '		<tr>
							<td colspan="7">'.$lang['all_mark_work'].':&nbsp;
								<select name="workname">
									<option value="pos" selected>'.$lang['save_posit'].'</option>
									<option value="act">'.$lang['included'].'&nbsp; &#8260; &nbsp;'.$lang['not_included'].'</option>
									<option value="del">'.$lang['all_delet'].'</option>
								</select>
								<input type="hidden" name="ops" value="'.$sess['hash'].'">
								<input type="hidden" name="dn" value="workphoto">
								<input type="hidden" name="p" value="'.$p.'">
								<input type="hidden" name="id" value="'.$id.'">
								<input type="hidden" name="nu" value="'.$nu.'">
								<input id="button" class="side-button" value="'.$lang['all_go'].'" type="submit">
							</td>
						</tr>
						<tr><td colspan="7">'.$pages.'</td></tr>
					</table>
					</form>
					</div>
					<div class="sline"></div>
					<div class="section">
					<form action="index.php" method="post" id="form-add">
					<table class="work">
						<caption>'.$lang['photo_add'].'</caption>
						<tr>
							<th></th>
							<th>
								<div class="tabs" id="tabs">
									<a href="#" data-tabs=".tab-1">'.$lang['home'].'</a>
									<a href="#" data-tabs=".tab-2" style="display: none;"></a>
									<a href="#" data-tabs="all">'.$lang['all_field'].'</a>
								</div>
							</th>
						</tr>
						<tbody class="tab-1">
						<tr>
							<td class="first"><span>*</span> '.$lang['all_name'].'</td>
							<td>
								<input name="title" id="title" size="70" type="text" required="required" />
							</td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['descript'].'</td>
							<td class="usewys">';
			if ($wysiwyg == 'yes')
			{
				define("USEWYS", 1);
				$form_short = 'descript';
				$WYSFORM = 'descript';
				$WYSVALUE = '';
				include(ADMDIR.'/includes/wysiwyg.php');
			}
			else
			{
				$tm->textarea('descript', 4, 70, '', 1);
			}
			echo '				</td>
						</tr>
						</tbody>
						<tbody class="tab-1">
						<tr>
							<td class="first"><span>*</span> '.$lang['all_image_thumb'].'</td>
							<td>
								<input name="image_thumb" id="image_thumb" size="70" type="text" required="required" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/photo/\',\'&amp;field[1]=image_thumb&amp;field[2]=image\')" value="'.$lang['filebrowser'].'" type="button" />
								<input class="side-button" onclick="javascript:$.quickupload(\''.$sess['hash'].'&amp;objdir=/'.PERMISS.'/photo/\')" value="'.$lang['file_review'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td class="first"><span>*</span> '.$lang['all_image'].'</td>
							<td>
								<input name="image" id="image" size="70" type="text" required="required" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/photo/\',\'&amp;field[1]=image&amp;field[2]=image_thumb\')" value="'.$lang['filebrowser'].'" type="button" />
							</td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['all_alt_image'].'</td>
							<td><input name="image_alt" id="image_alt" size="70" type="text" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_status'].'</td>
							<td>
								<select name="act" class="sw165">
									<option value="yes">'.$lang['included'].' </option>
									<option value="no">'.$lang['not_included'].'</option>
								</select>
							</td>
						</tr>
						</tbody>
						<tr class="tfoot">
							<td colspan="2">
								<input type="hidden" name="dn" value="photoaddsave">
								<input type="hidden" name="id" value="'.$id.'">
								<input type="hidden" name="ops" value="'.$sess['hash'].'">
								<input type="hidden" name="admid" value="'.$ADMIN_ID.'">
								<input class="main-button" value="'.$lang['all_submint'].'" type="submit">
							</td>
						</tr>
					</table>
					</form>
					</div><div id="load"></div>';

			echo "	<script>
						$(document).ready(function() {
							$('#tabs a').tabs('.tab-1');
						});
					</script>";

			if (preparse($ajax, THIS_INT) == 0)
			{
				echo '</div>';
				$tm->footer();
			}
		}

		/**
		 * Добавить изображение (сохранение)
		 -------------------------------------*/
		if ($_REQUEST['dn'] == 'photoaddsave')
		{
			global $id, $title, $descript, $image, $image_thumb, $image_alt, $act;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=photo&amp;id='.$id.'&amp;ops='.$sess['hash'].'">'.$lang['photos'].'</a>',
					$lang['photo_add']
				);

			$id = preparse($id, THIS_INT);

			if (
				preparse($id, THIS_EMPTY) == 1 OR
				preparse($title, THIS_EMPTY) == 1 OR
				preparse($image, THIS_EMPTY) == 1 OR
				preparse($image_thumb, THIS_EMPTY) == 1
			) {
				$tm->header();
				$tm->error($modname[PERMISS], $lang['photo_add'], $lang['forgot_name']);
				$tm->footer();
			}
			else
			{
				$extension = strtolower(pathinfo($image_thumb, PATHINFO_EXTENSION));
				if ( ! in_array($extension, array('gif', 'jpg', 'jpeg', 'png')) )
				{
					$tm->header();
					$tm->error($modname[PERMISS], $lang['photo_add'], $lang['photo_error_type']);
					$tm->footer();
				}

				$max = $db->fetchassoc($db->query("SELECT MAX(posit) + 1 AS posit FROM ".$basepref."_".PERMISS."_photo WHERE firm_id = '".$id."'"));
				$db->query("UPDATE ".$basepref."_".PERMISS." SET photos = photos + 1 WHERE id = '".$id."'");

				$title = preparse($title, THIS_TRIM, 0, 255);
				$descript = preparse($descript, THIS_TRIM);
				$image = preparse($image, THIS_TRIM, 0, 255);
				$image_thumb = preparse($image_thumb, THIS_TRIM, 0, 255);
				$image_alt = preparse($image_alt, THIS_TRIM, 0, 255);
				$posit = preparse($max['posit'], THIS_INT);
				$act = ($act == 'yes') ? 'yes' : 'no';

				$db->query
					(
						"INSERT INTO ".$basepref."_".PERMISS."_photo VALUES (
						 NULL,
						 '".$id."',
						 '".NEWTIME."',
						 '".$db->escape(preparse_sp($title))."',
						 '".$db->escape(preparse_sp($descript))."',
						 '".$db->escape($image)."',
						 '".$db->escape($image_thumb)."',
						 '".$db->escape(preparse_sp($image_alt))."',
						 '".$posit."',
						 '".$act."'
						 )"
					);

				redirect('index.php?dn=photo&amp;id='.$id.'&amp;ops='.$sess['hash']);
			}
		}

		/**
		 * Редактировать изображение
		 ------------------------------*/
		if ($_REQUEST['dn'] == 'photoedit')
		{
			global $id, $fid, $p, $nu;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=photo&id='.$fid.'&amp;ops='.$sess['hash'].'">'.$lang['photos'].'</a>',
					$lang['photo_edit']
				);

			$id = preparse($id, THIS_INT);
			$fid = preparse($fid, THIS_INT);

			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$id."'"));

			$tm->header();

			echo '	<div class="section">
					<form action="index.php" method="post">
					<table class="work">
						<caption>'.$lang['photo_edit'].': '.preparse_un($item['title']).'</caption>
						<tr>
							<td class="first"><span>*</span> '.$lang['all_name'].'</td>
							<td><input id="title" type="text" name="title" size="70" value="'.preparse_un($item['title']).'" required="required"></td>
						</tr>
						<tr>
							<td>'.$lang['descript'].'</td>
							<td class="usewys">';
			if ($wysiwyg == 'yes')
			{
				define("USEWYS", 1);
				$form_short = 'descript';
				$WYSFORM = 'descript';
				$WYSVALUE = $item['descript'];
				include(ADMDIR.'/includes/wysiwyg.php');
			}
			else
			{
				$tm->textarea('descript', 4, 70, $item['descript'], 1);
			}
			echo '			</td>
						</tr>
						<tr>
							<td class="first"><span>*</span> '.$lang['all_image_thumb'].'</td>
							<td>
								<input name="image_thumb" id="image_thumb" size="70" type="text" value="'.$item['image_thumb'].'" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/photo/\',\'&amp;field[1]=image_thumb&amp;field[2]=image\')" value="'.$lang['filebrowser'].'" type="button" />
								<input class="side-button" onclick="javascript:$.quickupload(\''.$sess['hash'].'&amp;objdir=/'.PERMISS.'/photo/\')" value="'.$lang['file_review'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td class="first"><span>*</span> '.$lang['all_image'].'</td>
							<td>
								<input name="image" id="image" size="70" type="text" value="'.$item['image'].'" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/photo/\',\'&amp;field[1]=image&amp;field[2]=image_thumb\')" value="'.$lang['filebrowser'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_alt_image'].'</td>
							<td><input name="image_alt" id="image_alt" size="50" type="text" value="'.preparse_un($item['image_alt']).'" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_status'].'</td>
							<td>
								<select name="act" class="sw165">
									<option value="yes"'.(($item['act'] == 'yes') ? ' selected' : '').'>'.$lang['included'].' </option>
									<option value="no"'.(($item['act'] == 'no')  ? ' selected' : '').'>'.$lang['not_included'].'</option>
								</select>
							</td>
						</tr>
						<tr class="tfoot">
							<td colspan="2">
								<input type="hidden" name="ops" value="'.$sess['hash'].'">
								<input type="hidden" name="fid" value="'.$fid.'">
								<input type="hidden" name="id" value="'.$id.'">
								<input type="hidden" name="p" value="'.$p.'">
								<input type="hidden" name="nu" value="'.$nu.'">
								<input type="hidden" name="dn" value="photoeditsave">
								<input class="main-button" value="'.$lang['all_save'].'" type="submit">
							</td>
						</tr>
					</table>
					</form>
					</div>';

			$tm->footer();
		}

		/**
		 * Редактировать изображение (сохранение)
		 ------------------------------------------*/
		if ($_REQUEST['dn'] == 'photoeditsave')
		{
			global $fid, $id, $act, $title, $descript, $image_thumb, $image_video, $image_alt, $p, $nu;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=photo&id='.$fid.'&amp;ops='.$sess['hash'].'">'.$lang['photos'].'</a>',
					$lang['photo_edit']
				);

			$id = preparse($id, THIS_INT);
			$fid = preparse($fid, THIS_INT);

			$item = $db->fetchassoc($db->query("SELECT title, act FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$id."'"));

			if (
				preparse($title, THIS_EMPTY) == 1 OR
				preparse($image_thumb, THIS_EMPTY) == 1 OR
				preparse($fid, THIS_EMPTY) == 1 OR
				preparse($id, THIS_EMPTY) == 1
			) {
				$tm->header();
				$tm->error($lang['photo_edit'], $item['title'], $lang['forgot_name']);
				$tm->footer();
			}
			else
			{
				$extension = strtolower(pathinfo($image_thumb, PATHINFO_EXTENSION));
				if ( ! in_array($extension, array('gif', 'jpg', 'jpeg', 'png')) )
				{
					$tm->header();
					$tm->error($lang['photo_edit'], $item['title'], $lang['photo_error_type']);
					$tm->footer();
				}

				$title = preparse($title, THIS_TRIM, 0, 255);
				$descript = preparse($descript, THIS_TRIM);
				$image = preparse($image, THIS_TRIM, 0, 255);
				$image_thumb = preparse($image_thumb, THIS_TRIM, 0, 255);
				$image_alt = preparse($image_alt, THIS_TRIM, 0, 255);
				$act = ($act == 'yes') ? 'yes' : 'no';

				$db->query
					(
						"UPDATE ".$basepref."_".PERMISS."_photo SET
						 title       = '".$db->escape(preparse_sp($title))."',
						 descript    = '".$db->escape($descript)."',
						 image       = '".$db->escape($image)."',
						 image_thumb = '".$db->escape($image_thumb)."',
						 image_alt   = '".$db->escape(preparse_sp($image_alt))."',
						 act         = '".$act."'
						 WHERE id = '".$id."'"
					);

				$sign = ($act == 'yes' AND $item['act'] == 'no') ? "+" : "-";
				$db->query("UPDATE ".$basepref."_".PERMISS." SET photos = photos ".$sign." 1 WHERE id = '".$fid."'");

				$redir = 'index.php?dn=photo';
				$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
				$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
				$redir.= ( ! empty($fid)) ? '&amp;id='.preparse($fid, THIS_INT) : '';
				$redir.= '&amp;ops='.$sess['hash'];

				redirect($redir);
			}
		}

		/**
		 * Изменение состояния фото (вкл./выкл.)
		 ----------------------------------------*/
		if ($_REQUEST['dn'] == 'photoact')
		{
			global $id, $fid, $act, $p, $nu;

			$id = preparse($id, THIS_INT);
			$act = preparse($act, THIS_TRIM);

			$item = $db->fetchassoc($db->query("SELECT act FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$id."'"));

			if ($act == 'no' OR $act == 'yes')
			{
				$db->query("UPDATE ".$basepref."_".PERMISS."_photo SET act = '".$act."' WHERE id = '".$id."'");
			}

			$sign = ($act == 'yes' AND $item['act'] == 'no') ? "+" : "-";
			$db->query("UPDATE ".$basepref."_".PERMISS." SET photos = photos ".$sign." 1 WHERE id = '".$fid."'");

			$redir = 'index.php?dn=photo&amp;ops='.$sess['hash'];
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= ( ! empty($fid)) ? '&amp;id='.preparse($fid, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Удалить изображение
		 --------------------------*/
		if ($_REQUEST['dn'] == 'photodel')
		{
			global $ok, $id, $fid, $p, $nu;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=photo&id='.$fid.'&amp;ops='.$sess['hash'].'">'.$lang['photos'].'</a>',
					$lang['photo_del']
				);

			$id = preparse($id, THIS_INT);
			$fid = preparse($fid, THIS_INT);

			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$id."'"));

			if ($ok == 'yes')
			{
				$db->query("DELETE FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$id."'");

				unlink(DNDIR.$item['image']);
				unlink(DNDIR.$item['image_thumb']);

				$count = $db->fetchassoc($db->query("SELECT COUNT(id) AS total FROM ".$basepref."_".PERMISS."_photo WHERE firm_id = '".$fid."'"));
				$db->query("UPDATE ".$basepref."_".PERMISS." SET photos = '".$count['total']."' WHERE id = '".$fid."'");

				$redir = 'index.php?dn=photo&amp;ops='.$sess['hash'];
				$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
				$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
				$redir.= ( ! empty($fid)) ? '&amp;id='.preparse($fid, THIS_INT) : '';

				redirect($redir);
			}
			else
			{
				$yes = 'index.php?dn=photodel&amp;id='.$id.'&amp;fid='.$fid.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;id='.$id.'&amp;ok=yes&amp;ops='.$sess['hash'];
				$not = 'index.php?dn=photo&amp;id='.$id.'&amp;fid='.$fid.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'];

				$tm->header();
				$tm->shortdel($lang['photo_del'], preparse_un($item['title']), $yes, $not);
				$tm->footer();
			}
		}

		/**
		 * Массовая обработка фото
		 ---------------------------*/
		if ($_REQUEST['dn'] == 'workphoto')
		{
			global $array, $workname, $posit, $p, $id, $nu;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=photo&amp;id='.$id.'&amp;ops='.$sess['hash'].'">'.$lang['photos'].'</a>',
					$lang['array_control']
				);

			if (preparse($array, THIS_ARRAY) == 1)
			{
				$hidden = '';
				foreach ($array as $key => $val) {
					$hidden.= '<input type="hidden" name="array['.$key.']" value="'.$val.'">';
				}
				$p = preparse($p, THIS_INT);
				$id = preparse($id, THIS_INT);
				$nu = preparse($nu, THIS_INT);
				$h = '<input type="hidden" name="p" value="'.$p.'">'
					.'<input type="hidden" name="nu" value="'.$nu.'">'
					.'<input type="hidden" name="id" value="'.$id.'">'
					.'<input type="hidden" name="ops" value="'.$sess['hash'].'">';

				$count = count($array);

				// Удаление
				if ($workname == 'del')
				{
					$tm->header();
					echo '	<div class="section">
							<form action="index.php" method="post">
							<table id="arr-work" class="work">
								<caption>'.$lang['array_control'].': '.$lang['all_delet'].' ('.$count.')</caption>
								<tr><td class="cont">'.$lang['alertdel'].'</td></tr>
								<tr class="tfoot">
									<td>
										<input type="hidden" name="ops" value="'.$sess['hash'].'">
										'.$hidden.'
										'.$h.'
										<input type="hidden" name="dn" value="photoarrdel">
										<input class="side-button" value="'.$lang['all_go'].'" type="submit">
										<input class="side-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button">
									</td>
								</tr>
							</table>
							</form>
							</div>';
					$tm->footer();

				// Активация
				}
				elseif ($workname == 'act')
				{
					$tm->header();
					echo '	<div class="section">
							<form action="index.php" method="post">
							<table id="arr-work" class="work">
								<caption>'.$lang['array_control'].': '.$lang['all_status'].' ('.$count.')</caption>
								<tr>
									<td class="cont">
										<select name="act">
											<option value="yes">'.$lang['included'].'</option>
											<option value="no">'.$lang['not_included'].'</option>
										</select>
									</td>
								</tr>
								<tr class="tfoot">
									<td>
										'.$hidden.'
										'.$h.'
										<input type="hidden" name="dn" value="photoarract">
										<input class="side-button" value="'.$lang['all_go'].'" type="submit">
										<input class="side-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button">
									</td>
								</tr>
							</table>
							</form>
							</div>';
					$tm->footer();

				// Позиции
				}
				elseif ($workname == 'pos')
				{
					if (preparse($posit, THIS_ARRAY) == 1)
					{
						while (list($fid, $val) = each($posit))
						{
							$fid = preparse($fid, THIS_INT);
							$db->query("UPDATE ".$basepref."_".PERMISS."_photo SET posit = '".intval($val)."' WHERE id = '".intval($fid)."'");
						}
					}
				}
			}

			$redir = 'index.php?dn=photo&amp;ops='.$sess['hash'];
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= ( ! empty($id)) ? '&amp;id='.preparse($id, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Массовое удаление фото (сохранение)
		 --------------------------------------*/
		if ($_REQUEST['dn'] == 'photoarrdel')
		{
			global $array, $p, $id, $nu;

			$id = preparse($id, THIS_INT);

			if (is_array($array) AND ! empty($array))
			{
				while (list($pid, $fid) = each($array))
				{
					$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$pid."'"));

					$db->query("UPDATE ".$basepref."_".PERMISS." SET photos = photos - 1 WHERE id = '".$fid."'");
					$db->query("DELETE FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$pid."'");

					unlink(DNDIR.$item['image']);
					unlink(DNDIR.$item['image_thumb']);
				}
			}

			$redir = 'index.php?dn=photo&amp;ops='.$sess['hash'];
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= ( ! empty($id)) ? '&amp;id='.preparse($id, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Массовая активация фото (сохранение)
		 ---------------------------------------*/
		if ($_REQUEST['dn'] == 'photoarract')
		{
			global $array, $p, $id, $act, $nu;

			$id = preparse($id, THIS_INT);

			if (preparse($array, THIS_ARRAY) == 1)
			{
				while (list($pid, $fid) = each($array))
				{
					$item = $db->fetchassoc($db->query("SELECT act FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$pid."'"));

					$sign = ($act == 'yes' AND $item['act'] == 'no') ? "+" : "-";
					$db->query("UPDATE ".$basepref."_".PERMISS." SET photos = photos ".$sign." 1 WHERE id = '".$fid."'");
					$db->query("UPDATE ".$basepref."_".PERMISS."_photo SET act = '".$act."' WHERE id = '".$pid."'");
				}
			}

			$redir = 'index.php?dn=photo&amp;ops='.$sess['hash'];
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= ( ! empty($id)) ? '&amp;id='.preparse($id, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Видео организации (листинг)
		 ------------------------------*/
		if ($_REQUEST['dn'] == 'video')
		{
			global $nu, $p, $id, $ajax;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					$lang['video_album']
				);

			$ajaxlink = (defined('ENABLE_AJAX') AND ENABLE_AJAX == 'yes') ? 1 : 0;

			if (preparse($ajax, THIS_INT) == 0)
			{
				$tm->header();
				echo '<div id="ajaxbox">';
			}
			else
			{
				echo '<script>$(function(){$("img, a").tooltip();});</script>';
			}

			if(isset($nu) AND ! empty($nu)) {
				echo '<script>cookie.set("num", "'.$nu.'", { path: "'.ADMPATH.'/" });</script>';
			}
			$nu = isset($nu) ? $nu : (isset($_COOKIE['num']) ? $_COOKIE['num'] : null);
			$nu = ( ! is_null($nu) AND in_array($nu, $conf['num'])) ? $nu : $conf['num'][0];
			$p  = ( ! isset($p) OR $p <= 1) ? 1 : $p;

			$id = preparse($id, THIS_INT);
			$items = $db->fetchassoc($db->query("SELECT title FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'"));

			$a = ($ajaxlink) ? '&amp;ajax=1' : '';
			$sql  = " WHERE firm_id = '".$id."'";
			$c = $db->fetchassoc($db->query("SELECT COUNT(id) AS total FROM ".$basepref."_".PERMISS."_video".$sql));
			if ($nu > 10 AND $c['total'] <= (($nu * $p) - $nu))
			{
				$p = 1;
			}
			$sf = $nu * ($p - 1);
			$inq = $db->query("SELECT * FROM ".$basepref."_".PERMISS."_video WHERE firm_id = '".$id."' ORDER BY posit DESC LIMIT ".$sf.", ".$nu);
			$pages  = $lang['all_pages'].':&nbsp; '.adm_pages(PERMISS."_video".$sql, 'id', 'index', 'video&amp;id='.$id.$a, $nu, $p, $sess, $ajaxlink);
			$amount = $lang['amount_on_page'].':&nbsp; '.amount_pages("index.php?dn=video&amp;id=".$id."&amp;p=".$p."&amp;ops=".$sess['hash'].$a, $nu, $ajaxlink);

			echo '	<script src="'.ADMPATH.'/js/jquery.apanel.tabs.js"></script>
					<script>
					$(document).ready(function()
					{
						$(".media-view").colorbox({
							initialWidth  : 450,
							initialHeight : 338,
							maxHeight     : 600,
							maxWidth      : 800,
							onLoad: function() {
								$("#cboxClose").hide();
							},
							onComplete: function () {
								$("#cboxClose").hide();
							}
						});
						$("#form-add").one("submit", function() {
							var link = $("#link").val();
							if(link.length > 0) {
								$("#load").html(\'<div class="save"></div>\');
							}
						});
					});
					$(window).bind("pageshow", function(event) {
						if (event.originalEvent.persisted) {
							$("#load").empty();
							window.location.reload();
						}
					});
					</script>';
			echo '	<div class="section">
					<form action="index.php" method="post" id="form">
					<table id="list" class="work">
						<caption>'.$lang['video_album'].': '.$items['title'].'</caption>
						<tr><td colspan="7">'.$amount.'</td></tr>
						<tr>
							<th>ID</th>
							<th class="al">'.$lang['all_name'].'</th>
							<th>'.$lang['descript'].'</th>
							<th>'.$lang['all_posit'].'</th>
							<th>'.$lang['all_video'].'</th>
							<th>'.$lang['sys_manage'].'</th>
							<th class="ac"><input name="checkboxall" id="checkboxall" value="yes" type="checkbox"></th>
						</tr>';
			while ($item = $db->fetchassoc($inq))
			{
				$style = ($item['act'] == 'no') ? 'no-active' : '';
				echo '	<tr class="list">
							<td class="'.$style.' ac pw5">'.$item['id'].'</td>
							<td class="'.$style.' pw15 al site">'.preparse_un($item['title']).'</td>
							<td class="'.$style.' pw25">'.preparse_un($item['descript']).'</td>
							<td class="'.$style.' pw15"><input type="text" name="posit['.$item['id'].']" size="3" value="'.$item['posit'].'" maxlength="3"></td>
							<td class="'.$style.' pw15">';
				if ( ! empty($item['video'])) {
					echo '		<a class="media-view" href="index.php?dn=view&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'"><img src="index.php?dn=thumb&amp;type=video&amp;id='.$item['id'].'&amp;x=36&amp;h=27&amp;r=yes&amp;ops='.$sess['hash'].'" alt="'.$lang['file_view'].'" /></a>';
				} else {
					echo '		<img src="index.php?dn=thumb&amp;type=video&amp;id='.$item['id'].'&amp;x=36&amp;h=27&amp;r=yes&amp;ops='.$sess['hash'].'">';
				}
				echo '		</td>
							<td class="'.$style.' gov pw10">
								<a href="index.php?dn=videoedit&amp;id='.$item['id'].'&amp;fid='.$id.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/edit.png" alt="'.$lang['all_edit'].'" /></a>';
				if ($item['act'] == 'yes') {
					echo '		<a href="index.php?dn=videoact&amp;act=no&amp;id='.$item['id'].'&amp;fid='.$id.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'].'"><img alt="'.$lang['not_included'].'" src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/act.png"></a>';
				} else {
					echo '		<a class="inact" href="index.php?dn=videoact&amp;act=yes&amp;id='.$item['id'].'&amp;fid='.$id.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'].'"><img alt="'.$lang['included'].'" src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/act.png"></a>';
				}
					echo '		<a href="index.php?dn=videodel&amp;id='.$item['id'].'&amp;fid='.$id.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/del.png" alt="'.$lang['all_delet'].'" /></a>
							</td>
							<td class="'.$style.' mark pw5"><input type="checkbox" name="array['.$item['id'].']" value="'.$id.'"></td>
						</tr>';
			}
			echo '		<tr>
							<td colspan="7">'.$lang['all_mark_work'].':&nbsp;
								<select name="workname">
									<option value="pos" selected>'.$lang['save_posit'].'</option>
									<option value="act">'.$lang['included'].'&nbsp; &#8260; &nbsp;'.$lang['not_included'].'</option>
									<option value="del">'.$lang['all_delet'].'</option>
								</select>
								<input type="hidden" name="ops" value="'.$sess['hash'].'">
								<input type="hidden" name="dn" value="workvideo">
								<input type="hidden" name="p" value="'.$p.'">
								<input type="hidden" name="id" value="'.$id.'">
								<input type="hidden" name="nu" value="'.$nu.'">
								<input id="button" class="side-button" value="'.$lang['all_go'].'" type="submit">
							</td>
						</tr>
						<tr><td colspan="7">'.$pages.'</td></tr>
					</table>
					</form>
					</div>
					<div class="sline"></div>
					<div class="section">
					<form action="index.php" method="post" id="form-add">
					<table class="work">
						<caption>'.$lang['video_add'].'</caption>
						<tr>
							<th></th>
							<th>
								<div class="tabs" id="tabs">
									<a href="#" data-tabs=".tab-1">'.$lang['home'].'</a>
									<a href="#" data-tabs=".tab-2" style="display: none;"></a>
									<a href="#" data-tabs="all">'.$lang['all_field'].'</a>
								</div>
							</th>
						</tr>
						<tbody class="tab-1">
						<tr>
							<td class="first"><span>*</span> '.$lang['video_link'].'</td>
							<td>
								<input name="link" id="link" size="70" type="text" placeholder="http://youtu.be/xxxxxxxxxxx" required="required" />';
								$tm->outhint($lang['video_help']);
			echo '			</td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['all_name'].'</td>
							<td>
								<input name="title" id="title" size="70" type="text" />';
								$tm->outhint($lang['video_help_title']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['descript'].'</td>
							<td class="usewys">';
			if ($wysiwyg == 'yes')
			{
				define("USEWYS", 1);
				$form_short = 'descript';
				$WYSFORM = 'descript';
				$WYSVALUE = '';
				include(ADMDIR.'/includes/wysiwyg.php');
			}
			else
			{
				$tm->textarea('descript', 4, 70, '', 1);
			}
			echo '				</td>
						</tr>
						<tr>
							<td>'.$lang['all_image'].'</td>
							<td>
								<input name="image" id="image" size="70" type="text" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/video/\',\'&amp;field[1]=image\')" value="'.$lang['filebrowser'].'" type="button" />';
								$tm->outhint($lang['video_help_photo']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_status'].'</td>
							<td>
								<select name="act" class="sw165">
									<option value="yes">'.$lang['included'].' </option>
									<option value="no">'.$lang['not_included'].'</option>
								</select>
							</td>
						</tr>
						</tbody>
						<tr class="tfoot">
							<td></td>
							<td class="al">
								<input type="hidden" name="dn" value="videoaddsave">
								<input type="hidden" name="id" value="'.$id.'">
								<input type="hidden" name="ops" value="'.$sess['hash'].'">
								<input type="hidden" name="admid" value="'.$ADMIN_ID.'">
								<input class="main-button" value="'.$lang['all_submint'].'" type="submit">
							</td>
						</tr>
					</table>
					</form>
					</div><div id="load"></div>';

			echo "	<script>
						$(document).ready(function() {
							$('#tabs a').tabs('.tab-1');
						});
					</script>";

			if (preparse($ajax, THIS_INT) == 0)
			{
				echo '</div>';
				$tm->footer();
			}
		}

		/**
		 * Добавить видео (сохранение)
		 -------------------------------*/
		if ($_REQUEST['dn'] == 'videoaddsave')
		{
			global $id, $link, $title, $descript, $image, $act;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=video&amp;id='.$id.'&amp;ops='.$sess['hash'].'">'.$lang['video_album'].'</a>',
					$lang['video_add']
				);

			$id = preparse($id, THIS_INT);

			if (
				preparse($id, THIS_EMPTY) == 1 OR
				preparse($link, THIS_EMPTY) == 1
			) {
				$tm->header();
				$tm->error($modname[PERMISS], $lang['video_add'], $lang['forgot_name']);
				$tm->footer();
			}
			else
			{
				$vd = new Video($link);

				if (preparse($vd->video, THIS_EMPTY) == 1)
				{
					$tm->header();
					$tm->error($lang['video_add'], $item['title'], $lang['video_error']);
					$tm->footer();
				}

				if (preparse($image, THIS_EMPTY) == 1)
				{
					$img = new Image;
					$img->url_thumb($vd->image, DNDIR.'up/'.PERMISS.'/video/');
					$image = 'up/'.PERMISS.'/video/'.$img->thumb;
				}
				else
				{
					$extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
					if ( ! in_array($extension, array('gif', 'jpg', 'jpeg', 'png')) )
					{
						$tm->header();
						$tm->error($modname[PERMISS], $lang['video_add'], $lang['photo_error_type']);
						$tm->footer();
					}
				}

				$max = $db->fetchassoc($db->query("SELECT MAX(posit) + 1 AS posit FROM ".$basepref."_".PERMISS."_video WHERE firm_id = '".$id."'"));
				$db->query("UPDATE ".$basepref."_".PERMISS." SET videos = videos + 1 WHERE id = '".$id."'");

				$title = (preparse($title, THIS_EMPTY) == 1) ? $vd->title : $title;
				$descript = preparse($descript, THIS_TRIM);
				$video = preparse($vd->video, THIS_TRIM, 0, 255);
				$image = preparse($image, THIS_TRIM, 0, 255);
				$posit = preparse($max['posit'], THIS_INT);
				$act = ($act == 'yes') ? 'yes' : 'no';

				$db->query
					(
						"INSERT INTO ".$basepref."_".PERMISS."_video VALUES (
						 NULL,
						 '".$id."',
						 '".NEWTIME."',
						 '".$db->escape(preparse_sp($title))."',
						 '".$db->escape(preparse_sp($descript))."',
						 '".$db->escape($link)."',
						 '".$db->escape($video)."',
						 '".$db->escape($image)."',
						 '".$posit."',
						 '".$act."'
						 )"
					);

				redirect('index.php?dn=video&amp;id='.$id.'&amp;ops='.$sess['hash']);
			}
		}

		/**
		 * Редактировать видео
		 -----------------------*/
		if ($_REQUEST['dn'] == 'videoedit')
		{
			global $id, $fid, $p, $nu;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=video&id='.$fid.'&amp;ops='.$sess['hash'].'">'.$lang['video_album'].'</a>',
					$lang['video_edit']
				);

			$id = preparse($id, THIS_INT);
			$fid = preparse($fid, THIS_INT);

			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_video WHERE id = '".$id."'"));

			$tm->header();

			echo '	<script>
					$(window).bind("pageshow", function(event) {
						if (event.originalEvent.persisted) {
							$("#load").empty();
							window.location.reload();
						}
					});
					$(function() {
						$("#form-add").one("submit", function() {
							var link = $("#link").val();
							if(link.length > 0) {
								$("#load").html(\'<div class="save"></div>\');
							}
						});
					});
					</script>';
			echo '	<div class="section">
					<form action="index.php" method="post" id="form-add">
					<table class="work">
						<caption>'.$lang['video_edit'].': '.preparse_un($item['title']).'</caption>
						<tr>
							<td class="first"><span>*</span> '.$lang['video_link'].'</td>
							<td>
								<input name="link" id="link" size="70" type="text" value="'.preparse_un($item['link']).'" placeholder="http://youtu.be/xxxxxxxxxxx" required="required" />';
								$tm->outhint($lang['video_help']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_name'].'</td>
							<td><input id="title" type="text" name="title" size="70" value="'.preparse_un($item['title']).'">';
								$tm->outhint($lang['video_help_title']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['descript'].'</td>
							<td class="usewys">';
			if ($wysiwyg == 'yes')
			{
				define("USEWYS", 1);
				$form_short = 'descript';
				$WYSFORM = 'descript';
				$WYSVALUE = $item['descript'];
				include(ADMDIR.'/includes/wysiwyg.php');
			}
			else
			{
				$tm->textarea('descript', 4, 70, $item['descript'], 1);
			}
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_image'].'</td>
							<td>
								<input name="image" id="image" size="70" type="text" value="'.$item['image'].'" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/video/\',\'&amp;field[1]=image\')" value="'.$lang['filebrowser'].'" type="button" />';
								$tm->outhint($lang['video_help_photo']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_status'].'</td>
							<td>
								<select name="act" class="sw165">
									<option value="yes"'.(($item['act'] == 'yes') ? ' selected' : '').'>'.$lang['included'].' </option>
									<option value="no"'.(($item['act'] == 'no')  ? ' selected' : '').'>'.$lang['not_included'].'</option>
								</select>
							</td>
						</tr>
						<tr class="tfoot">
							<td colspan="2">
								<input type="hidden" name="ops" value="'.$sess['hash'].'">
								<input type="hidden" name="fid" value="'.$fid.'">
								<input type="hidden" name="id" value="'.$id.'">
								<input type="hidden" name="p" value="'.$p.'">
								<input type="hidden" name="nu" value="'.$nu.'">
								<input type="hidden" name="dn" value="videoeditsave">
								<input class="main-button" value="'.$lang['all_save'].'" type="submit">
							</td>
						</tr>
					</table>
					</form>
					</div><div id="load"></div>';

			echo "	<script>
						$(document).ready(function() {
							$('#tabs a').tabs('.tab-1');
						});
					</script>";

			$tm->footer();
		}

		/**
		 * Редактировать видео (сохранение)
		 -----------------------------------*/
		if ($_REQUEST['dn'] == 'videoeditsave')
		{
			global $fid, $id, $act, $link, $title, $descript, $image, $p, $nu;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=video&id='.$fid.'&amp;ops='.$sess['hash'].'">'.$lang['video_album'].'</a>',
					$lang['video_edit']
				);

			$id = preparse($id, THIS_INT);
			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_video WHERE id = '".$id."'"));

			if (
				preparse($link, THIS_EMPTY) == 1 OR
				preparse($fid, THIS_EMPTY) == 1 OR
				preparse($id, THIS_EMPTY) == 1
			) {
				$tm->header();
				$tm->error($lang['video_edit'], $item['title'], $lang['forgot_name']);
				$tm->footer();
			}
			else
			{
				$vd = new Video($link);
				if ($link <> $item['link'])
				{
					if (preparse($vd->video, THIS_EMPTY) == 1)
					{
						$tm->header();
						$tm->error($lang['video_edit'], $item['title'], $lang['video_error']);
						$tm->footer();
					}
					$video = $vd->video;
				}
				else
				{
					$video = $item['video'];
				}

				if (preparse($image, THIS_EMPTY) == 1)
				{
					$img = new Image;
					$img->url_thumb($vd->image, DNDIR.'up/'.PERMISS.'/video/');
					$image = 'up/'.PERMISS.'/video/'.$img->thumb;

					if (file_exists(DNDIR.$item['image']))
					{
						unlink(DNDIR.$item['image']);
					}
				}
				else
				{
					$extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
					if ( ! in_array($extension, array('gif', 'jpg', 'jpeg', 'png')) )
					{
						$tm->header();
						$tm->error($lang['video_edit'], $item['title'], $lang['error_type']);
						$tm->footer();
					}

					if ($image <> $item['image'])
					{
						if (file_exists(DNDIR.$item['image']))
						{
							unlink(DNDIR.$item['image']);
						}
					}
				}

				$title = (preparse($title, THIS_EMPTY) == 1) ? $vd->title : $title;
				$descript = preparse($descript, THIS_TRIM);
				$image = preparse($image, THIS_TRIM, 0, 255);
				$act = ($act == 'yes') ? 'yes' : 'no';

				$db->query
					(
						"UPDATE ".$basepref."_".PERMISS."_video SET
						 title    = '".$db->escape(preparse_sp($title))."',
						 descript = '".$db->escape($descript)."',
						 link     = '".$db->escape($link)."',
						 video    = '".$db->escape($video)."',
						 image    = '".$db->escape($image)."',
						 act      = '".$act."'
						 WHERE id = '".$id."'"
					);

				$redir = 'index.php?dn=video';
				$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
				$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
				$redir.= ( ! empty($fid)) ? '&amp;id='.preparse($fid, THIS_INT) : '';
				$redir.= '&amp;ops='.$sess['hash'];

				redirect($redir);
			}
		}

		/**
		 * Изменение состояния видео (вкл./выкл.)
		 ----------------------------------------*/
		if ($_REQUEST['dn'] == 'videoact')
		{
			global $id, $fid, $act, $p, $nu;

			$id = preparse($id, THIS_INT);
			$act = preparse($act, THIS_TRIM);

			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_video WHERE id = '".$id."'"));

			if ($act == 'no' OR $act == 'yes')
			{
				$db->query("UPDATE ".$basepref."_".PERMISS."_video SET act = '".$act."' WHERE id = '".$id."'");
			}

			$sign = ($act == 'yes' AND $item['act'] == 'no') ? "+" : "-";
			$db->query("UPDATE ".$basepref."_".PERMISS." SET videos = videos ".$sign." 1 WHERE id = '".$fid."'");

			$redir = 'index.php?dn=video&amp;ops='.$sess['hash'];
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= ( ! empty($fid)) ? '&amp;id='.preparse($fid, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Удалить видео
		 --------------------------*/
		if ($_REQUEST['dn'] == 'videodel')
		{
			global $ok, $id, $fid, $p, $nu;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=video&id='.$fid.'&amp;ops='.$sess['hash'].'">'.$lang['video_album'].'</a>',
					$lang['video_del']
				);

			$id = preparse($id, THIS_INT);
			$fid = preparse($fid, THIS_INT);

			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_video WHERE id = '".$id."'"));

			if ($ok == 'yes')
			{
				$db->query("UPDATE ".$basepref."_".PERMISS." SET videos = videos - 1 WHERE id = '".$fid."'");
				$db->query("DELETE FROM ".$basepref."_".PERMISS."_video WHERE id = '".$id."'");

				if (file_exists(DNDIR.$item['image']))
				{
					unlink(DNDIR.$item['image']);
				}

				$redir = 'index.php?dn=video&amp;ops='.$sess['hash'];
				$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
				$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
				$redir.= ( ! empty($fid)) ? '&amp;id='.preparse($fid, THIS_INT) : '';

				redirect($redir);
			}
			else
			{
				$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_video WHERE id = '".$id."'"));

				$yes = 'index.php?dn=videodel&amp;id='.$id.'&amp;fid='.$fid.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;id='.$id.'&amp;ok=yes&amp;ops='.$sess['hash'];
				$not = 'index.php?dn=video&amp;id='.$id.'&amp;fid='.$fid.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'];

				$tm->header();
				$tm->shortdel($lang['video_del'], preparse_un($item['title']), $yes, $not);
				$tm->footer();
			}
		}

		/**
		 * Массовая обработка видео
		 ---------------------------*/
		if ($_REQUEST['dn'] == 'workvideo')
		{
			global $array, $workname, $posit, $p, $id, $nu;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=video&amp;id='.$id.'&amp;ops='.$sess['hash'].'">'.$lang['video_album'].'</a>',
					$lang['array_control']
				);

			if (preparse($array, THIS_ARRAY) == 1)
			{
				$hidden = '';
				foreach ($array as $key => $val) {
					$hidden.= '<input type="hidden" name="array['.$key.']" value="'.$val.'">';
				}
				$p = preparse($p, THIS_INT);
				$id = preparse($id, THIS_INT);
				$nu = preparse($nu, THIS_INT);
				$h = '<input type="hidden" name="p" value="'.$p.'">'
					.'<input type="hidden" name="nu" value="'.$nu.'">'
					.'<input type="hidden" name="id" value="'.$id.'">'
					.'<input type="hidden" name="ops" value="'.$sess['hash'].'">';

				$count = count($array);

				// Удаление
				if ($workname == 'del')
				{
					$tm->header();
					echo '	<div class="section">
							<form action="index.php" method="post">
							<table id="arr-work" class="work">
								<caption>'.$lang['array_control'].': '.$lang['all_delet'].' ('.$count.')</caption>
								<tr><td class="cont">'.$lang['alertdel'].'</td></tr>
								<tr class="tfoot">
									<td>
										<input type="hidden" name="ops" value="'.$sess['hash'].'">
										'.$hidden.'
										'.$h.'
										<input type="hidden" name="dn" value="videoarrdel">
										<input class="side-button" value="'.$lang['all_go'].'" type="submit">
										<input class="side-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button">
									</td>
								</tr>
							</table>
							</form>
							</div>';
					$tm->footer();

				// Активация
				}
				elseif ($workname == 'act')
				{
					$tm->header();
					echo '	<div class="section">
							<form action="index.php" method="post">
							<table id="arr-work" class="work">
								<caption>'.$lang['array_control'].': '.$lang['all_status'].' ('.$count.')</caption>
								<tr>
									<td class="cont">
										<select name="act">
											<option value="yes">'.$lang['included'].'</option>
											<option value="no">'.$lang['not_included'].'</option>
										</select>
									</td>
								</tr>
								<tr class="tfoot">
									<td>
										'.$hidden.'
										'.$h.'
										<input type="hidden" name="dn" value="videoarract">
										<input class="side-button" value="'.$lang['all_go'].'" type="submit">
										<input class="side-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button">
									</td>
								</tr>
							</table>
							</form>
							</div>';
					$tm->footer();

				// Позиции
				}
				elseif ($workname == 'pos')
				{
					if (preparse($posit, THIS_ARRAY) == 1)
					{
						while (list($fid, $val) = each($posit))
						{
							$fid = preparse($fid, THIS_INT);
							$db->query("UPDATE ".$basepref."_".PERMISS."_video SET posit = '".intval($val)."' WHERE id = '".intval($fid)."'");
						}
					}
				}
			}

			$redir = 'index.php?dn=video&amp;ops='.$sess['hash'];
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= ( ! empty($id)) ? '&amp;id='.preparse($id, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Массовое удаление видео (сохранение)
		 --------------------------------------*/
		if ($_REQUEST['dn'] == 'videoarrdel')
		{
			global $array, $p, $id, $nu;

			$id = preparse($id, THIS_INT);

			if (is_array($array) AND ! empty($array))
			{
				while (list($vid, $fid) = each($array))
				{
					$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_video WHERE id = '".$vid."'"));

					$db->query("UPDATE ".$basepref."_".PERMISS." SET videos = videos - 1 WHERE id = '".$fid."'");
					$db->query("DELETE FROM ".$basepref."_".PERMISS."_video WHERE id = '".$vid."'");

					if (file_exists(DNDIR.$item['image']))
					{
						unlink(DNDIR.$item['image']);
					}
				}
			}

			$counts = new Counts(PERMISS, 'id');
			$cache->cachesave(1);

			$redir = 'index.php?dn=video&amp;ops='.$sess['hash'];
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= ( ! empty($id)) ? '&amp;id='.preparse($id, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Массовая активация видео (сохранение)
		 ---------------------------------------*/
		if ($_REQUEST['dn'] == 'videoarract')
		{
			global $array, $p, $id, $act, $nu;

			$id = preparse($id, THIS_INT);

			if (preparse($array, THIS_ARRAY) == 1)
			{
				while (list($vid, $fid) = each($array))
				{
					$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_video WHERE id = '".$vid."'"));

					$sign = ($act == 'yes' AND $item['act'] == 'no') ? "+" : "-";
					$db->query("UPDATE ".$basepref."_".PERMISS." SET videos = videos ".$sign." 1 WHERE id = '".$fid."'");
					$db->query("UPDATE ".$basepref."_".PERMISS."_video SET act = '".$act."' WHERE id = '".$vid."'");
				}
			}

			$counts = new Counts(PERMISS, 'id');
			$cache->cachesave(1);

			$redir = 'index.php?dn=video&amp;ops='.$sess['hash'];
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= ( ! empty($id)) ? '&amp;id='.preparse($id, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Массовая обработка
		 -----------------------*/
		if ($_REQUEST['dn'] == 'work')
		{
			global $array, $workname, $selective, $p, $cat, $nu, $s, $l, $fid;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>',
					$lang['array_control']
				);

			if (preparse($array, THIS_ARRAY) == 1)
			{
				$temparray = $array;
				$count = count($temparray);
				$hidden = '';
				foreach ($array as $key => $id) {
					$hidden.= '<input type="hidden" name="array['.$key.']" value="yes" />';
				}
				$p = preparse($p, THIS_INT);
				$s = preparse($s, THIS_TRIM, 1, 7);
				$l = preparse($l, THIS_TRIM, 1, 4);
				$nu = preparse($nu, THIS_INT);
				$cat = preparse($cat, THIS_INT);
				$fid = preparse($fid, THIS_INT);
				$h = '	<input type="hidden" name="p" value="'.$p.'" />
						<input type="hidden" name="cat" value="'.$cat.'" />
						<input type="hidden" name="nu" value="'.$nu.'" />
						<input type="hidden" name="s" value="'.$s.'" />
						<input type="hidden" name="l" value="'.$l.'" />
						<input type="hidden" name="fid" value="'.$fid.'" />
						<input type="hidden" name="ops" value="'.$sess['hash'].'" />';

				// Удаление
				if ($workname == 'del')
				{
					$tm->header();
					echo '	<div class="section">
								<form action="index.php" method="post">
								<table id="arr-work" class="work">
								<caption>'.$lang['array_control'].': '.$lang['all_delet'].' ('.$count.')</caption>
									<tr>
										<td class="cont">'.$lang['alertdel'].'</td>
									</tr>
									<tr class="tfoot">
										<td>
											<input type="hidden" name="ops" value="'.$sess['hash'].'" />
											'.$hidden.'
											'.$h.'
											<input type="hidden" name="dn" value="arrdel" />
											<input class="main-button" value="'.$lang['all_go'].'" type="submit" />
											<input class="main-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button" />
										</td>
									</tr>
								</table>
								</form>';
					$tm->footer();

				// Перемещение
				}
				elseif ($workname == 'move')
				{
					$tm->header();

					if ($conf[PERMISS]['multcat'] == 'yes')
					{
						redirect('index.php?dn=list&amp;ops='.$sess['hash']);
					}

					$inquiry = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");
					$catcache = array();
					while ($item = $db->fetchassoc($inquiry)) {
						$catcache[$item['parentid']][$item['catid']] = $item;
					}
					this_selectcat(0);
					echo '	<div class="section">
							<form action="index.php" method="post">
							<table id="arr-work" class="work">
								<caption>'.$lang['array_control'].': '.$lang['all_move'].' '.$lang['all_in_cat'].' ('.$count.')</caption>
								<tr>
									<td class="cont">
										<select name="catid">
											<option value="0">'.$lang['cat_not'].'</option>
											'.$selective.'
										</select>
									</td>
								</tr>
								<tr class="tfoot">
									<td>
										<input type="hidden" name="ops" value="'.$sess['hash'].'" />
										'.$hidden.'
										'.$h.'
										<input type="hidden" name="dn" value="arrmove" />
										<input class="side-button" value="'.$lang['all_go'].'" type="submit" />
										<input class="side-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button" />
									</td>
								</tr>
							</table>
							</form>
							</div>';

					$tm->footer();

				// Активация
                }
				elseif ($workname == 'active')
				{
					$tm->header();

					echo '	<div class="section">
							<form action="index.php" method="post">
							<table id="arr-work" class="work">
								<caption>'.$lang['array_control'].': '.$lang['all_status'].' ('.$count.')</caption>
								<tr>
									<td class="cont">
										<select name="act">
											<option value="yes">'.$lang['included'].'</option>
											<option value="no">'.$lang['not_included'].'</option>
										</select>
									</td>
								</tr>
								<tr class="tfoot">
									<td>
										'.$hidden.'
										'.$h.'
										<input type="hidden" name="dn" value="arract" />
										<input class="side-button" value="'.$lang['all_go'].'" type="submit" />
										<input class="side-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button" />
									</td>
								</tr>
							</table>
							</form>
							</div>';

					$tm->footer();

				// Доступ
				}
				elseif (
					$workname == 'access' AND
					isset($conf['user']['regtype']) AND
					$conf['user']['regtype'] == 'yes'
				)
				{
					$tm->header();

					echo '	<div class="section">
							<form action="index.php" method="post">
							<table id="arr-work" class="work">
								<caption>'.$lang['array_control'].': '.$lang['all_access'].' ('.$count.')</caption>
								<tr>
									<td class="cont">
										<select name="acc">
											<option value="all">'.$lang['all_all'].'</option>
											<option value="user">'.$lang['all_user_only'].'</option>
										</select>
									</td>
								</tr>
								<tr class="tfoot">
									<td>
										'.$hidden.'
										'.$h.'
										<input type="hidden" name="dn" value="arracc" />
										<input class="side-button" value="'.$lang['all_go'].'" type="submit" />
										<input class="side-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button" />
									</td>
								</tr>
							</table>
							</form>
							</div>';

					$tm->footer();
				}
			}

			redirect('index.php?dn=list&amp;ops='.$sess['hash']);
		}

		/**
		 * Массовое удаление (сохранение)
		 ----------------------------------*/
		if ($_REQUEST['dn'] == 'arrdel')
		{
			global $array, $p, $cat, $nu, $s, $l, $fid;

			$fid = preparse($fid, THIS_INT);

			if (is_array($array) AND ! empty($array))
			{
				while (list($id) = each($array))
				{
					if ($conf[PERMISS]['multcat'] == 'yes')
					{
						$counts = new Counts(PERMISS, 'id', 0);
						$counts->del($id);
					}

					$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'"));
					if ( ! empty($item['image_thumb']))
					{
						unlink(WORKDIR.'/'.$item['image']);
						unlink(WORKDIR.'/'.$item['image_thumb']);
					}

					$db->query("DELETE FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'");
					$db->query("DELETE FROM ".$basepref."_reviews WHERE file = '".PERMISS."' AND pageid = '".$id."'");
					$db->query("DELETE FROM ".$basepref."_rating WHERE file = '".PERMISS."' AND id = '".$id."'");

					$inq = $db->query("SELECT * FROM ".$basepref."_".PERMISS."_photo WHERE firm_id = '".$id."'");
					if ($db->numrows($inq) > 0)
					{
						while ($photo = $db->fetchassoc($inq))
						{
							$db->query("DELETE FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$photo['id']."'");

							if (file_exists(DNDIR.$photo['image_thumb']))
							{
								unlink(DNDIR.$photo['image']);
								unlink(DNDIR.$photo['image_thumb']);
							}
						}
					}

					$inqs = $db->query("SELECT * FROM ".$basepref."_".PERMISS."_video WHERE firm_id = '".$id."'");
					if ($db->numrows($inqs) > 0)
					{
						while ($video = $db->fetchassoc($inqs))
						{
							$db->query("DELETE FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$video['id']."'");

							if (file_exists(DNDIR.$video['image']))
							{
								unlink(DNDIR.$video['image']);
							}
						}
					}
				}

				if ($conf[PERMISS]['multcat'] == 'no')
				{
					$counts = new Counts(PERMISS, 'id');
				}
			}

			$cache->cachesave(1);

			$redir = 'index.php?dn=list&amp;ops='.$sess['hash'];
			$redir.= (!empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= (!empty($cat)) ? '&amp;cat='.preparse($cat, THIS_INT) : '';
			$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= (!empty($s)) ? '&amp;s='.$s : '';
			$redir.= (!empty($l)) ? '&amp;l='.$l : '';
			$redir.= ($fid > 0) ? '&amp;fid='.$fid : '';

			redirect($redir);
		}

		/**
		 * Массовое перемещение (сохранение)
		 ------------------------------------*/
		if ($_REQUEST['dn'] == 'arrmove')
		{
			global $catid, $array, $p, $cat, $nu, $s, $l, $fid, $conf;

			if ($conf[PERMISS]['multcat'] == 'yes')
			{
				redirect('index.php?dn=list&amp;ops='.$sess['hash']);
			}

			$fid = preparse($fid, THIS_INT);
			$catid = preparse($catid, THIS_INT);

			allarrmove($array, $catid, PERMISS);

			$counts = new Counts(PERMISS, 'id');
			$cache->cachesave(1);

			$redir = 'index.php?dn=list&amp;ops='.$sess['hash'];
			$redir.= (!empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= (!empty($cat)) ? '&amp;cat='.preparse($cat, THIS_INT) : '';
			$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= (!empty($s)) ? '&amp;s='.$s : '';
			$redir.= (!empty($l)) ? '&amp;l='.$l : '';
			$redir.= ($fid > 0) ? '&amp;fid='.$fid : '';

			redirect($redir);
		}

		/**
		 * Массовая активация (сохранение)
		 ------------------------------------*/
		if ($_REQUEST['dn'] == 'arract')
		{
			global $array, $act, $p, $cat, $nu, $s, $l, $fid;

			$fid = preparse($fid, THIS_INT);
			$act = ($act == 'yes') ? 'yes' : 'no';

			if (is_array($array) AND ! empty($array))
			{
				if ($conf[PERMISS]['multcat'] == 'yes')
				{
					while (list($id) = each($array))
					{
						$counts = new Counts(PERMISS, 'id', 0);
						$counts->act($id, $act);
					}
					allarract($array, 'id', PERMISS, $act);
				}
				else
				{
					allarract($array, 'id', PERMISS, $act);
					$counts = new Counts(PERMISS, 'id');
				}
			}

			$cache->cachesave(1);

			$redir = 'index.php?dn=list&amp;ops='.$sess['hash'];
			$redir.= (!empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= (!empty($cat)) ? '&amp;cat='.preparse($cat, THIS_INT) : '';
			$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= (!empty($s)) ? '&amp;s='.$s : '';
			$redir.= (!empty($l)) ? '&amp;l='.$l : '';
			$redir.= ($fid > 0) ? '&amp;fid='.$fid : '';

			redirect($redir);
		}

		/**
		 * Массовое изменение доступа (сохранение)
		 ------------------------------------*/
		if ($_REQUEST['dn'] == 'arracc')
		{
			global $array, $acc, $p, $cat, $nu, $s, $l, $fid;

			if (preparse($array, THIS_ARRAY) == 1)
			{
				$acc = ($acc == 'all') ? 'all' : 'user';
				while (list($id) = each($array))
				{
					$id = preparse($id, THIS_INT);
					$db->query("UPDATE ".$basepref."_".PERMISS." SET acc = '".$acc."' WHERE id = '".$id."'");
				}
			}

			$cache->cachesave(1);
			$fid = preparse($fid, THIS_INT);

			$redir = 'index.php?dn=list&amp;ops='.$sess['hash'];
			$redir.= (!empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= (!empty($cat)) ? '&amp;cat='.preparse($cat, THIS_INT) : '';
			$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= (!empty($s)) ? '&amp;s='.$s : '';
			$redir.= (!empty($l)) ? '&amp;l='.$l : '';
			$redir.= ($fid > 0) ? '&amp;fid='.$fid : '';

			redirect($redir);
		}

		/**
		 * Добавить организацию
		 -------------------------*/
		if ($_REQUEST['dn'] == 'add')
		{
			global $catid, $selective, $lang;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>',
					$lang['firms_add']
				);

			$tm->header();

			$time = CalendarFormat(NEWTIME);
			$inqcat = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");

			echo '	<script src="'.ADMPATH.'/js/jquery.apanel.tabs.js"></script>
					<script>
						var all_name     = "'.$lang['all_name'].'";
						var all_cpu      = "'.$lang['all_cpu'].'";
						var all_popul    = "'.$lang['all_popul'].'";
						var all_thumb    = "'.$lang['all_image_thumb'].'";
						var all_img      = "'.$lang['all_image'].'";
						var all_images   = "'.$lang['all_image_big'].'";
						var all_align    = "'.$lang['all_align'].'";
						var all_right    = "'.$lang['all_right'].'";
						var all_left     = "'.$lang['all_left'].'";
						var all_center   = "'.$lang['all_center'].'";
						var all_alt      = "'.$lang['all_alt_image'].'";
						var all_copy     = "'.$lang['all_copy'].'";
						var all_delet    = "'.$lang['all_delet'].'";
						var code_paste   = "'.$lang['code_paste'].'";
						var all_file     = "'.$lang['all_file'].'";
						var all_path     = "'.$lang['all_path'].'";
						var page_one     = "'.$lang['page_one'].'";
						var ops          = "'.$sess['hash'].'";
						var filebrowser  = "'.$lang['filebrowser'].'";
						var filereview   = "'.$lang['file_review'].'";
						var page         = "'.PERMISS.'";
						$(function() {
							$(".imgcount").focus(function () {
								$(this).select();
							}).mouseup(function(e){
								e.preventDefault();
							});
							$("#facc").bind("change", function() {
								if ($(this).val() == "group") {
									$("#fgroup").slideDown();
								} else {
									$("#fgroup").slideUp();
								}
							});
						});
					</script>';

			echo "	<script>
						$(function() {
							$.addfiles = function(form, area, path) {
								var id = $('#fileid').attr('value');
								if (id) {
									var html = '<div class=\"section tag\" id=\"file-' + id + '\" style=\"display: none;\">';
									html += '<table class=\"work\"><tr>';
									html += '<td class=\"vm sw150\">' + all_path + '</td>';
									html += '<td>';
									html += '<input name=\"files[' + id + '][path]\" id=\"files' + id + '\" size=\"50\" type=\"text\" required>&nbsp;';
									html += '<input class=\"side-button\" onclick=\"javascript:$.filebrowser(\'' + ops + '\',\'' + path + '\',\'&amp;field[1]=files' + id + '\')\" value=\"' + filebrowser + '\" type=\"button\"> ';
									html += '<input class=\"side-button\" onclick=\"javascript:$.fileallupload(\'' + ops + '&amp;objdir=' + path + '\',\'&amp;field=files' + id + '\')\" value=\"' + filereview + '\" type=\"button\">';
									html += '<a class=\"but fr\" href=\"javascript:$.removetaginput(\'total-form\',\'file-area\',\'file-' + id + '\');\">&#215;</a>';
									html += '</td></tr><tr>';
									html += '<td class=\"vm sw150\">' + all_name + '</td>';
									html += '<td><input name=\"files[' + id + '][title]\" size=\"50\" type=\"text\" required></td>';
									html += '</tr></table></div>';
									$('form[id=' + form + '] #' + area).append(html);
									$('form[id=' + form + '] #' + area + ' #file-' + id).show('normal');
									id++;
									$('#fileid').attr({
										value: id
									});
								}
							}
							$.addsocial = function(form, area) {
								var id = $('#socialid').attr('value');
								if (id) {
									var html = '<div class=\"section tag\" id=\"social-' + id + '\" style=\"display: none;\">';
									html += '<table class=\"work\"><tr>';
									html += '<td class=\"vm sw150 al\"><input name=\"social[' + id + '][title]\" id=\"social' + id + '\" size=\"15\" type=\"text\" placeholder=\"' + all_name + '\" required></td>';
									html += '<td class=\"vm\"><input name=\"social[' + id + '][link]\" size=\"50\" type=\"text\" placeholder=\"' + page_one + '\" required>';
									html += '<a class=\"but fr\" href=\"javascript:$.removetaginput(\'total-form\',\'social-area\',\'social-' + id + '\');\">&#215;</a></td>';
									html += '</tr></table></div>';
									$('form[id=' + form + '] #' + area).append(html);
									$('form[id=' + form + '] #' + area + ' #social-' + id).show('normal');
									id++;
									$('#socialid').attr({
										value: id
									});
								}
							}
						});
					</script>";

			$tabs = '	<div class="tabs" id="tabs">
							<a href="#" data-tabs=".tab-1">'.$lang['home'].'</a>
							<a href="#" data-tabs=".tab-2" style="display: none;"></a>
							<a href="#" data-tabs="all">'.$lang['all_field'].'</a>
						</div>';

			echo '	<div class="section">
					<form action="index.php" method="post" id="total-form">
					<table class="work">
						<caption>'.$lang[PERMISS].': '.$lang['all_add'].'</caption>
						<tr>
							<th class="ar site">'.$lang['all_bookmark'].' &nbsp; </th>
							<th>'.$tabs.'</th>
						</tr>
						<tbody class="tab-1">
						<tr>
							<td class="first"><span>*</span> '.$lang['all_name'].'</td>
							<td>
								<input type="text" name="title" id="title" size="70" required="required" /> <span class="light">&lt;h1&gt;</span>
							</td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['sub_title'].'</td>
							<td><input type="text" name="subtitle" size="70" /> <span class="light">&lt;h2&gt;</span></td>
						</tr>';
			if ($conf['cpu'] == 'yes') {
			echo '		<tr>
							<td>'.$lang['all_cpu'].'</td>
							<td><input type="text" name="cpu" id="cpu" size="70" />';
								$tm->outtranslit('title', 'cpu', $lang['cpu_int_hint']);
			echo '			</td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['custom_title'].'</td>
							<td><input type="text" name="customs" size="70" /> <span class="light">&lt;title&gt;</span></td>
						</tr>
						<tr>
							<td>'.$lang['all_descript'].'</td>
							<td><input type="text" name="descript" size="70" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_keywords'].'</td>
							<td><input type="text" name="keywords" size="70" />';
								$tm->outhint($lang['keyword_hint']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_data'].'</td>
							<td><input type="text" name="public" id="public" value="'.$time.'" />';
								Calendar('cal', 'public');
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_stpublic'].'</td>
							<td><input type="text" name="stpublic" id="stpublic" />';
								Calendar('stcal', 'stpublic');
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_unpublic'].'</td>
							<td><input type="text" name="unpublic" id="unpublic" />';
								Calendar('uncal', 'unpublic');
            echo '			</td>
						</tr>
						</tbody>
						<tbody class="tab-1">';
			if ($db->numrows($inqcat) > 0 OR $conf[PERMISS]['letter'] == 'yes')
			{
				echo '    <tr><th></th><th class="site">'.$lang['all_cat'].'</th></tr>';
			}
			if ($db->numrows($inqcat) > 0)
			{
				echo '
						<tr>
							<td>'.$lang['cat_click'].'</td>
							<td>
								<select id="catid" name="catid" class="sw350">
									<option value=""> &#8212; '.$lang['cat_not'].' &#8212; </option>';
			$inquiry = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");
			$catcache = array();
			while ($item = $db->fetchassoc($inquiry)) {
				$catcache[$item['parentid']][$item['catid']] = $item;
			}
			this_selectcat(0);
			echo '					'.$selective.'
								</select>
							</td>
						</tr>';
			}
				echo '	</tbody>
						<tbody class="tab-2">';

			if ($conf[PERMISS]['letter'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['all_letter'].'</td>
							<td>
								<select name="letid" class="sw350">
									<option value="0"> &#8213; </option>';
				foreach($letter as $k => $v) {
					echo '			<option value="'.$k.'"> '.$v.' </option>';
				}
				echo '			</select>
							</td>
						</tr>';
			}
			if ($db->numrows($inqcat) > 0 AND $conf[PERMISS]['multcat'] == 'yes')
			{
				echo '	<script>
						function inCat(el){
							$(el).prepend(\'<option value="0">&#8212;</option>\');
							$(el).find("option:not(:first)").remove().end().prop("disabled", true );
						}
						function getCat(catid, sess){
							$.ajax({
								cache:false,
								url: $.apanel + "mod/'.PERMISS.'/index.php",
								data:"dn=getcat&nocat=" + catid + "&ops=" +  sess,
								error:function(msg){},
								success:function(data) {
									if (data.length > 0 && data.match(/option/)) {
										$("#catin").html(data);
										$("#catin").prop("disabled", false );
									} else {
										inCat("#catin");
									}
								}
							});
						}
						$(function() {
							var sess = "'.$sess['hash'].'";
							var cl = $("#catid").val();
							(cl > 0) ? getCat(cl, sess) : inCat("#catin");
							$("#catid").on("change", function() {
								var cc = $(this).val();
								(cc > 0) ? getCat(cc, sess) : inCat("#catin");
							});
						});
						</script>';
				echo '	<tr>
							<th></th><th class="site">'.$lang['other_category'].'</th>
						</tr>
						<tr>
							<td class="vm">'.$lang['all_submint'].'&nbsp; &#8260; &nbsp;'.$lang['all_delet'].'</td>
							<td>
								<div id="tagarea">
								<table class="work">
									<tr>
										<td class="pw45">
											<select name="catin" id="catin" size="5" multiple class="blue pw100  app">';
				echo '							<option value="0">&#8212;</option>';
				echo '						</select>
										</td>
										<td class="ac vm pw10">
											<input class="side-button" type="button" onclick="$.addcat();" value="&#9658;" /><br /><br />
											<input class="side-button" type="button" onclick="$.delcat();" value="&#9668;" />
										</td>
										<td>
											<select name="catout" id="catout" size="5" multiple class="green pw100  app">
											</select>
											<div id="area-cats"></div>
										</td>
									</tr>
								</table>
								</div>
							</td>
						</tr>';
			}
			echo '		</tbody>
						<tbody class="tab-1">
						<tr><th></th><th class="site">'.$lang['all_location'].'</th></tr>
						<tr>
							<td class="first">'.$lang['country'].'</td>
							<td><input type="text" name="country" size="70" /></td>
						</tr>
						<tr>
							<td class="first">'.$lang['all_region'].'</td>
							<td><input type="text" name="region" size="70" /></td>
						</tr>
						<tr>
							<td class="first">'.$lang['all_city'].'</td>
							<td><input type="text" name="city" size="70" /></td>
						</tr>
						<tr>
							<td class="first">'.$lang['all_address'].'</td>
							<td><input type="text" name="address" size="70" placeholder="'.$lang['place_address'].'" /></td>
						</tr>
						<tr><th></th><th class="site">'.$lang['contact_data'].'</th></tr>
						<tr>
							<td>'.$lang['author'].'</td>
							<td><input type="text" name="author" size="70" /></td>
						</tr>
						<tr>
							<td>'.$lang['person'].'</td>
							<td><input type="text" name="person" size="70" /></td>
						</tr>
						<tr>
							<td>'.$lang['e_mail'].'</td>
							<td><input type="text" name="email" size="70" placeholder="info@site.ru" /></td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['phone'].'</td>
							<td><input type="text" name="phone" size="70" placeholder="+7 (xxx) xxx-xx-xx" />';
								$tm->outhint($lang['phone_help']);
			echo '			</td>
						</tr>
						<tr>
							<td>Skype</td>
							<td><input type="text" name="skype" size="70" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_site'].'</td>
							<td><input type="text" name="site" size="70" placeholder="http://" /></td>
						</tr>
						</tbody>
						<tbody class="tab-1">
						<tr><th></th><th class="site">'.$lang['all_content'].'</th></tr>
						<tr>
							<td class="first"><span>*</span> '.$lang['input_text'].'</td>
							<td class="usewys">';
			if ($wysiwyg == 'yes') {
				define("USEWYS", 1);
				$WYSFORM = 'textshort';
				$WYSVALUE = '';
				include(ADMDIR.'/includes/wysiwyg.php');
			} else {
				$tm->textarea('textshort', 4, 70, '', 1, '', '', 1);
			}
			echo '			</td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['full_text'].'</td>
							<td class="usewys">';
			if ($wysiwyg == 'yes') {
				$WYSFORM = 'textmore';
				$WYSVALUE = '';
				include(ADMDIR.'/includes/wysiwyg.php');
			} else {
				$tm->textarea('textmore', 7, 70, '', 1);
			}
			echo '			</td>
						</tr>';
			echo '		<tr>
							<td>'.$lang['img_extra_hint'].'</td>
							<td>
								<a class="side-button" href="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/image/attach/\',\'&amp;ims=1\');">'.$lang['filebrowser'].'</a>&nbsp;
								<a class="side-button" href="javascript:$.personalupload(\''.$sess['hash'].'&amp;objdir=/'.PERMISS.'/image/attach/\');">'.$lang['file_upload'].'</a>
								<div id="image-area"></div>
							</td>
						</tr>
						<tr><th></th><th class="site">'.$lang['all_info'].'</th></tr>';
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'yes') {
				echo '	<tr>
							<td>'.$lang['user_texts'].'</td>
							<td>';
								$tm->textarea('textnotice', 2, 70, '', true, false, 'ignorewysywig');
				echo '		</td>
						</tr>';
			}
			if($conf[PERMISS]['tags'] == 'yes')
			{
				echo '	<tr>
							<th></th><th class="site">'.$lang['all_tags'].'</th>
						</tr>
						<tr>
							<td class="vm">'.$lang['all_submint'].'&nbsp; &#8260; &nbsp;'.$lang['all_delet'].'</td>
							<td>
								<div id="tagarea">
								<table class="work">
									<tr>
										<td class="pw45">
											<select name="tagin" id="tagin" size="5" multiple class="blue pw100  app">';
				$tags = $db->query("SELECT tagid, tagword FROM ".$basepref."_".PERMISS."_tag ORDER BY tagword ASC");
				while ($tag = $db->fetchassoc($tags))
				{
						echo '					<option value="'.$tag['tagid'].'">'.$tag['tagword'].'</option>';
				}
				echo '						</select>
										</td>
										<td class="ac vm pw10">
											<input class="side-button" type="button" onclick="$.addtag();" value="&#9658;" /><br /><br />
											<input class="side-button" type="button" onclick="$.deltag();" value="&#9668;" />
										</td>
										<td>
											<select name="tagout" id="tagout" size="5" multiple class="green pw100  app">
											</select>
											<div id="area-tags">
											</div>
										</td>
									</tr>
								</table>
								</div>
							</td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['tags_new'].'</td>
							<td><input class="pw45" name="new_tags" size="70" type="text" />';
								$tm->outhint($lang['tags_new_help']);
			echo '			</td>
						</tr>
						</tbody>
						<tbody class="tab-1">
						<tr><th></th><th class="site">&nbsp;'.$lang['all_image_big'].'</th></tr>
						<tr>
							<td>'.$lang['all_image_thumb'].'</td>
							<td>
								<input name="image_thumb" id="image_thumb" size="70" type="text" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/image/\',\'&amp;field[1]=image_thumb&amp;field[2]=image&amp;field[3]=video\')" value="'.$lang['filebrowser'].'" type="button" />
								<input class="side-button" onclick="javascript:$.quickupload(\''.$sess['hash'].'&amp;objdir=/'.PERMISS.'/image/\')" value="'.$lang['file_review'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_image'].'</td>
							<td>
								<input name="image" id="image" size="70" type="text" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/image/\',\'&amp;field[1]=image&amp;field[2]=image_thumb&amp;field[3]=video\')" value="'.$lang['filebrowser'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_alt_image'].'</td>
							<td><input name="image_alt" id="image_alt" size="70" type="text" /></td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['all_align_image'].'</td>
							<td>
								<select name="image_align" class="sw165">
									<option value="left">'.$lang['all_left'].'</option>
									<option value="right">'.$lang['all_right'].'</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>&nbsp;</th><th class="site">&nbsp;'.$lang['all_files'].'</th>
						</tr>
						<tr>
							<td class="vm">'.$lang['all_submint'].'&nbsp; &#8260; &nbsp;'.$lang['all_delet'].'</td>
							<td>
								<div id="file-area"></div>
								<input class="side-button" onclick="javascript:$.addfiles(\'total-form\',\'file-area\',\'/'.PERMISS.'/files/\')" value="'.$lang['down_add'].'" type="button" />
							</td>
						</tr>
						<tr><th></th><th class="site">'.$lang['firms_social'].'</th></tr>
						<tr>
							<td class="vm">'.$lang['all_submint'].'&nbsp; &#8260; &nbsp;'.$lang['all_delet'].'</td>
							<td>
								<div id="social-area"></div>
								<input class="side-button" onclick="javascript:$.addsocial(\'total-form\',\'social-area\')" value="'.$lang['link_add'].'" type="button" />
							</td>
						</tr>
						<tr><th></th><th class="site">'.$lang['all_set'].'</th></tr>';
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['page_access'].'</td>
							<td>
								<select class="group-sel sw165" name="acc" id="acc">
									<option value="all">'.$lang['all_all'].'</option>
									<option value="user">'.$lang['all_user_only'].'</option>';
				echo '				'.((isset($conf['user']['groupact']) AND $conf['user']['groupact'] == 'yes') ? '<option value="group">'.$lang['all_groups_only'].'</option>' : '');
				echo '			</select>
								<div id="group" class="group" style="display:none;">';
				if (isset($conf['user']['groupact']) AND $conf['user']['groupact'] == 'yes')
				{
					$inqs = $db->query("SELECT * FROM ".$basepref."_user_group");
					$group_out = '';
					while ($items = $db->fetchassoc($inqs)) {
						$group_out.= '<input type="checkbox" name="group['.$items['gid'].']" value="yes" /><span>'.$items['title'].'</span>,';
					}
					echo chop($group_out, ',');
				}
				echo '			</div>
							</td>
						</tr>
						<tr>
							<td>'.$lang['files_access'].'</td>
							<td>
								<select class="group-sel sw165" name="facc" id="facc">
									<option value="all">'.$lang['all_all'].'</option>
									<option value="user">'.$lang['all_user_only'].'</option>';
				echo '				'.(($conf['user']['groupact'] == 'yes') ? '<option value="group">'.$lang['all_groups_only'].'</option>' : '');
				echo '			</select>
								<div id="fgroup" class="group" style="display: none;">';
				if ($conf['user']['groupact'] == 'yes')
				{
					$inqs = $db->query("SELECT * FROM ".$basepref."_user_group");
					$group_out = '';
					while ($items = $db->fetchassoc($inqs)) {
						$group_out.= '<input type="checkbox" name="fgroups['.$items['gid'].']" value="yes" /><span>'.$items['title'].'</span>,';
					}
					echo chop($group_out, ',');
				}
				echo '			</div>
							</td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['all_vip'].'</td>
							<td>
								<select name="vip" class="sw165">
									<option value="0">'.$lang['all_no'].'</option>
									<option value="1">'.$lang['all_yes'].'</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_status'].'</td>
							<td>
								<select name="act" class="sw165">
									<option value="yes">'.$lang['included'].' </option>
									<option value="no">'.$lang['not_included'].'</option>
								</select>
							</td>
						</tr>
						</tbody>
						<tr class="tfoot">
							<td colspan="2">
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input type="hidden" id="imgid" value="0" />
								<input type="hidden" id="fileid" value="0" />
								<input type="hidden" id="socialid" value="0" />';
			if ($conf['cpu'] == 'no') {
				echo '			<input type="hidden" name="cpu" />';
			}
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'no')
			{
				echo '			<input type="hidden" name="reason" />
								<input type="hidden" name="acc" value="all" />';
			}
			echo '				<input type="hidden" name="dn" value="addsave" />
								<input class="main-button" value="&nbsp; '.$lang['all_submint'].' &nbsp;" type="submit" />
							</td>
						</tr>
					</table>
					</form>
					</div>';

			echo "	<script>
					$(document).ready(function() {
						$('#tabs a').tabs('.tab-1');
						$( '#total-form' ).submit(function(e)
						{
							if( $('#area-cats').html().trim() !== '' && $('#catid').val() == 0) {
								$('#catid').css({'background-color': '#fcfafa', 'border-color': '#ae4752', 'box-shadow': '0 0 0 2px #f2ccd0'});
								var el = $('#catid').offset().top;
								$(document).scrollTop(el - 20);
								e.preventDefault();
							}
						});
						$('#catid').focus(function() {
							$(this).css({'background-color': '#fff', 'border-color': '#999', 'box-shadow': 'none'});
						});
					});
					</script>";

			$tm->footer();
		}

		/**
		 * Добавление организации (сохранение)
		 ---------------------------------------*/
		if ($_REQUEST['dn'] == 'addsave')
		{
			global $id, $catid, $letid, $letter, $public, $stpublic, $unpublic, $title, $subtitle, $cpu, $customs, $descript, $keywords, $textshort, $textmore, $textnotice, $reason,
				$author, $person, $site, $country, $region, $city, $address, $skype, $email, $phone, $image_thumb, $image, $image_align, $image_alt, $images, $acc, $group, $act, $hits,
				$tagword, $new_tags, $social, $files, $facc, $fgroups, $userid,
				$vip, $new, $p, $nu, $pid, $send, $subcat;

			if ($pid == 'del')
			{
				redirect('index.php?dn=newdel&amp;id='.$id.'&amp;ops='.$sess['hash'].'');
			}

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>',
					$lang['firms_add']
				);

			$cpu = preparse($cpu, THIS_TRIM, 0, 255);
			$title = preparse($title, THIS_TRIM, 0, 255);
			$subtitle = preparse($subtitle, THIS_TRIM, 0, 255);
			$customs = preparse($customs, THIS_TRIM);
			$descript = preparse($descript, THIS_TRIM);
			$keywords = preparse($keywords, THIS_TRIM);
			$textshort = preparse($textshort, THIS_TRIM);
			$textmore = preparse($textmore, THIS_TRIM);
			$textnotice = preparse($textnotice, THIS_TRIM);
			$reason = preparse($reason, THIS_TRIM);
			$author = preparse($author, THIS_TRIM);

			if (in_array(NULL, array($title, $textshort)))
			{
				$tm->header();
				$tm->error($modname[PERMISS], $lang['firms_add'], $lang['pole_add_error']);
				$tm->footer();
			}
			else
			{
				if (preparse($cpu, THIS_EMPTY) == 1)
				{
					$cpu = cpu_translit($title);
				}

				$inqure = $db->query("SELECT title, cpu FROM ".$basepref."_".PERMISS." WHERE title = '".$db->escape($title)."' OR cpu = '".$db->escape($cpu)."'");
				if ($db->numrows($inqure) > 0)
				{
					$tm->header();
					$tm->error($modname[PERMISS], $lang['firms_add'], $lang['cpu_error_isset'], $title);
					$tm->footer();
				}
			}

			if (is_array($images) AND ! empty($images))
			{
				$c = 1;
				$img = array();
				foreach ($images as $k => $v)
				{
					if (isset($v['image_thumb']) AND ! empty($v['image_thumb']))
					{
						$img[$c] = array
									(
										'thumb' => $v['image_thumb'],
										'image' => $v['image'],
										'align' => $v['image_align'],
										'alt'   => str_replace(array("'", '"'), '', $v['image_alt']),
									);
						$c ++;
					}
				}
				$images = Json::encode($img);
			}

			if (is_array($social) AND ! empty($social))
			{
				$s = array();
				foreach ($social as $k => $v) {
					if (isset($v['link']) AND ! empty($v['link']) AND isset($v['title']) AND ! empty($v['title']))
					{
						$s[$k] = array
									(
										'link'  => $v['link'],
										'title' => str_replace(array("'", '"'), '', $v['title']),
									);
					}
				}
				$social = Json::encode($s);
			}

			if ( ! empty($email) AND verify_mail($email) == 0)
			{
				$tm->header();
				$tm->error($modname[PERMISS], $lang['firms_add'], $lang['bad_mail'], $email);
				$tm->footer();
			}

			$phone = trim($phone, ',');
			if ( ! empty($phone) AND ! preg_match('/^[0-9\-,()+ ]+$/D', $phone))
			{
				$tm->header();
				$tm->error($modname[PERMISS], $lang['firms_add'], $lang['bad_phone'], $phone);
				$tm->footer();
			}

			if (
				isset($conf['user']['groupact']) AND
				$conf['user']['groupact'] == 'yes' AND
				$acc == 'group' AND is_array($group)
			)
			{
				$group = Json::encode($group);
			}

			if (
				isset($conf['user']['groupact']) AND
				$conf['user']['groupact'] == 'yes' AND
				$facc == 'group' AND
				is_array($fgroups)
			)
			{
				$fgroups = Json::encode($fgroups);
			}

			if (is_array($files) AND ! empty($files))
			{
				$f = 1;
				$file = array();
				foreach ($files as $k => $v)
				{
					if (isset($v['path']) AND ! empty($v['path']) AND isset($v['title']) AND ! empty($v['title']))
					{
						$file[$f] = array
									(
										'path'  => $v['path'],
										'title' => str_replace(array("'", '"'), '', $v['title']),
									);
						$f ++;
					}
				}
				$files = Json::encode($file);
			}

			// New tags
			if (preparse($new_tags, THIS_EMPTY) == 0)
			{
				$new_tags = preg_replace('/\s+/', ' ', trim($new_tags));
				$arr_tags = explode(',', $new_tags);

				$tagid = array();
				foreach ($arr_tags as $tag)
				{
					$tag = trim($tag);
					$count = explode(' ', $tag);
					$parts = array_chunk($count, 3);
					foreach ($parts as $val)
					{
						$tag_val = implode(' ', $val);
						$tag_cpu = cpu_translit($tag_val);
						$get_cpu = $db->query("SELECT tagcpu FROM ".$basepref."_".PERMISS."_tag WHERE tagcpu = '".$db->escape($tag_cpu)."'");
						if ($db->numrows($get_cpu) == 0)
						{
							$db->query
							(
								"INSERT INTO ".$basepref."_".PERMISS."_tag VALUES (
								 NULL,
								 '".$db->escape($tag_cpu)."',
								 '".$db->escape(preparse_sp($tag_val))."',
								 '',
								 '',
								 '',
								 '',
								 '',
								 '0'
								)"
							);
							$tagid[] = $db->insertid();
						}
					}
				}
				$tagword = ( ! empty($tagword)) ? array_merge($tagword, $tagid) : $tagid;
			}

			$tags = ( ! empty($tagword)) ? implode(',', $tagword) : '';

			// Keywords
			if ( ! empty($tags))
			{
				$tag_word = '';
				$keywords = chop(trim($keywords), ',');
				$inq = $db->query("SELECT tagword FROM ".$basepref."_".PERMISS."_tag WHERE tagid IN (".$tags.")");
				while ($item = $db->fetchassoc($inq))
				{
					if (strpos($keywords, $item['tagword']) === false)
					{
						$tag_word.= $item['tagword'].', ';
					}
				}
				$tag_word = chop($tag_word, ', ');
				$keywords = ( ! empty($keywords)) ? trim($keywords).', '.$tag_word : $tag_word;
				$keywords = chop($keywords, ', ');
			}

			if ( ! empty($author))
			{
				$author = preg_replace('/[^\pL\pNd\pZs\pP\pM]/us', '', $author);
			}

			if ( ! empty($person))
			{
				$person = preg_replace('/[^\pL\pNd\pZs\pP\pM]/us', '', $person);
			}

			$hits = ($hits) ? preparse($hits, THIS_INT) : 0;
			$catid = preparse($catid, THIS_INT);
			$image = preparse($image, THIS_TRIM, 0, 255);
			$author = preparse($author, THIS_TRIM, 0, 255);
			$person = preparse($person, THIS_TRIM, 0, 255);
			$country = preparse($country, THIS_TRIM, 0, 255);
			$region = preparse($region, THIS_TRIM, 0, 255);
			$city = preparse($city, THIS_TRIM, 0, 255);
			$skype = preparse($skype, THIS_TRIM, 0, 255);
			$image_alt = preparse($image_alt, THIS_TRIM, 0, 255);
			$image_thumb =  preparse($image_thumb, THIS_TRIM, 0, 255);
			$public = (empty($public)) ? NEWTIME : ReDate($public);
			$stpublic = (ReDate($stpublic) > 0) ? ReDate($stpublic) : 0;
			$unpublic = (ReDate($unpublic) > 0) ? ReDate($unpublic) : 0;
			$image_align = ($image_align == 'left') ? 'left' : 'right';
			$acc = ($acc == 'user' OR $acc == 'group') ? 'user' : 'all';
			$facc = ($facc == 'user' OR $facc == 'group') ? 'user' : 'all';
			$site = (!parse_url($site, PHP_URL_SCHEME) AND !empty($site)) ? 'http://'.$site : $site;
			$letid = preparse($letid, THIS_INT);
			$letid = ($conf[PERMISS]['letter'] == 'yes' AND isset($letter[$letid])) ? $letid : 0;
			$userid = preparse($userid, THIS_INT);
			$act = ($act == 'yes') ? 'yes' : 'no';
			$vip = ($vip == 1) ? 1 : 0;
			$pid = ($pid == 1) ? 1 : 0;

			$max = $db->fetchassoc($db->query("SELECT MAX(posit) + 1 AS posit FROM ".$basepref."_".PERMISS.""));
			$posit = empty($max['posit']) ? 0 : $max['posit'];

			$db->query
				(
					"INSERT INTO ".$basepref."_".PERMISS." VALUES (
					 NULL,
					 '".$catid."',
					 '".$letid."',
					 '".$public."',
					 '".$stpublic."',
					 '".$unpublic."',
					 '".$cpu."',
					 '".$db->escape(preparse_sp($title))."',
					 '".$db->escape(preparse_sp($subtitle))."',
					 '".$db->escape(preparse_sp($customs))."',
					 '".$db->escape(preparse_dp($descript))."',
					 '".$db->escape(preparse_dp($keywords))."',
					 '".$db->escape($textshort)."',
					 '".$db->escape($textmore)."',
					 '".$db->escape($textnotice)."',
					 '".$db->escape($reason)."',
					 '".$db->escape($image)."',
					 '".$db->escape($image_thumb)."',
					 '".$image_align."',
					 '".$db->escape(preparse_sp($image_alt))."',
					 '".$db->escape($images)."',
					 '".$db->escape($country)."',
					 '".$db->escape($region)."',
					 '".$db->escape($city)."',
					 '".$db->escape($address)."',
					 '".$db->escape($author)."',
					 '".$db->escape($person)."',
					 '".$db->escape($phone)."',
					 '".$site."',
					 '".$email."',
					 '".$db->escape($skype)."',
					 '0',
					 '0',
					 '0',
					 '".$db->escape($files)."',
					 '".$facc."',
					 '".$db->escape($fgroups)."',
					 '".$acc."',
					 '".$db->escape($group)."',
					 '".$hits."',
					 '".$act."',
					 '".$vip."',
					 '0',
					 '0',
					 '".$db->escape($tags)."',
					 '".$db->escape($social)."',
					 '".$userid."',
					 '".$posit."',
					 '".$pid."'
					)"
				);

			$lastid = $db->insertid();

			if ($conf[PERMISS]['multcat'] == 'yes')
			{
				$counts = new Counts(PERMISS, 'id', 0);
				$counts->add($subcat, $catid, $lastid);
			}
			else
			{
				$counts = new Counts(PERMISS, 'id');
			}

			$cache->cachesave(1); 

			/**
			 * Сообщение
			 */
			if ($send == 'ok')
			{
				$obj = $db->fetchassoc($db->query("SELECT catcpu FROM ".$basepref."_".PERMISS."_cat WHERE catid = '".$catid."' LIMIT 1"));

				$pacpu = defined('SEOURL') ? '&amp;cpu='.$cpu : '';
				$catcpu = (defined('SEOURL') AND ! empty($obj['catcpu'])) ? '&amp;ccpu='.$obj['catcpu'] : '';

				require_once(WORKDIR.'/core/classes/Router.php');
				$ro = new Router();

				if ($pid == 0)
				{
					// Успешно
					$subject = "Re: ".$lang['firmsedit_subject']." - ".$conf['site'];
					$message = this_text(array
						(
							"br"    => "\r\n",
							"title" => $title,
							"link"  => $conf['site_url'].$ro->seo("index.php?dn=".PERMISS.$catcpu."&amp;to=page&amp;id=".$id.$pacpu, 1),
							"site"  => $conf['site'],
							"date"  => format_time(NEWTIME, 1, 1)
						),
						$lang['reedit_msg_ok']
					);
					send_mail($email, $subject, $message, $conf['site'], 'robot_'.$conf['site_mail']);
				}
				elseif ($pid == 1)
				{
					// Отклонение
					$subject = "Re: ".$lang['firmsedit_subject']." - ".$conf['site'];
					$message = this_text(array
						(
							"br"     => "\r\n",
							"title"  => $title,
							"link"   => $conf['site_url'].$ro->seo("index.php?dn=".PERMISS."&amp;re=my&amp;to=edit&amp;id=".$id),
							"notice" => $reason,
							"site"   => $conf['site'],
							"date"   => format_time(NEWTIME, 1, 1)
							),
							$lang['reedit_msg_no']
					);
					send_mail($email, $subject, $message, $conf['site'], 'robot_'.$conf['site_mail']);
				}
			}

			if ($new == 'yes')
			{
				$id = preparse($id, THIS_INT);
				$db->query("DELETE FROM ".$basepref."_".PERMISS."_user WHERE id = '".$id."'");

				$redir = 'index.php?dn=new&amp;ops='.$sess['hash'];
				$redir.= (!empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
				$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';

				redirect($redir);
			}
			else
			{
				redirect('index.php?dn=list&amp;ops='.$sess['hash']);
			}
		}

		/**
		 * Редактирование организации
		 ------------------------------------*/
		if ($_REQUEST['dn'] == 'edit')
		{
			global $selective, $id, $p, $cat, $nu, $s, $l, $fid, $pid, $sess;

			$pid = (isset($pid) AND empty($pid) AND $pid == 0) ? 0 : 1;
			$crumb = (($pid == 1) ? $lang['moderate'] : $lang['all_edit']);

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>',
					$crumb
				);

			$tm->header();

			$id = preparse($id, THIS_INT);
			$p = preparse($p, THIS_INT);
			$cat = preparse($cat, THIS_INT);
			$nu = preparse($nu, THIS_INT);
			$s = preparse($s, THIS_TRIM, 1, 7);
			$l = preparse($l, THIS_TRIM, 1, 4);
			$fid = preparse($fid, THIS_INT);

			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'"));

			$public = CalendarFormat($item['public']);
			$stpublic = ($item['stpublic'] == 0) ? '' : CalendarFormat($item['stpublic']);
			$unpublic = ($item['unpublic'] == 0) ? '' : CalendarFormat($item['unpublic']);

			$inqcat = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");

			echo '	<script src="'.ADMPATH.'/js/jquery.apanel.tabs.js"></script>';
			echo '	<script>
						var all_name     = "'.$lang['all_name'].'";
						var all_cpu      = "'.$lang['all_cpu'].'";
						var all_popul    = "'.$lang['all_popul'].'";
						var all_thumb    = "'.$lang['all_image_thumb'].'";
						var all_img      = "'.$lang['all_image'].'";
						var all_images   = "'.$lang['all_image_big'].'";
						var all_align    = "'.$lang['all_align'].'";
						var all_right    = "'.$lang['all_right'].'";
						var all_left     = "'.$lang['all_left'].'";
						var all_center   = "'.$lang['all_center'].'";
						var all_alt      = "'.$lang['all_alt_image'].'";
						var all_copy     = "'.$lang['all_copy'].'";
						var all_delet    = "'.$lang['all_delet'].'";
						var code_paste   = "'.$lang['code_paste'].'";
						var all_file     = "'.$lang['all_file'].'";
						var all_path     = "'.$lang['all_path'].'";
						var filebrowser  = "'.$lang['filebrowser'].'";
						var filereview   = "'.$lang['file_review'].'";
						var page_one     = "'.$lang['page_one'].'";
						var ops          = "'.$sess['hash'].'";
						var page = "'.PERMISS.'";
						$(function() {
							$(".imgcount").focus(function () {
								$(this).select();
							}).mouseup(function(e){
								e.preventDefault();
							});
							$("#facc").on("change", function() {
								if ($(this).val() == "group") {
									$("#fgroup").slideDown();
								} else {
									$("#fgroup").slideUp();
								}
							});
							var pid = "'.$item['pid'].'";
							if (pid == 1) {
								$(".notice").show();
								$(".notice textarea").removeAttr("required");
							}
							if ($("#pid").val() == 1) {
									$(".notice").show();
									$(".notice textarea").attr("required", "required");
							}
							$("#pid").on("change", function() {
								if ($(this).val() == "0" || $(this).val() == "del") {
									$(".notice").slideUp();
									$(".notice textarea").removeAttr("required");
								} else {
									$(".notice").slideDown();
									$(".notice textarea").attr("required", "required");
								}
							});
						});
					</script>';

			echo "	<script>
						$(function() {
							$.addfiles = function(form, area, path) {
								var id = $('#fileid').attr('value');
								if (id) {
									var html = '<div class=\"section tag\" id=\"file-' + id + '\" style=\"display: none;\">';
									html += '<table class=\"work\"><tr>';
									html += '<td class=\"vm sw150\">' + all_path + '</td>';
									html += '<td>';
									html += '<input name=\"files[' + id + '][path]\" id=\"files' + id + '\" size=\"50\" type=\"text\" required>&nbsp;';
									html += '<input class=\"side-button\" onclick=\"javascript:$.filebrowser(\'' + ops + '\',\'' + path + '\',\'&amp;field[1]=files' + id + '\')\" value=\"' + filebrowser + '\" type=\"button\"> ';
									html += '<input class=\"side-button\" onclick=\"javascript:$.fileallupload(\'' + ops + '&amp;objdir=' + path + '\',\'&amp;field=files' + id + '\')\" value=\"' + filereview + '\" type=\"button\">';
									html += '<a class=\"but fr\" href=\"javascript:$.removetaginput(\'total-form\',\'file-area\',\'file-' + id + '\');\">&#215;</a>';
									html += '</td></tr><tr>';
									html += '<td class=\"vm sw150\">' + all_name + '</td>';
									html += '<td><input name=\"files[' + id + '][title]\" size=\"50\" type=\"text\" required></td>';
									html += '</tr></table></div>';
									$('form[id=' + form + '] #' + area).append(html);
									$('form[id=' + form + '] #' + area + ' #file-' + id).show('normal');
									id++;
									$('#fileid').attr({
										value: id
									});
								}
							}
							$.addsocial = function(form, area) {
								var id = $('#socialid').attr('value');
								if (id) {
									var html = '<div class=\"section tag\" id=\"social-' + id + '\" style=\"display: none;\">';
									html += '<table class=\"work\"><tr>';
									html += '<td class=\"vm sw150 al\"><input name=\"social[' + id + '][title]\" id=\"social' + id + '\" size=\"15\" type=\"text\" placeholder=\"' + all_name + '\" required></td>';
									html += '<td class=\"vm\"><input name=\"social[' + id + '][link]\" size=\"50\" type=\"text\" placeholder=\"' + page_one + '\" required>';
									html += '<a class=\"but fr\" href=\"javascript:$.removetaginput(\'total-form\',\'social-area\',\'social-' + id + '\');\">&#215;</a></td>';
									html += '</tr></table></div>';
									$('form[id=' + form + '] #' + area).append(html);
									$('form[id=' + form + '] #' + area + ' #social-' + id).show('normal');
									id++;
									$('#socialid').attr({
										value: id
									});
								}
							}
						});
					</script>";

			$tabs = '	<div class="tabs" id="tabs">
							<a href="#" data-tabs=".tab-1">'.$lang['home'].'</a>
							<a href="#" data-tabs=".tab-2" style="display: none;"></a>
							<a href="#" data-tabs="all">'.$lang['all_field'].'</a>
						</div>';

			echo '	<div class="section">
					<form action="index.php" method="post" id="total-form">
					<table class="work">
						<caption>'.$lang[PERMISS].': '.$crumb.'</caption>
						<tr>
							<th class="ar site">'.$lang['all_bookmark'].' &nbsp; </th>
							<th>'.$tabs.'</th>
						</tr>
						<tbody class="tab-1">
						<tr>
							<td class="first"><span>*</span> '.$lang['all_name'].'</td>
							<td><input type="text" name="title" id="title" size="70" value="'.preparse_un($item['title']).'" required="required" /> <span class="light">&lt;h1&gt;</span></td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['sub_title'].'</td>
							<td><input type="text" name="subtitle" size="70" value="'.preparse_un($item['subtitle']).'" /> <span class="light">&lt;h2&gt;</span></td>
						</tr>';
			if ($conf['cpu'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['all_cpu'].'</td>
							<td><input type="text" name="cpu" id="cpu" size="70" value="'.$item['cpu'].'" />';
								$tm->outtranslit('title', 'cpu', $lang['cpu_int_hint']);
				echo '		</td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['custom_title'].'</td>
							<td><input type="text" name="customs" size="70" value="'.preparse_un($item['customs']).'" /> <span class="light">&lt;title&gt;</span></td>
						</tr>
						<tr>
							<td>'.$lang['all_descript'].'</td>
							<td><input type="text" name="descript" size="70" value="'.preparse_un($item['descript']).'" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_keywords'].'</td>
							<td><input type="text" name="keywords" size="70" value="'.preparse_un($item['keywords']).'" />';
								$tm->outhint($lang['keyword_hint']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_data'].'</td>
							<td><input type="text" name="public" id="public" value="'.$public.'" />';
								Calendar('cal', 'public');
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_stpublic'].'</td>
							<td><input type="text" name="stpublic" id="stpublic" value="'.$stpublic.'" />';
								Calendar('stcal', 'stpublic');
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_unpublic'].'</td>
							<td><input type="text" name="unpublic" id="unpublic" value="'.$unpublic.'" />';
								Calendar('uncal', 'unpublic');
			echo '			</td>
						</tr>
						</tbody>
						<tbody class="tab-1">';
			if ($db->numrows($inqcat) > 0 OR $conf[PERMISS]['letter'] == 'yes')
			{
				echo '	<tr><th></th><th class="site">'.$lang['all_cat'].'</th></tr>';
			}
			if ($db->numrows($inqcat) > 0)
			{
				echo '	<tr>
							<td>'.$lang['cat_click'].'</td>
							<td>
								<select id="catid" name="catid" class="sw350">
									<option value="0"> &#8212; '.$lang['cat_not'].' &#8212; </option>';
				$catcache = array();
				$catid = $item['catid'];
				while ($items = $db->fetchassoc($inqcat))
				{
					$catcache[$items['parentid']][$items['catid']] = $items;
				}
				this_selectcat(0);
				echo				$selective.'
								</select>
							</td>
						</tr>';
			}
				echo '	</tbody>
						<tbody class="tab-2">';
			if ($conf[PERMISS]['letter'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['all_letter'].'</td>
							<td>
								<select name="letid" class="sw350">
									<option value="0"> &#8213; </option>';
				foreach($letter as $k => $v) {
					echo '			<option value="'.$k.'"'.(($item['letid'] == $k) ? ' selected' : '').'> '.$v.' </option>';
				}
				echo '			</select>
							</td>
						</tr>';
			}
			if ($db->numrows($inqcat) > 0 AND $conf[PERMISS]['multcat'] == 'yes')
			{
				$cat_list = 0;
				$catout = $catshow = NULL;
				$cat_array = array();
				$inq_cats = $db->query("SELECT * FROM ".$basepref."_".PERMISS."_cats WHERE id = '".$id."'");

				if ($db->numrows($inq_cats) > 0)
				{
					while ($cat_item = $db->fetchassoc($inq_cats)) {
						$cat_array[] = $cat_item['catid'];
					}
					$cat_list = implode(',', $cat_array);
					$cat_in = $db->query("SELECT catid, catname FROM ".$basepref."_".PERMISS."_cat WHERE catid IN (".$cat_list.")");
					while ($cat_item = $db->fetchassoc($cat_in))
					{
						if ($cat_item['catid'] != $item['catid'])
						{
							$catshow.= ' <option value="'.$cat_item['catid'].'">'.$cat_item['catname'].'</option>';
						}
						$catout.= '  <input type="hidden" name="subcat[]" value="'.$cat_item['catid'].'">';
					}
				}
				echo '	<script>
						function inCat(el) {
							$(el).prepend(\'<option value="0">&#8212;</option>\');
							$(el).find("option:not(:first)").remove().end().prop("disabled", true );
						}
						function getCat(catid, sess) {
							$.nocat = new Array("'.$cat_list.'," + catid);
							$.ajax( {
								cache:false,
								url: $.apanel + "mod/'.PERMISS.'/index.php",
								data:"dn=getcat&nocat=" +  $.nocat + "&ops=" +  sess,
								error:function(msg){},
								success:function(data) {
									if (data.length > 0 && data.match(/option/)) {
										$("#catin").html(data);
										$("#catin").prop("disabled", false );
									} else {
										inCat("#catin");
									}
								}
							});
						}
						$(function() {
							var sess = "'.$sess['hash'].'";
							var cl = $("#catid").val();
							(cl > 0) ? getCat(cl, sess) : inCat("#catin");
							$("#catid").on("change", function() {
								var cc = $(this).val();
								(cc > 0) ? getCat(cc, sess) : inCat("#catin");
							});
						});
						</script>';
				echo '	<tr>
							<th></th><th class="site">'.$lang['other_category'].'</th>
						</tr>
						<tr>
							<td>'.$lang['all_submint'].'&nbsp; &#8260; &nbsp;'.$lang['all_delet'].'</td>
							<td>
								<div id="tagarea">
								<table class="work">
									<tr>
										<td class="pw45">
											<select name="catin" id="catin" size="5" multiple class="blue pw100 app">
												<option value="0">&#8212;</option>
											</select>
										</td>
										<td class="ac pw10 vm">
											<input class="side-button" type="button" onclick="$.addcat();" value="&#9658;" /><br /><br />
											<input class="side-button" type="button" onclick="$.delcat();" value="&#9668;" />
										</td>
										<td>
											<select name="catout" id="catout" size="5" multiple class="green pw100 app">
												'.$catshow.'
											</select>
											<div id="area-cats">
												'.$catout.'
											</div>
										</td>
									</tr>
								</table>
								</div>
							</td>
						</tr>';
			}
			echo '		</tbody>
						<tbody class="tab-1">
						<tr><th></th><th class="site">'.$lang['all_location'].'</th></tr>
						<tr>
							<td class="first">'.$lang['country'].'</td>
							<td><input type="text" name="country" size="70" value="'.$item['country'].'" /></td>
						</tr>
						<tr>
							<td class="first">'.$lang['all_region'].'</td>
							<td><input type="text" name="region" size="70" value="'.$item['region'].'" /></td>
						</tr>
						<tr>
							<td class="first">'.$lang['all_city'].'</td>
							<td><input type="text" name="city" size="70" value="'.$item['city'].'" /></td>
						</tr>
						<tr>
							<td class="first">'.$lang['all_address'].'</td>
							<td><input type="text" name="address" size="70" value="'.$item['address'].'" placeholder="'.$lang['place_address'].'" /></td>
						</tr>
						<tr><th></th><th class="site">'.$lang['contact_data'].'</th></tr>
						<tr>
							<td>'.$lang['author'].'</td>
							<td><input type="text" name="author" size="70" value="'.$item['author'].'" /></td>
						</tr>
						<tr>
							<td>'.$lang['person'].'</td>
							<td><input type="text" name="person" size="70" value="'.$item['person'].'" /></td>
						</tr>
						<tr>
							<td>'.$lang['e_mail'].'</td>
							<td><input type="text" name="email" size="70" value="'.$item['email'].'" placeholder="info@site.ru" /></td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['phone'].'</td>
							<td><input type="text" name="phone" size="70" value="'.$item['phone'].'" placeholder="+7 (xxx) xxx-xx-xx" />';
								$tm->outhint($lang['phone_help']);
			echo '			</td>
						</tr>
						<tr>
							<td>Skype</td>
							<td><input type="text" name="skype" value="'.$item['skype'].'" size="70" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_site'].'</td>
							<td><input type="text" name="site" size="70" value="'.$item['site'].'" placeholder="http://" />
								&nbsp; '.(!empty($item['site']) ? '<a href="'.$item['site'].'" target="_blank"><img src="'.ADMURL.'/template/images/blank.png" alt="'.$lang['go'].'" /></a>' : '').'
							</td>
						</tr>
						</tbody>
						<tbody class="tab-1">
						<tr><th></th><th class="site">'.$lang['all_content'].'</th></tr>
						<tr>
							<td class="first"><span>*</span> '.$lang['input_text'].'</td>
							<td class="usewys">';
			if ($wysiwyg == 'yes') {
				define("USEWYS", 1);
				$WYSFORM = 'textshort';
				$WYSVALUE = $item['textshort'];
				include(ADMDIR.'/includes/wysiwyg.php');
			} else {
				$tm->textarea('textshort', 4, 70, $item['textshort'], 1, '', '', 1);
			}
			echo '			</td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['full_text'].'</td>
							<td class="usewys">';
			if ($wysiwyg == 'yes') {
				$WYSFORM = 'textmore';
				$WYSVALUE = $item['textmore'];
				include(ADMDIR.'/includes/wysiwyg.php');
			} else {
				$tm->textarea('textmore', 7, 70, $item['textmore'], (($wysiwyg == 'yes') ? 0 : 1));
			}
			echo '			</td>
						</tr>';
			$img = Json::decode($item['images']);
			$class = (is_array($img) AND sizeof($img) > 0) ? ' class="image-area"' : '';
			echo '		<tr>
							<td>'.$lang['img_extra_hint'].'</td>
							<td>
								<a class="side-button" href="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/image/attach/\',\'&amp;ims=1\');">'.$lang['filebrowser'].'</a>&nbsp;
								<a class="side-button" href="javascript:$.personalupload(\''.$sess['hash'].'&amp;objdir=/'.PERMISS.'/image/attach/\');">'.$lang['file_upload'].'</a>
								<div id="image-area"'.$class.'>';
			$ic = 0;
			if (is_array($img))
			{
				foreach ($img as $k => $v)
				{
					$ic ++;
					echo '			<div id="imginput'.$ic.'" style="display:block;">
									<table class="work">
										<tr>
											<td>';
					if (!empty($v['image'])) {
						echo '					<img class="sw50" src="'.WORKURL.'/'.$v['thumb'].'" alt="'.$lang['all_image_thumb'].'" />';
					} else {
						echo '					<img class="sw70" src="'.WORKURL.'/'.$v['thumb'].'" alt="'.$lang['all_image_big'].'" />';
					}
					echo '						<input type="hidden" name="images['.$ic.'][image_thumb]" value="'.$v['thumb'].'" />';
					if (!empty($v['image'])) {
						echo '					&nbsp;&nbsp;<img class="sw70" src="'.WORKURL.'/'.$v['image'].'" alt="'.$lang['all_image'].'" />
												<input type="hidden" name="images['.$ic.'][image]" value="'.$v['image'].'" />';
					}
					echo '					</td>
											<td>
												<p><input type="text" size="3" value="{img'.$ic.'}" class="imgcount" readonly="readonly" title="'.$lang['all_copy'].'" /> <cite>'.$lang['code_paste'].'</cite></p>
												<p class="label">'.$lang['all_align'].'&nbsp; &nbsp; &nbsp; &nbsp;'.$lang['all_alt_image'].'</p>
												<p>
													<select name="images['.$ic.'][image_align]">
														<option value="left"'.(($v['align'] == 'left') ? ' selected' : '').'>'.$lang['all_left'].'</option>
														<option value="right"'.(($v['align'] == 'right') ? ' selected' : '').'>'.$lang['all_right'].'</option>
														<option value="center"'.(($v['align'] == 'center') ? ' selected' : '').'>'.$lang['all_center'].'</option>
													</select>&nbsp; &nbsp; &nbsp;
													<input type="text" name="images['.$ic.'][image_alt]" size="25" value="'.$v['alt'].'" />
												</p>
											</td>
											<td><a class="but" href="javascript:$.filebrowserimsremove(\''.$ic.'\');" title="'.$lang['all_delet'].'">x</a></td>
										</tr>
									</table>
									</div>';
				}
			}
			echo '				</div>
							</td>
						</tr>
						<tr><th></th><th class="site">'.$lang['all_info'].'</th></tr>';
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['user_texts'].'</td>
							<td>';
								$tm->textarea('textnotice', 2, 70, $item['textnotice'], true, false, 'ignorewysywig');
				echo '		</td>
						</tr>';
			}
			if($conf[PERMISS]['tags'] == 'yes')
			{
				echo '	<tr>
							<th></th><th class="site">'.$lang['all_tags'].'</th>
						</tr>
						<tr>
							<td>'.$lang['all_submint'].'&nbsp; &#8260; &nbsp;'.$lang['all_delet'].'</td>
							<td>
								<div id="tagarea">
								<table class="work">
									<tr>
										<td class="pw45">
											<select name="tagin" id="tagin" size="5" multiple class="blue pw100 app">';
				$tagword = $tagshow = NULL;
				if ( ! empty($item['tags']))
				{
					$tag_in = $db->query("SELECT tagid, tagword FROM ".$basepref."_".PERMISS."_tag WHERE tagid IN (".$item['tags'].")");
					while ($tag = $db->fetchassoc($tag_in))
					{
						$tagshow.= '				<option value="'.$tag['tagid'].'">'.$tag['tagword'].'</option>';
						$tagword.= '				<input type="hidden" name="tagword[]" value="'.$tag['tagid'].'">';
					}
				}
				$sql = ( ! empty($item['tags'])) ? ' WHERE tagid NOT IN ('.$item['tags'].')' : '';
				$tag_not = $db->query("SELECT tagid, tagword FROM ".$basepref."_".PERMISS."_tag".$sql);
				while ($tag = $db->fetchassoc($tag_not))
				{
						echo '					<option value="'.$tag['tagid'].'">'.$tag['tagword'].'</option>';
				}
				echo '						</select>
										</td>
										<td class="ac pw10 vm">
											<input class="side-button" type="button" onclick="$.addtag();" value="&#9658;" /><br /><br />
											<input class="side-button" type="button" onclick="$.deltag();" value="&#9668;" />
										</td>
										<td>
											<select name="tagout" id="tagout" size="5" multiple class="green pw100 app">
												'.$tagshow.'
											</select>
											<div id="area-tags">
												'.$tagword.'
											</div>
										</td>
									</tr>
								</table>
								</div>
							</td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['tags_new'].'</td>
							<td><input class="pw45" name="new_tags" size="70" type="text" />';
								$tm->outhint($lang['tags_new_help']);
			echo '			</td>
						</tr>
						</tbody>
						<tbody class="tab-1">
						<tr><th></th><th class="site">'.$lang['all_image_big'].'</th></tr>
						<tr>
							<td>'.$lang['all_image_thumb'].'</td>
							<td>
								<input name="image_thumb" id="image_thumb" size="70" type="text" value="'.$item['image_thumb'].'" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/image/\',\'&amp;field[1]=image_thumb&amp;field[2]=image&amp;field[3]=video\')" value="'.$lang['filebrowser'].'" type="button" />
								<input class="side-button" onclick="javascript:$.quickupload(\''.$sess['hash'].'&amp;objdir=/'.PERMISS.'/image/\')" value="'.$lang['file_review'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_image'].'</td>
							<td>
								<input name="image" id="image" size="70" type="text" value="'.$item['image'].'" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/image/\',\'&amp;field[1]=image&amp;field[2]=image_thumb&amp;field[3]=video\')" value="'.$lang['filebrowser'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_alt_image'].'</td>
							<td><input name="image_alt" id="image_alt" size="70" type="text" value="'.preparse_un($item['image_alt']).'" /></td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['all_align_image'].'</td>
							<td>
								<select name="image_align" class="sw165">
									<option value="left"'.(($item['image_align'] == 'left') ? ' selected' : '').'>'.$lang['all_left'].'</option>
									<option value="right"'.(($item['image_align'] == 'right') ? ' selected' : '').'>'.$lang['all_right'].'</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>&nbsp;</th><th class="site">&nbsp;'.$lang['all_files'].'</th>
						</tr>
						<tr>
							<td class="vm">'.$lang['all_submint'].'&nbsp; &#8260; &nbsp;'.$lang['all_delet'].'</td>
							<td>
								<div id="file-area">';
			$fp = Json::decode($item['files']);
			$f = 1;
			if (is_array($fp) AND sizeof($fp) > 0)
			{
				foreach ($fp as $k => $v)
				{
					echo '			<div class="section tag" id="file-'.$f.'">
										<table class="work">
											<tr>
												<td class="vm sw150">'.$lang['all_path'].'</td>
												<td>
													<input name="files['.$f.'][path]" id="files'.$f.'" size="50" type="text" value="'.$v['path'].'" required="required" />
													<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/files/\',\'&amp;field[1]=files'.$f.'\')" value="'.$lang['filebrowser'].'" type="button" />
													<input class="side-button" onclick="javascript:$.fileallupload(\''.$sess['hash'].'&amp;objdir=/'.PERMISS.'/files/\',\'&amp;field=files'.$f.'\')" value="'.$lang['file_review'].'" type="button" />
													<a class="but fr" href="javascript:$.removetaginput(\'total-form\',\'file-area\',\'file-'.$f.'\');" title="'.$lang['all_delet'].'">&#215;</a>';
					echo '						</td>
											<tr>
												<td class="vm sw150">'.$lang['all_name'].'</td>
												<td><input name="files['.$f.'][title]" size="50" type="text" value="'.$v['title'].'" required="required" /></td>
											</tr>
										</table>
									</div>';
					$f ++;
				}
			}
			echo '				</div>
								<input class="side-button" onclick="javascript:$.addfiles(\'total-form\',\'file-area\',\'/'.PERMISS.'/files/\')" value="'.$lang['down_add'].'" type="button" />
							</td>
						</tr>
						<tr>
							<th>&nbsp;</th><th class="site">&nbsp;'.$lang['firms_social'].'</th>
						</tr>
						<tr>
							<td class="vm">'.$lang['all_submint'].'&nbsp; &#8260; &nbsp;'.$lang['all_delet'].'</td>
							<td>
								<div id="social-area">';
			$sp = Json::decode($item['social']);
			$s = 1;
			if (is_array($sp) AND sizeof($sp) > 0)
			{
				foreach ($sp as $v)
				{
					echo '			<div class="section tag" id="social-'.$s.'">
										<table class="work">
											<tr>
												<td class="vm sw150 al"><input name="social['.$s.'][title]" id="social'.$s.'" size="15" type="text" value="'.$v['title'].'" required="required"></td>
												<td class="vm"><input name="social['.$s.'][link]" size="50" type="text" value="'.$v['link'].'" required="required">
												<a class="but fr" href="javascript:$.removetaginput(\'total-form\', \'social-area\', \'social-'.$s.'\');">&#215;</a></td>
												</tr>
										</table>
									</div>';
					$s ++;
				}
			}
			echo '				</div>
								<input class="side-button" onclick="javascript:$.addsocial(\'total-form\', \'social-area\')" value="'.$lang['link_add'].'" type="button" />
							</td>
						</tr>
						<tr><th></th><th class="site">'.$lang['all_set'].'</th></tr>';
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['page_access'].'</td>
							<td>
								<select class="group-sel sw165" name="acc" id="acc">
									<option value="all"'.(($item['acc'] == 'all') ? ' selected' : '').'>'.$lang['all_all'].'</option>
									<option value="user"'.(($item['acc'] == 'user' AND empty($item['groups']))  ? ' selected' : '').'>'.$lang['all_user_only'].'</option>';
				echo '				'.(($conf['user']['groupact'] == 'yes') ? '<option value="group"'.(($item['acc'] == 'user' AND ! empty($item['groups']))  ? ' selected' : '').'>'.$lang['all_groups_only'].'</option>' : '');
				echo '			</select>
								<div class="group" id="group"'.(($item['acc'] == 'all' OR $item['acc'] == 'user' AND empty($item['groups'])) ? ' style="display: none;"' : '').'>';
				if ($conf['user']['groupact'] == 'yes')
				{
					$inqs = $db->query("SELECT * FROM ".$basepref."_user_group");
					$group = Json::decode($item['groups']);
					$group_out = '';
					while ($items = $db->fetchassoc($inqs)) {
						$group_out.= '<input type="checkbox" name="group['.$items['gid'].']" value="yes"'.(isset($group[$items['gid']]) ? ' checked' : '').'><span>'.$items['title'].'</span>,';
					}
					echo chop($group_out, ',');
				}
				echo '			</div>
							</td>
						</tr>
						<tr>
							<td>'.$lang['files_access'].'</td>
							<td>
								<select class="group-sel sw165" name="facc" id="facc">
									<option value="all"'.(($item['facc'] == 'all') ? ' selected' : '').'>'.$lang['all_all'].'</option>
									<option value="user"'.(($item['facc'] == 'user' AND empty($item['fgroups']))  ? ' selected' : '').'>'.$lang['all_user_only'].'</option>';
				echo '				'.(($conf['user']['groupact'] == 'yes') ? '<option value="group"'.(($item['facc'] == 'user' AND ! empty($item['fgroups']))  ? ' selected' : '').'>'.$lang['all_groups_only'].'</option>' : '');
				echo '			</select>
								<div class="group" id="fgroup"'.(($item['facc'] == 'all' OR $item['facc'] == 'user' AND empty($item['fgroups'])) ? ' style="display: none;"' : '').'>';
				if ($conf['user']['groupact'] == 'yes')
				{
					$finqs = $db->query("SELECT * FROM ".$basepref."_user_group");
					$fgroup = Json::decode($item['fgroups']);
					$fgroup_out = '';
					while ($fitems = $db->fetchassoc($finqs)) {
						$fgroup_out.= '<input type="checkbox" name="fgroups['.$fitems['gid'].']" value="yes"'.(isset($fgroup[$fitems['gid']]) ? ' checked' : '').'><span>'.$fitems['title'].'</span>,';
					}
					echo chop($fgroup_out, ',');
				}
				echo '			</div>
							</td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['all_vip'].'</td>
							<td>
								<select name="vip" class="sw165">
									<option value="0"'.(($item['vip'] == 0) ? ' selected' : '').'>'.$lang['all_no'].'</option>
									<option value="1"'.(($item['vip'] == 1) ? ' selected' : '').'>'.$lang['all_yes'].'</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_status'].'</td>
							<td>
								<select name="act" class="sw165">
									<option value="yes"'.(($item['act'] == 'yes') ? ' selected' : '').'>'.$lang['included'].' </option>
									<option value="no"'.(($item['act'] == 'no')  ? ' selected' : '').'>'.$lang['not_included'].'</option>
								</select>
							</td>
						</tr>';
			if ($pid)
			{
				echo '	<tr><th></th><th class="alternative">'.$lang['moderate'].'</th></tr>
						<tr>
							<td></td>
							<td>
								<select name="pid" id="pid" class="sw165">
									<option value="0"'.(($item['pid'] == 0) ? ' selected' : '').'>'.$lang['all_accept'].' </option>
									<option value="1"'.(($item['pid'] == 1) ? ' selected' : '').'>'.$lang['not_accept'].'</option>
									<option value="del">'.$lang['all_delet'].'</option>
								</select>
							</td>
						</tr>';
				echo '	<input type="hidden" name="send" value="ok">';
				if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'yes')
				{
					echo '	<tr class="notice none">
								<td><div>'.$lang['err_moderate'].'</div></td>
								<td><div class="notice">';
								$tm->textarea('reason', 2, 70, $item['reason'], TRUE, FALSE, 'ignorewysywig');
					echo '		</div></td>
							</tr>';
				}
			}
			echo '
						</tbody>
						<tr class="tfoot">
							<td colspan="2">
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input type="hidden" id="imgid" value="'.$ic.'" />';
			if ($conf['cpu'] == 'no') {
				echo '			<input type="hidden" name="cpu" value="" />';
			}
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'no')
			{
					echo '		<input type="hidden" name="reason" />
								<input type="hidden" name="acc" value="all" />';
			}
			echo '				<input type="hidden" name="id" value="'.$id.'" />
								<input type="hidden" name="p" value="'.$p.'" />
								<input type="hidden" name="cat" value="'.$cat.'" />
								<input type="hidden" name="nu" value="'.$nu.'" />
								<input type="hidden" name="s" value="'.$s.'" />
								<input type="hidden" name="l" value="'.$l.'" />
								<input type="hidden" id="fileid" value="'.$f.'" />
								<input type="hidden" id="socialid" value="'.$s.'" />';
			if ($fid > 0) {
				echo '			<input type="hidden" name="fid" value="'.$fid.'" />';
            }
			echo '				<input type="hidden" name="dn" value="editsave" />
								<input class="main-button" value="'.$lang['all_save'].'" type="submit" />
							</td>
						</tr>
					</table>
					</form>
					</div>';

			echo "	<script>
					$(function(){
						$('#tabs a').tabs('.tab-1');
						$( '#total-form' ).submit(function(e)
						{
							if( $('#area-cats').html().trim() !== '' && $('#catid').val() == 0)
							{
								$('#catid').css({'background-color': '#fcfafa', 'border-color': '#ae4752', 'box-shadow': '0 0 0 2px #f2ccd0'});
								var el = $('#catid').offset().top;
								$(document).scrollTop(el - 20);
								e.preventDefault();
							}
						});
						$('#catid').focus(function() {
							$(this).css({'background-color': '#fff', 'border-color': '#999', 'box-shadow': 'none'});
						});
					});
					</script>";

			$tm->footer();
		}

		/**
		 * Редактирование организации (сохранение)
		 -------------------------------------------*/
		if ($_REQUEST['dn'] == 'editsave')
		{
			global $id, $catid, $letid, $letter, $public, $stpublic, $unpublic, $title, $subtitle, $cpu, $customs, $descript, $keywords,
				$textshort, $textmore, $textnotice, $reason, $author, $person, $site, $country, $region, $city, $address, $email, $phone, $skype,
				$image_thumb, $image, $image_align, $image_alt, $images, $tagword, $new_tags, $files, $facc, $fgroups,
				$vip, $acc, $group, $act, $hits, $p, $cat, $nu, $s, $l, $fid, $pid, $social, $send, $subcat;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>',
					$lang['firms_edit']
				);

			require_once(WORKDIR.'/core/classes/Router.php');
			$ro = new Router();

			$cpu = preparse($cpu, THIS_TRIM, 0, 255);
			$title = preparse($title, THIS_TRIM, 0, 255);
			$subtitle = preparse($subtitle, THIS_TRIM, 0, 255);
			$customs = preparse($customs, THIS_TRIM);
			$descript = preparse($descript, THIS_TRIM);
			$keywords = preparse($keywords, THIS_TRIM);
			$textshort = preparse($textshort, THIS_TRIM);
			$textmore = preparse($textmore, THIS_TRIM);
			$textnotice = preparse($textnotice, THIS_TRIM);

			$id = preparse($id, THIS_INT);
			$fid = preparse($fid, THIS_INT);

			if ($pid == 'del')
			{
				redirect('index.php?dn=del&amp;id='.$id.'&amp;ops='.$sess['hash'].'');
			}

			if (in_array(NULL, array($title, $textshort)))
			{
				$tm->header();
				$tm->error($modname[PERMISS], $lang['firms_edit'], $lang['pole_add_error']);
				$tm->footer();
			}
			else
			{
				if (preparse($cpu, THIS_EMPTY) == 1)
				{
					$cpu = cpu_translit($title);
				}

				$inqure = $db->query
							(
								"SELECT title, cpu FROM ".$basepref."_".PERMISS."
								 WHERE (title = '".$db->escape($title)."' OR cpu = '".$db->escape($cpu)."')
								 AND id <> '".$id."'"
							);

				if ($db->numrows($inqure) > 0)
				{
					$tm->header();
					$tm->error($modname[PERMISS], $lang['firms_edit'], $lang['cpu_error_isset'], $title);
					$tm->footer();
				}
			}

			if (is_array($images) AND ! empty($images))
			{
				$i = 1;
				foreach ($images as $v)
				{
					if (isset($v['image_thumb']) AND ! empty($v['image_thumb']))
					{
						$img[$i] = array
									(
										'thumb' => $v['image_thumb'],
										'image' => $v['image'],
										'align' => $v['image_align'],
										'alt'   => str_replace(array("'", '"'), '', $v['image_alt']),
									);
						$i ++;
					}
				}
				$images = Json::encode($img);
			}

			if (is_array($social) AND ! empty($social))
			{
				$s = array();
				foreach ($social as $k => $v) {
					if (isset($v['link']) AND ! empty($v['link']) AND isset($v['title']) AND ! empty($v['title']))
					{
						$s[$k] = array
									(
										'link'  => $v['link'],
										'title' => str_replace(array("'", '"'), '', $v['title']),
									);
					}
				}
				$social = Json::encode($s);
			}

			if ( ! empty($email) AND verify_mail($email) == 0)
			{
				$tm->header();
				$tm->error($modname[PERMISS], $lang['firms_edit'], $lang['bad_mail'], $email);
				$tm->footer();
			}

			$phone = trim($phone, ',');
			if ( ! empty($phone) AND ! preg_match('/^[0-9\-,()+ ]+$/D', $phone))
			{
				$tm->header();
				$tm->error($modname[PERMISS], $lang['firms_edit'], $lang['bad_phone'], $phone);
				$tm->footer();
			}

			$pid = $pid == 1 ? 1 : 0;
			$reason = $pid == 1 ? $reason : '';
			if ($pid AND preparse($reason, THIS_EMPTY) == 1)
			{
				$tm->header();
				$tm->error($modname[PERMISS], $lang['firms_edit'], $lang['pole_add_error'], $lang['user_texts']);
				$tm->footer();
			}

			if (
				isset($conf['user']['groupact']) AND
				$conf['user']['groupact'] == 'yes' AND
				$acc == 'group' AND is_array($group)
			)
			{
				$group = Json::encode($group);
			}

			if (
				isset($conf['user']['groupact']) AND
				$conf['user']['groupact'] == 'yes' AND
				$facc == 'group' AND
				is_array($fgroups)
			)
			{
				$fgroups = Json::encode($fgroups);
			}
			$facc = ($facc == 'user' OR $facc == 'group') ? 'user' : 'all';

			// Files
			$fold = array();
			$finq = $db->fetchassoc($db->query("SELECT files FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'"));
			if ( ! empty($finq['files']))
			{
				$dec = Json::decode($finq['files']);
				foreach ($dec as $v)
				{
					$fold[] = $v['path'];
				}
			}

			if ( ! empty($files))
			{
				$f = 1;
				foreach ($files as $v)
				{
					if (isset($v['path']) AND ! empty($v['path']) AND isset($v['title']) AND ! empty($v['title']))
					{
						$fnew[] = $v['path'];
						$file[$f] = array
									(
										'path'  => $v['path'],
										'title' => str_replace(array("'", '"'), '', $v['title']),
									);
						$f ++;
					}
				}
				$files = Json::encode($file);
			}
			else
			{
				$facc = 'all';
				$fgroups = '';
			}

			if ( ! empty($fold))
			{
				if ( ! empty($fnew)) {
					$fold = array_diff_assoc($fold, $fnew);
				}

				foreach ($fold as $file)
				{
					if (file_exists(WORKDIR.'/'.$file))
					{
						unlink(WORKDIR.'/'.$file);
					}
				}
			}

			// New tags
			if (preparse($new_tags, THIS_EMPTY) == 0)
			{
				$new_tags = preg_replace('/\s+/', ' ', trim($new_tags));
				$arr_tags = explode(',', $new_tags);

				$tagid = array();
				foreach ($arr_tags as $tag)
				{
					$tag = trim($tag);
					$count = explode(' ', $tag);
					$parts = array_chunk($count, 3);
					foreach ($parts as $val)
					{
						$tag_val = implode(' ', $val);
						$tag_cpu = cpu_translit($tag_val);
						$get_cpu = $db->query("SELECT tagcpu FROM ".$basepref."_".PERMISS."_tag WHERE tagcpu = '".$db->escape($tag_cpu)."'");
						if ($db->numrows($get_cpu) == 0)
						{
							$db->query
							(
								"INSERT INTO ".$basepref."_".PERMISS."_tag VALUES (
								 NULL,
								 '".$db->escape($tag_cpu)."',
								 '".$db->escape(preparse_sp($tag_val))."',
								 '',
								 '',
								 '',
								 '',
								 '',
								 '0'
								)"
							);
							$tagid[] = $db->insertid();
						}
					}
				}
				$tagword = ( ! empty($tagword)) ? array_merge($tagword, $tagid) : $tagid;
			}

			$tags = ( ! empty($tagword)) ? implode(',', $tagword) : '';

			// Keywords
			if ( ! empty($tags))
			{
				$tag_word = '';
				$keywords = chop(trim($keywords), ',');
				$inq = $db->query("SELECT tagword FROM ".$basepref."_".PERMISS."_tag WHERE tagid IN (".$tags.")");
				while ($item = $db->fetchassoc($inq))
				{
					if (strpos($keywords, $item['tagword']) === false)
					{
						$tag_word.= $item['tagword'].', ';
					}
				}
				$tag_word = chop($tag_word, ', ');
				$keywords = ( ! empty($keywords)) ? trim($keywords).', '.$tag_word : $tag_word;
				$keywords = chop($keywords, ', ');
			}

			if ( ! empty($author))
			{
				$author = preg_replace('/[^\pL\pNd\pZs\pP\pM]/us', '', $author);
			}

			$hits = ($hits) ? preparse($hits, THIS_INT) : 0;
			$catid = preparse($catid, THIS_INT);
			$image = preparse($image, THIS_TRIM, 0, 255);
			$author = preparse($author, THIS_TRIM, 0, 255);
			$person = preparse($person, THIS_TRIM, 0, 255);
			$country = preparse($country, THIS_TRIM, 0, 255);
			$region = preparse($region, THIS_TRIM, 0, 255);
			$city = preparse($city, THIS_TRIM, 0, 255);
			$skype = preparse($skype, THIS_TRIM, 0, 255);
			$site = preparse($site, THIS_TRIM, 0, 255);
			$email = preparse($email, THIS_TRIM, 0, 255);
			$image_alt = preparse($image_alt, THIS_TRIM, 0, 255);
			$image_thumb =  preparse($image_thumb, THIS_TRIM, 0, 255);
			$public = (empty($public)) ? NEWTIME : ReDate($public);
			$stpublic = (ReDate($stpublic) > 0) ? ReDate($stpublic) : 0;
			$unpublic = (ReDate($unpublic) > 0) ? ReDate($unpublic) : 0;
			$image_align = ($image_align == 'left') ? 'left' : 'right';
			$acc = ($acc == 'user' OR $acc == 'group') ? 'user' : 'all';
			$act = ($act == 'yes') ? 'yes' : 'no';
			$vip = ($vip == 1) ? 1 : 0;
			$site = ( ! parse_url($site, PHP_URL_SCHEME) AND ! empty($site)) ? 'http://'.$site : $site;
			$letid = preparse($letid, THIS_INT);
			$letid = ($conf[PERMISS]['letter'] == 'yes' AND isset($letter[$letid])) ? $letid : 0;

			$db->query
				(
					"UPDATE ".$basepref."_".PERMISS." SET
					 catid       = '".$catid."',
					 letid       = '".$letid."',
					 public      = '".$public."',
					 stpublic    = '".$stpublic."',
					 unpublic    = '".$unpublic."',
					 cpu         = '".$cpu."',
					 title       = '".$db->escape(preparse_sp($title))."',
					 subtitle    = '".$db->escape(preparse_sp($subtitle))."',
					 customs     = '".$db->escape(preparse_sp($customs))."',
					 descript    = '".$db->escape(preparse_dp($descript))."',
					 keywords    = '".$db->escape(preparse_dp($keywords))."',
					 textshort   = '".$db->escape($textshort)."',
					 textmore    = '".$db->escape($textmore)."',
					 textnotice  = '".$db->escape($textnotice)."',
					 reason      = '".$db->escape($reason)."',
					 image       = '".$db->escape($image)."',
					 image_thumb = '".$db->escape($image_thumb)."',
					 image_align = '".$image_align."',
					 image_alt   = '".$db->escape(preparse_sp($image_alt))."',
					 images      = '".$db->escape($images)."',
					 country     = '".$db->escape($country)."',
					 region      = '".$db->escape($region)."',
					 city        = '".$db->escape($city)."',
					 address     = '".$db->escape($address)."',
					 author      = '".$db->escape($author)."',
					 person      = '".$db->escape($person)."',
					 phone       = '".$db->escape($phone)."',
					 site        = '".$site."',
					 email       = '".$email."',
					 skype       = '".$db->escape($skype)."',
					 files       = '".$db->escape($files)."',
					 facc        = '".$facc."',
					 fgroups     = '".$db->escape($fgroups)."',
					 acc         = '".$acc."',
					 groups      = '".$db->escape($group)."',
					 hits        = '".$hits."',
					 act         = '".$act."',
					 vip         = '".$db->escape($vip)."',
					 tags        = '".$tags."',
					 social      = '".$db->escape($social)."',
					 pid         = '".$pid."'
					 WHERE id = '".$id."'"
				);

			if ($conf[PERMISS]['multcat'] == 'yes')
			{
				$counts = new Counts(PERMISS, 'id', 0);
				$counts->edit($subcat, $catid, $id);
			}
			else
			{
				$counts = new Counts(PERMISS, 'id');
			}

			$cache->cachesave(1);

			/**
			 * Сообщение
			 */
			if ($send == 'ok')
			{
				if ($pid == 0)
				{
					// Успешно
					$subject = "Re: ".$lang['firmsedit_subject']." - ".$conf['site'];
					$message = this_text(array
						(
							"br"    => "\r\n",
							"title" => $title,
							"link"  => $conf['site_url'].$ro->seo("index.php?dn=".PERMISS."&amp;to=page&amp;id=".$id),
							"site"  => $conf['site'],
							"date"  => format_time(NEWTIME, 1, 1)
						),
						$lang['reedit_msg_ok']
					);
					send_mail($email, $subject, $message, $conf['site'], 'robot_'.$conf['site_mail']);

				}
				elseif ($pid == 1)
				{
					// Отклонить
					$subject = "Re: ".$lang['firmsedit_subject']." - ".$conf['site'];
					$message = this_text(array
						(
							"br"     => "\r\n",
							"title"  => $title,
							"link"   => $conf['site_url'].$ro->seo("index.php?dn=".PERMISS."&amp;re=my&amp;to=edit&amp;id=".$id),
							"notice" => $reason,
							"site"   => $conf['site'],
							"date"   => format_time(NEWTIME, 1, 1)
							),
							$lang['reedit_msg_no']
					);
					send_mail($email, $subject, $message, $conf['site'], 'robot_'.$conf['site_mail']);
				}
			}

			$redir = 'index.php?dn=list&ops='.$sess['hash'];
			$redir.= (!empty($p)) ? '&p='.preparse($p, THIS_INT) : '';
			$redir.= (!empty($cat)) ? '&cat='.$cat : '';
			$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= (!empty($s)) ? '&s='.$s : '';
			$redir.= (!empty($l)) ? '&l='.$l : '';
			$redir.= ($fid > 0) ? '&fid='.$fid : '';

			redirect($redir);
		}

		/**
		 * Изменение состояния (вкл./выкл.)
		 ------------------------------------*/
		if ($_REQUEST['dn'] == 'act')
		{
			global $act, $id, $p, $nu, $cat, $s, $l, $fid;

			$id = preparse($id, THIS_INT);
			$act = preparse($act, THIS_TRIM);

			if ($act == 'no' OR $act == 'yes')
			{
				if ($conf[PERMISS]['multcat'] == 'yes')
				{
					$counts = new Counts(PERMISS, 'id', 0);
					$counts->act($id, $act);
				}
				$db->query("UPDATE ".$basepref."_".PERMISS." SET act='".$act."' WHERE id = '".$id."'");
				if ($conf[PERMISS]['multcat'] == 'no')
				{
					$counts = new Counts(PERMISS, 'id');
				}
			}

			$cache->cachesave(1);
			$nu = isset($nu) ? $nu : (isset($_COOKIE['num']) ? $_COOKIE['num'] : null);

			$redir = 'index.php?dn=list&amp;ops='.$sess['hash'];
			$redir.= ($cat !== '') ? '&amp;cat='.$cat : '';
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= ( ! empty($s)) ? '&amp;s='.$s : '';
			$redir.= ( ! empty($l)) ? '&amp;l='.$l : '';
			$redir.= ($fid > 0) ? '&amp;fid='.$fid : '';

			redirect($redir);
		}

		/**
		 * Удаление организации
		 -------------------------*/
		if ($_REQUEST['dn'] == 'del')
		{
			global $id, $p, $l, $s, $cat, $nu, $ok, $fid, $new;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>',
					$lang['all_delet']
				);

			$id = preparse($id, THIS_INT);
			$fid = preparse($fid, THIS_INT);

			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'"));

			if ($ok == 'yes')
			{
				if ($conf[PERMISS]['multcat'] == 'yes')
				{
					$counts = new Counts(PERMISS, 'id', 0);
					$counts->del($id);
				}

				if ( ! empty($item['image_thumb']))
				{
					unlink(WORKDIR.'/'.$item['image']);
					unlink(WORKDIR.'/'.$item['image_thumb']);
				}

				if ( ! empty($item['files']))
				{
					$fp = Json::decode($item['files']);
					if (is_array($fp) AND sizeof($fp) > 0)
					{
						foreach ($fp as $v)
						{
							if (file_exists(WORKDIR.'/'.$v['path']))
							{
								unlink(WORKDIR.'/'.$v['path']);
							}
						}
					}
				}

				$db->query("DELETE FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'");
				$db->query("DELETE FROM ".$basepref."_reviews WHERE file = '".PERMISS."' AND pageid = '".$id."'");
				$db->query("DELETE FROM ".$basepref."_rating WHERE file = '".PERMISS."' AND id = '".$id."'");

				$inq = $db->query("SELECT * FROM ".$basepref."_".PERMISS."_photo WHERE firm_id = '".$id."'");
				if ($db->numrows($inq) > 0)
				{
					while ($photo = $db->fetchassoc($inq))
					{
						$db->query("DELETE FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$photo['id']."'");

						if (file_exists(DNDIR.$photo['image_thumb']))
						{
							unlink(DNDIR.$photo['image']);
							unlink(DNDIR.$photo['image_thumb']);
						}
					}
				}

				$inqs = $db->query("SELECT * FROM ".$basepref."_".PERMISS."_video WHERE firm_id = '".$id."'");
				if ($db->numrows($inqs) > 0)
				{
					while ($video = $db->fetchassoc($inqs))
					{
						$db->query("DELETE FROM ".$basepref."_".PERMISS."_photo WHERE id = '".$video['id']."'");

						if (file_exists(DNDIR.$video['image']))
						{
							unlink(DNDIR.$video['image']);
						}
					}
				}

				if ($conf[PERMISS]['multcat'] == 'no')
				{
					$counts = new Counts(PERMISS, 'id');
				}

				$db->increment(PERMISS);
				$cache->cachesave(1);

				$redir = 'index.php?dn=list&amp;ops='.$sess['hash'];
				$redir.= (!empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
				$redir.= (!empty($cat)) ? '&amp;cat='.$cat : '';
				$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
				$redir.= (!empty($s)) ? '&amp;s='.$s : '';
				$redir.= (!empty($l)) ? '&amp;l='.$l : '';
				$redir.= ($fid > 0) ? '&amp;fid='.$fid : '';

				redirect($redir);
			}
			else
			{
				$yes = 'index.php?dn=del&amp;p='.$p.'&amp;s='.$s.'&amp;l='.$l.'&amp;cat='.$cat.'&amp;nu='.$nu.'&amp;id='.$id.'&amp;ok=yes&amp;ops='.$sess['hash'].(($fid > 0) ? '&amp;fid='.$fid : '');
				$not = 'index.php?dn=list&amp;p='.$p.'&amp;s='.$s.'&amp;l='.$l.'&amp;cat='.$cat.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'].(($fid > 0) ? '&amp;fid='.$fid : '');

				$tm->header();
				$tm->shortdel($modname[PERMISS], $lang['all_delet'], $yes, $not, preparse_un($item['title']));
				$tm->footer();
			}
		}

		/**
		 * Отзывы
		 ---------------------*/
		if (
			$_REQUEST['dn'] == 'reviews' OR
			$_REQUEST['dn'] == 'newreviews' OR
			$_REQUEST['dn'] == 'firmreviews'
		) {
			global $dn, $id, $nu, $p, $atime;

			$id    = preparse($id, THIS_INT);
			$atime = preparse($atime, THIS_INT);

			$active = ($dn == 'newreviews') ? 0 : 1;
			$sql = ($dn == 'firmreviews') ? " pageid = '".$id."' AND " : "";
			$sql_inq = ($dn == 'firmreviews') ? " a.pageid = '".$id."' AND " : "";

			$caption = ($dn == 'newreviews') ? $lang['reviews_new'] : $lang[$dn];
			$crumb = $caption = ($atime == 0) ? $caption : $lang['reviews_new'];

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>',
					$crumb
				);

			$tm->header();

			if(isset($nu) AND ! empty($nu)) {
				echo '<script>cookie.set("num", "'.$nu.'", { path: "'.ADMPATH.'/" });</script>';
			}
			$nu = isset($nu) ? $nu : (isset($_COOKIE['num']) ? $_COOKIE['num'] : null);

			$nu = (isset($nu) AND in_array($nu, $conf['num'])) ? $nu : $conf['num'][0];
			$p  = ( ! isset($p) OR $p <= 1) ? 1 : $p;

			$total = $db->fetchassoc($db->query("SELECT COUNT(reid) AS total FROM ".$basepref."_reviews WHERE".$sql." file = '".PERMISS."' AND (public >= '".$atime."') AND active = '".$active."'"));
			if (($p - 1) * $nu > $total['total'])
			{
				$p = 1;
			}
			$sf = $nu * ($p - 1);

			$pages = $lang['all_pages'].':&nbsp; '.adm_pages("reviews WHERE".$sql." active = '".$active."' AND (public >= '".$atime."') ORDER BY reid DESC", 'pageid', 'index', $dn.'&amp;id='.$id.'&amp;atime='.$atime, $nu, $p, $sess);
			$amount = $lang['all_col'].':&nbsp; '.amount_pages('index.php?dn='.$dn.'&amp;id='.$id.'&amp;p='.$p.'&amp;atime='.$atime.'&amp;ops='.$sess['hash'], $nu);

			$inq = $db->query
						(
							"SELECT a.*, b.id, b.title FROM ".$basepref."_reviews AS a
							 LEFT JOIN ".$basepref."_".PERMISS." AS b ON (a.pageid = b.id)
							 WHERE".$sql_inq." a.file = '".PERMISS."' AND (a.public >= '".$atime."') AND a.active = '".$active."'
							 ORDER BY a.public DESC LIMIT ".$sf.", ".$nu
						);

			echo '	<script>
					$(document).ready(function()
					{
						$("#select").click(function() {
							$("#reviews-form input[type=checkbox]").each(function() {
								this.checked = (this.checked) ? false : true;
							});
						});
					});
					</script>';

			echo '	<div class="section">
					<form id="reviews-form" action="index.php" method="post">
					<table id="list" class="work">
						<caption>'.$modname[PERMISS].': '.$caption.'</caption>
						<tr><td colspan="10">'.$amount.'</td></tr>
						<tr>
							<th>ID</th>
							<th>'.$lang['firms'].'</th>
							<th>'.$lang['review_all'].'</th>
							<th>'.$lang['one_add'].'</th>
							<th>'.$lang['author'].'</th>
							<th>'.$lang['sys_manage'].'</th>
							<th class="ac"><input name="checkboxall" id="checkboxall" value="yes" type="checkbox" /></th>
						</tr>';
			if ($db->numrows($inq) > 0)
			{
				while ($item = $db->fetchassoc($inq))
				{
					echo '	<tr class="list">
								<td class="ac sw50">'.$item['reid'].'</td>
								<td class="pw20">'.$item['title'].'</td>
								<td class="pw35">'.$item['message'].'</td>
								<td class="pw15">'.format_time($item['public'], 1, 1).'</td>
								<td class="pw15">'.(($item['userid'] > 0) ? '<a href="'.ADMPATH.'/mod/user/index.php?dn=edit&amp;uid='.$item['userid'].'&amp;ops='.$sess['hash'].'" title="'.$lang['all_edit'].'">'.$item['uname'].'</a>' : $item['uname']).'</td>
								<td class="gov pw10">
									<a href="index.php?dn=reviewedit&amp;reid='.$item['reid'].'&amp;req='.$dn.'&amp;id='.$item['id'].'&amp;p='.$p.'&amp;nu='.$nu.'&amp;atime='.$atime.'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/edit.png" alt="'.$lang['all_edit'].'" /></a>
									<a href="index.php?dn=reviewdel&amp;reid='.$item['reid'].'&amp;req='.$dn.'&amp;id='.$item['id'].'&amp;p='.$p.'&amp;nu='.$nu.'&amp;atime='.$atime.'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/del.png" alt="'.$lang['all_delet'].'" /></a>
								</td>
								<td class="mark sw50"><input type="checkbox" name="array['.$item['reid'].']" value="'.$item['id'].'" /></td>
							</tr>';
				}
			}
			else
			{
				echo '	<tr>
							<td class="ac" colspan="10">
								<div class="pads">'.$lang['data_not'].'</div>
							</td>
						</tr>';
			}
			echo '		<tr>
							<td colspan="10">'.$lang['all_mark_work'].':&nbsp;
								<select name="names">';
			if ($dn == 'newreviews') {
				echo '				<option value="active">'.$lang['all_submint'].'</option>';
			}
			echo '					<option value="del">'.$lang['all_delet'].'</option>
								</select>
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input type="hidden" name="atime" value="'.$atime.'" />
								<input type="hidden" name="req" value="'.$dn.'" />
								<input type="hidden" name="id" value="'.$id.'" />
								<input type="hidden" name="p" value="'.$p.'" />
								<input type="hidden" name="nu" value="'.$nu.'" />
								<input type="hidden" name="dn" value="reviewwork" />
								<input id="button" class="side-button" value="'.$lang['all_go'].'" type="submit" />
							</td>
						</tr>
						<tr><td class="sort ar" colspan="10">'.$pages.'</td></tr>
					</table>
					</form>
					</div>';

			$tm->footer();
		}

		/**
		 * Массовая обработка отзывов
		 ------------------------------*/
		if ($_REQUEST['dn'] == 'reviewwork')
		{
			global $req, $id, $p, $nu, $ok, $names, $atime, $array;

			$atime = preparse($atime, THIS_INT);
			$caption = ($req == 'newreviews') ? $lang['reviews_new'] : (($atime == 0) ? $lang[$req] : $lang['reviews_new']);

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>',
					$caption
				);

			$id = preparse($id, THIS_INT);
			$nu = preparse($nu, THIS_INT);
			$p  = preparse($p, THIS_INT);

			if (preparse($array, THIS_ARRAY) == 1)
			{
				$hidden = null;
				while (list($reid, $fid) = each($array))
				{
					$hidden.= '<input type="hidden" name="array['.$reid.']" value="'.$fid.'" />';
				}
				$h = '	<input type="hidden" name="p" value="'.$p.'" />
						<input type="hidden" name="nu" value="'.$nu.'" />
						<input type="hidden" name="req" value="'.$req.'" />
						<input type="hidden" name="atime" value="'.$atime.'" />
						<input type="hidden" name="id" value="'.$id.'" />';

				$count = count($array);

				// Удаление
				if ($names == 'del')
				{
					$tm->header();
					echo '	<div class="section">
							<form action="index.php" method="post">
							<table id="arr-work" class="work">
								<caption>'.$lang['array_control'].': '.$lang['all_delet'].' ('.$count.')</caption>
								<tr>
									<td class="cont">'.$lang['alertdel'].'</td>
								</tr>
								<tr class="tfoot">
									<td>
										'.$hidden.'
										'.$h.'
										<input type="hidden" name="dn" value="reviewarrdel" />
										<input type="hidden" name="ops" value="'.$sess['hash'].'" />
										<input class="side-button" value="'.$lang['all_go'].'" type="submit" />
										<input class="side-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button" />
									</td>
								</tr>
							</table>
							</form>
							</div>';
					$tm->footer();

				// Добавление
				}
				elseif ($names == 'active')
				{
					$tm->header();
					echo '	<div class="section">
							<form action="index.php" method="post">
							<table id="arr-work" class="work">
								<caption>'.$lang['array_control'].': '.$lang['all_add'].' ('.$count.')</caption>
								<tr>
									<td class="cont">'.$lang['add_mass'].'<br />'.$lang['confirm_del'].'</td>
								</tr>
								<tr class="tfoot">
									<td>
										'.$hidden.'
										'.$h.'
										<input type="hidden" name="dn" value="reviewarract" />
										<input type="hidden" name="ops" value="'.$sess['hash'].'" />
										<input class="side-button" value="'.$lang['all_go'].'" type="submit" />
										<input class="side-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button" />
									</td>
								</tr>
							</table>
							</form>
							</div>';
					$tm->footer();
				}
			}
		}

		/**
		 * Массовое удаление отзывов
		 -----------------------------*/
		if ($_REQUEST['dn'] == 'reviewarrdel')
		{
			global $req, $id, $p, $nu, $ok, $atime, $array;

			$atime = preparse($atime, THIS_INT);
			$caption = ($atime == 0) ? $lang[$req] : $lang['reviews_new'];

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>',
					$caption
				);

			$id = preparse($id, THIS_INT);
			$nu = preparse($nu, THIS_INT);
			$p  = preparse($p, THIS_INT);

			if (is_array($array) AND ! empty($array))
			{
				while (list($reid, $fid) = each($array))
				{
					if ($req <> 'newreviews')
					{
						$db->query("UPDATE ".$basepref."_".PERMISS." SET reviews = reviews - 1 WHERE id = '".$fid."'");
					}
					$db->query("DELETE FROM ".$basepref."_reviews WHERE file = '".PERMISS."' AND reid = '".$reid."'");
				}
				$cache->cachesave(1);
			}

			redirect('index.php?dn='.$req.'&amp;id='.$id.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;atime='.$atime.'&amp;ops='.$sess['hash']);
		}

		/**
		 * Массовое добавление отзывов
		 -------------------------------*/
		if ($_REQUEST['dn'] == 'reviewarract')
		{
			global $req, $id, $p, $nu, $array;

			$caption = ($req == 'newreviews') ? $lang['reviews_new'] : $lang[$dn];
			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>',
					$caption
				);

			$id = preparse($id, THIS_INT);
			$nu = preparse($nu, THIS_INT);
			$p  = preparse($p, THIS_INT);

			if ($req == 'newreviews')
			{
				if (is_array($array) AND ! empty($array))
				{
					while (list($reid, $fid) = each($array))
					{
						$db->query("UPDATE ".$basepref."_".PERMISS." SET reviews = reviews + 1 WHERE id = '".$fid."'");
						$db->query("UPDATE ".$basepref."_reviews SET active = '1' WHERE file = '".PERMISS."' AND reid = '".$reid."'");
					}
					$cache->cachesave(1);
				}
			}

			redirect('index.php?dn='.$req.'&amp;id='.$id.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash']);
		}

		/**
		 * Редактировать отзыв
		 -----------------------*/
		if ($_REQUEST['dn'] == 'reviewedit')
		{
			global $id, $reid, $req, $p, $nu, $atime;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['reviews'].'</a>',
					$lang[$req]
				);

            $tm->header();

            $reid = preparse($reid, THIS_INT);
            $item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_reviews WHERE file = '".PERMISS."' AND reid = '".$reid."'"));
			$time = CalendarFormat($item['public']);

			echo '	<div class="section">
					<form action="index.php" method="post">
					<table class="work">
						<caption>'.$lang[$req].': '.$lang['review_edit'].'</caption>
						<tr>
							<td class="first"><span>*</span> '.$lang['author'].'</td>
							<td><input type="text" name="uname" size="20" value="'.$item['uname'].'" required="required" /></td>
						</tr>
						<tr>
							<td>'.$lang['one_add'].'</td>
							<td><input type="text" name="public" id="public" value="'.$time.'" />';
								Calendar('cal', 'public');
			echo '			</td>
						</tr>
						<tr>
							<td class="first"><span>*</span> '.$lang['message'].'</td>
							<td class="usewys">';
			if ($wysiwyg == 'yes')
			{
				define("USEWYS", 1);
				$form_short = 'message';
				$WYSFORM = 'message';
				$WYSVALUE = $item['message'];
				include(ADMDIR.'/includes/wysiwyg.php');
			}
			else
			{
				$tm->textarea('message', 5, 70, $item['message'], 1, '', '', 1);
			}
			echo '			</td>
						</tr>';
			if ($req == 'newreviews')
			{
				echo '	<tr>
							<td>'.$lang['all_add'].'</td>
							<td><input type="checkbox" name="active" value="1" checked /></td>
						</tr>';
			}
			echo '		<tr class="tfoot">
							<td colspan="2">
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input type="hidden" name="atime" value="'.$atime.'" />
								<input type="hidden" name="req" value="'.$req.'" />
								<input type="hidden" name="reid" value="'.$reid.'" />
								<input type="hidden" name="id" value="'.$id.'" />
								<input type="hidden" name="p" value="'.$p.'" />
								<input type="hidden" name="nu" value="'.$nu.'" />
								<input type="hidden" name="dn" value="revieweditsave" />
								<input class="main-button" value="'.$lang['all_save'].'" type="submit" />
							</td>
						</tr>
					</table>
					</form>
					</div>';

			$tm->footer();
		}

		/**
		 * Редактировать отзыв (сохранение)
		 ------------------------------------*/
		if ($_REQUEST['dn'] == 'revieweditsave')
		{
			global $req, $id, $reid, $p, $nu, $public, $uname, $message, $active, $atime;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['reviews'].'</a>',
					$lang[$req]
				);

			$p = preparse($p, THIS_INT);
			$nu = preparse($nu, THIS_INT);

			$id = preparse($id, THIS_INT);
			$reid = preparse($reid, THIS_INT);
			$uname = preparse($uname, THIS_TRIM, 0, 255);
			$message = preparse($message, THIS_TRIM);
			$public = (empty($public)) ? NEWTIME : ReDate($public);
			$active = ($req == 'newreviews' AND $active) ? ", active  = '1'" : "";

			if (
				preparse($uname, THIS_EMPTY) == 1 OR
				preparse($message, THIS_EMPTY) == 1
			)
			{
				$tm->header();
				$tm->error($lang[$req], $lang['review_edit'], $lang['forgot_name']);
				$tm->footer();
			}

			$db->query
				(
					"UPDATE ".$basepref."_reviews SET
					 public  = '".$public."',
					 uname   = '".$db->escape(preparse_sp($uname))."',
					 message = '".$db->escape($message)."'
					 ".$active."
					 WHERE file = '".PERMISS."' AND reid = '".$reid."'"
				);

			if ($active)
			{
				$db->query("UPDATE ".$basepref."_".PERMISS." SET reviews = reviews + 1 WHERE id = '".$id."'");
			}

			$cache->cachesave(1);

			$redir = 'index.php?dn='.$req.'&amp;id='.$id;
			$redir.= ( ! empty($p) ) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu) ) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
			$redir.= ( ! empty($atime) ) ? "&amp;atime=".preparse($atime, THIS_INT) : '';
			$redir.= '&amp;ops='.$sess['hash'];

			redirect($redir);
		}

		/**
		 * Удалить отзыв
		 -----------------*/
		if ($_REQUEST['dn'] == 'reviewdel')
		{
			global $reid, $id, $req, $ok, $p, $nu, $atime;

			$atime = preparse($atime, THIS_INT);
			$caption = ($atime == 0) ? $lang[$req] : $lang['reviews_new'];

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['all_firms'].'</a>',
					$caption
				);

			$id = preparse($id, THIS_INT);
			$reid = preparse($reid, THIS_INT);

			if ($ok == 'yes')
			{
				if ($req <> 'newreviews')
				{
					$db->query("UPDATE ".$basepref."_".PERMISS." SET reviews = reviews - 1 WHERE id = '".$id."'");
				}

				$db->query("DELETE FROM ".$basepref."_reviews WHERE file = '".PERMISS."' AND reid = '".$reid."'");
				$cache->cachesave(1);

                $redir = 'index.php?dn='.$req.'&amp;ops='.$sess['hash'];
                $redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
                $redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';
                $redir.= ( ! empty($atime)) ? "&amp;atime=".preparse($atime, THIS_INT) : '';
                $redir.= ( ! empty($id)) ? '&amp;id='.$id : '';

                redirect($redir);
			}
			else
			{
                $item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_reviews WHERE file = '".PERMISS."' AND reid = '".$reid."'"));

				$not = 'index.php?dn='.$req.'&amp;p='.$p.'&amp;nu='.$nu.'&amp;id='.$id.'&amp;atime='.$atime.'&amp;ops='.$sess['hash'];
				$yes = 'index.php?dn=reviewdel&amp;req='.$req.'&amp;reid='.$reid.'&amp;id='.$id.'&amp;ok=yes&amp;p='.$p.'&amp;nu='.$nu.'&amp;atime='.$atime.'&amp;ops='.$sess['hash'];

				$tm->header();
				$tm->shortdel($caption, $lang['review_del'], $yes, $not, preparse_un($item['message']));
				$tm->footer();
			}
		}

		/**
		 * Добавленные организации
		 ----------------------------*/
		if ($_REQUEST['dn'] == 'new')
		{
			global $nu, $p, $cat;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['firms_all'].'</a>',
					$lang['all_new_firms']
				);

			$tm->header();

			if (isset($conf['userbase']))
			{
				if ($conf['userbase'] == 'danneo') {
					require_once(WORKDIR.'/core/userbase/danneo/danneo.user.php');
				} else {
					require_once(WORKDIR.'/core/userbase/'.$conf['userbase'].'/danneo.user.php');
				}

				$userapi = new userapi($db, false);
			}

			require_once(WORKDIR.'/core/classes/Router.php');
			$ro = new Router();

			echo '<script>cookie.set("num", "'.$nu.'", { path: "'.ADMPATH.'/" });</script>';
			$nu = isset($nu) ? $nu : (isset($_COOKIE['num']) ? $_COOKIE['num'] : null);

			$nu = (isset($nu) AND in_array($nu, $conf['num'])) ? $nu : $conf['num'][0];
			$p = (!isset($p) OR $p <= 1) ? 1 : $p;
			$sf = $nu * ($p - 1);

			$inq = $db->query("SELECT * FROM ".$basepref."_".PERMISS."_user ORDER BY id DESC LIMIT ".$sf.", ".$nu);
			$pages = $lang['pages'].':&nbsp; '.adm_pages(PERMISS.'_user', 'id', 'index', 'new', $nu, $p, $sess);
			$amount = $lang['all_col'].':&nbsp; '.amount_pages("index.php?dn=new&amp;p=".$p."&amp;ops=".$sess['hash'], $nu);

			$work = array();
			while ($item = $db->fetchassoc($inq))
			{
				$work[$item['id']] = $item;
			}

			$catcache = $catcaches = array();
			$inquiry = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");
			while ($item = $db->fetchassoc($inquiry))
			{
				$catcache[$item['parentid']][$item['catid']] = $item;
				$catcaches[$item['catid']] = array($item['parentid'], $item['catid'], $item['catname']);
			}

			echo '	<div class="section">
					<form action="index.php" method="post">
					<table id="list" class="work">
						<caption>'.$lang[PERMISS].': '.$lang['addit_new_firms'].'</caption>';
			if ($db->numrows($inq) > 0)
			{
				echo '	<tr><td colspan="7">'.$amount.'</td></tr>
						<tr>
							<th>ID</th>
							<th>'.$lang['all_name'].'&nbsp; &#8260; &nbsp;'.$lang['all_cat_one'].'</th>
							<th>'.$lang['one_add'].'</th>
							<th>'.$lang['author'].'</th>
							<th>'.$lang['ip_adress'].'</th>
							<th>'.$lang['sys_manage'].'</th>
							<th class="ac pw5"><input name="checkboxall" id="checkboxall" value="yes" type="checkbox" /></th>
						</tr>';
				foreach ($work as $k => $item)
				{
					// Автор
					$author = '&#8212;';
					if ( ! empty($item['userid']))
					{
						if (in_array('user', $realmod))
						{
							$udata = $userapi->userdata('userid', $item['userid']);
							if ( ! empty($udata))
							{
								$author = '<a href="'.$conf['site_url'].$ro->seo($userapi->data['linkprofile'].$item['userid']).'" title="'.$lang['profile'].' - '.$udata['uname'].'" target="_blank">'.$udata['uname'].'</a>';
							}
						}
					}

					echo '	<tr id="lists" class="list">
								<td class="ac sw50">'.$item['id'].'</td>
								<td class="pw25">
									<div>'.$item['title'].'</div>
									<div class="cats">'.preparse_un(linecat($item['catid'], $catcaches)).'</div>
								</td>
								<td>'.format_time($item['public'], 1, 1).'</td>
								<td>'.$author.'</td>
								<td class="pw10">'.$item['addip'].'</td>
								<td class="gov pw10">
									<a href="index.php?dn=newadd&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/edit.png" alt="'.$lang['moderate'].'" /></a>
									<a href="index.php?dn=newdel&amp;p='.$p.'&amp;nu='.$nu.'&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/del.png" alt="'.$lang['all_delet'].'" /></a>
								</td>
								<td class="check">
									<input type="checkbox" name="array['.$item['id'].']" value="yes" />
								</td>
							</tr>';
				}
				echo '		<tr><td colspan="7">'.$pages.'</td></tr>
							<tr class="tfoot">
								<td colspan="7">
									<input type="hidden" name="dn" value="newarrdel" />
									<input type="hidden" name="p" value="'.$p.'" />
									<input type="hidden" name="nu" value="'.$nu.'" />
									<input type="hidden" name="ops" value="'.$sess['hash'].'" />
									<input class="main-button" value="'.$lang['all_delet'].'" type="submit" />
								</td>
							</tr>';
			}
			else
			{
				echo '	<tr>
							<td class="ac">
								<div class="pads">'.$lang['all_new_firms_no'].'</div>
							</td>
						</tr>';
			}
			echo '	</table>
					</form>
					</div>';
			$tm->footer();
		}

		/**
		 * Удаление новой организации
		 -----------------------------*/
		if ($_REQUEST['dn'] == 'newdel')
		{
			global $id, $p, $cat, $nu, $ok, $fid;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=new&amp;ops='.$sess['hash'].'">'.$lang['all_new_firms'].'</a>',
					$lang['all_delet']
				);

			$id = preparse($id, THIS_INT);
			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_user WHERE id = '".$id."'"));

			if ($ok == 'yes')
			{
				if ( ! empty($item['image_thumb']) AND file_exists(WORKDIR.'/'.$item['image_thumb']))
				{
					unlink(WORKDIR.'/'.$item['image']);
					unlink(WORKDIR.'/'.$item['image_thumb']);
				}

				if ( ! empty($item['files']))
				{
					$fp = Json::decode($item['files']);
					if (is_array($fp) AND sizeof($fp) > 0)
					{
						foreach ($fp as $v)
						{
							if (file_exists(WORKDIR.'/'.$v['path']))
							{
								unlink(WORKDIR.'/'.$v['path']);
							}
						}
					}
				}

				$db->query("DELETE FROM ".$basepref."_".PERMISS."_user WHERE id = '".$id."'");

				$redir = 'index.php?dn=new&amp;ops='.$sess['hash'];
				$redir.= (!empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
				$redir.= (!empty($cat)) ? '&amp;cat='.$cat : '';
				$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';

				redirect($redir);
			}
			else
			{
				$yes = 'index.php?dn=newdel&amp;p='.$p.'&amp;cat='.$cat.'&amp;nu='.$nu.'&amp;id='.$id.'&amp;ok=yes&amp;ops='.$sess['hash'];
				$not = 'index.php?dn=new&amp;p='.$p.'&amp;cat='.$cat.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'];

				$tm->header();
				$tm->shortdel($modname[PERMISS], $lang['all_delet'], $yes, $not, preparse_un($item['title']));
				$tm->footer();
			}
		}

		/**
		 * Массовое удаление добавленных организаций
		 ---------------------------------------------*/
		if ($_REQUEST['dn'] == 'newarrdel')
		{
			global $array, $p, $nu, $ok;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=new&amp;ops='.$sess['hash'].'">'.$lang['all_new_firms'].'</a>',
					$lang['array_control']
				);

			$p   = preparse($p, THIS_INT);
			$nu  = preparse($nu, THIS_INT);

			if (preparse($array, THIS_ARRAY) == 1)
			{
				if ($ok == 'yes')
				{
					allarrdel($array, 'id', PERMISS.'_user', 0, 1);

					$redir = 'index.php?dn=new&amp;ops='.$sess['hash'];
					$redir.= (!empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
					$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';

					redirect($redir);
				}
				else
				{
					$temparray = $array;
					$count = count($temparray);

					$hidden = null;
					foreach ($array as $key => $id)
					{
						$hidden.= '<input type="hidden" name="array['.$key.']" value="yes" />';
					}

					$h = '	<input type="hidden" name="p" value="'.$p.'" />
							<input type="hidden" name="nu" value="'.$nu.'" />';

					$tm->header();

					echo '	<div class="section">
							<form action="index.php" method="post">
							<table id="arr-work" class="work">
								<caption>'.$lang['all_new_firms'].': '.$lang['all_delet'].' ('.$count.')</caption>
								<tr>
									<td class="cont">'.$lang['alertdel'].'</td>
								</tr>
								<tr class="tfoot">
									<td>
										'.$hidden.'
										'.$h.'
										<input type="hidden" name="dn" value="newarrdel" />
										<input type="hidden" name="ok" value="yes" />
										<input type="hidden" name="ops" value="'.$sess['hash'].'" />
										<input class="main-button" value="'.$lang['all_go'].'" type="submit" />
										<input class="main-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button" />
									</td>
								</tr>
							</table>
							</form>
							</div>';

					$tm->footer();
				}
			}

			$redir = 'index.php?dn=new&amp;ops='.$sess['hash'];
			$redir.= (!empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Модерировать добавленную организацию
		 -----------------------------------------*/
		if ($_REQUEST['dn'] == 'newadd')
		{
			global $selective, $id, $p, $nu, $pid, $sess;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					$lang['all_new_firms']
				);

			$tm->header();

			if (isset($conf['userbase']))
			{
				if ($conf['userbase'] == 'danneo') {
					require_once(WORKDIR.'/core/userbase/danneo/danneo.user.php');
				} else {
					require_once(WORKDIR.'/core/userbase/'.$conf['userbase'].'/danneo.user.php');
				}

				$userapi = new userapi($db, false);
			}

			$id = preparse($id, THIS_INT);
			$p = preparse($p, THIS_INT);
			$nu = preparse($nu, THIS_INT);

			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_user WHERE id = '".$id."'"));
			$time = CalendarFormat($item['public']);
			$id = $item['id'];

			$author = '';
			if ( ! empty($item['author']))
			{
				$author = preg_replace('/[^\pL\pNd\pZs\pP\pM]/us', '', $item['author']);
				if (in_array('user', $realmod))
				{
					$udata = $userapi->userdata('uname', $author);
					if ( ! empty($udata))
					{
						require_once(WORKDIR.'/core/classes/Router.php');
						$ro = new Router();
						$author = '<a href="'.$conf['site_url'].$ro->seo($userapi->data['linkprofile'].$udata['userid']).'" target="_blank"><img src="'.ADMURL.'/template/images/blank.png" alt="'.$lang['profile'].' - '.$author.'" /></a>';
					}
				}
			}

			$stpublic = 0;
			$unpublic = 0;
			$acc = 'all';

			$inqcat = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");

			echo '	<script src="'.ADMPATH.'/js/jquery.apanel.tabs.js"></script>
					<script>
						var all_name     = "'.$lang['all_name'].'";
						var all_cpu      = "'.$lang['all_cpu'].'";
						var all_popul    = "'.$lang['all_popul'].'";
						var all_thumb    = "'.$lang['all_image_thumb'].'";
						var all_img      = "'.$lang['all_image'].'";
						var all_images   = "'.$lang['all_image_big'].'";
						var all_align    = "'.$lang['all_align'].'";
						var all_right    = "'.$lang['all_right'].'";
						var all_left     = "'.$lang['all_left'].'";
						var all_center   = "'.$lang['all_center'].'";
						var all_alt      = "'.$lang['all_alt_image'].'";
						var all_copy     = "'.$lang['all_copy'].'";
						var all_delet    = "'.$lang['all_delet'].'";
						var code_paste   = "'.$lang['code_paste'].'";
						var all_file     = "'.$lang['all_file'].'";
						var all_path     = "'.$lang['all_path'].'";
						var page_one     = "'.$lang['page_one'].'";
						var ops          = "'.$sess['hash'].'";
						var filebrowser  = "'.$lang['filebrowser'].'";
						var filereview   = "'.$lang['file_review'].'";
						var page         = "'.PERMISS.'";
						$(function() {
							$(".imgcount").focus(function () {
								$(this).select();
							}).mouseup(function(e){
								e.preventDefault();
							});
							var pid = "'.(( ! empty($item['user_notice'])) ? 1 : 0).'";
							if (pid == 1) {
								$(".notice").show();
								$(".notice textarea").removeAttr("required");
							}
							$("#pid").bind("change", function() {
								if ($(this).val() == "0" || $(this).val() == "del") {
									$(".notice").slideUp();
									$(".notice textarea").removeAttr("required");
								} else {
									$(".notice").slideDown();
									$(".notice textarea").attr("required", "required");
								}
							});
						});
					</script>';

			echo "	<script>
						$(function() {
							$.addfiles = function(form, area, path) {
								var id = $('#fileid').attr('value');
								if (id) {
									var html = '<div class=\"section tag\" id=\"file-' + id + '\" style=\"display: none;\">';
									html += '<table class=\"work\"><tr>';
									html += '<td class=\"vm sw150\">' + all_path + '</td>';
									html += '<td>';
									html += '<input name=\"files[' + id + '][path]\" id=\"files' + id + '\" size=\"50\" type=\"text\" required>&nbsp;';
									html += '<input class=\"side-button\" onclick=\"javascript:$.filebrowser(\'' + ops + '\',\'' + path + '\',\'&amp;field[1]=files' + id + '\')\" value=\"' + filebrowser + '\" type=\"button\"> ';
									html += '<input class=\"side-button\" onclick=\"javascript:$.fileallupload(\'' + ops + '&amp;objdir=' + path + '\',\'&amp;field=files' + id + '\')\" value=\"' + filereview + '\" type=\"button\">';
									html += '<a class=\"but fr\" href=\"javascript:$.removetaginput(\'total-form\',\'file-area\',\'file-' + id + '\');\">&#215;</a>';
									html += '</td></tr><tr>';
									html += '<td class=\"vm sw150\">' + all_name + '</td>';
									html += '<td><input name=\"files[' + id + '][title]\" size=\"50\" type=\"text\" required></td>';
									html += '</tr></table></div>';
									$('form[id=' + form + '] #' + area).append(html);
									$('form[id=' + form + '] #' + area + ' #file-' + id).show('normal');
									id++;
									$('#fileid').attr({
										value: id
									});
								}
							}
							$.addsocial = function(form, area) {
								var id = $('#socialid').attr('value');
								if (id) {
									var html = '<div class=\"section tag\" id=\"social-' + id + '\" style=\"display: none;\">';
									html += '<table class=\"work\"><tr>';
									html += '<td class=\"vm sw150 al\"><input name=\"social[' + id + '][title]\" id=\"social' + id + '\" size=\"15\" type=\"text\" placeholder=\"' + all_name + '\" required></td>';
									html += '<td class=\"vm\"><input name=\"social[' + id + '][link]\" size=\"50\" type=\"text\" placeholder=\"' + page_one + '\" required>';
									html += '<a class=\"but fr\" href=\"javascript:$.removetaginput(\'total-form\',\'social-area\',\'social-' + id + '\');\">&#215;</a></td>';
									html += '</tr></table></div>';
									$('form[id=' + form + '] #' + area).append(html);
									$('form[id=' + form + '] #' + area + ' #social-' + id).show('normal');
									id++;
									$('#socialid').attr({
										value: id
									});
								}
							}
						});
					</script>";

			$tabs = '	<div class="tabs" id="tabs">
							<a href="#" data-tabs=".tab-1">'.$lang['home'].'</a>
							<a href="#" data-tabs=".tab-2" style="display: none;"></a>
							<a href="#" data-tabs="all">'.$lang['all_field'].'</a>
						</div>';

			echo '	<div class="section">
					<form action="index.php" method="post" id="total-form">
					<table class="work">
						<caption>'.$lang['all_new_firms'].': '.$lang['firms_add'].'</caption>
						<tr>
							<th class="ar site">'.$lang['all_bookmark'].' &nbsp; </th>
							<th>'.$tabs.'</th>
						</tr>
						<tbody class="tab-1">
						<tr>
							<td class="first"><span>*</span> '.$lang['all_firms'].'</td>
							<td><input type="text" name="title" id="title" size="70" value="'.preparse_un($item['title']).'" required="required" /> <span class="light">&lt;h1&gt;</span></td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['sub_title'].'</td>
							<td><input type="text" name="subtitle" size="70" value="'.preparse_un($item['title']).'" /> <span class="light">&lt;h2&gt;</span></td>
						</tr>';
			if ($conf['cpu'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['all_cpu'].'</td>
							<td><input type="text" name="cpu" id="cpu" size="70" value="'.$item['cpu'].'" />';
								$tm->outtranslit('title', 'cpu', $lang['cpu_int_hint']);
				echo '		</td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['custom_title'].'</td>
							<td><input type="text" name="customs" size="70" /> <span class="light">&lt;title&gt;</span></td>
						</tr>
						<tr>
							<td>'.$lang['all_descript'].'</td>
							<td><input type="text" name="descript" size="70" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_keywords'].'</td>
							<td><input type="text" name="keywords" size="70" />';
								$tm->outhint($lang['keyword_hint']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_data'].'</td>
							<td><input type="text" name="public" id="public" value="'.$time.'" />';
								Calendar('cal', 'public');
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_stpublic'].'</td>
							<td><input type="text" name="stpublic" id="stpublic" />';
								Calendar('stcal', 'stpublic');
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_unpublic'].'</td>
							<td><input type="text" name="unpublic" id="unpublic" />';
								Calendar('uncal', 'unpublic');
			echo '			</td>
						</tr>';
			if ($db->numrows($inqcat) > 0 OR $conf[PERMISS]['letter'] == 'yes')
			{
				echo '	<tr><th></th><th class="site">'.$lang['all_cat'].'</th></tr>';
			}
			if ($db->numrows($inqcat) > 0)
			{
				echo '	<tr>
							<td>'.$lang['all_in_cat'].'</td>
							<td>
								<select id="catid" name="catid" class="sw350">
									<option value="0">'.$lang['cat_not'].'</option>';
				$catcache = array();
				$catid = $item['catid'];
				while ($items = $db->fetchassoc($inqcat))
				{
					$catcache[$items['parentid']][$items['catid']] = $items;
				}
				this_selectcat(0);
				echo				$selective.'
								</select>
							</td>
						</tr>';
			}
			if ($conf[PERMISS]['letter'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['all_letter'].'</td>
							<td>
								<select name="letid" class="sw350">
									<option value="0"> &#8213; </option>';
				foreach($letter as $k => $v) {
					echo '			<option value="'.$k.'"'.(($item['letid'] == $k) ? ' selected' : '').'> '.$v.' </option>';
				}
				echo '			</select>
							</td>
						</tr>';
			}
			if ($db->numrows($inqcat) > 0 AND $conf[PERMISS]['multcat'] == 'yes')
			{
				$cat_list = 0;
				$catout = $catshow = '';
				if ( ! empty($item['cats']))
				{
					$cat_list = $item['cats'];
					$cat_in = $db->query("SELECT catid, catname FROM ".$basepref."_".PERMISS."_cat WHERE catid IN (".$cat_list.")");
					while ($cat_item = $db->fetchassoc($cat_in))
					{
						$catshow.= ' <option value="'.$cat_item['catid'].'">'.$cat_item['catname'].'</option>';
						$catout.= '  <input type="hidden" name="subcat[]" value="'.$cat_item['catid'].'">';
					}
				}
				echo '	<script>
						function inCat(el) {
							$(el).prepend(\'<option value="0">&#8212;</option>\');
							$(el).find("option:not(:first)").remove().end().prop("disabled", true );
						}
						function getCat(catid, sess) {
							$.nocat = new Array("'.$cat_list.'," + catid);
							$.ajax( {
								cache:false,
								url: $.apanel + "mod/'.PERMISS.'/index.php",
								data:"dn=getcat&nocat=" +  $.nocat + "&ops=" +  sess,
								error:function(msg){},
								success:function(data) {
									if (data.length > 0 && data.match(/option/)) {
										$("#catin").html(data);
										$("#catin").prop("disabled", false );
									} else {
										inCat("#catin");
									}
								}
							});
						}
						$(function() {
							var sess = "'.$sess['hash'].'";
							var cl = $("#catid").val();
							(cl > 0) ? getCat(cl, sess) : inCat("#catin");
							$("#catid").on("change", function() {
								var cc = $(this).val();
								(cc > 0) ? getCat(cc, sess) : inCat("#catin");
							});
						});
						</script>';
				echo '	<tr>
							<th></th><th class="site">'.$lang['other_category'].'</th>
						</tr>
						<tr>
							<td>'.$lang['all_submint'].'&nbsp; &#8260; &nbsp;'.$lang['all_delet'].'</td>
							<td>
								<div id="tagarea">
								<table class="work">
									<tr>
										<td class="pw45">
											<select name="catin" id="catin" size="5" multiple class="blue pw100 app">
												<option value="0">&#8212;</option>
											</select>
										</td>
										<td class="ac pw10 vm">
											<input class="side-button" type="button" onclick="$.addcat();" value="&#9658;" /><br /><br />
											<input class="side-button" type="button" onclick="$.delcat();" value="&#9668;" />
										</td>
										<td>
											<select name="catout" id="catout" size="5" multiple class="green pw100 app">
												'.$catshow.'
											</select>
											<div id="area-cats">
												'.$catout.'
											</div>
										</td>
									</tr>
								</table>
								</div>
							</td>
						</tr>';
			}
			echo '		<tr><th></th><th class="site">'.$lang['all_location'].'</th></tr>
						<tr>
							<td class="first">'.$lang['country'].'</td>
							<td><input type="text" name="country" size="70" value="'.$item['country'].'" /></td>
						</tr>
						<tr>
							<td class="first">'.$lang['all_region'].'</td>
							<td><input type="text" name="region" size="70" value="'.$item['region'].'" /></td>
						</tr>
						<tr>
							<td class="first">'.$lang['all_city'].'</td>
							<td><input type="text" name="city" size="70" value="'.$item['city'].'" /></td>
						</tr>
						<tr>
							<td class="first">'.$lang['all_address'].'</td>
							<td><input type="text" name="address" size="70" value="'.$item['address'].'" placeholder="'.$lang['place_address'].'" /></td>
						</tr>
						<tbody class="tab-1">
						<tr><th></th><th class="site">'.$lang['contact_data'].'</th></tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['author'].'</td>
							<td><input type="text" name="author" size="70" value="'.$item['author'].'" /> &nbsp; '.( ! empty($author) ? $author : '').'</td>
						</tr>
						<tbody class="tab-1">
						<tr>
							<td class="first"><span>*</span> '.$lang['person'].'</td>
							<td><input type="text" name="person" size="70" value="'.preparse_un($item['person']).'" required="required" /></td>
						</tr>
						<tr>
							<td class="first"><span>*</span> '.$lang['phone'].'</td>
							<td><input type="text" name="phone" size="70" value="'.$item['phone'].'" placeholder="+7(xxx) xxx-xx-xx" required="required" />';
								$tm->outhint($lang['help_phone']);
			echo '			</td>
						</tr>
						<tr>
							<td class="first"><span>*</span> '.$lang['e_mail'].'</td>
							<td><input type="text" name="email" size="70" value="'.$item['email'].'" placeholder="orgs@site.ru" required="required" /></td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['all_site'].'</td>
							<td><input type="text" name="site" size="70" value="'.$item['site'].'" placeholder="http://" />
								&nbsp; '.(!empty($item['site']) ? '<a href="'.$item['site'].'" target="_blank"><img src="'.ADMURL.'/template/images/blank.png" alt="'.$lang['go'].'" /></a>' : '').'
							</td>
						</tr>
						<tr>
							<td>Skype '.$lang['all_login'].'</td>
							<td><input type="text" name="skype" value="'.$item['skype'].'" size="70" /></td>
						</tr>
						<tbody class="tab-1">
						<tr><th></th><th class="site">'.$lang['all_content'].'</th></tr>
						<tr>
							<td class="first"><span>*</span> '.$lang['all_decs'].'</td>
							<td class="usewys">';
			if ($wysiwyg == 'yes') {
				define("USEWYS", 1);
				$WYSFORM = 'textshort';
				$WYSVALUE = $item['textshort'];
				include(ADMDIR.'/includes/wysiwyg.php');
			} else {
				$tm->textarea('textshort', 5, 70, $item['textshort'], 1, '', '', 1);
			}
			echo '			</td>
						</tr>
						</tbody>
						<tbody class="tab-2">
						<tr>
							<td>'.$lang['full_text'].'</td>
							<td class="usewys">';
			if ($wysiwyg == 'yes') {
				$WYSFORM = 'textmore';
				$WYSVALUE = $item['textmore'];
				include(ADMDIR.'/includes/wysiwyg.php');
			} else {
				$tm->textarea('textmore', 7, 70, $item['textmore'], (($wysiwyg == 'yes') ? 0 : 1));
			}
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['img_extra_hint'].'</td>
							<td class="vm">
								<a class="side-button" href="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/image/attach/\',\'&amp;ims=1\');">'.$lang['filebrowser'].'</a>&nbsp;
								<a class="side-button" href="javascript:$.personalupload(\''.$sess['hash'].'&amp;objdir=/'.PERMISS.'/image/attach/\');">'.$lang['file_upload'].'</a>
								<div id="image-area"></div>
							</td>
						</tr>
						<tr><th></th><th class="site">'.$lang['all_image_big'].'</th></tr>
						<tr>
							<td>'.$lang['all_image_thumb'].'</td>
							<td>
								<input name="image_thumb" id="image_thumb" size="70" type="text" value="'.$item['image_thumb'].'" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/image/\',\'&amp;field[1]=image_thumb&amp;field[2]=image&amp;field[3]=video\')" value="'.$lang['filebrowser'].'" type="button" />
								<input class="side-button" onclick="javascript:$.quickupload(\''.$sess['hash'].'&amp;objdir=/'.PERMISS.'/image/\')" value="'.$lang['file_review'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_image'].'</td>
							<td>
								<input name="image" id="image" size="70" type="text" value="'.$item['image'].'" />
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/image/\',\'&amp;field[1]=image&amp;field[2]=image_thumb&amp;field[3]=video\')" value="'.$lang['filebrowser'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_alt_image'].'</td>
							<td><input name="image_alt" id="image_alt" size="70" type="text" value="'.preparse_un($item['title']).'" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_align_image'].'</td>
							<td>
								<select name="image_align" class="sw165">
									<option value="left">'.$lang['all_left'].'</option>
									<option value="right">'.$lang['all_right'].'</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>&nbsp;</th><th class="site">&nbsp;'.$lang['all_files'].'</th>
						</tr>
						<tr>
							<td class="vm">'.$lang['all_submint'].'&nbsp; &#8260; &nbsp;'.$lang['all_delet'].'</td>
							<td>
								<div id="file-area">';
			$fp = Json::decode($item['files']);
			$f = 1;
			if (is_array($fp) AND sizeof($fp) > 0)
			{
				foreach ($fp as $k => $v)
				{
					echo '			<div class="section tag" id="file-'.$f.'">
										<table class="work">
											<tr>
												<td class="vm sw150">'.$lang['all_path'].'</td>
												<td>
													<input name="files['.$f.'][path]" id="files'.$f.'" size="50" type="text" value="'.$v['path'].'" required="required" />
													<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/files/\',\'&amp;field[1]=files'.$f.'\')" value="'.$lang['filebrowser'].'" type="button" />
													<input class="side-button" onclick="javascript:$.fileallupload(\''.$sess['hash'].'&amp;objdir=/'.PERMISS.'/files/\',\'&amp;field=files'.$f.'\')" value="'.$lang['file_review'].'" type="button" />
													<a class="but fr" href="javascript:$.removetaginput(\'total-form\',\'file-area\',\'file-'.$f.'\');" title="'.$lang['all_delet'].'">&#215;</a>';
					echo '						</td>
											<tr>
												<td class="vm sw150">'.$lang['all_name'].'</td>
												<td><input name="files['.$f.'][title]" size="50" type="text" value="'.$v['title'].'" required="required" /></td>
											</tr>
										</table>
									</div>';
					$f ++;
				}
			}
			echo '				</div>
								<input class="side-button" onclick="javascript:$.addfiles(\'total-form\',\'file-area\',\'/'.PERMISS.'/files/\')" value="'.$lang['down_add'].'" type="button" />
							</td>
						</tr>
						<tr>
							<th>&nbsp;</th><th class="site">&nbsp;'.$lang['firms_social'].'</th>
						</tr>
						<tr>
							<td class="vm">'.$lang['all_submint'].'&nbsp; &#8260; &nbsp;'.$lang['all_delet'].'</td>
							<td>
								<div id="social-area">';
			$sp = Json::decode($item['social']);
			$s = 1;
			if (is_array($sp) AND sizeof($sp) > 0)
			{
				foreach ($sp as $v)
				{
					echo '			<div class="section tag" id="social-'.$s.'">
										<table class="work">
											<tr>
												<td class="vm sw150 al"><input name="social['.$s.'][title]" id="social'.$s.'" size="15" type="text" value="'.$v['title'].'" required="required"></td>
												<td class="vm"><input name="social['.$s.'][link]" size="50" type="text" value="'.$v['link'].'" required="required">
												<a class="but fr" href="javascript:$.removetaginput(\'total-form\', \'social-area\', \'social-'.$s.'\');">&#215;</a></td>
												</tr>
										</table>
									</div>';
					$s ++;
				}
			}
			echo '				</div>
								<input class="side-button" onclick="javascript:$.addsocial(\'total-form\', \'social-area\')" value="'.$lang['link_add'].'" type="button" />
							</td>
						</tr>
						<tr><th></th><th class="site">'.$lang['all_set'].'</th></tr>';
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'yes')
			{
				echo '	<tr>
							<td>'.$lang['page_access'].'</td>
							<td>
								<select class="group-sel sw165" name="acc" id="acc">
									<option value="all">'.$lang['all_all'].'</option>
									<option value="user">'.$lang['all_user_only'].'</option>';
				echo '				'.((isset($conf['user']['groupact']) AND $conf['user']['groupact'] == 'yes') ? '<option value="group">'.$lang['all_groups_only'].'</option>' : '');
				echo '			</select>
								<div id="group" class="group" style="display:none;">';
				if (isset($conf['user']['groupact']) AND $conf['user']['groupact'] == 'yes')
				{
					$inqs = $db->query("SELECT * FROM ".$basepref."_user_group");
					$group_out = '';
					while ($items = $db->fetchassoc($inqs)) {
						$group_out.= '<input type="checkbox" name="group['.$items['gid'].']" value="yes" /><span>'.$items['title'].'</span>,';
					}
					echo chop($group_out, ',');
				}
				echo '			</div>
							</td>
						</tr>
						<tr>
							<td>'.$lang['files_access'].'</td>
							<td>
								<select class="group-sel sw165" name="facc" id="facc">
									<option value="all">'.$lang['all_all'].'</option>
									<option value="user">'.$lang['all_user_only'].'</option>';
				echo '				'.(($conf['user']['groupact'] == 'yes') ? '<option value="group">'.$lang['all_groups_only'].'</option>' : '');
				echo '			</select>
								<div id="fgroup" class="group" style="display: none;">';
				if ($conf['user']['groupact'] == 'yes')
				{
					$inqs = $db->query("SELECT * FROM ".$basepref."_user_group");
					$group_out = '';
					while ($items = $db->fetchassoc($inqs)) {
						$group_out.= '<input type="checkbox" name="fgroups['.$items['gid'].']" value="yes" /><span>'.$items['title'].'</span>,';
					}
					echo chop($group_out, ',');
				}
				echo '			</div>
							</td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['all_vip'].'</td>
							<td>
								<select name="vip" class="sw165">
									<option value="0">'.$lang['all_no'].'</option>
									<option value="1">'.$lang['all_yes'].'</option>
								</select>
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_status'].'</td>
							<td>
								<select name="act" class="sw165">
									<option value="yes">'.$lang['included'].' </option>
									<option value="no">'.$lang['not_included'].'</option>
								</select>
							</td>
						</tr>
						<tr><th></th><th class="alternative">'.$lang['moderate'].'</th></tr>
						<tr>
							<td></td>
							<td>
								<select name="pid" id="pid" class="sw165">
									<option value="0">'.$lang['all_accept'].' </option>
									<option value="1">'.$lang['not_accept'].'</option>
									<option value="del">'.$lang['all_delet'].'</option>
								</select>
							</td>
						</tr>
						<input type="hidden" name="send" value="ok">';
				if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'yes')
				{
					echo '	<tr class="notice none">
								<td><div>'.$lang['err_moderate'].'</div></td>
								<td><div class="notice">';
									$tm->textarea('reason', 2, 70, '', TRUE, FALSE, 'ignorewysywig');
					echo '		</div></td>
							</tr>';
				}
				echo '	</tbody>
						<tr class="tfoot">
							<td colspan="2">
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input type="hidden" id="imgid" value="0" />
								<input type="hidden" id="fileid" value="'.$f.'" />
								<input type="hidden" id="socialid" value="'.$s.'" />
								<input type="hidden" name="id" value="'.$id.'" />
								<input type="hidden" name="new" value="yes" />
								<input type="hidden" name="userid" value="'.$item['userid'].'" />';
			if ($conf['cpu'] == 'no') {
				echo '			<input type="hidden" name="cpu" />';
			}
			if (isset($conf['user']['regtype']) AND $conf['user']['regtype'] == 'no')
			{
				echo '			<input type="hidden" name="reason" />
								<input type="hidden" name="acc" value="all" />';
			}
			echo '				<input type="hidden" name="dn" value="addsave" />
								<input class="main-button" value="'.$lang['all_go'].'" type="submit" />
							</td>
						</tr>
					</table>
					</form>
					</div>';

			echo "	<script>
					$(document).ready(function() {
						$('#tabs a').tabs('.tab-1');
						$( '#total-form' ).submit(function(e)
						{
							if( $('#area-cats').html().trim() !== '' && $('#catid').val() == 0) {
								$('#catid').css({'background-color': '#fcfafa', 'border-color': '#ae4752', 'box-shadow': '0 0 0 2px #f2ccd0'});
								var el = $('#catid').offset().top;
								$(document).scrollTop(el - 20);
								e.preventDefault();
							}
						});
						$('#catid').focus(function() {
							$(this).css({'background-color': '#fff', 'border-color': '#999', 'box-shadow': 'none'});
						});
					});
					</script>";

			$tm->footer();
		}

		/**
		 * Редактирование организаций (постмодерация)
		 ---------------------------------------------*/
		if ($_REQUEST['dn'] == 'post')
		{
			global $nu, $p, $cat;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					$lang['post_moderate']
				);

			$tm->header();

			if (isset($conf['userbase']))
			{
				if ($conf['userbase'] == 'danneo') {
					require_once(WORKDIR.'/core/userbase/danneo/danneo.user.php');
				} else {
					require_once(WORKDIR.'/core/userbase/'.$conf['userbase'].'/danneo.user.php');
				}

				$userapi = new userapi($db, false);
			}

			require_once(WORKDIR.'/core/classes/Router.php');
			$ro = new Router();

			echo '<script>cookie.set("num", "'.$nu.'", { path: "'.ADMPATH.'/" });</script>';
			$nu = isset($nu) ? $nu : (isset($_COOKIE['num']) ? $_COOKIE['num'] : null);

			$nu = (isset($nu) AND in_array($nu, $conf['num'])) ? $nu : $conf['num'][0];
			$p = (!isset($p) OR $p <= 1) ? 1 : $p;
			$sf = $nu * ($p - 1);

			$inq = $db->query("SELECT * FROM ".$basepref."_".PERMISS." WHERE pid = '1' ORDER BY id DESC LIMIT ".$sf.", ".$nu);

			$pages = $lang['pages'].':&nbsp; '.adm_pages(PERMISS, 'id', 'index', 'post', $nu, $p, $sess);
			$amount = $lang['all_col'].':&nbsp; '.amount_pages("index.php?dn=post&amp;p=".$p."&amp;ops=".$sess['hash']."", $nu);

			$work = array();
			while ($item = $db->fetchassoc($inq))
			{
				$work[$item['id']] = $item;
			}

			$catcache = $catcaches = array();
			$inquiry = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");
			while ($item = $db->fetchassoc($inquiry))
			{
				$catcache[$item['parentid']][$item['catid']] = $item;
				$catcaches[$item['catid']] = array($item['parentid'], $item['catid'], $item['catname']);
			}

			echo '	<div class="section">
						<form action="index.php" method="post">
						<table id="list" class="work">
							<caption>'.$lang[PERMISS].': '.$lang['post_moderate'].'</caption>';
			if ($db->numrows($inq) > 0)
			{
				echo '		<tr><td colspan="10">'.$amount.'</td></tr>
							<tr>
								<th>ID</th>
								<th>'.$lang['all_name'].'&nbsp; &#8260; &nbsp;'.$lang['all_cat_one'].'</th>
								<th>'.$lang['author'].'</th>
								<th>'.$lang['one_add'].'</th>
								<th>'.$lang['all_hits'].'</th>
								<th>'.$lang['review_all'].'</th>
								<th>'.$lang['photo_one'].'</th>
								<th>'.$lang['all_video'].'</th>
								<th>'.$lang['sys_manage'].'</th>
								<th class="ac pw5"><input name="checkboxall" id="checkboxall" value="yes" type="checkbox" /></th>
							</tr>';
				foreach ($work as $item)
				{
					$author = '—';
					if ( ! empty($item['author']))
					{
						$author = preg_replace('/[^\pL\pNd\pZs\pP\pM]/us', '', $item['author']);
						if (in_array('user', $realmod))
						{
							$udata = $userapi->userdata('uname', $author);
							if ( ! empty($udata))
							{
								require_once(WORKDIR.'/core/classes/Router.php');
								$ro = new Router();
								$author = '<a href="'.$conf['site_url'].$ro->seo($userapi->data['linkprofile'].$udata['userid']).'" title="'.$lang['profile'].' - '.$author.'" target="_blank">'.$author.'</a>';
							}
						}
					}

					echo '	<tr id="lists" class="list">
								<td class="ac sw50">'.$item['id'].'</td>
								<td class="al site pw30">
									<div>'.preparse_un($item['title']).'</div><span class="cats">'.preparse_un(linecat($item['catid'], $catcaches)).'</span>
								</td>
								<td class="pw10">'.$author.'</td>
								<td class="pw10">'.format_time($item['public'], 1, 1).'</td>
								<td class="pw10">'.$item['hits'].'</td>
								<td class="pw10 gov">
									<a href="index.php?dn=firmreviews&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/edit.png" alt="'.$lang['reviews_firm'].'" /></a>&nbsp; ('.$item['reviews'].')
								</td>
								<td class="gov pw10">
									<a href="index.php?dn=photo&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'""><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/photo.png" alt="'.$lang['photo_album'].'" /></a>&nbsp; ('.$item['photos'].')
								</td>
								<td class="gov pw10">
									<a href="index.php?dn=video&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'""><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/video.png" alt="'.$lang['video_album'].'" /></a>&nbsp; ('.$item['videos'].')
								</td>
								<td class="gov pw10">
									<a href="index.php?dn=edit&amp;id='.$item['id'].'&amp;pid=1&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/edit.png" alt="'.$lang['all_edit'].'" /></a>
									<a href="index.php?dn=del&amp;id='.$item['id'].'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/del.png" alt="'.$lang['all_delet'].'" /></a>
								</td>
								<td class="mark">
									<input type="checkbox" name="array['.$item['id'].']" value="yes" />
								</td>
							</tr>';
				}
				echo '		<tr><td colspan="10">'.$pages.'</td></tr>
							<tr class="tfoot">
								<td colspan="10">
									<input type="hidden" name="dn" value="postarract" />
									<input type="hidden" name="p" value="'.$p.'" />
									<input type="hidden" name="nu" value="'.$nu.'" />
									<input type="hidden" name="ops" value="'.$sess['hash'].'" />
									<input class="main-button" value="'.$lang['all_accept'].'" type="submit" />
								</td>
							</tr>';
			}
			else
			{
				echo '		<tr>
								<td class="ac">
									<div class="pads">'.$lang['all_new_firms_no'].'</div>
								</td>
							</tr>';
			}
			echo '		</table>
						</form>
					</div>';

			$tm->footer();
		}

		/**
		 * Массовая активация модерируемых организаций
		 -----------------------------------------------*/
		if ($_REQUEST['dn'] == 'postarract')
		{
			global $array, $p, $nu, $ok, $act;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=post&amp;ops='.$sess['hash'].'">'.$lang['post_moderate'].'</a>',
					$lang['array_control']
				);

			$p   = preparse($p, THIS_INT);
			$nu  = preparse($nu, THIS_INT);

			if (preparse($array, THIS_ARRAY) == 1)
			{
				if ($ok == 'yes')
				{
					if (preparse($array, THIS_ARRAY) == 1)
					{
						while (list($id) = each($array))
						{
							$id = preparse($id, THIS_INT);
							$db->query("UPDATE ".$basepref."_".PERMISS." SET pid = '0' WHERE id = '".$id."'");
						}
					}

					$redir = 'index.php?dn=post&amp;ops='.$sess['hash'];
					$redir.= (!empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
					$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';

					redirect($redir);
				}
				else
				{
					$count = count($array);

					$hidden = null;
					foreach ($array as $key => $id)
					{
						$hidden.= '<input type="hidden" name="array['.$key.']" value="yes" />';
					}
					$hidden.= '	<input type="hidden" name="p" value="'.$p.'" />
								<input type="hidden" name="nu" value="'.$nu.'" />';

					$tm->header();

					echo '	<div class="section">
								<form action="index.php" method="post">
								<table id="arr-work" class="work">
									<caption>'.$lang['post_moderate'].': '.$lang['all_accept'].' ('.$count.')</caption>
									<tr>
										<td class="cont"><strong>'.$lang['all_alert'].'!</strong><br />'.$lang['alert_active'].'<br />'.$lang['confirm_del'].'</td>
									</tr>
									<tr class="tfoot">
										<td>
											'.$hidden.'
											<input type="hidden" name="dn" value="postarract" />
											<input type="hidden" name="ok" value="yes" />
											<input type="hidden" name="ops" value="'.$sess['hash'].'" />
											<input class="main-button" value="'.$lang['all_go'].'" type="submit" />
											<input class="main-button" onclick="javascript:history.go(-1)" value="'.$lang['cancel'].'" type="button" />
										</td>
									</tr>
								</table>
								</form>
							</div>';

					$tm->footer();
				}
			}

			$redir = 'index.php?dn=post&amp;ops='.$sess['hash'];
			$redir.= (!empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= (!empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Все теги
		 -------------*/
		if ($_REQUEST['dn'] == 'tag')
		{
			global $nu, $p;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['all_publication'].'</a>',
					$lang['all_tags']
				);

			$tm->header();

			if(isset($nu) AND ! empty($nu)) {
				echo '<script>cookie.set("num", "'.$nu.'", { path: "'.ADMPATH.'/" });</script>';
			}
			$nu = isset($nu) ? $nu : (isset($_COOKIE['num']) ? $_COOKIE['num'] : null);

			$nu = ( ! is_null($nu) AND in_array($nu, $conf['num'])) ? $nu : $conf['num'][0];
			$p  = ( ! isset($p) OR $p <= 1) ? 1 : $p;
			$c  = $db->fetchassoc($db->query("SELECT COUNT(tagid) AS total FROM ".$basepref."_".PERMISS."_tag"));
			if ($nu > 10 AND $c['total'] <= (($nu * $p) - $nu)) {
				$p = 1;
			}
			$sf = $nu * ($p - 1);

			$inq = $db->query("SELECT * FROM ".$basepref."_".PERMISS."_tag ORDER BY tagid DESC LIMIT ".$sf.", ".$nu);

			$pages = $lang['all_pages'].':&nbsp; '.adm_pages(PERMISS.'_tag', 'tagid', 'index', 'tag', $nu, $p, $sess);
			$amount = $lang['amount_on_page'].':&nbsp; '.amount_pages('index.php?dn=tag&amp;p='.$p.'&amp;ops='.$sess['hash'], $nu);

			echo '	<script>
						var all_cpu   = "'.$lang['all_cpu'].'";
						var all_name  = "'.$lang['all_name'].'";
						var all_popul = "'.$lang['all_popul'].'";
					</script>';
			echo '	<div class="section">
					<form action="index.php" method="post">
					<table id="list" class="work">
						<caption>'.$modname[PERMISS].': '.$lang['all_tags'].'</caption>
						<tr><td colspan="5">'.$amount.'</td></tr>
						<tr>
							<th class="ar pw20">'.$lang['all_name'].'</th>
							<th>'.$lang['all_cpu'].'</th>
							<th>'.$lang['all_icon'].'</th>
							<th>'.$lang['all_popul'].'</th>
							<th>'.$lang['sys_manage'].'</th>
						</tr>';
			while ($item = $db->fetchassoc($inq))
			{
				echo '	<tr class="list">
							<td class="site">'.$item['tagword'].'</td>
							<td class="server">'.$item['tagcpu'].'</td>
							<td>';
				if( ! empty($item['icon'])) {
					echo '		<img src="'.WORKURL.'/'.$item['icon'].'" style="max-width: 36px; max-height: 27px;" alt="'.preparse_un($item['tagword']).'" />';
				}
				echo '		</td>
							<td><input type="text" name="ratingid['.$item['tagid'].']" value="'.$item['tagrating'].'" size="3" maxlength="3" /></td>
							<td class="gov">
								<a href="index.php?dn=tagedit&amp;p='.$p.'&amp;nu='.$nu.'&amp;tagid='.$item['tagid'].'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/edit.png" alt="'.$lang['all_edit'].'" /></a>
								<a href="index.php?dn=tagdel&amp;p='.$p.'&amp;nu='.$nu.'&amp;tagid='.$item['tagid'].'&amp;ops='.$sess['hash'].'"><img src="'.ADMPATH.'/template/skin/'.$sess['skin'].'/images/del.png" alt="'.$lang['all_delet'].'" /></a>
							</td>
						</tr>';
			}
			echo '		<tr class="tfoot">
							<td colspan="5">
								<input type="hidden" name="dn" value="tagsetsave" />
								<input type="hidden" name="p" value="'.$p.'" />
								<input type="hidden" name="nu" value="'.$nu.'" />
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input class="main-button" value="'.$lang['all_save'].'" type="submit" />
							</td>
						</tr>
						<tr><td colspan="5">'.$pages.'</td></tr>
					</table>
					</form>
					</div>
					<div class="pad"></div>
					<div class="section">
					<form action="index.php" method="post" id="total-form">
					<table class="work">
						<caption>'.$lang['all_tags'].': '.$lang['all_submint'].'</caption>
						<tr>
							<td class="first"><span>*</span> '.$lang['all_name'].'</td>
							<td>
								<input type="text" name="tagword" id="tagword" size="70" required="required" />
							</td>
						</tr>';
			if ($conf['cpu'] == 'yes') {
			echo '		<tr>
							<td>'.$lang['all_cpu'].'</td>
							<td>
								<input type="text" name="tagcpu" id="cpu" size="70" />';
								$tm->outtranslit('tagword', 'cpu', $lang['cpu_int_hint']);
			echo '        </td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['custom_title'].'</td>
							<td><input type="text" name="custom" size="70" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_descript'].'</td>
							<td><input type="text" name="descript" size="70" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_keywords'].'</td>
							<td>
								<input type="text" name="keywords" size="70" />';
								$tm->outhint($lang['keyword_hint']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_icon'].'</td>
							<td>
								<input name="icon" id="icon" size="47" type="text" />&nbsp;
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/icon/\',\'&amp;field[1]=icon\')" value="'.$lang['filebrowser'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_decs'].'</td>
							<td>';
								$tm->textarea('tagdesc', 5, 50, '', 1);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_popul'].'</td>
							<td><input type="text" name="tagrating" size="25" /></td>
						</tr>
						<tr class="tfoot">
							<td colspan="2">
								<input type="hidden" name="dn" value="tagaddsave" />
								<input type="hidden" name="p" value="'.$p.'" />
								<input type="hidden" name="nu" value="'.$nu.'" />
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input class="main-button" value="'.$lang['all_submint'].'" type="submit" />
                          </td>
						</tr>
					</table>
					</form>
					</div>';

			$tm->footer();
		}

		/**
		 * Все теги, сохранение
		 -------------------------*/
		if ($_REQUEST['dn'] == 'tagsetsave')
		{
			global $ratingid, $p, $nu;

			if (preparse($ratingid, THIS_ARRAY) == 1)
			{
				this_tagup($ratingid, PERMISS);
			}

			$redir = 'index.php?dn=tag&amp;ops='.$sess['hash'];
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p,THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu,THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Добавление метки (сохранение)
		 --------------------------------*/
		if ($_REQUEST['dn'] == 'tagaddsave')
		{
			global $tagcpu, $tagword, $custom, $keywords, $descript, $icon, $tagdesc, $tagrating, $p, $nu;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['all_publication'].'</a>',
					'<a href="index.php?dn=tag&amp;ops='.$sess['hash'].'">'.$lang['all_tags'].'</a>',
					$lang['all_add']
				);

			$tagword = preparse($tagword, THIS_TRIM, 0, 255);
			$icon = preparse($icon, THIS_TRIM);
			$tagcpu = preparse($tagcpu, THIS_TRIM, 0, 255);
			$tagdesc = preparse($tagdesc, THIS_TRIM);
			$custom = preparse($custom, THIS_TRIM);
			$descript = preparse($descript, THIS_TRIM);
			$keywords = preparse($keywords, THIS_TRIM);

			if (preparse($tagword, THIS_EMPTY) == 1)
			{
				$tm->header();
				$tm->error($lang['all_tags'], $lang['all_add'], $lang['forgot_name']);
				$tm->footer();
			}
			else
			{
				if (preparse($tagcpu, THIS_EMPTY) == 1)
				{
					$tagcpu = cpu_translit($tagword);
				}

				$inqure = $db->query
							(
								"SELECT tagword, tagcpu FROM ".$basepref."_".PERMISS."_tag
								 WHERE tagword = '".$db->escape($tagword)."' OR tagcpu = '".$db->escape($tagcpu)."'"
							);

				if ($db->numrows($inqure) > 0)
				{
					$tm->header();
					$tm->error($lang['all_add'], $tagword, $lang['cpu_error_isset']);
					$tm->footer();
				}
			}

			$tagrating = ( ! empty($tagrating)) ? preparse($tagrating, THIS_INT) : 0;
			$db->query
				(
					"INSERT INTO ".$basepref."_".PERMISS."_tag VALUES (
					 NULL,
					 '".$db->escape($tagcpu)."',
					 '".$db->escape(preparse_sp($tagword))."',
					 '".$db->escape(preparse_sp($tagdesc))."',
					 '".$db->escape(preparse_sp($custom))."',
					 '".$db->escape(preparse_sp($descript))."',
					 '".$db->escape(preparse_sp($keywords))."',
					 '".$db->escape($icon)."',
					 '".$tagrating."'
					 )"
				);

			$redir = 'index.php?dn=tag&amp;ops='.$sess['hash'];
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Редактировать метку
		 ----------------------*/
		if ($_REQUEST['dn'] == 'tagedit')
		{
			global $tagid, $p, $nu;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['all_publication'].'</a>',
					'<a href="index.php?dn=tag&amp;ops='.$sess['hash'].'">'.$lang['all_tags'].'</a>',
					$lang['all_edit']
				);

			$tm->header();

			$tagid = preparse($tagid, THIS_INT);
			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_tag WHERE tagid = '".$tagid."'"));

			echo '	<div class="section">
					<form action="index.php" method="post">
					<table class="work">
						<caption>'.$lang['edit_tag'].': '.preparse_un($item['tagword']).'</caption>
						<tr>
							<td class="first"><span>*</span> '.$lang['all_name'].'</td>
							<td>
								<input type="text" name="tagword" id="tagword" size="70" value="'.preparse_un($item['tagword']).'" required="required" />
							</td>
						</tr>';
			if ($conf['cpu'] == 'yes') {
			echo '		<tr>
							<td>'.$lang['all_cpu'].'</td>
							<td>
								<input type="text" name="tagcpu" id="cpu" size="70" value="'.$item['tagcpu'].'" />';
								$tm->outtranslit('tagword', 'cpu', $lang['cpu_int_hint']);
			echo '        </td>
						</tr>';
			}
			echo '		<tr>
							<td>'.$lang['custom_title'].'</td>
							<td><input type="text" name="custom" size="70" value="'.preparse_un($item['custom']).'" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_descript'].'</td>
							<td><input type="text" name="descript" size="70" value="'.preparse_un($item['descript']).'" /></td>
						</tr>
						<tr>
							<td>'.$lang['all_keywords'].'</td>
							<td>
								<input type="text" name="keywords" size="70" value="'.preparse_un($item['keywords']).'" />';
								$tm->outhint($lang['keyword_hint']);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_icon'].'</td>
							<td>
								<input name="icon" id="icon" size="47" type="text" value="'.$item['icon'].'" />&nbsp;
								<input class="side-button" onclick="javascript:$.filebrowser(\''.$sess['hash'].'\',\'/'.PERMISS.'/icon/\',\'&amp;field[1]=icon\')" value="'.$lang['filebrowser'].'" type="button" />
							</td>
						</tr>
						<tr>
							<td>'.$lang['all_decs'].'</td>
							<td>';
								$tm->textarea('tagdesc', 5, 50, $item['tagdesc'], 1);
			echo '			</td>
						</tr>
						<tr>
							<td>'.$lang['all_popul'].'</td>
							<td><input type="text" name="tagrating" size="25" value="'.$item['tagrating'].'" /></td>
						</tr>
						<tr class="tfoot">
							<td colspan="2">
								<input type="hidden" name="dn" value="tageditsave" />
								<input type="hidden" name="p" value="'.$p.'" />
								<input type="hidden" name="nu" value="'.$nu.'" />
								<input type="hidden" name="tagid" value="'.$tagid.'" />
								<input type="hidden" name="ops" value="'.$sess['hash'].'" />
								<input class="main-button" value="'.$lang['all_save'].'" type="submit" />
							</td>
						</tr>
					</table>
					</form>
					</div>';

			$tm->footer();
		}

		/**
		 * Редактировать метку (сохранение)
		 -----------------------------------*/
		if ($_REQUEST['dn'] == 'tageditsave')
		{
			global $tagid, $tagword, $tagcpu, $custom, $keywords, $descript, $icon, $tagdesc, $tagrating, $p, $nu;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['all_publication'].'</a>',
					'<a href="index.php?dn=tag&amp;ops='.$sess['hash'].'">'.$lang['all_tags'].'</a>',
					$lang['all_edit']
				);

			$tagword = preparse($tagword, THIS_TRIM, 0, 255);
			$tagcpu = preparse($tagcpu, THIS_TRIM, 0, 255);
			$icon = preparse($icon, THIS_TRIM);
			$tagid = preparse($tagid, THIS_INT);

			if (preparse($tagword, THIS_EMPTY) == 1)
			{
				$tm->header();
				$tm->error($lang['edit_tag'], null, $lang['forgot_name']);
				$tm->footer();
			}
			else
			{
				if (preparse($tagcpu, THIS_EMPTY) == 1)
				{
					$tagcpu = cpu_translit($tagword);
				}

				$inqure = $db->query
							(
								"SELECT tagid, tagcpu, tagword FROM ".$basepref."_".PERMISS."_tag
								 WHERE (tagcpu = '".$db->escape($tagcpu)."' OR tagword = '".$db->escape($tagword)."')
								 AND tagid <> '".$tagid."'"
							);

				if ($db->numrows($inqure) > 0)
				{
					$tm->header();
					$tm->error($lang['edit_tag'], $tagword, $lang['cpu_error_isset']);
					$tm->footer();
				}
			}

			$tagrating = ( ! empty($tagrating)) ? preparse($tagrating, THIS_INT) : 0;
			$db->query
				(
					"UPDATE ".$basepref."_".PERMISS."_tag SET
					 tagcpu    = '".$db->escape($tagcpu)."',
					 tagword   = '".$db->escape(preparse_sp($tagword))."',
					 tagdesc   = '".$db->escape(preparse_sp($tagdesc))."',
					 custom    = '".$db->escape(preparse_sp($custom))."',
					 keywords  = '".$db->escape(preparse_sp($keywords))."',
					 descript  = '".$db->escape(preparse_sp($descript))."',
					 icon      = '".$db->escape($icon)."',
					 tagrating = '".$db->escape($tagrating)."'
					 WHERE tagid = '".$tagid."'"
				);

			$redir = 'index.php?dn=tag&amp;ops='.$sess['hash'];
			$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
			$redir.= ( ! empty($nu)) ? "&amp;nu=".preparse($nu, THIS_INT) : '';

			redirect($redir);
		}

		/**
		 * Удаление тегов
		 ------------------*/
		if ($_REQUEST['dn'] == 'tagdel')
		{
			global $p, $nu, $ok, $tagid;

			$template['breadcrumb'] = array
				(
					'<a href="'.ADMPATH.'/index.php?dn=index&amp;ops='.$sess['hash'].'">'.$lang['desktop'].'</a>',
					'<a href="'.ADMPATH.'/index.php?dn=content&amp;ops='.$sess['hash'].'">'.$lang['all_content'].'</a>',
					'<a href="index.php?dn=index&amp;ops='.$sess['hash'].'">'.$modname[PERMISS].'</a>',
					'<a href="index.php?dn=list&amp;ops='.$sess['hash'].'">'.$lang['all_publication'].'</a>',
					'<a href="index.php?dn=tag&amp;ops='.$sess['hash'].'">'.$lang['all_tags'].'</a>',
					$lang['all_delet']
				);

			$tagid = preparse($tagid, THIS_INT);

			if ($ok == 'yes')
			{
				$db->query("DELETE FROM ".$basepref."_".PERMISS."_tag WHERE tagid = '".$tagid."'");
				$db->increment(PERMISS.'_tag');

				$redir = 'index.php?dn=tag&amp;ops='.$sess['hash'];
				$redir.= ( ! empty($p)) ? '&amp;p='.preparse($p, THIS_INT) : '';
				$redir.= ( ! empty($nu)) ? '&amp;nu='.preparse($nu, THIS_INT) : '';

				redirect($redir);
			}
			else
			{
				$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS."_tag WHERE tagid = '".$tagid."'"));
				$yes = 'index.php?dn=tagdel&amp;p='.$p.'&amp;nu='.$nu.'&amp;tagid='.$tagid.'&amp;ok=yes&amp;ops='.$sess['hash'];
				$not = 'index.php?dn=tag&amp;p='.$p.'&amp;nu='.$nu.'&amp;ops='.$sess['hash'];

				$tm->header();
				$tm->shortdel($lang['all_delet'], $item['tagword'], $yes, $not);
				$tm->footer();
			}
		}

		/**
		 * Быстрое редактирование названия организации
		 -----------------------------------------------*/
		if ($_REQUEST['dn'] == 'ajaxedittitle')
		{
			global $id;

			$id = preparse($id, THIS_INT);

			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'"));
			echo '	<form action="index.php" method="post" id="post" name="post" onsubmit="return $.posteditor(this,\'te'.$item['id'].'\',\'index.php?dn=ajaxsavetitle&id='.$item['id'].'&ops='.$sess['hash'].'\')">
					<div class="wp100">
						<input type="text" name="title" size="60" value="'.preparse_un($item['title']).'" />&nbsp;
						<input type="hidden" name="ops" value="'.$sess['hash'].'" />
						<input type="hidden" name="dn" value="ajaxsavetitle" />
						<input type="hidden" name="id" value="'.$id.'" />
						<input class="side-button" value=" » " type="submit" />
					</div>
					</form>';
		}

		/**
		 * Быстрое редактирование названия организации (сохранение)
		 -----------------------------------------------------------*/
		if ($_REQUEST['dn'] == 'ajaxsavetitle')
		{
			global $id, $title;

			$id = preparse($id, THIS_INT);
			$title = preparse(utfread($title, $conf['langcharset']), THIS_TRIM, 0, 255);

			if ($id > 0 AND $title) {
				$db->query("UPDATE ".$basepref."_".PERMISS." SET title = '".$db->escape(preparse_sp($title))."' WHERE id = '".$id."'");
			}
			echo '<a class="notooltip" title="'.$lang['all_change'].'" href="javascript:$.ajaxeditor(\'index.php?dn=ajaxedittitle&amp;id='.$id.'&amp;ops='.$sess['hash'].'\',\'te'.$id.'\',\'\')">'.preparse_un($title).'</a>';

			$cache->cachesave(1);
			exit();
		}

		/**
		 * Быстрое изменение категории организации
		 -------------------------------------------*/
		if ($_REQUEST['dn'] == 'ajaxeditcat')
		{
			global $selective, $id, $conf;

			if ($conf[PERMISS]['multcat'] == 'yes')
			{
				exit();
			}

			$id = preparse($id, THIS_INT);
			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'"));
			echo '	<form action="index.php" method="post" id="post" name="post" onsubmit="return $.posteditor(this,\'ce'.$item['id'].'\',\'index.php?dn=ajaxsavecat&id='.$item['id'].'&ops='.$sess['hash'].'\')">
					<div>
						<select name="catid" style="float: left; width: 240px;">
							<option value="0">'.$lang['cat_not'].'</option>';
			$inquiry = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");
			$catcache = array();
			$catid = $item['catid'];
			while ($item = $db->fetchassoc($inquiry)) {
				$catcache[$item['parentid']][$item['catid']] = $item;
			}
			this_selectcat(0);
			echo '			'.$selective.'
						</select>&nbsp;
						<input type="hidden" name="ops" value="'.$sess['hash'].'" />
						<input type="hidden" name="dn" value="ajaxsavecat" />
						<input type="hidden" name="id" value="'.$id.'" />
						<input class="side-button" value=" » " type="submit" />
					</div>
					</form>';
		}

		/**
		 * Быстрое изменение категории организации (сохранение)
		 --------------------------------------------------------*/
		if ($_REQUEST['dn'] == 'ajaxsavecat')
		{
			global $id, $catid, $conf;

			if ($conf[PERMISS]['multcat'] == 'yes')
			{
				exit();
			}

			$id = preparse($id, THIS_INT);
			$catid = preparse($catid, THIS_INT);
			if ($id > 0) {
				$db->query("UPDATE ".$basepref."_".PERMISS." SET catid = '".$catid."' WHERE id = '".$id."'");
			}
			$catcaches = array();
			$inquiry = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat ORDER BY posit ASC");
			while ($item = $db->fetchassoc($inquiry))
			{
				$catcaches[$item['catid']] = array($item['parentid'], $item['catid'], $item['catname']);
			}
			echo '<a class="notooltip" title="'.$lang['all_change'].'" href="javascript:$.ajaxeditor(\'index.php?dn=ajaxeditcat&amp;id='.$id.'&amp;ops='.$sess['hash'].'\',\'ce'.$id.'\',\'\')">'.preparse_un(linecat($catid, $catcaches)).'</a>';
			$counts = new Counts(PERMISS, 'id');
			$cache->cachesave(1);
			exit();
		}

		/**
		 * Быстрое изменение даты организации
		 --------------------------------------*/
		if ($_REQUEST['dn'] == 'ajaxeditdate')
		{
			global $id;

			$id = preparse($id, THIS_INT);

			$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".PERMISS." WHERE id = '".$id."'"));
			$time = CalendarFormat($item['public']);

			echo '	<form action="index.php" method="post" id="post" name="post" onsubmit="return $.posteditor(this,\'de'.$item['id'].'\',\'index.php?dn=ajaxsavedate&id='.$item['id'].'&ops='.$sess['hash'].'\')">
					<div class="wp100">
						<input type="text" name="public" id="public" size="16" value="'.$time.'" />';
						Calendar('cal', 'public');
			echo '		<input type="hidden" name="ops" value="'.$sess['hash'].'" />
						<input type="hidden" name="dn" value="ajaxsavedate" />
						<input type="hidden" name="id" value="'.$id.'" />
						<input class="side-button" value=" » " type="submit" />
					</div>
					</form>';
		}

		/**
		 * Быстрое изменение даты организации (сохранение)
		 --------------------------------------------------*/
		if ($_REQUEST['dn'] == 'ajaxsavedate')
		{
			global $id, $public;

			$id = preparse($id, THIS_INT);
			$time = (empty($public)) ? NEWTIME : ReDate($public);

			if ($id > 0)
			{
				$db->query("UPDATE ".$basepref."_".PERMISS." SET public = '".$time."' WHERE id = '".$id."'");
			}
			echo '<a class="notooltip" title="'.$lang['all_change'].'" href="javascript:$.ajaxeditor(\'index.php?dn=ajaxeditdate&amp;id='.$id.'&amp;ops='.$sess['hash'].'\',\'de'.$id.'\',\'\')">'.format_time($time, 0, 1).'</a>';
			$cache->cachesave(1);
			exit();
		}

		/**
		 * Получение списка подкатегорий
		 --------------------------------*/
		if ($_REQUEST['dn'] == 'getcat')
		{
			global $catid, $nocat, $sess;

			header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
			header('Content-Type: text/html; charset='.$conf['langcharset'].'');

			$sql = ( ! empty($nocat)) ? " WHERE catid NOT IN (".$nocat.")" : "";

			$array = array();
			$inq = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".PERMISS."_cat".$sql." ORDER BY posit ASC");
			while ($item = $db->fetchassoc($inq))
			{
				$array[$item['parentid']][$item['catid']] = $item;
			}

			function select($cid = 0, $depth = 0)
			{
				global $array, $catid;
				if ( ! isset($array[$cid]) )
					return false;

				foreach ($array[$cid] as $val)
				{
					$indent = ($depth > 0) ? str_repeat('&nbsp;&nbsp;', $depth) : '';
					echo '<option value="'.$val['catid'].'">'.$indent.preparse_un($val['catname']).'</option>';
					select($val['catid'], $depth + 1);
				}
				unset($array[$cid]);
			}

			select();
			exit();
		}

		/**
		 * Функция создания эскиза изображения
		 --------------------------------------*/
		if ($_REQUEST['dn'] == 'thumb')
		{
			global $id, $type, $x, $h, $r;

			$id = preparse($id, THIS_INT);
			if ($type == 'video') {
				$item = $db->fetchassoc($db->query("SELECT image FROM ".$basepref."_".PERMISS."_".$type." WHERE id = '".$id."'"));
				$image = $item['image'];
			} else {
				$item = $db->fetchassoc($db->query("SELECT image_thumb FROM ".$basepref."_".PERMISS."_".$type." WHERE id = '".$id."'"));
				$image = $item['image_thumb'];
			}
			thumb($image, $x, $h, $r);
			exit();
		}

		/**
		 * Предпросмотр видео
		 ----------------------*/
		if ($_REQUEST['dn'] == 'view')
		{
			global $id;

			$id = preparse($id, THIS_INT);
			$item = $db->fetchassoc($db->query("SELECT video FROM ".$basepref."_".PERMISS."_video WHERE id = '".$id."'"));
			echo '	<div style="width: 640px; height: 360px;">
						<iframe src="'.$item['video'].'" width="640" height="360" frameborder="0" allowfullscreen></iframe>
					</div>';
		}

	/**
	 * Права доступа
	 */
	} else {
		$tm->header();
		$tm->error($lang['no_access']);
		$tm->footer();
	}
/**
 * Авторизация, редирект
 */
} else {
	redirect(ADMURL.'/login.php');
	exit();
}
