<?php
/**
 * File:        /mod/firms/add.php
 *
 * @package     Danneo Basis kernel
 * @version     Danneo CMS (Next) v1.5.4
 * @copyright   (c) 2005-2017 Danneo Team
 * @link        http://danneo.ru
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('DNREAD') OR die('No direct access');

/**
 * Глобальные мода
 */
global $db, $basepref, $config, $lang, $usermain, $tm, $api, $global, $catid, $subcat,
       $title, $short, $more, $country, $region, $city, $address, $person, $email, $phone, $site, $skype, $social, $tags,
       $image, $file, $captcha, $cid, $respon;

/**
 * Рабочий мод
 */
define('WORKMOD', basename(__DIR__)); $conf = $config[WORKMOD];

/**
 * Файл доп. функций
 */
require_once(DNDIR.'mod/'.WORKMOD.'/mod.function.php');

/**
 * Добавление отключено, редирект
 */
if ($conf['addit'] == 'no')
{
	redirect($ro->seo('index.php?dn='.WORKMOD));
}

/**
 * Меню, хлебные крошки
 */
$global['insert']['current'] = $global['modname'];
$global['insert']['breadcrumb'] = array('<a href="'.$ro->seo('index.php?dn='.WORKMOD).'">'.$global['modname'].'</a>', '<a href="'.$ro->seo('index.php?dn='.WORKMOD.'&amp;re=my').'">'.$lang['firms_add'].'</a>');

/**
 * Доступ
 */
$ins['active'] = FALSE;
if($conf['adduse'] == 'user')
{
	if ( ! defined('USER_LOGGED'))
	{
		$tm->noaccessprint();
	}
	if (defined('GROUP_ACT') AND ! empty($conf['groups']))
	{
		$group = Json::decode($conf['groups']);
		if ( ! isset($group[$usermain['gid']]))
		{
			$tm->norightprint();
		}
		if (isset($group[$usermain['gid']]) AND $usermain['gid'] == 1)
		{
			$ins['active'] = TRUE;
		}
	}
}

/**
 * Метки
 */
$legaltodo = array('index', 'save', 'getcat');

/**
 * Проверка меток
 */
$to = (isset($to) && in_array($api->sitedn($to),$legaltodo)) ? $api->sitedn($to) : 'index';

/**
 * Метка index
 * ---------------- */
