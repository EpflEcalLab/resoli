<?php

namespace Drupal\qs_photo\Commands;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystem;
use Drupal\taxonomy\Entity\Term;
use Drush\Commands\DrushCommands;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Define Drush command(s) to export all photos by community.
 */
class ExportPhotoCommand extends DrushCommands {

  /**
   * The database connection to use.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The node Storage.
   *
   * @var \Drupal\node\NodeStorageInterface
   */
  protected $nodeStorage;

  /**
   * Provides helpers to operate on files and stream wrappers.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fso;

  /**
   * The term Storage.
   *
   * @var \Drupal\taxonomy\TermStorageInterface
   */
  private $termStorage;

  /**
   * Construct a new ExportPhotoCommand object.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    Connection $connection,
    FileSystem $fso,
  ) {
    $this->nodeStorage = $entity_type_manager->getStorage('node');
    $this->termStorage = $entity_type_manager->getStorage('taxonomy_term');
    $this->connection = $connection;
    $this->fso = $fso;
  }

  /**
   * Export photos for a community.
   *
   * /{community name - ID}/
   *    └── {activity name - ID}/
   *      └── {startDate}-{event-name}/
   *        ├── JPG001.jpg
   *        ├── JPG002.jpg
   *        └── ...
   *
   * @param string $community_id
   *   The community ID to export.
   * @param string $dest
   *   (Optional) The absolute destination directory of exported photos.
   *   Default on current directory.
   *
   * @command qs:export:photos
   *
   * @aliases qs:ex:p
   *
   * @usage drush qs:export:photos 1 ./
   *   Export all photos of Community NID 1 on ./
   */
  public function export(string $community_id, ?string $dest = NULL): void {
    $community = $this->termStorage->load($community_id);

    if (!$community instanceof Term) {
      throw new \RuntimeException(\sprintf('Community ID %s not found.', $community_id));
    }

    // Verify the destination directory exists and is writable.
    if (empty($dest)) {
      $dest = getcwd();
    }
    $dest = rtrim($dest, '/');

    if (!is_dir($dest)) {
      throw new \RuntimeException(\sprintf('Destination directory %s does not exist.', $dest));
    }

    if (!is_writable($dest)) {
      throw new \RuntimeException(\sprintf('Destination directory %s is not writable.', $dest));
    }

    $this->io()->note(\sprintf('Start Exporting Photos for Community %s.', $community->getName()));

    // Create Community directory with slugified name.
    $communityDirName = $this->slugify($community->getName()) . '-' . $community_id;
    $communityDir = $dest . '/' . $communityDirName;

    if (!is_dir($communityDir)) {
      mkdir($communityDir, 0755, TRUE);
    }

    $query = $this->connection->select('node_field_data', 'photo');
    $query->fields('photo', ['nid'])
      ->condition('photo.type', 'photo');

    $query->leftJoin('node__field_event', 'field_event', 'field_event.entity_id = photo.nid');
    $query->leftJoin('node__field_activity', 'field_activity', 'field_activity.entity_id = field_event.field_event_target_id');
    $query->leftJoin('node__field_community', 'field_community', 'field_community.entity_id = field_activity.field_activity_target_id');
    $query->condition('field_community.field_community_target_id', $community_id);

    $rows = $query->execute()->fetchAll();

    $progressbar_objects = new ProgressBar($this->output, \count($rows));
    $progressbar_objects->setFormat(' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
    $progressbar_objects->start();

    foreach ($rows as $row) {
      $photo = $this->nodeStorage->load($row->nid);
      $event = $photo->field_event->entity;
      $activity = $event->field_activity->entity;

      // Create Activity directory with slugified name.
      $activityDirName = $this->slugify($activity->getTitle()) . '-' . $activity->id();
      $activityDir = $communityDir . '/' . $activityDirName;

      if (!is_dir($activityDir)) {
        mkdir($activityDir, 0755, TRUE);
      }

      // Create Event directory with slugified name.
      $eventStartDate = !$event->get('field_start_at')->isEmpty()
        ? $event->get('field_start_at')->value
        : date('Y-m-d');
      $eventDirName = $eventStartDate . '-' . $this->slugify($event->getTitle());
      $eventDir = $activityDir . '/' . $eventDirName;

      if (!is_dir($eventDir)) {
        mkdir($eventDir, 0755, TRUE);
      }

      if ($photo->get('field_image')->isEmpty()) {
        continue;
      }

      // Get the image file from the photo entity.
      $file = $photo->get('field_image')->entity;
      $file_uri = $file->getFileUri();
      $path = $this->fso->realpath($file_uri);

      if (!is_file($path)) {
        continue;
      }

      // Copy Drupal Photo to the event directory.
      $destination = $eventDir . '/' . $file->getFilename();
      copy($path, $destination);

      $progressbar_objects->advance();
    }

    $progressbar_objects->finish();

    // Create space between the progress bar and final message.
    $this->io()->writeln('');
    $this->io()->writeln('');

    $this->io()->success(\sprintf('%d photos exported.', \count($rows)));
  }

  /**
   * Slugify a string for filesystem usage.
   *
   * @param string $text
   *   The text to slugify.
   * @param string $separator
   *   The separator to use.
   *
   * @return string
   *   The slugified string.
   */
  protected function slugify(string $text, string $separator = '-'): string {
    // Replace filesystem-unsafe characters.
    $text = str_replace('/', $separator, $text);
    $text = str_replace('\\', $separator, $text);
    $text = str_replace('"', '', $text);
    $text = str_replace('\'', ' ', $text);
    $text = str_replace('*', '', $text);
    $text = str_replace('?', '', $text);
    $text = str_replace(':', '', $text);
    $text = str_replace('<', '', $text);
    $text = str_replace('>', '', $text);
    $text = str_replace('|', '', $text);
    $text = str_replace('&', '', $text);

    // Replace non-letter or non-digit with separator.
    $text = preg_replace('~[^\pL\d]+~u', $separator, $text);
    // Transliterate.
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters.
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim separator from ends.
    $text = trim($text, $separator);
    // Remove duplicate separators.
    $text = preg_replace('~-+~', $separator, $text);
    // Lowercase.
    $text = strtolower($text);

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }

}
