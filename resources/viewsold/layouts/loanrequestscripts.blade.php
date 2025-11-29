
<script>
    $(function () {
 
        checkrating();
       // autocinsertdata();
    });

    function autocinsertdata(){
        let loantypedraft = document.getElementById('loantypedraft');
        let loanpurposeid = document.getElementById('loanpurposeid');

        let selectedtext = loantypedraft.options[loantypedraft.selectedIndex].text.trim('');
        let selectedvalue = loantypedraft.options[loantypedraft.selectedIndex].value;
        
        let selectedtext2 = loanpurposeid.options[loanpurposeid.selectedIndex].text.trim('');
        
        /* $('#loanid').val(selectedvalue);
        $('#loannameid').val(selectedtext);
        $('#loanpurposeid').val(selectedtext2); */
        
    }


    function getnavigation(that) {
        switch (that) {
            case 'loaninfo':
                $('#tab1').hide();
                $('#tab1btn').hide();
                $('#tab3').hide();
                $('#tab3btn').hide();
                $('#tab2').show();
                $('#tab2btn').show();

                break;

            case 'customersinfo':
                $('#tab1').show();
                $('#tab1btn').show();
                $('#tab3').hide();
                $('#tab3btn').hide();
                $('#tab2').hide();
                $('#tab2btn').hide();

                break;

            case 'guarantorsinfo':
                $('#tab1').hide();
                $('#tab1btn').show();
                $('#tab3').show();
                $('#tab3btn').show();
                $('#tab2').hide();
                $('#tab2btn').hide();
                break;


        }
    }


    function checkrating2(that) {

        switch (that) {
            case '0':
                document.getElementById("loan_request_rating").value = 0;
                break;
            case '1':
                document.getElementById("loan_request_rating").value = 1;
                break;
            case '2':

                document.getElementById("loan_request_rating").value = 2;
                break;

            case '3':

                document.getElementById("loan_request_rating").value = 3;
                break;
            case '4':

                document.getElementById("loan_request_rating").value = 4;
                break;
            case '5':

                document.getElementById("loan_request_rating").value = 5;
                break;

        }
    }

    function checkrating() {


        switch (document.getElementById("loan_request_rating").value) {
            case '0':

                break;
            case '1':
                document.getElementById("star-1").setAttribute('checked', 'true');

                break;
            case '2':
                document.getElementById("star-2").setAttribute('checked', 'true');

                break;

            case '3':
                document.getElementById("star-3").setAttribute('checked', 'true');

                break;
            case '4':
                document.getElementById("star-4").setAttribute('checked', 'true');

                break;
            case '5':
                document.getElementById("star-5").setAttribute('checked', 'true');

                break;


        }

    }


    function getselectedtext(sel) {
        let selectedtext = sel.options[sel.selectedIndex].text.trim('');

        if (selectedtext == 'Other') {
            $('#otherdiv').show(300);
        } else {
            $('#otherdiv').hide(300);
        }
    }


    function getselecteddate(e,id){
      
     $('#' +id).val(e.target.value);
    }
    

    function getselectedloantext(that){
        let selectedtext = that.options[that.selectedIndex].text.trim('');
        let selectedvalue = that.options[that.selectedIndex].value;
        $('#loanid').val(selectedvalue);
        $('#loannameid').val(selectedtext);

    }
    
    function getselectedloanpurpose(that){
        let selectedtext = that.options[that.selectedIndex].text.trim('');
        $('#loanpurposeid').val(selectedtext);
    }

    
</script>
<style>

.date1{
    width:85%;
}
.date1,.date2{
    float:left;clear:right;
}
.date{
    width:10%;
    padding:0
}
    #tab1btn,#tab2btn,#tab3btn{
position:fixed;left:4px;bottom:40px;z-index:999999;
background-color:#ffffff;width:98%;border-radius:8px;
padding-top:10px;padding-bottom:10px;max-height: 60px;
background-color: rgba(65, 15, 65, 0.4);
    }

    div.stars {
        width: 100%;
        display: inline-block;
    }

    input.star {
        display: none;
    }

    label.star {
        float: right;
        padding: 10px;
        font-size: 25px;
        color: #444;
        transition: all .2s;
    }

    input.star:checked~label.star:before {
        content: '\f005';
        color: purple;
        transition: all .25s;
    }

    input.star-5:checked~label.star:before {
        color: purple;
        text-shadow: 0 0 20px rgb(245, 234, 226);
    }

    input.star-1:checked~label.star:before {
        color: #F62;
    }

    label.star:hover {
        transform: rotate(-15deg) scale(1.3);
    }

    label.star:before {
        content: '\f006';
        font-family: FontAwesome;
    }


    table td[class='mintd'] {
        padding: 5px 25px !important;
    }

    .account_name {
        color: #666666;
        text-align: left !important;
        font-weight: bold;
        font-family: verdana;
    }

    .table-panel td {
        font-size: 1em !important;
        color: rgb(65, 6, 65);
        font-family: Verdana, Geneva, Tahoma, sans-serif;
    }

    .accordion img {
        width: 65px;
        height: 65px;
    }

    .listdiv {
        width: 25%;
        height: 120px;


    }

    .listdiv .listdiv .image {
        width: 25%;
        height: 70px;


    }

    .listdiv .listdiv img {
        width: 70px;
        height: 70px;
    }

    .listdiv .listdiv .text {
        width: 75%;
        height: 70px;
        background-color: green;
    }

    .listdiv .listdiv .text a,
    .listdiv .listdiv .text span {
        float: left;
        color: purple;
    }


    .listdiv2 {
        height: auto !important;
        height: 600px;
    }

    .listdiv2 .listdiv2 .image {
        width: 25%;
        height: auto;

    }

    .listdiv2 .listdiv2 img {
        width: 70px;
        height: 70px;
    }

    .listdiv2 .listdiv2 .text {
        width: 75%;
        height: 70px;
        background-color: green;
    }

    .listdiv2 .listdiv2 .text a,
    .listdiv2 .listdiv2 .text span {
        float: left;
        color: purple;
    }

    .listdiv2 label {
        color: purple !important;
    }

    /*Profile Pic Start*/
    .picture-container {
        position: relative;
        cursor: pointer;
        text-align: center;
    }

    .picture {
        width: 106px;
        height: 106px;
        background-color: #999999;
        border: 4px solid #CCCCCC;
        color: #FFFFFF;
        border-radius: 50%;
        margin: 0px auto;
        overflow: hidden;
        transition: all 0.2s;
        -webkit-transition: all 0.2s;
    }

    .picture:hover {
        border-color: #2ca8ff;
    }

    .content.ct-wizard-green .picture:hover {
        border-color: #05ae0e;
    }

    .content.ct-wizard-blue .picture:hover {
        border-color: #3472f7;
    }

    .content.ct-wizard-orange .picture:hover {
        border-color: #ff9500;
    }

    .content.ct-wizard-red .picture:hover {
        border-color: #ff3b30;
    }

    .picture input[type="file"] {
        cursor: pointer;
        display: block;
        height: 100%;
        left: 0;
        opacity: 0 !important;
        position: absolute;
        top: 0;
        width: 100%;
    }

    .picture-src {
        width: 100%;

    }

    /*Profile Pic End*/
</style>