<?php

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$dbPath = $_ENV['DB_PATH'];

$pdo = new PDO("sqlite:$dbPath");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$queries = [
    'CREATE TABLE IF NOT EXISTS cities (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE
    )',
    'CREATE TABLE IF NOT EXISTS attractions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        distance_from_center REAL NOT NULL,
        city_id INTEGER NOT NULL,
        FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE
    )',
    'CREATE TABLE IF NOT EXISTS travelers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL UNIQUE
    )',
    'CREATE TABLE IF NOT EXISTS ratings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        traveler_id INTEGER NOT NULL,
        attraction_id INTEGER NOT NULL,
        score INTEGER NOT NULL CHECK(score BETWEEN 1 AND 5),
        UNIQUE (traveler_id, attraction_id),
        FOREIGN KEY (traveler_id) REFERENCES travelers(id) ON DELETE CASCADE,
        FOREIGN KEY (attraction_id) REFERENCES attractions(id) ON DELETE CASCADE
    )'
];

foreach ($queries as $query) {
    $pdo->exec($query);
}

echo "Tables created successfully.\n";