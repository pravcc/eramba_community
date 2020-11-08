-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.12-log - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table e_merge_enterprise.acos
DROP TABLE IF EXISTS `acos`;
CREATE TABLE IF NOT EXISTS `acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5169 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.acos: ~505 rows (approximately)
/*!40000 ALTER TABLE `acos` DISABLE KEYS */;
INSERT INTO `acos` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
	(72, NULL, NULL, NULL, 'controllers', 1, 990),
	(73, 72, NULL, NULL, 'Legals', 2, 13),
	(74, 73, NULL, NULL, 'index', 3, 4),
	(75, 73, NULL, NULL, 'delete', 5, 6),
	(76, 73, NULL, NULL, 'add', 7, 8),
	(77, 73, NULL, NULL, 'edit', 9, 10),
	(80, 72, NULL, NULL, 'Pages', 14, 23),
	(81, 80, NULL, NULL, 'display', 15, 16),
	(84, 72, NULL, NULL, 'ThirdParties', 24, 35),
	(85, 84, NULL, NULL, 'index', 25, 26),
	(86, 84, NULL, NULL, 'delete', 27, 28),
	(87, 84, NULL, NULL, 'add', 29, 30),
	(88, 84, NULL, NULL, 'edit', 31, 32),
	(91, 72, NULL, NULL, 'Users', 36, 59),
	(93, 91, NULL, NULL, 'index', 37, 38),
	(94, 91, NULL, NULL, 'add', 39, 40),
	(95, 91, NULL, NULL, 'edit', 41, 42),
	(96, 91, NULL, NULL, 'delete', 43, 44),
	(97, 91, NULL, NULL, 'profile', 45, 46),
	(98, 91, NULL, NULL, 'resetpassword', 47, 48),
	(99, 91, NULL, NULL, 'useticket', 49, 50),
	(100, 91, NULL, NULL, 'login', 51, 52),
	(101, 91, NULL, NULL, 'logout', 53, 54),
	(143, 72, NULL, NULL, 'Groups', 60, 71),
	(145, 143, NULL, NULL, 'index', 61, 62),
	(146, 143, NULL, NULL, 'add', 63, 64),
	(147, 143, NULL, NULL, 'edit', 65, 66),
	(148, 143, NULL, NULL, 'delete', 67, 68),
	(150, 72, NULL, NULL, 'AssetClassifications', 72, 83),
	(151, 150, NULL, NULL, 'index', 73, 74),
	(152, 150, NULL, NULL, 'delete', 75, 76),
	(153, 150, NULL, NULL, 'add', 77, 78),
	(154, 150, NULL, NULL, 'edit', 79, 80),
	(157, 72, NULL, NULL, 'BusinessUnits', 84, 95),
	(158, 157, NULL, NULL, 'index', 85, 86),
	(159, 157, NULL, NULL, 'add', 87, 88),
	(160, 157, NULL, NULL, 'edit', 89, 90),
	(163, 72, NULL, NULL, 'Processes', 96, 107),
	(164, 163, NULL, NULL, 'delete', 97, 98),
	(165, 163, NULL, NULL, 'add', 99, 100),
	(166, 163, NULL, NULL, 'edit', 101, 102),
	(169, 72, NULL, NULL, 'AssetLabels', 108, 119),
	(170, 169, NULL, NULL, 'index', 109, 110),
	(171, 169, NULL, NULL, 'delete', 111, 112),
	(172, 169, NULL, NULL, 'add', 113, 114),
	(173, 169, NULL, NULL, 'edit', 115, 116),
	(175, 72, NULL, NULL, 'Assets', 120, 135),
	(176, 175, NULL, NULL, 'index', 121, 122),
	(177, 175, NULL, NULL, 'delete', 123, 124),
	(178, 175, NULL, NULL, 'add', 125, 126),
	(179, 175, NULL, NULL, 'edit', 127, 128),
	(181, 72, NULL, NULL, 'BusinessContinuities', 136, 153),
	(182, 181, NULL, NULL, 'index', 137, 138),
	(183, 181, NULL, NULL, 'delete', 139, 140),
	(184, 181, NULL, NULL, 'add', 141, 142),
	(185, 181, NULL, NULL, 'edit', 143, 144),
	(187, 72, NULL, NULL, 'BusinessContinuityPlans', 154, 181),
	(188, 187, NULL, NULL, 'index', 155, 156),
	(189, 187, NULL, NULL, 'add', 157, 158),
	(190, 187, NULL, NULL, 'edit', 159, 160),
	(192, 72, NULL, NULL, 'BusinessContinuityTasks', 182, 191),
	(193, 192, NULL, NULL, 'delete', 183, 184),
	(194, 192, NULL, NULL, 'add', 185, 186),
	(195, 192, NULL, NULL, 'edit', 187, 188),
	(197, 72, NULL, NULL, 'ComplianceExceptions', 192, 207),
	(198, 197, NULL, NULL, 'index', 193, 194),
	(199, 197, NULL, NULL, 'delete', 195, 196),
	(200, 197, NULL, NULL, 'add', 197, 198),
	(201, 197, NULL, NULL, 'edit', 199, 200),
	(203, 72, NULL, NULL, 'DataAssets', 208, 221),
	(204, 203, NULL, NULL, 'index', 209, 210),
	(206, 72, NULL, NULL, 'PolicyExceptions', 222, 235),
	(207, 206, NULL, NULL, 'index', 223, 224),
	(208, 206, NULL, NULL, 'delete', 225, 226),
	(209, 206, NULL, NULL, 'add', 227, 228),
	(210, 206, NULL, NULL, 'edit', 229, 230),
	(212, 72, NULL, NULL, 'Projects', 236, 249),
	(213, 212, NULL, NULL, 'index', 237, 238),
	(214, 212, NULL, NULL, 'delete', 239, 240),
	(215, 212, NULL, NULL, 'add', 241, 242),
	(216, 212, NULL, NULL, 'edit', 243, 244),
	(218, 72, NULL, NULL, 'RiskClassifications', 250, 261),
	(219, 218, NULL, NULL, 'index', 251, 252),
	(220, 218, NULL, NULL, 'delete', 253, 254),
	(221, 218, NULL, NULL, 'add', 255, 256),
	(222, 218, NULL, NULL, 'edit', 257, 258),
	(224, 72, NULL, NULL, 'RiskExceptions', 262, 277),
	(225, 224, NULL, NULL, 'index', 263, 264),
	(226, 224, NULL, NULL, 'delete', 265, 266),
	(227, 224, NULL, NULL, 'add', 267, 268),
	(228, 224, NULL, NULL, 'edit', 269, 270),
	(230, 72, NULL, NULL, 'Risks', 278, 297),
	(231, 230, NULL, NULL, 'index', 279, 280),
	(232, 230, NULL, NULL, 'delete', 281, 282),
	(233, 230, NULL, NULL, 'add', 283, 284),
	(234, 230, NULL, NULL, 'edit', 285, 286),
	(236, 72, NULL, NULL, 'SecurityIncidentClassifications', 298, 309),
	(237, 236, NULL, NULL, 'index', 299, 300),
	(238, 236, NULL, NULL, 'delete', 301, 302),
	(239, 236, NULL, NULL, 'add', 303, 304),
	(240, 236, NULL, NULL, 'edit', 305, 306),
	(242, 72, NULL, NULL, 'SecurityIncidents', 310, 325),
	(243, 242, NULL, NULL, 'index', 311, 312),
	(244, 242, NULL, NULL, 'delete', 313, 314),
	(245, 242, NULL, NULL, 'add', 315, 316),
	(246, 242, NULL, NULL, 'edit', 317, 318),
	(248, 72, NULL, NULL, 'SecurityServices', 326, 349),
	(249, 248, NULL, NULL, 'index', 327, 328),
	(250, 248, NULL, NULL, 'delete', 329, 330),
	(251, 248, NULL, NULL, 'add', 331, 332),
	(252, 248, NULL, NULL, 'edit', 333, 334),
	(254, 72, NULL, NULL, 'ServiceClassifications', 350, 361),
	(255, 254, NULL, NULL, 'index', 351, 352),
	(256, 254, NULL, NULL, 'delete', 353, 354),
	(257, 254, NULL, NULL, 'add', 355, 356),
	(258, 254, NULL, NULL, 'edit', 357, 358),
	(260, 72, NULL, NULL, 'ServiceContracts', 362, 373),
	(261, 260, NULL, NULL, 'index', 363, 364),
	(262, 260, NULL, NULL, 'delete', 365, 366),
	(263, 260, NULL, NULL, 'add', 367, 368),
	(264, 260, NULL, NULL, 'edit', 369, 370),
	(266, 72, NULL, NULL, 'ThirdPartyRisks', 374, 391),
	(267, 266, NULL, NULL, 'index', 375, 376),
	(268, 266, NULL, NULL, 'delete', 377, 378),
	(269, 266, NULL, NULL, 'add', 379, 380),
	(270, 266, NULL, NULL, 'edit', 381, 382),
	(272, 157, NULL, NULL, 'delete', 91, 92),
	(297, 285, NULL, NULL, 'admin_get_role_controller_permission', 0, 0),
	(298, 72, NULL, NULL, 'CompliancePackages', 392, 405),
	(299, 298, NULL, NULL, 'index', 393, 394),
	(300, 298, NULL, NULL, 'delete', 395, 396),
	(301, 298, NULL, NULL, 'add', 397, 398),
	(302, 298, NULL, NULL, 'edit', 399, 400),
	(337, 336, NULL, NULL, 'history_state', 0, 0),
	(365, 350, NULL, NULL, 'admin_get_user_controller_permission', 0, 0),
	(366, 72, NULL, NULL, 'CompliancePackageItems', 406, 415),
	(367, 366, NULL, NULL, 'delete', 407, 408),
	(368, 366, NULL, NULL, 'add', 409, 410),
	(369, 366, NULL, NULL, 'edit', 411, 412),
	(465, 72, NULL, NULL, 'DebugKit', 0, 0),
	(466, 72, NULL, NULL, 'ComplianceManagements', 418, 433),
	(467, 466, NULL, NULL, 'index', 419, 420),
	(469, 466, NULL, NULL, 'add', 421, 422),
	(470, 466, NULL, NULL, 'edit', 423, 424),
	(490, 484, NULL, NULL, 'admin_role_permissions', 0, 0),
	(524, 523, NULL, NULL, 'history_state', 0, 0),
	(554, 537, NULL, NULL, 'admin_deny_user_permission', 0, 0),
	(584, 567, NULL, NULL, 'admin_deny_user_permission', 0, 0),
	(614, 597, NULL, NULL, 'admin_deny_user_permission', 0, 0),
	(635, 627, NULL, NULL, 'admin_empty_permissions', 0, 0),
	(697, 72, NULL, NULL, 'ComplianceAudits', 434, 451),
	(698, 697, NULL, NULL, 'index', 435, 436),
	(699, 697, NULL, NULL, 'delete', 437, 438),
	(700, 697, NULL, NULL, 'add', 439, 440),
	(701, 697, NULL, NULL, 'edit', 441, 442),
	(703, 466, NULL, NULL, 'analyze', 425, 426),
	(812, 72, NULL, NULL, 'ComplianceFindings', 452, 463),
	(813, 812, NULL, NULL, 'index', 453, 454),
	(814, 812, NULL, NULL, 'add', 455, 456),
	(815, 812, NULL, NULL, 'edit', 457, 458),
	(817, 72, NULL, NULL, 'ProjectAchievements', 464, 475),
	(818, 817, NULL, NULL, 'index', 465, 466),
	(820, 817, NULL, NULL, 'add', 467, 468),
	(821, 817, NULL, NULL, 'edit', 469, 470),
	(931, 72, NULL, NULL, 'ProjectExpenses', 476, 487),
	(932, 931, NULL, NULL, 'index', 477, 478),
	(933, 931, NULL, NULL, 'delete', 479, 480),
	(934, 931, NULL, NULL, 'add', 481, 482),
	(935, 931, NULL, NULL, 'edit', 483, 484),
	(1009, 203, NULL, NULL, 'add', 211, 212),
	(1046, 203, NULL, NULL, 'edit', 213, 214),
	(1083, 72, NULL, NULL, 'SecurityPolicies', 488, 509),
	(1084, 1083, NULL, NULL, 'index', 489, 490),
	(1085, 1083, NULL, NULL, 'delete', 491, 492),
	(1086, 1083, NULL, NULL, 'add', 493, 494),
	(1087, 1083, NULL, NULL, 'edit', 495, 496),
	(1125, 248, NULL, NULL, 'auditCalendarFormEntry', 335, 336),
	(1162, 72, NULL, NULL, 'SecurityServiceAudits', 510, 519),
	(1163, 1162, NULL, NULL, 'index', 511, 512),
	(1164, 1162, NULL, NULL, 'delete', 513, 514),
	(1166, 1162, NULL, NULL, 'edit', 515, 516),
	(1204, 187, NULL, NULL, 'auditCalendarFormEntry', 161, 162),
	(1241, 697, NULL, NULL, 'analyze', 443, 444),
	(1314, 812, NULL, NULL, 'delete', 459, 460),
	(1387, 72, NULL, NULL, 'BusinessContinuityPlanAudits', 520, 529),
	(1388, 1387, NULL, NULL, 'index', 521, 522),
	(1389, 1387, NULL, NULL, 'delete', 523, 524),
	(1390, 1387, NULL, NULL, 'edit', 525, 526),
	(1428, 72, NULL, NULL, 'SecurityServiceMaintenances', 530, 539),
	(1429, 1428, NULL, NULL, 'index', 531, 532),
	(1430, 1428, NULL, NULL, 'delete', 533, 534),
	(1431, 1428, NULL, NULL, 'edit', 535, 536),
	(1570, 1553, NULL, NULL, 'admin_deny_user_permission', 0, 0),
	(1710, 72, NULL, NULL, 'Reports', 540, 545),
	(1859, 72, NULL, NULL, 'Attachments', 546, 565),
	(1860, 1859, NULL, NULL, 'index', 547, 548),
	(1861, 1859, NULL, NULL, 'delete', 549, 550),
	(1862, 1859, NULL, NULL, 'add', 551, 552),
	(2339, 72, NULL, NULL, 'SystemRecords', 566, 573),
	(2340, 2339, NULL, NULL, 'index', 567, 568),
	(2405, 203, NULL, NULL, 'delete', 215, 216),
	(2504, 80, NULL, NULL, 'dashboard', 17, 18),
	(2537, 224, NULL, NULL, 'export', 271, 272),
	(2602, 242, NULL, NULL, 'export', 319, 320),
	(2667, 1083, NULL, NULL, 'export', 497, 498),
	(2700, 206, NULL, NULL, 'export', 231, 232),
	(2733, 197, NULL, NULL, 'export', 201, 202),
	(2766, 248, NULL, NULL, 'export', 337, 338),
	(2831, 203, NULL, NULL, 'export', 217, 218),
	(2864, 266, NULL, NULL, 'export', 383, 384),
	(2897, 230, NULL, NULL, 'export', 287, 288),
	(2930, 175, NULL, NULL, 'export', 129, 130),
	(2963, 298, NULL, NULL, 'import', 401, 402),
	(3062, 2339, NULL, NULL, 'export', 569, 570),
	(3127, 72, NULL, NULL, 'BackupRestore', 574, 581),
	(3128, 3127, NULL, NULL, 'index', 575, 576),
	(3161, 3127, NULL, NULL, 'getBackup', 577, 578),
	(3355, 466, NULL, NULL, 'export', 427, 428),
	(3389, 72, NULL, NULL, 'Awareness', 582, 607),
	(3390, 3389, NULL, NULL, 'index', 583, 584),
	(3391, 3389, NULL, NULL, 'video', 585, 586),
	(3392, 3389, NULL, NULL, 'questionnaire', 587, 588),
	(3393, 3389, NULL, NULL, 'results', 589, 590),
	(3394, 3389, NULL, NULL, 'login', 591, 592),
	(3395, 3389, NULL, NULL, 'logout', 593, 594),
	(3397, 3389, NULL, NULL, 'exportUserTrainings', 595, 596),
	(3398, 3389, NULL, NULL, 'exportReminders', 597, 598),
	(3399, 3389, NULL, NULL, 'exportIgnoringUsers', 599, 600),
	(3400, 187, NULL, NULL, 'delete', 163, 164),
	(3401, 187, NULL, NULL, 'acknowledge', 165, 166),
	(3402, 187, NULL, NULL, 'acknowledgeItem', 167, 168),
	(3403, 187, NULL, NULL, 'export', 169, 170),
	(3405, 817, NULL, NULL, 'delete', 471, 472),
	(3406, 1710, NULL, NULL, 'awareness', 541, 542),
	(3439, 1859, NULL, NULL, 'download', 553, 554),
	(3440, 1859, NULL, NULL, 'sendAuditWarningEmails', 555, 556),
	(3442, 181, NULL, NULL, 'calculateRiskScoreAjax', 145, 146),
	(3443, 72, NULL, NULL, 'BusinessContinuityPlanAuditImprovements', 608, 617),
	(3444, 3443, NULL, NULL, 'delete', 609, 610),
	(3445, 3443, NULL, NULL, 'add', 611, 612),
	(3446, 3443, NULL, NULL, 'edit', 613, 614),
	(3447, 187, NULL, NULL, 'deleteProductionJoins', 171, 172),
	(3448, 187, NULL, NULL, 'exportAudits', 173, 174),
	(3449, 187, NULL, NULL, 'exportTask', 175, 176),
	(3450, 72, NULL, NULL, 'Comments', 618, 635),
	(3451, 3450, NULL, NULL, 'index', 619, 620),
	(3452, 3450, NULL, NULL, 'delete', 621, 622),
	(3453, 3450, NULL, NULL, 'add', 623, 624),
	(3454, 3450, NULL, NULL, 'edit', 625, 626),
	(3455, 3450, NULL, NULL, 'sendAuditWarningEmails', 627, 628),
	(3456, 72, NULL, NULL, 'ComplianceAuditSettings', 636, 643),
	(3457, 3456, NULL, NULL, 'setup', 637, 638),
	(3458, 3456, NULL, NULL, 'sendNotifications', 639, 640),
	(3459, 697, NULL, NULL, 'analyzeAuditee', 445, 446),
	(3460, 72, NULL, NULL, 'Cron', 644, 659),
	(3462, 72, NULL, NULL, 'NotificationSystem', 660, 673),
	(3463, 3462, NULL, NULL, 'attach', 661, 662),
	(3464, 3462, NULL, NULL, 'delete', 663, 664),
	(3465, 3462, NULL, NULL, 'addNotification', 665, 666),
	(3466, 3462, NULL, NULL, 'feedback', 667, 668),
	(3467, 3462, NULL, NULL, 'addFeedbackAttachment', 669, 670),
	(3469, 72, NULL, NULL, 'Notifications', 674, 679),
	(3470, 3469, NULL, NULL, 'setNotificationsAsSeen', 675, 676),
	(3471, 230, NULL, NULL, 'calculateRiskScoreAjax', 289, 290),
	(3472, 72, NULL, NULL, 'Scopes', 680, 691),
	(3473, 3472, NULL, NULL, 'index', 681, 682),
	(3474, 3472, NULL, NULL, 'delete', 683, 684),
	(3475, 3472, NULL, NULL, 'add', 685, 686),
	(3476, 3472, NULL, NULL, 'edit', 687, 688),
	(3477, 242, NULL, NULL, 'deleteold', 321, 322),
	(3478, 72, NULL, NULL, 'SecurityServiceAuditImprovements', 692, 701),
	(3479, 3478, NULL, NULL, 'delete', 693, 694),
	(3480, 3478, NULL, NULL, 'add', 695, 696),
	(3481, 3478, NULL, NULL, 'edit', 697, 698),
	(3482, 248, NULL, NULL, 'deleteProductionJoins', 339, 340),
	(3483, 248, NULL, NULL, 'exportAudits', 341, 342),
	(3484, 248, NULL, NULL, 'exportMaintenances', 343, 344),
	(3485, 266, NULL, NULL, 'calculateRiskScoreAjax', 385, 386),
	(3486, 72, NULL, NULL, 'Workflows', 702, 727),
	(3487, 3486, NULL, NULL, 'index', 703, 704),
	(3488, 3486, NULL, NULL, 'edit', 705, 706),
	(3489, 3486, NULL, NULL, 'editWarning', 707, 708),
	(3490, 3486, NULL, NULL, 'editNoApprover', 709, 710),
	(3491, 3486, NULL, NULL, 'acknowledge', 711, 712),
	(3492, 3486, NULL, NULL, 'deleteWarning', 713, 714),
	(3493, 3486, NULL, NULL, 'deleteNoApprover', 715, 716),
	(3494, 3486, NULL, NULL, 'requestValidation', 717, 718),
	(3495, 3486, NULL, NULL, 'validateItem', 719, 720),
	(3496, 3486, NULL, NULL, 'requestApproval', 721, 722),
	(3497, 3486, NULL, NULL, 'approveItem', 723, 724),
	(3562, 3460, NULL, NULL, 'daily', 645, 646),
	(3723, 163, NULL, NULL, 'index', 103, 104),
	(3756, 72, NULL, NULL, 'ComplianceReports', 728, 735),
	(3757, 3756, NULL, NULL, 'index', 729, 730),
	(3758, 3756, NULL, NULL, 'awareness', 731, 732),
	(3760, 72, NULL, NULL, 'RiskReports', 736, 743),
	(3761, 3760, NULL, NULL, 'index', 737, 738),
	(3762, 3760, NULL, NULL, 'awareness', 739, 740),
	(3764, 72, NULL, NULL, 'SecurityControlReports', 744, 751),
	(3765, 3764, NULL, NULL, 'index', 745, 746),
	(3766, 3764, NULL, NULL, 'awareness', 747, 748),
	(3768, 72, NULL, NULL, 'SecurityOperationReports', 752, 759),
	(3769, 3768, NULL, NULL, 'index', 753, 754),
	(3770, 3768, NULL, NULL, 'awareness', 755, 756),
	(3804, 150, NULL, NULL, 'cancelAction', 81, 82),
	(3805, 169, NULL, NULL, 'cancelAction', 117, 118),
	(3806, 175, NULL, NULL, 'cancelAction', 131, 132),
	(3807, 1859, NULL, NULL, 'cancelAction', 557, 558),
	(3808, 3389, NULL, NULL, 'cancelAction', 601, 602),
	(3809, 3127, NULL, NULL, 'cancelAction', 579, 580),
	(3810, 181, NULL, NULL, 'cancelAction', 147, 148),
	(3811, 3443, NULL, NULL, 'cancelAction', 615, 616),
	(3812, 1387, NULL, NULL, 'cancelAction', 527, 528),
	(3813, 187, NULL, NULL, 'cancelAction', 177, 178),
	(3814, 192, NULL, NULL, 'cancelAction', 189, 190),
	(3815, 157, NULL, NULL, 'cancelAction', 93, 94),
	(3816, 3450, NULL, NULL, 'cancelAction', 629, 630),
	(3817, 3456, NULL, NULL, 'cancelAction', 641, 642),
	(3818, 697, NULL, NULL, 'cancelAction', 447, 448),
	(3819, 197, NULL, NULL, 'cancelAction', 203, 204),
	(3820, 812, NULL, NULL, 'cancelAction', 461, 462),
	(3821, 466, NULL, NULL, 'cancelAction', 429, 430),
	(3822, 366, NULL, NULL, 'cancelAction', 413, 414),
	(3823, 298, NULL, NULL, 'cancelAction', 403, 404),
	(3824, 3756, NULL, NULL, 'cancelAction', 733, 734),
	(3825, 3460, NULL, NULL, 'cancelAction', 647, 648),
	(3826, 203, NULL, NULL, 'cancelAction', 219, 220),
	(3827, 143, NULL, NULL, 'cancelAction', 69, 70),
	(3828, 73, NULL, NULL, 'cancelAction', 11, 12),
	(3829, 3462, NULL, NULL, 'cancelAction', 671, 672),
	(3830, 3469, NULL, NULL, 'cancelAction', 677, 678),
	(3831, 80, NULL, NULL, 'about', 19, 20),
	(3832, 80, NULL, NULL, 'cancelAction', 21, 22),
	(3833, 206, NULL, NULL, 'cancelAction', 233, 234),
	(3834, 163, NULL, NULL, 'cancelAction', 105, 106),
	(3835, 817, NULL, NULL, 'cancelAction', 473, 474),
	(3836, 931, NULL, NULL, 'cancelAction', 485, 486),
	(3837, 212, NULL, NULL, 'cancelAction', 245, 246),
	(3838, 1710, NULL, NULL, 'cancelAction', 543, 544),
	(3839, 218, NULL, NULL, 'cancelAction', 259, 260),
	(3840, 224, NULL, NULL, 'cancelAction', 273, 274),
	(3841, 3760, NULL, NULL, 'cancelAction', 741, 742),
	(3842, 230, NULL, NULL, 'cancelAction', 291, 292),
	(3843, 3472, NULL, NULL, 'cancelAction', 689, 690),
	(3844, 3764, NULL, NULL, 'cancelAction', 749, 750),
	(3845, 236, NULL, NULL, 'cancelAction', 307, 308),
	(3846, 242, NULL, NULL, 'cancelAction', 323, 324),
	(3847, 3768, NULL, NULL, 'cancelAction', 757, 758),
	(3848, 1083, NULL, NULL, 'cancelAction', 499, 500),
	(3849, 3478, NULL, NULL, 'cancelAction', 699, 700),
	(3850, 1162, NULL, NULL, 'cancelAction', 517, 518),
	(3851, 1428, NULL, NULL, 'cancelAction', 537, 538),
	(3852, 248, NULL, NULL, 'cancelAction', 345, 346),
	(3853, 254, NULL, NULL, 'cancelAction', 359, 360),
	(3854, 260, NULL, NULL, 'cancelAction', 371, 372),
	(3855, 2339, NULL, NULL, 'cancelAction', 571, 572),
	(3856, 84, NULL, NULL, 'cancelAction', 33, 34),
	(3857, 266, NULL, NULL, 'cancelAction', 387, 388),
	(3858, 91, NULL, NULL, 'cancelAction', 55, 56),
	(3859, 3486, NULL, NULL, 'cancelAction', 725, 726),
	(3932, 3460, NULL, NULL, 'yearly', 649, 650),
	(3933, 3460, NULL, NULL, 'checkPolicyException', 651, 652),
	(3934, 3460, NULL, NULL, 'updateAudits', 653, 654),
	(3935, 3460, NULL, NULL, 'getIndexUrlFromComponent', 655, 656),
	(3936, 3460, NULL, NULL, 'initEmailFromComponent', 657, 658),
	(3973, 72, NULL, NULL, 'Settings', 760, 781),
	(3974, 3973, NULL, NULL, 'index', 761, 762),
	(3975, 3973, NULL, NULL, 'edit', 763, 764),
	(3976, 3973, NULL, NULL, 'logs', 765, 766),
	(3977, 3973, NULL, NULL, 'deleteLogs', 767, 768),
	(3978, 3973, NULL, NULL, 'cancelAction', 769, 770),
	(4051, 72, NULL, NULL, 'LdapConnectors', 782, 797),
	(4052, 4051, NULL, NULL, 'index', 783, 784),
	(4053, 4051, NULL, NULL, 'delete', 785, 786),
	(4054, 4051, NULL, NULL, 'add', 787, 788),
	(4055, 4051, NULL, NULL, 'edit', 789, 790),
	(4056, 4051, NULL, NULL, 'authentication', 791, 792),
	(4057, 4051, NULL, NULL, 'testLdap', 793, 794),
	(4058, 4051, NULL, NULL, 'cancelAction', 795, 796),
	(4059, 212, NULL, NULL, 'export', 247, 248),
	(4060, 3973, NULL, NULL, 'testMailConnection', 771, 772),
	(4061, 3973, NULL, NULL, 'resetDashboards', 773, 774),
	(4106, 72, NULL, NULL, 'Threats', 798, 809),
	(4107, 4106, NULL, NULL, 'index', 799, 800),
	(4108, 4106, NULL, NULL, 'liveEdit', 801, 802),
	(4109, 4106, NULL, NULL, 'add', 803, 804),
	(4110, 4106, NULL, NULL, 'delete', 805, 806),
	(4111, 4106, NULL, NULL, 'cancelAction', 807, 808),
	(4112, 72, NULL, NULL, 'Vulnerabilities', 810, 821),
	(4113, 4112, NULL, NULL, 'index', 811, 812),
	(4114, 4112, NULL, NULL, 'liveEdit', 813, 814),
	(4115, 4112, NULL, NULL, 'add', 815, 816),
	(4116, 4112, NULL, NULL, 'delete', 817, 818),
	(4117, 4112, NULL, NULL, 'cancelAction', 819, 820),
	(4197, 72, NULL, NULL, 'Reviews', 822, 831),
	(4198, 4197, NULL, NULL, 'index', 823, 824),
	(4199, 4197, NULL, NULL, 'edit', 825, 826),
	(4200, 4197, NULL, NULL, 'delete', 827, 828),
	(4201, 4197, NULL, NULL, 'cancelAction', 829, 830),
	(4424, 697, NULL, NULL, 'export', 449, 450),
	(4883, 72, NULL, NULL, 'AssetMediaTypes', 832, 843),
	(4884, 4883, NULL, NULL, 'index', 833, 834),
	(4885, 4883, NULL, NULL, 'liveEdit', 835, 836),
	(4886, 4883, NULL, NULL, 'add', 837, 838),
	(4887, 4883, NULL, NULL, 'delete', 839, 840),
	(4888, 4883, NULL, NULL, 'cancelAction', 841, 842),
	(4890, 72, NULL, NULL, 'Policy', 844, 863),
	(4891, 4890, NULL, NULL, 'login', 845, 846),
	(4892, 4890, NULL, NULL, 'guestLogin', 847, 848),
	(4893, 4890, NULL, NULL, 'logout', 849, 850),
	(4894, 4890, NULL, NULL, 'index', 851, 852),
	(4895, 4890, NULL, NULL, 'isGuest', 853, 854),
	(4896, 4890, NULL, NULL, 'document', 855, 856),
	(4897, 4890, NULL, NULL, 'documentDirect', 857, 858),
	(4898, 4890, NULL, NULL, 'documentPdf', 859, 860),
	(4899, 4890, NULL, NULL, 'cancelAction', 861, 862),
	(4900, 72, NULL, NULL, 'SecurityIncidentStages', 864, 877),
	(4901, 4900, NULL, NULL, 'index', 865, 866),
	(4902, 4900, NULL, NULL, 'add', 867, 868),
	(4903, 4900, NULL, NULL, 'edit', 869, 870),
	(4904, 4900, NULL, NULL, 'delete', 871, 872),
	(4905, 4900, NULL, NULL, 'pocessStage', 873, 874),
	(4906, 4900, NULL, NULL, 'cancelAction', 875, 876),
	(4907, 1083, NULL, NULL, 'getDirectLink', 501, 502),
	(4908, 1083, NULL, NULL, 'duplicate', 503, 504),
	(4909, 1083, NULL, NULL, 'ldapGroups', 505, 506),
	(4910, 1083, NULL, NULL, 'sendNotifications', 507, 508),
	(4911, 72, NULL, NULL, 'SecurityPolicyReviews', 878, 887),
	(4912, 4911, NULL, NULL, 'index', 879, 880),
	(4913, 4911, NULL, NULL, 'edit', 881, 882),
	(4914, 4911, NULL, NULL, 'delete', 883, 884),
	(4915, 4911, NULL, NULL, 'cancelAction', 885, 886),
	(4916, 3973, NULL, NULL, 'customLogo', 775, 776),
	(4917, 3973, NULL, NULL, 'deleteCache', 777, 778),
	(4954, 175, NULL, NULL, 'exportPdf', 133, 134),
	(4955, 1859, NULL, NULL, 'deleteAjax', 559, 560),
	(4956, 1859, NULL, NULL, 'addAjax', 561, 562),
	(4957, 1859, NULL, NULL, 'getList', 563, 564),
	(4958, 3389, NULL, NULL, 'training', 603, 604),
	(4959, 3389, NULL, NULL, 'demo', 605, 606),
	(4960, 72, NULL, NULL, 'AwarenessPrograms', 888, 917),
	(4961, 4960, NULL, NULL, 'index', 889, 890),
	(4962, 4960, NULL, NULL, 'delete', 891, 892),
	(4963, 4960, NULL, NULL, 'add', 893, 894),
	(4964, 4960, NULL, NULL, 'edit', 895, 896),
	(4965, 4960, NULL, NULL, 'ldapGroups', 897, 898),
	(4966, 4960, NULL, NULL, 'ldapIgnoredUsers', 899, 900),
	(4967, 4960, NULL, NULL, 'deleteVideo', 901, 902),
	(4968, 4960, NULL, NULL, 'deleteQuestionnaire', 903, 904),
	(4969, 4960, NULL, NULL, 'start', 905, 906),
	(4970, 4960, NULL, NULL, 'stop', 907, 908),
	(4971, 4960, NULL, NULL, 'demo', 909, 910),
	(4972, 4960, NULL, NULL, 'clean', 911, 912),
	(4973, 4960, NULL, NULL, 'initEmailFromComponent', 913, 914),
	(4974, 4960, NULL, NULL, 'cancelAction', 915, 916),
	(4975, 181, NULL, NULL, 'exportPdf', 149, 150),
	(4976, 187, NULL, NULL, 'exportPdf', 179, 180),
	(4977, 3450, NULL, NULL, 'addAjax', 631, 632),
	(4978, 3450, NULL, NULL, 'listComments', 633, 634),
	(4979, 197, NULL, NULL, 'exportPdf', 205, 206),
	(4980, 224, NULL, NULL, 'exportPdf', 275, 276),
	(4981, 230, NULL, NULL, 'exportPdf', 293, 294),
	(4982, 248, NULL, NULL, 'exportPdf', 347, 348),
	(4983, 266, NULL, NULL, 'exportPdf', 389, 390),
	(5056, 91, NULL, NULL, 'chooseLdapUser', 57, 58),
	(5093, 181, NULL, NULL, 'getThreatsVulnerabilities', 151, 152),
	(5094, 466, NULL, NULL, 'exportPdf', 431, 432),
	(5095, 230, NULL, NULL, 'getThreatsVulnerabilities', 295, 296),
	(5132, 3973, NULL, NULL, 'resetDatabase', 779, 780),
	(5133, 72, NULL, NULL, 'Acl', 918, 979),
	(5134, 5133, NULL, NULL, 'Acl', 919, 926),
	(5135, 5134, NULL, NULL, 'index', 920, 921),
	(5136, 5134, NULL, NULL, 'admin_index', 922, 923),
	(5137, 5134, NULL, NULL, 'cancelAction', 924, 925),
	(5138, 5133, NULL, NULL, 'Acos', 927, 940),
	(5139, 5138, NULL, NULL, 'admin_index', 928, 929),
	(5140, 5138, NULL, NULL, 'admin_empty_acos', 930, 931),
	(5141, 5138, NULL, NULL, 'admin_build_acl', 932, 933),
	(5142, 5138, NULL, NULL, 'admin_prune_acos', 934, 935),
	(5143, 5138, NULL, NULL, 'admin_synchronize', 936, 937),
	(5144, 5138, NULL, NULL, 'cancelAction', 938, 939),
	(5145, 5133, NULL, NULL, 'Aros', 941, 978),
	(5146, 5145, NULL, NULL, 'admin_index', 942, 943),
	(5147, 5145, NULL, NULL, 'admin_check', 944, 945),
	(5148, 5145, NULL, NULL, 'admin_users', 946, 947),
	(5149, 5145, NULL, NULL, 'admin_update_user_role', 948, 949),
	(5150, 5145, NULL, NULL, 'admin_ajax_role_permissions', 950, 951),
	(5151, 5145, NULL, NULL, 'admin_role_permissions', 952, 953),
	(5152, 5145, NULL, NULL, 'admin_user_permissions', 954, 955),
	(5153, 5145, NULL, NULL, 'admin_empty_permissions', 956, 957),
	(5154, 5145, NULL, NULL, 'admin_clear_user_specific_permissions', 958, 959),
	(5155, 5145, NULL, NULL, 'admin_grant_all_controllers', 960, 961),
	(5156, 5145, NULL, NULL, 'admin_deny_all_controllers', 962, 963),
	(5157, 5145, NULL, NULL, 'admin_get_role_controller_permission', 964, 965),
	(5158, 5145, NULL, NULL, 'admin_grant_role_permission', 966, 967),
	(5159, 5145, NULL, NULL, 'admin_deny_role_permission', 968, 969),
	(5160, 5145, NULL, NULL, 'admin_get_user_controller_permission', 970, 971),
	(5161, 5145, NULL, NULL, 'admin_grant_user_permission', 972, 973),
	(5162, 5145, NULL, NULL, 'admin_deny_user_permission', 974, 975),
	(5163, 5145, NULL, NULL, 'cancelAction', 976, 977),
	(5164, 72, NULL, NULL, 'DebugKit', 980, 989),
	(5165, 5164, NULL, NULL, 'ToolbarAccess', 981, 988),
	(5166, 5165, NULL, NULL, 'history_state', 982, 983),
	(5167, 5165, NULL, NULL, 'sql_explain', 984, 985),
	(5168, 5165, NULL, NULL, 'cancelAction', 986, 987);
/*!40000 ALTER TABLE `acos` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.aros
DROP TABLE IF EXISTS `aros`;
CREATE TABLE IF NOT EXISTS `aros` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `foreign_key` int(10) DEFAULT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `lft` int(10) DEFAULT NULL,
  `rght` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.aros: ~10 rows (approximately)
/*!40000 ALTER TABLE `aros` DISABLE KEYS */;
INSERT INTO `aros` (`id`, `parent_id`, `model`, `foreign_key`, `alias`, `lft`, `rght`) VALUES
	(11, NULL, 'Group', 10, NULL, 1, 8),
	(12, NULL, 'Group', 11, NULL, 9, 12),
	(13, 11, 'User', 1, NULL, 2, 3),
	(14, 12, 'User', 2, NULL, 10, 11),
	(15, NULL, 'User', 3, NULL, 13, 14),
	(16, NULL, 'User', 4, NULL, 15, 16),
	(17, NULL, 'User', 5, NULL, 17, 18),
	(18, NULL, 'User', 6, NULL, 19, 20),
	(19, 11, 'User', 9, NULL, 4, 5),
	(20, 11, 'User', 10, NULL, 6, 7);
/*!40000 ALTER TABLE `aros` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.aros_acos
DROP TABLE IF EXISTS `aros_acos`;
CREATE TABLE IF NOT EXISTS `aros_acos` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `aro_id` int(10) NOT NULL,
  `aco_id` int(10) NOT NULL,
  `_create` varchar(2) NOT NULL DEFAULT '0',
  `_read` varchar(2) NOT NULL DEFAULT '0',
  `_update` varchar(2) NOT NULL DEFAULT '0',
  `_delete` varchar(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `ARO_ACO_KEY` (`aro_id`,`aco_id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.aros_acos: ~16 rows (approximately)
