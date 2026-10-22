<?php
include("../layouts/header.php");



?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="page-title">
            <h4>Requirement List</h4>
        </div>

    </div>
    <div class="card-body">
        <form action="" id="frmMain" method="post" enctype="multipart/form-data">
            <div class=" table-responsive">

                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Name </th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $stmt = $mysqli->prepare("SELECT * from requirement_mst order by id desc;");
                        // $stmt->bind_param('i', $_GET['id']);
                        $stmt->execute();
                        $po_det = $stmt->get_result();
                        $sno = 1;
                        $total = 0;

                        while ($row = $po_det->fetch_assoc()) {
                            echo '<tr>
                    <td>' . $sno++ . '</td>
                    <td>' . $row['name'] . '</td>
                    <td>' . $row['description'] . '</td>
                    <td>' . $row['created_at'] . '</td>
                    <td>
                    <button type="button" class="btn btn-sm btn-primary ShowList" data-id="' . $row['id'] . '"><i class="fa fa-eye" aria-hidden="true"></i></button>
                    <a  class="btn btn-sm btn-success" href="requirement-list-view?id=' . $row['id'] . '"><i class="fa fa-eye" aria-hidden="true"></i></a>
                    
                    </td>
                            </tr>';
                        }
                        ?>
                    </tbody>

                </table>
            </div>


        </form>
    </div>

</div>

<div
    class="modal fade"
    id="modalId">
    <div
        class="modal-dialog"
        role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitleId">
                    Requirement Products
                </h5>
                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody id="list">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Close
                </button>

            </div>
        </div>
    </div>
</div>

<!-- Optional: Place to the bottom of scripts -->
<script>
    const myModal = new bootstrap.Modal(
        document.getElementById("modalId"),
        options,
    );
</script>





<?php include("../layouts/footer.php"); ?>
<script>
    $(document).on("click", ".ShowList", function() {

        let id = $(this).data("id");
        $.ajax({
            method: 'POST',
            url: 'ajax/get-requirement-list.php',
            data: {
                id: id
            },
            success: function(data) {


                data = JSON.parse(data);
                var row = "";
                var sno = 1;
                data.forEach(element => {

                    row += `<tr>
                                <td>${sno++}</td>
                                <td>${element.product_name}</td>
                                <td>${element.qty}</td>
                            </tr>`;

                });
                $("#list").html(row)
                $("#modalId").modal("show")

            }
        });
    });
</script>