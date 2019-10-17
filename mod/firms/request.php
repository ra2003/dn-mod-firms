<?php
/**
 * File:        /mod/tender/request.php
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
global $dn, $to, $db, $basepref, $config, $lang, $usermain, $tm, $api,
       $name, $skype, $phone, $email, $message, $id;

/**
 * Рабочий мод
 */
define('WORKMOD', basename(__DIR__)); $conf = $config[WORKMOD];

/**
 * Метки
 */
$legaltodo = array('index', 'send');

/**
 * Проверка меток
 */
$to = (isset($to) && in_array($api->sitedn($to), $legaltodo)) ? $api->sitedn($to) : 'index';

/**
 * Метка index
 * ------------- */
if ($to == 'index')
{
    $id = preparse($id, THIS_INT);

	header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
	header('Content-Type: text/html; charset='.$config['langcharset'].'');

	/**
	 * Вывод
	 */
	$tm->parseprint(array
		(
			'post_url'    => $ro->seo('index.php?dn='.WORKMOD),
			'email_name'  => $lang['email_name'],
			'email'       => $lang['e_mail'],
			'email_text'  => $lang['email_text'],
			'email_phone' => $lang['mail_phone'],
			'uname'       => $usermain['uname'],
			'umail'       => $usermain['umail'],
			'title_send'  => $lang['link_send_mess'],
			'not_empty'   => $lang['all_not_empty'],
			'all_sends'   => $lang['all_sends'],
			'id'          => $id,
			'email_send'  => $lang['email_send']
		),
		$tm->parsein($tm->create('mod/'.WORKMOD.'/form.request')));

	exit();
}

/**
 * Метка send Request
 * -------------------- */
if ($to == 'send')
{
    $id = preparse($id, THIS_INT);

	header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
	header('Content-Type: text/html; charset='.$config['langcharset'].'');

	$item = $db->fetchrow($db->query("SELECT email FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."' LIMIT 1"));

	$ins['error'] = $ins['mess'] = null;
	$tm->manuale = array
		(
			'ok' => null,
			'error' => null
		);

	$ins['template'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/send.request'));

	/**
	 * Данные для отправки
	 */
	$from = $name." <".$email.">";
	$subject = $lang['message']." — ".$config['site'];
	$mess = this_text(array
				(
					"br" => "\r\n",
					"name" => $name,
					"phone" => $phone,
					"email" => $email,
					"message" => $message
				),
				$lang['user_mail_msg']);

	$cho = send_mail($item['email'], $subject, $mess, $from, '', true);

	/**
	 * Вывод сообщения
	 */
	if ($cho === TRUE)
	{
		$ins['mess'] = $tm->parse(array
						(
							'title'   => $lang['senk_title'],
							'success' => $lang['success']
						),
						$tm->manuale['ok']);

		$tm->parseprint(array('error' => '', 'message' => $ins['mess']), $ins['template']);
	}
	else
	{
		$ins['error'] = $tm->parse(array
							(
								'title' => $lang['isset_error'],
								'notice' => $lang['mess_error']
							),
							$tm->manuale['error']);

		$tm->parseprint(array('error' => $ins['error'], 'message' => ''), $ins['template']);
	}

	exit();
}
