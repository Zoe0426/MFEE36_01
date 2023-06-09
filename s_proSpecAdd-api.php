<?php
require './partsNOEDIT/connect-db.php';

$output = [
    'success' => false,
    'postData' => $_POST,       #除錯用
    'code' => 0,
    'error' => ""
];

$specSid = isset($_POST['spec_sid']) ? intval($_POST['spec_sid']) : 0;
if ($specSid > 0) {
    //目前大規格已經編到幾號了
    $sql_maxSpecSidNow = "SELECT MAX(`spec_sid`) FROM `shop_spec`";
    $r_maxSpecSidNow = $pdo->query($sql_maxSpecSidNow)->fetch(PDO::FETCH_NUM)[0];

    $specDetName = htmlentities($_POST['specDet_name']);

    //如果超出既有大編號，那就要新增大類別名稱
    if ($specSid > $r_maxSpecSidNow) {
        $specName = htmlentities($_POST['spec_name']);
        $sql_intoSpec1 = "INSERT INTO `shop_spec`(
        `spec_sid`, `specDet_sid`, `spec_name`, `specDet_name`
        ) VALUES (
        ?,1,?,?
        )";
        $stmt_intospec1 = $pdo->prepare($sql_intoSpec1);
        $stmt_intospec1->execute([
            $r_maxSpecSidNow + 1,
            $specName,
            $specDetName
        ]);
        $output['success'] = !!$stmt_intospec1->rowCount();
    } else {
        //新增至既有大規格
        //1.先搜尋目前細項規格已編到幾號
        $sql_checkSpecDetSidNow = "SELECT COUNT(*) FROM `shop_spec` WHERE `spec_sid` = {$specSid}";
        $r_maxSpecDetSidNow = $pdo->query($sql_checkSpecDetSidNow)->fetch(PDO::FETCH_NUM)[0];
        //2.查詢大規格名稱
        $sql_checkSpecName = "SELECT `spec_name` FROM `shop_spec` WHERE `spec_sid` = {$specSid} GROUP BY `spec_name`";
        $r_checkSpecName = $pdo->query($sql_checkSpecName)->fetchColumn();

        $sql_intoSpec2 = "INSERT INTO `shop_spec`(
        `spec_sid`, `specDet_sid`, `spec_name`, `specDet_name`
        ) VALUES (
        ?,?,?,?
        )";
        $stmt_intospec2 = $pdo->prepare($sql_intoSpec2);
        $stmt_intospec2->execute([
            $specSid,
            $r_maxSpecDetSidNow + 1,
            $r_checkSpecName,
            $specDetName
        ]);
        $output['success'] = !!$stmt_intospec2->rowCount();
    }
} else {
    $output['error'] = '沒有選到大規格';
}


header('Content-Type: application/json');
echo json_encode($output, JSON_UNESCAPED_UNICODE);
