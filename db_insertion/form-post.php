<?php

require_once '../classes/db.php';

// php script to load names and values to database with JS ajax
// open index.html in browser -> js selects all values from selectbox, organizes them and posts it to php, where data will be stored to database
if (isset($_POST['action']) && $_POST['action'] == 'saveSectorTree') {
    $sectors = json_decode($_POST['data']);
    $db = new db();
    foreach ($sectors as $sector) {
        try {
            $sector->name = str_replace('&nbsp;', ' ', htmlentities($sector->name));
            $sector->name = trim(html_entity_decode($sector->name));
            $db->insertSectorData((array)$sector);

        } catch (PDOException $e) {
            if (strstr($e->getMessage(),'Duplicate entry')) {
                continue;
            }
            var_dump($e->getMessage());
            die();
        }
    }
    echo 'import OK';

} else {
    header("Location: index.html");

}
