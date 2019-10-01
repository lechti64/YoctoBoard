<?php

namespace Yocto;

class Database implements \IteratorAggregate, \Countable
{

    const PATH = ROOT . '/data';

    /** @var array Conditions */
    private $conditions = [
        'limit' => [],
        'orderBy' => [],
        'where' => [],
    ];

    /** @var array Configuration */
    private $configuration = [];

    /** @var \stdClass Ligne lors d'un find, insert ou update */
    private $row;

    /** @var array Lignes */
    private $rows = [];

    /** @var string Table */
    private $table = '';

    /**
     * Retourne une valeur lors d'un find, insert ou update
     * @param string $column Colonne
     * @return mixed
     */
    public function __get($column)
    {
        return $this->row->{$column};
    }

    /**
     * Insert une valeur lors d'un find, insert ou update
     * @param string $column Colonne
     * @param mixed $value Valeur
     * @throws \Exception
     */
    public function __set($column, $value)
    {
        if (array_key_exists($column, $this->configuration['columns'])) {
            $this->row->{$column} = $this->filter($value, $this->configuration['columns'][$column]);
        } else {
            throw new \Exception('Column "' . $column . '" not found in "' . $this->table . '" table');
        }
    }

    /**
     * Alias de where
     * @param string $column Colonne
     * @param string $comparisonOperators Opérateur de comparaison (=, !=, >, >=, <, <=, IN, NOT IN, LIKE)
     * @param string $value Valeur
     * @return $this
     */
    public function andWhere($column, $comparisonOperators, $value)
    {
        $this->where($column, $comparisonOperators, $value);
        return $this;
    }

    /**
     * Compte le nombre de lignes
     * @return int
     */
    public function count()
    {
        return count($this->rows);
    }

    /**
     * Crée une table
     * @param string $table Table
     * @param array $columns Colonne et type de données
     * @throws \Exception
     */
    public static function create($table, array $columns = [])
    {
        // Crée la table
        if (self::exists($table) === false && mkdir(self::PATH . '/' . $table) === false) {
            throw new \Exception('Failed to save "' . $table . '" table');
        }
        // Crée le fichier de configuration
        $configuration = [
            'columns' => $columns,
            'increment' => 1,
        ];
        if (file_put_contents(self::PATH . '/' . $table . '/config.json', json_encode($configuration, JSON_PRETTY_PRINT)) === false) {
            throw new \Exception('Failed to save "config.json" for "' . $table . '" table');
        }
    }

    /**
     * Supprime la table, une ligne ou des lignes
     * @return bool
     * @throws \Exception
     */
    public function delete()
    {
        // Supprime une ligne
        if ($this->row->id) {
            if (
                is_file(self::PATH . '/' . $this->table . '/' . $this->row->id . '.json')
                && unlink(self::PATH . '/' . $this->table . '/' . $this->row->id . '.json') === false
            ) {
                throw new \Exception('Failed to delete "' . $this->row->id . '" row');
            }
        } // Supprime des lignes
        else if ($this->rows) {
            foreach ($this->rows as $row) {
                if (
                    is_file(self::PATH . '/' . $this->table . '/' . $row->id . '.json')
                    && unlink(self::PATH . '/' . $this->table . '/' . $row->id . '.json') === false
                ) {
                    throw new \Exception('Failed to delete "' . $row->id . '" row');
                }
            }
        } // Supprime la table
        else {
            if (in_array(false, array_map('unlink', glob(self::PATH . '/' . $this->table . '/*.json')))) {
                throw new \Exception('Row have not been deleted in "' . $this->table . '" table');
            }
            if (rmdir(self::PATH . '/' . $this->table) === false) {
                throw new \Exception('Failed to delete "' . $this->table . '" table');
            }
        }
        return true;
    }

    /**
     * Test l'existence d'une table
     * @param string $table Table
     * @return bool
     */
    public static function exists($table)
    {
        return is_dir(self::PATH . '/' . $table);
    }

