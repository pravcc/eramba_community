<?php
App::uses('File', 'Utility');
App::uses('Folder', 'Utility');

App::uses('BackupRestoreAppController', 'BackupRestore.Controller');

class BackupRestoreController extends BackupRestoreAppController {
    public $helpers = array( 'Html', 'Form' );
    public $components = array( 'Session' );

    public function beforeFilter() {
        parent::beforeFilter();
        // $this->Security->unlockedActions = array('index');
        // $this->Security->validatePost = true;
        // $this->Security->csrfCheck = false;

        if (
           ($this->request->is('post') || $this->request->is('put')) &&
           empty($_POST) && empty($_FILES)
        ) {
            $this->Security->csrfCheck = false;
        }
    }

    public function index()
    {
        $brFormName = 'BackupRestore';

        $this->Ajax->initModal('normal', __('Backup and Restore'));
        $this->Modals->addFooterButton(__('Restore Database'), [
            'class' => 'btn btn-danger',
            'data-yjs-request' => 'crud/submitForm',
            'data-yjs-event-on' => 'click',
            'data-yjs-datasource-url' => Router::url(['plugin' => 'backup_restore', 'controller' => 'backupRestore', 'action' => 'index']),
            'data-yjs-forms' => $brFormName,
            'data-yjs-target' => 'modal',
            'data-yjs-modal-id' => null
        ]);
        $this->Modals->addFooterButton(__('Prepare Files for Download'), [
            'class' => 'btn btn-success',
            'data-yjs-request' => 'crud/showForm',
            'data-yjs-event-on' => 'click',
            'data-yjs-datasource-url' => Router::url(['plugin' => 'backup_restore', 'controller' => 'backupRestore', 'action' => 'prepareFiles']),
            'data-yjs-target' => 'modal'
        ]);
        $this->Modals->addFooterButton(__('Download Database Backup'), [
            'class' => 'btn btn-primary',
            'href' => Router::url(['action' => 'getBackup'])
        ], null, true, 'a');

        if ($this->request->is(array('post', 'put'))) {
            $state = 'error';
            $this->BackupRestore->set($this->request->data);

            if (!empty($this->request->data['BackupRestore']['ZipFile']) && $this->BackupRestore->validates()) {
                $tmp_name = $this->request->data['BackupRestore']['ZipFile']['tmp_name'];
                if ($this->restoreBackup($tmp_name)) {
                    $state = 'success';
                }
            }
            else {
                $this->Session->setFlash(__( 'You forgot to upload a backup file or it is in wrong format.' ), FLASH_ERROR);
                $state = 'error';
            }

            $this->YoonityJSConnector->setState($state);
        }

        $this->set(compact('brFormName'));
    }

    public function prepareFiles()
    {
        $this->Ajax->initModal('normal', __('Prepared files for download'));

        $uploadsFiles = $this->getFolderTree(APP . 'webroot/files/uploads/');
        $awarenessFiles = $this->getFolderTree(APP . 'webroot/files/awareness/');
        $vendorAssessmentFiles = $this->getFolderTree(APP . 'webroot/files/vendor_assessment/');

        $files = array_merge($uploadsFiles, $awarenessFiles, $vendorAssessmentFiles);

        // Limit for one group of files in bytes
        $groupLimit = 350000000;
        // Current size of group of files
        $currentGroupSize = 0;
        $groupFiles = [];
        $filesGroups = [];
        $countFiles = count($files);
        for ($i = 0; $i < count($files); ++$i) {
            $file = $files[$i];

            $fileSkipped = false;
            $f = new File($file);
            if ($f->exists()) {
                if ($currentGroupSize == 0 || ($currentGroupSize + $f->size()) <= $groupLimit) {
                    $currentGroupSize += $f->size();
                    $groupFiles[] = $file;
                } else {
                    $fileSkipped = true;
                }
            }

            if ($i == $countFiles - 1 || $fileSkipped) {
                $filesGroups[] = [
                    'size' => $currentGroupSize,
                    'sizeFriendly' => $this->fileSizeConvert($currentGroupSize),
                    'files' => $groupFiles
                ];

                $currentGroupSize = 0;
                $groupFiles = [];
            }

            // Set current loop one file back
            if ($fileSkipped) {
                $i--;
            }
        }

        $this->Session->write('BackupRestore.backupFilesGroups', $filesGroups);

        $this->set('backupFileParts', $filesGroups);
    }

    public function downloadFile($fileId)
    {
        $files = $this->Session->read('BackupRestore.backupFilesGroups');

        if (empty($files) || !isset($files[$fileId])) {
            throw new NotFoundException(__('Requested backup file not exists.'));
        }

        $this->autoRender = false;

        $filename = 'FilesBackupPart' . ($fileId + 1) . '.zip';
        $filepath = TMP . $filename;

        $this->cleanTmpBackupFiles($filepath);

        $zip = new ZipArchive();
        $zip->open($filepath, ZipArchive::CREATE);
        foreach ($files[$fileId]['files'] as $file) {
            $zip->addFile($file, str_replace(APP . 'webroot/files/', '', $file));
        }
        $zip->close();

        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename={$filename}");
        //header("Content-length: " . filesize($filepath));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile("$filepath");

        $this->cleanTmpBackupFiles($filepath);

        exit;
    }

