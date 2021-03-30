<?php

namespace Drupal\qs_calendar\Plugin\Block;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Calendar Monthly.
 *
 * @Block(
 *     id="qs_calendar_monthly_block",
 *     admin_label=@Translation("Calendar Monthly"),
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

    // Get the community route parameter.
    $community = $master_request->attributes->get('community');

    // Get pagination day.
    $pagination_day = $master_request->query->get('day');

    $day = new DrupalDateTime();

    if ($pagination_day) {
      try {
        $day = DrupalDateTime::createFromFormat('Y-m-d', $pagination_day);
      }
      catch (\Exception $e) {
        $day = new DrupalDateTime();
      }
    }

    $next_month = clone $day;
    $next_month->modify('first day of next month');
    $next_month->setTime(0, 0);

    $prev_month = clone $day;
    $prev_month->modify('first day of previous month');
    $prev_month->setTime(0, 0);

    $variables['prev_month'] = $prev_month;
    $variables['next_month'] = $next_month;

    $variables['current_day'] = $day;

    $date_start = $this->calendarBuilder->getFirstMondayMonthFullWeek($day);
    $date_end = $this->calendarBuilder->getLastSundayMonthFullWeek($day);
    $date_end->setTime(23, 59, 59);
    $variables['dates'] = $this->calendarBuilder->build($date_start, $date_end);

    // Count for every days between two dates how many events occure by day.
    $variables['events'] = $this->badgeManager->countEventsByDates($community, $date_start, $date_end);

    // Get Badges for Dotes.
    $variables['badges'] = $this->getDotesBadges($community, $date_start, $date_end);

    return [
      '#theme' => 'qs_calendar_monthly_block',
      '#variables' => $variables,
      '#cache' => [
        'contexts' => [
          'user',
          'url.query_args',
        ],
        'tags' => [
          // Invalidated whenever any Event is updated, deleted or created.
          'node_list:event',
          // Invalidated whenever any Privilege is updated, deleted or created.
          'privilege_list:privilege',
        ],
      ],
    ];
  }

}
