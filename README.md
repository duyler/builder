# Duyler Application Builder

Duyler Builder is a package for building and configuring event-driven applications using the Duyler framework. It provides a flexible and intuitive way to manage dependencies, load packages, and configure your Duyler application.

### Features

- Register actions
- Register Events
- Register state handlers
- Add state handlers contexts
- Add shared services
- Loading extensions packages
- Dependency management
- Building and running application

### Installation

```bash
composer require duyler/builder
```

### Basic Usage

#### Register actions

```php
// build/actions.php

<?php

use Duyler\Builder\Build\Action\Action;
use Duyler\Web\Build\Attribute\Route;
use Duyler\Web\Build\Attribute\View;
use Duyler\Web\Enum\HttpMethod;

Action::declare()
    ->attributes(
        new Route(
            method: HttpMethod::Get,
            pattern: '/',
        ),
        new View(
            name: 'home',
        ),
    );
```

#### Build and run application

```php
<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

$applicationBuilder = new \Duyler\Builder\ApplicationBuilder();
$applicationBuilder->getBusBuilder()
    ->loadPackages()
    ->loadBuild()
    ->build()
    ->run();
```

### Testing

```bash
./vendor/bin/phpunit tests/Unit
```
