<?php
include("header.php");

?>
<div class="category-area">
    <header class="header header-fixed">
        <div class="container">
            <div class="header-content">
                <div class="left-content">
                    <a href="javascript:void(0);" class="back-btn">
                        <i class="icon feather icon-chevron-left"></i>
                    </a>
                    <h6 class="title">Back</h6>
                </div>
                <div class="mid-content">
                </div>

            </div>
        </div>
    </header>

    <div class="title-bar">
        <h6 class="title mb-0">Category</h6>
        <a href="/">View all <i class="icon feather icon-chevron-right"></i></a>
    </div>

    <ul class="row g-3 mb-5">

        <?php

        $stmt = $mysqli->prepare("SELECT a.*,b.name as category,b.image as image FROM brand a  join categories b on a.id=b.brand_id  where a.id in (" . $user['division'] . ")");
        $stmt->execute();
        $category = $stmt->get_result();
        while ($row = $category->fetch_assoc()) { 
            $img="";
            if(!empty($row['image'])){
                $img='  <img src="../admin/'.$row['image'].'">';
            }else{
                $img='<i style="font-size: 20px; margin-left: 10px;" class="fa-solid fa-briefcase-medical"></i>';
            }
            ?>


            <li class="category-item col-3">
                <a href="shop?search=<?= $row['category'] ?>">
                    <div class="media media-40">
                       <?= $img ?>
                    </div>
                    <span><?= $row['category'] ?></span>
                </a>
            </li>
        <?php }
        ?>

    </ul>
</div>

<?php
include("footer.php");
?>