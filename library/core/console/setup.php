<?php
function load_file($filepath)
{
    $rootPath = getcwd()  . "/..";
    if (file_exists($rootPath . $filepath)) {
        require_once($rootPath . $filepath);
        return 1;
    } else {
        return 0;
    }
}

//Migrate
load_file('/config/config_local.php');
load_file('/library/debug.php');
load_file('/library/naming_convention.php');
load_file('/app/exception/database_exception.php');
load_file('/library/db/db.php');
load_file('/library/active_record.php');
load_file('/library/migration.php');

$migration = new Migration();
$migration->up();

//Seeds
load_file('/library/object.php');
load_file('/library/db/db_obj.php');
load_file('/library/db/db_model.php');
load_file('/library/db/repository.php');
load_file('/db/seed.php');

echo '== creating seed' . "\n";
$seed = new Seed();
$seed->create();
echo '== setup done' . "\n";