<?php

namespace Drupal\graphql_test\Wrappers;

use Drupal\Core\Entity\Query\QueryInterface;
use GraphQL\Deferred;

class QueryConnection2 {

  /**
   * @var \Drupal\Core\Entity\Query\QueryInterface
   */
  protected $query;

  /**
   * graphql_testonnection constructor.
   *
   * @param \Drupal\Core\Entity\Query\QueryInterface $query
   */
  public function __construct(QueryInterface $query) {
    $this->query = $query;
    dpm('QueryConnection2');
  }

  /**
   * @return int
   */
  public function total() {
    $query = clone $this->query;
    $query->range(NULL, NULL)->count();
    return $query->execute();
  }

  /**
   * @return array|\GraphQL\Deferred
   */
  public function items() {
    dpm('items');

    $result = $this->query->execute();
    dpm($result);
    if (empty($result)) {
      return ['leer'];
    }

    $buffer = \Drupal::service('graphql.buffer.entity');
    $callback = $buffer->add($this->query->getEntityTypeId(), array_values($result));
    return new Deferred(function () use ($callback) {
      return $callback();
    });
  }

}
