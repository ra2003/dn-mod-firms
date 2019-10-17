<?php
/**
 * File:        /mod/firms/my.php
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
global $db, $basepref, $config, $lang, $usermain, $tm, $api, $global, $catid, $subcat,
       $title, $short, $more, $country, $region, $city, $address, $person, $email, $phone, $site, $skype, $social, $tags,
       $image, $file, $captcha, $cid, $respon, $ok;

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
$global['insert']['breadcrumb'] = array('<a href="'.$ro->seo('index.php?dn='.WORKMOD).'">'.$global['modname'].'</a>', '<a href="'.$ro->seo('index.php?dn='.WORKMOD.'&amp;re=my').'">'.$lang['firms_my'].'</a>');

/**
 * Доступ
 */
$profile = $egroups = $dgroups = $agroups = $dagroups = $vgroups = $dvgroups = FALSE;
if($conf['adduse'] == 'user')
{
	$profile = $egroups = $dgroups = $agroups = $dagroups = $vgroups = $dvgroups = TRUE;

	if ( ! defined('USER_LOGGED'))
	{
		$tm->noaccessprint();
	}

	if (defined('GROUP_ACT') AND ! empty($conf['groups']))
	{
		$profile = FALSE;
		$group = Json::decode(stripcslashes($conf['groups']));
		if ( ! isset($group[$usermain['gid']]))
		{
			$tm->error($lang['no_rights'], $lang['all_error'], 0, 0, 1);
		}
		else
		{
			$profile = TRUE;
		}
	}
	if (defined('GROUP_ACT') AND ! empty($conf['egroups']))
	{
		$egroups = FALSE;
		$egroup = Json::decode(stripcslashes($conf['egroups']));
		if (isset($egroup[$usermain['gid']]))
		{
			$egroups = TRUE;
		}
	}
	if (defined('GROUP_ACT') AND ! empty($conf['dgroups']))
	{
		$dgroups = FALSE;
		$dgroup = Json::decode(stripcslashes($conf['dgroups']));
		if (isset($dgroup[$usermain['gid']]))
		{
			$dgroups = TRUE;
		}
	}

	// Photo
	if (defined('GROUP_ACT') AND ! empty($conf['agroups']))
	{
		$agroups = FALSE;
		$agroup = Json::decode(stripcslashes($conf['agroups']));
		if (isset($agroup[$usermain['gid']]))
		{
			$agroups = TRUE;
		}
	}
	if (defined('GROUP_ACT') AND ! empty($conf['dagroups']))
	{
		$dagroups = FALSE;
		$dagroup = Json::decode(stripcslashes($conf['dagroups']));
		if (isset($dagroup[$usermain['gid']]))
		{
			$dagroups = TRUE;
		}
	}

	// Video
	if (defined('GROUP_ACT') AND ! empty($conf['vgroups']))
	{
		$vgroups = FALSE;
		$vgroup = Json::decode(stripcslashes($conf['vgroups']));
		if (isset($vgroup[$usermain['gid']]))
		{
			$vgroups = TRUE;
		}
	}
	if (defined('GROUP_ACT') AND ! empty($conf['dvgroups']))
	{
		$dvgroups = FALSE;
		$dvgroup = Json::decode(stripcslashes($conf['dvgroups']));
		if (isset($dvgroup[$usermain['gid']]))
		{
			$dvgroups = TRUE;
		}
	}
}

if ( ! $profile )
{
	$tm->error($lang['no_rights'], $lang['all_error'], 0, 0, 1);
}

/**
 * Метки
 */
$legaltodo = array
	(
		'index', 'edit', 'save', 'del',
		'photo', 'photoadd', 'photoup', 'photodel',
		'video', 'videoadd', 'videoup', 'videodel',
		'getcat', 'thumb'
	);

/**
 * Проверка меток
 */
$to = (isset($to) && in_array($api->sitedn($to), $legaltodo)) ? $api->sitedn($to) : 'index';

/**
 * Метка index
 * ---------------- */
