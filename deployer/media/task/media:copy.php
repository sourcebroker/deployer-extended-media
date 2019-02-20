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
                "FORBIDDEN: For security its forbidden to copy media to live instance!"
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
    $sourceServer = Configuration::getServer($sourceName);

    $targetDir = test('[ -e '. $targetServer['deploy_path'] .'/release ]') ?
        $targetServer['deploy_path'] . '/release' :
        $targetServer['deploy_path'] . '/current';
    $sourceDir = test('[ -e '. $sourceServer['deploy_path'] .'/release ]') ?
        $sourceServer['deploy_path'] .'/release' :
        $sourceServer['deploy_path'] .'/current';

    if ($targetServer['server']['host'] == $sourceServer['server']['host']
        && $targetServer['server']['port'] == $sourceServer['server']['port']) {
        // use copy on the same server

        // 1. cd to source server document root
        // 2. find all files fulfiting filter conditions (-L param makes find to search in linked directories - for example shared/)
        //    for each found file:
        //     2.1. check if file already exists on target instance - if it exists omit this file
        //     2.2. get directory name (on source instance) of file and create directories recursively (on destination instance)
        //     2.3. create copy (with `cp -L`) in target instance targeting source file
        $script = <<<BASH
cd {{media_cp_sourcedir}}
find -L . -type f {{media_cp_includes}} {{media_cp_excludes}} -printf '
    [ -f "{{media_cp_targetdir}}/%p" ] || \
    [ -L "{{media_cp_targetdir}}/%p" ] || \
    ( \
        dirname "{{media_cp_targetdir}}/%p" | xargs -n 1 mkdir -p && \
        cp -L "{{media_cp_sourcedir}}/%p" "{{media_cp_targetdir}}/%p" \
    )' | bash
BASH;

        set('media_cp_targetdir', $targetDir);
        set('media_cp_sourcedir', $sourceDir);

        run($script);
    } else {
        // use media:pull (rsync) command
        run('cd ' . $targetDir . ' && {{bin/php}} {{local/bin/deployer}} media:pull ' . $sourceName);
    }
})->desc('Copy files between istances (without using local instance).');
