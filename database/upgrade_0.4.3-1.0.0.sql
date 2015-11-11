USE openvoucher;
ALTER TABLE vouchers ADD COLUMN valid_for INTEGER DEFAULT 0;