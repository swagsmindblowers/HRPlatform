<?php

namespace App\Http\Middleware;

use Inertia\Inertia;
use App\Helpers\LocaleHelper;
use App\Helpers\InstanceHelper;

/**
 * Used by Jetstream to share data.
 * We needed to override this custom middleware in order to make
 * sure we pass the right data to the view.
 */
class ShareInertiaData
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  callable  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        // HandleInertiaRequests::share() (auth/errors/flash/demo_mode/help_links)
        // never actually reaches the response: this app's inertia-laravel
        // version (v0.2.5) doesn't invoke a middleware's share() hook
        // automatically, so that method is dead code. Its props are shared
        // here instead, since this is the middleware whose Inertia::share()
        // calls are demonstrably the ones that take effect. 'auth' alone is
        // read by 200+ templates (starting with the shared Layout every
        // page renders through) and 'errors' by 100+ more for validation
        // display - without them, the first access throws and crashes
        // Vue's render entirely.
        Inertia::share('auth', function () use ($request) {
            return [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'first_name' => $request->user()->first_name,
                    'last_name' => $request->user()->last_name,
                    'email' => $request->user()->email,
                    'name' => $request->user()->name,
                    'show_help' => $request->user()->show_help,
                    'locale' => $request->user()->locale,
                ] : null,
                'company' => $request->user() && ! is_null(InstanceHelper::getLoggedCompany()) ? InstanceHelper::getLoggedCompany() : null,
                'employee' => $request->user() && ! is_null(InstanceHelper::getLoggedEmployee()) ? [
                    'id' => InstanceHelper::getLoggedEmployee()->id,
                    'first_name' => InstanceHelper::getLoggedEmployee()->first_name,
                    'last_name' => InstanceHelper::getLoggedEmployee()->last_name,
                    'name' => InstanceHelper::getLoggedEmployee()->name,
                    'permission_level' => InstanceHelper::getLoggedEmployee()->permission_level,
                    'display_welcome_message' => InstanceHelper::getLoggedEmployee()->display_welcome_message,
                    'user' => (! InstanceHelper::getLoggedEmployee()->user) ? null : [
                        'id' => InstanceHelper::getLoggedEmployee()->user_id,
                    ],
                ] : null,
            ];
        });

        Inertia::share('demo_mode', function () {
            return config('officelife.demo_mode');
        });

        Inertia::share('help_links', function () {
            return config('officelife.help_links');
        });

        Inertia::share('errors', function () use ($request) {
            return $request->session()->get('errors')
                ? $request->session()->get('errors')->getBag('default')->getMessages()
                : [];
        });

        Inertia::share('flash', function () use ($request) {
            return [
                'message' => $request->session()->get('message'),
                'success' => $request->session()->get('success'),
            ];
        });

        Inertia::share('jetstream', function () use ($request) {
            return [
                'flash' => $request->session()->get('flash', []),
                'languages' => LocaleHelper::getLocaleList(),
                'enableSignups' => config('officelife.enable_signups'),
            ];
        });

        Inertia::share('user', function () use ($request) {
            if (! $request->user()) {
                return;
            }

            return [
                'two_factor_enabled' => ! is_null($request->user()->two_factor_secret),
            ];
        });

        Inertia::share('errorBags', function () use ($request) {
            return collect(optional($request->session()->get('errors'))->getBags() ?: [])->mapWithKeys(function ($bag, $key) {
                return [$key => $bag->messages()];
            })->all();
        });

        return $next($request);
    }
}
