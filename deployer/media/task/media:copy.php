<?php

namespace Deployer;

use SourceBroker\DeployerExtended\Configuration;

task('media:copy', function () {
    $targetName = input()->getArgument('stage');
    $sourceName = input()->getArgument('targetStage');

    if (null === $sourceName) {
        throw new \RuntimeException(
            "You must set the source instance the media will be copied from second parameter server."
        );
    }
    if (null !== $targetName) {
        if ($targetName == 'live') {
            throw new \RuntimeException(
                "FORBIDDEN: For security its forbidden to move media to live instance!"
            );
        }
        if ($targetName == 'local') {
            throw new \RuntimeException(
                "FORBIDDEN: For synchro local media use: \ndep media:pull " . $sourceName
            );
        }
    } else {
        throw new \RuntimeException("The target instance is required for media:copy command. [Error code: 1488149866477]");
    }

    $targetServer = Configuration::getServer($targetName);

    $targetDir = test('[ -e '. $targetServer['deploy_path'] .'/release ]') ?
        $targetServer['deploy_path'] . '/release' :
        $targetServer['deploy_path'] . '/current';

    run('cd ' . $targetDir . ' && {{bin/php}} {{local/bin/deployer}} media:pull ' . $sourceName);
})->desc('Copy files between istances (without using local instance).');
