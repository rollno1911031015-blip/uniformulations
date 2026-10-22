<?php
include("../layouts/header.php");

if (isset($_POST['btnUpdate'])) {
  $error = '';
  if (trim($_POST['name']) == '')
    $error = 'Name is required.';

  if (trim($_POST['password']) == '')
    $error = 'Password is required.';
  if (trim($_POST['password']) != '' &&  strlen($_POST['password']) < 5)
    $error = 'Password should be at least 5 digit.';



  if ($error == '') {
    try {
 
      $stmt=$mysqli->prepare("UPDATE  admin_users set name=?,password=? where id=?");
      $stmt->bind_param("ssi",$_POST['name'],$_POST['password'],$_POST['id']);
      $stmt->execute();
      $stmt->close();
      alert('Details updated successfully','success');
      redirect('profile.php');
    } catch (Exception $ex) {
      if ($ex->getCode() == 1062) {
        alert("Mobile Number is already exists.", 'error', 'ERROR');
      } else {
        alert($ex->getMessage(), 'error', 'ERROR');
      }
    }
  } else {
    echo $error;
    alert($error, 'error', 'ERROR');
  }
}


?>
 
      <div class="row">
        <div class="card col-lg-12">
          <div class="card-header">
            <h4 class="pull-left">My Profile</h4>
            <div class="pull-right">
            </div>
          </div>
          <div class="card-body">
            <form action="" class="row" method="post">
              <div class="">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">S.No</th>
                      <th scope="col">Name</th>
                      <th scope="col">Username</th>
                      <th scope="col">Password</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>




                    <?php
                    $stmt = $mysqli->prepare("SELECT * from  admin_users ");
                    //$stmt->bind_param("ssi",$_POST['name'],$_POST['password'],$user['id']);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    $stmt->close();
                    $sno = 1;
                    while ($row = $res->fetch_assoc()) {
                    ?>
                      <tr>
                        <td><?= $sno++ ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['password'] ?></td>
                        <td><button type="button" class="btn btn-primary btn-em edituser" data-id="<?= $row['id'] ?>" data-name="<?= $row['name'] ?>" data-password="<?= $row['password'] ?>"><i class="fa fa-pencil"></i></button></td>
                      </tr>

                    <?php

                    }
                    ?>

                  </tbody>
                </table>
              </div>

            </form>
          </div>
        </div>
      </div>
 

<?php
include("../layouts/footer.php");
?>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST">


      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="mt-2">
            <input type="hidden" name="id" id="id">
            <label>Name</label>
            <input class="form-control" name="name" id="uname">

          </div>
          <div class="mt-2">
            <label>Password</label>
            <input class="form-control" name="password" id="password">

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="btnUpdate" class="btn btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
  $(document).on("click", ".edituser", function() {
    $("#id").val($(this).data('id'))
    $("#uname").val($(this).data('name'))

    $("#password").val($(this).data('password'))
    $("#exampleModal").modal("show");
  })
</script>