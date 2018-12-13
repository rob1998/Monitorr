'use strict';

let nIntervId = [];
let nIntervId2;
let editMode = false;
let current_rfsysinfo;

$(function () {
    //fade-in effect
    $('body').removeClass('fade-out');


    //open service link in new tab
    $('.servicetile').on("click", function () {
        if (!$(this).hasClass("nolink") && !editMode) {
            let win = window.open($(this).data("location"), '_blank');
            win.focus();
        }
    });

    //<editor-fold desc="onClick actions">
    //<editor-fold desc="onClick actions">
    $(document).on('click', '.plugin-settings-button', function (e) {
        $.ajax({
            type: "POST",
            url: "/api/?v1/formBuilder/settingsForm/plugin",
            data: {'plugin': $(this).data("plugin")},
            dataType: "json",
            success: function (response) {
                let $html = response.data;
                $("#plugin-modal").html($html);
                $("#plugin-modal").fadeIn("slow");
            }
        });
    });

    $(document).on('click', '#save-order-btn', function (e) {
        //create order array;
        let $order = [];
        $("#statusloop > div").each(function () {
            $order.push($(this).data("order"));
        });
        console.log($order);
        $.ajax({
            type: "POST",
            url: "/api/?v1/services/sort/custom",
            data: {'order': $order},
            dataType: "json",
            success: function (response) {
                console.log(response);
                if (response.data === "success") {
                    notify("Success!", "Order saved");
                }
            }
        });
    });

    $(document).on('click', '.plugin-page-button', function (e) {
        let $pluginModal = $("#plugin-modal");
        $pluginModal.html("");
        $pluginModal.html('<object type="text/html" class="object" data="../../plugins/' + $(this).data("plugin") + "/" + $(this).data("page") + '" ></object>');
        $pluginModal.fadeIn("slow");
    });

    //close modal when clicked next to it
    $(document).mouseup(function (e) {
        const container = $("#plugin-modal");
        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) container.fadeOut("slow");
    });
    //</editor-fold>

    $(document).on('submit', '#plugin-settings', function (e) {
        e.preventDefault();

        let $plugin = $(this).data("plugin");
        let $formData = $(this).serializeArray();

        $('#plugin-settings input[type="checkbox"]').each(function () {
            if ($(this).is(":checked")) {
                let objIndex = $formData.findIndex((obj => obj.name === this.name));
                $formData[objIndex].value = true;
            } else {
                $formData.push({name: this.name, value: false});
            }
        });

        //rewrite to key->value array
        let $result = {};
        $formData.forEach(function (item) {
            $result[item.name] = item.value;
        });

        $.ajax({
            type: "POST",
            url: "/api/?v1/settings/update",
            data: {
                'plugins': {
                    [$plugin]: $result
                }
            },
            dataType: "json",
            success: function (response) {
                console.log("response");
                console.log(response);
            }
        });
    });


    //sortable
    $("#edit-mode-toggle :checkbox").change(function () {
        let $statusloop = $("#statusloop");
        editMode = ($(this).is(':checked'));
        if (editMode) {
            $statusloop.sortable();
            $statusloop.disableSelection();
            $statusloop.sortable("enable");
            $('#auto-update-toggle :checkbox').removeAttr('checked');
            $("#save-order-btn").removeClass("hidden");
        } else {
            $statusloop.sortable("disable");
            $("#save-order-btn").addClass("hidden");
        }
    });

    // Auto update services and offline marquee
    $("#auto-update-toggle :checkbox").change(function () {
        if ($(this).is(':checked')) {
            refreshConfig();
            updateSummary();
            statusCheck();
            nIntervId["refreshConfig"] = setInterval(updateSummary, settings.rfconfig);
            nIntervId["updateSummary"] = setInterval(updateSummary, settings.rfsysinfo);
            nIntervId2 = setInterval(statusCheck, settings.rfsysinfo);
            notify("Auto refresh: Enabled | Interval: " + settings.rfsysinfo + " ms");
        } else {
            clearInterval(nIntervId["refreshConfig"]);
            clearInterval(nIntervId["updateSummary"]);
            clearInterval(nIntervId2);
            notify("Auto refresh: Disabled");
        }
    });
    $('#auto-update-toggle :checkbox').attr('checked', 'checked').change();
});

