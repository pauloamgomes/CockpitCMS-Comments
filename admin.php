<?php

$this("acl")->addResource('comments', [
  'view',
  'add',
  'delete',
]);

 // Implements collections.entry.aside event.
 $this->on('collections.entry.aside', function ($name) use ($app) {
  if (!$app->module('cockpit')->hasaccess('comments', 'view')) {
    return;
  }

  $settings = $this->retrieve('config/comments', ['collections' => []]);
  if ($settings['collections'] === '*' || in_array($name, $settings['collections'])) {
    $this->renderView("comments:views/partials/comments-collection-aside.php");
  }
});

// Dashboard widgets.
$this->on("admin.dashboard.widgets", function ($widgets) use ($app) {

  $comments = [];
  $user = $app->module('cockpit')->getUser();
  $options['sort'] = ['_created' => -1];
  $options['filter'] = ['_created' => ['$gt' => strtotime('-1 week')]];
  $options['limit'] = 50;

  $results = $app->storage->find('cockpit/comments', $options)->toArray();

  $comments = [];
  $entries = [];

  foreach ($results as $idx => $result) {
    if (!isset($entries[$result['_oid']])) {
      $entry = $app->module('collections')->findOne($result['_collection'], ['_id' => $result['_oid']]);
      $entries[$result['_oid']] = $entry;
    } else {
      $entry = $entries[$result['_oid']];
    }

    if (!empty($entry) && ($result['_creator'] === $user['_id'] || $entry['_by'] === $user['_id'] || $entry['_mby'] === $user['_id'])) {
      $comments[] = $result;
    }
  }

  $widgets[] = [
    "name"    => "comments",
    "content" => $this->view("comments:views/widgets/dashboard.php", compact('comments')),
    "area"    => 'main',
  ];

}, 100);
