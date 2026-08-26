<?php

namespace App\Services\Integrations\Xero;

use Exception;
use Carbon\Carbon;
use App\Models\Company\Expense;
use App\Models\Company\Integration;
use Illuminate\Support\Facades\Http;

class SyncExpenseToXero
{
    const CONTACTS_URL = 'https://api.xero.com/api.xro/2.0/Contacts';
    const INVOICES_URL = 'https://api.xero.com/api.xro/2.0/Invoices';

    /**
     * Push an approved expense to Xero as an accounts-payable bill (the
     * employee is the payee), so it can be reconciled and paid there.
     *
     * @param Expense $expense
     */
    public function execute(Expense $expense): void
    {
        $integration = Integration::where('company_id', $expense->company_id)
            ->where('provider', Integration::PROVIDER_XERO)
            ->first();

        if (! $integration || ! $integration->isConnected()) {
            return;
        }

        try {
            $contactId = $this->findOrCreateContact($integration, $expense);
            $invoiceId = $this->createBill($integration, $expense, $contactId);

            $expense->update([
                'external_reference' => $invoiceId,
                'sync_status' => 'synced',
                'synced_at' => Carbon::now(),
            ]);
        } catch (Exception $e) {
            $expense->update(['sync_status' => 'failed']);
            $integration->update(['last_error' => $e->getMessage()]);
        }
    }

    /**
     * @param Integration $integration
     * @param Expense $expense
     *
     * @return string
     */
    private function findOrCreateContact(Integration $integration, Expense $expense): string
    {
        $name = $expense->employee->name;

        $search = $this->xeroRequest($integration, 'get', self::CONTACTS_URL, [
            'where' => 'Name=="'.addslashes($name).'"',
        ]);

        $existing = $search->json()['Contacts'][0]['ContactID'] ?? null;
        if ($existing) {
            return $existing;
        }

        $created = $this->xeroRequest($integration, 'post', self::CONTACTS_URL, [
            'Name' => $name,
            'EmailAddress' => $expense->employee->email,
        ]);

        return $created->json()['Contacts'][0]['ContactID'];
    }

    /**
     * @param Integration $integration
     * @param Expense $expense
     * @param string $contactId
     *
     * @return string
     */
    private function createBill(Integration $integration, Expense $expense, string $contactId): string
    {
        $description = ($expense->category ? $expense->category->name.' - ' : '').$expense->title;

        $response = $this->xeroRequest($integration, 'post', self::INVOICES_URL, [
            'Type' => 'ACCPAY',
            'Contact' => ['ContactID' => $contactId],
            'Date' => Carbon::now()->format('Y-m-d'),
            'DueDate' => Carbon::now()->addDays(14)->format('Y-m-d'),
            'LineItems' => [[
                'Description' => $description,
                'Quantity' => 1,
                'UnitAmount' => $expense->amount / 100,
            ]],
            'Status' => 'AUTHORISED',
        ]);

        if (! isset($response->json()['Invoices'][0]['InvoiceID'])) {
            throw new Exception('Xero did not return an invoice id: '.$response->body());
        }

        return $response->json()['Invoices'][0]['InvoiceID'];
    }

    /**
     * @param Integration $integration
     * @param string $method
     * @param string $url
     * @param array $payload
     *
     * @return \Illuminate\Http\Client\Response
     */
    private function xeroRequest(Integration $integration, string $method, string $url, array $payload)
    {
        $request = Http::withToken($integration->access_token)
            ->withHeaders([
                'Xero-tenant-id' => $integration->external_account_id,
                'Accept' => 'application/json',
            ]);

        $response = $method === 'get'
            ? $request->get($url, $payload)
            : $request->post($url, $payload);

        if ($response->status() === 401) {
            $integration->update(['status' => Integration::STATUS_ERROR, 'last_error' => 'Xero access token rejected (401) - reconnect required.']);
        }

        if ($response->failed()) {
            throw new Exception("Xero request to {$url} failed ({$response->status()}): ".$response->body());
        }

        return $response;
    }
}
