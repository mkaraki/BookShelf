CREATE TABLE userInfo(
    userId bigint unsigned primary key AUTOINCREMENT,
    userMail VARCHAR(254) not null,
    userName VARCHAR(256) not null,
    userPasswordHash VARCHAR(60),
    userType tinyint unsigned DEFAULT(0)
);
CREATE TABLE siteInfo(
    siteId bigint unsigned primary key AUTOINCREMENT,
    siteName VARCHAR(256) not null
);
CREATE TABLE roomInfo(
    roomId bigint unsigned primary key AUTOINCREMENT,
    roomName VARCHAR(256) not null,
    parentSite bigint unsigned not null,
    roomFloor TINYINT
);
CREATE TABLE caseInfo(
    caseId bigint unsigned primary key AUTOINCREMENT,
    caseName VARCHAR(256) not null,
    parentRoom bigint unsigned not null
);
CREATE TABLE shelfInfo(
    shelfId bigint unsigned primary key AUTOINCREMENT,
    shelfNumber tinyint unsigned not null,
    parentCase bigint unsigned not null
);
INSERT INTO siteInfo
VALUES (0, 'System Internal Site');
INSERT INTO roomInfo
VALUES (0, 'System Internal Room', 0, 1);
INSERT INTO caseInfo
VALUES (0, 'System Internal: Temporary Case', 0);
INSERT INTO shelfInfo
VALUES (0, 0, 0);
CREATE TABLE editionInfo(
    editionId bigint unsigned primary key AUTOINCREMENT,
    bookName VARCHAR(512) not null,
    publisherInfo bigint unsigned,
    bookDisambiguation VARCHAR(512)
);
CREATE TABLE authorInfo(
    authorId bigint unsigned primary key AUTOINCREMENT,
    authorName VARCHAR(256) not null,
    authorRead VARCHAR(256),
    authorDisambiguation VARCHAR(512),
);
CREATE TABLE publisherInfo(
    publisherId bigint unsigned primary key AUTOINCREMENT,
    publisherName VARCHAR(512) not null,
    publisherRead VARCHAR(512),
    publisherDisambiguation VARCHAR(512)
);
CREATE TABLE editionAuthorLinker(
    linkId bigint unsigned primary key AUTOINCREMENT,
    editionId bigint unsigned not null,
    authorId bigint unsigned not null
);
CREATE TABLE bookCollection(
    uniqueBookId bigint unsigned primary key AUTOINCREMENT,
    editionId bigint unsigned primary key AUTOINCREMENT,
    belongShelf bigint unsigned not null
);