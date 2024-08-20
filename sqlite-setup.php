<?php

$db = new SQLite3('words.db');

$db->exec("CREATE TABLE IF NOT EXISTS words (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    word TEXT NOT NULL,
    length INTEGER NOT NULL,
    language TEXT NOT NULL,
    UNIQUE(word, language)
)");

$db->exec("CREATE TABLE IF NOT EXISTS categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE
)");
 
$db->exec("CREATE TABLE IF NOT EXISTS word_categories (
    word_id INTEGER,
    category_id INTEGER,
    FOREIGN KEY(word_id) REFERENCES words(id),
    FOREIGN KEY(category_id) REFERENCES categories(id),
    UNIQUE(word_id, category_id)
)");



