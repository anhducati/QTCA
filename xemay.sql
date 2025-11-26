CREATE TABLE `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,               -- Tên người dùng
  `email` VARCHAR(255) NOT NULL,              -- Email đăng nhập
  `phone` VARCHAR(10) DEFAULT NULL,           -- Số điện thoại
  `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,           -- Mật khẩu (bcrypt)
  `role` TINYINT NOT NULL DEFAULT '0'         -- 0: bán hàng, 1: admin, 2: kế toán, 3: thủ kho
      COMMENT '0: bán hàng, 1: admin, 2: kế toán, 3: thủ kho',
  `status` TINYINT NOT NULL DEFAULT '1'       -- 1: hoạt động, 0: khóa
      COMMENT '0: khóa, 1: hoạt động',
  `remember_token` VARCHAR(100) DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `brands` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) DEFAULT NULL,      -- Mã hãng: YMH, YD...
  `name` VARCHAR(255) NOT NULL,         -- Tên hãng: Yamaha, YADEA...
  `note` TEXT DEFAULT NULL,
  `is_active` TINYINT DEFAULT 1,        -- 1: dùng, 0: ẩn
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `vehicle_models` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `brand_id` BIGINT UNSIGNED NOT NULL,       -- FK -> brands.id
  `code` VARCHAR(50) DEFAULT NULL,           -- Mã nội bộ: EX155, JNS125...
  `name` VARCHAR(255) NOT NULL,              -- Tên: Exciter 155, Janus...
  `vehicle_type` VARCHAR(50) DEFAULT NULL,   -- ga / số / côn tay / điện
  `cylinder_cc` INT DEFAULT NULL,            -- dung tích: 50/110/155...
  `year_default` INT DEFAULT NULL,           -- đời chuẩn
  `note` TEXT DEFAULT NULL,
  `is_active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_vehicle_models_brand_id` (`brand_id`),
  CONSTRAINT `fk_vehicle_models_brand`
    FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `colors` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) DEFAULT NULL,          -- Mã màu (nếu có)
  `name` VARCHAR(255) NOT NULL,             -- Tên màu: Đỏ đen...
  `hex_code` VARCHAR(10) DEFAULT NULL,      -- Mã màu hiển thị (optional)
  `note` TEXT DEFAULT NULL,
  `is_active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `warehouses` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) DEFAULT NULL,          -- Mã kho: KHO1, BAI...
  `name` VARCHAR(255) NOT NULL,             -- Tên kho
  `address` VARCHAR(255) DEFAULT NULL,
  `note` TEXT DEFAULT NULL,
  `is_active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `suppliers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) DEFAULT NULL,          -- Mã NCC
  `name` VARCHAR(255) NOT NULL,             -- Tên NCC
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `tax_code` VARCHAR(50) DEFAULT NULL,      -- MST
  `note` TEXT DEFAULT NULL,
  `is_active` TINYINT DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `customers` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) DEFAULT NULL,          -- Mã KH: KH0001...
  `name` VARCHAR(255) NOT NULL,             -- Tên khách
  `phone` VARCHAR(20) NOT NULL,             -- SĐT
  `email` VARCHAR(100) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `cccd` VARCHAR(20) DEFAULT NULL,          -- CCCD/CMND
  `dob` DATE DEFAULT NULL,                  -- Ngày sinh
  `gender` TINYINT DEFAULT NULL,            -- Giới tính
  `note` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_customers_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `vehicles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `model_id` BIGINT UNSIGNED NOT NULL,       -- Dòng xe
  `color_id` BIGINT UNSIGNED DEFAULT NULL,   -- Màu
  `warehouse_id` BIGINT UNSIGNED NOT NULL,   -- Kho hiện tại

  `frame_no` VARCHAR(100) NOT NULL,          -- Số khung
  `engine_no` VARCHAR(100) DEFAULT NULL,     -- Số máy
  `year` INT DEFAULT NULL,                   -- Năm SX
  `battery_no` VARCHAR(100) DEFAULT NULL,    -- Xe điện: mã pin
  `imei` VARCHAR(100) DEFAULT NULL,          -- Định vị / smartkey

  `license_plate` VARCHAR(20) DEFAULT NULL
      COMMENT 'Biển số xe sau khi đăng ký',
  `registered_at` DATE DEFAULT NULL
      COMMENT 'Ngày hoàn tất đăng ký biển số',

  `status` VARCHAR(50) DEFAULT 'in_stock',   -- in_stock / sold / ...
  `import_price` DECIMAL(15,2) DEFAULT NULL, -- Giá nhập
  `sale_price` DECIMAL(15,2) DEFAULT NULL,   -- Giá bán thực tế
  `sale_date` DATE DEFAULT NULL,             -- Ngày bán
  `customer_id` BIGINT UNSIGNED DEFAULT NULL,-- Bán cho ai
  `note` TEXT DEFAULT NULL,

  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_vehicles_frame_no` (`frame_no`),
  KEY `idx_vehicles_model_id` (`model_id`),
  KEY `idx_vehicles_color_id` (`color_id`),
  KEY `idx_vehicles_warehouse_id` (`warehouse_id`),
  KEY `idx_vehicles_customer_id` (`customer_id`),

  CONSTRAINT `fk_vehicles_model`
    FOREIGN KEY (`model_id`) REFERENCES `vehicle_models` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_vehicles_color`
    FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_vehicles_warehouse`
    FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_vehicles_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `import_receipts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL,               -- Mã phiếu: PN0001...
  `import_date` DATE NOT NULL,               -- Ngày nhập
  `supplier_id` BIGINT UNSIGNED NOT NULL,    -- Nhà cung cấp
  `warehouse_id` BIGINT UNSIGNED NOT NULL,   -- Nhập vào kho nào
  `total_amount` DECIMAL(15,2) DEFAULT NULL, -- Tổng tiền
  `note` TEXT DEFAULT NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,     -- User lập phiếu
  `approved_by` BIGINT UNSIGNED DEFAULT NULL,-- Người duyệt
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_import_receipts_code` (`code`),
  KEY `idx_import_supplier_id` (`supplier_id`),
  KEY `idx_import_warehouse_id` (`warehouse_id`),
  KEY `idx_import_created_by` (`created_by`),
  KEY `idx_import_approved_by` (`approved_by`),
  CONSTRAINT `fk_import_supplier`
    FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_import_warehouse`
    FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_import_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_import_approved_by`
    FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `import_receipt_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `import_receipt_id` BIGINT UNSIGNED NOT NULL, -- Thuộc phiếu nhập nào
  `vehicle_id` BIGINT UNSIGNED NOT NULL,        -- Chiếc xe nào
  `model_id` BIGINT UNSIGNED NOT NULL,          -- Model
  `quantity` INT DEFAULT 1,
  `unit_price` DECIMAL(15,2) DEFAULT NULL,
  `vat_percent` DECIMAL(5,2) DEFAULT NULL,
  `amount` DECIMAL(15,2) DEFAULT NULL,
  `note` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_import_item_receipt` (`import_receipt_id`),
  KEY `idx_import_item_vehicle` (`vehicle_id`),
  KEY `idx_import_item_model` (`model_id`),
  CONSTRAINT `fk_import_item_receipt`
    FOREIGN KEY (`import_receipt_id`) REFERENCES `import_receipts` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_import_item_vehicle`
    FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_import_item_model`
    FOREIGN KEY (`model_id`) REFERENCES `vehicle_models` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `export_receipts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL,               -- Mã PX/HD: PX0001, HD0001...
  `export_date` DATE NOT NULL,               -- Ngày bán
  `warehouse_id` BIGINT UNSIGNED NOT NULL,   -- Kho xuất
  `customer_id` BIGINT UNSIGNED DEFAULT NULL,-- Khách mua
  `export_type` VARCHAR(50) DEFAULT 'sell',  -- sell / transfer / demo...
  `total_amount` DECIMAL(15,2) DEFAULT NULL, -- Tổng tiền hóa đơn
  `paid_amount` DECIMAL(15,2) DEFAULT 0,     -- Đã trả
  `debt_amount` DECIMAL(15,2) DEFAULT 0,     -- Còn nợ
  `payment_status` VARCHAR(20) DEFAULT 'unpaid', -- unpaid / partial / paid
  `due_date` DATE DEFAULT NULL,              -- Hạn thanh toán
  `note` TEXT DEFAULT NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,     -- Người lập
  `approved_by` BIGINT UNSIGNED DEFAULT NULL,-- Người duyệt
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_export_receipts_code` (`code`),
  KEY `idx_export_wh` (`warehouse_id`),
  KEY `idx_export_customer` (`customer_id`),
  KEY `idx_export_created_by` (`created_by`),
  KEY `idx_export_approved_by` (`approved_by`),
  CONSTRAINT `fk_export_warehouse`
    FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_export_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_export_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_export_approved_by`
    FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `export_receipt_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `export_receipt_id` BIGINT UNSIGNED NOT NULL, -- Thuộc hóa đơn nào
  `vehicle_id` BIGINT UNSIGNED NOT NULL,        -- Chiếc xe nào
  `model_id` BIGINT UNSIGNED NOT NULL,          -- Dòng xe
  `quantity` INT DEFAULT 1,
  `unit_price` DECIMAL(15,2) DEFAULT NULL,      -- Giá bán 1 xe
  `discount_amount` DECIMAL(15,2) DEFAULT NULL, -- Giảm giá
  `amount` DECIMAL(15,2) DEFAULT NULL,          -- Thành tiền

  `license_plate` VARCHAR(20) DEFAULT NULL
      COMMENT 'Biển số tại thời điểm in hóa đơn',

  `note` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,

  PRIMARY KEY (`id`),
  KEY `idx_export_item_receipt` (`export_receipt_id`),
  KEY `idx_export_item_vehicle` (`vehicle_id`),
  KEY `idx_export_item_model` (`model_id`),

  CONSTRAINT `fk_export_item_receipt`
    FOREIGN KEY (`export_receipt_id`) REFERENCES `export_receipts` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_export_item_vehicle`
    FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_export_item_model`
    FOREIGN KEY (`model_id`) REFERENCES `vehicle_models` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `export_receipt_id` BIGINT UNSIGNED NOT NULL, -- Hóa đơn nào
  `payment_date` DATE NOT NULL,                -- Ngày khách trả tiền
  `amount` DECIMAL(15,2) NOT NULL,             -- Số tiền trả
  `method` VARCHAR(50) DEFAULT NULL,           -- Tiền mặt / CK / thẻ
  `note` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_payments_receipt` (`export_receipt_id`),
  CONSTRAINT `fk_payments_receipt`
    FOREIGN KEY (`export_receipt_id`) REFERENCES `export_receipts` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `stock_takes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `stock_take_date` DATE NOT NULL,
  `status` VARCHAR(20) DEFAULT 'draft',      -- draft / completed...
  `note` TEXT DEFAULT NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `approved_by` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_stock_takes_code` (`code`),
  KEY `idx_stock_wh` (`warehouse_id`),
  KEY `idx_stock_created_by` (`created_by`),
  KEY `idx_stock_approved_by` (`approved_by`),
  CONSTRAINT `fk_stock_wh`
    FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_approved_by`
    FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `stock_take_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `stock_take_id` BIGINT UNSIGNED NOT NULL,
  `vehicle_id` BIGINT UNSIGNED DEFAULT NULL,
  `frame_no` VARCHAR(100) DEFAULT NULL,
  `engine_no` VARCHAR(100) DEFAULT NULL,
  `system_exists` TINYINT DEFAULT 1,
  `is_present` TINYINT DEFAULT 1,
  `note` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_stock_item_stock` (`stock_take_id`),
  KEY `idx_stock_item_vehicle` (`vehicle_id`),
  CONSTRAINT `fk_stock_item_stock`
    FOREIGN KEY (`stock_take_id`) REFERENCES `stock_takes` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_stock_item_vehicle`
    FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `inventory_adjustments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` VARCHAR(50) NOT NULL,
  `adjustment_date` DATE NOT NULL,
  `warehouse_id` BIGINT UNSIGNED NOT NULL,
  `reason` VARCHAR(255) DEFAULT NULL,       -- kiem_ke / mat_xe / sai_so...
  `stock_take_id` BIGINT UNSIGNED DEFAULT NULL,
  `note` TEXT DEFAULT NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `approved_by` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_inv_adj_code` (`code`),
  KEY `idx_inv_adj_wh` (`warehouse_id`),
  KEY `idx_inv_adj_stock` (`stock_take_id`),
  KEY `idx_inv_adj_created_by` (`created_by`),
  KEY `idx_inv_adj_approved_by` (`approved_by`),
  CONSTRAINT `fk_inv_adj_wh`
    FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_adj_stock`
    FOREIGN KEY (`stock_take_id`) REFERENCES `stock_takes` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_adj_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_adj_approved_by`
    FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `inventory_adjustment_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `inventory_adjustment_id` BIGINT UNSIGNED NOT NULL,
  `vehicle_id` BIGINT UNSIGNED DEFAULT NULL,
  `frame_no` VARCHAR(100) DEFAULT NULL,
  `engine_no` VARCHAR(100) DEFAULT NULL,
  `action` VARCHAR(20) NOT NULL,       -- increase / decrease / create / remove
  `qty` INT DEFAULT 1,
  `note` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_inv_adj_item_adj` (`inventory_adjustment_id`),
  KEY `idx_inv_adj_item_vehicle` (`vehicle_id`),
  CONSTRAINT `fk_inv_adj_item_adj`
    FOREIGN KEY (`inventory_adjustment_id`) REFERENCES `inventory_adjustments` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_adj_item_vehicle`
    FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `inventory_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vehicle_id` BIGINT UNSIGNED NOT NULL,
  `log_type` VARCHAR(50) NOT NULL,          -- import / export / transfer / adjust
  `ref_table` VARCHAR(50) DEFAULT NULL,     -- Bảng chứng từ
  `ref_id` BIGINT UNSIGNED DEFAULT NULL,    -- ID chứng từ
  `from_warehouse_id` BIGINT UNSIGNED DEFAULT NULL,
  `to_warehouse_id` BIGINT UNSIGNED DEFAULT NULL,
  `log_date` DATETIME NOT NULL,
  `note` TEXT DEFAULT NULL,
  `created_by` BIGINT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_log_vehicle` (`vehicle_id`),
  KEY `idx_log_from_wh` (`from_warehouse_id`),
  KEY `idx_log_to_wh` (`to_warehouse_id`),
  KEY `idx_log_created_by` (`created_by`),
  CONSTRAINT `fk_log_vehicle`
    FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_log_from_wh`
    FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_log_to_wh`
    FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_log_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
