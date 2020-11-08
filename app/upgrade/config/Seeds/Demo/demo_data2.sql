#
# SQL Export
# Created by Querious (1068)
# Created: May 6, 2017 at 10:32:01 AM GMT+2
# Encoding: Unicode (UTF-8)
#


SET @PREVIOUS_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
	(1,1,10);

SET FOREIGN_KEY_CHECKS = @PREVIOUS_FOREIGN_KEY_CHECKS;