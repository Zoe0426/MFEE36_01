<?php
require './partsNOEDIT/connect-db.php';
$output = [
    'success' => false, #更新成功或失敗的結果（MUST）
    'postData' => $_POST, # 除錯用的
    'code' => 0,
    'error' => [],
];

// $rest_sid = isset($_POST['rest_sid']) ? intval($_POST['rest_sid']) : 0;



/*  -- `rest_menu`=?,
    -- `rest_f_img`=?,
    -- `ml_time`=?,
    -- `weekly`=?,*/

/* // $_POST['rest_menu'],
   // $_POST['rest_f_img'],
   // $_POST['ml_time'],
   // $_POST['weekly'],
   */

if (!empty($_POST['rest_name']) and !empty($_POST['rest_sid'])) {

    $isPass = true;
    $sqlParent = "UPDATE `rest_info` 
    SET 
    `rest_name`=?,
    `catg_sid`=?,
    `rest_phone`=?,

    `rest_address`=?,
    `rest_info`=?,
    `rest_notice`=?,

    `rest_f_title`=?,
    `rest_f_ctnt`=?,
    `date_start`=?,

    `date_end`=?,
    `m_start`=?,
    `m_end`=?,

    `e_start`=?,
    `e_end`=?,
    `n_start`=?,

    `n_end`=?,
    `p_max`=?,
    `pt_max`=?


 
    WHERE rest_sid= ? ";


    $stmt = $pdo->prepare($sqlParent);
    $weeklyString = implode(',', $_POST['weekly']);


    $stmt->execute([
        $_POST['rest_name'],
        $_POST['catg_sid'],
        $_POST['rest_phone'],

        $_POST['rest_address'],
        $_POST['rest_info'],
        $_POST['rest_notice'],

        $_POST['rest_f_title'],
        $_POST['rest_f_ctnt'],
        $_POST['date_start'],

        $_POST['date_end'],
        $_POST['m_start'],
        $_POST['m_end'],

        $_POST['e_start'],
        $_POST['e_end'],
        $_POST['n_start'],

        $_POST['n_end'],
        $_POST['p_max'],
        $_POST['pt_max'],



        $_POST['rest_sid'],
    ]);


    if (!empty($_POST['rest_sid'])) {
        $delSid = $_POST['rest_sid'];

        // 刪除資料庫中對應的項目
        $rsqlDelete = "DELETE FROM rest_c_rr WHERE rest_sid = :restSid";
        $rstmDelete = $pdo->prepare($rsqlDelete);
        $rstmDelete->execute(['restSid' => $delSid]);

        // 插入被勾選的項目
        $restRule = $_POST['rest_rule'];
        $rsqlInsert = "INSERT INTO rest_c_rr (rest_sid, r_sid) VALUES (:restSid, :rSid)";
        $rstmInsert = $pdo->prepare($rsqlInsert);
        foreach ($restRule as $rSid) {
            $rstmInsert->execute(['restSid' => $delSid, 'rSid' => $rSid]);
        }

        $ssqlDelete = "DELETE FROM rest_c_rs WHERE rest_sid = :restSid";
        $sstmDelete = $pdo->prepare($ssqlDelete);
        $sstmDelete->execute(['restSid' => $delSid]);


        $restSvc = $_POST['rest_svc'];
        $ssqlInsert = "INSERT INTO rest_c_rs (rest_sid, s_sid) VALUES (:restSid, :sSid)";
        $sstmInsert = $pdo->prepare($ssqlInsert);
        foreach ($restSvc as $sSid) {
            $sstmInsert->execute(['restSid' => $delSid, 'sSid' => $sSid]);
        }

        $output['success'] = true;
    }
}
header('Content-Type: application/json');
echo json_encode($output, JSON_UNESCAPED_UNICODE);