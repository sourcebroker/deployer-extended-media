<?php

namespace Deployer;

/*
 * Based on https://github.com/deployphp/recipes/blob/master/recipes/rsync.php
 */

use SourceBroker\DeployerExtendedMedia\Utility\ArrayUtility;

set(
    'media_default',
    [
        'exclude' => [],
        'exclude-case-insensitive' => [
            '*.mp4',
            '*.zip',
            '*.pdf',
            '*.exe',
            '*.doc',
            '*.docx',
            '*.pptx',
            '*.ppt',
            '*.xls',
            '*.xlsx',
            '*.xlsm',
            '*.tiff',
            '*.tif',
            '*.potx',
            '*.mpg',
            '*.mp3',
            '*.avi',
            '*.wmv',
            '*.flv',
            '*.eps',
            '*.ai',
            '*.mov',
        ],
        'exclude-file' => false,
        'include' => [],
        'include-file' => false,
        'filter' => [],
        'filter-file' => false,
        'filter-perdir' => false,
        'flags' => 'rz',
        'options' => ['copy-links', 'keep-dirlinks', 'safe-links'],
        'timeout' => 0,
    ]
);


set('media_rsync_dest', getcwd());

set('media_rsync_excludes', function () {
    $config = get('media_config');

    $excludes = $config['exclude'] ?? [];
    $excludesRsync = '';
    foreach ($excludes as $exclude) {
        $excludesRsync .= ' --exclude=' . escapeshellarg($exclude);
    }

    $excludeFile = $config['exclude-file'] ?? false;
    if (!empty($excludeFile) && file_exists($excludeFile) && is_file($excludeFile) && is_readable($excludeFile)) {
        $excludesRsync .= ' --exclude-from=' . escapeshellarg($excludeFile);
    }

    // rsync does not have the case-insensitive flag
    $excludesCaseInsensitive = $config['exclude-case-insensitive'] ?? [];
    foreach ($excludesCaseInsensitive as $excludeCaseInsensitive) {
        $excludePatternCaseInsensitive = '';
        $excludePatternNormalized = strtolower($excludeCaseInsensitive);
        foreach (str_split($excludePatternNormalized) as $letter) {
            if (strtoupper($letter) === $letter) {
                $excludePatternCaseInsensitive .= $letter;
            } else {
                $excludePatternCaseInsensitive .= '[' . $letter . mb_strtoupper($letter) . ']';
            }
        }
        $excludesRsync .= " --exclude '" . $excludePatternCaseInsensitive . "'";
    }

    return $excludesRsync;
});

set('media_rsync_includes', function () {
    $config = get('media_config');

    $includes = $config['include'] ?? [];
    $includesRsync = '';
    foreach ($includes as $include) {
        $includesRsync .= ' --include=' . escapeshellarg($include);
    }

    $includeFile = $config['include-file'] ?? false;
    if (!empty($includeFile) && file_exists($includeFile) && is_file($includeFile) && is_readable($includeFile)) {
        $includesRsync .= ' --include-from=' . escapeshellarg($includeFile);
    }
    return $includesRsync;
});

set('media_rsync_filter', function () {
    $config = get('media_config');

    $filters = $config['filter'] ?? [];
    $filtersRsync = '';
    foreach ($filters as $filter) {
        $filtersRsync .= " --filter='$filter'";
    }

    $filterFile = $config['filter-file'] ?? false;
    if (!empty($filterFile)) {
        $filtersRsync .= " --filter='merge $filterFile'";
    }

    $filterPerDir = $config['filter-perdir'] ?? false;
    if (!empty($filterPerDir)) {
        $filtersRsync .= " --filter='dir-merge $filterFile'";
    }

    return $filtersRsync;
});

set('media_rsync_options', function () {
    $config = get('media_config');

    $options = $config['options'] ?? [];
    $optionsRsync = [];
    foreach ($options as $option) {
        $optionsRsync[] = "--$option";
    }
    return (!empty($optionsRsync) ? ' ' : '') . implode(' ', $optionsRsync);
});

set('media_rsync_flags', function () {
    $config = get('media_config');

    return !empty($config['flags'])
        ? ' -' . $config['flags']
        : '';
});

set('local/bin/deployer', function () {
    return './vendor/bin/dep';
});

set('media_config', function () {
    $config = get('media_default');
    (new ArrayUtility)->mergeRecursiveWithOverrule($config, get('media', []));
    (new ArrayUtility)->mergeRecursiveWithOverrule($config, get('media_custom', []));
    return $config;
});
