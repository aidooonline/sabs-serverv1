<script type="text/javascript">


    function getbuttons(buttons, modalid) {

        $('#modalbody').html('');

        var mybuttons = buttons.split('___');
        for (i = 0; i < mybuttons.length; i++) {
            var i_splitter = mybuttons[i].split('***');
            var mybutton = document.createElement('a');
            mybutton.setAttribute('href', i_splitter[1]);
            mybutton.setAttribute('class', 'btn modal-btn-purple');
 
            mybutton.innerHTML = i_splitter[0];
            $('#modalbody').append(mybutton);
        }

    }

function opensystemdialog(msg){
    
      $('#modalbody').html(msg);

      $('#exampleModal4').modal('show');
}



</script>


<script>
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function () {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }


    $(document).ready(function () {
       replaceimages();
    });


    function replaceimages(){
        $(".profilepic").each(function (index) {
 
 try {
     var myimage = $(this).attr('profilevalue');


     var myimagesplit = myimage.split('users')[1].split('.')[0];
     var preparedimage = myimagesplit + '.jpg';
     var percentagesplitter = preparedimage.split('%2F');
     var firstreplace = '%252F';

     for (i = 0; i < percentagesplitter.length; i++) {
         if (i > 0) {
             if (i == percentagesplitter.length - 1) {
                 firstreplace += percentagesplitter[i];
             } else {
                 firstreplace += percentagesplitter[i] + '%252F';
             }

         }
     }
     this.src = "<?php echo e(env('NOBS_IMAGES')); ?>useraccounts/users" + firstreplace;
 }
 catch (err) {
     if($(this).attr('is_dataimage') == '1'){
        this.src = $(this).attr('profilevalue');
     }else{
        this.src = "<?php echo e(env('NOBS_IMAGES')); ?>useraccounts/profileimage.png";
     }
    
 }

 
});
    }

 </script>

  <?php /**PATH /home/banqgego/public_html/nobsback/resources/views/layouts/modalscripts.blade.php ENDPATH**/ ?>