function copy_text()
{
  $(this).CopyToClipboard();
  alert('Shortcode Copied');
}

function copy_script()
{
    var script = document.getElementById('script_code').value;
    var script = prompt("Copy this code", script);
   // var txt = $(this).val;
    //var txt = $("#script_code").text();
   // var script = txt.replace(/\s/g, '');
    
  //script.CopyToClipboard();
  //alert(txt);
}
