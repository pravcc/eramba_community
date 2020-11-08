<?php
use Phinx\Migration\AbstractMigration;

class ProgramIssueTypesMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        $result = $this->fetchAll("SELECT `id` FROM `program_issues` WHERE `issue_source` = 'external'");

        if (empty($result)) {
            return;
        }

        $issueIds = [];

        foreach ($result as $item) {
            $issueIds[] = $item['id'];
        }

        $this->query(sprintf("UPDATE `program_issue_types` SET `type` = (`type` + 20) WHERE `id` IN (%s)", implode(',', $issueIds)));
    }
}
