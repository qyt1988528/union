create database if not exists union_mingzhihuatong_com;
use union_mingzhihuatong_com;

create table if not exists user(
    id int auto_increment,
    name varchar(32),
    email varchar(128) not null,
    password char(255),
    phone varchar(32) comment '联系电话',
    status int default 0,
    channel_id int default 0,
    role int default 0,
    token varchar(128) comment '随机校验码',
    ctime datetime ,
    mtime datetime,
    unique key email_key(email),
    primary key(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 comment = '用户表' auto_increment = 100000;

create table if not exists cp(
    id int auto_increment,
    name varchar(128),
    fullname varchar(128) comment '全名',
    contact_name varchar(128) comment '接口人',
    contact_phone varchar(128) comment '接口人电话',
    contact_email varchar(128) comment '接口人邮箱',
    status int default 0,
    ctime datetime ,
    mtime datetime,
    primary key(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 comment = '广告主表' auto_increment = 100000;

create table if not exists advertise (
    id int auto_increment,
    name varchar(128),
    cp_id int,
    status int default 0,
    income_price float comment '接入价格',
    outcome_price float comment '放出价格',
    description text comment '备注，要求',
    cp_admin_url varchar(128) comment '对方数据对接后台',
    mtime datetime,
    ctime datetime ,
    foreign key (cp_id) references cp(id),
    primary key(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 comment = '广告表' auto_increment = 100000;

create table if not exists channel(
    id int auto_increment,
    name varchar(128) not null,
    contactor varchar(128) comment '接口人',
    account_name varchar(128) comment '收款人',
    account_number char(32) comment '收款账号',
    account_bank varchar(128) comment '开户行',
    status int default 0,
    mtime datetime,
    ctime datetime ,
    primary key(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 comment = '渠道表' auto_increment = 100000;

create table if not exists advertise_channel (
    id int auto_increment,
    cp_id int,
    adv_id int,
    adv_name varchar(128),
    channel_id int,
    tag varchar(128) comment '包名或其他用于区分的标记',
    data text comment '相关数据',
    status int default 0,
    mtime datetime,
    ctime datetime ,
  primary key (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 comment = '业务分发' auto_increment = 100000;

create table if not exists adv_data (
  id int auto_increment,
  cp_id int,
  adv_id int,
  channel_id int,
  adv_channel_id int,
  tag varchar(128) comment '包名或其他用于区分的标记',
  `date` date,
  download_number int,
  install_number int,
  active_number int,
  new_user int,
  left_user_2days float,
  left_user_7days float,
  left_user_14days float,
  convert_ratio float,
  status int default 0,
  upload_user_id int,
  star_level int comment '信用等级',
  comments varchar(200) comment '备注',
  ctime datetime,
  mtime datetime,
  unique key key_adv_channel_id(date, adv_channel_id),
  foreign key (upload_user_id) references user(id),
  foreign key (channel_id) references channel(id),
  foreign key (adv_id) references advertise(id),
  foreign key (adv_channel_id) references advertise_channel(id),
  foreign key (cp_id) references cp(id),
  primary key (id),
  index date_index(`date`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 comment = '业务数据报表' auto_increment = 100000;
create table if not exists adv_data_for_channel like adv_data;
#'给渠道的数据报表'

create table if not exists report_schema (
  id int auto_increment,
  cp_id int,
  adv_id int,
  primary key (id),
  foreign key (cp_id) references cp(id),
  foreign key (adv_id) references advertise(id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 comment = '定义报表的格式' auto_increment = 1;

create table if not exists report_schema_fields (
  schema_id int comment '',
  position int default 0 comment '在表中的顺序',
  name varchar(32) comment '显示的字段名',
  field varchar(32) comment '报表字段名，对应adv_data中的一个字段',
  foreign key (schema_id) references report_schema(id),
  primary key (schema_id, position)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 comment = '一个报表的格式，字段' auto_increment = 1;

