<?php

namespace SourceBroker\DeployerExtendedMedia\Utility;

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
    public function normalizeFilename($filename)
    {
        return preg_replace('/^[^a-zA-Z0-9_]+$/', '', $filename);
    }

    /**
     * @param $folder
     * @return string
     */
    public function normalizeFolder($folder)
    {
        return rtrim($folder, '/') . '/';
    }
}
