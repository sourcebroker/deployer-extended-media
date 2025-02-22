<?php

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;
use SourceBroker\DeployerExtendedMedia\Utility\FileUtility;
use SourceBroker\DeployerInstance\Configuration;

/*
 * @see https://github.com/sourcebroker/deployer-extended-media#media-push
 */
task('media:push', function () {
    $targetName = get('argument_host');
    if ($targetName === get('instance_live_name', 'live')) {
        if (!get('media_allow_push_live', true)) {
            throw new GracefulShutdownException(
                'FORBIDDEN: For security its forbidden to push media to top instance: "' . $targetName . '"!'
            );
        }
        if (!get('media_allow_push_live_force', false)) {
            writeln("<error>\n\n");
            writeln(sprintf(
                "You going to push media from instance: \"%s\" to top instance: \"%s\". ",
                get('local_host'),
                $targetName
            ));
            writeln("This can be destructive.\n\n");
            writeln("</error>");
            if (!askConfirmation('Do you really want to continue?', false)) {
                throw new GracefulShutdownException('Process aborted.');
            }
            if (!askConfirmation('Are you sure?', false)) {
                throw new GracefulShutdownException('Process aborted.');
            }
        }
    }

    $fileUtility = new FileUtility();
    $src = $fileUtility->resolveHomeDirectory(get('deploy_path')) . '/' . (test('[ -L {{deploy_path}}/release ]') ? 'release' : 'current');
    if (!trim($src)) {
        throw new GracefulShutdownException('You need to specify a source path.');
    }
    $src = (new FileUtility)->normalizeFolder($src);

    $dst = $fileUtility->resolveHomeDirectory(get('media_rsync_dest'));
    while (is_callable($dst)) {
        $dst = $dst();
    }
    if (!trim($dst)) {
        throw new GracefulShutdownException('You need to specify a destination path.');
    }
    $dst = (new FileUtility)->normalizeFolder($dst);

    $targetServer = Configuration::getHost($targetName);
    $host = $targetServer->getHostname();
    $user = !$targetServer->getRemoteUser() ? '' : $targetServer->getRemoteUser() . '@';

    $rsyncSshOptions = '';
    $connectionOptions = $targetServer->connectionOptionsString();
    if ($connectionOptions !== '') {
        $rsyncSshOptions = '-e "ssh ' . $connectionOptions . ' "';
    }
    runLocally(
        'rsync ' . $rsyncSshOptions . ' {{media_rsync_flags}}{{media_rsync_options}}{{media_rsync_includes}}{{media_rsync_excludes}}{{media_rsync_filter}}'
        . ' '
        . escapeshellarg($dst)
        . ' '
        . escapeshellarg($user . $host . ':' . $src)
    );
})->desc('Synchronize media from local instance to remote instance');
