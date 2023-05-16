<?php
require './partsNOEDIT/connect-db.php';

$perPage = 15; # 每頁最多幾筆
$page = isset($_GET['page']) ? intval($_GET['page']) : 1; # 用戶要看第幾頁

if ($page < 1) {
    header('Location: ?page=1');
    exit;
}

$t_sql = "SELECT COUNT(1) FROM act_info WHERE `type_sid`=5";
$totalRows = $pdo->query($t_sql)->fetch(PDO::FETCH_NUM)[0]; # 總筆數
$totalPages = ceil($totalRows / $perPage); # 總頁數
$rows = [];

if ($totalRows) {
    if ($page > $totalPages) {
        header("Location: ?page=$totalPages");
        exit;
    }


    $sql = sprintf("SELECT ai.`act_sid`,`type_sid`,`act_name`,`act_content`,ag.`group_date`,`group_time`,`ppl_max`,`act_post_date` FROM `act_info` ai JOIN `act_group` ag ON ai.`act_sid`=ag.`act_sid` WHERE `type_sid`=5 ORDER BY `act_sid` DESC LIMIT %s, %s", ($page - 1) * $perPage, $perPage);



    $rows = $pdo->query($sql)->fetchAll();
}




?>
<?php include './partsNOEDIT/html-head.php' ?>
<?php include './partsNOEDIT/navbar.php' ?>



<div class="container m-5">
    <div class="row">

        <!-- <div class="input-group">
            <div class="form-outline">
                <input type="search" id="form1" class="form-control" />
                <label class="form-label" for="form1">Search</label>
            </div>
            <button type="button" class="btn btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </div> -->

        <!-- 頁數 -->
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item <?= 1 == $page ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=1">
                        <i class="fa-solid fa-angles-left"></i>
                    </a>
                </li>
                <li class="page-item <?= 1 == $page ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">
                        <i class="fa-solid fa-angle-left"></i>
                    </a>
                </li>
                <?php for ($i = $page - 5; $i <= $page + 5; $i++) :
                    if ($i >= 1 and $i <= $totalPages) :
                ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                <?php endif;
                endfor; ?>
                <li class="page-item <?= $totalPages == $page ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">
                        <i class="fa-solid fa-angle-right"></i>
                    </a>
                </li>
                <li class="page-item <?= $totalPages == $page ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $totalPages ?>">
                        <i class="fa-solid fa-angles-right"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="mb-3">
            <label for="type_sid" class="form-label">活動類型搜尋</label>
            <select class="form-select" id="type_sid" name="type_sid" data-required="1">
                <option selected>--請選擇--</option>
                <option value="1">主題派對</option>
                <option value="2">在地活動</option>
                <option value="3">市集展覽</option>
                <option value="4">毛孩講座</option>
                <option value="5">寵物學校</option>
            </select>
            <div class="form-text"></div>
        </div>

        <!-- <div class="row mb-3">
            <div class="col-4">
                <input type="text" class="form-control" id="act_name" name="act_name">
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-primary ">搜尋</button>
            </div>
        </div> -->
    </div>



    <div class="row">
        <table class="table table-bordered table-striped" id="act_list">
            <thead>
                <tr>
                    <th scope="col"><i class="fa-solid fa-trash-can"></i></th>
                    <th scope="col">#</th>
                    <th scope="col">活動名稱</th>
                    <th scope="col">活動類別</th>
                    <th scope="col">活動內容</th>
                    <th scope="col">日期</th>
                    <th scope="col">時段</th>
                    <th scope="col">人數上限</th>
                    <th scope="col">上架時間</th>
                    <th scope="col"><i class="fa-solid fa-pen-to-square"></i></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $r) : ?>
                    <tr>
                        <td><a href="javascript: delete_it(<?= $r['act_sid'] ?>)">
                                <i class="fa-solid fa-trash-can"></i>
                            </a></td>
                        <td><?= $r['act_sid'] ?></td>
                        <td><?= $r['act_name'] ?></td>
                        <td>
                            <?php if ($r['type_sid'] == 1) : ?>
                                主題派對
                            <?php elseif ($r['type_sid'] == 2) : ?>
                                在地活動
                            <?php elseif ($r['type_sid'] == 3) : ?>
                                市集展覽
                            <?php elseif ($r['type_sid'] == 4) : ?>
                                毛孩講座
                            <?php elseif ($r['type_sid'] == 5) : ?>
                                寵物學校
                            <?php endif; ?>
                        </td>
                        <td><?= $r['act_content'] ?></td>
                        <td><?= $r['group_date'] ?></td>
                        <td>
                            <?php if ($r['group_time'] == 0) : ?>
                                上午
                            <?php elseif ($r['group_time'] == 1) : ?>
                                下午
                            <?php elseif ($r['group_time'] == 2) : ?>
                                全天
                            <?php endif; ?>
                        </td>
                        <td><?= $r['ppl_max'] ?></td>
                        <td><?= $r['act_post_date'] ?></td>
                        <td><a href="a_edit.php?act_sid=<?= $r['act_sid'] ?>">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>
</div>
<?php include './partsNOEDIT/script.php' ?>
<script>
    document.querySelector('li.page-item.active a').removeAttribute('href');

    function delete_it(sid) {
        if (confirm(`是否要刪除編號為 ${sid} 的資料?`)) {
            fetch('a_delete.php?act_sid=' + sid)
                .then(response => response.json())
                .then(data => console.log(data))
                .catch(error => console.error(error));
            location.reload();
        }

    }

    const typeSelect = document.getElementById('type_sid');
    //const actList = document.getElementById('table');

    typeSelect.addEventListener('change', function() {
        const actS = typeSelect.value;

        if (actS == 1) {
            window.location.href = 'a_list_admin_TypeS_01.php';
        }

        if (actS == 2) {
            window.location.href = 'a_list_admin_TypeS_02.php';
        }

        if (actS == 3) {
            window.location.href = 'a_list_admin_TypeS_03.php';
        }

        if (actS == 4) {
            window.location.href = 'a_list_admin_TypeS_04.php';
        }

        if (actS == 5) {
            window.location.href = 'a_list_admin_TypeS_05.php';
        }
    });
</script>
<?php include './partsNOEDIT/html-foot.php' ?>