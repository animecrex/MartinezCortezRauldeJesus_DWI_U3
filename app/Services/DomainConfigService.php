<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DomainConfigService
{
    private const CACHE_KEY = 'cursoplus_domain_config_v1';

    /**
     * Devuelve la configuración actual de dominio enriquecida con contexto Laravel y plantillas.
     */
    public function obtenerConfiguracion(?Request $request = null): array
    {
        $config = Cache::get(self::CACHE_KEY);

        if (!is_array($config) || empty($config['dominio_principal'])) {
            $config = $this->configuracionPorDefecto();
            Cache::put(self::CACHE_KEY, $config, now()->addHours(24));
        }

        return $this->enriquecer($config, $request);
    }

    /**
     * Actualiza la configuración de dominio en caché.
     */
    public function actualizar(array $datos, ?Request $request = null): array
    {
        $config = [
            'dominio_principal' => strtolower(trim($datos['dominio_principal'])),
            'subdominio_api' => strtolower(trim($datos['subdominio_api'] ?: 'api')),
            'ip_destino' => trim($datos['ip_destino']),
            'ttl_dns' => (int) ($datos['ttl_dns'] ?? 3600),
            'forzar_https' => (bool) ($datos['forzar_https'] ?? false),
            'ultima_actualizacion' => now()->format('d/m/Y H:i:s'),
        ];

        Cache::put(self::CACHE_KEY, $config, now()->addHours(24));

        return $this->enriquecer($config, $request);
    }

    /**
     * Restablece la configuración de dominio a sus valores por defecto.
     */
    public function restablecer(?Request $request = null): array
    {
        $config = $this->configuracionPorDefecto();
        Cache::put(self::CACHE_KEY, $config, now()->addHours(24));

        return $this->enriquecer($config, $request);
    }

    /**
     * Ejecuta una verificación de resolución DNS / IP en vivo para un dominio dado.
     */
    public function verificarDns(string $dominio): array
    {
        $dominio = strtolower(trim($dominio));
        $inicio = microtime(true);

        $ip = @gethostbyname($dominio);
        $resuelto = ($ip !== $dominio && filter_var($ip, FILTER_VALIDATE_IP) !== false);

        $registros = [];
        if (function_exists('dns_get_record')) {
            $raw = @dns_get_record($dominio, DNS_A | DNS_CNAME | DNS_MX | DNS_TXT);
            if (is_array($raw)) {
                foreach (array_slice($raw, 0, 8) as $rec) {
                    $registros[] = [
                        'tipo' => $rec['type'] ?? 'A',
                        'host' => $rec['host'] ?? $dominio,
                        'valor' => $rec['ip'] ?? $rec['target'] ?? $rec['txt'] ?? '—',
                        'ttl' => $rec['ttl'] ?? 0,
                    ];
                }
            }
        }

        $latenciaMs = round((microtime(true) - $inicio) * 1000, 2);

        return [
            'dominio' => $dominio,
            'resuelto' => $resuelto,
            'ip_resuelta' => $resuelto ? $ip : 'No resuelto en DNS local/público',
            'latencia_ms' => $latenciaMs,
            'registros' => $registros,
            'hora' => now()->format('H:i:s'),
        ];
    }

    private function configuracionPorDefecto(): array
    {
        return [
            'dominio_principal' => 'cursoplus.local',
            'subdominio_api' => 'api',
            'ip_destino' => '127.0.0.1',
            'ttl_dns' => 3600,
            'forzar_https' => true,
            'ultima_actualizacion' => now()->format('d/m/Y H:i:s'),
        ];
    }

    private function enriquecer(array $config, ?Request $request = null): array
    {
        $dom = $config['dominio_principal'];
        $apiSub = $config['subdominio_api'];
        $ip = $config['ip_destino'];
        $ttl = (int) $config['ttl_dns'];
        $https = (bool) $config['forzar_https'];
        $esquema = $https ? 'https' : 'http';
        $urlCompleta = "{$esquema}://{$dom}";
        $urlApi = "{$esquema}://{$apiSub}.{$dom}/api/v1";

        $registrosDns = [
            ['tipo' => 'A', 'host' => '@', 'destino' => $ip, 'ttl' => $ttl, 'proposito' => 'Dominio raíz de la plataforma Curso+'],
            ['tipo' => 'CNAME', 'host' => 'www', 'destino' => "{$dom}.", 'ttl' => $ttl, 'proposito' => 'Alias web principal hacia el dominio raíz'],
            ['tipo' => 'A', 'host' => $apiSub, 'destino' => $ip, 'ttl' => $ttl, 'proposito' => 'Subdominio dedicado a Web Services REST (/api/v1)'],
            ['tipo' => 'MX', 'host' => '@', 'destino' => "10 mail.{$dom}.", 'ttl' => $ttl, 'proposito' => 'Enrutamiento de correo para verificación y recuperación'],
            ['tipo' => 'TXT', 'host' => '@', 'destino' => "v=spf1 ip4:{$ip} -all", 'ttl' => $ttl, 'proposito' => 'Política SPF anti-suplantación de correo'],
        ];

        $docRoot = str_replace('\\', '/', public_path());

        $apacheVhost = "<VirtualHost *:80>\n"
            . "    ServerName {$dom}\n"
            . "    ServerAlias www.{$dom} {$apiSub}.{$dom}\n"
            . "    DocumentRoot \"{$docRoot}\"\n"
            . "    <Directory \"{$docRoot}\">\n"
            . "        Options -Indexes +FollowSymLinks\n"
            . "        AllowOverride All\n"
            . "        Require all granted\n"
            . "    </Directory>\n"
            . ($https ? "    # Redirección forzada a HTTPS\n    Redirect permanent / https://{$dom}/\n" : '')
            . "</VirtualHost>";

        if ($https) {
            $apacheVhost .= "\n\n<VirtualHost *:443>\n"
                . "    ServerName {$dom}\n"
                . "    ServerAlias www.{$dom} {$apiSub}.{$dom}\n"
                . "    DocumentRoot \"{$docRoot}\"\n"
                . "    SSLEngine on\n"
                . "    SSLCertificateFile \"conf/ssl.crt/server.crt\"\n"
                . "    SSLCertificateKeyFile \"conf/ssl.key/server.key\"\n"
                . "    Header always set Strict-Transport-Security \"max-age=31536000; includeSubDomains\"\n"
                . "</VirtualHost>";
        }

        $hostsWindows = "# Entradas locales para Curso+ (C:\\Windows\\System32\\drivers\\etc\\hosts)\n"
            . "{$ip}    {$dom}\n"
            . "{$ip}    www.{$dom}\n"
            . "{$ip}    {$apiSub}.{$dom}";

        $envSnippet = "APP_URL={$urlCompleta}\n"
            . "SESSION_DOMAIN=.{$dom}\n"
            . "SESSION_SECURE_COOKIE=" . ($https ? 'true' : 'false') . "\n"
            . "SESSION_SAME_SITE=lax\n"
            . "SANCTUM_STATEFUL_DOMAINS={$dom},www.{$dom},{$apiSub}.{$dom}";

        return [
            'config' => $config,
            'urls' => [
                'principal' => $urlCompleta,
                'www' => "{$esquema}://www.{$dom}",
                'api' => $urlApi,
            ],
            'entorno_actual' => [
                'app_url' => config('app.url'),
                'host_peticion' => $request ? $request->getHost() : 'localhost',
                'puerto_peticion' => $request ? $request->getPort() : 80,
                'esquema_peticion' => $request ? strtoupper($request->getScheme()) : 'HTTP',
                'session_domain' => config('session.domain') ?: '(Dominio actual automático)',
                'session_secure' => config('session.secure') ? 'Habilitado (HTTPS)' : 'Flexible (Local HTTP/HTTPS)',
                'session_same_site' => config('session.same_site', 'lax'),
                'sanctum_domains' => implode(', ', (array) config('sanctum.stateful', [])),
                'document_root' => $docRoot,
            ],
            'registros_dns' => $registrosDns,
            'plantillas' => [
                'apache_vhost' => $apacheVhost,
                'hosts_windows' => $hostsWindows,
                'env_laravel' => $envSnippet,
            ],
        ];
    }
}
