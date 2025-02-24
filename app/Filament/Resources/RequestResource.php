<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\RequestResource\Pages;
use App\Models\AiAnalysis;
use App\Models\Bag;
use App\Services\Contracts\AiRequestAnalyzer;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Plakhin\RequestChronicle\Enums\HttpMethod;
use Plakhin\RequestChronicle\Models\Request;

class RequestResource extends Resource
{
    protected static ?string $model = Request::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('model.slug')
                    ->label('Bag')
                    ->size(TextColumnSize::ExtraSmall)
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('method')
                    ->badge()
                    ->formatStateUsing(fn (Request $request) => $request->method->name)
                    ->color(fn (HttpMethod $state): string => self::httpMethodBageColor($state)),
                Tables\Columns\TextColumn::make('url')
                    ->limit(128)
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
                SelectFilter::make('bag')
                    ->options(fn () => Bag::pluck('slug', 'id')->toArray())
                    ->query(
                        fn (Builder $query, array $data) => $query->when(
                            $data['value'],
                            fn (Builder $query) => $query
                                ->where('model_type', Bag::class)
                                ->where('model_id', $data['value'])
                        )
                    ),
                SelectFilter::make('method')->multiple()->options(HttpMethod::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton()
                    ->icon(null)
                    ->mountUsing(function (Table $table) {
                        $table->poll(null);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->iconButton(),
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
                    ->formatStateUsing(fn (Request $request) => $request->method->name)
                    ->color(fn (HttpMethod $state): string => self::httpMethodBageColor($state)),
                TextEntry::make('model.slug')
                    ->label('Bag')
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
                    ->formatStateUsing(fn (string $state): HtmlString => new HtmlString(
                        '<pre style="max-width: 53rem; overflow: scroll">'.$state.'</pre>'
                    ))
                    ->copyable(),
                KeyValueEntry::make('getVariables')
                    ->label('GET Variables')
                    ->columnSpanFull(),
                KeyValueEntry::make('payload')
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
                            '<pre style="max-width: 53rem; overflow: scroll">'.htmlentities($state).'</pre>'
                        )
                    )
                    ->markdown()
                    ->fontFamily(FontFamily::Mono)
                    ->size(TextEntrySize::ExtraSmall)
                    ->copyable(),
                Section::make()
                    ->id('analysis')
                    ->schema([
                        TextEntry::make('analysis.analysis_result.response')
                            ->formatStateUsing(
                                fn (string $state): ?string => preg_replace(
                                    [
                                        '/\\\n/',
                                        '/\\\t/',
                                        '/\`((?:[^\`||\s]+))\`/U',
                                        '/```.*\n((.|\n)*)```/U',
                                    ],
                                    [
                                        '',
                                        '    ',
                                        '<code>$1</code>',
                                        '<pre style="max-width: 50rem; overflow: scroll">$1</pre>',
                                    ],
                                    $state
                                )
                            )
                            ->default(
                                resolve(AiRequestAnalyzer::class)->isConfigured()
                                    ? __('Analysis were not performed yet.')
                                    : __('AI Analyzer is not properly configured.')
                            )
                            ->markdown()
                            ->fontFamily(FontFamily::Mono)
                            ->size(TextEntrySize::ExtraSmall),
                    ])
                    ->footerActions([
                        Action::make('analyze')
                            ->icon('heroicon-m-beaker')
                            ->hidden(fn (Request $request) => $request
                                ->load('analysis')
                                ->getRelation('analysis')
                                ?->analysis_result
                                ?->is_successful)
                            ->disabled(fn () => ! resolve(AiRequestAnalyzer::class)->isConfigured())
                            ->action(fn (Request $request) => AiAnalysis::makeForRequest($request)),
                    ]),
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
