CREATE TABLE `tx_oclock_reminder` (
    uid INT(11) unsigned auto_increment NOT NULL,
    pid INT(11) NOT NULL DEFAULT '0',
    user INT(11) unsigned NOT NULL,
    message text,
    `datetime` INT(11) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY(uid),
    KEY parent(pid),
    KEY user(user)
);
