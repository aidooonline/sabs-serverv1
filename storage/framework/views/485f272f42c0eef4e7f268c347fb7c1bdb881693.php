<script>

    $(document).ready(function () {
       // dr_getselectedtexter();
        //cr_getselectedtexter();

        $('form').on('submit', function (e) {
            e.preventDefault();
            //rest of code
            if(document.getElementById('myFunctionid').value == 'empty'){
                opensystemdialog(`
                <div class="card bg-danger" style="">
        <div class="card-body">
          <div class="">
            <div class="row text-white">
              
              <div class="pl-0">
                <h5 class="text-white mb-1" style="padding-left:20px;padding-right:20px;padding-top:20px;padding-bottom:20px;">
                    Select Account to Debit or Credit.
                </h5>
                 
              </div>
            </div>
          </div>
        </div>
      </div>
        `);

            }else{
if(document.getElementById('cr_amount').value == ''||document.getElementById('cr_amount').value == ' '){
    opensystemdialog(`
                <div class="card bg-danger" style="">
        <div class="card-body">
          <div class="">
            <div class="row text-white">
              
              <div class="pl-0">
                <h5 class="text-white mb-1" style="padding-left:20px;padding-right:20px;padding-top:20px;padding-bottom:20px;">
                   Enter Amount to Credit or Debit.
                </h5>
                 
              </div>
            </div>
          </div>
        </div>
      </div>
        `);
}else{
    let myselected_ledger = document.getElementById('myselected_ledger').value;
            let myselect_debit_credit = document.getElementById('debitorcredit_id').value;
            let myselected_value = document.getElementById('cr_amount').value;
            let thevalue = calculate_actual_value(myselected_ledger, myselect_debit_credit, myselected_value);
            document.getElementById('actual_value').value = thevalue; 
            document.getElementById("mysubmitform").submit();  
}


           



            }
           
        })


    });

    function changedisplaymode_cr_dr(that) {
        // alert(that.value);
        $('#debit_div').toggle();
        $('#credit_div').toggle();
    }

    function dr_getselectedtexter() {
        let selectedtexter = $("#dr_account option:selected").text();

        $('#dr_name').val(selectedtexter);
    }
    function cr_getselectedtexter() {
        let selectedtexter = $("#cr_account option:selected").text();
        
        $('#cr_name').val(selectedtexter);
    }

    //this function checks the primary account type whether Asset, Expense, Owner Equity, Liability or Revenue and also checks whether it debit or credit to calculate it negative account thus substracting from the account or positive adding to the account.
    function calculate_actual_value(p_accounttype, dr_or_cr, value) {

        switch (p_accounttype) {

            case "Asset":
                if (dr_or_cr == 'Debit') {
                    // still positive.
                    //$('#dr_name').val(selectedtexter);
                    return value;
                } else {
                    // still negative value.
                    return -Math.abs(value);
                }
                break;


            case "Expense":
                if (dr_or_cr == 'Debit') {
                    // still positive.
                    return value;
                } else {
                    // still negative value.
                    return -Math.abs(value);
                }
                break;


            case "Owner Equity":
                if (dr_or_cr == 'Debit') {
                    // still negative value.
                    return -Math.abs(value);

                } else {
                    // still positive.
                    return value;
                }
                break;


            case "Liability":
                if (dr_or_cr == 'Debit') {
                    // still negative value.
                    return -Math.abs(value);

                } else {
                    // still positive.
                    return value;
                }
                break;


            case "Revenue":
                if (dr_or_cr == 'Debit') {
                    // still negative value.
                    return -Math.abs(value);

                } else {
                    // still positive.
                    return value;
                }
                break;


        }

    }
 /* When the user clicks on the button,
    toggle between hiding and showing the dropdown content */
    function myFunction() {
        document.getElementById("myDropdown").classList.toggle("show");
    }

    function filterFunction() {
        var input, filter, ul, li, a, i;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        div = document.getElementById("myDropdown");
        a = div.getElementsByTagName("a");
        for (i = 0; i < a.length; i++) {
            txtValue = a[i].textContent || a[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                a[i].style.display = "";
            } else {
                a[i].style.display = "none";
            }
        }
    }

    function selectme(that){
       // alert(that.id.split('_')[1]);
        $('#selectdbaccountlabel').html(that.innerHTML);
        $('#myFunctionid').val(that.getAttribute("cat"));
        $('#dr_name').val(that.getAttribute("cat"));
        $('#cr_name').val(that.getAttribute("cat"));
        myFunction();
    }





</script><?php /**PATH /home/banqgego/public_html/nobs001/resources/views/ledgergeneralsub/script.blade.php ENDPATH**/ ?>