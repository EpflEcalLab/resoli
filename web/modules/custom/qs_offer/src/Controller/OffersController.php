<?php

namespace Drupal\qs_offer\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\qs_acl\Service\AccessControl;
use Drupal\qs_export\Pdf;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Collection of offers for one community.
 */
class OffersController extends ControllerBase {
  /**
   * The QS PDF exporter.
   *
   * @var Pdf
   */
  protected $pdfExporter;

  /**
   * Access Control Service.
   *
   * @var AccessControl
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
  public function access(AccountInterface $account, TermInterface $community) {
    $access = AccessResult::forbidden();

    if ($this->acl->hasAdminAccessCommunity($community)) {
      $access = AccessResult::allowed();
    }

    return $access;
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
   * Export the complete list of offers by community.
   *
   * @param TermInterface $community
   *
   * @return void
   */
  public function exportToPdf(TermInterface $community) {
    /*
     * TODO: Once the "Entraide" is setup,
     * retrieve the data for the offers and pass them to the variables array.
     * The template will need to be updated as well to fit those data
     * */
    $this->pdfExporter->download(
      'qs_community_offers_pdf',
      ['community' => $community->getName()]
    );
  }
}
