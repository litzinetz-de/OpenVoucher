USE openvoucher;
INSERT INTO settings (setting,s_value) VALUES ('use_verification','n');
ALTER TABLE vouchers ADD verification_key VARCHAR(255) AFTER valid_until;