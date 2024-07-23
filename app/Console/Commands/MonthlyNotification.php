<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Carbon\Carbon;
use App\Notificationuser;
use App\Notifications\MonthlyNotification as MonthlyNotificationNotification;

class MonthlyNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    // protected $description = 'Command description';

    protected $signature = 'monthly:notification';
    // protected $signature = 'command:name {contest_id}';

    protected $description = 'Send a monthly notification to users.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // $this->contest_id = $contestId;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Logic to send the notification to winner users
        $endOfMonth = Carbon::now()->endOfMonth();
       
        // $winners = User::select('users.id', 'users.name','users.avatar')
        //     ->whereHas('participatedUsers', function ($q) use ($request) {
        //         $q->where('contest_id', $request->contest_id);
        //     })
        //     ->withCount([
        //         'participatedUsers as total_wins' => function ($q) use ($request) {
        //             $q->where('contest_id', $request->contest_id)->whereNotNull('gift_id');
        //         },
        //     ])
        //     ->get();

        // $contestId =16;
        // $winners = User::select('users.id', 'users.name', 'users.avatar')
        //     ->whereHas('participatedUsers', function ($q) use ($contestId) {
        //         $q->where('contest_id', $contestId);
        //     })
        //     ->withCount([
        //         'participatedUsers as total_wins' => function ($q) use ($contestId) {
        //             $q->where('contest_id', $contestId)->whereNotNull('gift_id');
        //         },
        //     ])
        //     ->get();
        $winners = User::all();

        foreach ($winners as $winner) {
            Notificationuser::send($winner, new MonthlyNotificationNotification());
            // $user = User::find($winner->user_id);
            // // dd($user);
            // if ($user) {
            //     Notificationuser::send($user, new MonthlyNotificationNotification());
            //     // $user->notify(new MonthlyWinnerNotificationNotification());
            // }
        }

        $this->info('Monthly winner notification sent successfully.');


        // return 0;
    
    }
}
