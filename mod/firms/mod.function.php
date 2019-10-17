<?php
/**
 * File:        /mod/firms/mod.function.php
 *
 * @package     Danneo Basis kernel
 * @version     Danneo CMS (Next) v1.5.4
 * @copyright   (c) 2005-2017 Danneo Team
 * @link        http://danneo.ru
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('DNREAD') OR die('No direct access');

/**
 * Типы файлов
 */
function file_type($path)
{
    $type = strtolower(substr(strrchr($path,'.'),1));
    $ext = array
           (
               'rar'  => 'rar',
               'zip'  => 'zip',
               '7z'   => 'zip',
               'bz2'  => 'zip',
               'cab'  => 'zip',
               'ace'  => 'zip',
               'arj'  => 'zip',
               'jar'  => 'zip',
               'gzip' => 'zip',
               'tar'  => 'zip',
               'tgz'  => 'zip',
               'gz'   => 'zip',
               'gif'  => 'img',
               'jpeg' => 'img',
               'jpg'  => 'img',
               'png'  => 'img',
               'bmp'  => 'img',
               'txt'  => 'txt',
               'sql'  => 'not',
               'exe'  => 'not',
               'swf'  => 'not',
               'fla'  => 'not',
               'wav'  => 'not',
               'mp2'  => 'not',
               'mp3'  => 'not',
               'mp4'  => 'not',
               'mid'  => 'not',
               'midi' => 'not',
               'mmf'  => 'not',
               'mpeg' => 'img',
               'mpe'  => 'not',
               'mpg'  => 'not',
               'mpa'  => 'not',
               'avi'  => 'not',
               'mpga' => 'not',
               'pdf'  => 'pdf',
               'pds'  => 'img',
               'xls'  => 'xls',
               'xlsx' => 'xls',
               'xl'   => 'xls',
               'xla'  => 'xls',
               'xlb'  => 'xls',
               'xlc'  => 'xls',
               'xld'  => 'xls',
               'xlk'  => 'xls',
               'xll'  => 'xls',
               'xlm'  => 'xls',
               'xlt'  => 'xls',
               'xlv'  => 'xls',
               'xlw'  => 'xls',
               'doc'  => 'doc', 
               'docx' => 'doc',
               'dot'  => 'doc',
               'wiz'  => 'doc',
               'wzs'  => 'doc', 
               'rtf'  => 'rtf',
               'pot'  => 'ppt',
               'ppa'  => 'ppt',
               'pps'  => 'ppt',
               'ppt'  => 'ppt',
               'pptx' => 'ppt',
               'pptm' => 'ppt',
               'pwz'  => 'ppt'
           );
    return (isset($ext[$type])) ? $ext[$type] : 'not';
}

/**
 * Функция рейтинга
 ---------------------*/
function rating($rate, $id, $current)
{
	global $config, $lang, $tm;

	if ($config['ajax'] == 'yes' AND $current == 0)
	{
		$width = intval((100 / 5) * $rate);
		return $tm->parse(array
					(
						'id'     => $id,
						'width'  => $width,
						'rate_1' => $lang['rate_1'],
						'rate_2' => $lang['rate_2'],
						'rate_3' => $lang['rate_3'],
						'rate_4' => $lang['rate_4'],
						'rate_5' => $lang['rate_5']
					),
					$tm->create('mod/'.WORKMOD.'/ajax.rating'));
	} else {
		return $r = '<img src="'.SITE_URL.'/template/'.SITE_TEMP.'/images/rating/'.$rate.'.gif" alt="'.(($rate == 0) ? $lang['rate_0'] : $lang['rate_'.$rate.'']).'" />';
	}
}

/**
 * Функции проверки дубликатов
 */
function check_name($field, $name)
{
	global $db, $basepref, $lang, $tm;

	$inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE ".$field." = '".$db->escape($name)."'");
	if ($db->numrows($inq) > 0)
	{
		$tm->error($lang['cpu_error_isset'], 0, 0);
	}

}
function check_name_edit($field, $name, $id)
{
	global $db, $basepref, $lang, $tm;

	$inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE ".$field." = '".$db->escape($name)."' AND id <> '".$id."'");
	if ($db->numrows($inq) > 0)
	{
		$tm->error($lang['cpu_error_isset'], 0, 0);
	}

}
function check_title($title)
{
	global $db, $basepref, $lang, $tm;

	$inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_user WHERE title = '".$db->escape($title)."'");
	if ($db->numrows($inq) > 0)
	{
		$tm->error($lang['cpu_error_isset'], 0, 0);
	}

}

/**
 * Категории
 -------------*/
function this_selectcat($cid = 0, $depth = 0)
{
	global $catcache, $selective, $catid, $selected;

	if (!isset($catcache[$cid]))
	{
		return false;
	}

	$catcount = 0;
	foreach ($catcache[$cid] as $key => $incat)
	{
		$catcount ++;
		$selected = ($incat['catid'] == $catid) ? ' selected="selected"' : '';

		if ($depth == 0)
		{
			$indent = '';
			$style = ' class="oneselect"';
		}
		else
		{
			$indent = str_repeat("&nbsp;&nbsp;", $depth);
			$style = '';
		}

		$selective.= '<option value="'.$incat['catid'].'"'.$style.$selected.'>'.$indent.$incat['catname'].'</option>';

		this_selectcat($incat['catid'], $depth + 1);
	}
	unset($catcache[$cid]);
	return;
}
