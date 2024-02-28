<?php

namespace Drupal\qs_sharing\Form;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_sharing\Repository\VolunteerismRepository;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to manage Volunteerism preferences.
 */
class VolunteerismManageForm extends FormBase {

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * The entity storage for users.
   *
   * @var \Drupal\user\UserStorage
   */
  private $userStorage;

  /**
   * The Volunteerism repository.
   *
   * @var \Drupal\qs_sharing\Repository\VolunteerismRepository
   */
  private $volunteerismRepository;

  /**
   * The Volunteerism storage.
   *
   * @var \Drupal\Core\Entity\ContentEntityStorageInterface
   */
  private $volunteerismStorage;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, EntityTypeManagerInterface $entity_type_manager, VolunteerismRepository $volunteerism_repository, LanguageManagerInterface $language_manager) {
    $this->acl = $acl;
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->volunteerismRepository = $volunteerism_repository;
    $this->userStorage = $entity_type_manager->getStorage('user');
    $this->volunteerismStorage = $entity_type_manager->getStorage('volunteerism');
    $this->languageManager = $language_manager;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this term.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?TermInterface $community = NULL) {
    // Get the current language.
    $currentLang = $this->languageManager->getCurrentLanguage();

    // Disable caching.
    $form['#cache']['max-age'] = 0;

    $form['#attributes'] = [
      'novalidate' => 'novalidate',
      'theme' => 'primary',
    ];

    // Save the community for submission.
    $form_state->set('community', $community->id());

    $form['#floating_buttons'][] = [
      'icon' => 'sharing',
      'label' => $this->t('qs_sharing.floating.dashboard'),
      'active' => TRUE,
    ];

    // Apply custom styles to wrapper.
    $form['#theme_wrappers'] = [
      'form__modal',
    ];

    $form['volunteerism'] = [
      '#type' => 'fieldset',
      '#description' => $this->t('qs_sharing.volunteerism.form.title'),
      '#theme_wrappers' => [
        'fieldset__step',
        'container__center__wide',
      ],
    ];

    $form['volunteerism']['description'] = [
      '#markup' => '<p class="font-weight-bold text-center mb-4">
       ' . $this->t('qs_sharing.volunteerism.form.description') . '
      </p>',
    ];

    $themes = $this->termStorage->loadTree('sharing_themes', 0, NULL, TRUE);

    foreach ($themes as $theme) {
      $volunteerism = $this->volunteerismRepository->isUserVolunteerForTheme($community, $this->currentUser(), $theme);

      // Get the translated theme.
      if ($theme->hasTranslation($currentLang->getId())) {
        $theme = $theme->getTranslation($currentLang->getId());
      }

      $form['volunteerism']['volunteerism_' . $theme->tid->value] = [
        '#title' => $theme->getName(),
        // phpcs:disable
        // Create a translation string for each of the sharing themes in the foreach loop
        '#body' => $this->t(sprintf('qs_sharing.volunteerism.form.description.theme.%s', $theme->field_sharing_icon->value)),
        // phpcs:enable
        '#icon' => $theme->field_sharing_icon->value,
        '#type' => 'checkbox',
        '#required' => FALSE,
        '#default_value' => isset($volunteerism),
        '#attributes' => [
          'variant' => 'toggle',
        ],
        '#theme_wrappers' => [
          'input__checkbox__toggle__volunteerism',
        ],
      ];
    }

    $form['volunteerism']['actions'] = [
      '#type' => 'fieldset',
      '#theme_wrappers' => [
        'container__center__extra_wide',
      ],
      '#attributes' => [
        'sticky_footer' => TRUE,
        'class' => [
          'text-center',
        ],
      ],
    ];

    $form['volunteerism']['actions']['save_and_set_default_values'] = [
      '#type' => 'submit',
      '#name' => 'save_and_set_default_values',
      '#value' => $this->t('qs_sharing.volunteerisms.form.manage.save_and_set_default_values'),
      '#attributes' => [
        'icon' => 'check',
        'icon_left' => TRUE,
        'class' => [
          'btn-outline-invert',
          'col-md-6',
        ],
      ],
    ];

    $form['volunteerism']['actions']['save_and_new_offer'] = [
      '#type' => 'submit',
      '#name' => 'save_and_new_offer',
      '#value' => $this->t('qs_sharing.volunteerisms.form.manage.save_and_new_offer'),
      '#attributes' => [
        'icon' => 'plus',
        'icon_left' => TRUE,
        'class' => [
          'btn-info',
          'col-md-6',
        ],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('entity_type.manager'),
      $container->get('qs_sharing.repository.volunteerism'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'qs_sharing_volunteerism_manage_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $community = $this->termStorage->load($form_state->get('community'));
    $user = $this->userStorage->load($this->currentUser()->id());

    $themes = $this->termStorage->loadTree('sharing_themes', 0, NULL, TRUE);

    foreach ($themes as $theme) {
      $currentValue = $this->volunteerismRepository->isUserVolunteerForTheme($community, $this->currentUser(), $theme);
      $updatedValue = (bool) $form_state->getValue('volunteerism_' . $theme->tid->value);

      // Delete Volunteerism.
      if (isset($currentValue) && !$updatedValue) {
        $volunteerism = $this->volunteerismStorage->load($currentValue->id());
        $volunteerism->delete();

        continue;
      }

      // Create Volunteerism.
      if (!isset($currentValue) && $updatedValue) {
        $volunteerism = $this->volunteerismStorage->create([
          'user' => $user,
          'theme' => $theme,
          'community' => $community,
        ]);
        $volunteerism->save();
      }

      $this->messenger()->addMessage($this->t('qs_sharing.volunteerisms.form.manage.success @community', [
        '@community' => $community->getName(),
      ]));

      // Handle redirection.
      $trigger = $form_state->getTriggeringElement();

      switch ($trigger['#name']) {
        case 'save_and_new_offer':
          $form_state->setRedirect('qs_sharing.offers.form.add', ['community' => $community->id()], []);

          break;

        case 'save_and_set_default_values':
        default:
          $form_state->setRedirect('qs_sharing.sharing.dashboard', [
            'community' => $community->id(),
            'user' => $user->id(),
          ], []);

          break;
      }
    }
  }

}