    /**
     * Retourne une ligne
     * @return $this
     * @throws \Exception
     */
    public function find()
    {
        $this->applyConditions();
        if ($this->count()) {
            $this->row = $this->rows[0];
        }
        return $this;
    }

    /**
     * Retourne toutes les lignes
     * @return $this
     * @throws \Exception
     */
    public function findAll()
    {
        $this->applyConditions();
        return $this;
    }

    /**
     * Crée l'itérateur
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->rows);
    }

    /**
     * Crée l'instance d'une table
     * @param string $table Table
     * @return Database
     * @throws \Exception
     */
    public static function instance($table)
    {
        if (self::exists($table) === false) {
            throw new \Exception('"' . $table . '" table not found');
        }
        $self = new self();
        // Table instanciée
        $self->table = $table;
        // Configuration de la table
        $self->configuration = json_decode(file_get_contents(self::PATH . '/' . $table . '/config.json'), true);
        // Ligne vide
        $self->row = new \stdClass();
        $self->row->id = 0;
        foreach ($self->configuration['columns'] as $column => $type) {
            $self->row->{$column} = $self->filter('', $type);
        }
        // Clefs étrangères vides
        $self->row = (object)array_merge((array)$self->row, (array)self::instanceForeign(
            $self->row,
            $self->configuration['foreignKeys'],
            [$table . $self->row->id]
        ));;
        // Lignes de la table
        foreach (new \DirectoryIterator(self::PATH . '/' . $table) as $file) {
            if ($file->getExtension() === 'json' && $file->getFilename() !== 'config.json') {
                $row = json_decode(file_get_contents(self::PATH . '/' . $table . '/' . $file->getFilename()));
                $row->id = (int)$file->getBasename('.json');
                $self->rows[] = (object)array_merge((array)$row, (array)self::instanceForeign(
                    $row,
                    $self->configuration['foreignKeys'],
                    [$table . $row->id]
                ));;
            }
        }
        // Ajout des clefs étrangères
        return $self;
    }

    /**
     * Ajoute condition une limit
     * @param int $offset Index de début
     * @param int $length Nombre de lignes
     * @return $this
     * @throws \Exception
     */
    public function limit($offset, $length)
    {
        $this->conditions['limit'] = [
            'length' => $this->filter($length, 'integer'),
            'offset' => $this->filter($offset, 'integer'),
        ];
        return $this;
    }

    /**
     * Ajoute une condition orderBy
     * @param string $column Colonne
     * @param string $direction Ordre de tri (ASC ou DESC)
     * @return $this
     */
    public function orderBy($column, $direction = 'ASC')
    {
        $this->conditions['orderBy'][] = [
            'column' => $column,
            'direction' => $direction,
        ];
        return $this;
    }

    /**
     * Alias de where
     * @param string $column Colonne
     * @param string $comparisonOperators Opérateur de comparaison (=, !=, >, >=, <, <=, IN, NOT IN, LIKE)
     * @param string $value Valeur
     * @return $this
     */
    public function orWhere($column, $comparisonOperators, $value)
    {
        $this->where($column, $comparisonOperators, $value, 'OR');
        return $this;
    }

    /**
     * Prépare une ligne
     * @return $this
     * @throws \Exception
     */
    public function prepare()
    {
        // Bloque la préparation si la valeur d'une ligne est égale à null
        if (!in_array(null, (array)$this->row, true)) {
            // Date de création / mise à jour
            $date = new \DateTime('now', new \DateTimeZone('UTC'));
            $date = $date->format('Y-m-d\TH:i:sO');
            // Insertion
            if ($this->row->id === 0) {
                // Ajoute un id lors d'une insertion
                $this->row->id = $this->configuration['increment']++;
                // Ajoute la date de création
                $this->row->createdAt = $date;
            }
            // Ajout de la date de mise à jour
            $this->row->updatedAt = $date;
        }
        return $this;
    }

