<?php

namespace Hardikkhorasiya09\ChangePassword;

use Spatie\LaravelPackageTools\PackageServiceProvider;

class ChangePasswordServiceProvider extends PackageServiceProvider
{
  public static string $name = 'change-password';

  public function configurePackage($package): void
  {
    $package
      ->name(self::$name)
      ->hasViews();
  }
}
