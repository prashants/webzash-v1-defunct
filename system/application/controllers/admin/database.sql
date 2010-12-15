CREATE TABLE IF NOT EXISTS groups (
  id int(11) NOT NULL AUTO_INCREMENT,
  parent_id int(11) NOT NULL,
  name varchar(100) NOT NULL,
  affects_gross int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS ledgers (
  id int(11) NOT NULL AUTO_INCREMENT,
  group_id int(11) NOT NULL,
  name varchar(100) NOT NULL,
  op_balance decimal(15,2) NOT NULL DEFAULT '0.00',
  op_balance_dc char(1) NOT NULL,
  type char(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS settings (
  id int(1) NOT NULL,
  label varchar(255) NOT NULL,
  name varchar(100) NOT NULL,
  address varchar(255) NOT NULL,
  email varchar(100) NOT NULL,
  fy_start datetime NOT NULL,
  fy_end datetime NOT NULL,
  currency_symbol varchar(10) NOT NULL,
  date_format varchar(30) NOT NULL,
  timezone varchar(255) NOT NULL,
  email_protocol varchar(9) NOT NULL,
  email_host varchar(255) NOT NULL,
  email_port int(5) NOT NULL,
  email_username varchar(255) NOT NULL,
  email_password varchar(255) NOT NULL,
  print_paper_height float NOT NULL,
  print_paper_width float NOT NULL,
  print_margin_top float NOT NULL,
  print_margin_bottom float NOT NULL,
  print_margin_left float NOT NULL,
  print_margin_right float NOT NULL,
  print_orientation varchar(1) NOT NULL,
  print_page_format varchar(1) NOT NULL,
  print_page_layout text NOT NULL,
  print_logo varchar(255) NOT NULL,
  receipt_voucher_prefix varchar(10) NOT NULL,
  payment_voucher_prefix varchar(10) NOT NULL,
  contra_voucher_prefix varchar(10) NOT NULL,
  journal_voucher_prefix varchar(10) NOT NULL,
  voucher_number_padding int(5) NOT NULL,
  database_version int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS vouchers (
  id int(11) NOT NULL AUTO_INCREMENT,
  tag_id int(11) NOT NULL DEFAULT '0',
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

CREATE TABLE IF NOT EXISTS tags (
  id int(11) NOT NULL AUTO_INCREMENT,
  title varchar(50) NOT NULL,
  color varchar(6) NOT NULL,
  background varchar(6) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS logs (
  id int(11) NOT NULL AUTO_INCREMENT,
  date datetime NOT NULL,
  level int(1) NOT NULL,
  host_ip varchar(25) NOT NULL,
  user varchar(25) NOT NULL,
  url varchar(255) NOT NULL,
  user_agent varchar(100) NOT NULL,
  message_title varchar(255) NOT NULL,
  message_desc mediumtext NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (1, 0, 'Assets', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (2, 0, 'Liabilities', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (3, 0, 'Incomes', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (4, 0, 'Expenses', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (5, 1, 'Fixed assets', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (6, 1, 'Current assets', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (7, 1, 'Investments', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (8, 2, 'Capital A/C', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (9, 2, 'Current Liabilities', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (10, 2, 'Loans (Liability)', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (11, 3, 'Direct Incomes', 1);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (12, 4, 'Direct Expenses', 1);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (13, 3, 'Indirect Incomes', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (14, 4, 'Indirect Expenses', 0);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (15, 3, 'Sales', 1);
INSERT INTO groups (id, parent_id, name, affects_gross) VALUES (16, 4, 'Purchases', 1);
