<?php

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;
use SourceBroker\DeployerInstance\Configuration;

task('media:pull', function () {
    $sourceName = input()->getArgument('stage');
    if ($sourceName !== null) {
        if (get('default_stage') === get('instance_live_name', 'live')) {
            throw new GracefulShutdownException(
                "FORBIDDEN: For security its forbidden to pull media to live instance! [Error code: 1488149981777]"
            );
        }
    } else {
        throw new GracefulShutdownException("The source instance is required for media:pull command. [Error code: 1488149981776]");
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
        throw new \RuntimeException('You need to specify a destination path.');
    }

    $sourceServer = Configuration::getHost($sourceName);
    $host = $sourceServer->getRealHostname();
    $port = $sourceServer->getPort() ? ' -p' . $sourceServer->getPort() : '';
    $identityFile = $sourceServer->getIdentityFile() ? ' -i ' . $sourceServer->getIdentityFile() : '';
    $user = !$sourceServer->getUser() ? '' : $sourceServer->getUser() . '@';

    $flags = isset($config['flags']) ? '-' . $config['flags'] : false;
    runLocally("rsync {$flags} -e 'ssh$port$identityFile' {{media_rsync_options}}{{media_rsync_includes}}{{media_rsync_excludes}}{{media_rsync_filter}} '$user$host:$src/' '$dst/'");
})->desc('Synchronize media from remote instance to current instance');
