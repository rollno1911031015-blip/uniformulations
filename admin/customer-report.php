<?php 
include("../layouts/header.php");




?>


       <div class="row">
           <div class="card col-lg-12">
             <div class="card-header">
             <h4 class="pull-left">Customer Report</h4>
 
             </div>
               <div class="card-body table-responsive">
               <div class="form-group col-md-4">
                <table>
                    <form method="get">
                    <tr>
                        <td>
                        <select name="loc_id" id="loc_id"  class="form-control" required>
                                <option value="">--Select Customer/Employee--</option>
                                <?php
                                $stmt=$mysqli->prepare("SELECT * from   lead  ;" );
                                $stmt->execute();
                                $res=$stmt->get_result();
                                while($row=$res->fetch_assoc()){ 
                                echo '<option value="'.$row['id'].'" >'.$row['name'].'</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td>
                        <button type="submit" name="getrecord" class="btn btn-success">Search</button>
                        </td>
                    </tr>
                    </form>
                </table>

                    </div> 
                    
                <table class="table table-hover datatable ">
                    <thead class="bg-ligth text-dark">
                        <tr>
                            <th>S.no</th>
                            <th>Challan ID</th>
                            <th>Date</th>
                            <th>Lead</th>
                       
                            <th>Location</th>
                            <th>User</th>
                            <th>Action</th>
                            
                        </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(isset($_GET['getrecord'])){
                                    
                                $stmt=$mysqli->prepare("SELECT a.*,b.name,c.name as loc,d.name as user_name from stock_outward_mst a JOIN lead b on a.lead_id=b.id JOIN locations c on a.loc_id=c.id JOIN admin_users d on a.user_id=d.id  where a.lead_id=? ");
                                $stmt->bind_param('i',$_GET['loc_id']);
                                $stmt->execute();
                                $res=$stmt->get_result();
                                $count=1;
                                while($row=$res->fetch_assoc()){
                                  echo '<tr>
                                  <td scope="row">'.$count++.'</td>';
                                echo '<td>'.$row['id'].'</td>
                                <td>'.$row['created_date'].'</td>
                              
                                    <td>'.$row['name'].'</td>
                                    <td>'.$row['loc'].'  </td>
                                    <td>'.$row['user_name'].'</td>
                                    <td><a class="btn btn-primary btn-sm" href="outward-report?id='.$row['id'].'"><i class="fa fa-eye"></i></a></td>';
     
                                    echo '
                              
                                </tr>';
                                }


                            }

                            ?>
                       
                            
                        </tbody>
                </table>
               </div>
           </div>
       </div>




<?php 
include("../layouts/footer.php");
?>
 

<script>

$(document).ready(function () {
//change selectboxes to selectize mode to be searchable
   $("#loc_id").select2();
   
});
 
  $(document).ready(function() {
    $('#customersTable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'pdf', 'print'
        ]
    } );
});
$("#loc_id").val(<?=$_GET['loc_id']?>)
</script>