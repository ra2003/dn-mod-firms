<?php
/**
 * File:        /admin/mod/firms/mod.menu.php
 *
 * @package     Danneo Basis kernel
 * @version     Danneo CMS (Next) v1.5.4
 * @copyright   (c) 2005-2017 Danneo Team
 * @link        http://danneo.ru
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('ADMREAD') OR die('No direct access');

global $db, $basepref, $conf, $sess, $realmod, $modposit, $modname, $lang, $ADMIN_PERM_ARRAY, $ADMIN_ID, $CHECK_ADMIN, $AJAX;

$block = array();
$WORKMOD = basename(__DIR__);
if (in_array($WORKMOD, $realmod))
{
	if (in_array($WORKMOD, $ADMIN_PERM_ARRAY) OR in_array($ADMIN_ID, $CHECK_ADMIN['admid']))
	{
		$block['id'] = $WORKMOD;
		$block['posit'] = $modposit[$WORKMOD];
		$block['title'] = $modname[$WORKMOD];
		$block['link'] = array(
			ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=index&amp;ops='.$sess['hash'] => $lang['all_set'],
			ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=list&amp;ops='.$sess['hash'] => $lang['firms_all'],
			ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=add&amp;ops='.$sess['hash'] => $lang['firms_add']
		);

		$tab_user = $db->tables($WORKMOD."_user");
		if ($tab_user AND isset($conf[$WORKMOD]['addit']) AND $conf[$WORKMOD]['addit'] == 'yes')
		{
			$c = $db->fetchrow($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_".$WORKMOD."_user"));
			$cnew = ($c['total'] > 0) ? ' ('.$c['total'].')' : '';
			$block['link'] = array_merge($block['link'], array(ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=new&amp;ops='.$sess['hash'] => $lang['all_new_firms'].$cnew));

			$p = $db->fetchrow($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_".$WORKMOD." WHERE pid = '1'"));
			$pnew = ($p['total'] > 0) ? ' ('.$p['total'].')' : '';
			if($conf[$WORKMOD]['edit'] == 'yes')
			{
				$block['link'] = array_merge($block['link'],array(ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=post&amp;ops='.$sess['hash'] => $lang['post_moderate'].$pnew));
			}
		}

        $block['link'] = array_merge($block['link'], array(
			ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=cat&amp;ops='.$sess['hash'] => $lang['all_cat'],
			ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=catadd&amp;ops='.$sess['hash'] => $lang['all_add_cat']
		));

		if (isset($conf[$WORKMOD]['resact']) AND $conf[$WORKMOD]['resact'] == 'yes')
		{
			$ac = $db->fetchrow($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_reviews WHERE file = '".$WORKMOD."' AND active = '1'"));
			$anew = ($ac['total'] > 0) ? ' ('.$ac['total'].')' : '';
			$block['link'] = array_merge($block['link'], array(ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=reviews&amp;ops='.$sess['hash'] => $lang['reviews'].$anew));
		}

		if (isset($conf[$WORKMOD]['resmoder']) AND $conf[$WORKMOD]['resmoder'] == 'yes')
		{
			$cr = $db->fetchrow($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_reviews WHERE file = '".$WORKMOD."' AND active = '0'"));
			$rnew = ($cr['total'] > 0) ? ' ('.$cr['total'].')' : '';
			$block['link'] = array_merge($block['link'], array(ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=newreviews&amp;ops='.$sess['hash'] => $lang['reviews_new'].$rnew));
		}

		if (isset($conf[$WORKMOD]['tags']) AND $conf[$WORKMOD]['tags'] == 'yes')
		{
			$block['link'] = array_merge($block['link'], array(ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=tag&amp;ops='.$sess['hash'] => $lang['all_tags']));
		}
	}
}
