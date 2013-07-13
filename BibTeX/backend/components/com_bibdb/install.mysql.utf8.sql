DROP TABLE IF EXISTS `#__bibdb_bibtex`;

CREATE TABLE `#__bibdb_bibtex` (
  `id`				INT(11) UNSIGNED AUTO_INCREMENT NOT NULL,
  `catid`			INT(11) NOT NULL DEFAULT 0,
  `bibtexentrytype`	VARCHAR(15),
  `bibtexcitation`	VARCHAR(100) CHARACTER SET LATIN1,
  `title`			TEXT,
  `year`			YEAR(4),
  `month`			VARCHAR(15),
  `note`			VARCHAR(255),
  `keywords`		VARCHAR(255),
  `abstract`		TEXT,
  `language`		VARCHAR(25),
  `isbn`			VARCHAR(20),
  `url`				VARCHAR(255),
  `contents`		TEXT,
  `series`			VARCHAR(255),
  `institution`		VARCHAR(255),
  `organization`	VARCHAR(255),
  `school`			VARCHAR(255),
  `address`			VARCHAR(255),
  `journal`			VARCHAR(255),
  `volume`			VARCHAR(20),
  `number`			VARCHAR(20),
  `pages`			VARCHAR(20),
  `chapter`			VARCHAR(20),
  `issn`			VARCHAR(60),
  `author`			TEXT,
  `affiliation`		VARCHAR(100),
  `editor`			VARCHAR(255),
  `publisher`		VARCHAR(255),
  `edition`			VARCHAR(50),
  `howpublished`	VARCHAR(255),
  `booktitle`		VARCHAR(255),
  `annote`			TEXT,
  `detalles`		TEXT,
  `path`			VARCHAR(255),
  `fechaalta`		DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering`		INT(11) NOT NULL DEFAULT 0,
  `published`		TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `catid` (`catid`)
) ENGINE=MyISAM CHARACTER SET utf8;