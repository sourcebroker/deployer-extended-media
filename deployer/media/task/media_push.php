<?php

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;
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
    $config = array_merge_recursive(get('media_default'), get('media'));

    $src = get('deploy_path') . '/current';
    if (!trim($src)) {
        throw new GracefulShutdownException('You need to specify a source path.');
    }

    $dst = get('media_rsync_dest');
    while (is_callable($dst)) {
        $dst = $dst();
    }
    if (!trim($dst)) {
        throw new GracefulShutdownException('You need to specify a destination path.');
    }

    $targetServer = Configuration::getHost($targetName);
    $host = $targetServer->getRealHostname();
    $port = $targetServer->getPort() ? ' -p' . $targetServer->getPort() : '';
    $identityFile = $targetServer->getIdentityFile() ? ' -i ' . $targetServer->getIdentityFile() : '';
    $user = !$targetServer->getUser() ? '' : $targetServer->getUser() . '@';

    $flags = isset($config['flags']) ? '-' . $config['flags'] : false;
    runLocally("rsync {$flags} -e 'ssh$port$identityFile' {{media_rsync_options}}{{media_rsync_includes}}{{media_rsync_excludes}}{{media_rsync_filter}} '$dst/' '$user$host:$src/' ", 0);
})->desc('Synchronize media from current instance to remote instance');
