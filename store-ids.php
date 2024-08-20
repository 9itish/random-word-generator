<?php

$db = new SQLite3('words.db');

$categories = ['adjective', 'adverb', 'conjunction', 'noun', 'preposition', 'verb', 'fruit'];

foreach($categories as $category) {
    
    // Get the ID of all words with a specific category.
    $result = $db->query("SELECT word_id FROM word_categories WHERE category_id = (SELECT id FROM categories WHERE name = '$category')ORDER BY RANDOM()");
    $ids = [];

    $count = 0;
    $file_num = 0;

    // Store these IDs in a multi-dimensional array 
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $ids[$file_num][] = $row['word_id'];
        $count += 1;

        if($count%2000 == 0) {
            $file_num += 1;
        }
    }

    $idx = 1;

    // Loop over the multi-dimensional array to store each index's array of IDs in one file.
    // We are doing this because shorter files make data randomization faster.
    foreach($ids as $id_array) {
        file_put_contents('ids/'.$category.'-'.$idx.'.txt', implode(',', $id_array));
        $idx += 1;
    }
}

$result = $db->query("SELECT w.id FROM words w ORDER BY RANDOM()");
$ids = [];

$count = 0;
$file_num = 0;

while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $ids[$file_num][] = $row['id'];
    $count += 1;

    if($count%2000 == 0) {
        $file_num += 1;
    }
}

// print_r($ids);

$idx = 1;

foreach($ids as $id_array) {
    file_put_contents('ids/words-'.$idx.'.txt', implode(',', $id_array));
    $idx += 1;
}