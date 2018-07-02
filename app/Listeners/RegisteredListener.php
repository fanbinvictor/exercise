<?php

namespace App\Listeners;

use App\Notifications\EmailVerificationNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

//实现ShouldQueue 接口 让监视器异步执行
class RegisteredListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 当事件被触发时,对应事件的监听器handle()方法会被调用,不是很明白user会在$event中
     *
     * @param  object  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        //获取刚刚注册的用户
        $user=$event->user;
        //调用notify发送通知
        $user->notify(new EmailVerificationNotification());
    }
}
