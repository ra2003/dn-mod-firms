DROP TABLE IF EXISTS {pref}_{mod};
--
CREATE TABLE {pref}_{mod} (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  catid int(11) unsigned NOT NULL DEFAULT '0',
  letid int(11) unsigned NOT NULL DEFAULT '0',
  public int(11) unsigned NOT NULL DEFAULT '0',
  stpublic int(11) unsigned NOT NULL DEFAULT '0',
  unpublic int(11) unsigned NOT NULL DEFAULT '0',
  cpu varchar(255) NOT NULL DEFAULT '',
  title varchar(255) NOT NULL DEFAULT '',
  subtitle varchar(255) NOT NULL DEFAULT '',
  customs varchar(255) NOT NULL DEFAULT '',
  descript text NOT NULL,
  keywords text NOT NULL,
  textshort text NOT NULL,
  textmore longtext NOT NULL,
  textnotice text NOT NULL,
  reason text NOT NULL,
  image varchar(255) NOT NULL DEFAULT '',
  image_thumb varchar(255) NOT NULL DEFAULT '',
  image_align enum('left','right') NOT NULL DEFAULT 'left',
  image_alt varchar(255) NOT NULL DEFAULT '',
  images text NOT NULL,
  country varchar(255) NOT NULL,
  region varchar(255) NOT NULL,
  city varchar(255) NOT NULL,
  address text NOT NULL,
  author varchar(255) NOT NULL DEFAULT '',
  person varchar(255) NOT NULL DEFAULT '',
  phone text NOT NULL,
  site varchar(255) NOT NULL DEFAULT '',
  email varchar(255) NOT NULL DEFAULT '',
  skype varchar(32) NOT NULL,
  reviews int(11) NOT NULL DEFAULT '0',
  photos int(11) NOT NULL DEFAULT '0',
  videos int(11) NOT NULL DEFAULT '0',
  files text NOT NULL,
  facc enum('all','user') NOT NULL DEFAULT 'user',
  fgroups text NOT NULL,
  acc enum('all','user') NOT NULL DEFAULT 'all',
  groups text NOT NULL,
  hits int(11) unsigned NOT NULL DEFAULT '0',
  act enum('yes','no') NOT NULL DEFAULT 'yes',
  vip smallint(1) unsigned NOT NULL DEFAULT '0',
  rating int(11) NOT NULL DEFAULT '0',
  totalrating int(11) NOT NULL DEFAULT '0',
  tags varchar(255) NOT NULL DEFAULT '',
  social text NOT NULL,
  userid int(11) unsigned NOT NULL DEFAULT '0',
  posit int(11) unsigned NOT NULL DEFAULT '0',
  pid smallint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY catid (catid),
  KEY cpu (cpu),
  KEY public (public),
  KEY stpublic (stpublic),
  KEY unpublic (unpublic),
  KEY tags (tags),
  KEY act (act),
  KEY vip (vip),
  KEY pid (pid)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--

DROP TABLE IF EXISTS {pref}_{mod}_cat;
--
CREATE TABLE {pref}_{mod}_cat (
  catid int(11) unsigned NOT NULL AUTO_INCREMENT,
  parentid int(11) unsigned NOT NULL DEFAULT '0',
  catcpu varchar(255) NOT NULL DEFAULT '',
  catname varchar(255) NOT NULL DEFAULT '',
  subtitle varchar(255) NOT NULL DEFAULT '',
  catdesc text NOT NULL,
  catcustom text NOT NULL,
  keywords text NOT NULL,
  descript text NOT NULL,
  posit int(11) unsigned NOT NULL DEFAULT '0',
  icon varchar(255) NOT NULL DEFAULT '',
  access enum('all','user') NOT NULL DEFAULT 'all',
  groups text NOT NULL,
  sort varchar(11) NOT NULL DEFAULT 'id',
  ord enum('asc','desc') NOT NULL DEFAULT 'asc',
  rss enum('yes','no') NOT NULL DEFAULT 'yes',
  total int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (catid),
  KEY parentid (parentid),
  KEY catcpu (catcpu),
  KEY access (access),
  KEY rss (rss)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--

DROP TABLE IF EXISTS {pref}_{mod}_cats;
--
CREATE TABLE {pref}_{mod}_cats (
  id int(11) unsigned NOT NULL DEFAULT '0',
  catid int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY id (id, catid)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--

DROP TABLE IF EXISTS {pref}_{mod}_photo;
--
CREATE TABLE IF NOT EXISTS {pref}_{mod}_photo (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  firm_id int(11) unsigned NOT NULL DEFAULT '0',
  public int(11) unsigned NOT NULL DEFAULT '0',
  title varchar(255) NOT NULL DEFAULT '',
  descript text NOT NULL,
  image varchar(255) NOT NULL DEFAULT '',
  image_thumb varchar(255) NOT NULL DEFAULT '',
  image_alt varchar(255) NOT NULL DEFAULT '',
  posit int(11) unsigned NOT NULL DEFAULT '0',
  act enum('yes','no') NOT NULL,
  PRIMARY KEY (id),
  KEY firm_id (firm_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--

DROP TABLE IF EXISTS {pref}_{mod}_search;
--
CREATE TABLE {pref}_{mod}_search (
  seaid int(11) unsigned NOT NULL AUTO_INCREMENT,
  seaword varchar(255) NOT NULL DEFAULT '',
  seaip varchar(255) NOT NULL DEFAULT '',
  seatime int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (seaid),
  KEY seaip (seaip),
  KEY seatime (seatime)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--

DROP TABLE IF EXISTS {pref}_{mod}_tag;
--
CREATE TABLE {pref}_{mod}_tag (
  tagid int(11) unsigned NOT NULL AUTO_INCREMENT,
  tagcpu varchar(255) NOT NULL DEFAULT '',
  tagword varchar(255) NOT NULL DEFAULT '',
  tagdesc text NOT NULL,
  custom text NOT NULL,
  descript text NOT NULL,
  keywords text NOT NULL,
  icon varchar(255) NOT NULL DEFAULT '',
  tagrating smallint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (tagid),
  KEY tagrating (tagrating),
  KEY tagcpu (tagcpu)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--

DROP TABLE IF EXISTS {pref}_{mod}_user;
--
CREATE TABLE {pref}_{mod}_user (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  catid int(11) unsigned NOT NULL DEFAULT '0',
  cats varchar(20) NOT NULL DEFAULT '',
  title varchar(125) NOT NULL DEFAULT '',
  cpu varchar(125) NOT NULL DEFAULT '',
  public int(11) unsigned NOT NULL DEFAULT '0',
  stpublic int(11) unsigned NOT NULL DEFAULT '0',
  unpublic int(11) unsigned NOT NULL DEFAULT '0',
  textshort text NOT NULL,
  textmore text NOT NULL,
  country varchar(30) NOT NULL DEFAULT '',
  region varchar(50) NOT NULL DEFAULT '',
  city varchar(30) NOT NULL DEFAULT '',
  address varchar(125) NOT NULL DEFAULT '',
  author varchar(30) NOT NULL DEFAULT '',
  person varchar(64) NOT NULL DEFAULT '',
  email varchar(64) NOT NULL DEFAULT '',
  phone varchar(20) NOT NULL DEFAULT '',
  site varchar(125) NOT NULL DEFAULT '',
  skype varchar(32) NOT NULL,
  image varchar(125) NOT NULL DEFAULT '',
  image_thumb varchar(125) NOT NULL DEFAULT '',
  files text NOT NULL,
  social text NOT NULL,
  tags varchar(255) NOT NULL DEFAULT '',
  addip char(14) NOT NULL DEFAULT '',
  userid int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY public (public),
  KEY addip (addip)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--

DROP TABLE IF EXISTS {pref}_{mod}_video;
--
CREATE TABLE IF NOT EXISTS {pref}_{mod}_video (
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  firm_id int(11) unsigned NOT NULL DEFAULT '0',
  public int(11) unsigned NOT NULL DEFAULT '0',
  title varchar(255) NOT NULL DEFAULT '',
  descript text NOT NULL,
  link varchar(255) NOT NULL DEFAULT '',
  video varchar(255) NOT NULL DEFAULT '',
  image varchar(255) NOT NULL DEFAULT '',
  posit int(11) unsigned NOT NULL DEFAULT '0',
  act enum('yes','no') NOT NULL,
  PRIMARY KEY (id),
  KEY firm_id (firm_id)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;