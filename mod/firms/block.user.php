<?php
/**
 * File:        /mod/firms/block.user.php
 *
 * @package     Danneo Basis kernel
 * @version     Danneo CMS (Next) v1.5.4
 * @copyright   (c) 2005-2017 Danneo Team
 * @link        http://danneo.ru
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('DNREAD') OR die('No direct access');

/**
 * Глобальные
 */
global	$config, $lang, $usermain, $tm, $ro;

/**
 * Рабочий мод
 */
$WORKMOD = basename(__DIR__);

/**
 * Добавить публикацию
 */
$groups = TRUE; $profile = FALSE;
if($config[$WORKMOD]['adduse'] == 'user')
{
	$profile = TRUE;
	if (defined('GROUP_ACT') AND ! empty($config[$WORKMOD]['groups']))
	{
		$groups = FALSE;
		$group = Json::decode($config[$WORKMOD]['groups']);
		if (isset($group[$usermain['gid']]))
		{
			$groups = TRUE;
		}
	}
}
if ($config[$WORKMOD]['addit'] == 'yes' AND $groups)
{
	if ($profile)
	{
		$added[] = array(
			'url'  => $ro->seo('index.php?dn='.$WORKMOD.'&amp;re=my'),
			'title' => $lang['firms_my'],
			'css' => 'my'
		);
	}
	$added[] = array(
		'url'  => $ro->seo('index.php?dn='.$WORKMOD.'&amp;re=add'),
		'title' => $lang['firms_add'],
		'css' => 'add'
	);
}
