<?php

namespace App\Http\Controllers\Integrations;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Helpers\InstanceHelper;
use App\Helpers\NotificationHelper;
use App\Models\Company\Integration;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Services\Integrations\Deel\ConnectDeel;
use App\Services\Integrations\Xero\ConnectXero;
use App\Services\Integrations\DisconnectIntegration;
use App\Services\Integrations\Deel\HandleDeelCallback;
use App\Services\Integrations\Xero\HandleXeroCallback;

class IntegrationsController extends Controller
{
    /**
     * The connectors panel: connect/disconnect Xero and Deel.
     *
     * @return Response
     */
    public function index(): Response
    {
        $company = InstanceHelper::getLoggedCompany();
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        $integrations = Integration::where('company_id', $company->id)->get()->keyBy('provider');

        $format = function (?Integration $integration) {
            return [
                'status' => $integration->status ?? Integration::STATUS_DISCONNECTED,
                'connected_at' => optional($integration)->connected_at,
                'last_error' => optional($integration)->last_error,
            ];
        };

        return Inertia::render('Integrations/Index', [
            'notifications' => NotificationHelper::getNotifications($loggedEmployee),
            'integrations' => [
                'xero' => $format($integrations->get(Integration::PROVIDER_XERO)),
                'deel' => $format($integrations->get(Integration::PROVIDER_DEEL)),
            ],
        ]);
    }

    /**
     * Redirect to the provider's OAuth authorize page.
     *
     * @param Request $request
     * @param int $companyId
     * @param string $provider
     * @return RedirectResponse
     */
    public function connect(Request $request, int $companyId, string $provider): RedirectResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();
        $state = Str::random(40);

        $request->session()->put('integration_oauth', [
            'state' => $state,
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'provider' => $provider,
        ]);

        $data = [
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'state' => $state,
        ];

        $authorizeUrl = match ($provider) {
            Integration::PROVIDER_XERO => (new ConnectXero)->execute($data),
            Integration::PROVIDER_DEEL => (new ConnectDeel)->execute($data),
            default => abort(404),
        };

        return redirect($authorizeUrl);
    }

    /**
     * Handle the OAuth callback redirect from the provider.
     *
     * @param Request $request
     * @param string $provider
     * @return RedirectResponse
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        $pending = $request->session()->get('integration_oauth');
        $request->session()->forget('integration_oauth');

        if (! $pending || $pending['provider'] !== $provider || $pending['state'] !== $request->query('state')) {
            abort(403, 'Invalid or expired OAuth state.');
        }

        $data = [
            'company_id' => $pending['company_id'],
            'author_id' => $pending['author_id'],
            'code' => $request->query('code'),
        ];

        try {
            match ($provider) {
                Integration::PROVIDER_XERO => (new HandleXeroCallback)->execute($data),
                Integration::PROVIDER_DEEL => (new HandleDeelCallback)->execute($data),
                default => abort(404),
            };
        } catch (Exception $e) {
            return redirect('/'.$pending['company_id'].'/integrations')
                ->with('message', 'Failed to connect: '.$e->getMessage());
        }

        return redirect('/'.$pending['company_id'].'/integrations')
            ->with('success', 'Connected successfully.');
    }

    /**
     * Disconnect an integration.
     *
     * @param Request $request
     * @param int $companyId
     * @param string $provider
     * @return RedirectResponse
     */
    public function disconnect(Request $request, int $companyId, string $provider): RedirectResponse
    {
        $loggedEmployee = InstanceHelper::getLoggedEmployee();

        (new DisconnectIntegration)->execute([
            'company_id' => $companyId,
            'author_id' => $loggedEmployee->id,
            'provider' => $provider,
        ]);

        return redirect('/'.$companyId.'/integrations');
    }
}
