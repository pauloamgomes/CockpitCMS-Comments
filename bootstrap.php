<?php

if (COCKPIT_ADMIN && !COCKPIT_API_REQUEST) {
  include_once __DIR__ . '/admin.php';

  $this->module('comments')->extend([
    'get' => function($oid) {
      $options['sort'] = ['_created' => 1];
      $options['filter'] = ['_oid' => $oid, '_pid' => FALSE];
      $results = $this->app->storage->find('cockpit/comments', $options)->toArray();

      foreach ($results as $idx => $result) {
        $options['filter']['_pid'] = $result['_id'];
        $replies = $this->app->storage->find('cockpit/comments', $options)->toArray();
        $results[$idx]['replies'] = $replies;
      }

      return $results;
    },

    'delete' => function($id, $oid) {
      $this->app->storage->remove('cockpit/comments', ['_id' => $id]);

      return $this->app->module('comments')->get($oid);
    },

    'create' => function($data) {
      extract($data);

      if (!isset($post) || !isset($oid) || !isset($collection)) {
        return;
      }

      $user = $this->app->module('cockpit')->getUser();

      $comment = NULL;

      $data = [
        'post' => $post,
        '_collection' => $collection ?? NULL,
        '_creator' => $user['_id'] ?? NULL,
        '_oid' => $oid,
        '_created' => time(),
        '_pid' => $pid ?? FALSE,
      ];

      $this->app->storage->insert('cockpit/comments', $data);

      return $this->app->module('comments')->get($oid);
    },
  ]);
}
