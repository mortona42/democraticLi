<?php

namespace Drupal\simple_page_manager;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Menu\MenuParentFormSelectorInterface;
use Drupal\simple_page_manager\Entity\Page;
use Drupal\user\PermissionHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for page forms.
 *
 * @internal
 */
class PageForm extends EntityForm {

  /**
   * The menu parent selector.
   *
   * @var \Drupal\Core\Menu\MenuParentFormSelectorInterface
   */
  protected $menuParentSelector;

  /**
   * The permission handler.
   *
   * @var \Drupal\user\PermissionHandlerInterface
   */
  protected $permissionHandler;

  /**
   * Constructs the PageForm object.
   *
   * @param \Drupal\user\PermissionHandlerInterface $permission_handler
   *   The permission handler.
   * @param \Drupal\Core\Menu\MenuParentFormSelectorInterface $menu_parent_selector
   *   The menu parent form selector.
   */
  public function __construct(PermissionHandlerInterface $permission_handler, MenuParentFormSelectorInterface $menu_parent_selector) {
    $this->permissionHandler = $permission_handler;
    $this->menuParentSelector = $menu_parent_selector;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.permissions'),
      $container->get('menu.parent_form_selector'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    if ($this->operation === 'add') {
      $form['#title'] = $this->t('Add page');
    }

    $form['label'] = [
      '#title' => $this->t('Name'),
      '#type' => 'textfield',
      '#default_value' => $this->entity->label(),
      '#description' => $this->t('The human-readable name of this page'),
      '#required' => TRUE,
      '#size' => 30,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $this->entity->id(),
      '#maxlength' => 32,
      '#disabled' => !$this->entity->isNew(),
      '#machine_name' => [
        'exists' => [Page::class, 'load'],
      ],
      '#description' => $this->t('A unique machine-readable name for this page.'),
    ];

    $form['path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Path'),
      '#default_value' => $this->entity->getPath(),
      '#maxlength' => '255',
      '#required' => TRUE,
      '#element_validate' => [[$this, 'validatePath']],
    ];

    $menu = $this->entity->getMenu();

    $form['menu'] = [
      '#type' => 'details',
      '#title' => t('Menu settings'),
      '#access' => $this->currentUser()->hasPermission('administer menu'),
      '#open' => FALSE,
      '#group' => 'advanced',
      '#tree' => TRUE,
      '#attributes' => ['class' => ['menu-link-form']],
    ];

    // Enhance the menu user interface if the Menu UI module is enabled.
    if ($this->moduleHandler->moduleExists('menu_ui')) {
      $form['menu']['#attached'] = [
        'library' => ['menu_ui/drupal.menu_ui'],
      ];
    }

    $form['menu']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Provide a menu link'),
      '#default_value' => $menu['enabled'],
    ];
    $form['menu']['link'] = [
      '#type' => 'container',
      '#parents' => ['menu'],
      '#states' => [
        'invisible' => [
          'input[name="menu[enabled]"]' => ['checked' => FALSE],
        ],
      ],
    ];

    $form['menu']['link']['title'] = [
      '#type' => 'textfield',
      '#title' => t('Menu link title'),
      '#default_value' => $menu['title'],
    ];

    $form['menu']['link']['description'] = [
      '#type' => 'textfield',
      '#title' => t('Description'),
      '#description' => $this->t('Shown when hovering over the menu link.'),
      '#default_value' => $menu['description'],
    ];

    $menu_parent = $menu['menu_name'] . ':' . $menu['parent'];
    $menu_link = 'page.' . $this->entity->id();

    $form['menu']['link']['menu_parent'] = $this->menuParentSelector->parentSelectElement($menu_parent, $menu_link);
    $form['menu']['link']['menu_parent']['#title'] = $this->t('Parent link');
    $form['menu']['link']['menu_parent']['#attributes']['class'][] = 'menu-parent-select';

    $form['menu']['link']['weight'] = [
      '#type' => 'number',
      '#title' => $this->t('Weight'),
      '#default_value' => $menu['weight'],
      '#description' => t('Menu links with lower weights are displayed before links with higher weights.'),
    ];

    $access = $this->entity->getAccess();

    $form['access'] = [
      '#type' => 'details',
      '#title' => t('Access settings'),
      '#open' => FALSE,
      '#group' => 'advanced',
      '#tree' => TRUE,
    ];

    $form['access']['type'] = [
      '#title' => $this->t('Access type'),
      '#type' => 'radios',
      '#options' => [
        'role' => 'Role',
        'permission' => 'Permission',
        'none' => 'None',
      ],
      '#default_value' => $access['type'],
      '#required' => TRUE,
    ];

    $form['access']['role'] = [
      '#title' => $this->t('Role'),
      '#type' => 'radios',
      '#options' => user_role_names(),
      '#states' => [
        'visible' => [
          ':input[name="access[type]"]' => ['value' => 'role'],
        ],
      ],
      '#default_value' => $access['type'] === 'role' ? $access['options']['role'] : FALSE,
    ];

    $permissions = $this->permissionHandler->getPermissions();
    $permissions_options = [];

    foreach ($permissions as $permission_name => $permission) {
      $provider = $permission['provider'];
      $display_name = $this->moduleHandler->getName($provider);
      $permissions_options[$display_name][$permission_name] = strip_tags($permission['title']);
    }

    $form['access']['permission'] = [
      '#title' => $this->t('Permission'),
      '#type' => 'select',
      '#options' => $permissions_options,
      '#states' => [
        'visible' => [
          ':input[name="access[type]"]' => ['value' => 'permission'],
        ],
      ],
      '#default_value' => $access['type'] === 'permission' ? $access['options']['permission'] : FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $access = $form_state->getValue('access');

    if ($access['type'] === 'role') {
      if (empty($access['role'])) {
        $form_state->setError($form['access']['role'], $this->t('You must select a role when using the Role access type.'));
      }
    }
  }

  /**
   * Validates the path.
   *
   * @param array $element
   *   The element being validated.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function validatePath(array &$element, FormStateInterface $form_state) {
    $path = $form_state->getValue('path');

    if ($path[0] !== '/') {
      $form_state->setErrorByName('path', $this->t('Path must start with a leading slash.'));
      return;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state) {
    parent::copyFormValuesToEntity($entity, $form, $form_state);

    $menu = $form_state->getValue('menu');

    if (!$menu['enabled']) {
      $entity->removeMenuLink();
    }
    else {
      [$menu_name, $parent] = explode(':', $menu['menu_parent'], 2);
      $menu['menu_name'] = $menu_name;
      $menu['parent'] = $parent;

      unset($menu['menu_parent']);

      $entity->set('menu', $menu);
    }

    $access = $form_state->getValue('access');

    $access_data = [
      'type' => 'none',
      'options' => [],
    ];

    if ($access['type'] === 'permission') {
      $access_data = [
        'type' => 'permission',
        'options' => [
          'permission' => $access['permission'],
        ]
      ];
    }

    if ($access['type'] === 'role') {
      $access_data = [
        'type' => 'role',
        'options' => [
          'role' => $access['role'],
        ]
      ];
    }

    $entity->set('access', $access_data);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);

    $form_state->setRedirectUrl($this->entity->toUrl('collection'));
  }

}
