<?php

return "
    CREATE TABLE IF NOT EXISTS user_user_groups (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT UNSIGNED NOT NULL,
        user_group_id BIGINT UNSIGNED NOT NULL,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_user
            FOREIGN KEY (user_id) REFERENCES users (id)
            ON DELETE CASCADE,
        CONSTRAINT fk_user_group
            FOREIGN KEY (user_group_id) REFERENCES user_groups (id)
            ON DELETE CASCADE
    ) ENGINE=INNODB;
";