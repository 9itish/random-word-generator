<?php

$db = new SQLite3('words.db');

$db->enableExceptions(true);

$files = glob('data/*.txt');

// Begin transaction
$db->exec("BEGIN TRANSACTION");

// I am processing 100 words at a time.
$batch_size = 1000;

foreach ($files as $file) {
    $data = file_get_contents($file);
    $words = explode(',', $data);

    for($i = 0; $i <= 10; $i++) {
        shuffle($words);
    }

    // The filename follows the format: data/category.lang.txt.
    // There can be two or more categories separated by a '-'. The lang is a two-character code.
    $categories = explode('-', substr($file, 5, -7));
    $language = substr($file, -6, 2);

    $category_ids = [];

    foreach($categories as $category) {

        $category_id = $db->querySingle("SELECT id FROM categories WHERE name = '$category'", false);

        if (!$category_id) {
            $db->exec("INSERT INTO categories (name) VALUES ('$category')");
            $category_id = $db->lastInsertRowID();
        }

        $category_ids[] = $category_id;
    }

    echo '<p>Processing ' . count($words) . ' words from the file: ' . basename($file) . '</p>';

    $word_count = 0;

    for ($i = 0; $i < count($words); $i += $batch_size) {

        $batch = array_slice($words, $i, $batch_size);

        $batch_words = array_map('trim', $batch);
        $placeholders = implode(',', array_fill(0, count($batch_words), '?'));

        // Execute the query that determines if any of the words in this batch are already in the database by executing the following query.
        $existing_words_stmt = $db->prepare("SELECT word FROM words WHERE word IN ($placeholders) AND language = ?");
        foreach ($batch_words as $index => $word) {
            $existing_words_stmt->bindValue($index + 1, $word, SQLITE3_TEXT);
        }
        $existing_words_stmt->bindValue(count($batch_words) + 1, $language, SQLITE3_TEXT);

        $existing_words_result = $existing_words_stmt->execute();

        // Store all existing words from this batch in the database.
        $existing_words = [];
        while ($row = $existing_words_result->fetchArray(SQLITE3_ASSOC)) {
            $existing_words[] = $row['word'];
        }

        // Get rid of any words in the batch that are also part of the database
        $new_words = array_diff($batch_words, $existing_words);

        // Now insert whatever words are left in the batch into the database.
        if (!empty($new_words)) {
            $insert_words_query = "INSERT INTO words (word, length, language) VALUES ";
            $insert_values = [];

            foreach ($new_words as $new_word) {
                $length = strlen($new_word);
                $new_word = SQLite3::escapeString(trim($new_word));

                $insert_values[] = "('$new_word', $length, '$language')";
                $word_count++;
            }

            $insert_words_query .= implode(',', $insert_values);
            $db->exec($insert_words_query);
        }

        // We are going over all the words in the current batch to update their categories because even if a word already exists in the database it might have been assigned additional categories.
        $new_word_ids_stmt = $db->prepare("SELECT id, word FROM words WHERE word IN ($placeholders) AND language = ?");
        foreach ($batch_words as $index => $word) {
            $new_word_ids_stmt->bindValue($index + 1, $word, SQLITE3_TEXT);
        }
        $new_word_ids_stmt->bindValue(count($batch_words) + 1, $language, SQLITE3_TEXT);

        $new_word_ids_result = $new_word_ids_stmt->execute();

        $insert_word_categories_query = "INSERT OR IGNORE INTO word_categories (word_id, category_id) VALUES ";
        $insert_word_category_values = [];

        // Looping over assigned categories to attach them to each word.
        while ($row = $new_word_ids_result->fetchArray(SQLITE3_ASSOC)) {
            foreach ($category_ids as $category_id) {
                $insert_word_category_values[] = "({$row['id']}, $category_id)";
            }
        }

        if (!empty($insert_word_category_values)) {
            $insert_word_categories_query .= implode(',', $insert_word_category_values);
            $db->exec($insert_word_categories_query);
        }
    }

    echo '<p>Processed all data from ' . $file . ' and inserted ' . $word_count . ' new words!</p>';
}

// Commit transaction
$db->exec("COMMIT");

echo "Data imported successfully!";


