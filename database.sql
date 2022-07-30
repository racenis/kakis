create table users (
id int unsigned auto_increment not null,
username varchar(150) not null,
passwrd varchar(150) not null,
email varchar(150) not null,
accounttype tinyint unsigned not null,
lang char(2) not null,
description nvarchar(500),
nickname nvarchar(30),
emailexist boolean not null,
imageexist boolean not null,
primary key (id));

create table email (
id int unsigned not null,
emailkey int unsigned not null,
foreign key (id) references users (id) on delete cascade,
primary key (id));

create table regions (
id smallint unsigned auto_increment not null,
x tinyint unsigned not null,
y tinyint unsigned not null,
regiontype tinyint unsigned not null,
citycount tinyint unsigned not null,
primary key (id));

create table cities (
id int unsigned auto_increment not null,
regionid smallint unsigned not null,
userid int unsigned not null,
location tinyint unsigned not null,
name nvarchar(30) not null,
foreign key (regionid) references regions (id) on delete cascade,
foreign key (userid) references users (id) on delete cascade,
primary key (id));

create table buildings (
id int unsigned auto_increment not null,
cityid int unsigned not null,
buildingtype tinyint unsigned not null,
buildinglevel tinyint unsigned not null,
buildingnumber int unsigned not null,
location tinyint unsigned not null,
foreign key (cityid) references cities (id) on delete cascade,
primary key (id));

create table resources (
cityid int unsigned not null,
resourcetype tinyint unsigned not null,
ammount int not null,
primary key (cityid, resourcetype),
foreign key (cityid) references cities (id) on delete cascade);

create table messages (
id int unsigned auto_increment not null,
recipient int unsigned not null,
sender int unsigned not null,
subject nvarchar(60) not null,
messagetext text not null,
seen boolean not null,
senddate datetime not null,
foreign key (recipient) references users (id) on delete cascade,
foreign key (sender) references users (id) on delete cascade,
primary key (id));

create table events (
id int unsigned auto_increment not null,
eventtype tinyint unsigned not null,
origin int unsigned not null,
destination int unsigned not null,
starttime datetime not null,
endtime timestamp not null,
options varchar(10) not null,
foreign key (destination) references cities (id) on delete cascade,
foreign key (origin) references cities (id) on delete cascade,
primary key (id));

create table event_resources (
eventid int unsigned not null,
resourcetype tinyint unsigned not null,
ammount int not null,
primary key (eventid, resourcetype),
foreign key (eventid) references events (id) on delete cascade);

create table resource_generation (
buildingid int unsigned not null,
resourcetype tinyint unsigned not null,
ammount int not null,
primary key (buildingid, resourcetype),
foreign key (buildingid) references buildings (id) on delete cascade);

create table notifications (
id int unsigned auto_increment not null,
recipient int unsigned not null,
message varchar(255) not null,
seen boolean not null,
senddate datetime not null,
foreign key (recipient) references users (id) on delete cascade,
primary key (id));

create table timepassed (lasttime int unsigned not null);

insert into regions (x, y, regiontype, citycount) values
(1, 1, 1, 0), (2, 1, 2, 0), (1, 2, 3, 0), (2, 2, 4, 0), (3, 1, 1, 0), (4, 1, 2, 0), (3, 2, 3, 0), 
(4, 2, 4, 0), (1, 3, 1, 0), (2, 3, 2, 0), (1, 4, 3, 0), (2, 4, 4, 0), (5, 2, 1, 0), (6, 2, 2, 0), 
(5, 3, 3, 0), (6, 3, 4, 0), (2, 6, 1, 0), (3, 6, 2, 0), (2, 7, 3, 0), (3, 7, 4, 0);

INSERT INTO `resources`(`cityid`, `resourcetype`, `ammount`) VALUES (2, 1, 10), (2, 2, 25), (2, 3, 50), (2, 4, 200), (2, 5, 30), (2, 6, 20), (2, 7, 15)

insert into users (username, passwrd, email, accounttype, lang, description, nickname, emailexist, imageexist) values ('admin', 'admin', 'admin@example.com', 0, 'lv', 'administrator', 'big boss', 0, 0);


update regions join (select regionid, count(regionid) as olas from cities group by regionid) as janis on janis.regionid = regions.id set citycount = janis.olas where regions.id = janis.regionid;