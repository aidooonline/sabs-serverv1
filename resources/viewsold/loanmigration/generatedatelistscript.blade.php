<script type="text/javascript">


    let valudationchecking = false;

    $(document).ready(function () {
      replaceimages();
      insertinterestinto('interest-layer');
     // insert_total_tobepaid('tobe-paid-layer');
     // calculateinterest();
        $('form').submit(function (e) {
            e.preventDefault();
            valudationchecking = true;
            checkvalidation(e);
            //e.currentTarget.submit();
        });
        generate_payment_duration_clicks('Daily');
        $('#loanscheduleid').change(function(){
        
        let paymentdurationselect = $("#loanscheduleid option:selected" ).text();
        generate_payment_duration_clicks(paymentdurationselect);
    
    ;
    })

    });


function calculate_cash_collateral(){
let cash_collateral = (10/100) * $('#approved_amount').val()
$('#cash_collateral').val(cash_collateral.toFixed(3));
}

function calculate_processing_fee(){
    let processing_fee = (5/100) * $('#approved_amount').val()
$('#processing_fee').val(processing_fee.toFixed(2));
}

function insertinterestinto(tobeinserted){
    let interest_tofixed = calculateinterest();

    document.getElementById(tobeinserted).innerHTML = interest_tofixed.toFixed(2);
    insert_total_tobepaid('tobe-paid-layer');
    $('#payment_scheduling_list_ul').html('');
}

function generate_payment_duration_clicks(payment_scheduling){
    
switch (payment_scheduling){
   
    case 'Daily':
     $('#selected_numberofdays_list').html(`
     <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">reset</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+1</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+3</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+7</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+14</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+30</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+90</a> 
     `);

    break;

    case 'Weekly':
    $('#selected_numberofdays_list').html(`
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">reset</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+7</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+14</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+28</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+35</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+42</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+49</a>  
     `);
    break;

    case 'Fortnight':
  
    $('#selected_numberofdays_list').html(`
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">reset</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+21</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+42</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+63</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+84</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+105</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+126</a>  
     `);
    break;

    case 'Monthly':
    $('#selected_numberofdays_list').html(`
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">reset</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+30</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+90</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+120</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+150</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+180</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+360</a>  
     `);
    break;

    case 'Quarterly':
    $('#selected_numberofdays_list').html(`
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">reset</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+90</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+180</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+270</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+360</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+450</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+540</a>  
     `);
    break;

    case 'Semi-Annually':
    $('#selected_numberofdays_list').html(`
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">reset</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+180</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+360</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+540</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+720</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+900</a>
        <a href="#" onclick="add_payent_duration_step(this)" class="card btn btn-outline-secondary">+1080</a>  
     `);
    break;

}
}

function percentage(partialValue, totalValue) {
   return (partialValue / 100 ) * totalValue;
} 

function update_cashcollateral_processingi_fee_fields(){
    
}


function add_payent_duration_step(that,duration){
    let tobeadded = that.innerHTML.split('+')[1];
    change_input_step(tobeadded, 'payment_duration');
    change_input_value(tobeadded, 'payment_duration', 'value');
    change_input_value(tobeadded, 'payment_duration', 'min');
    $('#range_weight_disp').val(tobeadded);
    
    setTimeout(insertinterestinto('interest-layer'), 400);
}


function insert_total_tobepaid(tobepaidid){
    let interest_tofixed = calculateinterest();
    interest_tofixed = interest_tofixed;
    let principal = parseFloat($('#principal-layer').html());
    // alert(principal);
    let tobe_paid = parseFloat(principal - interest_tofixed);
    tobe_paid += interest_tofixed + interest_tofixed;
    tobe_paid = tobe_paid.toFixed(3);
    //alert(tobe_paid);
    document.getElementById(tobepaidid).innerHTML = tobe_paid;
}