if ($to == 'index')
{
	$ins = array();

	/**
	 * Свой TITLE
	 */
	if (isset($config['mod'][WORKMOD]['custom']) AND ! empty($config['mod'][WORKMOD]['custom']))
	{
		define('CUSTOM', $config['mod'][WORKMOD]['custom'].' | '.$lang['firms_my']);
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
	$global['insert']['breadcrumb'] = array('<a href="'.$ro->seo('index.php?dn='.WORKMOD).'">'.$global['modname'].'</a>', $lang['firms_my']);

	/**
	 * Вывод на страницу, шапка
	 */
	$tm->header();

	/**
	 * Категории
	 */
	$cats = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_cat ORDER BY posit ASC", $config['cachetime'], WORKMOD);;
	while ($c = $db->fetchassoc($cats, $config['cache']))
	{
		$area[$c['parentid']][$c['catid']] = $obj[$c['catid']] = $c;
	}

	$inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE userid = '".$usermain['userid']."'");

    /**
	 * Вложенные шаблоны
	 */
	$tm->manuale = array
		(
			'titleok' => null,
			'titleno' => null,
			'photo'   => null,
			'video'   => null,
			'edit'    => null,
			'delete'  => null,
			'reason'  => null,
			'status'  => null
		);

	$ins['content'] = null;
	$tm->unmanule['data'] = 'no';
	if($db->numrows($inq) > 0)
	{
		$tm->unmanule['data'] = 'yes';
		$ins['template'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/my.rows'));

		while ($item = $db->fetchrow($inq))
		{
			$ins['edit'] = $ins['delete'] = $ins['reason'] = $ins['status'] = $ins['photo'] = $ins['video'] = $ins['style'] = null;

			$ins['cpu'] = (defined('SEOURL') AND $item['cpu']) ? '&amp;cpu='.$item['cpu'] : '';
			$ins['catcpu'] = (defined('SEOURL') AND ! empty($obj[$item['catid']]['catcpu'])) ? '&amp;ccpu='.$obj[$item['catid']]['catcpu'] : '';
			$ins['url_title'] = $ro->seo('index.php?dn='.WORKMOD.$ins['catcpu'].'&amp;to=page&amp;id='.$item['id'].$ins['cpu']);

			if (($conf['edit'] == 'yes' AND $egroups) OR ($conf['edit'] == 'yes' AND ! empty($item['reason']) AND $egroups))
			{
				$ins['url_edit'] = $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=edit&amp;id='.$item['id']);
				$ins['edit'] = $tm->parse(array('mod'  => WORKMOD, 'url'  => $ins['url_edit'], 'alt' => $lang['all_edit'] ), $tm->manuale['edit']);
			}

			if ($item['pid'] == 0)
			{
				$ins['title'] = $tm->parse(array
									(
										'mod'  => WORKMOD,
										'url'  => $ins['url_title'],
										'name' => $api->siteuni($item['title'])
									),
									$tm->manuale['titleok']);

				if ($conf['photo_add'] == 'yes' AND $agroups)
				{
					$ins['url_photo'] = $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=photo&amp;id='.$item['id']);
					$ins['photo'] = $tm->parse(array('mod'  => WORKMOD, 'url'  => $ins['url_photo'], 'alt' => $lang['photo_album'], 'count' => $item['photos']), $tm->manuale['photo']);
				}

				if ($conf['video_add'] == 'yes' AND $vgroups)
				{
					$ins['url_video'] = $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=video&amp;id='.$item['id']);
					$ins['video'] = $tm->parse(array('mod' => WORKMOD, 'url' => $ins['url_video'], 'alt' => $lang['video_album'], 'count' => $item['videos']), $tm->manuale['video']);
				}

				if ($conf['delete'] == 'yes' AND $dgroups)
				{
					$ins['url_del'] = $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=del&amp;id='.$item['id']);
					$ins['delete'] = $tm->parse(array('mod' => WORKMOD, 'url' => $ins['url_del'], 'alt' => $lang['all_delet'] ), $tm->manuale['delete']);
				}
			}
			else
			{
				$ins['title'] = $tm->parse(array
									(
										'mod' => WORKMOD,
										'name' => $api->siteuni($item['title'])
									),
									$tm->manuale['titleno']);

				if (empty($item['reason']))
				{
					$ins['status'] = $tm->parse(array
									(
										'mod' => WORKMOD,
										'lang_status' => $lang['post_moderate']
									),
									$tm->manuale['status']);
				}

				$ins['style'] = 'mod';
			}


			if ( ! empty($item['reason']) )
			{
				$ins['reason'] = $tm->parse(array('textnotice' => $api->siteuni($item['reason'])), $tm->manuale['reason']);
			}

			$ins['content'].= $tm->parse(array
								(
									'mod'    => WORKMOD,
									'id'     => $item['id'],
									'title'  => $ins['title'],
									'status' => $ins['status'],
									'photo'  => $ins['photo'],
									'video'  => $ins['video'],
									'edit'   => $ins['edit'],
									'delete' => $ins['delete'],
									'reason' => $ins['reason'],
									'style'  => $ins['style']
								),
								$ins['template']);
		}

	}

	/**
	 * Вывод
	 */
	$tm->parseprint(array
		(
			'title'    => $lang['firms_my'],
			'name'     => $lang['all_name'],
			'manage'   => $lang['sys_manage'],
			'photo'    => $lang['photo_one'],
			'video'    => $lang['all_video'],
			'data_not' => $lang['data_not'],
			'print'    => $ins['content']
		),
		$tm->parsein($tm->create('mod/'.WORKMOD.'/my')));

	/**
	 * Вывод на страницу, подвал
	 */
	$tm->footer();
}

/**
 * Метка index
 * ---------------- */
if ($to == 'edit')
{
    $id = preparse($id, THIS_INT);
    $ins = array();

	/**
	 * Свой TITLE
	 */
	if (isset($config['mod'][WORKMOD]['custom']) AND ! empty($config['mod'][WORKMOD]['custom']))
	{
		define('CUSTOM', $config['mod'][WORKMOD]['custom'].' - '.$lang['firms_my']);
	}

	/**
	 * Мета данные
	 */
	$global['descript'] = ( ! empty($config['mod'][WORKMOD]['descript'])) ? $config['mod'][WORKMOD]['descript'].', '.$lang['firms_my'].' - '.$lang['all_edit'] : '';
	$global['keywords'] = ( ! empty($config['mod'][WORKMOD]['keywords'])) ? $config['mod'][WORKMOD]['keywords'] : '';

	/**
	 * Меню, хлебные крошки
	 */
	$global['insert']['current'] = $global['modname'];
	$global['insert']['breadcrumb'] = array('<a href="'.$ro->seo('index.php?dn='.WORKMOD).'">'.$global['modname'].'</a>', '<a href="'.$ro->seo('index.php?dn='.WORKMOD.'&amp;re=my').'">'.$lang['firms_my'].'</a>', $lang['all_edit']);

	/**
	 * Если редактирование отключено, редирект
	 */
	if ($conf['edit']== 'no' OR ! $egroups)
	{
		$tm->norightprint();
	}

	$valid = $db->query
				(
					"SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."' AND userid = '".$usermain['userid']."'
					 AND act = 'yes'"
				);

    $item = $db->fetchrow($valid);

    /**
     * Страницы не существует
     */
    if ($db->numrows($valid) == 0)
    {
        $tm->noexistprint();
    }

    /**
     * Меню, хлебные крошки
     */

	/**
	 * Вывод на страницу, шапка
	 */
	$tm->header();

    /**
     * Категории
     */
    $catcache = array();
    $catid = $item['catid'];
    $inquiry = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".WORKMOD."_cat ORDER BY posit ASC");
    while ($items = $db->fetchrow($inquiry))
    {
        $catcache[$items['parentid']][$items['catid']] = $items;
    }

	/**
	 * Проверки, ключи
	 */
    $tm->unmanule['captcha'] = ($config['captcha']=='yes' && defined("REMOTE_ADDRS")) ? 'yes' : 'no';
    $tm->unmanule['control'] = ($config['control'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['editor']  = ($conf['addeditor'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['addfile'] = ($conf['addfile'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['modedit'] = ($conf['modedit'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['showcat'] = ( ! empty($catcache)) ? 'yes' : 'no';
	$tm->unmanule['multcat'] = ( ! empty($catcache) AND $conf['multcat'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['image'] = ! empty($item['image_thumb']) ? 'yes' : 'no';
	$tm->unmanule['file'] = ! empty($item['files']) ? 'yes' : 'no';

	/**
	 * Отключить проверку для пользователей
	 */
	noprotectspam(0);

	/**
	 * Контрольный вопрос
	 */
	$control = send_quest();

	/**
	 * Изображение
	 */
	$src_img = $name_img = $ext_img = '';
	if ( ! empty($item['image_thumb']))
	{
		$path_img = pathinfo($item['image_thumb']);
		$src_img  = $item['image_thumb'];
		$name_img = $path_img['filename'];
		$ext_img  = $path_img['extension'];
	}

	/**
	 * Файл
	 */
	$name_file = $ext_file = '';
	if ( ! empty($item['files']))
	{
		$files = Json::decode($item['files']);
		$name_file = $files[1]['title'];
		$ext_file  = pathinfo($files[1]['path'], PATHINFO_EXTENSION);
	}

	/**
	 * Вывод категорий
	 */
    this_selectcat(0);

    /**
	 * Вложенные шаблоны
	 */
	$tm->manuale = array
		(
			'ifile' => null,
			'rfile' => null,
			'mysocial' => null
		);

    $ins['template'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/form.edit'));

	/**
	 * Соц.сети
	 */
	$sk = 1; $ins['msrows'] = '';
	$social = Json::decode($item['social']);
	if (is_array($social) AND ! empty($social))
	{
		foreach ($social as $v)
		{
			$ins['msrows'].= $tm->parse(array(
								'sk'    => $sk,
								'link'  => $v['link'],
								'title' => $v['title']
							),
							$tm->manuale['mysocial']);
			$sk ++;
		}
	}

	/**
	 * Файлы
	 */
	if ( ! empty($item['files']))
	{
		$ins['file'] = $tm->parse(array(
							'name' => $name_file,
							'ext'  => $ext_file
						),
						$tm->manuale['rfile']);
	}
	else
	{
		$ins['file'] = $tm->parse(array(
							'add' => $lang['attach_file']
						),
						$tm->manuale['ifile']);
	}

    /**
     * Форма добавления
     */
    $tm->parseprint
    (
        array
        (
            'id'           => $id,
			'mod'          => WORKMOD,
			'post_url'     => $ro->seo('index.php?dn='.WORKMOD),
            'title'        => $lang['all_title'],
            'sel'          => $selective,
            'control'      => $control,
            'cid'          => $cid,
			// Re
            'sk'           => $sk,
            'msrows'       => $ins['msrows'],
			'src_img'      => $src_img,
			'name_img'     => $name_img,
			'ext_img'      => $ext_img,
            'out_file'     => $ins['file'],
			're_title'     => $api->siteuni($item['title']),
			're_author'    => $api->siteuni($item['author']),
			're_person'    => $api->siteuni($item['person']),
			're_region'    => $api->siteuni($item['region']),
			're_country'   => $api->siteuni($item['country']),
			're_city'      => $api->siteuni($item['city']),
			're_address'   => $api->siteuni($item['address']),
            're_phone'     => $item['phone'],
			're_mail'      => $item['email'],
            're_skype'     => $item['skype'],
            're_site'      => $item['site'],
			're_short'     => $api->siteuni($item['textshort']),
			're_more'      => $api->siteuni($item['textmore']),
			'file_help'    => this_text(array('ext' => $conf['extfile']), $lang['file_help']),
			'extfile'      => str_replace(',', '|', $conf['extfile']),
			'maxfile'      => $conf['maxfile'],
			// Lang
            'website'      => $lang['web_site'],
            'phones'       => $lang['phone'],
			'email'        => $lang['e_mail'],
			'not_email'    => $lang['not_email'],
            'address'      => $lang['all_address'],
			'help_person'  => $lang['person'],
            'contact'      => $lang['contact_data'],
            'login'        => $lang['all_login'],
            'social'       => $lang['firms_social'],
			'cat_basic'    => $lang['cat_basic'],
			'cat_click'    => $lang['cat_click'],
			'other_cats'   => $lang['other_category'],
			'multiple'     => $lang['multiple_help'],
            'service'      => $lang['added_service'],
			'help_short'   => $lang['input_text'],
			'help_more'    => $lang['full_text'],
            'img_help'     => $lang['img_help'],
			'img_large'    => $lang['is_large'],
			'img_format'   => $lang['incor_format'],
            'all_refresh'  => $lang['all_refresh'],
			'add_link'     => $lang['add_link'],
			'sel_file'     => $lang['select_file'],
			'add_file'     => $lang['attach_file'],
            'captcha'      => $lang['all_captcha'],
            'help_captcha' => $lang['help_captcha'],
            'help_control' => $lang['help_control'],
            'control_word' => $lang['control_word'],
            'location'     => $lang['all_location'],
            'country'      => $lang['country'],
            'region'       => $lang['all_region'],
            'city'         => $lang['all_city'],
            'cat_click'    => $lang['cat_click'],
            'image'        => $lang['all_image'],
            'all_add'      => $lang['all_add'],
			'detail'       => $lang['in_detail'],
			'basic'        => $lang['all_basic'],
            'select'       => $lang['all_select'],
            'not_empty'    => $lang['all_not_empty'],
            'moderation'   => $lang['send_edit'],
            'edit_save'    => $lang['all_save'],
            'delet'        => $lang['all_delet'],
            'socialnet'    => $lang['social_net'],
            'linkpage'     => $lang['link_page']
            ),
			$ins['template']
    );

	/**
	 * Вывод на страницу, подвал
	 */
	$tm->footer();
}

/**
 * Метка save
 * ------------ */
if ($to == 'save')
{
	$ins = $obj = array();

    $id = preparse($id, THIS_INT);
    $cid = preparse($cid, THIS_INT);
    $catid = preparse($catid, THIS_INT);

	$valid = $db->query
				(
					"SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."' AND userid = '".$usermain['userid']."'
					 AND act = 'yes'"
				);

    $item = $db->fetchassoc($valid);

    /**
     * Страницы не существует
     */
    if ($db->numrows($valid) == 0)
    {
        $tm->noexistprint();
    }

	/**
	 * Свой TITLE
	 */
	if (isset($config['mod'][WORKMOD]['custom']) AND ! empty($config['mod'][WORKMOD]['custom']))
	{
		define('CUSTOM', $config['mod'][WORKMOD]['custom'].' - '.$lang['firms_my']);
	}

	/**
	 * Мета данные
	 */
	$global['descript'] = ( ! empty($config['mod'][WORKMOD]['descript'])) ? $config['mod'][WORKMOD]['descript'].', '.$lang['firms_my'].' - '.$lang['all_edit'] : '';
	$global['keywords'] = ( ! empty($config['mod'][WORKMOD]['keywords'])) ? $config['mod'][WORKMOD]['keywords'] : '';

	/**
	 * Меню, хлебные крошки
	 */
	$global['insert']['current'] = $global['modname'];
	$global['insert']['breadcrumb'] = array('<a href="'.$ro->seo('index.php?dn='.WORKMOD).'">'.$global['modname'].'</a>', '<a href="'.$ro->seo('index.php?dn='.WORKMOD.'&amp;re=my').'">'.$lang['firms_my'].'</a>', $lang['all_edit']);

	if ($conf['edit'] == 'no' OR ! $egroups)
	{
		$tm->error($lang['no_rights'], $lang['all_error'], 0, 0, 1);
	}

    /**
     * Отключить проверку для авторизованных
     */
	noprotectspam(1);

    /**
     * Проверка captcha
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
     * Категории
     */
    if ($catid > 0)
	{
    	$cinq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_cat ORDER BY posit ASC", $config['cachetime'],WORKMOD);
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
     * Проверка данных
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

	$subtitle = $customs = $descript = $image_alt = $title;

	$trl = new Translit();
	$cpu = $trl->title($trl->process($title));

	check_name_edit('cpu', $cpu, $id);
	check_name_edit('title', $title, $id);

	$author = preg_replace('/[^\pL\pNd\pZs\pP\pM]/us', '', $usermain['userid']);

	if ( ! empty($person))
	{
		$person = preg_replace('/[^\pL\pNd\pZs\pP\pM]/us', '', $person);
	}

	$image = ( ! empty($item['image'])) ? $item['image'] : '';
	$image_thumb = ( ! empty($item['image_thumb'])) ? $item['image_thumb'] : '';
	$image_alt = ( ! empty($item['image_thumb'])) ? $title : '';

    /**
     * Изображение
     * ------------- */
	if (isset($_FILES['image']) AND ! empty($_FILES['image']['name']))
	{
		$tmp_name = $_FILES['image']['tmp_name'];
		if (is_uploaded_file($tmp_name))
		{
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

			$dirimg = 'up/'.WORKMOD.'/image/'.$folder.'/';
			$extname = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
			$newname = date("ymd", time()).'_'.mt_rand(0, 9999);
			$imgname = $newname.'.'.$extname;
			$imgname_thumb = $newname.'_thumb.'.$extname;

			$typename = exif_imagetype($tmp_name);
			if (in_array($typename, array(1, 2, 3))) // gif, jpg, png
			{
				$img_size = floor(round(filesize($tmp_name) / 1024, 2)); // Kb
				if ($img_size <= $conf['maxfile'])
				{
				 	if (move_uploaded_file($tmp_name, DNDIR.$dirimg.$imgname))
					{
						require DNDIR.'/core/classes/Image.php';
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
								$imgname_thumb,
								$config['width'],
								$config['height'],
								$config['resize']
							);

						if ($conf['bigimage'] == 'yes')
						{
							$img->createthumb
								(
									DNDIR.$dirimg.$imgname,
									DNDIR.$dirimg,
									$imgname,
									$config['wbig'],
									$config['hbig'],
									'symm'
								);
						}
						else
						{
							unlink(DNDIR.$dirimg.$imgname);
						}
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

		if ( ! empty($item['image_thumb']))
		{
			unlink(DNDIR.$item['image']);
			unlink(DNDIR.$item['image_thumb']);
		}

		$image = ($conf['bigimage'] == 'yes') ? $dirimg.$imgname : '';
		$image_thumb = $dirimg.$imgname_thumb;
		$image_alt = ( ! empty($image_thumb)) ? $title : '';
	}

	$files = ( ! empty($item['files'])) ? $item['files'] : '';

    /**
     * Файл
     * ------- */
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

		if ( ! empty($item['files']))
		{
			$dec = Json::decode($item['files']);
			foreach ($dec as $v)
			{
				unlink(DNDIR.$v['path']);
			}
		}
	}

	$pid = ($conf['modedit'] == 'yes') ? 1 : 0;

	$db->query
		(
			"UPDATE ".$basepref."_".WORKMOD." SET
			 catid       = '".$catid."',
			 cpu         = '".$cpu."',
			 title       = '".$db->escape($api->sitesp($title))."',
			 subtitle    = '".$db->escape($api->sitesp($subtitle))."',
			 customs     = '".$db->escape($api->sitesp($customs))."',
			 descript    = '".$db->escape($api->sitesp($descript))."',
			 textshort   = '".$db->escape($textshort)."',
			 textmore    = '".$db->escape($textmore)."',
			 image       = '".$db->escape($image)."',
			 image_thumb = '".$db->escape($image_thumb)."',
			 image_alt   = '".$db->escape($api->sitesp($image_alt))."',
			 country     = '".$db->escape($country)."',
			 region      = '".$db->escape($region)."',
			 city        = '".$db->escape($city)."',
			 address     = '".$db->escape($address)."',
			 person      = '".$db->escape($person)."',
			 phone       = '".$db->escape($phone)."',
			 site        = '".$site."',
			 email       = '".$email."',
			 skype       = '".$db->escape($skype)."',
			 files       = '".$db->escape($files)."',
			 social      = '".$db->escape($social)."',
			 pid         = '".$pid."'
			 WHERE id = '".$id."'"
		);

	if ($conf['multcat'] == 'yes')
	{
		$counts = new Counts(WORKMOD, 'id', 0);
		$counts->edit($subcat, $catid, $id);
	}
	else
	{
		$counts = new Counts(WORKMOD, 'id');
	}

	/**
	 * Сообщение на E-Mail
	 */
	if ($pid)
	{
		if ($conf['mailadd'] == 'yes')
		{
			$admin_mail = ( ! empty($conf['admin_mail'])) ? $conf['admin_mail'] : $config['site_mail'];
			$subject = $lang['firmsedit_subject'];
			$message = this_text(array
						(
							"br" => "\r\n",
							"uname" => $author,
							"title" => $title,
							"text" => deltags($textshort),
							"site" => $config['site'],
							"date" => $api->sitetime(NEWTIME, 1, 1)
						),
						$lang['moder_mail_msg']);

			send_mail($admin_mail, $subject, $message, $config['site']." <".$config['site_mail'].">");
		}

		$tm->message($lang['edit_moder'], 0, 0);
	}
	else {
		$tm->message($lang['edit_public'], 0, 0);
	}
}

/**
 * Метка photo
 * ---------------- */
if ($to == 'photo')
{
    $ins = array();
	$id = preparse($id, THIS_INT);

    /**
	 * Вложенные шаблоны
	 */
	$tm->manuale = array
		(
			'photo' => null,
			'datanot' => null
		);

	$valid = $db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."' AND userid = '".$usermain['userid']."' AND act = 'yes'");
    $item = $db->fetchrow($valid);

	/**
	 * Доступ
	 */
    if ($db->numrows($valid) == 0 OR $conf['photo_add']== 'no' OR ! $agroups)
    {
		$tm->error($lang['no_rights'], $lang['all_error'], 0, 0, 1);
    }

	$inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_photo WHERE firm_id = '".$id."' AND act = 'yes' ORDER BY posit DESC");

	/**
	 * Вывод на страницу, шапка
	 */
	$tm->header();

	$ins['photos'] = null;
	if ($db->numrows($inq) > 0)
	{
		$tm->unmanule['data'] = 'yes';
		$ins['template'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/photo.my'));
		while ($photo = $db->fetchrow($inq))
		{
			$ins['photos'].= $tm->parse(array
								(
									'thumb_url' => $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=thumb&amp;type=photo&amp;id='.$photo['id'].'&amp;x=48&amp;h=36&amp;r=yes'),
									'del_url'   => $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=photodel&amp;fid='.$id.'&amp;id='.$photo['id']),
									'title'     => $api->siteuni($photo['title']),
									'posit'     => $photo['posit'],
									'image'     => $photo['image'],
									'edit'      => $lang['all_edit'],
									'del'       => $lang['all_delet'],
									'alt'       => $lang['photo_open'],
									'id'        => $photo['id'],
									'mod'       => WORKMOD
								),
								$tm->manuale['photo']);
		}
	}
	else
	{
		$tm->unmanule['data'] = 'no';
		$ins['template'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/photo.my'));
		$ins['photos'].= $tm->parse(array('data_not' => $lang['data_not']), $tm->manuale['datanot']);
	}

	/**
	 * Вывод
	 */
	$tm->parseprint(array
		(
			'post_url'   => $ro->seo('index.php?dn='.WORKMOD),
			'title'      => $api->siteuni($item['title']),
			'photo_add'  => $lang['photo_add'],
			'descript'   => $lang['descript'],
			'add_save'   => $lang['all_add'],
			'img_help'   => $lang['img_help'],
			'img_large'  => $lang['is_large'],
			'img_format' => $lang['incor_format'],
			'sel_file'   => $lang['select_file'],
			'image'      => $lang['all_image'],
			'album'      => $lang['photo_album'],
			'name'       => $lang['all_name'],
			'photo'      => $lang['photo_one'],
			'posit'      => $lang['position'],
			'manage'     => $lang['sys_manage'],
			'save'       => $lang['all_save'],
			'print'	     => $ins['photos'],
			'fid'	     => $id
		),
		$ins['template']);

	/**
	 * Вывод на страницу, подвал
	 */
	$tm->footer();
}

/**
 * Метка photoup
 * -------------- */
if ($to == 'photoup')
{
	$id = preparse($id, THIS_INT);

	$valid = $db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."' AND userid = '".$usermain['userid']."' AND act = 'yes'");

	/**
	 * Доступ
	 */
    if ($db->numrows($valid) == 0 OR $conf['photo_add']== 'no' OR ! $agroups)
    {
		$tm->error($lang['no_rights'], $lang['all_error'], 0, 0, 1);
    }

	if (is_array($title) AND ! empty($title))
	{
		foreach ($title as $k => $v)
		{
			$k = preparse($k, THIS_INT);
			$v = preparse($v, THIS_TRIM);
			$db->query("UPDATE ".$basepref."_".WORKMOD."_photo SET title = '".$db->escape($api->sitesp($v))."' WHERE id = '".$k."'");
		}
	}

	if (is_array($posit) AND ! empty($posit))
	{
		foreach ($posit as $k => $v)
		{
			$k = preparse($k, THIS_INT);
			$v = preparse($v, THIS_INT);
			$db->query("UPDATE ".$basepref."_".WORKMOD."_photo SET posit = '".$v."' WHERE id = '".$k."'");
		}
	}

	redirect($ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=photo&amp;id='.$id));
}

/**
 * Метка photoadd
 * ----------------- */
if ($to == 'photoadd')
{
	$id = preparse($id, THIS_INT);

	$valid = $db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."' AND userid = '".$usermain['userid']."' AND act = 'yes'");

	/**
	 * Доступ
	 */
    if ($db->numrows($valid) == 0 OR $conf['photo_add']== 'no' OR ! $agroups)
    {
		$tm->error($lang['no_rights'], $lang['all_error'], 0, 0, 1);
    }

	if (preparse($title, THIS_EMPTY) == 1)
	{
		$tm->error($lang['pole_add_error'], $lang['all_error'], 0, 1, 1);
	}

    /**
     * Загрузка изображения
     * ---------------------- */
	if (isset($_FILES['image']) AND ! empty($_FILES['image']['name']))
	{
		$folder = strtolower(date("Y_M"));
		$ndir = '/up/'.WORKMOD.'/photo/'.$folder;

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

		$tmp_name = $_FILES['image']['tmp_name'];
		if (is_uploaded_file($tmp_name))
		{
			$dirimg = 'up/'.WORKMOD.'/photo/'.$folder.'/';
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
	else
	{
		$tm->error($lang['not_selected'], $lang['all_error'], 0, 1, 1);
	}

	$max = $db->fetchassoc($db->query("SELECT MAX(posit) + 1 AS posit FROM ".$basepref."_".WORKMOD."_photo WHERE firm_id = '".$id."'"));
	$db->query("UPDATE ".$basepref."_".WORKMOD." SET photos = photos + 1 WHERE id = '".$id."'");

	$title = preparse($title, THIS_TRIM, 0, 255);
	$desc = preparse($desc, THIS_TRIM);
	$image = preparse($image, THIS_TRIM, 0, 255);
	$image_thumb = preparse($image_thumb, THIS_TRIM, 0, 255);
	$posit = preparse($max['posit'], THIS_INT);

	$db->query
		(
			"INSERT INTO ".$basepref."_".WORKMOD."_photo VALUES (
			 NULL,
			 '".$id."',
			 '".NEWTIME."',
			 '".$db->escape($api->sitesp($title))."',
			 '".$db->escape($api->sitesp($desc))."',
			 '".$db->escape($image)."',
			 '".$db->escape($image_thumb)."',
			 '".$db->escape($api->sitesp($title))."',
			 '".$posit."',
			 'yes'
			 )"
		);

	redirect($ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=photo&amp;id='.$id));
}

/**
 * Метка photodel
 * ---------------- */
if ($to == 'photodel')
{
	$id = preparse($id, THIS_INT);
	$fid = preparse($fid, THIS_INT);

	$valid = $db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$fid."' AND userid = '".$usermain['userid']."' AND act = 'yes'");

	/**
	 * Доступ
	 */
    if ($db->numrows($valid) == 0 OR $conf['photo_del']== 'no' OR ! $dagroups)
    {
		$tm->error($lang['no_rights'], $lang['all_error'], 0, 0, 1);
    }

	$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".WORKMOD."_photo WHERE id = '".$id."'"));

	if ($ok == 'yes')
	{
		$db->query("DELETE FROM ".$basepref."_".WORKMOD."_photo WHERE id = '".$id."'");

		if (file_exists(DNDIR.$item['image_thumb']))
		{
			unlink(DNDIR.$item['image']);
			unlink(DNDIR.$item['image_thumb']);
		}

		$count = $db->fetchassoc($db->query("SELECT COUNT(id) AS total FROM ".$basepref."_".WORKMOD."_photo WHERE firm_id = '".$fid."'"));
		$db->query("UPDATE ".$basepref."_".WORKMOD." SET photos = '".$count['total']."' WHERE id = '".$fid."'");


		redirect($ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=photo&amp;id='.$fid));
	}
	else
	{
		$tm->header();
		$tm->parseprint(array
			(
				'title'    => $lang['photo_del'],
				'subtitle' => $api->siteuni($item['title']),
				'url_yes'  => $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=photodel&amp;fid='.$fid.'&amp;id='.$id.'&amp;ok=yes'),
				'url_not'  => $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=photo&amp;id='.$fid),
				'confirm'  => $lang['confirm_del'],
				'delet'    => $lang['all_delet'],
				'cancel'   => $lang['cancel']
			),
			$tm->parsein($tm->create('mod/'.WORKMOD.'/photo.del'))
		);
		$tm->footer();
	}
}

/**
 * Метка video
 * ---------------- */
if ($to == 'video')
{
    $ins = array();
	$id = preparse($id, THIS_INT);

    /**
	 * Вложенные шаблоны
	 */
	$tm->manuale = array
		(
			'video' => null,
			'datanot' => null
		);

	$valid = $db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."' AND userid = '".$usermain['userid']."' AND act = 'yes'");
    $item = $db->fetchrow($valid);

	/**
	 * Доступ
	 */
    if ($db->numrows($valid) == 0 OR $conf['video_add']== 'no' OR ! $agroups)
    {
		$tm->error($lang['no_rights'], $lang['all_error'], 0, 0, 1);
    }

	$inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_video WHERE firm_id = '".$id."' AND act = 'yes' ORDER BY posit DESC");

	/**
	 * Вывод на страницу, шапка
	 */
	$tm->header();

	$ins['videos'] = null;
	if ($db->numrows($inq) > 0)
	{
		$tm->unmanule['data'] = 'yes';
		$ins['template'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/video.my'));
		while ($video = $db->fetchrow($inq))
		{
			$ins['videos'].= $tm->parse(array
								(
									'thumb_url' => $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=thumb&amp;type=video&amp;id='.$video['id'].'&amp;x=48&amp;h=36&amp;r=yes'),
									'delet_url' => $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=videodel&amp;fid='.$id.'&amp;id='.$video['id']),
									'video_url' => $ro->seo('index.php?dn='.WORKMOD.'&amp;to=video&amp;id='.$video['id']),
									'title'     => $api->siteuni($video['title']),
									'posit'     => $video['posit'],
									'image'     => $video['image'],
									'edit'      => $lang['all_edit'],
									'del'       => $lang['all_delet'],
									'alt'       => $lang['video_open'],
									'id'        => $video['id'],
									'mod'       => WORKMOD
								),
								$tm->manuale['video']);
		}
	}
	else
	{
		$tm->unmanule['data'] = 'no';
		$ins['template'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/video.my'));
		$ins['videos'].= $tm->parse(array('data_not' => $lang['data_not']), $tm->manuale['datanot']);
	}

	/**
	 * Вывод
	 */
	$tm->parseprint(array
		(
			'post_url'   => $ro->seo('index.php?dn='.WORKMOD),
			'title'      => $api->siteuni($item['title']),
			'video_add'  => $lang['video_add'],
			'video_help' => $lang['video_help'],
			'title_help' => $lang['video_help_title'],
			'photo_help' => $lang['video_help_photo'],
			'descript'   => $lang['descript'],
			'add_save'   => $lang['all_add'],
			'img_help'   => $lang['img_help'],
			'img_large'  => $lang['is_large'],
			'img_format' => $lang['incor_format'],
			'sel_file'   => $lang['select_file'],
			'image'      => $lang['all_image'],
			'album'      => $lang['video_album'],
			'name'       => $lang['all_name'],
			'video'      => $lang['all_video'],
			'posit'      => $lang['position'],
			'manage'     => $lang['sys_manage'],
			'save'       => $lang['all_save'],
			'print'	     => $ins['videos'],
			'fid'	     => $id
		),
		$ins['template']);

	/**
	 * Вывод на страницу, подвал
	 */
	$tm->footer();
}

/**
 * Метка videoup
 * -------------- */
if ($to == 'videoup')
{
	$id = preparse($id, THIS_INT);

	$valid = $db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."' AND userid = '".$usermain['userid']."' AND act = 'yes'");

	/**
	 * Доступ
	 */
    if ($db->numrows($valid) == 0 OR $conf['video_add']== 'no' OR ! $vgroups)
    {
		$tm->error($lang['no_rights'], $lang['all_error'], 0, 0, 1);
    }

	if (is_array($title) AND ! empty($title))
	{
		foreach ($title as $k => $v)
		{
			$k = preparse($k, THIS_INT);
			$v = preparse($v, THIS_TRIM);
			$db->query("UPDATE ".$basepref."_".WORKMOD."_video SET title = '".$db->escape($api->sitesp($v))."' WHERE id = '".$k."'");
		}
	}

	if (is_array($posit) AND ! empty($posit))
	{
		foreach ($posit as $k => $v)
		{
			$k = preparse($k, THIS_INT);
			$v = preparse($v, THIS_INT);
			$db->query("UPDATE ".$basepref."_".WORKMOD."_video SET posit = '".$v."' WHERE id = '".$k."'");
		}
	}

	redirect($ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=video&amp;id='.$id));
}

/**
 * Метка videoadd
 * ----------------- */
if ($to == 'videoadd')
{
	$id = preparse($id, THIS_INT);

	$valid = $db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."' AND userid = '".$usermain['userid']."' AND act = 'yes'");

	/**
	 * Доступ
	 */
    if ($db->numrows($valid) == 0 OR $conf['video_add']== 'no' OR ! $vgroups)
    {
		$tm->error($lang['no_rights'], $lang['all_error'], 0, 0, 1);
    }

	/**
	 * Обязательное поле
	 */
	if (preparse($link, THIS_EMPTY) == 1)
	{
		$tm->error($lang['pole_add_error'], $lang['all_error'], 0, 1, 1);
	}

	$vd = new Video($link);
	$title = (preparse($title, THIS_EMPTY) == 1) ? $vd->title : $title;

	if (preparse($vd->video, THIS_EMPTY) == 1)
	{
		$tm->error($lang['video_error'], $lang['video_add'].': '.$item['title'], 0, 1, 1);
	}

	$inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_video WHERE firm_id = '".$id."' AND title = '".$db->escape($title)."'");
	if ($db->numrows($inq) > 0)
	{
		$tm->error($lang['cpu_error_isset'], $lang['all_error'], 0, 1, 1);
	}

	$folder = strtolower(date("Y_M"));
	$ndir = '/up/'.WORKMOD.'/video/'.$folder;

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
			$tm->error($lang['create_error'], 0);
		}
	}

	/**
	 * Обработка изображения
	 */
	if (isset($_FILES['image']) AND ! empty($_FILES['image']['name']))
	{
		$tmp_name = $_FILES['image']['tmp_name'];
		if (is_uploaded_file($tmp_name))
		{
			$dirimg = 'up/'.WORKMOD.'/video/'.$folder.'/';
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

						if (file_exists(DNDIR.$dirimg.$imgname))
						{
							unlink(DNDIR.$dirimg.$imgname);
						}

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
	else
	{
		$image = $vd->image;

		$img = new Image;
		$img->url_thumb($vd->image, DNDIR.'up/'.WORKMOD.'/video/'.$folder.'/');
		$image_thumb = 'up/'.WORKMOD.'/video/'.$folder.'/'.$img->thumb;
	}

	$max = $db->fetchassoc($db->query("SELECT MAX(posit) + 1 AS posit FROM ".$basepref."_".WORKMOD."_video WHERE firm_id = '".$id."'"));
	$db->query("UPDATE ".$basepref."_".WORKMOD." SET videos = videos + 1 WHERE id = '".$id."'");

	$desc = preparse($desc, THIS_TRIM);
	$video = preparse($vd->video, THIS_TRIM, 0, 255);
	$thumb = preparse($image_thumb, THIS_TRIM, 0, 255);
	$posit = preparse($max['posit'], THIS_INT);

	$db->query
		(
			"INSERT INTO ".$basepref."_".WORKMOD."_video VALUES (
			 NULL,
			 '".$id."',
			 '".NEWTIME."',
			 '".$db->escape($api->sitesp($title))."',
			 '".$db->escape($api->sitesp($desc))."',
			 '".$db->escape($link)."',
			 '".$db->escape($video)."',
			 '".$db->escape($thumb)."',
			 '".$posit."',
			 'yes'
			)"
		);

	redirect($ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=video&amp;id='.$id));
}

/**
 * Метка videodel
 * ---------------- */
if ($to == 'videodel')
{
	$id = preparse($id, THIS_INT);
	$fid = preparse($fid, THIS_INT);

	$valid = $db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$fid."' AND userid = '".$usermain['userid']."' AND act = 'yes'");

	/**
	 * Доступ
	 */
    if ($db->numrows($valid) == 0 OR $conf['video_del']== 'no' OR ! $dagroups)
    {
		$tm->error($lang['no_rights'], $lang['all_error'], 0, 0, 1);
    }

	$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".WORKMOD."_video WHERE id = '".$id."'"));

	if ($ok == 'yes')
	{
		$db->query("DELETE FROM ".$basepref."_".WORKMOD."_video WHERE id = '".$id."'");

		if (file_exists(DNDIR.$item['image']))
		{
			unlink(DNDIR.$item['image']);
		}

		$count = $db->fetchassoc($db->query("SELECT COUNT(id) AS total FROM ".$basepref."_".WORKMOD."_video WHERE firm_id = '".$fid."'"));
		$db->query("UPDATE ".$basepref."_".WORKMOD." SET videos = '".$count['total']."' WHERE id = '".$fid."'");


		redirect($ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=video&amp;id='.$fid));
	}
	else
	{
		$tm->header();
		$tm->parseprint(array
			(
				'title'    => $lang['video_del'],
				'subtitle' => $api->siteuni($item['title']),
				'url_yes'  => $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=videodel&amp;fid='.$fid.'&amp;id='.$id.'&amp;ok=yes'),
				'url_not'  => $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=video&amp;id='.$fid),
				'confirm'  => $lang['confirm_del'],
				'delet'    => $lang['all_delet'],
				'cancel'   => $lang['cancel']
			),
			$tm->parsein($tm->create('mod/'.WORKMOD.'/video.del'))
		);
		$tm->footer();
	}
}

/**
 * Метка del
 * ---------------- */
if ($to == 'del')
{
	$id = preparse($id, THIS_INT);

	/**
	 * Свой TITLE
	 */
	if (isset($config['mod'][WORKMOD]['custom']) AND ! empty($config['mod'][WORKMOD]['custom']))
	{
		define('CUSTOM', $config['mod'][WORKMOD]['custom'].' - '.$lang['firms_my']);
	}

	/**
	 * Мета данные
	 */
	$global['descript'] = ( ! empty($config['mod'][WORKMOD]['descript'])) ? $config['mod'][WORKMOD]['descript'].', '.$lang['firms_my'].' - '.$lang['firms_my'] : '';
	$global['keywords'] = ( ! empty($config['mod'][WORKMOD]['keywords'])) ? $config['mod'][WORKMOD]['keywords'] : '';

	/**
	 * Меню, хлебные крошки
	 */
	$global['insert']['current'] = $global['modname'];
	$global['insert']['breadcrumb'] = array('<a href="'.$ro->seo('index.php?dn='.WORKMOD).'">'.$global['modname'].'</a>', '<a href="'.$ro->seo('index.php?dn='.WORKMOD.'&amp;re=my').'">'.$lang['firms_my'].'</a>', $lang['all_delet']);

	if ($conf['delete'] == 'no' OR ! $dgroups)
	{
		$tm->error($lang['no_rights'], $lang['all_error'], 0, 1, 1);
	}

	$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."'"));

	if ($ok == 'yes')
	{
		if ($conf['multcat'] == 'yes')
		{
			$counts = new Counts(WORKMOD, 'id', 0);
			$counts->del($id);
		}

		if ( ! empty($item['image_thumb']))
		{
			unlink(DNDIR.$item['image']);
			unlink(DNDIR.$item['image_thumb']);
		}

		if ( ! empty($item['files']))
		{
			$fp = Json::decode($item['files']);
			if (is_array($fp) AND sizeof($fp) > 0)
			{
				foreach ($fp as $v)
				{
					if (file_exists(DNDIR.$v['path']))
					{
						unlink(DNDIR.$v['path']);
					}
				}
			}
		}

		$db->query("DELETE FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."'");
		$db->query("DELETE FROM ".$basepref."_reviews WHERE file = '".WORKMOD."' AND pageid = '".$id."'");
		$db->query("DELETE FROM ".$basepref."_rating WHERE file = '".WORKMOD."' AND id = '".$id."'");

		$inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_photo WHERE firm_id = '".$id."'");
		if ($db->numrows($inq) > 0)
		{
			while ($photo = $db->fetchassoc($inq))
			{
				$db->query("DELETE FROM ".$basepref."_".WORKMOD."_photo WHERE id = '".$photo['id']."'");

				if (file_exists(DNDIR.$photo['image_thumb']))
				{
					unlink(DNDIR.$photo['image']);
					unlink(DNDIR.$photo['image_thumb']);
				}
			}
		}

		$inqs = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_video WHERE firm_id = '".$id."'");
		if ($db->numrows($inqs) > 0)
		{
			while ($video = $db->fetchassoc($inqs))
			{
				$db->query("DELETE FROM ".$basepref."_".WORKMOD."_photo WHERE id = '".$video['id']."'");

				if (file_exists(DNDIR.$video['image']))
				{
					unlink(DNDIR.$video['image']);
				}
			}
		}

		if ($conf['multcat'] == 'no')
		{
			$counts = new Counts(WORKMOD, 'id');
		}

		$db->increment(WORKMOD);

		redirect($ro->seo('index.php?dn='.WORKMOD.'&amp;re=my'));
	}
	else
	{
		$tm->header();
		$tm->parseprint(array
			(
				'delet'   => $lang['all_delet'],
				'title'   => $api->siteuni($item['title']),
				'confirm' => $lang['confirm_del'],
				'url_yes' => $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my&amp;to=del&amp;id='.$id.'&amp;ok=yes'),
				'url_not' => $ro->seo('index.php?dn='.WORKMOD.'&amp;re=my'),
				'make'    => $lang['all_go'],
				'cancel'  => $lang['cancel']
			),
			$tm->parsein($tm->create('mod/'.WORKMOD.'/del'))
		);
		$tm->footer();
	}
}

/**
 * Метка getcat
 * ------------ */
if($to == 'getcat')
{
	global $id, $catid, $nocat, $cats;

	header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
	header('Content-Type: text/html; charset='.$config['langcharset'].'');

    $id = preparse($id, THIS_INT);
    $catid = preparse($catid, THIS_INT);

	$sql = ( ! empty($nocat)) ? " WHERE catid NOT IN (".$nocat.")" : "";
	$sql2 = ( ! empty($nocat)) ? " WHERE catid NOT IN (".$nocat.") AND id = '".$id."'" : "";

	$array = array();
	$inq = $db->query("SELECT catid, parentid, catname FROM ".$basepref."_".WORKMOD."_cat".$sql." ORDER BY posit ASC");
	while ($item = $db->fetchassoc($inq))
	{
		$array[$item['parentid']][$item['catid']] = $item;
	}

	$cats = array();
	$inqs = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_cats".$sql2);
	while ($items = $db->fetchassoc($inqs))
	{
		$cats[] = $items['catid'];
	}

	function select($cid = 0, $depth = 0)
	{
		global $array, $api, $cats;

		if ( ! isset($array[$cid]) )
			return false;

		foreach ($array[$cid] as $val)
		{
			$selected = (in_array($val['catid'], $cats)) ? ' selected="selected"' : '';
			$indent = ($depth > 0) ? str_repeat('&nbsp;&nbsp;', $depth) : '';
			echo '<option value="'.$val['catid'].'"'.$selected.'>'.$indent.$api->siteuni($val['catname']).'</option>';
			select($val['catid'], $depth + 1);
		}
		unset($array[$cid]);
	}

	select();
	exit();
}

/**
 * Создание эскиза
 ------------------*/
if ($to == 'thumb')
{
	global $id, $type, $x, $h, $r;

	$id = preparse($id, THIS_INT);
	if ($type == 'video')
	{
		$item = $db->fetchassoc($db->query("SELECT image FROM ".$basepref."_".WORKMOD."_".$type." WHERE id = '".$id."'"));
		$image = $item['image'];
	}
	else
	{
		$item = $db->fetchassoc($db->query("SELECT image_thumb FROM ".$basepref."_".WORKMOD."_".$type." WHERE id = '".$id."'"));
		$image = $item['image_thumb'];
	}

	$img = new Image();
	if (file_exists(DNDIR.$image))
	{
		$img->viewthumb(DNDIR.$image, $x, $h, $r);
	}
	else
	{
		$img->viewthumb(DNDIR.'up/firms/icon/blank.png', $x, $h, $r);
	}

	exit();
}
