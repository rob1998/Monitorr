<?php
include('functions.php');
?>
<html>
<head>
	<script src="../js/jquery.min.js"></script>
</head>
<body>
<div id="data"></div>
<script>
    function createPluginList(){
        $.ajax({
            type: "GET",
            url: "/api/?v1/getPlugins",
            dataType: "json",
            success: function(response){
                if(response.data.length == 0){
                    $("#data").html("No plugins found");
                } else {
                    for(let i = 0; i<response.data.length; i++) {
                        let $plugin = response.data[i];
                        let $imgUrl = "assets/plugins/" + $plugin.name + "/" + $plugin.image;
                        let $html = "";
                        $html += "<div class='plugin-box'>";
                        $html +=    "<img src='" + $imgUrl + "'>";
                        $html +=    "<h3>" + $plugin.name + "</h3>";
                        $html +=    "<div class='plugin-box-overlay'>";
                        $html +=        "<a class='btn'><i class='icon'></i></a>";
                        $html +=        "<a class='btn'><i class='icon'></i></a>";
                        $html +=    "</div>";
                        $html += "</div>";
                        $("#data").append($html);
                    }
                }
            }
        });
    }

    createPluginList();

    //test settings form
    /*$.ajax({
        type: "POST",
        url: "/api/?v1/createPluginSettingsForm",
        data: {'plugin': 'organizr'},
        dataType: "json",
        success: function(response){
            console.log(response);
            $("#data").append(response.data);
        }
    });*/
</script>
</body>
</html>
