<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reset-scheduler.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\ResetScheduler\Configuration;

use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * This class will streamline the values from extension manager configuration
 */
#[Autoconfigure(constructor: 'create')]
final readonly class ExtConf
{
    private const EXT_KEY = 'reset_scheduler';

    private string $emailFromAddress;

    private string $emailFromName;

    private const DEFAULT_SETTINGS = [
        'emailFromAddress' => '',
        'emailFromName' => '',
    ];

    public function __construct(
        string $emailFromAddress = self::DEFAULT_SETTINGS['emailFromAddress'],
        string $emailFromName = self::DEFAULT_SETTINGS['emailFromName'],
    ) {
        $this->emailFromAddress = $emailFromAddress;
        $this->emailFromName = $emailFromName;
    }

    public static function create(ExtensionConfiguration $extensionConfiguration): self
    {
        $extensionSettings = self::DEFAULT_SETTINGS;

        // Overwrite default extension settings with values from EXT_CONF
        try {
            $extensionSettings = array_merge(
                $extensionSettings,
                $extensionConfiguration->get(self::EXT_KEY),
            );
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
        }

        return new self(
            emailFromAddress: (string)$extensionSettings['emailFromAddress'],
            emailFromName: (string)$extensionSettings['emailFromName'],
        );
    }

    public function getEmailFromAddress(): string
    {
        if ($this->emailFromAddress === '') {
            return $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
        }

        return $this->emailFromAddress;
    }

    public function getEmailFromName(): string
    {
        if ($this->emailFromName === '') {
            return $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
        }

        return $this->emailFromName;
    }
}
