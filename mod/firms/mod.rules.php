<?php
/**
 * File:        /mod/firms/mod.rules.php
 *
 * @package     Danneo Basis kernel
 * @version     Danneo CMS (Next) v1.5.4
 * @copyright   (c) 2005-2017 Danneo Team
 * @link        http://danneo.ru
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('DNREAD') OR die('No direct access');

/**
 * Рабочий мод
 */
$WORKMOD = basename(__DIR__);

/**
 * Шаблоны преобразований URL
 */
return array
(
	$WORKMOD => array
	(
		// url > cpu
		're' => array
		(
			"index.php\?dn=".$WORKMOD."&to=index&p=(\d+)" => $WORKMOD."/p-$1",
			"index.php\?dn=".$WORKMOD."&to=index" => $WORKMOD."/",
			"index.php\?dn=".$WORKMOD."" => $WORKMOD."/",
			"index.php\?dn=".$WORKMOD."&to=video&id=(\d+)" => $WORKMOD."/video-$1",
			"index.php\?dn=".$WORKMOD."&re=([a-z]*)&id=(\d+)&p=(\d+)" => $WORKMOD."/$1-$2-$3",
			"index.php\?dn=".$WORKMOD."&re=([a-z]*)&id=(\d+)" => $WORKMOD."/$1-$2",
			"index.php\?dn=".$WORKMOD."&re=(tags|search|add|my|rss|reviews)" => $WORKMOD."/$1",
			"index.php\?dn=".$WORKMOD."&re=load&id=(\d+)&fid=(\d+)&ds=([a-zA-Z0-9_\-]*)" => $WORKMOD."/load-$1-$2-$3",
			"index.php\?dn=".$WORKMOD."&re=my&to=photodel&fid=(\d+)&id=(\d+)&ok=([a-z]*)" => $WORKMOD."/my/$1/photo/del-$2-$3",
			"index.php\?dn=".$WORKMOD."&re=my&to=photodel&fid=(\d+)&id=(\d+)" => $WORKMOD."/my/$1/photo/del-$2",
			"index.php\?dn=".$WORKMOD."&re=my&to=photo&id=(\d+)" => $WORKMOD."/my/$1/photo",
			"index.php\?dn=".$WORKMOD."&re=my&to=videodel&fid=(\d+)&id=(\d+)&ok=([a-z]*)" => $WORKMOD."/my/$1/video/del-$2-$3",
			"index.php\?dn=".$WORKMOD."&re=my&to=videodel&fid=(\d+)&id=(\d+)" => $WORKMOD."/my/$1/video/del-$2",
			"index.php\?dn=".$WORKMOD."&re=my&to=video&id=(\d+)" => $WORKMOD."/my/$1/video",
			"index.php\?dn=".$WORKMOD."&re=my&to=edit&id=(\d+)" => $WORKMOD."/my/edit-$1",
			"index.php\?dn=".$WORKMOD."&re=my&to=del&id=(\d+)" => $WORKMOD."/my/del-$1",
			"index.php\?dn=".$WORKMOD."&re=rss&ya=([a-zA-Z0-9_\-]*)" => $WORKMOD."/rss-$1",
			"index.php\?dn=".$WORKMOD."&to=dat&ye=(\d+)&mo=(\d+)&da=(\d+)&p=(\d+)" => $WORKMOD."/date-$1-$2-$3-p$4",
			"index.php\?dn=".$WORKMOD."&to=dat&ye=(\d+)&mo=(\d+)&da=(\d+)" => $WORKMOD."/date-$1-$2-$3",
			"index.php\?dn=".$WORKMOD."&re=tags&to=tag&id=(\d+)&cpu=([a-zA-Z0-9_\-]*)&p=(\d+)" => $WORKMOD."/tags/$2-p$3",
			"index.php\?dn=".$WORKMOD."&re=tags&to=tag&id=(\d+)&cpu=([a-zA-Z0-9_\-]*)" => $WORKMOD."/tags/$2",
			"index.php\?dn=".$WORKMOD."&to=cat&id=(\d+)&ccpu=([a-zA-Z0-9_\-]*)&p=(\d+)" => $WORKMOD."/$2/p-$3",
			"index.php\?dn=".$WORKMOD."&to=cat&id=(\d+)&ccpu=([a-zA-Z0-9_\-]*)" => $WORKMOD."/$2/",
			"index.php\?dn=".$WORKMOD."&to=page&id=(\d+)&cpu=([a-zA-Z0-9_\-]*)&p=(\d+)" => $WORKMOD."/$2-p$3",
			"index.php\?dn=".$WORKMOD."&to=page&id=(\d+)&cpu=([a-zA-Z0-9_\-]*)" => $WORKMOD."/$2",
			"index.php\?dn=".$WORKMOD."&ccpu=([a-zA-Z0-9_\-]*)&to=page&id=(\d+)&cpu=([a-zA-Z0-9_\-]*)&p=(\d+)" => $WORKMOD."/$1/$3-p$4",
			"index.php\?dn=".$WORKMOD."&ccpu=([a-zA-Z0-9_\-]*)&to=page&id=(\d+)&cpu=([a-zA-Z0-9_\-]*)" => $WORKMOD."/$1/$3",
		),

		// cpu > url
		'to' => array
		(
			$WORKMOD."/p-(\d+)" => "index.php?dn=".$WORKMOD."&to=index&p=$1",
			$WORKMOD."/" => "index.php?dn=".$WORKMOD."&to=index",
			$WORKMOD."/" => "index.php?dn=".$WORKMOD."",
			$WORKMOD."/video-(\d+)" => "index.php?dn=".$WORKMOD."&to=video&id=$1",
			$WORKMOD."/([a-z]*)-(\d+)-(\d+)" => "index.php?dn=".$WORKMOD."&re=$1&id=$2&p=$3",
			$WORKMOD."/([a-z]*)-(\d+)" => "index.php?dn=".$WORKMOD."&re=$1&id=$2",
			$WORKMOD."/(tags|search|add|my|rss|reviews)" => "index.php?dn=".$WORKMOD."&re=$1",
			$WORKMOD."/load-(\d+)-(\d+)-([a-zA-Z0-9_\-]*)" => "index.php?dn=".$WORKMOD."&re=load&id=$1&fid=$2&ds=$3",
			$WORKMOD."/my/(\d+)/photo/del-(\d+)-([a-z]*)" => "index.php?dn=".$WORKMOD."&re=my&to=photodel&fid=$1&id=$2&ok=$3",
			$WORKMOD."/my/(\d+)/photo/del-(\d+)" => "index.php?dn=".$WORKMOD."&re=my&to=photodel&fid=$1&id=$2",
			$WORKMOD."/my/(\d+)/photo" => "index.php?dn=".$WORKMOD."&re=my&to=photo&id=$1",
			$WORKMOD."/my/(\d+)/video/del-(\d+)-([a-z]*)" => "index.php?dn=".$WORKMOD."&re=my&to=videodel&fid=$1&id=$2&ok=$3",
			$WORKMOD."/my/(\d+)/video/del-(\d+)" => "index.php?dn=".$WORKMOD."&re=my&to=videodel&fid=$1&id=$2",
			$WORKMOD."/my/(\d+)/video" => "index.php?dn=".$WORKMOD."&re=my&to=video&id=$1",
			$WORKMOD."/my/edit-(\d+)" => "index.php?dn=".$WORKMOD."&re=my&to=edit&id=$1",
			$WORKMOD."/my/del-(\d+)" => "index.php?dn=".$WORKMOD."&re=my&to=del&id=$1",
			$WORKMOD."/rss-([a-zA-Z0-9_\-]*)" => "index.php?dn=".$WORKMOD."&re=rss&ya=$1",
			$WORKMOD."/date-(\d+)-(\d+)-(\d+)-p(\d+)" => "index.php?dn=".$WORKMOD."&to=dat&ye=$1&mo=$2&da=$3&p=$4",
			$WORKMOD."/date-(\d+)-(\d+)-(\d+)" => "index.php?dn=".$WORKMOD."&to=dat&ye=$1&mo=$2&da=$3",
			$WORKMOD."/tags/([a-zA-Z0-9_\-]*)-p(\d+)" => "index.php?dn=".$WORKMOD."&re=tags&to=tag&cpu=$1&p=$2",
			$WORKMOD."/tags/([a-zA-Z0-9_\-]*)" => "index.php?dn=".$WORKMOD."&re=tags&to=tag&cpu=$1",
			$WORKMOD."/([a-zA-Z0-9_\-]*)/p-(\d+)" => "index.php?dn=".$WORKMOD."&to=cat&ccpu=$1&p=$2",
			$WORKMOD."/([a-zA-Z0-9_\-]*)/" => "index.php?dn=".$WORKMOD."&to=cat&ccpu=$1",
			$WORKMOD."/([a-zA-Z0-9_\-]*)-p(\d+)" => "index.php?dn=".$WORKMOD."&to=page&cpu=$1&p=$2",
			$WORKMOD."/([a-zA-Z0-9_\-]*)" => "index.php?dn=".$WORKMOD."&to=page&cpu=$1",
			$WORKMOD."/([a-zA-Z0-9_\-]*)/([a-zA-Z0-9_\-]*)-p(\d+)" => "index.php?dn=".$WORKMOD."&ccpu=$1&to=page&cpu=$2&p=$3",
			$WORKMOD."/([a-zA-Z0-9_\-]*)/([a-zA-Z0-9_\-]*)" => "index.php?dn=".$WORKMOD."&ccpu=$1&to=page&cpu=$2",
		)
	)
);
