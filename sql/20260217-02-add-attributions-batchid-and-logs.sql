-- Migration: add batch_id to attributions and create action logs table
-- Run this after your schema migrations (safe to run on existing DB)

ALTER TABLE bngrc_attributions
    ADD COLUMN batch_id VARCHAR(64) NULL;

CREATE TABLE IF NOT EXISTS bngrc_action_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(100) NOT NULL,
    payload TEXT,
    `user` VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