    protected function cleanTmpBackupFiles($filepath)
    {
        $file = new File($filepath);
        if ($file->exists()) {
            $file->delete();
        }
    }

    protected function getFolderTree($path)
    {
        $folder = new Folder();
        $files = $folder->tree($path, false, 'file');

        return $files;
    }

    protected function fileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
        $arBytes = [
            0 => [
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ],
            1 => [
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ],
            2 => [
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ],
            3 => [
                "UNIT" => "KB",
                "VALUE" => 1024
            ],
            4 => [
                "UNIT" => "B",
                "VALUE" => 1
            ],
        ];

        foreach($arBytes as $arItem) {
            if($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

    /**
     * Extracts backup zip file and import database and attachments.
     */
    private function restoreBackup( $file ) {
        $this->cleanRestoreTmpFiles();

        $zip = new ZipArchive;
        $res = $zip->open( $file );

        if ( $res === TRUE ) {
            $zip->extractTo( TMP . 'restore/' );
            $zip->close();
        }
        else {
            $this->Session->setFlash( __( 'Error occured while opening the archive.' ) , FLASH_ERROR );
            return false;
        }

        $this->loadModel('Setting');
        $ret = $this->Setting->dropAllTables();

        // $output = $this->BackupRestore->restoreDatabase( TMP . 'restore/backup.sql' );
        $ret &= $this->Setting->runSchemaFile(TMP. 'restore/backup.sql');

        if (!$ret) {
            $this->Session->setFlash( __( 'Error occured while restoring data.' ) , FLASH_ERROR );
            return false;
        }

        // $this->recurse_copy( TMP . 'restore/uploads', APP . 'webroot/files/uploads/' );
        $this->cleanRestoreTmpFiles();

        $this->Session->setFlash( __( 'Restore completed successfully.' ) , FLASH_OK );
        return true;
    }

    private function cleanRestoreTmpFiles() {
        $dir = new Folder( TMP . 'restore' );
        $dir->delete();
    }

    private function recurse_copy( $src, $dst ) {
        $dir = opendir($src);
        //@mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                /*if ( is_dir($src . '/' . $file) ) {
                    recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {*/
                    copy($src . '/' . $file,$dst . '/' . $file);
                //}
            }
        }
        closedir($dir);
    }

    /**
     * Makes a zip file from sql dump and file uploads.
     */
    public function getBackup() {
        $today = CakeTime::format( 'Y-m-d', CakeTime::fromString( 'now' ) );

        $this->autoRender = false;

        $this->cleanTmpFiles();

        $output = $this->BackupRestore->backupDatabase( TMP . 'backup.sql', true );

        if ( ! $output ) {
            $this->Flash->error(__('Error occured while creating a backup database file.'));

            return $this->redirect(['controller' => 'Settings', 'action' => 'index']);
        }

        $filename = 'eramba_' . $today . '.zip';
        $filepath = TMP . $filename;
        $zipBackup = $this->BackupRestore->zipBackupFiles($filepath, array(
            'backup.sql' => TMP . 'backup.sql'
        ));
        if (!$zipBackup) {
            $this->Flash->error(__('Error occured while creating a backup file.'));

            return $this->redirect(['controller' => 'Settings', 'action' => 'index']);
        }

        // if ( ! $zip->addEmptyDir( 'uploads' ) ) {
        //     $this->Session->setFlash( __( 'Error occured while creating a backup file.' ), FLASH_ERROR );
        //     $this->redirect( array( 'controller' => 'backupRestore', 'action' => 'index' ) );
        // }

        // $options = array( 'add_path' => 'uploads/', 'remove_all_path' => TRUE );
        // if ( ! $zip->addGlob( './files/uploads/*', GLOB_BRACE, $options ) ) {
        //     $this->Session->setFlash( __( 'Error occured while moving attachments to a backup file.' ), FLASH_ERROR );
        //     $this->redirect( array( 'controller' => 'backupRestore', 'action' => 'index' ) );
        // }

        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename={$filename}");
        //header("Content-length: " . filesize($filepath));
        header("Pragma: no-cache");
        header("Expires: 0");
        readfile("$filepath");

        $this->cleanTmpFiles();

        exit;
    }

    private function cleanTmpFiles() {
        $today = CakeTime::format( 'Y-m-d', CakeTime::fromString( 'now' ) );

        $filename = 'eramba_' . $today . '.zip';
        $filepath = TMP . $filename;

        $file = new File( $filepath );
        if ( $file ) {
            $file->delete();
        }

        $file = new File( TMP . 'backup.sql' );
        if ( $file ) {
            $file->delete();
        }
    }
}