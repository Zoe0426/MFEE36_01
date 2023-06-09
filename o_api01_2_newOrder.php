<?php
require './partsNOEDIT/connect-db.php';

$output = [
    'orderSuccess' => false, #新增資料成功或失敗的結果（MUST）
    'postData' => $_POST, # 除錯用的
    'code' => 0,
    'error' => [],

];
$couponSendSid = isset($_POST['coupon']) ? $_POST['coupon'] : "";
$postAddress = isset($_POST['address']) ? $_POST['address'] : '';
$member_sid = isset($_POST['member_sid']) ? $_POST['member_sid'] : '';
$shopOrders = isset($_POST['prod']) ? $_POST['prod'] : '';
$actOrders = isset($_POST['act']) ? $_POST['act'] : '';
//商城-若有商城資料，取DB的金額*數量，封裝Order_detail的資料陣列。
$forProd = [];
if ($shopOrders) {
    foreach ($shopOrders as $d) {
        $dParsed =  urldecode($d);
        $dDataget = json_decode($dParsed, true);
        $dProsid = $dDataget['pro_sid'];
        $dProDetsid = $dDataget['proDet_sid'];
        $dProQty = intval($dDataget['prodQty']);

        $sqls = "SELECT 
        sp.`pro_sid` AS rel_sid, 
        spd.`proDet_sid` AS rel_seq_sid, 
        sp.`pro_name` AS relName, 
        spd.`proDet_name` AS rel_seqName, 
        spd.`proDet_price` AS prodAmount, 
        spd.proDet_price*? AS amount
        FROM `shop_pro` sp 
        JOIN `shop_prodet` spd 
        ON sp.`pro_sid` = spd.`pro_sid` 
        WHERE sp.pro_sid=? AND spd.proDet_sid = ?";

        $stm = $pdo->prepare($sqls);
        $stm->execute([$dProQty, $dProsid, $dProDetsid]);
        $sResult = $stm->fetch();
        $sResult['relType'] = 'prod';
        $sResult['prodQty'] = $dProQty;
        $sResult['adultAmount'] = null;
        $sResult['childAmount'] = null;
        $sResult['adultQty'] = null;
        $sResult['childQty'] = null;
        $forProd[] = $sResult;
    }
}
//商城-若有活動資料，取DB的金額*人數量，封裝Order_detail的資料陣列。
$forAct = [];
if ($actOrders) {
    foreach ($actOrders as $a) {
        $aParsed =  urldecode($a);
        $aDataget = json_decode($aParsed, true);
        $aActsid = $aDataget['act_sid'];
        $aGroupsid = $aDataget['group_sid'];
        $aAdultQty = intval($aDataget['adultQty']);
        $aChildQty = intval($aDataget['childQty']);

        $sqla = "SELECT 
        ai.`act_sid` AS rel_sid, 
        ai.`act_name` AS relName, 
        ag.`group_sid` AS rel_seq_sid, 
        ag.`group_date` AS rel_seqName, 
        ag.`price_adult` AS adultAmount, 
        ag.`price_kid` AS childAmount, 
        ag.`price_adult`*?+ag.`price_kid`*? AS amount
        FROM `act_info` ai 
        JOIN `act_group` ag ON ai.`act_sid` = ag.`act_sid` 
        WHERE ai.act_sid = ? AND ag.group_sid = ?";

        $stm = $pdo->prepare($sqla);
        $stm->execute([$aAdultQty, $aChildQty, $aActsid, $aGroupsid]);
        $aResult = $stm->fetch();
        $aResult['prodAmount'] = null;
        $aResult['prodQty'] = null;
        $aResult['adultQty'] = $aAdultQty;
        $aResult['childQty'] = $aChildQty;
        $aResult['relType'] = 'event';

        $forAct[] = $aResult;
    }
}
//封裝所有訂單明細
$orderDetails = array_merge($forProd, $forAct);
//若有COUPON，去資料庫拿金額
if ($couponSendSid) {
    $sqlc =
        "SELECT ct.coupon_price
        FROM mem_coupon_send cs
        JOIN mem_coupon_type ct ON cs.coupon_sid = ct.coupon_sid
    WHERE
        cs.couponSend_sid = ?";

    $stm = $pdo->prepare($sqlc);
    $stm->execute([$couponSendSid]);
    $couponAmount = $stm->fetchColumn();
} else {
    $couponAmount = 0;
}
//
$relAmount = 0;
foreach ($orderDetails as $o) {
    $relAmount += intval($o['amount']);
}

