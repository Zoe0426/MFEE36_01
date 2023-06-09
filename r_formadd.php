<?php
require './partsNOEDIT/connect-db.php';

// 餐廳類別
$sql = "SELECT `catg_sid`, `catg_name` FROM `rest_catg`";
$items = $pdo->query($sql)->fetchAll();

// 服務項目
$ssql = "SELECT `s_sid`, `s_name` FROM `rest_svc`";
$sitems = $pdo->query($ssql)->fetchAll();

//攜帶規則
$rsql = "SELECT `r_sid`, `r_name` FROM `rest_rule`";
$ritems = $pdo->query($rsql)->fetchAll();

?>
<?php include './partsNOEDIT/html-head.php' ?>
<style>
    #rest_pic,
    #f_pic,
    #rest_f_img,
    #pro_img {
        display: none;

    }

    #finalImg {
        border-radius: 4px;
        height: 280px;
        background-color: #efefef;
        padding: 0;
    }

    #imginfo,
    #f_imginfo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }
</style>
<?php include './partsNOEDIT/navbar.php' ?>

<!-- 隱藏餐廳圖片 -->
<form name="rest_pic" id="rest_pic">
    <input type="file" name="tempImg" accept="image/jpeg" id="tempImg">
</form>

<!-- 隱藏特色餐廳圖片 -->
<form name="f_pic" id="f_pic">
    <input type="file" name="tempImg" accept="image/jpeg" id="tempImg_f" enctype="multipart/form-data">
</form>

<!-- 填表單的區域 -->

