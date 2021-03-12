<?php

namespace Drupal\simple_page_manager\Plugin\SectionStorage;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\EntityContext;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TempStore\SharedTempStoreFactory;
use Drupal\Core\Url;
use Drupal\layout_builder\Entity\SampleEntityGeneratorInterface;
use Drupal\layout_builder\Plugin\SectionStorage\SectionStorageBase;
use Drupal\simple_page_manager\Entity\Page;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Defines the 'page' section storage type.
 *
 * @SectionStorage(
 *   id = "page",
 *   context_definitions = {
 *     "entity" = @ContextDefinition("entity:page"),
 *   },
 * )
 */
class PageSectionStorage extends SectionStorageBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The sample entity generator.
   *
   * @var \Drupal\layout_builder\Entity\SampleEntityGeneratorInterface
   */
  protected $sampleEntityGenerator;

  /**
   * The tempstore factory.
   *
   * @var \Drupal\Core\TempStore\SharedTempStoreFactory
   */
  protected $tempstore;

  /**
   * The entity bundle info.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * PageSectionStorage constructor.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\layout_builder\Entity\SampleEntityGeneratorInterface $sample_entity_generator
   *   The sample entity generator.
   * @param \Drupal\Core\TempStore\SharedTempStoreFactory $tempstore
   *   The tempstore factory.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity bundle information.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, SampleEntityGeneratorInterface $sample_entity_generator, SharedTempStoreFactory $tempstore, EntityTypeBundleInfoInterface $entity_type_bundle_info) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->entityTypeManager = $entity_type_manager;
    $this->sampleEntityGenerator = $sample_entity_generator;
    $this->tempstore = $tempstore;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('layout_builder.sample_entity_generator'),
      $container->get('tempstore.shared'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getSectionList() {
    return $this->getContextValue('entity');
  }

  /**
   * Gets the page entity.
   *
   * @return \Drupal\simple_page_manager\Entity\Page
   *   The page entity.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function getPage() {
    return $this->getContextValue('entity');
  }

  /**
   * {@inheritdoc}
   */
  public function getStorageId() {
    return $this->getContextValue('entity')->id();
  }

  /**
   * {@inheritdoc}
   */
  public function getRedirectUrl() {
    return $this->getPage()->toUrl('canonical');
  }

  /**
   * {@inheritdoc}
   */
  public function getLayoutBuilderUrl($rel = 'view') {
    return Url::fromRoute('layout_builder.page.view', ['page' => $this->getPage()->id()]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildRoutes(RouteCollection $collection) {
    $path = '/admin/structure/pages/manage/{page}/layout';

    $options['parameters']['page']['type'] = 'entity:page';
    $options['_admin_route'] = FALSE;

    $this->buildLayoutRoutes($collection, $this->getPluginDefinition(), $path, [], [], $options, '', 'page');
  }

  /**
   * {@inheritdoc}
   */
  public function deriveContextsFromRoute($value, $definition, $name, array $defaults) {
    // Try to load from defaults.
    $entity = $this->extractEntityFromRoute($value, $defaults);

    return [
      'entity' => EntityContext::fromEntity($entity),
    ];
  }

  /**
   * {@inheritdoc}
   */
  private function extractEntityFromRoute($value, array $defaults) {
    $storage = $this->entityTypeManager->getStorage('page');

    if (!empty($value)) {
      return $storage->load($value);
    }

    return $storage->load($defaults['page']);
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    return $this->getPage()->label();
  }

  /**
   * {@inheritdoc}
   */
  public function save() {
    $page = $this->getPage();
    return $page->save();
  }

  /**
   * {@inheritdoc}
   */
  public function access($operation, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = AccessResult::allowedIf($this->isPage())->addCacheableDependency($this);
    return $return_as_object ? $result : $result->isAllowed();
  }

  /**
   * {@inheritdoc}
   */
  public function isApplicable(RefinableCacheableDependencyInterface $cacheability) {
    return $this->isPage();
  }

  /**
   * Checks if the entity context is an instance of Page.
   *
   * @return bool
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function isPage() {
    return $this->getContextValue('entity') instanceof Page;
  }

  /**
   * {@inheritdoc}
   */
  public function getContextsDuringPreview() {
    $contexts = parent::getContextsDuringPreview();

    $page = $this->getPage();
    $contexts['layout_builder.entity'] = EntityContext::fromEntity($page);

    return $contexts;
  }

}
