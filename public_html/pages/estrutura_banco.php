<?php
/**
 * estrutura_banco.php
 *
 * Gera um JSON com a estrutura completa do banco MySQL.
 */

// CONFIGURAÇÃO DO BANCO -----------------------------
$config = [
    'host'    => 'localhost',
    'db'      => 'rspdiecast_dbsystem', // <<< ALTERAR AQUI
    'user'    => 'rspdiecast_usrmaster',              // <<< ALTERAR AQUI
    'pass'    => 'X7OjyzhHH2',                  // <<< ALTERAR AQUI
    'charset' => 'utf8mb4',
];

// ---------------------------------------------------
header('Content-Type: application/json; charset=utf-8');

$dsn = "mysql:host={$config['host']};dbname={$config['db']};charset={$config['charset']}";

try {
    $pdo = new PDO($dsn, $config['user'], $config['pass'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error'   => 'Erro ao conectar no banco de dados.',
        'details' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

$dbName = $config['db'];

try {
    // 1) TABELAS ------------------------------------
    $sqlTables = "
        SELECT 
            TABLE_NAME,
            TABLE_TYPE,
            ENGINE,
            TABLE_ROWS,
            CREATE_TIME,
            UPDATE_TIME,
            TABLE_COMMENT
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = :db
        ORDER BY TABLE_NAME
    ";
    $stmt = $pdo->prepare($sqlTables);
    $stmt->execute(['db' => $dbName]);
    $tables = $stmt->fetchAll();

    $schema = [
        'database'     => $dbName,
        'generated_at' => date('c'),
        'tables'       => [],
    ];

    foreach ($tables as $t) {
        $tableName = $t['TABLE_NAME'];

        $schema['tables'][$tableName] = [
            'name'          => $tableName,
            'type'          => $t['TABLE_TYPE'],
            'engine'        => $t['ENGINE'],
            'rows_estimate' => isset($t['TABLE_ROWS']) ? (int)$t['TABLE_ROWS'] : null,
            'created_at'    => $t['CREATE_TIME'],
            'updated_at'    => $t['UPDATE_TIME'],
            'comment'       => $t['TABLE_COMMENT'],
            'columns'       => [],
            'primary_key'   => [],
            'indexes'       => [],
            'foreign_keys'  => [],
            'create_sql'    => null,
        ];
    }

    // 2) COLUNAS ------------------------------------
    $sqlCols = "
        SELECT
            TABLE_NAME,
            ORDINAL_POSITION,
            COLUMN_NAME,
            COLUMN_TYPE,
            DATA_TYPE,
            CHARACTER_MAXIMUM_LENGTH,
            IS_NULLABLE,
            COLUMN_KEY,
            COLUMN_DEFAULT,
            EXTRA,
            COLUMN_COMMENT
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = :db
        ORDER BY TABLE_NAME, ORDINAL_POSITION
    ";
    $stmt = $pdo->prepare($sqlCols);
    $stmt->execute(['db' => $dbName]);
    $cols = $stmt->fetchAll();

    foreach ($cols as $c) {
        $tableName = $c['TABLE_NAME'];
        if (!isset($schema['tables'][$tableName])) {
            // Caso raro, mas por segurança
            continue;
        }

        $schema['tables'][$tableName]['columns'][] = [
            'position'      => (int)$c['ORDINAL_POSITION'],
            'name'          => $c['COLUMN_NAME'],
            'column_type'   => $c['COLUMN_TYPE'],
            'data_type'     => $c['DATA_TYPE'],
            'max_length'    => $c['CHARACTER_MAXIMUM_LENGTH'] !== null 
                               ? (int)$c['CHARACTER_MAXIMUM_LENGTH'] 
                               : null,
            'nullable'      => ($c['IS_NULLABLE'] === 'YES'),
            'key'           => $c['COLUMN_KEY'],      // PRI, MUL, UNI
            'default'       => $c['COLUMN_DEFAULT'],
            'extra'         => $c['EXTRA'],          // auto_increment, etc.
            'comment'       => $c['COLUMN_COMMENT'],
        ];
    }

    // 3) CHAVES PRIMÁRIAS ---------------------------
    $sqlPK = "
        SELECT
            KCU.TABLE_NAME,
            KCU.COLUMN_NAME,
            KCU.ORDINAL_POSITION
        FROM information_schema.KEY_COLUMN_USAGE KCU
        JOIN information_schema.TABLE_CONSTRAINTS TC
          ON  KCU.CONSTRAINT_NAME = TC.CONSTRAINT_NAME
          AND KCU.TABLE_SCHEMA    = TC.TABLE_SCHEMA
          AND KCU.TABLE_NAME      = TC.TABLE_NAME
        WHERE 
            KCU.TABLE_SCHEMA = :db
            AND TC.CONSTRAINT_TYPE = 'PRIMARY KEY'
        ORDER BY KCU.TABLE_NAME, KCU.ORDINAL_POSITION
    ";
    $stmt = $pdo->prepare($sqlPK);
    $stmt->execute(['db' => $dbName]);
    $pks = $stmt->fetchAll();

    foreach ($pks as $pk) {
        $tableName = $pk['TABLE_NAME'];
        if (!isset($schema['tables'][$tableName])) {
            continue;
        }
        $schema['tables'][$tableName]['primary_key'][] = $pk['COLUMN_NAME'];
    }

    // 4) CHAVES ESTRANGEIRAS ------------------------
    $sqlFK = "
        SELECT
            KCU.TABLE_NAME           AS table_name,
            KCU.COLUMN_NAME          AS column_name,
            KCU.CONSTRAINT_NAME      AS constraint_name,
            KCU.REFERENCED_TABLE_NAME  AS referenced_table,
            KCU.REFERENCED_COLUMN_NAME AS referenced_column
        FROM information_schema.KEY_COLUMN_USAGE KCU
        JOIN information_schema.TABLE_CONSTRAINTS TC
          ON  KCU.CONSTRAINT_NAME = TC.CONSTRAINT_NAME
          AND KCU.TABLE_SCHEMA    = TC.TABLE_SCHEMA
          AND KCU.TABLE_NAME      = TC.TABLE_NAME
        WHERE 
            KCU.TABLE_SCHEMA = :db
            AND TC.CONSTRAINT_TYPE = 'FOREIGN KEY'
        ORDER BY table_name, constraint_name, column_name
    ";
    $stmt = $pdo->prepare($sqlFK);
    $stmt->execute(['db' => $dbName]);
    $fks = $stmt->fetchAll();

    foreach ($fks as $fk) {
        $tableName = $fk['table_name'];
        if (!isset($schema['tables'][$tableName])) {
            continue;
        }

        $schema['tables'][$tableName]['foreign_keys'][] = [
            'constraint_name'   => $fk['constraint_name'],
            'column'            => $fk['column_name'],
            'referenced_table'  => $fk['referenced_table'],
            'referenced_column' => $fk['referenced_column'],
        ];
    }

    // 5) ÍNDICES ------------------------------------
    $sqlIdx = "
        SELECT
            TABLE_NAME,
            INDEX_NAME,
            NON_UNIQUE,
            SEQ_IN_INDEX,
            COLUMN_NAME,
            INDEX_TYPE
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = :db
        ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX
    ";
    $stmt = $pdo->prepare($sqlIdx);
    $stmt->execute(['db' => $dbName]);
    $idxRows = $stmt->fetchAll();

    $indexesByTable = [];

    foreach ($idxRows as $row) {
        $tableName = $row['TABLE_NAME'];
        $indexName = $row['INDEX_NAME'];

        if (!isset($indexesByTable[$tableName])) {
            $indexesByTable[$tableName] = [];
        }
        if (!isset($indexesByTable[$tableName][$indexName])) {
            $indexesByTable[$tableName][$indexName] = [
                'name'    => $indexName,
                'unique'  => ($row['NON_UNIQUE'] == 0),
                'type'    => $row['INDEX_TYPE'],
                'columns' => [],
            ];
        }

        $indexesByTable[$tableName][$indexName]['columns'][] = [
            'name'         => $row['COLUMN_NAME'],
            'seq_in_index' => (int)$row['SEQ_IN_INDEX'],
        ];
    }

    foreach ($indexesByTable as $tableName => $indexes) {
        if (!isset($schema['tables'][$tableName])) {
            continue;
        }
        // transforma em array numérico
        $schema['tables'][$tableName]['indexes'] = array_values($indexes);
    }

    // 6) CREATE TABLE (opcional, mas útil pra ser "completo")
    foreach ($schema['tables'] as $tableName => &$tableInfo) {
        $sqlShow = "SHOW CREATE TABLE `{$tableName}`";
        try {
            $stmt = $pdo->query($sqlShow);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                // Em MySQL o campo costuma ser 'Create Table' (tabela) ou 'Create View' (view)
                $createSql = $row['Create Table'] ?? ($row['Create View'] ?? null);
                $tableInfo['create_sql'] = $createSql;
            }
        } catch (PDOException $e) {
            // Se der erro em alguma tabela específica, ignora e segue
            $tableInfo['create_sql'] = null;
        }
    }
    unset($tableInfo); // boa prática ao usar referência em foreach

    // OUTPUT FINAL ----------------------------------
    echo json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error'   => 'Erro ao consultar metadata do banco.',
        'details' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
