<?php

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;
use SourceBroker\DeployerExtendedDatabase\Utility\FileUtility;
use SourceBroker\DeployerInstance\Configuration;

task('media:pull', function () {
    $sourceName = get('argument_stage');
    if (null === $sourceName) {
        throw new GracefulShutdownException("The source instance is required for media:pull command. [Error code: 1488149981776]");
    }
    if (get('default_stage') === get('instance_live_name', 'live')) {
        if (!get('media_allow_pull_live', true)) {
            throw new GracefulShutdownException(
                'FORBIDDEN: For security its forbidden to pull media to top instance: "' . get('default_stage') . '"!'
            );
        }
        if (!get('media_allow_pull_live_force', false)) {
            write("<error>\n\n");
            write(sprintf("You going to pull media from instance: \"%s\" to top instance: \"%s\". ",
                $sourceName, get('default_stage')));
            write("This can be destructive.\n\n");
            write("</error>");
            if (!askConfirmation('Do you really want to continue?', false)) {
                throw new GracefulShutdownException('Process aborted.');
            }
            if (!askConfirmation('Are you sure?', false)) {
                throw new GracefulShutdownException('Process aborted.');
            }
        }
    }
    $src = get('deploy_path') . '/current';
    if (!trim($src)) {
        throw new GracefulShutdownException('You need to specify a source path.');
    }
    $src = (new FileUtility)->normalizeFolder($src);

    $dst = get('media_rsync_dest');
    while (is_callable($dst)) {
        $dst = $dst();
    }
    if (!trim($dst)) {
        throw new GracefulShutdownException('You need to specify a destination path.');
    }
    $dst = (new FileUtility)->normalizeFolder($dst);

    $sourceServer = Configuration::getHost($sourceName);
    $host = $sourceServer->getRealHostname();
    $user = !$sourceServer->getUser() ? '' : $sourceServer->getUser() . '@';

    $sshOptions = '';
    if (!empty($sourceServer->getSshArguments())) {
        $sshOptions = '-e ' . escapeshellarg('ssh ' . $sourceServer->getSshArguments()->getCliArguments());
    }
    runLocally('rsync ' . $sshOptions . ' {{media_rsync_flags}}{{media_rsync_options}}{{media_rsync_includes}}{{media_rsync_excludes}}{{media_rsync_filter}}'
        . ' '
        . escapeshellarg($user . $host . ':' . $src)
        . ' '
        . escapeshellarg($dst)
    );
})->desc('Synchronize media from remote instance to local instance');
