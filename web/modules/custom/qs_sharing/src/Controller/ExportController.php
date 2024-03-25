<?php

namespace Drupal\qs_sharing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_export\Pdf;
use Drupal\qs_sharing\Repository\OfferRepository;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Export offers to a PDF files.
 */
class ExportController extends ControllerBase {

  /**
   * The QS PDF exporter.
   *
   * @var \Drupal\qs_export\Pdf
   */
  protected $pdfExporter;

  /**
   * Access Control Service.
   *
   * @var \Drupal\qs_acl\Service\AccessControl
   */
  private $acl;

  /**
   * The offer repository.
   *
   * @var \Drupal\qs_sharing\Repository\OfferRepository
   */
  private $offerRepository;

  /**
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, Pdf $pdf_exporter, OfferRepository $offer_repository) {
    $this->acl = $acl;
    $this->pdfExporter = $pdf_exporter;
    $this->offerRepository = $offer_repository;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\taxonomy\TermInterface $community
   *   Run access checks for this taxonomy.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function accessCommunity(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Checks access.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   * @param \Drupal\node\NodeInterface $offer_type
   *   Run access checks for this taxonomy.
   * @param \Drupal\taxonomy\TermInterface $theme
   *   The sharing theme.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function accessOfferType(AccountInterface $account, NodeInterface $offer_type, TermInterface $theme) {
    $access = AccessResult::forbidden();
    $community = $offer_type->field_community->entity;

    if ($community instanceof TermInterface && $this->acl->hasAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
  }

  /**
   * Export the complete list of offers by community.
   *
   * @param \Drupal\taxonomy\TermInterface $community
   *   The community object.
   */
  public function community(TermInterface $community) {
    $now = new DrupalDateTime();
    $offers = $this->offerRepository->getAllByCommunity($community);

    $this->pdfExporter->init();

    $title = $this->t('qs.sharing.export.offers.pdf.filename @community @date', [
      '@community' => $community->getName(),
      '@date' => $now->format('d-m-Y'),
    ]);
    $this->pdfExporter->setTitle($title->render());

    $this->pdfExporter->setContent('qs_sharing_export_offers_pdf', [
      'community' => $community->getName(),
      'offers' => $offers,
    ]);
    $this->pdfExporter->addPagination();

    return $this->pdfExporter->download('offres');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('qs_export.pdf'),
      $container->get('qs_sharing.repository.offer')
    );
  }

  /**
   * Export the complete list of offers by offer type.
   *
   * @param \Drupal\node\NodeInterface $offer_type
   *   The offer type.
   * @param \Drupal\taxonomy\TermInterface $theme
   *   The sharing theme.
   */
  public function offerType(NodeInterface $offer_type, TermInterface $theme) {
    $now = new DrupalDateTime();
    $community = $offer_type->field_community->entity;

    $offers = $this->offerRepository->getAllByOffersByTypeByTheme($offer_type, $theme);

    $this->pdfExporter->init();

    $title = $this->t('qs.sharing.export.offers.pdf.filename @community @offer_type @theme @date', [
      '@community' => $community->getName(),
      '@offer_type' => $offer_type->getTitle(),
      '@theme' => $theme->getName(),
      '@date' => $now->format('d-m-Y'),
    ]);
    $this->pdfExporter->setTitle($title->render());

    $this->pdfExporter->setContent('qs_sharing_export_offers_pdf', [
      'community' => $community->getName(),
      'offers' => $offers,
    ]);
    $this->pdfExporter->addPagination();

    return $this->pdfExporter->download();
  }

}
