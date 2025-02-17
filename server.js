
function gotowhatsapp() {
    
    var Product_Name = document.getElementById("productname").value;
    var Product_Colour = document.getElementById("productcolour").value;
    var Product_Quality = document.getElementById("productquality").value;
    var Product_Quantity = document.getElementById("productquantity").value;
    var Product_Weight = document.getElementById("productweight").value;
    var Product_Dimensions = document.getElementById("productdimension").value;
    var Product_Investigation_Deadline = document.getElementById("dateofinvestigation").value;
    var investigator_latitude = document.getElementById("latitude").value;
    var investigator_longitude = document.getElementById("longitude").value;
    var Product_Investigator_Name = document.getElementById("investigatorname").value;
    var Admin = document.getElementById("adminname").value;
    var investigatornumber =  document.getElementById("investigatornum").value;
    

    var url = `https://wa.me/${investigatornumber}?text=`
    + "Available Inspection - " + "%0a"
    + "Click to go to the Inspection App: " + "%0a"
    + encodeURIComponent("https://www.google.com") + "%0a"
    + "Regards," + "%0a"
    + Admin + "%0a"
    + "Admin " ;

    window.open(url, '_blank').focus();
}
