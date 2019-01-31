<?php
include('../functions.php');
include('../auth_check.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <link type="text/css" href="../../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" integrity="sha256-ENFZrbVzylNbgnXx0n3I1g//2WeO47XxoPe0vkp3NC8=" crossorigin="anonymous"/>
    <link type="text/css" href="../../css/alpaca.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.13/css/all.css" integrity="sha384-DNOHZ68U8hZfKXOrtjWvjxusGo9WQnrNx2sqG0tfsghAvtVlRW3tvkXWZh58N9jp" crossorigin="anonymous">
    <!-- <link type="text/css" href="../main.css" rel="stylesheet"> -->
    <link type="text/css" href="../../css/monitorr.css" rel="stylesheet">
    <link type="text/css" href="../../data/custom.css" rel="stylesheet">

    <meta name="theme-color" content="#464646"/>
    <meta name="theme_color" content="#464646"/>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="../../js/handlebars.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://code.cloudcms.com/alpaca/1.5.24/bootstrap/alpaca.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/ace.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" integrity="sha256-3blsJd4Hli/7wCQ+bmgXfOdK7p/ZUMtPXY08jmxSSgk=" crossorigin="anonymous"></script>
    <script src="../../js/monitorr.main.js"></script>

    <title>
		<?php
		$title = $GLOBALS['preferences']['sitetitle'];
		echo $title . PHP_EOL;
		?>
        | User Preferences
    </title>
</head>

<body id="settings-frame-wrapper" class="transparent-background">

<script>
    document.body.className += ' fade-out';
    $(function () {
        $('body').removeClass('fade-out');
    });
</script>

<p id="response"></p>


<div id="modalloading" title="Monitorr services are populating.">

    <div id="modalloadingspinner" style="transform:translateZ(0);"></div>

    <script>
        window.paceOptions = {
            target: "#modalloadingspinner",
            ajax: false
        };
    </script>

    <p class="modaltextloading">Loading services ...</p>

</div>

<div id="serviceform">
    <div id="servicesettings"></div>

    <script type="text/javascript">
        $(document).ready(function () {
            Alpaca.registerConnectorClass("custom");
            $("#servicesettings").alpaca({
                "connector": "custom",
                "dataSource": "./load-settings/services_load.php",
                "schemaSource": "./schemas/services.json",
                "view": {
                    "fields": {
                        "//serviceTitle": {
                            "templates": {
                                "control": "./templates/templates-services_title.html"
                            },
                            "bindings": {
                                "serviceTitle": "#title_input"
                            }
                        },
                        "//enabled": {
                            "templates": {
                                "control": "./templates/templates-services_enabled.html"
                            },
                            "bindings": {
                                "enabled": "#enabled_option"
                            }
                        },
                        "//image": {
                            "templates": {
                                "control": "./templates/templates-services_image.html"
                            },
                            "bindings": {
                                "image": "#image_option"
                            }
                        },
                        "//checkurl": {
                            "templates": {
                                "control": "./templates/templates-checkurl-control.html"
                            }
                        },
                        "//linkurl": {
                            "templates": {
                                "control": "./templates/templates-services_link.html"
                            }
                        }
                    }
                },
                "options": {
                    "toolbarSticky": true,
                    "focus": false,
                    "collapsible": true,
                    "actionbar": {
                        "showLabels": true,
                        "actions": [{
                            "label": "Add Service",
                            "action": "add",
                            "iconClass": "fa fa-plus"
                        }, {
                            "label": "Remove Service",
                            "action": "remove",
                            "iconClass": "fa fa-minus"
                        }, {
                            "label": "Move Up",
                            "action": "up",
                            "iconClass": "fa fa-arrow-up",
                            "enabled": true
                        }, {
                            "label": "Move Down",
                            "action": "down",
                            "iconClass": "fa fa-arrow-down",
                            "enabled": true
                        }, {
                            "label": "Clear",
                            "action": "clear",
                            "iconClass": "fa fa-trash",
                            "click": function(key, action, itemIndex) {
                                var item = this.children[itemIndex];
                                item.setValue("");
                            }
                        }, {
                            "label": "Images",
                            "action": "images",
                            "iconClass": "fa fa-image",
                            "click": function() {
                                var modal = document.getElementById('myModal3');
                                var span = document.getElementsByClassName("closeimg")[0];
                                modal.style.display = "block";

                                span.onclick = function() {
                                    modal.style.display = "none";
                                };

                                window.onclick = function(event) {
                                    if (event.target == modal) {
                                        modal.style.display = "none";
                                    }
                                }
                            }
                        }, {
                            "label": "Sort alphabetically",
                            "action": "sort",
                            "iconClass": "fas fa-sort-alpha-down",
                            "click": function (key) {
                                sortServicesAlphabetically();
                                setTimeout(location.reload.bind(location), 500);
                            }
                        }]
                    },
                    "items": {
                        "fields": {
                            "serviceTitle": {
                                "type": "text",
                                "validate": false,
                                "showMessages": true,
                                "disabled": false,
                                "hidden": false,
                                "label": "Service Title:",
                                "constrainMaxLength": true,
                                "showMaxLengthIndicator": true,
                                "hideInitValidationError": false,
                                "focus": false,
                                "optionLabels": [],
                                "name": "serviceTitle",
                                "size": 20,
                                "placeholder": "Service Name",
                                "typeahead": {},
                                "allowOptionalEmpty": false,
                                "data": {},
                                "autocomplete": false,
                                "disallowEmptySpaces": false,
                                "disallowOnlyEmptySpaces": false,
                                "fields": {},
                                "renderButtons": true,
                                "attributes": {},
                                "events": {
                                    "change": function() {
                                        $('.alpaca-form-button-submit').addClass('buttonchange');
                                    }
                                }
                            },
                            "enabled": {
                                "type": "select",
                                "validate": true,
                                "showMessages": true,
                                "disabled": false,
                                "hidden": false,
                                "label": "Enabled:",
                                "hideInitValidationError": false,
                                "focus": false,
                                "name": "enabled",
                                "typeahead": {},
                                "allowOptionalEmpty": false,
                                "data": {},
                                "autocomplete": false,
                                "disallowEmptySpaces": true,
                                "disallowOnlyEmptySpaces": false,
                                "removeDefaultNone": true,
                                "fields": {},
                                "events": {
                                    "change": function() {
                                        $('.alpaca-form-button-submit').addClass('buttonchange');
                                    }
                                }
                            },
                            "image": {
                                "type": "image",
                                "validate": true,
                                "showMessages": true,
                                "disabled": false,
                                "hidden": false,
                                "label": "Service Image:",
                                "hideInitValidationError": false,
                                "focus": false,
                                "optionLabels": [],
                                "size": 20,
                                "name": "image",
                                "styled": true,
                                "placeholder": "../img/monitorr.png",
                                "typeahead": {},
                                "allowOptionalEmpty": false,
                                "data": {},
                                "autocomplete": false,
                                "disallowEmptySpaces": true,
                                "disallowOnlyEmptySpaces": true,
                                "fields": {},
                                "renderButtons": true,
                                "attributes": {},
                                "events": {
                                    "ready": function() {
                                        $image_preview_id = this.id.substr(6) - 2;
                                        $("#image-preview-alpaca" + $image_preview_id).attr("src", "../" + this.data);
                                    },
                                    "change": function() {
                                        var value = this.getValue();
                                        if (value) {
                                            $image_preview_id = this.id.substr(6) - 2;
                                            $("#image-preview-alpaca" + $image_preview_id).attr("src", "../" + value);
                                        }
                                        $('.alpaca-form-button-submit').addClass('buttonchange');
                                    }
                                }
                            },
                            "checkurl": {
                                "type": "text",
                                "validate": true,
                                "allowIntranet": true,
                                "showMessages": true,
                                "disabled": false,
                                "hidden": false,
                                "label": "Check URL:",
                                "size": 30,
                                //"helpers": ["URL to check status"],
                                //"helper": "URL to check service status. (Port is required?)",
                                "hideInitValidationError": false,
                                "focus": false,
                                "name": "checkurl",
                                "placeholder": "http://localhost:80",
                                "typeahead": {},
                                "allowOptionalEmpty": false,
                                "data": {},
                                "autocomplete": false,
                                "disallowEmptySpaces": true,
                                "disallowOnlyEmptySpaces": true,
                                "fields": {},
                                "renderButtons": true,
                                "attributes": {},
                                "events": {
                                    "change": function() {
                                        $('.alpaca-form-button-submit').addClass('buttonchange');
                                    }
                                }
                            },
                            "linkurl": {
                                "type": "url",
                                "validate": false,
                                "allowIntranet": true,
                                "showMessages": true,
                                "disabled": false,
                                "hidden": false,
                                "label": "Service URL:",
                                "size": 30,
                                //"helpers": ["URL that will be linked to service"],
                                //"helper": "URL that will be linked to service from the UI. ('Link URL' field value is not applied if using 'ping only' option)",
                                "hideInitValidationError": false,
                                "focus": false,
                                "name": "linkurl",
                                "placeholder": "http://localhost:80",
                                "typeahead": {},
                                "allowOptionalEmpty": false,
                                "data": {},
                                "autocomplete": false,
                                "disallowEmptySpaces": false,
                                "disallowOnlyEmptySpaces": false,
                                "fields": {},
                                "renderButtons": true,
                                "attributes": {},
                                "events": {
                                    "change": function() {
                                        $('.alpaca-form-button-submit').addClass('buttonchange');
                                    }
                                }
                            }
                        },
                    },
                    "form": {
                        //   "attributes": {
                        //        "action": "post_receiver-services.php",
                        //        "method": "post",
                        //        "contentType": "application/json"
                        //    },
                        "buttons": {
                            "submit": {
                                "type": "button",
                                "label": "Submit",
                                "name": "submit",
                                "value": "submit",
                                "click": function formsubmit() {
                                    var data = $('#servicesettings').alpaca().getValue();
                                    $.post('post-settings/post_receiver-services.php', {
                                        data,
                                        success: function(data){
                                            console.log('Settings saved! Applying changes');
                                            alert("Settings saved! Applying changes...");
                                            // Refresh form after submit:
                                            setTimeout(location.reload.bind(location), 1000)
                                        },
                                        error: function(errorThrown){
                                            console.log(errorThrown);
                                        }
                                    },);
                                    $('.alpaca-form-button-submit').removeClass('buttonchange');
                                }
                            },
                            "reset":{
                                "label": "Clear Values"
                            }
                        }
                    }
                },
                "postRender": function(control) {
                    if (control.form) {
                        control.form.registerSubmitHandler(function (e) {
                            control.form.getButtonEl('submit').click();
                            return false;
                        });
                    }
                    document.getElementById("modalloading").remove();
                    // check service status ONCE:
                    /*console.log('Service check START');
                    $("#serviceshidden").load('loopsettings.php');
                    document.getElementById("serviceshidden").remove();*/
                },
            });
        });
    </script>

    <!-- Modal pop-up for images directory display: -->

    <div id="myModal3" >

        <span class="closeimg"  aria-hidden="true" title="close images">&times;</span>

        <p class="modaltext">Images:</p>
		<?php $imgpath = '../img/'; ?>
		<?php $usrimgpath = '../data/usrimg/'; ?>
        <p class="modalimgpath"> Default Images: <?php echo realpath($imgpath); ?> </p>
        <p class="modalimgpath"> User Images: <?php echo realpath($usrimgpath); ?> </p>

        <div id="uploadbutton">
            Upload new image to user images directory:
            <label for="choosefile" class="file-upload" title="Select image to upload">
                <i class="fa fa-plus"></i> Choose Image
            </label>

            <input id="choosefile" type="file" name="fileToUpload"/>
            <button id="upload" class="uploadbtn" title="Upload image to user images directory"><i class="fa fa-cloud-upload"></i> Upload</button>
            <br>
            <span id="file-selected" title="Image selected for upload"></span>

        </div>

        <div id="uploadreturn"></div>

        <!-- Modal content -->
        <div id="mymodal4">

			<?php

			$imgpath = __DIR__ . "/../../img/";
			$usrimgpath = __DIR__ . "/../../data/usrimg";
			$images = glob($imgpath.'*.*');
			$images2 = glob($usrimgpath.'*.*');

			$count = 0;

			foreach ($images as $image) {
			    $img_parts = explode("/", $image);
                $img_name = end($img_parts);
				echo '<div class="imgthumb">';
                    echo '<img src="../../img/'.$img_name.'" title="Click to copy"/>';
                    echo '<input type="text" value="../img/'.$img_name.'"\>';
				echo '</div>';
			}

			foreach ($images2 as $image) {
				$img_parts = explode("/", $image);
				$img_name = end($img_parts);
				echo '<div class="imgthumb">';
                    echo '<img src="../../data/userimg/'.$img_name.'" title="Click to copy"/>';
                    echo '<input type="text" value="../data/userimg/'.$img_name.'">';
				echo '</div>';
			}
			?>

        </div>

    </div>

    <!-- Click-to-copy function -->

    <script>
        $(function () {
           $(".imgthumb").click(function () {
               let $input = $(this).find("input");
               $input.select();
               document.execCommand("Copy");
               notify("Copied to clipboard", $input.val() + " was copied to your clipboard");
               $input.blur();
           });
        });
    </script>

    <!-- scroll to top   -->

    <button onclick="topFunction()" id="myBtn" title="Go to top"></button>

    <script>

        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {scrollFunction()};

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.getElementById("myBtn").style.display = "block";
            } else {
                document.getElementById("myBtn").style.display = "none";
            }
        }

        // When the user clicks on the button, scroll to the top of the document
        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }

    </script>
</div>


</body>

</html>
