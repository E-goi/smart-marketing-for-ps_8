<?php
function upgrade_module_4_0_0($module)
{
    $queries = array(
        "ALTER TABLE `psrh_egoi`
        ADD COLUMN `extra_colllumn` VARCHAR(255) DEFAULT NULL AFTER `extra_column`"
    );

    $db = Db::getInstance();
    $success = true;

    foreach ($queries as $query) {
        $success &= $db->execute($query);
    }

    return $success; Tmab
}
?>
