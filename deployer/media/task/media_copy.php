<?php

namespace Deployer;

use SourceBroker\DeployerExtendedMedia\Utility\ConsoleUtility;
use SourceBroker\DeployerInstance\Configuration;
use Deployer\Exception\GracefulShutdownException;

/*
 * @see https://github.com/sourcebroker/deployer-extended-media#media-copy
 */
task('media:copy', function () {
    $sourceName = get('argument_stage');
    $targetName = (new ConsoleUtility)->getOption('target');
    if (null === $targetName) {
        throw new GracefulShutdownException(
            "You must set the target instance in option '--options=target:[target]'. The media will be copied to this. [Error code: 1488149866477]"
        );
    }
    $doNotAskAgainForLive = false;
    if ($targetName == get('instance_live_name', 'live')) {
        if (!get('media_allow_copy_live', true)) {
            throw new GracefulShutdownException(
                'FORBIDDEN: For security its forbidden to copy media to top instance: "' . $targetName . '"!'
            );
        }
        if (!get('media_allow_copy_live_force', false)) {
            $doNotAskAgainForLive = true;
            write("<error>\n\n");
            write(sprintf(
                "You going to copy media from instance: \"%s\" to top instance: \"%s\". ",
                $sourceName,
                $targetName
            ));
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

    if (!$doNotAskAgainForLive && $targetName == get('instance_local_name', 'local')) {
        throw new GracefulShutdownException(
            "FORBIDDEN: For synchro local media use: \ndep media:pull " . $sourceName
        );
    }

    if (!askConfirmation(sprintf(
        "Do you really want to copy media from instance %s to instance %s",
        $sourceName,
        $targetName
    ), true)) {
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
