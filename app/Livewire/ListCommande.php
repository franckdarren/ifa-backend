<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Commande;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class ListCommande extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function table(Table $table): Table
    {
        return $table
            ->query(Commande::query()->orderBy('created_at', 'desc'))
            // ->paginated(false)
            ->columns([
                TextColumn::make('numero')
                    ->label('Numero')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('prix')
                    ->label('Prix')
                    ->formatStateUsing(fn ($state) => number_format($state, 0, ',', ' ') . ' FCFA')
                    ->searchable(),

                TextColumn::make('statut')
                    ->label('Statut')
                    ->searchable(),

                TextColumn::make('commentaire')
                    ->label('Commentaire')
                    ->searchable(),

                TextColumn::make('isLivrable')
                    ->label('Avec livraison')
                    ->formatStateUsing(fn ($state) => $state ? 'Oui' : 'Non')
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
        return view('livewire.list-commande');
    }
}
