$(document).ready(function(){
  var shop_name = "{{ shop.permanent_domain }}";
  var unavailableDates = [];
  var unavailableDays = [];
  var start_from = '';
  var allowed_month = '';
  var date_formate = '';
  $.ajax({
		url: "https://zestardshop.com/shopifyapp/bulkorder/public/getconfig",
		dataType: "json",
    	data:{shop:shop_name},
		success: function(data) {
          //console.log(data[0].admin_order_note);
          var app_status = data[0].app_status;
          if(app_status == "Deactive")
          {
            jQuery("#datepicker_box").remove();
          }
          var date_label = data[0].datepicker_label;
          jQuery('label[for=date]').text(date_label);
          var dates = data[0].block_date;
		  var day = data[0].days;
		  start_from = '+'+data[0].date_interval;
		  allowed_month = '+'+data[0].alloved_month+'M';
		  unavailableDates = $.parseJSON(dates);
		  unavailableDays = $.parseJSON(day);
          date_formate = data[0].date_format;
          //console.log(date_formate);
          var selected_hours = data[0].hours;
          var selected_minute = data[0].minute;
          var current_date = "{{ 'now' | date: "%d" }}";
          
          var hour = "{{ "now" | date: "%H" }}";
          var minute = "{{ "now" | date: "%M" }}";
          //console.log(hour+":"+minute);
          
          //for display date format
          if(date_formate == "mm/dd/yy"){
            var display_format = "(mm/dd/yyyy)";
          }
          else if(date_formate == "yy/mm/dd")
          {
            var display_format = "(yyyy/mm/dd)";
          }
          else if(date_formate == "dd/mm/yy")
          {
            var display_format = "(dd/mm/yyyy)";
          }
          else
          {
            var display_format = "(mm/dd/yyyy)";
          }
          if(display_format != "")
          {
            $("#selected_format").text(display_format);
          }
          else
          {
            $("#selected_format").text('mm/dd/yyyy');
          }
          
          //For Display notes message
          var notes_admin = data[0].admin_order_note;
          if(notes_admin != '')
          {
            $("#admin_notes").text(notes_admin);
          }
          //for cut off time
          if(selected_hours > 0 || selected_minute > 0)
          {
            if( parseInt(hour) >= parseInt(selected_hours)  && parseInt(minute) > parseInt(selected_minute) )
            {
              var str = parseInt(data[0].date_interval)+1;
              start_from = '+'+parseInt(str);
            }
            else{
              start_from = '+'+parseInt(data[0].date_interval);
              
            }
          }
          else{
            
          }
		}
	  });
setTimeout(function(){
		var days = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];

		function unavailable(date) {
			ymd = date.getFullYear() + "/" + ("0"+(date.getMonth()+1)).slice(-2) + "/" + ("0"+date.getDate()).slice(-2);
			day = new Date(ymd).getDay();
			if ($.inArray(ymd, unavailableDates) < 0 && $.inArray(days[day], unavailableDays) < 0) {
				return [true, "enabled", "Book Now"];
			} else {
				return [false,"disabled","Booked Out"];
			}
		}
  setTimeout(function(){
    jQuery("#date").datepicker( { 
      		dateFormat: date_formate ,
			minDate: start_from, 
			maxDate: "'"+allowed_month+"'",
			beforeShowDay: unavailable
			});
  }, 300)
        
	}, 3000);
});