USE openvoucher;
CREATE TABLE settings (setting VARCHAR(255) NOT NULL PRIMARY KEY,s_value VARCHAR(255) NOT NULL);
INSERT INTO settings (setting,s_value) VALUES ('vouchertext1','Please enter the code');
INSERT INTO settings (setting,s_value) VALUES ('vouchertext2','to get internet access');