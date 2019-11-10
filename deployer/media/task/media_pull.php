<?php

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;
use SourceBroker\DeployerExtendedDatabase\Utility\FileUtility;
use SourceBroker\DeployerInstance\Configuration;

task('media:pull', function () {
    $sourceName = input()->getArgument('stage');
    if (null !== $sourceName) {
        if (get('default_stage') === get('instance_live_name', 'live')) {
            throw new GracefulShutdownException(
                "FORBIDDEN: For security its forbidden to pull media to live instance! [Error code: 1488149981777]"
            );
        }
    } else {
        throw new GracefulShutdownException("The source instance is required for media:pull command. [Error code: 1488149981776]");
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
    runLocally('rsync ' . $sshOptions . ' {{media_rsync_flags}}{{media_rsync_options}}{{media_rsync_includes}}{{media_rsync_excludes}}{{media_rsync_filter}} '
        . escapeshellarg($user . $host . ':' . $src)
        . ' '
        . escapeshellarg($dst)
    );
})->desc('Synchronize media from remote instance to current instance');
