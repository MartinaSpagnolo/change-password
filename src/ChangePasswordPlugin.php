<?php

namespace Hardikkhorasiya09\ChangePassword;

use Closure;
use Filament\Contracts\Plugin;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Hardikkhorasiya09\ChangePassword\Filament\Pages\ChangePasswordPage;
use Illuminate\Foundation\Auth\User;

class ChangePasswordPlugin implements Plugin
{
  use EvaluatesClosures;

  /**
   * @var bool|Closure|mixed
   */
  protected bool $visible = true;

  public function getId(): string
  {
    return ChangePasswordServiceProvider::$name;
  }

  public function register(Panel $panel): void
  {
    $panel
      ->pages([
        ChangePasswordPage::class,
      ])
//      ->userMenuItems([
//        MenuItem::make()
//          ->label('Change Password')
//          ->url('/' . $panel->getId() .  '/change-password')
//          ->icon('heroicon-o-key')
//          ->visible(fn(): bool => self::canAccess()),
//      ]);
  }

  public function boot(Panel $panel): void
  {
    //
  }

  public static function make(): static
  {
    return app(static::class);
  }

  public static function canAccess(): bool
  {
    return auth()->check() && method_exists(auth()->user(), "canAccessChangePassword")
      ? auth()->user()->canAccessChangePassword()
      : true;
  }
}
