<?php

namespace App\Services\Querybuilder;

use App\Entity\User;
use InvalidArgumentException;

/**
 * Service QueryBuilderService
 */
class QueryBuilderService {
  /**
   * Create the query.
   *
   * @param mixed $data $query all the info from the form and the state of the query.
   * @return array $query the full query.
   */
  public function makeQuery($data, $query, $user) {

    $this->parseFirstBlock($data["initial"], $query, $user);
    $this->parseJoinsBlocks($data["joins"] ?? [], $query, $user);

    return $query;
  }

  /**
   * Get the selected fields of the query to create a template table for the results.
   *
   * @param mixed $data the array containing the info for the query.
   * @return array $resultsTab the array with the initial tables and the adjacent tables as keys and the corresponding fields as values.
   */
  public function getSelectFields($data) {
    $initial = $data["initial"];
    // Init the array with the first fields
    $resultsTab = [$initial['alias'] => $initial['fields']];

    // If there are join blocks in the query
    if (array_key_exists("joins", $data)) {
      $joins = $data["joins"];
      foreach ($joins as $j) {
        if (array_key_exists('fields', $j)) {
          $alias = $j["alias"];
          $joinsFields = $j["fields"];
          // Adding the selected fields for each join block
          $resultsTab[$alias] = $joinsFields;
        }
      }
    }

    return $resultsTab;
  }

  private function selectClause($alias, $field) {
    return $alias . "." . $field['id'] . " AS " . $alias . "_" . $field['id'];
  }

  private function scrambledSelectClause($alias, $field) {
    return "ROUND(CAST(" . $alias . "." . $field['id'] . " AS numeric), 2) AS " . $alias . "_" . $field['id'];
  }

  /**
   * Parse the first block of querybuilder.
   *
   * @param mixed $initial, $qb : the info from the first block, the current state of the query.
   * @return mixed $query : the query with the added chosen table, the fields and the constraints if the user choses to apply some constraints
   *
   * Warning : by default, all fields are checked for the first table, please keep at least one field selected.
   */
  private function parseFirstBlock($initial, $qb, User | null $user) {
    $table = $initial["table"];
    $alias = $initial["alias"];
    // Adding the initial table to the query
    $query = $qb->from('App:' . $table, $alias);
    foreach ($initial["fields"] as $field) {
      // Adding every field selected for the initial table with their alias
      if (
        !($user instanceof User) &&
        $table === "Station" && (
          $field['id'] === "latDegDec" ||
          $field['id'] === "longDegDec"
        )) {
        // Scramble coordinates
        $query = $query->addSelect($this->scrambledSelectClause($alias, $field));
      } else {
        $query = $query->addSelect($this->selectClause($alias, $field));
      }
    };
    // If there are some constraints addedby the user
    if ($initial['rules']) {
      $constraint = $this->parseGroup($initial['rules'], $qb, $alias);
      if ($constraint) {
        $query->andWhere($constraint);
      }
    }

    return $query;
  }

  /**
   * Get the info contained in each block of JOIN.
   *
   * @param mixed $joins, $query : the info contained on each block in the form, the current state of the query.
   * @return mixed $query : the query with the added chosen table, the fields and the constraints if the user choses to apply some constraints.
   *
   * Warning : by default, no fields are checked for the chosen adjacent table, the user is free to keep it that way or choose some fields to return.
   */
  private function parseJoinsBlocks($joins, $query, User | null $user) {
    foreach ($joins as $j) {
      // Adding the fields to the query if the user chooses to return some.
      foreach ($j["fields"] ?? [] as $field) {

        if (
          !($user instanceof User) &&
          $j['table'] === "Station" && (
            $field['id'] === "latDegDec" ||
            $field['id'] === "longDegDec"
          )) {
          // Scramble coordinates
          $query = $query->addSelect($this->scrambledSelectClause($j['alias'], $field));
        } else {
          $query = $query->addSelect($this->selectClause($j['alias'], $field));
        }

        // $query = $query->addSelect($j["alias"] . "." . $field['id'] . " AS " . $j["alias"] . "_" . $field['id']);
      };
      // Join tables
      $query = $this->makeJoin($j, $query);
      // Parse constraints if the user chooses to apply some.
      if ($j['rules'] ?? []) {
        $constraint = $this->parseGroup($j['rules'], $query, $j['alias']);
        if ($constraint) {
          $query->andWhere($constraint);
        }
      }
    }

    return $query;
  }

