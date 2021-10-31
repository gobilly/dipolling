<?php
require "template/menu.php";

$sql = "SELECT * FROM list_table";

$result = $show_polling->get_Query($sql);
$rows_list_table = $show_polling->loopFetch($result);

// Tambah Tabel polling
if(isset($_POST['submit'])){
    $table_name = $_POST['tablename'];
    $name_lower = strtolower($table_name);
    $name_replace = str_replace(" ", "_", $name_lower);
    $table_name_result = $name_replace;

    if(!empty($table_name_result)){

        // kueri tambah tabel
        $add_table_query = 'CREATE TABLE '. $table_name_result .' (id INT AUTO_INCREMENT PRIMARY KEY, polimg VARCHAR(150), polname VARCHAR(200), polvote INT)';
        $dipolling->add_table($add_table_query);

        //kueri tambah item di list tabel
        $add_list_table_item = "INSERT INTO list_table VALUES(NULL, '$table_name_result', 0)";
        $dipolling->add_table($add_list_table_item);
        header("Location: poll.php?name=" . $table_name_result . "&add=1&activate=0");

    }else{
        //Notifikasi error
       echo $notify->showNotify(false, 'Isi kolom nama tabel');

    }
}

// cek tabel polling aktif
$check_active_polling = $show_polling->get_Query("SELECT * FROM list_table WHERE polling_active=1");
if (mysqli_num_rows($check_active_polling) != 0) {
    $name_active_polling = $show_polling->singleFetch($check_active_polling);
    $name_active_polling_s = $name_active_polling['name'];
}else{
    $name_active_polling_s = '-';
}


// Notifikasi
if (isset($_GET['activate'])) {
    echo $notify->showNotify(true,'Tabel ' . str_replace('_', ' ', $_GET['table_name']) . ' aktif');
}elseif(isset($_GET['nonactivate'])){
    echo $notify->showNotify(true,'Tabel ' . str_replace('_', ' ', $_GET['table_name']) . ' nonaktif');
}
?>

    <div class="dib-admin-page-title fs-4 text-dark fw-bold">
        <i class="bi bi-bar-chart-line"></i> Polling
    </div>
    <div class="row d-flex justify-content-between">
        <div class="dip-admin-box text-dark col-sm-5 mt-2">
            <p class="text-dark">Total table</p>
            <span><?php echo mysqli_num_rows($show_polling->get_Query("SELECT * FROM list_table"));?></span>
        </div>
        <div class="dip-admin-box text-dark col-sm-5 mt-2">
            <p class="text-dark">Polling active <i class="bi bi-patch-check-fill text-success"></i></p>
            <span class="text-capitalize"><?= str_replace('_', ' ', $name_active_polling_s); ?></span>
        </div>
    </div>
    <a href="#" class="btn btn-lg btn-success mt-5" data-bs-toggle="modal" data-bs-target="#addPollings"><i class="bi bi-plus-lg"></i> Add Polling</a>
    <!-- Modal -->
    <div class="modal fade" id="addPollings" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Polling</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form class="" action="" method="post">
                <label for="tabel-name" class="mb-3">Nama Tabel</label>
                <input type="text" id="tabel-name" name="tablename" placeholder="Enter Table name" class="form-control mb-4">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="submit" class="btn btn-success">Add Polling</button>
              </div>
          </form>
        </div>
      </div>
    </div>
    <table class="table mt-5">
        <tr class="table-dark">
            <th>No</th>
            <th>Name</th>
            <th>Total Vote</th>
            <th class="text-center">Status</th>
        </tr>
        <?php $i = 1; ?>
        <?php foreach($rows_list_table as $row) :?>

            <?php
            // Ambil total keseluruhan dari tabel polling target
            $name_source_table = $row['name'];
            $name_source_query = "SELECT SUM(polvote) FROM " . $name_source_table;
            $res = $show_polling->get_Query($name_source_query);
             ?>

        <tr>
            <td><?php echo $i; ?></td>
            <td><a href="poll.php?name=<?php echo $row['name']; ?>&add=0"><?php echo str_replace("_", " ", $row['name']); ?></a></td>
            <td>
                <?php
                    $single = $show_polling->singleFetch($res);
                    if($single['SUM(polvote)'] == ""){
                        echo 0;
                    }else {
                        echo $single['SUM(polvote)'];
                    }
                ?>
            </td>
            <td class="text-center">
                <?php
                if ($row['polling_active'] > 0) {
                    echo '<i class="bi bi-check-circle text-success"></i>';
                }else{
                    echo '<span class="text-secondary">-</span>';
                }
                ?>
            </td>
        </tr>

        <?php
        $i++;
         endforeach; ?>
    </table>

<?php require "template/main.php"; ?>
