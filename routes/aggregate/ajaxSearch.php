<?php

const LIMIT = 10;
const SCORE_ENTITY = 10;
const SCORE_ENTITY_BEGINNING_OF_WORD = 20;
const SCORE_TAG = 30;

Log::info(var_export($_REQUEST, true));

$q = Request::get('q');

$results = [];

searchEntities($q, $results);
searchTags($q, $results);

$output['results'] = $results;

header('Content-Type: application/json');
print json_encode($output);

/*************************************************************************/

// Load entities by substring match at word boundary. MariaDB has a problem
// with regexp and collations, so this one-liner won't work:
//
//   name regexp "[[:<:]]%s" collate utf8mb4_general_ci
function searchEntities($q, &$results) {
  $escapedQ = addslashes($q);
  $entities = Model::factory('Entity')
    ->where_any_is([
      [ 'name' => "{$escapedQ}%" ],
      [ 'name' => "% {$escapedQ}%" ],
      [ 'name' => "%-{$escapedQ}%" ],
    ], 'like')
    ->order_by_asc('name')
    ->limit(LIMIT)
    ->find_many();

  if (count($entities)) {
    $data = [];
    foreach ($entities as $e) {
      $data[] = [
        'id' => $e->id,
        'text' => $e->name,
      ];
    }
    $results[] = [
      'text' => _('entities'),
      'children' => $data,
    ];
  }
}

// load tags by prefix match
function searchTags($q, &$results) {
  $escapedQ = addslashes($q);
  $tags = Model::factory('Tag')
    ->where_like('value', "{$escapedQ}%")
    ->limit(LIMIT)
    ->find_many();

  if (count($tags)) {
    $data = [];
    foreach ($tags as $t) {
      $data[] = [
        'id' => $t->id,
        'text' => $t->value,
      ];
    }
    $results[] = [
      'text' => _('tags'),
      'children' => $data,
    ];
  }
}