function calculateinterest(){
   let payment_duration = $('#payment_duration').val();
   let approvedamountval = $('#approved_amount').val();
  // alert('payment duration = ' + payment_duration);
   let interest_per_anum_hidden = $('#interest_per_anum_hidden').val();
   let interest_per_day_percent = (interest_per_anum_hidden / 365);

   let interest_per_selected_period = (approvedamountval * interest_per_day_percent * payment_duration)/ 100;

   return interest_per_selected_period;
}


    function sendgenerated_paymentschedules(e){
      let payment_schedule = '[';
     //loanaccountid,customeraccountid,to_be_paid,setdate
     let attributeslist = $('#payment_scheduling_list_ul li');

     let PSsplittered = '';
     

     for(i=0;i<attributeslist.length;i++){
       let myattrib = attributeslist[i].getAttribute('scheduledata');
       PSsplittered = myattrib.split('___');
       payment_schedule += '[' + returnphp_arraystyle('loan_account_id',PSsplittered[0],false) +'],' + '[' + returnphp_arraystyle('customer_account_id',PSsplittered[1],false) +'],'  + '[' + returnphp_arraystyle('amount',PSsplittered[2],false) +'],' + '[' + returnphp_arraystyle('date_to_be_paid',PSsplittered[3],true) +'],';
        
     }

     payment_schedule += ']';
     //alert(payment_schedule);
     
     e.currentTarget.submit();

    }
      /*[['user_id'=>'Coder 1', 'subject_id'=> 4096],['user_id'=>'Coder 2', 'subject_id'=> 2048]];*/


      function returnphp_arraystyle(key,value,isstring){
        let tobereturned ='';
        
        if(isstring){
          tobereturned = '"' + key + '"' +'=>'+'"'+ value + '"';
        }else{
          tobereturned = '"' + key + '"' +'=>'+ value;
        }

        return tobereturned;
      }
    

    

