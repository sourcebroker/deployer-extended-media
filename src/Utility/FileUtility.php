<?php

namespace SourceBroker\DeployerExtendedMedia\Utility;

use function Deployer\run;

/**
 * Class FileUtility
 * @package SourceBroker\DeployerExtendedMedia\Utility
 */
class FileUtility
{
    /**
     * @param $filename
     * @return string
     */
    public function normalizeFilename($filename): string
    {
        return preg_replace('/^[^a-zA-Z0-9_]+$/', '', $filename);
    }

    /**
     * @param $folder
     * @return string
     */
    public function normalizeFolder($folder): string
    {
        return rtrim($folder, '/') . '/';
    }

    public function resolveHomeDirectory(string $path): string
    {
        if ($path[0] === '~') {
            $path = run('echo ${HOME:-${USERPROFILE}}' . escapeshellarg(substr($path, 1)));
        }
        return $path;
    }
}
