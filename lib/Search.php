<?php

class Search {

  // limit per object type - entities and tags
  const LIMIT = 10;

  static function run($query, $limit = self::LIMIT) {
    $escapedQuery = addslashes($query);
    $results = [
      'entities' => self::searchEntities($escapedQuery, $limit),
      'tags' => self::searchTags($escapedQuery, $limit),
    ];
    return $results;
  }

  // Load entities by substring match at word boundary. MariaDB has a problem
  // with regexp and collations, so this one-liner won't work:
  //
  //   name regexp "[[:<:]]%s" collate utf8mb4_general_ci
  private static function searchEntities($escapedQuery, $limit) {
    return Model::factory('Entity')
      ->table_alias('e')
      ->select('e.*')
      ->distinct()
      ->left_outer_join('alias', ['e.id', '=', 'a.entityId'], 'a')
      ->where_any_is([
        [ 'e.name' => "{$escapedQuery}%" ],
        [ 'e.name' => "% {$escapedQuery}%" ],
        [ 'e.name' => "%-{$escapedQuery}%" ],
        [ 'a.name' => "{$escapedQuery}%" ],
      ], 'like')
      ->order_by_asc('e.name')
      ->limit($limit)
      ->find_many();
  }

  // load tags by prefix match
  private static function searchTags($escapedQuery, $limit) {
    return Model::factory('Tag')
      ->where_like('value', "{$escapedQuery}%")
      ->limit($limit)
      ->find_many();
  }

}