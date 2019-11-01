-- User
-- Table which contains users credentials.
DROP TABLE IF EXISTS User;
CREATE TABLE User (
	id		INT UNSIGNED NOT NULL AUTO_INCREMENT,
	admin		BOOLEAN NOT NULL DEFAULT FALSE,
	name		TINYTEXT NOT NULL,
	email		TINYTEXT NOT NULL,
	password	TINYTEXT NOT NULL,
	creationDate	DATETIME NOT NULL,
	modifDate	TIMESTAMP NOT NULL,
	PRIMARY KEY (id),
	UNIQUE INDEX email (email(40)),
	INDEX password (password(10)),
	INDEX creationDate (creationDate),
	INDEX modifDate (modifDate)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 DEFAULT COLLATE utf8mb4_general_ci;

-- Page
-- Table which contains each page definition and content.
DROP TABLE IF EXISTS Page;
CREATE TABLE Page (
	id			INT UNSIGNED NOT NULL AUTO_INCREMENT,
	title			TINYTEXT NOT NULL,
	html			MEDIUMTEXT,
	creationDate		DATETIME NOT NULL,
	modifDate		TIMESTAMP NOT NULL,
	creatorId		INT UNSIGNED NOT NULL DEFAULT '0',
	priority		TINYINT UNSIGNED NOT NULL DEFAULT '0',
	parentPageId		INT UNSIGNED NOT NULL DEFAULT '0',
	currentVersionId	INT UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (id),
	INDEX parentPageId (parentPageId)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 DEFAULT COLLATE utf8mb4_general_ci;

-- PageVersion
-- Contains information about each version of a page.
DROP TABLE IF EXISTS PageVersion;
CREATE TABLE PageVersion (
	id		INT UNSIGNED NOT NULL AUTO_INCREMENT,
	title		TINYTEXT NOT NULL,
	skriv		MEDIUMTEXT,
	creationDate	DATETIME NOT NULL,
	creatorId	INT UNSIGNED NOT NULL DEFAULT '0',
	pageId		INT UNSIGNED NOT NULL DEFAULT '0',
	PRIMARY KEY (id),
	INDEX pageId (pageId)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 DEFAULT COLLATE utf8mb4_general_ci;

-- Subscription
-- Used when a user wants to be warn when a page is modified.
DROP TABLE IF EXISTS Subscription;
CREATE TABLE Subscription (
	id		INT UNSIGNED NOT NULL AUTO_INCREMENT,
	userId		INT UNSIGNED NOT NULL DEFAULT '0',
	pageId		INT UNSIGNED NOT NULL DEFAULT '0',
	createDate	DATETIME NOT NULL,
	PRIMARY KEY (id),
	INDEX userId (userId),
	INDEX pageId (pageId),
	UNIQUE INDEX userId_pageId (userId, pageId)
) ENGINE=InnoDB DEFAULT CHARSET utf8mb4 DEFAULT COLLATE utf8mb4_general_ci;

