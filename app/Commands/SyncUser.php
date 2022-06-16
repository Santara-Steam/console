<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use LaravelZero\Framework\Commands\Command;

class SyncUser extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sync';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Sync user between santara and chatgroup';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("running...");
	users = DB::connection('mysql')
            ->table('users')
            ->join('traders', 'users.id', '=', 'traders.user_id')
            ->get(['users.*', 'traders.name']);
	
	$this->info($users->isEmpty());

        foreach ($users as $user) {
            $chatUser = DB::connection('mysql2')
                ->table('users')
                ->find($user->id);
	var_dump($chatUser);

            if (!$chatUser) {

                $email = $user->email;

                DB::connection('mysql2')->table('users')
                    ->insert([
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $email,
                        'email_verified_at' => now()->format('Y-m-d H:i:s'),
                        'password' => $user->password,
                        'is_active' => true,
                ]);

                $this->info("record with email {$email} created");
            }
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
