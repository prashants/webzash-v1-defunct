CREATE TABLE IF NOT EXISTS groups (
  id int(11) NOT NULL AUTO_INCREMENT,
  parent_id int(11) NOT NULL,
  name varchar(100) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY id (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS ledgers (
  id int(11) NOT NULL AUTO_INCREMENT,
  group_id int(11) NOT NULL,
  name varchar(100) NOT NULL,
  op_balance decimal(15,2) NOT NULL DEFAULT '0.00',
  op_balance_dc char(1) NOT NULL,
  type char(1) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY id (id),
  UNIQUE KEY id_2 (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS settings (
  id int(1) NOT NULL,
  label varchar(255) NOT NULL,
  name varchar(100) NOT NULL,
  address varchar(255) NOT NULL,
  email varchar(100) NOT NULL,
  ay_start datetime NOT NULL,
  ay_end datetime NOT NULL,
  currency_symbol varchar(10) NOT NULL,
  date_format varchar(30) NOT NULL,
  timezone varchar(255) NOT NULL,
  database_version int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS vouchers (
  id int(11) NOT NULL AUTO_INCREMENT,
  number int(11) NOT NULL,
  date datetime NOT NULL,
  dr_total decimal(15,2) NOT NULL DEFAULT '0.00',
  cr_total decimal(15,2) NOT NULL DEFAULT '0.00',
  narration text NOT NULL,
  draft int(1) NOT NULL,
  type int(2) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS voucher_items (
  id int(11) NOT NULL AUTO_INCREMENT,
  voucher_id int(11) NOT NULL,
  ledger_id int(11) NOT NULL,
  amount decimal(15,2) NOT NULL DEFAULT '0.00',
  dc char(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO groups (id, parent_id, name) VALUES (1, 0, 'Asset');
INSERT INTO groups (id, parent_id, name) VALUES (2, 0, 'Liability');
INSERT INTO groups (id, parent_id, name) VALUES (3, 0, 'Income');
INSERT INTO groups (id, parent_id, name) VALUES (4, 0, 'Expense');
INSERT INTO groups (id, parent_id, name) VALUES (5, 1, 'Fixed assets');
INSERT INTO groups (id, parent_id, name) VALUES (6, 1, 'Current assets');
INSERT INTO groups (id, parent_id, name) VALUES (7, 2, 'Capital A/c');
INSERT INTO groups (id, parent_id, name) VALUES (8, 2, 'Current Liabilities');
INSERT INTO groups (id, parent_id, name) VALUES (9, 2, 'Borrowings');
INSERT INTO groups (id, parent_id, name) VALUES (10, 3, 'Sales');
INSERT INTO groups (id, parent_id, name) VALUES (11, 3, 'Direct Income');
INSERT INTO groups (id, parent_id, name) VALUES (12, 3, 'Indirect Income');
INSERT INTO groups (id, parent_id, name) VALUES (13, 4, 'Purchase');
INSERT INTO groups (id, parent_id, name) VALUES (14, 4, 'Direct Expenses');
INSERT INTO groups (id, parent_id, name) VALUES (15, 4, 'Indirect Expenses');
