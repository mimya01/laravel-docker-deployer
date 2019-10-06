<?php
namespace Deployer;

require 'recipe/common.php';

// Project name
set('application', 'carpooling');

// Project repository
set('repository', 'git@gitlab.com:mouhcinemimya/covoiturage-v0.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
set('shared_files', ['.env']);
set('shared_dirs', ['storage']);//

// Writable dirs by web server
set('writable_dirs', []);
set('allow_anonymous_stats', false);
//set('branch', 'master');
set('git_recursive', false);
set('git_cache', false);
set('writable_mode', 'chmod');
set('writable_use_sudo', false);

// Hosts

host('142.93.99.47')
    ->user('carpooling_user')
    ->set('deploy_path', '/srv/carpooling.ma/src');


// Tasks

desc('Deploy your project');
// Tasks
task('docker:vendors', function () {
    run('docker exec carpooling_ma composer install -d release');
});
task('docker:storage:link', function () {
    run('docker exec carpooling_ma php release/artisan storage:link');
});

task('docker:cache:clear', function () {
    run('docker exec carpooling_ma php release/artisan cache:clear');
});

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',

    'docker:vendors',
    'docker:storage:link',
    'docker:cache:clear',

    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] If deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');