    /**
     * Enregistre une ou plusieurs lignes
     * @param Database[] ...$instances
     * @return bool
     * @throws \Exception
     */
    public static function save(...$instances)
    {
        // Bloque l'enregistrement si l'une des instances contient une ligne dont la valeur est égale à null
        foreach ($instances as $instance) {
            if (in_array(null, (array)$instance->row, true)) {
                return false;
            }
        }
        // Enregistre les lignes
        foreach ($instances as $instance) {
            // Incrémente le fichier de configuration
            if (file_put_contents(self::PATH . '/' . $instance->table . '/config.json', json_encode($instance->configuration, JSON_PRETTY_PRINT)) === false) {
                throw new \Exception('Failed to edit "config.json" for "' . $instance->table . '" table');
            }
            // Crée une copie sans les clefs étrangères, l'id et la présence d'erreur afin de ne pas les enregistrer
            $rowClone = clone $instance->row;
            foreach ($instance->configuration['foreignKeys'] as $foreignKey => $foreignInfo) {
                unset($rowClone->{$foreignKey});
            }
            unset($rowClone->id);
            // Enregistre la ligne
            if (file_put_contents(self::PATH . '/' . $instance->table . '/' . $instance->row->id . '.json', json_encode($rowClone, JSON_PRETTY_PRINT)) === false) {
                throw new \Exception('Failed to insert "' . $instance->row->id . '" row for "' . $instance->table . '" table');
            }
        }
        return true;
    }

    /**
     * Copie les lignes de l'itérateur dans un tableau
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this, true);
    }

    /**
     * Ajoute une condition where
     * @param string $column Colonne
     * @param string $comparisonOperators Opérateur de comparaison (=, !=, >, >=, <, <=, IN, NOT IN, LIKE)
     * @param string $value Valeur
     * @param string $logicalOperator Opérateur logique (AND ou OR)
     * @return $this
     */
    public function where($column, $comparisonOperators, $value, $logicalOperator = 'AND')
    {
        $this->conditions['where'][] = [
            'column' => $column,
            'comparisonOperators' => $comparisonOperators,
            'logicalOperator' => $logicalOperator,
            'value' => $value,
        ];
        return $this;
    }

    /**
     * Applique les conditions
     * @throws \Exception
     */
    private function applyConditions()
    {
        // Applique les conditions where
        if ($this->conditions['where']) {
            $this->applyWhere();
        }
        // Applique la condition orderBy
        if ($this->conditions['orderBy']) {
            $this->applyOrderBy();
        }
        // Applique la condition limit
        if ($this->conditions['limit']) {
            $this->applyLimit();
        }
        // Réindexation
        $this->rows = array_values($this->rows);
    }

    /**
     * Applique la condition limit
     */
    private function applyLimit()
    {
        $this->rows = array_slice(
            $this->rows,
            $this->conditions['limit']['offset'],
            $this->conditions['limit']['length']
        );
    }

    /**
     * Applique la condition orderBy
     */
    private function applyOrderBy()
    {
        uasort($this->rows, function ($a, $b) {
            $sort = 0;
            foreach ($this->conditions['orderBy'] as $condition) {
                if ($condition['direction'] === 'ASC') {
                    $sort = strnatcasecmp(
                        $a->{$condition['column']},
                        $b->{$condition['column']}
                    );
                } else if ($condition['direction'] === 'DESC') {
                    $sort = strnatcasecmp(
                        $b->{$condition['column']},
                        $a->{$condition['column']}
                    );
                } else {
                    throw new \Exception('Unknown "' . $this->conditions['orderBy'] . '" direction');
                }
                if ($sort) {
                    return $sort;
                }
            }
            return $sort;
        });

    }

