<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;

class StageCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $timezone = get_setting('site_timezone', 'UTC');
        $current_date = now()->timezone($timezone);
        $start_date = Carbon::parse(active_stage()->start_date, $timezone);
        $end_date   = Carbon::parse(active_stage()->end_date, $timezone);
        
        if ($current_date->gte($start_date) && $current_date->lte($end_date)) {
            return $next($request);
        } elseif ($start_date->gte($current_date) && $current_date->lte($end_date)) {
            return $next($request);
        } elseif ($current_date->gt($end_date) && active_stage()->soldout > 0) {
            $chk_stg = ['info' => __('messages.stage.completed')];
            return redirect(route('user.home'))->with($chk_stg);
        } else {
            $chk_stg = active_stage()->end_date == def_datetime('datetime_e') ? ['warning' => __('messages.stage.not_started')] : ['warning' => __('messages.stage.expired')];
            return redirect(route('user.home'))->with($chk_stg);
        }
    }
}