function sortServicesAlphabetically() {
    $.ajax({
        type: 'GET',
        url: '../../../api/?v1/services/sort/alphabetically',
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                //refresh page
            } else {
                console.log("ERROR: API error" + response.statusText);
                $('#ajaxmarquee').html('<i class="fa fa-fw fa-exclamation-triangle"></i>');
            }
        },
        error: function (x, t, m) {
            if (t === "timeout") {
                console.log("ERROR: marquee timeout");
                $('#ajaxmarquee').html('<i class="fa fa-fw fa-exclamation-triangle"></i>');
            } else {
            }
        }
    });
}

function updateSummary() {

    console.log('Service offline check START');

    $.ajax({
        type: 'GET',
        url: 'api/?v1/services/get/offline',
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                let $result = response.data;
                if ($result.length === 0) {
                    $("#summary").hide();
                } else {
                    $("#summary").fadeOut(function () {
                        $(this).html($result[0]).fadeIn();
                    });
                }
            } else {
                console.log("ERROR: API error" + response.statusText);
                $('#ajaxmarquee').html('<i class="fa fa-fw fa-exclamation-triangle"></i>');
            }
        },
        error: function (x, t, m) {
            if (t === "timeout") {
                console.log("ERROR: marquee timeout");
                $('#ajaxmarquee').html('<i class="fa fa-fw fa-exclamation-triangle"></i>');
            } else {
            }
        }
    });
}

function createPluginList(element) {
    $.ajax({
        type: "GET",
        url: "/api/?v1/getPlugins",
        dataType: "json",
        success: function (response) {
            if (response.data.length == 0) {
                $(element).html("No plugins found");
            } else {
                let $html = "";
                for (let i = 0; i < response.data.length; i++) {
                    let $plugin = response.data[i];
                    let $imgUrl = "../../plugins/" + $plugin.name + "/" + $plugin.image;
                    $html += "<div class='plugin-box'>";
                    $html += "<img src='" + $imgUrl + "'>";
                    $html += "<h3>" + $plugin.name + "</h3>";
                    $html += "<div class='plugin-box-overlay'>";
                    $html += "<a class='btn plugin-overlay-button plugin-page-button' data-plugin='" + $plugin.name + "' data-page='" + $plugin.page + "'><i class='icon fas fa-file-alt'></i></a>";
                    $html += "<a class='btn plugin-overlay-button plugin-settings-button' data-plugin='" + $plugin.name + "'><i class='icon fas fa-cogs'></i></a>";
                    $html += "</div>";
                    $html += "</div>";
                }
                $(element).html($html);
            }
        }
    });
}

function statusCheck(override) {

    if ($("#buttonStart input:checkbox").is(':checked') || override) {
        console.log('Service check START | Interval: ' + settings.rfsysinfo + ' ms');
        getSystemBadges();
        checkServices();
    }

}

