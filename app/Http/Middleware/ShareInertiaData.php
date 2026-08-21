<?php

namespace App\Http\Middleware;

use Inertia\Inertia;
use App\Helpers\LocaleHelper;

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
        // automatically, so that method is dead code. 'errors' is shared
        // here instead, since this is the middleware whose Inertia::share()
        // calls are demonstrably the ones that take effect - every page
        // using the common $page.props.errors.<field> pattern for
        // validation display (100+ templates) needs this key to exist,
        // even as an empty array, or accessing a field on it crashes Vue's
        // render entirely.
        Inertia::share('errors', function () use ($request) {
            return $request->session()->get('errors')
                ? $request->session()->get('errors')->getBag('default')->getMessages()
                : [];
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
