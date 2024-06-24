<?php

namespace App\Filament\Resources;

use App\Enums\HttpMethod;
use App\Filament\Resources\RequestResource\Pages;
use App\Models\Request;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('bag.slug')
                    ->size(TextColumnSize::ExtraSmall)
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('method')
                    ->badge()
                    ->formatStateUsing(fn (Request $request) => $request->method->name) //@phpstan-ignore-line
                    ->color(fn (HttpMethod $state): string => self::httpMethodBageColor($state)),
                Tables\Columns\TextColumn::make('url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ips')
                    ->label('IP')
                    ->size(TextColumnSize::ExtraSmall)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->size(TextColumnSize::ExtraSmall)
                    ->tooltip(fn (Request $request): string => $request->created_at)
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('bag')->relationship('bag', 'slug'),
                SelectFilter::make('method')->multiple()->options(HttpMethod::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->iconButton()->icon(null),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->poll('3s')
            ->deferLoading();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('method')
                    ->label('')
                    ->alignLeft()
                    ->badge()
                    ->formatStateUsing(fn (Request $request) => $request->method->name) //@phpstan-ignore-line
                    ->color(fn (HttpMethod $state): string => self::httpMethodBageColor($state)),
                TextEntry::make('bag.slug')
                    ->alignLeft(),
                TextEntry::make('ips')
                    ->label('IP')
                    ->listWithLineBreaks()
                    ->copyable()
                    ->size(TextEntrySize::Small),
                TextEntry::make('created_at')
                    ->label('')
                    ->alignRight()
                    ->dateTime()
                    ->size(TextEntrySize::Small),
                TextEntry::make('id')
                    ->label('')
                    ->alignRight(),
                TextEntry::make('url')
                    ->columnSpanFull()
                    ->fontFamily(FontFamily::Mono)
                    ->size(TextEntrySize::ExtraSmall)
                    ->copyable(),
                KeyValueEntry::make('getVariables')
                    ->label('GET Variables')
                    ->columnSpanFull(),
                KeyValueEntry::make('post')
                    ->label('Payload')
                    ->columnSpanFull(),
                KeyValueEntry::make('flatHeaders')
                    ->label('Headers')
                    ->keyLabel('Name')
                    ->columnSpanFull(),
                TextEntry::make('raw')
                    ->columnSpanFull()
                    ->formatStateUsing(
                        fn (string $state): HtmlString => new HtmlString(
                            '<pre style="max-width: 847px; overflow: scroll"><code>'.htmlentities($state).'</code></pre>'
                        )
                    )
                    ->fontFamily(FontFamily::Mono)
                    ->size(TextEntrySize::ExtraSmall)
                    ->copyable(),
            ])
            ->columns(5);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRequests::route('/'),
        ];
    }

    private static function httpMethodBageColor(HttpMethod $state): string
    {
        return match ($state) {
            HttpMethod::GET => 'info',
            HttpMethod::POST => 'success',
            HttpMethod::PUT => 'primary',
            HttpMethod::PATCH => 'primary',
            HttpMethod::DELETE => 'danger',
            default => 'gray',
        };
    }
}
