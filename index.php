<?php

require_once 'classes/db.php';

$db = new db();
if ($db) {
    $sectors = $db->loadSectorTree();
}
    if (isset($_POST['saveValues'])) {
        session_start();

        if (!isset($_POST['name'])) {
            $errors[] = 'Palun sisestage nimi';
        } elseif (!isset($_POST['sectors'])) {
            $errors[] = 'Palun valige sektorid';
        } elseif (!isset($_POST['agreeToTerms'])) {
            $errors[] = 'Palun nõustuge tingimustega';
        }
        if (!isset($errors)) {
            try {
                $selectedSectors = $_POST['sectors'];
                $name = $_POST['name'];
                $agreeToTerms = $_POST['agreeToTerms'];
                if (isset($_SESSION['user_id'])) {
                    $db->updateUserData($_SESSION['user_id'], $name, $selectedSectors, $agreeToTerms);
                }
                else {
                    $id = $db->insertUserData($name, $selectedSectors, $agreeToTerms);
                    $_SESSION['user_id'] = $id;
                }
            } catch (PDOException $e) {
                $errors[] = $e->getMessage();
            }
        }
    }

?>

<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
</head>
<body>
Please enter your name and pick the Sectors you are currently involved in.
<div>
    <?php if (isset($errors)):?>
        <?php foreach ($errors as $error):?>
            <h4><?php echo $error?></h4>
        <?php endforeach;?>
    <?php endif;?>
</div>
<form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
    <br>
    <br>
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" value="<?php if (isset($_POST['name'])) echo htmlspecialchars($_POST['name']); ?>" required>
    <br>
    <label for="sectors">Sectors:</label>
    <br>
    <select multiple="" size="10" name="sectors[]" id="sectors">
        <?php if (isset($sectors)):?>
            <?php foreach ($sectors as $sector):?>
                <option <?php if (isset($_POST['sectors']) && in_array($sector['id_sector'], $_POST['sectors'])) echo 'selected="selected"'?>
                        value=" <?php echo $sector['id_sector']?>">
                    <?php echo $sector['name'];?></option>
            <?php endforeach;?>
        <?php endif;?>
    </select>
    <br>
    <br>
    <input type="checkbox" value="1" <?php if (isset($_POST['agreeToTerms'])) echo 'checked' ?> name="agreeToTerms"> Agree to terms
    <br>
    <br>
    <input type="submit" name="saveValues" value="Save">
</form>
<script
    src="https://code.jquery.com/jquery-3.6.0.js"
    integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk="
    crossorigin="anonymous"></script>
<script src="db_insertion/scripts.js"></script>
</body>
</html>

