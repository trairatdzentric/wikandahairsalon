<?php
/**
 * Reads and saves external integration settings from the JSON database.
 */

namespace App\Services;

use App\Repositories\SettingRepository;

class IntegrationSettingsService
{
    private SettingRepository $settings;

    public function __construct()
    {
        $this->settings = new SettingRepository();
    }

    public function all(): array
    {
        return [
            'line' => [
                'enabled' => (bool) $this->settings->value('line.enabled', false),
                'channel_access_token' => (string) $this->settings->value('line.channel_access_token', ''),
            ],
            'slip2go' => [
                'enabled' => (bool) $this->settings->value('slip2go.enabled', false),
                'api_key' => (string) $this->settings->value('slip2go.api_key', ''),
            ],
        ];
    }

    public function lineConfig(array $defaults): array
    {
        $line = $this->all()['line'];
        if ($line['channel_access_token'] === '') {
            unset($line['channel_access_token']);
        }

        return array_merge($defaults, $line);
    }

    public function slip2goConfig(array $defaults): array
    {
        $slip2go = $this->all()['slip2go'];
        if ($slip2go['api_key'] === '') {
            unset($slip2go['api_key']);
        }

        return array_merge($defaults, $slip2go);
    }

    public function save(array $input): void
    {
        $this->settings->upsert('line.enabled', !empty($input['line_enabled']), 'boolean');
        $this->settings->upsert('slip2go.enabled', !empty($input['slip2go_enabled']), 'boolean');

        $lineToken = trim((string) ($input['line_channel_access_token'] ?? ''));
        if ($lineToken !== '') {
            $this->settings->upsert('line.channel_access_token', $lineToken, 'secret');
        }

        $slip2goKey = trim((string) ($input['slip2go_api_key'] ?? ''));
        if ($slip2goKey !== '') {
            $this->settings->upsert('slip2go.api_key', $slip2goKey, 'secret');
        }
    }

    public function status(): array
    {
        $settings = $this->all();

        return [
            'line_configured' => $settings['line']['channel_access_token'] !== '',
            'line_enabled' => $settings['line']['enabled'],
            'slip2go_configured' => $settings['slip2go']['api_key'] !== '',
            'slip2go_enabled' => $settings['slip2go']['enabled'],
        ];
    }
}
