<?php
require './partsNOEDIT/connect-db.php' ?>
<?php
$title = '編輯';

$board_sid = isset($_GET['board_sid']) ? intval($_GET['board_sid']) : 0;
$sql = "SELECT * FROM `post_board` WHERE `board_sid`={$board_sid}";

$upDate = $pdo->query($sql)->fetch();
// print_r($upDate);
if (empty($upDate)) {
  header('Location: p_readPost_board.php');
  exit;
}
?>
<?php include './partsNOEDIT/html-head.php' ?>
<style>
  .form-text {
    color: red;
  }
</style>

<?php include './partsNOEDIT/navbar.php' ?>

<div class="col-auto col-md-10 mt-5">
  <div class="container mt-5">
    <div class="row">
      <div class="col-6">
        <div class="card mt-3">
          <div class="card-body">

            <h5 class="card-title">編輯文章公告</h5>
            <form name="form1" onsubmit="checkForm(event)">
              <div class="mb-3">
                <label for="board_name">看板名稱：</label>
                <input type="text" name="board_name" id="board_name" data-required="1" value="<?= $upDate['board_name'] ?>" />
                <div class="form-text"></div>
              </div>
              <div class="alert alert-danger" role="alert" id="infoBar" style="display: none"></div>
              <input type="text" style="display:none" name="board_sid" value="<?= "$board_sid" ?>">
              <button type="submit" class="btn btn-primary">確定</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php include './partsNOEDIT/script.php' ?>
  <script>
    const board_name = document.querySelector('#board_name');
    const infoBar = document.querySelector('#infoBar');

    console.log(board_name);
    // 取得必填欄位
    const fields = document.querySelectorAll('form *[data-required="1"]');

    function checkForm(event) {
      event.preventDefault(); //避免submit就先送出

      for (let f of fields) {
        f.style.border = '1px solid blue';
        f.nextElementSibling.innerHTML = '';
      }

      let isPass = true; //預設值是通過的

      //跳出提示
      if (board_name.value === "") {
        isPass = false;

        board_name.style.border = '1px solid red';
        board_name.nextElementSibling.innerHTML = '請輸入文字';
      }

      if (isPass) {
        const fd = new FormData(document.form1); //沒有外觀的表單

        //infobar的東西
        fetch("p_updateBoard_api.php", {
            method: "POST", //資料傳遞的方式
            body: fd, // Content-Type 省略, multipart/form-data
          })
          .then((r) =>
            r.json()
          )
          .then((obj) => {
            console.log(obj);
            if (obj.success) {
              infoBar.classList.remove("alert-danger");
              infoBar.classList.add("alert-success");
              infoBar.innerHTML = "編輯成功";
              infoBar.style.display = "block";
            } else {
              infoBar.classList.remove("alert-success");
              infoBar.classList.add("alert-danger");
              infoBar.innerHTML = "編輯失敗";
              infoBar.style.display = "block";
            }
            // setTime(() => {
            //   infoBar.style.display = "none";
            // }, 2000);

            //跳轉頁面回去read
            location.href = 'p_readPost_board.php';
          })
          .catch(ex => {
            console.log(ex);
            infoBar.classList.remove('alert-success');
            infoBar.classList.add('alert-danger');
            infoBar.innerHTML = '編輯發生錯誤';
            infoBar.style.display = 'block';
            // setTimeout(() => {
            //     infoBar.style.display = 'none';
            // }, 2000);
          })
      } else {
        // 沒通過檢查
      }

      //輸入東西之後，提示消失
      board_name.addEventListener('input', (event) => {
        if (event.target.value != "") {
          event.target.style.border = '1px solid #ccc';
          event.target.nextElementSibling.textContent = "";
        } else {
          event.target.style.border = '1px solid red';
          event.target.nextElementSibling.textContent = "請輸入文字";
        }
      })



    }
  </script>
  <?php include './partsNOEDIT/html-foot.php' ?>