<?php
include('../functions.php');
include('../auth_check.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <link type="text/css" href="../../css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="../../css/alpaca.min.css" rel="stylesheet">
    <link type="text/css" href="../../css/monitorr.css" rel="stylesheet">
    <link type="text/css" href="../../data/custom.css" rel="stylesheet">

    <meta name="theme-color" content="#464646"/>
    <meta name="theme_color" content="#464646"/>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="../../js/handlebars.js"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://code.cloudcms.com/alpaca/1.5.24/bootstrap/alpaca.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.9/ace.js"></script>

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


<div id="siteform">

    <div id="sitesettings"></div>

    <script type="text/javascript">
        $(document).ready(function () {
            var CustomConnector = Alpaca.Connector.extend({
                buildAjaxConfig: function (uri, isJson) {
                    var ajaxConfig = this.base(uri, isJson);
                    ajaxConfig.headers = {
                        "ssoheader": "abcde12345"
                    };
                    return ajaxConfig;
                }
            });

            var data;
            $.ajax({
                dataType: "json",
                url: './load-settings/site_settings_load.php',
                data: data,
                success: function (data) {
                    console.log(data);
                },

                error: function (errorThrown) {
                    console.log(errorThrown);
                    document.getElementById("response").innerHTML = "GET failed (ajax)";
                    alert("GET failed (ajax)");
                },
            });

            Alpaca.registerConnectorClass("custom", CustomConnector);
            $("#sitesettings").alpaca({
                "connector": "custom",
                "dataSource": "./load-settings/site_settings_load.php",
                "schemaSource": "./schemas/site_settings.json",
                "view": {
                    "parent": "bootstrap-edit-horizontal",
                    "layout": {
                        "template": './templates/two-column-layout-template.html',
                        "bindings": {
                            "rfsysinfo": "leftcolumn",
                            "rftime": "leftcolumn",
                            "rfconfig": "leftcolumn",
                            "pinghost": "leftcolumn",
                            "pingport": "leftcolumn",
                            "disk1enable": "tdcenterleft",
                            "disk1": "disk1",
                            "disk2enable": "tdcenterleft",
                            "disk2": "disk2",
                            "disk3enable": "tdcenterleft",
                            "disk3": "disk3",
                            "hdok": "hdok",
                            "hdwarn": "hdwarn",
                            "cpuok": "cpuok",
                            "cpuwarn": "cpuwarn",
                            "ramok": "ramok",
                            "ramwarn": "ramwarn",
                            "pingok": "pingok",
                            "pingwarn": "pingwarn"
                        }
                    },
                    "fields": {
                        "/rfsysinfo": {
                            "templates": {
                                "control": "./templates/templates-site-settings_rfsysinfo.html"
                            },
                            "bindings": {
                                "rfsysinfo": "#rfsysinfo_input"
                            }
                        },
                        "/rftime": {
                            "templates": {
                                "control": "./templates/templates-site-settings_rftime.html"
                            },
                            "bindings": {
                                "rftime": "#rftime_input"
                            }
                        },
                        "/rfconfig": {
                            "templates": {
                                "control": "./templates/templates-site-settings_rfconfig.html"
                            },
                            "bindings": {
                                "rftime": "#rfconfig_input"
                            }
                        },
                        "/pinghost": {
                            "templates": {
                                "control": "./templates/templates-site-settings_pinghost.html"
                            },
                            "bindings": {
                                "pinghost": "#pinghost_input"
                            }
                        },
                        "/pingport": {
                            "templates": {
                                "control": "./templates/templates-site-settings_pingport.html"
                            },
                            "bindings": {
                                "pingport": "#pingport_input"
                            }
                        },
                        "/disk1enable": {
                            "templates": {
                                "control": "./templates/templates-site-settings_disk1enable.html"
                            },
                            "bindings": {
                                "disk1enable": "#disk1enable_select"
                            }
                        },
                        "/disk1": {
                            "templates": {
                                "control": "./templates/templates-site-settings_disk1.html"
                            },
                            "bindings": {
                                "disk1": "#disk1_input"
                            }
                        },
                        "/hdok": {
                            "templates": {
                                "control": "./templates/templates-site-settings_hdok.html"
                            },
                            "bindings": {
                                "hdok": "#hdok_input"
                            }
                        },
                        "/hdwarn": {
                            "templates": {
                                "control": "./templates/templates-site-settings_hdwarn.html"
                            },
                            "bindings": {
                                "hdwarn": "#hdwarn_input"
                            }
                        },
                        "/cpuok": {
                            "templates": {
                                "control": "./templates/templates-site-settings_cpuok.html"
                            },
                            "bindings": {
                                "cpuok": "#cpuok_input"
                            }
                        },
                        "/cpuwarn": {
                            "templates": {
                                "control": "./templates/templates-site-settings_cpuwarn.html"
                            },
                            "bindings": {
                                "cpuwarn": "#cpuwarn_input"
                            }
                        },
                        "/ramok": {
                            "templates": {
                                "control": "./templates/templates-site-settings_ramok.html"
                            },
                            "bindings": {
                                "ramok": "#ramok_input"
                            }
                        },
                        "/ramwarn": {
                            "templates": {
                                "control": "./templates/templates-site-settings_ramwarn.html"
                            },
                            "bindings": {
                                "ramwarn": "#ramwarn_input"
                            }
                        },
                        "/pingok": {
                            "templates": {
                                "control": "./templates/templates-site-settings_pingok.html"
                            },
                            "bindings": {
                                "pingok": "#pingok_input"
                            }
                        },
                        "/pingwarn": {
                            "templates": {
                                "control": "./templates/templates-site-settings_pingwarn.html"
                            },
                            "bindings": {
                                "pingwarn": "#pingwarn_input"
                            }
                        }
                    }
                },
                "options": {
                    "focus": false,
                    "type": "object",
                    "helpers": [],
                    "validate": true,
                    "disabled": false,
                    "showMessages": true,
                    "collapsible": false,
                    "legendStyle": "button",
                    "fields": {
                        "rfsysinfo": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "Service & system refresh interval:",
                            "helper": "Service & system info refresh interval in milliseconds.",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "rfsysinfo",
                            "placeholder": "5000",
                            "typeahead": {},
                            "size": "10",
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "rftime": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "Time sync interval:",
                            "helper": "Specifies how frequently (in milliseconds) the UI clock will synchronize time with the hosting webserver.",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "rftime",
                            "placeholder": "180000",
                            "typeahead": {},
                            "size": "10",
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "rfconfig": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "Config sync interval:",
                            "helper": "Specifies how frequently (in milliseconds) the UI will synchronize the config variables.",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "rfconfig",
                            "placeholder": "180000",
                            "typeahead": {},
                            "size": "10",
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "pinghost": {
                            "type": "text",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "Ping host:",
                            "size": 35,
                            "helper": "URL or IP to ping for latency check. <br> (WAN DNS provider is suggested)",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "pinghost",
                            "placeholder": "8.8.8.8",
                            "typeahead": {},
                            "size": "10",
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "pingport": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "Ping host port:",
                            "helper": "Ping host port for ping latency check. <br> (If using 8.8.8.8, value should be '53')",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "pingport",
                            "placeholder": "53",
                            "typeahead": {},
                            "size": "10",
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "disk1enable": {
                            "type": "select",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "HD1 display:",
                            "hideInitValidationError": false,
                            "focus": false,
                            "name": "disk1enable",
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
                        "disk1": {
                            "dependencies": {
                                "disk1enable": ["Enable"]
                            },
                            "type": "text",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "HD1 volume:",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "disk1",
                            "placeholder": "HD volume",
                            "typeahead": {},
                            "size": "7",
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
                        "disk2enable": {
                            "dependencies": {
                                "disk1enable": ["Enable"]
                            },
                            "type": "select",
                            "validate": false,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "HD2 display:",
                            "hideInitValidationError": false,
                            "focus": false,
                            "name": "disk2enable",
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
                        "disk2": {
                            "dependencies": {
                                "disk2enable": ["Enable"]
                            },
                            "type": "text",
                            "validate": false,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "HD2 volume:",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "disk2",
                            "placeholder": "HD volume",
                            "typeahead": {},
                            "size": "7",
                            "allowOptionalEmpty": true,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "disk3enable": {
                            "dependencies": {
                                "disk2enable": ["Enable"]
                            },
                            "type": "select",
                            "validate": false,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "HD3 display:",
                            "hideInitValidationError": false,
                            "focus": false,
                            "name": "disk3enable",
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
                        "disk3": {
                            "dependencies": {
                                "disk3enable": ["Enable"]
                            },
                            "type": "text",
                            "validate": false,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "HD3 volume:",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "disk3",
                            "placeholder": "HD volume",
                            "typeahead": {},
                            "size": "7",
                            "allowOptionalEmpty": true,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "hdok": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "HD OK color value:",
                            "helper": "HD used % less than this value will be green.",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "hdok",
                            "placeholder": "75",
                            "typeahead": {},
                            "size": 5,
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "hdwarn": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "HD warning color value:",
                            "helper": "HD free % less than this will be yellow.",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "hdwarn",
                            "placeholder": "95",
                            "typeahead": {},
                            "size": 5,
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "cpuok": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "CPU OK:",
                            "helper": "CPU usage % less than this value will appear green, above this value will appear yellow.",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "cpuok",
                            "placeholder": "50",
                            "typeahead": {},
                            "size": 5,
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "cpuwarn": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "CPU warning:",
                            "helper": "CPU usage % less than this value will appear yellow, above this value will appear red",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "cpuwarn",
                            "placeholder": "90",
                            "typeahead": {},
                            "size": 5,
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "ramok": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "RAM OK:",
                            "helper": "RAM usage % less than this value will appear green, above this value will appear yellow.",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "ramok",
                            "placeholder": "50",
                            "typeahead": {},
                            "size": 5,
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "ramwarn": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "RAM warning:",
                            "helper": "RAM usage % less than this value will appear yellow, above this value will appear red.",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "ramwarn",
                            "placeholder": "90",
                            "typeahead": {},
                            "size": 5,
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "pingok": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "Ping OK:",
                            "helper": "Ping RT response time in ms less than this value will appear green, above this value will appear yellow.",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "pingok",
                            "placeholder": "50",
                            "typeahead": {},
                            "size": 5,
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                        "pingwarn": {
                            "type": "number",
                            "validate": true,
                            "showMessages": true,
                            "disabled": false,
                            "hidden": false,
                            "label": "Ping warning:",
                            "helper": "Ping RT response time in ms less than this value will appear yellow, above this value will appear red.",
                            "hideInitValidationError": false,
                            "focus": false,
                            "optionLabels": [],
                            "name": "pingwarn",
                            "placeholder": "100",
                            "typeahead": {},
                            "size": 5,
                            "allowOptionalEmpty": false,
                            "data": {},
                            "autocomplete": false,
                            "disallowEmptySpaces": true,
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
                    "form": {
                        // "attributes": {
                        //     "action": "post_receiver-site_settings.php",
                        //     "method": "post",
                        // },
                        "buttons": {
                            "submit": {
                                "type": "button",
                                "label": "Submit",
                                "name": "submit",
                                "value": "submit",
                                click: function(){
                                    var data = $('#sitesettings').alpaca().getValue();
                                    $.post({
                                        url: './post-settings/post_receiver-site_settings.php',
                                        data: $('#sitesettings').alpaca().getValue(),
                                        success: function(data) {
                                            console.log("POST: Settings saved!");
                                            alert("Settings saved! Applying changes...");
                                            $('.alpaca-form-button-submit').removeClass('buttonchange');
                                            $('#sitepreview').load(document.URL + ' #sitepreview');
                                            $('#colortable').load(document.URL + ' #colortable');
                                        },
                                        error: function(errorThrown){
                                            console.log(errorThrown);
                                            alert("POST Error: submitting data.");
                                        }
                                    });
                                }
                            },
                            "reset":{
                                "label": "Clear Values"
                            }
                            // "view": {
                            //     "type": "button",
                            //     "label": "View JSON",
                            //     "value": "View JSON",
                            //     "click": function() {
                            //         alert(JSON.stringify(this.getValue(), null, "  "));
                            //     }
                            // }
                        },
                    }
                },
                "postRender": function(control) {
                    if (control.form) {
                        control.form.registerSubmitHandler(function (e) {
                            control.form.getButtonEl('submit').click();
                            return false;
                        });
                    }
                },
            });
        });
    </script>

    <div id="colortable">
        <table id="colortable2">

            <tr>
                <td id="colorkey" colspan="2">
                    Color value proportion key: <i class="fa fa-fw fa-question-circle input_icon" id="colorkey_icon" title="Represents your color values on a proportionate scale."> </i>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="hd" class="col-md-2 col-centered double-val-label">
                        <span> HD: </span>
                    </div>
                </td>
                <td id="hdbar1">
                    <div id="hdbar1">

                        <table id="hdbar2" class='colorbar'>
                            <tr style='width: 100%;'>
                                <td title="HD usage OK: 0% - <?php echo $GLOBALS["settings"]['hdok']; ?>%" style='background-color: #5cb85c; width: <?php echo $GLOBALS["settings"]['hdok']; ?>%;'> </td>
                                <td title="HD usage warn: <?php echo $GLOBALS["settings"]['hdok']; ?>% - <?php echo $GLOBALS["settings"]['hdwarn']; ?>%" style='background-color: #f0ad4e; width:<?php echo(100 - $GLOBALS["settings"]['hdok'] - (100 - $GLOBALS["settings"]['hdwarn'])); ?>%;'> </td>
                                <td title="HD usage not OK: <?php echo $GLOBALS["settings"]['hdwarn']; ?>% - 100%" style='background-color: #d9534f; width:<?php echo(100 - $GLOBALS["settings"]['hdwarn']); ?>%;'></td>
                            </tr>
                        </table>

                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="cpu" class="col-md-2 col-centered double-val-label">
                        <span>CPU: </span>
                    </div>
                </td>
                <td id="cpubar1">
                    <div id="cpubar1">

                        <table class='colorbar'>
                            <tr style='width: 100%;'>
                                <td title="CPU utilization OK: 0% - <?php echo $GLOBALS["settings"]['cpuok']; ?>%" style='background-color: #5cb85c; width: <?php echo $GLOBALS["settings"]['cpuok']; ?>%;'> </td>
                                <td title="CPU utilization warn: <?php echo $GLOBALS["settings"]['cpuok']; ?>% - <?php echo $GLOBALS["settings"]['cpuwarn']; ?>%" style='background-color: #f0ad4e; width:<?php echo(100 - $GLOBALS["settings"]['cpuok'] - (100 - $GLOBALS["settings"]['cpuwarn'])); ?>%;'> </td>
                                <td title="CPU utilization not OK: <?php echo $GLOBALS["settings"]['cpuwarn']; ?>% - 100%" style='background-color: #d9534f; width:<?php echo(100 - $GLOBALS["settings"]['cpuwarn']); ?>%;'></td>
                            </tr>
                        </table>

                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div id="ram" class="col-md-2 col-centered double-val-label">
                        <span>RAM: </span>
                    </div>
                </td>
                <td id="rambar1">
                    <div id="rambar1">

                        <table class='colorbar'>
                            <tr style='width: 100%;'>
                                <td title="RAM utilization OK: 0% - <?php echo $GLOBALS["settings"]['ramok']; ?>%" style='background-color: #5cb85c; width: <?php echo $GLOBALS["settings"]['ramok']; ?>%;'> </td>
                                <td title="RAM utilization warn: <?php echo $GLOBALS["settings"]['ramok']; ?>% - <?php echo $GLOBALS["settings"]['ramwarn']; ?>%" style='background-color: #f0ad4e; width:<?php echo(100 - $GLOBALS["settings"]['ramok'] - (100 - $GLOBALS["settings"]['ramwarn'])); ?>%;'> </td>
                                <td title="RAM utilization not OK: <?php echo $GLOBALS["settings"]['ramwarn']; ?>% - 100%" style='background-color: #d9534f; width:<?php echo(100 - $GLOBALS["settings"]['ramwarn']); ?>%;'></td>
                            </tr>
                        </table>

                    </div>
                </td>
            </tr>

        </table>
    </div>

</div>

</body>

</html>
