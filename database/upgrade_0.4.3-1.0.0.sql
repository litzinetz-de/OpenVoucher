USE openvoucher;
ALTER TABLE vouchers ADD COLUMN valid_for INTEGER DEFAULT 0;
INSERT INTO settings (setting,s_value) VALUES ('default_device-qty','3');
INSERT INTO settings (setting,s_value) VALUES ('default_voucher-qty','10');
INSERT INTO settings (setting,s_value) VALUES ('force_device-qty','');
INSERT INTO settings (setting,s_value) VALUES ('force_voucher-qty','');

INSERT INTO settings (setting,s_value) VALUES ('default_exp_d','0');
INSERT INTO settings (setting,s_value) VALUES ('default_exp_h','4');
INSERT INTO settings (setting,s_value) VALUES ('default_exp_m','0');
INSERT INTO settings (setting,s_value) VALUES ('force_exp','');
INSERT INTO settings (setting,s_value) VALUES ('default_start_exp','creation');
INSERT INTO settings (setting,s_value) VALUES ('force_start_exp','');

INSERT INTO settings (setting,s_value) VALUES ('deny_user_drop_device','');