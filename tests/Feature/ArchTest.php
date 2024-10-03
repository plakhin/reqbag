<?php

use App\Providers\Filament\AdminPanelProvider;

arch()->preset()->laravel()->ignoring(AdminPanelProvider::class); //@phpstan-ignore-line
arch()->preset()->security(); //@phpstan-ignore-line
