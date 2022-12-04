<?php

namespace SourceBroker\DeployerExtendedMedia\Utility;

use InvalidArgumentException;
use function Deployer\input;

/**
 * Class ConsoleUtility
 *
 * @package SourceBroker\DeployerExtendedMedia\Utility
 */
class ConsoleUtility
{
    /**
     * Check if option is present and return it. If not throw exception.
     *
     * @param $optionToFind
     * @param bool $required
     * @return mixed
     */
    public function getOption($optionToFind, bool $required = false)
    {
        $optionReturnValue = null;
        if (!empty(input()->getOption('options'))) {
            $options = explode(',', input()->getOption('options'));
            if (is_array($options)) {
                foreach ($options as $option) {
                    $optionParts = explode(':', $option);
                    if (!empty($optionParts[1])) {
                        $optionValue = $optionParts[1];
                    }
                    if ($optionToFind === $optionParts[0]) {
                        if (!empty($optionValue)) {
                            $optionReturnValue = $optionValue;
                        } else {
                            $optionReturnValue = true;
                        }
                    }
                }
            }
        }
        if ($required && $optionReturnValue === null) {
            throw new InvalidArgumentException('No `--options=' . $optionToFind . ':value` set.', 1458937128560);
        }
        return $optionReturnValue;
    }
}
