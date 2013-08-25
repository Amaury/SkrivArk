-- MySQL dump 10.13  Distrib 5.5.29, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: skrivark
-- ------------------------------------------------------
-- Server version	5.5.29-0ubuntu0.12.04.2-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Page`
--

DROP TABLE IF EXISTS `Page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Page` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `html` mediumtext,
  `creationDate` datetime NOT NULL,
  `modifDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `creatorId` int(10) unsigned NOT NULL DEFAULT '0',
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `parentPageId` int(10) unsigned NOT NULL DEFAULT '0',
  `currentVersionId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parentPageId` (`parentPageId`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Page`
--

LOCK TABLES `Page` WRITE;
/*!40000 ALTER TABLE `Page` DISABLE KEYS */;
INSERT INTO `Page` VALUES (1,'SkrivArk Documentation','<p>SkrivArk is a simple web-software, perfect to write hierarchical pages of formatted text.</p>\n\n<p>It\'s pretty useful to keep track of simple notes, without any trouble nor disturbance. Each page can be edited easily, and can contain sub-pages.</p>\n\n<p>By the way, it demonstrates the Skriv Markup Language capabilities.</p>','2013-03-20 13:54:12','2013-03-20 12:54:12',1,0,0,1),(2,'Wikipedia','<p>Just for fun, here a some articles taken from Wikipedia.</p>','2013-03-20 13:54:30','2013-03-20 12:54:30',1,0,0,2),(3,'History of computing hardware','<h2 id=\"Earliest-true-hardware\">Earliest true hardware</h2>\n<p>The <a href=\"http://en.wikipedia.org/wiki/Abacus\" target=\"_blank\" rel=\"nofollow\">abacus</a> was early used for arithmetic tasks. What we now call the <a href=\"http://en.wikipedia.org/wiki/Roman_abacus\" target=\"_blank\" rel=\"nofollow\">Roman abacus</a> was used in <a href=\"http://en.wikipedia.org/wiki/Babylonia\" target=\"_blank\" rel=\"nofollow\">Babylonia</a> as early as 2400 BC.</p>\n\n<p><img src=\"http://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Abacus_6.png/220px-Abacus_6.png\" alt=\"http://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Abacus_6.png/220px-Abacus_6.png\" /></p>\n\n<p>Several <a href=\"http://en.wikipedia.org/wiki/Analog_computer\" target=\"_blank\" rel=\"nofollow\">analog computers</a> were constructed in ancient and medieval times to perform astronomical calculations. These include the <a href=\"http://en.wikipedia.org/wiki/Antikythera_mechanism\" target=\"_blank\" rel=\"nofollow\">Antikythera mechanism</a> and the <a href=\"http://en.wikipedia.org/wiki/Astrolabe\" target=\"_blank\" rel=\"nofollow\">astrolabe</a> from <a href=\"http://en.wikipedia.org/wiki/Ancient_Greece\" target=\"_blank\" rel=\"nofollow\">ancient Greece</a> (c. 150–100 BC), which are generally regarded as the earliest known mechanical analog computers.</p>\n\n<p>In Japan, <a href=\"http://en.wikipedia.org/wiki/Ry%C5%8Dichi_Yazu\" target=\"_blank\" rel=\"nofollow\">Ryōichi Yazu</a> patented a mechanical calculator called the Yazu Arithmometer in 1903. It consisted of a single cylinder and 22 gears, and employed the mixed base-2 and base-5 number system familiar to users to the <a href=\"http://en.wikipedia.org/wiki/Soroban\" target=\"_blank\" rel=\"nofollow\">soroban</a> (Japanese abacus).</p>\n\n\n<h2 id=\"1801-punched-card-technology\">1801: punched card technology</h2>\n<ul>\n<li>In 1801, <a href=\"http://en.wikipedia.org/wiki/Joseph_Marie_Jacquard\" target=\"_blank\" rel=\"nofollow\">Joseph-Marie Jacquard</a> developed <a href=\"http://en.wikipedia.org/wiki/Jacquard_loom\" target=\"_blank\" rel=\"nofollow\">a loom</a> in which the pattern being woven was controlled by <a href=\"http://en.wikipedia.org/wiki/Punched_cards\" target=\"_blank\" rel=\"nofollow\">punched cards</a>.\n</li>\n<li>In 1833, <a href=\"http://en.wikipedia.org/wiki/Charles_Babbage\" target=\"_blank\" rel=\"nofollow\">Charles Babbage</a> moved on from developing his <a href=\"http://en.wikipedia.org/wiki/Difference_engine\" target=\"_blank\" rel=\"nofollow\">difference engine</a> (for navigational calculations) to a general purpose design, the Analytical Engine, which drew directly on Jacquard\'s punched cards for its program storage.\n</li>\n<li>In 1837, Babbage described his <a href=\"http://en.wikipedia.org/wiki/Analytical_engine\" target=\"_blank\" rel=\"nofollow\">analytical engine</a>. It was a general-purpose programmable computer, employing punch cards for input and a steam engine for power, using the positions of gears and shafts to represent numbers.</li></ul>\n\n\n\n<h2 id=\"Early-computer-characteristics\">Early computer characteristics</h2>\n<table class=\"bordered\"><tr><th>Name</th><th>Country</th><th>Date</th><th>System</th><th>Mechanism</th></tr>\n<tr><td>Zuse Z3</td><td>Ger.</td><td>May 1941</td><td>Bin. float pt</td><td>Electro-mech</td></tr>\n<tr><td>Colossus Mk 1</td><td>UK</td><td>Feb 1944</td><td>Binary</td><td>Electronic</td></tr>\n<tr><td>ENIAC</td><td>US</td><td>Jul 1946</td><td>Decimal</td><td>Electronic</td></tr></table>\n\n<p><img src=\"http://upload.wikimedia.org/wikipedia/commons/thumb/8/84/Von_Neumann_architecture.svg/220px-Von_Neumann_architecture.svg.png\" alt=\"http://upload.wikimedia.org/wikipedia/commons/thumb/8/84/Von_Neumann_architecture.svg/220px-Von_Neumann_architecture.svg.png\" /></p>\n\n<h2 id=\"See-also\">See also</h2>\n<ul>\n<li><a href=\"http://en.wikipedia.org/wiki/History_of_computing\" target=\"_blank\" rel=\"nofollow\">History of computing</a>\n</li>\n<li><a href=\"http://en.wikipedia.org/wiki/Information_Age\" target=\"_blank\" rel=\"nofollow\">Information Age</a>\n</li>\n<li><a href=\"http://en.wikipedia.org/wiki/IT_History_Society\" target=\"_blank\" rel=\"nofollow\">IT History Society</a>\n</li>\n<li><a href=\"http://en.wikipedia.org/wiki/The_Secret_Guide_to_Computers\" target=\"_blank\" rel=\"nofollow\">The Secret Guide to Computers</a>\n</li>\n<li><a href=\"http://en.wikipedia.org/wiki/Timeline_of_computing\" target=\"_blank\" rel=\"nofollow\">Timeline of computing</a></li></ul>\n\n','2013-03-20 13:54:46','2013-03-20 15:32:00',1,2,2,3),(4,'Skriv Markup Language','<h2 id=\"What-is-Skriv-Markup-Language\">What is Skriv Markup Language?</h2>\n<p>It\'s a lightweight markup language. Like Creole, Markdown, Textile, reStructuredText, and many wiki engines.</p>\n\n<ul>\n<li><a href=\"http://markup.skriv.org\" target=\"_blank\" rel=\"nofollow\">Main site</a>\n</li>\n<li><a href=\"http://markup.skriv.org/language/syntax\" target=\"_blank\" rel=\"nofollow\">Full syntax</a></li></ul>\n\n\n<h2 id=\"How-to-use-it\">How to use it?</h2>\n<ol>\n<li>With <a href=\"http://ark.skriv.org\" target=\"_blank\" rel=\"nofollow\">SkrivArk</a>\n</li>\n<li>Through <a href=\"http://skriv.io\" target=\"_blank\" rel=\"nofollow\">Skriv.io API</a>\n</li>\n<li>Integrating the <a href=\"http://markup.skriv.org/php/usage\" target=\"_blank\" rel=\"nofollow\">SkrivMarkup PHP library</a> in your own software</li></ol>\n\n','2013-03-20 13:55:14','2013-03-20 12:55:14',1,0,0,4),(5,'Who is using Skriv Markup?','<ul>\n<li><a href=\"http://docs.atoum.org\" target=\"_blank\" rel=\"nofollow\">Atoum unit test framework\'s documentation</a>\n</li>\n<li><a href=\"http://ark.skriv.org\" target=\"_blank\" rel=\"nofollow\">SkrivArk</a>\n</li>\n<li><a href=\"http://skriv.io\" target=\"_blank\" rel=\"nofollow\">Skriv.io</a> webservice</li></ul>\n','2013-03-20 13:55:45','2013-03-20 12:55:45',1,0,4,5),(6,'Some links','<ul>\n<li><a href=\"http://markup.skriv.org\" target=\"_blank\" rel=\"nofollow\">Skriv Markup website</a>\n</li>\n<li><a href=\"http://github.com/Amaury/SkrivMarkup\" target=\"_blank\" rel=\"nofollow\">SkrivMarkup project</a> on GitHub\n</li>\n<li><a href=\"http://markup.skriv.org/online/try\" target=\"_blank\" rel=\"nofollow\">Try SkrivML online</a>\n</li>\n<li><a href=\"http://skriv.io\" target=\"_blank\" rel=\"nofollow\">Skriv.io API</a></li></ul>\n','2013-03-20 13:56:02','2013-03-20 12:56:02',1,0,4,6),(7,'Some links about Bootstrap','<ul>\n<li><a href=\"http://twitter.github.com/bootstrap/\" target=\"_blank\" rel=\"nofollow\">Official site</a>\n</li>\n<li><a href=\"https://wrapbootstrap.com/\" target=\"_blank\" rel=\"nofollow\">{wrap}bootstrap</a>, themes from $6\n</li>\n<li><a href=\"http://bootswatch.com/\" target=\"_blank\" rel=\"nofollow\">Bootswatch</a>, free (as in free beer <em>and</em> as in free speech) themes\n</li>\n<li><a href=\"http://stylebootstrap.info/\" target=\"_blank\" rel=\"nofollow\">StyleBootstrap</a>, create your own theme\n</li>\n<li><a href=\"http://jhollingworth.github.com/bootstrap-wysihtml5/\" target=\"_blank\" rel=\"nofollow\">bootstrap-wysihtml5</a>, bootstrap WYSIWYG editor\n</li>\n<li><a href=\"http://fortawesome.github.com/Font-Awesome/\" target=\"_blank\" rel=\"nofollow\">FontAwesome</a>, font containing 250 icons\n</li>\n<li><a href=\"http://addyosmani.github.com/jquery-ui-bootstrap/\" target=\"_blank\" rel=\"nofollow\">jQuery UI Bootstrap</a>, jQuery-UI with Bootstrap theme\n</li>\n<li><a href=\"http://bootstrapwp.rachelbaker.me/\" target=\"_blank\" rel=\"nofollow\">Bootstrap WP</a>, Bootstrap theme for WordPress\n</li>\n<li><a href=\"http://ckrack.github.com/fbootstrapp/\" target=\"_blank\" rel=\"nofollow\">fbootstrapp</a>, a Facebook-like theme for Bootstrap\n</li>\n<li><a href=\"http://www.boottheme.com/\" target=\"_blank\" rel=\"nofollow\">BootTheme</a>, a theme generator</li></ul>\n','2013-03-20 13:56:21','2013-03-20 12:56:21',1,0,0,7);
/*!40000 ALTER TABLE `Page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PageVersion`
--

DROP TABLE IF EXISTS `PageVersion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PageVersion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `skriv` mediumtext,
  `creationDate` datetime NOT NULL,
  `creatorId` int(10) unsigned NOT NULL DEFAULT '0',
  `pageId` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pageId` (`pageId`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PageVersion`
--

LOCK TABLES `PageVersion` WRITE;
/*!40000 ALTER TABLE `PageVersion` DISABLE KEYS */;
INSERT INTO `PageVersion` VALUES (1,'SkrivArk Documentation','SkrivArk is a simple web-software, perfect to write hierarchical pages of formatted text.\r\n\r\nIt\'s pretty useful to keep track of simple notes, without any trouble nor disturbance. Each page can be edited easily, and can contain sub-pages.\r\n\r\nBy the way, it demonstrates the Skriv Markup Language capabilities.','2013-03-20 13:54:12',1,1),(2,'Wikipedia','Just for fun, here a some articles taken from Wikipedia.','2013-03-20 13:54:30',1,2),(3,'History of computing hardware','=Earliest true hardware\r\nThe [[abacus|http://en.wikipedia.org/wiki/Abacus]] was early used for arithmetic tasks. What we now call the [[Roman abacus|http://en.wikipedia.org/wiki/Roman_abacus]] was used in [[Babylonia|http://en.wikipedia.org/wiki/Babylonia]] as early as 2400 BC.\r\n\r\n{{http://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Abacus_6.png/220px-Abacus_6.png}}\r\n\r\nSeveral [[analog computers|http://en.wikipedia.org/wiki/Analog_computer]] were constructed in ancient and medieval times to perform astronomical calculations. These include the [[Antikythera mechanism|http://en.wikipedia.org/wiki/Antikythera_mechanism]] and the [[astrolabe|http://en.wikipedia.org/wiki/Astrolabe]] from [[ancient Greece|http://en.wikipedia.org/wiki/Ancient_Greece]] (c. 150–100 BC), which are generally regarded as the earliest known mechanical analog computers.\r\n\r\nIn Japan, [[Ryōichi Yazu|http://en.wikipedia.org/wiki/Ry%C5%8Dichi_Yazu]] patented a mechanical calculator called the Yazu Arithmometer in 1903. It consisted of a single cylinder and 22 gears, and employed the mixed base-2 and base-5 number system familiar to users to the [[soroban|http://en.wikipedia.org/wiki/Soroban]] (Japanese abacus).\r\n\r\n\r\n=1801: punched card technology\r\n* In 1801, [[Joseph-Marie Jacquard|http://en.wikipedia.org/wiki/Joseph_Marie_Jacquard]] developed [[a loom|http://en.wikipedia.org/wiki/Jacquard_loom]] in which the pattern being woven was controlled by [[punched cards|http://en.wikipedia.org/wiki/Punched_cards]].\r\n* In 1833, [[Charles Babbage|http://en.wikipedia.org/wiki/Charles_Babbage]] moved on from developing his [[difference engine|http://en.wikipedia.org/wiki/Difference_engine]] (for navigational calculations) to a general purpose design, the Analytical Engine, which drew directly on Jacquard\'s punched cards for its program storage.\r\n* In 1837, Babbage described his [[analytical engine|http://en.wikipedia.org/wiki/Analytical_engine]]. It was a general-purpose programmable computer, employing punch cards for input and a steam engine for power, using the positions of gears and shafts to represent numbers.\r\n\r\n\r\n=Early computer characteristics\r\n!! Name          !! Country !! Date     !! System        !! Mechanism\r\n|| Zuse Z3       || Ger.    || May 1941 || Bin. float pt || Electro-mech\r\n|| Colossus Mk 1 || UK      || Feb 1944 || Binary        || Electronic\r\n|| ENIAC         || US      || Jul 1946 || Decimal       || Electronic\r\n\r\n{{http://upload.wikimedia.org/wikipedia/commons/thumb/8/84/Von_Neumann_architecture.svg/220px-Von_Neumann_architecture.svg.png}}\r\n\r\n=See also\r\n* [[History of computing|http://en.wikipedia.org/wiki/History_of_computing]]\r\n* [[Information Age|http://en.wikipedia.org/wiki/Information_Age]]\r\n* [[IT History Society|http://en.wikipedia.org/wiki/IT_History_Society]]\r\n* [[The Secret Guide to Computers|http://en.wikipedia.org/wiki/The_Secret_Guide_to_Computers]]\r\n* [[Timeline of computing|http://en.wikipedia.org/wiki/Timeline_of_computing]]\r\n','2013-03-20 13:54:45',1,3),(4,'Skriv Markup Language','=What is Skriv Markup Language?\r\nIt\'s a lightweight markup language. Like Creole, Markdown, Textile, reStructuredText, and many wiki engines.\r\n\r\n* [[Main site | http://markup.skriv.org]]\r\n* [[Full syntax | http://markup.skriv.org/language/syntax]]\r\n\r\n=How to use it?\r\n# With [[SkrivArk | http://ark.skriv.org]]\r\n# Through [[Skriv.io API | http://skriv.io]]\r\n# Integrating the [[SkrivMarkup PHP library | http://markup.skriv.org/php/usage]] in your own software\r\n','2013-03-20 13:55:14',1,4),(5,'Who is using Skriv Markup?','* [[Atoum unit test framework\'s documentation | http://docs.atoum.org]]\r\n* [[SkrivArk | http://ark.skriv.org]]\r\n* [[Skriv.io | http://skriv.io]] webservice','2013-03-20 13:55:45',1,5),(6,'Some links','* [[Skriv Markup website|http://markup.skriv.org]]\r\n* [[SkrivMarkup project|http://github.com/Amaury/SkrivMarkup]] on GitHub\r\n* [[Try SkrivML online|http://markup.skriv.org/online/try]]\r\n* [[Skriv.io API|http://skriv.io]]','2013-03-20 13:56:02',1,6),(7,'Some links about Bootstrap','* [[Official site | http://twitter.github.com/bootstrap/]]\r\n* [[{wrap}bootstrap | https://wrapbootstrap.com/]], themes from $6\r\n* [[Bootswatch | http://bootswatch.com/]], free (as in free beer \'\'and\'\' as in free speech) themes\r\n* [[StyleBootstrap | http://stylebootstrap.info/]], create your own theme\r\n* [[bootstrap-wysihtml5 | http://jhollingworth.github.com/bootstrap-wysihtml5/]], bootstrap WYSIWYG editor\r\n* [[FontAwesome | http://fortawesome.github.com/Font-Awesome/]], font containing 250 icons\r\n* [[jQuery UI Bootstrap | http://addyosmani.github.com/jquery-ui-bootstrap/]], jQuery-UI with Bootstrap theme\r\n* [[Bootstrap WP | http://bootstrapwp.rachelbaker.me/]], Bootstrap theme for WordPress\r\n* [[fbootstrapp | http://ckrack.github.com/fbootstrapp/]], a Facebook-like theme for Bootstrap\r\n* [[BootTheme | http://www.boottheme.com/]], a theme generator','2013-03-20 13:56:21',1,7);
/*!40000 ALTER TABLE `PageVersion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `name` tinytext NOT NULL,
  `email` tinytext NOT NULL,
  `password` tinytext NOT NULL,
  `creationDate` datetime NOT NULL,
  `modifDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`(40)),
  KEY `password` (`password`(10)),
  KEY `creationDate` (`creationDate`),
  KEY `modifDate` (`modifDate`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES (1,1,'Demo User','demo@demo.com','fe01ce2a7fbac8fafaed7c982a04e229','2013-03-20 12:54:39','2013-03-20 15:11:11'), (2, 0, 'Test User', 'test@test.com', '098f6bcd4621d373cade4e832627b4f6', '2013-03-21 11:43:56', '2013-03-21 11:43:56');
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-03-20 16:32:17
