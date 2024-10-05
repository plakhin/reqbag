<?php

use App\Providers\Filament\AdminPanelProvider;

arch()->preset()->laravel()->ignoring( //@phpstan-ignore-line: PHPStan doesn't recognize preset()
    AdminPanelProvider::class, //uses Filament naming convention
);

arch()->preset()->security()->ignoring( //@phpstan-ignore-line: PHPStan doesn't recognize preset()
    'parse_str', //parse_str() considered safe since PHP 8.0
);
