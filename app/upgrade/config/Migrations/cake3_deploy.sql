#
# SQL Export
# Created by Querious (1064)
# Created: February 22, 2017 at 9:35:36 PM GMT+1
# Encoding: Unicode (UTF-8)
#


SET @PREVIOUS_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS = 0;


CREATE TABLE `acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2104 DEFAULT CHARSET=utf8;


CREATE TABLE `advanced_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `private` int(2) NOT NULL DEFAULT '0',
  `log_result_count` int(2) NOT NULL,
  `log_result_data` int(2) NOT NULL,
  `deleted` int(2) NOT NULL DEFAULT '0',
  `deleted_date` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `advanced_filters_ibfk_1` (`user_id`),
  CONSTRAINT `advanced_filters_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `advanced_filter_crons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `advanced_filter_id` int(11) NOT NULL,
  `cron_id` int(11) DEFAULT NULL,
  `type` int(4) DEFAULT NULL,
  `result` int(11) DEFAULT NULL,
  `execution_time` float NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `advanced_filter_id` (`advanced_filter_id`),
  KEY `cron_id` (`cron_id`),
  CONSTRAINT `advanced_filter_cron_ibfk_1` FOREIGN KEY (`advanced_filter_id`) REFERENCES `advanced_filters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `advanced_filter_cron_ibfk_2` FOREIGN KEY (`cron_id`) REFERENCES `cron` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `advanced_filter_cron_result_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `advanced_filter_cron_id` int(11) NOT NULL,
  `data` text NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `advanced_filter_cron_id` (`advanced_filter_cron_id`),
  CONSTRAINT `advanced_filter_cron_result_items_ibfk_1` FOREIGN KEY (`advanced_filter_cron_id`) REFERENCES `advanced_filter_crons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `advanced_filter_user_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `advanced_filter_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `default_index` int(2) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `advanced_filter_id` (`advanced_filter_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `advanced_filter_user_settings_ib_fk_1` FOREIGN KEY (`advanced_filter_id`) REFERENCES `advanced_filters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `advanced_filter_user_settings_ib_fk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `advanced_filter_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `advanced_filter_id` int(11) NOT NULL,
  `field` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `many` int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `advanced_filter_values_ibfk_1` (`advanced_filter_id`),
  CONSTRAINT `advanced_filter_values_ibfk_1` FOREIGN KEY (`advanced_filter_id`) REFERENCES `advanced_filters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `aros` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


