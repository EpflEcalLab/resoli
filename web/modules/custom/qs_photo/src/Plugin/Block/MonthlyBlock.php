<?php

namespace Drupal\qs_photo\Plugin\Block;

use Drupal\qs_calendar\Plugin\Block\PeriodBlockBase;
use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Calendar Monthly.
 *
 * @Block(
 *   id = "qs_photo_monthly_block",
 *   admin_label = @Translation("Calendar Monthly"),
 * )
 */
class MonthlyBlock extends PeriodBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build($params = []) {
    $variables = [];

    // The request should be took at the last moment, avoid it on constructor.
    $master_request = $this->requestStack->getMasterRequest();

    // Get pagination month.
    $pagination_month = $master_request->query->get('month');

    $month = new DrupalDateTime();
    if ($pagination_month) {
      try {
        $month = DrupalDateTime::createFromFormat('Y-m-d', $pagination_month);
      }
      catch (\Exception $e) {
        $month = new DrupalDateTime();
      }
    }

    $next_month = clone $month;
    $next_month->modify('first day of next month');
    $next_month->setTime(0, 0);

    $prev_month = clone $month;
    $prev_month->modify('first day of previous month');
    $prev_month->setTime(0, 0);

    $variables['prev_month'] = $prev_month;
    $variables['next_month'] = $next_month;

    $variables['current_day'] = $month;

    $date_start         = $this->calendarBuilder->getFirstMondayMonthFullWeek($month);
    $date_end           = $this->calendarBuilder->getLastSundayMonthFullWeek($month);
    $variables['dates'] = $this->calendarBuilder->build($date_start, $date_end);

    return [
      '#theme'     => 'qs_photo_monthly_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
      ],
    ];
  }

}
