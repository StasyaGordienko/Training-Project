<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Api\User;
use Illuminate\Support\Facades\Cache;
use function GuzzleHttp\Psr7\str;
use function PHPUnit\Framework\isEmpty;

class ApiUserAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:user:add {username?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new accounts';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $username = $this->argument('username');
        $password = $this->argument('password');

        if (empty($username) and empty($password)) {
            $confirm = $this->confirm('Do you want to enter your login and password?', true);
            if ($confirm) {
                $username = $this->ask('Enter your login:');
                $password = $this->secret('Enter your password:');
            }
        }elseif (!empty($username) and empty($password)){
            $confirm = $this->confirm('Do you want to enter your password?', true);
            if ($confirm) {
                $password = $this->secret('Enter your password:');
            }
        }

        $newUser = User::addUser($username, $password);
        Cache::store('database')->put($newUser->username, md5($newUser->password), 600);
        return 0;
    }
}
