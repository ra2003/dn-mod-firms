<?php
/**
 * File:        /core/classes/Counts.php
 *
 * @package     Danneo Basis kernel
 * @version     Danneo CMS (Next) v1.5.3
 * @copyright   (c) 2005-2017 Danneo Team
 * @link        http://danneo.ru
 * @license     http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('DNREAD') OR die('No direct access');

/**
 * Class Counts
 */
class Counts
{
	private $total = 0;
	private $table = '';
	private $tableid = '';
	private $cats = false;
	private $mult = false;
	private $counts = array();

	public function __construct($table, $tableid, $ins = true)
	{
		global $config, $db, $basepref;

		if (empty($table) OR empty($tableid))
			return false;

		$this->tableid = $tableid;
		$this->table = $table;

		$this->cats = ($db->tables($table."_cats")) ? true : false;
		$this->mult = (isset($config[$this->table]['multcat']) AND $config[$this->table]['multcat'] == 'yes') ? true : false;

		if ($ins)
		{
			if ($this->cats AND $this->mult) {
				$this->multcat();
			} else {
				$this->onlycat();
			}
		}

		$this->acc(0);
	}

	private function onlycat()
	{
		global $db, $basepref;

		$inq = $db->query("SELECT catid FROM ".$basepref."_".$this->table."_cat");
		if ($db->numrows($inq) > 0)
		{
			while ($item = $db->fetchassoc($inq))
			{
				$this->total = 0;
				$count = $db->fetchassoc($db->query("SELECT COUNT(".$this->tableid.") AS total FROM ".$basepref."_".$this->table." WHERE catid='".$item['catid']."' AND act = 'yes'"));
				$intot = (int)($this->level($item['catid']) + $count['total']);
				$db->query("UPDATE ".$basepref."_".$this->table."_cat SET total = '".$intot."' WHERE catid = '".$item['catid']."'");
			}
		}

		$this->acc(0);
	}

	private function level($catid = 0)
	{
		global $db, $basepref;

		$inquiry = $db->query("SELECT * FROM ".$basepref."_".$this->table."_cat WHERE parentid = '".$catid."'");
		if ($db->numrows($inquiry) > 0)
		{
			while ($item = $db->fetchassoc($inquiry))
			{
				$count = $db->fetchassoc($db->query("SELECT COUNT(".$this->tableid.") AS total FROM ".$basepref."_".$this->table." WHERE catid='".$item['catid']."' AND act = 'yes'"));
				$this->total += $count['total'];
				$this->level($item['catid']);
			}
		}
		return $this->total;
	}

	private function multcat()
	{
		global $db, $basepref;

		$_cat = $db->query("SELECT * FROM ".$basepref."_".$this->table."_cat");
		while ($item = $db->fetchassoc($_cat))
		{
			$db->query("UPDATE ".$basepref."_".$this->table."_cat SET total = '0' WHERE catid = '".$item['catid']."'");
		}

		$_cats = $db->query("SELECT * FROM ".$basepref."_".$this->table."_cats");
		while ($items = $db->fetchassoc($_cats))
		{
			$this->counts[] = $items['catid'];
		}

		$this->counts = array_count_values($this->counts);
		foreach ($this->counts as $key => $val)
		{
			$db->query("UPDATE ".$basepref."_".$this->table."_cat SET total = '".$val."' WHERE catid = '".$key."'");
		}

		$this->acc(0);
	}

	public function add($subcat, $catid, $id)
	{
		global $db, $basepref;

		if ($this->mult)
		{
			if ($catid > 0)
			{
				if (is_array($subcat) AND ! empty($subcat))
				{
					if ( ! in_array($catid, $subcat))
						array_unshift($subcat, $catid);

					foreach ($subcat as $cid)
					{
						$db->query("INSERT INTO ".$basepref."_".$this->table."_cats VALUES ('".$id."', '".$cid."')");
					}
				}
				else
				{
					$db->query("INSERT INTO ".$basepref."_".$this->table."_cats VALUES ('".$id."', '".$catid."')");
				}
			}
			$this->multcat();
		}
		else
		{
			$this->onlycat();
		}
	}

	public function edit($subcat, $catid, $id)
	{
		global $db, $basepref;

		if ($this->mult)
		{
			if ($catid > 0)
			{
				$db->query("DELETE FROM ".$basepref."_".$this->table."_cats WHERE id = '".$id."'");
				$db->query("INSERT INTO ".$basepref."_".$this->table."_cats VALUES ('".$id."', '".$catid."')");
				if (is_array($subcat) AND ! empty($subcat))
				{
					foreach ($subcat as $cid)
					{
						if ($catid <> $cid) {
							$db->query("INSERT INTO ".$basepref."_".$this->table."_cats VALUES ('".$id."', '".$cid."')");
						}
					}
				}
			}
			else
			{
				$db->query("DELETE FROM ".$basepref."_".$this->table."_cats WHERE id = '".$id."'");
			}
			$this->multcat();
		}
		else
		{
			$this->onlycat();
		}
	}

	public function act($id, $act)
	{
		global $db, $basepref;

		$array = array();

		$page = $db->fetchassoc($db->query("SELECT act FROM ".$basepref."_".$this->table." WHERE id = '".$id."' LIMIT 1"));
		$cats = $db->query("SELECT * FROM ".$basepref."_".$this->table."_cats WHERE id = '".$id."'");

		while ($item = $db->fetchassoc($cats))
		{
			$array[] = $item['catid'];
		}

		if ($this->mult)
		{
			if ( ! empty($array))
			{
				foreach ($array as $cid)
				{
					if ($act == 'yes' AND $page['act'] == 'no')
					{
						$db->query("UPDATE ".$basepref."_".$this->table."_cat SET total = total + 1 WHERE catid = '".$cid."'");
					}
					elseif ($act == 'no' AND $page['act'] == 'yes')
					{
						$db->query("UPDATE ".$basepref."_".$this->table."_cat SET total = total - 1 WHERE catid = '".$cid."'");
					}
				}
			}
		}
		else
		{
			$this->onlycat();
		}
	}

	public function del($id)
	{
		global $db, $basepref;

		$array = array();
		$cats = $db->query("SELECT * FROM ".$basepref."_".$this->table."_cats WHERE id = '".$id."'");
		$page = $db->fetchassoc($db->query("SELECT act FROM ".$basepref."_".$this->table." WHERE id = '".$id."' LIMIT 1"));

		while ($item = $db->fetchassoc($cats))
		{
			$array[] = $item['catid'];
		}

		if ($this->mult)
		{
			if ( ! empty($array))
			{
				foreach ($array as $cid)
				{
					if ($page['act'] == 'yes')
					{
						$db->query("UPDATE ".$basepref."_".$this->table."_cat SET total = total - 1 WHERE catid = '".$cid."'");
					}
				}
				$db->query("DELETE FROM ".$basepref."_".$this->table."_cats WHERE id = '".$id."'");
			}
		}
		else
		{
			$this->onlycat();
		}
	}

	private function acc($cid = 0)
	{
		global $db, $basepref;

		$inquiry = $db->query("SELECT * FROM ".$basepref."_".$this->table."_cat WHERE parentid = '".$cid."'");
		if ($db->numrows($inquiry) > 0)
		{
			while ($item = $db->fetchassoc($inquiry))
			{
				if ($item['access'] == 'user')
				{
					$db->query("UPDATE ".$basepref."_".$this->table."_cat SET access='".$item['access']."', groups='".$item['groups']."' WHERE parentid='".$item['catid']."'");
				}
				$this->acc($item['catid']);
			}
		}
	}
}