if ($to == 'index')
{
	$ins = $area = array();

	/**
	 * Свой TITLE
	 */
	if (isset($config['mod'][WORKMOD]['custom']) AND ! empty($config['mod'][WORKMOD]['custom']))
	{
		define('CUSTOM', $config['mod'][WORKMOD]['custom'].' | '.$lang['firms_add']);
	}

	/**
	 * Мета данные
	 */
	$global['descript'] = ( ! empty($config['mod'][WORKMOD]['descript'])) ? $config['mod'][WORKMOD]['descript'] : '';
	$global['keywords'] = ( ! empty($config['mod'][WORKMOD]['keywords'])) ? $config['mod'][WORKMOD]['keywords'] : '';

	/**
	 * Меню, хлебные крошки
	 */
	$global['insert']['current'] = $global['modname'];
	$global['insert']['breadcrumb'] = array('<a href="'.$ro->seo('index.php?dn='.WORKMOD).'">'.$global['modname'].'</a>', $lang['firms_add']);

	/**
	 * Вывод на страницу, шапка
	 */
	$tm->header();

	/**
	 * Категории
	 */
	$inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_cat ORDER BY posit ASC", $config['cachetime'], WORKMOD);
	while ($c = $db->fetchassoc($inq, $config['cache']))
	{
		$area[$c['parentid']][$c['catid']] = $c;
	}
	$api->catcache = $area;

	/**
	 * Проверки, ключи
	 */
	$tm->unmanule['sms']  = ($config['use_sms'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['captcha'] = ($config['captcha']=='yes' AND defined('REMOTE_ADDRS')) ? 'yes' : 'no';
	$tm->unmanule['control'] = ($config['control'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['editor']  = ($conf['addeditor'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['addfile']  = ($conf['addfile'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['modadd']  = ($conf['modadd'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['multcat']  = ( ! empty($area) AND $conf['multcat'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['showcat']  = ( ! empty($area)) ? 'yes' : 'no';

	/**
	 * Отключить проверку для пользователей
	 */
	noprotectspam(0);

	/**
	 * Контрольный вопрос
	 */
	$control = send_quest();

	/**
	 * Форма добавления
	 */
	$tm->parseprint(array
		(
			'post_url'     => $ro->seo('index.php?dn='.WORKMOD),
			'mod'          => WORKMOD,
			'title'        => $lang['all_title'],
			'email'        => $lang['e_mail'],
			'umail'        => (defined('USER_LOGGED')) ? $usermain['umail'] : '',
			'location'     => $lang['all_location'],
			'country'      => $lang['country'],
			'region'       => $lang['all_region'],
			'city'         => $lang['all_city'],
			'phones'       => $lang['phone'],
			'address'      => $lang['all_address'],
			'website'      => $lang['web_site'],
			'help_addr'    => $lang['place_address'],
			'contact'      => $lang['contact_data'],
			'login'        => $lang['all_login'],
			'social'       => $lang['firms_social'],
			'service'      => $lang['added_service'],
			'help_short'   => $lang['input_text'],
			'help_more'    => $lang['full_text'],
			'img_help'     => $lang['img_help'],
			'img_large'    => $lang['is_large'],
			'img_format'   => $lang['incor_format'],
			'sel'          => $api->selcat(),
			'cat_basic'    => $lang['cat_basic'],
			'cat_click'    => $lang['cat_click'],
			'multiple'     => $lang['multiple_help'],
			'all_refresh'  => $lang['all_refresh'],
			'captcha'      => $lang['all_captcha'],
			'help_captcha' => $lang['help_captcha'],
			'help_control' => $lang['help_control'],
			'control_word' => $lang['control_word'],
			'control'      => $control['quest'],
			'cid'          => $control['cid'],
			'image'        => $lang['all_image'],
			'alert_text'   => $lang['alert_text'],
			'moder_text'   => $lang['moder_text'],
			'select'       => $lang['all_select'],
			'not_email'    => $lang['not_email'],
			'not_empty'    => $lang['all_not_empty'],
			'other_cats'   => $lang['other_category'],
			'help_person'  => $lang['person'],
			'detail'       => $lang['in_detail'],
			'basic'        => $lang['all_basic'],
			'add_link'     => $lang['add_link'],
			'sel_file'     => $lang['select_file'],
			'add_file'     => $lang['attach_file'],
            'socialnet'    => $lang['social_net'],
            'linkpage'     => $lang['link_page'],
            'delet'        => $lang['all_delet'],
			'add_save'     => $lang['all_add'],
			'file_help'    => this_text(array('ext' => $conf['extfile']), $lang['file_help']),
			'extfile'      => str_replace(',', '|', $conf['extfile']),
			'maxfile'      => $conf['maxfile'],
		),
		$tm->parsein($tm->create('mod/'.WORKMOD.'/form.add'))
	);

	/**
	 * Вывод на страницу, подвал
	 */
	$tm->footer();
}

/**
 * Метка save
 * --------------- */
if ($to == 'save')
{
	/**
	 * Свой TITLE
	 */
	if (isset($config['mod'][WORKMOD]['custom']) AND ! empty($config['mod'][WORKMOD]['custom']))
	{
		define('CUSTOM', $config['mod'][WORKMOD]['custom'].' - '.$lang['firms_add']);
	}

	/**
	 * Мета данные
	 */
	$global['descript'] = ( ! empty($config['mod'][WORKMOD]['descript'])) ? $config['mod'][WORKMOD]['descript'].', '.$lang['profile'].' - '.$usermain['uname'] : '';
	$global['keywords'] = ( ! empty($config['mod'][WORKMOD]['keywords'])) ? $config['mod'][WORKMOD]['keywords'] : '';

	/**
	 * Меню, хлебные крошки
	 */
	$global['insert']['current'] = $global['modname'];
	$global['insert']['breadcrumb'] = array('<a href="'.$ro->seo('index.php?dn='.WORKMOD).'">'.$global['modname'].'</a>', $lang['firms_add']);

	$cid = preparse($cid, THIS_INT);
	$catid = preparse($catid, THIS_INT);

	/**
	 * Отключить проверки, для списка пользователей
	 */
	noprotectspam(1);

	/**
	 * Проверка секретного кода
	 */
	if ($config['captcha'] == 'yes')
	{
		if (findcaptcha(REMOTE_ADDRS, $captcha) == 1)
		{
			$tm->error($lang['bad_captcha'], $lang['all_error'], 0, 1, 1);
		}
	}

	/**
	 * Проверка контрольного вопроса
	 */
	check_quest($cid, $respon);

	/**
	 * Антифлудер
	 */
	$uadd = ($conf['addit'] == 'user') ? " AND userid = '".$usermain['userid']."'" : "";
	$checktime = $db->fetchrow
					(
						$db->query
						(
							"SELECT COUNT(id) AS total FROM ".$basepref."_".WORKMOD."_user WHERE (
							 addip = '".REMOTE_ADDRS."'".$uadd." AND public >= '".(NEWTIME - $conf['addtime'])."'
							)"
						)
					);

	if ($checktime['total'] > 0)
	{
		$tm->error($lang['add_time_error'], $lang['all_error'], 0, 1, 1);
	}

	/**
	 * Категории
	 */
	$catid = preparse($catid, THIS_INT);
	if ($catid > 0)
	{
		$cinq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_cat ORDER BY posit ASC", $config['cachetime'], WORKMOD);
		while ($citem = $db->fetchrow($cinq, $config['cache']))
		{
			$obj[$citem['catid']] = $citem;
		}
		$catid = (isset($obj[$catid])) ? $obj[$catid]['catid'] : 0;
	}

	/**
	 * Проверка телефона
	 */
	if ( ! preg_match('/^[0-9\-,()+ ]+$/D', trim($phone, ',')) )
	{
		$tm->error($lang['bad_phone'], $lang['all_error'], 0, 1, 1);
	}

	/**
	 * Проверка E-Mail
	 */
	if (verify_mail($email) == 0 AND ! empty($email))
	{
		$tm->error($lang['bad_mail'], $lang['all_error'], 0, 1, 1);
	}

	/**
	 * Обязательные поля
	 */
	if (in_array(null, array($catid, $title, $person, $phone, $email, $short)))
	{
		$tm->error($lang['pole_add_error'], $lang['all_error'], 0, 1, 1);
	}

	/**
	 * Соц. сети
	 */
	if (is_array($social) AND ! empty($social))
	{
		$s = array(); $k = 1;
		foreach ($social as $v) {
			if (isset($v['link']) AND ! empty($v['link']) AND isset($v['title']) AND ! empty($v['title']))
			{
				$s[$k] = array
						(
							'link'  => $v['link'],
							'title' => str_replace(array("'", '"'), '', $v['title']),
						);
			}
			$k ++;
		}
		$social = Json::encode($s);
	}

	$title = $api->sitesp(preparse($title, THIS_TRIM));
	$textshort = preparse($short, THIS_TRIM);
	$textmore  = preparse($more, THIS_TRIM);
	$country = preparse($country, THIS_TRIM, 0, 255);
	$region = preparse($region, THIS_TRIM, 0, 255);
	$city = preparse($city, THIS_TRIM, 0, 255);
	$address  = preparse($address, THIS_TRIM);
	$person = preparse($person, THIS_TRIM, 0, 255);
	$email = preparse($email, THIS_TRIM);
	$phone = preparse($phone, THIS_TRIM);
	$skype = preparse($skype, THIS_TRIM, 0, 255);
	$site = ( ! parse_url($site, PHP_URL_SCHEME) AND ! empty($site)) ? 'http://'.$site : $site;
	$image_thumb = '';

	$subtitle = $customs = $descript = $image_alt = $title;

	$trl = new Translit();
	$cpu = $trl->title($trl->process($title));

	check_name('cpu', $cpu);
	check_name('title', $title);
	check_title($title);

	$folder = strtolower(date("Y_M"));
	$ndir = '/up/'.WORKMOD.'/image/'.$folder;

	if(file_exists(DNDIR.$ndir))
	{
		if(is_dir(DNDIR.$ndir))
		{
			$folder = $folder;
		}
	}
	else
	{
		if(@mkdir(DNDIR.$ndir))
		{
			@chmod(DNDIR.$ndir, 0777);
			$html_write = fopen(DNDIR.$ndir."/index.html", "wb");
			fwrite($html_write,'<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body></body></html>');
			fclose($html_write);
		}
		else
		{
			$tm->error($lang['create_error'], $lang['all_error'], 0, 1, 1);
		}
	}

    /**
     * Загрузка и обработка изображения
     * ----------------------------------- */
	if (isset($_FILES['image']) AND ! empty($_FILES['image']['name']))
	{
		$tmp_name = $_FILES['image']['tmp_name'];
		if (is_uploaded_file($tmp_name))
		{
			$dirimg = 'up/'.WORKMOD.'/image/'.$folder.'/';
			$extname = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
			$newname = date("ymd", time()).'_'.mt_rand(0, 9999);
			$imgname = $newname.'.'.$extname;
			$imgname_thumb = $newname.'_thumb.'.$extname;

			$typename = exif_imagetype($tmp_name);
			if (in_array($typename, array(1, 2, 3))) // gif, jpg, png
			{
				if (filesize($tmp_name) <= $config['maxfile'])
				{
				 	if (move_uploaded_file($tmp_name, DNDIR.$dirimg.$imgname))
					{
						$img = new Image();
						if ($extname != 'jpg')
						{
							$img->imgconvert(DNDIR.$dirimg.$imgname, DNDIR.$dirimg.$newname.'.jpg');
							$imgname = $newname.'.jpg';
							$imgname_thumb = $newname.'_thumb.jpg';
						}

						$img->createthumb
								(
									DNDIR.$dirimg.$imgname,
									DNDIR.$dirimg,
									$imgname,
									$config['wbig'],
									$config['hbig'],
									'symm'
								);

						$img->createthumb
								(
									DNDIR.$dirimg.$imgname,
									DNDIR.$dirimg,
									$imgname_thumb,
									$config['width'],
									$config['height'],
									$config['resize']
								);

						$image = $dirimg.$imgname;
						$image_thumb = $dirimg.$imgname_thumb;

					} else {
						$tm->error($lang['down_na_title'].' Not move_uploaded_file', 0);
					}
				} else {
					$tm->error($lang['down_na_title'].' Not MAX_FILE_SIZE', 0);
				}
			} else {
				$tm->error($lang['down_na_title'].' Not imagetype', 0);
			}
		} else {
			$tm->error($lang['down_na_title'].' Not is_uploaded_file', 0);
		}
	}

    /**
     * Загрузка файла
     * --------------- */
	$files = null;
	if (isset($_FILES['file']) AND ! empty($_FILES['file']['name']))
	{
		$tmp_name = $_FILES['file']['tmp_name'];
		if (is_uploaded_file($tmp_name))
		{
			$dirifile = 'up/'.WORKMOD.'/files/';
			$extname = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
			$realname = substr($_FILES['file']['name'], 0, strpos($_FILES['file']['name'], "."));
			$newname = date("ymd", time()).'_'.mt_rand(0, 9999);
			$extarray = explode(',', $conf['extfile']);
			$filename = $newname.'.'.$extname;

			$file = array();
			if (in_array($extname, $extarray))
			{
				$file_size = floor(round(filesize($tmp_name) / 1024, 2)); // Kb
				if ($file_size <= $conf['maxfile'])
				{
				 	if (move_uploaded_file($tmp_name, DNDIR.$dirifile.$filename))
					{
						$file[1] = array
									(
										'path'  => $dirifile.$filename,
										'title' => $realname
									);
						$files = Json::encode($file);
					}
				}
				else
				{
					$tm->error($lang['is_large'], 0, 0);
				}
			}
			else
			{
				$tm->error($lang['incor_format'], 0, 0);
			}
		}
	}

	/**
	 * Добавление
	 */
	if ($conf['modadd'] == 'yes')
	{
		$cats =( ! empty($subcat)) ? implode(',', $subcat) : '';
		$db->query
			(
				"INSERT INTO ".$basepref."_".WORKMOD."_user VALUES (
				 NULL,
				 '".$catid."',
				 '".$db->escape($cats)."',
				 '".$db->escape($api->sitesp($title))."',
				 '".$db->escape($cpu)."',
				 '".NEWTIME."',
				 '0',
				 '0',
				 '".$db->escape($textshort)."',
				 '".$db->escape($textmore)."',
				 '".$db->escape($api->sitesp($country))."',
				 '".$db->escape($api->sitesp($region))."',
				 '".$db->escape($api->sitesp($city))."',
				 '".$db->escape($api->sitesp($address))."',
				 '".$usermain['uname']."',
				 '".$db->escape($api->sitesp($person))."',
				 '".$db->escape($email)."',
				 '".$db->escape($phone)."',
				 '".$db->escape($site)."',
				 '".$db->escape($skype)."',
				 '".$db->escape($image)."',
				 '".$db->escape($image_thumb)."',
				 '".$db->escape($files)."',
				 '".$db->escape($social)."',
				 '".$db->escape($tags)."',
				 '".REMOTE_ADDRS."',
				 '".$usermain['userid']."'
				)"
		);

		$tm->message($lang['moder_new'], 0, 0);
	}
	else
	{
		$max = $db->fetchassoc($db->query("SELECT MAX(posit) + 1 AS posit FROM ".$basepref."_".WORKMOD.""));
		$posit = empty($max['posit']) ? 0 : $max['posit'];

		$db->query
			(
				"INSERT INTO ".$basepref."_".WORKMOD." VALUES (
				 NULL,
				 '".$catid."',
				 '0',
				 '".NEWTIME."',
				 '0',
				 '0',
				 '".$cpu."',
				 '".$db->escape($api->sitesp($title))."',
				 '".$db->escape($api->sitesp($subtitle))."',
				 '".$db->escape($api->sitesp($customs))."',
				 '".$db->escape($api->sitesp($descript))."',
				 '',
				 '".$db->escape($textshort)."',
				 '".$db->escape($textmore)."',
				 '',
				 '',
				 '".$db->escape($image)."',
				 '".$db->escape($image_thumb)."',
				 'left',
				 '".$db->escape($api->sitesp($image_alt))."',
				 '',
				 '".$db->escape($country)."',
				 '".$db->escape($region)."',
				 '".$db->escape($city)."',
				 '".$db->escape($address)."',
				 '".$usermain['uname']."',
				 '".$db->escape($person)."',
				 '".$db->escape($phone)."',
				 '".$site."',
				 '".$email."',
				 '".$db->escape($skype)."',
				 '0',
				 '0',
				 '0',
				 '".$db->escape($files)."',
				 'all',
				 '',
				 'all',
				 '',
				 '0',
				 'yes',
				 '0',
				 '0',
				 '0',
				 '".$db->escape($tags)."',
				 '".$db->escape($social)."',
				 '".$usermain['userid']."',
				 '".$posit."',
				 '0'
				)"
			);

		$lastid = $db->insertid();

		if ($conf['multcat'] == 'yes')
		{
			$counts = new Counts(WORKMOD, 'id', 0);
			$counts->add($subcat, $catid, $lastid);
		}
		else
		{
			$counts = new Counts(WORKMOD, 'id');
		}

		$tm->message($lang['public_new'], 0, 0);
	}

	/**
	 * Если отправлять письмо
	 */
	if ($conf['mailadd'] == 'yes')
	{
		$subject = $lang['firmsadd_subject']." - ".$config['site'];
		$admin_mail = ( ! empty($conf['admin_mail']) ? $conf['admin_mail'] : $config['site_mail']);
		$message = this_text(array
						(
							"br"    => "\r\n",
							"title" => $title,
							"text"  => $textshort,
							"date"  => $api->siteuni($api->sitetime(NEWTIME, 1, 1))
						),
						$lang['firmsadd_message']
		);

		send_mail($admin_mail, $subject, $message, $config['site'], 'robot_'.$config['site_mail']);
	}

}

/**
 * Метка getcat
 * ------------ */
if($to == 'getcat')
{
	global $catid, $nocat;

	header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
	header('Content-Type: text/html; charset='.$config['langcharset'].'');

	$sql = ( ! empty($nocat)) ? " WHERE catid NOT IN (".$nocat.")" : "";

	$array = array();
	$inq = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".WORKMOD."_cat".$sql." ORDER BY posit ASC");
	while ($item = $db->fetchassoc($inq))
	{
		$array[$item['parentid']][$item['catid']] = $item;
	}

	function select($cid = 0, $depth = 0)
	{
		global $array, $api;

		if ( ! isset($array[$cid]) )
			return false;

		foreach ($array[$cid] as $val)
		{
			$indent = ($depth > 0) ? str_repeat('&nbsp;&nbsp;', $depth) : '';
			echo '<option value="'.$val['catid'].'">'.$indent.$api->siteuni($val['catname']).'</option>';
			select($val['catid'], $depth + 1);
		}
		unset($array[$cid]);
	}

	select();
	exit();
}
