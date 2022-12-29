<?php

namespace Deployer;

use Deployer\Exception\GracefulShutdownException;
use SourceBroker\DeployerExtendedMedia\Utility\FileUtility;
use SourceBroker\DeployerInstance\Configuration;

/*
 * @see https://github.com/sourcebroker/deployer-extended-media#media-pull
 */
task('media:pull', function () {
    $sourceName = get('argument_host');
    if (get('local_host') === get('instance_live_name', 'live')) {
        if (!get('media_allow_pull_live', true)) {
            throw new GracefulShutdownException(
                'FORBIDDEN: For security its forbidden to pull media to top instance: "' . get('local_host') . '"!'
            );
        }
        if (!get('media_allow_pull_live_force', false)) {
            writeln("<error>\n\n");
            writeln(sprintf(
                "You going to pull media from instance: \"%s\" to top instance: \"%s\". ",
                $sourceName,
                get('local_host')
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
    $src = get('deploy_path') . '/' . (test('[ -L {{deploy_path}}/release ]') ? 'release' : 'current');
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
    $host = $sourceServer->getHostname();
    $user = !$sourceServer->getRemoteUser() ? '' : $sourceServer->getRemoteUser() . '@';

    $rsyncSshOptions = '';
    $connectionOptions = $sourceServer->connectionOptionsString();
    if ($connectionOptions !== '') {
        $rsyncSshOptions = '-e "ssh ' . $connectionOptions . ' "';
    }
    runLocally(
        'rsync ' . $rsyncSshOptions . ' {{media_rsync_flags}}{{media_rsync_options}}{{media_rsync_includes}}{{media_rsync_excludes}}{{media_rsync_filter}}'
        . ' '
        . escapeshellarg($user . $host . ':' . $src)
        . ' '
        . escapeshellarg($dst)
    );
})->desc('Synchronize media from remote instance to local instance');