/*!40000 ALTER TABLE `aros_acos` DISABLE KEYS */;
INSERT INTO `aros_acos` (`id`, `aro_id`, `aco_id`, `_create`, `_read`, `_update`, `_delete`) VALUES
	(15, 11, 72, '1', '1', '1', '1'),
	(17, 12, 162, '1', '1', '1', '1'),
	(51, 12, 160, '1', '1', '1', '1'),
	(54, 12, 158, '1', '1', '1', '1'),
	(55, 12, 159, '1', '1', '1', '1'),
	(56, 12, 99, '1', '1', '1', '1'),
	(57, 12, 98, '1', '1', '1', '1'),
	(58, 12, 97, '1', '1', '1', '1'),
	(59, 12, 101, '1', '1', '1', '1'),
	(60, 12, 100, '1', '1', '1', '1'),
	(66, 12, 152, '1', '1', '1', '1'),
	(67, 12, 156, '1', '1', '1', '1'),
	(68, 12, 153, '1', '1', '1', '1'),
	(71, 12, 154, '1', '1', '1', '1'),
	(72, 12, 151, '1', '1', '1', '1'),
	(75, 12, 172, '1', '1', '1', '1');
/*!40000 ALTER TABLE `aros_acos` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.assets
DROP TABLE IF EXISTS `assets`;
CREATE TABLE IF NOT EXISTS `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `asset_label_id` int(11) DEFAULT NULL,
  `asset_media_type_id` int(11) DEFAULT NULL,
  `asset_owner_id` int(11) DEFAULT NULL,
  `asset_guardian_id` int(11) DEFAULT NULL,
  `asset_user_id` int(11) DEFAULT NULL,
  `review` date NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.assets: ~0 rows (approximately)
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.assets_business_units
DROP TABLE IF EXISTS `assets_business_units`;
CREATE TABLE IF NOT EXISTS `assets_business_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `business_unit_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `business_unit_id` (`business_unit_id`),
  CONSTRAINT `assets_business_units_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_business_units_ibfk_2` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.assets_business_units: ~0 rows (approximately)
/*!40000 ALTER TABLE `assets_business_units` DISABLE KEYS */;
/*!40000 ALTER TABLE `assets_business_units` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.assets_legals
DROP TABLE IF EXISTS `assets_legals`;
CREATE TABLE IF NOT EXISTS `assets_legals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `legal_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `legal_id` (`legal_id`),
  CONSTRAINT `assets_legals_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_legals_ibfk_2` FOREIGN KEY (`legal_id`) REFERENCES `legals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.assets_legals: ~0 rows (approximately)
/*!40000 ALTER TABLE `assets_legals` DISABLE KEYS */;
/*!40000 ALTER TABLE `assets_legals` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.assets_risks
DROP TABLE IF EXISTS `assets_risks`;
CREATE TABLE IF NOT EXISTS `assets_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `risk_id` (`risk_id`),
  CONSTRAINT `assets_risks_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_risks_ibfk_2` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.assets_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `assets_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `assets_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.assets_security_incidents
DROP TABLE IF EXISTS `assets_security_incidents`;
CREATE TABLE IF NOT EXISTS `assets_security_incidents` (
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

-- Dumping data for table e_merge_enterprise.assets_security_incidents: ~0 rows (approximately)
/*!40000 ALTER TABLE `assets_security_incidents` DISABLE KEYS */;
/*!40000 ALTER TABLE `assets_security_incidents` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.assets_third_party_risks
DROP TABLE IF EXISTS `assets_third_party_risks`;
CREATE TABLE IF NOT EXISTS `assets_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  CONSTRAINT `assets_third_party_risks_ibfk_1` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_third_party_risks_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.assets_third_party_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `assets_third_party_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `assets_third_party_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.asset_classifications
DROP TABLE IF EXISTS `asset_classifications`;
CREATE TABLE IF NOT EXISTS `asset_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `criteria` text NOT NULL,
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

-- Dumping data for table e_merge_enterprise.asset_classifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `asset_classifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_classifications` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.asset_classifications_assets
DROP TABLE IF EXISTS `asset_classifications_assets`;
CREATE TABLE IF NOT EXISTS `asset_classifications_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_classification_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_classification_id` (`asset_classification_id`),
  KEY `asset_id` (`asset_id`),
  CONSTRAINT `asset_classifications_assets_ibfk_1` FOREIGN KEY (`asset_classification_id`) REFERENCES `asset_classifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_classifications_assets_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.asset_classifications_assets: ~0 rows (approximately)
/*!40000 ALTER TABLE `asset_classifications_assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_classifications_assets` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.asset_classification_types
DROP TABLE IF EXISTS `asset_classification_types`;
CREATE TABLE IF NOT EXISTS `asset_classification_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `asset_classification_count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.asset_classification_types: ~0 rows (approximately)
/*!40000 ALTER TABLE `asset_classification_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_classification_types` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.asset_labels
DROP TABLE IF EXISTS `asset_labels`;
CREATE TABLE IF NOT EXISTS `asset_labels` (
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

-- Dumping data for table e_merge_enterprise.asset_labels: ~0 rows (approximately)
/*!40000 ALTER TABLE `asset_labels` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_labels` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.asset_media_types
DROP TABLE IF EXISTS `asset_media_types`;
CREATE TABLE IF NOT EXISTS `asset_media_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `editable` int(11) DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.asset_media_types: ~8 rows (approximately)
/*!40000 ALTER TABLE `asset_media_types` DISABLE KEYS */;
INSERT INTO `asset_media_types` (`id`, `name`, `editable`, `created`, `modified`) VALUES
	(1, 'Data Asset', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(2, 'Facilities', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(3, 'People', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(4, 'Hardware', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(5, 'Software', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(6, 'IT Service', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(7, 'Network', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(8, 'Financial', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `asset_media_types` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.attachments
DROP TABLE IF EXISTS `attachments`;
CREATE TABLE IF NOT EXISTS `attachments` (
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.attachments: ~1 rows (approximately)
/*!40000 ALTER TABLE `attachments` DISABLE KEYS */;
INSERT INTO `attachments` (`id`, `model`, `foreign_key`, `filename`, `extension`, `mime_type`, `file_size`, `description`, `user_id`, `created`, `modified`) VALUES
	(1, 'SecurityService', 2, '/files/uploads/6666666666666666666t-1.pdf', 'pdf', 'application/pdf', 21470, '', NULL, '2015-08-16 23:32:27', '2015-08-16 23:32:27');
/*!40000 ALTER TABLE `attachments` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.awareness_overtime_graphs
DROP TABLE IF EXISTS `awareness_overtime_graphs`;
CREATE TABLE IF NOT EXISTS `awareness_overtime_graphs` (
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.awareness_overtime_graphs: ~3 rows (approximately)
/*!40000 ALTER TABLE `awareness_overtime_graphs` DISABLE KEYS */;
INSERT INTO `awareness_overtime_graphs` (`id`, `awareness_program_id`, `title`, `doing`, `missing`, `correct_answers`, `average`, `timestamp`, `created`) VALUES
	(1, NULL, 'test', 50.00, 50.00, 50.00, 83.33, '1438874201', '2015-08-06 17:16:41'),
	(2, NULL, 'test', 50.00, 50.00, 50.00, 83.33, '1438874325', '2015-08-06 17:18:45'),
	(4, NULL, 'test', 50.00, 50.00, 50.00, 83.33, '1438875978', '2015-08-06 17:46:18');
/*!40000 ALTER TABLE `awareness_overtime_graphs` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.awareness_programs
DROP TABLE IF EXISTS `awareness_programs`;
CREATE TABLE IF NOT EXISTS `awareness_programs` (
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
  `questionnaire` varchar(255) NOT NULL,
  `welcome_text` text NOT NULL,
  `welcome_sub_text` text NOT NULL,
  `thank_you_text` text NOT NULL,
  `thank_you_sub_text` text NOT NULL,
  `email_subject` varchar(150) NOT NULL,
  `email_body` text NOT NULL,
  `status` enum('started','stopped') NOT NULL DEFAULT 'stopped',
  `awareness_training_count` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ldap_connector_id` (`ldap_connector_id`),
  CONSTRAINT `awareness_programs_ibfk_1` FOREIGN KEY (`ldap_connector_id`) REFERENCES `ldap_connectors` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.awareness_programs: ~0 rows (approximately)
/*!40000 ALTER TABLE `awareness_programs` DISABLE KEYS */;
/*!40000 ALTER TABLE `awareness_programs` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.awareness_program_demos
DROP TABLE IF EXISTS `awareness_program_demos`;
CREATE TABLE IF NOT EXISTS `awareness_program_demos` (
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

-- Dumping data for table e_merge_enterprise.awareness_program_demos: ~0 rows (approximately)
/*!40000 ALTER TABLE `awareness_program_demos` DISABLE KEYS */;
/*!40000 ALTER TABLE `awareness_program_demos` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.awareness_program_ignored_users
DROP TABLE IF EXISTS `awareness_program_ignored_users`;
CREATE TABLE IF NOT EXISTS `awareness_program_ignored_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `awareness_program_id` int(11) NOT NULL,
  `uid` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_program_ignored_users_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.awareness_program_ignored_users: ~0 rows (approximately)
/*!40000 ALTER TABLE `awareness_program_ignored_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `awareness_program_ignored_users` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.awareness_program_ldap_groups
DROP TABLE IF EXISTS `awareness_program_ldap_groups`;
CREATE TABLE IF NOT EXISTS `awareness_program_ldap_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `awareness_program_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_program_ldap_groups_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.awareness_program_ldap_groups: ~0 rows (approximately)
/*!40000 ALTER TABLE `awareness_program_ldap_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `awareness_program_ldap_groups` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.awareness_program_recurrences
DROP TABLE IF EXISTS `awareness_program_recurrences`;
CREATE TABLE IF NOT EXISTS `awareness_program_recurrences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `awareness_program_id` int(11) NOT NULL,
  `start` date NOT NULL,
  `awareness_training_count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_program_recurrences_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.awareness_program_recurrences: ~0 rows (approximately)
/*!40000 ALTER TABLE `awareness_program_recurrences` DISABLE KEYS */;
/*!40000 ALTER TABLE `awareness_program_recurrences` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.awareness_reminders
DROP TABLE IF EXISTS `awareness_reminders`;
CREATE TABLE IF NOT EXISTS `awareness_reminders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(100) NOT NULL,
  `awareness_program_id` int(11) NOT NULL,
  `demo` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `awareness_user_id` (`uid`),
  KEY `awareness_program_id` (`awareness_program_id`),
  CONSTRAINT `awareness_reminders_ibfk_1` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.awareness_reminders: ~0 rows (approximately)
/*!40000 ALTER TABLE `awareness_reminders` DISABLE KEYS */;
/*!40000 ALTER TABLE `awareness_reminders` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.awareness_trainings
DROP TABLE IF EXISTS `awareness_trainings`;
CREATE TABLE IF NOT EXISTS `awareness_trainings` (
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
  CONSTRAINT `awareness_trainings_ibfk_3` FOREIGN KEY (`awareness_program_id`) REFERENCES `awareness_programs` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `awareness_trainings_ibfk_4` FOREIGN KEY (`awareness_program_recurrence_id`) REFERENCES `awareness_program_recurrences` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.awareness_trainings: ~4 rows (approximately)
/*!40000 ALTER TABLE `awareness_trainings` DISABLE KEYS */;
INSERT INTO `awareness_trainings` (`id`, `awareness_user_id`, `awareness_program_id`, `awareness_program_recurrence_id`, `answers_json`, `correct`, `wrong`, `demo`, `created`, `modified`) VALUES
	(22, 1, NULL, NULL, NULL, NULL, NULL, 0, '2015-08-06 18:46:48', '2015-08-06 18:46:48'),
	(23, 1, NULL, NULL, '[1,3,4]', 1, 2, 1, '2015-08-08 11:39:50', '2015-08-08 11:39:50'),
	(24, 1, NULL, NULL, '[3,3,4]', 0, 3, 1, '2015-08-08 14:01:17', '2015-08-08 14:01:17'),
	(25, 1, NULL, NULL, '[1,1,3]', 2, 1, 1, '2015-08-08 14:56:08', '2015-08-08 14:56:08');
/*!40000 ALTER TABLE `awareness_trainings` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.awareness_users
DROP TABLE IF EXISTS `awareness_users`;
CREATE TABLE IF NOT EXISTS `awareness_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.awareness_users: ~2 rows (approximately)
/*!40000 ALTER TABLE `awareness_users` DISABLE KEYS */;
INSERT INTO `awareness_users` (`id`, `login`, `created`) VALUES
	(1, 'martin.horvath', '2015-08-01 15:10:39'),
	(2, 'martin.test', '2015-08-01 16:52:00');
/*!40000 ALTER TABLE `awareness_users` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuities
DROP TABLE IF EXISTS `business_continuities`;
CREATE TABLE IF NOT EXISTS `business_continuities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `impact` text NOT NULL,
  `threats` text NOT NULL,
  `vulnerabilities` text NOT NULL,
  `residual_score` int(11) NOT NULL,
  `risk_score` int(11) NOT NULL,
  `residual_risk` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `guardian_id` int(11) NOT NULL,
  `review` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `exceptions_issues` int(1) NOT NULL DEFAULT '0',
  `controls_issues` int(1) NOT NULL DEFAULT '0',
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

-- Dumping data for table e_merge_enterprise.business_continuities: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuities` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuities` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuities_business_continuity_plans
DROP TABLE IF EXISTS `business_continuities_business_continuity_plans`;
CREATE TABLE IF NOT EXISTS `business_continuities_business_continuity_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `business_continuity_plan_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `business_continuity_plan_id` (`business_continuity_plan_id`),
  CONSTRAINT `business_continuities_business_continuity_plans_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_business_continuity_plans_ibfk_2` FOREIGN KEY (`business_continuity_plan_id`) REFERENCES `business_continuity_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuities_business_continuity_plans: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuities_business_continuity_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuities_business_continuity_plans` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuities_business_units
DROP TABLE IF EXISTS `business_continuities_business_units`;
CREATE TABLE IF NOT EXISTS `business_continuities_business_units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `business_unit_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `business_unit_id` (`business_unit_id`),
  CONSTRAINT `business_continuities_business_units_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_business_units_ibfk_2` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuities_business_units: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuities_business_units` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuities_business_units` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuities_compliance_managements
DROP TABLE IF EXISTS `business_continuities_compliance_managements`;
CREATE TABLE IF NOT EXISTS `business_continuities_compliance_managements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `compliance_management_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_management_id` (`compliance_management_id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  CONSTRAINT `business_continuities_compliance_managements_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_compliance_managements_ibfk_2` FOREIGN KEY (`compliance_management_id`) REFERENCES `compliance_managements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuities_compliance_managements: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuities_compliance_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuities_compliance_managements` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuities_processes
DROP TABLE IF EXISTS `business_continuities_processes`;
CREATE TABLE IF NOT EXISTS `business_continuities_processes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `process_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `process_id` (`process_id`),
  CONSTRAINT `business_continuities_processes_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_processes_ibfk_2` FOREIGN KEY (`process_id`) REFERENCES `processes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuities_processes: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuities_processes` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuities_processes` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuities_projects
DROP TABLE IF EXISTS `business_continuities_projects`;
CREATE TABLE IF NOT EXISTS `business_continuities_projects` (
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

-- Dumping data for table e_merge_enterprise.business_continuities_projects: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuities_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuities_projects` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuities_risk_classifications
DROP TABLE IF EXISTS `business_continuities_risk_classifications`;
CREATE TABLE IF NOT EXISTS `business_continuities_risk_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `risk_classification_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `risk_classification_id` (`risk_classification_id`),
  CONSTRAINT `business_continuities_risk_classifications_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_risk_classifications_ibfk_2` FOREIGN KEY (`risk_classification_id`) REFERENCES `risk_classifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuities_risk_classifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuities_risk_classifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuities_risk_classifications` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuities_risk_exceptions
DROP TABLE IF EXISTS `business_continuities_risk_exceptions`;
CREATE TABLE IF NOT EXISTS `business_continuities_risk_exceptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `risk_exception_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `risk_exception_id` (`risk_exception_id`),
  CONSTRAINT `business_continuities_risk_exceptions_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_risk_exceptions_ibfk_2` FOREIGN KEY (`risk_exception_id`) REFERENCES `risk_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuities_risk_exceptions: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuities_risk_exceptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuities_risk_exceptions` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuities_security_services
DROP TABLE IF EXISTS `business_continuities_security_services`;
CREATE TABLE IF NOT EXISTS `business_continuities_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuities_security_services: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuities_security_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuities_security_services` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuities_threats
DROP TABLE IF EXISTS `business_continuities_threats`;
CREATE TABLE IF NOT EXISTS `business_continuities_threats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `threat_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `threat_id` (`threat_id`),
  CONSTRAINT `business_continuities_threats_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_threats_ibfk_2` FOREIGN KEY (`threat_id`) REFERENCES `threats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuities_threats: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuities_threats` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuities_threats` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuities_vulnerabilities
DROP TABLE IF EXISTS `business_continuities_vulnerabilities`;
CREATE TABLE IF NOT EXISTS `business_continuities_vulnerabilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_id` int(11) NOT NULL,
  `vulnerability_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_id` (`business_continuity_id`),
  KEY `vulnerability_id` (`vulnerability_id`),
  CONSTRAINT `business_continuities_vulnerabilities_ibfk_1` FOREIGN KEY (`business_continuity_id`) REFERENCES `business_continuities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_continuities_vulnerabilities_ibfk_2` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuities_vulnerabilities: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuities_vulnerabilities` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuities_vulnerabilities` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuity_plans
DROP TABLE IF EXISTS `business_continuity_plans`;
CREATE TABLE IF NOT EXISTS `business_continuity_plans` (
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

-- Dumping data for table e_merge_enterprise.business_continuity_plans: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuity_plans` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuity_plans` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuity_plan_audits
DROP TABLE IF EXISTS `business_continuity_plan_audits`;
CREATE TABLE IF NOT EXISTS `business_continuity_plan_audits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_plan_id` int(11) NOT NULL,
  `audit_metric_description` text NOT NULL,
  `audit_success_criteria` text NOT NULL,
  `result` int(1) DEFAULT NULL,
  `result_description` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `planned_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `business_continuity_plan_audits_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuity_plan_audits: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuity_plan_audits` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuity_plan_audits` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuity_plan_audit_dates
DROP TABLE IF EXISTS `business_continuity_plan_audit_dates`;
CREATE TABLE IF NOT EXISTS `business_continuity_plan_audit_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_continuity_plan_id` int(11) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_continuity_plan_id` (`business_continuity_plan_id`),
  CONSTRAINT `business_continuity_plan_audit_dates_ibfk_1` FOREIGN KEY (`business_continuity_plan_id`) REFERENCES `business_continuity_plans` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuity_plan_audit_dates: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuity_plan_audit_dates` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuity_plan_audit_dates` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuity_plan_audit_improvements
DROP TABLE IF EXISTS `business_continuity_plan_audit_improvements`;
CREATE TABLE IF NOT EXISTS `business_continuity_plan_audit_improvements` (
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

-- Dumping data for table e_merge_enterprise.business_continuity_plan_audit_improvements: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuity_plan_audit_improvements` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuity_plan_audit_improvements` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuity_plan_audit_improvements_projects
DROP TABLE IF EXISTS `business_continuity_plan_audit_improvements_projects`;
CREATE TABLE IF NOT EXISTS `business_continuity_plan_audit_improvements_projects` (
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

-- Dumping data for table e_merge_enterprise.business_continuity_plan_audit_improvements_projects: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuity_plan_audit_improvements_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuity_plan_audit_improvements_projects` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuity_tasks
DROP TABLE IF EXISTS `business_continuity_tasks`;
CREATE TABLE IF NOT EXISTS `business_continuity_tasks` (
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
  CONSTRAINT `business_continuity_tasks_ibfk_1` FOREIGN KEY (`awareness_role`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `business_continuity_tasks_ibfk_2` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_continuity_tasks: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuity_tasks` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuity_tasks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_continuity_task_reminders
DROP TABLE IF EXISTS `business_continuity_task_reminders`;
CREATE TABLE IF NOT EXISTS `business_continuity_task_reminders` (
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

-- Dumping data for table e_merge_enterprise.business_continuity_task_reminders: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_continuity_task_reminders` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_continuity_task_reminders` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_units
DROP TABLE IF EXISTS `business_units`;
CREATE TABLE IF NOT EXISTS `business_units` (
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_units: ~1 rows (approximately)
/*!40000 ALTER TABLE `business_units` DISABLE KEYS */;
INSERT INTO `business_units` (`id`, `name`, `description`, `workflow_status`, `workflow_owner_id`, `_hidden`, `created`, `modified`) VALUES
	(1, 'Everyone', '', 0, NULL, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `business_units` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_units_data_assets
DROP TABLE IF EXISTS `business_units_data_assets`;
CREATE TABLE IF NOT EXISTS `business_units_data_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_unit_id` int(11) NOT NULL,
  `data_asset_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_units_data_assets: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_units_data_assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_units_data_assets` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_units_legals
DROP TABLE IF EXISTS `business_units_legals`;
CREATE TABLE IF NOT EXISTS `business_units_legals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_unit_id` int(11) NOT NULL,
  `legal_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_unit_id` (`business_unit_id`),
  KEY `legal_id` (`legal_id`),
  CONSTRAINT `business_units_legals_ibfk_1` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_units_legals_ibfk_2` FOREIGN KEY (`legal_id`) REFERENCES `legals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_units_legals: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_units_legals` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_units_legals` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.business_units_users
DROP TABLE IF EXISTS `business_units_users`;
CREATE TABLE IF NOT EXISTS `business_units_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `business_unit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `business_unit_id` (`business_unit_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `business_units_users_ibfk_1` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `business_units_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.business_units_users: ~0 rows (approximately)
/*!40000 ALTER TABLE `business_units_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `business_units_users` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.comments
DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.comments: ~5 rows (approximately)
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` (`id`, `model`, `foreign_key`, `message`, `user_id`, `created`, `modified`) VALUES
	(1, 'Legal', 1, '645464\r\n5', 1, '2015-08-16 02:06:07', '2015-08-16 02:06:07'),
	(2, 'Legal', 13, 'refds', 1, '2015-08-16 11:16:28', '2015-08-16 11:16:28'),
	(3, 'Process', 1, 'commentiiik', 1, '2015-08-16 15:42:52', '2015-08-16 15:42:52'),
	(4, 'SecurityServiceAudit', 8, 'ewqaf', 1, '2015-08-16 15:45:51', '2015-08-16 15:45:51'),
	(5, 'Asset', 1, 'comment nr.1', 1, '2015-08-16 18:04:52', '2015-08-16 18:04:52');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_audits
DROP TABLE IF EXISTS `compliance_audits`;
CREATE TABLE IF NOT EXISTS `compliance_audits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `third_party_id` int(11) NOT NULL,
  `auditor_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `compliance_finding_count` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_id` (`third_party_id`),
  KEY `auditor_id` (`auditor_id`),
  CONSTRAINT `compliance_audits_ibfk_1` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_audits_ibfk_2` FOREIGN KEY (`auditor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_audits: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_audits` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_audits` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_audit_overtime_graphs
DROP TABLE IF EXISTS `compliance_audit_overtime_graphs`;
CREATE TABLE IF NOT EXISTS `compliance_audit_overtime_graphs` (
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

-- Dumping data for table e_merge_enterprise.compliance_audit_overtime_graphs: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_audit_overtime_graphs` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_audit_overtime_graphs` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_audit_settings
DROP TABLE IF EXISTS `compliance_audit_settings`;
CREATE TABLE IF NOT EXISTS `compliance_audit_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_audit_id` int(11) NOT NULL,
  `compliance_package_item_id` int(11) NOT NULL,
  `status` int(1) DEFAULT NULL,
  `auditee_notifications` int(1) NOT NULL DEFAULT '0',
  `auditee_emails` int(1) NOT NULL DEFAULT '0',
  `auditor_notifications` int(1) NOT NULL DEFAULT '0',
  `auditor_emails` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_audit_id` (`compliance_audit_id`),
  KEY `compliance_package_item_id` (`compliance_package_item_id`),
  CONSTRAINT `compliance_audit_settings_ibfk_1` FOREIGN KEY (`compliance_audit_id`) REFERENCES `compliance_audits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_audit_settings_ibfk_2` FOREIGN KEY (`compliance_package_item_id`) REFERENCES `compliance_package_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_audit_settings: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_audit_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_audit_settings` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_audit_settings_auditees
DROP TABLE IF EXISTS `compliance_audit_settings_auditees`;
CREATE TABLE IF NOT EXISTS `compliance_audit_settings_auditees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_audit_setting_id` int(11) NOT NULL,
  `auditee_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_audit_setting_id` (`compliance_audit_setting_id`),
  KEY `auditee_id` (`auditee_id`),
  CONSTRAINT `compliance_audit_settings_auditees_ibfk_1` FOREIGN KEY (`compliance_audit_setting_id`) REFERENCES `compliance_audit_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_audit_settings_auditees_ibfk_2` FOREIGN KEY (`auditee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_audit_settings_auditees: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_audit_settings_auditees` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_audit_settings_auditees` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_audit_setting_notifications
DROP TABLE IF EXISTS `compliance_audit_setting_notifications`;
CREATE TABLE IF NOT EXISTS `compliance_audit_setting_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_audit_setting_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_audit_setting_id` (`compliance_audit_setting_id`),
  CONSTRAINT `compliance_audit_setting_notifications_ibfk_1` FOREIGN KEY (`compliance_audit_setting_id`) REFERENCES `compliance_audit_settings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_audit_setting_notifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_audit_setting_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_audit_setting_notifications` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_exceptions
DROP TABLE IF EXISTS `compliance_exceptions`;
CREATE TABLE IF NOT EXISTS `compliance_exceptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `author_id` int(11) NOT NULL,
  `expiration` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL COMMENT '0-closed, 1-open',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_compliance_exceptions_users` (`author_id`),
  CONSTRAINT `FK_compliance_exceptions_users` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_exceptions: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_exceptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_exceptions` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_findings
DROP TABLE IF EXISTS `compliance_findings`;
CREATE TABLE IF NOT EXISTS `compliance_findings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `deadline` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `compliance_finding_status_id` int(11) DEFAULT NULL,
  `compliance_audit_id` int(11) NOT NULL,
  `compliance_package_item_id` int(11) DEFAULT NULL,
  `type` int(1) NOT NULL DEFAULT '1' COMMENT '1-audit finding, 2-assesed item',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_finding_status_id` (`compliance_finding_status_id`),
  KEY `compliance_audit_id` (`compliance_audit_id`),
  KEY `compliance_package_item_id` (`compliance_package_item_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `compliance_findings_ibfk_1` FOREIGN KEY (`compliance_finding_status_id`) REFERENCES `compliance_finding_statuses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `compliance_findings_ibfk_2` FOREIGN KEY (`compliance_audit_id`) REFERENCES `compliance_audits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_findings_ibfk_3` FOREIGN KEY (`compliance_package_item_id`) REFERENCES `compliance_package_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_findings_ibfk_4` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_findings: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_findings` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_findings` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_finding_classifications
DROP TABLE IF EXISTS `compliance_finding_classifications`;
CREATE TABLE IF NOT EXISTS `compliance_finding_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_finding_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_finding_id` (`compliance_finding_id`),
  CONSTRAINT `compliance_finding_classifications_ibfk_1` FOREIGN KEY (`compliance_finding_id`) REFERENCES `compliance_findings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_finding_classifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_finding_classifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_finding_classifications` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_finding_statuses
DROP TABLE IF EXISTS `compliance_finding_statuses`;
CREATE TABLE IF NOT EXISTS `compliance_finding_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_finding_statuses: ~2 rows (approximately)
/*!40000 ALTER TABLE `compliance_finding_statuses` DISABLE KEYS */;
INSERT INTO `compliance_finding_statuses` (`id`, `name`) VALUES
	(1, 'Open Item'),
	(2, 'Closed Item');
/*!40000 ALTER TABLE `compliance_finding_statuses` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_managements
DROP TABLE IF EXISTS `compliance_managements`;
CREATE TABLE IF NOT EXISTS `compliance_managements` (
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
  CONSTRAINT `compliance_managements_ibfk_1` FOREIGN KEY (`compliance_package_item_id`) REFERENCES `compliance_package_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_ibfk_2` FOREIGN KEY (`compliance_treatment_strategy_id`) REFERENCES `compliance_treatment_strategies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_ibfk_3` FOREIGN KEY (`compliance_exception_id`) REFERENCES `compliance_exceptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_ibfk_4` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_compliance_managements_legals` FOREIGN KEY (`legal_id`) REFERENCES `legals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_managements: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_managements` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_managements` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_managements_projects
DROP TABLE IF EXISTS `compliance_managements_projects`;
CREATE TABLE IF NOT EXISTS `compliance_managements_projects` (
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

-- Dumping data for table e_merge_enterprise.compliance_managements_projects: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_managements_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_managements_projects` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_managements_risks
DROP TABLE IF EXISTS `compliance_managements_risks`;
CREATE TABLE IF NOT EXISTS `compliance_managements_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_management_id` int(11) NOT NULL,
  `risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_id` (`risk_id`),
  KEY `compliance_management_id` (`compliance_management_id`),
  CONSTRAINT `compliance_managements_risks_ibfk_1` FOREIGN KEY (`compliance_management_id`) REFERENCES `compliance_managements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_risks_ibfk_2` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_managements_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_managements_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_managements_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_managements_security_policies
DROP TABLE IF EXISTS `compliance_managements_security_policies`;
CREATE TABLE IF NOT EXISTS `compliance_managements_security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_management_id` int(11) NOT NULL,
  `security_policy_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_management_id` (`compliance_management_id`),
  KEY `security_policy_id` (`security_policy_id`),
  CONSTRAINT `compliance_managements_security_policies_ibfk_1` FOREIGN KEY (`compliance_management_id`) REFERENCES `compliance_managements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_security_policies_ibfk_2` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_managements_security_policies: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_managements_security_policies` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_managements_security_policies` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_managements_security_services
DROP TABLE IF EXISTS `compliance_managements_security_services`;
CREATE TABLE IF NOT EXISTS `compliance_managements_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_management_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_management_id` (`compliance_management_id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `compliance_managements_security_services_ibfk_1` FOREIGN KEY (`compliance_management_id`) REFERENCES `compliance_managements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_security_services_ibfk_2` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_managements_security_services: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_managements_security_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_managements_security_services` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_managements_third_party_risks
DROP TABLE IF EXISTS `compliance_managements_third_party_risks`;
CREATE TABLE IF NOT EXISTS `compliance_managements_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compliance_management_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compliance_management_id` (`compliance_management_id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  CONSTRAINT `compliance_managements_third_party_risks_ibfk_1` FOREIGN KEY (`compliance_management_id`) REFERENCES `compliance_managements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `compliance_managements_third_party_risks_ibfk_2` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_managements_third_party_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_managements_third_party_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_managements_third_party_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_packages
DROP TABLE IF EXISTS `compliance_packages`;
CREATE TABLE IF NOT EXISTS `compliance_packages` (
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

-- Dumping data for table e_merge_enterprise.compliance_packages: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_packages` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_packages` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_package_items
DROP TABLE IF EXISTS `compliance_package_items`;
CREATE TABLE IF NOT EXISTS `compliance_package_items` (
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

-- Dumping data for table e_merge_enterprise.compliance_package_items: ~0 rows (approximately)
/*!40000 ALTER TABLE `compliance_package_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `compliance_package_items` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_statuses
DROP TABLE IF EXISTS `compliance_statuses`;
CREATE TABLE IF NOT EXISTS `compliance_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_statuses: ~4 rows (approximately)
/*!40000 ALTER TABLE `compliance_statuses` DISABLE KEYS */;
INSERT INTO `compliance_statuses` (`id`, `name`) VALUES
	(1, 'On-Going'),
	(2, 'Compliant'),
	(3, 'Non-Compliant'),
	(4, 'Not-Applicable');
/*!40000 ALTER TABLE `compliance_statuses` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.compliance_treatment_strategies
DROP TABLE IF EXISTS `compliance_treatment_strategies`;
CREATE TABLE IF NOT EXISTS `compliance_treatment_strategies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.compliance_treatment_strategies: ~3 rows (approximately)
/*!40000 ALTER TABLE `compliance_treatment_strategies` DISABLE KEYS */;
INSERT INTO `compliance_treatment_strategies` (`id`, `name`) VALUES
	(1, 'Compliant'),
	(2, 'Not Applicable'),
	(3, 'Not Compliant');
/*!40000 ALTER TABLE `compliance_treatment_strategies` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.cron
DROP TABLE IF EXISTS `cron`;
CREATE TABLE IF NOT EXISTS `cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('daily') NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.cron: ~3 rows (approximately)
/*!40000 ALTER TABLE `cron` DISABLE KEYS */;
INSERT INTO `cron` (`id`, `type`, `created`) VALUES
	(1, 'daily', '2015-08-06 17:16:41'),
	(2, 'daily', '2015-08-06 17:18:45'),
	(3, 'daily', '2015-08-06 17:46:19');
/*!40000 ALTER TABLE `cron` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.data_assets
DROP TABLE IF EXISTS `data_assets`;
CREATE TABLE IF NOT EXISTS `data_assets` (
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

-- Dumping data for table e_merge_enterprise.data_assets: ~0 rows (approximately)
/*!40000 ALTER TABLE `data_assets` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_assets` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.data_assets_projects
DROP TABLE IF EXISTS `data_assets_projects`;
CREATE TABLE IF NOT EXISTS `data_assets_projects` (
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

-- Dumping data for table e_merge_enterprise.data_assets_projects: ~0 rows (approximately)
/*!40000 ALTER TABLE `data_assets_projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_assets_projects` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.data_assets_security_services
DROP TABLE IF EXISTS `data_assets_security_services`;
CREATE TABLE IF NOT EXISTS `data_assets_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_asset_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.data_assets_security_services: ~0 rows (approximately)
/*!40000 ALTER TABLE `data_assets_security_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_assets_security_services` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.data_assets_third_parties
DROP TABLE IF EXISTS `data_assets_third_parties`;
CREATE TABLE IF NOT EXISTS `data_assets_third_parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_asset_id` int(11) NOT NULL,
  `third_party_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.data_assets_third_parties: ~0 rows (approximately)
/*!40000 ALTER TABLE `data_assets_third_parties` DISABLE KEYS */;
/*!40000 ALTER TABLE `data_assets_third_parties` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.data_asset_statuses
DROP TABLE IF EXISTS `data_asset_statuses`;
CREATE TABLE IF NOT EXISTS `data_asset_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.data_asset_statuses: ~7 rows (approximately)
/*!40000 ALTER TABLE `data_asset_statuses` DISABLE KEYS */;
INSERT INTO `data_asset_statuses` (`id`, `name`) VALUES
	(1, 'Created'),
	(2, 'Modified'),
	(3, 'Stored'),
	(4, 'Transit'),
	(5, 'Deleted'),
	(6, 'Tainted / Broken'),
	(7, 'Unnecessary');
/*!40000 ALTER TABLE `data_asset_statuses` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.groups
DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `status` int(11) DEFAULT '1' COMMENT '0-non active, 1-active',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.groups: ~1 rows (approximately)
/*!40000 ALTER TABLE `groups` DISABLE KEYS */;
INSERT INTO `groups` (`id`, `name`, `description`, `status`, `created`, `modified`) VALUES
	(10, 'Admin', '', 1, '2013-10-14 16:18:08', '2013-10-14 16:18:08');
/*!40000 ALTER TABLE `groups` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.ldap_connectors
DROP TABLE IF EXISTS `ldap_connectors`;
CREATE TABLE IF NOT EXISTS `ldap_connectors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `host` varchar(150) NOT NULL,
  `domain` varchar(150) DEFAULT NULL,
  `port` int(11) NOT NULL DEFAULT '389',
  `ssl_enabled` int(1) NOT NULL,
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
  `ldap_groupmemberlist_name` varchar(150) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0-disabled,1-active',
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.ldap_connectors: ~2 rows (approximately)
/*!40000 ALTER TABLE `ldap_connectors` DISABLE KEYS */;
INSERT INTO `ldap_connectors` (`id`, `name`, `description`, `host`, `domain`, `port`, `ssl_enabled`, `ldap_bind_dn`, `ldap_bind_pw`, `ldap_base_dn`, `type`, `ldap_auth_filter`, `ldap_auth_attribute`, `ldap_name_attribute`, `ldap_email_attribute`, `ldap_memberof_attribute`, `ldap_grouplist_filter`, `ldap_grouplist_name`, `ldap_groupmemberlist_filter`, `ldap_groupmemberlist_name`, `status`, `workflow_status`, `workflow_owner_id`, `created`, `modified`) VALUES
	(1, 'authenticator', '', 'ldap.eramba.org', 'eramba.org', 389, 0, 'cn=admin,dc=dev,dc=eramba,dc=org', 'llllll', 'ou=users,dc=dev,dc=eramba,dc=org', 'authenticator', '(| (sn=%USERNAME%) )', 'cn', 'givenname', '', 'memberof', '', '', '', '', 1, 4, 1, '2015-08-01 14:31:26', '2015-08-06 13:08:01'),
	(2, 'group connector', '', 'ldap.eramba.org', 'eramba.org', 389, 0, 'cn=admin,dc=dev,dc=eramba,dc=org', 'llllll', 'dc=dev,dc=eramba,dc=org', 'group', '(| (sn=%USERNAME%) )', '', '', '', '', '(objectClass=groupOfNames)', 'cn', '(objectClass=groupOfNames)', 'member', 1, 4, 1, '2015-08-01 14:33:23', '2015-08-01 20:50:35');
/*!40000 ALTER TABLE `ldap_connectors` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.ldap_connector_authentication
DROP TABLE IF EXISTS `ldap_connector_authentication`;
CREATE TABLE IF NOT EXISTS `ldap_connector_authentication` (
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

-- Dumping data for table e_merge_enterprise.ldap_connector_authentication: ~1 rows (approximately)
/*!40000 ALTER TABLE `ldap_connector_authentication` DISABLE KEYS */;
INSERT INTO `ldap_connector_authentication` (`id`, `auth_users`, `auth_users_id`, `auth_awareness`, `auth_awareness_id`, `auth_policies`, `auth_policies_id`, `modified`) VALUES
	(1, 0, NULL, 1, 1, 0, NULL, '2015-08-16 11:20:01');
/*!40000 ALTER TABLE `ldap_connector_authentication` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.legals
DROP TABLE IF EXISTS `legals`;
CREATE TABLE IF NOT EXISTS `legals` (
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
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.legals: ~0 rows (approximately)
/*!40000 ALTER TABLE `legals` DISABLE KEYS */;
/*!40000 ALTER TABLE `legals` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.legals_third_parties
DROP TABLE IF EXISTS `legals_third_parties`;
CREATE TABLE IF NOT EXISTS `legals_third_parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `legal_id` int(11) NOT NULL,
  `third_party_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `legal_id` (`legal_id`),
  KEY `third_party_id` (`third_party_id`),
  CONSTRAINT `legals_third_parties_ibfk_1` FOREIGN KEY (`legal_id`) REFERENCES `legals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `legals_third_parties_ibfk_2` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.legals_third_parties: ~0 rows (approximately)
/*!40000 ALTER TABLE `legals_third_parties` DISABLE KEYS */;
/*!40000 ALTER TABLE `legals_third_parties` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.legals_users
DROP TABLE IF EXISTS `legals_users`;
CREATE TABLE IF NOT EXISTS `legals_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `legal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `legal_id` (`legal_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `legals_users_ibfk_1` FOREIGN KEY (`legal_id`) REFERENCES `legals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `legals_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.legals_users: ~0 rows (approximately)
/*!40000 ALTER TABLE `legals_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `legals_users` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.log_security_policies
DROP TABLE IF EXISTS `log_security_policies`;
CREATE TABLE IF NOT EXISTS `log_security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_policy_id` int(11) NOT NULL,
  `index` varchar(100) NOT NULL,
  `short_description` varchar(150) NOT NULL,
  `description` text NOT NULL,
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

-- Dumping data for table e_merge_enterprise.log_security_policies: ~0 rows (approximately)
/*!40000 ALTER TABLE `log_security_policies` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_security_policies` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.notifications
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `model` varchar(150) NOT NULL,
  `user_id` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '1-new; 0-seen',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.notifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.notification_system_items
DROP TABLE IF EXISTS `notification_system_items`;
CREATE TABLE IF NOT EXISTS `notification_system_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `email_notification` int(1) NOT NULL DEFAULT '0',
  `header_notification` int(1) NOT NULL DEFAULT '0',
  `feedback` int(1) NOT NULL DEFAULT '0',
  `chase_interval` int(2) DEFAULT NULL,
  `chase_amount` int(3) DEFAULT NULL COMMENT 'how many times a notification will be remindered',
  `trigger_period` int(5) DEFAULT NULL COMMENT 'awareness uses this field',
  `type` enum('awareness','warning') NOT NULL,
  `status_feedback` int(2) NOT NULL DEFAULT '0' COMMENT '0-ok, 1- waiting for feedback, 2-feedback ignored',
  `log_count` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.notification_system_items: ~0 rows (approximately)
/*!40000 ALTER TABLE `notification_system_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_system_items` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.notification_system_items_scopes
DROP TABLE IF EXISTS `notification_system_items_scopes`;
CREATE TABLE IF NOT EXISTS `notification_system_items_scopes` (
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

-- Dumping data for table e_merge_enterprise.notification_system_items_scopes: ~0 rows (approximately)
/*!40000 ALTER TABLE `notification_system_items_scopes` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_system_items_scopes` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.notification_system_items_users
DROP TABLE IF EXISTS `notification_system_items_users`;
CREATE TABLE IF NOT EXISTS `notification_system_items_users` (
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

-- Dumping data for table e_merge_enterprise.notification_system_items_users: ~0 rows (approximately)
/*!40000 ALTER TABLE `notification_system_items_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_system_items_users` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.notification_system_item_custom_roles
DROP TABLE IF EXISTS `notification_system_item_custom_roles`;
CREATE TABLE IF NOT EXISTS `notification_system_item_custom_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_id` int(11) NOT NULL,
  `custom_identifier` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_id` (`notification_system_item_id`),
  CONSTRAINT `notification_system_item_custom_roles_ibfk_1` FOREIGN KEY (`notification_system_item_id`) REFERENCES `notification_system_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.notification_system_item_custom_roles: ~0 rows (approximately)
/*!40000 ALTER TABLE `notification_system_item_custom_roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_system_item_custom_roles` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.notification_system_item_custom_users
DROP TABLE IF EXISTS `notification_system_item_custom_users`;
CREATE TABLE IF NOT EXISTS `notification_system_item_custom_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_id` (`notification_system_item_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notification_system_item_custom_users_ibfk_1` FOREIGN KEY (`notification_system_item_id`) REFERENCES `notification_system_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notification_system_item_custom_users_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.notification_system_item_custom_users: ~0 rows (approximately)
/*!40000 ALTER TABLE `notification_system_item_custom_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_system_item_custom_users` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.notification_system_item_emails
DROP TABLE IF EXISTS `notification_system_item_emails`;
CREATE TABLE IF NOT EXISTS `notification_system_item_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_id` (`notification_system_item_id`),
  CONSTRAINT `notification_system_item_emails_ibfk_1` FOREIGN KEY (`notification_system_item_id`) REFERENCES `notification_system_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.notification_system_item_emails: ~0 rows (approximately)
/*!40000 ALTER TABLE `notification_system_item_emails` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_system_item_emails` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.notification_system_item_feedbacks
DROP TABLE IF EXISTS `notification_system_item_feedbacks`;
CREATE TABLE IF NOT EXISTS `notification_system_item_feedbacks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_log_id` int(11) NOT NULL,
  `notification_system_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_log_id` (`notification_system_item_log_id`),
  KEY `user_id` (`user_id`),
  KEY `notification_system_item_id` (`notification_system_item_id`),
  CONSTRAINT `notification_system_item_feedbacks_ibfk_1` FOREIGN KEY (`notification_system_item_log_id`) REFERENCES `notification_system_item_logs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notification_system_item_feedbacks_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notification_system_item_feedbacks_ibfk_3` FOREIGN KEY (`notification_system_item_id`) REFERENCES `notification_system_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.notification_system_item_feedbacks: ~0 rows (approximately)
/*!40000 ALTER TABLE `notification_system_item_feedbacks` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_system_item_feedbacks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.notification_system_item_logs
DROP TABLE IF EXISTS `notification_system_item_logs`;
CREATE TABLE IF NOT EXISTS `notification_system_item_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_system_item_id` int(11) NOT NULL,
  `is_new` int(1) NOT NULL DEFAULT '1' COMMENT '1-new, 0-reminder',
  `feedback_resolved` int(1) DEFAULT '0' COMMENT '1-feedback entered',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `notification_system_item_id` (`notification_system_item_id`),
  CONSTRAINT `notification_system_item_logs_ibfk_1` FOREIGN KEY (`notification_system_item_id`) REFERENCES `notification_system_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.notification_system_item_logs: ~0 rows (approximately)
/*!40000 ALTER TABLE `notification_system_item_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_system_item_logs` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.policy_exceptions
DROP TABLE IF EXISTS `policy_exceptions`;
CREATE TABLE IF NOT EXISTS `policy_exceptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `expiration` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL COMMENT '0-closed, 1-open',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `policy_exceptions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `policy_exceptions_ibfk_3` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.policy_exceptions: ~0 rows (approximately)
/*!40000 ALTER TABLE `policy_exceptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `policy_exceptions` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.policy_exceptions_security_policies
DROP TABLE IF EXISTS `policy_exceptions_security_policies`;
CREATE TABLE IF NOT EXISTS `policy_exceptions_security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `policy_exception_id` int(11) NOT NULL,
  `security_policy_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `policy_exception_id` (`policy_exception_id`),
  KEY `security_policy_id` (`security_policy_id`),
  CONSTRAINT `policy_exceptions_security_policies_ibfk_1` FOREIGN KEY (`policy_exception_id`) REFERENCES `policy_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `policy_exceptions_security_policies_ibfk_2` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.policy_exceptions_security_policies: ~0 rows (approximately)
/*!40000 ALTER TABLE `policy_exceptions_security_policies` DISABLE KEYS */;
/*!40000 ALTER TABLE `policy_exceptions_security_policies` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.policy_exceptions_third_parties
DROP TABLE IF EXISTS `policy_exceptions_third_parties`;
CREATE TABLE IF NOT EXISTS `policy_exceptions_third_parties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `policy_exception_id` int(11) NOT NULL,
  `third_party_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_policy_exceptions_third_parties_policy_exceptions` (`policy_exception_id`),
  KEY `FK_policy_exceptions_third_parties_third_parties` (`third_party_id`),
  CONSTRAINT `FK_policy_exceptions_third_parties_policy_exceptions` FOREIGN KEY (`policy_exception_id`) REFERENCES `policy_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_policy_exceptions_third_parties_third_parties` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.policy_exceptions_third_parties: ~0 rows (approximately)
/*!40000 ALTER TABLE `policy_exceptions_third_parties` DISABLE KEYS */;
/*!40000 ALTER TABLE `policy_exceptions_third_parties` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.policy_exception_classifications
DROP TABLE IF EXISTS `policy_exception_classifications`;
CREATE TABLE IF NOT EXISTS `policy_exception_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `policy_exception_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `policy_exception_id` (`policy_exception_id`),
  CONSTRAINT `policy_exception_classifications_ibfk_1` FOREIGN KEY (`policy_exception_id`) REFERENCES `policy_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.policy_exception_classifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `policy_exception_classifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `policy_exception_classifications` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.policy_users
DROP TABLE IF EXISTS `policy_users`;
CREATE TABLE IF NOT EXISTS `policy_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.policy_users: ~0 rows (approximately)
/*!40000 ALTER TABLE `policy_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `policy_users` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.processes
DROP TABLE IF EXISTS `processes`;
CREATE TABLE IF NOT EXISTS `processes` (
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.processes: ~0 rows (approximately)
/*!40000 ALTER TABLE `processes` DISABLE KEYS */;
/*!40000 ALTER TABLE `processes` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.projects
DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
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

-- Dumping data for table e_merge_enterprise.projects: ~0 rows (approximately)
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.projects_risks
DROP TABLE IF EXISTS `projects_risks`;
CREATE TABLE IF NOT EXISTS `projects_risks` (
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

-- Dumping data for table e_merge_enterprise.projects_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `projects_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.projects_security_policies
DROP TABLE IF EXISTS `projects_security_policies`;
CREATE TABLE IF NOT EXISTS `projects_security_policies` (
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

-- Dumping data for table e_merge_enterprise.projects_security_policies: ~0 rows (approximately)
/*!40000 ALTER TABLE `projects_security_policies` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_security_policies` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.projects_security_services
DROP TABLE IF EXISTS `projects_security_services`;
CREATE TABLE IF NOT EXISTS `projects_security_services` (
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

-- Dumping data for table e_merge_enterprise.projects_security_services: ~0 rows (approximately)
/*!40000 ALTER TABLE `projects_security_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_security_services` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.projects_security_service_audit_improvements
DROP TABLE IF EXISTS `projects_security_service_audit_improvements`;
CREATE TABLE IF NOT EXISTS `projects_security_service_audit_improvements` (
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

-- Dumping data for table e_merge_enterprise.projects_security_service_audit_improvements: ~0 rows (approximately)
/*!40000 ALTER TABLE `projects_security_service_audit_improvements` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_security_service_audit_improvements` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.projects_third_party_risks
DROP TABLE IF EXISTS `projects_third_party_risks`;
CREATE TABLE IF NOT EXISTS `projects_third_party_risks` (
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

-- Dumping data for table e_merge_enterprise.projects_third_party_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `projects_third_party_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects_third_party_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.project_achievements
DROP TABLE IF EXISTS `project_achievements`;
CREATE TABLE IF NOT EXISTS `project_achievements` (
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

-- Dumping data for table e_merge_enterprise.project_achievements: ~0 rows (approximately)
/*!40000 ALTER TABLE `project_achievements` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_achievements` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.project_expenses
DROP TABLE IF EXISTS `project_expenses`;
CREATE TABLE IF NOT EXISTS `project_expenses` (
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

-- Dumping data for table e_merge_enterprise.project_expenses: ~0 rows (approximately)
/*!40000 ALTER TABLE `project_expenses` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_expenses` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.project_overtime_graphs
DROP TABLE IF EXISTS `project_overtime_graphs`;
CREATE TABLE IF NOT EXISTS `project_overtime_graphs` (
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

-- Dumping data for table e_merge_enterprise.project_overtime_graphs: ~0 rows (approximately)
/*!40000 ALTER TABLE `project_overtime_graphs` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_overtime_graphs` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.project_statuses
DROP TABLE IF EXISTS `project_statuses`;
CREATE TABLE IF NOT EXISTS `project_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.project_statuses: ~3 rows (approximately)
/*!40000 ALTER TABLE `project_statuses` DISABLE KEYS */;
INSERT INTO `project_statuses` (`id`, `name`) VALUES
	(1, 'Planned'),
	(2, 'Ongoing'),
	(3, 'Completed');
/*!40000 ALTER TABLE `project_statuses` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.reviews
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(150) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `planned_date` date NOT NULL,
  `actual_date` date DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_reviews_users` (`user_id`),
  CONSTRAINT `FK_reviews_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.reviews: ~0 rows (approximately)
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risks
DROP TABLE IF EXISTS `risks`;
CREATE TABLE IF NOT EXISTS `risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `threats` text NOT NULL,
  `vulnerabilities` text NOT NULL,
  `residual_score` int(11) NOT NULL,
  `risk_score` int(11) NOT NULL,
  `residual_risk` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `guardian_id` int(11) NOT NULL,
  `review` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `exceptions_issues` int(1) NOT NULL DEFAULT '0',
  `controls_issues` int(1) NOT NULL DEFAULT '0',
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

-- Dumping data for table e_merge_enterprise.risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risks_security_incidents
DROP TABLE IF EXISTS `risks_security_incidents`;
CREATE TABLE IF NOT EXISTS `risks_security_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `security_incident_id` int(11) NOT NULL,
  `risk_type` enum('asset-risk','third-party-risk','business-risk') NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_incident_id` (`security_incident_id`),
  CONSTRAINT `risks_security_incidents_ibfk_2` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risks_security_incidents: ~0 rows (approximately)
/*!40000 ALTER TABLE `risks_security_incidents` DISABLE KEYS */;
/*!40000 ALTER TABLE `risks_security_incidents` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risks_security_policies
DROP TABLE IF EXISTS `risks_security_policies`;
CREATE TABLE IF NOT EXISTS `risks_security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `security_policy_id` int(11) NOT NULL,
  `document_type` enum('procedure','policy','standard') NOT NULL,
  `risk_type` enum('asset-risk','third-party-risk','business-risk') NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_policy_id` (`security_policy_id`),
  CONSTRAINT `risks_security_policies_ibfk_2` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risks_security_policies: ~0 rows (approximately)
/*!40000 ALTER TABLE `risks_security_policies` DISABLE KEYS */;
/*!40000 ALTER TABLE `risks_security_policies` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risks_security_services
DROP TABLE IF EXISTS `risks_security_services`;
CREATE TABLE IF NOT EXISTS `risks_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_id` (`risk_id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `risks_security_services_ibfk_1` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risks_security_services_ibfk_2` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risks_security_services: ~0 rows (approximately)
/*!40000 ALTER TABLE `risks_security_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `risks_security_services` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risks_threats
DROP TABLE IF EXISTS `risks_threats`;
CREATE TABLE IF NOT EXISTS `risks_threats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `threat_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_id` (`risk_id`),
  KEY `threat_id` (`threat_id`),
  CONSTRAINT `risks_threats_ibfk_1` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risks_threats_ibfk_2` FOREIGN KEY (`threat_id`) REFERENCES `threats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risks_threats: ~0 rows (approximately)
/*!40000 ALTER TABLE `risks_threats` DISABLE KEYS */;
/*!40000 ALTER TABLE `risks_threats` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risks_vulnerabilities
DROP TABLE IF EXISTS `risks_vulnerabilities`;
CREATE TABLE IF NOT EXISTS `risks_vulnerabilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `vulnerability_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_id` (`risk_id`),
  KEY `vulnerability_id` (`vulnerability_id`),
  CONSTRAINT `risks_vulnerabilities_ibfk_1` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risks_vulnerabilities_ibfk_2` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risks_vulnerabilities: ~0 rows (approximately)
/*!40000 ALTER TABLE `risks_vulnerabilities` DISABLE KEYS */;
/*!40000 ALTER TABLE `risks_vulnerabilities` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risk_classifications
DROP TABLE IF EXISTS `risk_classifications`;
CREATE TABLE IF NOT EXISTS `risk_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `criteria` text NOT NULL,
  `value` int(11) DEFAULT NULL,
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

-- Dumping data for table e_merge_enterprise.risk_classifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `risk_classifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_classifications` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risk_classifications_risks
DROP TABLE IF EXISTS `risk_classifications_risks`;
CREATE TABLE IF NOT EXISTS `risk_classifications_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_classification_id` int(11) NOT NULL,
  `risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_classification_id` (`risk_classification_id`),
  KEY `risk_id` (`risk_id`),
  CONSTRAINT `risk_classifications_risks_ibfk_1` FOREIGN KEY (`risk_classification_id`) REFERENCES `risk_classifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risk_classifications_risks_ibfk_2` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risk_classifications_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `risk_classifications_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_classifications_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risk_classifications_third_party_risks
DROP TABLE IF EXISTS `risk_classifications_third_party_risks`;
CREATE TABLE IF NOT EXISTS `risk_classifications_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_classification_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_classification_id` (`risk_classification_id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  CONSTRAINT `risk_classifications_third_party_risks_ibfk_1` FOREIGN KEY (`risk_classification_id`) REFERENCES `risk_classifications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risk_classifications_third_party_risks_ibfk_2` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risk_classifications_third_party_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `risk_classifications_third_party_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_classifications_third_party_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risk_classification_types
DROP TABLE IF EXISTS `risk_classification_types`;
CREATE TABLE IF NOT EXISTS `risk_classification_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `risk_classification_count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risk_classification_types: ~0 rows (approximately)
/*!40000 ALTER TABLE `risk_classification_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_classification_types` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risk_exceptions
DROP TABLE IF EXISTS `risk_exceptions`;
CREATE TABLE IF NOT EXISTS `risk_exceptions` (
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

-- Dumping data for table e_merge_enterprise.risk_exceptions: ~0 rows (approximately)
/*!40000 ALTER TABLE `risk_exceptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_exceptions` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risk_exceptions_risks
DROP TABLE IF EXISTS `risk_exceptions_risks`;
CREATE TABLE IF NOT EXISTS `risk_exceptions_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_id` int(11) NOT NULL,
  `risk_exception_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `risk_id` (`risk_id`),
  KEY `risk_exception_id` (`risk_exception_id`),
  CONSTRAINT `risk_exceptions_risks_ibfk_1` FOREIGN KEY (`risk_id`) REFERENCES `risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `risk_exceptions_risks_ibfk_2` FOREIGN KEY (`risk_exception_id`) REFERENCES `risk_exceptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risk_exceptions_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `risk_exceptions_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_exceptions_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risk_exceptions_third_party_risks
DROP TABLE IF EXISTS `risk_exceptions_third_party_risks`;
CREATE TABLE IF NOT EXISTS `risk_exceptions_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_exception_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risk_exceptions_third_party_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `risk_exceptions_third_party_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_exceptions_third_party_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risk_mitigation_strategies
DROP TABLE IF EXISTS `risk_mitigation_strategies`;
CREATE TABLE IF NOT EXISTS `risk_mitigation_strategies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risk_mitigation_strategies: ~4 rows (approximately)
/*!40000 ALTER TABLE `risk_mitigation_strategies` DISABLE KEYS */;
INSERT INTO `risk_mitigation_strategies` (`id`, `name`) VALUES
	(1, 'Accept'),
	(2, 'Avoid'),
	(3, 'Mitigate'),
	(4, 'Transfer');
/*!40000 ALTER TABLE `risk_mitigation_strategies` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.risk_overtime_graphs
DROP TABLE IF EXISTS `risk_overtime_graphs`;
CREATE TABLE IF NOT EXISTS `risk_overtime_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_count` int(11) NOT NULL,
  `risk_score` int(11) NOT NULL,
  `residual_score` int(11) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.risk_overtime_graphs: ~0 rows (approximately)
/*!40000 ALTER TABLE `risk_overtime_graphs` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_overtime_graphs` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.scopes
DROP TABLE IF EXISTS `scopes`;
CREATE TABLE IF NOT EXISTS `scopes` (
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

-- Dumping data for table e_merge_enterprise.scopes: ~0 rows (approximately)
/*!40000 ALTER TABLE `scopes` DISABLE KEYS */;
/*!40000 ALTER TABLE `scopes` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_incidents
DROP TABLE IF EXISTS `security_incidents`;
CREATE TABLE IF NOT EXISTS `security_incidents` (
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
  CONSTRAINT `security_incidents_ibfk_3` FOREIGN KEY (`security_incident_classification_id`) REFERENCES `security_incident_classifications__old` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `security_incidents_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `security_incidents_ibfk_6` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_incidents: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_incidents` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_incidents` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_incidents_security_services
DROP TABLE IF EXISTS `security_incidents_security_services`;
CREATE TABLE IF NOT EXISTS `security_incidents_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_incident_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_incident_id` (`security_incident_id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `security_incidents_security_services_ibfk_1` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_incidents_security_services_ibfk_2` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_incidents_security_services: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_incidents_security_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_incidents_security_services` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_incidents_third_parties
DROP TABLE IF EXISTS `security_incidents_third_parties`;
CREATE TABLE IF NOT EXISTS `security_incidents_third_parties` (
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

-- Dumping data for table e_merge_enterprise.security_incidents_third_parties: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_incidents_third_parties` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_incidents_third_parties` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_incident_classifications
DROP TABLE IF EXISTS `security_incident_classifications`;
CREATE TABLE IF NOT EXISTS `security_incident_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_incident_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_incident_id` (`security_incident_id`),
  CONSTRAINT `security_incident_classifications_ibfk_1` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_incident_classifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_incident_classifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_incident_classifications` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_incident_classifications__old
DROP TABLE IF EXISTS `security_incident_classifications__old`;
CREATE TABLE IF NOT EXISTS `security_incident_classifications__old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `criteria` text NOT NULL,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_incident_classifications__old: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_incident_classifications__old` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_incident_classifications__old` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_incident_stages
DROP TABLE IF EXISTS `security_incident_stages`;
CREATE TABLE IF NOT EXISTS `security_incident_stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text,
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_incident_stages: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_incident_stages` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_incident_stages` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_incident_stages_security_incidents
DROP TABLE IF EXISTS `security_incident_stages_security_incidents`;
CREATE TABLE IF NOT EXISTS `security_incident_stages_security_incidents` (
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
  CONSTRAINT `FK_security_incident_stages_security_incidents` FOREIGN KEY (`security_incident_id`) REFERENCES `security_incidents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_security_incident_stages_security_incident_stages` FOREIGN KEY (`security_incident_stage_id`) REFERENCES `security_incident_stages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_incident_stages_security_incidents: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_incident_stages_security_incidents` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_incident_stages_security_incidents` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_incident_statuses
DROP TABLE IF EXISTS `security_incident_statuses`;
CREATE TABLE IF NOT EXISTS `security_incident_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_incident_statuses: ~2 rows (approximately)
/*!40000 ALTER TABLE `security_incident_statuses` DISABLE KEYS */;
INSERT INTO `security_incident_statuses` (`id`, `name`) VALUES
	(2, 'Ongoing'),
	(3, 'Closed');
/*!40000 ALTER TABLE `security_incident_statuses` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_policies
DROP TABLE IF EXISTS `security_policies`;
CREATE TABLE IF NOT EXISTS `security_policies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `index` varchar(100) NOT NULL,
  `short_description` varchar(150) NOT NULL,
  `description` text,
  `use_attachments` int(1) NOT NULL DEFAULT '0',
  `document_type` enum('policy','standard','procedure') NOT NULL,
  `version` varchar(50) NOT NULL,
  `published_date` date NOT NULL,
  `next_review_date` date NOT NULL,
  `permission` enum('public','private','logged') NOT NULL,
  `ldap_connector_id` int(11) DEFAULT NULL,
  `asset_label_id` int(11) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0-draft, 1-released',
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
  CONSTRAINT `security_policies_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_policies_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_policies_ibfk_3` FOREIGN KEY (`asset_label_id`) REFERENCES `asset_labels` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_policies: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_policies` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_policies` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_policies_related
DROP TABLE IF EXISTS `security_policies_related`;
CREATE TABLE IF NOT EXISTS `security_policies_related` (
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

-- Dumping data for table e_merge_enterprise.security_policies_related: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_policies_related` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_policies_related` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_policies_security_services
DROP TABLE IF EXISTS `security_policies_security_services`;
CREATE TABLE IF NOT EXISTS `security_policies_security_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_policy_id` int(11) NOT NULL,
  `security_service_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_policy_id` (`security_policy_id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `security_policies_security_services_ibfk_1` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_policies_security_services_ibfk_2` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_policies_security_services: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_policies_security_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_policies_security_services` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_policies_users
DROP TABLE IF EXISTS `security_policies_users`;
CREATE TABLE IF NOT EXISTS `security_policies_users` (
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

-- Dumping data for table e_merge_enterprise.security_policies_users: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_policies_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_policies_users` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_policy_ldap_groups
DROP TABLE IF EXISTS `security_policy_ldap_groups`;
CREATE TABLE IF NOT EXISTS `security_policy_ldap_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_policy_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_policy_id` (`security_policy_id`),
  CONSTRAINT `security_policy_ldap_groups_ibfk_1` FOREIGN KEY (`security_policy_id`) REFERENCES `security_policies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_policy_ldap_groups: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_policy_ldap_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_policy_ldap_groups` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_policy_reviews
DROP TABLE IF EXISTS `security_policy_reviews`;
CREATE TABLE IF NOT EXISTS `security_policy_reviews` (
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

-- Dumping data for table e_merge_enterprise.security_policy_reviews: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_policy_reviews` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_policy_reviews` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_services
DROP TABLE IF EXISTS `security_services`;
CREATE TABLE IF NOT EXISTS `security_services` (
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_services: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_services` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_services` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_services_service_contracts
DROP TABLE IF EXISTS `security_services_service_contracts`;
CREATE TABLE IF NOT EXISTS `security_services_service_contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `service_contract_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  KEY `service_contract_id` (`service_contract_id`),
  CONSTRAINT `security_services_service_contracts_ibfk_1` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_services_service_contracts_ibfk_2` FOREIGN KEY (`service_contract_id`) REFERENCES `service_contracts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_services_service_contracts: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_services_service_contracts` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_services_service_contracts` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_services_third_party_risks
DROP TABLE IF EXISTS `security_services_third_party_risks`;
CREATE TABLE IF NOT EXISTS `security_services_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `third_party_risk_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  CONSTRAINT `security_services_third_party_risks_ibfk_1` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `security_services_third_party_risks_ibfk_2` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_services_third_party_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_services_third_party_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_services_third_party_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_services_users
DROP TABLE IF EXISTS `security_services_users`;
CREATE TABLE IF NOT EXISTS `security_services_users` (
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

-- Dumping data for table e_merge_enterprise.security_services_users: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_services_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_services_users` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_service_audits
DROP TABLE IF EXISTS `security_service_audits`;
CREATE TABLE IF NOT EXISTS `security_service_audits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `audit_metric_description` text NOT NULL,
  `audit_success_criteria` text NOT NULL,
  `result` int(1) DEFAULT NULL COMMENT 'null-not defined, 0-fail, 1-pass',
  `result_description` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `planned_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_service_audits: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_service_audits` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_service_audits` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_service_audit_dates
DROP TABLE IF EXISTS `security_service_audit_dates`;
CREATE TABLE IF NOT EXISTS `security_service_audit_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `security_service_audit_dates_ibfk_1` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_service_audit_dates: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_service_audit_dates` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_service_audit_dates` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_service_audit_improvements
DROP TABLE IF EXISTS `security_service_audit_improvements`;
CREATE TABLE IF NOT EXISTS `security_service_audit_improvements` (
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

-- Dumping data for table e_merge_enterprise.security_service_audit_improvements: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_service_audit_improvements` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_service_audit_improvements` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_service_classifications
DROP TABLE IF EXISTS `security_service_classifications`;
CREATE TABLE IF NOT EXISTS `security_service_classifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `security_service_classifications_ibfk_1` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_service_classifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_service_classifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_service_classifications` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_service_maintenances
DROP TABLE IF EXISTS `security_service_maintenances`;
CREATE TABLE IF NOT EXISTS `security_service_maintenances` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `task` text NOT NULL,
  `task_conclusion` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `planned_date` date NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `result` int(1) DEFAULT NULL COMMENT 'null-not defined, 0-fail, 1-pass',
  `workflow_owner_id` int(11) DEFAULT NULL,
  `workflow_status` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_owner_id` (`workflow_owner_id`),
  CONSTRAINT `security_service_maintenances_ibfk_1` FOREIGN KEY (`workflow_owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_service_maintenances: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_service_maintenances` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_service_maintenances` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_service_maintenance_dates
DROP TABLE IF EXISTS `security_service_maintenance_dates`;
CREATE TABLE IF NOT EXISTS `security_service_maintenance_dates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `security_service_id` int(11) NOT NULL,
  `day` int(2) NOT NULL,
  `month` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `security_service_id` (`security_service_id`),
  CONSTRAINT `security_service_maintenance_dates_ibfk_1` FOREIGN KEY (`security_service_id`) REFERENCES `security_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_service_maintenance_dates: ~0 rows (approximately)
/*!40000 ALTER TABLE `security_service_maintenance_dates` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_service_maintenance_dates` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.security_service_types
DROP TABLE IF EXISTS `security_service_types`;
CREATE TABLE IF NOT EXISTS `security_service_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.security_service_types: ~2 rows (approximately)
/*!40000 ALTER TABLE `security_service_types` DISABLE KEYS */;
INSERT INTO `security_service_types` (`id`, `name`) VALUES
	(2, 'Design'),
	(4, 'Production');
/*!40000 ALTER TABLE `security_service_types` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.service_classifications
DROP TABLE IF EXISTS `service_classifications`;
CREATE TABLE IF NOT EXISTS `service_classifications` (
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

-- Dumping data for table e_merge_enterprise.service_classifications: ~0 rows (approximately)
/*!40000 ALTER TABLE `service_classifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_classifications` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.service_contracts
DROP TABLE IF EXISTS `service_contracts`;
CREATE TABLE IF NOT EXISTS `service_contracts` (
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

-- Dumping data for table e_merge_enterprise.service_contracts: ~0 rows (approximately)
/*!40000 ALTER TABLE `service_contracts` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_contracts` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.settings
DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.settings: ~18 rows (approximately)
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` (`id`, `active`, `name`, `variable`, `value`, `default_value`, `values`, `type`, `options`, `hidden`, `required`, `setting_group_slug`, `setting_type`, `order`, `modified`, `created`) VALUES
	(2, 1, 'DB Schema Version', 'DB_SCHEMA_VERSION', '2.0.4.000', NULL, NULL, 'text', NULL, 1, 0, NULL, 'constant', 0, '2015-01-24 00:00:00', '2015-01-24 00:00:00'),
	(3, 1, 'Client ID', 'CLIENT_ID', NULL, NULL, NULL, 'text', NULL, 1, 0, NULL, 'constant', 0, '2015-01-24 00:00:00', '2015-01-24 00:00:00'),
	(4, 1, 'Bruteforce wrong logins', 'BRUTEFORCE_WRONG_LOGINS', '3', NULL, NULL, 'number', '{"min":1,"max":10,"step":1}', 0, 0, 'BFP', 'constant', 0, '2015-05-21 09:26:03', '0000-00-00 00:00:00'),
	(5, 1, 'Bruteforce second ago', 'BRUTEFORCE_SECONDS_AGO', '60', NULL, NULL, 'number', '{"min":10,"max":120,"step":1}', 0, 0, 'BFP', 'constant', 0, '2015-05-21 09:26:03', '0000-00-00 00:00:00'),
	(10, 1, 'Default currency', 'DEFAULT_CURRENCY', 'EUR', NULL, 'configDefaultCurrency', 'select', '{"AUD":"AUD","CAD":"CAD","USD":"USD","EUR":"EUR","GBP":"GBP","JPY":"JPY"}', 0, 0, 'CUE', 'config', 0, '2015-09-03 14:14:02', '0000-00-00 00:00:00'),
	(11, 1, 'Type', 'SMTP_USE', '0', NULL, NULL, 'select', '{"0":"Mail","1":"SMTP"}', 0, 0, 'MAILCNF', 'constant', 0, '2015-09-03 14:15:38', '0000-00-00 00:00:00'),
	(12, 1, 'SMTP host', 'SMTP_HOST', '', NULL, NULL, 'text', NULL, 0, 0, 'MAILCNF', 'constant', 0, '2015-09-03 14:15:38', '0000-00-00 00:00:00'),
	(13, 1, 'SMTP user', 'SMTP_USER', 'mioo', NULL, NULL, 'text', NULL, 0, 0, 'MAILCNF', 'constant', 0, '2015-09-03 14:15:38', '0000-00-00 00:00:00'),
	(14, 1, 'SMTP password', 'SMTP_PWD', 'michal', NULL, NULL, 'password', NULL, 0, 0, 'MAILCNF', 'constant', 0, '2015-09-03 14:15:38', '0000-00-00 00:00:00'),
	(15, 1, 'SMTP timeout', 'SMTP_TIMEOUT', '60', NULL, NULL, 'number', '{"min":1,"max":120,"step":1}', 0, 0, 'MAILCNF', 'constant', 0, '2015-09-03 14:15:38', '0000-00-00 00:00:00'),
	(16, 1, 'SMTP port', 'SMTP_PORT', '', NULL, NULL, 'text', NULL, 0, 0, 'MAILCNF', 'constant', 0, '2015-09-03 14:15:38', '0000-00-00 00:00:00'),
	(18, 1, 'No reply Email', 'NO_REPLY_EMAIL', 'noreply@domain.org', NULL, NULL, 'text', NULL, 0, 0, 'MAILCNF', 'constant', 0, '2015-09-03 14:15:38', '0000-00-00 00:00:00'),
	(19, 1, 'Cron security key', 'CRON_SECURITY_KEY', 'egkrjng328525798', NULL, NULL, 'text', NULL, 0, 0, 'SECKEY', 'constant', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(20, 1, 'Bruteforce ban from minutes', 'BRUTEFORCE_BAN_FOR_MINUTES', '5', NULL, NULL, 'number', '{"min":1,"max":120,"step":1}', 0, 0, 'BFP', 'constant', 0, '2015-05-21 09:26:03', '0000-00-00 00:00:00'),
	(21, 1, 'Banners off', 'BANNERS_OFF', '1', NULL, NULL, 'checkbox', NULL, 0, 0, 'BANNER', 'constant', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(22, 1, 'Debug', 'DEBUG', '1', NULL, 'configDebug', 'checkbox', NULL, 0, 0, 'DEBUGCFG', 'config', 0, '2015-09-03 14:17:31', '0000-00-00 00:00:00'),
	(23, 1, 'Email Debug', 'EMAIL_DEBUG', '0', NULL, 'configEmailDebug', 'checkbox', NULL, 0, 0, 'DEBUGCFG', 'config', 0, '2015-09-03 14:17:31', '0000-00-00 00:00:00'),
	(24, 1, 'Risk Appetite', 'RISK_APPETITE', '1', NULL, NULL, 'number', '{"min":0,"max":9999,"step":1}', 0, 0, 'RISKAPPETITE', 'constant', 0, '2015-05-21 16:57:11', '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.setting_groups
DROP TABLE IF EXISTS `setting_groups`;
CREATE TABLE IF NOT EXISTS `setting_groups` (
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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.setting_groups: ~31 rows (approximately)
/*!40000 ALTER TABLE `setting_groups` DISABLE KEYS */;
INSERT INTO `setting_groups` (`id`, `slug`, `parent_slug`, `name`, `icon_code`, `notes`, `url`, `hidden`, `order`) VALUES
	(1, 'ACCESSLST', 'ACCESSMGT', 'Access Lists', NULL, NULL, '{"controller":"admin", "action":"acl", "0" :"aros", "1":"ajax_role_permissions"}', 0, 0),
	(2, 'ACCESSMGT', NULL, 'Access Management', 'icon-cog', NULL, NULL, 0, 0),
	(3, 'AUTH', 'ACCESSMGT', 'Authentication ', NULL, NULL, '{"controller":"ldapConnectors","action":"authentication"}', 0, 0),
	(4, 'BANNER', 'SEC', 'Banners', NULL, NULL, NULL, 0, 0),
	(5, 'BAR', 'DB', 'Backup & Restore', NULL, NULL, '{"controller":"backupRestore","action":"index"}', 0, 0),
	(6, 'BFP', 'SEC', 'Brute Force Protection', NULL, 'This setting allows you to protect the login page of eramba from being brute-force attacked.', NULL, 0, 0),
	(7, 'CUE', 'LOC', 'Currency', NULL, NULL, NULL, 0, 0),
	(8, 'DASH', NULL, 'Dashboard', 'icon-cog', NULL, NULL, 0, 0),
	(9, 'DASHRESET', 'DASH', 'Reset Dashboards', NULL, NULL, '{"controller":"settings","action":"resetDashboards"}', 0, 0),
	(10, 'DB', NULL, 'Database', 'icon-cog', NULL, NULL, 0, 0),
	(11, 'DBCNF', 'DB', 'Database Configurations', NULL, NULL, NULL, 1, 0),
	(12, 'DBRESET', 'DB', 'Reset Database', NULL, NULL, '{"controller":"settings","action":"resetDatabase"}', 0, 0),
	(13, 'DEBUG', NULL, 'Debug Settings and Logs', 'icon-cog', NULL, NULL, 0, 0),
	(14, 'DEBUGCFG', 'DEBUG', 'Debug Config', NULL, NULL, NULL, 0, 0),
	(15, 'ERRORLOG', 'DEBUG', 'Error Log', NULL, NULL, '{"controller":"settings","action":"logs", "0":"error"}', 0, 0),
	(16, 'GROUP', 'ACCESSMGT', 'Groups ', NULL, NULL, '{"controller":"groups","action":"index"}', 0, 0),
	(17, 'LDAP', 'ACCESSMGT', 'LDAP Connectors', NULL, NULL, '{"controller":"ldapConnectors","action":"index"}', 0, 0),
	(18, 'LOC', NULL, 'Localization', 'icon-cog', NULL, NULL, 0, 0),
	(19, 'MAIL', NULL, 'Mail', 'icon-cog', NULL, NULL, 0, 0),
	(20, 'MAILCNF', 'MAIL', 'Mail Configurations', NULL, NULL, NULL, 0, 0),
	(21, 'MAILLOG', 'DEBUG', 'Email Log', NULL, NULL, '{"controller":"settings","action":"logs", "0":"email"}', 0, 0),
	(22, 'PRELOAD', 'DB', 'Pre-load the database with default databases', NULL, NULL, NULL, 1, 0),
	(23, 'RISK', NULL, 'Risk', 'icon-cog', NULL, NULL, 1, 0),
	(24, 'RISKAPPETITE', 'RISK', 'Risk appetite', NULL, NULL, NULL, 0, 0),
	(25, 'ROLES', 'ACCESSMGT', 'Roles', NULL, NULL, '{"controller":"scopes","action":"index"}', 0, 0),
	(26, 'SEC', NULL, 'Security', 'icon-cog', NULL, NULL, 0, 0),
	(27, 'SECKEY', 'SEC', 'Security KEY', NULL, NULL, NULL, 0, 0),
	(28, 'USER', 'ACCESSMGT', 'User Management', NULL, NULL, '{"controller":"users","action":"index"}', 0, 0),
	(29, 'CLRCACHE', 'DEBUG', 'Clear Cache', NULL, NULL, '{"controller":"settings","action":"deleteCache"}', 0, 0),
	(30, 'CLRACLCACHE', 'DEBUG', 'Clear ACL Cache', NULL, NULL, '{"controller":"settings","action":"deleteCache", "0":"acl"}', 0, 0),
	(31, 'LOGO', 'LOC', 'Custom Logo', NULL, NULL, '{"controller":"settings","action":"customLogo"}', 0, 0);
/*!40000 ALTER TABLE `setting_groups` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.suggestions
DROP TABLE IF EXISTS `suggestions`;
CREATE TABLE IF NOT EXISTS `suggestions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `suggestion` varchar(255) NOT NULL,
  `model` varchar(155) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.suggestions: ~0 rows (approximately)
/*!40000 ALTER TABLE `suggestions` DISABLE KEYS */;
/*!40000 ALTER TABLE `suggestions` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.system_records
DROP TABLE IF EXISTS `system_records`;
CREATE TABLE IF NOT EXISTS `system_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(70) NOT NULL,
  `model_nice` varchar(70) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `item` varchar(100) NOT NULL,
  `notes` text,
  `type` int(1) NOT NULL COMMENT '1-insert; 2-update; 3-delete, 4-login, 5-wrong login',
  `workflow_status` int(1) DEFAULT NULL,
  `workflow_comment` text,
  `ip` varchar(45) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `system_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.system_records: ~0 rows (approximately)
/*!40000 ALTER TABLE `system_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_records` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.tags
DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
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

-- Dumping data for table e_merge_enterprise.tags: ~0 rows (approximately)
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.third_parties
DROP TABLE IF EXISTS `third_parties`;
CREATE TABLE IF NOT EXISTS `third_parties` (
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

-- Dumping data for table e_merge_enterprise.third_parties: ~1 rows (approximately)
/*!40000 ALTER TABLE `third_parties` DISABLE KEYS */;
INSERT INTO `third_parties` (`id`, `name`, `description`, `third_party_type_id`, `security_incident_count`, `security_incident_open_count`, `service_contract_count`, `workflow_status`, `workflow_owner_id`, `_hidden`, `created`, `modified`) VALUES
	(1, 'None', '', NULL, 0, 0, 0, 0, NULL, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `third_parties` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.third_parties_third_party_risks
DROP TABLE IF EXISTS `third_parties_third_party_risks`;
CREATE TABLE IF NOT EXISTS `third_parties_third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_risk_id` int(11) NOT NULL,
  `third_party_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  KEY `third_party_id` (`third_party_id`),
  CONSTRAINT `third_parties_third_party_risks_ibfk_1` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `third_parties_third_party_risks_ibfk_2` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.third_parties_third_party_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `third_parties_third_party_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `third_parties_third_party_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.third_parties_users
DROP TABLE IF EXISTS `third_parties_users`;
CREATE TABLE IF NOT EXISTS `third_parties_users` (
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

-- Dumping data for table e_merge_enterprise.third_parties_users: ~0 rows (approximately)
/*!40000 ALTER TABLE `third_parties_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `third_parties_users` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.third_party_audit_overtime_graphs
DROP TABLE IF EXISTS `third_party_audit_overtime_graphs`;
CREATE TABLE IF NOT EXISTS `third_party_audit_overtime_graphs` (
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

-- Dumping data for table e_merge_enterprise.third_party_audit_overtime_graphs: ~0 rows (approximately)
/*!40000 ALTER TABLE `third_party_audit_overtime_graphs` DISABLE KEYS */;
/*!40000 ALTER TABLE `third_party_audit_overtime_graphs` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.third_party_incident_overtime_graphs
DROP TABLE IF EXISTS `third_party_incident_overtime_graphs`;
CREATE TABLE IF NOT EXISTS `third_party_incident_overtime_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_id` int(11) NOT NULL,
  `security_incident_count` int(11) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_id` (`third_party_id`),
  CONSTRAINT `third_party_incident_overtime_graphs_ibfk_1` FOREIGN KEY (`third_party_id`) REFERENCES `third_parties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.third_party_incident_overtime_graphs: ~0 rows (approximately)
/*!40000 ALTER TABLE `third_party_incident_overtime_graphs` DISABLE KEYS */;
/*!40000 ALTER TABLE `third_party_incident_overtime_graphs` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.third_party_overtime_graphs
DROP TABLE IF EXISTS `third_party_overtime_graphs`;
CREATE TABLE IF NOT EXISTS `third_party_overtime_graphs` (
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

-- Dumping data for table e_merge_enterprise.third_party_overtime_graphs: ~0 rows (approximately)
/*!40000 ALTER TABLE `third_party_overtime_graphs` DISABLE KEYS */;
/*!40000 ALTER TABLE `third_party_overtime_graphs` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.third_party_risks
DROP TABLE IF EXISTS `third_party_risks`;
CREATE TABLE IF NOT EXISTS `third_party_risks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `shared_information` text NOT NULL,
  `controlled` text NOT NULL,
  `threats` text NOT NULL,
  `vulnerabilities` text NOT NULL,
  `residual_score` int(11) NOT NULL,
  `risk_score` int(11) NOT NULL,
  `residual_risk` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `guardian_id` int(11) NOT NULL,
  `review` date NOT NULL,
  `expired` int(1) NOT NULL DEFAULT '0',
  `exceptions_issues` int(1) NOT NULL DEFAULT '0',
  `controls_issues` int(1) NOT NULL DEFAULT '0',
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

-- Dumping data for table e_merge_enterprise.third_party_risks: ~0 rows (approximately)
/*!40000 ALTER TABLE `third_party_risks` DISABLE KEYS */;
/*!40000 ALTER TABLE `third_party_risks` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.third_party_risks_threats
DROP TABLE IF EXISTS `third_party_risks_threats`;
CREATE TABLE IF NOT EXISTS `third_party_risks_threats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_risk_id` int(11) NOT NULL,
  `threat_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  KEY `threat_id` (`threat_id`),
  CONSTRAINT `third_party_risks_threats_ibfk_1` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `third_party_risks_threats_ibfk_2` FOREIGN KEY (`threat_id`) REFERENCES `threats` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.third_party_risks_threats: ~0 rows (approximately)
/*!40000 ALTER TABLE `third_party_risks_threats` DISABLE KEYS */;
/*!40000 ALTER TABLE `third_party_risks_threats` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.third_party_risks_vulnerabilities
DROP TABLE IF EXISTS `third_party_risks_vulnerabilities`;
CREATE TABLE IF NOT EXISTS `third_party_risks_vulnerabilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `third_party_risk_id` int(11) NOT NULL,
  `vulnerability_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `third_party_risk_id` (`third_party_risk_id`),
  KEY `vulnerability_id` (`vulnerability_id`),
  CONSTRAINT `third_party_risks_vulnerabilities_ibfk_1` FOREIGN KEY (`third_party_risk_id`) REFERENCES `third_party_risks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `third_party_risks_vulnerabilities_ibfk_2` FOREIGN KEY (`vulnerability_id`) REFERENCES `vulnerabilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.third_party_risks_vulnerabilities: ~0 rows (approximately)
/*!40000 ALTER TABLE `third_party_risks_vulnerabilities` DISABLE KEYS */;
/*!40000 ALTER TABLE `third_party_risks_vulnerabilities` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.third_party_risk_overtime_graphs
DROP TABLE IF EXISTS `third_party_risk_overtime_graphs`;
CREATE TABLE IF NOT EXISTS `third_party_risk_overtime_graphs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `risk_count` int(11) NOT NULL,
  `risk_score` int(11) NOT NULL,
  `residual_score` int(11) NOT NULL,
  `timestamp` varchar(45) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.third_party_risk_overtime_graphs: ~0 rows (approximately)
/*!40000 ALTER TABLE `third_party_risk_overtime_graphs` DISABLE KEYS */;
/*!40000 ALTER TABLE `third_party_risk_overtime_graphs` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.third_party_types
DROP TABLE IF EXISTS `third_party_types`;
CREATE TABLE IF NOT EXISTS `third_party_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.third_party_types: ~3 rows (approximately)
/*!40000 ALTER TABLE `third_party_types` DISABLE KEYS */;
INSERT INTO `third_party_types` (`id`, `name`) VALUES
	(1, 'Customers'),
	(2, 'Suppliers'),
	(3, 'Regulators');
/*!40000 ALTER TABLE `third_party_types` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.threats
DROP TABLE IF EXISTS `threats`;
CREATE TABLE IF NOT EXISTS `threats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.threats: ~28 rows (approximately)
/*!40000 ALTER TABLE `threats` DISABLE KEYS */;
INSERT INTO `threats` (`id`, `name`) VALUES
	(1, 'Intentional Complot'),
	(2, 'Pandemic Issues'),
	(3, 'Strikes'),
	(4, 'Unintentional Loss of Equipment'),
	(5, 'Intentional Theft of Equipment'),
	(6, 'Unintentional Loss of Information'),
	(7, 'Intentional Theft of Information'),
	(8, 'Remote Exploit'),
	(9, 'Abuse of Service'),
	(10, 'Web Application Attack'),
	(11, 'Network Attack'),
	(12, 'Sniffing'),
	(13, 'Phishing'),
	(14, 'Malware/Trojan Distribution'),
	(15, 'Viruses'),
	(16, 'Copyright Infrigment'),
	(17, 'Social Engineering'),
	(18, 'Natural Disasters'),
	(19, 'Fire'),
	(20, 'Flooding'),
	(21, 'Ilegal Infiltration'),
	(22, 'DOS Attack'),
	(23, 'Brute Force Attack'),
	(24, 'Tampering'),
	(25, 'Tunneling'),
	(26, 'Man in the Middle'),
	(27, 'Fraud'),
	(28, 'Other');
/*!40000 ALTER TABLE `threats` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.tickets
DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
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

-- Dumping data for table e_merge_enterprise.tickets: ~0 rows (approximately)
/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
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
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.users: ~1 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `surname`, `group_id`, `email`, `login`, `password`, `language`, `status`, `blocked`, `created`, `modified`) VALUES
	(1, 'Admin', 'Admin', 10, 'admin@eramba.org', 'admin', '$2a$10$WhVO3Jj4nFhCj6bToUOztun/oceKY6rT2db2bu430dW5/lU0w9KJ.', NULL, 1, 0, '2013-10-14 16:19:04', '2013-10-28 22:30:57');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.user_bans
DROP TABLE IF EXISTS `user_bans`;
CREATE TABLE IF NOT EXISTS `user_bans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `until` datetime NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_bans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.user_bans: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_bans` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_bans` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.vulnerabilities
DROP TABLE IF EXISTS `vulnerabilities`;
CREATE TABLE IF NOT EXISTS `vulnerabilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.vulnerabilities: ~31 rows (approximately)
/*!40000 ALTER TABLE `vulnerabilities` DISABLE KEYS */;
INSERT INTO `vulnerabilities` (`id`, `name`) VALUES
	(1, 'Lack of Information'),
	(2, 'Lack of Integrity Checks'),
	(3, 'Lack of Logs'),
	(4, 'No Change Management'),
	(5, 'Weak CheckOut Procedures'),
	(6, 'Supplier Failure'),
	(7, 'Lack of alternative Power Sources'),
	(8, 'Lack of Physical Guards'),
	(9, 'Lack of Patching'),
	(10, 'Web Application Vulnerabilities'),
	(11, 'Lack of CCTV'),
	(12, 'Lack of Movement Sensors'),
	(13, 'Lack of Procedures'),
	(14, 'Lack of Network Controls'),
	(15, 'Lack of Strong Authentication'),
	(16, 'Lack of Encryption in Motion'),
	(17, 'Lack of Encryption at Rest'),
	(18, 'Creeping Accounts'),
	(19, 'Hardware Malfunction'),
	(20, 'Software Malfunction'),
	(21, 'Lack of Fire Extinguishers'),
	(22, 'Lack of alternative exit doors'),
	(23, 'Weak Passwords'),
	(24, 'Weak Awareness'),
	(25, 'Missing Configuration Standards'),
	(26, 'Open Network Ports'),
	(27, 'Reputational Issues'),
	(28, 'Seismic Areas'),
	(29, 'Prone to Natural Disasters Area'),
	(30, 'Flood Prone Areas'),
	(31, 'Other');
/*!40000 ALTER TABLE `vulnerabilities` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflows
DROP TABLE IF EXISTS `workflows`;
CREATE TABLE IF NOT EXISTS `workflows` (
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
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.workflows: ~38 rows (approximately)
/*!40000 ALTER TABLE `workflows` DISABLE KEYS */;
INSERT INTO `workflows` (`id`, `model`, `name`, `notifications`, `parent_id`, `created`, `modified`) VALUES
	(1, 'SecurityIncident', 'Security Incidents', 1, NULL, '2014-10-15 00:00:00', '2014-10-15 00:00:00'),
	(2, 'BusinessUnit', 'Business Units', 1, NULL, '2014-10-15 00:00:00', '2014-12-17 21:51:38'),
	(3, 'Legal', 'Legals', 1, NULL, '2014-10-15 00:00:00', '2014-12-20 12:33:49'),
	(4, 'ThirdParty', 'Third Parties', 0, NULL, '2014-10-15 00:00:00', '2014-12-21 00:22:40'),
	(5, 'Process', 'Processes', 0, 2, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(6, 'Asset', 'Assets', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(7, 'AssetClassification', 'Asset Classifications', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(8, 'AssetLabel', 'Asset Labeling', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(9, 'RiskClassification', 'Risk Classifications', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(10, 'RiskException', 'Risk Exceptions', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(11, 'Risk', 'Risks', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(12, 'ThirdPartyRisk', 'Third Party Risks', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(13, 'BusinessContinuity', 'Business Continuities', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(14, 'SecurityService', 'Security Services', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(15, 'ServiceContract', 'Service Contracts', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(16, 'ServiceClassification', 'Service Classifications', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(17, 'BusinessContinuityPlan', 'Business Continuity Plans', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(18, 'SecurityPolicy', 'Security Policies', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(19, 'PolicyException', 'Policy Exceptions', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(20, 'Project', 'Projects', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(22, 'ProjectAchievement', 'Project Achievements', 0, 20, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(23, 'ProjectExpense', 'Project Expenses', 0, 20, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(24, 'SecurityServiceAudit', 'Security Service Audits', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(25, 'SecurityServiceMaintenance', 'Security Service Maintenances', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(26, 'CompliancePackageItem', 'Compliance Package Items', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(27, 'DataAsset', 'Data Assets', 0, 6, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(28, 'ComplianceManagement', 'Compliance Managements', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(29, 'BusinessContinuityPlanAudit', 'Business Continuity Plan Audits', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(30, 'ComplianceFinding', 'Compliance Findings', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(31, 'BusinessContinuityTask', 'Business Continuity Tasks', 0, NULL, '2014-10-15 00:00:00', '2014-12-24 02:11:56'),
	(32, 'LdapConnector', 'LDAP Connectors', 0, NULL, '2014-10-15 00:00:00', '2015-01-31 21:03:53'),
	(33, 'SecurityPolicyReview', 'Security Policy Reviews', 0, NULL, '2015-06-21 00:00:00', '2015-06-21 00:00:00'),
	(34, 'RiskReview', 'Risk Reviews', 0, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(35, 'ThirdPartyRiskReview', 'ThirdPartyRisk Reviews', 0, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(36, 'BusinessContinuityReview', 'BusinessContinuity Reviews', 0, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(37, 'AssetReview', 'Asset Reviews', 0, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(38, 'SecurityIncidentStage', 'Security Incident Stage', 0, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
	(39, 'SecurityIncidentStagesSecurityIncident', 'Security Incident Stages Security Incident', 0, 39, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
/*!40000 ALTER TABLE `workflows` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflows_all_approver_items
DROP TABLE IF EXISTS `workflows_all_approver_items`;
CREATE TABLE IF NOT EXISTS `workflows_all_approver_items` (
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

-- Dumping data for table e_merge_enterprise.workflows_all_approver_items: ~0 rows (approximately)
/*!40000 ALTER TABLE `workflows_all_approver_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflows_all_approver_items` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflows_all_validator_items
DROP TABLE IF EXISTS `workflows_all_validator_items`;
CREATE TABLE IF NOT EXISTS `workflows_all_validator_items` (
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

-- Dumping data for table e_merge_enterprise.workflows_all_validator_items: ~0 rows (approximately)
/*!40000 ALTER TABLE `workflows_all_validator_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflows_all_validator_items` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflows_approvers
DROP TABLE IF EXISTS `workflows_approvers`;
CREATE TABLE IF NOT EXISTS `workflows_approvers` (
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

-- Dumping data for table e_merge_enterprise.workflows_approvers: ~0 rows (approximately)
/*!40000 ALTER TABLE `workflows_approvers` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflows_approvers` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflows_approver_scopes
DROP TABLE IF EXISTS `workflows_approver_scopes`;
CREATE TABLE IF NOT EXISTS `workflows_approver_scopes` (
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

-- Dumping data for table e_merge_enterprise.workflows_approver_scopes: ~0 rows (approximately)
/*!40000 ALTER TABLE `workflows_approver_scopes` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflows_approver_scopes` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflows_custom_approvers
DROP TABLE IF EXISTS `workflows_custom_approvers`;
CREATE TABLE IF NOT EXISTS `workflows_custom_approvers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `custom_identifier` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.workflows_custom_approvers: ~0 rows (approximately)
/*!40000 ALTER TABLE `workflows_custom_approvers` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflows_custom_approvers` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflows_custom_validators
DROP TABLE IF EXISTS `workflows_custom_validators`;
CREATE TABLE IF NOT EXISTS `workflows_custom_validators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) NOT NULL,
  `custom_identifier` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.workflows_custom_validators: ~0 rows (approximately)
/*!40000 ALTER TABLE `workflows_custom_validators` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflows_custom_validators` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflows_validators
DROP TABLE IF EXISTS `workflows_validators`;
CREATE TABLE IF NOT EXISTS `workflows_validators` (
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

-- Dumping data for table e_merge_enterprise.workflows_validators: ~0 rows (approximately)
/*!40000 ALTER TABLE `workflows_validators` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflows_validators` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflows_validator_scopes
DROP TABLE IF EXISTS `workflows_validator_scopes`;
CREATE TABLE IF NOT EXISTS `workflows_validator_scopes` (
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

-- Dumping data for table e_merge_enterprise.workflows_validator_scopes: ~0 rows (approximately)
/*!40000 ALTER TABLE `workflows_validator_scopes` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflows_validator_scopes` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflow_acknowledgements
DROP TABLE IF EXISTS `workflow_acknowledgements`;
CREATE TABLE IF NOT EXISTS `workflow_acknowledgements` (
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

-- Dumping data for table e_merge_enterprise.workflow_acknowledgements: ~0 rows (approximately)
/*!40000 ALTER TABLE `workflow_acknowledgements` DISABLE KEYS */;
/*!40000 ALTER TABLE `workflow_acknowledgements` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflow_items
DROP TABLE IF EXISTS `workflow_items`;
CREATE TABLE IF NOT EXISTS `workflow_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(255) NOT NULL,
  `foreign_key` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.workflow_items: ~21 rows (approximately)
/*!40000 ALTER TABLE `workflow_items` DISABLE KEYS */;
INSERT INTO `workflow_items` (`id`, `model`, `foreign_key`, `owner_id`, `status`, `created`, `modified`) VALUES
	(1, 'LdapConnector', 2, 1, 0, '2015-08-01 20:24:59', '2015-08-01 20:24:59'),
	(2, 'LdapConnector', 1, 1, 0, '2015-08-06 12:43:23', '2015-08-06 12:43:23'),
	(3, 'SecurityService', 1, 1, 0, '2015-08-16 01:27:04', '2015-08-16 10:51:21'),
	(4, 'SecurityServiceAudit', 1, 1, 0, '2015-08-16 01:27:45', '2015-08-16 10:49:59'),
	(5, 'Legal', 1, 1, 0, '2015-08-16 02:05:29', '2015-08-16 02:05:59'),
	(6, 'Legal', 6, 1, 0, '2015-08-16 02:29:23', '2015-08-16 05:05:15'),
	(7, 'Legal', 4, 1, 0, '2015-08-16 02:33:08', '2015-08-16 02:52:47'),
	(8, 'Legal', 9, 1, 0, '2015-08-16 05:25:30', '2015-08-16 05:25:30'),
	(9, 'Legal', 11, 1, 0, '2015-08-16 05:28:46', '2015-08-16 05:28:46'),
	(10, 'Legal', 13, 1, 0, '2015-08-16 09:53:00', '2015-08-16 11:16:26'),
	(11, 'SecurityServiceAudit', 2, 1, 0, '2015-08-16 10:07:28', '2015-08-16 10:07:28'),
	(12, 'SecurityServiceAudit', 6, 1, 0, '2015-08-16 10:52:30', '2015-08-16 10:53:34'),
	(13, 'SecurityServiceAudit', 4, 1, 0, '2015-08-16 10:52:57', '2015-08-16 10:52:57'),
	(14, 'Legal', 14, 1, 0, '2015-08-16 12:41:46', '2015-08-16 16:17:30'),
	(15, 'Process', 1, 1, 0, '2015-08-16 15:42:45', '2015-08-16 15:42:45'),
	(16, 'SecurityServiceAudit', 8, 1, 0, '2015-08-16 15:45:48', '2015-08-16 15:45:48'),
	(17, 'SecurityServiceAudit', 9, 1, 0, '2015-08-16 15:46:44', '2015-08-16 16:11:42'),
	(18, 'Asset', 1, 1, 0, '2015-08-16 18:04:44', '2015-08-16 18:09:35'),
	(19, 'BusinessUnit', 2, 1, 0, '2015-08-16 23:25:05', '2015-08-16 23:25:20'),
	(20, 'SecurityService', 2, 1, 0, '2015-08-16 23:31:52', '2015-08-16 23:32:06'),
	(21, 'SecurityServiceAudit', 7, 1, 0, '2015-08-16 23:33:00', '2015-08-16 23:33:00');
/*!40000 ALTER TABLE `workflow_items` ENABLE KEYS */;


-- Dumping structure for table e_merge_enterprise.workflow_logs
DROP TABLE IF EXISTS `workflow_logs`;
CREATE TABLE IF NOT EXISTS `workflow_logs` (
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
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- Dumping data for table e_merge_enterprise.workflow_logs: ~54 rows (approximately)
/*!40000 ALTER TABLE `workflow_logs` DISABLE KEYS */;
INSERT INTO `workflow_logs` (`id`, `model`, `foreign_key`, `user_id`, `status`, `ip`, `created`, `modified`) VALUES
	(1, 'LdapConnector', 2, 1, 0, '::1', '2015-08-01 20:24:59', '2015-08-01 20:24:59'),
	(2, 'LdapConnector', 1, 1, 0, '::1', '2015-08-06 12:43:23', '2015-08-06 12:43:23'),
	(3, 'SecurityService', 1, 1, 0, '::1', '2015-08-16 01:27:04', '2015-08-16 01:27:04'),
	(4, 'SecurityService', 1, 1, 0, '::1', '2015-08-16 01:27:22', '2015-08-16 01:27:22'),
	(5, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 01:27:45', '2015-08-16 01:27:45'),
	(6, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 01:59:46', '2015-08-16 01:59:46'),
	(7, 'Legal', 1, 1, 0, '::1', '2015-08-16 02:05:29', '2015-08-16 02:05:29'),
	(8, 'Legal', 1, 1, 0, '::1', '2015-08-16 02:05:59', '2015-08-16 02:05:59'),
	(9, 'Legal', 6, 1, 0, '::1', '2015-08-16 02:29:23', '2015-08-16 02:29:23'),
	(10, 'Legal', 6, 1, 0, '::1', '2015-08-16 02:32:25', '2015-08-16 02:32:25'),
	(11, 'Legal', 4, 1, 0, '::1', '2015-08-16 02:33:08', '2015-08-16 02:33:08'),
	(12, 'Legal', 4, 1, 0, '::1', '2015-08-16 02:52:47', '2015-08-16 02:52:47'),
	(13, 'Legal', 6, 1, 0, '::1', '2015-08-16 05:04:56', '2015-08-16 05:04:56'),
	(14, 'Legal', 6, 1, 0, '::1', '2015-08-16 05:05:15', '2015-08-16 05:05:15'),
	(15, 'Legal', 9, 1, 0, '::1', '2015-08-16 05:25:31', '2015-08-16 05:25:31'),
	(16, 'Legal', 11, 1, 0, '::1', '2015-08-16 05:28:46', '2015-08-16 05:28:46'),
	(17, 'Legal', 13, 1, 0, '::1', '2015-08-16 09:53:01', '2015-08-16 09:53:01'),
	(18, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 10:07:12', '2015-08-16 10:07:12'),
	(19, 'SecurityServiceAudit', 2, 1, 0, '::1', '2015-08-16 10:07:28', '2015-08-16 10:07:28'),
	(20, 'SecurityService', 1, 1, 0, '::1', '2015-08-16 10:08:31', '2015-08-16 10:08:31'),
	(21, 'SecurityService', 1, 1, 0, '::1', '2015-08-16 10:08:54', '2015-08-16 10:08:54'),
	(22, 'SecurityService', 1, 1, 0, '::1', '2015-08-16 10:09:38', '2015-08-16 10:09:38'),
	(23, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 10:10:44', '2015-08-16 10:10:44'),
	(24, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 10:12:21', '2015-08-16 10:12:21'),
	(25, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 10:13:08', '2015-08-16 10:13:08'),
	(26, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 10:23:41', '2015-08-16 10:23:41'),
	(27, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 10:24:44', '2015-08-16 10:24:44'),
	(28, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 10:33:41', '2015-08-16 10:33:41'),
	(29, 'SecurityService', 1, 1, 0, '::1', '2015-08-16 10:35:44', '2015-08-16 10:35:44'),
	(30, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 10:37:52', '2015-08-16 10:37:52'),
	(31, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 10:45:42', '2015-08-16 10:45:42'),
	(32, 'SecurityServiceAudit', 1, 1, 0, '::1', '2015-08-16 10:49:59', '2015-08-16 10:49:59'),
	(33, 'SecurityService', 1, 1, 0, '::1', '2015-08-16 10:51:21', '2015-08-16 10:51:21'),
	(34, 'SecurityServiceAudit', 6, 1, 0, '::1', '2015-08-16 10:52:30', '2015-08-16 10:52:30'),
	(35, 'SecurityServiceAudit', 4, 1, 0, '::1', '2015-08-16 10:52:57', '2015-08-16 10:52:57'),
	(36, 'SecurityServiceAudit', 6, 1, 0, '::1', '2015-08-16 10:53:28', '2015-08-16 10:53:28'),
	(37, 'SecurityServiceAudit', 6, 1, 0, '::1', '2015-08-16 10:53:34', '2015-08-16 10:53:34'),
	(38, 'Legal', 13, 1, 0, '::1', '2015-08-16 11:16:21', '2015-08-16 11:16:21'),
	(39, 'Legal', 13, 1, 0, '::1', '2015-08-16 11:16:26', '2015-08-16 11:16:26'),
	(40, 'Legal', 14, 1, 0, '::1', '2015-08-16 12:41:46', '2015-08-16 12:41:46'),
	(41, 'Process', 1, 1, 0, '::1', '2015-08-16 15:42:45', '2015-08-16 15:42:45'),
	(42, 'SecurityServiceAudit', 8, 1, 0, '::1', '2015-08-16 15:45:48', '2015-08-16 15:45:48'),
	(43, 'SecurityServiceAudit', 9, 1, 0, '::1', '2015-08-16 15:46:44', '2015-08-16 15:46:44'),
	(44, 'SecurityServiceAudit', 9, 1, 0, '::1', '2015-08-16 16:11:42', '2015-08-16 16:11:42'),
	(45, 'Legal', 14, 1, 0, '::1', '2015-08-16 16:16:46', '2015-08-16 16:16:46'),
	(46, 'Legal', 14, 1, 0, '::1', '2015-08-16 16:16:56', '2015-08-16 16:16:56'),
	(47, 'Legal', 14, 1, 0, '::1', '2015-08-16 16:17:30', '2015-08-16 16:17:30'),
	(48, 'Asset', 1, 1, 0, '::1', '2015-08-16 18:04:44', '2015-08-16 18:04:44'),
	(49, 'Asset', 1, 1, 0, '::1', '2015-08-16 18:09:35', '2015-08-16 18:09:35'),
	(50, 'BusinessUnit', 2, 1, 0, '::1', '2015-08-16 23:25:05', '2015-08-16 23:25:05'),
	(51, 'BusinessUnit', 2, 1, 0, '::1', '2015-08-16 23:25:20', '2015-08-16 23:25:20'),
	(52, 'SecurityService', 2, 1, 0, '::1', '2015-08-16 23:31:52', '2015-08-16 23:31:52'),
	(53, 'SecurityService', 2, 1, 0, '::1', '2015-08-16 23:32:06', '2015-08-16 23:32:06'),
	(54, 'SecurityServiceAudit', 7, 1, 0, '::1', '2015-08-16 23:33:00', '2015-08-16 23:33:00');
/*!40000 ALTER TABLE `workflow_logs` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
