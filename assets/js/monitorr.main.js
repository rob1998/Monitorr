

function statusCheck(override) {

    if ($("#buttonStart input:checkbox").is(':checked') || override) {
        $("#modalloadingindex").fadeIn("slow");
        console.log('Service check START | Interval: ' + settings.rfsysinfo + ' ms');

        getSystemBadges();
        checkServices();

        $("#modalloadingindex").fadeOut("slow");

        setTimeout(statusCheck, settings.rfsysinfo);
    }

}

function checkServices() {
    for(let i = 0; i<services.length; i++) {
        let $service = services[i];
        let $serviceDiv = $('#service-' + $service.serviceTitle);

        if ( $serviceDiv.length){
            if($service.ping == "Enabled") {
                $("#pingindicator-"+$service.serviceTitle).show();
                ping($service);
            } else {
                $("#pingindicator-"+$service.serviceTitle).hide();
            }
        } else {

        }
    }

    //TODO: cleanup services that no longer exist or have different names
}

function ping(service) {
    $.ajax({
        type: "POST",
        url: "api/?v1/getPing",
        data: {'url': service.checkurl},
        dataType: "json",
        success: function(response){

            let $pingTime = response.data;
            let $serviceElement = $("#service-" + service.serviceTitle);
            let $pingClassElement = $("#pingindicator-" + service.serviceTitle + " > div");
            let $btnStatus = $("#status-" + service.serviceTitle);

            if(!$pingTime){

                $pingClassElement.removeClass("pinggreen");
                $pingClassElement.removeClass("pingyellow");
                $pingClassElement.removeClass("pingred");
                $pingClassElement.addClass("pingred");

                $pingClassElement.prop('title', "Ping response time: unresponsive");

                $btnStatus.removeClass("btnonline");
                $btnStatus.addClass("btnoffline");
                $btnStatus.text("Offline");

                $serviceElement.find(".servicetile").addClass("offline");
                $serviceElement.find(".servicetitle").addClass("offline");
                $serviceElement.find(".serviceimg").addClass("offline");

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

                $serviceElement.find(".servicetile").removeClass("offline");
                $serviceElement.find(".servicetitle").removeClass("offline");
                $serviceElement.find(".serviceimg").removeClass("offline");

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

            //<editor-fold desc="values">
            $("#cpu > .value").text(data.serverLoad + "%");
            $("#ram > .value").text(data.ramPercentage + "%");
            $("#uptime > .value").text(data.totalUptime);
            $("#ping > .value").text(data.pingTime + "ms");
            if(data.disk1Usage != "?") {
                $("#hdpercent1").show();
                $("#hdlabel1").show();
                $("#hdpercent1").text(data.disk1Usage + "%");
            } else {
                $("#hdpercent1").hide();
                $("#hdlabel1").hide();
            }
            if(data.disk2Usage != "?") {
                $("#hdpercent2").show();
                $("#hdlabel2").show();
                $("#hdpercent2").text(data.disk2Usage + "%");
            } else {
                $("#hdpercent2").hide();
                $("#hdlabel2").hide();
            }
            if(data.disk3Usage != "?") {
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
                var cpuok = settings.cpuok;
                var cpuwarn = settings.cpuwarn;
                var cpuClass;
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
                var ramok = settings.ramok;
                var ramwarn = settings.ramwarn;
                var ramClass;
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
                var pingok = settings.pingok;
                var pingwarn = settings.pingwarn;
                var pingClass;
                if(data.pingTime == "?") pingClass =  'danger';
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
                var diskok = settings.hdok;
                var diskwarn = settings.hdwarn;
                if(data.disk1Usage != "?") {
                    //<editor-fold desc="disk1 class">
                        var disk1Class;
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
                if(data.disk2Usage != "?") {
                    //<editor-fold desc="disk2 class">
                    var disk2Class;
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
                if(data.disk3Usage != "?") {
                    //<editor-fold desc="disk3 class">
                    var disk3Class;
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
    });
}

function refreshConfig(updateServices) {
    $.ajax({
        url: "assets/php/sync-config.php",
        type: "GET",
        success: function (response) {

            let json = JSON.parse(response);
            settings = json.settings;
            preferences = json.preferences;
            services = json.services;

            setTimeout(function () {
                refreshConfig()
            }, settings.rfconfig); //delay is rftime


            $("#auto-update-status").attr("data-enabled", settings.logRefresh);

            if (updateServices) {
                if (settings.logRefresh == "true" && (logInterval == false || settings.rflog != current_rflog)) {
                    clearInterval(nIntervId);
                    nIntervId = setInterval(refreshblockUI, settings.rflog);
                    logInterval = true;
                    $("#autoUpdateSlider").attr("data-enabled", "true");
                    current_rflog = settings.rflog;
                    console.log("Auto update: Enabled | Interval: " + settings.rflog + " ms");
                    $.growlUI("Auto update: Enabled");
                } else if (settings.logRefresh == "false" && logInterval == true) {
                    clearInterval(nIntervId);
                    logInterval = false;
                    $("#autoUpdateSlider").attr("data-enabled", "false");
                    console.log("Auto update: Disabled");
                    $.growlUI("Auto update: Disabled");
                }
            }

            document.title = preferences.sitetitle; //update page title to configured title
            //console.log('Refreshed config variables');
        }
    });
}

function syncServerTime() {
    console.log('Monitorr time update START | Interval: ' + settings.rftime + ' ms');
    $.ajax({
        url: "assets/php/time.php",
        type: "GET",
        success: function (response) {
            var response = $.parseJSON(response);
            servertime = response.serverTime;
            timeStandard = parseInt(response.timeStandard);
            timeZone = response.timezoneSuffix;
            rftime = response.rftime;
            date = new Date(servertime);
            setTimeout(function () {
                syncServerTime()
            }, settings.rftime); //delay is rftime
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('Monitorr time update START');
        }
    });
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

    var addItems = [];
    var fixItems = [];
    var changeItems = [];


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

    var i = 0;
    for (i = 0; i < addItems.length; i++) {
        result += "<li><i class='fa fa-plus'></i> ADD: " + addItems[i] + "</li>";
        if (i == addItems.length - 1 && i != 0) result += "<br>";
    }

    var i = 0;
    for (i = 0; i < fixItems.length; i++) {
        result += "<li><i class='fa fa-wrench'></i> FIX: " + fixItems[i] + "</li>";
        if (i == fixItems.length - 1 && i != 0) result += "<br>";
    }

    var i = 0;
    for (i = 0; i < changeItems.length; i++) {
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