function formatDate(date) {
  return new Date(date);
}

    function checkvalidation(e) {

        let approvedamount = $('#approved_amount').val();
        let processing_fee = $('#processing_fee').val();
        let minDate = $('#payment_start_at_text').val();
        let endDate = $('#payment_ends_at_text').val();

        let paymentduration = $('#payment_duration').val();

        if (minDate == '' || minDate == ' ') {
            opensystemdialog('<p class="text-primary" style="font-size:17px;">Enter Begining Payment Date</p>');
        } else {

            if (approvedamount == '' || approvedamount == ' ') {
                getnavigation('customersinfo');
                opensystemdialog('<p class="text-primary" style="font-size:17px;">Enter Approved Payment</p>');
                document.getElementById("approved_amount").focus();
            } else {
                if (processing_fee == '' || processing_fee == ' ') {
                    getnavigation('customersinfo');
                    opensystemdialog('<p class="text-primary" style="font-size:17px;">Enter Processing Fee</p>');
                    document.getElementById("processing_fee").focus();
                } else {


                    let checkingpayment_generator_list = $('#payment_scheduling_list_ul tr');
                    if (checkingpayment_generator_list.length == 0) {
                        opensystemdialog('<p class="text-primary" style="font-size:17px;">Generate Payment Schedule</p>');
                    } else {
                      sendgenerated_paymentschedules(e);
                       // e.currentTarget.submit();
                    }


                }
            }
        }
    }


    function generate_loan_schedule_list() {
       
       let interestcalculated = calculateinterest();
       let interest_per_period = interestcalculated / $('#payment_duration').val();

        let list = '';

        let approvedamount = $('#approved_amount').val();
        let processing_fee = $('#processing_fee').val();

        let minDate = $('#payment_start_at_text').val();
        let endDate = $('#payment_ends_at_text').val();
        let loanaccountid = $('#loan_account_id').val();
        let customeraccountid = $('#customer_account_id').val();
        let customer_account_number = $('#customer_account_number').val();

        let paymentduration = $('#payment_duration').val();

        if (minDate == '' || minDate == ' ') {
            opensystemdialog('<p class="text-primary" style="font-size:17px;">Enter Begining Payment Date</p>');
        } else {



            if (approvedamount == '' || approvedamount == ' ') {
                getnavigation('customersinfo');
                opensystemdialog('<p class="text-primary" style="font-size:17px;">Enter Approved Payment</p>');
                document.getElementById("approved_amount").focus();
            } else {
                if (processing_fee == '' || processing_fee == ' ') {
                    getnavigation('customersinfo');
                    opensystemdialog('<p class="text-primary" style="font-size:17px;">Enter Processing Fee</p>');
                    document.getElementById("processing_fee").focus();
                } else {


                    //PS = Payment Schedule Type
                    let PS = get_selected_payment_scheduling();

                    //BA = Begining Payment Date;
                    let BPD = new Date($('#payment_start_at_text').val());

                    // PD = Payment Duration
                    let PD = $('#payment_duration').val();

                    //GET the calculated  Payment Date List: PDL;
                    let PDL = '';
                    let PDL_count = (PD / PS);

                    //FORMAT DATE
                    let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

                    //Amount to be paid on schedule
                    let to_be_paid = ($('#tobe-paid-layer').html())/PDL_count;

                    let data = ``;

                    //insert number of count to disp_days_count2 to show as number of days to pay.
                    if(PDL_count > 1){
                        $('#disp_days_count2').html("(" + PDL_count + " times)")
                    }

                    if(PDL_count == 1){
                        $('#disp_days_count2').html("(" + PDL_count + " time)")
                    }
                   
                  //  generate_progress();
                    
 
                    for (i = 1; i <= PDL_count; i++) {
                         

                        if (PDL_count == 1) {
                         // `amount``date_to_be_paid``loan_account_id``customer_account_id``loan_migration_id`

                        
                         

                            let setdate = new Date(BPD.setDate(BPD.getDate()));
                           

                            PDL = '<tr scheduledata="' + loanaccountid +'___' + customeraccountid + '___' + to_be_paid.toFixed(3) + '___' + setdate +' " class="card row col-12"><td> <span style="color:#888;">(' + i + ') </span><span>' + BPD.toLocaleDateString("en-UK", options) + '</span></td> <td class="moneyclass"> ' + format_currency2(to_be_paid) + '</td></tr>';
                            $('#payment_scheduling_list_ul').html(PDL);

                            setdate = formatDate2(setdate);
 
                            data += to_be_paid.toFixed(3) + `__` + setdate +`__` + loanaccountid +`__`+ customeraccountid + `__`+ loanmigrationid;

                           

 
                        } else {


                            if (i == 1) {
                                let setdate = new Date(BPD.setDate(BPD.getDate()));

                               

                                PDL += '<tr scheduledata="' + loanaccountid +'___' + customeraccountid + '___' + to_be_paid.toFixed(3) + '___' + setdate +' " class="card row col-12"><td> <span style="color:#888;">(' + i + ') </span><span>' + BPD.toLocaleDateString("en-UK", options) + ' </span></td><td><span class="moneyclass"> ' + format_currency2(to_be_paid) + '</span></td></tr>';
                                $('#payment_scheduling_list_ul').html(PDL);

                                setdate = formatDate2(setdate);

                                data += to_be_paid.toFixed(3) + `__` + setdate +`__` + loanaccountid +`__`+ customeraccountid + `__`+ loanmigrationid;

                            } else {

                                let setdate = new Date(BPD.setDate(BPD.getDate() + PS)); 

                                PDL += '<tr scheduledata="' + loanaccountid +'___' + customeraccountid + '___' + to_be_paid.toFixed(3) + '___' + setdate +' " class="card row col-12"><td> <span style="color:#888;">(' + i + ') </span><span>' + setdate.toLocaleDateString("en-UK", options) + '</span></td><td> <span class="moneyclass"> ' + format_currency2(to_be_paid) + '</span>  </td></tr>';
                                $('#payment_scheduling_list_ul').html(PDL);

                                setdate = formatDate2(setdate);

                                data += `_____`+ to_be_paid.toFixed(3) + `__` + setdate +`__` + loanaccountid +`__`+ customeraccountid + `__`+ loanmigrationid;

                            }  

                        }


                    }

                    data += ``;                  
                    $('#loan_schedule_data').val(data);
                  //  alert(data);
                    


                }

            }




        }


    }



