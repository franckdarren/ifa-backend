<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Boutique;
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

class ListBoutique extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Boutique::query()->orderBy('created_at', 'desc'))
            // ->paginated(false)
            ->columns([
                ImageColumn::make('url_logo')
                    ->label('Logo')
                    ->url(fn($record) => asset($record->url_logo)) // Prends en compte le chemin relatif
                    ->size(50),

                TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('adresse')
                    ->label('Adresse')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('is_active')
                    ->label('Actif ?')
                    ->searchable(),

                TextColumn::make('solde')
                    ->label('Solde')
                    ->searchable(),

                TextColumn::make('heure_ouverture')
                    ->label('Heure ouverture')
                    ->searchable(),

                TextColumn::make('heure_fermeture')
                    ->label('Heure fermeture')
                    ->searchable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->searchable(),

            ])
            ->headerActions([

            ])
            ->filters([
            ])
            ->actions([

            ])
            ->bulkActions([]);
    }
    public function render()
    {
        return view('livewire.list-boutique');
    }
}