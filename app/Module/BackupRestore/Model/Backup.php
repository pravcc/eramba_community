<?php
App::uses('BackupRestoreAppModel', 'BackupRestore.Model');

class Backup extends BackupRestoreAppModel {

    const FILES_DELETED = 1;
    const FILES_NOT_DELETED = 0;

    public $actsAs = array(
        'Containable',
        'HtmlPurifier.HtmlPurifier' => array(
            'config' => 'Strict',
            'fields' => array(
            )
        )
    );

    public function createRecord($sqlFile) {
        $this->create();

        return $this->save(array(
            'sql_file' => $sqlFile
        ));
    }

    public function markFilesAsDeleted($id) {
        return $this->updateAll(array('deleted_files' => self::FILES_DELETED), array(
            'id' => $id
        ));
    }
}