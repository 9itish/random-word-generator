  <?php

$start = time();

$db = new SQLite3('words.db');

$category_list = ['adjective' => 1, 'adverb' => 2, 'conjunction' => 3, 'noun' => 4, 'preposition' => 5, 'verb' => 6, 'fruit' => 7];

$conditions = [];
$category_condition = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $query = "SELECT w.id, w.word FROM words w";

    if (isset($_GET['category'])) {
        $category = $_GET['category'];

        $category_result = $db->query("SELECT id FROM categories WHERE name = '$category'");
        $category_id = $category_result->fetchArray(SQLITE3_ASSOC)['id'];

        $category_condition = " JOIN word_categories wc ON w.id = wc.word_id WHERE wc.category_id = $category_id";
    }

    if (isset($_GET['length'])) {
        $length = $_GET['length'];
        $conditions[] = "w.length = $length";
    }

    // Only filter with minimum length if specific length isn't given.
    if (isset($_GET['min-length']) && !isset($_GET['length'])) {
        $min_length = $_GET['min-length'];
        $conditions[] = "w.length >= $min_length";
    }

    // Only filter with maximum length if specific length isn't given.
    if (isset($_GET['max-length']) && !isset($_GET['length'])) {
        $max_length = $_GET['max-length'];
        $conditions[] = "w.length <= $max_length";
    }

    if (isset($_GET['start'])) {
        $start = $_GET['start'];
        $conditions[] = "w.word LIKE '$start%'";
    }

    if (isset($_GET['end'])) {
        $end = $_GET['end'];
        $conditions[] = "w.word LIKE '%$end'";
    }

    if (isset($_GET['include'])) {
        $inc_letters = explode(',', $_GET['include']);

        $like_conditions = [];

        foreach ($inc_letters as $letter) {
            $letter = trim($letter);
            if (!empty($letter)) {
                $like_conditions[] = "w.word LIKE '%$letter%'";
            }
        }

        if (!empty($like_conditions)) {
            $like_query = implode(' AND ', $like_conditions);
            $conditions[] = "($like_query)";
        }
    }

    if (isset($_GET['exclude'])) {
        $exc_letters = explode(',', $_GET['exclude']);

        $like_conditions = [];

        foreach ($exc_letters as $letter) {
            $letter = trim($letter);
            if (!empty($letter)) {
                $like_conditions[] = "w.word NOT LIKE '%$letter%'";
            }
        }

        if (!empty($like_conditions)) {
            $like_query = implode(' AND ', $like_conditions);
            $conditions[] = "($like_query)";
        }
    }

    try {
        $count = isset($_GET['count']) ? $_GET['count'] : 1;
        if ($count > 15) {
            throw new Exception("Cannot request more than 15 words at a time but you asked for $count!");
        }
    } catch (Exception $e) {
        echo $e->getMessage();
        exit();
    }

    // All other conditions besides the category, are stored in $conditions.
    // If $conditions is empty, we can just grab the ID of different words from the text file to randomly output them.
    // This boost performance significantly.
    if(count($conditions) == 0) {

        if(isset($_GET['category'])) {
            $file_name = $category;
        } else {
            $file_name = 'words';
        }

        $files = glob('ids/'.$file_name.'-*.txt');

        shuffle($files);

        $ids = explode(',', file_get_contents($files[0]));

        shuffle($ids);

        $id_list = implode(',', array_slice($ids, 0, $count));


        $query = "SELECT w.word FROM words w WHERE w.id in ($id_list)";
    } else {

        // If a category is specified, apply other conditions using AND because WHERE is already in the category condition.
        if ($category_condition != '') {
            $query .= $category_condition;

            foreach ($conditions as $condition) {
                $query .= " AND " . $condition;
            }
        } else {

            $idx = 0;

            foreach ($conditions as $condition) {

                if ($idx == 0) {
                    // Apply the first condition using WHERE
                    $query .= " WHERE " . $condition;
                } else {
                    $query .= " AND " . $condition;
                }

                $idx += 1;
            }
        }

        $query .= " ORDER BY RANDOM() LIMIT $count";
    }

    $all_ids = [];
    $ids = [];

    $result = $db->query("$query");

    $output = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $output[] = $row['word'];
    }

    $conditions = [];

    header('Content-Type: application/json');
    echo json_encode(['words' => $output]);
}