<?php

namespace Drupal\qs_calendar\Plugin\Block;

use Drupal\Core\Datetime\DrupalDateTime;

/**
 * Calendar Weekly.
 *
 * @Block(
 *   id = "qs_calendar_weekly_block",
 *   admin_label = @Translation("Calendar Weekly"),
 * )
 */
class WeeklyBlock extends PeriodBlockBase {

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

    $next_week = clone $day;
    $next_week->modify('Monday next week');
    $next_week->setTime(0, 0);

    $prev_week = clone $day;
    $prev_week->modify('Monday last week');
    $prev_week->setTime(0, 0);

    $variables['prev_week'] = $prev_week;
    $variables['next_week'] = $next_week;

    $variables['current_day'] = $day;

    $date_start         = $this->calendarBuilder->getMondayWeek($day);
    $date_end           = $this->calendarBuilder->getSundayWeek($day);
    $variables['dates'] = $this->calendarBuilder->build($date_start, $date_end);

    // Count for every days between two dates how many events occure by day.
    $variables['events'] = $this->eventManager->countByDate($community, $date_start, $date_end);

    return [
      '#theme'     => 'qs_calendar_weekly_block',
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
