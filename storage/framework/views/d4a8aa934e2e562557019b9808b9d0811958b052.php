<?php
    $logo=asset(Storage::url('uploads/logo/'));
    $company_logo=Utility::getValByName('company_logo');
    $users=\Auth::user();
    $currantLang = $users->currentLanguage();
?>

 
<script type="text/javascript">
  function showhidediv(divid){
 $('#'+ divid).toggle();
  }
</script>
   
<style>
    
.menunavbar {
  overflow: hidden;
  background-color: #333;
  position: fixed;
  bottom: 0;
  width: 100%;
  z-index:1;

}

.menunavbar a {
  float: left;
  display: block;
  color: #f2f2f2;
  text-align: center;
  padding: 8px 1%;
  text-decoration: none;
  font-size: 17px;
  width:32%;
}

.menunavbar a:hover {
  background: #f1f1f1;
  color: black;
}

.menunavbar a.active {
  background-color: #AA336A;
   color: black;
}
</style>



<div class="menunavbar">
  <div id="loadingdiv" style="z-index:1;position:fixed;left:45%;top:45%;display:none;">
    <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
      <span class="sr-only">Loading...</span>
    </div>
    
  </div>
  <a href="<?php echo e(env('APP_URL')); ?>" class="active">
      <i class="fas fa-home text-light" aria-hidden="true"></i>
   
  </a>
  <a href="<?php echo e(env('BASE_URL')); ?>accounts/">
       <i class="fas fa-users text-light" aria-hidden="true"></i>
      </a>
  

      <a href="<?php echo e(route('logout')); ?>"
      onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">

      <i class="fas fa-sign-out-alt text-light"></i>
  </a>
</div>

<?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/partials/admin/menu.blade.php ENDPATH**/ ?>