<form name="rest_form" class="px-3 pt-2 " onsubmit="checkForm(event)">
    <!-- 分頁 -->
    <div class="px-3 pt-4">


        <h3 class="mb-4 ">基本資料</h3>
        <!-- 圖片區 -->
        <div class="row mb-4 px-3">
            <div class="col-3" onclick="restImg()" id="finalImg">
                <img src="" alt="" id="imginfo">
            </div>
            <input type="text" name="pro_img" id="pro_img">
        </div>

        <!-- 資料區 -->
        <div class="row mb-4">
            <div class="col-6">
                <label for="rest_name" class="form-label">餐廳名稱</label>
                <input type="text" class="form-control" id="rest_name" name="rest_name" data-required="1">
                <div class="form-text"></div>
            </div>
            <div class="col-6">
                <label for="" class="form-label">餐廳類別</label>
                <select class="form-select" name="catg_sid" id="catg_sid">
                    <option value="">--請選擇餐廳類別--</option>
                    <?php foreach ($items as $i) : ?>
                        <option value="<?= $i['catg_sid'] ?>"><?= $i['catg_name'] ?></option>
                    <?php endforeach ?>
                </select>
                <div class="form-text"></div>
            </div>
            <!-- <div class="col-3">
                <label for="rest_menu" class="form-label">菜單上傳</label>
                <div class="input-group mb-3">
                    <input type="file" class="form-control" id="inputGroupFile01" name="rest_menu">
                </div>
            </div> -->
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <label for="rest_phone" class="form-label">餐廳電話</label>
                <input type="text" class="form-control" id="rest_phone" name="rest_phone" data-required="1">
                <div class="form-text"></div>
            </div>

            <div class="col-6">
                <label for="rest_address" class="form-label">餐廳地址</label>
                <input type="text" class="form-control" id="rest_address" name="rest_address" data-required="1">
                <div class="form-text"></div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <label for="rest_info" class="form-label">餐廳簡介</label>
                <textarea class="form-control" id="rest_info" name="rest_info" placeholder="最多150字" data-required="1"></textarea>
                <div id="rest_info" class="form-text"></div>
                <div id="rest_info_error" class="form-text"></div>
            </div>

            <div class="col-6">
                <label for="rest_notice" class="form-label">注意事項</label>
                <textarea class="form-control" id="rest_notice" name="rest_notice" placeholder="最多150字"></textarea>
                <div id="rest_notice" class="form-text"></div>
            </div>
        </div>
    </div>

    <hr>
    <!-- 餐廳特色 -->
    <div class="row px-3 pt-4">
        <h3 class="mb-4">餐廳特色</h3>

        <div class="col-4">
            <label for="f_pic" class="form-label">特色圖片</label>
            <div onclick="restImg_f()" id="finalImg">
                <img src="" alt="" id="f_imginfo">
            </div>
            <input type="text" name="rest_f_img" id="rest_f_img">
        </div>


        <div class="col-8">
            <div class="col mt-5">
                <label for="rest_f_title" class="form-label  mt-2">特色標題</label>
                <input type="text" class="form-control" id="rest_f_title" name="rest_f_title">
                <div class="form-text"></div>
            </div>
            <div class="col mt-4">
                <label for="rest_f_ctnt" class="form-label">特色內容</label>
                <textarea class="form-control" id="rest_f_ctnt" name="rest_f_ctnt" placeholder="最多150字"></textarea>
                <div id="f_content" class="form-text"></div>
            </div>
        </div>
    </div>



    <hr>
    <!-- 營業設定 -->

    <div class="px-3 mb-4 pt-4">
        <h3 class="mb-4">營業設定</h3>

        <!-- 資料區 -->
        <div class="row mb-4">
            <div class="col-3">
                <label for="date_start" class="form-label">開始日期</label>
                <input type="date" class="form-control" id="date_start" name="date_start" data-required="1">
                <div class="form-text"></div>
            </div>
            <div class="col-3">
                <label for="date_end" class="form-label">結束日期</label>
                <input type="date" class="form-control" id="date_end" name="date_end">
                <div class="form-text"></div>
            </div>
            <div class="col-3">
                <label for="p_max" class="form-label">人數上限</label>
                <input type="text" class="form-control" id="p_max" name="p_max" data-required="1">
                <div class="form-text"></div>
            </div>
            <div class="col-3">
                <label for="pt_max" class="form-label">寵物上限</label>
                <input type="text" class="form-control" id="pt_max" name="pt_max" data-required="1">
                <div class="form-text"></div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-3">
                <label for="m_start" class="form-label">早上開始時間</label>
                <input type="time" class="form-control" id="m_start" name="m_start" data-required="1">
                <div class="form-text"></div>
            </div>
            <div class="col-3">
                <label for="m_end" class="form-label">早上結束時間</label>
                <input type="time" class="form-control" id="m_end" name="m_end">
                <div class="form-text"></div>
            </div>
            <div class="col-3">
                <label for="e_start" class="form-label">下午開始時間</label>
                <input type="time" class="form-control" id="e_start" name="e_start">
                <div class="form-text"></div>
            </div>
            <div class="col-3">
                <label for="e_end" class="form-label">下午結束時間</label>
                <input type="time" class="form-control" id="e_end" name="e_end">
                <div class="form-text"></div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-3">
                <label for="n_start" class="form-label">晚上開始時間</label>
                <input type="time" class="form-control" id="n_start" name="n_start">
                <div class="form-text"></div>
            </div>
            <div class="col-3">
                <label for="n_end" class="form-label">晚上結束時間</label>
                <input type="time" class="form-control" id="n_end" name="n_end" data-required="1">
                <div class="form-text"></div>
            </div>
            <!-- 用餐時間 -->
            <div class="col-6 " id="ml_time">
                <div class="d-flex align-item-center">
                    <label for="" class="form-label">用餐時間</label>
                    <div class="form-text ml_text"></div>
                </div>
                <div class="d-flex pt-2">
                    <div class=" form-check me-5">
                        <input class="form-check-input" type="radio" name="ml_time" id="60min" value="60">
                        <label class="form-check-label" for="60min">
                            60分鐘
                        </label>
                    </div>
                    <div class="form-check me-5">
                        <input class="form-check-input" type="radio" name="ml_time" id="90min" value="90">
                        <label class="form-check-label" for="90min">
                            90分鐘
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="ml_time" id="120min" value="120">
                        <label class="form-check-label" for="120min">
                            120分鐘
                        </label>
                    </div>
                </div>
            </div>

        </div>
        <!-- 星期幾 -->
        <div class="row mt-4">
            <div class="d-flex align-items-center">
                <label for="" class="form-label">星期幾</label>
                <div class="form-text week"></div>
            </div>
            <div class="d-flex pt-2">
                <div class="form-check me-5">
                    <input class="form-check-input" type="checkbox" value="0" id="sunday" name="weekly[]">
                    <label class="form-check-label" for="sunday">
                        星期日
                    </label>
                </div>
                <div class="form-check me-5">
                    <input class="form-check-input" type="checkbox" value="1" id="monday" name="weekly[]">
                    <label class="form-check-label" for="monday">
                        星期一
                    </label>
                </div>
                <div class="form-check me-5">
                    <input class="form-check-input" type="checkbox" value="2" id="tuesday" name="weekly[]">
                    <label class="form-check-label" for="tuesday">
                        星期二
                    </label>
                </div>
                <div class="form-check me-5">
                    <input class="form-check-input" type="checkbox" value="3" id="wendsday" name="weekly[]">
                    <label class="form-check-label" for="wendsday">
                        星期三
                    </label>
                </div>
                <div class="form-check me-5">
                    <input class="form-check-input" type="checkbox" value="4" id="thursday" name="weekly[]">
                    <label class="form-check-label" for="thursday">
                        星期四
                    </label>
                </div>
                <div class="form-check me-5">
                    <input class="form-check-input" type="checkbox" value="5" id="friday" name="weekly[]">
                    <label class="form-check-label" for="friday">
                        星期五
                    </label>
                </div>
                <div class="form-check ">
                    <input class="form-check-input" type="checkbox" value="6" id="saturday" name="weekly[]">
                    <label class="form-check-label" for="saturday">
                        星期六
                    </label>
                </div>
            </div>
        </div>
    </div>
    <hr>

    <!-- 服務/規範 -->

    <div class="mt-2 pt-4 mb-4 px-3">
        <div class="d-flex">
            <label for="" class="form-label">
                <h3>服務項目</h3>
            </label>
            <div class="form-text svc_items"></div>
        </div>
        <div class="d-flex ">
            <?php foreach ($sitems as $k => $j) : ?>
                <div class="form-check me-5">
                    <input class="form-check-input" type="checkbox" value="<?= $j['s_sid'] ?>" name="rest_svc[]" id="rest_svc[]<?= $j['s_sid'] ?>">
                    <label class="form-check-label" for="rest_svc[]<?= $j['s_sid'] ?>">
                        <?= $j['s_name'] ?>
                    </label>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <div class="mb-3 mt-5 pt-2 px-3">
        <div class="d-flex">
            <label for="" class="form-label">
                <h3>攜帶規則</h3>
            </label>
            <div class="form-text rule_items"></div>
        </div>
        <div class="d-flex ">
            <?php foreach ($ritems as $k => $r) : ?>
                <div class="form-check me-5">
                    <input class="form-check-input" type="checkbox" value="<?= $r['r_sid'] ?>" name="rest_rule[]" id="rest_rule[]<?= $r['r_sid'] ?>">
                    <label class="form-check-label" for="rest_rule[]<?= $r['r_sid'] ?>">
                        <?= $r['r_name'] ?>
                    </label>
                </div>
            <?php endforeach ?>
        </div>
    </div>
    <div class="alert alert-danger" role="alert" id="infoBar" style="display:none"></div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
        <a class="px-4 py-2 btn btn-outline-primary mt-4 mb-4" href="r_read.php">取消</a>
        <button type="submit" class="px-4 py-2 btn btn-primary mt-4 mb-4">新增餐廳</button>
    </div>