CREATE TABLE `aros_acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aro_id` int(10) NOT NULL,
  `aco_id` int(10) NOT NULL,
  `_create` varchar(2) NOT NULL DEFAULT '0',
  `_read` varchar(2) NOT NULL DEFAULT '0',
  `_update` varchar(2) NOT NULL DEFAULT '0',
  `_delete` varchar(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ARO_ACO_KEY` (`aro_id`,`aco_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;


CREATE TABLE `asset_classification_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `asset_classification_count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `asset_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `criteria` text NOT NULL,
  `value` float DEFAULT NULL,
  `asset_classification_type_id` int(11) NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_classification_type_id` (`asset_classification_type_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `asset_classifications_ibfk_1` FOREIGN KEY (`asset_classification_type_id`) REFERENCES `asset_classification_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_classifications_ibfk_2` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `asset_classifications_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_classification_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_classification_id` (`asset_classification_id`),
  KEY `asset_id` (`asset_id`),
  CONSTRAINT `asset_classifications_assets_ibfk_1` FOREIGN KEY (`asset_classification_id`) REFERENCES `asset_classifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_classifications_assets_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `asset_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `asset_labels_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `asset_media_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `editable` int(11) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


CREATE TABLE `asset_media_types_threats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_media_type_id` int(11) NOT NULL,
  `threat_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_media_type_id` (`asset_media_type_id`),
  KEY `threat_id` (`threat_id`),
  CONSTRAINT `FK_asset_media_types_threats_asset_media_types` FOREIGN KEY (`asset_media_type_id`) REFERENCES `asset_media_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_asset_media_types_threats_threats` FOREIGN KEY (`threat_id`) REFERENCES `threats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;


CREATE TABLE `asset_media_types_vulnerabilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_media_type_id` int(11) NOT NULL,
  `vulnerability_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_media_type_id` (`asset_media_type_id`),
  KEY `vulnerability_id` (`vulnerability_id`),
  CONSTRAINT `FK_asset_media_types_vulnerabilities_asset_media_types` FOREIGN KEY (`asset_media_type_id`) REFERENCES `asset_media_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_asset_media_types_vulnerabilities_vulnerabilities` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;


CREATE TABLE `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `asset_label_id` int(11) DEFAULT NULL,
  `asset_media_type_id` int(11) DEFAULT NULL,
  `asset_owner_id` int(11) DEFAULT NULL,
  `asset_guardian_id` int(11) DEFAULT NULL,
  `asset_user_id` int(11) DEFAULT NULL,
  `review` date NOT NULL,
  `expired_reviews` int(1) NOT NULL DEFAULT '0',
  `security_incident_open_count` int(11) NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_label_id` (`asset_label_id`),
  KEY `asset_media_type_id` (`asset_media_type_id`),
  KEY `asset_owner_id` (`asset_owner_id`),
  KEY `asset_guardian_id` (`asset_guardian_id`),
  KEY `asset_user_id` (`asset_user_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `assets_ibfk_1` FOREIGN KEY (`asset_label_id`) REFERENCES `asset_labels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `assets_ibfk_2` FOREIGN KEY (`asset_media_type_id`) REFERENCES `asset_media_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `assets_ibfk_4` FOREIGN KEY (`asset_owner_id`) REFERENCES `business_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `assets_ibfk_5` FOREIGN KEY (`asset_user_id`) REFERENCES `business_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `assets_ibfk_6` FOREIGN KEY (`asset_guardian_id`) REFERENCES `business_units` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `assets_ibfk_7` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `assets_business_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `business_unit_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `business_unit_id` (`business_unit_id`),
  CONSTRAINT `assets_business_units_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_business_units_ibfk_2` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `assets_legals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `legal_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `legal_id` (`legal_id`),
  CONSTRAINT `assets_legals_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_legals_ibfk_2` FOREIGN KEY (`legal_id`) REFERENCES `legals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `assets_related` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `asset_related_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `asset_related_id` (`asset_related_id`),
  CONSTRAINT `assets_related_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_related_ibfk_2` FOREIGN KEY (`asset_related_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `assets_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `risk_id` (`risk_id`),
  CONSTRAINT `assets_risks_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_risks_ibfk_2` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `assets_security_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `security_incident_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `security_incident_id` (`security_incident_id`),
  CONSTRAINT `assets_security_incidents_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_security_incidents_ibfk_2` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `assets_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  CONSTRAINT `assets_third_party_risks_ibfk_1` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_third_party_risks_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(45) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `filename` text NOT NULL,
  `extension` varchar(155) NOT NULL,
  `mime_type` varchar(155) NOT NULL,
  `file_size` int(11) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_attachments_users` (`user_id`),
  CONSTRAINT `FK_attachments_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `audits` (
  `id` varchar(36) NOT NULL,
  `version` int(11) NOT NULL,
  `event` varchar(255) NOT NULL,
  `model` varchar(255) NOT NULL,
  `entity_id` varchar(36) NOT NULL,
  `request_id` varchar(36) NOT NULL,
  `json_object` text NOT NULL,
  `description` text,
  `source_id` varchar(255) DEFAULT NULL,
  `restore_id` varchar(36) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `restore_id` (`restore_id`),
  CONSTRAINT `audits_ibfk_1` FOREIGN KEY (`restore_id`) REFERENCES `audits` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `audit_deltas` (
  `id` varchar(36) NOT NULL,
  `audit_id` varchar(36) NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `old_value` text,
  `new_value` text,
  PRIMARY KEY (`id`),
  KEY `audit_id` (`audit_id`),
  CONSTRAINT `audit_deltas_ibfk_1` FOREIGN KEY (`audit_id`) REFERENCES `audits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `recurrence` int(5) NOT NULL,
  `reminder_apart` int(11) NOT NULL,
  `reminder_amount` int(11) NOT NULL,
  `redirect` varchar(255) NOT NULL,
  `ldap_connector_id` int(11) NOT NULL,
  `video` varchar(255) DEFAULT NULL,
  `video_extension` varchar(50) DEFAULT NULL,
  `video_mime_type` varchar(150) DEFAULT NULL,
  `video_file_size` int(11) DEFAULT NULL,
  `questionnaire` varchar(255) DEFAULT NULL,
  `text_file` varchar(255) DEFAULT NULL,
  `text_file_extension` varchar(50) DEFAULT NULL,
  `uploads_sort_json` text NOT NULL,
  `welcome_text` text NOT NULL,
  `welcome_sub_text` text NOT NULL,
  `thank_you_text` text NOT NULL,
  `thank_you_sub_text` text NOT NULL,
  `email_subject` varchar(150) NOT NULL,
  `email_body` text NOT NULL,
  `email_reminder_custom` int(1) NOT NULL DEFAULT '0',
  `email_reminder_subject` varchar(150) NOT NULL,
  `email_reminder_body` text NOT NULL,
  `status` enum('started','stopped') NOT NULL DEFAULT 'stopped',
  `awareness_training_count` int(11) NOT NULL,
  `active_users` int(11) NOT NULL,
  `active_users_percentage` int(3) NOT NULL,
  `ignored_users` int(11) NOT NULL,
  `ignored_users_percentage` int(3) DEFAULT NULL,
  `compliant_users` int(11) NOT NULL,
  `compliant_users_percentage` int(3) NOT NULL,
  `not_compliant_users` int(11) NOT NULL,
  `not_compliant_users_percentage` int(3) NOT NULL,
  `stats_update_status` int(2) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ldap_connector_id` (`ldap_connector_id`),
  CONSTRAINT `awareness_programs_ibfk_1` FOREIGN KEY (`ldap_connector_id`) REFERENCES `ldap_connectors` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_overtime_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `awareness_program_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `doing` decimal(8,2) NOT NULL,
  `missing` decimal(8,2) NOT NULL,
  `correct_answers` decimal(8,2) NOT NULL,
  `average` decimal(8,2) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_overtime_graphs_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_program_active_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `awareness_program_id` int(11) NOT NULL,
  `uid` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(155) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_program_active_users_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_program_compliant_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `awareness_program_id` int(11) NOT NULL,
  `uid` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_program_compliant_users_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_program_demos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(100) NOT NULL,
  `awareness_program_id` int(11) NOT NULL,
  `completed` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_program_demos_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;


CREATE TABLE `awareness_program_ignored_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `awareness_program_id` int(11) NOT NULL,
  `uid` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_program_ignored_users_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_program_ldap_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `awareness_program_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_program_ldap_groups_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_program_missed_recurrences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(100) NOT NULL,
  `awareness_program_id` int(11) DEFAULT NULL,
  `awareness_program_recurrence_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_recurrence_id` (`awareness_program_recurrence_id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_program_missed_recurrences_ibfk_2` FOREIGN KEY (`awareness_program_recurrence_id`) REFERENCES `awareness_program_recurrences` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `awareness_program_missed_recurrences_ibfk_3` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_program_not_compliant_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `awareness_program_id` int(11) NOT NULL,
  `uid` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_program_not_compliant_users_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_program_recurrences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `awareness_program_id` int(11) NOT NULL,
  `start` date NOT NULL,
  `awareness_training_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_program_recurrences_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_programs_security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_policy_id` int(11) NOT NULL,
  `awareness_program_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_policy_id` (`security_policy_id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_programs_security_policies_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `awareness_programs_security_policies_ibfk_2` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_reminders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `awareness_program_id` int(11) NOT NULL,
  `demo` int(1) NOT NULL DEFAULT '0',
  `reminder_type` int(2) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_user_id` (`uid`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_reminders_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `awareness_trainings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `awareness_user_id` int(11) NOT NULL,
  `awareness_program_id` int(11) DEFAULT NULL,
  `awareness_program_recurrence_id` int(11) DEFAULT NULL,
  `answers_json` text,
  `correct` int(11) DEFAULT NULL,
  `wrong` int(11) DEFAULT NULL,
  `demo` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_user_id` (`awareness_user_id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  KEY `awareness_program_recurrence_id` (`awareness_program_recurrence_id`),
  CONSTRAINT `awareness_trainings_ibfk_1` FOREIGN KEY (`awareness_user_id`) REFERENCES `awareness_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `awareness_trainings_ibfk_3` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `awareness_trainings_ibfk_4` FOREIGN KEY (`awareness_program_recurrence_id`) REFERENCES `awareness_program_recurrences` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sql_file` varchar(255) NOT NULL,
  `deleted_files` int(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `bulk_actions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL,
  `model` varchar(150) NOT NULL,
  `json_data` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  CONSTRAINT `bulk_actions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `bulk_action_objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bulk_action_id` int(11) NOT NULL,
  `model` varchar(150) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `bulk_action_objects_ibfk_1` (`bulk_action_id`),
  CONSTRAINT `bulk_action_objects_ibfk_1` FOREIGN KEY (`bulk_action_id`) REFERENCES `bulk_actions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `impact` text NOT NULL,
  `threats` text NOT NULL,
  `vulnerabilities` text NOT NULL,
  `residual_score` int(11) NOT NULL,
  `risk_score` float DEFAULT NULL,
  `residual_risk` float NOT NULL,
  `user_id` int(11) NOT NULL,
  `guardian_id` int(11) NOT NULL,
  `review` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `exceptions_issues` int(1) NOT NULL DEFAULT '0',
  `controls_issues` int(1) NOT NULL DEFAULT '0',
  `control_in_design` int(1) NOT NULL DEFAULT '0',
  `expired_reviews` int(1) NOT NULL DEFAULT '0',
  `risk_above_appetite` int(1) NOT NULL DEFAULT '0',
  `plans_issues` int(1) NOT NULL DEFAULT '0',
  `risk_mitigation_strategy_id` int(11) DEFAULT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_mitigation_strategy_id` (`risk_mitigation_strategy_id`),
  KEY `user_id` (`user_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  KEY `guardian_id` (`guardian_id`),
  CONSTRAINT `business_continuities_ibfk_2` FOREIGN KEY (`risk_mitigation_strategy_id`) REFERENCES `risk_mitigation_strategies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_ibfk_5` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_ibfk_6` FOREIGN KEY (`guardian_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuity_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `objective` text NOT NULL,
  `audit_metric` text NOT NULL,
  `audit_success_criteria` text NOT NULL,
  `launch_criteria` text NOT NULL,
  `security_service_type_id` int(11) DEFAULT NULL,
  `opex` float NOT NULL,
  `capex` float NOT NULL,
  `resource_utilization` int(11) NOT NULL,
  `regular_review` date NOT NULL,
  `awareness_recurrence` varchar(150) DEFAULT NULL,
  `audits_all_done` int(1) NOT NULL,
  `audits_last_missing` int(1) NOT NULL,
  `audits_last_passed` int(1) NOT NULL,
  `audits_improvements` int(1) NOT NULL,
  `ongoing_corrective_actions` int(1) NOT NULL DEFAULT '0',
  `launch_responsible_id` int(11) NOT NULL,
  `sponsor_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_type_id` (`security_service_type_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  KEY `launch_responsible_id` (`launch_responsible_id`),
  KEY `sponsor_id` (`sponsor_id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `business_continuity_plans_ibfk_1` FOREIGN KEY (`security_service_type_id`) REFERENCES `security_service_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_plans_ibfk_2` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_plans_ibfk_3` FOREIGN KEY (`launch_responsible_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_plans_ibfk_4` FOREIGN KEY (`sponsor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_plans_ibfk_5` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities_business_continuity_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `business_continuity_plan_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `business_continuity_plan_id` (`business_continuity_plan_id`),
  CONSTRAINT `business_continuities_business_continuity_plans_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_business_continuity_plans_ibfk_2` FOREIGN KEY (`business_continuity_plan_id`) REFERENCES `business_continuity_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities_business_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `business_unit_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `business_unit_id` (`business_unit_id`),
  CONSTRAINT `business_continuities_business_units_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_business_units_ibfk_2` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities_compliance_managements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `compliance_management_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_management_id` (`compliance_management_id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  CONSTRAINT `business_continuities_compliance_managements_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_compliance_managements_ibfk_2` FOREIGN KEY (`compliance_management_id`) REFERENCES `compliance_managements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities_goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `goal_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `goal_id` (`goal_id`),
  CONSTRAINT `business_continuities_goals_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_goals_ibfk_2` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities_processes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `process_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `process_id` (`process_id`),
  CONSTRAINT `business_continuities_processes_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_processes_ibfk_2` FOREIGN KEY (`process_id`) REFERENCES `processes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `business_continuity_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_business_continuities_projects_projects` (`project_id`),
  KEY `FK_business_continuities_projects_business_continuities` (`business_continuity_id`),
  CONSTRAINT `FK_business_continuities_projects_business_continuities` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_business_continuities_projects_projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities_risk_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `risk_classification_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `risk_classification_id` (`risk_classification_id`),
  CONSTRAINT `business_continuities_risk_classifications_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_risk_classifications_ibfk_2` FOREIGN KEY (`risk_classification_id`) REFERENCES `risk_classifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities_risk_exceptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `risk_exception_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `risk_exception_id` (`risk_exception_id`),
  CONSTRAINT `business_continuities_risk_exceptions_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_risk_exceptions_ibfk_2` FOREIGN KEY (`risk_exception_id`) REFERENCES `risk_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities_threats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `threat_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `threat_id` (`threat_id`),
  CONSTRAINT `business_continuities_threats_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_threats_ibfk_2` FOREIGN KEY (`threat_id`) REFERENCES `threats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuities_vulnerabilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `vulnerability_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `vulnerability_id` (`vulnerability_id`),
  CONSTRAINT `business_continuities_vulnerabilities_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_vulnerabilities_ibfk_2` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuity_plan_audit_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_plan_id` int(11) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_plan_id` (`business_continuity_plan_id`),
  CONSTRAINT `business_continuity_plan_audit_dates_ibfk_1` FOREIGN KEY (`business_continuity_plan_id`) REFERENCES `business_continuity_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuity_plan_audit_improvements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_plan_audit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_plan_audit_id` (`business_continuity_plan_audit_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `business_continuity_plan_audit_improvements_ibfk_1` FOREIGN KEY (`business_continuity_plan_audit_id`) REFERENCES `business_continuity_plan_audits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_plan_audit_improvements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuity_plan_audit_improvements_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_plan_audit_improvement_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_plan_audit_improvement_id` (`business_continuity_plan_audit_improvement_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `business_continuity_plan_audit_improvements_projects_ibfk_1` FOREIGN KEY (`business_continuity_plan_audit_improvement_id`) REFERENCES `business_continuity_plan_audit_improvements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_plan_audit_improvements_projects_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuity_plan_audit_improvements_security_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_plan_audit_improvement_id` int(11) NOT NULL,
  `security_incident_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_plan_audit_improvement_id` (`business_continuity_plan_audit_improvement_id`),
  KEY `security_incident_id` (`security_incident_id`),
  CONSTRAINT `business_continuity_plan_audit_improvements_incidents_ibfk_1` FOREIGN KEY (`business_continuity_plan_audit_improvement_id`) REFERENCES `business_continuity_plan_audit_improvements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_plan_audit_improvements_incidents_ibfk_2` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuity_plan_audits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_plan_id` int(11) NOT NULL,
  `audit_metric_description` text NOT NULL,
  `audit_success_criteria` text NOT NULL,
  `result` int(1) DEFAULT NULL,
  `result_description` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `planned_date` date NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  KEY `user_id` (`user_id`),
  KEY `business_continuity_plan_id` (`business_continuity_plan_id`),
  CONSTRAINT `business_continuity_plan_audits_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_plan_audits_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_plan_audits_ibfk_5` FOREIGN KEY (`business_continuity_plan_id`) REFERENCES `business_continuity_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuity_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_plan_id` int(11) NOT NULL,
  `step` int(11) NOT NULL,
  `when` text NOT NULL,
  `who` text NOT NULL,
  `awareness_role` int(11) DEFAULT NULL,
  `does` text NOT NULL,
  `where` text NOT NULL,
  `how` text NOT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_role` (`awareness_role`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  KEY `business_continuity_plan_id` (`business_continuity_plan_id`),
  CONSTRAINT `business_continuity_tasks_ibfk_1` FOREIGN KEY (`awareness_role`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_tasks_ibfk_2` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_tasks_ibfk_3` FOREIGN KEY (`business_continuity_plan_id`) REFERENCES `business_continuity_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_continuity_task_reminders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_task_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT '0',
  `acknowledged` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_task_id` (`business_continuity_task_id`),
  CONSTRAINT `business_continuity_task_reminders_ibfk_1` FOREIGN KEY (`business_continuity_task_id`) REFERENCES `business_continuity_tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `_hidden` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `business_units_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE `business_units_data_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_unit_id` int(11) NOT NULL,
  `data_asset_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_units_legals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_unit_id` int(11) NOT NULL,
  `legal_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_unit_id` (`business_unit_id`),
  KEY `legal_id` (`legal_id`),
  CONSTRAINT `business_units_legals_ibfk_1` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_units_legals_ibfk_2` FOREIGN KEY (`legal_id`) REFERENCES `legals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `business_units_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_unit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_unit_id` (`business_unit_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `business_units_users_ibfk_1` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_units_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `cake_sessions` (
  `id` varchar(255) NOT NULL DEFAULT '',
  `data` text,
  `expires` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(150) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `message` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_audit_feedback_profiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `compliance_audit_feedback_count` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_audit_feedbacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_audit_feedback_profile_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_audit_feedback_profile_id` (`compliance_audit_feedback_profile_id`),
  CONSTRAINT `compliance_audit_feedbacks_ibfk_1` FOREIGN KEY (`compliance_audit_feedback_profile_id`) REFERENCES `compliance_audit_feedback_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_audit_auditee_feedbacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `compliance_audit_setting_id` int(11) NOT NULL,
  `compliance_audit_feedback_profile_id` int(11) NOT NULL,
  `compliance_audit_feedback_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_audit_setting_id` (`compliance_audit_setting_id`),
  KEY `compliance_audit_feedback_profile_id` (`compliance_audit_feedback_profile_id`),
  KEY `compliance_audit_feedback_id` (`compliance_audit_feedback_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `FK_compliance_audit_auditee_feedbacks_compliance_audit_feedback` FOREIGN KEY (`compliance_audit_feedback_id`) REFERENCES `compliance_audit_feedbacks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_compliance_audit_auditee_feedbacks_compliance_audit_settings` FOREIGN KEY (`compliance_audit_setting_id`) REFERENCES `compliance_audit_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_compliance_audit_auditee_feedbacks_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_audit_auditee_feedbacks_ibfk_3` FOREIGN KEY (`compliance_audit_feedback_profile_id`) REFERENCES `compliance_audit_feedback_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_audits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `third_party_id` int(11) NOT NULL,
  `auditor_id` int(11) NOT NULL,
  `third_party_contact_id` int(11) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `auditee_title` varchar(155) NOT NULL,
  `auditee_instructions` text NOT NULL,
  `use_default_template` int(1) NOT NULL DEFAULT '1',
  `email_subject` varchar(255) NOT NULL,
  `email_body` text NOT NULL,
  `auditee_notifications` tinyint(1) NOT NULL DEFAULT '0',
  `auditee_emails` tinyint(1) NOT NULL DEFAULT '0',
  `auditor_notifications` tinyint(1) NOT NULL DEFAULT '0',
  `auditor_emails` tinyint(1) NOT NULL DEFAULT '0',
  `show_analyze_title` tinyint(1) NOT NULL DEFAULT '0',
  `show_analyze_description` tinyint(1) NOT NULL DEFAULT '0',
  `show_analyze_audit_criteria` tinyint(1) NOT NULL DEFAULT '0',
  `show_findings` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(50) NOT NULL DEFAULT 'started' COMMENT 'started or stopped',
  `compliance_finding_count` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `deleted` int(2) NOT NULL DEFAULT '0',
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_id` (`third_party_id`),
  KEY `auditor_id` (`auditor_id`),
  KEY `third_party_contact_id` (`third_party_contact_id`),
  CONSTRAINT `compliance_audits_ibfk_1` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_audits_ibfk_2` FOREIGN KEY (`auditor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_audits_ibfk_3` FOREIGN KEY (`third_party_contact_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_audit_feedbacks_compliance_audits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_audit_feedback_id` int(11) NOT NULL,
  `compliance_audit_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_audit_feedback_id` (`compliance_audit_feedback_id`),
  KEY `compliance_audit_id` (`compliance_audit_id`),
  CONSTRAINT `compliance_audit_feedbacks_compliance_audits_ibfk_1` FOREIGN KEY (`compliance_audit_feedback_id`) REFERENCES `compliance_audit_feedbacks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_audit_feedbacks_compliance_audits_ibfk_2` FOREIGN KEY (`compliance_audit_id`) REFERENCES `compliance_audits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_audit_overtime_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_audit_id` int(11) NOT NULL,
  `open` int(3) NOT NULL,
  `closed` int(3) NOT NULL,
  `expired` int(3) NOT NULL,
  `no_evidence` int(3) NOT NULL,
  `waiting_evidence` int(3) NOT NULL,
  `provided_evidence` int(3) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_audit_id` (`compliance_audit_id`),
  CONSTRAINT `compliance_audit_overtime_graphs_ibfk_1` FOREIGN KEY (`compliance_audit_id`) REFERENCES `compliance_audits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_audit_provided_feedbacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `compliance_audit_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `compliance_audit_id` (`compliance_audit_id`),
  CONSTRAINT `compliance_audit_provided_feedbacks_ib_fk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_audit_provided_feedbacks_ib_fk_2` FOREIGN KEY (`compliance_audit_id`) REFERENCES `compliance_audits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_audit_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_audit_id` int(11) NOT NULL,
  `compliance_package_item_id` int(11) NOT NULL,
  `status` int(1) DEFAULT NULL,
  `compliance_audit_feedback_profile_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `deleted` int(2) NOT NULL DEFAULT '0',
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_audit_id` (`compliance_audit_id`),
  KEY `compliance_package_item_id` (`compliance_package_item_id`),
  KEY `compliance_audit_feedback_profile_id` (`compliance_audit_feedback_profile_id`),
  CONSTRAINT `compliance_audit_settings_ibfk_1` FOREIGN KEY (`compliance_audit_id`) REFERENCES `compliance_audits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_audit_settings_ibfk_2` FOREIGN KEY (`compliance_package_item_id`) REFERENCES `compliance_package_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_audit_settings_ibfk_4` FOREIGN KEY (`compliance_audit_feedback_profile_id`) REFERENCES `compliance_audit_feedback_profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_audit_setting_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_audit_setting_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_audit_setting_id` (`compliance_audit_setting_id`),
  CONSTRAINT `compliance_audit_setting_notifications_ibfk_1` FOREIGN KEY (`compliance_audit_setting_id`) REFERENCES `compliance_audit_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_audit_settings_auditees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_audit_setting_id` int(11) NOT NULL,
  `auditee_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_audit_setting_id` (`compliance_audit_setting_id`),
  KEY `auditee_id` (`auditee_id`),
  CONSTRAINT `compliance_audit_settings_auditees_ibfk_1` FOREIGN KEY (`compliance_audit_setting_id`) REFERENCES `compliance_audit_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_audit_settings_auditees_ibfk_2` FOREIGN KEY (`auditee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_exceptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `expiration` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL COMMENT '0-closed, 1-open',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_findings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `deadline` date DEFAULT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `compliance_finding_status_id` int(11) DEFAULT NULL,
  `compliance_audit_id` int(11) NOT NULL,
  `compliance_package_item_id` int(11) DEFAULT NULL,
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '1-audit finding, 2-assesed item',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `deleted` int(2) NOT NULL DEFAULT '0',
  `deleted_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_finding_status_id` (`compliance_finding_status_id`),
  KEY `compliance_audit_id` (`compliance_audit_id`),
  KEY `compliance_package_item_id` (`compliance_package_item_id`),
  CONSTRAINT `compliance_findings_ibfk_1` FOREIGN KEY (`compliance_finding_status_id`) REFERENCES `compliance_finding_statuses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `compliance_findings_ibfk_2` FOREIGN KEY (`compliance_audit_id`) REFERENCES `compliance_audits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_findings_ibfk_3` FOREIGN KEY (`compliance_package_item_id`) REFERENCES `compliance_package_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_exceptions_compliance_findings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_exception_id` int(11) NOT NULL,
  `compliance_finding_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_exception_id` (`compliance_exception_id`),
  KEY `compliance_finding_id` (`compliance_finding_id`),
  CONSTRAINT `compliance_exceptions_compliance_findings_ibfk1` FOREIGN KEY (`compliance_exception_id`) REFERENCES `compliance_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_exceptions_compliance_findings_ibfk2` FOREIGN KEY (`compliance_finding_id`) REFERENCES `compliance_findings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_exceptions_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_exception_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK__compliance_exceptions` (`compliance_exception_id`),
  KEY `FK__users` (`user_id`),
  CONSTRAINT `FK__compliance_exceptions` FOREIGN KEY (`compliance_exception_id`) REFERENCES `compliance_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_finding_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_finding_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_finding_id` (`compliance_finding_id`),
  CONSTRAINT `compliance_finding_classifications_ibfk_1` FOREIGN KEY (`compliance_finding_id`) REFERENCES `compliance_findings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_finding_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_findings_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_finding_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_finding_id` (`compliance_finding_id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  CONSTRAINT `compliance_findings_third_party_risks_ibfk1` FOREIGN KEY (`compliance_finding_id`) REFERENCES `compliance_findings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_findings_third_party_risks_ibfk2` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_managements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_package_item_id` int(11) NOT NULL,
  `compliance_treatment_strategy_id` int(11) DEFAULT NULL,
  `compliance_exception_id` int(11) DEFAULT NULL,
  `legal_id` int(11) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `efficacy` int(3) NOT NULL,
  `description` text NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_package_item_id` (`compliance_package_item_id`),
  KEY `compliance_treatment_strategy_id` (`compliance_treatment_strategy_id`),
  KEY `compliance_exception_id` (`compliance_exception_id`),
  KEY `FK_compliance_managements_legals` (`legal_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `FK_compliance_managements_legals` FOREIGN KEY (`legal_id`) REFERENCES `legals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_ibfk_1` FOREIGN KEY (`compliance_package_item_id`) REFERENCES `compliance_package_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_ibfk_2` FOREIGN KEY (`compliance_treatment_strategy_id`) REFERENCES `compliance_treatment_strategies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_ibfk_3` FOREIGN KEY (`compliance_exception_id`) REFERENCES `compliance_exceptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_ibfk_4` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_managements_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_management_id` int(11) DEFAULT NULL,
  `project_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_compliance_managements_projects_compliance_managements` (`compliance_management_id`),
  KEY `FK_compliance_managements_projects_projects` (`project_id`),
  CONSTRAINT `FK_compliance_managements_projects_compliance_managements` FOREIGN KEY (`compliance_management_id`) REFERENCES `compliance_managements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_compliance_managements_projects_projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_managements_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_management_id` int(11) NOT NULL,
  `risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_id` (`risk_id`),
  KEY `compliance_management_id` (`compliance_management_id`),
  CONSTRAINT `compliance_managements_risks_ibfk_1` FOREIGN KEY (`compliance_management_id`) REFERENCES `compliance_managements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_risks_ibfk_2` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_managements_security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_management_id` int(11) NOT NULL,
  `security_policy_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_management_id` (`compliance_management_id`),
  KEY `security_policy_id` (`security_policy_id`),
  CONSTRAINT `compliance_managements_security_policies_ibfk_1` FOREIGN KEY (`compliance_management_id`) REFERENCES `compliance_managements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_security_policies_ibfk_2` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_managements_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_management_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_management_id` (`compliance_management_id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `compliance_managements_security_services_ibfk_1` FOREIGN KEY (`compliance_management_id`) REFERENCES `compliance_managements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_security_services_ibfk_2` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_managements_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_management_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_management_id` (`compliance_management_id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  CONSTRAINT `compliance_managements_third_party_risks_ibfk_1` FOREIGN KEY (`compliance_management_id`) REFERENCES `compliance_managements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_third_party_risks_ibfk_2` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_packages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` varchar(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `third_party_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_id` (`third_party_id`),
  CONSTRAINT `compliance_packages_ibfk_1` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_package_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `audit_questionaire` text NOT NULL,
  `compliance_package_id` int(11) NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_package_id` (`compliance_package_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `compliance_package_items_ibfk_1` FOREIGN KEY (`compliance_package_id`) REFERENCES `compliance_packages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_package_items_ibfk_2` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


CREATE TABLE `compliance_treatment_strategies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


CREATE TABLE `cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(128) NOT NULL,
  `execution_time` float DEFAULT NULL,
  `status` enum('success','error') DEFAULT 'success',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `custom_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(155) NOT NULL,
  `name` varchar(155) NOT NULL,
  `slug` varchar(155) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `model` (`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `custom_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custom_form_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `type` int(3) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `custom_form_id` (`custom_form_id`),
  CONSTRAINT `FK_custom_fields_custom_forms` FOREIGN KEY (`custom_form_id`) REFERENCES `custom_forms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `custom_field_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `custom_field_id` int(11) NOT NULL,
  `value` varchar(155) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `custom_field_id` (`custom_field_id`),
  CONSTRAINT `FK_custom_field_options_custom_fields` FOREIGN KEY (`custom_field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `custom_field_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(155) NOT NULL,
  `status` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `model` (`model`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


CREATE TABLE `custom_field_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `custom_field_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_name` (`custom_field_id`),
  KEY `model` (`model`),
  KEY `foreign_key` (`foreign_key`),
  CONSTRAINT `FK_custom_field_values_custom_fields` FOREIGN KEY (`custom_field_id`) REFERENCES `custom_fields` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `data_asset_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;


CREATE TABLE `data_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `data_asset_status_id` int(11) DEFAULT NULL,
  `asset_id` int(11) NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `data_asset_status_id` (`data_asset_status_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `data_assets_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `data_assets_ibfk_2` FOREIGN KEY (`data_asset_status_id`) REFERENCES `data_asset_statuses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `data_assets_ibfk_3` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `data_assets_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `data_asset_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_data_assets_projects_projects` (`project_id`),
  KEY `FK_data_assets_projects_data_assets` (`data_asset_id`),
  CONSTRAINT `FK_data_assets_projects_data_assets` FOREIGN KEY (`data_asset_id`) REFERENCES `data_assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_data_assets_projects_projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `data_assets_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_asset_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `data_assets_third_parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_asset_id` int(11) NOT NULL,
  `third_party_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(155) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `audit_metric` text NOT NULL,
  `audit_criteria` text NOT NULL,
  `metrics_last_missing` int(1) NOT NULL,
  `ongoing_corrective_actions` int(1) NOT NULL DEFAULT '0',
  `status` enum('draft','discarded','current') NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `goals_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goal_audit_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_id` (`goal_id`),
  CONSTRAINT `goal_audit_dates_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goal_audit_improvements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_audit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_audit_id` (`goal_audit_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `goal_audit_improvements_ibfk_1` FOREIGN KEY (`goal_audit_id`) REFERENCES `goal_audits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `goal_audit_improvements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goal_audit_improvements_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_audit_improvement_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_audit_improvement_id` (`goal_audit_improvement_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `goal_audit_improvements_projects_ibfk_1` FOREIGN KEY (`goal_audit_improvement_id`) REFERENCES `goal_audit_improvements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `goal_audit_improvements_projects_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goal_audit_improvements_security_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_audit_improvement_id` int(11) NOT NULL,
  `security_incident_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_audit_improvement_id` (`goal_audit_improvement_id`),
  KEY `security_incident_id` (`security_incident_id`),
  CONSTRAINT `goal_audit_improvements_security_incidents_ibfk_1` FOREIGN KEY (`goal_audit_improvement_id`) REFERENCES `goal_audit_improvements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `goal_audit_improvements_security_incidents_ibfk_2` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goal_audits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `audit_metric_description` text NOT NULL,
  `audit_success_criteria` text NOT NULL,
  `result` int(1) DEFAULT NULL COMMENT 'null-not defined, 0-fail, 1-pass',
  `result_description` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `planned_date` date NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_id` (`goal_id`),
  KEY `user_id` (`user_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `goal_audits_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `goal_audits_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `goal_audits_ibfk_3` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goals_program_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `program_issue_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_id` (`goal_id`),
  KEY `program_issue_id` (`program_issue_id`),
  CONSTRAINT `goals_program_issues_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `goals_program_issues_ibfk_2` FOREIGN KEY (`program_issue_id`) REFERENCES `program_issues` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goals_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_id` (`goal_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `goals_projects_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `goals_projects_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goals_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `risk_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_id` (`goal_id`),
  KEY `risk_id` (`risk_id`),
  CONSTRAINT `goals_risks_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `goals_risks_ibfk_2` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goals_security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `security_policy_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_id` (`goal_id`),
  KEY `security_policy_id` (`security_policy_id`),
  CONSTRAINT `goals_security_policies_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `goals_security_policies_ibfk_2` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goals_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_id` (`goal_id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `goals_security_services_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `goals_security_services_ibfk_2` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `goals_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goal_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `goal_id` (`goal_id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  CONSTRAINT `goals_third_party_risks_ibfk_1` FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `goals_third_party_risks_ibfk_2` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `status` int(11) DEFAULT '1' COMMENT '0-non active, 1-active',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;


CREATE TABLE `issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(150) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` enum('open','closed') NOT NULL DEFAULT 'open',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_issues_users` (`user_id`),
  CONSTRAINT `FK_issues_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `ldap_connectors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `host` varchar(150) NOT NULL,
  `domain` varchar(150) DEFAULT NULL,
  `port` int(11) NOT NULL DEFAULT '389',
  `ldap_bind_dn` varchar(150) NOT NULL,
  `ldap_bind_pw` varchar(150) NOT NULL,
  `ldap_base_dn` varchar(150) NOT NULL,
  `type` enum('authenticator','group') NOT NULL,
  `ldap_auth_filter` varchar(150) DEFAULT '(| (sn=%USERNAME%) )',
  `ldap_auth_attribute` varchar(150) DEFAULT NULL,
  `ldap_name_attribute` varchar(150) DEFAULT NULL,
  `ldap_email_attribute` varchar(150) DEFAULT NULL,
  `ldap_memberof_attribute` varchar(150) DEFAULT NULL,
  `ldap_grouplist_filter` varchar(150) DEFAULT NULL,
  `ldap_grouplist_name` varchar(150) DEFAULT NULL,
  `ldap_groupmemberlist_filter` varchar(150) DEFAULT NULL,
  `ldap_group_account_attribute` varchar(150) DEFAULT NULL,
  `ldap_group_fetch_email_type` varchar(150) DEFAULT NULL,
  `ldap_group_email_attribute` varchar(150) DEFAULT NULL,
  `ldap_group_mail_domain` varchar(150) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0-disabled,1-active',
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `ldap_connector_authentication` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_users` int(1) NOT NULL,
  `auth_users_id` int(11) DEFAULT NULL,
  `auth_awareness` int(1) NOT NULL,
  `auth_awareness_id` int(11) DEFAULT NULL,
  `auth_policies` int(1) NOT NULL,
  `auth_policies_id` int(11) DEFAULT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auth_users_id` (`auth_users_id`),
  KEY `auth_awareness_id` (`auth_awareness_id`),
  KEY `auth_policies_id` (`auth_policies_id`),
  CONSTRAINT `ldap_connector_authentication_ibfk_1` FOREIGN KEY (`auth_users_id`) REFERENCES `ldap_connectors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `ldap_connector_authentication_ibfk_2` FOREIGN KEY (`auth_awareness_id`) REFERENCES `ldap_connectors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `ldap_connector_authentication_ibfk_3` FOREIGN KEY (`auth_policies_id`) REFERENCES `ldap_connectors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE `legals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `risk_magnifier` float DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `legals_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `legals_third_parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `legal_id` int(11) NOT NULL,
  `third_party_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `legal_id` (`legal_id`),
  KEY `third_party_id` (`third_party_id`),
  CONSTRAINT `legals_third_parties_ibfk_1` FOREIGN KEY (`legal_id`) REFERENCES `legals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `legals_third_parties_ibfk_2` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `legals_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `legal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `legal_id` (`legal_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `legals_users_ibfk_1` FOREIGN KEY (`legal_id`) REFERENCES `legals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `legals_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `log_security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_policy_id` int(11) NOT NULL,
  `index` varchar(100) NOT NULL,
  `short_description` varchar(150) NOT NULL,
  `description` text,
  `document_type` enum('policy','standard','procedure') NOT NULL,
  `version` varchar(50) NOT NULL,
  `published_date` date NOT NULL,
  `next_review_date` date NOT NULL,
  `permission` enum('public','private','logged') NOT NULL,
  `ldap_connector_id` int(11) DEFAULT NULL,
  `asset_label_id` int(11) DEFAULT NULL,
  `user_edit_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `notification_system_item_custom_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_id` int(11) NOT NULL,
  `custom_identifier` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_id` (`notification_system_item_id`),
  CONSTRAINT `notification_system_item_custom_roles_ibfk_1` FOREIGN KEY (`notification_system_item_id`) REFERENCES `notification_system_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `notification_system_items_objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_id` int(11) NOT NULL,
  `model` varchar(250) NOT NULL,
  `foreign_key` int(11) DEFAULT NULL,
  `status_feedback` int(2) NOT NULL DEFAULT '0' COMMENT '0-ok, 1- waiting for feedback, 2-feedback ignored',
  `log_count` int(11) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '0-disabled, 1-enabled',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_id` (`notification_system_item_id`),
  CONSTRAINT `notification_system_items_objects_ibfk_1` FOREIGN KEY (`notification_system_item_id`) REFERENCES `notification_system_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `notification_system_item_custom_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_object_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `notification_system_item_object_id` (`notification_system_item_object_id`),
  CONSTRAINT `notification_system_item_custom_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notification_system_item_custom_users_ibfk_3` FOREIGN KEY (`notification_system_item_object_id`) REFERENCES `notification_system_items_objects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `notification_system_item_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_id` (`notification_system_item_id`),
  CONSTRAINT `notification_system_item_emails_ibfk_1` FOREIGN KEY (`notification_system_item_id`) REFERENCES `notification_system_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `notification_system_item_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_object_id` int(11) NOT NULL,
  `is_new` int(1) NOT NULL DEFAULT '1' COMMENT '1-new, 0-reminder',
  `feedback_resolved` int(1) DEFAULT '0' COMMENT '1-feedback entered',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_object_id` (`notification_system_item_object_id`),
  CONSTRAINT `notification_system_item_logs_ibfk_1` FOREIGN KEY (`notification_system_item_object_id`) REFERENCES `notification_system_items_objects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `notification_system_item_feedbacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_log_id` int(11) NOT NULL,
  `notification_system_item_object_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_log_id` (`notification_system_item_log_id`),
  KEY `user_id` (`user_id`),
  KEY `notification_system_item_object_id` (`notification_system_item_object_id`),
  CONSTRAINT `notification_system_item_feedbacks_ibfk_1` FOREIGN KEY (`notification_system_item_log_id`) REFERENCES `notification_system_item_logs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notification_system_item_feedbacks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notification_system_item_feedbacks_ibfk_4` FOREIGN KEY (`notification_system_item_object_id`) REFERENCES `notification_system_items_objects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `notification_system_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `model` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `email_notification` int(1) NOT NULL DEFAULT '0',
  `header_notification` int(1) NOT NULL DEFAULT '0',
  `feedback` int(1) NOT NULL DEFAULT '0',
  `chase_interval` int(2) DEFAULT NULL,
  `chase_amount` int(3) DEFAULT NULL COMMENT 'how many times a notification will be remindered',
  `trigger_period` int(5) DEFAULT NULL COMMENT 'awareness uses this field',
  `automated` int(1) NOT NULL DEFAULT '0',
  `email_customized` int(1) NOT NULL DEFAULT '0',
  `email_subject` varchar(255) NOT NULL,
  `email_body` text NOT NULL,
  `report_send_empty_results` int(2) unsigned DEFAULT NULL,
  `report_attachment_type` int(2) unsigned DEFAULT NULL,
  `advanced_filter_id` int(11) DEFAULT NULL,
  `type` varchar(45) NOT NULL,
  `status_feedback` int(2) NOT NULL DEFAULT '0' COMMENT '0-ok, 1- waiting for feedback, 2-feedback ignored',
  `log_count` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `advanced_filter_id` (`advanced_filter_id`),
  CONSTRAINT `notification_system_items_ibfk_1` FOREIGN KEY (`advanced_filter_id`) REFERENCES `advanced_filters` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `notification_system_items_scopes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `custom_identifier` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_id` (`notification_system_item_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notification_system_items_scopes_ibfk_1` FOREIGN KEY (`notification_system_item_id`) REFERENCES `notification_system_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notification_system_items_scopes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `notification_system_items_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_id` (`notification_system_item_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notification_system_items_users_ibfk_1` FOREIGN KEY (`notification_system_item_id`) REFERENCES `notification_system_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notification_system_items_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `model` varchar(150) NOT NULL,
  `user_id` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1-new, 0-seen',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `policy_exceptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `expiration` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL COMMENT '0-closed, 1-open',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `policy_exceptions_ibfk_3` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `policy_exception_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `policy_exception_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `policy_exception_id` (`policy_exception_id`),
  CONSTRAINT `policy_exception_classifications_ibfk_1` FOREIGN KEY (`policy_exception_id`) REFERENCES `policy_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `policy_exceptions_security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `policy_exception_id` int(11) NOT NULL,
  `security_policy_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `policy_exception_id` (`policy_exception_id`),
  KEY `security_policy_id` (`security_policy_id`),
  CONSTRAINT `policy_exceptions_security_policies_ibfk_1` FOREIGN KEY (`policy_exception_id`) REFERENCES `policy_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `policy_exceptions_security_policies_ibfk_2` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `policy_exceptions_third_parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `policy_exception_id` int(11) NOT NULL,
  `third_party_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_policy_exceptions_third_parties_policy_exceptions` (`policy_exception_id`),
  KEY `FK_policy_exceptions_third_parties_third_parties` (`third_party_id`),
  CONSTRAINT `FK_policy_exceptions_third_parties_policy_exceptions` FOREIGN KEY (`policy_exception_id`) REFERENCES `policy_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_policy_exceptions_third_parties_third_parties` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `policy_exceptions_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `policy_exception_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_policy_exceptions_users_policy_exceptions` (`policy_exception_id`),
  KEY `FK_policy_exceptions_users_users` (`user_id`),
  CONSTRAINT `FK_policy_exceptions_users_policy_exceptions` FOREIGN KEY (`policy_exception_id`) REFERENCES `policy_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_policy_exceptions_users_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `policy_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `processes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_unit_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `rto` int(11) DEFAULT NULL,
  `rpo` int(11) DEFAULT NULL,
  `rpd` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_unit_id` (`business_unit_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `processes_ibfk_1` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `processes_ibfk_2` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `program_issues` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(155) NOT NULL,
  `issue_source` enum('internal','external') NOT NULL,
  `description` text NOT NULL,
  `status` enum('draft','discarded','current') NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `program_issue_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `program_issue_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `program_issue_id` (`program_issue_id`),
  CONSTRAINT `program_issue_types_ibfk_1` FOREIGN KEY (`program_issue_id`) REFERENCES `program_issues` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `program_scopes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `version` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `status` enum('draft','discarded','current') NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `project_achievements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `completion` int(3) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_order` int(3) NOT NULL DEFAULT '1',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `project_achievements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `project_achievements_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_achievements_ibfk_4` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `project_expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` float NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `project_id` int(11) NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `project_expenses_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `project_expenses_ibfk_2` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `project_overtime_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `current_budget` int(11) NOT NULL,
  `budget` int(11) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `project_overtime_graphs_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `project_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `goal` text NOT NULL,
  `start` date NOT NULL,
  `deadline` date NOT NULL,
  `plan_budget` int(11) DEFAULT NULL,
  `project_status_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `over_budget` int(1) NOT NULL DEFAULT '0',
  `expired_tasks` int(1) NOT NULL DEFAULT '0',
  `expired` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_status_id` (`project_status_id`),
  KEY `user_id` (`user_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`project_status_id`) REFERENCES `project_statuses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `projects_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `risk_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_projects_risks_projects` (`project_id`),
  KEY `FK_projects_risks_risks` (`risk_id`),
  CONSTRAINT `FK_projects_risks_projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_projects_risks_risks` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `projects_security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `security_policy_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_projects_security_policies_projects` (`project_id`),
  KEY `FK_projects_security_policies_security_policies` (`security_policy_id`),
  CONSTRAINT `FK_projects_security_policies_projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_projects_security_policies_security_policies` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `projects_security_service_audit_improvements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `security_service_audit_improvement_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `project_id` (`project_id`),
  KEY `security_service_audit_improvement_id` (`security_service_audit_improvement_id`),
  CONSTRAINT `projects_security_service_audit_improvements_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `projects_security_service_audit_improvements_ibfk_2` FOREIGN KEY (`security_service_audit_improvement_id`) REFERENCES `security_service_audit_improvements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `projects_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `security_service_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_projects_security_services_projects` (`project_id`),
  KEY `FK_projects_security_services_security_services` (`security_service_id`),
  CONSTRAINT `FK_projects_security_services_projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_projects_security_services_security_services` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `projects_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) DEFAULT NULL,
  `third_party_risk_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_projects_third_party_risks_projects` (`project_id`),
  KEY `FK_projects_third_party_risks_third_party_risks` (`third_party_risk_id`),
  CONSTRAINT `FK_projects_third_party_risks_projects` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_projects_third_party_risks_third_party_risks` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` mediumtext,
  `queue_id` varchar(255) DEFAULT NULL,
  `description` text,
  `status` int(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(150) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `planned_date` date DEFAULT NULL,
  `actual_date` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `completed` int(1) NOT NULL DEFAULT '0',
  `version` varchar(150) DEFAULT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_reviews_users` (`user_id`),
  CONSTRAINT `FK_reviews_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risk_calculations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) NOT NULL,
  `method` varchar(255) NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


CREATE TABLE `risk_calculation_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_calculation_id` int(11) NOT NULL,
  `field` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_calculation_id` (`risk_calculation_id`),
  CONSTRAINT `risk_calculation_values_ibfk_1` FOREIGN KEY (`risk_calculation_id`) REFERENCES `risk_calculations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risk_classification_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `risk_classification_count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risk_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `criteria` text NOT NULL,
  `value` float DEFAULT NULL,
  `risk_classification_type_id` int(11) DEFAULT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_classification_type_id` (`risk_classification_type_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `risk_classifications_ibfk_1` FOREIGN KEY (`risk_classification_type_id`) REFERENCES `risk_classification_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `risk_classifications_ibfk_2` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risk_classifications_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_classification_id` int(11) NOT NULL,
  `risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_classification_id` (`risk_classification_id`),
  KEY `risk_id` (`risk_id`),
  CONSTRAINT `risk_classifications_risks_ibfk_1` FOREIGN KEY (`risk_classification_id`) REFERENCES `risk_classifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risk_classifications_risks_ibfk_2` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risk_classifications_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_classification_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_classification_id` (`risk_classification_id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  CONSTRAINT `risk_classifications_third_party_risks_ibfk_1` FOREIGN KEY (`risk_classification_id`) REFERENCES `risk_classifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risk_classifications_third_party_risks_ibfk_2` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risk_exceptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `expiration` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL COMMENT '0-closed, 1-open',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  KEY `FK_risk_exceptions_users` (`author_id`),
  CONSTRAINT `FK_risk_exceptions_users` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risk_exceptions_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risk_exceptions_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `risk_exception_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_id` (`risk_id`),
  KEY `risk_exception_id` (`risk_exception_id`),
  CONSTRAINT `risk_exceptions_risks_ibfk_1` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risk_exceptions_risks_ibfk_2` FOREIGN KEY (`risk_exception_id`) REFERENCES `risk_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risk_exceptions_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_exception_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risk_mitigation_strategies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


CREATE TABLE `risk_overtime_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_count` int(11) NOT NULL,
  `risk_score` int(11) NOT NULL,
  `residual_score` int(11) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `threats` text NOT NULL,
  `vulnerabilities` text NOT NULL,
  `residual_score` int(11) NOT NULL,
  `risk_score` float DEFAULT NULL,
  `risk_score_formula` text NOT NULL,
  `residual_risk` float NOT NULL,
  `user_id` int(11) NOT NULL,
  `guardian_id` int(11) NOT NULL,
  `review` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `exceptions_issues` int(1) NOT NULL DEFAULT '0',
  `controls_issues` int(1) NOT NULL DEFAULT '0',
  `control_in_design` int(1) NOT NULL DEFAULT '0',
  `expired_reviews` int(1) NOT NULL DEFAULT '0',
  `risk_above_appetite` int(1) NOT NULL DEFAULT '0',
  `risk_mitigation_strategy_id` int(11) DEFAULT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_mitigation_strategy_id` (`risk_mitigation_strategy_id`),
  KEY `user_id` (`user_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  KEY `guardian_id` (`guardian_id`),
  CONSTRAINT `risks_ibfk_2` FOREIGN KEY (`risk_mitigation_strategy_id`) REFERENCES `risk_mitigation_strategies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `risks_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `risks_ibfk_4` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risks_ibfk_5` FOREIGN KEY (`guardian_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risks_security_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `security_incident_id` int(11) NOT NULL,
  `risk_type` enum('asset-risk','third-party-risk','business-risk') NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_incident_id` (`security_incident_id`),
  CONSTRAINT `risks_security_incidents_ibfk_2` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risks_security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `security_policy_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'treatment' COMMENT '''treatment'',''incident''',
  `document_type` enum('procedure','policy','standard') NOT NULL,
  `risk_type` enum('asset-risk','third-party-risk','business-risk') NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_policy_id` (`security_policy_id`),
  CONSTRAINT `risks_security_policies_ibfk_2` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risks_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_id` (`risk_id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `risks_security_services_ibfk_1` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risks_security_services_ibfk_2` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risks_threats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `threat_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_id` (`risk_id`),
  KEY `threat_id` (`threat_id`),
  CONSTRAINT `risks_threats_ibfk_1` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risks_threats_ibfk_2` FOREIGN KEY (`threat_id`) REFERENCES `threats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `risks_vulnerabilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `vulnerability_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_id` (`risk_id`),
  KEY `vulnerability_id` (`vulnerability_id`),
  CONSTRAINT `risks_vulnerabilities_ibfk_1` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risks_vulnerabilities_ibfk_2` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `schema_migrations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;


CREATE TABLE `scopes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ciso_role_id` int(11) DEFAULT NULL,
  `ciso_deputy_id` int(11) DEFAULT NULL,
  `board_representative_id` int(11) DEFAULT NULL,
  `board_representative_deputy_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ciso_role_id` (`ciso_role_id`),
  KEY `ciso_deputy_id` (`ciso_deputy_id`),
  KEY `board_representative_id` (`board_representative_id`),
  KEY `board_representative_deputy_id` (`board_representative_deputy_id`),
  CONSTRAINT `scopes_ibfk_1` FOREIGN KEY (`ciso_role_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `scopes_ibfk_2` FOREIGN KEY (`ciso_deputy_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `scopes_ibfk_3` FOREIGN KEY (`board_representative_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `scopes_ibfk_4` FOREIGN KEY (`board_representative_deputy_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_incident_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_incident_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_incident_id` (`security_incident_id`),
  CONSTRAINT `security_incident_classifications_ibfk_1` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_incident_stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_incident_stages_security_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_incident_stage_id` int(11) NOT NULL,
  `security_incident_id` int(11) NOT NULL,
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_security_incident_stages_security_incidents` (`security_incident_id`),
  KEY `FK_security_incident_stages_security_incident_stages` (`security_incident_stage_id`),
  CONSTRAINT `FK_security_incident_stages_security_incident_stages` FOREIGN KEY (`security_incident_stage_id`) REFERENCES `security_incident_stages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_security_incident_stages_security_incidents` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_incident_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


CREATE TABLE `security_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `reporter` varchar(100) NOT NULL,
  `victim` varchar(100) NOT NULL,
  `open_date` date NOT NULL,
  `closure_date` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `type` enum('event','possible-incident','incident') NOT NULL,
  `security_incident_status_id` int(11) DEFAULT NULL,
  `security_incident_classification_id` int(11) DEFAULT NULL,
  `lifecycle_incomplete` int(11) DEFAULT '1',
  `ongoing_incident` int(1) NOT NULL DEFAULT '0',
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_incident_status_id` (`security_incident_status_id`),
  KEY `security_incident_classification_id` (`security_incident_classification_id`),
  KEY `user_id` (`user_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `security_incidents_ibfk_1` FOREIGN KEY (`security_incident_status_id`) REFERENCES `security_incident_statuses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `security_incidents_ibfk_3` FOREIGN KEY (`security_incident_classification_id`) REFERENCES `security_incident_classifications` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `security_incidents_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `security_incidents_ibfk_6` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_incidents_security_service_audit_improvements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_incident_id` int(11) NOT NULL,
  `security_service_audit_improvement_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_incident_id` (`security_incident_id`),
  KEY `security_service_audit_improvement_id` (`security_service_audit_improvement_id`),
  CONSTRAINT `security_incidents_security_service_audit_improvements_ibfk_1` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_incidents_security_service_audit_improvements_ibfk_2` FOREIGN KEY (`security_service_audit_improvement_id`) REFERENCES `security_service_audit_improvements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_incidents_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_incident_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_incident_id` (`security_incident_id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `security_incidents_security_services_ibfk_1` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_incidents_security_services_ibfk_2` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_incidents_third_parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_incident_id` int(11) NOT NULL,
  `third_party_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_incident_id` (`security_incident_id`),
  KEY `third_party_id` (`third_party_id`),
  CONSTRAINT `security_incidents_third_parties_ibfk_1` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_incidents_third_parties_ibfk_2` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `index` varchar(100) NOT NULL,
  `short_description` varchar(255) NOT NULL,
  `description` text,
  `url` text,
  `use_attachments` int(1) NOT NULL DEFAULT '0',
  `document_type` enum('policy','standard','procedure') NOT NULL,
  `version` varchar(50) NOT NULL,
  `published_date` date NOT NULL,
  `next_review_date` date NOT NULL,
  `permission` enum('public','private','logged') NOT NULL,
  `ldap_connector_id` int(11) DEFAULT NULL,
  `asset_label_id` int(11) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0-draft, 1-released',
  `expired_reviews` int(1) NOT NULL DEFAULT '0',
  `author_id` int(11) NOT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  KEY `author_id` (`author_id`),
  KEY `asset_label_id` (`asset_label_id`),
  KEY `ldap_connector_id` (`ldap_connector_id`),
  CONSTRAINT `security_policies_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_policies_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_policies_ibfk_3` FOREIGN KEY (`asset_label_id`) REFERENCES `asset_labels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `security_policies_ibfk_4` FOREIGN KEY (`ldap_connector_id`) REFERENCES `ldap_connectors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_policies_related` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_policy_id` int(11) NOT NULL,
  `related_document_id` int(11) NOT NULL,
  `document_type` enum('procedure','policy','standard') NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_policy_id` (`security_policy_id`),
  KEY `related_document_id` (`related_document_id`),
  CONSTRAINT `security_policies_related_ibfk_1` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_policies_related_ibfk_2` FOREIGN KEY (`related_document_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_policies_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_policy_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_policy_id` (`security_policy_id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `security_policies_security_services_ibfk_1` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_policies_security_services_ibfk_2` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_policies_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_policy_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_policy_id` (`security_policy_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `security_policies_users_ibfk_1` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_policies_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_policy_ldap_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_policy_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_policy_id` (`security_policy_id`),
  CONSTRAINT `security_policy_ldap_groups_ibfk_1` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_policy_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_policy_id` int(11) NOT NULL,
  `planned_date` date NOT NULL,
  `actual_review_date` date DEFAULT NULL,
  `reviewer_id` int(11) DEFAULT NULL,
  `comments` text NOT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `reviewer_id` (`reviewer_id`),
  KEY `security_policy_id` (`security_policy_id`),
  CONSTRAINT `security_policy_reviews_ibfk_1` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `security_policy_reviews_ibfk_2` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_service_audit_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `security_service_audit_dates_ibfk_1` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_service_audits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `audit_metric_description` text NOT NULL,
  `audit_success_criteria` text NOT NULL,
  `result` int(1) DEFAULT NULL COMMENT 'null-not defined, 0-fail, 1-pass',
  `result_description` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `planned_date` date NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  KEY `user_id` (`user_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `security_service_audits_ibfk_1` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_service_audits_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `security_service_audits_ibfk_3` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_service_audit_improvements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_audit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_audit_id` (`security_service_audit_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `security_service_audit_improvements_ibfk_1` FOREIGN KEY (`security_service_audit_id`) REFERENCES `security_service_audits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_service_audit_improvements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_service_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `security_service_classifications_ibfk_1` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_service_maintenance_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `security_service_maintenance_dates_ibfk_1` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_service_maintenances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `task` text NOT NULL,
  `task_conclusion` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `planned_date` date NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `result` int(1) DEFAULT NULL COMMENT 'null-not defined, 0-fail, 1-pass',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  KEY `user_id` (`user_id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `security_service_maintenances_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_service_maintenances_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `security_service_maintenances_ibfk_3` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_service_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


CREATE TABLE `security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `objective` text NOT NULL,
  `security_service_type_id` int(11) DEFAULT NULL,
  `service_classification_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `documentation_url` varchar(100) NOT NULL,
  `audit_metric_description` text NOT NULL,
  `audit_success_criteria` text NOT NULL,
  `maintenance_metric_description` text NOT NULL,
  `opex` float NOT NULL,
  `capex` float NOT NULL,
  `resource_utilization` int(11) NOT NULL,
  `audits_all_done` int(1) NOT NULL,
  `audits_last_missing` int(1) NOT NULL,
  `audits_last_passed` int(1) NOT NULL,
  `audits_improvements` int(1) NOT NULL,
  `audits_status` int(1) NOT NULL,
  `maintenances_all_done` int(1) NOT NULL,
  `maintenances_last_missing` int(1) NOT NULL,
  `maintenances_last_passed` int(1) NOT NULL,
  `ongoing_security_incident` int(1) NOT NULL DEFAULT '0',
  `security_incident_open_count` int(11) NOT NULL,
  `control_with_issues` int(1) NOT NULL DEFAULT '0',
  `ongoing_corrective_actions` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_type_id` (`security_service_type_id`),
  KEY `service_classification_id` (`service_classification_id`),
  KEY `user_id` (`user_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `security_services_ibfk_1` FOREIGN KEY (`security_service_type_id`) REFERENCES `security_service_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `security_services_ibfk_2` FOREIGN KEY (`service_classification_id`) REFERENCES `service_classifications` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `security_services_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `security_services_ibfk_4` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_services_service_contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `service_contract_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  KEY `service_contract_id` (`service_contract_id`),
  CONSTRAINT `security_services_service_contracts_ibfk_1` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_services_service_contracts_ibfk_2` FOREIGN KEY (`service_contract_id`) REFERENCES `service_contracts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_services_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  CONSTRAINT `security_services_third_party_risks_ibfk_1` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_services_third_party_risks_ibfk_2` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `security_services_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `security_services_users_ibfk_1` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_services_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `service_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `service_classifications_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `service_contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `value` int(11) NOT NULL,
  `start` date NOT NULL,
  `end` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  KEY `third_party_id` (`third_party_id`),
  CONSTRAINT `service_contracts_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `service_contracts_ibfk_2` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `setting_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL,
  `parent_slug` varchar(50) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `icon_code` varchar(150) DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `hidden` tinyint(4) DEFAULT '0',
  `order` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `FK_setting_groups_setting_groups` (`parent_slug`),
  CONSTRAINT `FK_setting_groups_setting_groups` FOREIGN KEY (`parent_slug`) REFERENCES `setting_groups` (`slug`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;


CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `active` int(1) NOT NULL DEFAULT '1',
  `name` varchar(255) NOT NULL,
  `variable` varchar(100) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `default_value` varchar(255) DEFAULT NULL,
  `values` varchar(255) DEFAULT NULL,
  `type` enum('text','number','select','multiselect','checkbox','textarea','password') NOT NULL DEFAULT 'text',
  `options` varchar(150) DEFAULT NULL,
  `hidden` int(1) NOT NULL DEFAULT '0',
  `required` int(1) NOT NULL DEFAULT '0',
  `setting_group_slug` varchar(50) DEFAULT NULL,
  `setting_type` enum('constant','config') DEFAULT 'constant',
  `order` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_settings_setting_groups` (`setting_group_slug`),
  CONSTRAINT `FK_settings_setting_groups` FOREIGN KEY (`setting_group_slug`) REFERENCES `setting_groups` (`slug`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;


CREATE TABLE `status_triggers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(155) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `config_name` varchar(155) NOT NULL,
  `column_name` varchar(155) NOT NULL,
  `value` varchar(155) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `suggestions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `suggestion` varchar(255) NOT NULL,
  `model` varchar(155) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `system_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(70) NOT NULL,
  `model_nice` varchar(70) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `item` varchar(100) NOT NULL,
  `notes` text,
  `type` int(1) NOT NULL COMMENT '1-insert, 2-update, 3-delete, 4-login, 5-wrong login',
  `workflow_status` int(1) DEFAULT NULL,
  `workflow_comment` text,
  `ip` varchar(45) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `system_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(250) NOT NULL,
  `foreign_key` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tags_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `team_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `role` varchar(155) NOT NULL,
  `responsibilities` text NOT NULL,
  `competences` text NOT NULL,
  `status` enum('active','discarded') NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `team_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `third_parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `third_party_type_id` int(11) DEFAULT NULL,
  `security_incident_count` int(11) NOT NULL,
  `security_incident_open_count` int(11) NOT NULL,
  `service_contract_count` int(11) NOT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `_hidden` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_type_id` (`third_party_type_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `third_parties_ibfk_1` FOREIGN KEY (`third_party_type_id`) REFERENCES `third_party_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `third_parties_ibfk_2` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE `third_parties_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_risk_id` int(11) NOT NULL,
  `third_party_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  KEY `third_party_id` (`third_party_id`),
  CONSTRAINT `third_parties_third_party_risks_ibfk_1` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `third_parties_third_party_risks_ibfk_2` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `third_parties_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_id` (`third_party_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `third_parties_users_ibfk_1` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `third_parties_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `third_party_audit_overtime_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_id` int(11) NOT NULL,
  `open` int(3) DEFAULT NULL,
  `closed` int(3) DEFAULT NULL,
  `expired` int(3) DEFAULT NULL,
  `no_evidence` int(3) NOT NULL,
  `waiting_evidence` int(3) NOT NULL,
  `provided_evidence` int(3) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_id` (`third_party_id`),
  CONSTRAINT `third_party_audit_overtime_graphs_ibfk_1` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `third_party_incident_overtime_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_id` int(11) NOT NULL,
  `security_incident_count` int(11) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_id` (`third_party_id`),
  CONSTRAINT `third_party_incident_overtime_graphs_ibfk_1` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `third_party_overtime_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_id` int(11) NOT NULL,
  `no_controls` int(3) NOT NULL,
  `failed_controls` int(3) NOT NULL,
  `ok_controls` int(3) NOT NULL,
  `average_effectiveness` int(3) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_id` (`third_party_id`),
  CONSTRAINT `third_party_overtime_graphs_ibfk_1` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `third_party_risk_overtime_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_count` int(11) NOT NULL,
  `risk_score` int(11) NOT NULL,
  `residual_score` int(11) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `shared_information` text NOT NULL,
  `controlled` text NOT NULL,
  `threats` text NOT NULL,
  `vulnerabilities` text NOT NULL,
  `residual_score` int(11) NOT NULL,
  `risk_score` float DEFAULT NULL,
  `risk_score_formula` text NOT NULL,
  `residual_risk` float NOT NULL,
  `user_id` int(11) NOT NULL,
  `guardian_id` int(11) NOT NULL,
  `review` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `exceptions_issues` int(1) NOT NULL DEFAULT '0',
  `controls_issues` int(1) NOT NULL DEFAULT '0',
  `control_in_design` int(1) NOT NULL DEFAULT '0',
  `expired_reviews` int(1) NOT NULL DEFAULT '0',
  `risk_above_appetite` int(1) NOT NULL DEFAULT '0',
  `risk_mitigation_strategy_id` int(11) DEFAULT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_mitigation_strategy_id` (`risk_mitigation_strategy_id`),
  KEY `user_id` (`user_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  KEY `guardian_id` (`guardian_id`),
  CONSTRAINT `third_party_risks_ibfk_2` FOREIGN KEY (`risk_mitigation_strategy_id`) REFERENCES `risk_mitigation_strategies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `third_party_risks_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `third_party_risks_ibfk_4` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `third_party_risks_ibfk_5` FOREIGN KEY (`guardian_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `third_party_risks_threats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_risk_id` int(11) NOT NULL,
  `threat_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  KEY `threat_id` (`threat_id`),
  CONSTRAINT `third_party_risks_threats_ibfk_1` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `third_party_risks_threats_ibfk_2` FOREIGN KEY (`threat_id`) REFERENCES `threats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `third_party_risks_vulnerabilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_risk_id` int(11) NOT NULL,
  `vulnerability_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  KEY `vulnerability_id` (`vulnerability_id`),
  CONSTRAINT `third_party_risks_vulnerabilities_ibfk_1` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `third_party_risks_vulnerabilities_ibfk_2` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `third_party_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


CREATE TABLE `threats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;


CREATE TABLE `tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `hash` varchar(50) DEFAULT NULL,
  `data` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime NOT NULL,
  `expires` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `surname` varchar(45) DEFAULT NULL,
  `group_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `login` varchar(45) NOT NULL,
  `password` varchar(100) NOT NULL,
  `language` varchar(10) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '0-non active, 1-active',
  `blocked` int(1) NOT NULL DEFAULT '0',
  `local_account` int(3) DEFAULT '1',
  `api_allow` int(2) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


CREATE TABLE `user_bans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `until` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_bans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `vulnerabilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;


CREATE TABLE `workflow_acknowledgements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('edit','delete') NOT NULL,
  `model` varchar(255) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `resolved` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `workflow_acknowledgements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `workflow_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `workflow_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `ip` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `workflow_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `notifications` int(1) NOT NULL DEFAULT '1',
  `parent_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `workflows_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;


CREATE TABLE `workflows_all_approver_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `foreign_key` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_id` (`workflow_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `workflows_all_approver_items_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `workflows_all_approver_items_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `workflows_all_validator_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `foreign_key` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_id` (`workflow_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `workflows_all_validator_items_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `workflows_all_validator_items_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `workflows_approver_scopes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `custom_identifier` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_id` (`workflow_id`),
  KEY `scope_id` (`user_id`),
  CONSTRAINT `workflows_approver_scopes_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `workflows_approver_scopes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `workflows_approvers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `workflow_id` (`workflow_id`),
  CONSTRAINT `workflows_approvers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `workflows_approvers_ibfk_2` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `workflows_custom_approvers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `custom_identifier` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `workflows_custom_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `custom_identifier` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `workflows_validator_scopes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `custom_identifier` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_id` (`workflow_id`),
  KEY `scope_id` (`user_id`),
  CONSTRAINT `workflows_validator_scopes_ibfk_1` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `workflows_validator_scopes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `workflows_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `workflow_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `workflow_id` (`workflow_id`),
  CONSTRAINT `workflows_validators_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `workflows_validators_ibfk_2` FOREIGN KEY (`workflow_id`) REFERENCES `workflows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




SET FOREIGN_KEY_CHECKS = @PREVIOUS_FOREIGN_KEY_CHECKS;


SET @PREVIOUS_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS = 0;


INSERT INTO `acos` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES 
	(1,NULL,NULL,NULL,'controllers',1,3070),
	(2,1,NULL,NULL,'Ajax',2,25),
	(3,2,NULL,NULL,'modalSidebarWidget',3,4),
	(4,2,NULL,NULL,'cancelAction',5,6),
	(5,1,NULL,NULL,'AssetClassifications',26,57),
	(6,5,NULL,NULL,'index',27,28),
	(7,5,NULL,NULL,'delete',29,30),
	(8,5,NULL,NULL,'add',31,32),
	(9,5,NULL,NULL,'edit',33,34),
	(10,5,NULL,NULL,'addClassificationType',35,36),
	(11,5,NULL,NULL,'cancelAction',37,38),
	(12,1,NULL,NULL,'AssetLabels',58,87),
	(13,12,NULL,NULL,'index',59,60),
	(14,12,NULL,NULL,'delete',61,62),
	(15,12,NULL,NULL,'add',63,64),
	(16,12,NULL,NULL,'edit',65,66),
	(17,12,NULL,NULL,'cancelAction',67,68),
	(18,1,NULL,NULL,'AssetMediaTypes',88,117),
	(19,18,NULL,NULL,'index',89,90),
	(20,18,NULL,NULL,'liveEdit',91,92),
	(21,18,NULL,NULL,'add',93,94),
	(22,18,NULL,NULL,'delete',95,96),
	(23,18,NULL,NULL,'cancelAction',97,98),
	(24,1,NULL,NULL,'Assets',118,153),
	(25,24,NULL,NULL,'index',119,120),
	(26,24,NULL,NULL,'delete',121,122),
	(27,24,NULL,NULL,'add',123,124),
	(28,24,NULL,NULL,'edit',125,126),
	(30,24,NULL,NULL,'exportPdf',127,128),
	(31,24,NULL,NULL,'cancelAction',129,130),
	(32,1,NULL,NULL,'Attachments',154,193),
	(33,32,NULL,NULL,'index',155,156),
	(34,32,NULL,NULL,'delete',157,158),
	(36,32,NULL,NULL,'add',159,160),
	(37,32,NULL,NULL,'addAjax',161,162),
	(38,32,NULL,NULL,'getList',163,164),
	(39,32,NULL,NULL,'download',165,166),
	(40,32,NULL,NULL,'sendAuditWarningEmails',167,168),
	(41,32,NULL,NULL,'cancelAction',169,170),
	(42,1,NULL,NULL,'Awareness',194,237),
	(43,42,NULL,NULL,'index',195,196),
	(44,42,NULL,NULL,'training',197,198),
	(45,42,NULL,NULL,'demo',199,200),
	(46,42,NULL,NULL,'video',201,202),
	(47,42,NULL,NULL,'questionnaire',203,204),
	(48,42,NULL,NULL,'results',205,206),
	(49,42,NULL,NULL,'login',207,208),
	(50,42,NULL,NULL,'logout',209,210),
	(54,42,NULL,NULL,'cancelAction',211,212),
	(55,1,NULL,NULL,'AwarenessPrograms',238,289),
	(56,55,NULL,NULL,'index',239,240),
	(57,55,NULL,NULL,'delete',241,242),
	(58,55,NULL,NULL,'add',243,244),
	(59,55,NULL,NULL,'edit',245,246),
	(60,55,NULL,NULL,'ldapGroups',247,248),
	(61,55,NULL,NULL,'ldapIgnoredUsers',249,250),
	(62,55,NULL,NULL,'deleteVideo',251,252),
	(63,55,NULL,NULL,'deleteQuestionnaire',253,254),
	(64,55,NULL,NULL,'start',255,256),
	(65,55,NULL,NULL,'stop',257,258),
	(66,55,NULL,NULL,'demo',259,260),
	(67,55,NULL,NULL,'clean',261,262),
	(68,55,NULL,NULL,'initEmailFromComponent',263,264),
	(70,55,NULL,NULL,'cancelAction',265,266),
	(71,1,NULL,NULL,'BackupRestore',290,317),
	(75,1,NULL,NULL,'BusinessContinuities',318,355),
	(76,75,NULL,NULL,'index',319,320),
	(77,75,NULL,NULL,'delete',321,322),
	(78,75,NULL,NULL,'add',323,324),
	(79,75,NULL,NULL,'edit',325,326),
	(80,75,NULL,NULL,'calculateRiskScoreAjax',327,328),
	(81,75,NULL,NULL,'getThreatsVulnerabilities',329,330),
	(82,75,NULL,NULL,'exportPdf',331,332),
	(83,75,NULL,NULL,'cancelAction',333,334),
	(84,1,NULL,NULL,'BusinessContinuityPlanAuditImprovements',356,383),
	(85,84,NULL,NULL,'delete',357,358),
	(86,84,NULL,NULL,'add',359,360),
	(87,84,NULL,NULL,'edit',361,362),
	(88,84,NULL,NULL,'cancelAction',363,364),
	(89,1,NULL,NULL,'BusinessContinuityPlanAudits',384,415),
	(90,89,NULL,NULL,'index',385,386),
	(91,89,NULL,NULL,'delete',387,388),
	(92,89,NULL,NULL,'edit',389,390),
	(93,89,NULL,NULL,'cancelAction',391,392),
	(94,1,NULL,NULL,'BusinessContinuityPlans',416,461),
	(95,94,NULL,NULL,'index',417,418),
	(96,94,NULL,NULL,'delete',419,420),
	(97,94,NULL,NULL,'acknowledge',421,422),
	(98,94,NULL,NULL,'acknowledgeItem',423,424),
	(99,94,NULL,NULL,'add',425,426),
	(100,94,NULL,NULL,'edit',427,428),
	(101,94,NULL,NULL,'deleteProductionJoins',429,430),
	(102,94,NULL,NULL,'auditCalendarFormEntry',431,432),
	(103,94,NULL,NULL,'export',433,434),
	(104,94,NULL,NULL,'exportAudits',435,436),
	(105,94,NULL,NULL,'exportTask',437,438),
	(106,94,NULL,NULL,'exportPdf',439,440),
	(107,94,NULL,NULL,'cancelAction',441,442),
	(108,1,NULL,NULL,'BusinessContinuityTasks',462,491),
	(109,108,NULL,NULL,'delete',463,464),
	(110,108,NULL,NULL,'add',465,466),
	(111,108,NULL,NULL,'edit',467,468),
	(112,108,NULL,NULL,'cancelAction',469,470),
	(113,1,NULL,NULL,'BusinessUnits',492,521),
	(114,113,NULL,NULL,'index',493,494),
	(115,113,NULL,NULL,'delete',495,496),
	(116,113,NULL,NULL,'add',497,498),
	(117,113,NULL,NULL,'edit',499,500),
	(118,113,NULL,NULL,'cancelAction',501,502),
	(119,1,NULL,NULL,'Comments',522,561),
	(120,119,NULL,NULL,'index',523,524),
	(121,119,NULL,NULL,'delete',525,526),
	(122,119,NULL,NULL,'add',527,528),
	(123,119,NULL,NULL,'addAjax',529,530),
	(124,119,NULL,NULL,'edit',531,532),
	(125,119,NULL,NULL,'listComments',533,534),
	(126,119,NULL,NULL,'sendAuditWarningEmails',535,536),
	(127,119,NULL,NULL,'getIndexUrlFromComponent',537,538),
	(128,119,NULL,NULL,'initEmailFromComponent',539,540),
	(129,119,NULL,NULL,'cancelAction',541,542),
	(130,1,NULL,NULL,'ComplianceAuditFeedbacks',562,593),
	(131,130,NULL,NULL,'index',563,564),
	(132,130,NULL,NULL,'delete',565,566),
	(133,130,NULL,NULL,'add',567,568),
	(134,130,NULL,NULL,'edit',569,570),
	(135,130,NULL,NULL,'addClassificationType',571,572),
	(136,130,NULL,NULL,'cancelAction',573,574),
	(137,1,NULL,NULL,'ComplianceAuditSettings',594,623),
	(138,137,NULL,NULL,'edit',595,596),
	(139,137,NULL,NULL,'setup',597,598),
	(140,137,NULL,NULL,'sendNotifications',599,600),
	(141,137,NULL,NULL,'cancelAction',601,602),
	(142,1,NULL,NULL,'ComplianceAudits',624,675),
	(143,142,NULL,NULL,'index',625,626),
	(144,142,NULL,NULL,'analyze',627,628),
	(145,142,NULL,NULL,'analyzeAuditee',629,630),
	(146,142,NULL,NULL,'auditeeFeedback',631,632),
	(147,142,NULL,NULL,'delete',633,634),
	(148,142,NULL,NULL,'add',635,636),
	(149,142,NULL,NULL,'edit',637,638),
	(150,142,NULL,NULL,'duplicate',639,640),
	(151,142,NULL,NULL,'export',641,642),
	(152,142,NULL,NULL,'cancelAction',643,644),
	(153,1,NULL,NULL,'ComplianceExceptions',676,707),
	(154,153,NULL,NULL,'index',677,678),
	(155,153,NULL,NULL,'delete',679,680),
	(156,153,NULL,NULL,'add',681,682),
	(157,153,NULL,NULL,'edit',683,684),
	(159,153,NULL,NULL,'exportPdf',685,686),
	(160,153,NULL,NULL,'cancelAction',687,688),
	(161,1,NULL,NULL,'ComplianceFindings',708,745),
	(162,161,NULL,NULL,'index',709,710),
	(163,161,NULL,NULL,'delete',711,712),
	(164,161,NULL,NULL,'add',713,714),
	(165,161,NULL,NULL,'edit',715,716),
	(166,161,NULL,NULL,'cancelAction',717,718),
	(167,1,NULL,NULL,'ComplianceManagements',746,781),
	(168,167,NULL,NULL,'index',747,748),
	(169,167,NULL,NULL,'analyze',749,750),
	(170,167,NULL,NULL,'add',751,752),
	(171,167,NULL,NULL,'edit',753,754),
	(172,167,NULL,NULL,'export',755,756),
	(173,167,NULL,NULL,'exportPdf',757,758),
	(174,167,NULL,NULL,'cancelAction',759,760),
	(175,1,NULL,NULL,'CompliancePackageItems',782,811),
	(176,175,NULL,NULL,'delete',783,784),
	(177,175,NULL,NULL,'add',785,786),
	(178,175,NULL,NULL,'edit',787,788),
	(179,175,NULL,NULL,'cancelAction',789,790),
	(180,1,NULL,NULL,'CompliancePackages',812,845),
	(181,180,NULL,NULL,'index',813,814),
	(182,180,NULL,NULL,'delete',815,816),
	(183,180,NULL,NULL,'add',817,818),
	(184,180,NULL,NULL,'edit',819,820),
	(185,180,NULL,NULL,'import',821,822),
	(186,180,NULL,NULL,'cancelAction',823,824),
	(187,1,NULL,NULL,'ComplianceReports',846,871),
	(188,187,NULL,NULL,'index',847,848),
	(189,187,NULL,NULL,'awareness',849,850),
	(190,187,NULL,NULL,'cancelAction',851,852),
	(191,1,NULL,NULL,'Cron',872,905),
	(192,191,NULL,NULL,'daily',873,874),
	(193,191,NULL,NULL,'yearly',875,876),
	(196,191,NULL,NULL,'getIndexUrlFromComponent',877,878),
	(197,191,NULL,NULL,'initEmailFromComponent',879,880),
	(198,191,NULL,NULL,'cancelAction',881,882),
	(199,1,NULL,NULL,'DataAssets',906,937),
	(200,199,NULL,NULL,'index',907,908),
	(201,199,NULL,NULL,'delete',909,910),
	(202,199,NULL,NULL,'add',911,912),
	(203,199,NULL,NULL,'edit',913,914),
	(204,199,NULL,NULL,'export',915,916),
	(205,199,NULL,NULL,'cancelAction',917,918),
	(206,1,NULL,NULL,'GoalAuditImprovements',938,965),
	(207,206,NULL,NULL,'delete',939,940),
	(208,206,NULL,NULL,'add',941,942),
	(209,206,NULL,NULL,'edit',943,944),
	(210,206,NULL,NULL,'cancelAction',945,946),
	(211,1,NULL,NULL,'GoalAudits',966,993),
	(212,211,NULL,NULL,'index',967,968),
	(213,211,NULL,NULL,'delete',969,970),
	(214,211,NULL,NULL,'edit',971,972),
	(215,211,NULL,NULL,'cancelAction',973,974),
	(216,1,NULL,NULL,'Goals',994,1027),
	(217,216,NULL,NULL,'index',995,996),
	(218,216,NULL,NULL,'delete',997,998),
	(219,216,NULL,NULL,'add',999,1000),
	(220,216,NULL,NULL,'edit',1001,1002),
	(221,216,NULL,NULL,'auditCalendarFormEntry',1003,1004),
	(222,216,NULL,NULL,'exportPdf',1005,1006),
	(223,216,NULL,NULL,'cancelAction',1007,1008),
	(224,1,NULL,NULL,'Groups',1028,1057),
	(225,224,NULL,NULL,'index',1029,1030),
	(226,224,NULL,NULL,'add',1031,1032),
	(227,224,NULL,NULL,'edit',1033,1034),
	(228,224,NULL,NULL,'delete',1035,1036),
	(229,224,NULL,NULL,'cancelAction',1037,1038),
	(230,1,NULL,NULL,'Issues',1058,1087),
	(231,230,NULL,NULL,'index',1059,1060),
	(232,230,NULL,NULL,'add',1061,1062),
	(233,230,NULL,NULL,'edit',1063,1064),
	(234,230,NULL,NULL,'delete',1065,1066),
	(235,230,NULL,NULL,'cancelAction',1067,1068),
	(236,1,NULL,NULL,'LdapConnectors',1088,1121),
	(237,236,NULL,NULL,'index',1089,1090),
	(238,236,NULL,NULL,'delete',1091,1092),
	(239,236,NULL,NULL,'add',1093,1094),
	(240,236,NULL,NULL,'edit',1095,1096),
	(241,236,NULL,NULL,'authentication',1097,1098),
	(242,236,NULL,NULL,'testLdap',1099,1100),
	(243,236,NULL,NULL,'cancelAction',1101,1102),
	(244,1,NULL,NULL,'Legals',1122,1151),
	(245,244,NULL,NULL,'index',1123,1124),
	(246,244,NULL,NULL,'delete',1125,1126),
	(247,244,NULL,NULL,'add',1127,1128),
	(248,244,NULL,NULL,'edit',1129,1130),
	(249,244,NULL,NULL,'cancelAction',1131,1132),
	(250,1,NULL,NULL,'NotificationSystem',1152,1203),
	(252,250,NULL,NULL,'index',1153,1154),
	(253,250,NULL,NULL,'enableForObject',1155,1156),
	(254,250,NULL,NULL,'enableForAll',1157,1158),
	(255,250,NULL,NULL,'disableForObject',1159,1160),
	(256,250,NULL,NULL,'disableForAll',1161,1162),
	(258,250,NULL,NULL,'associateAjax',1163,1164),
	(259,250,NULL,NULL,'remove',1165,1166),
	(260,250,NULL,NULL,'attach',1167,1168),
	(261,250,NULL,NULL,'delete',1169,1170),
	(262,250,NULL,NULL,'addNotification',1171,1172),
	(263,250,NULL,NULL,'feedback',1173,1174),
	(264,250,NULL,NULL,'addFeedbackAttachment',1175,1176),
	(265,250,NULL,NULL,'cancelAction',1177,1178),
	(266,1,NULL,NULL,'Notifications',1204,1227),
	(267,266,NULL,NULL,'setNotificationsAsSeen',1205,1206),
	(268,266,NULL,NULL,'cancelAction',1207,1208),
	(269,1,NULL,NULL,'Pages',1228,1257),
	(271,269,NULL,NULL,'dashboard',1229,1230),
	(272,269,NULL,NULL,'about',1231,1232),
	(273,269,NULL,NULL,'cancelAction',1233,1234),
	(274,1,NULL,NULL,'Policy',1258,1297),
	(275,274,NULL,NULL,'login',1259,1260),
	(276,274,NULL,NULL,'guestLogin',1261,1262),
	(277,274,NULL,NULL,'logout',1263,1264),
	(278,274,NULL,NULL,'index',1265,1266),
	(279,274,NULL,NULL,'isGuest',1267,1268),
	(280,274,NULL,NULL,'document',1269,1270),
	(281,274,NULL,NULL,'documentDirect',1271,1272),
	(282,274,NULL,NULL,'documentPdf',1273,1274),
	(283,274,NULL,NULL,'cancelAction',1275,1276),
	(284,1,NULL,NULL,'PolicyExceptions',1298,1327),
	(285,284,NULL,NULL,'index',1299,1300),
	(286,284,NULL,NULL,'delete',1301,1302),
	(287,284,NULL,NULL,'add',1303,1304),
	(288,284,NULL,NULL,'edit',1305,1306),
	(290,284,NULL,NULL,'cancelAction',1307,1308),
	(291,1,NULL,NULL,'Processes',1328,1357),
	(292,291,NULL,NULL,'index',1329,1330),
	(293,291,NULL,NULL,'delete',1331,1332),
	(294,291,NULL,NULL,'add',1333,1334),
	(295,291,NULL,NULL,'edit',1335,1336),
	(296,291,NULL,NULL,'cancelAction',1337,1338),
	(297,1,NULL,NULL,'ProgramIssues',1358,1389),
	(298,297,NULL,NULL,'index',1359,1360),
	(299,297,NULL,NULL,'delete',1361,1362),
	(300,297,NULL,NULL,'add',1363,1364),
	(301,297,NULL,NULL,'edit',1365,1366),
	(302,297,NULL,NULL,'exportPdf',1367,1368),
	(303,297,NULL,NULL,'cancelAction',1369,1370),
	(304,1,NULL,NULL,'ProgramScopes',1390,1421),
	(305,304,NULL,NULL,'index',1391,1392),
	(306,304,NULL,NULL,'delete',1393,1394),
	(307,304,NULL,NULL,'add',1395,1396),
	(308,304,NULL,NULL,'edit',1397,1398),
	(309,304,NULL,NULL,'exportPdf',1399,1400),
	(310,304,NULL,NULL,'cancelAction',1401,1402),
	(311,1,NULL,NULL,'ProjectAchievements',1422,1451),
	(312,311,NULL,NULL,'index',1423,1424),
	(313,311,NULL,NULL,'delete',1425,1426),
	(314,311,NULL,NULL,'add',1427,1428),
	(315,311,NULL,NULL,'edit',1429,1430),
	(316,311,NULL,NULL,'cancelAction',1431,1432),
	(317,1,NULL,NULL,'ProjectExpenses',1452,1481),
	(318,317,NULL,NULL,'index',1453,1454),
	(319,317,NULL,NULL,'delete',1455,1456),
	(320,317,NULL,NULL,'add',1457,1458),
	(321,317,NULL,NULL,'edit',1459,1460),
	(322,317,NULL,NULL,'cancelAction',1461,1462),
	(323,1,NULL,NULL,'Projects',1482,1513),
	(324,323,NULL,NULL,'index',1483,1484),
	(325,323,NULL,NULL,'delete',1485,1486),
	(326,323,NULL,NULL,'add',1487,1488),
	(327,323,NULL,NULL,'edit',1489,1490),
	(329,323,NULL,NULL,'exportPdf',1491,1492),
	(330,323,NULL,NULL,'cancelAction',1493,1494),
	(331,1,NULL,NULL,'Reports',1514,1537),
	(332,331,NULL,NULL,'awareness',1515,1516),
	(333,331,NULL,NULL,'cancelAction',1517,1518),
	(334,1,NULL,NULL,'Reviews',1538,1569),
	(335,334,NULL,NULL,'index',1539,1540),
	(336,334,NULL,NULL,'add',1541,1542),
	(337,334,NULL,NULL,'edit',1543,1544),
	(338,334,NULL,NULL,'delete',1545,1546),
	(339,334,NULL,NULL,'cancelAction',1547,1548),
	(340,1,NULL,NULL,'RiskClassifications',1570,1601),
	(341,340,NULL,NULL,'index',1571,1572),
	(342,340,NULL,NULL,'delete',1573,1574),
	(343,340,NULL,NULL,'add',1575,1576),
	(344,340,NULL,NULL,'edit',1577,1578),
	(345,340,NULL,NULL,'addClassificationType',1579,1580),
	(346,340,NULL,NULL,'cancelAction',1581,1582),
	(347,1,NULL,NULL,'RiskExceptions',1602,1633),
	(348,347,NULL,NULL,'index',1603,1604),
	(349,347,NULL,NULL,'delete',1605,1606),
	(350,347,NULL,NULL,'add',1607,1608),
	(351,347,NULL,NULL,'edit',1609,1610),
	(353,347,NULL,NULL,'exportPdf',1611,1612),
	(354,347,NULL,NULL,'cancelAction',1613,1614),
	(355,1,NULL,NULL,'RiskReports',1634,1659),
	(356,355,NULL,NULL,'index',1635,1636),
	(357,355,NULL,NULL,'awareness',1637,1638),
	(358,355,NULL,NULL,'cancelAction',1639,1640),
	(359,1,NULL,NULL,'Risks',1660,1697),
	(360,359,NULL,NULL,'index',1661,1662),
	(361,359,NULL,NULL,'delete',1663,1664),
	(362,359,NULL,NULL,'add',1665,1666),
	(363,359,NULL,NULL,'edit',1667,1668),
	(364,359,NULL,NULL,'calculateRiskScoreAjax',1669,1670),
	(365,359,NULL,NULL,'getThreatsVulnerabilities',1671,1672),
	(367,359,NULL,NULL,'exportPdf',1673,1674),
	(368,359,NULL,NULL,'cancelAction',1675,1676),
	(369,1,NULL,NULL,'Scopes',1698,1727),
	(370,369,NULL,NULL,'index',1699,1700),
	(371,369,NULL,NULL,'delete',1701,1702),
	(372,369,NULL,NULL,'add',1703,1704),
	(373,369,NULL,NULL,'edit',1705,1706),
	(374,369,NULL,NULL,'cancelAction',1707,1708),
	(375,1,NULL,NULL,'SecurityControlReports',1728,1753),
	(376,375,NULL,NULL,'index',1729,1730),
	(377,375,NULL,NULL,'awareness',1731,1732),
	(378,375,NULL,NULL,'cancelAction',1733,1734),
	(379,1,NULL,NULL,'SecurityIncidentClassifications',1754,1783),
	(380,379,NULL,NULL,'index',1755,1756),
	(381,379,NULL,NULL,'delete',1757,1758),
	(382,379,NULL,NULL,'add',1759,1760),
	(383,379,NULL,NULL,'edit',1761,1762),
	(384,379,NULL,NULL,'cancelAction',1763,1764),
	(385,1,NULL,NULL,'SecurityIncidentStages',1784,1815),
	(386,385,NULL,NULL,'index',1785,1786),
	(387,385,NULL,NULL,'add',1787,1788),
	(388,385,NULL,NULL,'edit',1789,1790),
	(389,385,NULL,NULL,'delete',1791,1792),
	(390,385,NULL,NULL,'pocessStage',1793,1794),
	(391,385,NULL,NULL,'cancelAction',1795,1796),
	(392,1,NULL,NULL,'SecurityIncidents',1816,1859),
	(393,392,NULL,NULL,'index',1817,1818),
	(394,392,NULL,NULL,'deleteold',1819,1820),
	(395,392,NULL,NULL,'delete',1821,1822),
	(396,392,NULL,NULL,'add',1823,1824),
	(397,392,NULL,NULL,'edit',1825,1826),
	(398,392,NULL,NULL,'getAssets',1827,1828),
	(399,392,NULL,NULL,'getThirdParties',1829,1830),
	(401,392,NULL,NULL,'exportPdf',1831,1832),
	(402,392,NULL,NULL,'cancelAction',1833,1834),
	(403,1,NULL,NULL,'SecurityOperationReports',1860,1885),
	(404,403,NULL,NULL,'index',1861,1862),
	(405,403,NULL,NULL,'awareness',1863,1864),
	(406,403,NULL,NULL,'cancelAction',1865,1866),
	(407,1,NULL,NULL,'SecurityPolicies',1886,1923),
	(408,407,NULL,NULL,'index',1887,1888),
	(409,407,NULL,NULL,'delete',1889,1890),
	(410,407,NULL,NULL,'add',1891,1892),
	(411,407,NULL,NULL,'edit',1893,1894),
	(412,407,NULL,NULL,'getDirectLink',1895,1896),
	(413,407,NULL,NULL,'duplicate',1897,1898),
	(414,407,NULL,NULL,'ldapGroups',1899,1900),
	(416,407,NULL,NULL,'sendNotifications',1901,1902),
	(417,407,NULL,NULL,'cancelAction',1903,1904),
	(418,1,NULL,NULL,'SecurityPolicyReviews',1924,1951),
	(419,418,NULL,NULL,'index',1925,1926),
	(420,418,NULL,NULL,'edit',1927,1928),
	(421,418,NULL,NULL,'delete',1929,1930),
	(422,418,NULL,NULL,'cancelAction',1931,1932),
	(423,1,NULL,NULL,'SecurityServiceAuditImprovements',1952,1979),
	(424,423,NULL,NULL,'delete',1953,1954),
	(425,423,NULL,NULL,'add',1955,1956),
	(426,423,NULL,NULL,'edit',1957,1958),
	(427,423,NULL,NULL,'cancelAction',1959,1960),
	(428,1,NULL,NULL,'SecurityServiceAudits',1980,2011),
	(429,428,NULL,NULL,'index',1981,1982),
	(430,428,NULL,NULL,'delete',1983,1984),
	(431,428,NULL,NULL,'edit',1985,1986),
	(432,428,NULL,NULL,'cancelAction',1987,1988),
	(433,1,NULL,NULL,'SecurityServiceMaintenances',2012,2039),
	(434,433,NULL,NULL,'index',2013,2014),
	(435,433,NULL,NULL,'delete',2015,2016),
	(436,433,NULL,NULL,'edit',2017,2018),
	(437,433,NULL,NULL,'cancelAction',2019,2020),
	(438,1,NULL,NULL,'SecurityServices',2040,2075),
	(439,438,NULL,NULL,'index',2041,2042),
	(440,438,NULL,NULL,'delete',2043,2044),
	(441,438,NULL,NULL,'add',2045,2046),
	(442,438,NULL,NULL,'edit',2047,2048),
	(443,438,NULL,NULL,'deleteProductionJoins',2049,2050),
	(444,438,NULL,NULL,'auditCalendarFormEntry',2051,2052),
	(448,438,NULL,NULL,'exportPdf',2053,2054),
	(449,438,NULL,NULL,'cancelAction',2055,2056),
	(450,1,NULL,NULL,'ServiceClassifications',2076,2105),
	(451,450,NULL,NULL,'index',2077,2078),
	(452,450,NULL,NULL,'delete',2079,2080),
	(453,450,NULL,NULL,'add',2081,2082),
	(454,450,NULL,NULL,'edit',2083,2084),
	(455,450,NULL,NULL,'cancelAction',2085,2086),
	(456,1,NULL,NULL,'ServiceContracts',2106,2137),
	(457,456,NULL,NULL,'index',2107,2108),
	(458,456,NULL,NULL,'delete',2109,2110),
	(459,456,NULL,NULL,'add',2111,2112),
	(460,456,NULL,NULL,'edit',2113,2114),
	(461,456,NULL,NULL,'cancelAction',2115,2116),
	(462,1,NULL,NULL,'Settings',2138,2185),
	(463,462,NULL,NULL,'index',2139,2140),
	(464,462,NULL,NULL,'edit',2141,2142),
	(465,462,NULL,NULL,'logs',2143,2144),
	(466,462,NULL,NULL,'deleteLogs',2145,2146),
	(467,462,NULL,NULL,'testMailConnection',2147,2148),
	(468,462,NULL,NULL,'resetDashboards',2149,2150),
	(469,462,NULL,NULL,'customLogo',2151,2152),
	(470,462,NULL,NULL,'deleteCache',2153,2154),
	(471,462,NULL,NULL,'resetDatabase',2155,2156),
	(472,462,NULL,NULL,'systemHealth',2157,2158),
	(473,462,NULL,NULL,'cancelAction',2159,2160),
	(474,1,NULL,NULL,'SystemRecords',2186,2211),
	(475,474,NULL,NULL,'index',2187,2188),
	(476,474,NULL,NULL,'export',2189,2190),
	(477,474,NULL,NULL,'cancelAction',2191,2192),
	(478,1,NULL,NULL,'TeamRoles',2212,2243),
	(479,478,NULL,NULL,'index',2213,2214),
	(480,478,NULL,NULL,'delete',2215,2216),
	(481,478,NULL,NULL,'add',2217,2218),
	(482,478,NULL,NULL,'edit',2219,2220),
	(483,478,NULL,NULL,'exportPdf',2221,2222),
	(484,478,NULL,NULL,'cancelAction',2223,2224),
	(485,1,NULL,NULL,'ThirdParties',2244,2273),
	(486,485,NULL,NULL,'index',2245,2246),
	(487,485,NULL,NULL,'delete',2247,2248),
	(488,485,NULL,NULL,'add',2249,2250),
	(489,485,NULL,NULL,'edit',2251,2252),
	(490,485,NULL,NULL,'cancelAction',2253,2254),
	(491,1,NULL,NULL,'ThirdPartyRisks',2274,2309),
	(492,491,NULL,NULL,'index',2275,2276),
	(493,491,NULL,NULL,'delete',2277,2278),
	(494,491,NULL,NULL,'add',2279,2280),
	(495,491,NULL,NULL,'edit',2281,2282),
	(496,491,NULL,NULL,'calculateRiskScoreAjax',2283,2284),
	(498,491,NULL,NULL,'exportPdf',2285,2286),
	(499,491,NULL,NULL,'cancelAction',2287,2288),
	(500,1,NULL,NULL,'Threats',2310,2339),
	(501,500,NULL,NULL,'index',2311,2312),
	(502,500,NULL,NULL,'liveEdit',2313,2314),
	(503,500,NULL,NULL,'add',2315,2316),
	(504,500,NULL,NULL,'delete',2317,2318),
	(505,500,NULL,NULL,'cancelAction',2319,2320),
	(506,1,NULL,NULL,'Users',2340,2387),
	(507,506,NULL,NULL,'index',2341,2342),
	(508,506,NULL,NULL,'add',2343,2344),
	(509,506,NULL,NULL,'edit',2345,2346),
	(510,506,NULL,NULL,'delete',2347,2348),
	(511,506,NULL,NULL,'profile',2349,2350),
	(512,506,NULL,NULL,'resetpassword',2351,2352),
	(513,506,NULL,NULL,'useticket',2353,2354),
	(514,506,NULL,NULL,'login',2355,2356),
	(515,506,NULL,NULL,'logout',2357,2358),
	(516,506,NULL,NULL,'chooseLdapUser',2359,2360),
	(517,506,NULL,NULL,'cancelAction',2361,2362),
	(518,1,NULL,NULL,'Vulnerabilities',2388,2417),
	(519,518,NULL,NULL,'index',2389,2390),
	(520,518,NULL,NULL,'liveEdit',2391,2392),
	(521,518,NULL,NULL,'add',2393,2394),
	(522,518,NULL,NULL,'delete',2395,2396),
	(523,518,NULL,NULL,'cancelAction',2397,2398),
	(524,1,NULL,NULL,'Workflows',2418,2461),
	(525,524,NULL,NULL,'index',2419,2420),
	(526,524,NULL,NULL,'edit',2421,2422),
	(527,524,NULL,NULL,'editWarning',2423,2424),
	(528,524,NULL,NULL,'editNoApprover',2425,2426),
	(529,524,NULL,NULL,'acknowledge',2427,2428),
	(530,524,NULL,NULL,'deleteWarning',2429,2430),
	(531,524,NULL,NULL,'deleteNoApprover',2431,2432),
	(532,524,NULL,NULL,'requestValidation',2433,2434),
	(533,524,NULL,NULL,'validateItem',2435,2436),
	(534,524,NULL,NULL,'requestApproval',2437,2438),
	(535,524,NULL,NULL,'approveItem',2439,2440),
	(536,524,NULL,NULL,'cancelAction',2441,2442),
	(610,32,NULL,NULL,'getIndexUrlFromComponent',171,172),
	(611,32,NULL,NULL,'initEmailFromComponent',173,174),
	(613,250,NULL,NULL,'associateForObject',1179,1180),
	(614,250,NULL,NULL,'associateForAll',1181,1182),
	(615,1,NULL,NULL,'ProgramHealth',2462,2487),
	(616,615,NULL,NULL,'index',2463,2464),
	(617,615,NULL,NULL,'cancelAction',2465,2466),
	(658,269,NULL,NULL,'license',1235,1236),
	(659,615,NULL,NULL,'exportPdf',2467,2468),
	(696,506,NULL,NULL,'changeLanguage',2363,2364),
	(733,108,NULL,NULL,'index',471,472),
	(806,2,NULL,NULL,'isAuthorized',7,8),
	(807,5,NULL,NULL,'isAuthorized',39,40),
	(808,12,NULL,NULL,'isAuthorized',69,70),
	(809,18,NULL,NULL,'isAuthorized',99,100),
	(810,24,NULL,NULL,'isAuthorized',131,132),
	(811,32,NULL,NULL,'isAuthorized',175,176),
	(812,42,NULL,NULL,'isAuthorized',213,214),
	(813,55,NULL,NULL,'isAuthorized',267,268),
	(815,75,NULL,NULL,'isAuthorized',335,336),
	(816,84,NULL,NULL,'isAuthorized',365,366),
	(817,89,NULL,NULL,'isAuthorized',393,394),
	(818,94,NULL,NULL,'isAuthorized',443,444),
	(819,108,NULL,NULL,'isAuthorized',473,474),
	(820,113,NULL,NULL,'isAuthorized',503,504),
	(821,119,NULL,NULL,'isAuthorized',543,544),
	(822,130,NULL,NULL,'isAuthorized',575,576),
	(823,137,NULL,NULL,'isAuthorized',603,604),
	(824,142,NULL,NULL,'isAuthorized',645,646),
	(825,153,NULL,NULL,'isAuthorized',689,690),
	(826,161,NULL,NULL,'isAuthorized',719,720),
	(827,167,NULL,NULL,'isAuthorized',761,762),
	(828,175,NULL,NULL,'isAuthorized',791,792),
	(829,180,NULL,NULL,'isAuthorized',825,826),
	(830,187,NULL,NULL,'isAuthorized',853,854),
	(831,191,NULL,NULL,'isAuthorized',883,884),
	(832,199,NULL,NULL,'isAuthorized',919,920),
	(833,206,NULL,NULL,'isAuthorized',947,948),
	(834,211,NULL,NULL,'isAuthorized',975,976),
	(835,216,NULL,NULL,'isAuthorized',1009,1010),
	(836,224,NULL,NULL,'isAuthorized',1039,1040),
	(837,230,NULL,NULL,'isAuthorized',1069,1070),
	(838,236,NULL,NULL,'isAuthorized',1103,1104),
	(839,244,NULL,NULL,'isAuthorized',1133,1134),
	(840,250,NULL,NULL,'isAuthorized',1183,1184),
	(841,266,NULL,NULL,'isAuthorized',1209,1210),
	(842,269,NULL,NULL,'isAuthorized',1237,1238),
	(843,274,NULL,NULL,'isAuthorized',1277,1278),
	(844,284,NULL,NULL,'isAuthorized',1309,1310),
	(845,291,NULL,NULL,'isAuthorized',1339,1340),
	(846,615,NULL,NULL,'isAuthorized',2469,2470),
	(847,297,NULL,NULL,'isAuthorized',1371,1372),
	(848,304,NULL,NULL,'isAuthorized',1403,1404),
	(849,311,NULL,NULL,'isAuthorized',1433,1434),
	(850,317,NULL,NULL,'isAuthorized',1463,1464),
	(851,323,NULL,NULL,'isAuthorized',1495,1496),
	(852,331,NULL,NULL,'isAuthorized',1519,1520),
	(853,334,NULL,NULL,'isAuthorized',1549,1550),
	(854,340,NULL,NULL,'isAuthorized',1583,1584),
	(855,347,NULL,NULL,'isAuthorized',1615,1616),
	(856,355,NULL,NULL,'isAuthorized',1641,1642),
	(857,359,NULL,NULL,'isAuthorized',1677,1678),
	(858,369,NULL,NULL,'isAuthorized',1709,1710),
	(859,375,NULL,NULL,'isAuthorized',1735,1736),
	(860,379,NULL,NULL,'isAuthorized',1765,1766),
	(861,385,NULL,NULL,'isAuthorized',1797,1798),
	(862,392,NULL,NULL,'isAuthorized',1835,1836),
	(863,403,NULL,NULL,'isAuthorized',1867,1868),
	(864,407,NULL,NULL,'isAuthorized',1905,1906),
	(865,418,NULL,NULL,'isAuthorized',1933,1934),
	(866,423,NULL,NULL,'isAuthorized',1961,1962),
	(867,428,NULL,NULL,'isAuthorized',1989,1990),
	(868,433,NULL,NULL,'isAuthorized',2021,2022),
	(869,438,NULL,NULL,'isAuthorized',2057,2058),
	(870,450,NULL,NULL,'isAuthorized',2087,2088),
	(871,456,NULL,NULL,'isAuthorized',2117,2118),
	(872,462,NULL,NULL,'isAuthorized',2161,2162),
	(873,474,NULL,NULL,'isAuthorized',2193,2194),
	(874,478,NULL,NULL,'isAuthorized',2225,2226),
	(875,485,NULL,NULL,'isAuthorized',2255,2256),
	(876,491,NULL,NULL,'isAuthorized',2289,2290),
	(877,500,NULL,NULL,'isAuthorized',2321,2322),
	(878,506,NULL,NULL,'isAuthorized',2365,2366),
	(879,518,NULL,NULL,'isAuthorized',2399,2400),
	(880,524,NULL,NULL,'isAuthorized',2443,2444),
	(961,269,NULL,NULL,'welcome',1239,1240),
	(1042,506,NULL,NULL,'unblock',2367,2368),
	(1083,392,NULL,NULL,'reloadLifecycle',1837,1838),
	(1124,1,NULL,NULL,'Acl',2488,2603),
	(1125,1124,NULL,NULL,'Acl',2489,2514),
	(1126,1125,NULL,NULL,'index',2490,2491),
	(1127,1125,NULL,NULL,'admin_index',2492,2493),
	(1128,1125,NULL,NULL,'isAuthorized',2494,2495),
	(1129,1125,NULL,NULL,'cancelAction',2496,2497),
	(1130,1124,NULL,NULL,'Acos',2515,2546),
	(1131,1130,NULL,NULL,'admin_index',2516,2517),
	(1132,1130,NULL,NULL,'admin_empty_acos',2518,2519),
	(1133,1130,NULL,NULL,'admin_build_acl',2520,2521),
	(1134,1130,NULL,NULL,'admin_prune_acos',2522,2523),
	(1135,1130,NULL,NULL,'admin_synchronize',2524,2525),
	(1136,1130,NULL,NULL,'isAuthorized',2526,2527),
	(1137,1130,NULL,NULL,'cancelAction',2528,2529),
	(1138,1124,NULL,NULL,'Aros',2547,2602),
	(1139,1138,NULL,NULL,'admin_index',2548,2549),
	(1140,1138,NULL,NULL,'admin_check',2550,2551),
	(1141,1138,NULL,NULL,'admin_users',2552,2553),
	(1142,1138,NULL,NULL,'admin_update_user_role',2554,2555),
	(1143,1138,NULL,NULL,'admin_ajax_role_permissions',2556,2557),
	(1144,1138,NULL,NULL,'admin_role_permissions',2558,2559),
	(1145,1138,NULL,NULL,'admin_user_permissions',2560,2561),
	(1146,1138,NULL,NULL,'admin_empty_permissions',2562,2563),
	(1147,1138,NULL,NULL,'admin_clear_user_specific_permissions',2564,2565),
	(1148,1138,NULL,NULL,'admin_grant_all_controllers',2566,2567),
	(1149,1138,NULL,NULL,'admin_deny_all_controllers',2568,2569),
	(1150,1138,NULL,NULL,'admin_get_role_controller_permission',2570,2571),
	(1151,1138,NULL,NULL,'admin_grant_role_permission',2572,2573),
	(1152,1138,NULL,NULL,'admin_deny_role_permission',2574,2575),
	(1153,1138,NULL,NULL,'admin_get_user_controller_permission',2576,2577),
	(1154,1138,NULL,NULL,'admin_grant_user_permission',2578,2579),
	(1155,1138,NULL,NULL,'admin_deny_user_permission',2580,2581),
	(1156,1138,NULL,NULL,'isAuthorized',2582,2583),
	(1157,1138,NULL,NULL,'cancelAction',2584,2585),
	(1158,1,NULL,NULL,'DebugKit',2604,2631),
	(1159,1158,NULL,NULL,'ToolbarAccess',2605,2630),
	(1160,1159,NULL,NULL,'history_state',2606,2607),
	(1161,1159,NULL,NULL,'sql_explain',2608,2609),
	(1162,1159,NULL,NULL,'isAuthorized',2610,2611),
	(1163,1159,NULL,NULL,'cancelAction',2612,2613),
	(1164,2,NULL,NULL,'saveAdvancedFilter',9,10),
	(1165,2,NULL,NULL,'deleteAdvancedFilter',11,12),
	(1166,2,NULL,NULL,'exportAdvancedFilterToPdf',13,14),
	(1167,2,NULL,NULL,'exportAdvancedFilterToCsv',15,16),
	(1168,5,NULL,NULL,'saveAdvancedFilter',41,42),
	(1169,5,NULL,NULL,'deleteAdvancedFilter',43,44),
	(1170,5,NULL,NULL,'exportAdvancedFilterToPdf',45,46),
	(1171,5,NULL,NULL,'exportAdvancedFilterToCsv',47,48),
	(1172,12,NULL,NULL,'saveAdvancedFilter',71,72),
	(1173,12,NULL,NULL,'deleteAdvancedFilter',73,74),
	(1174,12,NULL,NULL,'exportAdvancedFilterToPdf',75,76),
	(1175,12,NULL,NULL,'exportAdvancedFilterToCsv',77,78),
	(1176,18,NULL,NULL,'saveAdvancedFilter',101,102),
	(1177,18,NULL,NULL,'deleteAdvancedFilter',103,104),
	(1178,18,NULL,NULL,'exportAdvancedFilterToPdf',105,106),
	(1179,18,NULL,NULL,'exportAdvancedFilterToCsv',107,108),
	(1180,24,NULL,NULL,'saveAdvancedFilter',133,134),
	(1181,24,NULL,NULL,'deleteAdvancedFilter',135,136),
	(1182,24,NULL,NULL,'exportAdvancedFilterToPdf',137,138),
	(1183,24,NULL,NULL,'exportAdvancedFilterToCsv',139,140),
	(1184,32,NULL,NULL,'saveAdvancedFilter',177,178),
	(1185,32,NULL,NULL,'deleteAdvancedFilter',179,180),
	(1186,32,NULL,NULL,'exportAdvancedFilterToPdf',181,182),
	(1187,32,NULL,NULL,'exportAdvancedFilterToCsv',183,184),
	(1188,42,NULL,NULL,'saveAdvancedFilter',215,216),
	(1189,42,NULL,NULL,'deleteAdvancedFilter',217,218),
	(1190,42,NULL,NULL,'exportAdvancedFilterToPdf',219,220),
	(1191,42,NULL,NULL,'exportAdvancedFilterToCsv',221,222),
	(1192,55,NULL,NULL,'saveAdvancedFilter',269,270),
	(1193,55,NULL,NULL,'deleteAdvancedFilter',271,272),
	(1194,55,NULL,NULL,'exportAdvancedFilterToPdf',273,274),
	(1195,55,NULL,NULL,'exportAdvancedFilterToCsv',275,276),
	(1200,75,NULL,NULL,'saveAdvancedFilter',337,338),
	(1201,75,NULL,NULL,'deleteAdvancedFilter',339,340),
	(1202,75,NULL,NULL,'exportAdvancedFilterToPdf',341,342),
	(1203,75,NULL,NULL,'exportAdvancedFilterToCsv',343,344),
	(1204,84,NULL,NULL,'saveAdvancedFilter',367,368),
	(1205,84,NULL,NULL,'deleteAdvancedFilter',369,370),
	(1206,84,NULL,NULL,'exportAdvancedFilterToPdf',371,372),
	(1207,84,NULL,NULL,'exportAdvancedFilterToCsv',373,374),
	(1208,89,NULL,NULL,'getIndexUrlFromComponent',395,396),
	(1209,89,NULL,NULL,'initEmailFromComponent',397,398),
	(1210,89,NULL,NULL,'saveAdvancedFilter',399,400),
	(1211,89,NULL,NULL,'deleteAdvancedFilter',401,402),
	(1212,89,NULL,NULL,'exportAdvancedFilterToPdf',403,404),
	(1213,89,NULL,NULL,'exportAdvancedFilterToCsv',405,406),
	(1214,94,NULL,NULL,'saveAdvancedFilter',445,446),
	(1215,94,NULL,NULL,'deleteAdvancedFilter',447,448),
	(1216,94,NULL,NULL,'exportAdvancedFilterToPdf',449,450),
	(1217,94,NULL,NULL,'exportAdvancedFilterToCsv',451,452),
	(1218,108,NULL,NULL,'saveAdvancedFilter',475,476),
	(1219,108,NULL,NULL,'deleteAdvancedFilter',477,478),
	(1220,108,NULL,NULL,'exportAdvancedFilterToPdf',479,480),
	(1221,108,NULL,NULL,'exportAdvancedFilterToCsv',481,482),
	(1222,113,NULL,NULL,'saveAdvancedFilter',505,506),
	(1223,113,NULL,NULL,'deleteAdvancedFilter',507,508),
	(1224,113,NULL,NULL,'exportAdvancedFilterToPdf',509,510),
	(1225,113,NULL,NULL,'exportAdvancedFilterToCsv',511,512),
	(1226,119,NULL,NULL,'saveAdvancedFilter',545,546),
	(1227,119,NULL,NULL,'deleteAdvancedFilter',547,548),
	(1228,119,NULL,NULL,'exportAdvancedFilterToPdf',549,550),
	(1229,119,NULL,NULL,'exportAdvancedFilterToCsv',551,552),
	(1230,130,NULL,NULL,'saveAdvancedFilter',577,578),
	(1231,130,NULL,NULL,'deleteAdvancedFilter',579,580),
	(1232,130,NULL,NULL,'exportAdvancedFilterToPdf',581,582),
	(1233,130,NULL,NULL,'exportAdvancedFilterToCsv',583,584),
	(1234,137,NULL,NULL,'saveAdvancedFilter',605,606),
	(1235,137,NULL,NULL,'deleteAdvancedFilter',607,608),
	(1236,137,NULL,NULL,'exportAdvancedFilterToPdf',609,610),
	(1237,137,NULL,NULL,'exportAdvancedFilterToCsv',611,612),
	(1238,142,NULL,NULL,'sendAuditWarningEmails',647,648),
	(1239,142,NULL,NULL,'saveAdvancedFilter',649,650),
	(1240,142,NULL,NULL,'deleteAdvancedFilter',651,652),
	(1241,142,NULL,NULL,'exportAdvancedFilterToPdf',653,654),
	(1242,142,NULL,NULL,'exportAdvancedFilterToCsv',655,656),
	(1243,153,NULL,NULL,'saveAdvancedFilter',691,692),
	(1244,153,NULL,NULL,'deleteAdvancedFilter',693,694),
	(1245,153,NULL,NULL,'exportAdvancedFilterToPdf',695,696),
	(1246,153,NULL,NULL,'exportAdvancedFilterToCsv',697,698),
	(1247,161,NULL,NULL,'saveAdvancedFilter',721,722),
	(1248,161,NULL,NULL,'deleteAdvancedFilter',723,724),
	(1249,161,NULL,NULL,'exportAdvancedFilterToPdf',725,726),
	(1250,161,NULL,NULL,'exportAdvancedFilterToCsv',727,728),
	(1251,167,NULL,NULL,'saveAdvancedFilter',763,764),
	(1252,167,NULL,NULL,'deleteAdvancedFilter',765,766),
	(1253,167,NULL,NULL,'exportAdvancedFilterToPdf',767,768),
	(1254,167,NULL,NULL,'exportAdvancedFilterToCsv',769,770),
	(1255,175,NULL,NULL,'saveAdvancedFilter',793,794),
	(1256,175,NULL,NULL,'deleteAdvancedFilter',795,796),
	(1257,175,NULL,NULL,'exportAdvancedFilterToPdf',797,798),
	(1258,175,NULL,NULL,'exportAdvancedFilterToCsv',799,800),
	(1259,180,NULL,NULL,'saveAdvancedFilter',827,828),
	(1260,180,NULL,NULL,'deleteAdvancedFilter',829,830),
	(1261,180,NULL,NULL,'exportAdvancedFilterToPdf',831,832),
	(1262,180,NULL,NULL,'exportAdvancedFilterToCsv',833,834),
	(1263,187,NULL,NULL,'saveAdvancedFilter',855,856),
	(1264,187,NULL,NULL,'deleteAdvancedFilter',857,858),
	(1265,187,NULL,NULL,'exportAdvancedFilterToPdf',859,860),
	(1266,187,NULL,NULL,'exportAdvancedFilterToCsv',861,862),
	(1267,191,NULL,NULL,'saveAdvancedFilter',885,886),
	(1268,191,NULL,NULL,'deleteAdvancedFilter',887,888),
	(1269,191,NULL,NULL,'exportAdvancedFilterToPdf',889,890),
	(1270,191,NULL,NULL,'exportAdvancedFilterToCsv',891,892),
	(1271,199,NULL,NULL,'saveAdvancedFilter',921,922),
	(1272,199,NULL,NULL,'deleteAdvancedFilter',923,924),
	(1273,199,NULL,NULL,'exportAdvancedFilterToPdf',925,926),
	(1274,199,NULL,NULL,'exportAdvancedFilterToCsv',927,928),
	(1275,206,NULL,NULL,'saveAdvancedFilter',949,950),
	(1276,206,NULL,NULL,'deleteAdvancedFilter',951,952),
	(1277,206,NULL,NULL,'exportAdvancedFilterToPdf',953,954),
	(1278,206,NULL,NULL,'exportAdvancedFilterToCsv',955,956),
	(1279,211,NULL,NULL,'saveAdvancedFilter',977,978),
	(1280,211,NULL,NULL,'deleteAdvancedFilter',979,980),
	(1281,211,NULL,NULL,'exportAdvancedFilterToPdf',981,982),
	(1282,211,NULL,NULL,'exportAdvancedFilterToCsv',983,984),
	(1283,216,NULL,NULL,'saveAdvancedFilter',1011,1012),
	(1284,216,NULL,NULL,'deleteAdvancedFilter',1013,1014),
	(1285,216,NULL,NULL,'exportAdvancedFilterToPdf',1015,1016),
	(1286,216,NULL,NULL,'exportAdvancedFilterToCsv',1017,1018),
	(1287,224,NULL,NULL,'saveAdvancedFilter',1041,1042),
	(1288,224,NULL,NULL,'deleteAdvancedFilter',1043,1044),
	(1289,224,NULL,NULL,'exportAdvancedFilterToPdf',1045,1046),
	(1290,224,NULL,NULL,'exportAdvancedFilterToCsv',1047,1048),
	(1291,230,NULL,NULL,'saveAdvancedFilter',1071,1072),
	(1292,230,NULL,NULL,'deleteAdvancedFilter',1073,1074),
	(1293,230,NULL,NULL,'exportAdvancedFilterToPdf',1075,1076),
	(1294,230,NULL,NULL,'exportAdvancedFilterToCsv',1077,1078),
	(1295,236,NULL,NULL,'saveAdvancedFilter',1105,1106),
	(1296,236,NULL,NULL,'deleteAdvancedFilter',1107,1108),
	(1297,236,NULL,NULL,'exportAdvancedFilterToPdf',1109,1110),
	(1298,236,NULL,NULL,'exportAdvancedFilterToCsv',1111,1112),
	(1299,244,NULL,NULL,'saveAdvancedFilter',1135,1136),
	(1300,244,NULL,NULL,'deleteAdvancedFilter',1137,1138),
	(1301,244,NULL,NULL,'exportAdvancedFilterToPdf',1139,1140),
	(1302,244,NULL,NULL,'exportAdvancedFilterToCsv',1141,1142),
	(1303,1,NULL,NULL,'News',2632,2657),
	(1304,1303,NULL,NULL,'index',2633,2634),
	(1305,1303,NULL,NULL,'markAsRead',2635,2636),
	(1306,1303,NULL,NULL,'isAuthorized',2637,2638),
	(1307,1303,NULL,NULL,'cancelAction',2639,2640),
	(1308,1303,NULL,NULL,'saveAdvancedFilter',2641,2642),
	(1309,1303,NULL,NULL,'deleteAdvancedFilter',2643,2644),
	(1310,1303,NULL,NULL,'exportAdvancedFilterToPdf',2645,2646),
	(1311,1303,NULL,NULL,'exportAdvancedFilterToCsv',2647,2648),
	(1312,250,NULL,NULL,'saveAdvancedFilter',1185,1186),
	(1313,250,NULL,NULL,'deleteAdvancedFilter',1187,1188),
	(1314,250,NULL,NULL,'exportAdvancedFilterToPdf',1189,1190),
	(1315,250,NULL,NULL,'exportAdvancedFilterToCsv',1191,1192),
	(1316,266,NULL,NULL,'saveAdvancedFilter',1211,1212),
	(1317,266,NULL,NULL,'deleteAdvancedFilter',1213,1214),
	(1318,266,NULL,NULL,'exportAdvancedFilterToPdf',1215,1216),
	(1319,266,NULL,NULL,'exportAdvancedFilterToCsv',1217,1218),
	(1320,269,NULL,NULL,'saveAdvancedFilter',1241,1242),
	(1321,269,NULL,NULL,'deleteAdvancedFilter',1243,1244),
	(1322,269,NULL,NULL,'exportAdvancedFilterToPdf',1245,1246),
	(1323,269,NULL,NULL,'exportAdvancedFilterToCsv',1247,1248),
	(1324,274,NULL,NULL,'saveAdvancedFilter',1279,1280),
	(1325,274,NULL,NULL,'deleteAdvancedFilter',1281,1282),
	(1326,274,NULL,NULL,'exportAdvancedFilterToPdf',1283,1284),
	(1327,274,NULL,NULL,'exportAdvancedFilterToCsv',1285,1286),
	(1328,284,NULL,NULL,'saveAdvancedFilter',1311,1312),
	(1329,284,NULL,NULL,'deleteAdvancedFilter',1313,1314),
	(1330,284,NULL,NULL,'exportAdvancedFilterToPdf',1315,1316),
	(1331,284,NULL,NULL,'exportAdvancedFilterToCsv',1317,1318),
	(1332,291,NULL,NULL,'saveAdvancedFilter',1341,1342),
	(1333,291,NULL,NULL,'deleteAdvancedFilter',1343,1344),
	(1334,291,NULL,NULL,'exportAdvancedFilterToPdf',1345,1346),
	(1335,291,NULL,NULL,'exportAdvancedFilterToCsv',1347,1348),
	(1336,615,NULL,NULL,'saveAdvancedFilter',2471,2472),
	(1337,615,NULL,NULL,'deleteAdvancedFilter',2473,2474),
	(1338,615,NULL,NULL,'exportAdvancedFilterToPdf',2475,2476),
	(1339,615,NULL,NULL,'exportAdvancedFilterToCsv',2477,2478),
	(1340,297,NULL,NULL,'saveAdvancedFilter',1373,1374),
	(1341,297,NULL,NULL,'deleteAdvancedFilter',1375,1376),
	(1342,297,NULL,NULL,'exportAdvancedFilterToPdf',1377,1378),
	(1343,297,NULL,NULL,'exportAdvancedFilterToCsv',1379,1380),
	(1344,304,NULL,NULL,'saveAdvancedFilter',1405,1406),
	(1345,304,NULL,NULL,'deleteAdvancedFilter',1407,1408),
	(1346,304,NULL,NULL,'exportAdvancedFilterToPdf',1409,1410),
	(1347,304,NULL,NULL,'exportAdvancedFilterToCsv',1411,1412),
	(1348,311,NULL,NULL,'saveAdvancedFilter',1435,1436),
	(1349,311,NULL,NULL,'deleteAdvancedFilter',1437,1438),
	(1350,311,NULL,NULL,'exportAdvancedFilterToPdf',1439,1440),
	(1351,311,NULL,NULL,'exportAdvancedFilterToCsv',1441,1442),
	(1352,317,NULL,NULL,'saveAdvancedFilter',1465,1466),
	(1353,317,NULL,NULL,'deleteAdvancedFilter',1467,1468),
	(1354,317,NULL,NULL,'exportAdvancedFilterToPdf',1469,1470),
	(1355,317,NULL,NULL,'exportAdvancedFilterToCsv',1471,1472),
	(1356,323,NULL,NULL,'saveAdvancedFilter',1497,1498),
	(1357,323,NULL,NULL,'deleteAdvancedFilter',1499,1500),
	(1358,323,NULL,NULL,'exportAdvancedFilterToPdf',1501,1502),
	(1359,323,NULL,NULL,'exportAdvancedFilterToCsv',1503,1504),
	(1360,331,NULL,NULL,'saveAdvancedFilter',1521,1522),
	(1361,331,NULL,NULL,'deleteAdvancedFilter',1523,1524),
	(1362,331,NULL,NULL,'exportAdvancedFilterToPdf',1525,1526),
	(1363,331,NULL,NULL,'exportAdvancedFilterToCsv',1527,1528),
	(1364,334,NULL,NULL,'saveAdvancedFilter',1551,1552),
	(1365,334,NULL,NULL,'deleteAdvancedFilter',1553,1554),
	(1366,334,NULL,NULL,'exportAdvancedFilterToPdf',1555,1556),
	(1367,334,NULL,NULL,'exportAdvancedFilterToCsv',1557,1558),
	(1368,1,NULL,NULL,'RiskCalculations',2658,2683),
	(1369,1368,NULL,NULL,'warning',2659,2660),
	(1370,1368,NULL,NULL,'edit',2661,2662),
	(1371,1368,NULL,NULL,'isAuthorized',2663,2664),
	(1372,1368,NULL,NULL,'cancelAction',2665,2666),
	(1373,1368,NULL,NULL,'saveAdvancedFilter',2667,2668),
	(1374,1368,NULL,NULL,'deleteAdvancedFilter',2669,2670),
	(1375,1368,NULL,NULL,'exportAdvancedFilterToPdf',2671,2672),
	(1376,1368,NULL,NULL,'exportAdvancedFilterToCsv',2673,2674),
	(1377,340,NULL,NULL,'saveAdvancedFilter',1585,1586),
	(1378,340,NULL,NULL,'deleteAdvancedFilter',1587,1588),
	(1379,340,NULL,NULL,'exportAdvancedFilterToPdf',1589,1590),
	(1380,340,NULL,NULL,'exportAdvancedFilterToCsv',1591,1592),
	(1381,347,NULL,NULL,'saveAdvancedFilter',1617,1618),
	(1382,347,NULL,NULL,'deleteAdvancedFilter',1619,1620),
	(1383,347,NULL,NULL,'exportAdvancedFilterToPdf',1621,1622),
	(1384,347,NULL,NULL,'exportAdvancedFilterToCsv',1623,1624),
	(1385,355,NULL,NULL,'saveAdvancedFilter',1643,1644),
	(1386,355,NULL,NULL,'deleteAdvancedFilter',1645,1646),
	(1387,355,NULL,NULL,'exportAdvancedFilterToPdf',1647,1648),
	(1388,355,NULL,NULL,'exportAdvancedFilterToCsv',1649,1650),
	(1389,359,NULL,NULL,'saveAdvancedFilter',1679,1680),
	(1390,359,NULL,NULL,'deleteAdvancedFilter',1681,1682),
	(1391,359,NULL,NULL,'exportAdvancedFilterToPdf',1683,1684),
	(1392,359,NULL,NULL,'exportAdvancedFilterToCsv',1685,1686),
	(1393,369,NULL,NULL,'saveAdvancedFilter',1711,1712),
	(1394,369,NULL,NULL,'deleteAdvancedFilter',1713,1714),
	(1395,369,NULL,NULL,'exportAdvancedFilterToPdf',1715,1716),
	(1396,369,NULL,NULL,'exportAdvancedFilterToCsv',1717,1718),
	(1397,375,NULL,NULL,'saveAdvancedFilter',1737,1738),
	(1398,375,NULL,NULL,'deleteAdvancedFilter',1739,1740),
	(1399,375,NULL,NULL,'exportAdvancedFilterToPdf',1741,1742),
	(1400,375,NULL,NULL,'exportAdvancedFilterToCsv',1743,1744),
	(1401,379,NULL,NULL,'saveAdvancedFilter',1767,1768),
	(1402,379,NULL,NULL,'deleteAdvancedFilter',1769,1770),
	(1403,379,NULL,NULL,'exportAdvancedFilterToPdf',1771,1772),
	(1404,379,NULL,NULL,'exportAdvancedFilterToCsv',1773,1774),
	(1405,385,NULL,NULL,'saveAdvancedFilter',1799,1800),
	(1406,385,NULL,NULL,'deleteAdvancedFilter',1801,1802),
	(1407,385,NULL,NULL,'exportAdvancedFilterToPdf',1803,1804),
	(1408,385,NULL,NULL,'exportAdvancedFilterToCsv',1805,1806),
	(1409,392,NULL,NULL,'saveAdvancedFilter',1839,1840),
	(1410,392,NULL,NULL,'deleteAdvancedFilter',1841,1842),
	(1411,392,NULL,NULL,'exportAdvancedFilterToPdf',1843,1844),
	(1412,392,NULL,NULL,'exportAdvancedFilterToCsv',1845,1846),
	(1413,403,NULL,NULL,'saveAdvancedFilter',1869,1870),
	(1414,403,NULL,NULL,'deleteAdvancedFilter',1871,1872),
	(1415,403,NULL,NULL,'exportAdvancedFilterToPdf',1873,1874),
	(1416,403,NULL,NULL,'exportAdvancedFilterToCsv',1875,1876),
	(1417,407,NULL,NULL,'saveAdvancedFilter',1907,1908),
	(1418,407,NULL,NULL,'deleteAdvancedFilter',1909,1910),
	(1419,407,NULL,NULL,'exportAdvancedFilterToPdf',1911,1912),
	(1420,407,NULL,NULL,'exportAdvancedFilterToCsv',1913,1914),
	(1421,418,NULL,NULL,'saveAdvancedFilter',1935,1936),
	(1422,418,NULL,NULL,'deleteAdvancedFilter',1937,1938),
	(1423,418,NULL,NULL,'exportAdvancedFilterToPdf',1939,1940),
	(1424,418,NULL,NULL,'exportAdvancedFilterToCsv',1941,1942),
	(1425,423,NULL,NULL,'saveAdvancedFilter',1963,1964),
	(1426,423,NULL,NULL,'deleteAdvancedFilter',1965,1966),
	(1427,423,NULL,NULL,'exportAdvancedFilterToPdf',1967,1968),
	(1428,423,NULL,NULL,'exportAdvancedFilterToCsv',1969,1970),
	(1429,428,NULL,NULL,'getIndexUrlFromComponent',1991,1992),
	(1430,428,NULL,NULL,'initEmailFromComponent',1993,1994),
	(1431,428,NULL,NULL,'saveAdvancedFilter',1995,1996),
	(1432,428,NULL,NULL,'deleteAdvancedFilter',1997,1998),
	(1433,428,NULL,NULL,'exportAdvancedFilterToPdf',1999,2000),
	(1434,428,NULL,NULL,'exportAdvancedFilterToCsv',2001,2002),
	(1435,433,NULL,NULL,'saveAdvancedFilter',2023,2024),
	(1436,433,NULL,NULL,'deleteAdvancedFilter',2025,2026),
	(1437,433,NULL,NULL,'exportAdvancedFilterToPdf',2027,2028),
	(1438,433,NULL,NULL,'exportAdvancedFilterToCsv',2029,2030),
	(1439,438,NULL,NULL,'saveAdvancedFilter',2059,2060),
	(1440,438,NULL,NULL,'deleteAdvancedFilter',2061,2062),
	(1441,438,NULL,NULL,'exportAdvancedFilterToPdf',2063,2064),
	(1442,438,NULL,NULL,'exportAdvancedFilterToCsv',2065,2066),
	(1443,450,NULL,NULL,'saveAdvancedFilter',2089,2090),
	(1444,450,NULL,NULL,'deleteAdvancedFilter',2091,2092),
	(1445,450,NULL,NULL,'exportAdvancedFilterToPdf',2093,2094),
	(1446,450,NULL,NULL,'exportAdvancedFilterToCsv',2095,2096),
	(1447,456,NULL,NULL,'saveAdvancedFilter',2119,2120),
	(1448,456,NULL,NULL,'deleteAdvancedFilter',2121,2122),
	(1449,456,NULL,NULL,'exportAdvancedFilterToPdf',2123,2124),
	(1450,456,NULL,NULL,'exportAdvancedFilterToCsv',2125,2126),
	(1451,462,NULL,NULL,'saveAdvancedFilter',2163,2164),
	(1452,462,NULL,NULL,'deleteAdvancedFilter',2165,2166),
	(1453,462,NULL,NULL,'exportAdvancedFilterToPdf',2167,2168),
	(1454,462,NULL,NULL,'exportAdvancedFilterToCsv',2169,2170),
	(1455,474,NULL,NULL,'saveAdvancedFilter',2195,2196),
	(1456,474,NULL,NULL,'deleteAdvancedFilter',2197,2198),
	(1457,474,NULL,NULL,'exportAdvancedFilterToPdf',2199,2200),
	(1458,474,NULL,NULL,'exportAdvancedFilterToCsv',2201,2202),
	(1459,478,NULL,NULL,'saveAdvancedFilter',2227,2228),
	(1460,478,NULL,NULL,'deleteAdvancedFilter',2229,2230),
	(1461,478,NULL,NULL,'exportAdvancedFilterToPdf',2231,2232),
	(1462,478,NULL,NULL,'exportAdvancedFilterToCsv',2233,2234),
	(1463,485,NULL,NULL,'saveAdvancedFilter',2257,2258),
	(1464,485,NULL,NULL,'deleteAdvancedFilter',2259,2260),
	(1465,485,NULL,NULL,'exportAdvancedFilterToPdf',2261,2262),
	(1466,485,NULL,NULL,'exportAdvancedFilterToCsv',2263,2264),
	(1467,491,NULL,NULL,'saveAdvancedFilter',2291,2292),
	(1468,491,NULL,NULL,'deleteAdvancedFilter',2293,2294),
	(1469,491,NULL,NULL,'exportAdvancedFilterToPdf',2295,2296),
	(1470,491,NULL,NULL,'exportAdvancedFilterToCsv',2297,2298),
	(1471,500,NULL,NULL,'saveAdvancedFilter',2323,2324),
	(1472,500,NULL,NULL,'deleteAdvancedFilter',2325,2326),
	(1473,500,NULL,NULL,'exportAdvancedFilterToPdf',2327,2328),
	(1474,500,NULL,NULL,'exportAdvancedFilterToCsv',2329,2330),
	(1475,1,NULL,NULL,'Updates',2684,2709),
	(1476,1475,NULL,NULL,'index',2685,2686),
	(1477,1475,NULL,NULL,'update',2687,2688),
	(1478,1475,NULL,NULL,'isAuthorized',2689,2690),
	(1479,1475,NULL,NULL,'cancelAction',2691,2692),
	(1480,1475,NULL,NULL,'saveAdvancedFilter',2693,2694),
	(1481,1475,NULL,NULL,'deleteAdvancedFilter',2695,2696),
	(1482,1475,NULL,NULL,'exportAdvancedFilterToPdf',2697,2698),
	(1483,1475,NULL,NULL,'exportAdvancedFilterToCsv',2699,2700),
	(1484,506,NULL,NULL,'searchLdapUsers',2369,2370),
	(1485,506,NULL,NULL,'saveAdvancedFilter',2371,2372),
	(1486,506,NULL,NULL,'deleteAdvancedFilter',2373,2374),
	(1487,506,NULL,NULL,'exportAdvancedFilterToPdf',2375,2376),
	(1488,506,NULL,NULL,'exportAdvancedFilterToCsv',2377,2378),
	(1489,518,NULL,NULL,'saveAdvancedFilter',2401,2402),
	(1490,518,NULL,NULL,'deleteAdvancedFilter',2403,2404),
	(1491,518,NULL,NULL,'exportAdvancedFilterToPdf',2405,2406),
	(1492,518,NULL,NULL,'exportAdvancedFilterToCsv',2407,2408),
	(1493,524,NULL,NULL,'saveAdvancedFilter',2445,2446),
	(1494,524,NULL,NULL,'deleteAdvancedFilter',2447,2448),
	(1495,524,NULL,NULL,'exportAdvancedFilterToPdf',2449,2450),
	(1496,524,NULL,NULL,'exportAdvancedFilterToCsv',2451,2452),
	(1497,1125,NULL,NULL,'saveAdvancedFilter',2498,2499),
	(1498,1125,NULL,NULL,'deleteAdvancedFilter',2500,2501),
	(1499,1125,NULL,NULL,'exportAdvancedFilterToPdf',2502,2503),
	(1500,1125,NULL,NULL,'exportAdvancedFilterToCsv',2504,2505),
	(1501,1130,NULL,NULL,'saveAdvancedFilter',2530,2531),
	(1502,1130,NULL,NULL,'deleteAdvancedFilter',2532,2533),
	(1503,1130,NULL,NULL,'exportAdvancedFilterToPdf',2534,2535),
	(1504,1130,NULL,NULL,'exportAdvancedFilterToCsv',2536,2537),
	(1505,1138,NULL,NULL,'saveAdvancedFilter',2586,2587),
	(1506,1138,NULL,NULL,'deleteAdvancedFilter',2588,2589),
	(1507,1138,NULL,NULL,'exportAdvancedFilterToPdf',2590,2591),
	(1508,1138,NULL,NULL,'exportAdvancedFilterToCsv',2592,2593),
	(1509,1159,NULL,NULL,'saveAdvancedFilter',2614,2615),
	(1510,1159,NULL,NULL,'deleteAdvancedFilter',2616,2617),
	(1511,1159,NULL,NULL,'exportAdvancedFilterToPdf',2618,2619),
	(1512,1159,NULL,NULL,'exportAdvancedFilterToCsv',2620,2621),
	(1513,137,NULL,NULL,'index',613,614),
	(1522,1,NULL,NULL,'CustomFields',2710,2801),
	(1547,161,NULL,NULL,'initEmailFromComponent',729,730),
	(1548,161,NULL,NULL,'getIndexUrlFromComponent',731,732),
	(1549,167,NULL,NULL,'getPolicies',771,772),
	(1550,250,NULL,NULL,'listItems',1193,1194),
	(1551,392,NULL,NULL,'getControls',1847,1848),
	(1552,392,NULL,NULL,'getRiskProcedures',1849,1850),
	(1553,462,NULL,NULL,'getTimeByTimezone',2171,2172),
	(1554,75,NULL,NULL,'getPolicies',345,346),
	(1555,142,NULL,NULL,'start',657,658),
	(1556,142,NULL,NULL,'stop',659,660),
	(1557,142,NULL,NULL,'exportPdf',661,662),
	(1558,161,NULL,NULL,'exportPdf',733,734),
	(1559,359,NULL,NULL,'getPolicies',1687,1688),
	(1560,456,NULL,NULL,'initOptions',2127,2128),
	(1561,491,NULL,NULL,'getPolicies',2299,2300),
	(1562,1,NULL,NULL,'ImportTool',2802,2831),
	(1563,1562,NULL,NULL,'ImportTool',2803,2830),
	(1564,1563,NULL,NULL,'index',2804,2805),
	(1565,1563,NULL,NULL,'downloadTemplate',2806,2807),
	(1566,1563,NULL,NULL,'preview',2808,2809),
	(1567,1563,NULL,NULL,'isAuthorized',2810,2811),
	(1568,1563,NULL,NULL,'cancelAction',2812,2813),
	(1569,1563,NULL,NULL,'saveAdvancedFilter',2814,2815),
	(1570,1563,NULL,NULL,'deleteAdvancedFilter',2816,2817),
	(1571,1563,NULL,NULL,'exportAdvancedFilterToPdf',2818,2819),
	(1572,1563,NULL,NULL,'exportAdvancedFilterToCsv',2820,2821),
	(1573,24,NULL,NULL,'getLegals',141,142),
	(1574,42,NULL,NULL,'text',223,224),
	(1575,1,NULL,NULL,'AwarenessProgramUsers',2832,2855),
	(1576,1575,NULL,NULL,'index',2833,2834),
	(1577,1575,NULL,NULL,'isAuthorized',2835,2836),
	(1578,1575,NULL,NULL,'cancelAction',2837,2838),
	(1579,1575,NULL,NULL,'saveAdvancedFilter',2839,2840),
	(1580,1575,NULL,NULL,'deleteAdvancedFilter',2841,2842),
	(1581,1575,NULL,NULL,'exportAdvancedFilterToPdf',2843,2844),
	(1582,1575,NULL,NULL,'exportAdvancedFilterToCsv',2845,2846),
	(1583,55,NULL,NULL,'deleteTextFile',277,278),
	(1584,55,NULL,NULL,'downloadExample',279,280),
	(1585,1,NULL,NULL,'AwarenessReminders',2856,2879),
	(1586,1585,NULL,NULL,'index',2857,2858),
	(1587,1585,NULL,NULL,'isAuthorized',2859,2860),
	(1588,1585,NULL,NULL,'cancelAction',2861,2862),
	(1589,1585,NULL,NULL,'saveAdvancedFilter',2863,2864),
	(1590,1585,NULL,NULL,'deleteAdvancedFilter',2865,2866),
	(1591,1585,NULL,NULL,'exportAdvancedFilterToPdf',2867,2868),
	(1592,1585,NULL,NULL,'exportAdvancedFilterToCsv',2869,2870),
	(1593,1,NULL,NULL,'AwarenessTrainings',2880,2903),
	(1594,1593,NULL,NULL,'index',2881,2882),
	(1595,1593,NULL,NULL,'isAuthorized',2883,2884),
	(1596,1593,NULL,NULL,'cancelAction',2885,2886),
	(1597,1593,NULL,NULL,'saveAdvancedFilter',2887,2888),
	(1598,1593,NULL,NULL,'deleteAdvancedFilter',2889,2890),
	(1599,1593,NULL,NULL,'exportAdvancedFilterToPdf',2891,2892),
	(1600,1593,NULL,NULL,'exportAdvancedFilterToCsv',2893,2894),
	(1601,175,NULL,NULL,'index',801,802),
	(1602,191,NULL,NULL,'index',893,894),
	(1603,334,NULL,NULL,'filterIndex',1559,1560),
	(1604,1,NULL,NULL,'ComplianceAuditAuditeeFeedbacks',2904,2927),
	(1605,1604,NULL,NULL,'index',2905,2906),
	(1606,1604,NULL,NULL,'isAuthorized',2907,2908),
	(1607,1604,NULL,NULL,'cancelAction',2909,2910),
	(1608,1604,NULL,NULL,'saveAdvancedFilter',2911,2912),
	(1609,1604,NULL,NULL,'deleteAdvancedFilter',2913,2914),
	(1610,1604,NULL,NULL,'exportAdvancedFilterToPdf',2915,2916),
	(1611,1604,NULL,NULL,'exportAdvancedFilterToCsv',2917,2918),
	(1612,1,NULL,NULL,'Api',2928,2987),
	(1613,1612,NULL,NULL,'ApiSecurityIncidentStages',2929,2954),
	(1614,1613,NULL,NULL,'index',2930,2931),
	(1615,1613,NULL,NULL,'view',2932,2933),
	(1616,1613,NULL,NULL,'isAuthorized',2934,2935),
	(1617,1613,NULL,NULL,'cancelAction',2936,2937),
	(1618,1613,NULL,NULL,'saveAdvancedFilter',2938,2939),
	(1619,1613,NULL,NULL,'deleteAdvancedFilter',2940,2941),
	(1620,1613,NULL,NULL,'exportAdvancedFilterToPdf',2942,2943),
	(1621,1613,NULL,NULL,'exportAdvancedFilterToCsv',2944,2945),
	(1622,1612,NULL,NULL,'ApiSecurityIncidents',2955,2986),
	(1623,1622,NULL,NULL,'index',2956,2957),
	(1624,1622,NULL,NULL,'add',2958,2959),
	(1625,1622,NULL,NULL,'view',2960,2961),
	(1626,1622,NULL,NULL,'edit',2962,2963),
	(1627,1622,NULL,NULL,'delete',2964,2965),
	(1628,1622,NULL,NULL,'isAuthorized',2966,2967),
	(1629,1622,NULL,NULL,'cancelAction',2968,2969),
	(1630,1622,NULL,NULL,'saveAdvancedFilter',2970,2971),
	(1631,1622,NULL,NULL,'deleteAdvancedFilter',2972,2973),
	(1632,1622,NULL,NULL,'exportAdvancedFilterToPdf',2974,2975),
	(1633,1622,NULL,NULL,'exportAdvancedFilterToCsv',2976,2977),
	(1634,2,NULL,NULL,'exportDailyCountResults',17,18),
	(1635,2,NULL,NULL,'exportDailyDataResults',19,20),
	(1636,5,NULL,NULL,'exportDailyCountResults',49,50),
	(1637,5,NULL,NULL,'exportDailyDataResults',51,52),
	(1638,12,NULL,NULL,'exportDailyCountResults',79,80),
	(1639,12,NULL,NULL,'exportDailyDataResults',81,82),
	(1640,18,NULL,NULL,'exportDailyCountResults',109,110),
	(1641,18,NULL,NULL,'exportDailyDataResults',111,112),
	(1642,24,NULL,NULL,'exportDailyCountResults',143,144),
	(1643,24,NULL,NULL,'exportDailyDataResults',145,146),
	(1644,32,NULL,NULL,'exportDailyCountResults',185,186),
	(1645,32,NULL,NULL,'exportDailyDataResults',187,188),
	(1646,42,NULL,NULL,'exportDailyCountResults',225,226),
	(1647,42,NULL,NULL,'exportDailyDataResults',227,228),
	(1648,1575,NULL,NULL,'exportDailyCountResults',2847,2848),
	(1649,1575,NULL,NULL,'exportDailyDataResults',2849,2850),
	(1650,55,NULL,NULL,'exportDailyCountResults',281,282),
	(1651,55,NULL,NULL,'exportDailyDataResults',283,284),
	(1652,1585,NULL,NULL,'exportDailyCountResults',2871,2872),
	(1653,1585,NULL,NULL,'exportDailyDataResults',2873,2874),
	(1654,1593,NULL,NULL,'exportDailyCountResults',2895,2896),
	(1655,1593,NULL,NULL,'exportDailyDataResults',2897,2898),
	(1658,75,NULL,NULL,'exportDailyCountResults',347,348),
	(1659,75,NULL,NULL,'exportDailyDataResults',349,350),
	(1660,84,NULL,NULL,'exportDailyCountResults',375,376),
	(1661,84,NULL,NULL,'exportDailyDataResults',377,378),
	(1662,89,NULL,NULL,'exportDailyCountResults',407,408),
	(1663,89,NULL,NULL,'exportDailyDataResults',409,410),
	(1664,94,NULL,NULL,'exportDailyCountResults',453,454),
	(1665,94,NULL,NULL,'exportDailyDataResults',455,456),
	(1666,108,NULL,NULL,'exportDailyCountResults',483,484),
	(1667,108,NULL,NULL,'exportDailyDataResults',485,486),
	(1668,113,NULL,NULL,'exportDailyCountResults',513,514),
	(1669,113,NULL,NULL,'exportDailyDataResults',515,516),
	(1670,119,NULL,NULL,'exportDailyCountResults',553,554),
	(1671,119,NULL,NULL,'exportDailyDataResults',555,556),
	(1672,1604,NULL,NULL,'exportDailyCountResults',2919,2920),
	(1673,1604,NULL,NULL,'exportDailyDataResults',2921,2922),
	(1674,130,NULL,NULL,'exportDailyCountResults',585,586),
	(1675,130,NULL,NULL,'exportDailyDataResults',587,588),
	(1676,137,NULL,NULL,'exportDailyCountResults',615,616),
	(1677,137,NULL,NULL,'exportDailyDataResults',617,618),
	(1678,142,NULL,NULL,'exportDailyCountResults',663,664),
	(1679,142,NULL,NULL,'exportDailyDataResults',665,666),
	(1680,153,NULL,NULL,'exportDailyCountResults',699,700),
	(1681,153,NULL,NULL,'exportDailyDataResults',701,702),
	(1682,161,NULL,NULL,'exportDailyCountResults',735,736),
	(1683,161,NULL,NULL,'exportDailyDataResults',737,738),
	(1684,167,NULL,NULL,'exportDailyCountResults',773,774),
	(1685,167,NULL,NULL,'exportDailyDataResults',775,776),
	(1686,175,NULL,NULL,'exportDailyCountResults',803,804),
	(1687,175,NULL,NULL,'exportDailyDataResults',805,806),
	(1688,180,NULL,NULL,'exportDailyCountResults',835,836),
	(1689,180,NULL,NULL,'exportDailyDataResults',837,838),
	(1690,187,NULL,NULL,'exportDailyCountResults',863,864),
	(1691,187,NULL,NULL,'exportDailyDataResults',865,866),
	(1692,191,NULL,NULL,'exportDailyCountResults',895,896),
	(1693,191,NULL,NULL,'exportDailyDataResults',897,898),
	(1700,199,NULL,NULL,'exportDailyCountResults',929,930),
	(1701,199,NULL,NULL,'exportDailyDataResults',931,932),
	(1702,206,NULL,NULL,'exportDailyCountResults',957,958),
	(1703,206,NULL,NULL,'exportDailyDataResults',959,960),
	(1704,211,NULL,NULL,'exportDailyCountResults',985,986),
	(1705,211,NULL,NULL,'exportDailyDataResults',987,988),
	(1706,216,NULL,NULL,'exportDailyCountResults',1019,1020),
	(1707,216,NULL,NULL,'exportDailyDataResults',1021,1022),
	(1708,224,NULL,NULL,'exportDailyCountResults',1049,1050),
	(1709,224,NULL,NULL,'exportDailyDataResults',1051,1052),
	(1710,230,NULL,NULL,'exportDailyCountResults',1079,1080),
	(1711,230,NULL,NULL,'exportDailyDataResults',1081,1082),
	(1712,236,NULL,NULL,'exportDailyCountResults',1113,1114),
	(1713,236,NULL,NULL,'exportDailyDataResults',1115,1116),
	(1714,244,NULL,NULL,'exportDailyCountResults',1143,1144),
	(1715,244,NULL,NULL,'exportDailyDataResults',1145,1146),
	(1716,1303,NULL,NULL,'exportDailyCountResults',2649,2650),
	(1717,1303,NULL,NULL,'exportDailyDataResults',2651,2652),
	(1718,250,NULL,NULL,'exportDailyCountResults',1195,1196),
	(1719,250,NULL,NULL,'exportDailyDataResults',1197,1198),
	(1720,266,NULL,NULL,'exportDailyCountResults',1219,1220),
	(1721,266,NULL,NULL,'exportDailyDataResults',1221,1222),
	(1722,269,NULL,NULL,'exportDailyCountResults',1249,1250),
	(1723,269,NULL,NULL,'exportDailyDataResults',1251,1252),
	(1724,274,NULL,NULL,'exportDailyCountResults',1287,1288),
	(1725,274,NULL,NULL,'exportDailyDataResults',1289,1290),
	(1726,284,NULL,NULL,'exportDailyCountResults',1319,1320),
	(1727,284,NULL,NULL,'exportDailyDataResults',1321,1322),
	(1728,291,NULL,NULL,'exportDailyCountResults',1349,1350),
	(1729,291,NULL,NULL,'exportDailyDataResults',1351,1352),
	(1730,615,NULL,NULL,'exportDailyCountResults',2479,2480),
	(1731,615,NULL,NULL,'exportDailyDataResults',2481,2482),
	(1732,297,NULL,NULL,'exportDailyCountResults',1381,1382),
	(1733,297,NULL,NULL,'exportDailyDataResults',1383,1384),
	(1734,304,NULL,NULL,'exportDailyCountResults',1413,1414),
	(1735,304,NULL,NULL,'exportDailyDataResults',1415,1416),
	(1736,311,NULL,NULL,'exportDailyCountResults',1443,1444),
	(1737,311,NULL,NULL,'exportDailyDataResults',1445,1446),
	(1738,317,NULL,NULL,'exportDailyCountResults',1473,1474),
	(1739,317,NULL,NULL,'exportDailyDataResults',1475,1476),
	(1740,323,NULL,NULL,'exportDailyCountResults',1505,1506),
	(1741,323,NULL,NULL,'exportDailyDataResults',1507,1508),
	(1742,331,NULL,NULL,'exportDailyCountResults',1529,1530),
	(1743,331,NULL,NULL,'exportDailyDataResults',1531,1532),
	(1744,334,NULL,NULL,'exportDailyCountResults',1561,1562),
	(1745,334,NULL,NULL,'exportDailyDataResults',1563,1564),
	(1746,1368,NULL,NULL,'exportDailyCountResults',2675,2676),
	(1747,1368,NULL,NULL,'exportDailyDataResults',2677,2678),
	(1748,340,NULL,NULL,'exportDailyCountResults',1593,1594),
	(1749,340,NULL,NULL,'exportDailyDataResults',1595,1596),
	(1750,347,NULL,NULL,'exportDailyCountResults',1625,1626),
	(1751,347,NULL,NULL,'exportDailyDataResults',1627,1628),
	(1752,355,NULL,NULL,'exportDailyCountResults',1651,1652),
	(1753,355,NULL,NULL,'exportDailyDataResults',1653,1654),
	(1754,359,NULL,NULL,'exportDailyCountResults',1689,1690),
	(1755,359,NULL,NULL,'exportDailyDataResults',1691,1692),
	(1756,369,NULL,NULL,'exportDailyCountResults',1719,1720),
	(1757,369,NULL,NULL,'exportDailyDataResults',1721,1722),
	(1758,375,NULL,NULL,'exportDailyCountResults',1745,1746),
	(1759,375,NULL,NULL,'exportDailyDataResults',1747,1748),
	(1760,379,NULL,NULL,'exportDailyCountResults',1775,1776),
	(1761,379,NULL,NULL,'exportDailyDataResults',1777,1778),
	(1762,385,NULL,NULL,'exportDailyCountResults',1807,1808),
	(1763,385,NULL,NULL,'exportDailyDataResults',1809,1810),
	(1764,392,NULL,NULL,'exportDailyCountResults',1851,1852),
	(1765,392,NULL,NULL,'exportDailyDataResults',1853,1854),
	(1766,403,NULL,NULL,'exportDailyCountResults',1877,1878),
	(1767,403,NULL,NULL,'exportDailyDataResults',1879,1880),
	(1768,407,NULL,NULL,'exportDailyCountResults',1915,1916),
	(1769,407,NULL,NULL,'exportDailyDataResults',1917,1918),
	(1770,418,NULL,NULL,'exportDailyCountResults',1943,1944),
	(1771,418,NULL,NULL,'exportDailyDataResults',1945,1946),
	(1772,423,NULL,NULL,'exportDailyCountResults',1971,1972),
	(1773,423,NULL,NULL,'exportDailyDataResults',1973,1974),
	(1774,428,NULL,NULL,'exportDailyCountResults',2003,2004),
	(1775,428,NULL,NULL,'exportDailyDataResults',2005,2006),
	(1776,433,NULL,NULL,'exportDailyCountResults',2031,2032),
	(1777,433,NULL,NULL,'exportDailyDataResults',2033,2034),
	(1778,438,NULL,NULL,'exportDailyCountResults',2067,2068),
	(1779,438,NULL,NULL,'exportDailyDataResults',2069,2070),
	(1780,450,NULL,NULL,'exportDailyCountResults',2097,2098),
	(1781,450,NULL,NULL,'exportDailyDataResults',2099,2100),
	(1782,456,NULL,NULL,'exportDailyCountResults',2129,2130),
	(1783,456,NULL,NULL,'exportDailyDataResults',2131,2132),
	(1784,462,NULL,NULL,'exportDailyCountResults',2173,2174),
	(1785,462,NULL,NULL,'exportDailyDataResults',2175,2176),
	(1786,474,NULL,NULL,'exportDailyCountResults',2203,2204),
	(1787,474,NULL,NULL,'exportDailyDataResults',2205,2206),
	(1788,478,NULL,NULL,'exportDailyCountResults',2235,2236),
	(1789,478,NULL,NULL,'exportDailyDataResults',2237,2238),
	(1790,485,NULL,NULL,'exportDailyCountResults',2265,2266),
	(1791,485,NULL,NULL,'exportDailyDataResults',2267,2268),
	(1792,491,NULL,NULL,'exportDailyCountResults',2301,2302),
	(1793,491,NULL,NULL,'exportDailyDataResults',2303,2304),
	(1794,500,NULL,NULL,'exportDailyCountResults',2331,2332),
	(1795,500,NULL,NULL,'exportDailyDataResults',2333,2334),
	(1796,1475,NULL,NULL,'exportDailyCountResults',2701,2702),
	(1797,1475,NULL,NULL,'exportDailyDataResults',2703,2704),
	(1798,506,NULL,NULL,'exportDailyCountResults',2379,2380),
	(1799,506,NULL,NULL,'exportDailyDataResults',2381,2382),
	(1800,518,NULL,NULL,'exportDailyCountResults',2409,2410),
	(1801,518,NULL,NULL,'exportDailyDataResults',2411,2412),
	(1802,524,NULL,NULL,'exportDailyCountResults',2453,2454),
	(1803,524,NULL,NULL,'exportDailyDataResults',2455,2456),
	(1804,1613,NULL,NULL,'exportDailyCountResults',2946,2947),
	(1805,1613,NULL,NULL,'exportDailyDataResults',2948,2949),
	(1806,1622,NULL,NULL,'exportDailyCountResults',2978,2979),
	(1807,1622,NULL,NULL,'exportDailyDataResults',2980,2981),
	(1808,1563,NULL,NULL,'exportDailyCountResults',2822,2823),
	(1809,1563,NULL,NULL,'exportDailyDataResults',2824,2825),
	(1810,1125,NULL,NULL,'exportDailyCountResults',2506,2507),
	(1811,1125,NULL,NULL,'exportDailyDataResults',2508,2509),
	(1812,1130,NULL,NULL,'exportDailyCountResults',2538,2539),
	(1813,1130,NULL,NULL,'exportDailyDataResults',2540,2541),
	(1814,1138,NULL,NULL,'exportDailyCountResults',2594,2595),
	(1815,1138,NULL,NULL,'exportDailyDataResults',2596,2597),
	(1816,1159,NULL,NULL,'exportDailyCountResults',2622,2623),
	(1817,1159,NULL,NULL,'exportDailyDataResults',2624,2625),
	(1818,1522,NULL,NULL,'CustomFieldSettings',2711,2734),
	(1819,1818,NULL,NULL,'edit',2712,2713),
	(1820,1818,NULL,NULL,'isAuthorized',2714,2715),
	(1821,1818,NULL,NULL,'cancelAction',2716,2717),
	(1822,1818,NULL,NULL,'saveAdvancedFilter',2718,2719),
	(1823,1818,NULL,NULL,'deleteAdvancedFilter',2720,2721),
	(1824,1818,NULL,NULL,'exportAdvancedFilterToPdf',2722,2723),
	(1825,1818,NULL,NULL,'exportAdvancedFilterToCsv',2724,2725),
	(1826,1818,NULL,NULL,'exportDailyCountResults',2726,2727),
	(1827,1818,NULL,NULL,'exportDailyDataResults',2728,2729),
	(1828,1522,NULL,NULL,'CustomFields',2735,2770),
	(1829,1828,NULL,NULL,'delete',2736,2737),
	(1830,1828,NULL,NULL,'add',2738,2739),
	(1831,1828,NULL,NULL,'warning',2740,2741),
	(1832,1828,NULL,NULL,'edit',2742,2743),
	(1833,1828,NULL,NULL,'saveOptions',2744,2745),
	(1834,1828,NULL,NULL,'deleteOptions',2746,2747),
	(1835,1828,NULL,NULL,'getOptions',2748,2749),
	(1836,1828,NULL,NULL,'isAuthorized',2750,2751),
	(1837,1828,NULL,NULL,'cancelAction',2752,2753),
	(1838,1828,NULL,NULL,'saveAdvancedFilter',2754,2755),
	(1839,1828,NULL,NULL,'deleteAdvancedFilter',2756,2757),
	(1840,1828,NULL,NULL,'exportAdvancedFilterToPdf',2758,2759),
	(1841,1828,NULL,NULL,'exportAdvancedFilterToCsv',2760,2761),
	(1842,1828,NULL,NULL,'exportDailyCountResults',2762,2763),
	(1843,1828,NULL,NULL,'exportDailyDataResults',2764,2765),
	(1844,1522,NULL,NULL,'CustomForms',2771,2800),
	(1845,1844,NULL,NULL,'delete',2772,2773),
	(1846,1844,NULL,NULL,'index',2774,2775),
	(1847,1844,NULL,NULL,'add',2776,2777),
	(1848,1844,NULL,NULL,'edit',2778,2779),
	(1849,1844,NULL,NULL,'isAuthorized',2780,2781),
	(1850,1844,NULL,NULL,'cancelAction',2782,2783),
	(1851,1844,NULL,NULL,'saveAdvancedFilter',2784,2785),
	(1852,1844,NULL,NULL,'deleteAdvancedFilter',2786,2787),
	(1853,1844,NULL,NULL,'exportAdvancedFilterToPdf',2788,2789),
	(1854,1844,NULL,NULL,'exportAdvancedFilterToCsv',2790,2791),
	(1855,1844,NULL,NULL,'exportDailyCountResults',2792,2793),
	(1856,1844,NULL,NULL,'exportDailyDataResults',2794,2795),
	(1857,2,NULL,NULL,'handleSystemRecords',21,22),
	(1858,2,NULL,NULL,'editAdvancedFilter',23,24),
	(1859,5,NULL,NULL,'handleSystemRecords',53,54),
	(1860,5,NULL,NULL,'editAdvancedFilter',55,56),
	(1861,12,NULL,NULL,'handleSystemRecords',83,84),
	(1862,12,NULL,NULL,'editAdvancedFilter',85,86),
	(1863,18,NULL,NULL,'handleSystemRecords',113,114),
	(1864,18,NULL,NULL,'editAdvancedFilter',115,116),
	(1865,24,NULL,NULL,'beforeDelete',147,148),
	(1866,24,NULL,NULL,'handleSystemRecords',149,150),
	(1867,24,NULL,NULL,'editAdvancedFilter',151,152),
	(1868,32,NULL,NULL,'handleSystemRecords',189,190),
	(1869,32,NULL,NULL,'editAdvancedFilter',191,192),
	(1870,42,NULL,NULL,'downloadStepFile',229,230),
	(1871,42,NULL,NULL,'viewText',231,232),
	(1872,42,NULL,NULL,'handleSystemRecords',233,234),
	(1873,42,NULL,NULL,'editAdvancedFilter',235,236),
	(1874,1575,NULL,NULL,'handleSystemRecords',2851,2852),
	(1875,1575,NULL,NULL,'editAdvancedFilter',2853,2854),
	(1876,55,NULL,NULL,'handleSystemRecords',285,286),
	(1877,55,NULL,NULL,'editAdvancedFilter',287,288),
	(1878,1585,NULL,NULL,'handleSystemRecords',2875,2876),
	(1879,1585,NULL,NULL,'editAdvancedFilter',2877,2878),
	(1880,1593,NULL,NULL,'handleSystemRecords',2899,2900),
	(1881,1593,NULL,NULL,'editAdvancedFilter',2901,2902),
	(1882,75,NULL,NULL,'handleSystemRecords',351,352),
	(1883,75,NULL,NULL,'editAdvancedFilter',353,354),
	(1884,84,NULL,NULL,'handleSystemRecords',379,380),
	(1885,84,NULL,NULL,'editAdvancedFilter',381,382),
	(1886,89,NULL,NULL,'handleSystemRecords',411,412),
	(1887,89,NULL,NULL,'editAdvancedFilter',413,414),
	(1888,94,NULL,NULL,'handleSystemRecords',457,458),
	(1889,94,NULL,NULL,'editAdvancedFilter',459,460),
	(1890,108,NULL,NULL,'handleSystemRecords',487,488),
	(1891,108,NULL,NULL,'editAdvancedFilter',489,490),
	(1892,113,NULL,NULL,'handleSystemRecords',517,518),
	(1893,113,NULL,NULL,'editAdvancedFilter',519,520),
	(1894,119,NULL,NULL,'handleSystemRecords',557,558),
	(1895,119,NULL,NULL,'editAdvancedFilter',559,560),
	(1896,1604,NULL,NULL,'handleSystemRecords',2923,2924),
	(1897,1604,NULL,NULL,'editAdvancedFilter',2925,2926),
	(1898,130,NULL,NULL,'handleSystemRecords',589,590),
	(1899,130,NULL,NULL,'editAdvancedFilter',591,592),
	(1900,137,NULL,NULL,'handleSystemRecords',619,620),
	(1901,137,NULL,NULL,'editAdvancedFilter',621,622),
	(1902,142,NULL,NULL,'trash',667,668),
	(1903,142,NULL,NULL,'auditeeExportFindings',669,670),
	(1904,142,NULL,NULL,'handleSystemRecords',671,672),
	(1905,142,NULL,NULL,'editAdvancedFilter',673,674),
	(1906,153,NULL,NULL,'handleSystemRecords',703,704),
	(1907,153,NULL,NULL,'editAdvancedFilter',705,706),
	(1908,161,NULL,NULL,'trash',739,740),
	(1909,161,NULL,NULL,'handleSystemRecords',741,742),
	(1910,161,NULL,NULL,'editAdvancedFilter',743,744),
	(1911,167,NULL,NULL,'handleSystemRecords',777,778),
	(1912,167,NULL,NULL,'editAdvancedFilter',779,780),
	(1913,175,NULL,NULL,'handleSystemRecords',807,808),
	(1914,175,NULL,NULL,'editAdvancedFilter',809,810),
	(1915,180,NULL,NULL,'duplicate',839,840),
	(1916,180,NULL,NULL,'handleSystemRecords',841,842),
	(1917,180,NULL,NULL,'editAdvancedFilter',843,844),
	(1918,187,NULL,NULL,'handleSystemRecords',867,868),
	(1919,187,NULL,NULL,'editAdvancedFilter',869,870),
	(1920,191,NULL,NULL,'hourly',899,900),
	(1921,191,NULL,NULL,'handleSystemRecords',901,902),
	(1922,191,NULL,NULL,'editAdvancedFilter',903,904),
	(1923,199,NULL,NULL,'handleSystemRecords',933,934),
	(1924,199,NULL,NULL,'editAdvancedFilter',935,936),
	(1925,206,NULL,NULL,'handleSystemRecords',961,962),
	(1926,206,NULL,NULL,'editAdvancedFilter',963,964),
	(1927,211,NULL,NULL,'handleSystemRecords',989,990),
	(1928,211,NULL,NULL,'editAdvancedFilter',991,992),
	(1929,216,NULL,NULL,'handleSystemRecords',1023,1024),
	(1930,216,NULL,NULL,'editAdvancedFilter',1025,1026),
	(1931,224,NULL,NULL,'handleSystemRecords',1053,1054),
	(1932,224,NULL,NULL,'editAdvancedFilter',1055,1056),
	(1933,230,NULL,NULL,'handleSystemRecords',1083,1084),
	(1934,230,NULL,NULL,'editAdvancedFilter',1085,1086),
	(1935,236,NULL,NULL,'handleSystemRecords',1117,1118),
	(1936,236,NULL,NULL,'editAdvancedFilter',1119,1120),
	(1937,244,NULL,NULL,'handleSystemRecords',1147,1148),
	(1938,244,NULL,NULL,'editAdvancedFilter',1149,1150),
	(1939,1303,NULL,NULL,'handleSystemRecords',2653,2654),
	(1940,1303,NULL,NULL,'editAdvancedFilter',2655,2656),
	(1941,250,NULL,NULL,'handleSystemRecords',1199,1200),
	(1942,250,NULL,NULL,'editAdvancedFilter',1201,1202),
	(1943,266,NULL,NULL,'handleSystemRecords',1223,1224),
	(1944,266,NULL,NULL,'editAdvancedFilter',1225,1226),
	(1945,269,NULL,NULL,'handleSystemRecords',1253,1254),
	(1946,269,NULL,NULL,'editAdvancedFilter',1255,1256),
	(1947,274,NULL,NULL,'downloadAttachment',1291,1292),
	(1948,274,NULL,NULL,'handleSystemRecords',1293,1294),
	(1949,274,NULL,NULL,'editAdvancedFilter',1295,1296),
	(1950,284,NULL,NULL,'handleSystemRecords',1323,1324),
	(1951,284,NULL,NULL,'editAdvancedFilter',1325,1326),
	(1952,291,NULL,NULL,'handleSystemRecords',1353,1354),
	(1953,291,NULL,NULL,'editAdvancedFilter',1355,1356),
	(1954,615,NULL,NULL,'handleSystemRecords',2483,2484),
	(1955,615,NULL,NULL,'editAdvancedFilter',2485,2486),
	(1956,297,NULL,NULL,'handleSystemRecords',1385,1386),
	(1957,297,NULL,NULL,'editAdvancedFilter',1387,1388),
	(1958,304,NULL,NULL,'handleSystemRecords',1417,1418),
	(1959,304,NULL,NULL,'editAdvancedFilter',1419,1420),
	(1960,311,NULL,NULL,'handleSystemRecords',1447,1448),
	(1961,311,NULL,NULL,'editAdvancedFilter',1449,1450),
	(1962,317,NULL,NULL,'handleSystemRecords',1477,1478),
	(1963,317,NULL,NULL,'editAdvancedFilter',1479,1480),
	(1964,323,NULL,NULL,'handleSystemRecords',1509,1510),
	(1965,323,NULL,NULL,'editAdvancedFilter',1511,1512),
	(1966,1,NULL,NULL,'Queue',2988,3013),
	(1967,1966,NULL,NULL,'index',2989,2990),
	(1968,1966,NULL,NULL,'delete',2991,2992),
	(1969,1966,NULL,NULL,'handleSystemRecords',2993,2994),
	(1970,1966,NULL,NULL,'isAuthorized',2995,2996),
	(1971,1966,NULL,NULL,'cancelAction',2997,2998),
	(1972,1966,NULL,NULL,'saveAdvancedFilter',2999,3000),
	(1973,1966,NULL,NULL,'editAdvancedFilter',3001,3002),
	(1974,1966,NULL,NULL,'deleteAdvancedFilter',3003,3004),
	(1975,1966,NULL,NULL,'exportAdvancedFilterToPdf',3005,3006),
	(1976,1966,NULL,NULL,'exportAdvancedFilterToCsv',3007,3008),
	(1977,1966,NULL,NULL,'exportDailyCountResults',3009,3010),
	(1978,1966,NULL,NULL,'exportDailyDataResults',3011,3012),
	(1979,331,NULL,NULL,'handleSystemRecords',1533,1534),
	(1980,331,NULL,NULL,'editAdvancedFilter',1535,1536),
	(1981,334,NULL,NULL,'handleSystemRecords',1565,1566),
	(1982,334,NULL,NULL,'editAdvancedFilter',1567,1568),
	(1983,1368,NULL,NULL,'handleSystemRecords',2679,2680),
	(1984,1368,NULL,NULL,'editAdvancedFilter',2681,2682),
	(1985,340,NULL,NULL,'handleSystemRecords',1597,1598),
	(1986,340,NULL,NULL,'editAdvancedFilter',1599,1600),
	(1987,347,NULL,NULL,'handleSystemRecords',1629,1630),
	(1988,347,NULL,NULL,'editAdvancedFilter',1631,1632),
	(1989,355,NULL,NULL,'handleSystemRecords',1655,1656),
	(1990,355,NULL,NULL,'editAdvancedFilter',1657,1658),
	(1991,359,NULL,NULL,'handleSystemRecords',1693,1694),
	(1992,359,NULL,NULL,'editAdvancedFilter',1695,1696),
	(1993,369,NULL,NULL,'handleSystemRecords',1723,1724),
	(1994,369,NULL,NULL,'editAdvancedFilter',1725,1726),
	(1995,375,NULL,NULL,'handleSystemRecords',1749,1750),
	(1996,375,NULL,NULL,'editAdvancedFilter',1751,1752),
	(1997,379,NULL,NULL,'handleSystemRecords',1779,1780),
	(1998,379,NULL,NULL,'editAdvancedFilter',1781,1782),
	(1999,385,NULL,NULL,'handleSystemRecords',1811,1812),
	(2000,385,NULL,NULL,'editAdvancedFilter',1813,1814),
	(2001,392,NULL,NULL,'handleSystemRecords',1855,1856),
	(2002,392,NULL,NULL,'editAdvancedFilter',1857,1858),
	(2003,403,NULL,NULL,'handleSystemRecords',1881,1882),
	(2004,403,NULL,NULL,'editAdvancedFilter',1883,1884),
	(2005,407,NULL,NULL,'handleSystemRecords',1919,1920),
	(2006,407,NULL,NULL,'editAdvancedFilter',1921,1922),
	(2007,418,NULL,NULL,'handleSystemRecords',1947,1948),
	(2008,418,NULL,NULL,'editAdvancedFilter',1949,1950),
	(2009,423,NULL,NULL,'handleSystemRecords',1975,1976),
	(2010,423,NULL,NULL,'editAdvancedFilter',1977,1978),
	(2011,428,NULL,NULL,'handleSystemRecords',2007,2008),
	(2012,428,NULL,NULL,'editAdvancedFilter',2009,2010),
	(2013,433,NULL,NULL,'handleSystemRecords',2035,2036),
	(2014,433,NULL,NULL,'editAdvancedFilter',2037,2038),
	(2015,438,NULL,NULL,'handleSystemRecords',2071,2072),
	(2016,438,NULL,NULL,'editAdvancedFilter',2073,2074),
	(2017,450,NULL,NULL,'handleSystemRecords',2101,2102),
	(2018,450,NULL,NULL,'editAdvancedFilter',2103,2104),
	(2019,456,NULL,NULL,'handleSystemRecords',2133,2134),
	(2020,456,NULL,NULL,'editAdvancedFilter',2135,2136),
	(2021,462,NULL,NULL,'downloadLogs',2177,2178),
	(2022,462,NULL,NULL,'getLogo',2179,2180),
	(2023,462,NULL,NULL,'handleSystemRecords',2181,2182),
	(2024,462,NULL,NULL,'editAdvancedFilter',2183,2184),
	(2025,474,NULL,NULL,'handleSystemRecords',2207,2208),
	(2026,474,NULL,NULL,'editAdvancedFilter',2209,2210),
	(2027,478,NULL,NULL,'handleSystemRecords',2239,2240),
	(2028,478,NULL,NULL,'editAdvancedFilter',2241,2242),
	(2029,485,NULL,NULL,'handleSystemRecords',2269,2270),
	(2030,485,NULL,NULL,'editAdvancedFilter',2271,2272),
	(2031,491,NULL,NULL,'handleSystemRecords',2305,2306),
	(2032,491,NULL,NULL,'editAdvancedFilter',2307,2308),
	(2033,500,NULL,NULL,'handleSystemRecords',2335,2336),
	(2034,500,NULL,NULL,'editAdvancedFilter',2337,2338),
	(2035,1475,NULL,NULL,'handleSystemRecords',2705,2706),
	(2036,1475,NULL,NULL,'editAdvancedFilter',2707,2708),
	(2037,506,NULL,NULL,'handleSystemRecords',2383,2384),
	(2038,506,NULL,NULL,'editAdvancedFilter',2385,2386),
	(2039,518,NULL,NULL,'handleSystemRecords',2413,2414),
	(2040,518,NULL,NULL,'editAdvancedFilter',2415,2416),
	(2041,524,NULL,NULL,'handleSystemRecords',2457,2458),
	(2042,524,NULL,NULL,'editAdvancedFilter',2459,2460),
	(2043,1613,NULL,NULL,'handleSystemRecords',2950,2951),
	(2044,1613,NULL,NULL,'editAdvancedFilter',2952,2953),
	(2045,1622,NULL,NULL,'handleSystemRecords',2982,2983),
	(2046,1622,NULL,NULL,'editAdvancedFilter',2984,2985),
	(2047,71,NULL,NULL,'BackupRestore',291,316),
	(2048,2047,NULL,NULL,'index',292,293),
	(2049,2047,NULL,NULL,'getBackup',294,295),
	(2050,2047,NULL,NULL,'handleSystemRecords',296,297),
	(2051,2047,NULL,NULL,'isAuthorized',298,299),
	(2052,2047,NULL,NULL,'cancelAction',300,301),
	(2053,2047,NULL,NULL,'saveAdvancedFilter',302,303),
	(2054,2047,NULL,NULL,'editAdvancedFilter',304,305),
	(2055,2047,NULL,NULL,'deleteAdvancedFilter',306,307),
	(2056,2047,NULL,NULL,'exportAdvancedFilterToPdf',308,309),
	(2057,2047,NULL,NULL,'exportAdvancedFilterToCsv',310,311),
	(2058,2047,NULL,NULL,'exportDailyCountResults',312,313),
	(2059,2047,NULL,NULL,'exportDailyDataResults',314,315),
	(2060,1,NULL,NULL,'BulkActions',3014,3041),
	(2061,2060,NULL,NULL,'BulkActions',3015,3040),
	(2062,2061,NULL,NULL,'apply',3016,3017),
	(2063,2061,NULL,NULL,'submit',3018,3019),
	(2064,2061,NULL,NULL,'handleSystemRecords',3020,3021),
	(2065,2061,NULL,NULL,'isAuthorized',3022,3023),
	(2066,2061,NULL,NULL,'cancelAction',3024,3025),
	(2067,2061,NULL,NULL,'saveAdvancedFilter',3026,3027),
	(2068,2061,NULL,NULL,'editAdvancedFilter',3028,3029),
	(2069,2061,NULL,NULL,'deleteAdvancedFilter',3030,3031),
	(2070,2061,NULL,NULL,'exportAdvancedFilterToPdf',3032,3033),
	(2071,2061,NULL,NULL,'exportAdvancedFilterToCsv',3034,3035),
	(2072,2061,NULL,NULL,'exportDailyCountResults',3036,3037),
	(2073,2061,NULL,NULL,'exportDailyDataResults',3038,3039),
	(2074,1818,NULL,NULL,'handleSystemRecords',2730,2731),
	(2075,1818,NULL,NULL,'editAdvancedFilter',2732,2733),
	(2076,1828,NULL,NULL,'handleSystemRecords',2766,2767),
	(2077,1828,NULL,NULL,'editAdvancedFilter',2768,2769),
	(2078,1844,NULL,NULL,'handleSystemRecords',2796,2797),
	(2079,1844,NULL,NULL,'editAdvancedFilter',2798,2799),
	(2080,1563,NULL,NULL,'handleSystemRecords',2826,2827),
	(2081,1563,NULL,NULL,'editAdvancedFilter',2828,2829),
	(2082,1,NULL,NULL,'ObjectVersion',3042,3069),
	(2083,2082,NULL,NULL,'ObjectVersion',3043,3068),
	(2084,2083,NULL,NULL,'history',3044,3045),
	(2085,2083,NULL,NULL,'restore',3046,3047),
	(2086,2083,NULL,NULL,'cancelAction',3048,3049),
	(2087,2083,NULL,NULL,'handleSystemRecords',3050,3051),
	(2088,2083,NULL,NULL,'isAuthorized',3052,3053),
	(2089,2083,NULL,NULL,'saveAdvancedFilter',3054,3055),
	(2090,2083,NULL,NULL,'editAdvancedFilter',3056,3057),
	(2091,2083,NULL,NULL,'deleteAdvancedFilter',3058,3059),
	(2092,2083,NULL,NULL,'exportAdvancedFilterToPdf',3060,3061),
	(2093,2083,NULL,NULL,'exportAdvancedFilterToCsv',3062,3063),
	(2094,2083,NULL,NULL,'exportDailyCountResults',3064,3065),
	(2095,2083,NULL,NULL,'exportDailyDataResults',3066,3067),
	(2096,1125,NULL,NULL,'handleSystemRecords',2510,2511),
	(2097,1125,NULL,NULL,'editAdvancedFilter',2512,2513),
	(2098,1130,NULL,NULL,'handleSystemRecords',2542,2543),
	(2099,1130,NULL,NULL,'editAdvancedFilter',2544,2545),
	(2100,1138,NULL,NULL,'handleSystemRecords',2598,2599),
	(2101,1138,NULL,NULL,'editAdvancedFilter',2600,2601),
	(2102,1159,NULL,NULL,'handleSystemRecords',2626,2627),
	(2103,1159,NULL,NULL,'editAdvancedFilter',2628,2629);












INSERT INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES 
	(1,NULL,'Group',10,NULL,1,16),
	(2,11,'User',1,NULL,2,3),
	(3,NULL,'Group',11,NULL,17,18),
	(4,NULL,'Group',12,NULL,19,20),
	(5,NULL,'Group',13,NULL,21,22);


INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES 
	(1,1,1,'1','1','1','1'),
	(3,4,263,'1','1','1','1'),
	(4,4,264,'1','1','1','1'),
	(5,3,144,'1','1','1','1'),
	(6,3,145,'1','1','1','1'),
	(7,3,3,'1','1','1','1'),
	(8,3,146,'1','1','1','1'),
	(9,5,1,'1','1','1','1'),
	(10,5,473,'-1','-1','-1','-1'),
	(11,5,469,'-1','-1','-1','-1'),
	(12,5,470,'-1','-1','-1','-1'),
	(13,5,466,'-1','-1','-1','-1'),
	(14,5,464,'-1','-1','-1','-1'),
	(15,5,463,'-1','-1','-1','-1'),
	(16,5,872,'-1','-1','-1','-1'),
	(17,5,465,'-1','-1','-1','-1'),
	(18,5,468,'-1','-1','-1','-1'),
	(19,5,471,'-1','-1','-1','-1'),
	(20,5,472,'-1','-1','-1','-1'),
	(21,5,467,'-1','-1','-1','-1'),
	(22,3,122,'1','1','1','1'),
	(23,3,123,'1','1','1','1'),
	(24,3,125,'1','1','1','1'),
	(25,3,36,'1','1','1','1'),
	(26,3,37,'1','1','1','1'),
	(27,3,38,'1','1','1','1'),
	(29,4,696,'1','1','1','1'),
	(30,3,696,'1','1','1','1'),
	(31,4,514,'1','1','1','1'),
	(32,3,514,'1','1','1','1'),
	(33,3,512,'1','1','1','1'),
	(34,4,512,'1','1','1','1'),
	(35,4,513,'1','1','1','1'),
	(36,3,513,'1','1','1','1'),
	(37,4,515,'1','1','1','1'),
	(38,3,515,'1','1','1','1'),
	(39,3,4,'1','1','1','1'),
	(40,3,152,'1','1','1','1');










INSERT INTO `asset_media_types` (`id`, `name`, `editable`, `created`, `modified`) VALUES 
	(1,'Data Asset',0,NULL,NULL),
	(2,'Facilities',0,NULL,NULL),
	(3,'People',0,NULL,NULL),
	(4,'Hardware',0,NULL,NULL),
	(5,'Software',0,NULL,NULL),
	(6,'IT Service',0,NULL,NULL),
	(7,'Network',0,NULL,NULL),
	(8,'Financial',0,NULL,NULL);


INSERT INTO `asset_media_types_threats` (`id`, `asset_media_type_id`, `threat_id`) VALUES 
	(1,1,6),
	(2,1,7),
	(3,1,10),
	(4,1,16),
	(5,1,27),
	(6,2,2),
	(7,2,3),
	(8,2,18),
	(9,2,19),
	(10,2,20),
	(12,2,30),
	(13,2,31),
	(14,2,32),
	(15,3,1),
	(16,3,2),
	(17,3,3),
	(18,3,4),
	(19,3,5),
	(20,3,6),
	(21,3,7),
	(22,3,13),
	(23,3,14),
	(24,3,15),
	(25,3,16),
	(26,3,17),
	(27,3,21),
	(28,3,26),
	(29,3,27),
	(30,3,30),
	(31,3,32),
	(32,3,33),
	(33,3,34),
	(34,3,35),
	(35,4,4),
	(36,4,5),
	(37,4,14),
	(38,4,15),
	(39,5,8),
	(40,5,9),
	(41,5,10),
	(42,5,14),
	(43,5,15),
	(44,5,21),
	(45,5,22),
	(46,5,23),
	(47,5,33),
	(48,6,8),
	(49,6,9),
	(50,6,10),
	(51,6,13),
	(52,6,14),
	(53,6,15),
	(54,6,21),
	(55,6,22),
	(56,6,23),
	(57,6,26),
	(58,6,30),
	(59,6,33),
	(60,7,8),
	(61,7,9),
	(62,7,10),
	(63,7,11),
	(64,7,12),
	(65,7,14),
	(66,7,15),
	(67,7,21),
	(68,7,22),
	(69,7,24),
	(70,7,25),
	(71,7,26),
	(72,8,16),
	(73,8,27);


INSERT INTO `asset_media_types_vulnerabilities` (`id`, `asset_media_type_id`, `vulnerability_id`) VALUES 
	(1,1,2),
	(2,1,3),
	(3,3,1),
	(4,3,3),
	(5,5,2),
	(6,5,3),
	(7,6,2),
	(8,6,3),
	(9,7,3),
	(10,8,3),
	(11,8,2);
































































































INSERT INTO `business_units` (`id`, `name`, `description`, `workflow_status`, `workflow_owner_id`, `_hidden`, `created`, `modified`) VALUES 
	(1,'Everyone','',0,NULL,1,'2015-12-19 00:00:00','2015-12-19 00:00:00');










INSERT INTO `compliance_finding_statuses` (`id`, `name`) VALUES 
	(1,'Open Item'),
	(2,'Closed Item');




















INSERT INTO `compliance_statuses` (`id`, `name`) VALUES 
	(1,'On-Going'),
	(2,'Compliant'),
	(3,'Non-Compliant'),
	(4,'Not-Applicable');


INSERT INTO `compliance_treatment_strategies` (`id`, `name`) VALUES 
	(1,'Compliant'),
	(2,'Not Applicable'),
	(3,'Not Compliant');










INSERT INTO `custom_field_settings` (`id`, `model`, `status`) VALUES 
	(1,'SecurityService',0),
	(2,'SecurityServiceAudit',0),
	(3,'SecurityServiceMaintenance',0),
	(4,'BusinessUnit',0),
	(5,'Process',0),
	(6,'ThirdParty',0),
	(7,'Asset',0),
	(8,'Risk',0),
	(9,'ThirdPartyRisk',0),
	(10,'BusinessContinuity',0);




INSERT INTO `data_asset_statuses` (`id`, `name`) VALUES 
	(1,'Created'),
	(2,'Modified'),
	(3,'Stored'),
	(4,'Transit'),
	(5,'Deleted'),
	(6,'Tainted / Broken'),
	(7,'Unnecessary');


































INSERT INTO `groups` (`id`, `name`, `description`, `status`, `created`, `modified`) VALUES 
	(10,'Admin','',1,'2013-10-14 16:18:08','2013-10-14 16:18:08'),
	(11,'Third Party Feedback','',1,'2016-01-07 17:07:53','2016-01-07 17:07:53'),
	(12,'Notification Feedback','',1,'2016-01-07 17:08:02','2016-01-07 17:08:02'),
	(13,'All but Settings','',1,'2016-01-07 17:08:10','2016-01-07 17:08:10');






INSERT INTO `ldap_connector_authentication` (`id`, `auth_users`, `auth_users_id`, `auth_awareness`, `auth_awareness_id`, `auth_policies`, `auth_policies_id`, `modified`) VALUES 
	(1,0,NULL,0,NULL,0,NULL,'2015-08-16 11:20:01');
























































INSERT INTO `project_statuses` (`id`, `name`) VALUES 
	(1,'Planned'),
	(2,'Ongoing'),
	(3,'Completed');


















INSERT INTO `risk_calculations` (`id`, `model`, `method`, `modified`) VALUES 
	(1,'Risk','eramba','2016-11-18 14:38:23'),
	(2,'ThirdPartyRisk','eramba','2016-11-18 14:38:23'),
	(3,'BusinessContinuity','eramba','2016-11-18 14:38:23');


















INSERT INTO `risk_mitigation_strategies` (`id`, `name`) VALUES 
	(1,'Accept'),
	(2,'Avoid'),
	(3,'Mitigate'),
	(4,'Transfer');
















INSERT INTO `schema_migrations` (`id`, `class`, `type`, `created`) VALUES 
	(1,'InitMigrations','Migrations','2016-01-17 20:45:25'),
	(2,'ConvertVersionToClassNames','Migrations','2016-01-17 20:45:25'),
	(3,'IncreaseClassNameLength','Migrations','2016-01-17 20:45:25'),
	(4,'E101000','app','2016-01-17 20:47:16'),
	(5,'E101001','app','2016-11-18 14:34:44'),
	(6,'E101002','app','2016-11-18 14:38:23'),
	(7,'E101003','app','2016-11-18 14:39:17'),
	(8,'E101004','app','2016-11-18 14:39:23'),
	(9,'E101005','app','2016-11-18 14:40:22'),
	(10,'E101006','app','2016-11-18 14:40:47'),
	(11,'E101007','app','2016-11-18 14:42:46'),
	(12,'E101008','app','2016-11-18 14:47:11'),
	(13,'E101009','app','2016-11-18 14:48:32'),
	(14,'E101010','app','2017-02-22 21:32:29'),
	(15,'E101011','app','2017-02-22 21:32:35'),
	(16,'E101012','app','2017-02-22 21:32:37'),
	(17,'E101013','app','2017-02-22 21:32:39'),
	(18,'E101014','app','2017-02-22 21:32:39'),
	(19,'E101015','app','2017-02-22 21:32:40'),
  (20,'E101016','app','2017-02-22 21:32:40');










INSERT INTO `security_incident_statuses` (`id`, `name`) VALUES 
	(2,'Ongoing'),
	(3,'Closed');


































INSERT INTO `security_service_types` (`id`, `name`) VALUES 
	(2,'Design'),
	(4,'Production');














INSERT INTO `setting_groups` (`id`, `slug`, `parent_slug`, `name`, `icon_code`, `notes`, `url`, `hidden`, `order`) VALUES 
	(1,'ACCESSLST','ACCESSMGT','Access Lists',NULL,NULL,'{"controller":"admin", "action":"acl", "0" :"aros", "1":"ajax_role_permissions"}',0,0),
	(2,'ACCESSMGT',NULL,'Access Management','icon-cog',NULL,NULL,0,0),
	(3,'AUTH','ACCESSMGT','Authentication ',NULL,NULL,'{"controller":"ldapConnectors","action":"authentication"}',0,0),
	(4,'BANNER','SEC','Banners',NULL,NULL,NULL,1,0),
	(5,'BAR','DB','Backup & Restore',NULL,NULL,'{"controller":"backupRestore","action":"index", "plugin":"backupRestore"}',0,0),
	(6,'BFP','SEC','Brute Force Protection',NULL,'This setting allows you to protect the login page of eramba from being brute-force attacked.',NULL,0,0),
	(7,'CUE','LOC','Currency',NULL,NULL,NULL,0,0),
	(8,'DASH',NULL,'Dashboard','icon-cog',NULL,NULL,0,0),
	(9,'DASHRESET','DASH','Reset Dashboards',NULL,NULL,'{"controller":"settings","action":"resetDashboards"}',0,0),
	(10,'DB',NULL,'Database','icon-cog',NULL,NULL,0,0),
	(11,'DBCNF','DB','Database Configurations',NULL,NULL,NULL,1,0),
	(12,'DBRESET','DB','Reset Database',NULL,NULL,'{"controller":"settings","action":"resetDatabase"}',0,0),
	(13,'DEBUG',NULL,'Debug Settings and Logs','icon-cog',NULL,NULL,0,0),
	(14,'DEBUGCFG','DEBUG','Debug Config',NULL,NULL,NULL,0,0),
	(15,'ERRORLOG','DEBUG','Error Log',NULL,NULL,'{"controller":"settings","action":"logs", "0":"error"}',0,0),
	(16,'GROUP','ACCESSMGT','Groups ',NULL,NULL,'{"controller":"groups","action":"index"}',0,0),
	(17,'LDAP','ACCESSMGT','LDAP Connectors',NULL,NULL,'{"controller":"ldapConnectors","action":"index"}',0,0),
	(18,'LOC',NULL,'Localization','icon-cog',NULL,NULL,0,0),
	(19,'MAIL',NULL,'Mail','icon-cog',NULL,NULL,0,0),
	(20,'MAILCNF','MAIL','Mail Configurations',NULL,NULL,NULL,0,0),
	(21,'MAILLOG','DEBUG','Email Log',NULL,NULL,'{"controller":"settings","action":"logs", "0":"email"}',0,0),
	(22,'PRELOAD','DB','Pre-load the database with default databases',NULL,NULL,NULL,1,0),
	(23,'RISK',NULL,'Risk','icon-cog',NULL,NULL,1,0),
	(24,'RISKAPPETITE','RISK','Risk appetite',NULL,NULL,NULL,0,0),
	(25,'ROLES','ACCESSMGT','Roles',NULL,NULL,'{"controller":"scopes","action":"index"}',0,0),
	(26,'SEC',NULL,'Security','icon-cog',NULL,NULL,0,0),
	(27,'SECKEY','SEC','Security Key',NULL,NULL,NULL,0,0),
	(28,'USER','ACCESSMGT','User Management',NULL,NULL,'{"controller":"users","action":"index"}',0,0),
	(29,'CLRCACHE','DEBUG','Clear Cache',NULL,NULL,'{"controller":"settings","action":"deleteCache"}',0,0),
	(30,'CLRACLCACHE','DEBUG','Clear ACL Cache',NULL,NULL,'{"controller":"settings","action":"deleteCache", "0":"acl"}',1,0),
	(31,'LOGO','LOC','Custom Logo',NULL,NULL,'{"controller":"settings","action":"customLogo"}',0,0),
	(32,'HEALTH','SEC','System Health',NULL,NULL,'{"controller":"settings","action":"systemHealth"}',0,0),
	(33,'TZONE','LOC','Timezone',NULL,NULL,NULL,0,0),
	(34,'UPDATES','SEC','Updates',NULL,NULL,'{"controller":"updates","action":"index"}',0,0),
	(35,'NOTIFICATION','ACCESSMGT','Notifications',NULL,NULL,'{"controller":"notificationSystem","action":"listItems"}',0,0),
	(36,'CRON','ACCESSMGT','Cron Jobs',NULL,NULL,'{"controller":"cron","action":"index"}',0,0),
	(37,'BACKUP','DB','Backup Configuration',NULL,NULL,NULL,0,2),
	(38,'QUEUE','MAIL','Emails In Queue',NULL,NULL,'{"controller":"queue", "action":"index", "?" :"advanced_filter=1"}',0,0);


INSERT INTO `settings` (`id`, `active`, `name`, `variable`, `value`, `default_value`, `values`, `type`, `options`, `hidden`, `required`, `setting_group_slug`, `setting_type`, `order`, `modified`, `created`) VALUES 
	(2,1,'DB Schema Version','DB_SCHEMA_VERSION','e1.0.1.015',NULL,NULL,'text',NULL,1,0,NULL,'constant',0,'2017-02-22 21:32:39','2015-12-19 00:00:00'),
	(3,1,'Client ID','CLIENT_ID',NULL,NULL,NULL,'text',NULL,1,0,NULL,'constant',0,'2016-11-18 14:37:22','2015-12-19 00:00:00'),
	(4,1,'Bruteforce wrong logins','BRUTEFORCE_WRONG_LOGINS','3',NULL,NULL,'number','{"min":1,"max":10,"step":1}',0,0,'BFP','constant',0,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(5,1,'Bruteforce second ago','BRUTEFORCE_SECONDS_AGO','60',NULL,NULL,'number','{"min":10,"max":120,"step":1}',0,0,'BFP','constant',0,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(10,1,'Default currency','DEFAULT_CURRENCY','EUR',NULL,'configDefaultCurrency','select','{"AUD":"AUD","CAD":"CAD","USD":"USD","EUR":"EUR","GBP":"GBP","JPY":"JPY"}',0,0,'CUE','config',0,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(11,1,'Type','SMTP_USE','0',NULL,NULL,'select','{"0":"Mail","1":"SMTP"}',0,0,'MAILCNF','constant',0,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(12,1,'SMTP host','SMTP_HOST','',NULL,NULL,'text',NULL,0,0,'MAILCNF','constant',1,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(13,1,'SMTP user','SMTP_USER','',NULL,NULL,'text',NULL,0,0,'MAILCNF','constant',3,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(14,1,'SMTP password','SMTP_PWD','',NULL,NULL,'password',NULL,0,0,'MAILCNF','constant',4,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(15,1,'SMTP timeout','SMTP_TIMEOUT','60',NULL,NULL,'number','{"min":1,"max":120,"step":1}',0,0,'MAILCNF','constant',5,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(16,1,'SMTP port','SMTP_PORT','',NULL,NULL,'text',NULL,0,0,'MAILCNF','constant',6,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(18,1,'No reply Email','NO_REPLY_EMAIL','noreply@domain.org',NULL,NULL,'text',NULL,0,0,'MAILCNF','constant',7,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(19,1,'Cron security key','CRON_SECURITY_KEY','egkrjng328525798',NULL,NULL,'text',NULL,0,0,'SECKEY','constant',0,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(20,1,'Bruteforce ban from minutes','BRUTEFORCE_BAN_FOR_MINUTES','5',NULL,NULL,'number','{"min":1,"max":120,"step":1}',0,0,'BFP','constant',0,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(21,1,'Banners off','BANNERS_OFF','1',NULL,NULL,'checkbox',NULL,0,0,'BANNER','constant',0,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(22,1,'Debug','DEBUG','0',NULL,'configDebug','checkbox',NULL,0,0,'DEBUGCFG','config',0,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(23,1,'Email Debug','EMAIL_DEBUG','0',NULL,'configEmailDebug','checkbox',NULL,0,0,'DEBUGCFG','config',0,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(24,1,'Risk Appetite','RISK_APPETITE','1',NULL,NULL,'number','{"min":0,"max":9999,"step":1}',0,0,'RISKAPPETITE','constant',0,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(25,1,'Encryption','USE_SSL','0',NULL,NULL,'select','{"0":"No Encryption","1":"SSL","2":"TLS"}',0,0,'MAILCNF','constant',2,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(26,1,'Timezone','TIMEZONE',NULL,NULL,'configTimezone','select',NULL,0,0,'TZONE','config',0,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(27,1,'Backups Enabled','BACKUPS_ENABLED','1',NULL,NULL,'checkbox',NULL,0,0,'BACKUP','constant',0,'2017-02-22 21:32:29','2017-02-22 21:32:29'),
	(28,1,'Backup Day Period','BACKUP_DAY_PERIOD','1',NULL,NULL,'select','{"1":"Every day","2":"Every 2 days","3":"Every 3 days","4":"Every 4 days","5":"Every 5 days","6":"Every 6 days","7":"Every 7 days"}',0,0,'BACKUP','constant',0,'2017-02-22 21:32:29','2017-02-22 21:32:29'),
	(29,1,'Backup Files Limit','BACKUP_FILES_LIMIT','15',NULL,NULL,'select','{"1":"1","5":"5","10":"10","15":"15"}',0,0,'BACKUP','constant',0,'2017-02-22 21:32:29','2017-02-22 21:32:29'),
	(30,1,'Name','EMAIL_NAME','',NULL,NULL,'text',NULL,0,0,'MAILCNF','constant',6,'2017-02-22 21:32:29','2017-02-22 21:32:29');












INSERT INTO `third_parties` (`id`, `name`, `description`, `third_party_type_id`, `security_incident_count`, `security_incident_open_count`, `service_contract_count`, `workflow_status`, `workflow_owner_id`, `_hidden`, `created`, `modified`) VALUES 
	(1,'None','',NULL,0,0,0,0,NULL,1,'2015-12-19 00:00:00','2015-12-19 00:00:00');




















INSERT INTO `third_party_types` (`id`, `name`) VALUES 
	(1,'Customers'),
	(2,'Suppliers'),
	(3,'Regulators');


INSERT INTO `threats` (`id`, `name`) VALUES 
	(1,'Intentional Complot'),
	(2,'Pandemic Issues'),
	(3,'Strikes'),
	(4,'Unintentional Loss of Equipment'),
	(5,'Intentional Theft of Equipment'),
	(6,'Unintentional Loss of Information'),
	(7,'Intentional Theft of Information'),
	(8,'Remote Exploit'),
	(9,'Abuse of Service'),
	(10,'Web Application Attack'),
	(11,'Network Attack'),
	(12,'Sniffing'),
	(13,'Phishing'),
	(14,'Malware/Trojan Distribution'),
	(15,'Viruses'),
	(16,'Copyright Infrigment'),
	(17,'Social Engineering'),
	(18,'Natural Disasters'),
	(19,'Fire'),
	(20,'Flooding'),
	(21,'Ilegal Infiltration'),
	(22,'DOS Attack'),
	(23,'Brute Force Attack'),
	(24,'Tampering'),
	(25,'Tunneling'),
	(26,'Man in the Middle'),
	(27,'Fraud'),
	(28,'Other'),
	(30,'Terrorist Attack'),
	(31,'Floodings'),
	(32,'Third Party Intrusion'),
	(33,'Abuse of Priviledge'),
	(34,'Unauthorised records'),
	(35,'Spying');




INSERT INTO `users` (`id`, `name`, `surname`, `group_id`, `email`, `login`, `password`, `language`, `status`, `blocked`, `local_account`, `api_allow`, `created`, `modified`) VALUES 
	(1,'Admin','Admin',10,'admin@eramba.org','admin','$2a$10$WhVO3Jj4nFhCj6bToUOztun/oceKY6rT2db2bu430dW5/lU0w9KJ.','eng',1,0,1,0,'2013-10-14 16:19:04','2015-09-11 18:19:52');




INSERT INTO `vulnerabilities` (`id`, `name`) VALUES 
	(1,'Lack of Information'),
	(2,'Lack of Integrity Checks'),
	(3,'Lack of Logs'),
	(4,'No Change Management'),
	(5,'Weak CheckOut Procedures'),
	(6,'Supplier Failure'),
	(7,'Lack of alternative Power Sources'),
	(8,'Lack of Physical Guards'),
	(9,'Lack of Patching'),
	(10,'Web Application Vulnerabilities'),
	(11,'Lack of CCTV'),
	(12,'Lack of Movement Sensors'),
	(13,'Lack of Procedures'),
	(14,'Lack of Network Controls'),
	(15,'Lack of Strong Authentication'),
	(16,'Lack of Encryption in Motion'),
	(17,'Lack of Encryption at Rest'),
	(18,'Creeping Accounts'),
	(19,'Hardware Malfunction'),
	(20,'Software Malfunction'),
	(21,'Lack of Fire Extinguishers'),
	(22,'Lack of alternative exit doors'),
	(23,'Weak Passwords'),
	(24,'Weak Awareness'),
	(25,'Missing Configuration Standards'),
	(26,'Open Network Ports'),
	(27,'Reputational Issues'),
	(28,'Seismic Areas'),
	(29,'Prone to Natural Disasters Area'),
	(30,'Flood Prone Areas'),
	(31,'Other'),
	(32,'Unprotected Network'),
	(33,'Cabling Unsecured'),
	(34,'Weak Software Development Procedures');








INSERT INTO `workflows` (`id`, `model`, `name`, `notifications`, `parent_id`, `created`, `modified`) VALUES 
	(1,'SecurityIncident','Security Incidents',1,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(2,'BusinessUnit','Business Units',1,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(3,'Legal','Legals',1,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(4,'ThirdParty','Third Parties',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(5,'Process','Processes',0,2,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(6,'Asset','Assets',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(7,'AssetClassification','Asset Classifications',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(8,'AssetLabel','Asset Labeling',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(9,'RiskClassification','Risk Classifications',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(10,'RiskException','Risk Exceptions',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(11,'Risk','Risks',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(12,'ThirdPartyRisk','Third Party Risks',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(13,'BusinessContinuity','Business Continuities',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(14,'SecurityService','Security Services',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(15,'ServiceContract','Service Contracts',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(16,'ServiceClassification','Service Classifications',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(17,'BusinessContinuityPlan','Business Continuity Plans',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(18,'SecurityPolicy','Security Policies',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(19,'PolicyException','Policy Exceptions',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(20,'Project','Projects',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(22,'ProjectAchievement','Project Achievements',0,20,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(23,'ProjectExpense','Project Expenses',0,20,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(24,'SecurityServiceAudit','Security Service Audits',0,14,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(25,'SecurityServiceMaintenance','Security Service Maintenances',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(26,'CompliancePackageItem','Compliance Package Items',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(27,'DataAsset','Data Assets',0,6,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(28,'ComplianceManagement','Compliance Managements',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(29,'BusinessContinuityPlanAudit','Business Continuity Plan Audits',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(31,'BusinessContinuityTask','Business Continuity Tasks',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(32,'LdapConnector','LDAP Connectors',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(33,'SecurityPolicyReview','Security Policy Reviews',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(34,'RiskReview','Risk Reviews',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(35,'ThirdPartyRiskReview','ThirdPartyRisk Reviews',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(36,'BusinessContinuityReview','BusinessContinuity Reviews',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(37,'AssetReview','Asset Reviews',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(38,'SecurityIncidentStage','Security Incident Stage',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(39,'SecurityIncidentStagesSecurityIncident','Security Incident Stages Security Incident',0,39,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(41,'AwarenessProgram','Awareness Programs',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(42,'ProgramScope','Scopes',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(43,'ProgramIssue','Issues',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(44,'TeamRole','Team Roles',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(45,'Goal','Goals',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(46,'GoalAudit','Goal Audits',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00'),
	(47,'SecurityServiceIssue','Security Service Issues',0,NULL,'2015-12-19 00:00:00','2015-12-19 00:00:00');




















SET FOREIGN_KEY_CHECKS = @PREVIOUS_FOREIGN_KEY_CHECKS;


