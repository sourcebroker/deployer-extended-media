<?php

namespace Deployer;

use SourceBroker\DeployerExtended\Configuration;

task('media:link', function () {
    $force = input()->getOption('force');
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

    $targetEnv = Configuration::getEnvironment($targetName);
    $sourceEnv = Configuration::getEnvironment($sourceName);

    $targetDir = $targetEnv->get('deploy_path') . '/' .
        (test('[ -e ' . $targetEnv->get('deploy_path') . '/release ]') ? 'release' : 'current');
    $sourceDir = $sourceEnv->get('deploy_path') . '/' .
        (test('[ -e ' . $sourceEnv->get('deploy_path') . '/release ]') ? 'release' : 'current');

    $targetServer = Configuration::getServer($targetName);
    $sourceServer = Configuration::getServer($sourceName);

    if ($targetServer->getConfiguration()->getHost() != $sourceServer->getConfiguration()->getHost()
        || $targetServer->getConfiguration()->getPort() != $sourceServer->getConfiguration()->getPort()) {
        throw new \RuntimeException(
            "FORBIDDEN: Creating links only allowed on same machine. [Error code: 1488234862247]"
        );
    }

    $mode = !$force
        ? '--update'
        : '';

    // linking on the same remote server
    // 1. cd to source server document root
    // 2. find all files fulfiting filter conditions (-L param makes find to search in linked directories - for example shared/)
    //    for each found file:
    //     2.1. check if file already exists on target instance - if it exists omit this file
    //     2.2. get directory name (on source instance) of file and create directories recursively (on destination instance)
    //     2.3. create link (with `ln -s`) in target instance targeting source file
    $script = <<<BASH
rsync {{media_rsync_flags}} --info=all0,name1 {$mode} --dry-run {{media_rsync_options}}{{media_rsync_includes}}{{media_rsync_excludes}}{{media_rsync_filter}} '$sourceDir/' '$targetDir/' |
while read path; do
    if [ -d "{$sourceDir}/\$path" ]
    then
        echo "Creating directory \$path"
        mkdir -p "{$targetDir}/\$path"
    else
        if [ -e "{$targetDir}/\$path" ] && [ ! -d "{$targetDir}/\$path" ]
        then
            echo "Delete current file \$path"
            rm "{$targetDir}/\$path"
        fi
        
        if [ ! -e "{$targetDir}/\$path" ]
        then 
            echo "Linking file \$path"
            ln -s "{$sourceDir}/\$path" "{$targetDir}/\$path"
        fi
    fi
done
BASH;

    run($script);
})->desc('Copy files between istances (without using local instance).');
