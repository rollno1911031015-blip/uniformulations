<!-- rts footer one area start -->
<div class="rts-footer-area pt--80 bg_light-1">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="footer-main-content-wrapper pb--70 pb_sm--30">
                    <!-- single footer area wrapper -->
                    <div class="single-footer-wized">
                        <h3 class="footer-title">About Company</h3>
                        <div class="call-area">
                            <div class="icon">
                                <i class="fa-solid fa-phone-rotary"></i>
                            </div>
                            <div class="info">
                                <h3>DM PHARMA (GLOBAL) PRIVATE LIMITED</h3>
                                <p>Office : SCO-176, 2ND Floor, Sector-38c Chandigarh
                                    <br>
                                    Godown Khewat no 313, <br> Khatuni No 441 Khasra No 30/27/1, Village Maloya CHD <br>
                                    Phone : 7717304942

                                </p>
                            </div>
                        </div>

                    </div>
              
                </div>
             
            </div>
        </div>
    </div>
</div>
<!-- rts footer one area end -->

<!-- rts copyright-area start -->
<div class="rts-copyright-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="copyright-between-1">
                    <p class="disc">
                        Copyright <?=date("Y")?> <a href="#">©DM PHARMA</a>. All rights reserved.
                    </p>
                  
                </div>
            </div>
        </div>
    </div>
</div>
<!-- rts copyright-area end -->

<form method="GET" class="search-header" action="index">
<div class="search-input-area">

    <div class="container">
        <div class="search-input-inner">
            <div class="input-div">
                
         
                <input id="search" name="search"   value="<?php if (!empty($_GET['search'])) echo $_GET['search'] ?>" class="search-input" type="text" placeholder="Search by keyword or #">
                <button type="submit"><i class="far fa-search"></i></button>
          
            </div>
        </div>
    </div>

    <div id="close" class="search-close-icon"><i class="far fa-times"></i></div>
</div>
</form>


<div id="anywhere-home" class="anywere"></div>
<!-- progress area start -->
<div class="progress-wrap">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
        <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;"></path>
    </svg>
</div>
<!-- progress area end -->
<script defer src="assets/js/plugins.js"></script>


<script defer src="assets/js/main.js"></script>

<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
</script>
<?php toaster(); ?>