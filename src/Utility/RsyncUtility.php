<?php

namespace SourceBroker\DeployerExtendedMedia\Utility;

use Deployer\Host\Host;

/**
 * Class RsyncUtility
 * @package SourceBroker\DeployerExtendedMedia\Utility
 */
class RsyncUtility
{
    /**
     * Returns rsync "-e" option with ssh connection options for given host.
     * Compatible with Deployer 7 (Host::connectionOptionsString) and Deployer 8 (Host::connectionOptions).
     *
     * @param Host $host
     * @return string
     */
    public function getSshOptions(Host $host): string
    {
        $connectionOptions = method_exists($host, 'connectionOptionsString')
            ? $host->connectionOptionsString()
            : implode(' ', array_map('escapeshellarg', $host->connectionOptions()));
        if ($connectionOptions !== '') {
            return '-e "ssh ' . $connectionOptions . '"';
        }
        return '';
    }
}
