<?php

namespace Deployer;

use SourceBroker\DeployerExtendedMedia\Utility\ConsoleUtility;
use SourceBroker\DeployerInstance\Configuration;
use Deployer\Exception\GracefulShutdownException;

task('media:copy', function () {
    $sourceName = input()->getArgument('stage');
    $targetName = (new ConsoleUtility)->getOption('target');

    if (null === $targetName) {
        throw new GracefulShutdownException(
            "You must set the target instance in option '--options=target:[target]', the media will be copied to, as second parameter. [Error code: 1488149866477]"
        );
    } else {
        if (!get('media_allow_copy_live', false) && $targetName == get('instance_live_name', 'live')) {
            throw new GracefulShutdownException(
                "FORBIDDEN: For security its forbidden to copy media to live instance! " .
                "To allow you need to use \"get('media_allow_copy_live', true);\""
            );
        }
        if ($targetName == get('instance_local_name', 'local')) {
            throw new GracefulShutdownException(
                "FORBIDDEN: For synchro local media use: \ndep media:pull " . $sourceName
            );
        }
    }

    if (!askConfirmation(sprintf("Do you really want to copy media from instance %s to instance %s",
        $sourceName,
        $targetName), true)) {
        throw new GracefulShutdownException('Process aborted.');
    }

    $targetServer = Configuration::getHost($targetName);
    $sourceServer = Configuration::getHost($sourceName);

    $targetDir = $targetServer->getConfig()->get('deploy_path') . '/' .
        (test('[ -e ' . $targetServer->getConfig()->get('deploy_path') . '/release ]') ? 'release' : 'current');
    $sourceDir = $sourceServer->getConfig()->get('deploy_path') . '/' .
        (test('[ -e ' . $sourceServer->getConfig()->get('deploy_path') . '/release ]') ? 'release' : 'current');

    if ($targetServer->getRealHostname() == $sourceServer->getRealHostname()
        && $targetServer->getPort() == $sourceServer->getPort()) {
        // use copy on the same server
        // 1. cd to source server document root
        // 2. find all files fulfiting filter conditions (-L param makes find to search in linked directories - for example shared/)
        //    for each found file:
        //     2.1. check if file already exists on target instance - if it exists omit this file
        //     2.2. get directory name (on source instance) of file and create directories recursively (on destination instance)
        //     2.3. create copy (with `cp -L`) in target instance targeting source file
        $script = <<<BASH
rsync {{media_rsync_flags}} --info=all0,name1 --dry-run --update {{media_rsync_options}}{{media_rsync_includes}}{{media_rsync_excludes}}{{media_rsync_filter}} '$sourceDir/' '$targetDir/' |
while read path; do
    if [ -d "{$sourceDir}/\$path" ]
    then
        echo "Creating directory \$path"
        mkdir -p "{$targetDir}/\$path"
    else
        echo "Copying file \$path"
        cp -L "{$sourceDir}/\$path" "{$targetDir}/\$path"
    fi
done
BASH;

        run($script);
    } else {
        // use media:pull (rsync) command
        run('cd ' . $sourceDir . ' && {{bin/php}} {{local/bin/deployer}} media:push ' . $targetName);
    }
})->desc('Synchronize files between instances');