function checkServices() {
    let titleArray = [];
    for (let i = 0; i < services.length; i++) {
        let $service = services[i];
        let $serviceDiv = $('#service-' + $service.serviceTitle.replace(/ /g, "-"));

        if ($serviceDiv.length) {
            $serviceDiv.attr("data-order", i);
            ping($service);
        } else {
            let html = "";
            html += "<div id=\"service-" + $service.serviceTitle.replace(/ /g, "-") + "\"  class=\"col-lg-4\" data-order='" + i + "' data-offline='false'>";
            html += "   <div id=\"pingindicator-" + $service.serviceTitle.replace(/ /g, "-") + "\" class='pingindicator' >";
            html += "       <div class=\"pingcircle\"></div>";
            html += "   </div>";
            if ($service.link == "Yes") {
                html += "   <div class=\"servicetile\" data-location=\"" + $service.linkurl + "\" style=\"display: block\">";
            } else {
                html += "   <div class=\"servicetile nolink\" data-location=\"" + $service.linkurl + "\" style=\"display: block\">";
            }
            html += "       <img id=\"" + $service.serviceTitle.replace(/ /g, "-") + "-service-img\" src=\"assets/img/" + $service.image + "\" class=\"serviceimg\" alt='" + $service.serviceTitle + "'>";
            html += "       <div class=\"servicetitle\">";
            html += $service.serviceTitle;
            html += "       </div>";
            html += "       <div id=\"status-" + $service.serviceTitle.replace(/ /g, "-") + "\">Loading</div>";
            html += "   </div>";
            html += "</div>";

            // INSERT AT CORRECT POSITION
            let n = order - 1;
            let $nextElement = $("#statusloop div[data-order='" + n + "']");
            while ($($nextElement).data("offline") === "true") {
                n++;
            }
            $(html).insertAfter($($nextElement));
            ping($service);
        }
        titleArray.push($service.serviceTitle);
    }

    $("#statusloop > div").each(function () {
        let split = $(this).prop("id").split("-");
        let title;
        if (split.length > 2) {
            split.shift();
            title = split.join(" ");
        } else {
            title = split[1];
        }
        if ($.inArray(title, titleArray) == -1) $(this).remove();
    });
}

function ping(service) {
    $.ajax({
        type: "POST",
        url: "api/?v1/getPing",
        data: {'service': service.serviceTitle},
        dataType: "json",
        success: function (response) {
            let $pingTime = response.data;
            let $serviceElement = $("#service-" + service.serviceTitle.replace(/ /g, "-"));
            let $pingClassElement = $("#pingindicator-" + service.serviceTitle.replace(/ /g, "-") + " > div");
            let $btnStatus = $("#status-" + service.serviceTitle.replace(/ /g, "-"));

            if (!$pingTime) {

                $pingClassElement.removeClass("pinggreen");
                $pingClassElement.removeClass("pingyellow");
                $pingClassElement.removeClass("pingred");
                $pingClassElement.addClass("pingred");

                $pingClassElement.prop('title', "Ping response time: unresponsive");

                if ($serviceElement.attr("data-offline") === "false") {
                    $btnStatus.removeClass("btnonline");
                    $btnStatus.addClass("btnoffline");
                    $btnStatus.text("Offline");

                    $serviceElement.find(".servicetile").addClass("offline");
                    $serviceElement.find(".servicetitle").addClass("offline");
                    $serviceElement.find(".serviceimg").addClass("offline");
                    $serviceElement.attr("data-offline", "true");

                    if (preferences.offlineServicesFirst === "True") {
                        $($serviceElement).parent().prepend($serviceElement);
                    }
                }

            } else {

                let $pingok = settings.pingok;
                let $pingwarn = settings.pingwarn;
                let $pingClass;

                if ($pingTime < $pingok) {
                    $pingClass = 'pinggreen';
                } else if (($pingTime >= $pingok) && ($pingTime < $pingwarn)) {
                    $pingClass = 'pingyellow';
                } else {
                    $pingClass = 'pingred';
                }

                $pingClassElement.removeClass("pinggreen");
                $pingClassElement.removeClass("pingyellow");
                $pingClassElement.removeClass("pingred");
                $pingClassElement.addClass($pingClass);

                $pingClassElement.prop('title', "Ping response time: " + $pingTime + " ms");

                $btnStatus.removeClass("btnoffline");
                $btnStatus.addClass("btnonline");
                $btnStatus.text("Online");

                if ($serviceElement.attr("data-offline") === "true") {

                    $serviceElement.find(".servicetile").removeClass("offline");
                    $serviceElement.find(".servicetitle").removeClass("offline");
                    $serviceElement.find(".serviceimg").removeClass("offline");
                    $serviceElement.attr("data-offline", "false");

                    if (preferences.offlineServicesFirst === "True") {
                        let order = $($serviceElement).data("order");
                        let n = order - 1;
                        let $nextElement = $("#statusloop div[data-order='" + n + "']");
                        while ($($nextElement).data("offline") === "true") {
                            n++;
                        }
                        $($serviceElement).insertAfter($($nextElement));
                    }
                }
            }
        }
    });
}

