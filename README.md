## Pre-reqs
* PHP 7.1
* Laravel 5.8
## Installation

1. install with composer `composer require uniguide/pportalen-laravel-gw`


2. Edit `config\services.php` and add

```php
    'pportalen' => [
        'endpoint' => env('PPORTALEN_ENDPOINT', 'https://api.personal.uniguide.se/v1'),
        'app_id' => env('PPORTALEN_APP_ID'), // public
        'access_token' => env('PPORTALEN_ACCESS_TOKEN'), // secret
    ]
```


## Authentication flow

1. Non authenticated users should be redirected to `https://personal.uniguide.se/login?app=<PPORTALEN_APP_ID>`
2. After a succesful login a GET request is made to `https://<app-callback-url>?tmpToken=<TmpToken>`
3. The callback endpoint can resolve `Gateway::resolveToken('<TmpToken>')`


### Example implementation

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Exception\RequestException;
use Uniguide\Pportalen\Gateway as PPGateway;
use \Illuminate\Http\Request;

class PPAuthController
{
    public function __invoke(Request $request)
    {
        try {
            $authenticatedUser = PPGateway::resolveToken($request->get('tmpToken'));
            
            // Active Directory (AD), a security identifier (SID) with format "S-1-5-21-XXXXXXXX-YYYYYYYYYYY-ZZZZZZZ-123"
            $userModel = User::firstOrNew(['ad_sid' => $authenticatedUser->ad_sid]);
            $userModel->name = $authenticatedUser->full_name;
            $userModel->email = $authenticatedUser->work_email;
            // ...
            $userModel->save();
            // Allow user 
            return redirect()->to('admin.dashboard')
           
        } catch (RequestException $exception) {
            // something went wrong, expired token, wrong token, wrong access token etc.
        }
    }
}
```

## Sync all users

To always keep a copy of all employees 

### Example of implementation

`app\Jobs\SyncUsersJob.php`
```php
<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Uniguide\Pportalen\DataTransferObjects\UserDTO;
use Uniguide\Pportalen\Gateway as PPGateway;

class SyncUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        PPGateway::getUsers()->each(function (UserDTO $DTO) {

            // Active Directory (AD), a security identifier (SID) with format "S-1-5-21-XXXXXXXX-YYYYYYYYYYY-ZZZZZZZ-123"
            $user = User::firstOrNew([
                'ad_sid' => $DTO->ad_sid
            ]);
            // Set fields
            $user->email = $DTO->work_email;
            // Save the user
            $user->save();
            if ($user->wasRecentlyCreated) {
                // User was newely created
            } else {
                // User existed since before
            }
        });
    }
}
```

In `app\Console\Kernel.php`

```php
<?php

namespace App\Console;

use App\Console\Commands\BeaWorkerCommand;
use App\Jobs\SyncUsersJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    //...
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new SyncUsersJob())->everyFifteenMinutes();
    }
    //...
}

```
