<?php
/**
 * Copyright (c) 2015 by Robin Wieschendorf. All Rights
 * Reserved. Proprietary and Confidential - This source code is not for
 * redistribution.
 */
load_file('/library/core/console/script/script.php');
load_file('/library/core/migration.php');

class MigrateScript extends Script
{
    public function action($argv)
    {
        $command = $argv[1];
        if ($command == 'migrate' || $command == '-m') {
            $migration = new Migration();
            $migration->up();
        } elseif ($command == 'rollback' || $command == '-r') {
            $migration = new Migration();
            $migration->rollback();
        } elseif ($command == 'status' || $command == '-s') {
            $migration = new Migration();
            $migration->printStatus();
        } elseif ($command === 'help' || $command == '-h') {
            Debug::out('Command: -h (help)');
            Debug::out('migrate or -m');
            Debug::out('rollback or -r');
            Debug::out('status or -s');
        } else {
            Debug::out('Command unknown. Try -h for help.');
        }
    }
}
