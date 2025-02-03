<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Boutique;
use App\Models\Publicite;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ListPublicite extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Publicite::query()->orderBy('created_at', 'desc'))
            // ->paginated(false)
            ->columns([

                TextColumn::make('date_start')
                    ->label('Début')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('d F Y'))
                    ->sortable(),

                TextColumn::make('date_end')
                    ->label('Fin')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->translatedFormat('d F Y'))
                    ->searchable(),

                TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable(),

                    ImageColumn::make('url_image')
                    ->label('Image')
                    ->url(fn($record) => asset( $record->url_image)) // Prends en compte le chemin relatif
                    ->size(50),

                TextColumn::make('lien')
                    ->label('Lien')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable(),

                    TextColumn::make('isActif')
                    ->label('Actif ?')
                    ->formatStateUsing(fn ($state) => $state ? 'Oui' : 'Non')
                    ->searchable(),

            ])
            ->headerActions([

            ])
            ->filters([
            ])
            ->actions([

            ])
            ->bulkActions([]);
    }    public function render()
    {
        return view('livewire.list-publicite');
    }
}
