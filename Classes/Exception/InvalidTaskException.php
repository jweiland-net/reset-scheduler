<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reset-scheduler.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\ResetScheduler\Exception;

use JWeiland\ResetScheduler\Exception;

/**
 * Thrown if a Task could not be successfully unserialized or the unserialized
 * Task is not an instance of AbstractTask.
 * SF: This is the original class content backported from EXT:scheduler of TYPO3 12
 */
class InvalidTaskException extends Exception {}
