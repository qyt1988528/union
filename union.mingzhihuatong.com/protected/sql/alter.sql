alter table advertise_channel add price decimal(6, 3) comment '单价';

alter table adv_data add price decimal(6,3) comment '单价';
alter table adv_data add total_price decimal(9,3) comment '总价';

alter table adv_data_for_channel add price decimal(6,3) comment '单价';
alter table adv_data_for_channel add total_price decimal(9,3) comment '总价';
