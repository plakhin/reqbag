<?php

namespace App\Filament\Resources\RequestResource\Pages;

use App\Filament\Resources\RequestResource;
use App\Models\Bag;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRecords;

class ManageRequests extends ManageRecords
{
    protected static string $resource = RequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Create request bag')
                ->icon('heroicon-m-shopping-bag')
                ->modalIcon('heroicon-m-shopping-bag')
                ->modalDescription(
                    'To store incoming requests you need to create at least one bag (subdomain).'
                    .' Once the bag is created, all requests coming to '
                    .config('app.scheme').'{bag}.'.config('app.central_domain')
                    .' will be stored to the database and displayed on this page grouped by bag.'
                )
                ->form([
                    TextInput::make('slug')->label('Bag subdomain')->required()->unique((new Bag())->getTable(), 'slug'),
                ])
                ->modalSubmitActionLabel('Create')
                ->action(function (array $data): void {
                    Bag::create($data);
                }),
            Action::make('Delete request bags')
                ->icon('heroicon-m-x-circle')
                ->modalIcon('heroicon-m-x-circle')
                ->color('danger')
                ->outlined()
                ->form([Select::make('bags')->multiple()->required()->options(Bag::pluck('slug', 'id'))])
                ->requiresConfirmation()
                ->action(fn (array $data): int => Bag::destroy($data['bags']))
                ->hidden(Bag::doesntExist()),
        ];
    }
}