  /**
   * Parse each rule contained in a group.
   *
   * @param mixed $rule, $qb, $tableAlias : the rule, the qb object and the alias of the table.
   * @return mixed $qb : the qb updated with  the constraint.
   *
   */
  private function parseRule($rule, $qb, $tableAlias) {
    $rule['operator'] = str_replace(" ", "_", $rule['operator']);
    $value = $rule['value'] ?? null;
    if ($value) {
      if (in_array($rule["operator"], ["between", "not_between", "in", "not_in"])) {
        $value = array_map(function ($v) use ($rule) {
          $v = trim($v);
          if (in_array($rule['operator'], ['between', 'not_between'])) {
            return "'" . $v . "'";
          } else {
            return $v;
          }
        }, $value);
      } else {
        $value = trim($value);
        if (in_array($rule["operator"], ['begins_with', 'not_begins_with', 'contains', 'does_not_contain'])) {
          $value = $value . "%";
        }
        if (in_array($rule["operator"], ['ends_with', 'not_ends_with', 'contains', 'does_not_contain'])) {
          $value = '%' . $value;
        }
        $value = "'" . $value . "'";
      }
    }
    $column = $tableAlias . "." . $rule["rule"];

    // Find the right operator
    switch ($rule["operator"]) {
    case 'equals':
    case '=':
    case 'on_day':
      return $qb->expr()->eq($column, $value);
      break;
    case 'does_not_equal':
    case '<>':
    case '!=':
    case '≠':
    case 'not_on_day':
      return $qb->expr()->neq($column, $value);
      break;
    case 'in':
      return $qb->expr()->in($column, $value);
      break;
    case 'not_in':
      return $qb->expr()->notIn($column, $value);
      break;
    case 'less':
    case '<':
      return $qb->expr()->lt($column, $value);
      break;
    case 'less_or_equal':
    case '<=':
    case '≤':
      return $qb->expr()->lte($column, $value);
      break;
    case 'greater':
    case '>':
      return $qb->expr()->gt($column, $value);
      break;
    case 'greater_or_equal':
    case '>=':
    case '≥':
      return $qb->expr()->gte($column, $value);
      break;
    case 'between':
      return $qb->expr()->between($column, ...$value);
      break;
    case 'not_between':
      return $qb->expr()->not($qb->expr()->between($column, ...$value));
      break;
    case 'is_null':
      return $qb->expr()->isNull($column);
      break;
    case 'is_not_null':
      return $qb->expr()->not($qb->expr()->isNull($column));
      break;
    case 'begins_with':
    case 'ends_with':
    case 'contains':
      return $qb->expr()->like('lower(' . $column . ')', strtolower($value));
      break;
    case 'not_begins_with':
    case 'does_not_contain':
    case 'not_ends_with':
      return $qb->expr()->not($qb->expr()->like('lower(' . $column . ')', strtolower($value)));
      break;
    case 'is_empty':
      return $qb->expr()->eq($column, "''");
      break;
    case 'is_not_empty':
      return $qb->expr()->not($qb->expr()->eq($column, "''"));
      break;

    default:
      throw new InvalidArgumentException("Querybuilder : Unknown operator " . $rule['operator']);
      break;
    }
  }

  /**
   * Parse each group contained in the full constraints object.
   *
   * @param mixed $group, $qb, $tableAlias : the group, the qb object and the alias of the table.
   * @return mixed $qb : the qb updated with  the constraint.
   *
   */
  private function parseGroup($group, $qb, $tableAlias) {
    // Make sure parseRule has the correct arguments
    $parseChild = function ($rule) use (&$qb, &$tableAlias) {
      if ($rule['type'] == "query-builder-rule") {
        return $this->parseRule($rule["query"], $qb, $tableAlias);
      } else if ($rule['type'] == "query-builder-group") {
        return $this->parseGroup($rule['query'], $qb, $tableAlias);
      } else {
        throw new InvalidArgumentException("Querybuilder : unknown rule type " . $rule['type']);
      }

    };
    // Create an array with all the rules
    if ($group['children']) {
      $constraints = array_filter(array_map($parseChild, $group["children"]));
      $operator = $group["logicalOperator"];
      // Create the constraint with the appropriate operator
      if (empty($constraints)) {
        return null;
      } else if ($operator === "and") {
        return $qb->expr()->andX(...$constraints);
      } else if ($operator === "or") {
        return $qb->expr()->orX(...$constraints);
      } else {
        throw new InvalidArgumentException("Querybuilder : Invalid operator " . $operator);
      }
    } else {
      return null;
    }
  }

  /**
   * Get the type of JOIN and makes the appropriate query.
   *
   * @param mixed $joins, $query, $formerTable, $jointype, $adjTable, $adjTableAlias, $srcField, $tgtField : the array containing the JOIN info,
   *              the current state of the query, the chosen former table, the JOIN type,
   *              the source and the target fields.
   * @return mixed $query : the query with the JOIN added
   *
   */
  private function makeJoin($joinBlock, $query) {
    $table = $joinBlock['table'];
    $alias = $joinBlock['alias'];
    $source = $joinBlock['join']['from']['alias'] . '.' . $joinBlock['join']['from']['column'];
    $target = $alias . '.' . $joinBlock['join']["column"];

    $joinFields = $source . " = " . $target;

    $joinArgs = ['App:' . $table, $alias, 'WITH', $joinFields];

    $joinType = $joinBlock["join"]['type'];
    if ($joinType == "Inner Join") {
      $query = $query->innerJoin(...$joinArgs);
    } elseif ($joinType == "Left Join") {
      $query = $query->leftJoin(...$joinArgs);
    } else {
      throw new InvalidArgumentException("Querybuilder : Invalid join type " . $joinType);
    }

    return $query;
  }
}