    /**
     * Applique la condition where
     */
    private function applyWhere()
    {
        $this->rows = array_filter($this->rows, function ($row) {
            $isFound = [
                'AND' => null,
                'OR' => null,
            ];
            foreach ($this->conditions['where'] as $condition) {
                // Stop la recherche si une condition OR = true
                if ($isFound['OR']) {
                    break;
                }
                // Ignore les conditions AND lorsqu'une condition AND = false
                if ($condition['logicalOperator'] === 'AND' && $isFound['AND'] === false) {
                    continue;
                }
                // Check le contenu des colonnes
                switch ($condition['comparisonOperators']) {
                    case '=':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} === $condition['value']);
                        break;
                    case '!=':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} !== $condition['value']);
                        break;
                    case '>':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} > $condition['value']);
                        break;
                    case '>=':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} >= $condition['value']);
                        break;
                    case '<':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} < $condition['value']);
                        break;
                    case '<=':
                        $isFound[$condition['logicalOperator']] = ($row->{$condition['column']} <= $condition['value']);
                        break;
                    case 'IN':
                        $isFound[$condition['logicalOperator']] = in_array($row->{$condition['column']}, $condition['value']);
                        break;
                    case 'NOT IN':
                        $isFound[$condition['logicalOperator']] = (in_array($row->{$condition['column']}, $condition['value']) === false);
                        break;
                    case 'REVERSE IN':
                        $isFound[$condition['logicalOperator']] = in_array($condition['value'], $row->{$condition['column']});
                        break;
                    case 'REVERSE NOT IN':
                        $isFound[$condition['logicalOperator']] = (in_array($condition['value'], $row->{$condition['column']}) === false);
                        break;
                    case 'LIKE':
                        $isFound[$condition['logicalOperator']] = preg_match(
                            '/^' . str_replace('%', '(.*?)', preg_quote($condition['value'])) . '$/i',
                            $row->{$condition['column']}
                        );
                        break;
                    default:
                        throw new \Exception('Unknown "' . $condition['logicalOperator'] . '" logical operator');
                }
            }
            return ($isFound['OR'] || $isFound['AND']);
        });
    }

    /**
     * Filtre une valeur
     * @param mixed $value Valeur
     * @param string $type Type
     * @return bool|float|int|string
     * @throws \Exception
     */
    private static function filter($value, $type)
    {
        // Pas de filtre pour une valeur null, car null signifie qu'un champ obligatoire est vide
        if ($value === null) {
            return null;
        }
        // Filtre la valeur
        switch ($type) {
            case 'boolean':
                return boolval($value);
            case 'booleanArray':
                return array_map('boolval', (array)$value);
            case 'float':
                return floatval($value);
            case 'floatArray':
                return array_map('floatval', (array)$value);
            case 'integer':
                return intval($value);
            case 'integerArray':
                return array_map('intval', (array)$value);
            case 'string':
                return strval($value);
            case 'stringArray':
                return array_map('strval', (array)$value);
            default:
                throw new \Exception('Unknown "' . $value . '" filter type');
        }
    }

    /**
     * Crée l'instance des lignes rattachées aux clefs étrangères
     * @param \stdClass $row Lignes
     * @param array $foreignKeys Clefs étrangères
     * @param array $history Historique des recherches pour éviter les boucles infinies
     * @return \stdClass
     * @throws \Exception
     */
    private static function instanceForeign($row, $foreignKeys, $history)
    {
        foreach ($foreignKeys as $foreignKey => $foreignInfo) {
            $foreignRowId = $row->{$foreignInfo['column']};
            $search = $foreignInfo['table'] . $foreignRowId;
            if (!in_array($search, $history)) {
                // Configuration de la table rattachée à la clef étrangère
                $configuration = json_decode(file_get_contents(self::PATH . '/' . $foreignInfo['table'] . '/config.json'), true);
                // Ligne rattachée à la clef étrangère
                if ($foreignRowId) {
                    $foreignRow = json_decode(file_get_contents(self::PATH . '/' . $foreignInfo['table'] . '/' . $foreignRowId . '.json'));
                    $foreignRow->id = $foreignRowId;
                } // Ligne vide si aucune clef rattachée
                else {
                    $foreignRow = new \stdClass();
                    $foreignRow->id = 0;
                    foreach ($configuration['columns'] as $column => $type) {
                        $foreignRow->{$column} = self::filter('', $type);
                    }
                }
                // Récursivité
                $history[] = $search;
                $row->{$foreignKey} = (object)array_merge((array)$foreignRow, (array)self::instanceForeign(
                    $foreignRow,
                    $configuration['foreignKeys'],
                    $history
                ));
            }
        }
        return $row;
    }

}