<?php

namespace Drupal\qs_sharing\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_export\Pdf;
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
   * {@inheritdoc}
   */
  public function __construct(AccessControl $acl, Pdf $pdf_exporter) {
    $this->acl = $acl;
    $this->pdfExporter = $pdf_exporter;
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
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function accessOfferType(AccountInterface $account, NodeInterface $offer_type) {
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
    return $this->pdfExporter->download(
      'qs_sharing_export_offers_pdf',
      ['community' => $community->getName()],
      'offres'
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load customs services used in this class.
      $container->get('qs_acl.access_control'),
      $container->get('qs_export.pdf')
    );
  }

  /**
   * Export the complete list of offers by offer type.
   *
   * @param \Drupal\node\NodeInterface $offer_type
   *   The offer type.
   */
  public function offerType(NodeInterface $offer_type) {
    $community = $offer_type->field_community->entity;

    return $this->pdfExporter->download(
      'qs_sharing_export_offers_pdf',
      ['community' => $community->getName()],
      'offres'
    );
  }

}
