CREATE TABLE userInfo(
    userId bigint unsigned primary key AUTO_INCREMENT,
    userMail VARCHAR(254) not null,
    userName VARCHAR(256) not null,
    userPasswordHash VARCHAR(60),
    userType tinyint unsigned
);
CREATE TABLE siteInfo(
    siteId bigint unsigned primary key AUTO_INCREMENT,
    siteName VARCHAR(256) not null
);
CREATE TABLE roomInfo(
    roomId bigint unsigned primary key AUTO_INCREMENT,
    roomName VARCHAR(256) not null,
    parentSite bigint unsigned not null,
    roomFloor TINYINT
);
CREATE TABLE caseInfo(
    caseId bigint unsigned primary key AUTO_INCREMENT,
    caseName VARCHAR(256) not null,
    parentRoom bigint unsigned not null
);
CREATE TABLE shelfInfo(
    shelfId bigint unsigned primary key AUTO_INCREMENT,
    shelfNumber tinyint unsigned not null,
    parentCase bigint unsigned not null
);
INSERT INTO siteInfo
VALUES (1, 'System Internal Site');
INSERT INTO roomInfo
VALUES (1, 'System Internal Room', 1, 1);
INSERT INTO caseInfo
VALUES (1, 'System Internal: Temporary Case', 1);
INSERT INTO shelfInfo
VALUES (1, 1, 1);
CREATE TABLE authorInfo(
    authorId bigint unsigned primary key AUTO_INCREMENT,
    authorName VARCHAR(256) not null,
    authorRead VARCHAR(256),
    authorDisambiguation VARCHAR(512)
);
CREATE TABLE publisherInfo(
    publisherId bigint unsigned primary key AUTO_INCREMENT,
    publisherName VARCHAR(512) not null,
    publisherRead VARCHAR(512),
    publisherDisambiguation VARCHAR(512)
);
CREATE TABLE bookAuthorLinker(
    linkId bigint unsigned primary key AUTO_INCREMENT,
    uniqueBookId bigint unsigned not null,
    authorId bigint unsigned not null
);
CREATE TABLE bookCollection(
    uniqueBookId bigint unsigned primary key AUTO_INCREMENT,
    belongShelf bigint unsigned not null,
    bookName VARCHAR(512) not null,
    bookRead VARCHAR(512),
    publisherId bigint unsigned,
    isbn bigint unsigned,
    bookDisambiguation VARCHAR(512)
);