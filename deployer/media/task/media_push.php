<?php

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;
use SourceBroker\DeployerExtendedDatabase\Utility\FileUtility;
use SourceBroker\DeployerInstance\Configuration;

task('media:push', function () {
    $targetName = get('argument_stage');
    if (null === $targetName) {
        throw new GracefulShutdownException("The target instance is required for media:push command. [Error code: 1488149981776]");
    }
    if ($targetName === get('instance_live_name', 'live')) {
        if (!get('media_allow_push_live', true)) {
            throw new GracefulShutdownException(
                'FORBIDDEN: For security its forbidden to push media to top instance: "' . $targetName . '"!'
            );
        }
        if (!get('media_allow_push_live_force', false)) {
            write("<error>\n\n");
            write(sprintf("You going to push media to top instance \"%s\". ", $targetName));
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

    $targetServer = Configuration::getHost($targetName);
    $host = $targetServer->getRealHostname();
    $user = !$targetServer->getUser() ? '' : $targetServer->getUser() . '@';

    $sshOptions = '';
    if (!empty($targetServer->getSshArguments())) {
        $sshOptions = '-e ' . escapeshellarg('ssh ' . $targetServer->getSshArguments()->getCliArguments());
    }
    runLocally('rsync ' . $sshOptions . ' {{media_rsync_flags}}{{media_rsync_options}}{{media_rsync_includes}}{{media_rsync_excludes}}{{media_rsync_filter}}'
        . ' '
        . escapeshellarg($dst)
        . ' '
        . escapeshellarg($user . $host . ':' . $src)
    );
})->desc('Synchronize media from local instance to remote instance');
