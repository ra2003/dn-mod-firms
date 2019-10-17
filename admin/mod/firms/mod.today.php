<?php
/**
 * File:        /admin/mod/firms/mod.today.php
 *
 * @package     Danneo Basis kernel
 * @version     Danneo CMS (Next) v1.5.4
 * @copyright   (c) 2005-2017 Danneo Team
 * @link        http://danneo.ru
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('ADMREAD') OR die('No direct access');

global $db, $basepref, $conf, $lang, $sess, $realmod, $modposit, $modname, $ADMIN_PERM_ARRAY, $ADMIN_ID, $CHECK_ADMIN, $AJAX;

$WORKMOD = basename(__DIR__);

if (in_array($WORKMOD, $realmod))
{
	if (in_array($WORKMOD, $ADMIN_PERM_ARRAY) OR in_array($ADMIN_ID, $CHECK_ADMIN['admid']))
	{
		// Added
		$tadd = $db->fetchassoc($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_".$WORKMOD." WHERE (public >= '".$altime."')"));
		if ($tadd['total'] > 0)
		{
			$check = true;
			echo '	<tr>
						<td>'.$modname[$WORKMOD].'&nbsp; &#8260; &nbsp;'.$lang['all_new_firms'].'</td>
						<td>'.$tadd['total'].'</td>
						<td>
							<a href="'.ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=list&amp;atime='.$altime.'&amp;ops='.$sess['hash'].'">'.$lang['all_new_firms'].'</a>
						</td>
					</tr>';
		}

		if (isset($conf[$WORKMOD]['add']) AND $conf[$WORKMOD]['add'] == 'yes')
		{
			$tadd = $db->fetchassoc($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_".$WORKMOD."_user"));
			if ($tadd['total'] > 0)
			{
				$check = true;
				echo '	<tr>
							<td>'.$modname[$WORKMOD].'&nbsp; &#8260; &nbsp;'.$lang['added'].'</td>
							<td>'.$tadd['total'].'</td>
							<td>
								<a href="'.ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=new&amp;ops='.$sess['hash'].'">'.$lang['added'].'</a>
							</td>
						</tr>';
			}

			if (isset($conf[$WORKMOD]['modadd']) AND $conf[$WORKMOD]['modadd'] == 'yes')
			{
				$tmod = $db->fetchassoc($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_".$WORKMOD." WHERE pid = '1'"));
				if ($tmod['total'] > 0)
				{
					$check = true;
					echo '	<tr>
								<td>'.$modname[$WORKMOD].'&nbsp; &#8260; &nbsp;'.$lang['moderate_firms'].'</td>
								<td>'.$tmod['total'].'</td>
								<td>
									<a href="'.ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=post&amp;ops='.$sess['hash'].'">
										'.$lang['moderate_firms'].'
									</a>
								</td>
							</tr>';
				}
			}
		}

		// Reviews
		if (isset($conf[$WORKMOD]['resact']) AND $conf[$WORKMOD]['resact'] == 'yes')
		{
			$res = $db->fetchassoc($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_reviews WHERE file = '".$WORKMOD."' AND  (public >= '".$altime."') AND active = '1'"));
			if ($res['total'] > 0)
			{
				$check = true;
				echo '	<tr>
							<td>'.$modname[$WORKMOD].'&nbsp; &#8260; &nbsp;'.$lang['reviews_new'].'</td>
							<td>'.$res['total'].'</td>
							<td>
								<a href="'.ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=reviews&amp;atime='.$altime.'&amp;ops='.$sess['hash'].'">
									'.$lang['reviews_new'].'
								</a>
							</td>
						</tr>';
			}
			if (isset($conf[$WORKMOD]['resmoder']) AND $conf[$WORKMOD]['resmoder'] == 'yes')
			{
				$fn = $db->fetchassoc($db->query("SELECT COUNT(*) AS total FROM ".$basepref."_reviews WHERE file = '".$WORKMOD."' AND active = '0'"));
				if ($fn['total'] > 0)
				{
					$check = true;
					echo '	<tr>
								<td>'.$modname[$WORKMOD].'&nbsp; &#8260; &nbsp;'.$lang['reviews_moderate'].'</td>
								<td>'.$fn['total'].'</td>
								<td>
									<a href="'.ADMPATH.'/mod/'.$WORKMOD.'/index.php?dn=newreviews&amp;ops='.$sess['hash'].'">
										'.$lang['reviews_moderate'].'
									</a>
								</td>
							</tr>';
				}
			}
		}
	}
}
