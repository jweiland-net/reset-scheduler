# TYPO3 Extension `reset_scheduler`

[![Latest Stable Version](https://poser.pugx.org/jweiland/jwtools2/v/stable.svg)](https://packagist.org/packages/jweiland/reset-scheduler)
[![TYPO3 11.5](https://img.shields.io/badge/TYPO3-11.5-green.svg)](https://get.typo3.org/version/11)
[![TYPO3 12.4](https://img.shields.io/badge/TYPO3-12.4-green.svg)](https://get.typo3.org/version/12)
[![License](http://poser.pugx.org/jweiland/jwtools2/license)](https://packagist.org/packages/jweiland/reset-scheduler)
[![Total Downloads](https://poser.pugx.org/jweiland/jwtools2/downloads.svg)](https://packagist.org/packages/jweiland/reset-scheduler)
[![Monthly Downloads](https://poser.pugx.org/jweiland/jwtools2/d/monthly)](https://packagist.org/packages/jweiland/reset-scheduler)

Extension "reset_scheduler" provides a schedulable command to inform you via email, if some of the other defined scheduler tasks failed. Further you have the possibility to reset failing scheduler tasks. That is helpful as TYPO3 will not automatically restart failing scheduler tasks.

## 1 Usage

### 1.1 Installation

#### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your Composer based TYPO3 project:

```
composer require jweiland/reset-scheduler
```

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install `reset_scheduler` with the extension manager module.

### 2.2 Minimal setup

1) Install the extension
2) Create new scheduler task: `Execute console commands (scheduler)`
3) Schedulable Command: `scheduler:reset`
