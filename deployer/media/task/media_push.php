<?php

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;
use SourceBroker\DeployerExtendedDatabase\Utility\FileUtility;
use SourceBroker\DeployerInstance\Configuration;

task('media:push', function () {
    $targetName = input()->getArgument('stage');
    if (null !== $targetName) {
        if ($targetName === get('instance_live_name', 'live')) {
            throw new GracefulShutdownException(
                "FORBIDDEN: For security its forbidden to push media to live instance!"
            );
        }
    } else {
        throw new GracefulShutdownException("The target instance is required for media:push command. [Error code: 1488149981776]");
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
})->desc('Synchronize media from current instance to remote instance');
