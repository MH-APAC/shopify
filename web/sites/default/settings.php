<?php

/**
 * Location of the site configuration files.
 */
$settings['config_sync_directory'] = '../sync';

/**
 * Salt for one-time login links, cancel links, form tokens, etc.
 */
// SET SALT.
$settings['hash_salt'] = getenv('DRUPAL_HASH_SALT');

/**
 * Access control for update.php script.
 */
$settings['update_free_access'] = FALSE;

/**
 * Load common services definition file.
 */
$settings['container_yamls'][] = __DIR__ . '/services.yml';

/**
 * Temporary file path:
 *
 * A local file system path where temporary files will be stored. This directory
 * must be absolute, outside of the Drupal installation directory and not
 * accessible over the web.
 *
 * If this is not set, the default for the operating system will be used.
 *
 * @see \Drupal\Component\FileSystem\FileSystem::getOsTemporaryDirectory()
 */
$settings['file_temp_path'] = '/tmp';

/**
 * Access control for update.php script.
 */
$settings['update_free_access'] = FALSE;
$settings['maintenance_theme'] = 'claro';

/**
 * Fast 404 pages.
 */
$config['system.performance']['fast_404']['exclude_paths'] = '/\/(?:styles)|(?:system\/files)\//';
$config['system.performance']['fast_404']['paths'] = '/\.(?:txt|png|gif|jpe?g|css|js|ico|swf|flv|cgi|bat|pl|dll|exe|asp)$/i';
$config['system.performance']['fast_404']['html'] = '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL "@path" was not found on this server.</p></body></html>';

/**
 * Trusted host configuration. String with comma as a separator.
 */
$settings['trusted_host_patterns'] = explode(',', getenv('TRUSTED_HOST_PATTERNS'));

# Cache
if (!\Drupal\Core\Installer\InstallerKernel::installationAttempted()) {
  $settings['redis.connection']['interface'] = 'PhpRedis';
  $settings['redis.connection']['host'] = getenv('REDIS_HOST');
  $settings['redis.connection']['port'] = getenv('REDIS_PORT');
  $settings['cache']['default'] = 'cache.backend.redis';
  $settings['cache_prefix']['default'] = getenv('REDIS_CACHE_PREFIX');
  $settings['container_yamls'][] = DRUPAL_ROOT . '/modules/contrib/redis/example.services.yml';
}

/**
 * Files system path settings.
 */
$settings['file_private_path'] = '/app/private';
$settings['file_public_path'] = 'sites/default/files';
$config['locale.settings']['translation']['path'] = 'sites/default/files/translations';

/**
 * Database settings.
 */
$databases = [];

$databases['default']['default'] = [
  // See .env file for the DB name.
  'database' => getenv('DB_NAME'),
  'username' => getenv('DB_USER'),
  'password' => getenv('DB_PASSWORD'),
  'host' => getenv('DB_HOST'),
  'port' => getenv('DB_PORT'),
  'driver' => 'mysql',
  'prefix' => '',
];

/**
 * SSO SAML config overrides.
 */
$config['simplesamlphp_auth.settings']['auth_source'] = 'default-sp';

/**
 * SMTP configs.
 */
$config['smtp.settings']['smtp_on'] = 'true'; // Set false for local env.
$config['smtp.settings']['smtp_host'] = 'relay-smtp.relay-smtp.svc.cluster.local';
$config['smtp.settings']['smtp_port'] = '25';
$config['smtp.settings']['smtp_username'] = '';
$config['smtp.settings']['smtp_password'] = '';
$config['mailsystem.settings']['defaults']['sender'] = 'SMTPMailSystem';

/**
 * LOG STDOUT configs.
 */
$config['log_stdout.settings']['format'] = 'drupal_log|@base_url|@timestamp|@type|@ip|@request_uri|@referer|@uid|@link|@message';
$config['log_stdout.settings']['use_stderr'] = null;

/**
 * Include site and environment specific override settings.
 */
$environment = getenv('APP_ENV');
if (file_exists(__DIR__ . "/$environment.settings.inc")) {
  include "$environment.settings.inc";
}
