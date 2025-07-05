<?php

namespace Hardikkhorasiya09\ChangePassword\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Closure;
use Hardikkhorasiya09\ChangePassword\ChangePasswordPlugin;

class ChangePasswordPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'change-password::filament.pages.change-password';
    protected static ?string $slug = 'change-password';

    protected static ?string $title = 'Benvenuto!';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return ChangePasswordPlugin::canAccess();
    }

    public function mount(): void
    {
        // 
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([

                \Filament\Forms\Components\Section::make('Cambio Password')
                    ->description('Inserisci la Password fornita vie email e procedi con l\'inserimento di una nuova Password. Una volta effettuato il reset potrai procedere con la navigazione.')->aside()
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Password attuale')
                            ->password()
                            ->required()
                            ->revealable(true)
                            ->rules([
                                function () {
                                    return function (string $attribute, $value, Closure $fail) {
                                        if (!Hash::check($value, auth()->user()->password)) {
                                            $fail('La Password inserita non è corretta.');
                                        }
                                    };
                                },
                            ]),

                        Forms\Components\TextInput::make('new_password')
                            ->password()
                            ->required()
                            ->same('password_confirmation')
                            ->minLength(8)
                            ->label('Nuova Password')
                            ->revealable(true),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->password()
                            ->required()
                            ->label('Conferma Password')
                            ->revealable(true),
                    ])


            ])


            ->statePath('data');
    }

    public function save()
    {
        $this->form->validate();

        $data = $this->data;

        // Check if the current password is correct
        if (!Hash::check($data['current_password'], Auth::user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'La password fornita non corrisponde alla password attuale.',
            ]);
        }

        // Update the password if validation passes
        Auth::user()->update([
            'password' => $data['new_password'],
            'must_change_password' => false,
        ]);

        // Refill the form with the reset data
        $this->form->fill();

        session()->put([
            'password_hash_' . Auth::getDefaultDriver() => Auth::user()->password
        ]);

        // Success notification
        Notification::make()
            ->title('Password cambiata con successo!')
            ->success()
            ->send();

        return redirect()->route('filament.client.pages.dashboard');

    }
}

