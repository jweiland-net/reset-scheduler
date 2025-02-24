<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reset-scheduler.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\ResetScheduler;

use TYPO3\CMS\Core\Exception as CoreException;

/**
 * A generic scheduler exception
 * SF: This is the original class content backported from EXT:scheduler of TYPO3 12
 */
class Exception extends CoreException {}
