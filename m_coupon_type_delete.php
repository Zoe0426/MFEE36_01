<?php
require "./partsNOEDIT/connect-db.php";

$sid = isset($_GET["coupon_sid"]) ? $_GET["coupon_sid"] : '';
$sql = "DELETE FROM `mem_coupon_type` WHERE coupon_sid=:sid";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':sid', $sid, PDO::PARAM_STR);
$stmt->execute();
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$sid = '';
$comeFrom = "m_coupon_type-list.php";
if (!empty($_SERVER["HTTP_REFERER"])) {
    $comeFrom = $_SERVER["HTTP_REFERER"];
};
unset($_GET);
header("Location: " . $comeFrom);