var i = 0;
function generate_progress() {
$('#myProgress').show(200);
  if (i == 0) {
    i = 1;
    var elem = document.getElementById("myBar");
    var width = 1;
    var id = setInterval(frame, 10);
    function frame() {
      if (width >= 100) {
        clearInterval(id);
        i = 0;
        
      } else {
        width++;
        elem.style.width = width + "%";
      }
    }
  }
}

    function changeApprovedAmount() {
        var x = document.getElementById("amountapproveddynamic");
        x.innerHTML = 'Approved Amt: <strong style="padding-left:7px;"> ' + $('#approved_amount').val() + '</strong>';
        $('#principal-layer').html($('#approved_amount').val());
        insertinterestinto('interest-layer');
    }



    function change_input_step(step, inputstepid) {
        $('#' + inputstepid).attr('step', step);

    }

    function change_input_value(value, inputstepid, attri) {
        $('#' + inputstepid).attr(attri, value);
        $('#' + inputstepid).val(value);
    }

    function set_loan_schedule_value() {

        let selected_schedule = $("#loanscheduleid option:selected").text();

        switch (selected_schedule) {
            case 'Daily':
                change_input_step(1, 'payment_duration');
                change_input_value(1, 'payment_duration', 'value');
                change_input_value(1, 'payment_duration', 'min');
                $('#range_weight_disp').val(1);
                insertinterestinto('interest-layer');


                break;

            case 'Weekly':
                change_input_step(7, 'payment_duration');
                change_input_value(7, 'payment_duration', 'value');
                change_input_value(7, 'payment_duration', 'min');
                $('#range_weight_disp').val(7);
                insertinterestinto('interest-layer');
                break;

            case 'Fortnight':
                change_input_step(14, 'payment_duration');
                change_input_value(14, 'payment_duration', 'value');
                change_input_value(14, 'payment_duration', 'min');
                $('#range_weight_disp').val(14);
                insertinterestinto('interest-layer');
                break;

            case 'Monthly':
                change_input_step(30, 'payment_duration');
                change_input_value(30, 'payment_duration', 'value');
                change_input_value(30, 'payment_duration', 'min');
                $('#range_weight_disp').val(30);
                insertinterestinto('interest-layer');
                break;

            case 'Quarterly':
                change_input_step(90, 'payment_duration');
                change_input_value(90, 'payment_duration', 'value');
                change_input_value(90, 'payment_duration', 'min');
                $('#range_weight_disp').val(90);
                insertinterestinto('interest-layer');
                break;

            case 'Semi-Annually':
                change_input_step(180, 'payment_duration');
                change_input_value(180, 'payment_duration', 'value');
                change_input_value(180, 'payment_duration', 'min');
                $('#range_weight_disp').val(180);
                insertinterestinto('interest-layer');
                break;

        }

        


    }



    function get_selected_payment_scheduling() {

        let selected_schedule2 = $("#loanscheduleid option:selected").text();
 
        switch (selected_schedule2) {
            case 'Daily':
                return 1;

                break;

            case 'Weekly':
                return 7;
                break;

            case 'Fortnight':
                return 14;
                break;

            case 'Monthly':
                return 30;
                break;

            case 'Quarterly':
                return 30;
                break;

            case 'Semi-Annually':
                return 180;
                break;

        }

    }

    function formatDate2(date1) {
  return date1.getFullYear() + '-' +
    (date1.getMonth() < 9 ? '0' : '') + (date1.getMonth()+1) + '-' +
    (date1.getDate() < 10 ? '0' : '') + date1.getDate();
}


    function format_currency2(money) {
        // Create our number formatter.
        var formatter = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'GHS',

            // These options are needed to round to whole numbers if that's what you want.
            //minimumFractionDigits: 0, // (this suffices for whole numbers, but will print 2500.10 as $2,500.1)
            //maximumFractionDigits: 0, // (causes 2500.99 to be printed as $2,501)
        });

        return formatter.format(money); /* $2,500.00 */
    }
</script>