</form>
<?php include './partsNOEDIT/script.php' ?>
<script>
    function checkForm(event) {
        event.preventDefault();


        const fields = document.querySelectorAll('form *[data-required="1"]');
        const name = document.querySelector('#rest_name');
        const catg = document.querySelector('#catg_sid');
        const selectedOption = catg.options[catg.selectedIndex];
        const selectedValue = selectedOption.value;
        const phone = document.querySelector('#rest_phone');
        const address = document.querySelector('#rest_address');
        const info = document.querySelector('#rest_info');
        const date_start = document.querySelector('#date_start');
        const date_end = document.querySelector('#date_end');
        const p_max = document.querySelector('#p_max');
        const pt_max = document.querySelector('#pt_max');
        const m_start = document.querySelector('#m_start');
        const n_end = document.querySelector('#n_end');
        const infoBar = document.querySelector('#infoBar');


        let isPass = true;



        // 檢查p_max輸入是否為數字


        //用餐時間沒有填
        const mlTimeContainer = document.querySelector('.ml_text');
        const mlTimeRadios = document.querySelectorAll('input[name="ml_time"]');
        let isMlTimeSelected = false;

        mlTimeRadios.forEach(radio => {
            if (radio.checked) {
                isMlTimeSelected = true;
            }
        });

        if (!isMlTimeSelected) {
            isPass = false;
            mlTimeContainer.innerHTML = '<i class="fas fa-exclamation-circle"></i> 請選擇用餐時間';
            mlTimeContainer.style.color = 'red';
            mlTimeContainer.style.paddingLeft = '10px';
        }


        //星期沒有填

        const weeklyContainer = document.querySelector('.week');
        const checkboxes = document.querySelectorAll('input[name="weekly[]"]');

        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const selectedCheckboxes = document.querySelectorAll('input[name="weekly[]"]:checked');
                if (selectedCheckboxes.length === 0) {
                    isPass = false;
                    weeklyContainer.innerHTML = '<i class="fas fa-exclamation-circle"></i> 請選擇星期!';
                    weeklyContainer.style.color = 'red';
                    weeklyContainer.style.paddingLeft = '10px';
                } else {
                    weeklyContainer.innerHTML = '';
                }
            });
        });

        //服務沒有填

        // const svcItemsContainer = document.querySelector('.svc_items');
        // const checkboxes = document.querySelectorAll('input[name="rest_svc[]"]');


        // checkboxes.forEach((checkbox) => {
        //     checkbox.addEventListener('change', () => {
        //         const selectedCheckboxes = document.querySelectorAll('input[name="rest_svc[]"]:checked');
        //         if (selectedCheckboxes.length === 0) {
        //             isPass = false;
        //             svcItemsContainer.innerHTML = '<i class="fas fa-exclamation-circle"></i> 请选择服务项目！';
        //             svcItemsContainer.style.color = 'red';
        //         } else {
        //             isSvcItemSelected = true;
        //             svcItemsContainer.innerHTML = '';
        //         }
        //     });
        // });

        for (let f of fields) {
            f.style.border = '1px solid #ccc';
            f.nextElementSibling.innerHTML = '';
        }

        name.style.border = '1px solid #ccc';
        name.nextElementSibling.innerHTML = '';


        catg.style.border = '1px solid #ccc';
        catg.nextElementSibling.innerHTML = '';


        //完全沒有輸入的情況
        for (let f of fields) {
            if (!f.value) {
                isPass = false;
                f.style.border = '1px solid red';
                f.nextElementSibling.innerHTML = '<i class="fas fa-exclamation-circle"></i> 請輸入資料!';
                f.nextElementSibling.style.color = 'red';
                f.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }
        //餐廳類別沒有篩選

        if (selectedValue == "") {
            isPass = false;
            catg.style.border = '1px solid red';
            catg.nextElementSibling.innerHTML = '<i class="fas fa-exclamation-circle"></i> 請選擇餐廳類別!';
            catg.nextElementSibling.style.color = 'red';
            catg.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // 餐廳名稱格式
        if (name.value.length < 2) {
            isPass = false;
            name.style.border = '1px solid red';
            name.nextElementSibling.innerHTML = '<i class="fas fa-exclamation-circle"></i> 請輸入至少三個字!';
            name.nextElementSibling.style.color = 'red';
            name.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }


        //輸入內容長度限制
        if (info.value.length > 150) {
            isPass = false;
            const count = Number(info.value.length - 150);
            info.style.border = '1px solid red';
            info.nextElementSibling.innerHTML = `字數超過!超過${count}個字`;
            info.nextElementSibling.style.color = 'red';
            info.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        if (isPass) {
            const fd = new FormData(document.rest_form);
            fetch('r_add_api.php', {
                    method: 'POST',
                    body: fd,
                })
                .then(r => r.json())
                .then(obj => {
                    if (obj.success) {
                        infoBar.classList.remove('alert-danger');
                        infoBar.classList.add('alert-success');
                        infoBar.innerHTML = "餐廳新增成功!";
                        infoBar.style.display = 'block';

                    } else {
                        infoBar.classList.remove('alert-success');
                        infoBar.classList.add('alert-danger');
                        infoBar.innerHTML = "餐廳新增失敗";
                        infoBar.style.display = 'block';
                    }
                    setTimeout(() => {
                        infoBar.style.display = 'none';
                        location.href = 'r_read.php';
                    }, 2000);

                    console.log(obj);
                })
                .catch(ex => {
                    console.log(ex);
                    infoBar.classList.remove('alert-success');
                    infoBar.classList.add('alert-danger');
                    infoBar.innerHTML = "發生錯誤";
                    setTimeout(() => {
                        infoBar.style.display = 'none';
                    }, 2000);
                })
        }
        // } else {
        // 沒通過檢查
    }





    const r_tempImg = document.querySelector("#tempImg");

    function restImg() {
        //模擬點擊
        r_tempImg.click();
    }

    r_tempImg.addEventListener("change", () => {
        const fd = new FormData(document.rest_pic);
        fetch('r_file_api.php', { //這邊請填入自己要連結的api名稱
                method: 'POST',
                body: fd,
            }).then(r => r.json())
            .then(obj => {
                if (obj.filename) {
                    const imginfo = document.querySelector('#imginfo');
                    const pro_img = document.querySelector('#pro_img');
                    imginfo.src = `./r_img/${obj.filename}`;
                    imginfo.style.display = "block";
                    pro_img.value = obj.filename;
                }
            }).catch(ex => {
                console.log(ex)
            })
    })



    const tempImg_f = document.querySelector("#tempImg_f");

    function restImg_f() {
        //模擬點擊
        tempImg_f.click();
    }

    tempImg_f.addEventListener("change", () => {
        const fd = new FormData(document.f_pic);
        fetch('r_file_api.php', { //這邊請填入自己要連結的api名稱
                method: 'POST',
                body: fd,
            }).then(r => r.json())
            .then(obj => {
                if (obj.filename) {
                    const f_imginfo = document.querySelector('#f_imginfo');
                    const rest_f_img = document.querySelector('#rest_f_img');
                    f_imginfo.src = `./r_img/${obj.filename}`;
                    f_imginfo.style.display = "block";
                    rest_f_img.value = obj.filename;
                }
            }).catch(ex => {
                console.log(ex)
            })
    })
</script>
<?php include './partsNOEDIT/html-foot.php' ?>