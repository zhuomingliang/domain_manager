<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use Iodev\Whois\Whois;
use Iodev\Whois\Server;
use App\Parser\CommonParser;
use App\Server\WhoisServer;
use App\Domain;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function (){
            $domains = Domain::select(['id', 'domain'])
                ->whereNull('expire_time')
                ->orWhere('expire_time', '<', Carbon::today()->format('Y-m-d H:m:s'))
                ->get();

            foreach($domains as $domain) {
                $_domain = $domain->domain;
                $postfix = str_after($_domain, '.');
                $server  = new Server('.' . $postfix);

                $server->host = isset(WhoisServer::$list[$postfix]) ? WhoisServer::$list[$postfix][0] : '';
                $server->parser = new CommonParser();
                $server->isCentralized = false;

                $whois = Whois::create([$server]);
                if (isset(WhoisServer::$list[$postfix])) {
                    if(preg_match("/^https?:\/\//i", WhoisServer::$list[$postfix][0])) {
                        $whois = Whois::create([$server], new \App\Loader\HttpLoader);
                    }
                }

                $info = $whois->loadInfo($_domain);
                if ($info) {
                    // dd($info->response);
                    $time = Carbon::createFromTimestamp($info->expirationDate);
                    $domain->register_url = $info->registrar;
                    // dump($info->registrar);
                    $domain->expire_time  = $time->format('Y-m-d H:m:s');
                    $domain->save();
                }
            }

            $domains = Domain::select(['id', 'domain'])
                ->Where('expire_time', '<', Carbon::today()->format('Y-m-d H:m:s'))
                ->Where('updated_at', '>', Carbon::today()->format('Y-m-d 00:00:00'))
                ->get();

                foreach($domains as $domain) {
                    $_domain = $domain->domain;
                    $postfix = str_after($_domain, '.');
                    $server  = new Server('.' . $postfix);

                    $server->host = isset(WhoisServer::$list[$postfix]) ? WhoisServer::$list[$postfix][0] : '';
                    $server->parser = new CommonParser();
                    $server->isCentralized = false;

                    $whois = Whois::create([$server], new \App\Loader\CommandLoader);

                    $info = $whois->loadInfo($_domain);

                    if ($info) {
                        $time = Carbon::createFromTimestamp($info->expirationDate);
                        $domain->register_url = $info->registrar;
                        $domain->expire_time  = $time->format('Y-m-d H:m:s');
                        $domain->save();
                    }
                }

        })->everyMinute()->name('whois_everyMinute')->withoutOverlapping();

        $schedule->call(function (){
            $domains = Domain::select(['id', 'domain'])
                ->Where('expire_time', '<', Carbon::today()->addDays(60)->format('Y-m-d 00:00:00'))
                ->Where('updated_at', '<', Carbon::yesterday()->format('Y-m-d 00:00:00'))
                ->get();

            foreach($domains as $domain) {
                $_domain = $domain->domain;
                $postfix = str_after($_domain, '.');
                $server  = new Server('.' . $postfix);

                $server->host = isset(WhoisServer::$list[$postfix]) ? WhoisServer::$list[$postfix][0] : '';
                $server->parser = new CommonParser();
                $server->isCentralized = false;

                $whois = Whois::create([$server]);
                if (isset(WhoisServer::$list[$postfix])) {
                    if(preg_match("/^https?:\/\//i", WhoisServer::$list[$postfix][0])) {
                        $whois = Whois::create([$server], new \App\Loader\HttpLoader);
                    }
                }

                $info = $whois->loadInfo($_domain);

                if ($info) {
                    $time = Carbon::createFromTimestamp($info->expirationDate);
                    $domain->register_url = $info->registrar;
                    $domain->expire_time  = $time->format('Y-m-d H:m:s');
                    $domain->save();
                }
            }
        })->daily()->name('whois_daily');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
