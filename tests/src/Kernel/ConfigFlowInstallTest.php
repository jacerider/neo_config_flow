<?php

declare(strict_types=1);

namespace Drupal\Tests\neo_config_flow\Kernel;

use Drupal\Core\Config\FileStorage;
use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\Group;

/**
 * neo_config_flow's install hook, which writes its split and ignore settings.
 *
 * On a fresh install that is the module's purpose. Two cases must not take the
 * bundled settings: a site that already has its own (moving to this module
 * from another one), and a config import, where the site's own settings arrive
 * in the same import. The bundled ignore list names nothing real, so a site
 * left with it stops ignoring the config it keeps out of sync — its webforms,
 * say — and the next import deletes what the sync directory does not hold.
 */
#[Group('neo_config_flow')]
final class ConfigFlowInstallTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['system'];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    require_once $this->root . '/' . $this->bundledPath() . '/../../neo_config_flow.install';
  }

  /**
   * A fresh install writes every bundled setting.
   */
  public function testInstallWritesBundledSettings(): void {
    $bundled = new FileStorage($this->bundledPath());
    $storage = $this->container->get('config.storage');

    neo_config_flow_install(FALSE);

    $this->assertNotEmpty($bundled->listAll(''));
    foreach ($bundled->listAll('') as $name) {
      $this->assertSame($bundled->read($name), $storage->read($name), "$name is the bundled setting.");
    }
  }

  /**
   * Settings the site already has are kept.
   */
  public function testInstallKeepsWhatTheSiteAlreadyHas(): void {
    $bundled = new FileStorage($this->bundledPath());
    $storage = $this->container->get('config.storage');
    $name = 'config_ignore.settings';
    $own = ['mode' => 'simple', 'ignored_config_entities' => ['webform.webform.*', 'system.site']];
    $storage->write($name, $own);

    neo_config_flow_install(FALSE);

    $this->assertSame($own, $storage->read($name), 'The site keeps its own ignore list.');
    // The rest is still written.
    foreach (array_diff($bundled->listAll(''), [$name]) as $other) {
      $this->assertSame($bundled->read($other), $storage->read($other), "$other is the bundled setting.");
    }
  }

  /**
   * A config import writes nothing at all.
   */
  public function testInstallDuringAConfigImportWritesNothing(): void {
    $bundled = new FileStorage($this->bundledPath());
    $storage = $this->container->get('config.storage');

    neo_config_flow_install(TRUE);

    foreach ($bundled->listAll('') as $name) {
      $this->assertNull($storage->read($name) ?: NULL, "$name was not written during the import.");
    }
  }

  /**
   * The module's bundled config directory.
   */
  private function bundledPath(): string {
    return $this->container->get('extension.list.module')->getPath('neo_config_flow') . '/config/optional';
  }

}
