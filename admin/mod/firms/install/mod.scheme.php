<?php
/**
 * File:        /admin/mod/news/install/mod.scheme.php
 *
 * @package     Danneo Basis kernel
 * @version     Danneo CMS (Next) v1.5.4
 * @copyright   (c) 2005-2017 Danneo Team
 * @link        http://danneo.ru
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('ADMREAD') OR die('No direct access');

/**
 * Рабочий мод
 */
$WORKMOD = basename(dirname(__DIR__));

/**
 * Массив меток мода
 *
 * @param key - file
 * @param val - labels array
 * @return setting for blocks by default, when mod install
 */
$label = array
(
	'index'   => array('index' => 1, 'cat' => 1, 'page' => 1),
	'tags'    => array('index' => 1, 'tag' => 1),
	'add'     => array('index' => 1, 'save' => 1),
	'search'  => array('index' => 1),
	'letter'  => array('index' => 1),
	'respond' => array('index' => 1),
	'rating'  => array('index' => 1),
	'my'      => array('index' => 1, 'edit' => 1, 'save' => 1)
);

/**
 * Массив имен таблиц мода
 *
 * @param val | tables of module
 * @return array
 */
$tables = array
(
	$WORKMOD,
	$WORKMOD.'_cat',
	$WORKMOD.'_cats',
	$WORKMOD.'_search',
	$WORKMOD.'_tag',
	$WORKMOD.'_user'
);

/**
 * Массив каталогов с правами на запись
 *
 * @param val | folders with write access
 * @return array
 */
$chmod = array
(
	'/cache/sql/'.$WORKMOD,
	'/up/'.$WORKMOD,
	'/up/'.$WORKMOD.'/files',
	'/up/'.$WORKMOD.'/icon',
	'/up/'.$WORKMOD.'/image',
	'/up/'.$WORKMOD.'/image/attach',
	'/up/'.$WORKMOD.'/video',
);

/**
 * Список каталогов и файлов мода
 *
 * @param val | tables of module
 * @return array
 */
$filelist = array
(
	'/'.APANEL.'/mod/'.$WORKMOD,
	'/cache/sql/'.$WORKMOD,
	'/mod/'.$WORKMOD,
	'/up/'.$WORKMOD,
	'/template/'.SITE_TEMP.'/mod/'.$WORKMOD
);
