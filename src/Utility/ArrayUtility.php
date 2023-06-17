<?php

namespace SourceBroker\DeployerExtendedMedia\Utility;

class ArrayUtility
{
    public function mergeRecursiveWithOverrule(
        array &$original,
        array $overrule
    ) {
        foreach ($overrule as $key => $_) {
            if ($_ === '__UNSET') {
                unset($original[$key]);
                continue;
            }
            if (isset($original[$key]) && is_array($original[$key]) && is_array($_)) {
                if (isset($_['__UNSET']) && is_array($_['__UNSET'])) {
                    foreach ($_['__UNSET'] as $unsetValue) {
                        while (($keyToDelete = array_search($unsetValue, $original[$key], true)) !== false) {
                            unset($original[$key][$keyToDelete]);
                        }
                    }
                    unset($_['__UNSET']);
                    if (empty($_)) {
                        continue;  // if $_ is an empty array, skip the rest of the processing for this key
                    }
                }
            }
            if (isset($original[$key]) && is_array($original[$key])) {
                if (is_array($_)) {
                    if (empty($_)) {
                        $original[$key] = $_; // if $_ is an empty array, override original
                    } else {
                        // If both $original[$key] and $_ are arrays, and $_ is not empty, merge them instead of overwriting
                        $original[$key] = array_merge($original[$key], $_);
                    }
                } else {
                    // if $original[$key] is an array but $_ is not, append $_ to the array
                    $original[$key][] = $_;
                }
            } elseif (
                $_ || (is_array($_) && empty($_))
            ) {
                $original[$key] = $_;
            }
        }
        reset($original);
    }
}