function showpace() {
    $('.pace-activity').addClass('showpace');
}

function getSystemBadges() {
    $.ajax({
        url: "api/?v1/getSystemBadges",
        type: "GET",
        success: function (response) {
            let data = JSON.parse(response).data;
            if (data != null) {
                //<editor-fold desc="values">
                $("#cpu > .value").text(data.serverLoad + "%");
                $("#ram > .value").text(data.ramPercentage + "%");
                $("#uptime > .value").text(data.totalUptime);
                $("#ping > .value").text(data.pingTime + "ms");
                if (data.disk1Usage != "?") {
                    $("#hdpercent1").show();
                    $("#hdlabel1").show();
                    $("#hdpercent1").text(data.disk1Usage + "%");
                } else {
                    $("#hdpercent1").hide();
                    $("#hdlabel1").hide();
                }
                if (data.disk2Usage != "?") {
                    $("#hdpercent2").show();
                    $("#hdlabel2").show();
                    $("#hdpercent2").text(data.disk2Usage + "%");
                } else {
                    $("#hdpercent2").hide();
                    $("#hdlabel2").hide();
                }
                if (data.disk3Usage != "?") {
                    $("#hdpercent3").show();
                    $("#hdlabel3").show();
                    $("#hdpercent3").text(data.disk3Usage + "%");
                } else {
                    $("#hdpercent3").hide();
                    $("#hdlabel3").hide();
                }
                //</editor-fold>

                //<editor-fold desc="classes">
                //<editor-fold desc="cpu class">
                const cpuok = settings.cpuok;
                const cpuwarn = settings.cpuwarn;
                let cpuClass;
                if (data.serverLoad < cpuok) {
                    cpuClass = 'success';
                } else if ((data.serverLoad >= cpuok) && (data.serverLoad < cpuwarn)) {
                    cpuClass = 'warning';
                } else {
                    cpuClass = 'danger';
                }
                $("#cpu > span:first").addClass(cpuClass);
                //</editor-fold>

                //<editor-fold desc="ram class">
                const ramok = settings.ramok;
                const ramwarn = settings.ramwarn;
                let ramClass;
                if (data.ramPercentage < ramok) {
                    ramClass = 'success';
                } else if ((data.ramPercentage >= ramok) && (data.ramPercentage < ramwarn)) {
                    ramClass = 'warning';
                } else {
                    ramClass = 'danger';
                }
                $("#ram > span:first").addClass(ramClass);
                //</editor-fold>

                //<editor-fold desc="ping class">
                const pingok = settings.pingok;
                const pingwarn = settings.pingwarn;
                let pingClass;
                if (data.pingTime == "?") pingClass = 'danger';
                else if (data.pingTime < pingok) {
                    pingClass = 'success';
                } else if ((data.pingTime >= pingok) && (data.pingTime < pingwarn)) {
                    pingClass = 'warning';
                } else {
                    pingClass = 'danger';
                }
                $("#ping > span:first").addClass(pingClass);
                //</editor-fold>

                //<editor-fold desc="disk classes">
                const diskok = settings.hdok;
                const diskwarn = settings.hdwarn;
                if (data.disk1Usage != "?") {
                    //<editor-fold desc="disk1 class">
                    let disk1Class;
                    if (data.disk1Usage < diskok) {
                        disk1Class = 'success';
                    } else if ((data.disk1Usage >= diskok) && (data.disk1Usage < diskwarn)) {
                        disk1Class = 'warning';
                    } else {
                        disk1Class = 'danger';
                    }
                    $("#hdlabel1").addClass(disk1Class);
                    //</editor-fold>
                }
                if (data.disk2Usage != "?") {
                    //<editor-fold desc="disk2 class">
                    let disk2Class;
                    if (data.disk2Usage < diskok) {
                        disk2Class = 'success';
                    } else if ((data.disk2Usage >= diskok) && (data.disk2Usage < diskwarn)) {
                        disk2Class = 'warning';
                    } else {
                        disk2Class = 'danger';
                    }
                    $("#hdlabel2").addClass(disk2Class);
                    //</editor-fold>
                }
                if (data.disk3Usage != "?") {
                    //<editor-fold desc="disk3 class">
                    let disk3Class;
                    if (data.disk3Usage < diskok) {
                        disk3Class = 'success';
                    } else if ((data.disk3Usage >= diskok) && (data.disk3Usage < diskwarn)) {
                        disk3Class = 'warning';
                    } else {
                        disk3Class = 'danger';
                    }
                    $("#hdlabel3").addClass(disk3Class);
                    //</editor-fold>
                }
                //</editor-fold>
                //</editor-fold>
            }
        }
    });
}

