CREATE TABLE `account` (
  `user_id` bigint(20) NOT NULL,
  `username` varchar(128) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `email` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `pfp` mediumtext DEFAULT NULL,
  `show_email` varchar(5) DEFAULT 'False',
  `about` varchar(255) DEFAULT NULL,
  `disabled` varchar(5) DEFAULT 'False'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `chatlog` (
  `message_id` bigint(20) NOT NULL,
  `message` varchar(2000) NOT NULL,
  `message_date` varchar(255) NOT NULL,
  `user_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `user_status_log` (
  `user_id` bigint(20) NOT NULL,
  `last_active_date_time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `friendship` (
  `requester_id` bigint(20) NOT NULL,
  `addressee_id` bigint(20) NOT NULL,
  `created_date_time` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE `friendship_status` (
  `friendship_req_id` bigint(20) NOT NULL,
  `requester_id` bigint(20) NOT NULL,
  `addressee_id` bigint(20) NOT NULL,
  `specified_date_time` varchar(255) NOT NULL,
  `status_code` char(1) NOT NULL,
  `specifier_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci