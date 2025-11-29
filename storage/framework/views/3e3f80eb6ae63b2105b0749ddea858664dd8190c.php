<style type="text/css">


::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
  color:purple !important;
  opacity: 1; /* Firefox */
}

:-ms-input-placeholder { /* Internet Explorer 10-11 */
  color:purple !important;
}

::-ms-input-placeholder { /* Microsoft Edge */
  color:purple !important;
}


    .icondiv {
        width: 96px;
        height: 96px;
        margin: 7px 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;

    }

    @media  only screen and (max-width: 500px) {
        .icondiv {
            width: 29%;
            height: 96px;
            margin: 7px 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;

        }
    }

.panel{
    padding-left:0;padding-right:0;
}

    .listdiv{
        
       
            height: 96px;
            margin:5px 2%;
            pading-left:0 !important;padding-right:0 !important;
    }

    .listdiv a{
        padding:5px 8px;
    }



    .listdiv2{
        width: 96%;
            height: auto;
            margin:5px 2%;
             padding:0 0;
    }

    .listdiv2 a{
        padding:5px 8px;
    }


    
    .search{
        border-radius:4px 4px 4px 4px !important;
        color:purple;
        border-color:purple;
        width:100%;
        height:40px;
    }
    body {
        background-color: #5c1659 !important;
    }


    .icondiv span {
        font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        font-size: 13px;

    }


    .accordion {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  padding: 18px;
   
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  transition: 0.4s;
}

.active, .accordion:hover {
  background-color: #ccc;
}

.panel {
  padding: 0 18px;
  background-color: white;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.2s ease-out;
}




    .dashboardtext,
    .dashboardtext a span {
        font-color: purple !important;
        color: purple;
    }

    a i[class="purple"] {
        color: purple !importantp;
    }

    .footer {
        display: none !important;
    }


    #modalbody {
        height: 100% !important;
    }

    .btn-purple {
        color: #FFF;
        background-color: purple;
        border-color: #f7b5f2;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15);
    }

    .modal-btn-purple {
        color: #FFF;
        background-color: purple;
        border-color: #f7b5f2;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.15);
        height: 75px;
        width: 150px;
    }


    @media (max-width: 576px) {
        .modal-dialog.modal-dialog-slideout {
            width: 97%;
            position: fixed;
            bottom: 0;
        }
    }

    .modal-dialog-slideout {
        min-height: 40%;
        margin: 0 auto 0 0;
        background: #fff;
    }

    .modal.fade .modal-dialog.modal-dialog-slideout {
        -webkit-transform: translate(-100%, 0);
        transform: translate(-100%, 0);
    }

    .modal.fade.show .modal-dialog.modal-dialog-slideout {
        -webkit-transform: translate(0, 0);
        transform: translate(0, 0);
        flex-flow: column;
    }

    .modal-dialog-slideout .modal-content {
        border: 0;
    }
</style><?php /**PATH /Applications/MAMP/htdocs/nobsbackend/resources/views/layouts/inlinecss.blade.php ENDPATH**/ ?>