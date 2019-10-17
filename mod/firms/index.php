<?php
/**
 * File:        /mod/firms/index.php
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
global $db, $basepref, $config, $lang, $usermain, $tm, $ro, $api,
       $global, $ccpu, $cpu, $to, $p, $id, $ye, $mo, $da, $cpu;

/**
 * Рабочий мод
 */
define('WORKMOD', basename(__DIR__)); $conf = $config[WORKMOD];

/**
 * Файл доп. функций
 */
require_once(DNDIR.'mod/'.WORKMOD.'/mod.function.php');

/**
 * Метки
 */
$legaltodo = array('index', 'cat', 'page', 'phone', 'video');

/**
 * Проверка меток
 */
$to = (isset($to) AND in_array($api->sitedn($to), $legaltodo)) ? $api->sitedn($to) : 'index';

/**
 * Метка index
 * --------------- */
if($to == 'index')
{
	$obj = $area = $ins = $tc = array();

	$ins = array
		(
			'last'     => null,
			'pages'    => null,
			'best'     => null,
			'category' => null,
			'cats'     => null,
			'multcat'  => null
		);

	$posts = FALSE;

	$sp = isset($p) ? 1 : 0;
	$p = preparse($p, THIS_INT);
	$p = ( ! isset($p) OR $p <= 1) ? 1 : $p;
	$s = $conf['lastcol'] * ($p -1);

	if ($conf['main'] == 'all')
	{
		$total = $db->fetchassoc
				(
					$db->query
					(
						"SELECT COUNT(id) AS total FROM ".$basepref."_".WORKMOD." WHERE act = 'yes'
						 AND (stpublic = 0 OR stpublic < '".NEWTIME."')
						 AND (unpublic = 0 OR unpublic > '".NEWTIME."')"
					)
				);
	}
	else
	{
		$total = $db->fetchassoc
				(
					$db->query
					(
						"SELECT COUNT(c.id) as total FROM (
							SELECT id FROM ".$basepref."_".WORKMOD." WHERE act = 'yes'
							AND (stpublic = 0 OR stpublic < '".NEWTIME."')
							AND (unpublic = 0 OR unpublic > '".NEWTIME."') LIMIT ".$conf['lastcol']."
							) c"
					)
				);
	}

	/**
	 * Ошибка листинга
	 */
	$nums = ceil($total['total'] / $conf['lastcol']);
	if ($p > $nums AND $p != 1)
	{
		$tm->noexistprint();
	}

	/**
	 * Номер страницы, SEO
	 */
	$seopage = isset($p) ? ', '.mb_strtolower($lang['page_one']).'-'.$p : '';

	$p = preparse($p, THIS_INT);
	$p = ( ! isset($p) OR $p <= 1) ? 1 : $p;
	$s = $conf['lastcol'] * ($p - 1);

	/**
	 * Свой TITLE
	 */
	if (isset($config['mod'][WORKMOD]['custom']) AND ! empty($config['mod'][WORKMOD]['custom']))
	{
		define('CUSTOM', $config['mod'][WORKMOD]['custom'].$seopage);
	} else {
		$global['title'] = $global['modname'].$seopage;
	}

	/**
	 * Мета данные
	 */
	$global['keywords'] = (preparse($config['mod'][WORKMOD]['keywords'], THIS_EMPTY) == 0) ? $api->siteuni($config['mod'][WORKMOD]['keywords']) : '';
	$global['descript'] = (preparse($config['mod'][WORKMOD]['descript'], THIS_EMPTY) == 0) ? $api->siteuni($config['mod'][WORKMOD]['descript'].$seopage) : '';

	/**
	 * Мета данные Open Graph
	 */
	$global['og_title'] = (defined('CUSTOM')) ? CUSTOM : $global['modname'];
	if ( ! empty($config['mod'][WORKMOD]['map'])) {
		$global['og_desc'] = $api->siteuni($config['mod'][WORKMOD]['map']);
	} elseif ( ! empty($config['mod'][WORKMOD]['descript'])) {
		$global['og_desc'] = $api->siteuni($config['mod'][WORKMOD]['descript']);
	}

	/**
	 * Меню, хлебные крошки
	 */
	$global['insert']['current'] = $global['insert']['breadcrumb'] = $global['modname'];

	/**
	 * Вывод на страницу, шапка
	 */
	$tm->header();

	/**
	 * Листинг, формирование постраничной разбивки
	 */
	$ins['pages'] = null;
	if ($total['total'] > $conf['lastcol'])
	{
		$ins['pages'] = $tm->parse(array
							(
								'text' => $lang['all_pages'],
								'pages' => $api->pages('', '', 'index', WORKMOD.'&amp;to=index', $conf['lastcol'], $p, $total['total'])
							),
							$tm->manuale['pagesout']);
	}

	/**
	 * Рубрикатор с литерами
	 */
	if ($conf['letter'] == 'yes')
	{
		foreach ($api->letters(1) as $k => $v)
		{
			if ($k <= 27) {
				$latin[] = '<a href="'.$ro->seo('index.php?dn='.WORKMOD.'&amp;re=letter&amp;sym='.$v[1]).'" title="'.$lang['all_letter'].' - '.$v[0].'">'.$v[0].'</a>';
			} else {
				$other[] = '<a href="'.$ro->seo('index.php?dn='.WORKMOD.'&amp;re=letter&amp;sym='.$v[1]).'" title="'.$lang['all_letter'].' - '.$v[0].'">'.$v[0].'</a>';
			}
		}

		$tm->parseprint(array
			(
				'latin' => implode('', $latin),
				'other' => ($config['langcode'] != 'en') ? implode('', $other) : ''
			),
			$tm->create('mod/'.WORKMOD.'/letter'));
	}

	/**
	 * Категории
	 */
	$inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_cat ORDER BY posit ASC", $config['cachetime'], WORKMOD);
	$count_cat = $db->numrows($inq, $config['cache']);
	while ($c = $db->fetchassoc($inq, $config['cache']))
	{
		$area[$c['parentid']][$c['catid']] = $obj[$c['catid']] = $c;
	}

	if ($conf['catmain'] == 'yes')
	{
		if ( ! empty($area))
		{
			$api->subcatcache = $area;
			$ins['tempcat'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/cat'));
			$api->printsitecat(0);
			if ( ! empty($api->print))
			{
				$stat = $db->fetchassoc
							(
								$db->query
								(
									"SELECT COUNT(id) AS total, SUM(hits) AS hits FROM ".$basepref."_".WORKMOD." WHERE act = 'yes'
									 AND (stpublic = 0 OR stpublic < '".NEWTIME."')
									 AND (unpublic = 0 OR unpublic > '".NEWTIME."')"
								)
							);

				$catprint = $tm->tableprint($api->print, $conf['catcol']);

				$ins['category'] = $tm->parse(array
					(
						'cd'         => $lang['cat_desc'],
						'lang_icon'	 => $lang['all_icon'],
						'lang_col'   => $lang['all_col'],
						'lang_total' => $lang['public_count'],
						'lang_cat'   => $lang['all_cats'],
						'lang_hits'  => $lang['all_hits'],
						'catprint'   => $catprint,
						'total'      => $stat['total'],
						'hits'       => ( ! empty($stat['hits'])) ? $stat['hits'] : 0,
						'cats'       => $count_cat
					),
					$ins['tempcat']);
			}
		}
	}

	$tm->unmanule['desc'] = (preparse($config['mod'][WORKMOD]['map'], THIS_EMPTY) == 0) ? 'yes' : 'no';

    /**
	 * Переключатели
	 */
	$tm->unmanule['date'] = $conf['date'];
	$tm->unmanule['rating'] = $conf['rating'];
	$tm->unmanule['review'] = $conf['resact'];
	$tm->unmanule['link'] = 'yes';

    /**
	 * Вложенные шаблоны
	 */
	$tm->manuale = array
		(
			'cat' => null,
			'cats' => null,
			'icon' => null,
			'thumb' => null,
			'author' => null,
			'locations' => null
		);

	/**
	 * Описание раздела
	 */
	$ins['map'] = (preparse($config['mod'][WORKMOD]['map'], THIS_EMPTY) == 0) ? $config['mod'][WORKMOD]['map'] : '';

	/**
	 * Шаблоны
	 */
	$ins['standart'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/standart'));
	$ins['section'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/index.section'));

	/**
	 * Связи категорий
	 */
	if ($conf['multcat'] == 'yes')
	{
		$mult = array();
		$cat_inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_cats", $config['cachetime'], WORKMOD);
		while ($cs = $db->fetchassoc($cat_inq, $config['cache']))
		{
			$mult[$cs['id']][$cs['catid']] = $cs['catid'];
		}
	}

	/**
	 * Все организации
	 */
	$inq = $db->query
			(
				"SELECT * FROM ".$basepref."_".WORKMOD." WHERE act = 'yes' AND pid = '0'
				 AND (stpublic = 0 OR stpublic < '".NEWTIME."')
				 AND (unpublic = 0 OR unpublic > '".NEWTIME."')
				 ORDER BY posit DESC LIMIT ".$s.", ".$conf['lastcol']
			);

	if($db->numrows($inq) > 0)
	{
		$posts = true;
		$ins['content'] = array();
		while ($item = $db->fetchassoc($inq))
		{
			$ins['cat'] = $ins['icon'] = $ins['image'] = $ins['author'] = $ins['location'] = $multcat = $location = null;

			// CPU
			$ins['cpu'] = (defined('SEOURL') AND $item['cpu']) ? '&amp;cpu='.$item['cpu'] : '';
			$ins['catcpu'] = (defined('SEOURL') AND ! empty($obj[$item['catid']]['catcpu'])) ? '&amp;ccpu='.$obj[$item['catid']]['catcpu'] : '';

			// URL
			$ins['url'] = $ro->seo('index.php?dn='.WORKMOD.$ins['catcpu'].'&amp;to=page&amp;id='.$item['id'].$ins['cpu']);
			$ins['caturl'] = $ro->seo('index.php?dn='.WORKMOD.'&amp;to=cat&amp;id='.$item['catid'].$ins['catcpu']);

			// Категория
			if ($conf['linkcat'] == 'yes' AND isset($obj[$item['catid']]['catname']{0}))
			{

				$ins['cat'] = $tm->parse(array(
										'caturl'  => $ins['caturl'],
										'catname' => $api->siteuni($obj[$item['catid']]['catname'])
									),
									$tm->manuale['cat']);
			}

			// Иконка категории
			if ($conf['linkicon'] == 'yes' AND ! empty($obj[$item['catid']]['icon']))
			{
				$ins['icon'] = $tm->parse(array(
										'icon'  => $obj[$item['catid']]['icon'],
										'alt'   => $api->siteuni($obj[$item['catid']]['catname'])
									),
									$tm->manuale['icon']);
			}

			// Доп. категории
			if (isset($obj[$item['catid']]) AND $conf['multcat'] == 'yes')
			{
				$ins['temp_cats'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/standart.cats'));
				if ($conf['linkcat'] == 'yes')
				{
					unset($mult[$item['id']][$item['catid']]);
				}

				if ( ! empty($mult[$item['id']]))
				{
					foreach ($mult[$item['id']] as $catid)
					{
						$cat_cpu = (defined('SEOURL')) ? '&amp;ccpu='.$area[0][$catid]['catcpu'] : '';
						$cat_url = $ro->seo('index.php?dn='.WORKMOD.'&amp;to=cat&amp;id='.$catid.$cat_cpu);

						$multcat .= $tm->parse(array(
								'cat_url'  => $cat_url,
								'cat_name' => $area[0][$catid]['catname']
							),
							$tm->manuale['cats']);
					}

					$ins['cats'] = $tm->parse(array
								(
									'cats' => chop(trim($multcat), ','),
									'langcats' => $lang['all_cat']
								),
								$ins['temp_cats']);
				}
			}

			// Регион
			if (in_array( ! NULL, array($item['country'], $item['region'], $item['city'])))
			{
				$ins['temp_location'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/standart.location'));

				$location .= !empty($item['country']) ? $tm->parse(array('names' => $api->siteuni($item['country'])), $tm->manuale['locations']) : '';
				$location .= !empty($item['region']) ? $tm->parse(array('names' => $api->siteuni($item['region'])), $tm->manuale['locations']) : '';
				$location .= !empty($item['city']) ? $tm->parse(array('names' => $api->siteuni($item['city'])), $tm->manuale['locations']) : '';

				$ins['location'] = $tm->parse(array
					(
						'langs'  => $lang['state'],
						'region' => chop(trim($location), ',')
					),
					$ins['temp_location']);
			}

			// Изображение
			if ( ! empty($item['image_thumb']))
			{
				$ins['float'] = ($item['image_align'] == 'left') ? 'imgleft' : 'imgright';
				$ins['alt']   = ( ! empty($item['image_alt'])) ? $api->siteuni($item['image_alt']) : '';

				$ins['image'] = $tm->parse(array(
										'float' => $ins['float'],
										'thumb' => $item['image_thumb'],
										'alt'   => $ins['alt']
									),
									$tm->manuale['thumb']);
			}

			// Автор
			if ( ! empty($item['author']) AND $conf['author'] == 'yes')
			{
				$author = preg_replace('/[^\pL\pNd\pZs\pP\pM]/us', '', $item['author']);
				if (isset($config['mod']['user']))
				{
					$udata = $userapi->userdata('uname', $author);
					if ( ! empty($udata))
					{
						$author = '<a href="'.$ro->seo($userapi->data['linkprofile'].$udata['userid']).'">'.$udata['uname'].'</a>';
					}
				}
				$ins['author'] = $tm->parse(array(
										'author' => $author,
										'lang_author' => $lang['author']
									),
									$tm->manuale['author']);
			}

			// Кол. отзывов
			$ins['review'] = ($conf['resact'] == 'yes') ? $item['reviews'] : '';

			// Рейтинг
			$ins['rate'] = ($item['rating'] == 0) ? 0 : round($item['totalrating'] / $item['rating']);
			$ins['title_rate'] = ($ins['rate'] == 0) ? $lang['rate_0'] : $lang['rate_'.$ins['rate'].''];
			$ins['rating'] = ($conf['rating'] == 'yes') ?  rating($ins['rate'], 0, 1) : '';

			// Дата
			$ins['public'] = ($item['stpublic'] > 0) ? $item['stpublic'] : $item['public'];

			$ins['content'][] = $tm->parse(array
				(
					'mod'          => WORKMOD,
					'id'           => $item['id'],
					'title'        => $api->siteuni($item['title']),
					'text'         => $api->siteuni($item['textshort']),
					'phone'        => $item['phone'],
					'hits'         => $item['hits'],
					// ins
					'icon'         => $ins['icon'],
					'cat'          => $ins['cat'],
					'date'         => $ins['public'],
					'cats'         => $ins['cats'],
					'location'     => $ins['location'],
					'image'        => $ins['image'],
					'author'	   => $ins['author'],
					'rating'       => $ins['rating'],
					'title_rate'   => $ins['title_rate'],
					'review'       => $ins['review'],
					'url'          => $ins['url'],
					// lang
					'lang_phone'   => $lang['phone'],
					'public'       => $lang['all_data'],
					'lang_show'    => $lang['all_show'],
					'lang_contact' => $lang['in_contact'],
					'langhits'     => $lang['all_hits'],
					'lang_review'  => $lang['response_total'],
					'lang_rate'    => $lang['all_rating'],
					'read'         => $lang['in_detail']
				),
				$ins['standart']);
		}

		$ins['output'] = $tm->tableprint($ins['content'], $conf['indcol']);
		$ins['last'] = $tm->parse(array
			(
				'title'   => $lang['firms_all'],
				'content' => $ins['output'],
				'type'    => 'new'
			),
			$ins['section']);
    }

	$tm->unmanule['posts'] = ($posts) ? 'no' : 'yes';

	/**
	 * Вывод
	 */
	$tm->parseprint(array
		(
			'category'	=> $ins['category'],
			'descript'	=> $ins['map'],
			'last'		=> $ins['last'],
			'pages'		=> $ins['pages'],
			'noposts'	=> $lang['no_posts'],
			'search'	=> $tm->search($conf['search'], WORKMOD, 1)
		),
		$tm->parsein($tm->create('mod/'.WORKMOD.'/index'))
	);

	/**
	 * Вывод на страницу, подвал
	 */
	$tm->footer();
}

/**
 * Метка cat
 * ------------ */
if($to == 'cat')
{
	$obj = $ins = $menu = $area = $tc = array();

	$id = preparse($id, THIS_INT);

	$sp = isset($p) ? 1 : 0;
	$p = preparse($p, THIS_INT);
	$p = ( ! isset($p) OR $p <= 1) ? 1 : $p;
	$s = $conf['pagcol'] * ($p - 1);

	/**
	 * Категории
	 */
	$inq = $db->query
				(
					"SELECT * FROM ".$basepref."_".WORKMOD."_cat
					 ORDER BY posit ASC", $config['cachetime'], WORKMOD
				);

	$count_cat = $db->numrows($inq, $config['cache']);

	while ($c = $db->fetchassoc($inq, $config['cache']))
	{
		$cid = $c['catid'];
		$area[$c['parentid']][$cid] = $menu[$cid] = $obj['id'][$cid] = $obj['ccpu'][$c['catcpu']] = $c;
	}

	if ( ! empty($ccpu) AND preparse($ccpu, THIS_SYMNUM, TRUE) == 0 AND defined('SEOURL'))
	{
		$ccpu = preparse($ccpu, THIS_TRIM, 0, 255);
		$ins['catcpu'] = '&amp;ccpu='.$ccpu;
		$ins['valid'] = (isset($obj['ccpu'][$ccpu]) ? 1 : 0);
		$obj = ($ins['valid'] == 1) ? $obj['ccpu'][$ccpu] : 'empty';
		$v = 0;
	}
	else
	{
		$ins['catcpu'] = '';
		$ins['valid'] = (isset($obj['id'][$id]) ? 1 : 0);
		$obj = ($ins['valid'] == 1) ? $obj['id'][$id] : 'empty';
		$v = 1;
	}

    /**
     * Страница не существует
     */
	if ($ins['valid'] == 0 OR $obj == 'empty')
	{
		$tm->noexistprint();
	}
	elseif ( ! isset($ccpu) AND $config['cpu'] == 'yes' AND $v)
	{
		$tm->noexistprint();
	}

	$in = $api->findsubcat($area, $obj['catid']);
	$whe = (is_array($in) AND sizeof($in) > 0) ? ','.implode(',', $in) : '';
	if ($conf['multcat'] == 'yes')
	{
		$total = $db->query
					(
						"SELECT DISTINCT a.* FROM ".$basepref."_".WORKMOD."_cat AS c
						 INNER JOIN ".$basepref."_".WORKMOD."_cats AS b ON (b.catid = c.catid)
						 INNER JOIN ".$basepref."_".WORKMOD." AS a ON (a.id = b.id)
						 WHERE c.catid IN (".$obj['catid'].$whe.") AND a.act = 'yes' AND a.pid = '0'
						 AND (stpublic = 0 OR stpublic < '".NEWTIME."')
						 AND (unpublic = 0 OR unpublic > '".NEWTIME."')"
					);

		$count = $db->numrows($total);
	}
	else
	{
		$total = $db->fetchassoc
					(
						$db->query
						(
							"SELECT COUNT(id) AS total FROM ".$basepref."_".WORKMOD."
							 WHERE catid IN (".$obj['catid'].$whe.") AND act = 'yes' AND pid = '0'
							 AND (stpublic = 0 OR stpublic < '".NEWTIME."')
							 AND (unpublic = 0 OR unpublic > '".NEWTIME."')"
						)
					);

		$count = $total['total'];
	}

	/**
	 * Ошибка листинга
	 */
	$nums = ceil($count / $conf['pagcol']);
	if ($p > $nums AND $p != 1)
	{
		$tm->noexistprint();
	}

	/**
	 * Сортировки
	 */
	$ins['order'] = array('asc', 'desc');
	$ins['sort'] = array('public', 'id', 'title', 'hits', 'posit');
	$order = ($obj['ord'] AND in_array($obj['ord'], $ins['order'])) ? $obj['ord'] : 'asc';
	$sort = ($obj['sort'] AND in_array($obj['sort'], $ins['sort'])) ? $obj['sort'] : 'id';

	/**
	 * Номер страницы, SEO
	 */
	$inpage = str_replace(array('{p}', '{t}'), array($p, $nums), $lang['pagenation']);
	$seopage = ($sp) ? ', '.mb_strtolower($inpage) : '';

	/**
	 * Свой TITLE
	 */
	if (isset($obj['catcustom']) AND ! empty($obj['catcustom'])) {
		define('CUSTOM', $api->siteuni($obj['catcustom']).$seopage);
	} else {
		$global['title'] = $api->siteuni($obj['catname']).$seopage;
	}

	/**
	 * Мета данные
	 */
	$global['keywords'] = (preparse($obj['keywords'], THIS_EMPTY) == 0) ? $api->siteuni($obj['keywords']) : '';
	$global['descript'] = (preparse($obj['descript'], THIS_EMPTY) == 0) ? $api->siteuni($obj['descript']).$seopage : '';

	/**
	 * Меню, хлебные крошки
	 */
	$api->catcache = $menu;
	$global['insert']['current'] = $api->siteuni($obj['catname']);
	$global['insert']['breadcrumb'] = $api->sitecat($obj['catid']);

	/**
	 * Ограничение доступа
	 */
	if ($obj['access'] == 'user')
	{
		if ( ! defined('USER_LOGGED'))
		{
			$tm->noaccessprint();
		}
		if (defined('GROUP_ACT') AND ! empty($obj['groups']))
		{
			$group = Json::decode($obj['groups']);
			if ( ! isset($group[$usermain['gid']]))
			{
				$tm->norightprint();
			}
		}
	}

	/**
	 * Вывод на страницу, шапка
	 */
	$tm->header();

	/**
	 * Рубрикатор с литерами
	 */
	if ($conf['letter'] == 'yes')
	{
		$letters = $api->letters(1);

		foreach ($letters as $k => $v)
		{
			if ($k <= 27) {
				$latin[] = '<a href="'.$ro->seo('index.php?dn='.WORKMOD.'&amp;re=letter&amp;sym='.$v[1]).'" title="'.$lang['all_letter'].' - '.$v[0].'">'.$v[0].'</a>';
			} else {
				$other[] = '<a href="'.$ro->seo('index.php?dn='.WORKMOD.'&amp;re=letter&amp;sym='.$v[1]).'" title="'.$lang['all_letter'].' - '.$v[0].'">'.$v[0].'</a>';
			}
		}
		$tm->parseprint(array
			(
				'latin' => implode('', $latin),
				'other' => ($config['langcode'] != 'en') ? implode('', $other) : ''
			),
			$tm->create('mod/'.WORKMOD.'/letter'));
	}

	/**
	 * Категории
	 */
	$ins['category'] = null;
	if ( ! empty($area))
	{
		$api->subcatcache = $area;
		$caticon = ($conf['iconcat'] == 'yes') ? 1 : 0;
		$ins['template'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/cat'));
		$api->printsitecat($obj['catid'], 0, $caticon);

		if ( ! empty($api->print))
		{
			$stat = $db->fetchassoc
						(
							$db->query
							(
								"SELECT COUNT(id) AS total, SUM(hits) AS hits FROM ".$basepref."_".WORKMOD." WHERE act = 'yes'
								 AND (stpublic = 0 OR stpublic < '".NEWTIME."')
								 AND (unpublic = 0 OR unpublic > '".NEWTIME."')"
							)
						);

			$catprint = $tm->tableprint($api->print, $conf['catcol']);

			$tm->parseprint(array
				(
					'cd'         => $lang['cat_desc'],
					'lang_icon'  => $lang['all_icon'],
					'lang_col'   => $lang['all_col'],
					'lang_total' => $lang['public_count'],
					'lang_cat'   => $lang['all_cats'],
					'lang_hits'  => $lang['all_hits'],
					'catprint'   => $catprint,
					'total'      => $stat['total'],
					'hits'       => $stat['hits'],
					'cats'       => $count_cat
				),
				$ins['template']);
		}
		else
		{
			$stat = $db->fetchassoc
						(
							$db->query
							(
								"SELECT COUNT(id) AS total
								 FROM ".$basepref."_".WORKMOD." WHERE act = 'yes'
								 AND catid = '".$obj['catid']."'
								 AND (stpublic = 0 OR stpublic < '".NEWTIME."')
								 AND (unpublic = 0 OR unpublic > '".NEWTIME."')"
							)
						);
		}
	}

	/**
	 * Данные
	 */
	if ($conf['multcat'] == 'yes')
	{
		$inq = $db->query
			(
				"SELECT DISTINCT a.* FROM ".$basepref."_".WORKMOD."_cat AS c
				 INNER JOIN ".$basepref."_".WORKMOD."_cats AS b ON (b.catid = c.catid)
				 INNER JOIN ".$basepref."_".WORKMOD." AS a ON (a.id = b.id)
				 WHERE c.catid IN (".$obj['catid'].$whe.") AND a.act = 'yes' AND a.pid = '0'
				 AND (a.stpublic = 0 OR a.stpublic < '".NEWTIME."')
				 AND (a.unpublic = 0 OR a.unpublic > '".NEWTIME."')
				 ORDER BY a.catid = '".$obj['catid']."' ".$order.", ".$sort." ".$order." LIMIT ".$s.", ".$conf['pagcol']
			);
	}
	else
	{
		$inq = $db->query
			(
				"SELECT * FROM ".$basepref."_".WORKMOD."
				 WHERE catid IN (".$obj['catid'].$whe.") AND act = 'yes'
				 AND (stpublic = 0 OR stpublic < '".NEWTIME."')
				 AND (unpublic = 0 OR unpublic > '".NEWTIME."')
				 ORDER BY ".$sort." ".$order." LIMIT ".$s.", ".$conf['pagcol']
			);
	}

	/**
	 * Связи категорий
	 */
	if ($conf['multcat'] == 'yes')
	{
		$mult = array();
		$cat_inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_cats", $config['cachetime'], WORKMOD);
		while ($cs = $db->fetchassoc($cat_inq, $config['cache']))
		{
			$mult[$cs['id']][$cs['catid']] = $cs['catid'];
		}
	}

	if ($db->numrows($inq) > 0)
	{
		// Листинг, функция
		$ins['pages'] = null;
		if ($count > $conf['pagcol'])
		{
			$ins['pagesview'] = $api->pages('', 'id', 'index', WORKMOD.'&amp;to=cat&amp;id='.$obj['catid'].$ins['catcpu'], $conf['pagcol'], $p, $count);
			$ins['pages'] = $tm->parse(array
									(
										'text' => $lang['all_pages'],
										'pages' => $ins['pagesview']
									),
									$tm->manuale['pagesout']);
		}

		$tm->unmanule['date'] = 'yes';
		$tm->unmanule['tags'] = $conf['tags'];
		$tm->unmanule['rating'] = $conf['rating'];
		$tm->unmanule['review'] = $conf['resact'];
		$tm->unmanule['link'] = $tm->unmanule['info'] = 'yes';
		$tm->unmanule['desc'] = (preparse($menu[$obj['catid']]['catdesc'], THIS_EMPTY) == 0) ? 'yes' : 'no';
		$tm->unmanule['subtitle'] = (preparse($menu[$obj['catid']]['subtitle'], THIS_EMPTY) == 0) ? 'yes' : 'no';

		/**
		 * Вложенные шаблоны
		 */
		$tm->manuale = array
			(
				'cat' => null,
				'cats' => null,
				'icon' => null,
				'thumb' => null,
				'author' => null,
				'locations' => null
			);

		// Шаблон
		$ins['template'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/standart'));

		$ins['content'] = array();
		while ($item = $db->fetchassoc($inq))
		{
			$ins['cat'] = $ins['icon'] = $ins['image'] = $ins['author'] = $ins['cats'] = $ins['location'] = $multcat = $location = null;

			// CPU
			$ins['cpu']    = (defined('SEOURL') AND $item['cpu']) ? '&amp;cpu='.$item['cpu'] : '';
			$ins['catcpu'] = (defined('SEOURL') AND ! empty($menu[$item['catid']]['catcpu'])) ? '&amp;ccpu='.$menu[$item['catid']]['catcpu'] : '';

			// URL
			$ins['url'] = $ro->seo('index.php?dn='.WORKMOD.$ins['catcpu'].'&amp;to=page&amp;id='.$item['id'].$ins['cpu']);
			$ins['caturl'] = $ro->seo('index.php?dn='.WORKMOD.'&amp;to=cat&amp;id='.$item['catid'].$ins['catcpu']);

			// Доп. категории
			if (isset($menu[$item['catid']]) AND $conf['multcat'] == 'yes')
			{
				$ins['temp_cats'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/standart.cats'));
				if ($item['catid'] == $obj['catid'] OR isset($mult[$item['id']][$obj['catid']]))
				{
					unset($mult[$item['id']][$obj['catid']]);
				}

				if ( ! empty($mult[$item['id']]))
				{
					foreach ($mult[$item['id']] as $catid)
					{
						$cat_cpu = (defined('SEOURL')) ? '&amp;ccpu='.$menu[$catid]['catcpu'] : '';
						$cat_url = $ro->seo('index.php?dn='.WORKMOD.'&amp;to=cat&amp;id='.$catid.$cat_cpu);

						$multcat .= $tm->parse(array(
								'cat_url'  => $cat_url,
								'cat_name' => $menu[$catid]['catname']
							),
							$tm->manuale['cats']);
					}

					$ins['cats'] = $tm->parse(array
								(
									'cats' => chop(trim($multcat), ','),
									'langcats' => $lang['all_cat']
								),
								$ins['temp_cats']);
				}
			}

			// Регион
			if (in_array( ! NULL, array($item['country'], $item['region'], $item['city'])))
			{
				$ins['temp_location'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/standart.location'));

				$location .= !empty($item['country']) ? $tm->parse(array('names' => $api->siteuni($item['country'])), $tm->manuale['locations']) : '';
				$location .= !empty($item['region']) ? $tm->parse(array('names' => $api->siteuni($item['region'])), $tm->manuale['locations']) : '';
				$location .= !empty($item['city']) ? $tm->parse(array('names' => $api->siteuni($item['city'])), $tm->manuale['locations']) : '';

				$ins['location'] = $tm->parse(array
					(
						'langs'  => $lang['state'],
						'region' => chop(trim($location), ',')
					),
					$ins['temp_location']);
			}

			// Изображение
			if ( ! empty($item['image_thumb']))
			{
				$ins['float'] = ($item['image_align'] == 'left') ? 'imgleft' : 'imgright';
				$ins['alt']   = ( ! empty($item['image_alt'])) ? $api->siteuni($item['image_alt']) : '';

				$ins['image'] = $tm->parse(array(
										'float' => $ins['float'],
										'thumb' => $item['image_thumb'],
										'alt'   => $ins['alt']
									),
									$tm->manuale['thumb']);
			}

			// Категория
			if (isset($menu[$item['catid']]['catname']))
			{
				if ($conf['linkicon'] == 'yes' AND  ! empty($menu[$item['catid']]['icon']))
				{
					$ins['icon'] = $tm->parse(array(
											'icon'  => $menu[$item['catid']]['icon'],
											'alt'   => $api->siteuni($menu[$item['catid']]['catname'])
										),
										$tm->manuale['icon']);
				}

				if ($conf['linkcat'] == 'yes' AND $item['catid'] != $obj['catid'])
				{
					$ins['cat'] = $tm->parse(array(
											'caturl'  => $ins['caturl'],
											'catname' => $api->siteuni($menu[$item['catid']]['catname'])
										),
										$tm->manuale['cat']);
				}
			}

			// Автор
			if ( ! empty($item['author']) AND $conf['author'] == 'yes')
			{
				$author = preg_replace('/[^\pL\pNd\pZs\pP\pM]/us', '', $item['author']);
				if (isset($config['mod']['user']))
				{
					$udata = $userapi->userdata('uname', $author);
					if ( ! empty($udata))
					{
						$author = '<a href="'.$ro->seo($userapi->data['linkprofile'].$udata['userid']).'">'.$udata['uname'].'</a>';
					}
				}
				$ins['author'] = $tm->parse(array(
										'author' => $author,
										'lang_author' => $lang['author']
									),
									$tm->manuale['author']);
			}

			// Кол. отзывов
			$ins['review'] = ($conf['resact'] == 'yes') ? $item['reviews'] : '';

			// Рейтинг
			$ins['rate'] = ($item['rating'] == 0) ? 0 : round($item['totalrating'] / $item['rating']);
			$ins['title_rate'] = ($ins['rate'] == 0) ? $lang['rate_0'] : $lang['rate_'.$ins['rate'].''];
			$ins['rating'] = ($conf['rating'] == 'yes') ?  rating($ins['rate'], 0, 1) : '';

			// Дата
			$ins['public'] = ($item['stpublic'] > 0) ? $item['stpublic'] : $item['public'];

			$ins['content'][] = $tm->parse(array
				(
					'mod'          => WORKMOD,
					'id'           => $item['id'],
					'title'        => $api->siteuni($item['title']),
					'text'         => $api->siteuni($item['textshort']),
					'phone'        => $item['phone'],
					'hits'         => $item['hits'],
					// ins
					'icon'         => $ins['icon'],
					'cat'          => $ins['cat'],
					'date'         => $ins['public'],
					'cats'         => $ins['cats'],
					'location'     => $ins['location'],
					'image'        => $ins['image'],
					'author'	   => $ins['author'],
					'rating'       => $ins['rating'],
					'title_rate'   => $ins['title_rate'],
					'review'       => $ins['review'],
					'url'          => $ins['url'],
					// lang
					'lang_phone'   => $lang['phone'],
					'public'       => $lang['all_data'],
					'lang_show'    => $lang['all_show'],
					'lang_contact' => $lang['in_contact'],
					'langhits'     => $lang['all_hits'],
					'lang_review'  => $lang['response_total'],
					'lang_rate'    => $lang['all_rating'],
					'read'         => $lang['in_detail']
				),
				$ins['template']);
        }

		// Разбивка
		$ins['output'] = $tm->tableprint($ins['content'], $conf['indcol']);

		// Описание категории
		$ins['catdesc'] =  (preparse($menu[$obj['catid']]['catdesc'], THIS_EMPTY) == 0) ? $menu[$obj['catid']]['catdesc'] : '';

		/**
		 * Вывод
		 */
		$tm->parseprint(array
			(
				'category'	=> $ins['category'],
				'catdesc'	=> $ins['catdesc'],
				'title'		=> $api->siteuni($obj['catname']),
				'subtitle'	=> $api->siteuni($obj['subtitle']),
				'content'	=> $ins['output'],
				'pages'		=> $ins['pages'],
				'search'	=> $tm->search($conf['search'], WORKMOD, 1)
			),
			$tm->parsein($tm->create('mod/'.WORKMOD.'/cat.index'))
		);
	}
	else
	{
		$tm->message($lang['data_not'], 0, 1, 1);
	}

	/**
	 * Вывод на страницу, подвал
	 */
	$tm->footer();
}

/**
 * Метка page
 * -------------------- */
if($to == 'page')
{
    $id = preparse($id, THIS_INT);
    $obj = $ins = $area = array();

	/**
	 * Переменные
	 */
	$ins = array('cats','rec','icon','tags','file','image','maps','files','video','photo','author','search','social','tagword','rating','valrate','formrate','langtags','notice','srows','msrows','reviews','reform','ajaxbox','mysocial','filenotice');
	$ins = array_fill_keys($ins, null);

	/**
	 * Запрос с учетом чпу
	 */
	if ( ! empty($cpu) AND preparse($cpu, THIS_SYMNUM, TRUE) == 0 AND defined('SEOURL'))
	{
		$v = 0;
		$cpu = preparse($cpu, THIS_TRIM, 0, 255);
		$valid = $db->query
					(
						"SELECT * FROM ".$basepref."_".WORKMOD." WHERE cpu = '".$db->escape($cpu)."'
						 AND (stpublic = 0 OR stpublic < '".NEWTIME."')
						 AND (unpublic = 0 OR unpublic > '".NEWTIME."')
						 AND act = 'yes' AND pid = '0'"
					);
	}
	else
	{
		$v = 1;
		$valid = $db->query
					(
						"SELECT * FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."'
						 AND (stpublic = 0 OR stpublic < '".NEWTIME."')
						 AND (unpublic = 0 OR unpublic > '".NEWTIME."')
						 AND act = 'yes' AND pid = '0'"
					);
	}

	/**
	 * Массив данных страницы
	 */
	$item = $db->fetchassoc($valid);

	/**
	 * Массив категорий
	 */
	$inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_cat ORDER BY posit ASC", $config['cachetime'], WORKMOD);
	while ($c = $db->fetchassoc($inq, $config['cache']))
	{
		$area[$c['catid']] = $c;
	}

	$ins['catcpu'] = (defined('SEOURL') AND $item['catid'] > 0) ? $area[$item['catid']]['catcpu'] : '';

	/**
	 * Страницы не существует
	 */
	if ($db->numrows($valid) == 0)
	{
		$tm->noexistprint();
	}
	elseif ( ! empty($item['cpu']) AND $config['cpu'] == 'yes' AND $v)
	{
		$tm->noexistprint();
	}
	elseif ( ! isset($ccpu) AND $ins['catcpu'] OR isset($ccpu) AND $ins['catcpu'] != $ccpu)
	{
		$tm->noexistprint();
	}

	/**
	 * Ошибка листинга отзывов
	 */
	$p = preparse($p, THIS_INT);
	if ($conf['resact'] == 'yes')
	{
		$p = ($p <= 1) ? 1 : $p;
		$nums = ceil($item['reviews'] / $conf['respage']);
		if ($p > $nums AND $p != 1)
		{
			$tm->noexistprint();
		}
	}
	else
	{
		if ($p > 0) {
			$tm->noexistprint();
		} else {
			$p = 1;
		}
	}

	/**
	 * Данные категории
	 */
	if (isset($area[$item['catid']]))
	{
		$obj = $area[$item['catid']];
	}
	else
	{
		$obj = array
			(
				'catid'    => '',
				'parentid' => '',
				'catcpu'   => '',
				'catname'  => '',
				'icon'     => '',
				'access'   => '',
				'groups'   => ''
			);
	}

    /**
     * Количество просмотров (обновляем)
     */
    $db->query("UPDATE ".$basepref."_".WORKMOD." SET hits = hits + 1 WHERE id = '".$item['id']."'");

	/**
	 * Свой заголовок
	 */
	if (isset($item['customs']) AND ! empty($item['customs']))
	{
		define('CUSTOM', $api->siteuni($item['customs']));
	}
	else
	{
		$global['title'] = preparse($item['title'],THIS_TRIM);
		$global['title'].= (empty($obj['catname'])) ? '' : ' - '.$obj['catname'];
	}

	/**
	 * Мета данные
	 */
	$global['keywords'] = (empty($item['keywords'])) ? $api->seokeywords($item['title'].' '.$item['textshort'].' '.$item['textmore'], 5, 35) : $item['keywords'];
	$global['descript'] = (empty($item['descript'])) ? '' : $item['descript'];

	/**
	 * Меню, хлебные крошки, с учетом категории
	 */
	if ($item['catid'] > 0)
	{
		$api->catcache = $area;
		$global['insert']['current'] = preparse($item['title'], THIS_TRIM);
		$global['insert']['breadcrumb'] = $api->sitecat($item['catid']);
	}
	else
	{
		$global['insert']['current'] = preparse($item['title'], THIS_TRIM);
		$global['insert']['breadcrumb'] = '<a href="'.$ro->seo('index.php?dn='.WORKMOD).'">'.$global['modname'].'</a>';
	}

	/**
	 * Ограничение доступа
	 */
	if($obj['access'] == 'user' OR $item['acc'] == 'user')
	{
		if ( ! defined('USER_LOGGED'))
		{
			$tm->noaccessprint();
		}
		if (defined('GROUP_ACT') AND ! empty($item['groups']))
		{
			$group = Json::decode($item['groups']);
			if ( ! isset($group[$usermain['gid']]))
			{
				$tm->norightprint();
			}
		}
		if (defined('GROUP_ACT') AND ! empty($obj['groups']))
		{
			$group = Json::decode($obj['groups']);
			if ( ! isset($group[$usermain['gid']]))
			{
				$tm->norightprint();
			}
		}
	}

	/**
	 * Ограничение доступа к файлам
	 */
	$facc = $fgroups = TRUE;
	if ( ! empty($item['files']) AND $item['facc'] == 'user')
	{
		if ( ! defined('USER_LOGGED'))
		{
			$facc = FALSE;
		}
		elseif ( ! empty($item['fgroups']))
		{
			$group = Json::decode($item['fgroups']);
			if ( ! isset($group[$usermain['gid']]))
			{
				$fgroups = FALSE;
			}
		}
	}

	/**
	 * Вывод на страницу, шапка
	 */
	$tm->header();

	/**
	 * Сортировки
	 */
	$ins['order'] = array('asc', 'desc');
	$ins['sort'] = array('public', 'id', 'title', 'hits');
	$order = (isset($obj['ord']) AND in_array($obj['ord'], $ins['order'])) ? $obj['ord'] : 'asc';
	$sort = (isset($obj['sort']) AND in_array($obj['sort'], $ins['sort'])) ? $obj['sort'] : 'id';

	// CPU
	$ins['cpu']    = (defined('SEOURL') AND $item['cpu']) ? '&amp;cpu='.$item['cpu'] : '';
	$ins['catcpu'] = (defined('SEOURL') AND ! empty($obj['catcpu'])) ? '&amp;ccpu='.$obj['catcpu'] : '';

	// URL
	$ins['url'] = $ro->seo('index.php?dn='.WORKMOD.$ins['catcpu'].'&amp;to=page&amp;id='.$item['id'].$ins['cpu']);
	$ins['caturl'] = $ro->seo('index.php?dn='.WORKMOD.'&amp;to=cat&amp;id='.$item['catid'].$ins['catcpu']);

	/**
	 * Отзывы
	 */
	if ($conf['resact'] == 'yes')
	{
		$tm->unmanule['logged'] = (empty($usermain['uname'])) ? 'yes' : 'no';

		$re = new Reviews(WORKMOD);

		// Вывод
		if ($item['reviews'] > 0)
		{
			$ins['reviews'] = $re->reviews($item['id'], $item['reviews'], $ins['cpu'], $ins['catcpu'], $item['title'], $p);
		}

		// Новые посты ajax
		$ins['ajaxbox'] = $tm->parse(array('empty' => 'empty'), $tm->manuale['ajaxbox']);

		// Форма
		$ins['reform'] = $re->reform($item['id'], $item['title']);
	}

    /**
	 * Переключатели
	 */
	$tm->unmanule['video'] = ($conf['video_add']=='yes' AND ! empty($item['videos'])) ? 'yes' : 'no';
	$tm->unmanule['photo'] = ($conf['photo_add']=='yes' AND ! empty($item['photos'])) ? 'yes' : 'no';
	$tm->unmanule['author'] = ( ! empty($item['author']) AND $conf['author'] == 'yes') ? 'yes' : 'no';
	$tm->unmanule['country'] = ( ! empty($item['country'])) ? 'yes' : 'no';
	$tm->unmanule['region'] = ( ! empty($item['region'])) ? 'yes' : 'no';
	$tm->unmanule['address'] = ( ! empty($item['address'])) ? 'yes' : 'no';
	$tm->unmanule['site'] = ( ! empty($item['site'])) ? 'yes' : 'no';
	$tm->unmanule['city'] = ( ! empty($item['city'])) ? 'yes' : 'no';
	$tm->unmanule['skype'] = ( ! empty($item['skype'])) ? 'yes' : 'no';
	$tm->unmanule['tags'] = ( ! empty($item['tags'])) ? 'yes' : 'no';
	$tm->unmanule['image'] = ( ! empty($item['image'])) ? 'yes' : 'no';
	$tm->unmanule['social'] = $config['social_bookmark'];
	$tm->unmanule['review'] = $conf['resact'];
	$tm->unmanule['print'] = $conf['print'];
	$tm->unmanule['date'] = $conf['date'];

	/**
	 * Вложенные шаблоны
	 */
	$tm->manuale = array
		(
			'cat' => null,
			'icon' => null,
			'tags' => null,
			'rows' => null,
			'media' => null,
			'social' => null,
			'ajaxbox' => null,
			'valrate' => null,
			'formajax' => null,
			'formrate' => null
		);

	/**
	 * Шаблон
	 */
    $ins['template'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/open'));

	/**
	 * Содержимое
	 */
	$ins['textshort'] = $api->siteuni($item['textshort']);
	$ins['textmore']  = $api->siteuni($item['textmore']);

	/**
	 * Карта Яндекс
	 */
	$location = strip_tags($item['country'].', '.$item['region'].', '.$item['city'].', '.$item['address']);
	if ($conf['yandex']=='yes' AND strlen($location) > 0)
	{
		$ins['maps'] = $tm->parse(array
								(
									'langmap'  => $lang['firms_map'],
									'location' => $location
								),
								$tm->parsein($tm->create('mod/'.WORKMOD.'/map')));
	}

	/**
	 * Фото галерея
	 */
	if ($conf['photo_add']=='yes' AND ! empty($item['photos']))
	{
		$inq_photo = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_photo WHERE firm_id = '".$item['id']."' AND act = 'yes' ORDER BY posit DESC");
		if ($db->numrows($inq_photo) > 0)
		{
			$ins['photos'] = array();
			while ($photo = $db->fetchrow($inq_photo))
			{
				$tm->unmanule['title'] = ( ! empty($photo['title'])) ? 'yes' : 'no';
				$tm->unmanule['descript'] = ( ! empty($photo['descript'])) ? 'yes' : 'no';

				$ins['photos'][] = $tm->parse(array
									(
										'title'    => $api->siteuni($photo['title']),
										'descript' => $api->siteuni($photo['descript']),
										'image'    => $photo['image'],
										'thumb'    => $photo['image_thumb'],
										'alt'      => $photo['image_alt']
									),
									$tm->parsein($tm->create('mod/'.WORKMOD.'/photo')));
			}

			$ins['photo'] = $tm->tableprint($ins['photos'], 3);
		}
	}

	/**
	 * Видео галерея
	 */
	if ($conf['video_add']=='yes' AND ! empty($item['videos']))
	{
		$inq_photo = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_video WHERE firm_id = '".$item['id']."' AND act = 'yes' ORDER BY posit DESC");
		if ($db->numrows($inq_photo) > 0)
		{
			$ins['videos'] = array();
			while ($video = $db->fetchrow($inq_photo))
			{
				$tm->unmanule['title'] = ( ! empty($video['title'])) ? 'yes' : 'no';
				$tm->unmanule['descript'] = ( ! empty($video['descript'])) ? 'yes' : 'no';

				$ins['videos'][] = $tm->parse(array
									(
										'title'    => $api->siteuni($video['title']),
										'descript' => $api->siteuni($video['descript']),
										'video'    => $ro->seo('index.php?dn='.WORKMOD.'&amp;to=video&amp;id='.$video['id']),
										'image'    => $video['image'],
										'alt'      => ''
									),
									$tm->parsein($tm->create('mod/'.WORKMOD.'/video')));
			}

			$ins['video'] = $tm->tableprint($ins['videos'], 3);
		}
	}

	/**
	 * Рейтинг
	 */
	if ($conf['rating'] == 'yes')
	{
		$ins['temp_rating'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/rating'));

		$ruser = $db->numrows
						(
							$db->query
							(
								"SELECT ratingid FROM ".$basepref."_rating WHERE (
								 file = '".WORKMOD."'
								 AND id = '".$item['id']."'
								 AND ratingip = '".REMOTE_ADDRS."'
								 AND ratingtime >= '".(NEWTIME - $conf['ratetime'])."'
								)"
							)
						);

		$ruser = ($ruser > 0) ? FALSE : TRUE;
		$ins['rate'] = ($item['rating'] == 0) ? 0 : round($item['totalrating'] / $item['rating']);
		$ins['wrate'] = intval((100 / 5) * $ins['rate']);

		$ins['valrate'] = $tm->parse(array
								(
									'imgrate'   => $ins['rate'],
									'titlerate' => ($ins['rate'] == 0) ? $lang['rate_0'] : $lang['rate_'.$ins['rate'].'']
								),
								$tm->manuale['valrate']);

		if (
			$conf['rateuse'] == 'all' OR
			$conf['rateuse'] == 'user' AND
			defined('USER_LOGGED')
		) {
			if ($config['ajax'] == 'yes')
			{
				if ($ruser)
				{
					$ins['valrate'] = $tm->parse(array
											(
												'mod'    => WORKMOD,
												'rate_1' => $lang['rate_1'],
												'rate_2' => $lang['rate_2'],
												'rate_3' => $lang['rate_3'],
												'rate_4' => $lang['rate_4'],
												'rate_5' => $lang['rate_5'],
												'width'  => $ins['wrate'],
												'id'     => $item['id']
											),
											$tm->manuale['formajax']);
				}
			}
			else
			{
				if ($ruser)
				{
					$ins['formrate'] = $tm->parse(array
											(
												'post_url' => $ro->seo('index.php?dn='.WORKMOD),
												'rate_but' => $lang['rate_button'],
												'choose'   => $lang['choose'],
												'rate_1'   => $lang['rate_1'],
												'rate_2'   => $lang['rate_2'],
												'rate_3'   => $lang['rate_3'],
												'rate_4'   => $lang['rate_4'],
												'rate_5'   => $lang['rate_5'],
												'width'    => $ins['wrate'],
												'id'       => $item['id']
											),
											$tm->manuale['formrate']);
				}
			}
		}

		$ins['rating'] = $tm->parse(array
							(
								'valrate'     => $ins['valrate'],
								'formrate'    => $ins['formrate'],
								'rating'      => $item['rating'],
								'totalrating' => $item['totalrating'],
								'langrate'    => $lang['all_rating'],
								'waitup'      => $lang['wait_up'],
								'countrating' => $lang['rate_'.$ins['rate']]
							),
							$ins['temp_rating']);
	}

	/**
	 * Теги
	 */
	if ($conf['tags'] == 'yes')
	{
		$ins['temptags'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/open.tags'));

		$tc = array();
		$taginq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_tag", $config['cachetime'], WORKMOD);
		while ($t = $db->fetchassoc($taginq, $config['cache']))
		{
			$tc[$t['tagid']] = $t;
		}

		$key = explode(',', $item['tags']);
		foreach ($key as $k)
		{
			if (isset($tc[$k]))
			{
				$tag_cpu = (defined('SEOURL') AND $tc[$k]['tagcpu']) ? '&amp;cpu='.$tc[$k]['tagcpu'] : '';
				$tag_url = $ro->seo('index.php?dn='.WORKMOD.'&amp;re=tags&amp;to=tag&amp;id='.$tc[$k]['tagid'].$tag_cpu);
				$ins['tagword'] .= $tm->parse(array(
								'tag_url'  => $tag_url,
								'tag_word' => $tc[$k]['tagword']
							),
							$tm->manuale['tags']);
			}
		}
		if (isset($tc[$k]) AND ! empty($key))
		{
			$tm->unmanule['tags'] = 'yes';
			$ins['tags'] = $tm->parse(array
								(
									'tags'     => chop(trim($ins['tagword']), ','),
									'langtags' => $lang['all_tags']
								),
								$ins['temptags']);
		}
	}

	/**
	 * Прикрепленные файлы
	 */
	if ( ! empty($item['files']))
	{
		// Сообщение
		$tm->unmanule['fgroups'] = ($fgroups) ? 'yes' : 'no';
		$ins['temp_notice'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/files.notice'));

		if ( ! $facc OR ! $fgroups)
		{
			$ins['filenotice'] = $tm->parse(array(
										'langdown' => $lang['block_down'],
										'text'     => $lang['access_file'],
										'login'    => $ro->seo('index.php?dn=user&amp;re=login'),
										'enter'    => $lang['user_login']
									),
									$ins['temp_notice']);
		}

		// Файлы
		$fs = Json::decode($item['files']);
		if (is_array($fs) AND $facc AND $fgroups)
		{
			$trl = new Translit();
			$ins['temp_file'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/files'));

			$ins['file'] = null;
			foreach ($fs as $k => $v)
			{
				$ext = pathinfo($v['path'], PATHINFO_EXTENSION);
				$fname = $trl->title($trl->process($v['title']));
				$loads = $ro->seo('index.php?dn='.WORKMOD.'&amp;re=load&amp;id='.$item['id'].'&amp;fid='.$k.'&amp;ds='.$fname);
				$ins['file'].= $tm->parse(array(
										'key'   => $k,
										'mod'   => WORKMOD,
										'ext'   => $ext,
										'type'  => file_type($v['path']),
										'size'  => file_size(@filesize($v['path'])),
										'link'  => $loads,
										'title' => $v['title']
									),
									$tm->manuale['files']);
			}

			$ins['files'] = $tm->parse(array(
										'langdown' => $lang['block_down'],
										'filerows' => $ins['file']
									),
									$ins['temp_file']);
		}
	}

	/**
	 * Категории
	 */
	if (isset($area[$item['catid']]))
	{
		$ins['open_cats'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/open.cats'));

		$cats[$item['id']][$item['catid']] = $item['catid'];
		if ($conf['multcat'] == 'yes')
		{
			$cat_inq = $db->query("SELECT * FROM ".$basepref."_".WORKMOD."_cats", $config['cachetime'], WORKMOD);
			while ($cs = $db->fetchassoc($cat_inq, $config['cache']))
			{
				$cats[$cs['id']][$cs['catid']] = $cs['catid'];
			}
		}

		if ( ! empty($cats[$item['id']]))
		{
			$printcat = '';
			foreach ($cats[$item['id']] as $catid)
			{
				$cat_cpu = (defined('SEOURL')) ? '&amp;ccpu='.$area[$catid]['catcpu'] : '';
				$cat_url = $ro->seo('index.php?dn='.WORKMOD.'&amp;to=cat&amp;id='.$catid.$cat_cpu);

				$printcat .= $tm->parse(array(
								'cat_url'  => $cat_url,
								'cat_name' => $area[$catid]['catname']
							),
							$tm->manuale['cats']);
			}

			$ins['cats'] = $tm->parse(array
								(
									'cats'     => chop(trim($printcat), ','),
									'langcats' => $lang['all_cat']
								),
								$ins['open_cats']);
		}

		if (  ! empty($area[$item['catid']]['icon']))
		{
			$ins['icon'] = $tm->parse(array(
									'icon' => $area[$item['catid']]['icon'],
									'alt'  => $api->siteuni($area[$item['catid']]['catname'])
								),
								$tm->manuale['icon']);
		}
	}


	/**
	 * Вводное изображение
	 */
	if ( ! empty($item['image_thumb']))
	{
		$ins['float'] = ($item['image_align'] == 'left') ? 'imgleft' : 'imgright';
		$ins['alt']   = ( ! empty($item['image_alt'])) ? $api->siteuni($item['image_alt']) : '';
		$ins['image'] = $tm->parse(array
							(
								'float' => $ins['float'],
								'thumb' => $item['image_thumb'],
								'image' => $item['image'],
								'alt'   => $ins['alt']
							),
							$tm->parsein($tm->create('mod/'.WORKMOD.'/thumb')));
	}

	/**
	 * Изображения по тексту
	 */
	if ( ! empty($item['images']))
	{
		$im = Json::decode($item['images']);
		if (is_array($im))
		{
			foreach ($im as $k => $v)
			{
				$ins['float'] = 'imgtext-'.$v['align'];
				$ins['alt']   = ( ! empty($v['alt'])) ? $api->siteuni($v['alt']) : '';

				$tm->unmanule['image'] = ( ! empty($v['image'])) ? 'yes' : 'no';
				$ins['temp_thumb'] = $tm->parsein($tm->create('mod/'.WORKMOD.'/thumb'));

				if ( ! empty($v['thumb']))
				{
					$ins['img'] = $tm->parse(array
						(
							'float' => $ins['float'],
							'thumb' => $v['thumb'],
							'image' => $v['image'],
							'alt'   => $ins['alt']
						),
						$ins['temp_thumb']);
				}

				$ins['textmore'] = $tm->parse(array('img'.$k => $ins['img']), $ins['textmore']);
			}
		}
	}

	/**
	 * Сообщения для пользователей
	 */
	if ( ! empty($item['textnotice']))
	{
		$tm->unmanule['notice'] = defined('USER_LOGGED') ? 'yes' : 'no';
		$ins['notice'] = $tm->parse(array
							(
								'guest' => $lang['block_user_view'],
								'user'  => $api->siteuni($item['textnotice'])
							),
							$tm->parsein($tm->create('mod/'.WORKMOD.'/notice')));
	}

	/**
	 * Рекомендуемые
	 */
	if ($conf['rec'] == 'yes')
	{
		if ($conf['multcat'] == 'yes')
		{
			$inq = $db->query
				(
					"SELECT DISTINCT a.* FROM ".$basepref."_".WORKMOD."_cat AS c
					 INNER JOIN ".$basepref."_".WORKMOD."_cats AS b ON (b.catid = c.catid)
					 INNER JOIN ".$basepref."_".WORKMOD." AS a ON (a.id = b.id)
					 WHERE c.catid IN (".$item['catid'].") AND a.act = 'yes' AND a.pid = '0' AND a.id <> '".$item['id']."'
					 AND (stpublic = 0 OR stpublic < '".NEWTIME."') AND (unpublic = 0 OR unpublic > '".NEWTIME."')
					 ORDER BY a.id, ".$sort." ".$order." LIMIT ".$conf['lastrec']
				);
		}
		else
		{
			$inq = $db->query
				(
					"SELECT catid, id, cpu, public, stpublic, unpublic, title FROM ".$basepref."_".WORKMOD."
					 WHERE act = 'yes' AND pid = '0' AND catid = '".$item['catid']."'
					 AND (stpublic = 0 OR stpublic < '".NEWTIME."') AND (unpublic = 0 OR unpublic > '".NEWTIME."')
					 AND id <> '".$item['id']."' ORDER BY public DESC LIMIT ".$conf['lastrec']
				);
		}

		if ($db->numrows($inq) > 0)
		{
			$i = 1;
			while ($items = $db->fetchassoc($inq))
			{
				$rec[$i] = $items;
				$i ++;
			}

			$ins['temprec']= $tm->parsein($tm->create('mod/'.WORKMOD.'/rec'));

			foreach ($rec as $v)
			{
				$rcpu = (defined('SEOURL') AND $v['cpu']) ? "&amp;cpu=".$v['cpu'] : "";
				$rccpu = (defined('SEOURL') AND ! empty($area[$v['catid']]['catcpu'])) ? '&amp;ccpu='.$area[$v['catid']]['catcpu'] : '';

				$ins['rec'].= $tm->parse(array
								(
									'title' => $api->siteuni($v['title']),
									'link'  => $ro->seo('index.php?dn='.WORKMOD.$rccpu.'&amp;to=page&amp;id='.$v['id'].$rcpu),
									'date'  => ($v['stpublic'] > 0) ? $v['stpublic'] : $v['public']
								),
								$tm->manuale['rows']);
			}

			$ins['rec'] = $tm->parse(array
							(
								'rectitle' => $lang['all_recommend'],
								'recprint' => $ins['rec']
							),
							$ins['temprec']);
		}
	}

	/**
	 * Перелинковка
	 */
 	if ($config['anchor'] == 'yes' AND $config['mod'][WORKMOD]['seo'] == 'yes')
	{
		$array_links = DNDIR.'cache/cache.seo.php';
		if (file_exists($array_links))
		{
			include($array_links);
			if (! empty($seo) AND isset($seo[WORKMOD]))
			{
				foreach ($seo[WORKMOD] as $val)
				{
					$seolink = seo_link($val['link']);
					if (isset($seolink))
					{
						$ins['textshort'] = preg_replace
												(
													'/([^\<\>])'.$val['word'].'(?![^<]*>)(?=\W|$)/um',
													' <a href="'.$seolink.'" title="'.$val['title'].'">'.$val['word'].'</a>',
													$ins['textshort'],
													$val['count'],
													$done
												);
						$ins['textmore'] = preg_replace
												(
													'/([^\<\>])'.$val['word'].'(?![^<]*>)(?=\W|$)/um',
													' <a href="'.$seolink.'" title="'.$val['title'].'">'.$val['word'].'</a>',
													$ins['textmore'],
													$val['count'] - $done
												);
					}
				}
			}
		}
	}

	/**
	 * Социальные закладки
	 */
	if ($config['social_bookmark'] == 'yes')
	{
		$ins['tempsocial']= $tm->parsein($tm->create('mod/'.WORKMOD.'/social'));

		$l = Json::decode($config['social']);
		if (is_array($l))
		{
			foreach ($l as $k => $v)
			{
				$ins['cpu'] = (defined('SEOURL') AND ! empty($item['cpu'])) ? '&amp;cpu='.$item['cpu'] : '';
				$ins['catcpu'] = (defined('SEOURL') AND ! empty($obj['catcpu'])) ? '&amp;ccpu='.$obj['catcpu'] : '';

				$url = $ro->seo('index.php?dn='.WORKMOD.$ins['catcpu'].'&amp;to=page&amp;id='.$item['id'].$ins['cpu'], true);
				$url = urlencode(stripslashes($url));
				$title = urlencode(stripslashes($item['title']));
				$link = str_replace(array('{link}', '{title}'), array($url, $title), $v['link']);

				if ($v['act'] == 'yes')
				{
					$ins['srows'] .= $tm->parse(array
											(
												'link' => $link,
												'icon' => $v['icon'],
												'alt'  => $v['alt']
											),
											$tm->manuale['social']);
				}
			}

			// Вывод
			$ins['social'] = $tm->parse(array('socialrows' => $ins['srows']), $ins['tempsocial']);
		}
	}

	/**
	 * Организация в соц.сетях
	 */
	$social = Json::decode($item['social']);
	if (is_array($social) AND ! empty($social))
	{
		$ins['tempmysocial']= $tm->parsein($tm->create('mod/'.WORKMOD.'/open.social'));
		foreach ($social as $v)
		{
			$ins['msrows'] .= $tm->parse(array(
								'link' => $v['link'],
								'name' => $v['title']
							),
							$tm->manuale['mysocial']);
		}
		$ins['mysocial'] = $tm->parse(array(
								'title'  => $lang['firms_social'],
								'msrows' => $ins['msrows']
							),
							$ins['tempmysocial']);
	}

	/**
	 * Автор
	 */
	if ( ! empty($item['author']) AND $conf['author'] == 'yes')
	{
		$ins['author'] = preg_replace('/[^\pL\pNd\pZs\pP\pM]/us', '', $item['author']);
		if (isset($config['mod']['user']))
		{
			$udata = $userapi->userdata('uname', $ins['author']);
			if ( ! empty($udata))
			{
				$ins['author'] = '<a href="'.$ro->seo($userapi->data['linkprofile'].$udata['userid']).'">'.$udata['uname'].'</a>';
			}
		}
	}

    /**
     * Поиск
     */
    $ins['search'] = $tm->search($conf['search'], WORKMOD, 1);

	// Дата
	$ins['public'] = ($item['stpublic'] > 0) ? $item['stpublic'] : $item['public'];

	/**
	 * Печать
	 */
	$ins['print_url']  = $ro->seo('index.php?dn='.WORKMOD.'&amp;re=print&amp;id='.$item['id']);

	/**
	 * Сайт организации
	 */
	$ins['hostsite']= parse_url($item['site'], PHP_URL_HOST);

	/**
	 * Отзывы
	 */
	$ins['review'] = ($conf['resact'] == 'yes') ? $item['reviews'] : '';

	/**
	 * Рейтинг
	 */
	$ins['rate'] = ($item['rating'] == 0) ? 0 : round($item['totalrating'] / $item['rating']);
	$ins['title_rate'] = ($ins['rate'] == 0) ? $lang['rate_0'] : $lang['rate_'.$ins['rate'].''];

	/**
	 * Вывод
	 */
	$tm->parseprint(array
		(
			'mod'           => WORKMOD,
			'id'            => $item['id'],
			'title'         => $api->siteuni($item['title']),
			'country'       => $api->siteuni($item['country']),
			'region'        => $api->siteuni($item['region']),
			'city'          => $api->siteuni($item['city']),
			'address'       => $api->siteuni($item['address']),
			'person'        => $api->siteuni($item['person']),
			'website'       => $item['site'],
			'phone'         => $item['phone'],
			'skype'         => $item['skype'],
			'counts'        => $item['hits'],
			// ins
			'icon'          => $ins['icon'],
			'cats'          => $ins['cats'],
			'image'         => $ins['image'],
			'textshort'     => $ins['textshort'],
			'textmore'      => $ins['textmore'],
			'date'          => $ins['public'],
			'hostsite'      => $ins['hostsite'],
			'textnotice'    => $ins['notice'],
			'social'        => $ins['social'],
			'maps'          => $ins['maps'],
			'video'         => $ins['video'],
			'print_url'     => $ins['print_url'],
			'search'        => $ins['search'],
			'recommend'     => $ins['rec'],
			'author'        => $ins['author'],
			'rating'        => $ins['rating'],
			'langtags'      => $ins['langtags'],
			'files'         => $ins['files'],
			'filenotice'    => $ins['filenotice'],
			'review'        => $ins['review'],
			'ratings'       => $ins['rate'],
			'rating'        => $ins['rating'],
			'titlerate'     => $ins['title_rate'],
			'tags'          => $ins['tags'],
			'mysocial'      => $ins['mysocial'],
			'ajaxbox'       => $ins['ajaxbox'],
			'reviews'       => $ins['reviews'],
			'reform'        => $ins['reform'],
			'ajaxbox'       => $ins['ajaxbox'],
			'link'          => $ins['url'],
			'photo'         => $ins['photo'],
			// lang
			'lang_public'   => $lang['all_data'],
			'lang_hits'     => $lang['all_hits'],
			'lang_contacts' => $lang['vcard'],
			'lang_country'  => $lang['country'],
			'lang_region'   => $lang['all_region'],
			'lang_city'     => $lang['city'],
			'lang_address'  => $lang['all_address'],
			'lang_phone'    => $lang['phone'],
			'lang_show'     => $lang['all_show'],
			'lang_url'      => $lang['web_site'],
			'lang_details'  => $lang['order_detail'],
			'lang_author'   => $lang['author'],
			'lang_person'   => $lang['person'],
			'lang_photo'    => $lang['photo_album'],
			'lang_video'    => $lang['video_album'],
			'lang_email'    => $lang['e_mail'],
			'lang_contact'  => $lang['in_contact'],
			'lang_rate'     => $lang['all_rating'],
			'lang_print'    => $lang['print_link'],
			'lang_review'   => $lang['response_total']
		),
		$ins['template']
	);

	/**
	 * Вывод на страницу, подвал
	 */
	$tm->footer();
}

/**
 * Предпросмотр видео
 ----------------------*/
if ($to == 'video')
{
	header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
	header('Content-Type: text/html; charset='.$config['langcharset'].'');

	global $id;

	$id = preparse($id, THIS_INT);
	$item = $db->fetchassoc($db->query("SELECT * FROM ".$basepref."_".WORKMOD."_video WHERE id = '".$id."'"));
	echo '	<div style="width: 640px; height: 360px;">
				<iframe src="'.$item['video'].'" width="640" height="360" frameborder="0" allowfullscreen></iframe>
			</div>';
}

/**
 * Метка phone
 * ------------ */
if($to == 'phone')
{
	header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
	header('Content-Type: text/html; charset='.$config['langcharset'].'');

    $id = preparse($id, THIS_INT);
	$item = $db->fetchassoc($db->query("SELECT phone FROM ".$basepref."_".WORKMOD." WHERE id = '".$id."' ORDER BY id DESC LIMIT 1"));
	echo $item['phone'];
	exit();
}
