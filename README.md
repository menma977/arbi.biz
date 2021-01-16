# ARIB.BIZ

### todo

```
reigster (register menggunakan pin dari seponsornya 1x register motong 1 pin)
    -name
    -email
    -username
    -password
    -wallet_dax
    -wallet
    -username_coin
    -password_coin
    -cookie
    -suspend
    -validasi wallet (jika gagal registrasi ulang)
        -link(https://sochain.com/api/v2/is_address_valid/DOGE/$wallet){response : [status:'success']}
        -ketika register tambah ke binary

main
    -cek versi APK
    -maintenance apa kagak
    -jika punya token langsung masuh ke navigation
    -jika gak punya token masuk login

login
    -username
    -password
    -jiksa user suspend gak bisa login
    -update cookie

navgation (terserah di buat footer navigation/sidebar navgation/header navigation)
    -home
        -menu deposit
        -menu withdraw
        -menu treding
            -treding ada 2 (MIN default : 3000, MAX default : 10000,{kalah dapet point. rumus( { (HARGA DOGE SEKARANG * NOMINAL TERDING) / 500000 } )})
                -jika saldo kurang/lebih dari max min gak bisa treding
                -treding rumus sendiri
                -kirim ke tampungan wallet randome (kalok wallet itu ada saldonya diya menang kalok kosong diya kalah semua salodo masuk ke situ {mirip dakon :V})
                -treding ke 999doge marti angel
    -network
        -webview binary seperti biasa
    -setting
        -ganti wallet_dex (validasi dulu)
        -ganti password

WEB
shere level
    -IT = 1%
    -BUYWALL = 1%
    -SPONSOR = 1%
    -SISA jadi profit usernya

generate random wallet (ini jumlahnya terserah tar bisa di random. LOGIN : (jika gagal login ulang))
    -username_coin
    -password_coin
    -cookie
    -wallet

pin
    -user
    -debit
    -credit
    -description

setting
    -version (default: 1)
    -maintenance (default: false)
    -min_tred (default: 3000)
    -max_tred (default: 10000)
    -doge_price (default: 0) link(https://indodax.com/api/summaries)
```

## install breeze

```shell
composer require laravel/breeze --dev

php artisan breeze:install
```

## install passport

```shell
composer require laravel/passport
php artisan migrate
php artisan passport:install
```

### App\Models\User

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}
```

### App\Providers\AuthServiceProvider

```php
<?php
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
```

### config/auth.php

```
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'passport',
        'provider' => 'users',
    ],
],
```