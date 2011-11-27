CREATE TABLE IF NOT EXISTS groups (
  id int(11) NOT NULL AUTO_INCREMENT,
  parent_id int(11) NOT NULL,
  name varchar(100) NOT NULL,
  affects_gross int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS ledgers (
  id int(11) NOT NULL AUTO_INCREMENT,
  group_id int(11) NOT NULL,
  name varchar(100) NOT NULL,
  op_balance decimal(15,2) NOT NULL DEFAULT '0.00',
  op_balance_dc char(1) NOT NULL,
  type INT(2) NOT NULL DEFAULT 0,
  reconciliation int(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS entry_types (
  id int(5) NOT NULL,
  label varchar(15) NOT NULL,
  name varchar(100) NOT NULL,
  description varchar(255) NOT NULL,
  base_type int(2) NOT NULL,
  numbering int(2) NOT NULL,
  prefix varchar(10) NOT NULL,
  suffix varchar(10) NOT NULL,
  zero_padding int(2) NOT NULL,
  bank_cash_ledger_restriction int(2) NOT NULL DEFAULT 1,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS entries (
  id int(11) NOT NULL AUTO_INCREMENT,
  tag_id int(11) DEFAULT NULL,
  entry_type int(5) NOT NULL,
  number int(11) DEFAULT NULL,
  date datetime NOT NULL,
  dr_total decimal(15,2) NOT NULL DEFAULT '0.00',
  cr_total decimal(15,2) NOT NULL DEFAULT '0.00',
  narration text NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS entry_items (
  id int(11) NOT NULL AUTO_INCREMENT,
  entry_id int(11) NOT NULL,
  ledger_id int(11) NOT NULL,
  amount decimal(15,2) NOT NULL DEFAULT '0.00',
  dc char(1) NOT NULL,
  reconciliation_date datetime NULL,
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

CREATE TABLE IF NOT EXISTS settings (
  id int(1) NOT NULL,
  name varchar(100) NOT NULL,
  address varchar(255) NOT NULL,
  email varchar(100) NOT NULL,
  fy_start datetime NOT NULL,
  fy_end datetime NOT NULL,
  currency_symbol varchar(10) NOT NULL,
  date_format varchar(30) NOT NULL,
  timezone varchar(255) NOT NULL,
  manage_inventory int(1) NOT NULL,
  account_locked int(1) NOT NULL,
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
  database_version int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