//===========CREATE ORDER===========

$sqlHead = "SELECT IFNULL(MAX(order_sid), 'ORD0000') FROM `ord_order`";
$stmt1 = $pdo->query($sqlHead);
$last_ord_sid = $stmt1->fetchColumn();

if ($last_ord_sid === false) { // 空表格的話，第一筆是xxx0001
    $new_ord_sid = 'ORD00001';
} else { // 有訂單
    $new_ord_num = (int)substr($last_ord_sid, 3) + 1;
    $new_ord_sid = 'ORD' . sprintf('%05d', $new_ord_num);
}

// ====加到父表格====
$sqlParent = "INSERT INTO `ord_order`
(`order_sid`, `member_sid`, `coupon_sid`, 
`postAddress`, `postType`, `postStatus`, 
`treadType`, `relAmount`, `postAmount`, 
`couponAmount`, `order_status`, `creator`, 
`createDt`, `moder`, `modDt`) 
VALUES 
(?,?,?,
?,?,?,
?,?,?,
?,?,?,
NOW(),?,NOW())";
$stmt2 = $pdo->prepare($sqlParent);
$stmt2->execute([
    $new_ord_sid, $member_sid, $couponSendSid,
    $postAddress, 1, 1,
    3, $relAmount, 80,
    $couponAmount, 0, "Admin01",
    null
]);
$output['createNewOrder'] = !!$stmt2->rowCount();
//====加到子表格====

//所有訂單明細
$orderDetails = array_merge($forProd, $forAct);
foreach ($orderDetails as $o) {
    $sqlChild = "INSERT INTO `ord_details`
        (`order_sid`, `relType`, `rel_sid`, 
        `rel_seq_sid`, `relName`, `rel_seqName`, 
        `prodAmount`, `prodQty`, `adultAmount`, 
        `adultQty`, `childAmount`, `childQty`, 
        `amount`) 
        VALUES 
        (?,?,?,
        ?,?,?,
        ?,?,?,
        ?,?,?,
        ?)";
    $stm3 = $pdo->prepare($sqlChild);

    $stm3->execute([
        $new_ord_sid, $o['relType'], $o['rel_sid'],
        $o['rel_seq_sid'], $o['relName'], $o['rel_seqName'],
        $o['prodAmount'], $o['prodQty'], $o['adultAmount'],
        $o['adultQty'], $o['childAmount'], $o['childQty'],
        $o['amount']
    ]);
}
$output['createNewOrderDetails'] = !!$stmt2->rowCount();
//增加地址
if ($output['createNewOrderDetails'] == true) {
    $sqlAddress = "INSERT INTO `mem_address`
    ( `member_sid`, `order_sid`, `address`,
     `default_status`, `create_time`, `update_time`) 
    VALUES (?,?,?,
    ?,NOW(),NOW())";
    $stmadd = $pdo->prepare($sqlAddress);
    $stmadd->execute([
        $member_sid,
        $new_ord_sid,
        $postAddress,
        0
    ]);
    $output['createNewAddress'] = !!$stmadd->rowCount();
}

//更新購物車內容
if ($output['createNewOrderDetails'] == true) {
    foreach ($orderDetails as $o) {
        $sqlDelCart = "UPDATE `ord_cart`
        SET `orderStatus` = '002'
        WHERE
        `rel_sid` = ? AND `rel_seqNum_sid` = ?";
        $stm4 = $pdo->prepare($sqlDelCart);
        $stm4->execute([
            $o['rel_sid'],
            $o['rel_seq_sid']
        ]);
    }
    $sqlDelCoupon = "UPDATE `mem_coupon_send` SET `coupon_status`=1 WHERE couponSend_sid =?;";
    $stm5 = $pdo->prepare($sqlDelCoupon);
    $stm5->execute([$couponSendSid]);
    $output['orderSuccess'] = true;
}
//更新coupon Status
if ($output['createNewOrderDetails'] == true) {
    $sqlUpdateCouponStatus = "UPDATE `mem_coupon_send` SET `coupon_status`=?, `used_time`=NOW() WHERE `couponSend_sid`=?";
    $stmUpdateCoupon = $pdo->prepare($sqlUpdateCouponStatus);
    $stmUpdateCoupon->execute([
        1,
        $couponSendSid
    ]);
}


header('Content-Type: application/json');
echo json_encode($output, JSON_UNESCAPED_UNICODE);