function refreshConfig() {
    $.ajax({
        url: "api/?v1/settings/get",
        type: "GET",
        success: function (response) {

            let json = JSON.parse(response);
            if (json.status === "success") {
                let $data = json.data;
                settings = $data.settings;
                preferences = $data.preferences;
                services = $data.services;
                document.title = preferences.sitetitle; //update page title to configured title
                //console.log('Refreshed config variables');
            }
        }
    });
}

function syncServerTime() {
    /*console.log('Monitorr time update START | Interval: ' + settings.rftime + ' ms');
    $.ajax({
        url: "assets/php/time.php",
        type: "GET",
        success: function (response) {
            var response = $.parseJSON(response);
            serverTime = response.serverTime;
            timeStandard = parseInt(response.timeStandard);
            timeZone = response.timezoneSuffix;
            rftime = response.rftime;
            date = new Date(serverTime);
            setTimeout(function () {
                syncServerTime()
            }, settings.rftime); //delay is rftime
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Monitorr time update START');
        }
    });*/
    // TODO: create API call for this one
}

function parseGithubToHTML(result) {

    result = result.replace(/\n/g, '<br />'); //convert line breaks

    result = result.replace(/\*\*\*(.*)\*\*\*/g, '<em class="bold italic">$1</em>'); // convert bold italic text
    result = result.replace(/\*\*(.*)\*\*/g, '<em class="bold">$1</em>'); // convert bold italic text
    result = result.replace(/\*(.*)\*/g, '<em class="italic">$1</em>'); // convert bold italic text

    result = result.replace(/\_(.*)\_/g, '<em class="italic">$1</em>'); // convert to italic text

    result = result.replace(/\#\#\#(.*)/g, '<h3>$1</h3>'); // convert to H3
    result = result.replace(/\#\#(.*)/g, '<h2>$1</h2>'); // convert to H2
    result = result.replace(/\#\s(.*)/g, '<h1>$1</h1>'); // convert to H1

    result = result.replace(/\[(.*)\]\((http.*)\)/g, '<a class="releaselink" href=$2 target="_blank" title="$1">$1</a>'); // convert links with titles
    result = result.replace(/(https:\/\/github.com\/Monitorr\/Monitorr\/issues\/(\d*))/g, '<a class="releaselink" href="$1" title="GitHub Issue" target="_blank">#$2</a>'); // convert issue links
    result = result.replace(/\s(https?:\/\/?[-A-Za-z0-9+&@#/%?=~_|!:,.;]+[-A-Za-z0-9+&@#/%=~_|])/g, '<a class="releaselink" href="$1" target="_blank">$1</a>'); // convert normal links

    const addItems = [];
    const fixItems = [];
    const changeItems = [];


    result = result.replace(/(?:<br \/>)*\d+\.\s*ADD: (.*)/gi, function (s, match) {
        addItems.push(match);
        return "";
    });
    result = result.replace(/(?:<br \/>)*\d+\.\s*FIX: (.*)/gi, function (s, match) {
        fixItems.push(match);
        return "";
    });
    result = result.replace(/(?:<br \/>)*\d+\.\s*CHANGE: (.*)/gi, function (s, match) {
        changeItems.push(match);
        return "";
    });

    if ((addItems.length > 0) || (fixItems.length > 0) || (changeItems.length > 0)) {
        result += "<ol>";
    }

    for (let i = 0; i < addItems.length; i++) {
        result += "<li><i class='fa fa-plus'></i> ADD: " + addItems[i] + "</li>";
        if (i == addItems.length - 1 && i != 0) result += "<br>";
    }

    for (let i = 0; i < fixItems.length; i++) {
        result += "<li><i class='fa fa-wrench'></i> FIX: " + fixItems[i] + "</li>";
        if (i == fixItems.length - 1 && i != 0) result += "<br>";
    }

    for (let i = 0; i < changeItems.length; i++) {
        result += "<li><i class='fa fa-lightbulb'></i> CHANGE: " + changeItems[i] + "</li>";
    }

    if ((addItems.length > 0) || (fixItems.length > 0) || (changeItems.length > 0)) {
        result += "</ol>";
    }

    return result;
}

function load_info() {
    document.getElementById("setttings-page-title").innerHTML = 'Information';
    document.getElementById("includedContent").innerHTML = '<object  type="text/html" class="object" data="assets/php/settings/info.php" ></object>';
    $(".sidebar-nav-item").removeClass('active');
    $("li[data-item='info']").addClass("active");
}

function load_preferences() {
    document.getElementById("setttings-page-title").innerHTML = 'User Preferences';
    document.getElementById("includedContent").innerHTML = '<object type="text/html" class="object" data="assets/php/settings/user_preferences.php" ></object>';
    $(".sidebar-nav-item").removeClass('active');
    $("li[data-item='user-preferences']").addClass("active");
}

function load_settings() {
    document.getElementById("setttings-page-title").innerHTML = 'Monitorr Settings';
    document.getElementById("includedContent").innerHTML = '<object type="text/html" class="object" data="assets/php/settings/site_settings.php" ></object>';
    $(".sidebar-nav-item").removeClass('active');
    $("li[data-item='monitorr-settings']").addClass("active");
}

function load_authentication() {
    document.getElementById("setttings-page-title").innerHTML = 'Monitorr Authentication';
    document.getElementById("includedContent").innerHTML = '<object type="text/html" class="object" data="assets/php/settings/authentication.php" ></object>';
    $(".sidebar-nav-item").removeClass('active');
    $("li[data-item='monitorr-authentication']").addClass("active");
}

function load_services() {
    document.getElementById("setttings-page-title").innerHTML = 'Services Configuration';
    document.getElementById("includedContent").innerHTML = '<object type="text/html" class="object" data="assets/php/settings/services_settings.php" ></object>';
    $(".sidebar-nav-item").removeClass('active');
    $("li[data-item='logs-configuration']").addClass("active");
}

function load_registration() {
    document.getElementById("setttings-page-title").innerHTML = 'Registration';
    document.getElementById("includedContent").innerHTML = '<object type="text/html" class="object" data="assets/php/settings/registration.php" ></object>';
    $(".sidebar-nav-item").removeClass('active');
    $("li[data-item='registration']").addClass("active");
}

function load_plugins() {
    document.getElementById("setttings-page-title").innerHTML = 'Plugins';
    document.getElementById("includedContent").innerHTML = '<object type="text/html" class="object" data="assets/php/settings/plugins.php" ></object>';
    $(".sidebar-nav-item").removeClass('active');
    $("li[data-item='plugins']").addClass("active");
}

function notify(title, content) {

    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-bottom-left",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    toastr["success"](content, title);

    console.log(title + " " + content);
}