<?php

namespace SourceBroker\DeployerExtendedMedia;

class Loader
{
    public function __construct()
    {
        \SourceBroker\DeployerExtendedMedia\Utility\FileUtility::requireFilesFromDirectoryReqursively(
            dirname((new \ReflectionClass('\SourceBroker\DeployerExtendedMedia\Loader'))->getFileName()) . '/../deployer/'
        );
    }
}
